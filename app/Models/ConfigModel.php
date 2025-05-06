<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigModel extends Model
{
    Use HasFactory;
    protected $table = 'config_table';

    protected $fillable=[
        'id',
        'api_name',
        'api_url',
        'created_at',
        'updated_at'
    ];

}
