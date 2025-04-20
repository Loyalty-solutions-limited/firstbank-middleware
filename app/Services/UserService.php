<?php

namespace App\Services;

use App\Models\User;
use App\Models\Enrollment;

class UserService{

    public function __construct($enrollment_id)
    {
        $user = Enrollment::where('enrolment_id', $enrollment_id)->get();
        return $user;
    }

    public static function getDetails($membership_id){
        $user = Enrollment::where('member_reference', $membership_id)->first();
        return $user; 
    }
}