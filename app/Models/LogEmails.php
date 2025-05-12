<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEmails extends Model
{
    use HasFactory;

    protected $table = "email_logs";

    protected $fillable = [
        'membership_id',
        'email_type',
        'subject',
        'body',
        'trans_ref',
        'email',
        'cron_id',
        'tries',
        'first_name',
        'last_name',
        'status'
    ];
}