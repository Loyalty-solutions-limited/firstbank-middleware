<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Exports\TransactionReportExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportManagementService extends TransactionReportExport{

    public function __construct(){

    }

    protected static function fetchAllLogMembers($log_id){
        $reportStats = array();
        $placeholder = array('$count','$created_at','$date_from','$date_to', '$successful', '$pending', '$failed');
        
        $reportStats['all_transactions_count'] = Transaction::where('transaction_log_id', $log_id)->count();
        $reportStats['successful_transactions_count'] = Transaction::where('transaction_log_id', $log_id)->where('status', 1)->count();
        $reportStats['pending_transactions_count'] = Transaction::where('transaction_log_id', $log_id)->where('status', 0)->count();
        $reportStats['failed_transactions_count'] = Transaction::where('transaction_log_id', $log_id)->where('status', 3)->count();
        $transaction_from = Transaction::where('transaction_log_id', $log_id)->select('transaction_date')->first();
        $transaction_to = Transaction::where('transaction_log_id', $log_id)->select('transaction_date')->orderBy('id', 'desc')->first();
        $created_at = Transaction::where('transaction_log_id', $log_id)->select('created_at')->orderBy('id', 'desc')->first();
        $values = array($reportStats['all_transactions_count'], $created_at['created_at'], $transaction_from['transaction_from'], $transaction_to['transaction_to'], $reportStats['successful_transactions_count'],
        $reportStats['pending_transactions_count'], $reportStats['failed_transactions_count']);
        $str = EmailDispatcher::BuildReportTemplate($placeholder, $values);
        return $str;

    }

    public function generateTransactionReport(){
        $transactions = Transaction::where('status', '<', 5)->groupBy('transaction_log_id')->select('transaction_log_id')->get();
        foreach($transactions as $transaction){
            $template = self::fetchAllLogMembers($transaction->transaction_log_id);
            
            if(Excel::store(new TransactionReportExport($transaction->transaction_log_id), $transaction->transaction_log_id.'_reportfile.xlsx')){  
                storage_path($transaction->transaction_log_id.'_reportfile.xlsx');
                $template;
                $data = array("sender"=>
                env('FROM_EMAIL'), 'from'=>'Fidelity Bank Green Rewards',
                 "subject"=>"Migration Report", "to"=>"ojiodujoachim@gmail.com", "body"=>$template, 'bcc'=>'joachim@loyaltysolutionsnigeria.com', 'attachment'=>storage_path($transaction->transaction_log_id.'_reportfile.xlsx'));
                EmailDispatcher::testCurl(http_build_query($data));
            }else{
                return 0;
            }
            
        }
    }


}