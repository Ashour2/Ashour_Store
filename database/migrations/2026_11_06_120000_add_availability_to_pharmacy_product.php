<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pharmacy_product', 'is_available')) {
            Schema::table('pharmacy_product', fn (Blueprint $table) => $table->boolean('is_available')->default(true));
        }
    }

    public function down(): void
    {
        Schema::table('pharmacy_product', fn (Blueprint $table) => $table->dropColumn('is_available'));
    }
};
