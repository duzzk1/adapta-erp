<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobProgress extends Model
{
    protected $table = 'job_progresses';

    protected $fillable = [
        'run_id',
        'job',
        'status',
        'current',
        'total',
        'message',
    ];
}
