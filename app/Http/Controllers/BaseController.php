<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class BaseController extends Controller
{
    protected function fetchData($url)
    {

    }

    protected function postData($data, $url)
    {
        // $client = new Client();
        // $headers = [
        // 'Content-Type' => 'application/xml',
        // 'Api-Key' => config("BAP_API_KEY"),
        // ];
        // $body = $data;
        // $request = new Request('POST', config("BAP_URL") . $url, $headers, $body);
        // $res = $client->sendAsync($request)->wait();
        // echo $res->getBody();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => config("BAP_URL") . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/xml',
            'Api-Key: config("BAP_API_KEY")',
            'Content-Type: application/xml'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;


    }
}
