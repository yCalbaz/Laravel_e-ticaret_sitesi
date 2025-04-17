<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    protected $fillable = [
        'store_name',
        'store_max',
        'store_priority',
        'is_active'
        
    ];

    public $timestamps = false;
    public function orderLines()
    {
        return $this->belongsTo(OrderLine::class, 'store_id');
    }
    public function members()
{
    return $this->belongsToMany(Member::class, 'member_store', 'store_id', 'member_id');
}
}