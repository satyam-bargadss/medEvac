<?php

namespace App\Http\Controllers\API;
//use Request;
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
					 $customers=DB::select( DB::raw("select cu.customerId, CONCAT(cu.firstName,' ',cu.LastName) as
					 customer_name,cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 concat(groups.groupCode,' ',groups.groupId) AS `groupCode`,cu.ModBy, (CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId` WHERE cu.isActive = 'Yes' order by `cu`.`customerId` desc") );
			}
			else{
				 $customers=DB::select( DB::raw("select cu.customerId, CONCAT(cu.firstName,' ',cu.LastName) as
					 customer_name,cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 concat(groups.groupCode,' ',groups.groupId) AS `groupCode`,cu.ModBy,(CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId` WHERE cu.isActive = 'No' order by `cu`.`customerId` desc") );
			}
		}
		else if($request->country != ''){
			if($request->country == 'USA')
			{
			$customers=DB::select( DB::raw("select cu.customerId, CONCAT(cu.firstName,' ',cu.LastName) as
					 customer_name,cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 concat(groups.groupCode,' ',groups.groupId) AS `groupCode`,cu.ModBy,(CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId` WHERE cu.country = 'USA' order by `cu`.`customerId` desc") );
			}
			else{
				$customers=DB::select( DB::raw("select cu.customerId, CONCAT(cu.firstName,' ',cu.LastName) as
					 customer_name,cu.email , cu.cellPhone as cellPhone, date_format(cu.DOB,'%m-%d-%Y')as DOB, cu.country,
					 concat(groups.groupCode,' ',groups.groupId) AS `groupCode`,cu.ModBy,(CASE
WHEN cu.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status from `customers` as cu
					 left join `groups` ON `cu`.`groupId`=`groups`.`groupId` WHERE cu.country != 'USA' order by `cu`.`customerId` desc") );

			}
		}
    else
		{
	  $customers=DB::select( DB::raw("select customers.customerId, CONCAT(customers.firstName,' ',customers.LastName) as customer_name,customers.email , customers.cellPhone as cellPhone, date_format(customers.DOB,'%m-%d-%Y')as DOB,
			 customers.country,concat(groups.groupCode,' ',groups.groupId) AS `groupCode`,customers.ModBy,(CASE
WHEN customers.isActive='Yes' THEN 'Active'
ELSE 'InActive'
END
)active_status
             from `customers`
			left join `groups` ON `customers`.`groupId`=`groups`.`groupId`
			order by `customers`.`customerId` desc") );
		}
      $totalCustomers = DB::table('customers')->select('customerId','firstName','LastName','DOB',
      'country','created_at')->orderBy('customerId', 'desc')->get();

	   $activeUsers = DB::table('customers')->where('isActive', 'Yes')->count();
	   $inActiveUsers = DB::table('customers')->where('isActive', 'No')->count();
     $usCustomber = DB::table('customers')->where('country', 'USA')->count();
	   $internationalCustomber = DB::table('customers')->where('country', '!=',  'USA' )->count();
     $insPayment=DB::select( DB::raw("select cu.customerId from `customers` as cu left join `agentpayment` ON cu.customerId=`agentpayment`.`customerId`
     WHERE  `agentpayment`.`recurringPaymentDate` BETWEEN CURDATE() AND (CURDATE() + INTERVAL 30 DAY) AND cu.isActive='Yes'
     order by `cu`.`customerId` desc") );
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

	//function Dashboard

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

     /**
      * Show the form for creating a new resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function register_basic1(Request $request)
     {

		  //echo "<pre>";
	    //print_r($request->all());
      $selectedAgentId = $request->selectedAgentId['value'];
	  //echo $selectedAgentId;exit;
      $agentManagerPromotionId="";
	  $agentPromotionId="";
      $agentStateManagerPromotionId="";
      $agent_PromotionId=DB::select( DB::raw("SELECT agentwisePromotioncode.agentPromotionId FROM `agentwisePromotioncode` Where agentwisePromotioncode.agentId=".$request->selectedAgentId['value']) );
      foreach ($agent_PromotionId as $key => $value) {
            // code...
            $agentPromotionId=$value->agentPromotionId;
            //echo $agentPromotionId;
            //die('aaaa');
            $agentManager_PromotionId =  DB::select( DB::raw("SELECT am.managerId
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
            }
      }


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

         $postCustomber = [
                              'firstName'      => $request->firstname,
                              'LastName'      => $request->lastname,
                              'DOB'=>$request->dob,
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
                              'password'      => $request->password,
                              'email' =>$request->email,
                              'companyName'      => $request->companyname,
                              //'writing_agent'      => $managerId,
                              'writing_agent'      => $agentManagerPromotionId,
                              'agentId'  =>  $selectedAgentId,
                              'agentPromotionId'  =>  $agentPromotionId,
                              //'agent_manager' =>$managerId,
                              'agent_manager'=>$agentManagerPromotionId,
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
                              'dependent1LastName' =>$request->customerRegisterFormDependantLastName,
                              'Dependent2LastName' =>$request->customerRegisterFormDependantLastName1,
                              'dependent3LastName' =>$request->customerRegisterFormDependantLastName2,
                              'dependent1DOB' =>$request->customerRegisterFormDob,
                              'dependent2DOB' =>$request->customerRegisterFormDob1,
                              'dependent3DOB' =>$request->customerRegisterFormDob2,
                              'dependent4DOB' =>$request->customerRegisterFormDob3,
                              'spouseFirstName'  =>$request->spouse_first_name,
                              'spouseLastName'  =>$request->spouse_last_name,
                              'spouseDOB' =>$request->familyDateOfBirth,
                              'initiationFee'=>$request->initiationFee,
							  'burialFee'=>$request->burialFee,
                              'created_at' =>Carbon::now(),
                              'membershipDate' =>date("y-m-d"),
                              'modDate' =>date("y-m-d"),
                              'modBy' =>$request->aurthName,
							  'note' => $request->note
                          ];
		// print($request->plan);exit;
		$plan = $request->plan;
		if($plan == '1'||$plan =='2')
	    {
			//echo $plan;exit;
		 $customerId = customer::insertGetId($postCustomber);
		 $agentCommisionDetail = DB::table('agents')
            ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
            ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
            ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			->where('agentwisePromotioncode.agentPromotionId','=' ,$agentPromotionId)
            ->get();
      //echo "<pre>"; print_r($agentCommisionDetail);
      //die('aaaaa');
		  $agentCommisionDetail =   json_decode($agentCommisionDetail);

		  $agentCommisionPercent  = $agentCommisionDetail[0]->FirstYrComRate;
		    //print_r( $agentCommisionPercent);exit;

			 if($agentManagerPromotionId !=''){
                  //echo 		$agentManagerPromotionId;exit;
			 	 $managerCommisionDetail = DB::table('agents')
				->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
        ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
				->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				->where('agentwisePromotioncode.agentPromotionId','=' ,$agentManagerPromotionId)
				->get();
		        $managerCommisionDetail =   json_decode($managerCommisionDetail);
		        //print_r( $managerCommisionDetail[0]->FirstYrComRate);exit;
				$managerCommisionPercent  = $managerCommisionDetail[0]->FirstYrComRate;
				$eff_man_comm_percent =$managerCommisionPercent -  $agentCommisionPercent;
				$planFee = $request->fees;
				$managerCommisionFee = ($eff_man_comm_percent/100)*$planFee;
        //echo $managerCommisionFee;die('bbbb');
			 }else{
				$eff_man_comm_percent=0;
				$managerCommisionFee=0;
				$managerId=0;
			 }

			/*$eff_man_comm_percent=0;
				$managerCommisionFee=0;
				$managerId=0;*/

			if($agentStateManagerPromotionId!=''){
          //echo 		$agentManagerPromotionId;exit;
			  $stateManagerCommisionDetail = DB::table('agents')
              ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
              ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
              ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			        ->where('agentwisePromotioncode.agentPromotionId','=' ,$agentStateManagerPromotionId)
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
				$eff_stateman_comm_percent=0;
				$stateManagerCommisionFee=0;
				$stateManagerId=0;

			}

			/*$eff_stateman_comm_percent=0;
				$stateManagerCommisionFee=0;
				$stateManagerId=0;*/
				//echo"asdsds";exit;
				//print_r($plan);exit;
			if($plan == '1')
			{
				//echo"abhi";exit;
			  $planFee = $request->fees;
			  $time = strtotime(date("y-m-d"));
        $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
			  $chargeback = $planFee*6;
			  $chargebackCommisionFee = ($agentCommisionPercent/100)*$chargeback;
			  $chargebackCommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
			  $chargebackCommisionFeeForStateManager = ($eff_stateman_comm_percent/100)*$chargeback;

			//inserting
			$agentArray = [
                        'planId'=>$plan,
                        'customerId'=>  $customerId,
                        //'AgentId'=> $selectedAgentId,
                        'AgentId'=>$agentPromotionId,
                        'managerId'=>$agentManagerPromotionId,
                        'stateManagerId'=>$agentStateManagerPromotionId,
                        'stateManagerCommission'=>$chargebackCommisionFeeForStateManager,
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=>$chargebackCommisionFee,
                        'chargeBackCommision'=>$chargebackCommisionFee,
                        'chargeBackInstalment'=>6,
                        'ChargeBackInterest'=>($chargebackCommisionFee*0.01*6),
                        'ChargeBackInterestForManager'=>($chargebackCommisionFeeForManager*0.01*6),
                        'ChargeBackInterestForStateManager'=>($chargebackCommisionFeeForStateManager*0.01*6),
                        'managerCommission'=>$chargebackCommisionFeeForManager,
                        'IsAdvance'=>'YES',
                        'PaymentMode'=>'',
                        'PaymentDate'=>'',
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$request->fees,
                        'newOrRenew'=>'NEW',
                        'paymentletterDate'=>$nextPaymentDate,
                        'recurringPaymentDate'=>date("Y-m-d")
			               ];
			   //print_r( $agentArray);exit;
			$One_Percent_of_chargebackCommisionFee=(($chargebackCommisionFee*1)/100);
			$total_payable=($chargebackCommisionFee-$One_Percent_of_chargebackCommisionFee);

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
			  $agentCommisionFee = ($agentCommisionPercent/100)*$planFee;
			  //echo"$planFee";exit;
			$agentArray = [
                        'planId'=>$plan,
                        'customerId'=>  $customerId,
                        'AgentId'=> $agentPromotionId,
                        'managerId'=>$agentManagerPromotionId,
                        'PercentOrDollar'=> 'dollar',
                        'Commission'=> $agentCommisionFee,
                        'stateManagerId'=>$agentStateManagerPromotionId,
                        'stateManagerCommission'=>$stateManagerCommisionFee,
                        'chargeBackCommision'=>null,
                        'chargeBackInstalment'=>null,
                        'managerCommission'=>$managerCommisionFee,
                        'IsAdvance'=>'NO',
                        'PaymentMode'=>'',
                        'PaymentDate'=>'',
                        'ModDate'=>date("Y-m-d"),
                        'feeAmount'=>$request->fees,
                        'newOrRenew'=>'NEW',
                        'paymentletterDate'=>$nextPaymentDate,
                        'recurringPaymentDate'=>Carbon::now()
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
		    $agentCommisionDetail = DB::table('agents')
            ->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
            ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
            ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			      ->where('agentwisePromotioncode.agentPromotionId','=' ,$agentPromotionId)
            ->get();

		  $agentCommisionDetail =   json_decode($agentCommisionDetail);
		 // print_r( $agentCommisionDetail[0]->FirstYrComRate);exit;
		  $agentCommisionPercent  = $agentCommisionDetail[0]->FiveYrLifeComRate;
			//manager commission

			 if($agentManagerPromotionId){
			 	 $managerCommisionDetail = DB::table('agents')
				->leftJoin('agentlevels', 'agents.levelID', '=', 'agentlevels.levelID')
        ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
				->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				->where('agentwisePromotioncode.agentPromotionId','=' ,$agentManagerPromotionId)
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
              ->leftJoin('agentwisePromotioncode','agents.agentId', '=', 'agentwisePromotioncode.agentId')
              ->select('agents.agentId', 'agentlevels.LevelName', 'agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
			        ->where('agentwisePromotioncode.agentPromotionId','=' ,$agentStateManagerPromotionId)
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

			$planFee = $request->fees;
			  $time = strtotime(date("y-m-d"));
              $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
			  $agentCommisionFee = ($agentCommisionPercent/100)*$planFee;
			$agentArray = [
			       'planId'=>$plan,
					'customerId'=>  $customerId,
					//'AgentId'=> $selectedAgentId,
          'AgentId'=>$agentPromotionId,
					'managerId'=>$agentManagerPromotionId,
				  'PercentOrDollar'=> 'dollar',
			    'Commission'=> $agentCommisionFee,
					'stateManagerId'=>$agentStateManagerPromotionId,
					'stateManagerCommission'=>$stateManagerCommisionFee,
				  'chargeBackCommision'=>null,
				  'chargeBackInstalment'=>null,
				  'managerCommission'=>$managerCommisionFee,
					'IsAdvance'=>'NO',
					'PaymentMode'=>'',
					'PaymentDate'=>'',
					'ModDate'=>date("Y-m-d"),
					'feeAmount'=>$request->fees,
					'newOrRenew'=>'NEW',
					'paymentletterDate'=>$nextPaymentDate,
				    'recurringPaymentDate'=>Carbon::now()
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
     public function customberDetail(Request $request)
     {
	    //print_r($request->all());exit;
          $customer = DB::table('customers')
            ->select('customers.*','agentpayment.paymentId as agent_payment_id','customers.created_at as cc_at','agents.firstName as agentFirstName', 'agents.lastName as agentLastName', DB::raw('CONCAT(groups.groupCode, " ", groups.groupId) AS groupCode') ,'plans.*')
			->leftJoin('groups', 'customers.groupId', '=', 'groups.groupId')
			->leftJoin('agents', 'customers.agentId', '=', 'agents.agentId')
			->leftJoin('agentpayment', 'customers.customerId', '=', 'agentpayment.customerId')
			->Join('plans', 'customers.planId', '=', 'plans.planId')
            ->where('customers.customerId', $request->customerId)
            ->get();
			
			
			
			/*$customer = DB::select( DB::raw("select customers.*,agentpayment.paymentId as agent_payment_id,customers.created_at as cc_at, CONCAT(agents.firstName,' ',agents.lastName) as agentLastName, CONCAT(groups.groupCode, ' ', groups.groupId) AS groupCode ,plans.*
			from customers
			left join groups ON customers.groupId=groups.groupId
			left join agentpayment ON customers.customerId=agentpayment.customerId
			LEFT JOIN agentPromotion ON agentPromotion.agentId=agentpayment.agentId
			left join agents ON agents.agentId=agentPromotion.agentId
			left join plans ON customers.planId = plans.planId
			where customers.customerId=".$request->customerId."
			group BY agentPromotion.agentId") );*/
			
			//echo"<pre>";
			//print_r($customer);exit;
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
      //getting plan
	   public function getPlan(){
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
     $today =strtotime(date("Y-m-d"));
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
			  'payeeName'=>$request->payeeName,
			  'orderNumber'=>$request->checkNumber,
			  'checkMoneyDate'=>$request->checkDate,
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
		   }


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
	   public function getplanDetail(Request $request)
       {

		  //print($request->currentPlanId);
		 // print($request->clientType);exit;
		  if($request->clientType == 'Family')
		  {
           $plan_detail = DB::table('plans')
            ->select('frequency','familyFee','initiatonFee','burial_family as burialfee' )
			->where('planId', $request->currentPlanId)
            ->get();
		  }
		  else{
			 $plan_detail = DB::table('plans')
				->select('frequency','fee','initiatonFee','burial_individual as burialfee')
				->where('planId', $request->currentPlanId)
                ->get();
		  }

			if($plan_detail)
			  return response()->json([
              'status'=>'200',
			  'plan_detail'=>$plan_detail
           ]);
     }
	 //public function updatecustomer(Request $request){
	  public function updatecustomer(Request $request){
	 /*  echo "<pre>";
	   print_r($request->all());
	   die('aaaa');*/
	    $postArray = [
              'firstName'      => $request->firstName,
              'LastName'      => $request->lastName,
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
			  'dependent1LastName'=>$request->dependent1LastName,
			  'dependent1DOB'=>$request->dependent1DOB,
			  'spouseFirstName'=>$request->spouseFirstName,
			  'spouseLastName'=>$request->spouseLastName,
			  'spouseDOB'=>$request->familyDateOfBirth,
			  'note'=>$request->note,
			 ];
       $agentPaimentDone =DB::table('customers')->where('customerId',$request->customerId)->update($postArray);
	   if($agentPaimentDone)
			  return response()->json([
              'status'=>'200',
           ]);
 }

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
      $plans_fee=DB::select( DB::raw("SELECT c.customerId,c.firstName,c.LastName,c.email,c.burialFee, c.initiationFee as modifyIniFee,
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

	 public function getmembershipPlan(){

	  $plans_fee=DB::select( DB::raw("select planId,planName,fee,initiatonFee,familyFee from plans") );

			if($plans_fee)
				  return response()->json([
				  'status'=>'200',
				  'planDetails'=>$plans_fee,
			   ]);
	 }

	public function groupCodeDetails(){
	   $group_code=DB::select( DB::raw("select groups.groupId,concat(groups.groupCode,'',groups.groupId) as groupCode, date_format(groups.created_at,'%m-%d-%Y'),
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId"));
    $total_groups=DB::select( DB::raw("select count(groupId) total_groups from groups"));
	$total_active_groups=DB::select( DB::raw("select count(groupId) total_active_groups from groups where status='Active'"));
	$total_inactive_groups=DB::select( DB::raw("select count(groupId) total_active_groups from groups where status='Inactive'"));
	         if($group_code)
				  return response()->json([
				  'status'=>'200',
				  'groupDetails'=>$group_code,
				  'total_groups'=>$total_groups,
				  'total_active_groups'=>$total_active_groups,
				  'total_inactive_groups'=>$total_groups,
			   ]);
	}
 public function frontendregister(Request $request){
	    /*echo "<pre>";
	   print_r($request->all());
	   die('aaaa');  */
	   $frontendCustomerArray = [
           'firstName'      => $request->first_name,
           'LastName'      => $request->last_name,
           'DOB'=>$request->date_of_birth,
           'city'      => $request->city,
		   'city1'      => $request->city1,
		   'state'      => $request->state,
		   'state1'      => $request->state_s1,
		   'zip'  =>$request->zipcode,
		   'zip1'      => $request->zip_code,
		   'zip'  =>$request->zipcode,
		   'groupId'=>$request->group_code,
		   'isActive'=>'No',
		   'password'      => $request->set_your_password,
		   'email' =>$request->email_address,
		   'companyName'      => $request->company_name,
		   'agentId'  => $request->selectedAgentId,
		   'country'  =>$request->country,
           'country1'  =>$request->country1,
		   'cellPhone'  =>$request->primary_phone_number,
		   'clientType'  =>$request->type,
		   'mobile2'  =>$request->alternate_phone_number,
		   'planId'  =>$request->plan_id,
           'address1'  => $request->client_address_1,
		   'address2'  => $request->client_address_2,
		   'mailing_address1'  => $request->mailing_address_1,
		   'mailing_address2'  => $request->mailing_address_2,
		   'dependent1FirstName' =>$request->dependant_name,
		    'spouseFirstName'  =>$request->spouse_name,
			'spouseDOB' =>$request->familyDateOfBirth,
			'price'=>($request->initiation_fees+$request->membership_fees),
            'created_at' =>Carbon::now(),
			'membershipDate' =>date("y-m-d"),
           'modDate' =>date("y-m-d"),
           'modBy' =>'customer',
		   'membership_type'=>$request->membership_type
         ];
		$frontendCustomer_id = DB::table('frontend_customer_temp')->insertGetId($frontendCustomerArray);
		$customberDetail=DB::select( DB::raw("select frontend_customer_temp.customerId,frontend_customer_temp.price, frontend_customer_temp.firstName, frontend_customer_temp.LastName, frontend_customer_temp.email, frontend_customer_temp.membership_type,plans.planName
from frontend_customer_temp
left join plans on frontend_customer_temp.planId=plans.planId
where frontend_customer_temp.customerId=".$frontendCustomer_id) );
	    if($customberDetail)
			  return response()->json([
              'status'=>'200',
			  'customer_details'=>$customberDetail
           ]);


  }


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
  public function installShedule(Request $request){
	  //echo $request->clientId; exit;
	  $install=DB::select( DB::raw("SELECT customers.firstName,customers.LastName,
	  customers.clientType,customers.planId,
	  plans.planName from customers
	  LEFT JOIN plans ON plans.planId = customers.planId
      left join agentpayment ON customers.customerId=agentpayment.customerId
      where customers.customerId =$request->clientId"));

	  $payment_details=DB::select( DB::raw("SELECT  (@a:=@a+1) AS serial_number,date_format(agentpayment.PaymentDate,'%m-%d-%Y')as payment_date,plans.planName,

(CASE
   WHEN customers.clientType = 'Family' THEN plans.familyFee
        ELSE plans.fee
    END) AS plan_original_fee,
    agentpayment.feeAmount as overridefee,
agentpayment.PaymentMode,
(CASE
   WHEN customers.paymentmode = 12 THEN 'Monthly'
        ELSE 'Annual'
    END) as payment_frequency,
 (CASE
   WHEN agentpayment.MonthCounter=1 THEN
     round((agentpayment.feeAmount/customers.paymentmode),2)
   ELSE
      round(agentpayment.feeAmount,2)
  END
  ) as feeAmount
    from `customers`
    join (SELECT @a:= 0) AS a
    LEFT JOIN `plans` ON `plans`.`planId` = `customers`.`planId`
    left join `agentpayment` ON `customers`.`customerId`=`agentpayment`.`customerId`
    where `customers`.`customerId` =$request->clientId  and agentpayment.PaymentDate <> '0'"));
	//print_r " $payment_details"; die;
      if($install)
				  return response()->json([
				  'status'=>'200',
				  'install'=>$install,
				  'payment_details'=>$payment_details,
			   ]);
  }

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

   public function groupInsert(Request $request){
	          /*   echo "<pre>";
	  print_r($request->all());
	  die('aaaa'); */
	    $group = [
           'groupCode'      => $request->groupCode,
           'groupName'      => $request->groupName,
           'companyName'=>$request->companyName,
           'status'      => $request->isActive,
            'created_at' => Carbon::now()
         ];
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
    public function adminDetail(Request $request){
	     	  $admin=DB::select( DB::raw("select id,name,email,date_format(created_at,'%m-%d-%Y')
			  as created_date from `users` where userName !=1 order by id desc") );

		 if($admin)
		 {
				  return response()->json([
				  'admin'=> $admin,
				  'status'=>'200'
			   ]);
		 }
   }

   public function totalCustomberInstallment(){
     $payment_details=DB::select( DB::raw("SELECT  (@a:=@a+1) AS serial_number, `customers`.`customerId` AS customerId,
CONCAT(`customers`.`firstName`,`customers`.`LastName`) AS customer_name,
date_format(`agentpayment`.`recurringPaymentDate`,'%m-%d-%Y')as payment_date,plans.planName,
      (CASE
         WHEN customers.clientType = 'Family' THEN plans.familyFee
         ELSE plans.fee
       END) AS plan_original_fee,
     agentpayment.feeAmount as overridefee,
 (CASE
    WHEN customers.paymentmode = 12 THEN 'Monthly'
         ELSE 'Annual'
     END) as payment_frequency,
  (CASE
      WHEN plans.planName='Monthly' THEN agentpayment.feeAmount
      ELSE round(agentpayment.feeAmount/customers.paymentmode,2)
      END
  ) AS feeAmount
     from `customers`
     join (SELECT @a:= 0) AS a
     LEFT JOIN `plans` ON `plans`.`planId` = `customers`.`planId`
     left join `agentpayment` ON `customers`.`customerId`=`agentpayment`.`customerId`
     WHERE `agentpayment`.`recurringPaymentDate` BETWEEN CURDATE() AND (CURDATE() + INTERVAL
     30 DAY)  and agentpayment.PaymentDate <> '0'
     order by `customers`.`customerId` desc"));
   if($payment_details)
           return response()->json([
           'status'=>'200',
           'payment_details'=>$payment_details,
          ]);
   }
   //CustomberInstallmentPayment
  public function CustomberInstallmentPayment(Request $request){
      $customer_details=DB::select( DB::raw("select AgentId,managerId,stateManagerId,planId,MonthCounter,recurringPaymentDate from `agentpayment` WHERE `agentpayment`.`customerId`=$request->customerId") );
      $agent_id='';
      $monthCounter='';
      $plan_Id='';
      $managerId='';
      $stateManagerId='';
      $recurringPaymentDate='';
       foreach($customer_details as $row){
         $agent_id=$row->AgentId;
         $monthCounter=$row->MonthCounter;
         $recurringPaymentDate=$row->recurringPaymentDate;
         $plan_Id=$row->planId;
         $managerId=$row->managerId;
         $stateManagerId=$row->stateManagerId;
       }
             $previousPaymentDate=strtotime($recurringPaymentDate);
             $nextPaymentDate = date('Y-m-d', strtotime('+1 month', $previousPaymentDate));
             $postArray=[
              'planId'=>$plan_Id,
              'AgentId'=>$agent_id,
              'feeAmount'=>$request->customerPayAmount,
              'MonthCounter'=>$monthCounter+1,
              'PaymentMode'=>$request->paymentmethod,
              'newOrRenew'=>'installment',
              'recurringPaymentDate'=>$nextPaymentDate,
              'PaymentDate'=>date('Y-m-d'),
              'customerId'=>$request->customerId
            ];
            $customerInstallmentPayment=DB::table('agentpayment')->insert($postArray);
      if($customerInstallmentPayment){
        return response()->json([
        'status'=>'200'
       ]);
      }
  }

  public function customberCancellation(Request $request){
        $postArray=[
         'isActive'=>'No'
       ];
       $customerInstallmentPayment=DB::table('customers')->where('customerId',$request->customerId)->update($postArray);
    if($customerInstallmentPayment){
        return response()->json([
        'status'=>'200'
        ]);
    }
  }
  public function membershipRefund(Request $request){
    /*echo "<pre>";
    print_r($request->id);
    die('aaaaa');*/
    //select count(paymentId) total_installment from agentpayment where customerId=12
    $member_details=DB::select( DB::raw("SELECT distinct CONCAT(customers.firstName,' ',customers.LastName) AS customer_name,
	  customers.clientType,customers.planId,
	  plans.planName,customers.paymentmode from customers
	  LEFT JOIN plans ON plans.planId = customers.planId
      left join agentpayment ON customers.customerId=agentpayment.customerId
      where customers.customerId =$request->id"));

    $membership_fees=DB::select( DB::raw("SELECT agentpayment.feeAmount from agentpayment
      where agentpayment.customerId =$request->id Limit 0,1"));
	  $total_payment_details=DB::select( DB::raw("select count(paymentId) total_installment from agentpayment where customerId=$request->id"));
     $total_days_from_membership_date=DB::select( DB::raw("select datediff(now(),customers.membershipDate) AS total_date_difference FROM customers where customers.customerId=$request->id"));
    $total_month_from_membership_date=DB::select( DB::raw("select TIMESTAMPDIFF(MONTH,customers.membershipDate,now()) AS total_month_difference FROM customers where customers.customerId=$request->id"));

      if($member_details)
				  return response()->json([
				  'status'=>'200',
				  'member_details'=>$member_details,
          'membership_fees'=>$membership_fees,
				  'total_payment_details'=>$total_payment_details,
          'total_days'=>$total_days_from_membership_date,
          'total_month'=>$total_month_from_membership_date
			   ]);
  }

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

}
