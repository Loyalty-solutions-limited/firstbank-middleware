<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PointToCashController extends Controller
{
    public function __construct()
{
    $this->url = env('POINTS_TO_CASH_URL');
}

public function aquisition(Request $request)
{
  $url = $this->url . "aquisition";
  $validInputs = $request->validate([
    'pointValue' => ['required'],
    'body' => ['required'],
    'amount' => ['required'],
    'transactionDate' => ['required'],
    'transactionId' => ['required'],
    'transactionChannel' => ['required'],
    'membershipId' => ['required']
  ]);

  return $this->makeCurl($url, 'POST', $validInputs);
}

public function redeem(Request $request)
{
  $url = $this->url . "redeem";
  $validInputs = $request->validate([
    'transactionId' => ['required'],
    'acid' => ['required'],
    'amount' => ['required']
  ]);
  $data = array(
    "transactionId: ".$validInputs['transactionId'],
    "acid: ".$validInputs['acid'],
    "amount: ".$validInputs['amount']
  );
  
  return json_decode($this->makeCurl($url, 'POST', $data));
} 

public function makeCurl($url, $action, $data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => $action,
    CURLOPT_POSTFIELDS =>$data,
    CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        "AppId: ".config("externalservices.POINTS_TO_CASH_APPID"),
        "AppKey: ".config("externalservices.POINTS_TO_CASH_APPKEY"),
        'Content-Type: application/json',
        'Cookie: ARRAffinity=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33; ARRAffinitySameSite=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33'
    ),
    ));

    $response = curl_exec($curl);

    
    curl_close($curl);
    // return $response;
}
}
