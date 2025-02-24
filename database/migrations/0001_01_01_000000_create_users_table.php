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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Kullanıcı adı
            $table->string('email')->unique(); // E-posta
            $table->timestamp('email_verified_at')->nullable(); // E-posta doğrulama tarihi
            $table->string('password'); // Şifre alanı, hash'lenmiş olarak kaydedilecek
            $table->rememberToken(); // Kullanıcı oturum hatırlama
            $table->timestamps(); // Oluşturulma ve güncellenme tarihleri
        });

        // Şifre sıfırlama işlemi için token tablosu
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // E-posta adresi
            $table->string('token'); // Şifre sıfırlama token'ı
            $table->timestamp('created_at')->nullable(); // Token oluşturulma zamanı
        });

        // Kullanıcı oturum bilgileri
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Oturum ID'si
            $table->foreignId('user_id')->nullable()->index(); // Kullanıcı ID'si
            $table->string('ip_address', 45)->nullable(); // IP adresi
            $table->text('user_agent')->nullable(); // Kullanıcı ajansı (tarayıcı vb.)
            $table->longText('payload'); // Oturum verisi
            $table->integer('last_activity')->index(); // Son etkinlik zaman damgası
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
