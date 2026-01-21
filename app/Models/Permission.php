<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
    ];

    public function userGroups()
    {
        return $this->belongsToMany(UserGroup::class, 'group_permission', 'permission_id', 'group_id');
    }
}
