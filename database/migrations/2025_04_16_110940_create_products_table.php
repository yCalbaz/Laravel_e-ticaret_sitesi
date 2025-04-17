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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name'); 
            $table->string('product_sku')->unique();
            $table->decimal('product_price', 8, 2);
            $table->string('product_image')->nullable();
            $table->text('details')->nullable();
            //$table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();
             // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
