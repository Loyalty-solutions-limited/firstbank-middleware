<?php

namespace App\Interfaces;

interface IChannelConfig extends IBase
{
    public function getConfigs(Int $channelTypeId, $programId);
}
