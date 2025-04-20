<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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

}
