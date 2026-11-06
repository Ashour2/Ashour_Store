<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        $pharmacistRoleId = Role::where('slug', Role::PHARMACIST)->value('id');
        if (! $pharmacistRoleId) return;

        $ids = DB::table('users')->where('role_id', $pharmacistRoleId)->whereNull('pharmacy_id')->pluck('id');
        if ($ids->isEmpty()) return;

        DB::table('sessions')->whereIn('user_id', $ids)->delete();
        DB::table('users')->whereIn('id', $ids)->update([
            'role_id' => null,
            'type' => 'customer',
            'remember_token' => Str::random(60),
        ]);
    }

    public function down(): void { /* Removed roles are intentionally not restored. */ }
};