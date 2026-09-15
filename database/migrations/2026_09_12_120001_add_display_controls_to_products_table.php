<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-product display controls: whether to show the price and how to
     * present the stock on the public site.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_price')->default(true)->after('price');
            // quantity | available | hidden
            $table->string('stock_display')->default('quantity')->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['show_price', 'stock_display']);
        });
    }
};
