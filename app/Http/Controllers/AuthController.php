<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CompanyService;
use App\Services\ProgramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    //
    public function login(Request $request)
    {

         $validator = Validator::make($request->all(), [
             'email' => 'required|email',
             'password' => 'required|string'
         ]);

         if($validator->fails())
         {
             return $this->sendBadRequestResponse($validator->errors());
         }

        // 
        $user = User::where(['email' => $request->email])->first();

        if ($user == null) {
            return $this->sendUnauthoriseRequest(['error' => "Invalid email and password combinationn $user"]);
        }

        if (Hash::check("password", $user->password)) {
            $companyService = new CompanyService(1);
            $program = new ProgramService();
            $success['token'] =  $user->createToken('MyApp',['admin'])->accessToken;
            $success['first_name'] = $user->first_name;
            $success['last_name'] = $user->last_name;
            $success['program_data'] = $program->getProgramName(env('COMPANY_ID ', 1));
            $success['company_data']  = $companyService->getCompanyDetails()->get();

            return $this->sendSuccessResponse($success, 'User login successfully.');
        }
        else {
            return $this->sendUnauthoriseRequest(['error' => 'Invalid email and password combination']);
        }

    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_group_id' => 'required|numeric|exists:user_groups,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|numeric',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }

        $check_user = $this->check_user_exists($request->email);
        if($check_user){

            return $this->sendBadRequestResponse([], "Email address already exists");
        }
        $hash_password = Hash::make($request->password);
        $insert = new User;
        $insert->user_group_id = $request->user_group_id;
        $insert->first_name = $request->first_name;
        $insert->last_name = $request->last_name;
        $insert->email = $request->email;
        $insert->phone_number = $request->phone_number;
        $insert->password = $hash_password;
        //$insert->phone_number = $request->phone_number;
        $insert->save();

        if($insert)
        {
            return $this->sendSuccessResponse("User creation successful", $insert);
        }
        else
        {
            return $this->sendBadRequestResponse($insert->errors(), "User not created");
        }

    }

    public function viewUsers(Request $request){

        $get_data = $this->view_users_paginate();
        return $this->sendSuccessResponse("Success", $get_data);

    }



    // support functions

    private function view_users_paginate(){

        return User::select('users.id', 'first_name','last_name','email','phone_number', 'last_change_password', 'users.created_at', 'users.status', 'users.updated_at', 'user_groups.name')
        ->leftjoin('user_groups', 'user_groups.id', 'users.user_group_id')
        ->orderby('id', 'desc')
        ->paginate();

    }
    private function check_user_exists($email){

        return User::where('email', $email)->first();
    }

    private function update_status($id, $status)
    {
        try
        {

            $edit = User::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    private function change_user_password($id, $password)
    {
        try
        {

            $hash_password = Hash::make($password);
            $date = date('YYYY-MM-DD hh:mm:s');
            $edit = User::where('id', $id)->update(['password' => $hash_password, 'last_change_password'=>$date]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    private function check_user_password($id, $password)
    {
        try
        {

            $retrieve = DB::table('users')->select('id','password')->first();
            $hash_password = $retrieve->password;

            if(hash::check($password, $hash_password))
            {
                return true;
            }
            else
            {
                return false;
            }

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function edit_user_name($id, $first_name, $last_name)
    {
        try
        {

            $edit = User::where('id', $id)->update(['first_name' => $first_name,  'last_name'=>$last_name]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    private function edit_user_contact($id, $phone = null, $email)
    {
        try
        {

            $edit = User::where('id', $id)->update(['phone_number' => $phone, 'email' =>$email]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    public function UpdateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'nullable|numeric'
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try{

            $output = $this->update_profile($request->user()->id,$request->email,$request->first_name,$request->last_name,$request->phone);

            if($output){
               return $this->sendSuccessResponse('Profile Updated', $output);
            }
        }

        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
    }

    private function update_profile($id,$email,$first_name,$last_name,$phone){
        $update = User::where('id',$id)->first();
            $update->first_name = $first_name;
            $update->last_name = $last_name;
            $update->email = $email;
            $update->phone_number = $phone;
            $update->save();

            return $update;
    }

    public function profile(Request $request){

        try{

            $output = $this->getProfile($request->user()->email);

            if($output){
               return $this->sendSuccessResponse('User Profile', $output);
               //return $request->route()->getName();
            }
        }

        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function getProfile($data){
        $user = DB::table('users')
        ->join('user_groups', 'user_groups.id', '=', 'users.user_group_id')
        ->where('email', $data)
        ->get();

        return $user;
    }
}