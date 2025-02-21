<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // 🔹 Authenticatable Kullanıldı
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable // 🔹 Modeli Authenticatable'dan türettik
{
    use HasFactory, Notifiable;

    protected $table = 'members'; 

    public $timestamps = false; // 🔹 `created_at` ve `updated_at` otomatik eklenmesin

    protected $fillable = ['email', 'password'];

    protected $hidden = ['password']; // 🔹 Şifreyi gizli tut

    // 🔹 Şifreyi otomatik olarak hash'leme
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
