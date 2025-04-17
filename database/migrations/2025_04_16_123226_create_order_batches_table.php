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
        Schema::create('order_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name');
            $table->string('customer_address');
            $table->decimal('product_price', 8, 2)->default(0.00);
            $table->string('order_id')->unique()->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('order_id');

            $table->foreign('customer_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_batches');
    }
};
