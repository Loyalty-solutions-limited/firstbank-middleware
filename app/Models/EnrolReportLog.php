<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnrolReportLog extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "enrolreport_log";

    public function branch(){
    return $this->hasMany(Branch::class);    
    }
}
