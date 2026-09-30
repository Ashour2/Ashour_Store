<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Link pharmacist accounts and record who added each medicine. */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('pharmacy_id')->nullable()->after('role_id')
                ->constrained('pharmacies')->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('added_by_id')->nullable()->after('category_id')
                ->constrained('users')->nullOnDelete();
        });

        // Pharmacists operate only their pharmacy's medicine catalogue.
        $pharmacistId = Role::where('slug', Role::PHARMACIST)->value('id');
        if ($pharmacistId) {
            $permissionIds = Permission::whereIn('code', [
                'dashboard.view', 'products.view', 'products.create',
                'products.edit', 'products.delete',
            ])->pluck('id')->all();

            DB::table('permission_role')->where('role_id', $pharmacistId)->delete();
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insert([
                    'role_id' => $pharmacistId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('added_by_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pharmacy_id');
        });
    }
};
