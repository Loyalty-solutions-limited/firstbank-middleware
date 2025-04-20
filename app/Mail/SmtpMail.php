<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SmtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The Content of email.
     *
     * @var String
     */
    public $content;

    /**
     * The subject of email.
     *
     * @var String
     */
    public $subject;

    /**
     * Create a new message instance.
     *
     * @param  $content
     * @return void
     */
    public function __construct(String $content, String $subject)
    {
        $this->content = $content;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('templates.smtp')->with(['content' => $this->content]);
    }
}
