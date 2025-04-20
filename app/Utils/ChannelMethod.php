<?php

namespace App\Utils;

use App\Interfaces\INotType;

class ChannelMethod
{
    private $config;

    public function __construct(Array $configs, Array $templates)
    {
        $this->configs = $configs;
        $this->templates = $templates;
        $this->notTypeRepo = resolve(INotType::class);
    }
    

    public function pushTo($recipient)
    {
        $channelResults =[];
        foreach($this->configs as $config){
            $className = $config->channel_class;
            $channel = new $className($config);

            $channelResults[] = $channel->deliver($recipient, $this->getTemplateForConfig($config));
        }
        
        

        return  $channelResults;
    }
    private function getTemplateForConfig($config)
        {
            foreach($this->templates as $template){
                if($template->channel_id == $config->channel_id && 
                    $template->program_id == $config->program_id &&
                    $template->not_type_id == $this->notTypeRepo
                    ->findItem(["slug" => request()->not_type])->id){
                        return $template;
                    }
            }
        }    
}
