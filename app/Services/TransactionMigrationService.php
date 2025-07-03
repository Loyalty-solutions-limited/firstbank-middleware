<?php
namespace App\Services;
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Services\CurlService;
use App\Services\UserService;
use App\Services\EmailDispatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\TransactionReportLog ;

class TransactionMigrationService extends MigrationService{
    public static $key = '!QAZXSW@#EDCVFR$';
    public static $iv = '5666685225155700';
    public static $username = 'diamondcustomer';
    public static $placeholders = array('$first_name', '$last_name', '$points_earned','$current_balance', '$program', '$Membership_ID', '$link', '$product_name');
    //public static $password =  self::$password = parent::string_encrypt('Di@mond10$#', self::$key,self::$iv);
    public function __construct()
    {

    }

    public static function migrateTransaction1():void{
      $success_count = 0;  $failure_count = 0;
	  //echo $success_count;
	  //echo Transaction->all
      $pendingTransactions = Transaction::where('status', 0)->limit(200);
	  //echo $pendingTransactions->count(); exit;
      if($pendingTransactions->count() > 0){
          foreach($pendingTransactions->get() as $pendingTransaction){
            //$pendingTransaction->quantity  = 1;
            $membership_id_resolved = parent::string_encrypt(parent::resolveMemberReference($pendingTransaction->cif_id), self::$key,self::$iv);
              $arrayToPush = array(
                'Company_username'=>self::$username,
                'Company_password'=>parent::passwordReturn(),
                'Membership_ID'=>$membership_id_resolved,
                'Transaction_Date'=>$pendingTransaction->transaction_date,
                'Transaction_Type_code'=>$pendingTransaction->transaction_type,
                'Transaction_channel_code'=>$pendingTransaction->channel,
                'Transaction_amount'=>$pendingTransaction->amount,
                'Branch_code'=>$pendingTransaction->branch_code,
                'Transaction_ID'=>$pendingTransaction->transaction_reference,
                'Product_Code' =>$pendingTransaction->product_code,
                'Product_Quantity' =>$pendingTransaction->quantity,
                'API_flag' => 'stran'
                );

                //print_r($pendingTransaction->cif_id);
                $member =  Enrollment::where('cif_id', trim($pendingTransaction->cif_id))->first();
                //print_r($member);
                $product_name = json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/get_product_group_name?product_code=".$pendingTransaction->product_code), true);
              //  if (isset($member->first_name)){
                $product_name = $product_name['product_brand_name'];
                $values = array(isset($member->first_name)?$member->first_name:'', isset($member->last_name)?$member->last_name:'', 0, 0, parent::$program, isset($member->cif_id)?$member->cif_id:'', "https://loyalty.fidelitybank.ng/login.php", $product_name);
                $resp =
                parent::pushToPERX(parent::$url, $arrayToPush, parent::$headerPayload);
                print_r($resp);
				//echo".." . $member->cif_id . $pendingTransaction->cif_id ."<br>";
                $repsonse = json_decode($resp, true);
            if ($repsonse){
            if ($repsonse['status'] == 1001){
                //print_r($repsonse);
                $values[2] = $repsonse['Loyalty_points']?number_format($repsonse['Loyalty_points']):0;
                $customer_balance = CurlService::makeGet("https://perxapi.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$pendingTransaction->cif_id);//, true); //["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;
                $customer_balance = json_decode($customer_balance, true);
                $values[3] = number_format($customer_balance['profile'][0]['Current_balance']);//json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;


                //SendNotificationService::sendMail($repsonse['Email_subject'], $repsonse['Email_body'], $repsonse['bcc_email_address']);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 1]);
                if(intval($values[2])>0){
                    EmailDispatcher::pendMails(parent::resolveMemberReference(trim($pendingTransaction->cif_id)), "YOU JUST EARNED LOYALTY POINTS ON THE FIDELITY GREEN REWARDS PROGRAMME", EmailDispatcher::buildTransactionTemplate(self::$placeholders, $values), 'no-reply@greenrewards.com');
                }
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date, 'status_message'=>$repsonse['Status_message']]);

            }else{
                //Transaction::where('cif_id', $pendingTransaction->cif_id)->update(['tries'=>$pendingTransaction->tries+ 1 ]);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 3]);
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'status_message'=>$repsonse['Status_message'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date]);


            }
        }else{


        }

    }
         // }
    } else{
        $data['message'] = "no transactions on queue for migration";
        //print_r($data);
    }
  }


    public static function migrateTransactionCron($cron_id):void{
      $success_count = 0;  $failure_count = 0;
	  //echo $success_count;
	  //echo Transaction->all
      $pendingTransactions = Transaction::where('status', 0)->where('cron_id', $cron_id)->limit(300);
	  //echo $pendingTransactions->count(); exit;
      if($pendingTransactions->count() > 0){
          foreach($pendingTransactions->get() as $pendingTransaction){
            //$pendingTransaction->quantity  = 1;
            $membership_id_resolved = parent::string_encrypt(parent::resolveMemberReference($pendingTransaction->cif_id), self::$key,self::$iv);
              $arrayToPush = array(
                'Company_username'=>self::$username,
                'Company_password'=>parent::passwordReturn(),
                'Membership_ID'=>$membership_id_resolved,
                'Transaction_Date'=>$pendingTransaction->transaction_date,
                'Transaction_Type_code'=>$pendingTransaction->transaction_type,
                'Transaction_channel_code'=>$pendingTransaction->channel,
                'Transaction_amount'=>$pendingTransaction->amount,
                'Branch_code'=>$pendingTransaction->branch_code,
                'Transaction_ID'=>$pendingTransaction->transaction_reference,
                'Product_Code' =>$pendingTransaction->product_code,
                'Product_Quantity' =>$pendingTransaction->quantity,
                'API_flag' => 'stran'
                );

                //print_r($pendingTransaction->cif_id);
                $member =  Enrollment::where('cif_id', trim($pendingTransaction->cif_id))->first();
                //print_r($member);
                $product_name = json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/get_product_group_name?product_code=".$pendingTransaction->product_code), true);
              //  if (isset($member->first_name)){
                $product_name = $product_name['product_brand_name'];
                $values = array(isset($member->first_name)?$member->first_name:'', isset($member->last_name)?$member->last_name:'', 0, 0, parent::$program, isset($member->cif_id)?$member->cif_id:'', "https://loyalty.fidelitybank.ng/login.php", $product_name);
                $resp =
                parent::pushToPERX(parent::$url, $arrayToPush, parent::$headerPayload);
                print_r($resp);
				//echo".." . $member->cif_id . $pendingTransaction->cif_id ."<br>";
                $repsonse = json_decode($resp, true);
            if ($repsonse){
            if ($repsonse['status'] == 1001){
                //print_r($repsonse);
                $values[2] = $repsonse['Loyalty_points']?number_format($repsonse['Loyalty_points']):0;
                $customer_balance = CurlService::makeGet("https://perxapi.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$pendingTransaction->cif_id);//, true); //["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;
                $customer_balance = json_decode($customer_balance, true);
                $values[3] = number_format($customer_balance['profile'][0]['Current_balance']);//json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;


                //SendNotificationService::sendMail($repsonse['Email_subject'], $repsonse['Email_body'], $repsonse['bcc_email_address']);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 1]);
                if(intval($values[2])>0){
                    EmailDispatcher::pendMails(parent::resolveMemberReference(trim($pendingTransaction->cif_id)), "YOU JUST EARNED LOYALTY POINTS ON THE FIDELITY GREEN REWARDS PROGRAMME", EmailDispatcher::buildTransactionTemplate(self::$placeholders, $values), 'no-reply@greenrewards.com');
                }
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date, 'status_message'=>$repsonse['Status_message']]);

            }else{
                //Transaction::where('cif_id', $pendingTransaction->cif_id)->update(['tries'=>$pendingTransaction->tries+ 1 ]);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 3]);
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'status_message'=>$repsonse['Status_message'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date]);


            }
        }else{


        }

    }
         // }
    } else{
        $data['message'] = "no transactions on queue for migration";
        //print_r($data);
    }
  }



