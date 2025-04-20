<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
 
class HSTS
{
    public function handle(Request $request, Closure $next)
    {
        //echo " hi ";
        return $next($request);
 
       
    }
}