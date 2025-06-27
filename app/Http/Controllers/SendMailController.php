<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Request as GuzzleRequest;


class SendMailController extends Controller
{
    public function sendMailNew(Request $request)
    {
        $req = $request->all();
        return $request->body . " " . $request->acid;
        $data = [
            'body' => "Hello world!",
            'acid' => "MO53791387",
            'requestId' => (string) mt_rand(),
            'isBodyHtml' => true,
            'title' => "enrollment test",
            'fromAddress' => env('MAIL_FROM'),
            'sendPdfAttachment' => false,
            'pdfAttachmentBody' => null,
            'subject' => "Enrollment test from postman"
        ];

        $sendmail = $this->sendMailGuzzle($data);

        Log::debug("Response from email service for " . $request->acid . " " . json_encode($sendmail));

        print_r($sendmail);
    }
}