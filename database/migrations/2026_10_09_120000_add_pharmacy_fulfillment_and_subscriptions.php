<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'pharmacy_id')) {
                $table->foreignId('pharmacy_id')->nullable()->after('user_id')->constrained('pharmacies')->nullOnDelete();
            }
        });

        Schema::table('pharmacies', function (Blueprint $table) {
            if (! Schema::hasColumn('pharmacies', 'subscription_plan')) $table->string('subscription_plan', 60)->nullable()->after('is_active');
            if (! Schema::hasColumn('pharmacies', 'subscription_status')) $table->string('subscription_status', 30)->default('active')->after('subscription_plan');
            if (! Schema::hasColumn('pharmacies', 'subscription_ends_at')) $table->date('subscription_ends_at')->nullable()->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'pharmacy_id')) $table->dropConstrainedForeignId('pharmacy_id');
        });
        Schema::table('pharmacies', function (Blueprint $table) {
            $columns = array_filter(['subscription_plan', 'subscription_status', 'subscription_ends_at'], fn ($column) => Schema::hasColumn('pharmacies', $column));
            if ($columns) $table->dropColumn($columns);
        });
    }
};