<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('transactions')) {
            return;
        }

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('invoice_no')->unique();
            $table->decimal('total_amount', 14, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('paid_amount', 14, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'qris']);
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date');
            $table->timestamps();
 
            $table->index('transaction_date');
            $table->index(['user_id', 'transaction_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('transactions'); }
};
