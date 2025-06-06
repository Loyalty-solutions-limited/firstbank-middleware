<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table ="QUALIFIED_TRANSACTIONS";
    // protected $table ="transactions";

    protected $guarded = ['id'];

    public function branch()
    {
        return $this->hasMany(Branch::class, 'branch_code', 'branch_code');
    }


}