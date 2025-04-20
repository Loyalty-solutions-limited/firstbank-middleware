<?php

namespace App\Concretes;

use App\Interfaces\IChannel;
use App\Interfaces\IChannelService;
use App\Utils\RecipientRule;
use Illuminate\Http\Request;


class ChannelService implements IChannelService
{
    /**
     * @var IChannel
     */
    private $channelRepo;

    public function __construct(IChannel $channelRepo)
    {
        $this->channelRepo = $channelRepo;
    }

    public function channel(Int $channelTypeId) : RecipientRule
    {
        try{
            $channelTypeInst = $this->channelRepo->findItem(['id' => $channelTypeId]);
            if(!$channelTypeInst)
            {
                return (object)[
                    'status' => false,
                    'error' => 'Channel type does not exist'
                ];
            }

            if($channelTypeInst->status == 0)
            {
                return (object)[
                    'status' => false,
                    'error' => "Channel type ({$channelTypeInst->name}) has been disabled"
                ];
            }

            return new RecipientRule($channelTypeInst->validate);
        }
        catch(\Exception $ex)
        {
            throw new \Exception("Error getting channel type - {$ex->getMessage()}", 400);
        }
    }
}
