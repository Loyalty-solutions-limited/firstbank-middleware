<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table ="branches";

    protected $fillable = [
        'company_id',
        'branch_code',
        'branch_name',
        'status',
    ];

    public function transaction(){
        return $this->hasMany(Transaction::class, 'branch_code', 'branch_code');
    }

}