<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Service;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     return  Service::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $rules = [
            'serviceName'     => 'required|min:3',
            'serviceDesc'    => 'required|max:255',                       
          ];
          $validator = Validator::make($request->all(), $rules);
          if ($validator->fails()) {
            // Validation failed
            return response()->json([
              'message' => $validator->messages(),
            ]);
          }
          else{
              
            $postArray = [
                'serviceName' => $request->serviceName,
                'serviceDesc' => $request->serviceDesc,
                'modDate' =>Carbon::now(),
                'modUser' => 'Abscd',            
                'created_at'=>Carbon::now(),
              ];
              // $user = User::GetInsertId($postArray);
              $service = Service::insert($postArray);
             // print_r( $plan);exit;
               //dd($plan->planId);
               
              if($service) {
                $user = Auth::user();
               // print_r($user);
                return response()->json([
                    'message' => 'Service added sucessfully.',
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
        $rules = [
            'serviceName'     => 'required|min:3',
            'serviceDesc'    => 'required|max:255',                       
          ];
          $validator = Validator::make($request->all(), $rules);
          if ($validator->fails()) {
            // Validation failed
             return response()->json([
              'message' => $validator->messages(),
            ]);
          }
          else{
                   $service = Service::find($id);
                    $service->serviceName=$request->serviceName;
                    $service->serviceDesc=$request->serviceDesc;
                    $service->modDate=Carbon::now();
                    $service->modUser='adads';
                    $service->updated_at=Carbon::now();
                    $service->save();

              
                    if ( $service->save())
                    {
                        return response()->json([
                            'message' => 'servicee added sucessfully.',
                        ]);
                    }
                    
                    else {
                        return response()->json([
                        'message' => 'Something went wrong please try letter.',
                        ]);
                    }
    }
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
