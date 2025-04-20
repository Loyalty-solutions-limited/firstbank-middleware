<?php

namespace App\Http\Controllers;

use App\Interfaces\IEmail;
use App\Interfaces\IProgram;
use App\Interfaces\ITemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailAddressController extends Controller
{
    public $email, $template, $program;

    public function __construct(IEmail $email, ITemplate $template, IProgram $program)
    {
        $this->email = $email;
        $this->template = $template;
        $this->program = $program;    
    }

    //get emails base on the program
    public function program_emails($program_slug)
    {
        $for_dropdown = request()->query('dropdown');
        $program = $this->program->findItem(['slug' => $program_slug], ["id"]);
        if(!$program){
            return $this->sendBadRequestResponse('No program was found for the slug');
        }
        $program_id = $program->id;
        if(!is_null($for_dropdown) && ($for_dropdown == true)){
            $emails = $this->email->getItem(['status'=> 1, 'program_id'=> $program_id], ['id', 'email']);
        }else{

            if(request()->query('limit')){
                $this->limit =  request()->query('limit');
            }

            $emails = $this->email->program_emails($program_id);
        }

        return $this->sendSuccessResponse( '',$emails);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_slug'=> 'nullable|string',
            'email'=> 'required|email|unique:email_addresses,email',
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $program = 0;
        if($request->get('program_slug') != null){
            $program = $this->program->findItem(['slug'=> $request->get('program_slug')]);

            if(is_null($program)){
                return $this->sendBadRequestResponse("Program not found");
            }

            if($program->status != 1){
                return $this->sendBadRequestResponse("Program not active");
            }

            // if($program->company_id != $request->client_id){
            //     return $this->sendBadRequestResponse("Program do not belong to the client");
            // }
            
            // $program = $program->id;
        }


        $store = $this->email->create([
            'email'=> $request->get('email'),
            
            'program_id'=> $program->id
        ]);

        if(!$store){
            return $this->sendBadRequestResponse("Error adding email");
        }

        return $this->sendSuccessResponse('Email added successfully');

    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email'=>"required|email|unique:email_addresses,email,{$id}",
            'program_slug'=> 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $email = $this->email->findItem(['id'=> $id]);

        if(is_null($email)){
            return $this->sendBadRequestResponse("Email with {$id} not found");
        }

        if($email->status == 0){
            return $this->sendBadRequestResponse("Email is currently disabled");
        }

        $program = $email->program_id;

        if ($request->get('program_slug') != null) {
            $program = $this->program->findItem(['slug' => $request->get('program_slug')]);

            if (is_null($program)) {
                return $this->sendBadRequestResponse("Program not found");
            }

            if ($program->status != 1) {
                return $this->sendBadRequestResponse("Program not active");
            }

            // if ($program->client_id != $request->client_id) {
            //     return $this->sendBadRequestResponse("Program do not belong to the client");
            // }

            $program = $program->id;
        }

        $update = $this->email->updateItem(['id'=>$email->id],[
            'email'=> $request->get('email'),
            'program_id'=> $program
        ]);

        if(!$update){
            return $this->sendBadRequestResponse("Error updating email address");
        }

        return $this->sendSuccessResponse('Email updated successfully');


    }

    public function disable($id)
    {
        $email = $this->email->findItem(['id'=> $id]);

        if(is_null($email)){
            return $this->sendBadRequestResponse("Email not found");
        }

        $disable = $this->email->updateItem(['id'=> $id],['status'=> 0]);

        if(!$disable){
            return $this->sendBadRequestResponse("Error disabling email address");
        }

        return $this->sendSuccessResponse("{$email->email} disabled successfully");
    }


    public function enable($id)
    {
        $email = $this->email->findItem(['id' => $id]);

        if (is_null($email)) {
            return $this->sendBadRequestResponse("Email not found");
        }

        $enable = $this->email->updateItem(['id' => $id], ['status' => 1]);

        if (!$enable) {
            return $this->sendBadRequestResponse("Error enabling email address");
        }

        return $this->sendSuccessResponse("{$email->email} enabled successfully");
    }



    public function delete($id)
    {
        $delete = $this->email->deleteItem(['id'=> $id]);

        if(!$delete){
            return $this->sendBadRequestResponse("Error deleting email address");
        }

        return $this->sendSuccessResponse( 'Email address deleted successfully');
    }


   

}

