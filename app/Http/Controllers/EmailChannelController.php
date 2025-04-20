<?php

namespace App\Http\Controllers;
use App\Http\Controllers\EnrollmentController;
use App\Http\Requests\EmailRequest;
use App\Services\EmailDispatcher;
use App\Models\Enrollment;
use App\Services\CurlService;
use App\Services\EnrolmentMigrationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailChannelController extends Controller
{
    //

    public function channelMail(EmailRequest $request){
       //print_r($request->all());
            $data = [
                "sender"=>"noreply@fidelitybank",
                "from"=> "noreply@fidelitybank.ng",
                "to"=>isset($request->email)==true?$request->email:$request->to,
                "subject"=>$request->subject,
                "body"=> $request->body
            ];

            //$loyalty_number = trim(EnrolmentMigrationService::string_decrypt($request->Membership_ID, 'SmoothJay', '5666685225155700'));
            $url = env('EMAIL_SERVICE_URL');
            $response = CurlService::doCURL($url, $data);

            if (!empty($response)){

                return response()->json($response);
            }else{

                return response()->json("failed to send email to:" . $request->to, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // return json_encode($response);


    }

    public static function sendInternalMail($receipient, $subject, $body)
    {
        $data = [
            "sender"    =>  "noreply@fidelitybank",
            "from"      =>  "noreply@fidelitybank.ng",
            "to"        =>  $receipient,
            "subject"   =>  $subject,
            "body"      =>  $body
        ];

        $url = env('EMAIL_SERVICE_URL', 'https://10.10.5.24/bankapi/messaging/v1/email/send ');

        $response = CurlService::doCURL($url, $data);

        // if(!empty($response))
        // {
            return json_decode($response);
        // }else{
        //     return 0;
        // }
    }
}

?>
