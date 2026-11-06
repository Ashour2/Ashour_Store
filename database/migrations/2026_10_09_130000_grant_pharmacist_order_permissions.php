<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roleId = DB::table('roles')->where('slug', 'pharmacist')->value('id');
        if (! $roleId) return;
        $permissionIds = DB::table('permissions')->whereIn('code', ['orders.view', 'orders.update'])->pluck('id');
        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $permissionId]);
        }
    }
    public function down(): void {}
};