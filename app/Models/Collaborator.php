<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'role', 'status', 'hired_at',
    ];

    protected $casts = [
        'hired_at' => 'datetime',
    ];
}
