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
        $data = [
            'body' => $request->body,
            'acid' => $request->acid,
            'requestId' => (string) mt_rand(),
            'isBodyHtml' => true,
            'title' => $request->title,
            'fromAddress' => env('MAIL_FROM'),
            'sendPdfAttachment' => false,
            'pdfAttachmentBody' => null
        ];

        $sendmail = $this->sendMailGuzzle($data);

        Log::debug("Response from email service for " . $request->acid . " " . json_encode($sendmail));

        print_r($sendmail);
    }
}