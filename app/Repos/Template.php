<?php

namespace App\Repos;

use App\Interfaces\INotTypeMailGroup;
use App\Interfaces\ITemplate;
use App\Interfaces\INotChannel;
use Illuminate\Support\Facades\DB;

class Template extends Base implements ITemplate
{
    const LETTERS_PER_PAGE = 160;
    //public $table_name;

    /**
     * @var INotTypeMailGroup
     */
    private $progNotTypeMailGroupRepo;

    public function __construct(INotTypeMailGroup $progNotTypeMailGroupRepo,
            INotChannel $notChannelRepo)
    {
        //parent::__construct($table_name);
        $this->progNotTypeMailGroupRepo = $progNotTypeMailGroupRepo;
        $this->notChannelRepo = $notChannelRepo;
    }


    public function getTemplates(Object $notType, $program_id,$channelsConfigured)
    {
        $templates = [];
        foreach($channelsConfigured as $channel){
            $templates[] = DB::table('templates')
            //$templates[] = DB::table($this->table_name)
            // ->where([
            //     ['not_type_id', $notType->id],
            //     ['program_id', $program_id],
            //     ['channel_id',$channel->channel_id]
            // ])->first();

            ->where([
                ['not_type_id', 1],
                ['program_id', 1],
                ['channel_id',1]
            ])->first();
        }
        

        //throw new \Exception(json_encode($template));
        
        
        foreach($templates as $template)
        {
           if(is_null($template)){
               continue; 
               // This means that for this particular notification a template has not been 
               //configured.
               // Ask whether we should cancel or not.
           }
           //for email type, do these
           if($this->notChannelRepo->findItem(["id" => $template->channel_id])->channel_type_id == 1)
            {
                $groups = $this->progNotTypeMailGroupRepo->getGroups($notType);
                
                if (count($groups) == 0) {
                    $template->bcc_group = [];
                    $template->cc_group = [];
                }else{
                    $template->bcc_group = $groups['bcc'];
                $template->cc_group = $groups['cc'];
                }
    
                
                $template->email = true;
               
            }
            else
            {
                //for sms do these
            //To do letter

                $template->email = false;
            }
            
           
        }
        
        
        return $templates;
    }
}
