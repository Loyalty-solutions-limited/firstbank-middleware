<?php

namespace App\Concretes;

use App\Interfaces\INotifyService;
use App\Interfaces\ITemplate;
use App\Interfaces\IChannelConfig;
use App\Interfaces\INotChannel;
use App\Jobs\SendMultipleNotification;
use App\Models\NotificationLog;
use App\Utils\ChannelMethod;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;

class NotifyService implements INotifyService
{
    /**
     * @var ITemplate
     */
    private $templateRepo;

    /**
     * @var IChannelConfig
     */
    private $channelConfigRepo;

    public function __construct(ITemplate $templateRepo, IChannelConfig $channelConfigRepo,INotChannel $notChannelRepo)
    {
        $this->templateRepo = $templateRepo;
        $this->channelConfigRepo = $channelConfigRepo;
        $this->notChannelRepo = $notChannelRepo;
        $this->channelCode = null;
    }

    public function grabTemplates(Object $notType, Array $variables = [], $program_id)
    {
        $templates = $this->templateRepo->getTemplates($notType, $program_id,request()->channelsConfigured);

        return (count($templates) === 0) ? [] : $this->addVariables($templates, $variables);
    }


    public function setTemplateAndTypes($templates)
    {
        $this->templates = $templates;
        foreach($templates as $template){
            if(!$template) continue;
            $this->channelIds[] = $template->channel_id;
        }
        
    }


    private function addVariables($templates, Array $variables = [])
    {
        foreach($templates as $template){
            if(is_null($template)) continue;
            foreach($variables as $key => $variable)
            {
                $template->content = str_replace('$' . "{$key}", $variable, $template->content);
            }
        }
        
        return $templates;
    }


    public function push($recipient,$program_id)
    {
        // Grab configuration
        $configs = $this->channelConfigRepo->getConfigs($program_id,$this->channelIds);
        if(count($configs) == 0)
        {
            return (object)[
                'status' => false,
                'error' => 'Notification Channel does not exist'
            ];
        }
        foreach($configs as $config){
            if($config->channel_status == 0)
        {
            return (object)[
                'status' => false,
                'error' => "Notification Channel ({$config->channel_name}) has been disabled"
            ];
        }
        $this->recipient = $recipient;
        request()->channelCode = $this->channelCode = $config->channel_code;

        }
        
        

        // Use channel id to pick method of communication
        $responses = $this->setChannelAndPush($configs);

        return $responses;
    }


    private function setChannelAndPush(Array $configs)
    {
        $method = new ChannelMethod($configs, $this->templates);
        return $method->pushTo($this->recipient);
    }


    public function getChannelCode()
    {
        return $this->channelCode;
    }
}
