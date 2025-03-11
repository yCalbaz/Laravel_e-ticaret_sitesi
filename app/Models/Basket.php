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
        'created_at',	
        'updated_at',
        'is_active',	

    ];

    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(BasketItem::class, 'order_id');
    }
}

