<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyProgram extends Model
{
    use HasFactory;

    protected $table ="loyalty_programs";

    protected $fillable = [
        'company_id',
        'name',
        'currency_name',
        'image_url',
        'status',
    ];


    public function enrollment(){
        return $this->hasMany(Enrollment::class);
    }
    
    public function company(){
        return $this->belongsTo(Company::class);
    }
}