<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'product_sku',
        'store_id',
        'product_piece',
        'size_id'
    ];

    public $timestamps = true;

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'product_sku');
    }
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id'); 
    }
}