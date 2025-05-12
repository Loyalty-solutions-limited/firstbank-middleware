<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Http\Requests\LogEmailsRequest;
use App\Models\LogEmails;
use App\Services\CurlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LogEmailsController extends Controller
{
    public function log(LogEmailsRequest $request){
        $insert_log = LogEmails::create(array_merge($request->all(), ['status' => 0]));
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
                    echo json_encode($response);exit;
                    if($response['response'] == 1){
                        $update_status = LogEmails::where('id',$data->id)->update(['status' => 1]);
                        echo "Email Sent Successfullt";
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
                        echo "An error Occurred";
                    }
                }else{
                    echo "Membership ID $get_email->membership_id does not have an email address";
                }
            }
        }else{
            echo "No Pending Emails";
        }
    }


}