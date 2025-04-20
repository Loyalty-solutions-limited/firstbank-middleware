<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CRPassword extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $current_password = $request->old_password;
        $desired_paasword = $request->cur_password;
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (count($user) == 0){
            return array("staus"=>404, "message"=>"user not found");
        }
        $hashed_password = $user->password;
        if (Hash::check($current_password, $hashed_password)){
            $hash_desired_password = Hash::make($desired_paasword);
            User::where('email', $email)->update(['password'=>$hash_desired_password]);
            $mail_body = "<b>Your new access pass is: $desired_paasword </b>";
            $mail_body_alt = strip_tags($mail_body);
            $mail_content = array($mail_body, $mail_body_alt);
            //EmailDispatcher::sendMail('Successful Reset', $mail_content,$email,array($user->first_name, $user->last_name));
            return array("status"=>200, "message"=>"password changed");
        }else{
            return array("status"=>300, "message"=>"operation failed!, please try again"); 
        }
        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}