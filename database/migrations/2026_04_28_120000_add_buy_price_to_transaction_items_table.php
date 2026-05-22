<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        // Tambah kolom buy_price untuk snapshot harga beli saat transaksi
        if (!Schema::hasColumn('transaction_items', 'buy_price')) {
            Schema::table('transaction_items', function (Blueprint $table) {
                $table->decimal('buy_price', 12, 2)->default(0)->after('unit_price');
            });
        }

        // Backfill: isi buy_price dari products.buy_price untuk data yang sudah ada
        DB::statement('
            UPDATE transaction_items ti
            SET buy_price = p.buy_price
            FROM products p
            WHERE ti.product_id = p.id
            AND ti.buy_price = 0
        ');
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn('buy_price');
        });
    }
};
