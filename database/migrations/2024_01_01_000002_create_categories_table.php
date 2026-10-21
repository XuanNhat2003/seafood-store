<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_url', 1000)->nullable();
            // Self-referencing hierarchy: null parent_id => root (parent) category.
            $table->foreignId('parent_id')->nullable()->index()->constrained('categories')->restrictOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
