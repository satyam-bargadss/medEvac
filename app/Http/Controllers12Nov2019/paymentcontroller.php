<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class paymentcontroller extends Controller
{
    function index(Request $request){
		//print_r($request->paymentId);exit;
		$data = array('paymentId' => $request->paymentId,
		                      'planename' => $request->planename,
							  'firstName' => $request->firstName,
							  'lastName' => $request->lastName,
							  'fee' => $request->fee,
							  'tax' =>$request->countPerson*$request->burialFee
		                        );
								//print_r($paymentDetail);
		
		
		
		
		//return view("payment",$paymentDetail);
		return view('payment')->with('data2',$data);
	}
	
}
