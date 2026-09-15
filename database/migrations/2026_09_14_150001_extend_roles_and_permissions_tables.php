<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flesh out the roles/permissions scaffolding into a usable system:
     * roles get a stable slug and a bilingual name, permissions get a unique
     * code and a feature group so the matrix can be laid out by area.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('slug')->unique()->after('id');
            // Locked roles (the owner's) cannot be edited or deleted
            $table->boolean('is_locked')->default(false)->after('name');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group')->after('code');
            $table->unique('code');
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->unique(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropUnique(['permission_id', 'role_id']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('group');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_locked']);
        });
    }
};
