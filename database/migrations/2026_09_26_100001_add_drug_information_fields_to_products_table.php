<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Add the medicine information shown to customers. */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('scientific_name')->nullable()->after('active_ingredient');
            $table->string('trade_name')->nullable()->after('scientific_name');
            // Bilingual JSON, consistent with the product name and description.
            $table->text('contraindications_warnings')->nullable()->after('trade_name');
        });
    }

    /** Reverse the added medicine information fields. */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'scientific_name',
                'trade_name',
                'contraindications_warnings',
            ]);
        });
    }
};
