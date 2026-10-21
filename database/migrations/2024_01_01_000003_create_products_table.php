<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->index()->constrained('categories')->restrictOnDelete();
            $table->string('product_code', 50)->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 18, 2);
            $table->decimal('sale_price', 18, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('unit', 50)->default('kg');
            $table->string('image_url', 1000)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('image_url', 1000);
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
}
