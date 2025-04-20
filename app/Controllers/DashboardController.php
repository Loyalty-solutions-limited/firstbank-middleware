<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index(){
         try{
             $output = $this->get_dashboard();
             if(!$output){
                 return $this->sendBadRequestResponse('Error', 'Could not get data');
             }

             return $this->sendSuccessResponse('Success', $output);
         }
        catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function get_dashboard(){
        $res = [];
        $result = User::all()->count();
        $data = Enrollment::all()->count();
        $trans = Transaction::all();
        $res['usersCount'] = $result;
        $res['customerCount'] = $data;
        $res['transactionCount'] = $trans->count();
        $res['transactions'] = $trans;
        
        return $res;
    }
}
