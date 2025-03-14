<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('stores', function (Blueprint $table) {
        $table->string('store_name', 255)->change();
        $table->string('store_max', 100)->change();
        $table->integer('store_priority')->change();
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
