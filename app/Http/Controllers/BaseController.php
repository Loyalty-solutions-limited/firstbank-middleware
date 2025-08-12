<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

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
        CURLOPT_URL => config("externalservices.BAP_URL") . $url,
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
            'Api-Key: config("externalservices.BAP_API_KEY")',
            'Content-Type: application/xml'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;


    }

    protected function postDataGuzzle($data, $url)
    {
        $endpoint = config("externalservices.BAP_URL") . $url;

        // $res = Http::withOptions(['verify' => false])->post($url);

        $client = new Client(['verify' => false]);
        $headers = [
        'Accept' => 'application/xml',
        'Api-Key' => config("externalservices.BAP_API_KEY"),
        'Content-Type' => 'application/xml; charset=UTF8',
        'Cookie' => 'ARRAffinity=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33; ARRAffinitySameSite=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33'
        ];

        // print_r(json_encode($request->all()));

        $request = new GuzzleRequest('POST', $endpoint, $headers, $data);
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }

    protected function getDataGuzzle($url)
    {
        $endpoint = config("externalservices.BAP_URL") . $url;

        $client = new Client(['verify' => false]);
        $headers = [
        'Accept' => 'application/xml',
        'Content-Type' => 'text/xml; charset=UTF8',
        'Api-Key' => config("externalservices.BAP_API_KEY"),
        'Cookie' => 'ARRAffinity=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33; ARRAffinitySameSite=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33'
        ];
        // $res = $client->request('GET', $url);
        $request = new GuzzleRequest('GET', $endpoint, $headers);
        $res = $client->sendAsync($request)->wait();

        echo $res->getBody();
    }
}