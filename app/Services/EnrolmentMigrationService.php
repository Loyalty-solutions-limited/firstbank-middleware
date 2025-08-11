<?php

namespace App\Services;

//ini_set('memory_limit', '128M');

use GuzzleHttp\Client;

use App\Models\LogEmails;

use App\Models\Enrollment;

use Illuminate\Support\Str;

use App\Models\EnrolReportLog;

use Illuminate\Mail\PendingMail;
use App\Services\EmailDispatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Database\Migrations\Migration;



class EnrolmentMigrationService extends MigrationService{

  public static $username, $password;

  public static $key = '!QAZXSW@#EDCVFR$';

  public static $iv = '1234567891011121'; //'5666685225155700';

  public static $placeholders = array('$first_name', '$last_name', '$membership_id',  '$password', '$program', '$link');



    public function __construct()

    {

    }


public static function migrateEnrolments1()
{
    // dd(mt_rand());
        // dd(Hash::make("Passw0rd11!"));
        //$this->key = '!QAZXSW@#EDCVFR$';
        //self::$username = 'diamondcustomer';
        self::$username = 'firstbank@1234';

        //self::$password = parent::string_encrypt('Di@mond10$#', self::$key,self::$iv);
        self::$password = parent::string_encrypt('ssw0rd20', self::$key,self::$iv);
        $data = [];

        $failure_count = 0;

        $success_count = 0;

        // $company_details = new CompanyService(env('COMPANY_ID', 3));

        // $company_details = $company_details->getCompanyDetails()->get();

        //dd($company_details);

        // $pendingEnrolments = Enrollment::where('enrollment_status',0)->where('tries', '<=', 11)->limit(100)->get();//->where('tries', '<', 5);//->get();
        $pendingEnrolments = DB::table('LOYAL_ENROLLMENT')
                                ->where('enrollment_status', '=', 0)
                                ->where('tries', '<=', 50)
                                ->limit(50)
                                ->get();

        // $alreadyEnrolled = DB::table('LOYAL_ENROLLMENT')
        //                         ->where('enrollment_status', '=', 1)
                                // ->where('tries', '<=', 10)
                                // ->limit(150)
                                // ->get();

        // $allEnrolments = DB::table('LOYAL_ENROLLMENT')
        //                         ->get();
        // Enrollment::where('cif_id', '483006203')->update(['enrollment_status' => 1]);
        // $pendingEnrolments = Enrollment::limit(50)->get();

        // return response()->json(['data' => $pendingEnrolments]);
        // $pendingEnrolments = Enrollment::where('enrollment_status',0)->where('tries', '<=', 4)->select('first_name' ,'last_name', 'email','enrollment_status', 'tries', 'cif_id', 'branch_code', 'accountnumber', 'cif_id', 'pin', 'password')->limit(1000);//->get();//->where('tries', '<', 5);//->get();
        // print_r(
        //     ["pending" => $pendingEnrolments->count(),
        //     "enrolled" => $alreadyEnrolled->count(),
        //     "all" => $allEnrolments->count()]
        // );
       if ($pendingEnrolments->count()>0)
       {
            foreach($pendingEnrolments->unique('cif_id') as $pendingEnrolment)
            {
                // dd($pendingEnrolments->unique('cif_id'));
                //Enrollment::where('member_reference', $pendingEnrolment->member_reference)->where('enrollment_status',1)->get();
                // $existingCustomer = Enrollment::where('cif_id', $pendingEnrolment->cif_id)->where('enrollment_status',1)->first();
                $existingCustomer = false;
                if($existingCustomer)
                {
                    //CHECK MEMBER_REFERENCE EXISTS. IF YES, PUSH TO ACCOUNT_NUMBER TABLE ON PERX
                    $accDataToPush = array(
                    'Company_username'=>self::$username,//$company_details->username? $company_details->username: 0,
                    'Company_password'=>self::$password,//$company_details->password?$company_details->password:0,
                    'Membership_ID'=>$existingCustomer->cif_id,
                    // 'Membership_ID'=>parent::string_encrypt($existingCustomer->cif_id, self::$key,self::$iv),
                    'Account_number'=>$pendingEnrolment->acid,
                    'API_flag'=>'attachAcountNumber',

                    );

                    try {
                        $res = parent::pushToPERX(parent::$url, $accDataToPush, parent::$headerPayload);
                        echo $res;
                    } catch (\Exception $ex) {
                        throw new \Exception("something went wrong " . $ex->getMessage());
                    }
                    // array_push($data, $accDataToPush);
                } else {
                    $pendingEnrolment->password ? $pendingEnrolment->password = Hash::make($pendingEnrolment->password) : $pendingEnrolment->password = Hash::make(1234);

                    $pendingEnrolment->pin ? $pendingEnrolment->pin = $pendingEnrolment->pin : $pendingEnrolment->pin = '0000';

                    $pendingEnrolment->email ? $pendingEnrolment->email = $pendingEnrolment->email : $pendingEnrolment->email = $pendingEnrolment->cif_id . '@noemail.com';

                    $pendingEnrolment->branch_code ? $pendingEnrolment->branch_code = $pendingEnrolment->branch_code : $pendingEnrolment->branch_code = '000';
                    // $default_password = "P@" . "123456";
                    $default_password = "P@" . Str::random(4) . mt_rand();
                    $default_pin = rand(1000,9999);
                    // echo $default_pin;

                    $arrayToPush = array(

                        'Company_username'=>self::$username,//$company_details->username? $company_details->username: 0,

                        'Company_password'=>self::$password,//$company_details->password?$company_details->password:0,

                        'Membership_ID'=>$pendingEnrolment->cif_id,
                        'Acid' => $pendingEnrolment->acid ?? $pendingEnrolment->accountnumber,
                        // 'Acid' => $pendingEnrolment->accountnumber,
                        // 'Membership_ID'=>parent::string_encrypt($pendingEnrolment->cif_id, self::$key,self::$iv),

                        'Branch_code'=>$pendingEnrolment->branch_code,

                        //'auto_gen_password'=>$pendingEnrolment->password?$pendingEnrolment->password:'1234',
                        'auto_gen_password'=>Hash::make($default_password),
                        // 'auto_gen_password'=>$pendingEnrolment->password?Hash::make($pendingEnrolment->password):Hash::make(1234),

                        'auto_gen_pin'=> Hash::make($default_pin),
                        // 'auto_gen_pin'=>$pendingEnrolment->pin?$pendingEnrolment->pin:'0000',
                        'member_reference'=>$pendingEnrolment->cif_id?$pendingEnrolment->cif_id:'',

                        'API_flag'=>'enrol',
                    );
                    try {
                        $resp = parent::pushToPERX(parent::$url, $arrayToPush, parent::$headerPayload);
                        // echo $resp;
                    } catch (\Exception $ex) {
                        throw new \Exception("Something went wrong " . $ex->getMessage());
                    }
                    print_r($resp);// return;
                    if (parent::isJSON($resp))
                    {
                        $repsonse = json_decode($resp, true);

                   // dd($repsonse);

                        if ($repsonse)
                        {

                            // EnrolReportLog::create([

                            //     'firstname' => $pendingEnrolment->first_name?$pendingEnrolment->first_name:'',

                            //     'lastname' => $pendingEnrolment->last_name?$pendingEnrolment->last_name:'',

                            //     'email' => $pendingEnrolment->email ? $pendingEnrolment->email : $pendingEnrolment->cif_id . '@noemail.com',

                            //     'customerid' => $pendingEnrolment->cif_id?$pendingEnrolment->cif_id:'undefined',

                            //     'branchcode' => $pendingEnrolment->branch_code?$pendingEnrolment->branch_code:'undefined',

                            //     'fileid' => 0,

                            //     'status_code' => $repsonse['status']?$repsonse['status']:'undefined',

                            //     'status_message' => $repsonse['Status_message']?$repsonse['Status_message']:'undefined'

                            // ]);

                            if ($repsonse['status'] == 1001)
                            {
                                $success_count++;

                                //implement send mail

                                $values = array($pendingEnrolment->first_name, $pendingEnrolment->last_name, $pendingEnrolment->cif_id, $pendingEnrolment->password, parent::$program, parent::$link,$pendingEnrolment->pin);
                                $mail_payload = [
                                    'subject' => 'FLEX BIG ON THE FIRSTBANK LOYALTY PROGRAMME',
                                    'email_type'=> 'Enrollment',
                                    'body' => $values,
                                    'email' => $pendingEnrolment->email,
                                ];

                                //EmailDispatcher::pendMails($pendingEnrolment->cif_id, "FLEX BIG ON THE FIRST GREEN REWARDS PROGRAMME", EmailDispatcher::buildEnrolmentTemplate(self::$placeholders, $values), 'no-reply@firstbank-ng.com');

                // SendNotificationService::sendMail($repsonse['Email_subject'], $repsonse['Email_body'], $repsonse['bcc_email_address']);
                    //   $jen = SendNotificationService::sendMail('Customer Enrolment Notification', EmailDispatcher::buildEnrolmentTemplate(self::$placeholders, $values),'', $pendingEnrolment->email);

                                // $log = LogEmails::create(array_merge($mail_payload, ['status' => 0]));
                                // echo json_encode($log);

                                if( Enrollment::where('cif_id', $pendingEnrolment->cif_id)->update(['enrollment_status' => 1]))
                                {
                                    $values = [
                                        'membership_id' => $arrayToPush['Membership_ID'],
                                        'program_name' => "FirstRewards",
                                        'currency_name' => "FirstCoin",
                                        'password' => $default_password,
                                        'pin' => $default_pin,
                                        'link' => 'https://firstrewards.firstbanknigeria.com/',
                                    ];

                                    $placeholders = [
                                        '$membershipID',
                                        '$LoyaltyProgramName',
                                        '$CurrencyName',
                                        '$Password',
                                        '$Pin',
                                        '$here'
                                    ];

                                    $data = [
                                        'body' => parent::buildEnrolmentTemplate($placeholders, $values),
                                        // 'acid' => $pendingEnrolment->acid,
                                        'acid' => $pendingEnrolment->accountnumber ?? $pendingEnrolment->acid,
                                        'requestId' => (string) mt_rand(),
                                        'isBodyHtml' => true,
                                        'title' => "FLEX BIG WITH FIRST BANK LOYALTY PROGRAMME",
                                        'fromAddress' => env('MAIL_FROM'),
                                        'sendPdfAttachment' => false,
                                        'pdfAttachmentBody' => null
                                    ];

                                    // print_r($data);

                                    $sendmail = parent::sendMailGuzzle($data);

                                    // print_r($sendmail);
                                    Log::debug("Response from email service for " . $arrayToPush['Membership_ID'] . " " . json_encode($sendmail));

                                    $data['message'] = 'data migrated ' . $success_count;
                                }
                                else{
                                    echo "...";
                                }

                            }
                            else {

                            if(Enrollment::where('cif_id', $pendingEnrolment->cif_id)->update(['tries' => $pendingEnrolment->tries + 1]))
                            {
                                //Log::info('failed to migrate '. $failure_count);
                                $data['message'] = 'data failed ' . $failure_count;
                            }
                            else{
                                echo "__ loced";
                            }

                            }

                        }
                        else{
                            $data['message'] = "no response from server";
                        }

                    }
                    else{
                        $data['format'] = "not json serialized";
                    }

            }

        }
        }else{
            $data['message'] = "no un-enroled customers found";
        }

        return json_encode($data);

}



public static function migrateEnrolments1_old() : string
{

        //$this->key = '!QAZXSW@#EDCVFR$';



        self::$username = 'diamondcustomer';

        self::$password = parent::string_encrypt('Di@mond10$#', self::$key,self::$iv);

        $data = [];

        $failure_count = 0;

        $success_count = 0;

        $company_details = new CompanyService(env('COMPANY_ID', 1));

        $company_details = $company_details->getCompanyDetails()->get();

        $pendingEnrolments = Enrollment::where('enrollment_status',0)->where('tries', '<=', 4)->select('first_name' ,'last_name', 'email','enrollment_status', 'tries', 'member_reference', 'branch_code', 'cif_id', 'pin', 'password')->limit(1000);//->get();//->where('tries', '<', 5);//->get();

       if ($pendingEnrolments->count()>0){

        foreach($pendingEnrolments->get() as $pendingEnrolment){

        $pendingEnrolment->password ? $pendingEnrolment->password = $pendingEnrolment->password : $pendingEnrolment->password = '1234';

        $pendingEnrolment->pin ? $pendingEnrolment->pin = $pendingEnrolment->pin : $pendingEnrolment->pin = '0000';

        $pendingEnrolment->email ? $pendingEnrolment->email = $pendingEnrolment->email : $pendingEnrolment->email = $pendingEnrolment->cif_id . '@noemail.com';

        $pendingEnrolment->branch_code ? $pendingEnrolment->branch_code = $pendingEnrolment->branch_code : $pendingEnrolment->branch_code = '000';

                $arrayToPush = array(

                    'Company_username'=>self::$username,//$company_details->username? $company_details->username: 0,

                    'Company_password'=>self::$password,//$company_details->password?$company_details->password:0,

                    'Membership_ID'=>$pendingEnrolment->cif_id,

                    'Branch_code'=>$pendingEnrolment->branch_code,

                    'auto_gen_password'=>$pendingEnrolment->password?$pendingEnrolment->password:'1234',

                    'auto_gen_pin'=>$pendingEnrolment->pin?$pendingEnrolment->pin:'0000',

                    'API_flag'=>'enrol',



         );

          $resp = parent::pushToPERX(parent::$url, $arrayToPush, parent::$headerPayload);

        if (parent::isJSON($resp)) {

          $repsonse = json_decode($resp, true);

          echo $resp . "<br>";

          if ($repsonse) {

            EnrolReportLog::create([

              'firstname' => $pendingEnrolment->first_name?$pendingEnrolment->first_name:'',

              'lastname' => $pendingEnrolment->last_name?$pendingEnrolment->last_name:'',

              'email' => $pendingEnrolment->email ? $pendingEnrolment->email : $pendingEnrolment->cif_id . '@noemail.com',

              'customerid' => $pendingEnrolment->cif_id?$pendingEnrolment->cif_id:'undefined',

              'branchcode' => $pendingEnrolment->branch_code?$pendingEnrolment->branch_code:'undefined',

              'fileid' => 0,

              'status_code' => $repsonse['status']?$repsonse['status']:'undefined',

              'status_message' => $repsonse['Status_message']?$repsonse['Status_message']:'undefined'

            ]);

            if ($repsonse['status'] == 1001) {

              $success_count++;

              //implement send mail

              $values = array($pendingEnrolment->first_name, $pendingEnrolment->last_name, $pendingEnrolment->cif_id, $pendingEnrolment->password, parent::$program, parent::$link);

              EmailDispatcher::pendMails($pendingEnrolment->cif_id, "FLEX BIG ON THE FIDELITY GREEN REWARDS PROGRAMME", EmailDispatcher::buildEnrolmentTemplate(self::$placeholders, $values), 'no-reply@fdelitybank-ng.com');

              //SendNotificationService::sendMail($repsonse['Email_subject'], $repsonse['Email_body'], $repsonse['bcc_email_address']);

             if( Enrollment::where('member_reference', $pendingEnrolment->member_reference)->update(['enrollment_status' => 1])){

              $data['message'] = 'data migrated ' . $success_count;
			 }else{
				 echo "...";
			 }

            } else {

              if(Enrollment::where('member_reference', $pendingEnrolment->member_reference)->update(['tries' => $pendingEnrolment->tries + 1])){

              //Log::info('failed to migrate '. $failure_count);

              $data['message'] = 'data failed ' . $failure_count;}else{echo "__ loced";}

            }

          } else {

            $data['message'] = "no response from server";

          }

        }else{

          $data['format'] = "not json serialized";

        }

        }



        }else{

          $data['message'] = "no un-enroled customers found";



       }

        return json_encode($data);

}










}

?>