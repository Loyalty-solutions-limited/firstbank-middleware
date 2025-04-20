<?php

namespace App\Interfaces;

interface ITemplate extends IBase
{
    public function getTemplates(Object $notType, $program_id,$channelsConfigured);
}
