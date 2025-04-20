<?php

namespace App\Concretes;

use App\Interfaces\INotTypeService;
use App\Interfaces\INotType;
use App\Interfaces\IChannelService;
use App\Interfaces\IProgNotType;
use App\Interfaces\INotChannel;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotTypeService implements INotTypeService
{

    /**
     * @var INotType
     */
    private $notTypeRepo;

    /**
     * @var IProgNotType
     */
    private $progNotTypeRepo;

    /**
     * @var IChannelService
     */
    private $channelService;

    private $notChannelRepo;

    public function __construct(INotType $notTypeRepo, IProgNotType $progNotTypeRepo,
                                INotChannel $notChannelRepo, IChannelService $channelService)
    {
        $this->notTypeRepo = $notTypeRepo;
        $this->notChannelRepo = $notChannelRepo;
        $this->progNotTypeRepo = $progNotTypeRepo;
        $this->channelService = $channelService;
        
    }


    public function checkType(Request $request, $bulk = 0)
    {
        $type = $request->not_type;
        $notTypeInst = $this->notTypeRepo->findItem(['slug' => $type]);

        if(!$notTypeInst)
        {
            return (object)[
                'status' => false,
                'error' => 'Notification Type does not exist'
            ];
        }

        if($notTypeInst->status == 0)
        {
            return (object)[
                'status' => false,
                'error' => "Notification Type ({$notTypeInst->name}) has been disabled"
            ];
        }

        // Check if program is linked to notification type
        // $progNotTypeInst = $this->progNotTypeRepo->findItem([
        //     ['not_type_id',  $notTypeInst->id],
        //     ['program_id', request()->programId]
        // ]);

        // if(!$progNotTypeInst)
        // {
        //     return (object)[
        //         'status' => false,
        //         'error' => 'Notification Type is not assigned to the program'
        //     ];
        // }
        
        $this->channelTypeId = $this->notChannelRepo
                                ->findItem(["id" =>request()->channelsConfigured[0]->channel_id])
                                ->channel_type_id;
        // $notTypeInst->progNotTypeId = $progNotTypeInst->id;

        if($bulk == 0){
            NotificationLog::injectLog([
                'not_type' => $notTypeInst->id,
                'channel_type_id' => $this->channelTypeId,
                'recipient' => $request->recipient,
                'variables' => $request->variables,
                'bulk_flag' => 0
            ]);
        }

        return (object)[
            'status' => true,
            'data' => $notTypeInst
        ];
    }

    public function validateRecipient(String $recipient)
    {
        return $this->channelService->channel($this->channelTypeId)
            ->validateRecipient($recipient);
    }

}
