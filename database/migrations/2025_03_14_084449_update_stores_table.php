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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('store_max');
            $table->integer('store_priority');
            $table->boolean('is_active')->default(1); // 1 aktif, 0 pasif
            $table->timestamps(); // Oluşturulma ve güncellenme zaman damgaları
        });
    }




    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('stores', function (Blueprint $table) {
        $table->string('store_name', 255)->change();
        $table->string('store_max', 100)->change();
        $table->integer('store_priority')->change();
    });
}
};
