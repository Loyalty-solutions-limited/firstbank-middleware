<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EnrollmentController;
use App\Services\EmailDispatcher;
use App\Models\Enrollment;
use App\Services\CurlService;
use App\Services\EnrolmentMigrationService;
use Illuminate\Http\Request;

class EmailChannelController extends Controller
{
    //

    public function channelMail(Request $request){
       // print_r($request->all());
        $data = array("sender"=>"noreply@fidelitybank", "from"=> "noreply@fidelitybank.ng","to"=>isset($request->email)==true?$request->email:$request->to, "bcc"=> "greenrewards@perxclm.com", "subject"=>$request->subject, "body"=> $request->body);
            //$loyalty_number = trim(EnrolmentMigrationService::string_decrypt($request->Membership_ID, 'SmoothJay', '5666685225155700'));
            $url = env('EMAIL_SERVICE_URL', 'https://loyaltysolutionsnigeria.com/email_templates/sendmail2.php');
            $response = CurlService::doCURL($url, $data);

             //print_r($response);
            if (!empty($response)){
              
            }else{
               
                return 0;
            }

            return json_encode($response);
            
                
            }
}

?>