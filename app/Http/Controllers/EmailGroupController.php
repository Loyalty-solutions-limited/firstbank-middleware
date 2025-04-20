<?php

namespace App\Http\Controllers;

use App\Interfaces\IEmail;
use App\Interfaces\IEmailGroup;
use App\Interfaces\INotType;
use App\Interfaces\INotTypeMailGroup;
use App\Interfaces\IProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmailGroupController extends Controller
{
    public $email_group,$limit, $email, $prog_not_type_mail_group, $not_type;

    public function __construct(IEmailGroup $email_group, IEmail $email, INotTypeMailGroup $prog_not_type, 
    INotType $not_type, IProgram $program)
    {
        $this->email_group = $email_group;
        $this->email = $email;
        $this->prog_not_type_mail_group = $prog_not_type;
        $this->not_type = $not_type;
        $this->program = $program;
    }

    public function create_mail_group(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string|unique:mailing_groups,name',
        ]); 

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }
        
        
        $store = $this->email_group->create([
            'name'=> $request->get('name'),
            'program_id'=> $request->programId
        ]);

        if(!$store){
            return $this->sendBadRequestResponse("Error creating mail group");
        }

        return $this->sendSuccessResponse('Mail Group created successfully');
    }

    public function get_program_groups()
    {

        $program_id = request()->programId;

        $for_dropdown = request()->query('dropdown');


        if (!is_null($for_dropdown) && ($for_dropdown == true)) {
            $groups = $this->email_group->getItem(['status' => 1, 'program_id'=> $program_id], ['id', 'name','status']);
        } else {
            if (request()->query('limit')) {
                $this->limit = request()->query('limit');
            }

            $groups = $this->email_group->program_groups($program_id, $this->limit);
        }
        
        return $this->sendSuccessResponse( '',$groups);

    }

    public function disable($program_id, $group_id)
    {
        $program_id = request()->programId;
       
        $group = $this->email_group->findItem(['id'=>$group_id,'program_id'=> $program_id]);
       
        if(is_null($group)){
            return $this->sendBadRequestResponse("Email group not found");
        }

        $disable = $this->email_group->updateItem(['id'=> $group->id],['status'=> 0]);

        if(!$disable){
            return $this->sendBadRequestResponse("Error disabling mail group");
        }

        return $this->sendSuccessResponse("{$group->name} disabled successfully");


    }


    public function enable($program_id, $group_id)
    {
        $program_id = request()->programId;

        $group = $this->email_group->findItem(['id' => $group_id, 'program_id' => $program_id]);

        if (is_null($group)) {
            return $this->sendBadRequestResponse("Email group not found");
        }

        $enable = $this->email_group->updateItem(['id' => $group->id], ['status' => 1]);

        if (!$enable) {
            return $this->sendBadRequestResponse("Error enabling mail group");
        }

        return $this->sendSuccessResponse("{$group->name} enabled successfully");

    }



    public function add_mails_to_group(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id'=> 'required|integer',
            'email_ids'=> 'required|array',
            'email_ids.*'=> 'required|integer'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }   

        $group = $this->email_group->findItem(['id'=> $request->get('group_id')]);

        if(is_null($group)){
            return $this->sendBadRequestResponse("Group not found");
        }

        if($group->status == 0){
            return $this->sendBadRequestResponse("Group is currently not active");
        }

        if($group->program_id != $request->programId){
            return $this->sendBadRequestResponse("Group do not belong to program");
        }

        $error_insert = [];
        $success_insert = [];

        foreach ($request->email_ids as $email) {
        
            $check_email = $this->email_group->check_mail($request->get('group_id'), $email);

            $email = $this->email->findItem(['id'=> $email]);
            if ($check_email) {
                array_push($error_insert, $email->email);
            } else {
                $this->email_group->add_email($email->id, $request->get('group_id'));
              
                array_push($success_insert, $email->email);
            }
            
        }

        $results['variable_not_stored'] = $error_insert;
        $results['total_added'] = count($success_insert);
        $results['total_failed'] = count($error_insert);

        if (count($error_insert) > 0) {
            $results['reason'] = 'Email address already exist in the group';
        }

        return $this->sendSuccessResponse( 'Email(s) added successfully to '. $group->name,$results);

    }

    public function remove_mails_from_group(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer',
            'email_ids' => 'required|array',
            'email_ids.*' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $group = $this->email_group->findItem(['id' => $request->get('group_id')]);

        if (is_null($group)) {
            return $this->sendBadRequestResponse("Group not found");
        }

        if ($group->status == 0) {
            return $this->sendBadRequestResponse("Group is currently not active");
        }

        if ($group->program_id != $request->programId) {
            return $this->sendBadRequestResponse("Group do not belong to program");
        }

        foreach ($request->email_ids as $email) {
           $delete = $this->email_group->remove_email($email, $group->id);           
        }

        if (!$delete) {
            return $this->sendBadRequestResponse("Error removing email");
        }

        return $this->sendSuccessResponse("Email(s) removed successfully");
    }

    public function group_emails($program_id, $group_id)
    {
        $for_dropdown = request()->query('dropdown');

        if(!is_null($for_dropdown) && ($for_dropdown == true)){
            $emails = $this->email_group->email_dropdown($group_id);
        }else{
            if (request()->query('limit')) {
                $this->limit = request()->query('limit');
            }
            $emails = $this->email_group->emails($group_id, $this->limit);
        }

        return $this->sendSuccessResponse( '',$emails);
    }

   

    public function delete_mail_groups(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'groups'=> 'required|array',
            'groups.*'=> 'required|integer'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        // $error_insert = [];
        // $success_insert = [];
        DB::beginTransaction();

        try {
            foreach ($request->groups as $group) {

                $this->email_group->deleteItem(['id' => $group]);

                DB::table('mailing_group_emails')->where('group_id', $group)->delete();
            }
        } catch (\Exception $ex) {
            
            DB::rollBack();
            return $this->sendBadRequestResponse($ex->getMessage());
        }
       

        return $this->sendSuccessResponse('Email Groups deleted successfully');
    }

    public function add_groups_to_program_not_type(Request $request, $program_slug)
    {
       
        $validator = Validator::make($request->all(), [
            'groups' => 'required|array',
            'groups.*.group_id' => 'required|integer',
            'groups.*.email_copy' => 'required|string',
            "not_type_slug" => 'required|string'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $program_id = $request->programId;

        $type = $this->not_type->findItem(['slug' => $request->not_type_slug]);

        if (is_null($type)) {
            return $this->sendBadRequestResponse('Notification type not found');
        }

        // $check = $this->prog_not_type->check($program_id, $type->id);

        // if (is_null($check)) {
        //     return $this->sendBadRequestResponse('Notification type do not belong to program');
        // }


        $errors = [];
        $success = [];
  
        foreach($request->groups as $mail_group){
            
            if(!($mail_group['email_copy'] == "bcc" || $mail_group['email_copy'] == "cc")){
                return $this->sendBadRequestResponse("Incorrect field set for email_copy");
            }
        }
        DB::beginTransaction();
        try{
            foreach ($request->groups as $mail_group) {

                //check if the group belong to the program
                $check_group = $this->email_group->belongToprogrm($program_id,$mail_group['group_id']);
                
                if (is_null($check_group)) {
                    array_push($errors, "The $program_slug specified or group_id (" . $mail_group['group_id'] .") specified is invalid");
                } else {
                    
                    $mail_group = $this->prog_not_type_mail_group
                    ->updateOrInsert([ "group_id" => $check_group->id, 
                            "program_id" => $program_id,
                            "not_type_id" => $type->id],
                            ["email_copy" =>$mail_group['email_copy'] ]
                        );
                    if(!$mail_group){
                        throw new \Exception("link was unable to be establish between group and notification_type");
                    }
                    array_push($success, $check_group->name);

                }
            }
            DB::commit();
        }catch(\Exception $err){
            DB::rollback();
            $this->sendBadRequestResponse($err->getMessage());
        }
        
        

          

        $results['failed_email_groups'] = $errors;
        $results['total_added'] = count($success);
        $results['total_failed'] = count($errors);

        if (count($errors) > 0) {
            $results['reason'] = 'Groups do not belong to program';
        }


        return $this->sendSuccessResponse( 'Groups added successfully',$results);

    }

    public function remove_groups_from_program_not_type(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'groups' => 'required|array',
            'groups.*.group_id' => 'required|integer',
            'groups.*.email_copy' => 'required|string',
            'not_type_slug' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors()->first());
        }


        $program_id = $request->programId;

        $type = $this->not_type->findItem(['slug' => $request->not_type_slug]);

        if (is_null($type)) {
            return $this->sendBadRequestResponse('Notification type not found');
        }

        // $check = $this->prog_not_type->check($program_id, $type->id);

        // if (is_null($check)) {
        //     return $this->sendBadRequestResponse('Notification type do not belong to program');
        // }


        $errors = [];
        $success = [];
        DB::beginTransaction();
        try{
            foreach ($request->groups as $mail_group) {

                //check if the group belong to the program
                $check_group = $this->email_group->belongToprogrm( $program_id,$mail_group['group_id']);
    
                if (is_null($check_group)) {
                    array_push($errors,  "The $program_slug specified or group_id (" . $mail_group['group_id'] .") specified is invalid");
                } else {
                    $this->prog_not_type_mail_group->deleteItem(
                        ["group_id" =>$mail_group['group_id'], 
                        "email_copy" =>$mail_group['email_copy'], 
                        "program_id" => $program_id,
                        "not_type_id" => $type->id]);
                    array_push($success, $check_group->name);
                }
            }
            DB::commit();
        }
        catch(\Exception $err){
            DB::rollback();
           return $this->sendBadRequestResponse("Could not delete groups");
        }

       

        $results['failed_email_groups'] = $errors;
        $results['total_removed'] = count($success);
        $results['total_failed_process'] = count($errors);

        if (count($errors) > 0) {
            $results['reason'] = 'Groups do not belong to program';
        }


        return $this->sendSuccessResponse( 'Group(s) removed successfully',$results);
    }
}
