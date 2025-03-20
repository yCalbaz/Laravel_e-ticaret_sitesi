<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySub extends Model
{
    use HasFactory;

    protected $table = 'category_sub';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}