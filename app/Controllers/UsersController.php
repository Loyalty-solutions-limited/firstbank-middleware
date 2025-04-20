<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Enrollment;
use Illuminate\Support\Str;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use App\Models\EnrolReportLog;
use App\Imports\EnrollmentsImport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use App\Services\SendNotificationService;
use Illuminate\Support\Facades\Validator;
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = DB::table('users'); //->select('id');
                   // ->select('id', 'first_name', 'middle_name', 'last_name', 'loyalty_program_id', 'phone_number', 'email', 'current_bal', 'member_reference', 'first_login', 'first_login_time', 'terms_agreed', 'blocked_points', 'last_change_password', 'status','created_at');
            //if (isset($status)) {
            
        $data = [];
            $data['status'] = true;
            $data['status_code'] = 1;
            $data['data'] = $users->get();
            return $data;
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
    public function show(Request $request, $id)
    {
        //enable-disable user
        $data = array();
        $user = User::find($id);
        $user->status = $request->status;
        if(
        $user->save()) {
        
            $data['status'] == 200;
            $data['message'] == 'user status updated';
        }else{
            $data['status'] == 200;
            $data['message'] == 'operation failed, try again';
        }
        return $data;
        
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