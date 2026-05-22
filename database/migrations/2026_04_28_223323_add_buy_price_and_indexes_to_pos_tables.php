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
        Schema::table('transaction_items', function (Blueprint $table) {
            // Adding index to product_id
            $table->index('product_id');
        });

        // transaction_date index already created in create_transactions_table migration
        // Schema::table('transactions', function (Blueprint $table) {
        //     $table->index('transaction_date');
        // });
    }

    public function down(): void
    {
        // Schema::table('transactions', function (Blueprint $table) {
        //     $table->dropIndex(['transaction_date']);
        // });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });
    }
};
