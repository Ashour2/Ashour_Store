<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pharmacy_product', 'is_allowed')) {
            Schema::table('pharmacy_product', function (Blueprint $table) {
                $table->boolean('is_allowed')->default(true)->after('quantity');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pharmacy_product', 'is_allowed')) {
            Schema::table('pharmacy_product', fn (Blueprint $table) => $table->dropColumn('is_allowed'));
        }
    }
};