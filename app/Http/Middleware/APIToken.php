<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use App\User;
class APIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
    
     $user = User::where('api_token', $request->header('Authorization'))->first();
     if($user->email != '' )

      {
        /*commented section use letter for checking API KEY
    
           $apikey = env('API_KEY');
              //echo  $apikey;exit;
		 if($request->header('Authorization') == $apikey){
       
        }

        */
        return $next($request);
      }
         
           return response()->json([
       'message' => 'Not a valid USER request.',
         ]);exit;
       // return $next($request);
    }
}
