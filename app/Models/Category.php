<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    protected $fillable = [
        'category_name', 
        'category_slug', 
        ];

        public function products()
        {
            return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
        }
    
        public function subcategories()
        {
            return $this->hasMany(CategorySub::class, 'category_id');
        }
    
        public function parentCategories()
        {
            return $this->hasMany(CategorySub::class, 'parent_id');
        }
        public $timestamps=false;
}