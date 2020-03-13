<?php
namespace App\Http\Controllers\API;
//use Request;
use Mail;

use App\customer;
use App\Agent;
use App\plan;
use App\group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
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
	/*member list start*/
	public function index(Request $request)
    {

	   /* $customers = DB::table('customers')
            ->leftJoin('groups', 'customers.groupId', '=', 'groups.groupId')
            ->select('customers.customerId', 'customers.firstName', 'customers.LastName', 'customers.DOB',
			 'customers.country','groups.groupCode')
			->orderBy('customers.customerId', 'desc')
            ->get(); */

	   if($request->isActive != '')
		{
			//echo $request->isActive; exit;
			if($request->isActive == 'Yes')
			{
					 $customers=DB::select( DB::raw("select cu.customerId,
					 (case
when (cu.middleName is not null)  then (CONCAT(cu.firstName,' ',cu.middleName,' ',cu.LastName))
 else (CONCAT(cu.firstName,' ',cu.LastName)) 
 end) AS customer_name,
					 cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 groups.groupCode AS `groupCode`,cu.ModBy, (CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status ,agentpayment.customer_id from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId` 
					 left join `agentpayment` ON `cu`.`customerId`=`agentpayment`.`customerId`
					 WHERE cu.isActive = 'Yes'
					 GROUP BY agentpayment.customerId
					 order by `cu`.`customerId` desc") );
			}
			
			else{
				 $customers=DB::select( DB::raw("select cu.customerId, (case
when (cu.middleName is not null) then (CONCAT(cu.firstName,' ',cu.middleName,' ',cu.LastName))
 else (CONCAT(cu.firstName,' ',cu.LastName)) 
 end) as customer_name,cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 groups.groupCode AS `groupCode`,cu.ModBy,(CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status ,agentpayment.customer_id from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId`
					 left join `agentpayment` ON `cu`.`customerId`=`agentpayment`.`customerId`
					 WHERE cu.isActive = 'No'
					 GROUP BY agentpayment.customerId
					 order by `cu`.`customerId` desc") );
			}
			/* echo "pre";
			print_r($customers);
			exit; */
		}
		else if($request->country != ''){
			if($request->country == 'USA')
			{
			$customers=DB::select( DB::raw("select cu.customerId,(case
when (cu.middleName is not null)  then (CONCAT(cu.firstName,' ',cu.middleName,' ',cu.LastName))
 else (CONCAT(cu.firstName,' ',cu.LastName)) 
 end) AS customer_name,cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 groups.groupCode AS `groupCode`,cu.ModBy,(CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status ,agentpayment.customer_id from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId` 
					 left join `agentpayment` ON `cu`.`customerId`=`agentpayment`.`customerId`
					 WHERE cu.country = 'USA' 
					 GROUP BY agentpayment.customerId
					 order by `cu`.`customerId` desc") );
			}
			else{
				$customers=DB::select( DB::raw("select cu.customerId,(case
when (cu.middleName is not null)  then (CONCAT(cu.firstName,' ',cu.middleName,' ',cu.LastName))
 else (CONCAT(cu.firstName,' ',cu.LastName)) 
 end) AS customer_name,cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 groups.groupCode AS `groupCode`,cu.ModBy,(CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status ,agentpayment.customer_id from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId`
					 left join `agentpayment` ON `cu`.`customerId`=`agentpayment`.`customerId`
					 WHERE cu.country != 'USA'
					 GROUP BY agentpayment.customerId
					 order by `cu`.`customerId` desc") );

			}
		}
    else
		{
	  $customers=DB::select( DB::raw("select customers.customerId,(case
when (customers.middleName is not null)  then (CONCAT(customers.firstName,' ',customers.middleName,' ',customers.LastName))
 else (CONCAT(customers.firstName,' ',customers.LastName)) 
 end) AS customer_name,customers.email , customers.cellPhone as cellPhone, date_format(customers.DOB,'%m-%d-%Y')as DOB,
			 customers.country,groups.groupCode AS `groupCode`,customers.ModBy,(CASE
WHEN customers.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status ,agentpayment.customer_id
             from `customers`
			left join `groups` ON `customers`.`groupId`=`groups`.`groupId`
			left join `agentpayment` ON `customers`.`customerId`=`agentpayment`.`customerId`
			GROUP BY agentpayment.customerId
			order by `customers`.`customerId` desc") );
		}
		//echo"<pre>";
		//print_r($customers);exit;
      $totalCustomers = DB::table('customers')->select('customerId','firstName','LastName','DOB',
      'country','created_at')->orderBy('customerId', 'desc')->get();

	   $activeUsers = DB::table('customers')->where('isActive', 'Yes')->count();
	   $inActiveUsers = DB::table('customers')->where('isActive', 'No')->count();
     $usCustomber = DB::table('customers')->where('country', 'USA')->count();
	   $internationalCustomber = DB::table('customers')->where('country', '!=',  'USA' )->count();
     $insPayment=DB::select( DB::raw("SELECT primary_table.`customerId`,primary_table.paymentletterDate
     FROM
`agentpayment` primary_table,
(SELECT MAX(paymentId) as pay_id FROM `agentpayment` Group by customerId) second_table
     where primary_table.`paymentId` = second_table.pay_id
     AND MONTH(`primary_table`.`paymentletterDate`) = MONTH(CURRENT_DATE()) and primary_table.PaymentDate <> '0'") );
      $totalduecustomer=count($insPayment);
	  $totalCustomers = count($totalCustomers);

	  //echo $count;exit;
	return response()->json([
        'customers' =>$customers,
		'totalCustomers'=>$totalCustomers,
		'activeUsers'=>$activeUsers,
		'inActiveUsers'=>$inActiveUsers,
		'usCustomber'=>$usCustomber,
		'internationalCustomber'=>$internationalCustomber,
    'installmentpayment'=>$totalduecustomer

      ]);
    }
	/*member list end*/
	//function Dashboard
		/*dashboard start*/
	public function dashboard(){
		 /*$totalCustomers = DB::table('customers')->select('customerId','firstName','LastName','DOB',
          'country','created_at')->orderBy('customerId', 'desc')->get();
		   $totalAgents = DB::table('agents')
	      ->select('agentId', 'firstName', 'lastName','dob','isActive')
	      ->orderBy('agentId', 'desc')->get();
		  $all_agent_total_commission=DB::select( DB::raw("SELECT round(sum(`agent_wise_commission`.`total_commission`),2)
		  as all_agent_total_commission FROM `agent_wise_commission`") );
		   $totalAgent_paid_amount = DB::select( DB::raw("SELECT round(sum(TotalFee-adhocPayment),2) as total_paid_ammount FROM `agenttotalfee`"));
	        $totalAgents = count($totalAgents);
	        $totalCustomers = count($totalCustomers);
			return response()->json([
        'totalCustomers' =>$totalCustomers,
		'totalAgents'=>$totalAgents,

		'all_agent_total_commission'=>$all_agent_total_commission,
		'totalAgent_paid_amount'=>$totalAgent_paid_amount,
  ]);*/
  $totalCustomers = DB::table('customers')->select('customerId','firstName','LastName','DOB',
       'country','created_at')->orderBy('customerId', 'desc')->get();
    $totalAgents = DB::table('agents')
     ->select('agentId', 'firstName', 'lastName','dob','isActive')
     ->orderBy('agentId', 'desc')->get();
   $all_agent_total_commission=DB::select( DB::raw("SELECT round(sum(`agent_wise_commission`.`total_commission`),2)
   as all_agent_total_commission FROM `agent_wise_commission`") );
    $totalAgent_paid_amount = DB::select( DB::raw("SELECT round(sum(TotalFee-adhocPayment),2) as total_paid_ammount FROM `agenttotalfee`"));
       $totalAgents = count($totalAgents);
       $totalCustomers = count($totalCustomers);
   $totalCommisionDue = DB::select( DB::raw("SELECT round(sum(total_commission),2)
   as totalCommisionDue FROM `agent_wise_commission`"));
   return response()->json([
     'totalCustomers' =>$totalCustomers,
 'totalAgents'=>$totalAgents,
 'totalCommisionDue'=>$totalCommisionDue,
 'all_agent_total_commission'=>$all_agent_total_commission,
 'totalAgent_paid_amount'=>$totalAgent_paid_amount,
   ]);

	}
	/*dashboard end*/
	/*member login start */
    public function login(Request $request){
        /*if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
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
		/*member login end*/
     /**
      * Show the form for creating a new resource.
      *
      * @return \Illuminate\Http\Response
      */
	  /*member register start*/
     public function register_basic1(Request $request)
     {
		     echo "<pre>";
       print_r($request->all());
       exit;    
             // print $request->type;
     // die('aaaa');
	 if($request->email !='')
	 {
		    $email = $request->email;
	 }


		 if($request->type == 'Family' AND $request->spouse_first_name =='')
		 {
	        return response()->json([
              'status'=>'203',
			  'message'=>'Spouse name is required For Family Type Plan'
		 ]);
		 }
		  // print $request->type;
     // die('aaaa');
      $selectedAgentId = $request->selectedAgentId['value'];
      $agentManagerId="";
      $agentStateManagerId="";
      $agentManagerPromotionId="";
      $agentStateManagerPromotionId="";
      $agent_PromotionId=DB::select( DB::raw("SELECT agentwisePromotioncode.agentPromotionId FROM `agentwisePromotioncode` Where agentwisePromotioncode.agentId=".$request->selectedAgentId['value']) );
      foreach ($agent_PromotionId as $key => $value) {
            // code...
            $agentPromotionId=$value->agentPromotionId;
            //echo $agentPromotionId;
            //die('aaaa');
            //SELECT `managerId`,`managerPromotionId`,`stateManagerId`,`stateManagerPromotionId` FROM `agentmanagers` WHERE `agentId`=19 and `agentPromotionId`=23
            $agent_upper_level_id=DB::select( DB::raw("SELECT `managerId`,`managerPromotionId`,`stateManagerId`,`stateManagerPromotionId` FROM `agentmanagers` WHERE `agentId`=".$request->selectedAgentId['value']." and `agentPromotionId`=".$agentPromotionId) );

            foreach ($agent_upper_level_id as $key => $value) {
              // code...
              $agentManagerId=$value->managerId;
              $agentStateManagerId=$value->stateManagerId;
              $agentManagerPromotionId=$value->managerPromotionId;
              $agentStateManagerPromotionId=$value->stateManagerPromotionId;
            }

            /*$agentManager_PromotionId =  DB::select( DB::raw("SELECT am.managerId
            FROM `agentmanagers` as am
            WHERE am.agentId IN (SELECT am.agentId
            FROM `agentwisePromotioncode` as awp
            LEFT JOIN agentmanagers as am ON am.agentId=awp.agentPromotionId
            where awp.agentPromotionId=$agentPromotionId)"));
            foreach ($agentManager_PromotionId as $key => $value) {
              // code...
              $agentManagerPromotionId=$value->managerId;
            }

            $agentStateManager_PromotionId =DB::select( DB::raw("SELECT am.stateManagerId
            FROM `agentmanagers` as am
            WHERE am.agentId IN (SELECT am.agentId
            FROM `agentwisePromotioncode` as awp
            LEFT JOIN agentmanagers as bm ON bm.agentId=awp.agentPromotionId
            Where awp.agentPromotionId=$agentPromotionId)"));
            foreach ($agentStateManager_PromotionId as $key => $value) {
              // code...
              $agentStateManagerPromotionId=$value->stateManagerId;
            }*/
      }
      $planFee="";
       if($request->type==='Family'){
         $plan_actual_fees=DB::select( DB::raw("SELECT familyFee FROM `plans` Where planId=$request->plan") );
         foreach ($plan_actual_fees as $key => $value) {
           // code...
           $planFee=$value->familyFee;
         }
       }else{
         $plan_actual_fees=DB::select( DB::raw("SELECT fee FROM `plans` Where planId=$request->plan") );
         foreach ($plan_actual_fees as $key => $value) {
           // code...
           $planFee=$value->fee;
         }
       }
       $overRideFee=($planFee-$request->fees);
		  // echo "<pre>";
	  // print_r($request->selectedAgentId['value']);
	 //  die('aaaa');


	   //echo  $selectedAgentId;exit;
		DB::beginTransaction();
			try {
              /* $groupId = DB::table('groups')->insertGetId(
              [ 'groupCode' => $request->planid ]
              ); */
              /*if($request->selectedManagerId){
              $managerId=$request->selectedManagerId;
              }else{
                $managerId=0;
              }

              if($request->selectedStateManagerId){
              $statemanagerId=$request->selectedStateManagerId;
              }else{
                $statemanagerId=0;
              }*/
                  $password = $this->random_num(8);
				  //$customerId = $request->customerId;
				 
         $postCustomber = [
							  //'customerId' =>$request->customerId,
                              'firstName'      => $request->firstname,
							  'middleName'    =>$request->middlename,
                              'LastName'      => $request->lastname,
                              'DOB'=>date("Y-m-d", strtotime("$request->dob")),
                              'city'      => $request->city,
                              'city1'      => $request->city1,
                              'state'      => $request->state_s,
                              'state1'      => $request->state_s1,
                              'zip'  =>$request->zipcode,
                              'zip1'      => $request->zip,
                              'zip'  =>$request->zipcode,
                              //'groupCode'=>$request->groupId,
                              'groupId'=>$request->groupId,
                              'isActive'=>'Yes',
                              //'password'      => $request->password,
							  'password'      => $password,
                              'email' =>$request->email,
                              'companyName'      => $request->companyname,
                              //'writing_agent'      => $managerId,
                              'writing_agent'      => $agentManagerId,
                              //'agentId'  =>  $selectedAgentId,
                              'agentId'  =>  $selectedAgentId,
                              //'agent_manager' =>$managerId,
                              'agent_manager'=>$agentManagerId,
                              'country'  =>$request->country,
                              'country1'  =>$request->country1,
                              'cellPhone'  =>$request->phone,
                              'clientType'  =>$request->type,
                              'mobile2'  =>$request->phone1,
                              'planId'  =>$request->plan,
                              'address1'  => $request->address1,
                              'address2'  => $request->address2,
                              'mailing_address1'  => $request->maddress1,
                              'mailing_address2'  => $request->maddress2,
                              'dependent1FirstName' =>$request->customerRegisterFormDependantFirstName,
                              'dependent2FirstName' =>$request->customerRegisterFormDependantFirstName1,
                              'dependent3FirstName' =>$request->customerRegisterFormDependantFirstName2,
							  'dependent1MiddleName' =>$request->customerRegisterFormDependantMiddleName,
                              'dependent2MiddleName' =>$request->customerRegisterFormDependantMiddleName1,
                              'dependent3MiddleName' =>$request->customerRegisterFormDependantMiddleName2,
							  /* 'dependent4FirstName' =>$request->customerRegisterFormDependantFirstName3,
							  'dependent5FirstName' =>$request->customerRegisterFormDependantFirstName4,
							  'dependent6FirstName' =>$request->customerRegisterFormDependantFirstName5,
							  'dependent7FirstName' =>$request->customerRegisterFormDependantFirstName6,
							  'dependent8FirstName' =>$request->customerRegisterFormDependantFirstName7,
							  'dependent9FirstName' =>$request->customerRegisterFormDependantFirstName8,
							  'dependent10FirstName' =>$request->customerRegisterFormDependantFirstName9, */
							  'dependent1LastName' =>$request->customerRegisterFormDependantLastName,
                              'Dependent2LastName' =>$request->customerRegisterFormDependantLastName1,
                              'dependent3LastName' =>$request->customerRegisterFormDependantLastName2,
							  /* 'dependent4LastName' =>$request->customerRegisterFormDependantLastName3,
							  'dependent5LastName' =>$request->customerRegisterFormDependantLastName4,
							  'dependent6LastName' =>$request->customerRegisterFormDependantLastName5,
							  'dependent7LastName' =>$request->customerRegisterFormDependantLastName6,
							  'dependent8LastName' =>$request->customerRegisterFormDependantLastName7,
							  'dependent9LastName' =>$request->customerRegisterFormDependantLastName8,
							  'dependent10LastName' =>$request->customerRegisterFormDependantLastName9, */
							  'dependent1DOB' =>$request->customerRegisterFormDob,
                              'dependent2DOB' =>$request->customerRegisterFormDob1,
                              'dependent3DOB' =>$request->customerRegisterFormDob2,
                              /* 'dependent4DOB' =>$request->customerRegisterFormDob3,
							  'dependent5DOB' =>$request->customerRegisterFormDob4,
							  'dependent6DOB' =>$request->customerRegisterFormDob5,
							  'dependent7DOB' =>$request->customerRegisterFormDob6,
							  'dependent8DOB' =>$request->customerRegisterFormDob7,
							  'dependent9DOB' =>$request->customerRegisterFormDob8,
							  'dependent10DOB' =>$request->customerRegisterFormDob9, */
							  'spouseFirstName'  =>$request->spouse_first_name,
							  'spouseMiddleName' =>$request->spouse_middle_name,
                              'spouseLastName'  =>$request->spouse_last_name,
                              'spouseDOB' =>$request->familyDateOfBirth,
                              'initiationFee'=>$request->initiationFee,
                              'burialFee'=>$request->burialFee,
							  'seminarFee'=>$request->seminarFee,
                              'created_at' =>Carbon::now(),
                              'membershipDate' =>date("y-m-d"),
                              'modDate' =>date("y-m-d"),
                              'modBy' =>$request->aurthName,
                              'note'=>$request->note
                          ];
						      /* echo "<pre>";
						  print_r($postCustomber);
						  exit;     */
						//  $customerId = customer::insertGetId($postCustomber);
				  //echo $customerId;exit;
						 /* if($request->email !='')
						  {
							  
                          Mail::raw('Thank You for registration!', array('password' => $password), function($message)    use ($email)
                          {

                              $message->subject('Registration Successful!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						 
						   
						 Mail::send([], [], function ($message )use ($email,$customerId,$password)  {
                        $message->to($email)
                              ->subject('Registration Successful!')
							  ->from('globalmedivac@gmail.com', 'Global Medivac')
                            ->setBody("Hi, welcome user Thank You for registration.<br>You may now login your account at:
										http://35.235.80.37:3000/member-login-form <br>MemberId: $customerId<br> Password:$password", 'text/html');  
                   }); 
						  } */
		// print($request->plan);exit;
		$plan = $request->plan;
		//if($plan == '1'||$plan =='2')
			if($plan == '1')
	    {
			//echo $plan;exit;
		 $customerId = customer::insertGetId($postCustomber);
		   if($request->email !='')
						  {
							  /*
                          Mail::raw('Thank You for registration!', array('password' => $password), function($message)    use ($email)
                          {

                              $message->subject('Registration Successful!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						  */
						   
						 Mail::send([], [], function ($message )use ($email,$customerId,$password)  {
                        $message->to($email)
                              ->subject('Registration Successful!')
							  ->from('globalmedivac@gmail.com', 'Global Medevac')
                            /*->setBody("Thank you for your Global Medevac application.Your information is being processed now and your official Global Medevac Membership Welcome Kit will be mailed out soon.Once received, please remember to always keep your Global Medevac membership card with you.Welcome to the family!!<br>You may now login your account at:
										http://34.94.147.238:3000/member-login-form <br>MemberId: $customerId<br> Password:$password", 'text/html');*/ 
							->setBody("<b>Welcome to the Global Medevac Family!!</b><br><br>
								Thank you for joining our family! Global Medevac is the best, most comprehensive protection plan available anywhere!<br>Your new member package is being processed and should arrive to you by mail in 7-10 business days. If you should need any of our services before then, please contact us at 1-833-GET-MVAC (1-833-438-6822) or collect at 1-512-277-7560 and one of our transport coordinators will assist you with your needs.<br>Please provide your membership number which is listed below.<br>Be safe and remember, we've got you covered!! <br> <b>Member Number</b>:$customerId<br><br><b>Global Medevac Team</b>", 'text/html');
                   }); 
						  }
		 $agentCommisionDetail = DB::table('agents')
            ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
            ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
            ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			->where('agentwisePromotioncode.agentPromotionId','=' ,'')
            ->get();
      //echo "<pre>"; print_r($agentCommisionDetail);
      //die('aaaaa');
		  $agentCommisionDetail =   json_decode($agentCommisionDetail);

		  $agentCommisionPercent  = '';
		    //print_r( $agentCommisionPercent);exit;
        if($agentManagerPromotionId>0){
                  //echo 		$agentManagerPromotionId;exit;
			 	 $managerCommisionDetail = DB::table('agents')
				->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
        ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
				->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				->where('agentPromotion.agentPromotionId','=' ,$agentManagerPromotionId)
				->get();
		        $managerCommisionDetail =   json_decode($managerCommisionDetail);
		        //print_r( $managerCommisionDetail[0]->FirstYrComRate);exit;
				$managerCommisionPercent  = '';
        $eff_man_comm_percent ='';
				$planFee = $request->fees;
				$managerCommisionFee = '';
        //echo $managerCommisionFee;die('bbbb');
			 }else{
        $managerCommisionPercent=0;
				$eff_man_comm_percent=0;
				$managerCommisionFee=0;
				$managerId=0;
			 }


			if($agentStateManagerPromotionId>0){
          //echo 		$agentManagerPromotionId;exit;
			  $stateManagerCommisionDetail = DB::table('agents')
              ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
              ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
              ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			        ->where('agentPromotion.agentPromotionId','=' ,'')
              ->get();

			  $stateManagerCommisionDetail =   json_decode($stateManagerCommisionDetail);
		      //print_r( $stateManagerCommisionDetail[0]->FirstYrComRate);exit;
		    $stateManagerCommisionPercent  = '';
			  $eff_stateman_comm_percent ='';
        //echo  $eff_stateman_comm_percent;
			  $planFee = $request->fees;
			 // $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$planFee;
			 $stateManagerCommisionFee = '';
        $stateManagerId=$request->selectedStateManagerId;
        //echo $stateManagerCommisionFee;die('aaa');
			}else{
        $stateManagerCommisionPercent=0;
				$eff_stateman_comm_percent=0;
				$stateManagerCommisionFee=0;
				$stateManagerId=0;

			}

			if($plan == '1')
			{
				//echo"abhi";exit;
			  $planFee = $request->fees;
			  $time = strtotime(date("y-m-d"));
        $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
			  $chargeback = '';
			  $chargebackCommisionFee = '';
			  $chargebackCommisionFeeForManager = '';
			  $chargebackCommisionFeeForStateManager = '';

			//inserting
			$agentArray = [
                        'planId'=>$plan,
                        'customerId'=>  $customerId,
                        //'AgentId'=> $selectedAgentId,
                        'AgentId'=>$request->selectedAgentId['value'],
                        'agentPromotionId'=>$agentPromotionId,
                        'managerId'=>$agentManagerId,
                        'managerPromotionId'=>$agentManagerPromotionId,
                        'stateManagerId'=>$agentStateManagerId,
                        'stateManagerPromotionId'=>$agentStateManagerPromotionId,
                        'stateManagerCommission'=>$chargebackCommisionFeeForStateManager,
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=>'',
                        'chargeBackCommision'=>'',
                        'chargeBackInstalment'=>'',
                        'ChargeBackInterest'=>'',
                        'ChargeBackInterestForManager'=>'',
                        'ChargeBackInterestForStateManager'=>'',
                        'managerCommission'=>'',
                        'IsAdvance'=>'',
                        'PaymentMode'=>'',
                        'PaymentDate'=>'',
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$request->fees,
                        'newOrRenew'=>'NEW',
                        'paymentletterDate'=>$nextPaymentDate,
                        'recurringPaymentDate'=>$nextPaymentDate,
                        'override_fees'=>$overRideFee
			               ];
			   //print_r( $agentArray);exit;
			$One_Percent_of_chargebackCommisionFee='';
			$total_payable='';

			 //$agentPayment = agent::GetInsertId($agentArray);
			  DB::table('agentpayment')->insert(
                $agentArray
                );
			$paymentId = DB::getPdo()->lastInsertId();
			}
			else{
			  $planFee = $request->fees;
			  $time = strtotime(date("y-m-d"));
        $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
			  $agentCommisionFee = '';
			  //echo"$planFee";exit;
			$agentArray = [
                        'planId'=>$plan,
                        'customerId'=>  $customerId,
                        'AgentId'=>$request->selectedAgentId['value'],
                        'agentPromotionId'=>$agentPromotionId,
                        'managerId'=>$agentManagerId,
                        'managerPromotionId'=>$agentManagerPromotionId,
                        'stateManagerId'=>$agentStateManagerId,
                        'stateManagerPromotionId'=>$agentStateManagerPromotionId,
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=> '',
                        'stateManagerCommission'=>'',
                        'chargeBackCommision'=>null,
                        'chargeBackInstalment'=>null,
                        'managerCommission'=>'',
                        'IsAdvance'=>'NO',
                        'PaymentMode'=>'',
                        'PaymentDate'=>'',
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$request->fees,
                        'newOrRenew'=>'NEW',
                        'paymentletterDate'=>$nextPaymentDate,
                        'recurringPaymentDate'=>Carbon::now(),
                        'override_fees'=>$overRideFee
			                ];
              /*echo "<pre>";
              print_r($agentArray);
              die('aaaa');*/
			   DB::table('agentpayment')->insert($agentArray);
			 $paymentId = DB::getPdo()->lastInsertId();
			}
		}else{
			//for life-time and 5years
			$customerId = customer::insertGetId($postCustomber);
			  if($request->email !='')
						  {
							  /*
                          Mail::raw('Thank You for registration!', array('password' => $password), function($message)    use ($email)
                          {

                              $message->subject('Registration Successful!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						  */
						   
						 Mail::send([], [], function ($message )use ($email,$customerId,$password)  {
                        $message->to($email)
                              ->subject('Registration Successful!')
							  ->from('globalmedivac@gmail.com', 'Global Medevac')
                            /*->setBody("Hi, welcome user Thank You for registration.<br>You may now login your account at:
										http://35.235.80.37:3000/member-login-form <br>MemberId: $customerId<br> Password:$password", 'text/html');  */
										->setBody("<b>Welcome to the Global Medevac Family<b>!!<br><br>
										Thank you for joining our family! Global Medevac is the best, most comprehensive protection plan available anywhere!
										Your application is being processed and a Global Medevac representative will be contacting you regarding your payment. If you should need any of our services before then, please contact us at 1-833-GET-MVAC (1-833-438-6822) or collect at 1-512-277-7560 and one of our transport coordinators will assist you with your needs.
										<br>Be safe and remember, we've got you covered!!<br><br><b>Global Medevac Team</b>", 'text/html');
										
                   }); 
						  }
		    $agentCommisionDetail = DB::table('agents')
            ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
            ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
            ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			      ->where('agentPromotion.agentPromotionId','=' ,$agentPromotionId)
            ->get();

		  $agentCommisionDetail =   '';
		 // print_r( $agentCommisionDetail[0]->FirstYrComRate);exit;
		  $agentCommisionPercent  = '';
			//manager commission

			 if($agentManagerPromotionId){
			 	 $managerCommisionDetail = DB::table('agents')
				->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
        ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
				->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				->where('agentPromotion.agentPromotionId','=' ,$agentManagerPromotionId)
				->get();
		        $managerCommisionDetail =   '';
		        //print_r( $managerCommisionDetail[0]->FirstYrComRate);exit;
				$managerCommisionPercent  = '';
				$eff_man_comm_percent ='';
				$planFee = $request->fees;
				$managerCommisionFee = '';
			 }else{
				$eff_man_comm_percent=0;
				$managerCommisionFee=0;
				$managerId=0;
			 }
			if($agentStateManagerPromotionId){
			  $stateManagerCommisionDetail = DB::table('agents')
              ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
              ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
              ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			        ->where('agentPromotion.agentPromotionId','=' ,$agentStateManagerPromotionId)
              ->get();

			  $stateManagerCommisionDetail =   json_decode($stateManagerCommisionDetail);
		      //print_r( $stateManagerCommisionDetail[0]->FirstYrComRate);exit;
		      $stateManagerCommisionPercent  ='';
			  $eff_stateman_comm_percent ='';
			  $planFee = $request->fees;
			  $stateManagerCommisionFee = '';
			  $stateManagerId='';
			}else{
				$eff_stateman_comm_percent=0;
				$stateManagerCommisionFee=0;
				$stateManagerId=0;

			}

			$planFee = $request->fees;
			  $time = strtotime(date("y-m-d"));
              $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
			  $agentCommisionFee = '';
			$agentArray = [
			       'planId'=>$plan,
					'customerId'=>  $customerId,
					//'AgentId'=> $selectedAgentId,
          'AgentId'=>$request->selectedAgentId['value'],
          'agentPromotionId'=>$agentPromotionId,
          'managerId'=>$agentManagerId,
          'managerPromotionId'=>$agentManagerPromotionId,
          'stateManagerId'=>$agentStateManagerId,
          'stateManagerPromotionId'=>$agentStateManagerPromotionId,
				  'PercentOrDollar'=> 'dollar',
			    'Commission'=> '',
					'stateManagerCommission'=>'',
				  'chargeBackCommision'=>null,
				  'chargeBackInstalment'=>null,
				  'managerCommission'=>'',
					'IsAdvance'=>'NO',
					'PaymentMode'=>'',
					'PaymentDate'=>'',
					'ModDate'=>date("Y-m-d"),
					'feeAmount'=>$request->fees,
					'newOrRenew'=>'NEW',
					'paymentletterDate'=>$nextPaymentDate,
				    'recurringPaymentDate'=>Carbon::now(),
            'override_fees'=>$overRideFee
			];
			   DB::table('agentpayment')->insert(
                $agentArray
                );
			 $paymentId = DB::getPdo()->lastInsertId();


		   }

				DB::commit();

			  return response()->json([
              'status'=>'200',
			  'agentPaymentId'=>$paymentId
           ]);
			} catch (Exception $e) {
				DB::rollBack();
				return response()->json([
              'status'=>'202',
			  'message'=>'something went wrong'
           ]);
	}
 }
	/*member register end*/
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
	  /*customer details start*/
     public function customberDetail(Request $request)
     {
	    //print_r($request->all());exit;
      /*$customer = DB::table('customers')
  ->select('customers.*','agentpayment.paymentId as agent_payment_id','customers.created_at as cc_at', DB::raw('agentwisePromotioncode.agentName as agentFirstName'), DB::raw('CONCAT(groups.groupCode, " ", groups.groupId) AS groupCode') ,'plans.*')
  ->leftJoin('groups', 'customers.groupId', '=', 'groups.groupId')
  ->leftJoin('agentwisePromotioncode', 'customers.agentId', '=', 'agentwisePromotioncode.agentPromotionId')
  ->leftJoin('agentPromotion','agentPromotion.agentId','=','agentwisePromotioncode.agentId')
  ->leftJoin('agentpayment', 'customers.customerId', '=', 'agentpayment.customerId')
  ->Join('plans', 'customers.planId', '=', 'plans.planId')
        ->where('customers.customerId', $request->customerId)
        ->get();*/
    $customer = DB::select( DB::raw("select customers.*,customers.country as count,agentpayment.paymentId as agent_payment_id,customers.created_at as cc_at, CONCAT(agents.firstName,' ', agents.lastName) as agentLastName, groups.groupCode AS groupCode ,customers.groupId as grpid,plans.*,burialdetail.*,agents.agentId,agents.agentCode
from customers
left join groups ON customers.groupId=groups.groupId
left join agentpayment ON customers.customerId=agentpayment.customerId
LEFT JOIN agentPromotion ON agentPromotion.agentId=agentpayment.agentId
LEFT join agents ON agents.agentId=agentPromotion.agentId
LEFT join plans ON customers.planId = plans.planId
LEFT join burialdetail ON customers.customerId = burialdetail.memberId
where customers.customerId=$request->customerId
group BY agentPromotion.agentId ORDER by agentpayment.paymentId DESC
limit 0,1") );

           
			/* echo"<pre>";
			print_r($customer);exit; */
			$pay_status = DB::table('agentpayment')
			->select(DB::raw("date_format(PaymentDate,'%m-%d-%Y') AS PaymentDate"),'feeAmount','PaymentMode','naration')
			->where([['customerId',$request->customerId],['newOrRenew', 'NEW']])
			->get();
			//print_r($pay_status);exit;
			$manger_detail = DB::select( DB::raw("select CONCAT(agt.firstName,' ',agt.LastName) As managerName,CONCAT(Ag.firstName,' ',Ag.LastName) As State_managerName,PaymentMode,PaymentDate  from `agentpayment`
left join `agents` as agt on `agentpayment`.`managerId` = agt.`agentId`
left join `agents` as Ag on `agentpayment`.`stateManagerId` = Ag.`agentId`
where agentpayment.customerId=$request->customerId ") );
			//print_r($manger_detail);
			//exit;
		   $agents_details=DB::select( DB::raw("SELECT CONCAT(C.firstName,' ',C.LastName) As agentName,C.agentId
FROM `agents` as C ") );

		  $client_claim=DB::select( DB::raw("select claim_id,claim_reason,comments,(CASE
        WHEN (claim_status <> '0')
        THEN
            CASE
            WHEN (claim_status='1')
                THEN 'Declined'
                ELSE 'Approved'
            END
        ELSE
            'Pending'
        END
) as claim_status,DATE_FORMAT(claimed_at, '%m-%d-%Y') as claimed_at ,calim_doc
from client_claim where clientId=$request->customerId") );
           //print_r( $client_claim);exit;
      $plans=DB::select( DB::raw("SELECT planName,planId FROM `plans`") );
      $total_month_from_membership_date=DB::select( DB::raw("select TIMESTAMPDIFF(MONTH,customers.membershipDate,now()) AS total_month_difference FROM customers where customers.customerId=$request->customerId"));
      $membership_fees=DB::select( DB::raw("SELECT agentpayment.feeAmount from agentpayment where agentpayment.customerId =$request->customerId Limit 0,1"));
      $total_payment_details=DB::select( DB::raw("select count(paymentId) total_installment from agentpayment where customerId=$request->customerId"));
      $cardPrintData =  DB::select(DB::raw("SELECT customers.customerId,CONCAT(customers.firstName,' ',customers.LastName) AS customer_name,
      customers.email,customers.cellPhone,DATE_FORMAT(customers.DOB, '%m-%d-%Y') as DOB,customers.city,customers.state,customers.zip,plans.planName
      from customers
      LEFT JOIN plans ON plans.planId = customers.planId
      LEFT JOIN memeber_card ON memeber_card.memberId = customers.customerId
      where memeber_card.memberId =$request->customerId"));

		   //print stop
		  //print_r( $customer);exit;
			if($customer)
			  return response()->json([
        'status'=>'200',
			  'customer'=>$customer,
			  'payment_status' => $pay_status,
			  'agentlist'=>$agents_details,
			  'manager_name'=>$manger_detail,
			  'plans'=>$plans,
			  'calim_details'=>$client_claim,
        'total_payment_details'=>$total_payment_details,
        'membership_fees'=>$membership_fees,
        'total_month'=>$total_month_from_membership_date,
		    'cardPrintData'=>$cardPrintData
           ]);
     }
	 /*customer details end*/
	 /*geting memberdetails start*/
	 public function memberdetail(Request $request){
		 
		// print_r($request->all());exit;
		 $memberdetails = DB::TABLE('customers') 
				->select('customers.customerId',DB::raw('CONCAT(customers.firstName," ",customers.LastName) AS firstName'),'customers.clientType','customers.burialFee','customers.seminarFee','customers.membershipDate','agentpayment.PaymentMode','plans.fee','plans.planName')
				->leftjoin('agentpayment','agentpayment.customerId','=','customers.customerId')
				->leftjoin('plans','plans.planId','=','customers.planId')
				->where('customers.customerId',$request->memberId)
				->get();
		 if($memberdetails)
          {
              return response()->json([
                    'status'=>'200',
                    'memberdetails'=>$memberdetails
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'memberdetails'=>$memberdetails
              ]);
          }	
			
	 }
	 /*geting member details end*/
			/*fetch member start*/
	  public function fetchmember(Request $request){
		  //print_r($request->all());exit;
		$fetchmember = DB::TABLE('customers') 
				 ->select('customers.customerId',DB::raw('CONCAT(customers.firstName," ",customers.LastName) AS firstName'),'customers.DOB','customers.cellPhone','customers.mobile2','customers.email',DB::raw('CONCAT(customers.spouseFirstName," ",customers.spouseLastName) AS spouseFirstName'),'customers.spouseDOB','customers.clientType','plans.planName','plans.fee','customers.initiationFee','customers.companyName','groups.groupCode',DB::raw('CONCAT(agents.firstName," ",agents.lastName) AS agentfirstname'),DB::raw('CONCAT(customers.dependent1FirstName," ",customers.dependent1LastName) AS dependent1FirstName'),DB::raw('CONCAT(customers.address1," ",customers.address2," ",customers.city," ",customers.country," ",customers.zip) AS homeAddress'), DB::raw('CONCAT(customers.mailing_address1," ",customers.mailing_address2," ",customers.city1," ",customers.state1," ",customers.zip1) AS mailing_address1'))
				->leftJoin('agentpayment','agentpayment.customerId','=','customers.customerId')
				->leftJoin('plans','plans.planId','=','customers.planId')
				->leftJoin('groups','groups.groupId','=','customers.groupId')
				->leftJoin('agents','agents.agentId','=','customers.agentId')
				->where('customers.customerId',$request->memberId)
				->get(); 
				
		 if($fetchmember)
          {
              return response()->json([
                    'status'=>'200',
                    'fetchmember'=>$fetchmember
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'fetchmember'=>$fetchmember
              ]);
          }	
	  }
	  /*fetch member end*/
      /*get plan start*/
	   public function getPlan(Request $request){
		   /* echo "<pre>";
		   print_r($request->all());
		   exit; */
		  $planDetails = DB::table('plans')
           ->select('plans.planId', 'planName')
           ->get();
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
		/*get plan end*/
		/*payment customer start*/
	public function customerpayment(Request $request){
     /*$today =strtotime(date("Y-m-d"));
     if($request->paymentmode===12){
       $date = date('Y-m-d', strtotime('+1 month', $today));
       $monthcounter=1;
      }else{
       $date = date('Y-m-d', strtotime('+12 month', $today));
       $monthcounter=12;
      }
	   $postArray = [
			  'PaymentMode' => $request->paymentmethod,
			  'naration'=>$request->narration,
			  'PaymentDate'=>date('Y-m-d '),
			  'ModDate'=>date('Y-m-d '),
        'recurringPaymentDate'=>$date,
        'MonthCounter'=>$monthcounter
        ];
      $agentPaimentDone =DB::table('agentpayment')->where('paymentId',$request->agentPaymentId)->update($postArray);
		 if($agentPaimentDone)
		   {

			  $customer_id = DB::table('agentpayment')->select('agentpayment.customerId as customer_id')
				->where('agentpayment.paymentId', $request->agentPaymentId)
				->get();
			  $postArray = [
			  'isPaidCustomer' =>'1',
			  'paymentmode'=>$request->paymentmode,
			 ];
			 $clientPaimentDone =DB::table('customers')->where('customerId',$customer_id[0]->customer_id)->update($postArray);
			  return response()->json([
              'status'=>'200',
			  'msg'=>'success'
			  ]);
		   }
		   else
		   {
			  return response()->json([
              'status'=>'202',
			  'msg'=>'Error'
			 ]);
     }*/
    /*echo "<pre>";
    print_r($request->all());
    die('aaaa');*/
     $today =strtotime(date("Y-m-d"));
     if($request->paymentmode == '12'){
       $date = date('Y-m-d', strtotime('+1 month', $today));
       $monthcounter=1;
      }else{
       $date = date('Y-m-d', strtotime('+12 month', $today));
       $monthcounter=12;
      }
	   $postArray = [
			  'PaymentMode' => $request->paymentmethod,
			  'naration'=>$request->narration,
			  'PaymentDate'=>date('Y-m-d '),
			  'ModDate'=>date('Y-m-d '),
			  'payeeName'=>$request->payeeName,
			  'orderNumber'=>$request->checkNumber,
			  'checkMoneyDate'=>$request->checkDate,
        'recurringPaymentDate'=>$date,
        'MonthCounter'=>$monthcounter,
		'totalBurialFee'=>$request->totalBurialFee
        ];
      $agentPaimentDone =DB::table('agentpayment')->where('paymentId',$request->agentPaymentId)->update($postArray);
		 if($agentPaimentDone)
		   {

			  $customer_id = DB::table('agentpayment')->select('agentpayment.customerId as customer_id')
				->where('agentpayment.paymentId', $request->agentPaymentId)
				->get();
			  $postArray = [
			  'isPaidCustomer' =>'1',
			  'paymentmode'=>$request->paymentmode,
			 ];
			 $clientPaimentDone =DB::table('customers')->where('customerId',$customer_id[0]->customer_id)->update($postArray);
			  return response()->json([
              'status'=>'200',
			  'msg'=>'success'
			  ]);
		   }
		   else
		   {
			  return response()->json([
              'status'=>'202',
			  'msg'=>'Error'
			 ]);
		   }


	}
		/*payment customer end*/
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
		/*get plan details start*/
	   public function getplanDetail(Request $request)
       {

		  //print($request->currentPlanId);
		 // print($request->clientType);exit;
     if($request->clientType == 'Family')
     {
          $plan_detail = DB::table('plans')
           ->select('frequency','familyFee','initiatonFee','burial_family as burialfee','seminar_family as seminar_fee' )
     ->where('planId', $request->currentPlanId)
           ->get();
     }
     else{
      $plan_detail = DB::table('plans')
       ->select('frequency','fee','initiatonFee','burial_individual as burialfee','seminar_individual as seminar_fee')
       ->where('planId', $request->currentPlanId)
               ->get();
     }

			if($plan_detail)
			  return response()->json([
              'status'=>'200',
			  'plan_detail'=>$plan_detail
           ]);
     }
	/*get plan details end*/
		/*update customer start*/
	  public function updatecustomer(Request $request){
		     /*   echo "<pre>";
		  print_r($request->all());
		  exit;   */  
		$groupId='';     
		if($request->groupId['value'] !=''){
			$groupId=$request->groupId['value'];
		 } 
		 else{
			 $groupId=$request->groupId;
		 }
	      // echo $groupId;exit; 
		  if($request->agentIdToChange){
			  $agentIdToChange = $request->agentIdToChange['value'];
		  }
		  else {
			  $agentIdToChange = '';
		  }
		      if($agentIdToChange){
				  $newAgentId = $agentIdToChange;
			  }
			  else {
				  $newAgentId = $request->agentId;
			  }
			 
			  	$customberPaymentDetail  = DB::table('agentpayment')->select('paymentId','planId','customerId','PaymentMode','AgentId','MonthCounter','agentPromotionId','PaymentDate','ModDate','feeAmount','newOrRenew','paymentletterDate','recurringPaymentDate','totalBurialFee','isPaidAgent','override_fees','IsAdvance','chargeBackInstalment','MonthCounter')->where('customerId',$request->customerId)->orderBy('paymentId', 'DESC')->offset(0)->limit(1)->get();
				
			    
				
				
				
	        $postArray = [
              'firstName' =>$request->firstName,
			  'middleName' =>$request->middleName,
              'lastName'=>$request->lastName,
			  'DOB'=>$request->customer_dob,
			  'address1'=>$request->address1,
			  'address2'=>$request->address2,
			  'mailing_address1'=>$request->mailing_address1,
			  'mailing_address2'=>$request->mailing_address2,
			  'city'=>$request->city,

			  'cellPhone'  =>$request->phone,
			  'mobile2'=>$request->phone1,
			  'country'=>$request->country,
			  'country1'=>$request->country1,
			  'companyName'=>$request->companyName,
			  'zip'=>$request->zip2,
			  'zip1'=>$request->zip1,
			  'state1'=>$request->state1,
			  'dependent1FirstName'=>$request->dependent1FirstName,
			  'dependent2FirstName'=>$request->dependent2FirstName,
			  'dependent3FirstName'=>$request->dependent3FirstName,
			  'dependent1MiddleName'=>$request->dependent1MiddleName,
			  'dependent2MiddleName'=>$request->dependent2MiddleName,
			  'dependent3MiddleName'=>$request->dependent3MiddleName,
			  'dependent1LastName'=>$request->dependent1LastName,
			  'dependent2LastName'=>$request->dependent2LastName,
			  'dependent3LastName'=>$request->dependent3LastName,
			  'dependent1DOB'=>$request->dependent1DOB,
			  'dependent2DOB'=>$request->dependent2DOB,
			  'dependent3DOB'=>$request->dependent3DOB,
			  'spouseFirstName'=>$request->spouseFirstName,
			  'spouseMiddleName'=>$request->spouseMiddleName,
			  'spouseDOB'=>$request->familyDateOfBirth,
              'note'=>$request->note,
			  'agentId'=>$newAgentId,
			  'groupId'=>$groupId,
			  'created_at'=>$request->created_at
			 ];
			//echo "<pre>";
			 //print_r($postArray);exit;
	
		   //print_r($customberPaymentDetail);exit;
		   
		  if($customberPaymentDetail[0]->AgentId <> $newAgentId ){
			 // echo "hi";
			 // echo $customberPaymentDetail[0]->AgentId;echo "<br>";
		   //echo $newAgentId;
		  // exit;
		   if($customberPaymentDetail[0]->isPaidAgent == 0 ){
			          //promotion id and detail of agent manager and state manager
					  $commission_detail = array ();
					$commission_detail =    $this->commission_calculation($customberPaymentDetail[0]->planId,$newAgentId,$customberPaymentDetail[0]->feeAmount);
					//print_r ($commission_detail);exit;
					
                     
					$reInsertAgentPayment = [
                        'planId'=>$customberPaymentDetail[0]->planId,
                        'customerId'=> $request->customerId,
                        'AgentId'=>$newAgentId,
                        'agentPromotionId'=>$commission_detail['agentPormotionId'],
                        'managerId'=>$commission_detail['managerPormotionId'],
                        'managerPromotionId'=>$commission_detail['managerPormotionId'],
                        'stateManagerId'=>$commission_detail['stateManagerPormotionId'],
                        'stateManagerPromotionId'=>$commission_detail['stateManagerPormotionId'],
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=>$commission_detail['CommisionFee'],
						'MonthCounter'=>$customberPaymentDetail[0]->MonthCounter,
			            'ChargeBackInterest'=>$commission_detail['ChargeBackInterest'],
                        'stateManagerCommission'=>$commission_detail['stateManagerCommisionFee'] ,
                        'chargeBackCommision'=>$commission_detail['chargeBackCommision'],
                        'chargeBackInstalment'=>$commission_detail['chargeBackInstalment'],
                        'managerCommission'=>$commission_detail['CommisionFeeForManager'],
                        'IsAdvance'=>$customberPaymentDetail[0]->IsAdvance,
                        'PaymentMode'=>$customberPaymentDetail[0]->PaymentMode,
                        'PaymentDate'=>$customberPaymentDetail[0]->PaymentDate,
                        'ModDate'=>date("Y-m-d"),
						'isPaidAgent'=>'0',
						'isPaidManager'=>'0',
						'isPaidStateManager'=>'0',
                        'feeAmount'=>$customberPaymentDetail[0]->feeAmount,
                        'newOrRenew'=>$customberPaymentDetail[0]->newOrRenew,
                        'paymentletterDate'=>$customberPaymentDetail[0]->paymentletterDate,
                        'recurringPaymentDate'=>$customberPaymentDetail[0]->recurringPaymentDate,
                        'override_fees'=>$customberPaymentDetail[0]->override_fees
			                ];
						//	echo "1";
							//print_r($reInsertAgentPayment);exit;
		    DB::table('agentpayment')->insert(
             $reInsertAgentPayment
                   );
			DB::table('agentpayment')->where('AgentId', '=', $customberPaymentDetail[0]->AgentId)->where('customerId', '=', $request->customerId)->delete();
		}
		else{
			
			
			     $sql12 = "SELECT agent_chargeBack_commision from agent_commission_details
                    Where customerId=".$request->customerId;
			    //echo $sql12;exit;
	            $agent_commision = DB::select( DB::raw("SELECT agent_chargeBack_commision from agent_paid_commission_details
                    Where customerId=".$request->customerId) );
					if(@$agent_commision[0]!=''){
						// print_r($agent_commision[0]->agent_chargeBack_commision);exit;
						 $agent_commision = $agent_commision[0]->agent_chargeBack_commision;
					}
					else {
						$agent_commision ='';
					}
					
			    // echo "sdsdfdsf";exit;
			     //$pormotion_detail = $this->fetch_promotion_id($newAgentId);
			     $pormotion_detail = $this->fetch_promotion_id($customberPaymentDetail[0]->AgentId);
					//print_r($pormotion_detail);exit;
				     //print_r ($customberPaymentDetail);exit;
				if($customberPaymentDetail[0]->planId ==1 ){
					 $MonthCounter = $customberPaymentDetail[0]->MonthCounter;
					 //echo $MonthCounter; exit;
					 if(is_numeric($MonthCounter) AND  ($MonthCounter < 13)){
						// echo $MonthCounter ; exit;
						 if($MonthCounter<12)
						 {
						
					
						$overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverrideCommisionAnual')->where('agentPromotionId', $pormotion_detail['agentPormotionId'])->get());
						
							
						   if($overrideCommAnual[0]->OverrideCommisionAnual){
							   $FirstYearCommission = $overrideCommAnual[0]->OverrideCommisionAnual;
						   }
						   else {
							    $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName',$pormotion_detail['agent_level'])->get());
								$FirstYearCommission= $agtFstCommRat[0]->FirstYrComRate; 
						   }
						   //echo $pormotion_detail['agent_level'];
						   //print_r($FirstYearCommission);exit;
						   if(is_numeric($FirstYearCommission)  AND $FirstYearCommission>0){
							  
							/*  $chargeback = (((($FirstYearCommission/100)*(6-$MonthCounter))*$this->isNumericCheck($customberPaymentDetail[0]->feeAmount))*.01*6)+((($FirstYearCommission/100)*(6-$MonthCounter)*$this->isNumericCheck($customberPaymentDetail[0]->feeAmount)));
							  */
							  
							  //+ 
							  $one_persent = ((($this->isNumericCheck($customberPaymentDetail[0]->feeAmount)*($FirstYearCommission/100))*6)*.01*6);
							//  echo 
							  /*$chargeback =$this->isNumericCheck($customberPaymentDetail[0]->feeAmount)*($FirstYearCommission/100)*(6-$MonthCounter) ;*/
			                 $chargeback =$customberPaymentDetail[0]->feeAmount*($FirstYearCommission/100)*(6-$MonthCounter);
							  //echo $FirstYearCommission."<br/>Satyam";
							  $charge_back_int = $one_persent;
							  
							  
							  $charge_back_amount = ($this->isNumericCheck($customberPaymentDetail[0]->feeAmount)*($FirstYearCommission/100))*(6-$MonthCounter) +  $one_persent;
							  if($agent_commision >  $chargeback ){
							  $comission = 0;
							  }
							  else {
								   $comission = 0;
							  }
							  
							  /* echo "$MonthCounter"; echo"</br>";
							  echo "$chargeback"; echo"</br>";
							  echo "$one_persent"; echo"</br>";
							  echo "$charge_back_amount"; echo"</br>";
							 exit; */
							  
						   }
						   
					 }
					 else {
						  $chargeback = 0 ;
					 }
					// echo $chargeback;exit;
					 
				   }
				   $payableCommisssion = [
				   
				        'agentId'=>$customberPaymentDetail[0]->AgentId,
                        'agentPromotionId'=> $customberPaymentDetail[0]->agentPromotionId,
                        'agentChargebackAmount'=>$charge_back_amount,
                        'chageBackIntrest'=> $charge_back_int,
						'agent_cherge_back' => $chargeback,
                        'customerId'=>$request->customerId,
                        'cancelDate'=>date("Y-m-d")
				   ];
				    
				// print_r( $payableCommisssion);exit;
				     //print_r($payableCommisssion);exit;
				     DB::table('agentpayable')->insert(
            $payableCommisssion
                   );
				}
				 /*$charge_back_calculation = 
				  DB::table('agentpayable')->insert(
             $reInsertAgentPayment
                   );*/
				   
			$reInsertAgentPayment = [
                        'planId'=>$customberPaymentDetail[0]->planId,
                        'customerId'=> $request->customerId,
                        'AgentId'=>$newAgentId,
                        'agentPromotionId'=>$pormotion_detail['agentPormotionId'],
                        'managerId'=>$pormotion_detail['managerPormotionId'],
                        'managerPromotionId'=>$pormotion_detail['managerPormotionId'],
                        'stateManagerId'=>$pormotion_detail['stateManagerPormotionId'],
                        'stateManagerPromotionId'=>$pormotion_detail['stateManagerPormotionId'],
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=> $comission,
						'MonthCounter'=>$customberPaymentDetail[0]->MonthCounter,
                        'stateManagerCommission'=>'',
                        'chargeBackCommision'=>$comission,
                        'chargeBackInstalment'=>$customberPaymentDetail[0]->chargeBackInstalment,
                        'managerCommission'=>'',
                        'IsAdvance'=>'NO',
                        'PaymentMode'=>$customberPaymentDetail[0]->PaymentMode,
                        'PaymentDate'=>$customberPaymentDetail[0]->PaymentDate,
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$customberPaymentDetail[0]->feeAmount,
                        'newOrRenew'=>$customberPaymentDetail[0]->newOrRenew,
						'isPaidAgent'=>'0',
						'isPaidManager'=>'0',
						'isPaidStateManager'=>'0',
                        'paymentletterDate'=>$customberPaymentDetail[0]->paymentletterDate,
                        'recurringPaymentDate'=>$customberPaymentDetail[0]->recurringPaymentDate,
                        'override_fees'=>$customberPaymentDetail[0]->override_fees,
						'is_agent_change'=>'0'
			                ];
							//echo "0";
					//print_r($reInsertAgentPayment);exit;
		    DB::table('agentpayment')->insert(
             $reInsertAgentPayment
                   );
	        	
		//echo $customberPaymentDetail[0]->isPaidAgent;exit;
		//print_r($customberPaymentDetail[0]->planId);exit;
		/*
		$reInsertAgentPayment = [
                        'planId'=>$customberPaymentDetail[0]->planId,
                        'customerId'=>  $request->customerId,
                        'AgentId'=>$[pormotion_detail][],
                        'agentPromotionId'=>$[pormotion_detail][],
                        'managerId'=>$[pormotion_detail][],
                        'managerPromotionId'=>$[pormotion_detail][],
                        'stateManagerId'=>$[pormotion_detail][],
                        'stateManagerPromotionId'=>$[pormotion_detail][],
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=> 0,
                        'stateManagerCommission'=>0,
                        'chargeBackCommision'=>'0',
                        'chargeBackInstalment'=>'0',
                        'managerCommission'=>0,
                        'IsAdvance'=>'',
                        'PaymentMode'=>$customberPaymentDetail[0]->PaymentMode,
                        'PaymentDate'=>$customberPaymentDetail[0]->PaymentMode,
                        'ModDate'=>$customberPaymentDetail[0]->ModDate,
                        'feeAmount'=>$customberPaymentDetail[0]->feeAmount,
                        'newOrRenew'=>$customberPaymentDetail[0]->newOrRenew,
                        'paymentletterDate'=>$customberPaymentDetail[0]->paymentletterDate,
                        'recurringPaymentDate'=>$customberPaymentDetail[0]->recurringPaymentDate,
                        'override_fees'=>$customberPaymentDetail[0]->override_fees
			                ];
							*/
							//exit('testing');
	  }
}
           // DB::enableQueryLog();
		  //print_r($postArray);
		  //die('aaa');
       $agentPaimentDone = DB::table('customers')->where('customerId',$request->customerId)->update($postArray);
	   //print_r( $agentPaimentDone);exit;
	   if($agentPaimentDone){
		  
       return response()->json([
             'status'=>'200',
          ]);
     }else{
       return response()->json([
             'status'=>'203',
          ]);
     }

 }
		/*update customer end*/
		/*customer membership start*/
 public function customermembershippayment($id){
       /*echo "<pre>";
	   print_r($id);
	   die('aaaa');*/
	  /*$plans_fee=DB::select( DB::raw("SELECT c.customerId,c.firstName,c.LastName,c.email, AP.feeAmount, FBF.planName,FBF.frequency, FBF.initiatonFee,sum(AP.feeAmount+FBF.initiatonFee) as initiationFee_with_total_amount,(AP.feeAmount) as initiationFee_with_out_total_amount
FROM `agentpayment`As AP
left join plans AS FBF ON AP.planId=FBF.planId
left join customers AS c ON c.customerId=AP.customerId
Where AP.paymentId=".$id) );
		if($plans_fee)
			  return response()->json([
              'status'=>'200',
			  'total_amount'=>$plans_fee
      ]);*/
      $plans_fee=DB::select( DB::raw("SELECT c.customerId,c.firstName,c.middleName,c.LastName,c.email,c.burialFee,c.DOB,c.spouseDOB,
	  c.dependent1DOB,c.dependent2DOB,c.dependent3DOB,
	   c.seminarFee,c.initiationFee as modifyIniFee,
  	  DATE_FORMAT(c.created_at, '%m-%d-%Y') as membershipDate,c.clientType,AP.feeAmount, FBF.planName,
  	  FBF.frequency, FBF.initiatonFee,sum(AP.feeAmount+FBF.initiatonFee) as initiationFee_with_total_amount,
  	  (AP.feeAmount) as initiationFee_with_out_total_amount
  FROM `agentpayment`As AP
  left join plans AS FBF ON AP.planId=FBF.planId
  left join customers AS c ON c.customerId=AP.customerId
  Where AP.paymentId=".$id) );
  		if($plans_fee)
  			  return response()->json([
                'status'=>'200',
  			  'total_amount'=>$plans_fee
             ]);
 }
	/*customer membership end */
	/*get membership plan start*/
	 public function getmembershipPlan(){
		// $group_code_all=array();
      
	  $plans_fee=DB::select( DB::raw("select plans.planId,plans.planName,plans.country,plans.fee,plans.initiatonFee,plans.familyFee,plans.status from plans 
	  ;
	  ") );
	  
	  /* $plans_fee=DB::select( DB::raw("select plans.planId,plans.planName,plans.country,plans.fee,plans.initiatonFee,plans.familyFee,plans.groupId from plans 
	  ") );
	       
		   $count_gr_size = count($plans_fee);
		   for($i = 0; $i< $count_gr_size;$i++){
              $group = $plans_fee[$i]->groupId;
			  $grop_explode = explode(',',$group);
			  //print_r($grop_explode);exit;
			
			  for($j =0; $j<count($grop_explode);$j++){
				    $group_code_id = $grop_explode[$j];
					//echo $group_code_id; exit;
				  $groupcode  = DB::table('groups')->select('groupCode')->where('groupId',$group_code_id )->get();
					if(@$groupcode[0])
					{
						$group_code_all[$i][$j]=$groupcode[0]->groupCode;
					}
			  }
		   }*/
              //echo "<pre>";
			  //print_r($group_code_all);exit;
			if($plans_fee)
				  return response()->json([
				  'status'=>'200',
				  'planDetails'=>$plans_fee,
				 // 'groupCode'=>$group_code_all,
			   ]);
	 }
	 /* get membership plan end */
	 /*group code details for member start*/
    public function groupCodeDetailsForMember(Request $request){
    //print_r($request->all());exit;
    /* $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId Where groups.status='Active'"));*/
	
$group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId Where groups.status='Active'"));

    $total_groups=DB::select( DB::raw("select count(groupId) total_groups from groups"));
  $total_active_groups=DB::select( DB::raw("select count(groupId) total_active_groups from groups where status='Active'"));
  $total_inactive_groups=DB::select( DB::raw("select count(groupId) total_inactive_groups from groups where status='Inactive'"));
    //echo "<pre>";
  //  print_r($group_code);
  //  exit;
           if($group_code)
          return response()->json([
          'status'=>'200',
          'groupDetails'=>$group_code,
          'total_groups'=>$total_groups,
          'total_active_groups'=>$total_active_groups,
          'total_inactive_groups'=>$total_inactive_groups,
         ]);
  } 	
	/*group code details for member end*/
	/*grou code details start*/
	public function groupCodeDetails(Request $request){
		//print_r($request->all());exit;
		
		   if($request['status'] == ''){
			  $where = 'Where 1=1';
		  }
		   if($request['status'] == 'Active'){
			  $where = "Where groups.status='Active'";
		  }
		   if($request['status'] == 'Inactive'){
			   $where = "Where groups.status='Inactive'";
		  } 
	   $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups 
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId $where"));
     
    $total_groups=DB::select( DB::raw("select count(groupId) total_groups from groups"));
	$total_active_groups=DB::select( DB::raw("select count(groupId) total_active_groups from groups where status='Active'"));
	$total_inactive_groups=DB::select( DB::raw("select count(groupId) total_inactive_groups from groups where status='Inactive'"));
		/* echo "<pre>";
		print_r($total_inactive_groups);
		exit; */
	         if($group_code)
				  return response()->json([
				  'status'=>'200',
				  'groupDetails'=>$group_code,
				  'total_groups'=>$total_groups,
				  'total_active_groups'=>$total_active_groups,
				  'total_inactive_groups'=>$total_inactive_groups,
			   ]);
	}
	/*group code details end*/
	/*register from front end start*/
 public function frontendregister(Request $request){
	        // $agent
	        /* print_r($request->all());
		   die('aaaa');  */
		  // echo $request->agentNumber;exit;
		   if($request->agentNumber!=''){
			$get_agentId = DB::table('agents')->select('agentId')->where('agentCode',$request->agentNumber)->get();   
			   
			  // print_r($get_agentId);exit;
			   $agentId = $get_agentId[0]->agentId;
			   //echo $agentId;exit;
		   }
		   else {
			     $agentId = 1041;
			   //print($agentId);exit;
		   }
         //echo $request->dependantDoB2;exit;
		 if($request->dependantDoB2!=''){
		 $dependantDoB2=date("Y-m-d", strtotime($request->dependantDoB2));
		 }else{
			 $dependantDoB2='';
		 }
		  if($request->dependantDoB1!=''){
		 $dependantDoB1=date("Y-m-d", strtotime($request->dependantDoB1));
		 }else{
			 $dependantDoB1='';
		 }
		  if($request->spouseDoB!=''){
		 $spouseDoB=date("Y-m-d", strtotime($request->spouseDoB));
		 }else{
			 $spouseDoB='';
		 }
		  //echo "$dependantDoB1";
		 // echo "$dependantDoB2";
		 // echo "$spouseDoB";exit;
		 // $agentid
	   $frontendCustomerArray = [
                'firstName'      => $request->first_name,
				'middleName'      => $request->middle_name,
                'LastName' => $request->last_name,
                'DOB'=>date("Y-m-d", strtotime($request->customerRegisterFormDob)),
                'InitiationFees'=>$request->InitiationFees,
                'city'      => $request->city,
                //'city1'      => $request->city1,
				'city1'      => $request->city,
                'state'      => $request->state,
                //'state1'      => $request->state_s1,
				'state1'      => $request->state,
                'zip'  =>$request->zip,
				'zip1'  =>$request->zip,
                'isBurial' => $request->colorRadio,
                'planId' => $request->planDetail,
                //'groupId'=>$request->group_code,
                'isActive'=>'No',
				'agentId' =>$agentId,
				'groupId' =>1002,
                'password'      => $request->set_your_password,
                'email' =>$request->email_address,
                'companyName'      => $request->company_name,
                
                'country'  =>$request->country,
                //'country1'  =>$request->country1,
				'country1'  =>$request->country,
                'cellPhone'  =>$request->primary_phone_number,
                'clientType'  =>$request->colorRadio1,
                'mobile2'  =>$request->alternate_phone_number,
                'agent_first_name' =>$request->agent_first_name,
				'agent_middle_name'=>$request->agent_middle_name,
                'agent_last_name' =>$request->agent_last_name,
                'agentNumber' =>$request->agentNumber,
                'address1'  => $request->streetaddress,
				'address2'  => $request->streetaddress,
				'mailing_address1' => $request->streetaddress,
				'mailing_address2' => $request->streetaddress,
                'dependent1FirstName' =>$request->dependant1_first_name,
				'dependent1MiddleName' =>$request->dependant1_middle_name,
                'dependent1LastName' =>$request->dependant1_last_name,
                'dependent1DOB' =>$dependantDoB1,
                'dependent2FirstName' =>$request->dependant2_first_name,
				'dependent2MiddleName' =>$request->dependant2_middle_name,
                'dependent2LastName' =>$request->dependant2_last_name,
                'dependent2DOB' =>$dependantDoB2,
                'spouseFirstName'  =>$request->spouseFirstName,
				'spouseMiddleName' =>$request->spouseMiddleName,
                'spouseLastName'  =>$request->spouseLastName,
                'spouseDOB' =>$spouseDoB,
                'price'=>($request->InitiationFees+$request->membership_fees),
                'created_at' =>Carbon::now(),
                'membershipDate' =>date("y-m-d"),
                'modDate' =>date("y-m-d"),
                'modBy' =>'customer',
                'burialCity'=>$request->burialCity,
                'burialState'=>$request->burialState,
                'isAutoRenew' => $request->auto_renew
         ];
		  /*   echo "<pre>";
		 print_r($frontendCustomerArray);
		 exit;    */
		$frontendCustomer_id = DB::table('frontend_customer_temp')->insertGetId($frontendCustomerArray);

$customer_fee=DB::select( DB::raw("SELECT (CASE
When frontend_customer_temp.clientType='Individual' Then plans.fee
ELSE plans.familyFee
END) AS fee, frontend_customer_temp.InitiationFees,customerId
from frontend_customer_temp
INNER join plans ON frontend_customer_temp.planId=plans.planId where frontend_customer_temp.customerId =  $frontendCustomer_id"));
 $frontendCustomerPriceArray = [
      'price'=>$customer_fee[0]->fee
     ];

$customerInstallmentPayment=DB::table('frontend_customer_temp')->where('customerId',$customer_fee[0]->customerId)->update($frontendCustomerPriceArray);

		$customberDetail=DB::select( DB::raw("select frontend_customer_temp.customerId,frontend_customer_temp.isBurial, frontend_customer_temp.price,
(CASE
  WHEN frontend_customer_temp.DOB<>'0000-00-00' THEN date_format(frontend_customer_temp.DOB,'%m-%d-%Y')
  ELSE NULL
  END) as customer_dob, frontend_customer_temp.cellPhone,
CONCAT(frontend_customer_temp.address1,', ',frontend_customer_temp.city,', ',frontend_customer_temp.state,', ',frontend_customer_temp.country,',',frontend_customer_temp.zip) as address1,
frontend_customer_temp.country,(case
when (frontend_customer_temp.spouseMiddleName is not null)  then
(CONCAT(frontend_customer_temp.spouseFirstName,' ', frontend_customer_temp.spouseMiddleName,' ', frontend_customer_temp.spouseLastName))  
else
(CONCAT(frontend_customer_temp.spouseLastName,' ' ,frontend_customer_temp.spouseLastName)) 
end 
) as spousename,
(CASE
  WHEN frontend_customer_temp.spouseDOB<>'0000-00-00' THEN date_format(frontend_customer_temp.spouseDOB,'%m-%d-%Y')
  ELSE NULL
  END) as spouseDOB,
 (case
when (frontend_customer_temp.dependent1MiddleName is not null)  then
(CONCAT(frontend_customer_temp.dependent1FirstName,' ', frontend_customer_temp.dependent1MiddleName,' ', frontend_customer_temp.dependent1LastName))  
else
(CONCAT(frontend_customer_temp.dependent1FirstName,' ' ,frontend_customer_temp.dependent1LastName)) 
end 
) as dependent1_name,
 (CASE
  WHEN frontend_customer_temp.dependent1DOB<>'0000-00-00' THEN date_format(frontend_customer_temp.dependent1DOB,'%m-%d-%Y')
  ELSE NULL
  END) as dependent1DOB,
(case
when (frontend_customer_temp.dependent2MiddleName is not null)  then
(CONCAT(frontend_customer_temp.dependent2FirstName,' ', frontend_customer_temp.dependent2MiddleName,' ', frontend_customer_temp.dependent2LastName))  
else
(CONCAT(frontend_customer_temp.dependent2FirstName,' ' ,frontend_customer_temp.dependent2LastName)) 
end 
) as dependent2_name,
(CASE
  WHEN frontend_customer_temp.dependent2DOB<>'0000-00-00' THEN date_format(frontend_customer_temp.dependent2DOB,'%m-%d-%Y')
  ELSE NULL
  END) as dependent2DOB,
  frontend_customer_temp.agentNumber,(case
when (frontend_customer_temp.agent_middle_name is not null)  then
(CONCAT(frontend_customer_temp.agent_first_name,' ', frontend_customer_temp.agent_middle_name,' ', frontend_customer_temp.agent_last_name))  
else
(CONCAT(frontend_customer_temp.agent_first_name,' ' ,frontend_customer_temp.agent_last_name)) 
end 
)as agent_name, frontend_customer_temp.InitiationFees, frontend_customer_temp.agentNumber,	
frontend_customer_temp.firstName,frontend_customer_temp.middleName, frontend_customer_temp.LastName,
(case
when (frontend_customer_temp.middleName is not null)  then
(CONCAT(frontend_customer_temp.firstname,' ', frontend_customer_temp.middleName,' ', frontend_customer_temp.LastName))  
else
(CONCAT(frontend_customer_temp.firstName,' ' ,frontend_customer_temp.LastName)) 
end 
)as client_name, frontend_customer_temp.email, plans.planName,(CASE When clientType='Individual' Then plans.burial_individual else plans.burial_family END) AS burial_fee
from frontend_customer_temp
left join plans on frontend_customer_temp.planId=plans.planId
where frontend_customer_temp.customerId=".$frontendCustomer_id) );
$planDetail=DB::select( DB::raw("select frontend_customer_temp.clientType,frontend_customer_temp.planId
from frontend_customer_temp
where frontend_customer_temp.customerId=".$frontendCustomer_id) );
         //echo "<pre>";
		 //print_r($customberDetail);exit;
		 $isBurial = $customberDetail[0]->isBurial;

	    if($customberDetail)
			  return response()->json([
              'status'=>'200',
			  'customer_details'=>$customberDetail,
			   'planDetail'=>$planDetail,

           ]);


  }
	/*register from front end */
	/*burial from frontend start */
  public function frontendburial(Request $request){
	    // echo "<pre>";
		// print_r($request->all());
		// die('aaaa');
			 $burialDetail=DB::select( DB::raw("select
(CASE When clientType=$request->plan_type Then plans.burial_individual else plans.burial_family END) AS burial_fee
from frontend_customer_temp
left join plans on frontend_customer_temp.planId=plans.planId
where frontend_customer_temp.customerId=".$frontendCustomer_id) );
		if($burialDetail)
			  return response()->json([
              'status'=>'200',
			  'customer_details'=>$customberDetail
           ]);

  }
	/*burial from frontend end*/
	/*client claim submit start*/
  public function clientClaimSubmit(Request $request){

	       $target_path = public_path("upload/").$_FILES['image']['name'];

		//echo base_path();
		//exit;
  if($_FILES['image']['name'])
  {
	  $file_name = $_FILES['image']['name'];
if(move_uploaded_file($_FILES['image']['tmp_name'],  $target_path)) {
  echo "file upload sucessful";
} else{
    echo "Sorry, file not uploaded, please try again!";
}
  }


	   $claimDetail = [
	   'clientId'=> $_POST['customerId'],
	   'claim_reason' => $_POST['claim'],
	   'calim_doc' => $file_name,
	   'claim_status' => $_POST['claimStatus'],
	   'comments' => $_POST['comment']
	   ];
	  // print_r($claimDetail);exit;
	   $customberDetail=DB::table('client_claim')->insert(
                $claimDetail
                );
	    if($customberDetail){
			  return response()->json([
              'status'=>'200'
           ]);
		}else{
			return response()->json([
              'status'=>'203'
           ]);
		}

  }
  /*client claim submit end*/
  /*schedule install start*/
  public function installShedule(Request $request){
	  //echo $request->clientId; exit;
	  $install=DB::select( DB::raw("SELECT customers.firstName,customers.LastName,
	  customers.clientType,customers.planId,
	  plans.planName from customers
	  LEFT JOIN plans ON plans.planId = customers.planId
      left join agentpayment ON customers.customerId=agentpayment.customerId
      where customers.customerId =$request->clientId"));

	  $payment_details=DB::select( DB::raw("SELECT  (@a:=@a+1) AS serial_number,date_format(agentpayment.PaymentDate,'%m-%d-%Y')as payment_date,plans.planName,agentpayment.totalBurialFee,

(CASE
   WHEN customers.clientType = 'Family' THEN plans.familyFee
        ELSE plans.fee
    END) AS plan_original_fee,
    agentpayment.feeAmount as overridefee,
agentpayment.PaymentMode,

 (CASE
   WHEN agentpayment.MonthCounter=1 THEN
     round(agentpayment.feeAmount,2)
   ELSE
      round(agentpayment.feeAmount,2)
  END
  ) as feeAmount
    from `customers`
    join (SELECT @a:= 0) AS a
    LEFT JOIN `plans` ON `plans`.`planId` = `customers`.`planId`
    left join `agentpayment` ON `customers`.`customerId`=`agentpayment`.`customerId`
    where `customers`.`customerId` =$request->clientId  and agentpayment.PaymentDate <> '0' and agentpayment.is_agent_change=0"));
	//print_r " $payment_details"; die;
      if($install)
				  return response()->json([
				  'status'=>'200',
				  'install'=>$install,
				  'payment_details'=>$payment_details,
			   ]);
  }
	/*install schedule end*/
	/*customer email checking start*/
  public function customerEmailChecking(Request $request){
	  if($request->email !='')
	  {
     $duplicateEmail = DB::table('customers')
                                 ->where( [
                                     'email'       => $request->email,
                                 ] )->first();
                if (  $duplicateEmail ) {
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
	/*customer email checking end*/
	/*customer cell phone checking start*/
   public function customerCellPhoneChecking(Request $request){
      /* echo "<pre>";
	  print_r($request->all());
	  die('aaaa'); */

	  $duplicateEmail = DB::table('customers')
                                 ->where( [
                                     'cellPhone' => $request->phone,
                                 ] )->first();
                if (  $duplicateEmail ) {
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
	/*customer cell phone checking end*/
 /* public function groupInsert(Request $request){
	 echo "<pre>";
	  print_r($request->all());
	  //die('aaaa');

	    $groupArray = [
           'groupCode'      => $request->groupCode,
           'groupName'      => $request->groupName,
           'companyName'=>$request->companyName,

		   'address'		=>$request->address,
		   'city'			=>$request->city,
		   'state'			=>$request->state,
		   'zip'			=>$request->zip,
		   'phone'			=>$request->phone,
		   'country'		=>$request->country,
		   'membershiptype' =>$request->membershiptype,
		   'availableplans'	=>$request->availableplans,
		   'basegroup'		=>$request->basegroup,
           'status'      => $request->isActive,
            'created_at' => Carbon::now()
         ];
		 print_r($groupArray);
		 die('aaaa');
		 $groupId =  DB::table('groups')->insert(
                $groupArray
                );
		 if($groupId)
		 {
				  return response()->json([
				  'status'=>'200',
			   ]);
		 }
   }*/
	/*group insert start*/
   public function groupInsert(Request $request){
	   /*   echo "<pre>";
	  print_r($request->all());
	  die('aaaa');   
         exit;  */
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
   /*group insert end*/
	/*admin details start*/
    public function adminDetail(Request $request){
	     	 /* $admin=DB::select( DB::raw("select id,name,email,date_format(created_at,'%m-%d-%Y')
			  as created_date from `users` where roll_id !=1 order by id desc") );  */
			  
$admin = DB::TABLE('users') 
				 ->select('users.id','users.first_name','users.last_name' , 'users.email','users.created_at','users.status','users.userName','role_management.roll_name')
				->leftJoin('role_management','role_management.roll_id','=','users.roll_id')
				->get();
				$active_user = DB::TABLE('users') 
				 ->select('users.id','users.first_name','users.last_name' , 'users.email','users.created_at','users.status','users.userName','role_management.roll_name')
				->leftJoin('role_management','role_management.roll_id','=','users.roll_id')
				->where('users.status',1)
				->get();
				$inactive_user = DB::TABLE('users') 
				 ->select('users.id','users.first_name','users.last_name' , 'users.email','users.created_at','users.status','users.userName','role_management.roll_name')
				->leftJoin('role_management','role_management.roll_id','=','users.roll_id')
				->where('users.status',0)
				->get();
           $total_member = count($admin);
		   $total_active = count($active_user);
		   $total_inactive = count($inactive_user);
          // echo "$total_member"; 
		    //echo "$total_active";
			// echo "$total_inactive";
		  // exit;		   
		 if($admin)
		 {
				  return response()->json([
				  'admin'=> $admin,
				  'total_member' =>$total_member,
				  'total_active' =>$total_active,
				  'total_inactive' =>$total_inactive,
				  'status'=>'200'
			   ]);
		 }
   }
	/*admin details end*/
	/*total customer installment start*/
   public function totalCustomberInstallment(){
    $payment_details=DB::select( DB::raw("SELECT  (@a:=@a+1) AS serial_number, `customers`.`customerId` AS customerId,
CONCAT(`customers`.`firstName`,' ' ,`customers`.`LastName`) AS customer_name,
primary_table.paymentletterDate,
plans.planName,primary_table.totalBurialFee,primary_table.PaymentMode,
      (CASE
         WHEN customers.clientType = 'Family' THEN plans.familyFee
         ELSE plans.fee
       END) AS plan_original_fee,
     primary_table.feeAmount as overridefee,
 (CASE
    WHEN plans.planName='Monthly' THEN 'Monthly'
         ELSE 'Annual'
     END) as payment_frequency,
  (CASE
      WHEN plans.planName='Monthly' THEN primary_table.feeAmount
      ELSE round(primary_table.feeAmount/customers.paymentmode,2)
      END
  ) AS feeAmount

     from `customers`
     join (SELECT @a:= 0) AS a
     LEFT JOIN `plans` ON `plans`.`planId` = `customers`.`planId`
     left join `agentpayment` AS primary_table ON `customers`.`customerId`=`primary_table`.`customerId`

     INNER JOIN (SELECT MAX(paymentId) as pay_id FROM `agentpayment` Group by customerId) second_table ON primary_table.`paymentId` = second_table.pay_id

     WHERE MONTH(`primary_table`.`paymentletterDate`) = MONTH(CURRENT_DATE()) and primary_table.PaymentDate <> '0'
     order by `customers`.`customerId` desc"));

	 /*
	 $payment_details=DB::select( DB::raw("SELECT  (@a:=@a+1) AS serial_number, `customers`.`customerId` AS customerId,
CONCAT(`customers`.`firstName`,' ' ,`customers`.`LastName`) AS customer_name,
primary_table.paymentletterDate,
plans.planName, primary_table.feeAmount ,SUM(primary_table.feeAmount + primary_table.totalBurialFee) AS TotalFee
     from `customers`
     join (SELECT @a:= 0) AS a
     LEFT JOIN `plans` ON `plans`.`planId` = `customers`.`planId`
     left join `agentpayment` AS primary_table ON `customers`.`customerId`=`primary_table`.`customerId`

     INNER JOIN (SELECT MAX(paymentId) as pay_id FROM `agentpayment` Group by customerId) second_table ON primary_table.`paymentId` = second_table.pay_id

     WHERE MONTH(`primary_table`.`paymentletterDate`) = MONTH(CURRENT_DATE()) and primary_table.PaymentDate <> '0'
     order by `customers`.`customerId` desc"));
	 */

   if($payment_details)
           return response()->json([
           'status'=>'200',
           'payment_details'=>$payment_details,
          ]);
   }
   /*total customer installment end*/
   //CustomberInstallmentPayment
   /*
  public function CustomberInstallmentPayment(Request $request){

	 
	
    $agent_id='';
    $monthCounter='';
    $plan_Id='';
    $managerId='';
    $stateManagerId='';
    $agentPromotionId='';
    $managerPromotionId='';
    $recurringPaymentDate='';
    $stateManagerPromotionId='';
    $paymentletterDate='';
	$chargeBackInstalment='';
	$feeAmount="";
      $customer_details=DB::select( DB::raw("select AgentId,agentPromotionId,managerId,feeAmount,paymentId,managerPromotionId,stateManagerId,stateManagerPromotionId, planId,MonthCounter,paymentletterDate,recurringPaymentDate,chargeBackInstalment,total_earned_advance,
unearned_advance,	total_earned_advanc_managere,total_earned_advanc_stat_manager,unearned_adv_mang,unearned_adv_stat_mang   from `agentpayment` WHERE `agentpayment`.`customerId`=$request->customerId ORDER BY paymentId DESC Limit 0,1 ") );
       echo "<pre>";
	  print_r($customer_details);
	   die('aaaaa');
	  foreach($customer_details as $row){
		 $agent_id=$row->AgentId;
         $agentPromotionId=$row->agentPromotionId;
         $managerPromotionId=$row->managerPromotionId;
         $stateManagerPromotionId=$row->stateManagerPromotionId;
         $monthCounter=$row->MonthCounter;
         $recurringPaymentDate=$row->recurringPaymentDate;
         $plan_Id=$row->planId;
		 $feeAmount=$row->feeAmount;
         $managerId=$row->managerId;
         $stateManagerId=$row->stateManagerId;
         $paymentletterDate=$row->paymentletterDate;
		 $chargeBackInstalment=$row->chargeBackInstalment;
       }
	    $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agent_id)->where ('enddate' ,NULL)->get();
           
			$previousPaymentDate=strtotime($paymentletterDate);
			$nextPaymentDate = date('Y-m-d', strtotime('+1 month', $previousPaymentDate));
			 if($plan_Id == 1){
				 //$nextMonth = $monthCounter+1;
				  $chargeBackInstalment=($chargeBackInstalment+1);
			 }
			//if($monthCounter=6||$monthCounter%6==0){
				//echo $monthCounter;exit;
				//$check = $monthCounter % 6;
				//echo $check; exit;
			
				if($monthCounter % 6 == 0){
					$memberType =  DB::table('customers')->select('clientType')->where('customerId',$request->customerId)->get();
			        
					$overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverrideCommisionAnual')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->get());
						   if($overrideCommAnual[0]->OverrideCommisionAnual){
							   $FirstYearCommission = $overrideCommAnual[0]->OverrideCommisionAnual;
						   }
						   else {
							    $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
								$FirstYearCommission= $agtFstCommRat[0]->FirstYrComRate;

						   }
						if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $feeAmount*6;
						}
						else {
							 $chargeback = $feeAmount*6;
						}
                          $ChargeBackInterestForStateManager = 0;
						  //if()
						  $chargeBackInstalment =$chargeBackInstalment;
			              $MonthCounter=($monthCounter+1);
						  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
						  $chargeBackCommision =  $CommisionFee ;
						  $ChargeBackInterest= $CommisionFee*0.01*6;
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						  $IsAdvance='YES';
						  if(@$manager_Promotion_detail[0]==''){
						  $CommisionFeeForManager = 0;
						   $managerPormotionId = 0;
						   $ChargeBackInterestForManager = '';
					      }
					    else {
						 // echo "0";

						 $mngCommRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $manager_Promotion_detail[0]->level)->get());
						 $eff_man_comm_percent =$mngCommRate[0]->FirstYrComRate -  $FirstYearCommission;
						 if($eff_man_comm_percent>0){
							 $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 }
						 else{
							 $CommisionFeeForManager =0;
						 }
						 //$CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;

						  $ChargeBackInterestForManager = $CommisionFeeForManager*0.01*6;
					    }

						//comm calculation for state manager
						if(@$sate_mang__detail[0]==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
						  $ChargeBackInterestForStateManager = 0;
					      }
					  else {

						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FirstYrComRate -  $mngCommRate[0]->FirstYrComRate;
						  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$chargeback;
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
						  $ChargeBackInterestForStateManager = $stateManagerCommisionFee*0.01*6;
					  }

					$postArray = [
					'planId'=>$plan_Id,
					'AgentId'=>$agent_id,
					'PaymentMode' => $request->paymentmethod,
					'customerId' => $request->customerId,
					'PaymentDate'=>Carbon::now(),
					'newOrRenew'=>'installment',
					'feeAmount'=>$request->customerPayAmount,
					'totalBurialFee'=>$request->totalBurialFee,
					'stateManagerPromotionId'=>$stateManagerPormotionId,
					'Commission'=>$CommisionFee,
					'managerCommission'=>$CommisionFeeForManager,
					'stateManagerCommission'=>$stateManagerCommisionFee,
					'chargeBackCommision'=>$chargeBackCommision,
					'ChargeBackInterest'=>$ChargeBackInterest,
					'ChargeBackInterestForManager'=>$ChargeBackInterestForManager,
					'ChargeBackInterestForStateManager'=>$ChargeBackInterestForStateManager,
					'chargeBackInstalment' => $chargeBackInstalment,
					'MonthCounter'=>$MonthCounter,
					'IsAdvance'=>$IsAdvance,
					'paymentletterDate'=>$nextPaymentDate,
                    'managerId' =>$managerId ,
                    'managerPromotionId' =>$managerPromotionId
					];
			}else{
				  $chargeBackInstalment =$chargeBackInstalment-1;
			              $MonthCounter=($monthCounter+1);
             $postArray=[
              'planId'=>$plan_Id,
              'AgentId'=>$agent_id,
              'feeAmount'=>$request->customerPayAmount,
              'MonthCounter'=>$monthCounter+1,
              'PaymentMode'=>$request->paymentmethod,
              'newOrRenew'=>'installment',
              'paymentletterDate'=>$nextPaymentDate,
              'PaymentDate'=>date('Y-m-d'),
              'customerId'=>$request->customerId,
              'agentPromotionId'=>$agentPromotionId,
              'managerId'=>$managerId,
              'stateManagerId'=>$stateManagerId,
              'managerPromotionId'=>$managerPromotionId,
              'stateManagerPromotionId'=>$stateManagerPromotionId,
			  'chargeBackInstalment'=>$chargeBackInstalment,
			 'feeAmount'=>$request->customerPayAmount,
			 'totalBurialFee'=>$request->totalBurialFee,
			  'IsAdvance'=>'YES',
			  'paymentletterDate'=>$nextPaymentDate,
            ];

			} 
            // echo "<pre>";
           // print_r($postArray);
           // die('aaaaa');
           $customerInstallmentPayment=DB::table('agentpayment')->insert($postArray);
      if($customerInstallmentPayment){
        return response()->json([
        'status'=>'200'
       ]);
      }
  }
*/
	/*customer cancellation start*/
  public function customberCancellation(Request $request){
        $postArray=[
         'isActive'=>'No',
         'writing_agent'=>NULL,
         'agent_manager'=>NULL
       ];
        $customerInstallmentPayment=DB::table('customers')->where('customerId',$request->customerId)->update($postArray);
        $getCustomerRecords=DB::select( DB::raw("select agentpayment.chargeBackInstalment,agentpayment.newOrRenew,agentpayment.feeAmount, agentpayment.AgentId, agentpayment.agentPromotionId, agentpayment.managerId, agentpayment.managerPromotionId, agentpayment.stateManagerId, agentpayment.stateManagerPromotionId,  agentpayment.planId, plans.planName from `agentpayment`
left join plans ON plans.planId=agentpayment.planId
WHERE `agentpayment`.`customerId`=$request->customerId Limit 0,1") );
        //{this.state.paymentMode!=null?(this.state.total_days<=25?(this.state.planName=='Monthly'?0:((this.state.total_membership_fees/this.state.paymentMode)*(this.state.total_payment_details))).toFixed(2):(((this.state.total_membership_fees/12)*(12-this.state.total_month))).toFixed(2)):0}
        $total_days_from_membership_date=DB::select( DB::raw("select datediff(now(),agentpayment.PaymentDate) AS total_date_difference FROM agentpayment where agentpayment.customerId=$request->customerId"));

        $total_month_from_membership_date=DB::select( DB::raw("select CEIL(datediff(now(),agentpayment.PaymentDate)/30) AS total_month_difference FROM agentpayment where agentpayment.customerId=$request->customerId"));

        $customerCancelPayment="";
        $customerCancelDetails='';
        $total_chargeback_amount="";
        foreach($getCustomerRecords as $row){
          $totalDate_diff=$total_days_from_membership_date[0]->total_date_difference;
          $total_refund_amount='';
          if($totalDate_diff<25){
            $total_refund_amount=$row->feeAmount;
          }else{
            if($row->planName=='Monthly'){
              $total_refund_amount=0;
            }else{
              $total_refund_amount=(($row->feeAmount/12)*(12-$total_month_from_membership_date[0]->total_month_difference));
            }

          }

          $customerCancelDetails=[
           'customer_id'=>$request->customerId,
           'cancel_amount'=>$total_refund_amount,
           'planId'=>$row->planId,
           'canceldate'=>date('Y-m-d'),
           'agent_id'=>$row->AgentId,
           'agentPromotionId'=>$row->agentPromotionId,
           'managerId'=>$row->managerId,
           'managerPromotionId'=>$row->managerPromotionId,
           'stateManagerId'=>$row->stateManagerId,
           'stateManagerPromotionId'=>$row->stateManagerPromotionId,
          ];
          $customerCancelPayment=DB::table('customer_cancellation')->insert($customerCancelDetails);

          //Agent Chargeback calculation
          $agentComission=DB::select( DB::raw("SELECT DISTINCT agentPromotion.agentId,agents.levelID,(CASE
                                                WHEN (agentpayment.newOrRenew = 'NEW')
                                                THEN
                                                    CASE
                                                    WHEN (plans.planName='Monthly'||plans.planName='Annual')
                                                        THEN (agentlevels.FirstYrComRate)
                                                        ELSE (agentlevels.FiveYrLifeComRate)
                                                    END
                                                ELSE
                                                 agentlevels.RenewComRate
                                                END ) AS commissionRate
                                        FROM `agents`
                                        left join agentPromotion ON agentPromotion.agentId=agents.agentId
                                        left join agentlevels ON agentlevels.levelID=agentPromotion.level
                                        left join agentpayment ON agentpayment.AgentId=agents.agentId
                                        left join plans ON agentpayment.planId=plans.planId
                                        where agentPromotion.enddate IS NULL and agentpayment.customerId=$request->customerId"));
              if($totalDate_diff<25){
                $total_chargeback_amount=0;
              }else{
                if($row->planName=='Monthly'){
                  $total_chargeback_amount=(($row->feeAmount)*($agentComission[0]->commissionRate/100)*($row->chargeBackInstalment-$total_month_from_membership_date[0]->total_month_difference));
                }else{
                  $total_chargeback_amount=(($row->feeAmount/12)*($agentComission[0]->commissionRate/100)*(12-$total_month_from_membership_date[0]->total_month_difference));
                }

              }

            $agentChargeBack=[
            'agentId'=>$row->AgentId,
            'agentPromotionId'=>$row->agentPromotionId,
            'agentChargebackAmount'=>$total_chargeback_amount,
            'customerId'=>$request->customerId,
            'cancelDate'=>date('Y-m-d')
          ];

          $agentTotalChargeBackCommission=DB::table('agentPayable')->insert($agentChargeBack);

		  //manager  Chargeback calculation
          /* $agentComission=DB::select( DB::raw("SELECT DISTINCT agentPromotion.agentId,agents.levelID,(CASE
                                                WHEN (agentpayment.newOrRenew = 'NEW')
                                                THEN
                                                    CASE
                                                    WHEN (plans.planName='Monthly'||plans.planName='Annual')
                                                        THEN (agentlevels.FirstYrComRate)
                                                        ELSE (agentlevels.FiveYrLifeComRate)
                                                    END
                                                ELSE
                                                 agentlevels.RenewComRate
                                                END ) AS commissionRate
                                        FROM `agents`
                                        left join agentPromotion ON agentPromotion.agentId=agents.agentId
                                        left join agentlevels ON agentlevels.levelID=agentPromotion.level
                                        left join agentpayment ON agentpayment.AgentId=agents.agentId
                                        left join plans ON agentpayment.planId=plans.planId
                                        where agentPromotion.enddate IS NULL and agentpayment.customerId=$request->customerId"));
              if($totalDate_diff<25){
                $total_chargeback_amount=0;
              }else{
                if($row->planName=='Monthly'){
                  $total_chargeback_amount=(($row->feeAmount)*($agentComission[0]->commissionRate/100)*($row->chargeBackInstalment-$total_month_from_membership_date[0]->total_month_difference));
                }else{
                  $total_chargeback_amount=(($row->feeAmount/12)*($agentComission[0]->commissionRate/100)*(12-$total_month_from_membership_date[0]->total_month_difference));
                }

              }

            $agentChargeBack=[
            'agentId'=>$row->AgentId,
            'agentPromotionId'=>$row->agentPromotionId,
            'agentChargebackAmount'=>$total_chargeback_amount,
            'customerId'=>$request->customerId,
            'cancelDate'=>date('Y-m-d')
          ];

          $managerTotalChargeBackCommission=DB::table('agentPayable')->insert($agentChargeBack); */

        }
        if($customerCancelPayment!=""){
        return response()->json([
        'status'=>'200'
        ]);
    }
  }
  /*customer cancellation end*/
  /*membership refund start*/
  public function membershipRefund(Request $request){
    /*echo "<pre>";
    print_r($request->id);
    die('aaaaa');*/
    //select count(paymentId) total_installment from agentpayment where customerId=12
    $member_details=DB::select( DB::raw("SELECT distinct CONCAT(customers.firstName,' ',customers.LastName) AS customer_name,
	  customers.clientType,customers.planId,
	  plans.planName,agentpayment.feeAmount, customer_cancellation.cancel_amount from customers
	  LEFT JOIN plans ON plans.planId = customers.planId
      left join agentpayment ON customers.customerId=agentpayment.customerId
      left join customer_cancellation ON customers.customerId=customer_cancellation.customer_id
      where customers.customerId =$request->id"));

    /*$membership_fees=DB::select( DB::raw("SELECT agentpayment.feeAmount from agentpayment
      where agentpayment.customerId =$request->id Limit 0,1"));
	  $total_payment_details=DB::select( DB::raw("select count(paymentId) total_installment from agentpayment where customerId=$request->id"));
     $total_days_from_membership_date=DB::select( DB::raw("select datediff(now(),customers.membershipDate) AS total_date_difference FROM customers where customers.customerId=$request->id"));
    //$total_month_from_membership_date=DB::select( DB::raw("select TIMESTAMPDIFF(MONTH,customers.membershipDate,now()) AS total_month_difference FROM customers where customers.customerId=$request->id"));
    $total_month_from_membership_date=DB::select( DB::raw("select CEIL(datediff(now(),customers.membershipDate)/30) AS total_date_difference FROM customers where customers.customerId=$request->id"));
      if($member_details)
				  return response()->json([
				  'status'=>'200',
				  'member_details'=>$member_details,
          'membership_fees'=>$membership_fees,
				  'total_payment_details'=>$total_payment_details,
          'total_days'=>$total_days_from_membership_date,
          'total_month'=>$total_month_from_membership_date
        ]);*/
        if($member_details)
  				  return response()->json([
              'status'=>'200',
    				  'member_details'=>$member_details
          ]);
  }
	/*membership refund end*/
	/*card managment start*/
  public function card_management(){
	   $customer = DB::select(DB::raw("SELECT customers.customerId,CONCAT(customers.firstName,' ',customers.LastName) AS customer_name,
 	  customers.email,customers.cellPhone,DATE_FORMAT(customers.DOB, '%m-%d-%Y') as DOB,customers.city,customers.state,customers.zip,plans.planName
    from customers
    LEFT JOIN plans ON plans.planId = customers.planId
	LEFT JOIN memeber_card ON memeber_card.memberId = customers.customerId
	LEFT JOIN agentpayment ON agentpayment.customerId = customers.customerId
	where memeber_card.memberId IS NULL AND customers.isActive ='Yes' AND agentpayment.PaymentMode !=''
    order by customers.customerId DESC"));
     /*DB::table('customers')
->select('customers.firstName','customers.LastName','customers.email','customers.city',
'customers.state','customers.zip','cellPhone','plans.planName','customers.DOB','customers.DOB')

			->Join('plans', 'customers.planId', '=', 'plans.planId')
			-->where('customers.customerId', $request->customerId) ->get();*/

			if($customer){
              return response()->json([
             'status'=>'200',
             'customer_details'=>$customer,
            ]);

           }
	     /*else{
			 return response()->json([
             'status'=>'200'

            ]);

		}*/

  }
  /*card managment end*/
  /*customer inactive to active start*/
  public function customberActivation(Request $request){
   $postArray=[
    'isActive'=>'Yes'
     ];
  $customerActivation=DB::table('customers')->where('customerId',$request->customerId)->update($postArray);
   if($customerActivation){
       return response()->json([
       'status'=>'200'
       ]);
     }
   }
   /*customer inactive to active end*/
   /* member list download start*/
   public function member_list_download(Request $request){
          // print_r($request->data);exit;
		  $current_timestamp = Carbon::now()->timestamp;
		  foreach($request->all() as $key=>$val){

			//print_r($val['memberId']);


				$data = DB::table('memeber_card')->insert(
                   array(
                  'memberId'     =>   $val['memberId'],
                  'isCard'   =>   '1'
                 )
               );
		  }
		     if($data)
			 {
              return response()->json([
             'status'=>'200',
             'data'=>$data,
            ]);
			 }
			else{
				  return response()->json([
                 'status'=>'203',
                 'data'=>$data,
            ]);
			}


		   //echo"<pre>";
		   //print_r($request->all());exit;

   }
   /*member list download end*/
   /*card managment name start*/
    public function card_management_name(Request $request){
	 // print_r($request->search);exit;
	// echo $request->search;exit;

	$customer = DB::select(DB::raw("SELECT customers.customerId,CONCAT(customers.firstName,' ',customers.LastName) AS customer_name,
 	  customers.email,customers.cellPhone,DATE_FORMAT(customers.DOB, '%m-%d-%Y') as DOB,customers.city,customers.state,customers.zip,plans.planName,customers.burialFee
    from customers
    LEFT JOIN plans ON plans.planId = customers.planId

	LEFT JOIN agentpayment ON agentpayment.customerId = customers.customerId
	where  customers.isActive ='Yes' AND agentpayment.PaymentMode !='' AND customers.firstName LIKE '%$request->search%' OR customers.lastName LIKE '%$request->search%' OR customers.customerId LIKE '%$request->search%'
    order by customers.customerId DESC"));
     /*DB::table('customers')
->select('customers.firstName','customers.LastName','customers.email','customers.city',
'customers.state','customers.zip','cellPhone','plans.planName','customers.DOB','customers.DOB')

			->Join('plans', 'customers.planId', '=', 'plans.planId')
			-->where('customers.customerId', $request->customerId) ->get();*/

			if($customer){
              return response()->json([
             'status'=>'200',
             'customer_details'=>$customer,
            ]);

           }
	     else{
			 return response()->json([
             'status'=>'203'

            ]);

		}

  }
	/*card managment name end*/
	/*web payment start*/
  public function web_payment(Request $request){

	// print_r($request->all());exit;
	    $URL = 'https://sandbox.forte.net/api';//sandbox
	    // $URL = 'https://api.forte.net'; ///live
	 
	
	
		//$url = 'https://api.forte.net';
		$AccountID  = 'act_383572';
		$LocationID = 'loc_244363';
		$APIKey = '563b59201ed8da9f9874f246fc44b62d';//sandbox
		$SecureTransactionKey = '91fb895a47e2765c5436fd9f4498d180';//sandbox	
		//$APIKey = '3b6322cdfd84db83c04e106d3bef32e9';//live
		 //$SecureTransactionKey = '0c1eff89b5900c0d6c038b35c41a2ddd';//live
	
	  $auth_token = base64_encode($APIKey.':'.$SecureTransactionKey);
	
		         
		  
		  if($request->check=='check')
		  {
			  
		  $payment_mode = 'Bank Account';
		  $shedule_amount = $request->sheduleAmount;
			$pg_total_amount =$request->pg_total_amount;
		 //$sheduleAmount = $request->sheduleAmount;
		 $first_name = $request->client_first_name;
		 $last_name = $request->client_last_name;
		 $plan_name = $request->plan_name;
		 $account_holder_name = $request->account_holder_name;
		 $routing_number = $request->ecom_payment_check_trn;
		 $account_numer =  $request->payment_check_account;
		 $account_type =  $request->payment_check_account_type;
		 $plane_name = $request->plane_name;		 
		 
		 $customerId = uniqid();
		 $auto_renew  = $request->auto_renew;
		 $create_customer = array(
            "first_name"=>$first_name,
            "last_name" =>$last_name,
          "customer_id" =>$customerId,

		  );
		  }
		  else
		  {
			  $payment_mode = 'Card';
			 $pg_total_amount =$request->total_amount;
		 $first_name = $request->client_first_name;
		 
		 $last_name = $request->client_last_name;
		 $postalcode = $request->pg_total_amount;
		 $shedule_amount = $request->sheduleAmount;
		 $card_holoder_name = $request->card_holder_name;
		 $e_card_type = $request->card_type;
		 $plan_name = $request->plan_name;
		 $payment_card_number = $request->card_number;
		 $cvv = $request->cvv;
		 $ecom_payment_card_expdate_month = $request->exp_month;
		 $ecom_payment_card_expdate_year = $request->exp_year;
		 $auto_renew = $request->auto_renew;
		 //query for creating customberActivatin
		 $customerId = uniqid();
		 $paymethod_token=md5(uniqid());
		 $create_customer = array(
            "first_name"=>$first_name,
            "last_name" =>$last_name,
          "customer_id" =>$customerId,
		  
		 
		  );  
			  
		  }
		 //print_r($create_customer);exit;
		  $create_customer =  json_encode($create_customer);
		 // print_r($create_customer);exit;
		  $create_customer_url =  $URL.'/v2/locations/'.$LocationID.'/customers/';
	  $curl = curl_init($create_customer_url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$auth_token,
					             'X-Forte-Auth-Account-Id: '.$AccountID,
						     'Content-Type: application/json'));		
    curl_setopt($curl, CURLOPT_POST,1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$create_customer); 
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	
	$curl_response = curl_exec($curl);

	//Use this to look for bad HTTP status codes
	$info = curl_getinfo($curl);
	//print_r('HttpStatus Code: ' . $info['http_code'] . '<br>');

	//if using CURLOPT_FAILONERROR = true:
	//(Will not contain the information found in the response body.)
	if($curl_response === false)
	{
	    print_r('Curl error: ' . curl_error($curl) . '<br>');
	}
	
	curl_close($curl);
	//print_r($curl_response);

	$decoded_customber = json_decode($curl_response);
	 // echo "</pre>";print_r($decoded_customber);exit;
		 $forte_customber_id = $decoded_customber->customer_token;
	      //$forte_customber_id = $cst_Dm_SF1viQ02VcONul8c7AA
	 
		//print_r($_POST);exit;
		 
		
		 //$pg_total_amount =$request->total_amount;
		 /*$pg_total_amount =1;
		 $first_name = $request->client_first_name;
		 $last_name = $request->client_last_name;
		 $postalcode = $request->pg_total_amount;
		 $plan_name = $request->plan_name;
		 $card_holoder_name = $request->card_holder_name;
		 $e_card_type = $request->card_type;
		 $payment_card_number = $request->card_number;
		 $cvv = $request->cvv;
		 $ecom_payment_card_expdate_month = $request->exp_month;
		 $ecom_payment_card_expdate_year = $request->exp_year;
		 $auto_renew = $request->auto_renew;*/
		   if($request->check=='check')
		  {
			  $raw = array (
               "authorization_amount"=>$pg_total_amount,
			   "order_number"=>"$plane_name",
            "subtotal_amount"=>$pg_total_amount,
			 "action"=>"sale",
                  "billing_address"=>array( "first_name"=>$first_name,
                   "last_name"=> $last_name ),        			
            "echeck"=>array("account_holder"=>$account_holder_name,"account_number"=>$account_numer,"routing_number"=>$routing_number,"account_type"=>$account_type));
			  $raw_to_send = json_encode($raw,true); 
		  }
		  else {
			  
			  
			   $raw = array(
	  "action"=>"sale","authorization_amount"=>$pg_total_amount,"order_number"=>"$plan_name","subtotal_amount"=>$pg_total_amount,"billing_address"=>
	  array("first_name"=>"$first_name","last_name"=>"$last_name"),
	  "card"=>array("card_type"=>"$e_card_type","name_on_card"=>"$card_holoder_name","account_number"=>"$payment_card_number","expire_month"=>"$ecom_payment_card_expdate_month","expire_year"=>"$ecom_payment_card_expdate_year","card_verification_value"=>"$cvv"));
	  $raw_to_send = json_encode($raw,true);
		  }
		
	  // print_r($raw_to_send);	exit;
	
		$service_url = $URL.'/v2/accounts/'.$AccountID.'/locations/'.$LocationID.'/transactions/';
		//$service_url = $URL.'/v2/accounts/'.$AccountID.'/locations/'.$LocationID.'/customers/';
	//$service_url = $URL.'/v2/accounts/'.$AccountID.'/locations/'.$LocationID.'customers/'.$forte_customber_id.'/paymethods';
	
	
	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$auth_token,
					             'X-Forte-Auth-Account-Id: '.$AccountID,
						     'Content-Type: application/json'));		
    curl_setopt($curl, CURLOPT_POST,1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$raw_to_send); 
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	
	$curl_response = curl_exec($curl);

	//Use this to look for bad HTTP status codes
	$info = curl_getinfo($curl);
	//print_r('HttpStatus Code: ' . $info['http_code'] . '<br>');

	//if using CURLOPT_FAILONERROR = true:
	//(Will not contain the information found in the response body.)
	if($curl_response === false)
	{
	    print_r('Curl error: ' . curl_error($curl) . '<br>');
	}
	
	curl_close($curl);
	//print_r($curl_response);

	$decoded = json_decode($curl_response);
	//echo "</pre>";print_r($decoded);exit;
	$transcation_id = $decoded->transaction_id;
	//echo "</pre>";print_r($decoded);exit;
	if(($decoded->response->response_code == "A01"))
	{
		//sheduler
		
		
			 if($auto_renew  ==1)
			 
			 {
				/*
				
				

	$decoded_paymethod = json_decode($curl_response);
	
	
	print_r($decoded_paymethod);exit;
	*/
	//payment method token create 
	
   $payment_method = array (
   "notes"=>"Brwn Work Card",
   "card"=>array("name_on_card"=>"$card_holoder_name","card_type"=>"$request->card_type","account_number"=>"$payment_card_number","expire_month"=>$ecom_payment_card_expdate_month,"expire_year"=>$ecom_payment_card_expdate_year,"card_verification_value"=>"$cvv"));
    $payment_method = json_encode($payment_method);

  $payment_method_url = $URL.'/v2/accounts/'.$AccountID.'/locations/'.$LocationID.'/customers/'.$forte_customber_id.'/paymethods';

		$curl = curl_init($payment_method_url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$auth_token,
					             'X-Forte-Auth-Account-Id: '.$AccountID,
						     'Content-Type: application/json'));		
    curl_setopt($curl, CURLOPT_POST,1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$payment_method); 
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	
	$curl_response = curl_exec($curl);

	//Use this to look for bad HTTP status codes
	$info = curl_getinfo($curl);
	//print_r('HttpStatus Code: ' . $info['http_code'] . '<br>');

	//if using CURLOPT_FAILONERROR = true:
	//(Will not contain the information found in the response body.)
	if($curl_response === false)
	{
	    print_r('Curl error: ' . curl_error($curl) . '<br>');
	}
	
	curl_close($curl);
	

	$decoded_paymemt_response = json_decode($curl_response);
	  $payment_token = $decoded_paymemt_response->paymethod_token;
	//payment token code end

	 $one_advance_date =  date('Y-m-d', strtotime("+1 day"));
	$sheduler = array(
  "action" =>"sale",
  "schedule_quantity" => "12",
  "schedule_frequency" => "monthly",
  "schedule_amount"=>  $shedule_amount,
  "schedule_start_date"=>$one_advance_date,
  "reference_id"=>"INV-123",
  "order_number"=>"98762222",
  "item_description"=>"Monthly Plan",
  "paymethod_token"=>$payment_token
  
  );
  // print_r($sheduler);exit;
   $create_customer =  json_encode($sheduler);
            $shedule_url = $URL.'/v2/accounts/'.$AccountID.'/locations/'.$LocationID.'/customers/'.$forte_customber_id.'/schedules/';
			
			$curl = curl_init($shedule_url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$auth_token,
					             'X-Forte-Auth-Account-Id: '.$AccountID,
						     'Content-Type: application/json'));		
    curl_setopt($curl, CURLOPT_POST,1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$create_customer); 
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	
	$curl_response = curl_exec($curl);

	//Use this to look for bad HTTP status codes
	$info = curl_getinfo($curl);
	//print_r('HttpStatus Code: ' . $info['http_code'] . '<br>');

	//if using CURLOPT_FAILONERROR = true:
	//(Will not contain the information found in the response body.)
	if($curl_response === false)
	{
	    print_r('Curl error: ' . curl_error($curl) . '<br>');
	}
	
	curl_close($curl);
	

	$decoded212 = json_decode($curl_response);
	//print_r($decoded212);exit;
	$transcation_id = $decoded->transaction_id;
	//print_r($decoded);exit;
	
	  }
		
		
		
	 $agentManagerPromotionId=0;
		$agentStateManagerPromotionId=0;
		$agentManagerId=0;
		$agentStateManagerId=0;
		$update =  DB::table('frontend_customer_temp')
        ->where('customerId', $request->customer_id)
        ->update(array('isPad' => 1));
	$webCustonmber  = DB::table('frontend_customer_temp')->select('firstName','middleName','LastName','DOB','address1','address2','mailing_address1','mailing_address2','email','city','city1','country','country1','clientType','zip','zip1','state','state1','spouseFirstName','spouseMiddleName','spouseLastName','price','cellPhone','spouseDOB','dependent1FirstName','dependent1MiddleName','dependent1LastName','dependent1DOB','planId','agentNumber as agentId','membershipDate','dependent2FirstName','dependent2MiddleName','Dependent2LastName','dependent2DOB','modDate','InitiationFees','isBurial','burialCity','burialState')->where('customerId',$request->customer_id)->get();
	//print_r($webCustonmber);exit;
	
	  $burialFee =  DB::table('plans')->select('burial_individual','burial_family')->where('planId',$webCustonmber[0]->planId)->get();
	  
	  if($webCustonmber[0]->agentId!=''){
		$agent_PromotionId = DB::select( DB::raw("SELECT agentwisePromotioncode.agentId, agentwisePromotioncode.agentPromotionId FROM `agentwisePromotioncode` Where agentwisePromotioncode.agentCode='".$webCustonmber[0]->agentId."'"));
		$agentPromotionId = $agent_PromotionId[0]->agentPromotionId;
		$agentId=$agent_PromotionId[0]->agentId;
	  }else{
		   //$agentPromotionId =1027 ;//for local
		     $agentPromotionId =21 ;//for development
		    //$agentPromotionId =41 ;//for live
		
		 //1027 for local  21 for developmentg and 41 for live
		$agentId=1041; 
	  }
	  
		//echo"<pre>";
		//print_r($burialFee);
		 //  echo "$agentId";exit;
		$password = $this->random_num(8);
		   $customerId =$request->customerId;
		      $postCustomber = [
                              'firstName'      => $webCustonmber[0]->firstName,
							  'middleName'      => $webCustonmber[0]->middleName,
                              'LastName'      => $webCustonmber[0]->LastName,
                              'DOB'=>$webCustonmber[0]->DOB,
                              'city'      => $webCustonmber[0]->city,
                              'city1'      => $webCustonmber[0]->city,
                              'state'      => $webCustonmber[0]->state,
                              'state1'      =>$webCustonmber[0]->state,
                              'zip'  =>$webCustonmber[0]->zip,
                              'zip1'      => $webCustonmber[0]->zip,
							  'isPaidCustomer'=>'1',
                              //'groupCode'=>$request->groupId,
                              'groupId'=>'1002',
                              'isActive'=>'Yes',
                              'password'      => $password,
                              'email' =>$webCustonmber[0]->email,
                              'companyName'      =>'',
                              //'writing_agent'      => $managerId,
                              'writing_agent'      => '',

                              //'agentId'  =>  $webCustonmber[0]->agentId,
                              'agentId'  =>  $agentId,
                              //'agent_manager' =>$managerId,
                              'agent_manager'=>'',
                              'country'  =>$webCustonmber[0]->country,
                              'country1'  =>$webCustonmber[0]->country,
                              'cellPhone'  =>$webCustonmber[0]->cellPhone,
                              'clientType'  =>$webCustonmber[0]->clientType,
                              'mobile2'  =>'',
                              'planId'  =>$webCustonmber[0]->planId,
                              'address1'  =>$webCustonmber[0]->address1,
                              'address2'  =>$webCustonmber[0]->address1,
							  'mailing_address1'  =>$webCustonmber[0]->address1,
							  'mailing_address2'  =>$webCustonmber[0]->address1,
                              //'mailing_address1'  =>'',
                              //'mailing_address2'  => '',
                              'dependent1FirstName' =>$webCustonmber[0]->dependent1FirstName,
                              'dependent2FirstName' =>$webCustonmber[0]->dependent2FirstName,
                              'dependent3FirstName' =>'',
							  'dependent1MiddleName' =>$webCustonmber[0]->dependent1MiddleName,
							  'dependent2MiddleName' =>$webCustonmber[0]->dependent2MiddleName,
                              'dependent3MiddleName' =>'',
                              'dependent1LastName' =>$webCustonmber[0]->dependent1LastName,
                              'dependent2LastName' =>$webCustonmber[0]->Dependent2LastName,
                              'dependent3LastName' =>'',
                              'dependent1DOB' =>$webCustonmber[0]->dependent1DOB,
                              'dependent2DOB' =>'',
                              'spouseFirstName'  =>$webCustonmber[0]->spouseFirstName,
							  'spouseMiddleName'  =>$webCustonmber[0]->spouseMiddleName,
                              'spouseLastName'  =>$webCustonmber[0]->spouseLastName,
                              'spouseDOB' =>$webCustonmber[0]->spouseDOB,
                              'initiationFee'=>$webCustonmber[0]->InitiationFees,
                              'burialFee'=>'',
							  'seminarFee'=>'',
                              'created_at' =>Carbon::now(),
                              'membershipDate' =>date("y-m-d"),
                              'modDate' =>date("y-m-d"),
                              'modBy' =>'Member',
                              'note'=>'',
							  
							  ];
							  
		//print_r($postCustomber);exit;
		  $customerId = DB::table('customers')->insertGetId(
                $postCustomber
                );
				
		//print_r ($webCustonmber[0]->firstName);exit;
		   //echo  $customerId; exit;
		//$customberId = DB::getPdo()->lastInsertId();
		if($webCustonmber[0]->isBurial == 'yes'){
			$count =1;
			if($webCustonmber[0]->clientType == 'Individual' )
			{
			$burialFee_per_per =  	$burialFee[0]->burial_individual;
			}
			else {
				$burialFee_per_per =  	$burialFee[0]->burial_family;
				
			}
				
			
			
		
		//$burial_per_person 
		
		  if($webCustonmber[0]->spouseFirstName != '')
		  {
			  $count =2;
		  }
		   if ($webCustonmber[0]->dependent1FirstName != ''){
			 $count =3; 
		  }
		  if ($webCustonmber[0]->dependent2FirstName != ''){
			 $count =4; 
		  }
		  $total_burial_fee = $burialFee_per_per*$count;
		  $burialArray = [
		              'memberId'  => $customerId,
                      'feePerPerson'  => $burialFee_per_per,
                      'totalFee' =>$total_burial_fee,
                      'burialCity' =>$webCustonmber[0]->burialCity,
                      'burialState'=>$webCustonmber[0]->burialState,						
		  
		  ];
		   $burialId = DB::table('burialdetail')->insertGetId(
                 $burialArray
                );
	 }
	 else {
		 $total_burial_fee= '';
	 }
		
		
		//print_r($agent_PromotionId);exit;
		$agent_upper_level_id=DB::select( DB::raw("SELECT `managerId`,`managerPromotionId`,`stateManagerId`,`stateManagerPromotionId` FROM `agentmanagers` WHERE `agentId`=".$agentId." and `agentPromotionId`=".$agentPromotionId) );
		//print_r($agent_upper_level_id);exit;
            foreach ($agent_upper_level_id as $key => $value) {
              // code...
              $agentManagerId=$value->managerId;
              $agentStateManagerId=$value->stateManagerId;
              $agentManagerPromotionId=$value->managerPromotionId;
              $agentStateManagerPromotionId=$value->stateManagerPromotionId;
            }
		
		 $plan = $webCustonmber[0]->planId;
		 if($plan == '1'||$plan =='2'){
			 $chargeBackInstalment = 1;
		 $agentCommisionDetail = DB::table('agents')
            ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
            ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
            ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			->where('agentwisePromotioncode.agentPromotionId','=' ,$agentPromotionId)
            ->get();
     //echo "<pre>"; print_r($agentCommisionDetail);exit;
	        
			
				   $agentCommisionDetail =   json_decode($agentCommisionDetail);
		  $agentCommisionPercent  = $agentCommisionDetail[0]->FirstYrComRate;
		  //print( $agentCommisionPercent);exit;
		   if($agentManagerPromotionId>0){
                  //echo 		$agentManagerPromotionId;exit;
			 	 $managerCommisionDetail = DB::table('agents')
				->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
				->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
				->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				->where('agentPromotion.agentPromotionId','=' ,$agentManagerPromotionId)
				->get();
		        $managerCommisionDetail =   json_decode($managerCommisionDetail);
		        //print_r( $managerCommisionDetail[0]->FirstYrComRate);exit;
				$managerCommisionPercent  = $managerCommisionDetail[0]->FirstYrComRate;
              $eff_man_comm_percent =$managerCommisionPercent -  $agentCommisionPercent;
				$planFee = $webCustonmber[0]->price;
				$managerCommisionFee = ($eff_man_comm_percent/100)*$planFee;
        //echo $managerCommisionFee;die('bbbb');
			 }else{
                 $managerCommisionPercent=0;
				$eff_man_comm_percent=0;
				$managerCommisionFee=0;
				$managerId=0;
			 }
			 if($agentStateManagerPromotionId>0){
          //echo 		$agentManagerPromotionId;exit;
			  $stateManagerCommisionDetail = DB::table('agents')
              ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
              ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
              ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			        ->where('agentPromotion.agentPromotionId','=' ,$agentStateManagerPromotionId)
              ->get();
			  $stateManagerCommisionDetail =   json_decode($stateManagerCommisionDetail);
		      //print_r( $stateManagerCommisionDetail[0]->FirstYrComRate);exit;
		    $stateManagerCommisionPercent  = $stateManagerCommisionDetail[0]->FirstYrComRate;
			  $eff_stateman_comm_percent =$stateManagerCommisionPercent - $managerCommisionPercent;
        //echo  $eff_stateman_comm_percent;
			  $planFee = $request->fees;
			  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$planFee;
        $stateManagerId=$request->selectedStateManagerId;
        //echo $stateManagerCommisionFee;die('aaa');
			}else{
        $stateManagerCommisionPercent=0;
				$eff_stateman_comm_percent=0;
				$stateManagerCommisionFee=0;
				$stateManagerId=0;
			}
		  // echo $plan; exit;
			 if($plan == '1')
			{
				//echo"abhi";exit;
			    $total_earned_advance = 0;
				$unearned_advance = 0;
				$total_earned_advanc_managere = 0;
				$total_earned_advanc_stat_manager = 0;
				$unearned_adv_stat_mang = 0;
				$unearned_adv_mang = 0;
			  $planFee = $webCustonmber[0]->price;
			  $time = strtotime(date("y-m-d"));
        $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
			  $chargeback = $planFee*6;
			 // $chargebackCommisionFee = 0;
			 // some extra code adding 
			    $CommisionFee = ($agentCommisionPercent/100)*$chargeback;
					       $total_earned_advance = ($agentCommisionPercent/100)* $planFee*$chargeBackInstalment;
						  $unearned_advance =  $CommisionFee - $total_earned_advance;
			  $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
			  $chargebackCommisionFeeForStateManager = ($eff_stateman_comm_percent/100)*$chargeback;
			  $total_earned_advanc_managere = ($eff_man_comm_percent/100)* $planFee*$chargeBackInstalment;
			  $unearned_adv_mang =  $CommisionFeeForManager -  $total_earned_advanc_managere;
			  $total_earned_advanc_stat_manager = ($eff_stateman_comm_percent/100)* $planFee*$chargeBackInstalment;
			  $unearned_adv_stat_mang =$stateManagerCommisionFee - $total_earned_advanc_stat_manager;
			//inserting
			$agentArray = [
                        'planId'=>$plan,
                        'customerId'=>  $customerId,
                        //'AgentId'=> $selectedAgentId,
                        //'AgentId'=>$webCustonmber[0]->agentId,
                        'AgentId'=>$agentId,
                        'agentPromotionId'=>$agentPromotionId,
                        'managerId'=>$agentManagerId,
                        'managerPromotionId'=>$agentManagerPromotionId,
                        'stateManagerId'=>$agentStateManagerId,
                        'stateManagerPromotionId'=>$agentStateManagerPromotionId,
                        'stateManagerCommission'=>$chargebackCommisionFeeForStateManager,
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=>$CommisionFee,
                        'chargeBackCommision'=>0,
                        'chargeBackInstalment'=>$chargeBackInstalment,
                        //'ChargeBackInterest'=>($chargebackCommisionFee*0.01*6),
                        //'ChargeBackInterestForManager'=>($chargebackCommisionFeeForManager*0.01*6),
                       // 'ChargeBackInterestForStateManager'=>($chargebackCommisionFeeForStateManager*0.01*6),
					    'ChargeBackInterest'=>0,
                        'ChargeBackInterestForManager'=>0,
                        'ChargeBackInterestForStateManager'=>0,
                        'managerCommission'=>$CommisionFeeForManager,
                        'IsAdvance'=>'YES',
                        'PaymentMode'=>$payment_mode,
                        'PaymentDate'=>date("Y-m-d"),
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$webCustonmber[0]->price,
                        'newOrRenew'=>'NEW',
                        'paymentletterDate'=>$nextPaymentDate,
                        'recurringPaymentDate'=>$nextPaymentDate,
                        'override_fees'=>0,
						 'totalBurialFee'=>$total_burial_fee,
						 'transction_id' => $transcation_id,
						 'customer_id'  => $forte_customber_id,
						  'total_earned_advance' =>$total_earned_advance,
							'unearned_advance' => $unearned_advance,
							'total_earned_advanc_managere' =>$total_earned_advanc_managere,
							'total_earned_advanc_stat_manager' =>$total_earned_advanc_stat_manager,
							'unearned_adv_stat_mang' =>$unearned_adv_stat_mang,
							'unearned_adv_mang' => $unearned_adv_mang
			               ];
			   
			  //print_r( $agentArray);exit;
			$One_Percent_of_chargebackCommisionFee=(($CommisionFee*1)/100);
			$total_payable=($CommisionFee-$One_Percent_of_chargebackCommisionFee);
			 //$agentPayment = agent::GetInsertId($agentArray);
			  DB::table('agentpayment')->insert(
                $agentArray
                );
			$paymentId = DB::getPdo()->lastInsertId();
			}
			else{
				
				//echo $agentId;exit;
			   $planFee = $webCustonmber[0]->price;
			  $time = strtotime(date("y-m-d"));
        $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
			  $agentCommisionFee = ($agentCommisionPercent/100)*$planFee;
			  //echo"$planFee";exit;
			$agentArray = [
                        'planId'=>$plan,
                        'customerId'=>  $customerId,
                        'AgentId'=>$agentId,
                        'agentPromotionId'=>$agentPromotionId,
                        'managerId'=>$agentManagerId,
                        'managerPromotionId'=>$agentManagerPromotionId,
                        'stateManagerId'=>$agentStateManagerId,
                        'stateManagerPromotionId'=>$agentStateManagerPromotionId,
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=> $agentCommisionFee,
                        'stateManagerCommission'=>$stateManagerCommisionFee,
                        'chargeBackCommision'=>null,
                        'chargeBackInstalment'=>'',
                        'managerCommission'=>$managerCommisionFee,
                        'IsAdvance'=>'NO',
                        'PaymentMode'=>$payment_mode,
                        'PaymentDate'=>date("Y-m-d"),
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$webCustonmber[0]->price,
                        'newOrRenew'=>'NEW',
                        'paymentletterDate'=>$nextPaymentDate,
                        'recurringPaymentDate'=>Carbon::now(),
                        'override_fees'=>0,
						 'totalBurialFee'=>$total_burial_fee,
						 'transction_id' => $transcation_id
			                ];
							//print_r($agentArray);exit;
             			   DB::table('agentpayment')->insert($agentArray);
			 $paymentId = DB::getPdo()->lastInsertId();
			}
			
			
		}
		//life time calculation
		
		
		/*
		else{
			//for life-time and 5years
			$customerId = customer::insertGetId($postCustomber);
		    $agentCommisionDetail = DB::table('agents')
            ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
            ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
            ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			      ->where('agentPromotion.agentPromotionId','=' ,$agentPromotionId)
            ->get();
		  $agentCommisionDetail =   json_decode($agentCommisionDetail);
		 // print_r( $agentCommisionDetail[0]->FirstYrComRate);exit;
		  $agentCommisionPercent  = $agentCommisionDetail[0]->FiveYrLifeComRate;
			//manager commission
			 if($agentManagerPromotionId){
			 	 $managerCommisionDetail = DB::table('agents')
				->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
        ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
				->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				->where('agentPromotion.agentPromotionId','=' ,$agentManagerPromotionId)
				->get();
		        $managerCommisionDetail =   json_decode($managerCommisionDetail);
		        //print_r( $managerCommisionDetail[0]->FirstYrComRate);exit;
				$managerCommisionPercent  = $managerCommisionDetail[0]->FiveYrLifeComRate;
				$eff_man_comm_percent =$managerCommisionPercent -  $agentCommisionPercent;
				$planFee = $request->fees;
				$managerCommisionFee = ($eff_man_comm_percent/100)*$planFee;
			 }else{
				$eff_man_comm_percent=0;
				$managerCommisionFee=0;
				$managerId=0;
			 }
			if($agentStateManagerPromotionId){
			  $stateManagerCommisionDetail = DB::table('agents')
              ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
              ->leftJoin('agentPromotion','agents.agentId', '=', 'agentPromotion.agentId')
              ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			        ->where('agentPromotion.agentPromotionId','=' ,$agentStateManagerPromotionId)
              ->get();
			  $stateManagerCommisionDetail =   json_decode($stateManagerCommisionDetail);
		      //print_r( $stateManagerCommisionDetail[0]->FirstYrComRate);exit;
		      $stateManagerCommisionPercent  = $stateManagerCommisionDetail[0]->FiveYrLifeComRate;
			  $eff_stateman_comm_percent =$stateManagerCommisionPercent - $managerCommisionPercent;
			  $planFee = $request->fees;
			  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$planFee;
			  $stateManagerId=$request->selectedStateManagerId;
			}else{
				$eff_stateman_comm_percent=0;
				$stateManagerCommisionFee=0;
				$stateManagerId=0;
			}
			 $planFee = $webCustonmber[0]->price;
			  $time = strtotime(date("y-m-d"));
              $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
			  $agentCommisionFee = ($agentCommisionPercent/100)*$planFee;
			$agentArray = [
			       'planId'=>$plan,
					'customerId'=>  $customerId,
					//'AgentId'=> $selectedAgentId,
          'AgentId'=>$agentId,,
          'agentPromotionId'=>$agentPromotionId,
          'managerId'=>$agentManagerId,
          'managerPromotionId'=>$agentManagerPromotionId,
          'stateManagerId'=>$agentStateManagerId,
          'stateManagerPromotionId'=>$agentStateManagerPromotionId,
				  'PercentOrDollar'=> 'dollar',
			    'Commission'=> $agentCommisionFee,
					'stateManagerCommission'=>$stateManagerCommisionFee,
				  'chargeBackCommision'=>null,
				  'chargeBackInstalment'=>null,
				  'managerCommission'=>$managerCommisionFee,
					'IsAdvance'=>'NO',
					'PaymentMode'=>'Card',
					'PaymentDate'=>date("Y-m-d"),
					'ModDate'=>date("Y-m-d"),
					'feeAmount'=>$webCustonmber[0]->price,
					'newOrRenew'=>'NEW',
					'paymentletterDate'=>$nextPaymentDate,
				    'recurringPaymentDate'=>Carbon::now(),
            'override_fees'=>0,
			 'totalBurialFee'=>$total_burial_fee,
			  'transction_id' => $transcation_id
			];
			   DB::table('agentpayment')->insert(
                $agentArray
                );
			 $paymentId = DB::getPdo()->lastInsertId();
		   }
			 
			 */
			 
			
	       
		    
		/* $agentArray = [
                        'planId'=>$webCustonmber[0]->planId,
                        'customerId'=>  $customerId,
                        //'AgentId'=> $selectedAgentId,
                        'AgentId'=>$webCustonmber[0]->agentId,
                        'agentPromotionId'=>'',
                        'managerId'=>'',
                        'managerPromotionId'=>'',
                        'stateManagerId'=>'',
                        'stateManagerPromotionId'=>'',
                        'stateManagerCommission'=>'',
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=>'',
                        'chargeBackCommision'=>'',
                        'chargeBackInstalment'=>'',
                        'ChargeBackInterest'=>'',
                        'ChargeBackInterestForManager'=>'',
                        'ChargeBackInterestForStateManager'=>'',
                        'managerCommission'=>'',
                        'IsAdvance'=>'',
                        'PaymentMode'=>'card',
                        'PaymentDate'=>date("y-m-d"),
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$webCustonmber[0]->price,
                        'newOrRenew'=>'NEW',
                        'paymentletterDate'=>'',
                        'recurringPaymentDate'=>'',
                        'override_fees'=>''
			               ]; */
			   //print_r( $agentArray);exit;
			

		


	 if($paymentId){
		 
		       if($webCustonmber[0]->email)
						  {
							 $email =  $webCustonmber[0]->email;
                         /*  Mail::raw('Thank you for your Global Medevac application. Your information is being processed now and your official Global Medevac Membership Welcome Kit will be mailed out soon. </br>Once received, please remember to always keep your Global Medevac membership card with you.  Welcome to the family!!', function($message)    use ($email)
                          {

                              $message->subject('Global Medvac payment Confirmation!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          }); */
						  Mail::send([], [], function ($message )use ($email,$customerId,$password)  {
                        $message->to($email)
                              ->subject('Registration Successful!')
							  ->from('globalmedivac@gmail.com', 'Global Medevac')
							 //->setBody('src="{{asset(public/images/logo.png)}}"') 
							
                            /*->setBody("Thank you for your Global Medevac application.Your information is being processed now and your official Global Medevac Membership Welcome Kit will be mailed out soon.Once received, please remember to always keep your Global Medevac membership card with you.Welcome to the family!!<br>You may now login your account at:
										http://34.94.147.238:3000/member-login-form <br> CustomerId:$customerId<br>Password:$password", 'text/html');  */
							->setBody("<b>Welcome to the Global Medevac Family!!</b><br><br>
								Thank you for joining our family! Global Medevac is the best, most comprehensive protection plan available anywhere!<br>Your new member package is being processed and should arrive to you by mail in 7-10 business days. If you should need any of our services before then, please contact us at 1-833-GET-MVAC (1-833-438-6822) or collect at 1-512-277-7560 and one of our transport coordinators will assist you with your needs.<br>Please provide your membership number which is listed below.<br>Be safe and remember, we've got you covered!! <br> <b>Member Number</b>:$customerId<br><br><b>Global Medevac Team</b>", 'text/html');
                   });
						 
						  } 
						  
             return response()->json([
             'status'=>'200',
             'ispaid'=>1,
            ]);
           }
	     else{
			 return response()->json([
             'status'=>'203',
			  'ispaid'=>0,

            ]);
      }
	}
	else {
		 return response()->json([
             'status'=>'203',
			  'ispaid'=>0,

            ]);
	}
  
  }
  /*web payment end*/
  /*add customer plan start*/
  public function addCustomerPlan(Request $request){
	  // echo "<pre>";
	  // print_r($request->all());
	  // die('aaaaaaa');
	  $fee='';
	  $burial_individual='';
	  $family_fee='';
	  $burial_family='';
	  $postArraay=array();
	  if($request->membershipType =='Individual'){
		  $fee=$request->individual_fees;
		  $burial_individual=$request->burial_fees;
		  }else{
			$family_fee=$request->family_fees;
			$burial_family=$request->burial_family;
		  }
		  if($fee!="" & $burial_individual!=""){
				  $postArray=[
				  'planName'=>$request->plan_name,
				  'frequency'=>$request->frequency_type,
				  'initiatonFee'=>$request->initiation_fees,
				  'fee'=>$fee,
				  'burial_individual'=>$burial_individual
				  ];
		  }else{
					$postArray=[
					'planName'=>$request->plan_name,
					'frequency'=>$request->frequency_type,
					'initiatonFee'=>$request->initiation_fees,
					'familyFee'=>$family_fee,
					'burial_family'=>$burial_family
					];
		  }
	  $plans_insert = DB::table('plans')->insert($postArray);

       if($plans_insert){
              return response()->json([
             'status'=>'200'

            ]);

           }
	     else{
			 return response()->json([
             'status'=>'203'

            ]);

		}
  
  }
	/*add customer plan end*/
	/*frontend customer register auto renew start*/
  public function frontendcustomerregisterautorenew(Request $request){
			 $postArray = ['isAutoRenew' => $request->auto_renew];
             $login = DB::table('frontend_customer_temp')->where('customerId',$request->customer_id)->update($postArray);

             if($login) {
               return response()->json([
                  'status'=>'200'
                ]);
             }
  }
  /*frontend customer register auto renew end*/
  /*commission calculation start*/
  function commission_calculation($planId,$agentId,$planPrice){
	  
	   $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agentId)->where ('enddate' ,NULL)->get();
		   // print_r($agent_Promotion_detail);exit;
		 $managerPormotionId = DB::table('agentmanagers')->select('managerPromotionId')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->orderBy('id','desc')->limit(1)->get();
		   // print_r(   $managerPormotionId);exit;
		 
		    if(@$managerPormotionId[0] )
			{
				$managerPormotionId = $managerPormotionId[0]->managerPromotionId;
				//echo "1";
			}
			else {
				 $managerPormotionId = 0;
				//echo "0";
			}
			//exit;
		  
		   $manager_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId', $managerPormotionId)->get();
		   //print_r( $manager_Promotion_detail);exit;
		  $stateManagerPormotionId = DB::table('agentmanagers')->select('stateManagerPromotionId')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->orderBy('id','desc')->limit(1)->get();
		 // print_r($stateManagerPormotionId);exit;
		     if(@$stateManagerPormotionId[0])
			 {
		    $stateManagerPormotionId = $stateManagerPormotionId[0]->stateManagerPromotionId;
			//echo"1";
			 }
			 else {
				$stateManagerPormotionId = 0; 
				//echo"0";
			 }
			//exit; 
		  /*$sate_mang__detail=DB::select( DB::raw("SELECT * FROM `agentPromotion` where agentId=".$stateManagerPormotionId." AND enddate IS null") );*/
		    $sate_mang__detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId',  $stateManagerPormotionId)->get();
	 if($planId == 1){
				         // $agent_Promotion_detail[0]->agentPromotionId;
                        $overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverrideCommisionAnual')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->get());	
						   if($overrideCommAnual[0]->OverrideCommisionAnual){
							   $FirstYearCommission = $overrideCommAnual[0]->OverrideCommisionAnual;
						   }
						   else {
							    $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
								$FirstYearCommission= $agtFstCommRat[0]->FirstYrComRate; 
						   }
                    
					
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
					
						  $chargeback = $planPrice*6;
						
                          $ChargeBackInterestForStateManager = 0;
						  //if()
						  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
						  $chargeBackCommision =  $CommisionFee ;
						  $ChargeBackInterest= $CommisionFee*0.01*6;
						  $chargeBackInstalment = 6;
						  $MonthCounter = 1;
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						  $IsAdvance='YES';
						  if(@$manager_Promotion_detail[0]==''){
						  $CommisionFeeForManager = 0;
						   $managerPormotionId = 0;
						   $ChargeBackInterestForManager = '';
					      }
					    else {
						 // echo "0";
						 
						 $mngCommRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $manager_Promotion_detail[0]->level)->get());
						 $eff_man_comm_percent =$mngCommRate[0]->FirstYrComRate -  $FirstYearCommission;
						 if($eff_man_comm_percent>0){
							 $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 }
						 else{
							 $CommisionFeeForManager =0;
						 }
						 //$CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;
                         
						  $ChargeBackInterestForManager = $CommisionFeeForManager*0.01*6;
					    }
						
						//comm calculation for state manager
						if(@$sate_mang__detail[0]==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
						  $ChargeBackInterestForStateManager = 0;
					      }
					  else {
						  
						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FirstYrComRate -  $mngCommRate[0]->FirstYrComRate;
						  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$chargeback;
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
						  $ChargeBackInterestForStateManager = $stateManagerCommisionFee*0.01*6;
					  }
					  
				//exit;
						
			 
			
			  }
			  elseif($planId == 2){
				  
				  
				            $overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverrideCommisionAnual')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->get());	
						   if($overrideCommAnual[0]->OverrideCommisionAnual){
							   $FirstYearCommission = $overrideCommAnual[0]->OverrideCommisionAnual;
						   }
						   else {
							    $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
								$FirstYearCommission = $agtFstCommRat[0]->FirstYrComRate;
							   
						   }
				         // $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
						  //print_r( $agtFstCommRat);exit;
						   $chargeBackCommision = '' ;
			              $ChargeBackInterest= 0;
                          $ChargeBackInterestForManager = 0;
                          $ChargeBackInterestForStateManager=0;
						  $chargeBackInstalment = '';
						  $MonthCounter = 12;
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
						  $chargeback = $planPrice;						
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
						  $IsAdvance='NO';
						 if(@$manager_Promotion_detail[0]  ==''){
						 // echo "1";
						  $CommisionFeeForManager = 0;
						   $managerPormotionId = 0;
					      }
					    else {
						 // echo "0";
						 
						 $mngCommRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $manager_Promotion_detail[0]->level)->get());
						// print_r($mngCommRate);exit;
						 $eff_man_comm_percent =$mngCommRate[0]->FirstYrComRate -  $FirstYearCommission;
						 if($eff_man_comm_percent>0){
							 $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 }
						 else{
							 $CommisionFeeForManager =0;
						 }
						// $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;

					    }
						
						//comm calculation for state manager
						if(@$sate_mang__detail[0]==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
					      }
					  else {
						// print_r( $sate_mang__detail[0]->level);exit;
						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  // print_r($staMngCommiRate[0]->FirstYrComRate);
						  // print_r($mngCommRate[0]->FirstYrComRate);exit;
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FirstYrComRate -  $mngCommRate[0]->FirstYrComRate;
						  //echo $eff_stateman_comm_percent;exit;
						  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$chargeback;
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
					  }
			  }
			   elseif($planId == 3 ||$planId == 4 ){
				     $overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverRideCommisionLifeTime')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->get());	
						   if($overrideCommAnual[0]->OverRideCommisionLifeTime){
							   $FirstYearCommission = $overrideCommAnual[0]->OverRideCommisionLifeTime;
						   }
						   else {
							  $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FiveYrLifeComRate')->where(   'LevelName', $agent_Promotion_detail[0]->level)->get());
								$FirstYearCommission = $agtFstCommRat[0]->FiveYrLifeComRate;
							   
						   }
				  
						  $chargeBackCommision='';
			              $ChargeBackInterest='';
                          $ChargeBackInterestForManager='';
                          $ChargeBackInterestForStateManager='';
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
						  $chargeback = $planPrice;
						   //code  for 0 levid id agent
						   $IsAdvance='NO';
						    $chargeBackInstalment = '';
							if($planId == 3){
								$MonthCounter = 60;
							}else{
								$MonthCounter = '';
							}
						  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						 // $chargebackCommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 // $chargebackCommisionFeeForStateManager = ($eff_stateman_comm_percent/100)*$chargeback;
						 if(@$manager_Promotion_detail[0] == ''){
						 // echo "1";
						  $CommisionFeeForManager = 0;
						   $managerPormotionId = 0;
					      }
					    else {
						 // echo "0";
						 
						 $mngCommRate = json_decode(DB::table('agentlevels')->select('FiveYrLifeComRate')->where('LevelName', $manager_Promotion_detail[0]->level)->get());
						 $eff_man_comm_percent =$mngCommRate[0]->FiveYrLifeComRate -  $FirstYearCommission;
						 if($eff_man_comm_percent>0){
							$CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback; 
						 }
						 else{
							 $CommisionFeeForManager = 0; 
						 }
						 
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;

					    }
						
						//comm calculation for state manager
						if(@$sate_mang__detail[0] ==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
					      }
					  else {
						 
						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FiveYrLifeComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FiveYrLifeComRate -  $mngCommRate[0]->FiveYrLifeComRate;
						  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$chargeback;
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
					  }
			  }
                    		  	  
			   return array('charge_back'=> $chargeback,'ChargeBackInterestForStateManager'=>$ChargeBackInterestForStateManager,'CommisionFee'=>$CommisionFee,'chargeBackCommision'=>$chargeBackCommision,'ChargeBackInterest'=>$ChargeBackInterest ,'chargeBackInstalment'=>$chargeBackInstalment,'MonthCounter'=>$MonthCounter,'CommisionFeeForManager'=>$CommisionFeeForManager,'stateManagerCommisionFee'=>$stateManagerCommisionFee,'agentPormotionId'=>$agent_Promotion_detail[0]->agentPromotionId,'managerPormotionId'=>$managerPormotionId,'stateManagerPormotionId'=>$stateManagerPormotionId,'IsAdvance'=>$IsAdvance); 
  }
  /*commission calculation end*/
  /* promotion fetch start*/
  public function fetch_promotion_id($agentId){
	  $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agentId)->where ('enddate' ,NULL)->get();
		    //print_r($agent_Promotion_detail);exit;
		 $managerPormotionId = DB::table('agentmanagers')->select('managerPromotionId')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->orderBy('id','desc')->limit(1)->get();
		   // print_r(   $managerPormotionId);exit;
		 
		    if(@$managerPormotionId[0] )
			{
				$managerPormotionId = $managerPormotionId[0]->managerPromotionId;
				//echo "1";
			}
			else {
				 $managerPormotionId = 0;
				//echo "0";
			}
			//exit;
		  
		   $manager_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId', $managerPormotionId)->get();
		   //print_r( $manager_Promotion_detail);exit;
		  $stateManagerPormotionId = DB::table('agentmanagers')->select('stateManagerPromotionId')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->orderBy('id','desc')->limit(1)->get();
		 // print_r($stateManagerPormotionId);exit;
		     if(@$stateManagerPormotionId[0])
			 {
		    $stateManagerPormotionId = $stateManagerPormotionId[0]->stateManagerPromotionId;
			//echo"1";
			 }
			 else {
				$stateManagerPormotionId = 0; 
				//echo"0";
			 }
			//exit; 
		  /*$sate_mang__detail=DB::select( DB::raw("SELECT * FROM `agentPromotion` where agentId=".$stateManagerPormotionId." AND enddate IS null") );*/
		    $sate_mang__detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId',  $stateManagerPormotionId)->get();
			 return array('agentPormotionId'=>$agent_Promotion_detail[0]->agentPromotionId,'managerPormotionId'=>$managerPormotionId,'stateManagerPormotionId'=>$stateManagerPormotionId,'agent_level'=>$agent_Promotion_detail[0]->level); 
  }
  /*fetch promotion end*/
  /*Numeric check start*/
  public function isNumericCheck ($number){
	  if(is_numeric($number)AND $number >0){
		  return $number;
	  }
	  else {
		  return 0;
	  }
  }
  /*numeric check end*/
  /*create random number start*/
  function random_num($size) {
	$alpha_key = '';
	$keys = range('A', 'Z');
	
	for ($i = 0; $i < 2; $i++) {
		$alpha_key .= $keys[array_rand($keys)];
	}
	
	$length = $size - 2;
	
	$key = '';
	$keys = range(0, 9);
	
	for ($i = 0; $i < $length; $i++) {
		$key .= $keys[array_rand($keys)];
	}
	
	return $alpha_key . $key;
}
/*create random number end*/
/*for lifetime annual 5years register in web form start*/
public function lifetimeFee(Request $request){
	      
	        /*  print_r($request->all());
			exit('aaa');  */ 
		  
		 $webCustonmber  = DB::table('frontend_customer_temp')
					->select('firstName','middleName','LastName','DOB','address1','address2','mailing_address1','mailing_address2','email','city','city1','country','country1','clientType','zip','zip1','state','state1','spouseFirstName','spouseMiddleName','spouseLastName','price','cellPhone','spouseDOB','dependent1FirstName','dependent1MiddleName','dependent1LastName','dependent1DOB','planId','agentId','membershipDate','dependent2FirstName','dependent2MiddleName','Dependent2LastName','dependent2DOB','modDate','InitiationFees','isBurial','burialCity','burialState')->where('customerId',$request->customer_id)
					->get();
		 
		 // echo "<pre>";
		 //print_r($webCustonmber);exit;  
		 //$frontendCustomer_id = DB::table('customers')->insertGetId($webCustonmber);
		 
		 $password = $this->random_num(8);
		   $customerId =$request->customerId;
		 
		 $postCustomber = [
                              'firstName'      => $webCustonmber[0]->firstName,
							  'middleName'      => $webCustonmber[0]->middleName,
                              'LastName'      => $webCustonmber[0]->LastName,
                              'DOB'=>$webCustonmber[0]->DOB,
                              'city'      => $webCustonmber[0]->city,
                              'city1'      => $webCustonmber[0]->city,
                              'state'      => $webCustonmber[0]->state,
                              'state1'      =>$webCustonmber[0]->state,
                              'zip'  =>$webCustonmber[0]->zip,
                              'zip1'      => $webCustonmber[0]->zip,
							  'isPaidCustomer'=>'1',
                              //'groupCode'=>$request->groupId,
                              'groupId'=>'1002',
                              'isActive'=>'Yes',
                              'password'      => $password,
                              'email' =>$webCustonmber[0]->email,
                              'companyName'      =>'',
                              //'writing_agent'      => $managerId,
                              'writing_agent'      => '',

                              'agentId'  =>  $webCustonmber[0]->agentId,
                              //'agentId'  =>  $agentId,
                              //'agent_manager' =>$managerId,
                              'agent_manager'=>'',
                              'country'  =>$webCustonmber[0]->country,
                              'country1'  =>$webCustonmber[0]->country,
                              'cellPhone'  =>$webCustonmber[0]->cellPhone,
                              'clientType'  =>$webCustonmber[0]->clientType,
                              'mobile2'  =>'',
                              'planId'  =>$webCustonmber[0]->planId,
                              'address1'  =>$webCustonmber[0]->address1,
                              'address2'  => $webCustonmber[0]->address1,
                              'mailing_address1'  =>$webCustonmber[0]->address1,
                              'mailing_address2'  => $webCustonmber[0]->address1,
                              'dependent1FirstName' =>$webCustonmber[0]->dependent1FirstName,
                              'dependent2FirstName' =>$webCustonmber[0]->dependent2FirstName,
                              'dependent3FirstName' =>'',
							  'dependent1MiddleName' =>$webCustonmber[0]->dependent1MiddleName,
                              'dependent2MiddleName' =>$webCustonmber[0]->dependent2MiddleName,
                              'dependent3MiddleName' =>'',
                              'dependent1LastName' =>$webCustonmber[0]->dependent1LastName,
                              'dependent2LastName' =>$webCustonmber[0]->Dependent2LastName,
                              'dependent3LastName' =>'',
                              'dependent1DOB' =>$webCustonmber[0]->dependent1DOB,
                              'dependent2DOB' =>'',
                              'spouseFirstName'  =>$webCustonmber[0]->spouseFirstName,
							  'spouseMiddleName'  =>$webCustonmber[0]->spouseMiddleName,
                              'spouseLastName'  =>$webCustonmber[0]->spouseLastName,
                              'spouseDOB' =>$webCustonmber[0]->spouseDOB,
                              'initiationFee'=>$webCustonmber[0]->InitiationFees,
                              'burialFee'=>'',
							  'seminarFee'=>'',
                              'created_at' =>Carbon::now(),
                              'membershipDate' =>date("y-m-d"),
                              'modDate' =>date("y-m-d"),
                              'modBy' =>'Member',
                              'note'=>'',
							  
							  ];
							//echo"<pre>";  print_r($postCustomber);exit;
		  $customerId = DB::table('customers')->insertGetId(
                $postCustomber
                );
				/* 
				print_r($customerId);
				exit; */
				//print_r($webCustonmber[0]->agentId);exit;
				$agent_detail = $this->fetch_promotion_id($webCustonmber[0]->agentId);
				//print_r($agent_detail);exit;
				$postAgentPayment = [
			  'PaymentMode' =>'',
			  'planId' =>$webCustonmber[0]->planId,
			  'customerId' =>$customerId,
			  'PaymentDate'=>'',
			  'AgentId' =>$webCustonmber[0]->agentId,
			  'ModDate'=>Carbon::now(),
			  'payeeName'=>'',
			  'orderNumber'=>'',
			  'checkMoneyDate'=>'',
              'recurringPaymentDate'=>'',
		     'totalBurialFee'=>'',
			 'feeAmount' =>$webCustonmber[0]->price,
			 'override_fees'=>$webCustonmber[0]->price,
			 'agentPromotionId'=>$agent_detail['agentPormotionId'],
			 'managerPromotionId'=>$agent_detail['managerPormotionId'],
			 'stateManagerPromotionId'=>$agent_detail['stateManagerPormotionId'],
			 'Commission'=>0,
			 'managerCommission'=>0,
			 'stateManagerCommission'=>0,
			 'chargeBackCommision'=>0,
			 'ChargeBackInterest'=>0,
             'ChargeBackInterestForManager'=>0,
             'ChargeBackInterestForStateManager'=>0,
			 'chargeBackInstalment'=>null,
			 'IsAdvance'=>'NO',
			 'newOrRenew'=>'NEW',
			 'paymentletterDate'=>''
			 
			
        ];
        //echo "<pre>"; print_r($postArray);exit;
		//echo"<pre>";  print_r($postAgentPayment);exit;
      $agentPaimentDone =DB::table('agentpayment')->where('paymentId',$request->agentPaymentId)->insert($postAgentPayment);
				
				
				if($customerId){
					
					    if($webCustonmber[0]->email)
						  {
							 $email =  $webCustonmber[0]->email;
                        
						  
						    Mail::send([], [], function ($message )use ($email,$customerId,$password)  {
                        $message->to($email)
                              ->subject('Registration Successful!')
							  ->from('globalmedivac@gmail.com', 'Global Medevac')
                           /* ->setBody("Thank You for registration <br>You may now login your account at:
										http://35.235.80.37:3000//member-login-form <br> CustomerId:$customerId<br>Password:$password", 'text/html'); */
							->setBody("<b>Welcome to the Global Medevac Family!!</b><br><br>
										Thank you for joining our family! Global Medevac is the best, most comprehensive protection plan available anywhere!
										Your application is being processed and a Global Medevac representative will be contacting you regarding your payment. If you should need any of our services before then, please contact us at 1-833-GET-MVAC (1-833-438-6822) or collect at 1-512-277-7560 and one of our transport coordinators will assist you with your needs.
										<br>Be safe and remember, we've got you covered!!<br><br><b>Global Medevac Team</b>", 'text/html');
											
                   });
						 
						  }    
					
					
					
				 return response()->json([
             'status'=>'200',
             
            ]);
           }
	     else{
			 return response()->json([
             'status'=>'203',
			  ]);
      }
					
				
				
   } 
   /*for lifetime annual 5years register in web form end*/
   
   /*group detail start */
    public function groupDetail(Request $request){
		 
		 //print_r($request->all());exit;
		 $groupDetails = DB::TABLE('groups') 
				->select('groupName','status','groupId','companyName','groupCode','membershiptype','created_at','phone','address','address2','city','state','zip','country','basegroup','contactperson')
				
				->where('groups.groupId',$request->groupId)
				->get();
				/* echo "<pre>";
				print_r($groupDetails);
				exit; */
		 if($groupDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'groupDetails'=>$groupDetails
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'groupDetails'=>$groupDetails
              ]);
          }	
			
	 }
	 /*group details end */
	 /*update group start*/
	  public function updateGroup(Request $request){
		 
		 //print_r($request->all());exit;
		 $postAgentPayment = [
			  
			  'status' =>$request->status,
			  
			 
			
        ];
			//print_r($postAgentPayment);exit;
		 $updategroupDetails = DB::TABLE('groups') 
				->where('groups.groupId',$request->groupId)
				->update($postAgentPayment);
				
				//print_r($updategroupDetails);exit;
				
				
		 if($updategroupDetails)
          {
              return response()->json([
                    'status'=>'200',
                    'updategroupDetails'=>$updategroupDetails
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'updategroupDetails'=>$updategroupDetails
              ]);
          }	
			
	 }
	/*update group end*/
	/*group member list start*/
	 public function groupMemberList(Request $request){
		 /* echo "<pre>";
		 print_r($request->all());
		 die('aaaa'); */
		 $groupMemberList = DB::TABLE('groups')->SELECT('customers.customerId as MemberId', DB::raw('concat(customers.firstName," ",customers.LastName) as CustomerName'),'customers.membershipDate','plans.planName','plans.fee', DB::raw('concat(agents.firstName," ",agents.lastName) as AgentName'))
				->leftjoin('customers','customers.groupId','=','groups.groupId')
				->leftjoin('agents','agents.agentId','=','customers.agentId')
				->leftjoin('plans','plans.planId','=','customers.planId')
				->where('groups.groupId',$request->groupId)
				->get();
		 if($groupMemberList)
          {
              return response()->json([
                    'status'=>'200',
                    'groupMemberList'=>$groupMemberList
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'203',
                    'groupMemberList'=>$groupMemberList
              ]);
          }	
			
	 }	
		/*group member list end*/
		/*FREST INSTALLMENYT METHOD start*/
	    public function CustomberInstallmentPayment(Request $request){

	 
	    
							$agent_id='';
							$monthCounter='';
							$plan_Id='';
							$managerId='';
							$stateManagerId='';
							$agentPromotionId='';
							$managerPromotionId='';
							$recurringPaymentDate='';
							$stateManagerPromotionId=0;
							$paymentletterDate='';
							$chargeBackInstalment='';
							$feeAmount="";
							$CommisionFee_ins ="";
							$CommisionFeeForManager_ins = '';
							$stateManagerCommisionFee_ins ='';
							$total_earned_advance = 0;
							$unearned_advance = 0;
							$total_earned_advanc_managere = 0;
							$total_earned_advanc_stat_manager = 0;
							$unearned_adv_stat_mang = 0;
							$unearned_adv_mang = 0;
      $customer_details=DB::select( DB::raw("select AgentId,agentPromotionId,managerId,feeAmount,paymentId,managerPromotionId,stateManagerId,stateManagerPromotionId, planId,MonthCounter,paymentletterDate,recurringPaymentDate,chargeBackInstalment,total_earned_advance,
      unearned_advance,	total_earned_advanc_managere,total_earned_advanc_stat_manager,unearned_adv_mang,unearned_adv_stat_mang   from `agentpayment` WHERE `agentpayment`.`customerId`=$request->customerId ORDER BY paymentId DESC Limit 0,1 ") );
      // echo "<pre>";
	 // print_r($customer_details[0]->unearned_adv_stat_mang);
	//  exit;
	   //die('aaaaa');
	  foreach($customer_details as $row){
		 $agent_id=$row->AgentId;
         $agentPromotionId=$row->agentPromotionId;
         $managerPromotionId=$row->managerPromotionId;
         $stateManagerPromotionId=$row->stateManagerPromotionId;
         $monthCounter=$row->MonthCounter;
         $recurringPaymentDate=$row->recurringPaymentDate;
         $plan_Id=$row->planId;
		 $feeAmount=$row->feeAmount;
         $managerId=$row->managerId;
         $stateManagerId=$row->stateManagerId;
         $paymentletterDate=$row->paymentletterDate;
		 $chargeBackInstalment=$row->chargeBackInstalment;
		 //$unearnedadv=$row->unearned_advance;
       }
	      
	      $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agent_id)->where ('enddate' ,NULL)->get();
		   $manager_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId', $managerPromotionId)->get();
		   $planDetails =  json_decode(DB::table('plans')->select('planId','frequency')->where('planId',$plan_Id)->get());
	    if($planDetails[0]->frequency == 'Monthly'){
			         
				     $previousPaymentDate=strtotime($paymentletterDate);
			         $nextPaymentDate = date('Y-m-d', strtotime('+1 month', $previousPaymentDate));   
					$memberType =  DB::table('customers')->select('clientType')->where('customerId',$request->customerId)->get();
			        
					$overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverrideCommisionAnual')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->get());
						   if($overrideCommAnual[0]->OverrideCommisionAnual){
							   $FirstYearCommission = $overrideCommAnual[0]->OverrideCommisionAnual;
						   }
						   else {
							    $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
								$FirstYearCommission= $agtFstCommRat[0]->FirstYrComRate;

						   }
						if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $feeAmount*6;
						}
						else {
							 $chargeback = $feeAmount*6;
						}
                          $ChargeBackInterestForStateManager = 0;
						  /*
						    if($chargeBackInstalment == 6){
								
							}
							else {

							}
						  */
						 // if($chargeBackInstalment == 6){
							  //echo "hi new chargeback is return";
							  //exit;
							  //$one_percent_interest_payble=$unearnedadv/100;
							    if($chargeBackInstalment == 6){
								   $chargeBackInstalment =1;
								   
							   }
							   else {
								   $chargeBackInstalment = $chargeBackInstalment+1;
								   
							   }
							  //$chargeBackInstalment = $chargeBackInstalment+1;
							     
							  $MonthCounter=($monthCounter+1);
							  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
							  //$one_percent_interest=$unearnedadv/100;
							   if($chargeBackInstalment == 1){
								   $CommisionFee_ins = $CommisionFee;
							  $chargeBackCommision = $CommisionFee ;
							  //$one_percent_interest_payble=0;
							   }
							   else {
								    $CommisionFee_ins = 0;
							  $chargeBackCommision = 0;
							  //$one_percent_interest_payble=$one_percent_interest;
							   }
							  
							 // $rest_install = (6 - $monthCounter);
							 
							  $total_earned_advance = ($FirstYearCommission/100)* $feeAmount*$MonthCounter;
							  $unearned_advance = ($FirstYearCommission/100)* $feeAmount*(6-$chargeBackInstalment);
							  $ChargeBackInterest= $customer_details[0]->unearned_advance*0.01;   
							  
							  
						  //}
						  /*
						  else {
							  $chargeBackInstalment =$chargeBackInstalment+1;
							  $MonthCounter=($monthCounter+1);
							  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
							  $CommisionFee_ins = '';
							  $chargeBackCommision =  $CommisionFee ;
							  $total_earned_advance = ($FirstYearCommission/100)* $feeAmount*$MonthCounter;
							  $unearned_advance =  ($FirstYearCommission/100)* $feeAmount*(6-$chargeBackInstalment);
							  //$ChargeBackInterest= $CommisionFee*0.01*6;
							   $ChargeBackInterest= $customer_details[0]->unearned_advance*0.01;  
						  }
						  */
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						  $IsAdvance='YES';
						  if(@$manager_Promotion_detail[0]==''){
						  $CommisionFeeForManager = 0;
						   $managerPormotionId = 0;
						   $ChargeBackInterestForManager = '';
					      }
					    else {
						 // echo "0";

						 $mngCommRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $manager_Promotion_detail[0]->level)->get());
						  //  echo $mngCommRate[0]->FirstYrComRate;
							//  $FirstYearCommission; exit;
						 $eff_man_comm_percent =$mngCommRate[0]->FirstYrComRate -  $FirstYearCommission;
						 if($eff_man_comm_percent>0){
							 $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
							  if($chargeBackInstalment == 1){
								 $CommisionFeeForManager_ins =  ($eff_man_comm_percent/100)*$chargeback;
							  }
							  else {
								   $CommisionFeeForManager_ins = '';
							  }
							
						 }
						 else{
							 $CommisionFeeForManager =0;
						 }
						 //$CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;
						  $total_earned_advanc_managere = ($eff_man_comm_percent/100)* $feeAmount*$chargeBackInstalment;
						  $unearned_adv_mang = ($eff_man_comm_percent/100)* $feeAmount*(6-$chargeBackInstalment);

						  //$ChargeBackInterestForManager = $CommisionFeeForManager*0.01*6;
						  $ChargeBackInterestForManager = $customer_details[0]->unearned_adv_mang*0.01;
					    }

						//comm calculation for state manager
						if(@$sate_mang__detail[0]==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
						  $ChargeBackInterestForStateManager = 0;
					      }
					  else {

						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FirstYrComRate -  $mngCommRate[0]->FirstYrComRate;
						  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$chargeback;
						    if($chargeBackInstalment == 1){
								 $stateManagerCommisionFee_ins =  ($eff_stateman_comm_percent/100)*$chargeback;
							  }
							  else {
								    $stateManagerCommisionFee_ins = '';
							  }
						
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
						  //$ChargeBackInterestForStateManager = $stateManagerCommisionFee*0.01*6;
						   $total_earned_advanc_stat_manager = ($eff_stateman_comm_percent/100)* $feeAmount*$chargeBackInstalment;
						  $unearned_adv_stat_mang =($eff_stateman_comm_percent/100)* $feeAmount*(6-$chargeBackInstalment);
						  $ChargeBackInterestForStateManager =$customer_details[0]->unearned_adv_stat_mang*0.01;
					  }

					
			 
			    
			  }
			  $postArray = [
					'planId'=>$plan_Id,
					'AgentId'=>$agent_id,
					'PaymentMode' => $request->paymentmethod,
					'customerId' => $request->customerId,
					'PaymentDate'=>Carbon::now(),
					'newOrRenew'=>'installment',
					'feeAmount'=>$request->customerPayAmount,
					'totalBurialFee'=>$request->totalBurialFee,
					'stateManagerPromotionId'=>$stateManagerPormotionId,
					'Commission'=>$CommisionFee_ins,
					'managerCommission'=>$CommisionFeeForManager_ins,
					'stateManagerCommission'=>$stateManagerCommisionFee_ins,
					'chargeBackCommision'=>$CommisionFee_ins,
					'ChargeBackInterest'=>$ChargeBackInterest,
					'ChargeBackInterestForManager'=>$ChargeBackInterestForManager,
					'ChargeBackInterestForStateManager'=>$ChargeBackInterestForStateManager,
					'chargeBackInstalment' => $chargeBackInstalment,
					'MonthCounter'=>$MonthCounter,
					'IsAdvance'=>$IsAdvance,
					'paymentletterDate'=>$nextPaymentDate,
                    'managerId' =>$managerId ,
                    'managerPromotionId' =>$managerPromotionId,
					'total_earned_advance' =>$total_earned_advance,
					'unearned_advance' => $unearned_advance,
					'total_earned_advanc_managere' =>$total_earned_advanc_managere,
					'total_earned_advanc_stat_manager' =>$total_earned_advanc_stat_manager,
					'unearned_adv_stat_mang' =>$unearned_adv_stat_mang,
					'unearned_adv_mang' => $unearned_adv_mang
					];
					 // print_r($postArray);exit;
			  $customerInstallmentPayment=DB::table('agentpayment')->insert($postArray);
				  if($customerInstallmentPayment){
					return response()->json([
					'status'=>'200'
				   ]);
					
				  }
		}
   /*instrallment function end */	
			
		

}
