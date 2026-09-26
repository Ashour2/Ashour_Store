<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Replace the unavailable free icon with a Font Awesome Free icon. */
    public function up(): void
    {
        DB::table('categories')
            ->where('icon_code', 'fa-solid fa-stomach')
            ->update(['icon_code' => 'fa-solid fa-bowl-food']);
    }

    public function down(): void
    {
        DB::table('categories')
            ->where('icon_code', 'fa-solid fa-bowl-food')
            ->update(['icon_code' => 'fa-solid fa-stomach']);
    }
};
