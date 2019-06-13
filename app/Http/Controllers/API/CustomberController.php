<?php

namespace App\Http\Controllers\API;

use App\customer; 
use App\plan;
use App\group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Carbon\Carbon;
use DB;
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
	public function index()
    {
      $customers = DB::table('customers')->select('customerId','firstName','LastName','DOB',
      'country','created_at')->get();
      return response()->json([
        'customers' =>$customers,
      ]);
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
     public function register_basic1(Request $request)
     {
      //print_r($request->all());exit;
        // $agent->generateToken();
     // print_r($_POST);exit;
        // return response()->json(['data' => $agent->toArray()], 201);
        /*$rules = [
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
		   */
		   $postPlan = [
		     'planName'      => $request->plan,
			  'fee' =>'100',
			  'created_at' =>Carbon::now(),
		   ];
		     $plan = plan::insert($postPlan);
		     $postGroup = [
		     'groupCode'      => $request->planid,
			
			  'created_at' =>Carbon::now(),
		   ];
		    $group = group::insert($postGroup);
         $postCustomber = [
           'firstName'      => $request->firstname,
           'LastName'      => $request->lastname,
           
           'city'      => $request->city,
		   'city1'      => $request->city1,
		   'state1'      => $request->city,
		   'zip1'      => $request->city,
		   'agent_manager'      => $request->city,
		   'spouseFirstName'      => $request->city,
		 
		   'password'      => $request->city,
		   'companyName'      => $request->companyname,
		   
		   'country'      => $request->country,
		   'writing_agent'      => $request->city,
		   'writing_agent'      => $request->city,
		    'country'  =>$request->country,
           'zip'  =>$request->zip,
		   'cellPhone'  =>$request->mobilenumber,
           'address1'  => $request->address1,
		   'address2'  => $request->address1,
		   'mailing_address1'  => $request->address1,
		   'mailing_address2'  => $request->address1,
		   'companyName' => $request->companyName,		  
		   'dependent1FirstName' =>$request->customerRegisterFormDependantFirstName,
		   'dependent2FirstName' =>$request->customerRegisterFormDependantFirstName1,
		   'dependent3FirstName' =>$request->customerRegisterFormDependantFirstName2,
		   'dependent4FirstName' =>$request->customerRegisterFormDependantFirstName3,
		   'dependent1LastName' =>$request->customerRegisterFormDependantLastName,		   
		   'Dependent2LastName' =>$request->customerRegisterFormDependantLastName1,		  
		   'dependent3LastName' =>$request->customerRegisterFormDependantLastName2,		  
		   'dependent4LastName' =>$request->customerRegisterFormDependantLastName3,	    
			'dependent1DOB' =>$request->customerRegisterFormDob,
			'dependent2DOB' =>$request->customerRegisterFormDob1,
			'dependent3DOB' =>$request->customerRegisterFormDob2,
			'dependent4DOB' =>$request->customerRegisterFormDob3,          
		    'spouseFirstName'  =>$request->spousename,
            'created_at' =>Carbon::now(),
           'modDate' =>"2019-06-14",
           'modBy' =>'asddaf',
         ];
         // $agent = agent::GetInsertId($postArray);
         $customer = customer::insert($postCustomber);
     
         if($customer) {
           return response()->json([
              'status'=>'200',
			  'message'=>'sucess'
			  
           ]);
         } else {
           return response()->json([
		     'status'=>'201',
             'message' => 'Registration failed, please try again.',
           ]);
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
