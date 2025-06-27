<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendBadRequestResponse($errors, $message = 'Invalid user request')
    {
        return response()->json([
            "message"=>$message,
            "errors"=>$errors,
            "status"=>0,
            "status_code"=>400,
        ],400);
    }

    public function sendUnauthoriseRequest($errors, $message = 'Unauthorise Request')
    {
        return response()->json([
            "message"=>$message,
            "errors"=>$errors,
            "status"=>0,
            "status_code"=>401,
        ],401);
    }

    protected function sendSuccessResponse($message,$data=[])
    {
        $response = [
            "message"=>$message,
            "status"=>1,
            "status_code"=>200,
        ];
        if($data)
            $response["data"] = $data;

        return response()->json($response,200);
    }

    protected function sendNotFoundResponse($message,$data=[])
    {
        $response = [
            "message"=>$message,
            "status"=>0,
            "status_code"=>404,
        ];
        if(count($data))
            $response["data"] = $data;

        return response()->json($response,404);
    }

    public static function sendMailGuzzle($request)
{
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

        $request = new GuzzleRequest('POST', $url, $headers, json_encode($request));
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();


}

}