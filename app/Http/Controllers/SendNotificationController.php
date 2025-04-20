<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Services\SendNotificationService;

use Closure;


class SendNotificationController extends Controller
{
    private $sendNotService;
    public function __construct(SendNotificationService $send)
    {
        resolve(SendNotificationService::class);
        $this->sendNotService = $send ;
        // $this->middleware(function(Request $request, Closure $next){
        //     $loyalty = LoyaltyProgram::find($request->program_slug);
        //     if(!$loyalty){
        //        return $this->sendBadRequestResponse("No program was sent");
        //     }
        //     $request->programId = $loyalty->id;
        //     return $next($request);
        // });

    }
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $request->programId = 1;
      return $this->sendNotService->sendNotification($request);
    }

}
