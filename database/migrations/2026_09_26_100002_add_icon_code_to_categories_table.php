<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Add an optional Font Awesome icon code for each category. */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon_code', 100)->nullable()->after('description');
        });
    }

    /** Remove the optional category icon code. */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('icon_code');
        });
    }
};
