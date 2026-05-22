<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // products: stock_qty, stock_min → decimal + add unit column
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock_qty', 10, 3)->default(0)->change();
            $table->decimal('stock_min', 10, 3)->default(5)->change();
            $table->string('unit', 20)->default('pcs')->after('stock_min');
        });

        // transaction_items: qty → decimal
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->decimal('qty', 10, 3)->change();
        });

        // purchase_items: qty → decimal
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('qty', 10, 3)->change();
        });

        // stock_opname_items: system_qty, actual_qty, difference → decimal
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->decimal('system_qty', 10, 3)->change();
            $table->decimal('actual_qty', 10, 3)->change();
            $table->decimal('difference', 10, 3)->change();
        });

        // stock_movements: qty_before, qty_change, qty_after → decimal
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('qty_before', 10, 3)->change();
            $table->decimal('qty_change', 10, 3)->change();
            $table->decimal('qty_after', 10, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_qty')->default(0)->change();
            $table->integer('stock_min')->default(5)->change();
            $table->dropColumn('unit');
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->integer('qty')->change();
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->integer('qty')->change();
        });

        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->integer('system_qty')->change();
            $table->integer('actual_qty')->change();
            $table->integer('difference')->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->integer('qty_before')->change();
            $table->integer('qty_change')->change();
            $table->integer('qty_after')->change();
        });
    }
};
