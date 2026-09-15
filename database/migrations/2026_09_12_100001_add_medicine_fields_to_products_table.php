<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds pharmacy-specific fields so a product can represent a medicine.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('active_ingredient')->nullable()->after('description');
            $table->string('manufacturer')->nullable()->after('active_ingredient');
            $table->string('dosage_form')->nullable()->after('manufacturer');
            // recommended_dosage is bilingual JSON like name/description
            $table->text('recommended_dosage')->nullable()->after('dosage_form');
            $table->boolean('requires_prescription')->default(false)->after('recommended_dosage');
            $table->string('barcode')->nullable()->after('requires_prescription');
            $table->date('expiry_date')->nullable()->after('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'active_ingredient',
                'manufacturer',
                'dosage_form',
                'recommended_dosage',
                'requires_prescription',
                'barcode',
                'expiry_date',
            ]);
        });
    }
};
