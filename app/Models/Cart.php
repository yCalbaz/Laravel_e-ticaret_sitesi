<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts'; 

    protected $fillable = [
        'product_name',
        'product_sku',
        'product_price', 
        'product_image',
        'product_piece'
    ];

    public $timestamps = false;
}

