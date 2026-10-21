<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersContactsVouchersReviewsTables extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_url', 1000);
            $table->string('link', 1000)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->decimal('discount_amount', 18, 2);
            $table->string('discount_type', 20)->default('fixed'); // fixed | percentage
            $table->decimal('min_order_amount', 18, 2)->default(0);
            $table->dateTime('expiry_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->index()->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('user_name');           // snapshot of reviewer name
            $table->unsignedTinyInteger('rating'); // 1..5 stars
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('banners');
    }
}
