<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;

use App\Plan;
use DB;

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
	 /*create plan start */
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
		  /*create plan end*/

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
	 /*plan view start*/
    public function show($id)
    {
        $plan = User::where('planId', $id)->first();
		 if($plan) {
               return response()->json([
                'planName' => $plan->planName,
                'frequency' => $plan->frequency,
                'fee' => $plan->fee,
                'initiatonFee' => $plan->initiatonFee,
                'monthlyFee' => $plan->monthlyFee,               
               ]);
             }
            else {
             return response()->json([
               'message' => 'Invalid Password',
             ]);
           }
    }
	/*plan view end*/
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
	 /*update plan start */
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
	/*end update plan*/
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 /*plan delete start*/
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
	/*plan delete end*/
	/*plan insert start */
	  public function planInsert(Request $request){
	        /*  echo "<pre>";
			 print_r($request->all());
			exit;   */  
			
			$group_all = $request->groupId;
			//print_r($group_all);exit;
			$count = count($group_all);
			//echo $count;exit;
			for ( $i =0; $i<$count; $i++){
				//print_r($key); exit;
				$group_id[] = $group_all[$i]['id'];
				 
				}
				//print_r($group_id);exit;
				$groupId = implode(',',$group_id);
			   // print_r($groupId);exit;
			
		 /* $groupId = array("id"=>group_id
         );
		echo "<pre>";
		print_r($groupId);
		exit;  */
	    $planDetails = [
           'planName'      => $request->planeName,
           'frequency'      => $request->frequency,
           //'companyName'=>$request->companyName,
			//'groupCode' =>$request->groupCode,
			'groupId' =>$groupId,
			'fee'		=>$request->individualFee,
		   'familyFee'		=>$request->familyFee,
		   'burial_individual'	=>$request->burialInd,
		   'burial_family'			=>$request->burial_family,
		   'country'			=>$request->country,
		   'initiatonFee'			=>$request->initiatonFee,
		   'state1' 	=>$request->state1,
		  /* 'country'		=>$request->country,
		   'membershiptype' =>$request->membershiptype,
		   'availableplans'	=>$request->availableplans,
		   'basegroup'		=>$request->basegroup, */
          // 'isActive'      => $request->isActive,
            'created_at' => Carbon::now()
         ];
				/* echo "<pre>";
				print_r($planDetails);
				exit;  */   
		 $planId =  DB::table('plans')->insert($planDetails);
		 if($planId)
		 {
				  return response()->json([
				  'status'=>'200',
			   ]);
		 }
   }
   /*plan insert end*/
   /*Group code details for plan start*/
   public function groupCodeDetailsForPlan(Request $request){
   // print_r($request->all());exit;
		
		 if($request->country == 'USA')
     {
          $group_code = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   
     ->where('country', $request->country)
	 ->where('status', '=', 'Active')
	 //'column_1', '=', 'value_1'
           ->get();
		   
		   
     }
	  if($request->country == 'Bahamas')
     {
          $group_code = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   
     ->where('country', $request->country)
	 ->where('status', '=', 'Active')
           ->get();
		   
		   
     }
	  if($request->country == 'BVI')
     {
          $group_code = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   
     ->where('country', $request->country)
	 ->where('status', '=', 'Active')
           ->get();
		   
		   
     }
	  if($request->country == 'US Virgin Islands')
     {
          $group_code = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
		   
     }
	
	 /* if($request->country == 'USA')
     {
     $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId Where groups.status='Active' AND country='USA'"));
	 }
	 else if($request->country == 'Bahamas')
     {
     $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId Where groups.status='Active' AND country='Bahamas'"));
	 }
	 else if($request->country == 'BVI')
     {
     $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId Where groups.status='Active' AND country='BVI'"));
	 }
	  else if($request->country == 'USVI')
     {
     $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId Where groups.status='Active' AND country='USVI'"));
	 } */
	// echo"<pre>";
	 //print_r($group_code);exit;
    $total_groups=DB::select( DB::raw("select count(groupId) total_groups from groups"));
  $total_active_groups=DB::select( DB::raw("select count(groupId) total_active_groups from groups where status='Active'"));
  $total_inactive_groups=DB::select( DB::raw("select count(groupId) total_inactive_groups from groups where status='Inactive'"));
   /*  echo "<pre>";
    print_r($group_code);
    exit; */
	       $count = count($group_code);
		   //echo $count;exit;
           if($count > 0)
		   {
          return response()->json([
          'status'=>'200',
          'groupDetails'=>$group_code,
          'total_groups'=>$total_groups,
          'total_active_groups'=>$total_active_groups,
          'total_inactive_groups'=>$total_inactive_groups,
         ]);
		   }
		 else 
		 {
		return response()->json([
          'status'=>'203',
          
         ]);
		 
		 }
  } 
  /*group code details for plan end*/
  
  /* start select plan details */
    public function planDetail(Request $request)
     {
         /* echo "<pre>";
 	    print_r($request->all());
 	    exit();   */

		   $planDetails = DB::TABLE('plans') 
				->select('plans.planId','plans.planName','plans.frequency','plans.fee','plans.burial_individual','plans.burial_family','plans.familyFee','plans.initiatonFee','plans.country','plans.state1','plans.status','plans.created_at','groups.groupCode')
				->leftjoin('groups', 'plans.groupId', '=', 'groups.groupId')
				->where('plans.planId',$request->planId)
				//->whereRaw("find_in_set($request->groupId,groupId)")
				->get();
				//selected group
				 $group_deatil = DB::TABLE('plans') 
				->select('groupId')
				->where('plans.planId',$request->planId)
				->get();
				$selectedGroup_id = $group_deatil[0]->groupId;
				//echo$selectedGroup_id;exit;
				$selectedGroup_id = explode(',',$selectedGroup_id);
				
				//$selected__group_detail =
				//$a = [1,2,3];
				//print_r($a);exit;
                			 
			 $getgroupCode=DB::TABLE('groups')
							->select('groupId','groupCode')->whereRaw("groupId IN (".implode(',', $selectedGroup_id).")")->get();
				  // echo "<pre>";
				//print_r($getgroupCode);
				//exit;  
				//$selectGroupCode=explode(',',$getgroupCode);
				/* echo "<pre>";
				print_r($selectGroupCode);
				exit; */
				
				
		 if($planDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'planDetails'=>$planDetails,
					'selectedGroupId'=>$getgroupCode
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'planDetails'=>$planDetails,
					'selectedGroupId'=>$getgroupCode
              ]);
          }	
     }
	 /*end plan details*/
	 /*update plan start*/
	 public function updatePlan(Request $request){
		 //echo "<pre>";
		 //print_r($request->all());exit;
		 $group_all = $request->groupId;
			//print_r($group_all);exit;
			$count = count($group_all);
			//echo $count;exit;
			for ( $i =0; $i<$count; $i++){
				//print_r($key); exit;
				$group_id[] = $group_all[$i]['id'];
				 
				}
				//print_r($group_id);exit;
				$groupId = implode(',',$group_id);
				
		 $postplan = [
			  
			  'planName' =>$request->planName,
			  'frequency' =>$request->frequency,
			  'initiatonFee' =>$request->initiatonFee,
			  'familyFee' =>$request->familyFee,
			  'burial_individual' =>$request->burialInd,
			  'burial_family' =>$request->burial_family,
			  'groupId' =>$groupId,
			  'country' =>$request->country,
			  'state1' =>$request->state1,
			  'fee' =>$request->fee
			  
			  
			 
			
        ];
			//echo"<pre>";
			//print_r($postplan);exit;
		 $updateplanDetails = DB::TABLE('plans') 
				->where('plans.planId',$request->planId)
				->update($postplan);
				
				//echo "<pre>";
				//print_r($updateplanDetails);exit;
				
				
		 if($updateplanDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'updateplanDetails'=>$updateplanDetails
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'updateplanDetails'=>$updateplanDetails
              ]);
          }	
			
	 }
	 /*end upadet plan*/
	 /*get group code acording to country start*/
	 public function getGroupDetailCode(Request $request)
	 {
		/* echo "<pre>";
		print_r($request->all());
		exit; */
		// if($request->country == 'USA')
    // {
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
		   /* $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId Where groups.status='Active'"));
		    */
		   
    /*  }
	 
     		else if($request->country == 'Bahamas')
     {
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
     }
	 
	 	else if($request->country == 'BVI')
     {
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
     }
	 else if($request->country == 'US Virgin Islands')
     {
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
     } */
	/*  echo "<pre>";
	 print_r($group_detail);
	 exit; */
	 
	 
			 $count = count($group_detail);
		   //echo $count;exit;
           if($count > 0)
		   {
         return response()->json([
              'status'=>'200',
			  'group_detail'=>$group_detail
           ]);
		   }
		 else 
		 {
		return response()->json([
          'status'=>'203',
          
         ]);
		 
		 }
			
			
	 

			/* if($group_detail)
			  return response()->json([
              'status'=>'200',
			  'group_detail'=>$group_detail
           ]); */
	 }
	 /*end group code acording to country*/
	 /*get plan details acording to group id start*/
	 public function getPlanDetail(Request $request){
		 /*  echo "<pre>";
		print_r($request->all());
		exit; */
		   
		 $planDetails = DB::table('plans')
           ->select('plans.planId', 'planName')
		   //->where('groupId', $request->groupId)
		    ->whereRaw("find_in_set($request->groupId,groupId)")
           ->get();
		  /*  echo "<pre>";
		print_r($planDetails);
		exit; */
          if($planDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'planDetails'=>$planDetails
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'planDetails'=>$planDetails
              ]);
          }
	 }
	 /*end plan details acording to group id */
	 /*total plan start*/
	 public function totalPlan(Request $request){
		/*  echo"<pre>";
		 print_r($request->all());
		 exit; */
     
    $total_plans=DB::select( DB::raw("select count(planId) total_plans from plans"));
	$total_active_plans=DB::select( DB::raw("select count(planId) total_active_plans from plans where status='Active'"));
	$total_inactive_plans=DB::select( DB::raw("select count(planId) total_inactive_plans from plans where status='Inactive'"));
		/* echo "<pre>";
		print_r($total_inactive_groups);
		exit; */
	         if($total_plans)
				  return response()->json([
				  'status'=>'200',
				  //'groupDetails'=>$group_code,
				  'total_plans'=>$total_plans,
				  'total_active_plans'=>$total_active_plans,
				  'total_inactive_plans'=>$total_inactive_plans,
			   ]);
	}
	
	/*end total plan*/
	/*plan edit acording to group code start*/
	 public function getplanEditGroupCode(Request $request){
		 /* echo "<pre>";
		 print_r($request->all());
		 exit; */
		 
	//	  if($request->country == 'USA')
    // {
		 // echo  $request->country; exit;
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
		// print_r($group_detail) ;exit;
		   
    // }
	 
     	/*	else if($request->country == 'Bahamas')
     {
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
     }
	 
	 	else if($request->country == 'BVI')
     {
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
     }
	 else if($request->country == 'US Virgin Islands')
     {
          $group_detail = DB::table('groups')
           ->select('groupId','groupCode','groupName','created_at','status' )
		   //->select('groupId')
     ->where('country', $request->country)
	 ->where('status','Active')
           ->get();
		   
     }*/
	/*  echo "<pre>";
	 print_r($group_detail);
	 exit; */
	 
	 
			 $count = count($group_detail);
		   //echo $count;exit;
           if($count > 0)
		   {
         return response()->json([
              'status'=>'200',
			  'group_detail'=>$group_detail
           ]);
		   }
		 else 
		 {
		return response()->json([
          'status'=>'203',
          
         ]);
		 
		 }
			
		 
	 }
	 /*plan edit acording to group code end*/
	 /*for update active to inactive group start */
	 public function InactiveGroup(Request $request){
		 /* echo "<pre>";
		 print_r($request->all());
		 exit; */
		  $groupStatus = [
			  
			  'status' =>'Inactive'
			  
			
        ];
			//print_r($postAgentPayment);exit;
		 $updategroupDetails = DB::TABLE('groups') 
				->where('groups.groupId',$request->groupId)
				->update($groupStatus);
				
				//print_r($updategroupDetails);exit;
/* 		 $memberstatus= DB::TABLE('customers')
						->where('customers.groupId',$request->groupId)
						->get(); */
				/* echo "<pre>";
				print_r($memberstatus);
				exit; */
				
				//isActive
				 $memberinactive = [
			  
			  'isActive'=>'No'
        ];
		 $updateMemberstatus = DB::TABLE('customers') 
				->where('customers.groupId',$request->groupId)
				->update($memberinactive); 
				
				/* echo "<pre>";
				print_r($updateMemberstatus);
				exit; */
				
				
		 if($updategroupDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'updategroupDetails'=>$updategroupDetails,
					'updateMemberstatus'=>$updateMemberstatus
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'updategroupDetails'=>$updategroupDetails,
					'updateMemberstatus'=>$updateMemberstatus
              ]);
          }	
		 
		 
	 }
	 /*for update active to inactive group end*/
	 /*for group active start*/
	  public function ActiveGroup(Request $request){
		 /*  echo "<pre>";
		 print_r($request->all());
		 exit; */ 
		  $groupStatus = [
			  
			  'status' =>'Active'
			  
			
        ];
			//print_r($postAgentPayment);exit;
		 $activegroupDetails = DB::TABLE('groups') 
				->where('groups.groupId',$request->groupId)
				->update($groupStatus);
				
				//print_r($updategroupDetails);exit;
/* 		 $memberstatus= DB::TABLE('customers')
						->where('customers.groupId',$request->groupId)
						->get(); */
				/* echo "<pre>";
				print_r($memberstatus);
				exit; */
				
				//isActive
				
				
				
		 if($activegroupDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'activegroupDetails'=>$activegroupDetails,
					//'updateMemberstatus'=>$updateMemberstatus
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'activegroupDetails'=>$activegroupDetails,
					//'updateMemberstatus'=>$updateMemberstatus
              ]);
          }	
		 
		 
	 }
	 /*for group active end*/
	 /*for inactive plan start*/
	 public function InactivePlan(Request $request){
		  /* echo "<pre>";
		 print_r($request->all());
		 exit; */ 
		  $planStatus = [
			  
			  'status' =>'Inactive'
			  
			
        ];
			//print_r($planStatus);exit;
		 $updateplanDetails = DB::TABLE('plans') 
				->where('plans.planId',$request->planId)
				->update($planStatus);
				
				//print_r($updateplanDetails);exit;
/* 		 $memberstatus= DB::TABLE('customers')
						->where('customers.groupId',$request->groupId)
						->get(); */
				/* echo "<pre>";
				print_r($memberstatus);
				exit; */
				
				//isActive
				 $memberinactive = [
			  
			  'isActive'=>'No'
        ];
		 $updateMemberstatus = DB::TABLE('customers') 
				->where('customers.planId',$request->planId)
				->update($memberinactive); 
				
				/* echo "<pre>";
				print_r($updateMemberstatus);
				exit; */
				
				
		 if($updateplanDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'updateplanDetails'=>$updateplanDetails,
					'updateMemberstatus'=>$updateMemberstatus
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'updateplanDetails'=>$updateplanDetails,
					'updateMemberstatus'=>$updateMemberstatus
              ]);
          }	
		 
		 
	 }
	 /*for inactive plan end*/
	 /*for active plan start*/
	 
	 public function ActivePlan(Request $request){
		   /* echo "<pre>";
		 print_r($request->all());
		 exit;  */
		  $planStatus = [
			  
			  'status' =>'Active'
			  
			
        ];
			//print_r($planStatus);exit;
		 $activeplanDetails = DB::TABLE('plans') 
				->where('plans.planId',$request->planId)
				->update($planStatus);
				
				//print_r($updategroupDetails);exit;
/* 		 $memberstatus= DB::TABLE('customers')
						->where('customers.groupId',$request->groupId)
						->get(); */
				/* echo "<pre>";
				print_r($memberstatus);
				exit; */
				
				//isActive
				
				
				
		 if($activeplanDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'activeplanDetails'=>$activeplanDetails,
					//'updateMemberstatus'=>$updateMemberstatus
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'activeplanDetails'=>$activeplanDetails,
					//'updateMemberstatus'=>$updateMemberstatus
              ]);
          }	
		 
		 
	 }
	 /*for active plan end*/
	 /*group code check active or inactive start*/
	  public function GroupCodeSearch(Request $request){
		   /* echo "<pre>";
		  print_r($request->all());
		  exit;    */
	  if($request->groupCode !='')
	  {
     $duplicateGroup = DB::table('groups')
                                 ->where( [
                                     'groupCode'       => $request->groupCode,
									 'country' => $request->country,
                                 ] )->first();
				/* echo "<pre>";
				print_r($duplicateGroup);
				exit; */
                if (  $duplicateGroup ) {
                    return response()->json([
              'status'=>'203',
			   ]);
                  }
				  else{

                    return response()->json([
              'status'=>'200',
			   ]);
                  }
	  }
   }
   /*group code check active or inactive end*/
   /*MemberCommission Report*/
   public function MemberCommission(Request $request){
	  /*  echo "<pre>";
	   print_r($request->all());
	   exit; */
	   //$customerId=$request->all();
	   //print_r($customerId);exit;
	   $memberCommission=DB::select(DB::raw("SELECT agents.agentCode,(CASE WHEN (agents.middleName is NOT NULL) THEN (CONCAT(`agents`.`firstName`,' ',`agents`.`LastName`)) 
ELSE 
	(CONCAT(`agents`.`firstName`,' ',`agents`.`LastName`)) END) as agent,groups.groupCode,cu.clientType,plans.frequency,plans.fee,agentpayment.Commission,agentpayment.total_earned_advance,agentpayment.unearned_advance,cast(ChargeBackInterest as decimal(10,2))as ChargeBackInterest

	FROM customers as cu
	LEFT JOIN agents ON cu.agentId = agents.agentId 
	LEFT JOIN plans ON cu.planId = plans.planId 
	LEFT JOIN groups ON cu.groupId = groups.groupId 
	LEFT JOIN agentpayment ON cu.customerId = agentpayment.customerId
	where cu.customerId=$request->customerId
							 "));
		$membername=DB::select(DB::raw("SELECT cu.customerId,(CASE WHEN (cu.middleName is NOT NULL) THEN (CONCAT(cu.firstName,' ',cu.middleName,' ',cu.LastName))
 ELSE 
	(CONCAT(cu.firstName,' ',cu.LastName)) END) AS member
			FROM customers as cu
			where cu.customerId=$request->customerId
							"));
							// echo "<pre>";
							// print_r($memberCommission);
							// exit; 
							 if ($memberCommission ) {
								return response()->json([
								
								'status'=>'200',
								'memberCommission'=>$memberCommission,
								'membername'=>$membername,
								]);
							}
							else{

								return response()->json([
								'status'=>'203',
								'memberCommission'=>$memberCommission,
								'membername'=>$membername,
								]);
							}
							
   }
}
