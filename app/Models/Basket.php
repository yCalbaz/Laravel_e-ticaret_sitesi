<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;

    protected $table = 'baskets'; 

    protected $fillable = [
        'customer_id',
        'cart_id',	
        'created_at',	
        'updated_at',	

    ];

    public $timestamps = false;
}

