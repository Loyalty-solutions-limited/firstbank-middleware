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
        echo gettype($req);
        // echo "If object " . $req->acid;
        // echo "If array " . $req->acid;
        print_r($req);
        // return $request->body . " " . $request->acid . $request['body'] . $request['acid'] . $request['requestId'] . $request->requestId;
        $data = [
            'body' => $req['body'],
            'acid' => $req['acid'],
            'requestId' => (string) mt_rand(),
            'isBodyHtml' => true,
            'title' => $req['title'],
            'fromAddress' => env('MAIL_FROM'),
            'sendPdfAttachment' => false,
            'pdfAttachmentBody' => null,
            'subject' => $req['subject']
        ];

        print_r($data);

        $sendmail = $this->sendMailGuzzle($data);

        Log::debug("Response from email service for " . $request->acid . " " . json_encode($sendmail));

        print_r($sendmail);
    }
}