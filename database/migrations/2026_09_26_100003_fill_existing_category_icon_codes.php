<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** Give the categories already in the catalogue a suitable default icon. */
    public function up(): void
    {
        Category::query()
            ->whereNull('icon_code')
            ->orWhere('icon_code', '')
            ->each(function (Category $category) {
                $category->update(['icon_code' => $category->suggestedIconClass()]);
            });
    }

    /** Keep icons when rolling back; they are content chosen from category names. */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
