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
        Schema::create('members', function (Blueprint $table) {
            $table->id(); // ID sütunu
            $table->string('name'); // Üye adı
            $table->string('email')->unique(); // Üye e-posta adresi (benzersiz)
            $table->string('password'); // Şifre
            $table->timestamps(); // Created_at ve updated_at sütunları
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members'); // Eğer tablo varsa siler
    }
};
