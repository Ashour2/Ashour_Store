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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // id | product_id | user_id | star |comment
            $table->foreignId('product_id')->nullable()->
            constrained('products','id')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->
            constrained('users','id')->nullOnDelete();
            $table->integer('star');
            $table->text('comment');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