public static function runSpecificTransactions($data){


      $success_count = 0;  $failure_count = 0;
	  //echo $success_count;
	  //echo Transaction->all
      $pendingTransactions = Transaction::whereIn('cif_id', $data);
	  //echo $pendingTransactions->count(); exit;
      if($pendingTransactions->count() > 0){
          foreach($pendingTransactions->get() as $pendingTransaction){
            //$pendingTransaction->quantity  = 1;
            $membership_id_resolved = parent::string_encrypt(parent::resolveMemberReference($pendingTransaction->cif_id), self::$key,self::$iv);
              $arrayToPush = array(
                'Company_username'=>self::$username,
                'Company_password'=>parent::passwordReturn(),
                'Membership_ID'=>$membership_id_resolved,
                'Transaction_Date'=>$pendingTransaction->transaction_date,
                'Transaction_Type_code'=>$pendingTransaction->transaction_type,
                'Transaction_channel_code'=>$pendingTransaction->channel,
                'Transaction_amount'=>$pendingTransaction->amount,
                'Branch_code'=>$pendingTransaction->branch_code,
                'Transaction_ID'=>$pendingTransaction->transaction_reference,
                'Product_Code' =>$pendingTransaction->product_code,
                'Product_Quantity' =>$pendingTransaction->quantity,
                'API_flag' => 'stran'
                );

                //print_r($pendingTransaction->cif_id);
                $member =  Enrollment::where('cif_id', trim($pendingTransaction->cif_id))->first();
                //print_r($member);
                $product_name = json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/get_product_group_name?product_code=".$pendingTransaction->product_code), true);
              //  if (isset($member->first_name)){
                $product_name = $product_name['product_brand_name'];
                $values = array(isset($member->first_name)?$member->first_name:'', isset($member->last_name)?$member->last_name:'', 0, 0, parent::$program, isset($member->cif_id)?$member->cif_id:'', "https://loyalty.fidelitybank.ng/login.php", $product_name);
                $resp =
                parent::pushToPERX(parent::$url, $arrayToPush, parent::$headerPayload);
                print_r($resp);
				//echo".." . $member->cif_id . $pendingTransaction->cif_id ."<br>";
                $repsonse = json_decode($resp, true);
            if ($repsonse){
            if ($repsonse['status'] == 1001){
                //print_r($repsonse);
                $values[2] = $repsonse['Loyalty_points']?number_format($repsonse['Loyalty_points']):0;
                $customer_balance = CurlService::makeGet("https://perxapi.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$pendingTransaction->cif_id);//, true); //["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;
                $customer_balance = json_decode($customer_balance, true);
                $values[3] = number_format($customer_balance['profile'][0]['Current_balance']);//json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;


                //SendNotificationService::sendMail($repsonse['Email_subject'], $repsonse['Email_body'], $repsonse['bcc_email_address']);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 1]);
                if(intval($values[2])>0){
                    EmailDispatcher::pendMails(parent::resolveMemberReference(trim($pendingTransaction->cif_id)), "YOU JUST EARNED LOYALTY POINTS ON THE FIDELITY GREEN REWARDS PROGRAMME", EmailDispatcher::buildTransactionTemplate(self::$placeholders, $values), 'no-reply@greenrewards.com');
                }
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date, 'status_message'=>$repsonse['Status_message']]);

            }else{
                //Transaction::where('cif_id', $pendingTransaction->cif_id)->update(['tries'=>$pendingTransaction->tries+ 1 ]);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 3]);
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'status_message'=>$repsonse['Status_message'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date]);


            }
        }else{


        }

    }
         // }
    } else{
        $data['message'] = "no transactions on queue for migration";
        //print_r($data);
    }
  //}


}

