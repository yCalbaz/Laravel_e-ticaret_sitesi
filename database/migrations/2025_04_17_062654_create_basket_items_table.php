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
        Schema::create('basket_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_sku');
            $table->decimal('product_price', 8, 2);
            $table->integer('product_piece')->unsigned();
            $table->string('product_image')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('size_id');

            $table->foreign('order_id')->references('id')->on('baskets')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes');
            $table->index('order_id');
            $table->index('product_sku');
            $table->index('size_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basket_items');
    }
};
