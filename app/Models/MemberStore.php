<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MemberStore extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'member_store';

    protected $fillable = [
        'member_id', 
        'store_id', 
    ];

    

    public function stores()
{
    return $this->belongsToMany(Store::class, 'member_store', 'member_id', 'store_id');
}
}
