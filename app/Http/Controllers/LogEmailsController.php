<?php

namespace App\Http\Controllers;

use App\Models\LogEmails;
use Illuminate\Http\Request;
use App\Services\CurlService;
use App\Http\Requests\EmailRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\LogEmailsRequest;

class LogEmailsController extends Controller
{
    public function log(LogEmailsRequest $request){
        // $insert_log = LogEmails::create(array_merge($request->all(), [
        //     'status' => 0,
        //     'first_name' => '',
        //     'last_name' => '',
        //     'email' => '',
        //     'trans_ref' =>
        // ]));
        $insert_log = new LogEmails();
        $insert_log->status = 0;
        $insert_log->first_name = '';
        $insert_log->last_name = '';
        $insert_log->email = '';
        $insert_log->trans_ref = $request->trans_ref ?? '';
        $insert_log->email_type = $request->email_type;
        $insert_log->body = $request->body;
        $insert_log->subject = $request->subject;
        $insert_log->membership_id = $request->acid;
        $insert_log->save();

        return response()->json("Email Logged Successfully");
    }
//ask what criteria to get the mails from log email
    public function sendWithAPI(){
        $get_emails = LogEmails::where('status', 0)->get();
        if(count($get_emails) > 0){
            foreach($get_emails as $get_email){
                if($get_email->email != ""){
                    $data = array("sender"=>"noreply@firstbank", "from"=> "noreply@firstbank.ng","to"=>$get_email->email, "subject"=>$get_email->subject, "body"=> $get_email->body);
                    $url = env('EMAIL_SERVICE_URL');
                    $response = CurlService::doCURL($url, $data);
                    echo json_encode($response);
                    if($response['response'] == 1){
                        $update_status = LogEmails::where('id',$data->id)->update(['status' => 1]);
                        echo "Email Sent Successfully";
                    }else{
                        echo "Email failed to send";
                    }
                }else{
                    echo "$get_email->membership_id Doesnt have Email Address";
                }
            }
        }else{
                echo "No Pending Emails";
        }
    }

    public function sendMail(LogEmailsRequest $request)
    {
        $data = array("sender"=>"noreply@firstbank", "from"=> "noreply@firstbank.ng","to"=>$request->email, "subject"=>$request->subject, "body"=> $request->body);
        $url = env('POINTS_TO_CASH_URL') . "email/send-mail";
        // $response = CurlService::doCURL($url, $request->all());
        try{

            // echo $url;
        //    $response = json_decode($this->makeCurl($url, 'POST', $request->all()));
           $response = $this->makeCurl($url, 'POST', $request->all());
            return $response;
            echo json_decode($response);
                if($response['responseCode'] == "00"){
                    // $update_status = LogEmails::where('id',$data->id)->update(['status' => 1]);
                $insert_log = LogEmails::create(array_merge($request->all(), ['status' => 1]));
                    echo "Email Sent Successfully";
                }else{
                    echo "Email failed to send";
                }
        }catch(\Exception $ex){
            throw new \Exception("something went wrong " . $ex->getMessage());
        }
    }

    public function sendMailGuzzle(LogEmailsRequest $request)
    {
        $url = env('POINTS_TO_CASH_URL') . "email/send-mail";


        $response = Http::withoutVerifying()
            ->withHeaders([
                'Accept' => 'application/json',
                "AppId: ".config("externalservices.POINTS_TO_CASH_APPID"),
                "AppKey: ".config("externalservices.POINTS_TO_CASH_APPKEY"),
                'Content-Type: application/json',
                ])
            ->withOptions(["verify"=>false])
            ->post($url, $request->all());

        return $response;


    }



    public function sendWithSMTP(){
        $get_emails = LogEmails::where('status', 0)->get();
        if(count($get_emails) > 0){
            foreach($get_emails as $get_email){
                if($get_email->email != ""){
                    $subject = $get_email->subject;
                    $primary_recipient = $get_email->email;
                    $template_body = $get_email->body;
                    try{
                        Mail::send([], [], function ($message) use ($subject, $primary_recipient, $template_body) {
                                $message->to($primary_recipient);
                                $message->subject($subject);
                                $message->setBody($template_body, 'text/html');
                        });
                         // Check for failures
                        if (Mail::failures()) {
                            echo "Email Failed To Send";
                        } else {
                            $update_status = LogEmails::where('id',$get_email->id)->update(['status' => 1]);
                            echo "Email Sent Successfully";
                        }
                    } catch (\Exception $e) {
                        echo "Failed to send. An error Occurred";
                    }
                }else{
                    echo "Membership ID $get_email->membership_id does not have an email address";
                }
            }
        }else{
            echo "No Pending Emails";
        }
    }

    public function getMailParameters()
    {
        $url = env('POINTS_TO_CASH_URL') . "email/supported-parameters";

        return $this->makeCurl($url, 'GET', "");


    }

    public function makeCurl($url, $action, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        // CURLOPT_SETOPT => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $action,
        CURLOPT_POSTFIELDS =>$data,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            "AppId: ".config("externalservices.POINTS_TO_CASH_APPID"),
            "AppKey: ".config("externalservices.POINTS_TO_CASH_APPKEY"),
            'Content-Type: application/json',
            'Cookie: ARRAffinity=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33; ARRAffinitySameSite=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33'
        ),
        ));

        $response = curl_exec($curl);


        curl_close($curl);
        // return $response;
    }


}