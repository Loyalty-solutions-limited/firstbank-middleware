<?php

namespace App\Utils;

use Illuminate\Support\Facades\Validator;

class RecipientRule
{
    private $channelTypeRule;

    public function __construct(String $channelTypeRule = null)
    {
        $this->channelTypeRule = $channelTypeRule;
    }

    public function validateRecipient($recipient)
    {
        switch ($this->channelTypeRule)
        {
            //Email Validation
            case 'email':
                $rule = 'email';
            break;

            //Numeric Validation
            case 'numeric':
                $rule = 'numeric';
            break;

            // String
            default:
                $rule = 'string';
        }

        $validator = Validator::make(['recipient' => $recipient], [
            'recipient'=> "required|$rule"
        ]);

        if($validator->fails()){
            return (object)[
                'status' => false,
                'message' => $validator->errors()->first()
            ];
        }

        return (object)['status' => true];
    }
}
