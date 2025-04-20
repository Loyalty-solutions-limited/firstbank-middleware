<?php

namespace App\Imports;

use App\Models\Enrollment;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\EnrolReportLog;
use App\Models\EmailReportLog;
use Illuminate\Support\Facades\DB;

class EnrollmentImport implements ToCollection,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $enrollment = new Enrollment();  
        $records = $rows;
        $data = [];
        $data['payload'] = [];
        $successful_enrollments_count = 0;
        $failed_enrollments_count = 0;
        
        
        $i = 0; 
        $duplicateCount = 0;
        foreach ($rows as $row) 
        {
            $record = $row;
            $password = time();
            $pin =time();
            $random_email = time(). "@noemail.com";
            $record = $row;
            
            
            $check_if_user_exists = Enrollment::where('member_reference', $row['member_reference'])->count();
             if ($check_if_user_exists == 0){
                $array = array('first_name'=>$record['first_name'], 'last_name'=>$record['last_name'],
                'middle_name'=>$record['middle_name'], 'email'=>empty($record['email'])==false ? $record['email'] : $random_email, 'password'=>Hash::make($password),
                'member_reference'=>$record['member_reference'], 'branch_code'=>$record['branch_code'], 'pin'=>Hash::make($pin),
                'tier_id'=>1, 'loyalty_program_id'=>1, 'loyalty_number'=>$record['first_name'] . time(),
                'cron_id'=>1
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
         $data['status'] = 2029;
        return json_encode($data);
    }
   

    public function rules(): array
    {
        return [
        '*.email' => ['email','required','unique:enrollments,email'],
        ];
    }
}