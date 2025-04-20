<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\LoyaltyProgram;
use App\Models\Transaction;
use App\Models\User;

class FetchStatsService{
    
    public function __construct($program_slug)
    {
        $this->prog_slug = strval($program_slug);
        //$this->data = array("name"=>"hi");
        
        
        
    }
    private function getLoyaltyProgramID(){
        
        $program_id = LoyaltyProgram::where('slug', $this->prog_slug)->first();
        return $program_id->id;
        
    }
    
    public function getUsersCount(){
        $users = User::all();
        $this->data['users'] = $users->count();
    }

    public function getCustomersCount(){
        $enrollments = Enrollment::where('loyalty_program_id', $this->getLoyaltyProgramID())->get();
        $this->data['customers'] = $enrollments->count();
    }

    public function getTransactionsCount(){
        $transactions = Transaction::all();
        $this->data['transactions'] = $transactions->count();
    }
    
    function showData(){
        return json_encode($this->data);
    }
}


?>