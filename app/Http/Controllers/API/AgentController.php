<?php

namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Agent; 
//use Illuminate\Support\Facades\Auth; 
use Validator;

class AgentController extends Controller
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
         $agent = Agent::where('email',$request->email)->first();
         if($agent) {
           // Verify the password
           if( password_verify($request->password, $agent->password) ) {
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
         'agentName'     => 'required|min:3|regex:/^[a-zA-Z]+$/u',
         'email'    => 'required|unique:agents,email|max:160',
         'password' => 'required|min:8',
         'city' =>'required|max:40|regex:/^[a-zA-Z]+$/u',
         'agentStartDate' =>'required',
         'address1' =>'required|max:100',
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
           'agentName'      => $request->agentName,
           'email'      => $request->email,
           'password'      => bcrypt($request->password),
           'city'      => $request->city,
           'email'     => $request->email,
           'address1'  => $request->address1,
           'address2'  =>$request->address2,
           'country'  =>$request->country,
           'location'  =>$request->location,
           'zip'  =>$request->zip,
           'cellPhone'  =>$request->cellPhone,
           'groupId'  =>$request->groupId,
           'agentStartDate'  =>$request->agentStartDate,
           'userName'  =>$request->userName,
           'isActive'  =>'0',
           'api_token' => $this->apiToken,
            'created_at' =>Carbon::now(),
           'modDate' =>Carbon::now(),
           'modBy' =>'asddaf',
         ];
         // $agent = agent::GetInsertId($postArray);
         $agent = agent::insert($postArray);
     
         if($agent) {
           return response()->json([
             'agentId' =>  $request->agentId,
             'name'         => $request->agentName,
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
