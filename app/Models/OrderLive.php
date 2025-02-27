<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLive extends Model
{
    use HasFactory;

    protected $table = 'order_lives';

    protected $fillable = [
        'product_sku',
        'product_name',
        'store_id',
        'product_piece',
        'updated_at',
        'created_at',
    ];
}