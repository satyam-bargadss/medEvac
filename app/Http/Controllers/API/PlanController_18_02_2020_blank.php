<?php 
  namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Mail;
use App\plan;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str; 
 

class PlanController extends Controller
{

      public function planInsert(Request $request){
	      echo "<pre>";
	  print_r($request->all());
	    exit; 
	    $group = [
           'groupCode'      => $request->groupCode,
           'groupName'      => $request->groupName,
           //'companyName'=>$request->companyName,

		   'address'		=>$request->address,
		   'address2'		=>$request->address2,
		   'city'			=>$request->city,
		   'state'			=>$request->state,
		   'zip'			=>$request->zip,
		   'phone'			=>$request->phone,
		   'contactperson' 	=>$request->contactperson,
		   'country'		=>$request->country,
		   'membershiptype' =>$request->membershiptype,
		   'availableplans'	=>$request->availableplans,
		   'basegroup'		=>$request->basegroup,
           'status'      => $request->isActive,
            'created_at' => Carbon::now()
         ];
		  /*  echo "<pre>";
	  print_r($group);
	  die('aaaa');  */
		 $groupId =  DB::table('groups')->insert(
                $group
                );
		 if($groupId)
		 {
				  return response()->json([
				  'status'=>'200',
			   ]);
		 }
   }
	 
	
	 

}
?>