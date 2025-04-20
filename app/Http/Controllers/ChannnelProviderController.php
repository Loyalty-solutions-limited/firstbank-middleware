<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IChannel;
use App\Interfaces\IConfigureVar;
use App\Interfaces\INotChannel;
use App\Interfaces\INotType;
use App\Interfaces\IChannelConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChannnelProviderController extends Controller
{
    public $notchannel, $channel, $type, $variable;

    public $limit = 15;


    public function __construct(INotChannel $notchannel, IChannel $channel, INotType $type, 
    IConfigureVar $variable, IChannelConfig $channelConfig)
    {
        $this->notchannel = $notchannel;
        $this->channel = $channel;
        $this->type = $type;
        $this->variable = $variable;
        $this->channelConfig = $channelConfig;
    }

    public function all()
    {
        $for_dropdown = request()->query('dropdown');

        if(!is_null($for_dropdown) && ($for_dropdown == true)){
            $not_channels = $this->notchannel->getItem(['status'=> 1], ['id', 'name', 'code']);
        }else{
            if (request()->query('limit')) {
                $this->limit = request()->query('limit');
            }
            
            $not_channels = $this->notchannel->notification_channels($this->limit);
        }

        return $this->sendSuccessResponse( '',$not_channels);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string',
            'description'=> 'required|string',
            'channel_type_slug'=> 'required',
            'variables'=> 'required|array',
            'variables.*.key'=> 'required',
            'variables.*.required'=> 'required'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $channel_type = $this->channel->findItem(['slug'=> $request->get('channel_type_slug')]);

        if(is_null($channel_type)){
            return $this->sendBadRequestResponse("Channel type not found");
        }

        if($channel_type->status == 0){
            return $this->sendBadRequestResponse("Channel type selected is currently disabled");
        }

        $slug = Str::slug($request->get('name').' '.'notify channel', '-');

        $error_insert = [];
        $success_insert = [];
        DB::beginTransaction();

        try {
            $className = '';
            if(stristr($slug,"email")){
                $className = 'App\Utils\Channels\Smtp';
            }
            elseif(stristr($slug,"sms")){
                $className = 'App\Utils\Channels\SMSApi';
            }
            
            $store = $this->notchannel->insertGetId([
                'name'=> $request->get('name'),
                'description'=> $request->get('escription'),
                'channel_type_id'=> $channel_type->id,
                'code'=> $slug,
                'class' => $className
            ]);

            foreach ($request->variables as $variable) {
                
                $check_var = $this->variable->var_check($variable['key'], $store);

                if($check_var){
                    array_push($error_insert, $variable['key']);
                }else{
                    $this->variable->create_var($variable['key'], $variable['required'] == "true" ? 1: 0, $store);
                    array_push($success_insert, $variable['key']);
                }

            }
            DB::commit();
        } catch (\Exception $ex) {
           DB::rollBack();
           return $this->sendBadRequestResponse($ex->getMessage());
        }

        $results['variable_not_stored'] = $error_insert;
        
        if(count($error_insert) > 0){
            $results['reason'] = 'Variable already exist for notification channel';
        }
        
        return $this->sendSuccessResponse( 'Notification channel and variables created successfully',$results);
    }
    public function store(Request $request)
    {
        $rules = [
            "not_channel_slug" =>'required|string',
            "variables" =>'required|array',
            "variables.sender_id" =>'nullable|string',
            "variables.sender_name" =>'nullable|string',
            "variables.config" =>'required|array',
            "variables.config.*" => "required|string"
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }
        
        $notChannelInst = $this->notchannel->findItem(['code' => $request->not_channel_slug]);
        
        
        $chanCon = $this->channelConfig->create([
            "target" => @$request->variables["target"],
            "sender_id" => @$request->variables["sender_id"],
            "sender_name"=> @$request->variables["sender_name"],
            "header" => @$request->variables["header"],
            "program_id" => $request->input("program_id") ,
            "channel_id" => $notChannelInst->id,
            "config" => @json_encode($request->variables["config"])

        ]);
        return $this->sendSuccessResponse( '',$chanCon);

    }
    public function update(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string',
            'description'=> 'required|string',
            'channel_type_slug'=> 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $not_channel = $this->notchannel->findItem(['code' => $slug]);

        if (is_null($not_channel)) {
            return $this->sendBadRequestResponse('Notification channel not found');
        }

        if($not_channel->status == 0){
            return $this->sendBadRequestResponse("Notification channel is currently disabled");
        }

        $channel_type = $this->channel->findItem(['slug' => $request->get('channel_type_slug')]);

        if (is_null($channel_type)) {
            return $this->sendBadRequestResponse("Channel type not found");
        }

        if ($channel_type->status == 0) {
            return $this->sendBadRequestResponse("Channel type selected is currently disabled");
        }

        $update = $this->notchannel->updateItem(['id'=> $not_channel->id], [
            'name'=> $request->get('name'),
            'description'=> $request->get('description'),
            'channel_type_id'=> $channel_type->id
        ]);

        if(!$update){
            return $this->sendBadRequestResponse("Error updating notification channel");
        }

        return $this->sendSuccessResponse('', 'Notification channel updated successfully');

    }

    public function disable($slug)
    {
        $not_channel = $this->notchannel->findItem(['code'=> $slug]);

        if(is_null($not_channel)){
            return $this->sendBadRequestResponse("Notification not found");
        }

        $disable = $this->notchannel->updateItem(['id'=> $not_channel->id], [
            'status'=> 0
        ]);

        if(!$disable){
            return $this->sendBadRequestResponse("Error disabling {$not_channel->name}");
        }

        return $this->sendSuccessResponse("", "{$not_channel->name} disabled successfully");
    }

    public function enable($slug)
    {
        $not_channel = $this->notchannel->findItem(['code' => $slug]);

        if (is_null($not_channel)) {
            return $this->sendBadRequestResponse("Notification not found");
        }

        $enable = $this->notchannel->updateItem(['id' => $not_channel->id], [
            'status' => 1
        ]);

        if (!$enable) {
            return $this->sendBadRequestResponse("Error enabling {$not_channel->name}");
        }

        return $this->sendSuccessResponse("", "{$not_channel->name} enabled successfully");
    }
    

    public function notificationchannels($not_type_slug)
    {
        $type = $this->type->findItem(['slug'=> $not_type_slug]);

        if(is_null($type)){
            return $this->sendBadRequestResponse("Notification Type not found");
        }
    }
}
