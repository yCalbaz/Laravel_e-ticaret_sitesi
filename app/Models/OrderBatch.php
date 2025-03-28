<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBatch extends Model
{
    use HasFactory;

    protected $table = 'order_batches';

    protected $fillable = [
        'id',
        'customer_id',
        'customer_name',
        'customer_address',
        'product_price',
        'updated_at',
        'created_at',
        'order_id',
        'order_status'
    ];
    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'order_batch_id', 'id'); 
    }
}