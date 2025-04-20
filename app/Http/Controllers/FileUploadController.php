<?php

namespace App\Http\Controllers;

ini_set('memory_limit','2024M');
ini_set('post_max_size','2024M');
ini_set('upload_max_filesize','2024M');
ini_set('max_input_time', 36000); // 10 hours
set_time_limit(36000);


use Illuminate\Http\Request;
use App\Imports\EnrollmentImport;
use App\Imports\TransactionImport;
use Illuminate\Support\Facades\Storage;
use \Maatwebsite\Excel\Facades\Excel;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailReportLog;
class FileUploadController extends Controller
{
    
    //
    public function saveTransactionFile(Request $request){
        $data = [];
       // print_r($request->file()); 
        if ($request->file('data')){
           $save = $request->file('data')->store('transactions'); 
        }
        if ($save){
            $data['status'] = 200;
            $data['message'] = "File Has Been Saved, \n Processing Will Commence In Chunks";
        }else{
            $data['status'] = 2029;
            $data['message'] = "File Could Not Be Saved";
        }
        return $data;
    }

    public function saveCustomerFile(Request $request){
        $data = [];
     
       
        if ($request->file('data')){
           $save = $request->file('data')->store('customers'); 
        }
        if ($save){
            $data['status'] = 200;
            $data['message'] = "File Has Been Saved, \n Processing Will Commence In Chunks";
        }else{
            $data['status'] = 2029;
            $data['message'] = "File Could Not Be Saved";
        }
        return $data;
    }

    public function saveFile(){
        $n = 0;
        $uploadedFiles = scandir(storage_path('app/customers/'));
        $length = count($uploadedFiles);
        if ($length>0){
        
        // $file = time().'_'.request()->data->getClientOriginalName();
        // request()->file('data')->storeAs('customers', $file, 'public');
        for($n = 0; $n< $length; $n++){
            if (pathinfo($uploadedFiles[$n], PATHINFO_EXTENSION) == 'xlsx'){
        
        $rows = \Excel::toArray(new EnrollmentImport, storage_path("app/customers/".$uploadedFiles[$n]));
        $count = count($rows[0]);
        $data = [];
        $data['payload'] = [];
        $successful_enrollments_count = 0;
        $failed_enrollments_count = 0;
        $i = 0; 
        $duplicateCount = 0;
       // print_r($rows[0]);
        for($i=0; $i < $count; $i++){
            if ($i == ($count - 1)){
                $this->pushToBeDeleted(storage_path("app/customers/".$uploadedFiles[$n]));
            }else{
            $record = $rows[0][$i];
            $password = time();
            $pin = time() + time();
            $random_email = time(). "-" . $record['middle_name'] ."@noemail.com";
            // $record = $row;
            
            
            $check_if_user_exists = Enrollment::where('member_reference', $record['member_reference'])->count();
             if ($check_if_user_exists == 0){
                $array = array('first_name'=>$record['first_name'], 'last_name'=>$record['last_name'],
                'middle_name'=>$record['middle_name'], 'email'=>empty($record['email'])==false ? $record['email'] : $random_email, 'password'=>Hash::make($password),
                'member_reference'=> 'FB_' .$record['member_reference'], 'branch_code'=>$record['branch_code'], 'pin'=>Hash::make($pin),
                'tier_id'=>1, 'loyalty_program_id'=>1, 'loyalty_number'=>time(),
                'cron_id'=>time()
            );
            //}
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
                        "status" => 0, 'subject'=>'Enrolment Email');
                        EmailReportLog::create($array2);
                    }
                    
                }else{
                    $failed_enrollments_count++;
                     array_push($data['payload'], array('data'=>" " . $record['first_name'] ." " .$record['last_name'] . " with ref: " . $record['member_reference'] . " failed to insert"));
                     $data['failure_count'] = $failed_enrollments_count; 
                     $data['success_count'] = $successful_enrollments_count;
                }  $status_code = 200;
            }else{
                $data['message'] = "Attempt to upload " . $duplicateCount++ . " duplicate records rolled back";
                $status_code = 3001;
            }
        }

            $i++;
        }
    }
    }
         $data['status'] = 2029;
        return json_encode($data);
    }
}

    public function saveTransactionData(){
        $uploadedFiles = scandir(storage_path('app/transactions/'));
       
        $n = 0;
        $length = count($uploadedFiles);
        
        if ($length>0){
       for($n = 0; $n< $length; $n++){
            if (pathinfo($uploadedFiles[$n], PATHINFO_EXTENSION) == 'xlsx'){
              
        $rows = \Excel::toArray(new TransactionImport, storage_path("app/transactions/".$uploadedFiles[$n]));
        $count = count($rows[0]);
        
        $data = [];
        $data['payload'] = [];
        $status_code = 0;
        $successful_uploads_count = 0;
        $failed_uploads_count = 0;
        $i = 0; 
        $duplicateCount = 0;
        for($i=0; $i < $count; $i++){
            if ($i == ($count - 1)){
                $this->pushToBeDeleted(storage_path("app/transactions/".$uploadedFiles[$n]));
            }else{
            $record = $rows[0][$i]; 
            $check_if_user_exists = Transaction::where('transaction_reference', $record['transaction_reference'])->count();
            if ($check_if_user_exists == 0){
                $trans_date = isset($record['transaction_date'])==true?$record['transaction_date']:$record['Date'];
                $UNIX_DATE = ($trans_date  - 25569) * 86400;
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
                $status_code = 200;
            }
            else{
                $data['message'] = "Attempt to upload " . $duplicateCount++ . " duplicate records rolled back";
                $status_code = 3001;
            }
            
           
            
        }
        
       
    }
}

}
$data['status'] = $status_code;
return json_encode($data);
        }
        
        
    }  
        

    public function pushToBeDeleted($x){
        Storage::delete($x);
    }
}   