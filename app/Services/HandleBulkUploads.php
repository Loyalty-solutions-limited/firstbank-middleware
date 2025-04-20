<?php

namespace App\Services;

use App\Models\EmailReportLog;
use App\Models\Enrollment;
use App\Models\EnrolReportLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HandleBulkUploads
{

    public function uploadEnrolmentData($request)
    {
        $enrollment = new Enrollment();  
        $records = $request->data;
        $data = [];
        $data['payload'] = [];
        $successful_enrollments_count = 0;
        $failed_enrollments_count = 0;
        //$emailProps = [];
        $i = 0; 
        $duplicateCount = 0;
        while($i < count($records)){
           
            $password = time();
            $pin = time();
            $random_email = time(). "@noemail.com";
            $record = $records[$i];
            
            $check_if_user_exists = Enrollment::where('member_reference', $record['member_reference'])->count();
            if ($check_if_user_exists == 0){
                $array = array('first_name'=>$record['first_name'], 'last_name'=>$record['last_name'],
                'middle_name'=>$record['middle_name'], 'email'=>empty($record['email'])==false ? $record['email'] : $random_email, 'password'=>Hash::make($password),
                'member_reference'=>$record['member_reference'], 'branch_code'=>$record['branch_code'], 'pin'=>Hash::make($pin),
                'tier_id'=>1, 'loyalty_program_id'=>1, 'loyalty_number'=>$record['first_name'] .time(),
                'cron_id'=>1
            );
               
                $enrol =  Enrollment::create($array);
                if($enrol){
                    $successful_enrollments_count++;
                     array_push($data['payload'], array('data'=>" " . $record['first_name'] ." " .$record['last_name'] . " with ref: " . $record['member_reference'] . " " . empty($record['email'])==false ? $record['email'] : $random_email . " successfully inserted"));
                     $data['success_count'] = $successful_enrollments_count;
                     $data['failure_count'] = $failed_enrollments_count; 
                    $LogMail = new EmailReportLog();
                    if(EmailReportLog::where('enrollment_id', $enrol->id)->count()== 0 ){
                        $LogMail->email_body;
                         $array2 = array("email_body"=>$record['first_name'] .  $record['last_name'] . " you have been successfully enrolled for the fidelity loyalty program with ref: " . $record['member_reference'] . ", password: $password, pin: $pin",
                        'email' => empty($record['email'])==false ? $record['email'] : $random_email,
                        "enrollment_id" => $enrol->id,
                        "status" => 0);
                        EmailReportLog::create($array2);
                    }
                    
                }else{
                    $failed_enrollments_count++;
                     array_push($data['payload'], array('data'=>" " . $record['first_name'] ." " .$record['last_name'] . " with ref: " . $record['member_reference'] . " failed to insert"));
                     $data['failure_count'] = $failed_enrollments_count; 
                     $data['success_count'] = $successful_enrollments_count;
                }

               $status_code = 200;
            }else{
                $data['message'] = "Attempt to upload " . $duplicateCount++ . " duplicate records rolled back";
                $status_code = 3001;
            }
            
            $i++;
        
        }
        $data['status'] = 2029;
        return json_encode($data);
        
       
    }

    public function uploadTransactionData($request){
        $records = $request->data;
        $data = [];
        $data['payload'] = [];
        $successful_uploads_count = 0;
        $failed_uploads_count = 0;
        //$emailProps = [];
        $i = 0; 
        $duplicateCount = 0;
        while($i < count($records)){
           $record = $records[$i];
            $check_if_user_exists = Transaction::where('transaction_reference', $record['transaction_reference'])->count();
            if ($check_if_user_exists == 0){
                $UNIX_DATE = ($record['transaction_date']  - 25569) * 86400;
                $transaction_date = gmdate("d-m-Y H:i:s", $UNIX_DATE);
                
                $explode_transaction_date = explode(' ',$transaction_date);
                $new_transaction_date = explode('-', $explode_transaction_date[0]);
                $new_transaction_date = $new_transaction_date[2] . "-" . $new_transaction_date[1] . '-' . $new_transaction_date[0];
                $transaction_date = $new_transaction_date . ' ' . $explode_transaction_date[1];  
                //$transaction_date = $explode_transaction_date[2] .'-'. $explode_transaction_date[1] . '-' . $explode_transaction_date[0]; 
                $record['quantity'] = 0;
                $array = array('member_reference'=>$record['member_reference'], 'product_code'=>$record['product_code'],
                 'quantity'=>$record['quantity'] , 'transaction_type'=>0, 'channel'=>$record['channel'],
                 'amount'=>$record['amount'], 'branch_code'=>$record['branch_code'], 'transaction_reference'=>$record['transaction_reference'],
                 'cron_id'=>1, 'transaction_log_id' =>0, 'transaction_date'=>$transaction_date, 'account_number'=>$record['account_number'],
                );
                       
                $create_transaction =  Transaction::create($array);
                if($create_transaction){
                    $successful_uploads_count++;
                     array_push($data['payload'], array('data'=>" ". $record['transaction_reference'] . " was successfully uploaded" ));
                     $data['success_count'] = $successful_uploads_count;
                     $data['failure_count'] = $failed_uploads_count;
                
                } else{
                    $failed_uploads_count++;
                    array_push($data['payload'], array('data'=>" ". $record['transaction_reference'] . " failed to upload" ));
                     $data['failure_count'] = $failed_uploads_count; 
                     $data['success_count'] = $successful_uploads_count;
                }
            }
            else{
                $data['message'] = "Attempt to upload " . $duplicateCount++ . " duplicate records rolled back";
                $status_code = 3001;
            }
            
            $i++;
            
        }
        $data['status'] = $status_code;
        return json_encode($data);
    }
    
}


?>