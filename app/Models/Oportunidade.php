<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oportunidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'company', 'email', 'phone', 'source', 'stage', 'status', 'value', 'probability', 'next_action_at', 'notes', 'score',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'integer',
        'next_action_at' => 'datetime',
        'score' => 'integer',
    ];
}
