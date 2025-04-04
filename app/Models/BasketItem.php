<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasketItem extends Model
{
    use HasFactory;

    protected $table = 'basket_items'; 

    protected $fillable = [
        'product_name',
        'product_sku',
        'product_price', 
        'product_piece',
        'product_image',
        'order_id',
        'size_id'
    ];

    public $timestamps = false;

    public function baskets()
    {
        return $this->belongsTo(Basket::class, 'order_id'); 
    }
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id'); 
    }
}

