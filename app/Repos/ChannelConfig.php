<?php

namespace App\Repos;

use App\Interfaces\IChannelConfig;
use Illuminate\Support\Facades\DB;

class ChannelConfig extends Base implements IChannelConfig
{
    // private $table_name;

    public function __construct($table_name = "channel_configs")
    {
        parent::__construct($table_name);
        $this->channelsTable = 'notification_channels';
    }

    public function getConfigs($programId,$channelIds)
    {
        $configs = [];
        foreach($channelIds as $channelId){
            $configs[] = DB::table("{$this->table_name} as cc")
            ->leftJoin($this->channelsTable, "cc.channel_id", '=', "{$this->channelsTable}.id")
            ->where('cc.channel_id', $channelId)
            ->where('program_id', $programId)
            ->select('cc.*', "{$this->channelsTable}.name as channel_name", "{$this->channelsTable}.class as channel_class", "{$this->channelsTable}.code as channel_code", "{$this->channelsTable}.status as channel_status")
            ->first();
        }
        return $configs;         
    }
}
