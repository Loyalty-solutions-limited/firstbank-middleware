<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\EmailDispatcher;

class NotificationController extends Controller
{
    public function staged_dumped(Request $request)
    {
        if($request->query('last_row'))
        {
            $total_dumped = Transaction::where('id', '>', $request->query('last_row'))
                                ->count();

            return $total_dumped;

        }else{
            return response()->json([
                "message" => "Please, specify the last row id",
                "status"  => false
            ], 417);
        }
    }

    public function blank_membership(Request $request)
    {
        if(!($request->query('last_row')))
        {
            return response()->json([
                "message" => "Please, specify the last row id",
                "status" => false
            ]);
        }else{
            $blank_membership = Transaction::where('id', '>', $request->query('last_row'))->where('member_reference', '=', '');

            return $blank_membership->count();
        }
    }

    public function staging_done(Request $request)
    {
        if(!($request->query('last_row')))
        {
            return response()->json([
                "message" => "Please, specify the last row id",
                "status" => false
            ]);
        }else{
            $staging_status = Transaction::where('status', '=', 4);

            if($staging_status > 0) return $staging_status->count();

            return response()->json([
                "message" => "Transaction staging is done",
                "status" => true
            ]);

        }
    }
}
