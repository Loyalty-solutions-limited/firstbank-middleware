<?php

namespace App\Http\Controllers;

use App\Interfaces\IChannel;
use App\Interfaces\INotType;
use App\Interfaces\IVariable;
use App\Interfaces\IProgram;
use App\Interfaces\IEmailGroup;
use App\Interfaces\INotTypeMailGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class NotificationTypeController extends Controller
{
    public $not_types, $channel, $variable, $program, $email_group;
    public $prog_not_type_mail_group;
    

    public $limit =15;

    public function __construct(INotType $not_type, IChannel $channel, 
    IVariable $variable, IProgram $program, IEmailGroup $email_group, INotTypeMailGroup $prog_not_type_mail_group)
    {
        $this->not_types = $not_type;
        $this->channel = $channel;
        $this->variable = $variable;
        $this->program = $program;
        $this->email_group = $email_group;
        $this->prog_not_type_mail_group = $prog_not_type_mail_group;
    }

    public function all()
    {
        $for_dropdown = request()->query('dropdown');

        if(!is_null($for_dropdown) && ($for_dropdown == true)){
             $types = $this->not_types->getItem(['status'=> 1], ['name', 'slug','status']);
        }else{
            if (request()->query('limit')) {
                $this->limit = request()->query('limit');
            }
            
            $types = $this->not_types->selectItems(['status' => 1],["created_at","id","name","slug","status"],$this->limit);
        }

        return $this->sendSuccessResponse('',$types);
       
    }

    public function create(Request $request,$program_slug)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string',
            'description'=> 'required|string',
             
            "groups" =>'nullable|array',
            "groups.*.group_ids" => 'nullable|array',
            "groups.*.email_copy" => 'required|string'
        ]);
        
        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first(), $validator->errors());
        }

        // $channel = $this->channel->findItem(['slug'=> $request->get('channel_type_slug')]);
        $program_id = $request->programId;
        // if(is_null($channel)){
        //     return $this->sendBadRequestResponse('Channel type not found');
        // }

        // if($channel->status == 0){
        //     return $this->sendBadRequestResponse('Channel type currently disabled');
        // }

        $slug = Str::slug( $request->get('name'), '-');

        $check_slug = $this->not_types->findItem(['slug'=> $slug]);

        if(!is_null($check_slug)){

            return $this->sendBadRequestResponse('Notification type with the following details passed already exists');

        }
        $errors = [];
        $success = [];
  
        foreach($request->groups as $mail_group){
            
            if(!($mail_group['email_copy'] == "bcc" || $mail_group['email_copy'] == "cc")){
                return $this->sendBadRequestResponse("Incorrect field set for email_copy");
            }
        }
        DB::beginTransaction();
        try{
            $store = $this->not_types->insertGetId([
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'slug' => $slug,
                // 'channel_type_id' => $channel->id
            ]);
            if(!$store){
                throw new \Exception("Error creating Notification type");
            }
            foreach ($request->groups as $mail_group) 
            {
                
                //check if the group belong to the program
                foreach($mail_group["group_ids"] as $group_id)
                {
                    $check_group = $this->email_group->belongToprogrm($program_id,$group_id);
                    if (is_null($check_group)) {
                        array_push($errors, "The $program_slug specified or group_id (" . $mail_group['group_id'] .") does not exist");
                    } else {
                        
                        $mail_group_inst = $this->prog_not_type_mail_group
                        ->updateOrInsert([ "group_id" => $check_group->id, 
                                "program_id" => $program_id,
                                "not_type_id" => $store],
                                ["email_copy" =>$mail_group['email_copy'] ]
                            );
                            if(!$mail_group_inst){
                                throw new \Exception("link was unable to be establish between group". $check_group->id . " and notification_type". $store->name);
                            }
                            array_push($success, $check_group->name);
                    }
                }
                
                
                    
                   

            }
            DB::commit();
        }
        catch(\Exception $err)
        {
            
            DB::rollback();
            return $this->sendBadRequestResponse($err->getMessage() || "Error creating Notification type");
        }
        

       
        $results['failed_email_groups'] = $errors;
        $results['total_added'] = count($success);
        $results['total_failed'] = count($errors);

        if (count($errors) > 0) {
            $results['reason'] = 'Groups do not belong to program';
        }


        return $this->sendSuccessResponse( 'Notification and groups linked successfully',$results);

       

    }

    public function update(Request $request, $not_type_slug)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            // 'channel_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $type = $this->not_types->findItem(['slug'=> $not_type_slug]);

        if(is_null($type)){
            return $this->sendBadRequestResponse('Notification type not found');
        }

        if($type->status == 0){
            return $this->sendBadRequestResponse('Notification type is currently disabled');
        }

        // $channel = $this->channel->findItem(['id' => $request->get('channel_id')]);

        // if (is_null($channel)) {
        //     return $this->sendBadRequestResponse('Channel not found');
        // }

        $update = $this->not_types->updateItem(['id'=> $type->id], [
            'name'=> $request->get('name'),
            'description'=> $request->get('description')
           
            // 'channel_id'=> $channel->id
        ]);

        if(!$update){
            return $this->sendBadRequestResponse('Error updating notification type');
        }

        return $this->sendSuccessResponse('Notification type updated successfully');

    }

    public function disable($not_type_slug)
    {
        $type = $this->not_types->findItem(['slug' => $not_type_slug]);

        if (is_null($type)) {
            return $this->sendBadRequestResponse('Notification type not found');
        }

        $disable = $this->not_types->updateItem(['id'=> $type->id], [
            'status'=> 0
        ]);

        if(!$disable){
            return $this->sendBadRequestResponse('Error disabling notification type');
        }

        return $this->sendSuccessResponse('Notification type disabled successfully');
    }

    public function enable($not_type_slug)
    {
        $type = $this->not_types->findItem(['slug' => $not_type_slug]);

        if (is_null($type)) {
            return $this->sendBadRequestResponse('Notification type not found');
        }

        $enable = $this->not_types->updateItem(['id' => $type->id], [
            'status' => 1
        ]);

        if (!$enable) {
            return $this->sendBadRequestResponse('Error enable notification type');
        }

        return $this->sendSuccessResponse('Notification type enabled successfully');
    }


    public function variables($not_type_slug)
    {
        $type = $this->not_types->findItem(['slug' => $not_type_slug]);

        if (is_null($type)) {
            return $this->sendBadRequestResponse('Notification type not found');
        }

        $notification_type = $this->variable->notification_type_variables($type->id);
        
        return $this->sendSuccessResponse( '',$notification_type);


    }
    
}
