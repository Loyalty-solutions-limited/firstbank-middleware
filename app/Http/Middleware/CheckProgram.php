<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Interfaces\IProgram;

class CheckProgram
{
    public function __construct(IProgram $program){
        $this->program = $program;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $programSlug = $request->pro_slug;
        
        $program = $this->program->findItem(["slug"=> $programSlug],["id","name"]);

        if(!$program){
            return response()->json([
                "message" => "Invalid program",
                "status" => 0,
                'success' => false,
                "status_code" => 401,
            ], 401);
           
        }
        $request->programId = $program->id;
        return $next($request);

    
    }
}
