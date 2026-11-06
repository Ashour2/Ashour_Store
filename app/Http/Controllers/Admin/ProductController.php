<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use App\Models\Pharmacy;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $products = Product::visibleTo($this->user())
            ->with(['pharmacies', 'addedBy', 'category'])
            ->withCount('pharmacies')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($search) use ($q) {
                    $search->where('name', 'like', "%{$q}%")
                        ->orWhere('active_ingredient', 'like', "%{$q}%")
                        ->orWhere('scientific_name', 'like', "%{$q}%")
                        ->orWhere('trade_name', 'like', "%{$q}%")
                        ->orWhere('barcode', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $pharmacies = $this->user()->isPharmacist()
            ? collect()
            : Pharmacy::active()->orderBy('id')->get();

        return view('admin.products.index', compact('products', 'pharmacies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->requirePharmacyForPharmacist();
        $isPharmacist = $this->user()->isPharmacist();
        $categories = Category::select('id', 'name')->get();
        $product = new Product();

        return view('admin.products.create', compact('categories', 'product') + $this->branches($product));
    }

    /** Toggle whether this medicine is visible on the public site. */
    public function toggleActive(Product $product)
    {
        $this->ensureVisibleToUser($product);
        if ($this->user()->isPharmacist()) {
            $pharmacy = $product->pharmacies()->whereKey($this->user()->pharmacy_id)->firstOrFail();
            $product->pharmacies()->updateExistingPivot($pharmacy->id, ['is_available' => ! $pharmacy->pivot->is_available]);
            return back()->with('msg', 'تم تحديث حالة التوفر في صيدليتك.')->with('type', 'success');
        }
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('msg', $product->is_active ? 'تم تفعيل الدواء وظهر في الموقع.' : 'تم إخفاء الدواء من الموقع.')
            ->with('type', 'info');
    }

    /** Make every medicine this user can manage visible on the public site. */
    public function activateAll()
    {
        if ($this->user()->isPharmacist()) return $this->setOwnInventoryAvailability(true);
        $updated = Product::visibleTo($this->user())->where('is_active', false)->update(['is_active' => true]);

        return back()->with('msg', "تم تنشيط {$updated} دواء وظهروا في الموقع.")->with('type', 'success');
    }

    /** Hide every medicine this user can manage from the public site. */
    public function deactivateAll()
    {
        if ($this->user()->isPharmacist()) return $this->setOwnInventoryAvailability(false);
        $updated = Product::visibleTo($this->user())->where('is_active', true)->update(['is_active' => false]);

        return back()->with('msg', "تم إلغاء تنشيط {$updated} دواء وإخفاؤهم من الموقع.")->with('type', 'info');
    }

    /** Attach every product to selected branches, preserving existing stock rows. */
    public function bulkAttachPharmacies(Request $request)
    {
        $request->validate([
            'pharmacy_ids' => 'required|array|min:1',
            'pharmacy_ids.*' => 'integer|exists:pharmacies,id',
        ]);

        $pharmacyIds = Pharmacy::active()->whereIn('id', $request->pharmacy_ids)->pluck('id');
        $now = now();

        Product::select('id')->orderBy('id')->chunkById(500, function ($products) use ($pharmacyIds, $now) {
            $rows = [];
            foreach ($products as $product) {
                foreach ($pharmacyIds as $pharmacyId) {
                    $rows[] = [
                        'product_id' => $product->id,
                        'pharmacy_id' => $pharmacyId,
                        'quantity' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            DB::table('pharmacy_product')->insertOrIgnore($rows);
        });

        return back()->with('msg', 'تم ربط جميع الأدوية بالصيدليات المختارة بدون تكرار.')->with('type', 'success');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->requirePharmacyForPharmacist();
        $isPharmacist = $this->user()->isPharmacist();
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'image' => 'nullable|image|max:4096',
            'gallery' => 'nullable',
            'price' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'quantity' => 'required',
            'category_id' => 'required',
            'scientific_name' => 'nullable|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'contraindications_warnings_en' => 'nullable|string',
            'contraindications_warnings_ar' => 'nullable|string',
            'pharmacy_notes' => 'nullable|string|max:2000',
            'pharmacies' => 'array',
            'pharmacies.*' => 'integer',
            'quantities' => 'array',
            'quantities.*' => 'nullable|integer|min:0|max:1000000',
        ]);

        // $data = $request->except('_token', 'image', 'gallery');

        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];

        $warnings = [
            'en' => $request->contraindications_warnings_en,
            'ar' => $request->contraindications_warnings_ar,
        ];

        $product = Product::create([
            // 'name' => $request->name,
            // 'description' => $request->description,
            'name'=> json_encode($name,JSON_UNESCAPED_UNICODE),
            'description'=> json_encode($desc,JSON_UNESCAPED_UNICODE),
            'scientific_name' => $isPharmacist ? null : $request->scientific_name,
            'trade_name' => $isPharmacist ? null : $request->trade_name,
            'contraindications_warnings' => $isPharmacist ? null : json_encode($warnings, JSON_UNESCAPED_UNICODE),

            'price' => $request->price,
            'show_price' => $request->boolean('show_price'),
            'is_active' => false,
            'quantity' => $request->quantity,
            'stock_display' => $request->stock_display ?? 'quantity',
            'category_id' => $request->category_id,
            'added_by_id' => $this->user()->id,
            'active_ingredient' => $isPharmacist ? null : $request->active_ingredient,
            'dosage_form' => $isPharmacist ? null : $request->dosage_form,
            'requires_prescription' => $isPharmacist ? false : $request->boolean('requires_prescription'),
            'barcode' => $isPharmacist ? null : $request->barcode,
            'expiry_date' => $isPharmacist ? null : $request->expiry_date,
        ]);

        // Which branches stock it, and how many units each holds
        $this->syncProductPharmacies($product, $request);
        if ($isPharmacist) {
            $product->pharmacies()->updateExistingPivot($this->user()->pharmacy_id, ['notes' => $request->input('pharmacy_notes')]);
        }

        // The catalogue image is optional. Do not create a fake placeholder
        // record when the manager or pharmacist did not upload one.
        if ($request->hasFile('image')) {
            $img_name = rand().time().$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $img_name);
            $product->image()->create([
                'path' => $img_name,
            ]);
        }

        if($request->has('gallery')) {
            foreach($request->gallery as $img) {
                $img_name = rand().time().$img->getClientOriginalName();
                $img->move(public_path('images'), $img_name);
                $product->image()->create([
                    'path' => $img_name,
                    'type' => 'gallery'
                ]);
            }
        }

        return redirect()
        ->route('admin.products.index')
        ->with('msg', 'Product added successfully')
        ->with('type', 'success');
    }

    /**
     * Branches and this medicine's current quantity at each, for the form.
     */
    protected function branches(Product $product): array
    {
        $user = $this->user();
        $pharmacies = $user->isPharmacist()
            ? Pharmacy::whereKey($user->pharmacy_id)->get()
            : Pharmacy::orderByDesc('is_active')->orderBy('id')->get();

        return [
            'pharmacies' => $pharmacies,
            'held' => $product->exists
                ? $product->pharmacies()->pluck('quantity', 'pharmacies.id')
                : collect(),
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $this->ensureVisibleToUser($product);
        if ($this->user()->isPharmacist()) {
            $inventory = $product->pharmacies()->whereKey($this->user()->pharmacy_id)->firstOrFail()->pivot;
            return view('admin.pharmacies.inventory-edit', compact('product', 'inventory'));
        }
        $categories = Category::select('id', 'name')->get();

        return view('admin.products.edit', compact('product', 'categories') + $this->branches($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->ensureVisibleToUser($product);
        if ($this->user()->isPharmacist()) {
            return app(PharmacyInventoryController::class)->attach($request, $product);
        }
        $isPharmacist = false;
        // dd($request->all());
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'price' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'quantity' => 'required',
            'category_id' => 'required',
            'scientific_name' => 'nullable|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'contraindications_warnings_en' => 'nullable|string',
            'contraindications_warnings_ar' => 'nullable|string',
            'pharmacy_notes' => 'nullable|string|max:2000',
            'pharmacies' => 'array',
            'pharmacies.*' => 'integer',
            'quantities' => 'array',
            'quantities.*' => 'nullable|integer|min:0|max:1000000',
        ]);

        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];

        $warnings = [
            'en' => $request->contraindications_warnings_en,
            'ar' => $request->contraindications_warnings_ar,
        ];

        $product->update([
            'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
            'description' => json_encode($desc, JSON_UNESCAPED_UNICODE),
            'scientific_name' => $isPharmacist ? null : $request->scientific_name,
            'trade_name' => $isPharmacist ? null : $request->trade_name,
            'contraindications_warnings' => $isPharmacist ? null : json_encode($warnings, JSON_UNESCAPED_UNICODE),
            'price' => $request->price,
            'show_price' => $request->boolean('show_price'),
            'quantity' => $request->quantity,
            'stock_display' => $request->stock_display ?? 'quantity',
            'category_id' => $request->category_id,
            'active_ingredient' => $isPharmacist ? null : $request->active_ingredient,
            'dosage_form' => $isPharmacist ? null : $request->dosage_form,
            'requires_prescription' => $isPharmacist ? false : $request->boolean('requires_prescription'),
            'barcode' => $isPharmacist ? null : $request->barcode,
            'expiry_date' => $isPharmacist ? null : $request->expiry_date,
        ]);

        $this->syncProductPharmacies($product, $request);
        if ($isPharmacist) {
            $product->pharmacies()->updateExistingPivot($this->user()->pharmacy_id, ['notes' => $request->input('pharmacy_notes')]);
        }

        // Add image to relation
        if($request->hasFile('image')) {
            if($product->image) {
                File::delete( public_path('images/'.$product->image->path) );
            }

            $product->image()->delete();

            $img_name = rand().time().$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $img_name);
            $product->image()->create([
                'path' => $img_name,
            ]);
        }


        if($request->has('gallery')) {
            foreach($request->gallery as $img) {
                $img_name = rand().time().$img->getClientOriginalName();
                $img->move(public_path('images'), $img_name);
                $product->image()->create([
                    'path' => $img_name,
                    'type' => 'gallery'
                ]);
            }
        }

        return redirect()
        ->route('admin.products.index')
        ->with('msg', 'Product updated successfully')
        ->with('type', 'info');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->ensureVisibleToUser($product);
        if ($this->user()->isPharmacist()) {
            $product->pharmacies()->detach($this->user()->pharmacy_id);
            return back()->with('msg', 'تمت إزالة الدواء من صيدليتك فقط.')->with('type', 'info');
        }
        if ($product->image?->path) File::delete(public_path('images/'.$product->image->path));
        foreach($product->gallery as $img ) {
            File::delete( public_path('images/'.$img->path) );
        }

        $product->image()->delete();
        $product->gallery()->delete();
        $product->delete();

        return redirect()
        ->route('admin.products.index')
        ->with('msg', 'Product deleted successfully')
        ->with('type', 'info');
    }

    function delete_img($id) {
        abort_if($this->user()->isPharmacist(), 403, 'صور الكتالوج من صلاحيات المدير.');
        $img = Image::findOrFail($id);
        if ($img->imageable instanceof Product) {
            $this->ensureVisibleToUser($img->imageable);
        }
        File::delete( public_path('images/'.$img->path) );
        return Image::destroy($id);
    }

    /** Display the safe, one-shot Excel medicine importer. */
    public function importForm()
    {
        $this->requirePharmacyForPharmacist();
        $isPharmacist = $this->user()->isPharmacist();

        return view('admin.products.import');
    }

    /** Download the medicines visible to the signed-in administrator as Excel. */
    public function exportExcel()
    {
        $this->loadSpreadsheetClasses();
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle('Medicines');
        $sheet->fromArray([['ID', 'الاسم العربي', 'English name', 'السعر', 'الكمية', 'التصنيف', 'المادة الفعالة', 'الباركود', 'الحالة']]);

        Product::visibleTo($this->user())->with('category', 'pharmacies')->orderBy('id')->chunkById(500, function ($products) use ($sheet) {
            foreach ($products as $product) {
                $ownStock = $this->user()->isPharmacist() ? $product->pharmacies->firstWhere('id', $this->user()->pharmacy_id)?->pivot : null;
                $sheet->fromArray([[
                    $product->id, $product->name_ar, $product->name_en, $ownStock?->price ?? $product->price, $ownStock?->quantity ?? $product->quantity,
                    $product->category?->trans_name, $product->active_ingredient, $product->barcode,
                    $ownStock ? ($ownStock->is_available && $ownStock->quantity > 0 ? 'متوفر' : 'غير متوفر') : ($product->is_active ? 'نشط' : 'غير نشط'),
                ]], null, 'A' . ($sheet->getHighestRow() + 1));
            }
        });

        $sheet->freezePane('A2');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        foreach (range('A', 'I') as $column) $sheet->getColumnDimension($column)->setAutoSize(true);
        $writer = new Xlsx($sheet->getParent());

        return response()->streamDownload(fn () => $writer->save('php://output'), 'medicines-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Opens a print-ready report; the browser's Print dialog can save it as a PDF. */
    public function exportPdf()
    {
        $products = Product::visibleTo($this->user())->with('category', 'pharmacies')->orderBy('id')->get();

        return view('admin.products.export-pdf', compact('products'));
    }

    /** Download the exact column layout accepted by the importer. */
    public function downloadImportTemplate()
    {
        $this->loadSpreadsheetClasses();
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle('Medicines');
        $sheet->fromArray([[
            'name_en', 'name_ar', 'price', 'quantity', 'category', 'active_ingredient',
            'scientific_name', 'trade_name', 'dosage_form', 'barcode', 'expiry_date',
            'requires_prescription', 'show_price', 'stock_display',
        ], [
            'Paracetamol 500mg', 'باراسيتامول 500 ملغ', 2.50, 100, 'Pain relief', 'Paracetamol',
            'Acetaminophen', 'Panadol', 'Tablet', '1234567890123', '2028-12-31', 'no', 'yes', 'quantity',
        ]]);
        $sheet->freezePane('A2');
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($sheet->getParent());

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            'medicine-import-template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    /**
     * Import a spreadsheet atomically. A malformed row rejects the whole file,
     * so a partial catalogue can never be left behind.
     */
    public function import(Request $request)
    {
        $this->requirePharmacyForPharmacist();
        $isPharmacist = $this->user()->isPharmacist();
        $this->loadSpreadsheetClasses();
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $rows = IOFactory::load($request->file('file')->getRealPath())
                ->getActiveSheet()->toArray(null, true, true, false);
        } catch (\Throwable $exception) {
            return back()->withInput()->withErrors(['file' => 'تعذر قراءة الملف. استخدم قالب الاستيراد أو ملف Excel/CSV صالح.']);
        }

        if (count($rows) < 2) {
            return back()->withInput()->withErrors(['file' => 'الملف فارغ. أضف صفًا واحدًا للدواء على الأقل بعد العناوين.']);
        }

        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($rows));
        $required = ['name_en', 'price'];
        $missing = array_values(array_diff($required, $headers));
        if ($missing) {
            return back()->withInput()->withErrors(['file' => 'أعمدة مطلوبة غير موجودة: ' . implode(', ', $missing)]);
        }

        $categories = Category::all()->keyBy('id');
        $categoryNames = Category::all()->mapWithKeys(function ($category) {
            return [
                mb_strtolower(trim($category->name_en)) => $category->id,
                mb_strtolower(trim($category->name_ar)) => $category->id,
            ];
        });
        $errors = [];
        $records = [];
        $barcodes = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $data = array_combine($headers, array_slice(array_pad($row, count($headers), null), 0, count($headers)));
            $data = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $data);
            if (!array_filter($data, fn ($value) => $value !== null && $value !== '')) {
                continue;
            }

            $name = (string) ($data['name_en'] ?? '');
            if ($name === '') $errors[] = "السطر {$line}: name_en مطلوب.";
            if (!is_numeric($data['price'] ?? null) || (float) $data['price'] < 0) $errors[] = "السطر {$line}: price يجب أن يكون رقمًا موجبًا أو صفرًا.";
            $quantity = $data['quantity'] ?? 0;
            if ($quantity === '' || $quantity === null) $quantity = 0;
            if (filter_var($quantity, FILTER_VALIDATE_INT) === false || (int) $quantity < 0) $errors[] = "السطر {$line}: quantity يجب أن يكون عددًا صحيحًا موجبًا أو صفرًا.";
            $data['quantity'] = $quantity;

            $categoryValue = trim((string) ($data['category'] ?? ''));
            $categoryId = null;
            if ($categoryValue !== '') {
                $categoryId = $categories->has((int) $categoryValue)
                    ? (int) $categoryValue
                    : $categoryNames->get(mb_strtolower($categoryValue));
                if (! $categoryId) $errors[] = "السطر {$line}: التصنيف '{$categoryValue}' غير موجود.";
            }

            $barcode = trim((string) ($data['barcode'] ?? ''));
            if ($barcode !== '') {
                if (isset($barcodes[$barcode])) $errors[] = "السطر {$line}: barcode مكرر داخل الملف.";
                $barcodes[$barcode] = true;
            }

            $expiry = trim((string) ($data['expiry_date'] ?? ''));
            if ($expiry !== '' && !strtotime($expiry)) $errors[] = "السطر {$line}: expiry_date يجب أن يكون تاريخًا صالحًا بصيغة YYYY-MM-DD.";

            $records[] = compact('line', 'data', 'categoryId', 'barcode', 'expiry');
        }

        if (! $records) $errors[] = 'لا توجد صفوف أدوية صالحة للاستيراد.';
        if (count($records) > 10000) $errors[] = 'الحد الأقصى للاستيراد هو 10,000 صف في الملف الواحد.';
        $existing = Product::whereIn('barcode', array_keys($barcodes))->pluck('barcode')->all();
        foreach ($existing as $barcode) $errors[] = "الباركود '{$barcode}' موجود مسبقًا في النظام.";

        if ($errors) {
            return back()->withInput()->withErrors(['file' => implode(' ', array_slice($errors, 0, 20)) . (count($errors) > 20 ? ' توجد أخطاء إضافية.' : '')]);
        }

        DB::transaction(function () use ($records) {
            foreach ($records as $record) {
                $data = $record['data'];
                $nameAr = $data['name_ar'] ?? $data['name_en'];
                $stockDisplay = $data['stock_display'] ?? 'quantity';
                $product = Product::create([
                    'name' => json_encode(['en' => $data['name_en'], 'ar' => $nameAr ?: $data['name_en']], JSON_UNESCAPED_UNICODE),
                    'description' => json_encode(['en' => $data['description_en'] ?? $data['name_en'], 'ar' => $data['description_ar'] ?? ($nameAr ?: $data['name_en'])], JSON_UNESCAPED_UNICODE),
                    'price' => (float) $data['price'],
                    'quantity' => (int) $data['quantity'],
                    'category_id' => $record['categoryId'],
                    'added_by_id' => $this->user()->id,
                    'active_ingredient' => $data['active_ingredient'] ?? null,
                    'scientific_name' => $data['scientific_name'] ?? null,
                    'trade_name' => $data['trade_name'] ?? null,
                    'dosage_form' => $data['dosage_form'] ?? null,
                    'barcode' => $record['barcode'] ?: null,
                    'expiry_date' => $record['expiry'] ?: null,
                    'requires_prescription' => $this->excelBoolean($data['requires_prescription'] ?? false),
                    'show_price' => !array_key_exists('show_price', $data) || $this->excelBoolean($data['show_price']),
                    'is_active' => false,
                    'stock_display' => in_array($stockDisplay, ['quantity', 'available', 'hidden'], true) ? $stockDisplay : 'quantity',
                ]);

                if ($this->user()->isPharmacist()) {
                    $product->syncBranchStock([$this->user()->pharmacy_id => (int) $data['quantity']], [$this->user()->pharmacy_id]);
                }
            }
        });

        return redirect()->route('admin.products.index')->with('msg', count($records) . ' دواء تم استيرادهم بنجاح.')->with('type', 'success');
    }

    protected function excelBoolean(mixed $value): bool
    {
        return in_array(mb_strtolower(trim((string) $value)), ['1', 'yes', 'y', 'true', 'نعم', 'ن'], true);
    }

    /**
     * Lets a shared-hosting deployment load the spreadsheet package even when
     * it has been uploaded through a file manager instead of Composer/SSH.
     */
    protected function loadSpreadsheetClasses(): void
    {
        $vendor = base_path('vendor');
        $prefixes = [
            'PhpOffice\\PhpSpreadsheet\\' => $vendor . '/phpoffice/phpspreadsheet/src/PhpSpreadsheet/',
            'Composer\\Pcre\\' => $vendor . '/composer/pcre/src/',
            'ZipStream\\' => $vendor . '/maennchen/zipstream-php/src/',
            'Complex\\' => $vendor . '/markbaker/complex/classes/src/',
            'Matrix\\' => $vendor . '/markbaker/matrix/classes/src/',
        ];

        spl_autoload_register(static function (string $class) use ($prefixes): void {
            foreach ($prefixes as $prefix => $directory) {
                if (! str_starts_with($class, $prefix)) {
                    continue;
                }

                $file = $directory . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
                if (is_file($file)) {
                    require_once $file;
                }

                return;
            }
        });

        // Shared hosting may have PhpSpreadsheet but not Composer's pcre
        // helper package. The importer needs only these native-PCRE wrappers.
        if (! class_exists(\Composer\Pcre\Preg::class)) {
            require_once app_path('Support/ComposerPcrePregFallback.php');
        }
    }

    protected function user()
    {
        return auth()->user();
    }

    private function setOwnInventoryAvailability(bool $available)
    {
        $this->requirePharmacyForPharmacist();
        DB::table('pharmacy_product')->where('pharmacy_id', $this->user()->pharmacy_id)
            ->where('is_allowed', true)->update(['is_available' => $available, 'updated_at' => now()]);
        return back()->with('msg', 'تم تحديث حالة توفر أدوية صيدليتك.')->with('type', 'success');
    }

    /** A pharmacist must be assigned to a branch before working with medicines. */
    protected function requirePharmacyForPharmacist(): void
    {
        abort_if($this->user()->isPharmacist() && ! $this->user()->pharmacy_id, 403,
            __('admin.pharmacy_assignment_required'));
    }

    /** Deny direct URLs and forged requests for a medicine outside the branch. */
    protected function ensureVisibleToUser(Product $product): void
    {
        $user = $this->user();
        if ($user->isPharmacist()) {
            $this->requirePharmacyForPharmacist();
            abort_unless($product->pharmacies()->whereKey($user->pharmacy_id)->wherePivot('is_allowed', true)->exists(), 403);
        }
    }

    /** A pharmacist cannot choose or modify any branch other than their own. */
    protected function syncProductPharmacies(Product $product, Request $request): void
    {
        $user = $this->user();
        if ($user->isPharmacist()) {
            $product->pharmacies()->syncWithoutDetaching([$user->pharmacy_id => [
                'quantity' => (int) $request->quantity,
                'price' => $request->input('price'),
                'show_price' => $request->boolean('show_price'),
                'is_available' => true,
            ]]);
            return;
        }

        $product->syncBranchStock($request->input('quantities', []), $request->input('pharmacies', []));
    }
}
