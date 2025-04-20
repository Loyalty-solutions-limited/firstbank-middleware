<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface INotifyService
{
    // public function send(Object $notType, Request $request);
    // public function sendMultiple(Object $notType, Request $request);
    public function grabTemplates(Object $notType, Array $variables = [], $program_id);
    public function setTemplateAndTypes($templates);
    public function push($recipient, $program_id);
    public function getChannelCode();
}
