<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Services\HandleBulkUploads;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //
    
    public function add_transaction($member_id, $trans_type, $trans_ref, $service, $amount_in, $amount_out, $balance, $date)
    {
        try 
        { 
            if(!isset($date))
            {
                $date = date('YYYY-MM-DD hh:mm:s');
            }

            $insert = new Transaction();
            $insert->member_id = $member_id;
            $insert->trans_type = $trans_type;
            $insert->trans_ref = $trans_ref;
            $insert->service = $service;
            $insert->amount_in = $amount_in;
            $insert->amount_out = $amount_out;
            $insert->balance = $balance;
            $insert->create_at = $date;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    public function view_transactions($member_id = null, $trans_type = null, $trans_ref = null, $service = null, $from = null, $to = null)
    {
        try 
        { 
            $this->request =  new Request;

            $view = DB::table('transactions');
                    //->select('id', 'member_reference', 'trans_type', 'trans_ref', 'service', 'amount_in', 'amount_out', 'balance', 'date_of_transaction', 'branch_code');
            if(isset($member_id))
            {
                $view->where('member_id', $member_id);
            }

            if(isset($trans_type))
            {
                $view->where('trans_type', $trans_type);
            }

            if(isset($trans_ref))
            {
                $view->where('trans_ref', $trans_ref);
            }

            if(isset($service))
            {
                $view->where('service', $service);
            }

            if(isset($from))
            {
                $view->where('created_at', '>', $from);
            }

            if(isset($to))
            {
                $view->where('created_at', '<', $to);
            }
            if(isset($_GET['limit'])){
                $view->orderBy('id', 'DESC')->limit($_GET['limit']);
            }

            $data = [];
            $data['status'] = true;
            $data['status_code'] = 1;
            $data['data'] = $view->get();

            return $data;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
    }

    public function uploadTransactions(Request $request)
    {
       // print_r($request->all());
        $validator = Validator::make($request->all(), [
            'data' => 'required'
            ]);
    
            
            if ($validator->fails()) {
                return $this->sendBadRequestResponse($validator->errors());
            }
        $classToUpload = new HandleBulkUploads();
        return $classToUpload->uploadTransactionData($request);
        
    }
        
}