public static function migrateTransaction2():void{
      $success_count = 0;  $failure_count = 0;
	  //echo $success_count;
	  //echo Transaction->all
      $pendingTransactions = Transaction::where('status', 0)->limit(200);
	  //echo $pendingTransactions->count(); exit;
      if($pendingTransactions->count() > 0){
          foreach($pendingTransactions->get() as $pendingTransaction){
            //$pendingTransaction->quantity  = 1;
            $membership_id_resolved = parent::string_encrypt(parent::resolveMemberReference($pendingTransaction->cif_id), self::$key,self::$iv);
              $arrayToPush = array(
                'Company_username'=>self::$username,
                'Company_password'=>parent::passwordReturn(),
                'Membership_ID'=>$membership_id_resolved,
                'Transaction_Date'=>$pendingTransaction->transaction_date,
                'Transaction_Type_code'=>$pendingTransaction->transaction_type,
                'Transaction_channel_code'=>$pendingTransaction->channel,
                'Transaction_amount'=>$pendingTransaction->amount,
                'Branch_code'=>$pendingTransaction->branch_code,
                'Transaction_ID'=>$pendingTransaction->transaction_reference,
                'Product_Code' =>$pendingTransaction->product_code,
                'Product_Quantity' =>$pendingTransaction->quantity,
                'API_flag' => 'stran'
                );

                //print_r($pendingTransaction->cif_id);
                $member =  Enrollment::where('cif_id', trim($pendingTransaction->cif_id))->first();
                //print_r($member);
                $product_name = json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/get_product_group_name?product_code=".$pendingTransaction->product_code), true);
              //  if (isset($member->first_name)){
                $product_name = $product_name['product_brand_name'];
                $values = array(isset($member->first_name)?$member->first_name:'', isset($member->last_name)?$member->last_name:'', 0, 0, parent::$program, isset($member->cif_id)?$member->cif_id:'', "https://loyalty.fidelitybank.ng/login.php", $product_name);
                $resp =
                parent::pushToPERX(parent::$url, $arrayToPush, parent::$headerPayload);
                print_r($resp);
				//echo".." . $member->cif_id . $pendingTransaction->cif_id ."<br>";
                $repsonse = json_decode($resp, true);
            if ($repsonse){
            if ($repsonse['status'] == 1001){
                //print_r($repsonse);
                $values[2] = $repsonse['Loyalty_points']?number_format($repsonse['Loyalty_points']):0;
                $customer_balance = CurlService::makeGet("https://perxapi.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$pendingTransaction->cif_id);//, true); //["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;
                $customer_balance = json_decode($customer_balance, true);
                $values[3] = number_format($customer_balance['profile'][0]['Current_balance']);//json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"] > 0?json_decode(CurlService::makeGet("https://perxapi2.perxclm.com/api/profile?token=LSLonlypass&membership_id=".$membership_id_resolved), true)["profile"][0]["Current_balance"]:0.00;


                //SendNotificationService::sendMail($repsonse['Email_subject'], $repsonse['Email_body'], $repsonse['bcc_email_address']);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 1]);
                if(intval($values[2])>0){
                    EmailDispatcher::pendMails(parent::resolveMemberReference(trim($pendingTransaction->cif_id)), "YOU JUST EARNED LOYALTY POINTS ON THE FIDELITY GREEN REWARDS PROGRAMME", EmailDispatcher::buildTransactionTemplate(self::$placeholders, $values), 'no-reply@greenrewards.com');
                }
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date, 'status_message'=>$repsonse['Status_message']]);

            }else{
                //Transaction::where('cif_id', $pendingTransaction->cif_id)->update(['tries'=>$pendingTransaction->tries+ 1 ]);
                Transaction::where('transaction_reference', $pendingTransaction->transaction_reference)->update(['status' => 3]);
                TransactionReportLog::create(['customer_reference'=>$pendingTransaction->cif_id, 'branch_code'=>$pendingTransaction->branch_code,
                'status_code'=>$repsonse['status'], 'status_message'=>$repsonse['Status_message'], 'account_number'=>$pendingTransaction->account_number, 'transaction_date'=>$pendingTransaction->transaction_date]);


            }
        }else{


        }

    }
         // }
    } else{
        $data['message'] = "no transactions on queue for migration";
        //print_r($data);
    }
  }


