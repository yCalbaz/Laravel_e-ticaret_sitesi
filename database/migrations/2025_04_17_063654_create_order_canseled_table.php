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
        Schema::create('order_canceled', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('product_price', 8, 2)->nullable();
            $table->string('product_sku')->nullable();
            $table->text('details')->nullable();
            $table->text('return_address')->nullable();

            $table->foreign('order_id')->references('id')->on('order_batches')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_canceled');
    }
};
