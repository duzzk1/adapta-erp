<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_key',
        'can_use',
    ];

    protected $casts = [
        'can_use' => 'boolean',
    ];
}
