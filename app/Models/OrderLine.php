<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use HasFactory;

    protected $table = 'order_lines';

    protected $fillable = [
        'product_sku',
        'product_name',
        'store_id',
        'updated_at',
        'created_at',
        'order_id',
        'order_batch_id',
        'quantity',
        'order_status',
        'product_size'
    ];
    
    public function orderBatch()
    {
        return $this->belongsTo(OrderBatch::class, 'order_batch_id', 'id'); 
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'product_sku');
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    
}