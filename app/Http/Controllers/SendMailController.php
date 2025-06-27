<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Client;


class SendMailController extends Controller
{
    public function sendMailNew(Request $request)
    {
        // return $request->all();
        $url = env('POINTS_TO_CASH_URL') . "email/send-email";


        // $res = Http::withOptions(['verify' => false])->post($url);

        $client = new Client(['verify' => false]);
        $headers = [
        'Accept' => 'application/json',
        'AppId' => config("externalservices.POINTS_TO_CASH_APPID"),
        'AppKey' => config("externalservices.POINTS_TO_CASH_APPKEY"),
        'Content-Type' => 'application/json',
        'Cookie' => 'ARRAffinity=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33; ARRAffinitySameSite=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33'
        ];

        // print_r(json_encode($request->all()));

        $request = new GuzzleRequest('POST', $url, $headers, json_encode($request->all()));
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }
}