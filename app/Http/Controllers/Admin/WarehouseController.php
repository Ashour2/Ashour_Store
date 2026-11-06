<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Role, User, Warehouse, WarehouseStock};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount(['stocks', 'orders'])->with('users:id,name,email,warehouse_id')->latest()->paginate(15);
        return view('admin.warehouses.manage', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:150', 'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255', 'manager_name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email', 'password' => 'required|string|min:8|confirmed']);
        DB::transaction(function () use ($data) {
            $warehouse = Warehouse::create(collect($data)->only(['name', 'phone', 'address'])->all());
            User::create(['name' => $data['manager_name'], 'email' => $data['email'], 'password' => $data['password'],
                'role_id' => Role::where('slug', Role::WAREHOUSE)->firstOrFail()->id, 'warehouse_id' => $warehouse->id, 'type' => 'employee']);
        });
        return back()->with('msg', 'تم إنشاء المستودع وحساب المسؤول بنجاح.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate(['name' => 'required|string|max:150', 'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255', 'is_active' => 'required|boolean']);
        $warehouse->update($data);
        return back()->with('msg', 'تم تحديث بيانات المستودع.');
    }

    public function inventory(Request $request)
    {
        $warehouse = $this->ownWarehouse();
        $q = trim((string) $request->input('q'));
        $products = Product::publiclyVisible()->with('category')->when($q !== '', fn ($query) => $query
            ->where(fn ($search) => $search->where('name', 'like', "%{$q}%")->orWhere('barcode', 'like', "%{$q}%")))
            ->orderBy('id')->paginate(20)->withQueryString();
        $stocks = $warehouse->stocks()->whereIn('product_id', $products->modelKeys())->get()->keyBy('product_id');
        return view('admin.warehouses.inventory', compact('warehouse', 'products', 'stocks', 'q'));
    }

    public function saveStock(Request $request, Product $product)
    {
        $warehouse = $this->ownWarehouse();
        abort_unless($product->is_active, 403);
        $data = $request->validate(['price' => 'required|numeric|min:0|max:999999.99',
            'quantity' => 'required|integer|min:0|max:1000000', 'is_available' => 'required|boolean']);
        DB::transaction(function () use ($warehouse, $product, $data) {
            Warehouse::whereKey($warehouse->id)->lockForUpdate()->firstOrFail();
            WarehouseStock::updateOrCreate(['warehouse_id' => $warehouse->id, 'product_id' => $product->id], $data);
        });
        return back()->with('msg', 'تم حفظ سعر الدواء ومخزون المستودع.');
    }

    private function ownWarehouse(): Warehouse
    {
        abort_unless(auth()->user()->isWarehouse() && auth()->user()->warehouse_id, 403);
        return Warehouse::findOrFail(auth()->user()->warehouse_id);
    }
}
