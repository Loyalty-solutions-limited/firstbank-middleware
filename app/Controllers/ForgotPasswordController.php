<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Services\EmailDispatcher;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Services\SendNotificationService;
use Egulias\EmailValidator\Warning\EmailTooLong;

class ForgotPasswordController extends Controller
{
    public function __construct(SendNotificationService $notService){
        $this->sendNotService = $notService;
    }

    public function sendMail(Request $request, $arr){
      
        $request->request->add(["not_type" => "forgot-password"]);
        $request->request->add(["recipient" => $arr["email"]]);
        $request->request->add(["immediate" => "true"]);
        $request->request->add(["variables" => 
            [
                "url" => $arr["link"],
                "first_name" => $arr["first_name"]
            ]
        ]);
        $emailDispatcher = new EmailDispatcher();
        $template_id  = env("RESET_PASSWRD", 4);
        $template  =  EmailTemplate::find($template_id);
        $user = User::where('email', $arr['email']);
        $name_arr = array($user->first_name, $user->last_name);
        $mail_body = $emailDispatcher->replaceString($name_arr, $template->body);
        $subject = $template->subject;
        return $emailDispatcher->sendMail($subject, $mail_body, $arr['email'], $name_arr);
        //return $this->sendNotService->sendNotification($request);

    }

       public function forgot_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'link' => 'required|url'
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {

            $result = $this->forgotPassword($request->email);
            if ($result) {
                
                $me = $result;
                    $token = $me["token"];
                    $name = $me["first_name"];
                    $new_link = $request->link."?email=".$request->email."&token=".$token;
                    $newArray = [];
                    $newArray['email'] = $request->email;
                    $newArray['link'] = $new_link;
                    $newArray['first_name'] = $name;
                    $this->sendMail($request, $newArray);

                    return $this->sendSuccessResponse('Success',$result);
            }
        }
        catch(QueryException $ex)
        {
            //return $ex->getMessage();
            return $this->sendBadRequestResponse('', "An error an occured");
        }
    }

    private function checkResetExist($email){
        return PasswordReset::where('email', $email)->first();
    }

    private function forgotPassword($email)
    {
            $user = User::where('email', $email)->first();

            $expired_date = Carbon::now()->addMinute(15);

            $data = [];
            $token = time();
            $mtoken = md5($token);
            $data['token'] = $mtoken;
            $data['email'] = $email;
            $data['first_name'] = $user->first_name;
            $newPassword = self::randomPassword(8);
            $update_password = User::where('email', $email)->update(['password'=>$newPassword]);
            if ($update_password){
                $mail_body = "<b>Your new access pass is: $newPassword </b>";
                $mail_body_alt = strip_tags($mail_body);
                $mail_content = array($mail_body, $mail_body_alt);
                EmailDispatcher::sendMail('Successful Reset', $mail_content,$email,array($user->first_name, $user->last_name));
                return 1;
            }else{
                return 0; 
            }
        
        //     if($this->checkResetExist($email)){

        //      PasswordReset::where('email', $email)->update(['token' => $mtoken, 'expired_at' => $expired_date]);

        //         return $data;
        //    }else{
        //         DB::table('password_resets')->insert(['email' => $email,'token' => $mtoken, 'expired_at' => $expired_date]);
        //         return $data;

        //    }

    }

    public function verifyResetLink(Request $request){
        try {
            $answer = $this->verifyPasswordReset($request->email, $request->token);
            
            if(!$answer){
                return $this->sendBadRequestResponse('Error', "Invalid Email Address or Token");
            }

            if(Carbon::now()->format('Y-m-d H:i:s') > $answer->expired_at){
               return $this->sendBadRequestResponse('Error', "Token Expired");
            }
            
                return $this->sendSuccessResponse('Success, Kindly proceed to reset your passoword');

        }

        catch(QueryException $ex)
        {
            return $ex->getMessage();
            //return $this->sendBadRequestResponse([], "An error an occured");
        }
    }


    private function verifyPasswordReset($email,$token){

    $updatePassword = DB::table('password_resets')->where(['email' => $email,'token' => $token])->first();

     return $updatePassword;
    
    }

    public function password_reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|confirmed'
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try{
            if($this->passwordReset($request->email,$request->password)){

                return $this->sendSuccessResponse('Password successfully reset');
            }

            return $this->sendBadRequestResponse('Error', 'Password reset failed');
        }

        catch(QueryException $ex)
        {
            return $ex->getMessage();
            //return $this->sendBadRequestResponse([], "An error an occured");
        }
    }

    private function passwordReset($email,$password)
    {
     $user = PasswordReset::where('email', $email)->first();
        if($user){

            DB::transaction(function () use ($email,$password){

            //Update user password
            User::where('email', $email)->update(['password' => Hash::make($password)]);

            //Delete user record on password reset table
            PasswordReset::where('email', $email)->delete();

            });

            return true;
        }else{

            return false;
        }
    }
    private static function randomPassword($length){
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*&#$";
        $splited_chars = str_split($chars);
        $newPassword  = '';
        for($i =0; $i < $length; $i++){
            $index = 2;//removed(0, count($splited_chars) - 1);
            $newPassword .= $splited_chars[$index];
        }
        return $newPassword;
    }

}