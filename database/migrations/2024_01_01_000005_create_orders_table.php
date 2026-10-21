<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_code', 50)->unique();
            $table->string('full_name');
            $table->string('phone', 30);
            $table->text('shipping_address');
            $table->text('note')->nullable();
            $table->string('payment_method', 20)->default('cod');    // cod | bank | vnpay
            $table->string('payment_status', 20)->default('unpaid'); // unpaid | paid
            $table->string('status', 20)->default('pending');        // pending | confirmed | shipping | delivered | cancelled
            $table->decimal('sub_total', 18, 2)->default(0);         // sum of items before discount
            $table->decimal('discount_amount', 18, 2)->default(0);   // amount discounted via voucher
            $table->string('voucher_code', 50)->nullable();          // applied voucher code (snapshot)
            $table->decimal('total_amount', 18, 2)->default(0);      // final payable = sub_total - discount_amount
            // VNPay payment info
            $table->string('vnpay_txn_ref', 100)->nullable();
            $table->string('vnpay_transaction_no', 100)->nullable();
            $table->string('vnpay_bank_code', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('product_name');
            $table->string('product_image', 1000)->nullable();
            $table->integer('quantity');
            $table->decimal('price', 18, 2);
            $table->string('unit', 50)->default('kg');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
}
