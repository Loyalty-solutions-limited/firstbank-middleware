<?php

namespace App\Http\Controllers;

use App\Interfaces\IEmailGroup;
use App\Interfaces\INotType;
use App\Interfaces\IProgNotType;
use App\Interfaces\ITemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    public $template, $not_type, $prog_not_type, $emailGroup;

    public $limit = 15;

    public function __construct(ITemplate $template, INotType $not_type, IProgNotType $prog_not_type, IEmailGroup $emailGroup)
    {
        $this->template = $template;
        $this->not_type = $not_type;
        $this->prog_not_type = $prog_not_type;
        $this->emailGroup = $emailGroup;
    }

    public function program_templates()
    {
        $program_id = request()->programId;

        $for_dropdown = request()->query('dropdown');


        if(!is_null($for_dropdown) && ($for_dropdown == true)){
            $templates = $this->template->getItem(['program_id'=> $program_id], ['id', 'subject', 'content']);
        }else{
            if (request()->query('limit')) {
                $this->limit = request()->query('limit');
            }
            $templates = $this->template->selectItems(['program_id'=> $program_id], ['id','subject','content','created_at','not_type_id', 'program_id'], $this->limit);
        }

        return $this->sendSuccessResponse('', $templates);
    }

    public function notification_templates($_pro, $not_type_slug)
    {
        $program_id = request()->programId;
        $for_dropdown = request()->query('dropdown');

        $type = $this->not_type->findItem(['slug'=> $not_type_slug]);

        if(is_null($type)){
            return $this->sendBadRequestResponse('Notification type not found');
        }

        $check = DB::table('templates')->where('program_id', $program_id)->where('not_type_id', $type->id)->exists();

        if(!$check){
            return $this->sendBadRequestResponse('Notification type do not belong to program');
        }

        
        if (!is_null($for_dropdown) && ($for_dropdown == true)) {
            $templates = $this->template->getItem(['program_id' => $program_id, 'not_type_id'=> $type->id], ['id', 'subject', 'content']);
        }else{

            if (request()->query('limit')) {
                $this->limit = request()->query('limit');
            }

            $templates = $this->template->selectItems(['program_id'=> $program_id, 'not_type_id'=> $type->id], ['id', 'subject', 'content', 'not_type_id', 'program_id','created_at'], $this->limit);
            
        }

        return $this->sendSuccessResponse('', $templates);

    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject'=> 'nullable|string|max:255',
            'content'=> 'required',
            'notification_slug' => 'required|string',
            'channel_id' => 'required|integer',
            'reply_to' => 'nullable|string',
            "name" => 'required|string|max:255'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $program_id = $request->programId;
        $type = $this->not_type->findItem(['slug' => $request->notification_slug]);

        if (is_null($type)) {
            return $this->sendBadRequestResponse('Notification type not found');
        }

        // $check = $this->prog_not_type->check($program_id, $type->id);

        // if (is_null($check)) {
        //     return $this->sendBadRequestResponse('Notification type do not belong to program');
        // }

        $errors = [];
        

        DB::beginTransaction();

        try {

           $create = $this->template->create([
               "name" => $request->name,
                'not_type_id' => $type->id,
                'program_id' => $program_id,
                'channel_id' => $request->channel_id,
                'subject' => $request->get('subject') ?? null,
                'content' => $request->get('content'),
                "reply_to" => $request->get("reply_to") ?? null
            ]);

            DB::commit();

        } catch (\Exception $ex) {

            DB::rollBack();

            return $this->sendBadRequestResponse($ex->getMessage());

        }

 

        

        return $this->sendSuccessResponse( 'New Notification templated successfully',$create);
       

        

    }

    public function update(Request $request, $pro,$template_id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'nullable|string|max:255',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $program_id = $request->programId;
        $template = $this->template->findItem(['id'=> $template_id, 'program_id'=> $program_id]);

        if(is_null($template)){
            return $this->sendBadRequestResponse('Program Template not found');
        }

        $update = $this->template->updateItem(['id'=> $template->id], [
            'subject'=> $request->get('subject') ?? null,
            'content'=> $request->get('content')
        ]);

        if(!$update){
            return $this->sendBadRequestResponse('Error updating program template');
        }

        return $this->sendSuccessResponse('Program template updated successfully');

    }


    public function disable($pro, $template_id)
    {
        $template = $this->template->findItem(['id'=> $template_id]);

        if(is_null($template)){
            return $this->sendBadRequestResponse('Template not found');
        }

        $disable = $this->template->updateItem(['id'=> $template->id], [
            'status'=> 0
        ]);

        if(!$disable){
            return $this->sendBadRequestResponse('Error disabling template');
        }

        return $this->sendSuccessResponse('Template disabled successfully');
    }

    public function enable($pro, $template_id)
    {
        $template = $this->template->findItem(['id' => $template_id]);

        if (is_null($template)) {
            return $this->sendBadRequestResponse('Template not found');
        }

        $disable = $this->template->updateItem(['id' => $template->id], [
            'status' => 1
        ]);

        if (!$disable) {
            return $this->sendBadRequestResponse('Error enabling template');
        }

        return $this->sendSuccessResponse('Template enabled successfully');
    }
   
}


