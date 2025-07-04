<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MigrateTransactionController extends Controller
{
    public static $key = '!QAZXSW@#EDCVFR$';
    public static $iv = '5666685225155700';
    public static $username = 'diamondcustomer';
    public static $password = 'ssw0rd20';

    public function migrate_transaction()
    {
        $success_count = 0;  $failure_count = 0;
	  //echo $success_count;
	  //echo Transaction->all
	  $payload = array(); //rollback emails till 7th of feb 2023
    //   $pendingTransactions = Transaction::limit(50)->get();
    //   $pendingTransactions = Transaction::where('status', '=', 0)->limit(50);
    $pendingTransactions = DB::table('QUALIFIED_TRANSACTIONS')
                                ->where('status', '=', 0)
                                ->limit(100)
                                ->get();
    // dd($pendingTransactions->count());
    // $alreadyStaged = DB::table('transactions')
    //                             ->where('status', '=', 1)
    //                             ->get();

    // $allTransactions = DB::table('transactions')
    //                             ->get();

    // print_r(
    //         ["pending" => $pendingTransactions->count(),
    //         "staged" => $alreadyStaged->count(),
    //         "all" => $allTransactions->count()]
    //     );
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
                'Company_password'=>self::$password,
                'Membership_ID'=>$pendingTransaction->cif_id,
                // 'Membership_ID'=>$pendingTransaction->member_reference,
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
                    $this->pushToPERX("https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/staging/stage_data.php", $payload, "");
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
}