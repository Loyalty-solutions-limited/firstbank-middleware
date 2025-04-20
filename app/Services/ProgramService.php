<?php
namespace App\Services;

use App\Models\LoyaltyProgram;

class ProgramService{
    
    public function getProgramName($company_id){
        $program = LoyaltyProgram::where('company_id', $company_id)->first();
        if($program){
            return $program->name;
        }else{
            return null;
        }
    }
}

?>