<?php

namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User; 
//use App\Customer;
use DB;
use Illuminate\Support\Facades\Auth; 
use Validator;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $successStatus = 200;
    public $failureStatus = 401;
	//203 Non-Authoritative Information
	public $nonAuthoritative = 203;
    private $apiToken;

    public function __construct()
    {
        // Unique Token
        $this->apiToken = str_random(60);
    }

    public function index()
    {
        //
    }
    
	/*for login start*/
	 public function login(Request $request){ 
         //print_r( $request->all());exit;
		// echo $request->email;
		 //echo "hellow";exit;
		//echo json_encode(array('message'=>'hi'));exit;
        //echo json_encode(array($request->all));exit;
    $rules = [
        'email'=>'required|email',
        'password'=>'required|min:8'
      ];
      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        // Validation failed
        return response()->json([
          'message' => $validator->messages(),
		  'status' =>$this->nonAuthoritative,
        ]);
      } else {
        // Fetch User
        $user = User::where('email',$request->email)->first();
		//echo $user->id;exit;
		//echo $user->password ;exit;
        if($user) {
          // Verify the password
		 /* if( password_verify($request->password, $user->password) ) { */
		 //$2y$10$HXiuNpDqBcQIsuDlSrRXcOGoXGxN7UO5teYVPpjxuan2Z07cur0wW
          if( $request->password == $user->password ) {
            // Update Token
            $postArray = ['api_token' => $this->apiToken];
            $login = User::where('email',$request->email)->update($postArray);
			$permission_data = DB::table('user_permission')->where('user_id',$user->id)->first();
			//print_r($permission_data);exit;
            if($login){
              return response()->json([
                'name'         => $user->name,
                'email'        => $user->email,
				'permission'   => $user->userName,
                'access_token' =>  $this->apiToken,
				'permission_data' => $permission_data,
				'status' => $this->successStatus,
              ]);
            }
          } else {
            return response()->json([
              'message' => 'Invalid Password',
			  'status' => $this->failureStatus,
            ]);
          }
        } else {
          return response()->json([
            'message' => 'User not found',
			'status' => $this->failureStatus,
			
          ]);
        }
      }
    
    /**
     * Register
     */
    }
	/*login end*/
	/*member login start */
	public function memberlogin(Request $request){
		//print_r( $request->all());exit;
		$rules = [
        //'email'=>'required|email',
		'customerId'=>'required',
        'password'=>'required|min:8'
      ];
	  $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        // Validation failed
        return response()->json([
          'message' => $validator->messages(),
		  'status' =>$this->nonAuthoritative,
        ]);
      } else {
        // Fetch User
       // $user = Customer::where('customerId', $request->customerId )->where('password',$request->password)->first();
		$user = DB::table('customers')->where('password',$request->password)->first();
		//print_r( $user);exit;
        if($user) {
           
          
            // Update Token
            $postArray = ['api_token' => $this->apiToken];
			
            $memberlogin =DB::table('customers')->where('customerId',$request->customerId)->update($postArray);
			/* print_r($memberlogin);
			exit; */
             if($memberlogin){
              return response()->json([
				'firstName' => $user->firstName,
                'customerId' => $user->customerId,
				'permission'   => $user->userName,
                'access_token' =>  $this->apiToken,
				'status' => $this->successStatus,
              ]);
            }
          
        }  else {
          return response()->json([
            'message' => 'User not found',
			'status' => $this->failureStatus,
			
          ]);
        } 
      }
	}
	/*member login end*/
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	 /*for admin register start*/
    public function register(Request $request)
    {
        
       // $user->generateToken();
    // print_r($_POST);exit;
       // return response()->json(['data' => $user->toArray()], 201);
       $rules = [
        'userName'     => 'required|min:3',
        'email'    => 'required|unique:users,email',
        'password' => 'required|min:8',
        'phone' =>'required|min:10|numeric'
      ];
      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        // Validation failed
        return response()->json([
          'message' => $validator->messages(),
        ]);
      } else {
        $postArray = [
          'userName'      => $request->userName,
          'email'     => $request->email,
          'password'  => bcrypt($request->password),
          'phone'  =>$request->phone,
          'api_token' => $this->apiToken,
         
          'modDate' =>Carbon::now()
        ];
        // $user = User::GetInsertId($postArray);
        $user = User::insert($postArray);
    
        if($user) {
          return response()->json([
            'name'         => $request->name,
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
	/*for admin member register end*/
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
