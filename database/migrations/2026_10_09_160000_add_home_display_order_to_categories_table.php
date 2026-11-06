<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'home_display_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('home_display_order')->default(999)->after('icon_code');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'home_display_order')) {
            Schema::table('categories', fn (Blueprint $table) => $table->dropColumn('home_display_order'));
        }
    }
};