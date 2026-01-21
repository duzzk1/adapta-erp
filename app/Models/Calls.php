<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calls extends Model
{
    use HasFactory;

    protected $fillable = [
        'call_date',
        'from',
        'to',
        'direction',
        'status',
        'call_time',
    ];

    protected $table = 'calls';
}
