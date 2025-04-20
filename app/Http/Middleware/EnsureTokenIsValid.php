<?php

namespace App\Http\Middleware;

use App\Models\VendorAccessKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //return $request->header('token');
        if(self::isValid($request->header('token', password_hash('1234', PASSWORD_BCRYPT)), VendorAccessKey::latest()->first()->value)){
           
            return $next($request);
        }else{
           
            abort(403, json_encode(array("message"=>"access denied")));
             
        }
            
    }

    private static function isValid($passed, $stored) :bool{
        if (Hash::check($stored, $passed)){
           
            return true;
        }else{
           
            return false;
        }
    }
}
