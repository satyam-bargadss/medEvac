<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;

use App\Plan;

use Carbon\Carbon;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
       return Plan::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //return $request->all();exit;
       
        $rules = [
            'planName'     => 'required|min:3',
            'frequency'    => 'required|max:255',
            'fee' => 'required|min:8|numeric',
            'initiatonFee' =>'required|min:10|numeric',
            'monthlyFee' =>'required|min:10|numeric',
                      
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
                'planName' => $request->planName,
                'frequency' => $request->frequency,
                'fee' => $request->fee,
                'initiatonFee' => $request->initiatonFee,
                'monthlyFee' => $request->monthlyFee,               
                'modDate'=>Carbon::now(),
                'modUser'=>'abc',
                'created_at'=>Carbon::now()
              ];
              // $user = User::GetInsertId($postArray);
              $plan = Plan::insert($postArray);
             // print_r( $plan);exit;
               //dd($plan->planId);
              if($plan) {
                $user = Auth::user();
                print_r($user);
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
        //return $request->all();exit;
        $rules = [

            'planName'     => 'required|min:3',
            'frequency'    => 'required|max:255',
            'fee' => 'required|min:8|numeric',
            'initiatonFee' =>'required|min:10|numeric',
            'monthlyFee' =>'required|min:10|numeric',                     
          ];
          $validator = Validator::make($request->all(), $rules);
          if ($validator->fails()) {
            // Validation failed
             return response()->json([
              'message' => $validator->messages(),
            ]);
          }
          else{
                   $plan = Plan::find($id);
                    $plan->planName=$request->planName;
                    $plan->frequency=$request->frequency;
                    $plan->fee=$request->fee;
                    $plan->initiatonFee=$request->initiatonFee;
                    $plan->monthlyFee=$request->monthlyFee;
                    $plan->modDate=Carbon::now();
                    $plan->modUser='adads';
                    $plan->updated_at=Carbon::now();
                    $plan->save();

              
                    if ( $plan->save())
                    {
                        return response()->json([
                            'message' => 'Plane added sucessfully.',
                        ]);
                    }
                    
                    else {
                        return response()->json([
                        'message' => 'Something went wrong please try letter.',
                        ]);
                    }
            }
    /*
          $share = Share::find($id);
          $share->share_name = $request->get('share_name');
          $share->share_price = $request->get('share_price');
          $share->share_qty = $request->get('share_qty');
          $share->save();
    
          return redirect('/shares')->with('success', 'Stock has been updated');
     */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = Plan::find($id);
        $plan->delete();
        if( $plan->delete() == false ){
            return response()->json([
                'message' => 'Plan deleted sucessfully.',
            ]);
        }
        else{
            return response()->json([
                'message' => 'Something went wrong please try letter.',
                ]);
        }
    }
}
