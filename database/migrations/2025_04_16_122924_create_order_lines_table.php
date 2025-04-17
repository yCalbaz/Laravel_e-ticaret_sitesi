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
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->string('product_sku');
            $table->string('product_name');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('order_batch_id');
            $table->string('order_id'); 
            $table->integer('quantity')->default(1);
            $table->string('order_status')->default('sipariş alındı');
            $table->unsignedBigInteger('product_size_id')->nullable();
            $table->timestamps();

            
            $table->foreign('product_sku')->references('product_sku')->on('products')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            //$table->foreign('order_batch_id')->references('id')->on('order_batches')->onDelete('cascade');
            $table->foreign('product_size_id')->references('id')->on('sizes')->onDelete('set null');

           
            $table->index('order_batch_id');
            $table->index('product_sku');
            $table->index('store_id');
            $table->index('product_size_id');
            $table->index('order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
