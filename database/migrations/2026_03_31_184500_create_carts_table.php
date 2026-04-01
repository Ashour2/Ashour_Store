<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            //  id | user_id | product_id | price | quentity | total

            $table->double('price',10,3);
            $table->integer('quantity');

            $table->foreignId('user_id')
            ->constrained('users','id')->cascadeOnDelete();

            $table->foreignId('product_id')
            ->constrained('products')->cascadeOnDelete();
            $table->double('total',10,3);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
