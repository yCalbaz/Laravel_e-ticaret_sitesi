<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCanceled extends Model
{
    use HasFactory;

    protected $table = 'order_canceled';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'customer_id',
        'product_price',
        'product_sku',
        'details',
    ];
    
    
}