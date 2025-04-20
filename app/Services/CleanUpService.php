<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CleanUpService{
    
    // public function cleanEnrolments(){
    //     return DB::table('users')->where('enrollment_status', '>', 0)->delete();
    // }

    public function cleanTransactions(){
        return DB::table('transactions')->where('status', 1)->delete();
    }

    public function clearEmailLogs(){
        return DB::table('pending_emails')->where('status', 1)->delete();
    }

    
}
?>