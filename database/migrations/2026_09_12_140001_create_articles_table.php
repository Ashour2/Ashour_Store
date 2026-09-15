<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Health articles managed from the dashboard and shown on the landing page.
     * name = title (bilingual JSON), description = content (bilingual JSON) — reuses the Trans trait.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // title JSON {ar,en}
            $table->longText('description');   // content JSON {ar,en}
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
