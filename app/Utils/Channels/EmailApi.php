<?php

namespace App\Utils\Channels;

use Illuminate\Support\Facades\Mail;
use App\Mail\SmtpMail;
use App\Interfaces\IMailingGroupEmail;

class Email
{
    private $configInst;
    private $defaultPort;
    private $defaultMailer;
    private $mailGroupRepo;

    public function __construct(Object $config)
    {
        $this->configInst = $config;
        $this->defaultPort = 2525;
        $this->defaultMailer = 'postmark';
        $this->mailGroupRepo = resolve(IMailingGroupEmail::class);
    }

    public function deliver($recipient, Object $template)
    {
        // Set Config
        $this->setConfigs($template->reply_to);

        // Send
        try{
            $mail = Mail::to($recipient);
            if (count($template->bcc_group) > 0)
            {
                $bcc = $this->getCopies($template->bcc_group);
                $mail = $mail->bcc($bcc);
            }

            if (count($template->cc_group) > 0)
            {
                $cc = $this->getCopies($template->cc_group);
                $mail = $mail->cc($cc);
            }
            if($template->immediate === 0){
                //if the email is to be send along request
                $mail->later(now()->addSeconds(10), 
                new SmtpMail($template->content, $template->subject));
            }
            else{
                $mail->send(new SmtpMail($template->content, $template->subject));
            }
            

            return (object)['status' => true, 'message' => 'Success'];
        }
        catch(\Exception $ex)
        {
            return (object)[
                'status' => false,
                'error' => $ex->getMessage()
            ];
        }
    }

    private function setConfigs(String $replyTo = null)
    {
        $variables = json_decode($this->configInst->config);

        config([
            'mail.default' => $this->defaultMailer,
            'server.postmark.token' => $variables->api_key,
            'mail.from.address' => $this->configInst->sender_id,
            'mail.from.name' => $this->configInst->sender_name ?? env('MAIL_FROM_NAME'),
            'mail.reply_to.address' => @$replyTo,
        ]);

        return true;
    }


    private function getCopies(array $groupIds): array
    {
        $emails = [];
        foreach ($groupIds as $groupId) {
            $_emails = $this->mailGroupRepo->getEmails($groupId);
            $emails = array_unique(array_merge($emails, $_emails));
        }
        return $emails;
    }
}
