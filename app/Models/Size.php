<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Size extends Model
{
    use HasFactory;

    protected $table = 'sizes';

    protected $fillable = [
        'size_name'
    ];
    public function orderLines()
        {
            return $this->hasMany(OrderLine::class, 'product_size_id');
        }

    public $timestamps = false;
}