<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route_name = $request->route()->getName();
        $user_group = Auth::user()->user_group_id;
        $check_route = $this->check_route($route_name);
        if(is_null($check_route)){
            return response()->json([
                'message'=> 'No route found',
                'status'=> 0,
                'status_code' => 404
            ], 404);
        }

        $check_access = $this->check_user_access($user_group, $check_route->priviledge_id, $check_route->activity);
        if(is_null($check_access)){
            return response()->json([
                'message'=> 'Access Denied',
                'status'=> 0,
                'status_code' => 401

            ], 401);

        }

        return $next($request);
    }


    //check route
    private function check_route($route_name){

        return DB::table('routes')->where('route_name', $route_name)->first();
    }

    //check user priviledge
    private function check_user_access($user_group, $priviledge_id, $activity){
// Added priviledge_id column 
        return DB::table('user_group_privileges')->where(['usergroup_id'=> $user_group, 'priviledge_id'=> $priviledge_id, $activity=> 1])->first();
    
    }
}
