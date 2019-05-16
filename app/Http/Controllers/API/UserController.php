<?php

namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User; 
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
    
	
	 public function login(Request $request){ 
       /* if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->generateToken();; 
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
        // Fetch User
        $user = User::where('email',$request->email)->first();
        if($user) {
          // Verify the password
          if( password_verify($request->password, $user->password) ) {
            // Update Token
            $postArray = ['api_token' => $this->apiToken];
            $login = User::where('email',$request->email)->update($postArray);
            
            if($login) {
              return response()->json([
                'name'         => $user->name,
                'email'        => $user->email,
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
            'message' => 'User not found',
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