public static function rollbackTransactions($id)
{

      $success_count = 0;  $failure_count = 0;
	  //echo $success_count;
	  //echo Transaction->all
	  $payload = array(); //rollback emails till 7th of feb 2023
    //   $pendingTransactions = Transaction::limit(50)->get();
    //   $pendingTransactions = Transaction::where('status', '=', 0)->limit(50);
    $pendingTransactions = DB::table('QUALIFIED_TRANSACTIONS')
                                ->where('status', '=', 0)
                                ->limit(30)
                                ->get();

    $alreadyStaged = DB::table('QUALIFIED_TRANSACTIONS')
                                ->where('status', '=', 1)
                                ->get();

    $allTransactions = DB::table('QUALIFIED_TRANSACTIONS')
                                ->get();

    print_r(
            ["pending" => $pendingTransactions->count(),
            "staged" => $alreadyStaged->count(),
            "all" => $allTransactions->count()]
        );
    //   return $pendingTransactions->count();
    //   dd($pendingTransactions->count());
	  //echo $pendingTransactions->count(); exit;
    //   return response()->json(['data' => $pendingTransactions]);
      if($pendingTransactions->count() > 0){
          foreach($pendingTransactions->unique('transaction_reference') as $pendingTransaction){
            //$pendingTransaction->quantity  = 1;
            //dd($pendingTransaction);
            // $membership_id_resolved = parent::resolveMemberReference($pendingTransaction->cif_id) ?? '8731110';
            //dd($membership_id_resolved);
              $arrayToPush = array(
                'Company_username'=>self::$username,
                'Company_password'=>parent::passwordReturn(),
                'Membership_ID'=>$pendingTransaction->member_reference,
                'Acid' => $pendingTransaction->account_number,
                // 'Membership_ID'=>$membership_id_resolved ?? '8711130',
                'Transaction_Date'=>$pendingTransaction->transaction_date,
                'Transaction_Type_code'=>$pendingTransaction->transaction_type,
                'Transaction_channel_code'=>$pendingTransaction->channel,
                'Transaction_amount'=>$pendingTransaction->amount,
                'Branch_code'=>$pendingTransaction->branch_code,
                'Transaction_ID'=>$pendingTransaction->transaction_reference,
                'Product_Code' =>$pendingTransaction->product_code,
                'Product_Quantity' =>$pendingTransaction->quantity,
                'API_flag' => 'stran',
                'id'=>$pendingTransaction->id
                );
                // dd($arrayToPush);
                // $pendingTransaction->update(['status' => 1]);
                DB::table('QUALIFIED_TRANSACTIONS')
                        ->where('id', '=', $pendingTransaction->id)
                        ->update(['status' => 1]);
				array_push($payload, $arrayToPush);
		  }


                try {

                    $resp =
                    // parent::pushToPERX("https://staging-env.perxclm.com/stage-data.php", $payload, parent::$headerPayload);
                    //parent::pushToPERX("https://firstbankloyalty.perxclm.com/stage_data/stage_data.php", $payload, parent::$headerPayload);
                    //parent::pushToPERX("https://demo.firstrewards.loyaltysolutionsnigeria.com/stage_data/stage_data.php", $payload, parent::$headerPayload);
                    parent::pushToPERX("https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/staging/stage_data.php", $payload, parent::$headerPayload);
                    print_r($resp);
                    //dd($resp);
                    // return response()->json($resp);
                } catch (\Exception $ex) {
                    throw new \Exception("Something went wrong " . $ex->getMessage());
                }

        }else{

            return response()->json([
                "message" => "There are no staging data currently",
                "status" => false
                //"status" => "Not Working"
            ]);
        }

}


