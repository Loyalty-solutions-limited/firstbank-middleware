<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Enrollment extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table ="enrollments";

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'loyalty_program_id',
        'loyalty_number',
        'phone_number',
        'email',
         'branch_code',
        'current_bal',
        'member_reference',
        'first_login',
        'first_login_time',
        'terms_agreed',
        'last_change_password',
        'pin',
        'status',
        'password',
        'tier_id',
        'cron_id',
    ];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
       'password',
        'pin'
    ];


    public function LoyaltyProgram(){
        return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id');
    }
    
    public function EmailReportLog(){
        return $this->belongsTo(EmailReportLog::class);
    }


}