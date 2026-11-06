<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pharmacy_product', function (Blueprint $table) {
            if (!Schema::hasColumn('pharmacy_product', 'price')) $table->decimal('price', 10, 2)->nullable()->after('quantity');
            if (!Schema::hasColumn('pharmacy_product', 'show_price')) $table->boolean('show_price')->default(true)->after('price');
        });
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'font_family')) $table->string('font_family', 40)->default('cairo')->after('is_published');
            if (!Schema::hasColumn('articles', 'font_size')) $table->string('font_size', 10)->default('medium')->after('font_family');
        });
        if (!Schema::hasTable('pharmacy_suggestions')) {
            Schema::create('pharmacy_suggestions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pharmacy_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('message');
                $table->string('status', 20)->default('new');
                $table->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('pharmacy_suggestions');
        Schema::table('articles', function (Blueprint $table) { $columns=array_filter(['font_family','font_size'],fn($column)=>Schema::hasColumn('articles',$column)); if($columns)$table->dropColumn($columns); });
        Schema::table('pharmacy_product', function (Blueprint $table) { $columns=array_filter(['price','show_price'],fn($column)=>Schema::hasColumn('pharmacy_product',$column)); if($columns)$table->dropColumn($columns); });
    }
};