public static function rollbackTransactions_old($id)
{

      $success_count = 0;  $failure_count = 0;
	  //echo $success_count;
	  //echo Transaction->all
	  $payload = array(); //rollback emails till 7th of feb 2023
      $pendingTransactions = Transaction::where('id', '>', $id)->limit(5000);
    //   dd($pendingTransactions->count());
	  //echo $pendingTransactions->count(); exit;
      if($pendingTransactions->count() > 0){
          foreach($pendingTransactions->get() as $pendingTransaction){
            //$pendingTransaction->quantity  = 1;
            $membership_id_resolved = parent::resolveMemberReference($pendingTransaction->cif_id);
              $arrayToPush = array(
                'Company_username'=>self::$username,
                'Company_password'=>parent::passwordReturn(),
                'Membership_ID'=>$membership_id_resolved ?? '8711130',
                'Transaction_Date'=>$pendingTransaction->transaction_date,
                'Transaction_Type_code'=>$pendingTransaction->transaction_type,
                'Transaction_channel_code'=>$pendingTransaction->channel,
                'Transaction_amount'=>$pendingTransaction->amount,
                'Branch_code'=>$pendingTransaction->branch_code,
                'Transaction_ID'=>$pendingTransaction->transaction_reference,
                'Product_Code' =>$pendingTransaction->product_code,
                'Product_Quantity' =>$pendingTransaction->quantity,
                'API_flag' => 'stran',
                'id'=>$pendingTransaction->id
                );
                $pendingTransaction->update(['status' => 4]);
				array_push($payload, $arrayToPush);
		  }


                $resp =
                // parent::pushToPERX("https://staging-env.perxclm.com/stage-data.php", $payload, parent::$headerPayload);
                parent::pushToPERX("http://localhost:8888/staging/stage_data.php", $payload, parent::$headerPayload);
				// print_r($resp);
                return response()->json($resp);

        }else{

            return response()->json([
                "message" => "There are no staging data currently",
                "status" => false
            ]);
        }

    }





}

?>