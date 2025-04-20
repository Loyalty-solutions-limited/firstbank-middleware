<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usergroup extends Model
{
    use HasFactory;

    protected $table = "user_groups";

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function userID(){
        return $this->hasOne(User::class);
    }
}
