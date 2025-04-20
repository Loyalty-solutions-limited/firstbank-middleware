<?php

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\INotifyService;
use App\Interfaces\INotTypeService;
use App\Interfaces\IChannelConfig;
use App\Interfaces\INotType;
use App\Jobs\SendMultipleNotification;
use App\Models\NotificationLog;


//nclude 'vendor/autoload.php';

class SendNotificationService 
{

    public function __construct(INotifyService $notifyService, INotTypeService $notTypeService,
        IChannelConfig $channelConfigRepo, INotType $notTypeRepo)
    {
        $this->notifyService = $notifyService;
        $this->notTypeService = $notTypeService;
        $this->channelConfigRepo = $channelConfigRepo;
        $this->notTypeRepo = $notTypeRepo;
    }


    public function sendNotification(Request $request)
    {
        $request->programId = 1;
        $rules = [
            'not_type'=> 'required|string',
            'variables'=> 'required|array',
            'variables.*'=> 'nullable|string',
            'recipient' => 'required|string',
            'immediate' =>'nullable|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors());
        }

        // Validate Notification Type against program
        // $notType = $this->notTypeService->checkType($request);

        //For these particular program are these channels configured
        $request->channelsConfigured = $channelsConfigured = $this->channelConfigRepo->getAll(["program_id"=>request()->programId]);
        if(!$channelsConfigured){
            return $this->sendBadRequestResponse("You do not have no channels configured");
        }
        
        $notType = $this->notTypeService->checkType($request);
        if(!$notType->status)
        {
            return $this->sendBadRequestResponse($notType->error);
        }

        // Validate Recipient based on channel type
        $checkRecipient = $this->notTypeService->validateRecipient($request->recipient);
        if(!$checkRecipient->status)
        {
            return $this->sendBadResponseLog($checkRecipient->error);
        }

        // Send to receipient
        $sends = $this->send($notType->data, $request);
        foreach($sends as $send){
            if(!$send->status){
                return $this->sendBadResponseLog($send->error);
            }
        }
        

        return $this->sendSuccessResponseLog('', 'Notification sent successfully');
    }


    public function send(Object $notType, Request $request)
    {
        // SendMultipleNotification::dispatchNow($request->variables, $notType, $request->recipient);

        // Get Template with variables in it
        $templates = $this->notifyService->grabTemplates($notType, $request->variables, request()->programId);
        if(count($templates) === 0)
        {
            return (object)[
                'status' => false,
                'error' => 'Unable to get a template'
            ];
        }
       
            request()->templateContents = $templates;
            $this->notifyService->setTemplateAndTypes($templates);
       
        

        // Use configuration based on channel to send notification
        return $this->notifyService->push($request->recipient, $request->programId);
    }


    public function sendMultipleNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'not_type'=> 'required|string|exists:notification_types,slug',
            'details' => 'required|array',
            'details.*' => 'required|array',
            'details.*.recipient' => 'required|string',
            'details.*.variables' => 'required|array',
            'details.*.variables.*' => 'string',
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors());
        }

        // Validate Notification Type against program
        $notType = $this->notTypeService->checkType($request, 1); // For bulk notification

        if(!$notType->status)
        {
            return $this->sendBadRequestResponse($notType->error);
        }

        // Send to receipient
        $send = $this->sendMultiple($notType->data, $request);

        if(!$send->status){
            return $this->sendBadRequestResponse($send->error);
        }

        return $this->sendSuccessResponse('', $send->message);
    }


    private function sendMultiple(Object $notType, Request $request)
    {
        foreach($request->details as $notEntry){
            //sendMultipleNotification::dispatch($notEntry['variables'], $notType, $notEntry['recipient']);
                // ->afterResponse()
                // ->onQueue('notification')
                // ->delay(now()->addSeconds(25));
        }

        return (object)['status' => true, 'message' => 'Notifications have been queued and will be dispatched'];
    }
    public function sendSuccessResponse($result, $message)
    {
        $response = [
            'success' => true,
            'status' => 1,
            'data'    => $result,
            'message' => $message,
            'status_code' => 200
        ];

        return response()->json($response, 200);
    }


    public function sendSuccessResponseLog($result, $message)
    {
        NotificationLog::updateLog([
            'content' => @request()->templateContents[0]->content,
            'channel' => @request()->channelCode,
            'status' => true, // true
            'result' => $message
        ]);

        $response = [
            'success' => true,
            'status' => 1,
            'data'    => $result,
            'message' => $message,
            'status_code' => 200
        ];

        return response()->json($response, 200);
    }

    // No Loggin
    protected function sendBadRequestResponse($error, $errorMessages=[])
    {
        $response = [
            'success'=> false,
            'status'=> 0,
            'message'=> $error,
            'error'=> $errorMessages
        ];

        return response()->json($response,400);
    }


    protected function sendBadResponseLog($errors)
    {
        NotificationLog::updateLog([
            'content' => @request()->templateContents[0]->content,
            'channel' => @request()->channelCode,
            'status' => false, // false
            'result' => $errors
        ]);

        return response()->json([
            "message" => "Invalid user request",
            "error" => $errors,
            "status" => 0,
            "status_code" => 400,
        ], 400);
    }


    protected function sendNotFoundResponse($message, $data = [])
    {
        $response = [
            "message" => $message,
            "status" => 0,
            "status_code" => 404,
        ];
        if (count($data))
            $response["data"] = $data;

        return response()->json($response, 404);
    }

    protected function sendUnAuthorisedResponse($message = "Unauthorised request")
    {
        $response = [
            "message" => $message,
            "status" => 0,
            "status_code" => 401,
        ];
        return response()->json($response, 401);
    }

    // public static function sendMail($mail_subject, $mail_body,$bcc_mails, $name_arr, $recipient){
    //     $mail = new PHPMailer(true);

    //     try {
    //       $mail->isSMTP();                      // Set mailer to use SMTP 
    //       $mail->Host = 'smtp.gmail.com';       // Specify main and backup SMTP servers 
    //       $mail->SMTPAuth = true;               // Enable SMTP authentication 
    //       $mail->Username = 'ojiodujoachim95@gmail.com';   // SMTP username 
    //       $mail->Password = 'JUne2995';   // SMTP password 
    //       $mail->SMTPSecure = 'tls';            // Enable TLS encryption, `ssl` also accepted 
    //       $mail->Port = 587; 
            
    //         //Recipients
    //         $mail->setFrom('contactus@perxclm.com', 'Loyalty Manager');
    //         $mail->addAddress($recipient, $name_arr[0] . ' ' . $name_arr[1] );     //Add a recipient
    //         //$mail->addAddress('Joachim@loyaltysolutionsnigeria.com');               //Name is optional
    //         //$mail->addReplyTo('contactus@perxclm.com', 'Loyalty Manager');
    //         //$mail->addCC('joseph@loyaltysolutionsnigeria.com');
    //         $mail->addBCC('ojiodujoachim@gmail.com');
    //         if(!empty($bcc_mails)){
    //             $mail->addBCC($bcc_mails);
    //         }
    //         $mail->isHTML(true);                                  //Set email format to HTML
    //         $mail->Subject = $mail_subject;
    //         $mail->Body    = $mail_body;
    //         $mail->AltBody = 'You have been enrolled in the GEMZONE!


    //         GEMZONE is Access Bank"s points based rewards program designed specially for you.
            
            
    //         It"s our way of thanking you for your';

    //         $mail->send();
    //          'Message has been sent';
    //             } catch (Exception $e) {
    //                  "Message could not be sent. Mailer Error: {$mail->ErrorInfo} $e";
    //             }
    // } 

}