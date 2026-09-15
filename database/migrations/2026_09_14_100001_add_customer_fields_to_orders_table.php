<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Orders are placed by guests from the public pharmacy site, so the order
     * carries the customer contact details itself and user_id becomes optional.
     *
     * Every column is added only when missing: MySQL cannot roll DDL back inside
     * a transaction, so a half-applied run has to be resumable.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();

            if (! Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->after('id');
            }
            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->after('user_id');
            }
            if (! Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->after('customer_name');
            }
            if (! Schema::hasColumn('orders', 'customer_address')) {
                $table->string('customer_address')->nullable()->after('customer_phone');
            }
            if (! Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('customer_address');
            }
            if (! Schema::hasColumn('orders', 'status')) {
                // new | confirmed | delivered | cancelled
                $table->string('status')->default('new')->after('total');
            }
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();

            if (! Schema::hasColumn('order_details', 'product_name')) {
                // Snapshot of the medicine name at order time (bilingual JSON like products.name)
                $table->string('product_name')->nullable()->after('product_id');
            }
        });

        // Delisting a medicine must not erase the orders it appeared in: the
        // line keeps its price and its name snapshot, only the link is dropped.
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreignId('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->dropColumn('product_name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_number', 'customer_name', 'customer_phone',
                'customer_address', 'note', 'status',
            ]);
        });
    }
};
