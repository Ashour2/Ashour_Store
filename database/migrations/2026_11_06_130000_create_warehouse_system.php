<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\{Permission, Role};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::table('users', fn (Blueprint $table) => $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete());
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['warehouse_id', 'product_id']);
        });
        Schema::create('wholesale_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->uuid('request_token')->unique();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('pharmacy_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pharmacy_name');
            $table->string('pharmacy_phone', 30)->nullable();
            $table->string('delivery_address');
            $table->string('status', 30)->default('new');
            $table->decimal('total', 14, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('wholesale_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wholesale_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('total', 14, 2);
        });
        $manage = Permission::firstOrCreate(['code' => 'warehouses.manage'], ['name' => '{"ar":"إدارة المستودعات","en":"Manage warehouses"}', 'group' => 'warehouses']);
        $dashboard = Permission::firstOrCreate(['code' => 'dashboard.view'], ['name' => '{"ar":"عرض لوحة التحكم","en":"View dashboard"}', 'group' => 'dashboard']);
        $role = Role::firstOrCreate(['slug' => 'warehouse'], ['name' => '{"ar":"مستودع أدوية","en":"Warehouse"}', 'is_locked' => false]);
        $role->permissions()->syncWithoutDetaching([$dashboard->id]);
        foreach (Role::whereIn('slug', [Role::ADMIN, Role::SUPER_ADMIN])->get() as $manager) {
            $manager->permissions()->syncWithoutDetaching([$manage->id]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wholesale_order_items');
        Schema::dropIfExists('wholesale_orders');
        Schema::dropIfExists('warehouse_stocks');
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('warehouse_id'));
        Schema::dropIfExists('warehouses');
    }
};
