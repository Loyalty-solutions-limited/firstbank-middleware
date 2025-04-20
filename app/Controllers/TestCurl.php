<?php

namespace App\Http\Controllers;
use App\Services\CurlService;

use Illuminate\Http\Request;

class TestCurl extends Controller
{


    public function testCurl(){
        $url = env("EMAIL_SERVICE_URL", "https://sandboxgateway.fidelitybank.ng/bankapi/messaging/v1/email/send");
        $arr =  array("sender"=>
        env('FROM_EMAIL'), 'from'=>'Fidelity Green Reward',
         "subject"=>"test", "to"=>"ojiodujoachim@gmail.com", "body"=>"<b>test</b>", 'bcc'=>'joachim.ojiodu@loyaltysolutionsnigeria.com', 'attachments'=>[]);
            $response = CurlService::doCURL($url, $arr);
         
            
    }

    public function testAPI(){
        return json_decode(CurlService::makeGet("https://perxapi.perxclm.com/api/profile?token=LSLonlypass&membership_id=9999"), true)["profile"][0]["Current_balance"] < 0?json_decode(CurlService::makeGet("https://perxapi.perxclm.com/api/profile?token=LSLonlypass&membership_id=9999"), true)["profile"][0]["Current_balance"]:0.00;
        //return $values;
    }
}