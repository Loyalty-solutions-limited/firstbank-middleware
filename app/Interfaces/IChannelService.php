<?php

namespace App\Interfaces;

use App\Utils\RecipientRule;

interface IChannelService
{
    public function channel(Int $channelTypeId) : RecipientRule;
}
