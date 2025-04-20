<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_group_privilege extends Model
{
    use HasFactory;

    protected $table ="user_group_privileges";

    protected $fillable = [
        'usergroup_id',
        'route_id',
        'create',
        'read',
        'edit',
        'delete',
    ];

}
