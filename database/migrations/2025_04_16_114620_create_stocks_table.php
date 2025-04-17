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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('product_sku');
            $table->unsignedBigInteger('store_id');
            $table->integer('product_piece')->default(0);
            $table->unsignedBigInteger('size_id')->nullable();
            $table->timestamps();

            $table->foreign('product_sku')->references('product_sku')->on('products')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            $table->unique(['product_sku', 'store_id', 'size_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
