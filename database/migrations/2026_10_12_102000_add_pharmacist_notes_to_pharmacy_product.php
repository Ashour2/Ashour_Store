<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if(!Schema::hasColumn('pharmacy_product','notes')) Schema::table('pharmacy_product',fn(Blueprint $table)=>$table->text('notes')->nullable()->after('show_price')); } public function down(): void { if(Schema::hasColumn('pharmacy_product','notes')) Schema::table('pharmacy_product',fn(Blueprint $table)=>$table->dropColumn('notes')); } };