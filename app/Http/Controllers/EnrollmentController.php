<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use App\Models\EnrolReportLog;
use App\Imports\EnrollmentsImport;
use App\Services\HandleBulkUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use App\Services\SendNotificationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class EnrollmentController extends Controller
{

    public function __construct(SendNotificationService $notService){
        $this->sendNotService = $notService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        $user = Enrollment::where(['email' => $request->email])->first();
        if ($user == null) {
            return $this->sendUnauthoriseRequest(['error' => 'Invalid email and password combination']);
        }
        if (Hash::check($request->password, $user->password)) {
            $success['token'] =  $user->createToken('MyApp', ['user'])->accessToken;
            $success['name'] = $user->first_name;
            $success['terms'] = $user->terms_agreed;

            if ($user->first_time_login == 0) {
                $this->update_first_login($user->id);
            }

            return $this->sendSuccessResponse($success, 'User login successfully.');
        } else {
            return $this->sendUnauthoriseRequest(['error' => 'Invalid email and password combination']);
        }
    }

    public function Enrollment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'string|nullable',
            'email' => 'required|email|unique:enrollments',
            'loyalty_program_id' => 'required|numeric',
            'member_reference' => 'string|required',
            'branch' => 'required|numeric',
            'phone' => 'nullable|numeric',
            'tier_id' => 'required|numeric',
            'loyalty_number' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {
            $response = $this->add_enrollment($request->first_name, $request->middle_name, $request->last_name, $request->branch, $request->loyalty_program_id, $request->email, $request->phone, $request->member_reference, $request->tier_id, $request->loyalty_number);
            if (!$response) {
                //Insert Failed Enrollment Log
                $this->EnrolLog($request->first_name, $request->last_name, $request->email, $request->phone, $response->id, 'branchCode', $file_id=0, false, 'Customer could not be enrolled');
                return $this->sendBadRequestResponse('Error', 'Customer could not be added');
            }
            //Insert successful Enrollment log
            $this->EnrolLog($request->first_name, $request->last_name, $request->email, $request->phone, $response->id, 'branchCode', $file_id=0, true, 'Customer enrolled successfully');

            $data = [];
            $data["email"] = $request->email;
            $data["first_name"] = $request->first_name;
            $data["pin"] = $response->pin;
            $data["password"] = $response->password;
            //$this->sendMail($request, $data);

            return $this->sendSuccessResponse('Success', $response);
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function add_enrollment($first_name, string $middle_name = null, $last_name, $branch, $loyalty_program_id, $email, int $phone=null, $member_no, $tier_id, $loyalty_number)
    {
        $pin = time();
        $password = $first_name.'@'.time();
        $harsh_pass = Hash::make($password);
        $hash_pin = Hash::make($pin);
        $insert = new Enrollment();
        $insert->first_name = $first_name;
        $insert->middle_name = $middle_name;
        $insert->last_name = $last_name;
        $insert->branch_id = $branch;
        $insert->loyalty_program_id = $loyalty_program_id;
        $insert->email = $email;
        $insert->phone_number = $phone;
        $insert->password = $harsh_pass;
        $insert->tier_id = $tier_id;
        $insert->loyalty_number = $loyalty_number;
        $insert->member_reference = $member_no;
        $insert->first_login = 0;
        $insert->terms_agreed = 0;
        $insert->pin = $hash_pin;
        $insert->save();

        $insert["pin"] = strval($pin);
        $insert["password"] = $password;
        return $insert;
    }

    private function update_first_login($user_id)
    {
        return Enrollment::where('id', $user_id)->update(['first_login' => 1, 'first_login_time'=> Carbon::now()]);
    }

    private function edit_enrollment_name($id, $first_name, string $middle_name = null, $last_name)
    {
        try {
            $edit = Enrollment::where('id', $id)->update(['first_name' => $first_name, 'middle_name' =>$middle_name, 'last_name'=>$last_name]);

            return true;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function edit_enrollment_contact($id, int $phone = null, $email)
    {
        try {
            $edit = Enrollment::where('id', $id)->update(['phone_number' => $phone, 'email' =>$email]);

            return true;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function change_enrollment_password($id, $password)
    {
        try {
            $hash_password = Hash::make($password);
            $date = date('YYYY-MM-DD hh:mm:s');
            $edit = Enrollment::where('id', $id)->update(['password' => $hash_password, 'last_change_password'=>$date]);

            return true;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function check_enrollment_password($id, $password)
    {
        try {
            $retrieve = DB::table('enrollments')->select('id', 'password')->first();
            $hash_password = $retrieve->password;

            if (hash::check($password, $hash_password)) {
                return true;
            } else {
                return false;
            }
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }


    private function change_enrollment_pin($id, $pin)
    {
        try {
            $hash_pin = Hash::make($pin);
            $edit = Enrollment::where('id', $id)->update(['pin' => $hash_pin]);

            return true;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function check_enrollment_pin($id, $pin)
    {
        try {
            $retrieve = DB::table('enrollments')->select('id', 'pin')->first();
            $hash_pin = $retrieve->pin;

            if (hash::check($pin, $hash_pin)) {
                return true;
            } else {
                return false;
            }
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function reset_enrollment_pin($id)
    {
        try {
            $pin = time();
            $hash_pin = Hash::make($pin);
            $edit = Enrollment::where('id', $id)->update(['pin' => $hash_pin]);

            return $pin;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function first_time_login($id)
    {
        try {
            $date = date('YYYY-MM-DD hh:mm:s');
            $edit = Enrollment::where('id', $id)->update(['first_login' => 1, 'first_login_time'=>$date]);

            return true;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function check_first_time_login($id)
    {
        try {
            $retrieve = DB::table('enrollments')->select('id', 'first_login')->first();
            $first_login = $retrieve->first_login;

            if ($first_login == 0) {
                return true;
            } else {
                return false;
            }
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function agree_terms($id)
    {
        try {
            $edit = Enrollment::where('id', $id)->update(['terms_agreed' => 1]);

            return true;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function check_terms($id)
    {
        try {
            $retrieve = DB::table('enrollments')->select('id', 'terms_agreed')->first();
            $first_login = $retrieve->terms_agreed;

            if ($first_login == 0) {
                return true;
            } else {
                return false;
            }
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }


    private function update_status($id, $status)
    {
        try {
            $edit = Enrollment::where('id', $id)->update(['status' => $status]);

            return true;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    public function getAllEnrollments(){
        return $this->view_enrollments();
    }

    private function view_enrollments()
    {
        try {
            $view = DB::table('enrollments');
                    //->select('id', 'first_name', 'middle_name', 'last_name', 'loyalty_program_id', 'phone_number', 'email', 'current_bal', 'member_reference', 'first_login', 'first_login_time', 'terms_agreed', 'blocked_points', 'last_change_password', 'status','created_at');
            //if (isset($status)) {
                //$view->where('status', $status);
            //}
            //if (isset($loyalty_program_id)) {
                //$view->where('loyalty_program_id', $loyalty_program_id);
            //}

            $data = [];
            $data['status'] = true;
            $data['status_code'] = 1;
            $data['data'] = $view->get();

            return $data;
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    public function get_enrollment_by_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loyalty_number' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {
            $customer = $this->getEnrollmentByNumber($request->loyalty_number);
            // dd($customer);

            if ($customer->isEmpty()) {
                return $this->sendBadRequestresponse(['error' => 'Invalid Member Number']);
            }

            return $this->sendSuccessResponse('Success', $customer);
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function getEnrollmentByNumber($member_no)
    {
        $customer = DB::table('enrollments')
        ->join('loyalty_programs', 'loyalty_programs.id', '=', 'enrollments.loyalty_program_id')
        ->join('branches', 'branches.id', '=', 'enrollments.branch_id')
        ->join('tiers', 'tiers.id', '=', 'enrollments.tier_id')
        ->where('loyalty_number', $member_no)
        ->get();

        return $customer;
    }

    public function searchEnrollment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_query' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {
            $result = $this->search_enrollment($request->search_query);
            if ($result->isEmpty()) {
                return $this->sendBadRequestresponse('error', 'No Customer with '.$request->search_query.' in our record');
            }
            return $this->sendSuccessResponse('Success', $result);
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function search_enrollment($res)
    {
        $data  = DB::table('enrollments')
        ->join('loyalty_programs', 'loyalty_programs.id', '=', 'enrollments.loyalty_program_id')
        ->join('branches', 'branches.id', '=', 'enrollments.branch_id')
        ->join('tiers', 'tiers.id', '=', 'enrollments.tier_id')
        ->where('first_name', 'LIKE', "%{$res}%")
        ->orWhere('last_name', 'LIKE', "%{$res}%")
        ->orWhere('email', 'LIKE', "%{$res}%")->get();

        return $data;
    }

    public function editMember($id)
    {
        try {
            $memb = $this->edit_member($id);
            if (!$memb) {
                return $this->sendBadRequestresponse('error', 'Member not found');
            }

            return $this->sendSuccessresponse('Success', $memb);
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }
    private function edit_member($id)
    {
        $member = DB::table('enrollments')
        ->join('tiers', 'tiers.id', 'enrollments.tier_id')
        ->where('enrollments.id', $id)
        ->get();

        // if($member == null){
        //     return false;
        // }

        return $member;
    }

    public function updateMemberInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'string',
            'email' => 'required|email',
            'phone_number' => 'required|numeric',
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {
            $updated = $this->update_member_info($request);
            if (!$updated) {
                return $this->sendBadRequestResponse([], 'Could not update profile');
            }

            return $this->sendSuccessResponse('Success', 'Member Profile Updated Successfully');
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function update_member_info($request)
    {
        DB::table('enrollments')->where('id', $request->id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number
        ]);

        return true;
    }

    public function updateMemberContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'birthday' => 'date',
            'anniversary' => 'date',
            'gender' => 'string',
            'id' => 'numeric|required'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {
            $updated = $this->update_member_contact($request);
            if (!$updated) {
                return $this->sendBadRequestResponse([], 'Could not update contact information');
            }

            return $this->sendSuccessResponse('Success', 'Member Contact Information Updated Successfully');
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function update_member_contact($request)
    {
        DB::table('enrollments')->where('id', $request->id)->update([
            'birthday' => $request->birthday,
            'anniversary' => $request->anniversary,
            'gender' => $request->gender
        ]);

        return true;
    }

    public function getStatement($loyalty_number)
    {
        $data = $this->get_statement($loyalty_number);
        if (!$data) {
            return $this->sendBadRequestResponse('Error', 'Transaction details not found');
        }

        return $this->sendSuccessResponse('Success', $data);
    }

    public function updateMemberTier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tier' => 'required|numeric',
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {
            $updated = $this->update_member_tier($request);
            if (!$updated) {
                return $this->sendBadRequestResponse([], 'Could not update tier information');
            }

            return $this->sendSuccessResponse('Success', 'Member Tier Name Updated Successfully');
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function update_member_tier($request)
    {
        DB::table('enrollments')->where('id', $request->id)->update([
            'tier_id' => $request->tier
        ]);

        return true;
    }
    private function pullTransactions($id)
    {
        $output = DB::table('transactions')->select('trans_type', 'description', 'amount_out', 'amount_in', 'balance', 'created_at')
        ->where('member_id', $id)
        ->get();
        return $output;
    }

    private function get_statement($id)
    {
        $enroller = DB::table('enrollments')->where('loyalty_number', $id)->first();
        if (!$enroller) {
            return $this->sendBadRequestResponse('Error', 'Member Not found');
        }

        return $this->pullTransactions($enroller->id);
    }

    public function searchStatement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => '',
            'end_date' => '',
            'loyalty_number' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        try {
            $response = $this->search_statement($request->start_date, $request->end_date, $request->loyalty_number);
            return $this->sendSuccessResponse('Success', $response);
        } catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }


    private function search_statement($var1, $var2, $number)
    {
        $member = DB::table('enrollments')->where('loyalty_number', $number)->first();
        if (!$member) {
            return $this->sendBadRequestResponse('Error', 'Member Not found');
        }

        if (empty($var1) && empty($var2)) {
            return $this->pullTransactions($member->id);
        } else {
            $result =  DB::table('transactions')->select('trans_type', 'description', 'amount_out', 'amount_in', 'balance', 'created_at')
                   ->whereBetween('created_at', [$var1, $var2])
                   ->where('member_id', $member->id)
                   ->get();
            return $result;
        }
    }


    public function uploadEnrollments(Request $request)
    {
        //return $request->all(); exit;
        $validator = Validator::make($request->all(), [
        'data' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors());
        }

        $service = new HandleBulkUploads();

        return $service->uploadEnrolmentData($request);


    }


    // public function uploadEnrollments(Request $request){

//     $file = $request->upload_file->store('import');//stores the file in the server

//     $data = (new EnrollmentsImport)->import($file);
//     // $data->import($file);
//     // if($data->failures()->isNotEmpty()){
//     //     return $data->failures();
//     // }

//         if($data){
//        return $this->sendSuccessResponse('Success',  $data->import($file));

//     }else{
//         return $this->sendBadRequestResponse('Error');
//     }

    // }

    public function sendMail(Request $request, $arr)
    {
        $request->request->add(["not_type" => "enrollment-notification"]);
        $request->request->add(["recipient" => $arr["email"]]);
        $request->request->add(["immediate" => "true"]);
        $request->request->add(["variables" =>
        [
            "pin" => $arr["pin"],
            "password" => $arr["password"],
            "first_name" => $arr["first_name"]
        ]
    ]);

        return $this->sendNotService->sendNotification($request);
    }

    public function EnrolLog(
        string $firstname,
        string $lastname,
        string $email,
        string $phoneno,
        int $customerid,
        string $branch_code,
        int $file_id,
        int $statuscode,
        string $status_message
    ) {
        return $this->attepmtLogging($firstname, $lastname, $email, $phoneno, $customerid, $branch_code, $file_id, $statuscode, $status_message);
    }


    private function attepmtLogging(
        string $firstname,
        string $lastname,
        string $email,
        string $phoneno,
        int $customerid,
        string $branch_code,
        int $file_id,
        int $statuscode,
        string $status_message
    ) {
        return EnrolReportLog::create([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phoneno' => $phoneno,
            'customerid' => $customerid,
            'branchcode' => $branch_code,
            'fileid' => $file_id == null? 0: $file_id,
            'status_code' => $statuscode,
            'status_message' => $status_message
        ]);
    }
    public function insertFileLog($filename,$uploaddate,$uploadedby,$filetype,$totalnumber,$totalerror,$totalpoints,$perxerrors,$status,$email_code,$email_message,$errorreport,$finishdate,$perxsuccess,$uploadsuccess,$perxreport)
    {
        DB::table('file_log')->insert([
        'filename' => $filename,
        'uploaddate' => $uploaddate,
        'uploadedby' => $uploadedby,
        'filetype' => $filetype,
        'totalnumber' => $totalnumber,
        'totalerror' => $totalerror,
        'totalpoints' => $totalpoints,
        'perxerrors' => $perxerrors,
        'status' => $status,
        'email_code' => $email_code,
        'email_message' => $email_message,
        'errorreport' => $errorreport,
        'finishdate' => $finishdate,
        'perxsuccess' => $perxsuccess,
        'uploadsuccess' => $uploadsuccess,
        'perxreport' => $perxreport
    ]);
    }

    public static function getCustomerDetails($membershipID){
        return Enrollment::select('email', 'first_name', 'last_name')->where('membership_id', $membershipID)->first();
    }

    public function whoAmI(Request $request)
    {

        if(!($request->loyalty_number)) return response()->json([
            "message" => "Please, provide a loyalty number",
            "status" => false
        ], Response::HTTP_EXPECTATION_FAILED);

        $user = Enrollment::where('loyalty_number', $request->loyalty_number)
                            ->select('email', 'first_name', 'last_name', 'member_reference')
                            ->first();

        if($user) return response()->json([
            "message" => "Record retrieved successfully",
            "status" => true,
            "user" => $user
        ]);

        return response()->json([
            "message"   =>  $request->loyalty_number . " does not exists",
            "status"    =>  false,
        ], Response::HTTP_NOT_FOUND);
    }

    public function whoAmI2(Request $request){
        //return $request->all();
        if(!($request->member_reference)) return response()->json([
            "message" => "Please, provide a member reference",
            "status"  => false
        ], Response::HTTP_EXPECTATION_FAILED);
        
        $user = Enrollment::where('member_reference', $request->member_reference)
                            ->select('email', 'first_name', 'last_name', 'loyalty_number')
                            ->first();

        if($user) return response()->json([
            "message" => "Record retrieved successfully",
            "status" => true,
            "user" => $user
        ]);

        return response()->json([
            "message" => $request->member_reference . " does not exists",
            "status" => false
        ], Response::HTTP_NOT_FOUND);
    }


}
