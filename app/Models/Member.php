<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'members';

    protected $fillable = [
        'name', 
        'email', 
        'password',
        'authority_id',
        'customer_id',
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];
}
