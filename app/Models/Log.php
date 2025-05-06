<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    Use HasFactory;
    protected $table= 'logs';
    
    protected $fillable=[
        'id',
        'log_title',
        'operaton',
        'message',
        'error',
        'success',
        'request',
        'response',
        'created_at',
        'uptdated_at'
    ];
}
