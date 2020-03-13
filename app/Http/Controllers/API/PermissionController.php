<?php 
namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Agent;
//use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Mail;
use App\User;
//use App\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class PermissionController extends Controller
{
		/*for admin permission managment start*/
      public function permission_management(Request $request){
		   // print_r($request->all());exit;
			 $permission = [
          'member_view' => $request->memberView,
			  'member_edit' => $request->memberEdit,
			  'member_add' => $request->memberAdd,
			  'agent_add' => $request->agentAdd,
            'agent_view' => $request->agentView,
            'agent_edit' => $request->agentEdit,
			'commission_view' => $request->commission,
			 'group_add' =>$request->groupAdd,
			 'group_edit' =>$request->groupEdit,
			 'group_view' =>$request->groupView,
			 'claim' => $request->claim,
			 'member_payment' =>$request->memberPayment,
		   
         ];
		// print_r($permission);exit;
			$isUserPermission = DB::table('user_permission')->where('user_id', '=', $request->userId)
                ->get();
				$permission_count = $isUserPermission->count();
				//echo $permission_count;exit;
			if($permission_count == 0){
				//echo "insert";exit;
			  $permission = DB::table('user_permission')->insert($permission);
			}
			else {
				//echo "update";exit;
			
				$permission = DB::table('user_permission')->where('user_id',$request->userId)->update($permission);
			}
			if($permission)
			{
			 return response()->json([
				 'status'=>'200',
				 'permission'=>$permission,
				]);
			}
            else {
				 return response()->json([
				 'status'=>'203',
				 'permission'=>$permission,
				]);
			}			
         
	 }
	 /*for admin permission managment end*/
	 /*for user permission start*/
	 public function user_permission(Request $request){
		/* $per_user_detail = DB::table('user_permission')
		 ->select('user_permission.permission_Id','user_permission.member_view','user_permission.member_edit','user_permission.member_add',
      'user_permission.agent_add','user_permission.agent_view','user_permission.agent_edit','user_permission.commission_view','user_permission.group_add','user_permission.group_edit','user_permission.group_view','user_permission.claim','user_permission.member_payment','user_permission.user_id','users.email', 'users.first_name', 'users.last_name')
	     ->leftJoin('users','users.id', '=', 'user_permission.user_id')
	    ->leftJoin('users','users.id', '=', 'role_management.roll_id')
		 ->where('user_permission.user_id', '=', $request->userId)
                ->get();
				*/
			$per_user_detail = 	DB::select( DB::raw("SELECT user_permission.permission_Id,user_permission.member_view,user_permission.member_edit,user_permission.member_add,
 user_permission.agent_add,user_permission.agent_view,user_permission.agent_edit,user_permission.commission_view,
 user_permission.group_add,user_permission.group_edit,user_permission. group_view,user_permission.claim,user_permission.member_payment,user_permission.user_id,users.email, users.first_name, users.last_name ,role_management.roll_name
FROM `user_permission`
left join `users` ON users.id=user_permission.user_id
left join `role_management` ON users.roll_id=role_management.roll_id
where user_permission.user_id = $request->userId") );
	   if($per_user_detail)
			{
			 return response()->json([
				 'status'=>'200',
				 'per_user_detail'=>$per_user_detail,
				]);
			}
			else {
				 return response()->json([
				 'status'=>'203',
				 'per_user_detail'=>'',
				]);
			}
	 }
	 /*for user permission end*/
	 /*fo user registration start*/
	 public function user_registration(Request $request){
		//print_r($request->all());exit;
		 /*
		      Array ( [firstname] => Ana [lastname] => green [email] => uzzal@gmail.com [phone] => +33 3 25 86 48 9 [password] => 12345678 [reTypePassword] => 12345678 [degination] => [role] => 2 [status] => 1 )
		 
		 */
		// $password = Hash::make($request->newPassword);
		 //$2y$10$HXiuNpDqBcQIsuDlSrRXcOGoXGxN7UO5teYVPpjxuan2Z07cur0wW
		 //
		 //$2y$10$js1NystQIN8mAgaBrXQsIeYzCwCO/Rl.GcFKHM54rmI.O/YTVHpXu
		 
		 //$2y$10$WCT7k7Ex5zbRaINt83.rROg63M1eW3ANg/hfEeQFWzmIr2yHFYKzG
		 
		 
		 //$2y$10$JCgsm1EWex.bAuzsP5i1SeS0rTp3ET2lsgXGB0I2//bSwjHv1Cvgy
		 //echo $password;exit;
		 $register_user = array(
		     'first_name' => $request->firstname,
			  'last_name' => $request->lastname,
			  'designation' =>$request->firstname,
			   'modDate' => date("y-m-d"),
			
            'email' => $request->email,
            'password' =>$request->password,
			////Hash::make($request->newPassword)
			'api_token' => Str::random(60),
			 'name' =>$request->firstname,
			 'status' => $request->status,
			 'roll_id' => $request->role,
			 'created_at'=>Carbon::now(),
			  'updated_at'=>date("y-m-d"),
		 );
		 DB::table('users')->insert(
                 $register_user
                );
			 $user_id = DB::getPdo()->lastInsertId();
			  $get_role_wise_permis = DB::table('role_management')->where('roll_id', '=', $request->role)
                ->get();
				//print_r($get_role_wise_permis[0]->roll_id);exit;
				/*
				( [items:protected] => Array ( [0] => stdClass Object ( [roll_id] => 2 [member_view] => 1 [member_edit] => 1 [member_add] => 1 [agent_add] => 1 [agent_view] => 1 [agent_edit] => 0 [commission_view] => 1 [group_add] => 1 [group_edit] => 1 [group_view] => 1 [claim] => 1 [payment] => 1 [roll_name] => Admin ) ) )
				*/
				 $permission = [
					   'member_view' =>$get_role_wise_permis[0]->member_view,
			  'member_edit' =>$get_role_wise_permis[0]->member_edit,
			  'member_add' =>$get_role_wise_permis[0]->member_add,
			  'agent_add' =>$get_role_wise_permis[0]->agent_add,
            'agent_view' => $get_role_wise_permis[0]->agent_view,
            'agent_edit' =>$get_role_wise_permis[0]->agent_edit,
			'commission_view' =>$get_role_wise_permis[0]->commission_view,
			 'group_add' =>$get_role_wise_permis[0]->group_add,
			 'group_edit' =>$get_role_wise_permis[0]->group_edit,
			 'group_view' =>$get_role_wise_permis[0]->group_view,
			 'claim' => $get_role_wise_permis[0]->claim,
			 'member_payment' =>$get_role_wise_permis[0]->member_payment,
					   'user_id'=>$user_id,		   
                ];
				
			// role_management
			//print_r($permission);exit;
			  $permission_insert = DB::table('user_permission')->insert($permission);
			  
			  if($permission_insert)
			{
			 return response()->json([
				 'status'=>'200',
				
				]);
			}
            else {
				 return response()->json([
				 'status'=>'203',
				
				]);
			}
	 }
	 /*for user registration end*/
		/*for role permission start*/
	  public function role_permission(Request $request){
		  /*
		  
		  Array ( [memberAdd] => 1 [memberView] => 1 [memberEdit] => 1 [agentView] => 1 [agentAdd] => 1 [groupView] => 1 [groupAdd] => 1 [commission] => 1 [payment] => 1 [claim] => 1 [groupEdit] => 1 [agentEdit] => 1 )
		  
		  
		  
		  
		  */
		 //print_r($request->all());exit;
		 $role_permission = array(
		     'member_view' => $request->memberView,
			  'member_edit' => $request->memberEdit,
			  'member_add' => $request->memberAdd,
			  'agent_add' => $request->agentAdd,
            'agent_view' => $request->agentView,
            'agent_edit' => $request->agentEdit,
			'commission_view' => $request->commission,
			 'group_add' =>$request->groupAdd,
			 'group_edit' =>$request->groupEdit,
			 'group_view' =>$request->groupView,
			 'claim' => $request->claim,
			 'member_payment' =>$request->memberPayment,
		    
		 );
		 $role_permission = DB::table('role_management')->where('roll_id',$request->userRole)->update($role_permission);
			
			if($role_permission)
			{
			 return response()->json([
				 'status'=>'200',
				 'permission'=>$role_permission,
				]);
			}
            else {
				 return response()->json([
				 'status'=>'203',
				 'permission'=>$role_permission,
				]);
			}
	 }
		/*role permission end*/
	     // forte shedule report
	
	 public function role_wise_detail(Request $request){
		   //print_r($request->all());exit;
		  
		$get_role_details = DB::table('role_management')->where('roll_id', '=', $request->roleId)
                ->get();  
			//print_r($get_role_details);exit;
			
			
			
			if($get_role_details)
			{
			 return response()->json([
				 'status'=>'200',
				 'roleDetails'=>$get_role_details,
				]);
			}
            else {
				 return response()->json([
				 'status'=>'203',
				 'roleDetails'=>$get_role_details,
				]);
			}
			
	 }
	 public function commision_calculation(){
		 
	 }


}
?>