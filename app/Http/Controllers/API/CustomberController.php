<?php

namespace App\Http\Controllers\API;

use App\customer; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Carbon\Carbon;
class CustomberController extends Controller
{
    public $successStatus = 200;

    private $apiToken;

    public function __construct()
    {
        // Unique Token
        //$this->middleware('auth:agent');
        $this->apiToken = str_random(60);
    }
    public function login(Request $request){ 
        /* if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
             $agent = Auth::agent(); 
             $success['token'] =  $agent->generateToken();; 
             return response()->json(['success' => $success], $this-> successStatus); 
         } 
         else{ 
             return response()->json(['error'=>'Unauthorised'], 401); 
         } 
 
 
         */
          // Validations
     $rules = [
         'email'=>'required|email',
         'password'=>'required|min:8'
       ];
       $validator = Validator::make($request->all(), $rules);
       if ($validator->fails()) {
         // Validation failed
         return response()->json([
           'message' => $validator->messages(),
         ]);
       } else {
         // Fetch agent
         $customer = customer::where('email',$request->email)->first();
         if($customer) {
           // Verify the password
           if( password_verify($request->password, $customer->password) ) {
             // Update Token
             $postArray = ['api_token' => $this->apiToken];
             $login = Agent::where('email',$request->email)->update($postArray);
             
             if($login) {
               return response()->json([
                 'agentName'         => $agent->agentName,
                 'email'        => $agent->email,
                 'access_token' =>  $this->apiToken,
               ]);
             }
           } else {
             return response()->json([
               'message' => 'Invalid Password',
             ]);
           }
         } else {
           return response()->json([
             'message' => 'agent not found',
           ]);
         }
       }
     
     /**
      * Register
      */
     }
     
     /**
      * Show the form for creating a new resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function register(Request $request)
     {
      //print_r($request->all());exit;
        // $agent->generateToken();
     // print_r($_POST);exit;
        // return response()->json(['data' => $agent->toArray()], 201);
        $rules = [
         'firstName'     => 'required|min:3|regex:/^[a-zA-Z]+$/u',
         'LastName'     => 'required|min:3|regex:/^[a-zA-Z]+$/u',
         'email'    => 'required|unique:agents,email|max:160',
         'DOB' => 'required|date',
         'city' =>'required|max:50|regex:/^[a-zA-Z]+$/u',
         'address1' =>'required|max:40',
         'country' =>'required|max:50|regex:/^[a-zA-Z]+$/u',
        
         'location' =>'required|max:150|regex:/^[a-zA-Z]+$/u',
         'cellPhone' =>'required|min:10|numeric',
         'zip' =>'required|numeric',
       ];
       $validator = Validator::make($request->all(), $rules);
       if ($validator->fails()) {
         // Validation failed
         return response()->json([
           'message' => $validator->messages(),
         ]);
       } else {
         $postArray = [
           'firstName'      => $request->agentName,
           'LastName'      => $request->agentName,
           'email'      => $request->email,
           'DOB'      => bcrypt($request->password),
           'city'      => $request->city,
           'address1'  => $request->address1,
           'country'  =>$request->country,
           'location'  =>$request->location,
           'zip'  =>$request->zip,
           'cellPhone'  =>$request->cellPhone,
           'api_token' => $this->apiToken,
            'created_at' =>Carbon::now(),
           'modDate' =>Carbon::now(),
           'modBy' =>'asddaf',
         ];
         // $agent = agent::GetInsertId($postArray);
         $customer = customer::insert($postArray);
     
         if($customer) {
           return response()->json([
             'firstName' =>  $request->agentId,
             'LastName'         => $request->agentName,
             'email'        => $request->email,
             'access_token' => $this->apiToken,
           ]);
         } else {
           return response()->json([
             'message' => 'Registration failed, please try again.',
           ]);
         }
       }
     }
 
     /**
      * Store a newly created resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response
      */
     public function store(Request $request)
     {
         //
     }
 
     /**
      * Display the specified resource.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function show($id)
     {
         //
     }
 
     /**
      * Show the form for editing the specified resource.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function edit($id)
     {
         //
     }
 
     /**
      * Update the specified resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function update(Request $request, $id)
     {
         //
     }
 
     /**
      * Remove the specified resource from storage.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function destroy($id)
     {
         //
     }
}
