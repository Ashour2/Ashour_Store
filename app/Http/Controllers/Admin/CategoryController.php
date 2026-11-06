<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('image')->withCount('products')->orderBy('home_display_order')->orderBy('id')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = new Category();
        return view('admin.categories.create', compact('category'));
    }

    /** Excel list of all categories. */
    public function exportExcel()
    {
        $this->loadSpreadsheetClasses();
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle('Categories');
        $sheet->fromArray([['ID', 'الاسم العربي', 'English name', 'الوصف العربي', 'English description', 'عدد الأدوية']]);

        foreach (Category::withCount('products')->orderBy('id')->get() as $category) {
            $sheet->fromArray([[
                $category->id, $category->name_ar, $category->name_en,
                $category->description_ar, $category->description_en, $category->products_count,
            ]], null, 'A' . ($sheet->getHighestRow() + 1));
        }

        $this->formatSheet($sheet, 'F');
        $writer = new Xlsx($sheet->getParent());

        return response()->streamDownload(fn () => $writer->save('php://output'), 'categories-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** A lightweight template to add many categories at once. */
    public function downloadImportTemplate()
    {
        $this->loadSpreadsheetClasses();
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle('Categories');
        $sheet->fromArray([['name_en', 'name_ar', 'description_en', 'description_ar'], ['Pain relief', 'مسكنات', 'Pain and fever medicines', 'أدوية الألم والحرارة']]);
        $this->formatSheet($sheet, 'D');
        $writer = new Xlsx($sheet->getParent());

        return response()->streamDownload(fn () => $writer->save('php://output'), 'category-import-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importForm()
    {
        return view('admin.categories.import');
    }

    /** Import names/descriptions. Images can be added later from edit screen. */
    public function import(Request $request)
    {
        $this->loadSpreadsheetClasses();
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        try {
            $rows = IOFactory::load($request->file('file')->getRealPath())->getActiveSheet()->toArray(null, true, true, false);
        } catch (\Throwable) {
            return back()->withInput()->withErrors(['file' => 'تعذر قراءة الملف. استخدم قالب Excel أو CSV صالح.']);
        }

        if (count($rows) < 2) {
            return back()->withInput()->withErrors(['file' => 'الملف فارغ. أضف صف تصنيف واحدًا على الأقل.']);
        }

        $headers = array_map(fn ($value) => strtolower(trim((string) $value)), array_shift($rows));
        $required = ['name_en', 'name_ar'];
        if ($missing = array_diff($required, $headers)) {
            return back()->withInput()->withErrors(['file' => 'الأعمدة المطلوبة غير موجودة: ' . implode(', ', $missing)]);
        }

        $existing = Category::all()->flatMap(fn ($category) => [mb_strtolower(trim($category->name_en)), mb_strtolower(trim($category->name_ar))])->filter()->flip();
        $records = [];
        $errors = [];
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $data = array_combine($headers, array_slice(array_pad($row, count($headers), null), 0, count($headers)));
            $data = array_map(fn ($value) => trim((string) $value), $data);
            if (! array_filter($data)) continue;
            if (($data['name_en'] ?? '') === '' || ($data['name_ar'] ?? '') === '') {
                $errors[] = "السطر {$line}: name_en و name_ar مطلوبان.";
                continue;
            }
            $names = [mb_strtolower($data['name_en']), mb_strtolower($data['name_ar'])];
            if ($existing->has($names[0]) || $existing->has($names[1])) {
                $errors[] = "السطر {$line}: التصنيف موجود مسبقًا.";
                continue;
            }
            $existing->put($names[0], true)->put($names[1], true);
            $records[] = $data;
        }

        if ($errors || ! $records) {
            return back()->withInput()->withErrors(['file' => implode(' ', array_slice($errors ?: ['لا توجد تصنيفات صالحة للاستيراد.'], 0, 15))]);
        }

        foreach ($records as $data) {
            Category::create([
                'name' => json_encode(['en' => $data['name_en'], 'ar' => $data['name_ar']], JSON_UNESCAPED_UNICODE),
                'description' => json_encode(['en' => $data['description_en'] ?? '', 'ar' => $data['description_ar'] ?? ''], JSON_UNESCAPED_UNICODE),
            ]);
        }

        return redirect()->route('admin.categories.index')->with('msg', count($records) . ' تصنيف تم استيراده. أضف الصورة للتصنيف من زر التعديل.')->with('type', 'success');
    }

    public function exportPdf()
    {
        $categories = Category::withCount('products')->orderBy('id')->get();

        return view('admin.categories.export-pdf', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'image' => 'nullable|image|max:4096',
            'home_display_order' => 'nullable|integer|min:1|max:9999',
        ]);

        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];

        $category = Category::create([
            // 'name' => $request->name,
            // 'description' => $request->description
            'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
            'description' => json_encode($desc, JSON_UNESCAPED_UNICODE),
            'home_display_order' => $request->integer('home_display_order') ?: 999,
        ]);

        $this->saveImage($request, $category);

        return redirect()
            ->route('admin.categories.index')
            ->with('msg', 'Category added successfully')
            ->with('type', 'success');
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
    public function edit(Category $category)
    {
        // $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'image' => 'nullable|image|max:4096',
            'home_display_order' => 'nullable|integer|min:1|max:9999',
        ]);

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];
        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $category->update([
            'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
            'description' => json_encode($desc, JSON_UNESCAPED_UNICODE),
            'home_display_order' => $request->integer('home_display_order') ?: 999,
        ]);

        if ($request->hasFile('image')) {
            if ($category->image && $category->image->path) {
                File::delete(public_path('images/' . $category->image->path));
            }
            $category->image()->delete();
            $this->saveImage($request, $category);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('msg', 'Category updated successfully')
            ->with('type', 'info');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->image && $category->image->path) {
            File::delete(public_path('images/' . $category->image->path));
        }
        $category->image()->delete();
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('msg', 'Category deleted successfully')
            ->with('type', 'danger');
    }

    private function saveImage(Request $request, Category $category): void
    {
        if (! $request->hasFile('image')) {
            return;
        }

        $image = $request->file('image');
        $name = uniqid('category_', true) . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $name);
        $category->image()->create(['path' => $name]);
    }

    private function formatSheet($sheet, string $lastColumn): void
    {
        $sheet->freezePane('A2');
        $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);
        foreach (range('A', $lastColumn) as $column) $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    private function loadSpreadsheetClasses(): void
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
                if (! str_starts_with($class, $prefix)) continue;
                $file = $directory . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
                if (is_file($file)) require_once $file;
                return;
            }
        });
        if (! class_exists(\Composer\Pcre\Preg::class)) require_once app_path('Support/ComposerPcrePregFallback.php');
    }
}
