<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Store a consistent, line-style icon key for every existing category. */
    public function up(): void
    {
        $icons = [
            'fa-solid fa-temperature-half' => 'line:thermometer',
            'fa-solid fa-capsules' => 'line:capsule',
            'fa-solid fa-tablets' => 'line:vitamins',
            'fa-solid fa-bowl-food' => 'line:stomach',
            'fa-solid fa-head-side-cough' => 'line:cough',
            'fa-solid fa-pump-soap' => 'line:skin',
            'fa-solid fa-heart-pulse' => 'line:heart',
            'fa-solid fa-droplet' => 'line:diabetes',
            'fa-solid fa-baby' => 'line:baby',
            'fa-solid fa-tooth' => 'line:tooth',
        ];

        foreach ($icons as $from => $to) {
            DB::table('categories')->where('icon_code', $from)->update(['icon_code' => $to]);
        }
    }

    public function down(): void
    {
        // The icon keys are presentation content and should remain if rolled back.
    }
};
