<?php

namespace App\Utils\Channels;

use App\Concretes\MakeRequest;
use Carbon\Carbon;

class SmsApi
{
    private $configInst;

    public function __construct(Object $config)
    {
        $this->configInst = $config;
    }

    public function deliver($recipient, Object $template)
    {
        // Set Config
        $this->setConfigs();

        // Send - at least try to
        try {
            $content = ['to' => $recipient, 'body' => $template->content];
            $makeRequest = new MakeRequest($this->configInst->target);

            $payload = [
                'from' => $this->configInst->sender_id,
                'send_flag' => 1, //Always send immediately
                'send_date' => Carbon::now()->addMinute(1),
                'message_details' => [$content]
            ];
            $payload = array_merge($this->variables, $payload);

            $response = $makeRequest->postAsJson("", $payload);
            if(!$response->status)
            {
                // throw new \Exception('', 400);
                // return $this->formulateErrorResponse($ex);
                return (object)['status' => false, 'error' => "System error - " . $response->body->message];
            }

            return (object)['status' => true, 'message' => 'Success'];
        }
        catch (\Exception $ex) {
            //dd($ex);
            return (object)[
                'status' => false,
                'error' => $ex->getMessage()
            ];
        }
    }

    private function setConfigs()
    {
        $this->variables = (array)json_decode($this->configInst->config);

        return true;
    }
}
