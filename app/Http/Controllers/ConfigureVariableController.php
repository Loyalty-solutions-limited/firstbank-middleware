<?php

namespace App\Http\Controllers;

use App\Interfaces\IConfigureVar;
use App\Interfaces\INotChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConfigureVariableController extends Controller
{
    public $configurevar, $channel;

    public function __construct(IConfigureVar $configurevar, INotChannel $channel)
    {
        $this->configurevar = $configurevar;
        $this->channel = $channel;
    }

    public function index()
    {
        $for_dropdown = request()->query('dropdown');

        if(!is_null($for_dropdown) && ($for_dropdown == true)){
            $variables = $this->configurevar->getItem(['status'=> 1], ['id','key', 'channel_id', 'required']);
        }else{

            if(request()->query('limit')){
                $this->limit = request()->query('limit');
            }

            $variables = $this->configurevar->get_all_variables();
        }

        return $this->sendSuccessResponse( '',$variables);
    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channel_code'=> 'required',
            'variables' => 'required|array',
            'variables.*.key' => 'required|unique:config_vars,key',
            'variables.*.required' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $check_channel = $this->channel->findItem(['code'=> $request->get('channel_code')]);

        if(is_null($check_channel)){
            return $this->sendBadRequestResponse("Notification channel not found");
        }

        if($check_channel->status != 1){
            return $this->sendBadRequestResponse("Notification channel not active");
        }

        $error_insert = [];
        $success_insert = [];

        DB::beginTransaction();

        try {
            
            foreach ($request->variables as $variable) {
                
                $check_var = $this->configurevar->var_check($variable['key'], $check_channel->id);

                if ($check_var) {
                    array_push($error_insert, $variable['key']);
                } else {
                    $this->configurevar->create_var($variable['key'], $variable['required'] == "true" ? 1 : 0, $check_channel->id);
                    array_push($success_insert, $variable['key']);
                }

            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendBadRequestResponse($ex->getMessage());

        }

        $results['variable_not_stored'] = $error_insert;
        $results['total_added'] = count($success_insert);
        $results['total_failed'] = count($error_insert);

        if (count($error_insert) > 0) {
            $results['reason'] = 'Variable already exist for notification channel';
        }

        return $this->sendSuccessResponse( 'Configuration variable(s) added successfully',$results);


    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'channel_code' => 'required',
            'key' => "required|unique:config_vars,key,{$id}",
            'required' => 'nullable|string'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $config_var = $this->configurevar->findItem(['id'=> $id]);

        if(is_null($config_var)){
            return $this->sendBadRequestResponse("Configuration variable not found");
        }

        $check_channel = $this->channel->findItem(['code' => $request->get('channel_code')]);

        if (is_null($check_channel)) {
            return $this->sendBadRequestResponse("Notification channel not found");
        }

        if ($check_channel->status != 1) {
            return $this->sendBadRequestResponse("Notification channel not active");
        }

        $update = $this->configurevar->updateItem(['id'=> $id], [
            'channel_id'=> $check_channel->id,
            'key'=> $request->get('key'),
            'required'=>$request->get('required') == "true" ? 1:0
        ]);

        if(!$update){
            return $this->sendBadRequestResponse("Error updating configuration variable");
        }

        return $this->sendSuccessResponse('', "Configuration variable updated successfully");

    }

    public function delete(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'channel_code'=> 'required',
            'field_ids' => 'required|array',
            "field_ids.*" => 'required|integer'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $check_channel = $this->channel->findItem(['code' => $request->get('channel_code')]);

        if (is_null($check_channel)) {
            return $this->sendBadRequestResponse("Notification channel not found");
        }

        foreach ($request->field_ids as $value) {
            $delete = $this->configurevar->deleteItem(['channel_id'=>$check_channel->id,'id' => $value]);
        }
       
        if(!$delete){
            return $this->sendBadRequestResponse("Error deleting configuration variable");
        }

        return $this->sendSuccessResponse("Configuration variable deleted successfully");
    }
}
