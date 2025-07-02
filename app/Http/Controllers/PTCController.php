<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PTCController extends Controller
{
    public function __construct()
    {
        $this->url = env('POINTS_TO_CASH_URL');

    }

    public function acquisition(Request $request)
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
}