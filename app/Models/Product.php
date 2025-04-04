<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; 

    protected $fillable = [
        'product_name',
        'product_sku',
        'product_price', 
        'product_image',
        'details',
        'category_id'
    ];

    public $timestamps = false;
    
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_sku', 'product_sku');
    }
    public function orderLines()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'product_sku');
    }
}

