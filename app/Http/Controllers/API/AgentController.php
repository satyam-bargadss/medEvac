<?php
// //$pormotion_detail = $this->fetch_promotion_id($request->agentId);
namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Agent;
//use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Mail;
class AgentController extends Controller
{
    public $successStatus = 200;

    private $apiToken;

    public function __construct()
    {
        // Unique Token
        //$this->middleware('auth:agent');
        $this->apiToken = str_random(60);
    }
	/*agent index start*/
	public function index(Request $request)
    {
		if($request->isActive != '')
		{
		$agents = DB::select( DB::raw("SELECT ag.agentId,ag.agentCode, (case
when (ag.middleName is not null)  then (CONCAT(ag.firstName,' ',ag.MiddleName,' ',ag.LastName))
 else (CONCAT(ag.firstName,' ',ag.LastName))
 end) as agent_name,

 ag.email,ag.cellPhone, date_format(ag.dob,'%m-%d-%Y') as dob, (al.LevelName) AS levelID, awc.total_commission,ag.isActive
FROM `agents` ag
Left join `agentpayment` Agpt on ag.agentId=Agpt.agentId
left join `agents` AGM ON AGM.AgentId=Agpt.managerId
LEFT join `agent_wise_commission` awc ON awc.agentId=ag.agentId
LEFT join `agentlevels` al ON ag.levelID=al.levelID
where ag.isActive=$request->isActive
group by ag.agentId
Order BY ag.agentId DESC ") );
		}
		else
		{

			 $agents = DB::select( DB::raw("SELECT ag.agentId,ag.agentCode,(case
when (ag.middleName is not null)  then (CONCAT(ag.firstName,' ',ag.middleName,' ',ag.LastName))
 else (CONCAT(ag.firstName,' ',ag.LastName))
 end) as agent_name,  ag.email,ag.cellPhone, date_format(ag.dob,'%m-%d-%Y') as dob,(al.LevelName) AS levelID, awc.total_commission,ag.isActive
FROM `agents` ag
Left join `agentpayment` Agpt on ag.agentId=Agpt.agentId
left join `agents` AGM ON AGM.AgentId=Agpt.managerId
LEFT join `agent_wise_commission` awc ON awc.agentId=ag.agentId
LEFT join `agentlevels` al ON ag.levelID=al.levelID
group by ag.agentId
Order BY ag.agentId DESC") );
		}

	   /* $agents = DB::select( DB::raw("SELECT ag.agentId, CONCAT(ag.firstName,' ',ag.LastName) as agent_name, ag.dob,ag.levelID, CONCAT(AGM.firstName,' ',AGM.LastName) as manager_name
FROM `agents` ag
Left join agentmanagers AGt on ag.agentId=AGt.agentId
Left join agents AGM ON AGM.agentId=AGt.managerId Order BY ag.agentId DESC") );  */

	  /* $agents = DB::select( DB::raw("SELECT ag.agentId, CONCAT(ag.firstName,' ',ag.LastName) as agent_name, ag.dob,ag.levelID, CONCAT(AGM.firstName,' ',AGM.LastName) as manager_name, FORMAT(sum(`TransAmount`),2)as totalCommision
FROM `agents` ag
Left join agentmanagers AGt on ag.agentId=AGt.agentId
Left join agents AGM ON AGM.agentId=AGt.managerId
LEFT JOIN feetransaction AS fT ON fT.AgentId=ag.agentId
WHERE fT.AgentId IN (SELECT AgentId from feetransaction fT2 ) group by fT.AgentId
Order BY ag.agentId DESC
    ") ); */

     $all_agent_total_commission=DB::select( DB::raw("SELECT round(sum(`agent_wise_commission`.`total_commission`),2) as all_agent_total_commission
FROM `agent_wise_commission`") );

     $total_manager_list=DB::select( DB::raw("SELECT count('manager_name') as total_manager FROM `agent_manager_list` where (manager_name <>0 || manager_name <> '')") );

	 // $totalCommision = DB::select( DB::raw("SELECT AgentId,sum(`TransAmount`) as totalCommision
	  //from feetransaction WHERE `AgentId`IN (SELECT AgentId from feetransaction) group by `AgentId`"));
      $totalCommision = DB::select( DB::raw("SELECT round(sum(`Commission`),2) as totalCommision from agentpayment"));

	  $totalAgent_paid_amount = DB::select( DB::raw("SELECT round(sum(TotalFee-adhocPayment),2) as total_paid_ammount FROM `agenttotalfee`"));

      $totalAgents = DB::table('agents')
	  ->select('agentId', 'firstName', 'lastName','dob','isActive')
	  ->orderBy('agentId', 'desc')->get();
	  //$totalNumAgents = count($totalAgents);
	   $activeAgents = DB::table('agents')->where('isActive', '0')->count();
	   $inActiveAgents = DB::table('agents')->where('isActive', '1')->count();
	   //$manager =  DB::table('agents')->where('isActive', '1')->count();

        //var_dump($agents);exit;
	   //echo "$totalCustomers";exit;
	  // echo "$inActiveUsers";
	   //echo "$usCustomber";
	  // echo "$internationalCustomber";
	 //  exit;
      //print_r($customers);exit;
	  //return $customers;
	  //extract($customers);
	  //echo"<pre>";
	 // print_r($customers);exit;
	  $totalAgents = count($totalAgents);
	  //echo $count;exit;
	return response()->json([
        'agents' =>$agents,
		'totalAgents'=>$totalAgents,
		'activeAgents'=>$activeAgents,
		'inActiveAgents'=>$inActiveAgents,
		'total_commission_payable'=>$all_agent_total_commission,
		'total_manager_list'=>$total_manager_list,
		'total_agent_paid_amount'=>$totalAgent_paid_amount


      ]);
    }
	/*agent index end*/
	/*agent payment schedule start*/
	public function agentPaymentSchedule(Request $request){
             // echo $request->id;exit;

		/*$agents = DB::select( DB::raw("SELECT  agent_commission_details.customerId, agent_commission_details.customerName, agent_commission_details.client_type, agent_commission_details.membership_plan, agent_commission_details.fees, agent_commission_details.groupCode, agent_commission_details.agent_commision, agent_commission_details.agent_chargeBack_commision, agent_commission_details.agent_interest, agent_commission_details.renewal_commision, agent_commission_details.manager_Commision, agent_commission_details.manager_interest, agent_commission_details.state_manager_commission,
agent_commission_details.state_manager_interest,agentpayment.PaymentDate, (CASE
                                                                           when(customer_cancellation.canceldate<>0) THEN customer_cancellation.canceldate
ELSE 0  END) as cancelDate ,(CASE
                                                WHEN (agentpayment.newOrRenew = 'NEW')
                                                THEN
                                                    CASE
                                                    WHEN (plans.planName='Monthly'||plans.planName='Annual')
                                                        THEN (agentlevels.FirstYrComRate)
                                                        ELSE (agentlevels.FiveYrLifeComRate)
                                                    END
                                                ELSE
                             						CASE WHEN (agentpayment.newOrRenew = 'installment') THEN 0
                                                    ELSE agentlevels.RenewComRate
                                                    END

                                                END ) AS commissionRate,
                                                (CASE
                                                  WHEN agentPayable.agentChargebackAmount IS null THEN 0
                                                    ELSE agentPayable.agentChargebackAmount
                                                    END) AS chargebackAmount,
                                                    (CASE
                                                      WHEN (agentPayable.agentChargebackAmount IS null || agentPayable.agentChargebackAmount='0.0') THEN  agent_commission_details.earned_commission
                                                      ELSE (agent_commission_details.earned_commission-agentPayable.agentChargebackAmount)
                                                      END) AS earned_commission
 FROM agents
 left JOIN agentpayment ON agents.agentId=agentpayment.AgentId
 LEFT JOIN plans ON plans.planId=agentpayment.planId
 LEFT join agentlevels ON agentlevels.levelID=agents.levelID
 LEFT JOIN agent_commission_details ON (agent_commission_details.agentId=agents.agentId AND agentpayment.customerId=agent_commission_details.customerId)
 left join agentPayable ON (agent_commission_details.agentId=agentPayable.agentId AND agent_commission_details.customerId=agentPayable.customerId)
 LEFT JOIN customer_cancellation ON customer_cancellation.customer_id=agentpayment.customerId
 WHERE agents.AgentId=$request->id
 GROUP BY agentpayment.customerId
 ") );*/
  if($request->startDate)
		 {
			$startDate_timestamp = strtotime($request->startDate);
			$startDate = date("Y-m-d", $startDate_timestamp);
		 }else{
			$startDate='';
		 }
		 if($request->endDate){
			 $endDate_timestamp = strtotime($request->endDate);
			 $endDate = date("Y-m-d", $endDate_timestamp);
		 }else{
			 $endDate='';
		 }
		 if($startDate!='' && $endDate!=''){
				$where="Where agentId = '".$request->id."' AND PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate!='' && $endDate==''){
				$endDate=date("Y-m-d");
			  $where="Where agentId = '".$request->id."' AND PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate=='' && $endDate!=''){
			   $where=" Where agentId = '".$request->id."' AND PaymentDate <='".$endDate."'";
			}else{
				$where=" Where agentId = '".$request->id."'";
			}

			$sql = "SELECT customerId,customerName,client_type,membership_plan,fees,groupCode,agent_commision,agent_chargeBack_commision as advance_comission,agent_interest,renewal_commision,manager_Commision,manager_interest,state_manager_commission,state_manager_interest,PaymentDate,cancelDate, commissionRate, chargebackAmount,earned_commission
FROM agent_commission_details AS a ".$where ."";
//echo $sql;exit;
$agents = DB::select( DB::raw("SELECT customerId,customerName,client_type,membership_plan,fees,groupCode,agent_commision,agent_chargeBack_commision as advance_comission,agent_interest,renewal_commision,manager_Commision,manager_interest,state_manager_commission,state_manager_interest,PaymentDate,cancelDate, commissionRate, round(chargebackAmount,2) as chargebackAmount ,round(earned_commission,2) as earned_commission
FROM agent_commission_details AS a ".$where ."") );

	   $agents_details=DB::select( DB::raw("SELECT C.agentCode, CONCAT(C.firstName,' ',C.LastName) As agentName, al.LevelName AS agentLevel, CONCAT(C.address1,', ',C.city,'-',C.zip) As agentAddress,
round((case when (apay.agentChargebackAmount <> 0||apay.agentChargebackAmount<>null) then (ap.total_commission-apay.agentChargebackAmount) else ap.total_commission end),2) AS total_commission
FROM agents as C
Left Join agentlevels as al ON al.levelID=C.levelID
Left join agent_wise_commission as ap on ap.AgentId=C.agentId
left join agentPayable as apay ON ap.agentId=apay.agentId
WHERE C.agentId=".$request->id ) );

		$total_customer=DB::select( DB::raw("SELECT count(customerId)total_customer FROM `agentpayment` WHERE `AgentId` =".$request->id) );
		return response()->json([
        'agents' =>$agents,
		'agentDetails' =>$agents_details,
		'total_customer' =>$total_customer
      ]);

	}
	/*agent payment schedule end*/
	/*agent pay now start*/
	public function agentpayNow(Request $request){
                     $email = $request->email;

		          $agent = DB::table('agenttotalfee')->insert([
									'AgentId'=>$request->currentAgent,
									'TotalFee'=>$request->total_amount,
									'IsPaid'=>'1',
									'PayDate'=>date('Y-m-d '),
									'paymentMethod'=>$request->payment_method,
									'adhocPayment'=>$request->adhocpayment,
									'adhocPaymentNaration'=>$request->narration
								]);
		 if($agent) {

			// Update Token
             $postArray = [
			  'isPaidAgent' => '1',
			  'ModDate'=>date('Y-m-d ')
			 ];
			 if($request->email !='')
						  {
                          Mail::raw('Thank You for Payment!', function($message)    use ($email)
                          {

                              $message->subject('Payment email!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						  }
             $agentPaimentDone =DB::table('agentpayment')->where('AgentId',$request->currentAgent)->update($postArray);
			 $postArray2 = [
			  'isPaidManager' => '1',
			  'ModDate'=>date('Y-m-d ')
			 ];
             $ManagerPaimentDone = DB::table('agentpayment')->where('managerId',$request->currentAgent)->update($postArray2);

			 $postArray3 = [
			  'isPaidStateManager' => '1',
			  'ModDate'=>date('Y-m-d ')
			 ];
             $ManagerPaimentDone = DB::table('agentpayment')->where('stateManagerId',$request->currentAgent)->update($postArray3);

           return response()->json([
             'status' =>  200,
             'message'   => 'Payment Done sucessfully'

           ]);
         }
		 else {
			   return response()->json([
				 'status' => '203',
				 'message'=>'Error
				 '
			   ]);
           }


	}
	/*agent pay now end*/
	/*login start*/
    public function login(Request $request){
        /* if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
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
         $agent = Agent::where('email',$request->email)->first();
         if($agent) {
           // Verify the password
           if( password_verify($request->password, $agent->password) ) {
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
	/*login end*/
	/*agent login start*/
	 public function agentlogin(Request $request){
		//print_r( $request->all());exit;
		$rules = [
        //'email'=>'required|email',
		'agentCode'=>'required',
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
        // Fetch Agent
        //$user = Agent::where('agentCode', $request->agentCode )->where('password',$request->password)->first();
		$user = DB::table('agents')->where('agentCode', $request->agentCode )->where('password',$request->password)->first();
		//print_r( $user);exit;
        if($user) {


            // Update Token
            $postArray = ['api_token' => $this->apiToken];

            $agentlogin =DB::table('agents')->where('agentCode',$request->agentCode)->update($postArray);
			  /* print_r($agentlogin);
			exit;  */
             if($agentlogin){
              return response()->json([
                'agentCode' => $user->agentCode,
				'permission'   => $user->agentName,
                'access_token' =>  $this->apiToken,
				'status' => $this->successStatus,
				 'agentId' => $user->agentId,
				 'firstName' => $user->firstName,
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
	/*agent login end*/
		/*agent dashboard start*/
	 public function agentdashboard(Request $request){

		 //print_r($request->all());exit;
		 $agentdetails = DB::TABLE('agents')
				->select('agents.agentCode',DB::raw('CONCAT(agents.firstName," ",agents.lastName) AS firstName'),'agents.levelID','agents.agentStartDate','agents.agentId','customers.customerId','agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				//->join('agentpayment','agentpayment.customerId','=','customers.customerId')
				->leftjoin('agentlevels','agentlevels.levelID','=','agents.levelID')
				->leftjoin('customers','customers.agentId','=','agents.agentId')
				->where('agents.agentCode',$request->agentCode)
				->get();
		 if($agentdetails)
          {
              return response()->json([
                    'status'=>'200',
                    'agentdetails'=>$agentdetails
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'agentdetails'=>$agentdetails
              ]);
          }

	 }
	/*agent dashboard end*/
	/*get agent start*/
	 public function getagent(Request $request){

		 //print_r($request->all());exit;
		// $request->agentCode='GMA1005';
		 $fetchagent = DB::TABLE('agents')
				//->select('agents.agentCode',DB::raw('CONCAT(agents.firstName," ",agents.lastName) AS firstName'),'agents.levelID','agents.agentStartDate','agents.agentId','agents.dob','agents.cellPhone','agents.alt_phone_num','agents.address1','agents.address2','agents.city','agents.country','agents.zip','agents.email')
				//->join('agentpayment','agentpayment.customerId','=','customers.customerId')
				//->join('plans','plans.planId','=','customers.planId')
				//->join('customers','customers.agentId','=','agents.agentId')
				 ->select('agents.agentCode',DB::raw('CONCAT(agents.firstName," ",agents.lastName) AS firstName'),
				 'agents.levelID','agents.agentStartDate','agents.agentId','agents.dob','agents.cellPhone','agents.alt_phone_num','agents.address1',DB::raw('(CASE
								WHEN agents.address2 IS NULL THEN agents.city
								ELSE agents.address2
								END) as adress2'),
								DB::raw('(CASE
								WHEN agents.city IS NULL THEN agents.country
								ELSE agents.city
								END) as city'),
								DB::raw('(CASE
								WHEN agents.country IS NULL THEN agents.zip
								ELSE concat(agents.country," ",agents.zip)
								END) as country'),

								'agents.address3',DB::raw('(CASE
								WHEN agents.address4 IS NULL THEN agents.city1
								ELSE agents.address4
								END) as address4'),
								DB::raw('(CASE
								WHEN agents.city1 IS NULL THEN agents.country1
								ELSE agents.city1
								END) as city1'),
								DB::raw('(CASE
								WHEN agents.country1 IS NULL THEN agents.zip1
								ELSE concat(agents.country1," ",agents.zip1)
								END) as country1'),

				'agents.email')
				->leftjoin('agentmanagers','agentmanagers.managerId','=','agents.agentId')
				->where('agents.agentId',$request->agentId)
				->get();
				 /*  echo"<pre>";
				print_r($fetchagent);
				die('aaaaaaaa'); */

				$agentManager = DB::select( DB::raw("select CONCAT(manager.firstName,' ',manager.lastName) as managerName, CONCAT(statemanager.firstName,' ',statemanager.lastName) as statemanagerName from agentmanagers
						LEFT JOIN agents as manager ON manager.agentId=agentmanagers.managerId
						LEFT JOIN agents as statemanager ON statemanager.agentId=agentmanagers.stateManagerId
						WHERE agentmanagers.agentId=".$request->agentId." ORDER BY agentmanagers.id DESC limit 0,1"));

				/* $agentManager = DB::TABLE('agentmanagers')
				->select(DB::raw('CONCAT(manager.firstName," ",manager.lastName) AS managerName'), DB::raw('CONCAT(statemanager.firstName," ",statemanager.lastName) AS statemanagerName'))
				->leftjoin('agents as manager','manager.agentId','=','agentmanagers.managerId')
				->leftjoin('agents as statemanager','statemanager.agentId','=','agentmanagers.stateManagerId')
				->where('agentmanagers.agentId',$request->agentId)
				->orderBy('agentmanagers.id','DESC')
				->Limit(0,1)
				->get(); */
				   /*     echo"<pre>";
				print_r($agentManager);
				die('aaaaaaaa');   */


		 if($fetchagent)
          {
              return response()->json([
                    'status'=>'200',
                    'fetchagent'=>$fetchagent,
					'agentManager'=>$agentManager
					//'agentManager'=>$agentManager[0]->statemanagerName
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'fetchagent'=>$fetchagent,
					//'agentManager'=>$agentManager[0]->statemanagerName
					'agentManager'=>$agentManager
              ]);
          }

	 }
	/*get agent end*/

		/*agent payment start*/
	 public function agentPayment(Request $request){

		 //print_r($request->all());exit;
		 $agentdetails = DB::TABLE('agents')
				->select('agents.agentCode',DB::raw('CONCAT(agents.firstName," ",agents.lastName) AS firstName'),'agents.levelID','agents.agentStartDate','agents.agentId','customers.customerId','agentlevels.FirstYrComRate','agentlevels.RenewComRate','agentlevels.FiveYrLifeComRate')
				//->join('agentpayment','agentpayment.customerId','=','customers.customerId')
				//->join('agentlevels','agentlevels.levelID','=','agents.levelID')
				//->join('customers','customers.agentId','=','agents.agentId')
				->where('agents.agentCode',$request->agentCode)
				->get();
		 if($agentdetails)
          {
              return response()->json([
                    'status'=>'200',
                    'agentdetails'=>$agentdetails
              ]);
          }
          else
          {
              return response()->json([
                    'status'=>'200',
                    'agentdetails'=>$agentdetails
              ]);
          }

	 }
	/*agent payment end*/


     /**
      * Show the form for creating a new resource.
      *
      * @return \Illuminate\Http\Response
      */
	/*agent register start*/
     public function register(Request $request)
     {

		 /* echo "<pre>";
		 print_r($request->all());
		 exit; */
       /* DB::beginTransaction();
   			try { */
       /*echo "<pre>";
       print_r($request->all());
       exit;*/
	    $managerId = $request->selectedAgentId['value'];
		//echo  $managerId;exit;
		 $statemanagerId = $request->selectedManagerId['value'];
    //echo "$managerId"."$statemanagerId";exit;
        // $agent->generateToken();
     // print_r($_POST);exit;
        // return response()->json(['data' => $agent->toArray()], 201);
		/*
        $rules = [
         'agentName'     => 'required|min:3|regex:/^[a-zA-Z]+$/u',
         'email'    => 'required|unique:agents,email|max:160',
         'password' => 'required|min:8',
         'city' =>'required|max:40|regex:/^[a-zA-Z]+$/u',
         'agentStartDate' =>'required',
         'address1' =>'required|max:100',
         'country' =>'required|max:50|regex:/^[a-zA-Z]+$/u',
         'location' =>'required|max:150|regex:/^[a-zA-Z]+$/u',
         'cellPhone' =>'required|min:10|numeric',
         'zip' =>'required|numeric',
       ];
       $validator = Validator::make($request->all(), $rules);
       if ($validator->fails()) {
         // Validation failed
         return response()->json([
           'message' => $validator->messages(),
         ]);
       } else {
		   */
		   if($request->email !=''){
			   $email = $request->email;

		   }
		   $password = $this->random_num(8);
		   $agentCode =$request->agentCode;
         $postArray = [
		   'agentCode' => $request->agentCode,
			'firstName'  => $request->firstname,
			'middleName'  => $request->middlename,
			'lastName'      => $request->lastname,
			'email'      => $request->email ,
		   'levelID' =>$request->agentLevel,

			'city'      => $request->city,
		   'city1'      => $request->city1,
		   'state' => $request->state_s,
		   'state1' => $request->state_s1,
		   'dob'=>date("Y-m-d", strtotime("$request->agentDateOfBirth")),
       'country'     => $request->country,
		   'country1'     => $request->country1,
       'address1'  => $request->billingaddress1,
       'address2'  =>$request->billingaddress2,
		   'address3'  =>$request->maddress1,
		   'address4'  =>$request->maddress2,
       //'country1'  =>$request->country1,
       'zip'  =>$request->zip,
		   'zip1'  =>$request->zipcode,
		   'agentStartDate'=> date('Y-m-d '),
       'cellPhone'  =>$request->phone,
		   'alt_phone_num'  =>$request->phone1,
       'password'=>$password,
       'isActive'  =>$request->isActive,
       'created_at' =>Carbon::now(),
       'bank_name' =>$request->bankname,
		   'paymentMethod'=>$request->paymentMethod,
       'account_name' =>$request->accountname,
		   'account_number'=>$request->accountnumber,
       'note'=>$request->note
         ];
		 /* echo "<pre>";
		 print_r($postArray);
		 die('aaaa'); */
         // $agent = agent::GetInsertId($postArray);
         //$agent = agent::insert($postArray);
          DB::table('agents')->insert(
                $postArray
                );
			 $agentId = DB::getPdo()->lastInsertId();
       $agentLevelId="";
       $agentLevel=DB::select( DB::raw("SELECT `agentId`,`levelID` FROM `agents` WHERE `agentId`=$agentId"));
       foreach($agentLevel as $value){
         $agentPresentLevel=[
           'agentId'=>$value->agentId,
           'level'=>$value->levelID,
           'enddate'=>null,
		   'OverrideCommisionAnual'=>$request->overrideCommisionAnual,
		   'OverRideCommisionLifeTime'=>$request->overrideCommisionFiveYearLifeTime,
		   'overRideCommisionDate'=>Carbon::now(),
         ];
         DB::table('agentPromotion')->insert($agentPresentLevel);
        $agentLevelId = DB::getPdo()->lastInsertId();
       }
       $managerPromotionId="";
       $agentPromotionId="";
       $stateManagerPromotionId="";
       //die('aaa');
			 if($managerId>0){
          $agentPromotion_Id=DB::select( DB::raw("SELECT `agentPromotionId` FROM `agentPromotion` WHERE `agentId`=$agentId"));
          foreach($agentPromotion_Id as $value){
              $agentPromotionId=$value->agentPromotionId;
          }
          $managerPromotion_Id=DB::select( DB::raw("SELECT `agentPromotionId` FROM `agentPromotion` WHERE `agentId`=$managerId"));
          foreach($managerPromotion_Id as $value){
              $managerPromotionId=$value->agentPromotionId;
          }
          if($statemanagerId>0){
            $stateManagerPromotion_Id=DB::select( DB::raw("SELECT `agentPromotionId` FROM `agentPromotion` WHERE `agentId`=$statemanagerId"));
            foreach($stateManagerPromotion_Id as $value){
                $stateManagerPromotionId=$value->agentPromotionId;
            }
        }
				 $agentManager =[
				    'agentid' =>$agentId,
            'agentPromotionId'=>$agentPromotionId,
				    'managerId' =>$managerId,
            'managerPromotionId'=>$managerPromotionId,
            'stateManagerId'=>$statemanagerId,
				    'stateManagerPromotionId' => $stateManagerPromotionId
				 ];

         DB::table('agentmanagers')->insert( $agentManager );

			 }
                if($request->email !='')
						  {
                          /*  Mail::raw('Thank You for registration!</br>password:$password', function($message)    use ($email)
                          {

                              $message->subject('Welcome message');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });  */
						   Mail::send([], [], function ($message )use ($email,$agentCode,$password)  {
                        $message->to($email)
                              ->subject('Registration Successful!')
							  ->from('globalmedivac@gmail.com', 'Global Medivac')
                           /* ->setBody("Thank You for registration <br>You may now login your account at:
										http://35.235.80.37:3000/agent-login-form <br> AgentCode:$agentCode<br>Password:$password", 'text/html');  */
							->setBody("Dear Global Medevac representative, <br><br>Thank you for making the decision to become a new sales representative for Global Medevac, the best and most comprehensive service program in America. Your agent number is listed below, which you should include on every new application you sell. Please feel free to call us at 1-833-GET-MVAC (1-833-438-6822) or 1-512-277-7560 for any needs, questions, or concerns. You made a great decision when you signed on, so now go start your new career! <br><br> <b>Agent Number<b>:$agentCode<br><br><b>Tim Green-President</b>", 'text/html');
                   });

						  }
           return response()->json([
             'status' =>  200,
             'name'         => $request->firstName,
             'lastName'        => $request->lastname,
			 'message'   => 'Account created sucessfully'

           ]);

    /* } catch (Exception $e) {
      DB::rollBack();
      return response()->json([
            'status'=>'202',
      'message'=>'something went wrong'
         ]);
} */


     }
	/*agent register end*/
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
	 /*agent by id name start*/
	 public function AllAgentByIdName()
     {
         $agentsName = DB::table('agents')
	  ->select('agentId', 'firstName', 'lastName','agentCode')
	  ->where('isActive', '=', 0)
	  ->orderBy('agentId', 'desc')->get();

	  if($agentsName) {
           return response()->json([
             'status' =>  200,
             'AgentByIdName' => $agentsName,
			 'message'   => 'Account created sucessfully'

           ]);

     }
	  else{
			 return response()->json([
             'status' =>  203,
             'message'=>'Something Went wrong'

           ]);
		   }
	 }
	 /*agent for manager start*/
	 //fetching Agent as manager
	  public function agentForManager(Request $request)
     {
		//echo $request->currentLevelId ; exit;
         $agentManager = DB::table('agents')
	  ->select('agentId', 'firstName', 'lastName')

	  ->where('agentId','!=', $request->agentId)
	  ->where('levelID','>', $request->currentLevelId)
	  ->get();
	  if($agentManager) {
           return response()->json([
             'status' =>  200,
             'agentManager' => $agentManager,
			 'message'   => 'Sucess'

           ]);

     }
	  else{
			 return response()->json([
             'status' =>  203,
             'message'=>'Something Went wrong'

           ]);
		   }
	 }
	 /*agent for manager end*/
   /* Start Agent Details for react agentEdit Page*/
	/*agent details start*/
	   public function agentDetail(Request $request)
     {
       /*echo "<pre>";
 	    print_r($request->all());
 	    die('aaaa');*/

		  //print_r($request->agentId);exit;
          /*$agent = DB::table('agents')
            ->select('agents.*')
			->where('agentId', $request->agentId)
            ->get();

			*/
			/*$agent=DB::select( DB::raw("SELECT agents.*,agentlevels.*
FROM `agents`
left join `agentlevels` ON agents.levelID=agentlevels.levelID
Where agents.agentId=".$request->agentId) );*/
//
/*$agent=DB::select( DB::raw("SELECT agents.*,agentlevels.*,a.*
FROM `agents`
left join `agentlevels` ON agents.levelID=agentlevels.levelID
LEFT join `agentPromotion` ON agents.agentId=agentPromotion.agentId
Left join `agentmanagers` as a ON a.agentId=agentPromotion.agentId
Where agents.agentId=".$request->agentId) );*/

/*$agent=DB::select( DB::raw("SELECT b.*,al.*,a.*,(SELECT awp.agentId
FROM `agentmanagers` as am
LEFT JOIN `agentwisePromotioncode` as awp ON awp.agentPromotionId=am.managerId
WHERE am.agentId IN (SELECT am.agentId
FROM `agentwisePromotioncode` as awp
LEFT JOIN agentmanagers as am ON am.agentId=awp.agentPromotionId
where awp.agentId=$request->agentId)) as manager_id,
(SELECT awp.agentId
FROM `agentmanagers` as am
LEFT JOIN `agentwisePromotioncode` as awp ON awp.agentPromotionId=am.managerId
WHERE am.agentId IN (SELECT am.agentId
FROM `agentwisePromotioncode` as awp
LEFT JOIN agentmanagers as am ON am.agentId=awp.agentPromotionId
where awp.agentId=$request->agentId) )as state_manager
FROM `agents` AS b
left join `agentlevels` AS al ON b.levelID=al.levelID
LEFT join `agentPromotion` as ap ON b.agentId=ap.agentId
Left join `agentmanagers` as a ON a.agentId=ap.agentId
Where b.agentId=$request->agentId and ap.enddate IS null") );*/
$agent=DB::select( DB::raw("SELECT b.*,al.*,a.*,ap.*
FROM `agents` AS b
left join `agentlevels` AS al ON b.levelID=al.levelID
LEFT join `agentPromotion` as ap ON b.agentId=ap.agentId
Left join `agentmanagers` as a ON a.agentId=ap.agentId
Where ap.agentId=$request->agentId and ap.enddate IS NULL
ORDER by ap.agentPromotionId DESC Limit 0,1") );


		  /* $agentPayment = DB::select( DB::raw("SELECT agentpayment.Commission,agentpayment.PaymentMode,
		   agentpayment.feeAmount,agentpayment.naration
		   from agentpayment
Where AgentId=".$request->agentId) );*/

/*$agentManager =  DB::select( DB::raw("SELECT awp.agentName,awp.agentCode
FROM `agentmanagers` as am
LEFT JOIN `agentwisePromotioncode` as awp ON awp.agentPromotionId=am.managerId
WHERE am.agentId IN (SELECT am.agentId
FROM `agentwisePromotioncode` as awp
LEFT JOIN agentmanagers as am ON am.agentPromotionId=awp.agentPromotionId
where awp.agentId=$request->agentId)"));*/
$agentManager =  DB::select( DB::raw("select concat(agents.firstName,' ',agents.lastName) as agentName, agents.agentCode
from agentmanagers
LEFT JOIN agents ON agents.agentId=agentmanagers.managerId
where agentmanagers.agentId=$request->agentId order by id DESC limit 0,1"));

/*$stateManager =DB::select( DB::raw("SELECT awp.agentName,awp.agentCode
FROM `agentmanagers` as am
LEFT JOIN `agentwisePromotioncode` as awp ON awp.agentPromotionId=am.stateManagerId
WHERE am.agentId IN (SELECT am.agentId
FROM `agentwisePromotioncode` as awp
LEFT JOIN agentmanagers as am ON am.agentPromotionId=awp.agentPromotionId
where awp.agentId=$request->agentId)"));*/

$stateManager =DB::select( DB::raw("select concat(agents.firstName,' ',agents.lastName) as agentName, agents.agentCode
from agentmanagers
LEFT JOIN agents ON agents.agentId=agentmanagers.stateManagerId
where agentmanagers.agentId=$request->agentId order by id DESC limit 0,1"));


$all_agent_total_commission=DB::select( DB::raw("SELECT round(sum(`agent_wise_commission`.`total_commission`),2) as all_agent_total_commission
FROM `agent_wise_commission` where `agent_wise_commission`.`AgentId`=$request->agentId") );


//
$agentLevel=DB::select( DB::raw("SELECT `levelID`,`LevelName` FROM `agentlevels`") );


		   if($agent)
			  return response()->json([
        'status'=>'200',
			  'agent'=>$agent,
			  'agentManager'=>$agentManager,
			  'stateManager'=>$stateManager,
        'agentLevel'=>$agentLevel,
        'total_commission_payable'=>$all_agent_total_commission,

           ]);
     }
	 /*agent by id name end*/
	 /*insert agent manager start*/
	  public function insertAgentManager(Request $request)
     {
		    //echo "<pre>";
			//print_r($request->all());exit;
			 $currentAgentId =$request->currentAgent;
              $count = DB::table('agentmanagers')
            ->select('agentId')
            ->where('agentId', '=', $currentAgentId)
            ->count();
             //echo"$count";exit;
              if($count > 0)
              {
				DB::table('agentmanagers')->where('agentId', '=', $currentAgentId)->delete();
				//echo  $currentAgentId;exit;
			 for($i=0; $i<count($request->selectetedmanager);$i++)
			 {
				$insertAgent = DB::table('agentmanagers')->insert([
							'agentId' => $currentAgentId,
							'managerId' =>$request->selectetedmanager[$i]['id']
					       ]);
			  }
			  }
   			else
			{
			 for($i=0; $i<count($request->selectetedmanager);$i++)
			 {
				 $insertAgent = DB::table('agentmanagers')->insert([
							'agentId' => $currentAgentId,
							'managerId' =>$request->selectetedmanager[$i]['id']
					       ]);

				 }
			}

		   if($insertAgent)
		   {
			  return response()->json([
              'status'=>'200',
			  'message'=>'Manager Added Sucess'
			   ]);
		   }
		   else {
			    return response()->json([
              'manager'=>'202',
			  'message'=>'something went wrong'
			   ]);
		   }
     }
	 /*insert agent manager end*/
	 /*get manager by agent start*/
	 public function getManagerByAgent(Request $request){
		 //print_r($request->all());exit;
		 // $currentAgentId =$request->currentAgent;
		     $agent_label = $request->agentLevel;
			 //echo $agent_label;exit;

		  /*$agentManager = DB::table('agents')
		    ->select('agents.firstName','agents.lastName','agents.agentId')
            ->Join('agentmanagers', 'agents.agentId', '=', 'agentmanagers.managerId')
			->where('agentmanagers.agentId','=',$currentAgentId)
            ->get();*/
			//$agentManager = DB::select( DB::raw("SELECT b.firstName,b.lastName,b.agentId FROM `agents` b
          // WHERE b.levelID >(select a.levelID from agents a where a.agentId=".$currentAgentId.")and b.isActive=0") );
		    $agentManager = DB::select( DB::raw("SELECT b.firstName,b.lastName,b.agentId,b.agentCode FROM `agents` b
           WHERE b.levelID > $agent_label and b.isActive=0") );

			 if($agentManager)
		   {
			  return response()->json([

			  'status'=>'200',
			  'agentManager'=>$agentManager,
			   ]);
		   }
		   else {
			    return response()->json([
				 'status'=>'202',

			  'message'=>'something went wrong'
			   ]);
		   }

	 }
	/*get manager by agent end*/
	/*get state manager by manager start*/
	 public function getStateManagerByManager(Request $request){
		 // print_r($request->all());exit;
		  $currentManager =$request->currentManager;
		  /*$agentManager = DB::table('agents')
		    ->select('agents.firstName','agents.lastName','agents.agentId')
            ->Join('agentmanagers', 'agents.agentId', '=', 'agentmanagers.managerId')
			->where('agentmanagers.agentId','=',$currentAgentId)
            ->get();*/
			$stateManager = DB::select( DB::raw("SELECT b.firstName,b.lastName,b.agentId,b.agentCode FROM `agents` b
WHERE b.levelID >(select a.levelID from agents a where a.agentId=".$currentManager.") and b.isActive=0") );

			 if($stateManager)
		     {
			  return response()->json([

			  'status'=>'200',
			  'stateManager'=>$stateManager,
			   ]);
		   }
		   else {
			    return response()->json([
				 'status'=>'202',
                 'message'=>'something went wrong'
			   ]);
		   }

	 }
	/*get state manager by manager end*/

	/*agent comission start*/
	 public function agentCommision()
	 {
	   $agentComission = DB::table('agentpayment')
		    ->select('agentpayment.AgentId','agentpayment.CustomerFee','agentpayment.Commission','agents.firstName',
			  'agents.lastName' )
            ->Join('agents', 'agents.agentId', '=', 'agentpayment.AgentId')
            ->get();
			 if($agentComission)
		   {
			  return response()->json([

			  'status'=>'200',
			  'agentComission'=>$agentComission,
			   ]);
		   }
		   else {
			    return response()->json([
				 'status'=>'202',

			  'message'=>'something went wrong'
			   ]);
		   }

	 }
	 /*agent commission end*/
	 /*member wise commision start*/
	  public function memberWiseCommision(Request $request )
	 {

			  /* $agentCommision = DB::select( DB::raw("SELECT (@a:=@a+1) AS serial_number,A.agentCode, CONCAT(A.firstName,' ',A.LastName)
			  As agentName, A.levelID, AL.FirstYrComRate, DATE_FORMAT(ATF.PayDate, '%m-%d-%Y') as PayDate,
			  round(ATF.TotalFee,2) payablecommission ,
			  round((case when (ATF.adhocPayment <>0) THEN ATF.adhocPayment ELSE 0 END),2) adjustmentcommision,
			  round((ATF.TotalFee-ATF.adhocPayment),2) as paidcommission

              FROM agenttotalfee as ATF
			  join (SELECT @a:= 0) AS a
              LEFT JOIN agents as A ON ATF.AgentId =A.agentId
              LEFT JOIN agent_wise_commission as AWC ON A.agentId = AWC.AgentId
              LEFT JOIN agentlevels as AL ON AL.levelID = A.levelID
              where ATF.IsPaid=1
			  ") ); */
			 // date_default_timezone_set('Asia/Kolkata');
		 if($request->startDate)
		 {
			$startDate_timestamp = strtotime($request->startDate);
			$startDate = date("Y-m-d", $startDate_timestamp);
		 }else{
			$startDate='';
		 }
		 if($request->endDate){
			 $endDate_timestamp = strtotime($request->endDate);
			 $endDate = date("Y-m-d", $endDate_timestamp);
		 }else{
			 $endDate='';
		 }


			if($startDate!='' && $endDate!=''){
				$where="Where apt.PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate!='' && $endDate==''){
				$endDate=date("Y-m-d");
			  $where="Where apt.PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate=='' && $endDate!=''){
			   $where="Where apt.PaymentDate <='".$endDate."'";
			}else{
				$where=' Where 1';
			}

			$sql="SELECT a.agentId, concat(ag.firstName,' ',ag.lastName) as agent_name,  a.customerId,customerName, a.client_type, a.membership_plan, a.fees, a.groupCode, a.agent_commision, a.agent_chargeBack_commision as agent_advance_comission, a.agent_interest, a.renewal_commision, a.manager_Commision, a.manager_interest, a.state_manager_commission, a.state_manager_interest, a.PaymentDate, a.cancelDate, a.commissionRate,a.chargebackAmount, a.earned_commission
FROM agent_paid_commission_details as a
left join agents as ag ON a.agentId=ag.agentId
left join agent_wise_commission as awc ON a.agentId=awc.AgentId
left join agentpayment as apt ON a.customerId=apt.customerId ".$where." ";
			//echo $sql; exit;
			$sql1 = "SELECT  SUM(a.earned_commission) as earned_commission
FROM agent_paid_commission_details as a";

			$agentCommision =  DB::select( DB::raw($sql));
			$agentTotalCommision =  DB::select( DB::raw($sql1));
			 if($agentCommision)
		   {
			  return response()->json([

			  'status'=>'200',
			  'agentCommision'=>$agentCommision,
			  'agentTotalCommision'=>$agentTotalCommision
			   ]);
		   }
		   else {
			    return response()->json([
				 'status'=>'202',

			  'message'=>'something went wrong'
			   ]);
		   }
	 }
	 /*member wise commission end*/
	/*paid commission start*/
	   public function paid_commision(Request $request){
		     //print_r($request->all());exit;
		    if($request->startDate)
		 {
			$startDate_timestamp = strtotime($request->startDate);
			$startDate = date("Y-m-d", $startDate_timestamp);
		 }else{
			$startDate='';
		 }
		 if($request->endDate){
			 $endDate_timestamp = strtotime($request->endDate);
			 $endDate = date("Y-m-d", $endDate_timestamp);
		 }else{
			 $endDate='';
		 }


			if($startDate!='' && $endDate!=''){
				$where="Where a.agentId = '".$request->agentId."' apt.PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate!='' && $endDate==''){
				$endDate=date("Y-m-d");
			  $where="Where a.agentId = '".$request->agentId."' AND apt.PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate=='' && $endDate!=''){
			   $where=" Where a.agentId = '".$request->agentId."' AND apt.PaymentDate <='".$endDate."'";
			}else{
				$where=" Where a.agentId = '".$request->agentId."'";
			}

			$sql="SELECT a.agentId, concat(ag.firstName,' ',ag.lastName) as agent_name,  a.customerId,customerName, a.client_type, a.membership_plan, a.fees, a.groupCode, a.agent_commision, a.agent_chargeBack_commision as agent_advance_comission, a.agent_interest, a.renewal_commision, a.manager_Commision, a.manager_interest, a.state_manager_commission, a.state_manager_interest, a.PaymentDate, a.cancelDate, a.commissionRate,a.chargebackAmount, a.earned_commission
FROM agent_paid_commission_details as a
left join agents as ag ON a.agentId=ag.agentId
left join agent_wise_commission as awc ON a.agentId=awc.AgentId
left join agentpayment as apt ON a.customerId=apt.customerId ".$where." ";
            $totalPaidCommision =  DB::select( DB::raw("SELECT  SUM(earned_commission) as totalPaidCommissions
FROM agent_paid_commission_details Where agentId =  '".$request->agentId."'"));
			//echo $sql; exit;
			$agentCommision =  DB::select( DB::raw($sql));
			 if($agentCommision)
		   {
			  return response()->json([

			  'status'=>'200',
			  'agentCommision'=>$agentCommision,
			   'totalPaidCommision' => $totalPaidCommision
			   ]);
		   }
		   else {
			    return response()->json([
				 'status'=>'202',

			  'message'=>'something went wrong'
			   ]);
		   }

	   }
		/*paid commission end*/
		/*agent member commission details start*/
	   public function agent_member_commision_detail(Request $request)
	 {

		 //date_default_timezone_set('Asia/Kolkata');
		 if($request->startDate)
		 {
			$startDate_timestamp = strtotime($request->startDate);
			$startDate = date("Y-m-d", $startDate_timestamp);
		 }else{
			$startDate='';
		 }
		 if($request->endDate){
			 $endDate_timestamp = strtotime($request->endDate);
			 $endDate = date("Y-m-d", $endDate_timestamp);
		 }else{
			 $endDate='';
		 }


			if($startDate!='' && $endDate!=''){
				$where="Where apt.PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate!='' && $endDate==''){
				$endDate=date("Y-m-d");
			  $where="Where apt.PaymentDate BETWEEN '".$startDate."' and '".$endDate."'";
			}else if($startDate=='' && $endDate!=''){
			   $where="Where apt.PaymentDate <='".$endDate."'";
			}else{
				$where=' Where 1';
			}

			/*$sql="SELECT a.agentId, concat(ag.firstName,' ',ag.lastName) as agent_name,  a.customerId,customerName, a.client_type, a.membership_plan, a.fees, a.groupCode, a.agent_commision, a.agent_chargeBack_commision, a.agent_interest, a.renewal_commision, a.manager_Commision, a.manager_interest, a.state_manager_commission, a.state_manager_interest,
      a.PaymentDate, a.cancelDate, a.commissionRate,a.chargebackAmount, awc.total_commission
			FROM agent_commission_details as a
			left join agents as ag ON a.agentId=ag.agentId
			left join agent_wise_commission as awc ON a.agentId=awc.AgentId
			left join agentpayment as apt ON a.customerId=apt.customerId ".$where."";*/

       /*  backup of 27Feb2020     */
	   /*
      $sql="SELECT a.agentId, concat(ag.firstName,' ',ag.lastName) as agent_name,  a.customerId, customerName, a.client_type, a.membership_plan, a.fees, a.groupCode, a.agent_commision, a.agent_chargeBack_commision, a.agent_interest, a.renewal_commision, a.manager_Commision, a.manager_interest, a.state_manager_commission, a.state_manager_interest,
      a.PaymentDate, a.cancelDate, a.commissionRate,a.chargebackAmount, a.earned_commission
			FROM agent_commission_details as a
			left join agents as ag ON a.agentId=ag.agentId
			left join agent_wise_commission as awc ON a.agentId=awc.AgentId
			left join agentpayment as apt ON a.customerId=apt.customerId ".$where."";
            */
			$sql="SELECT a.agentId, concat(ag.firstName,' ',ag.lastName) as agent_name, a.customerId, a.customerName, a.client_type, a.membership_plan, a.fees, a.groupCode, a.agent_commision, a.agent_chargeBack_commision as agent_advance_comission, a.agent_interest, a.renewal_commision, a.manager_Commision, a.manager_interest, a.state_manager_commission, a.state_manager_interest,
            a.PaymentDate, a.cancelDate, a.commissionRate,a.chargebackAmount, a.earned_commission
	        FROM agent_commission_details as a left join agents as ag ON a.agentId=ag.agentId";
			
			
			
			
            $sql1 ="SELECT ROUND(SUM(total_commission), 2) AS totalMemberCommi FROM agent_wise_commission  ";
			//echo $sql; exit;
			$commisionByMember =  DB::select( DB::raw($sql));
			$totalMemberCommi =  DB::select( DB::raw($sql1));
      
		 

         
			//echo $sql; exit;
			$commisionByMember =  DB::select( DB::raw($sql));
			$totalMemberCommi =  DB::select( DB::raw($sql1));

			if($commisionByMember)
		   {
			  return response()->json([

			  'status'=>'200',
			  'agentCommision'=>$commisionByMember,
			  'totalMemberCommi'=>$totalMemberCommi,
			   ]);
		   }else{
			    return response()->json([
				 'status'=>'202',

			  'message'=>'something went wrong'
			   ]);
		   }
	 }
	 /*agent member commission end*/
  // This is used for agent Update start
	 public function updateagent(Request $request){

	     /* echo "<pre>";
	   print_r($request->all());
       exit; */
	   $isActive = $request->isActive;
	  // echo  $isActive;exit;
	  if($isActive == '1'){
		 // echo "inactive";
		  $agentid_update = array('agentId'=>'1012');
		  //house Accound id 1012
		  //$agentPaimentDone =DB::table('agents')->where('agentId',$request->agentId)->update($postArray);
		  $customerId = DB::table('customers')->where('agentId',$request->agentId)->update($agentid_update);
		    // $pormotion_detail =  $this->fetch_promotion_id($request->agentId);
			 //Array ( [agentPormotionId] => 16 [managerPormotionId] => 13 [managerId] => 16 [stateManagerPormotionId] => 5 [stateManagerId] => 5 )
			  $agentid_update = array('agentId'=>'1012',
			                           'agentPromotionId'=>'39',


			  );
			 //print_r($pormotion_detail);exit;
		  $agtent_pormotion_Id = DB::table('agentpayment')->where('agentId',$request->agentId)->update($agentid_update);
	  }

	 // exit;
     if(empty($request->agentManager)){$request->agentManager=0;}
     if(empty($request->agent_StateManager)){$request->agent_StateManager=0;}

	   //echo $request->agentId;exit;
     $NewagentPromotionID=[];
	   $postArray = [

	      'firstName' =>$request->firstName,
		  'middleName' =>$request->middleName,
			  'lastName'=>$request->lastName,
			  'dob' =>$request->dob,
			  'cellPhone'=>$request->cellPhone,
		    'alt_phone_num'=>$request->alternatemobilenumber,
			  'email'=>$request->email,
			  'country'     => $request->country,
	      'country1'     => $request->country1,
			  'zip'=>$request->zip,
			  'zip1'=>$request->zip1,
			  'state'=>$request->state,
			  'state1'=>$request->state1,
			  'address1'=>$request->address1,
			  'address2'=>$request->address2,
			  'address3'=>$request->address3,
			  'address4'=>$request->address4,
			  'city'=>$request->city,
			  'city1'=>$request->city1,
			  'isActive' =>$request->isActive,
        'levelID'=>$request->agentLevel,
        'note'=>$request->note,
			 ];
       $agentPaimentDone =DB::table('agents')->where('agentId',$request->agentId)->update($postArray);
		//$agentPaimentDone =DB::table('agents')->where('agentId',$request->agentId)->update($postArray);
        $agent_promotion_done_or_not=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentId' AND `agentPromotion`.`level`=$request->agentLevel and `agentPromotion`.`enddate` IS NULL"));
            //updating override commision in testing mode

			// print_r($pormotion_detail);exit;
			  $overRideCommission=[
                      'OverrideCommisionAnual'=>$request->overrideCommisionAnual,
                      'OverRideCommisionLifeTime'=>$request->overrideCommisionFiveYearLifeTime,



                      ];

			 // print_r( $agent_promotion_done_or_not);exit;

			  if(!empty($agent_promotion_done_or_not)){
				  //$pormotion_detail = $this->fetch_promotion_id($request->agentId);
			 $overRideCommissionUpdate =DB::table('agentPromotion')->where('agentPromotionId',$agent_promotion_done_or_not[0]->agentPromotionId)->update($overRideCommission);
			    //Satyam
				/* $agentManagerPromotion=[
                      'agentId'=>$request->agentId,
                      'agentPromotionId'=>$agent_promotion_done_or_not[0]->agentPromotionId,
                      'managerId'=>$pormotion_detail['managerId'],
                      'managerPromotionId'=>$pormotion_detail['managerPormotionId'],
                      'stateManagerId'=>$pormotion_detail['stateManagerId'],
                      'stateManagerPromotionId'=>$pormotion_detail['stateManagerPormotionId'],


                      ];
					//  print_r($agentManagerPromotion);exit;
                      $NewagentPromotionID =DB::table('agentmanagers')->where('agentPromotionId',$agent_promotion_done_or_not[0]->agentPromotionId)->update($agentManagerPromotion); */
			  }


			  /* else  {

				  $agent_promotion_EndDate=$agentCommision = DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentId' and `agentPromotion`.`enddate` IS NULL") );
				  $select_Manager_promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`=".$request->agentManager." AND `agentPromotion`.`enddate` IS NULL"));
                    $select_State_Manager_Promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`=".$request->agentStateManager." AND `agentPromotion`.`enddate` IS NULL"));

                 if($agent_promotion_EndDate){
                   foreach ($agent_promotion_EndDate as $key => $value) {
                     // code...
                     //echo $value->agentPromotionId;
                     $postArray = [
                			  'enddate' =>date('Y-m-d'),

                			 ];
                       $agentPromotionEndDate=DB::table('agentPromotion')->where('agentPromotionId',$value->agentPromotionId)->update($postArray);
                       $agentPromotion=[
                        'agentId'=>$request->agentId,
                        'level'=>$request->agentLevel,
						'OverrideCommisionAnual'=>$request->overrideCommisionAnual,
                        'OverRideCommisionLifeTime'=>$request->overrideCommisionFiveYearLifeTime,

                      ];
                       $agentPromotionID=DB::table('agentPromotion')->insertGetId($agentPromotion);
				  $agentManagerPromotion=[
                      'agentId'=>$request->agentId,
                      'agentPromotionId'=>$agentPromotionID,
                      'managerId'=>$request->agentManager,
                      'managerPromotionId'=>$select_Manager_promotion_id[0]->agentPromotionId,
                      'stateManagerId'=>$request->agentStateManager,
                      'stateManagerPromotionId'=>$select_State_Manager_Promotion_id[0]->agentPromotionId,

                      ];
                      $NewagentPromotionID=DB::table('agentmanagers')->insert($agentManagerPromotion);

			  }
				 }
			  } */

			//end

       if(!empty($agent_promotion_done_or_not[0]->agentPromotionId)){
		   /* echo "SELECT managerId,managerPromotionId,stateManagerId,stateManagerPromotionId FROM `agentmanagers` WHERE `agentmanagers`.`agentPromotionId`=".$agent_promotion_done_or_not[0]->agentPromotionId." order By agentmanagers.agentPromotionId DESC Limit 0,1";
		   die('aaaa'); */
         $previous_agent_manager_data=DB::select( DB::raw("SELECT managerId,managerPromotionId,stateManagerId,stateManagerPromotionId FROM `agentmanagers` WHERE `agentmanagers`.`agentPromotionId`=".$agent_promotion_done_or_not[0]->agentPromotionId." order By agentmanagers.agentPromotionId DESC Limit 0,1"));
          if(($request->agentManager!=0)&&($request->agent_StateManager!=0)){

                    if(!empty($agent_promotion_done_or_not[0]->managerId)){
                      $database_Manager=$previous_agent_manager_data[0]->managerId;
                      $database_Manager_PromotionId=$previous_agent_manager_data[0]->managerPromotionId;
                    }else{
                      $database_Manager=0;
                      $database_Manager_PromotionId=0;
                    }
                    if(!empty($agent_promotion_done_or_not[0]->stateManagerId)){
                      $database_State_Manager=$previous_agent_manager_data[0]->stateManagerId;
                      $database_State_Manager_PromotionId=$previous_agent_manager_data[0]->stateManagerPromotionId;
                    }else{
                      $database_State_Manager=0;
                      $database_State_Manager_PromotionId=0;
                    }
                    $select_Manager_promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`=".$request->agentManager." AND `agentPromotion`.`enddate` IS NULL"));
                    $select_State_Manager_Promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`=".$request->agent_StateManager." AND `agentPromotion`.`enddate` IS NULL"));

                    if(($database_Manager==$request->agentManager)&&($database_State_Manager==$request->agent_StateManager)){

                      $agentManagerPromotion=[
                      'agentId'=>$request->agentId,
                      'agentPromotionId'=>$agent_promotion_done_or_not[0]->agentPromotionId,
                      'managerId'=>$request->agentManager,
                      'managerPromotionId'=>$select_Manager_promotion_id[0]->agentPromotionId,
                      'stateManagerId'=>$request->agent_StateManager,
                      'stateManagerPromotionId'=>$select_State_Manager_Promotion_id[0]->agentPromotionId,


                      ];
                      $NewagentPromotionID =DB::table('agentmanagers')->where('agentPromotionId',$agent_promotion_done_or_not[0]->agentPromotionId)->update($agentManagerPromotion);
                    }else{
                      $agentManagerPromotion=[
                      'agentId'=>$request->agentId,
                      'agentPromotionId'=>$agent_promotion_done_or_not[0]->agentPromotionId,
                      'managerId'=>$request->agentManager,
                      'managerPromotionId'=>$select_Manager_promotion_id[0]->agentPromotionId,
                      'stateManagerId'=>$request->agent_StateManager,
                      'stateManagerPromotionId'=>$select_State_Manager_Promotion_id[0]->agentPromotionId,

                      ];
                      $NewagentPromotionID=DB::table('agentmanagers')->insert($agentManagerPromotion);
                    }
          }
          elseif(($request->agentManager!=0)&&($request->agent_StateManager==0)){

                    $select_Manager_promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`=".$request->agentManager." AND `agentPromotion`.`enddate` IS NULL"));
                    $agentManagerPromotion=[
                    'agentId'=>$request->agentId,
                    'agentPromotionId'=>$agent_promotion_done_or_not[0]->agentPromotionId,
                    'managerId'=>$request->agentManager,
                    'managerPromotionId'=>$select_Manager_promotion_id[0]->agentPromotionId,
                    'stateManagerId'=>0,
                    'stateManagerPromotionId'=>NULL,

                  ];
                  //print_r($agentManagerPromotion);
                  //die('aaaa');
                  $NewagentPromotionID =DB::table('agentmanagers')->where('agentPromotionId',$agent_promotion_done_or_not[0]->agentPromotionId)->update($agentManagerPromotion);
                  if($NewagentPromotionID==0){
                    $NewagentPromotionID=DB::table('agentmanagers')->insert($agentManagerPromotion);
                  }
          }else{
                    $agentManagerPromotion=[
                    'agentId'=>$request->agentId,
                    'agentPromotionId'=>$agent_promotion_done_or_not[0]->agentPromotionId,
                    'managerId'=>0,
                    'managerPromotionId'=>0,
                    'stateManagerId'=>0,
                    'stateManagerPromotionId'=>0,

                  ];
                  //print_r($agentManagerPromotion);
                  //die('aaaa');
                  $NewagentPromotionID =DB::table('agentmanagers')->where('agentPromotionId',$agent_promotion_done_or_not[0]->agentPromotionId)->update($agentManagerPromotion);
                  $NewagentPromotionID =true;
              }
    }

	else{
                 $agent_promotion_EndDate=$agentCommision = DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentId' and `agentPromotion`.`enddate` IS NULL") );
                 if($agent_promotion_EndDate){
                   foreach ($agent_promotion_EndDate as $key => $value) {
                     // code...
                     //echo $value->agentPromotionId;
                     $postArray = [
                			  'enddate' =>date('Y-m-d'),

                			 ];
                       $agentPromotionEndDate=DB::table('agentPromotion')->where('agentPromotionId',$value->agentPromotionId)->update($postArray);
                       $agentPromotion=[
                        'agentId'=>$request->agentId,
                        'level'=>$request->agentLevel,
						'OverrideCommisionAnual'=>$request->overrideCommisionAnual,
                        'OverRideCommisionLifeTime'=>$request->overrideCommisionFiveYearLifeTime,

                      ];
                       $agentPromotionID=DB::table('agentPromotion')->insert($agentPromotion);
                   }
                 }
                 $agentManagerPromotionId="";
                 $agentStateManagerPromotionId="";
                 $agentPromotionId="";
                 $agent_promotion_new_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentId' and `agentPromotion`.`enddate` IS NULL") );
                 foreach ($agent_promotion_new_id as $key => $value) {
                   // code...
                   $agentPromotionId=$value->agentPromotionId;
                 }
                 if($request->agentManager){
                     $agent_manager_promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentManager' and `agentPromotion`.`enddate` IS NULL") );
                     foreach($agent_manager_promotion_id as $key => $value) {
                       // code...
                       $agentManagerPromotionId=$value->agentPromotionId;
                     }
                   }else{
                     $request->agentManager=0;
                     $agentManagerPromotionId=0;
                   }
                 if($request->agent_StateManager){
                     $agent_state_manager_promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agent_StateManager' and `agentPromotion`.`enddate` IS NULL") );
                     foreach($agent_state_manager_promotion_id as $key => $value) {
                       // code...
                       $agentStateManagerPromotionId=$value->agentPromotionId;
                     }
                  }else{
                    $request->agent_StateManager=0;
                    $agentStateManagerPromotionId=0;
                  }
                 //agentId
                 $agentManagerPromotion=[
                  'agentId'=>$request->agentId,
                  'agentPromotionId'=>$agentPromotionId,
                  'managerId'=>$request->agentManager,
                  'managerPromotionId'=>$agentManagerPromotionId,
                  'stateManagerId'=>$request->agent_StateManager,
                  'stateManagerPromotionId'=>$agentStateManagerPromotionId,

                ];
                $NewagentPromotionID=DB::table('agentmanagers')->insert($agentManagerPromotion);
        }

     if($NewagentPromotionID || $agentPaimentDone)
	   {
			  return response()->json([
              'status'=>'200',
           ]);
	   }
     else	{
		  return response()->json([
              'status'=>'203',
			   ]);
	 }

	 }
		/*agent update end*/
		/*agent email checking start*/
	public function agentEmailChecking(Request $request){
		 /* echo "<pre>";
		  print_r($request->all());
		  die('aaaa');
		  */
		  //SELECT * FROM `customers` WHERE `email` LIKE '%nehru@gmail.com%'
		    $duplicateEmail = DB::table('agents')
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
		/*agent email checking end*/
	  /*agent cell phone checking start*/
	  public function agentCellPhoneChecking(Request $request){
		  /*echo "<pre>";
		  print_r($request->all());
		  die('aaaa');
		  */
		    $duplicateEmail = DB::table('agents')
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
	  /*agent cell phone checking end*/
	  /*agent status start*/
	   public function agentStatus(Request $request)
       {
			$agentStatus = DB::table('agents')->select('agentId','isActive','email')->where('agentId',$request->agentId)->get();
			  if($agentStatus) {
				   return response()->json([
					 'status' =>  200,
					 'agentStatus' => $agentStatus,


				   ]);

                 }
	   }
	   /*agent status end*/
	   /*agent code search start*/
	   public function agentCodeSearch(Request $request)
       {
		   //print $request->agentCode;exit;
			 $duplicateAgentCode = DB::table('agents')
                                 ->where( [
                                     'agentCode' => $request->agentCode,
                                 ] )->first();
                if ( $duplicateAgentCode ) {
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
	   /*agent code search end*/
	   /*total agent start*/
	    public function totalAgents(Request $request)
       {
				 $totalAgents = DB::table('agents')
		  ->select('agentId', 'firstName', 'lastName','dob','isActive')
		  ->orderBy('agentId', 'desc')->get();
		  $totalAgents = count($totalAgents);

	   if (  $totalAgents ) {
                    return response()->json([
              'status'=>'200',
			  'totalAgents'=>$totalAgents
			   ]);
                  }
				  else{

                    return response()->json([
              'status'=>'203',
			   ]);
                  }
	   }
	   /*total agent end*/
	   /*all agent by id name start*/
	   public function AllAgentByIdNameCode(Request $request)
      {
        $agent_details=[];
		$agentStatus=DB::select( DB::raw("SELECT agentId, CONCAT(firstName,' ',LastName,'-',agentCode) as agent FROM agents WHERE agentCode LIKE '%".$request->agentId."%' or firstName LIKE '%".$request->agentId."%' or lastName LIKE '%".$request->agentId."%'") );
		foreach($agentStatus as $row){
			 $agent_details[]=['id'=>$row->agentId, 'text'=>$row->agent];
			}
			if($agentStatus) {
				   echo json_encode($agent_details);
                }
       }
	   /*all agent by id name end*/
	   //This  is use for agent promotion table start
public function agentPromotion()
{
 $agentStatus=DB::select( DB::raw("SELECT agentId,levelID FROM agents") );
 foreach($agentStatus as $row){
    $group = [
         'agentId'      => $row->agentId,
         'level'      => $row->levelID
       ];
    DB::table('agentPromotion')->insert(
              $group
              );
   }


}
/*agent promotion end*/
/*sale report start*/
public function SellReport(Request $request)
  {
	       /*   echo "<pre>";
		print_r($request->all());
		exit;  */
		//$group_id = $request->groupName['label'];
		$group_id = $request->groupName['value'];
		//echo $group_id;exit;
		 if($group_id  !=''){
			//$group_id = $request->group_id;
			//$group = "and groups.groupName = '$group_id'";
			$group = "and groups.groupId = ".$group_id;

		}
		else{
			$group = 'and 1=1 ';
		}
		$location = $request->location;
		//echo $location;exit;
		/* if($request->location !=''){
			$location = $request->location;
			//$location = "and groups.groupId = ".$group_id;

		}
	  else {
			$location = '%a%';
		} */
		if($location  !=''){
			//$group_id = $request->group_id;
			//$group = "and groups.groupName = '$group_id'";
			$location = "and groups.state = '$location'";

		}
		else{
			$location = 'and 1=1 ';
		}
		date_default_timezone_set('Asia/Kolkata');
		 if($request->startDate)
		 {
			$startDate_timestamp = strtotime($request->startDate);
			$startDate = date("Y-m-d", $startDate_timestamp);
		 }else{
			$startDate='';
		 }
		 if($request->endDate){
			 $endDate_timestamp = strtotime($request->endDate);
			 $endDate = date("Y-m-d", $endDate_timestamp);
		 }else{
			 $endDate='';
		 }


			if($startDate!='' && $endDate!=''){
				$where="Where customers.isPaidCustomer='1' and agentpayment.PaymentDate BETWEEN '".$startDate."' and '".$endDate."' and agentpayment.is_agent_change='0' $group $location";
			}else if($startDate!='' && $endDate==''){
				$endDate=date("Y-m-d");
			  $where="Where customers.isPaidCustomer='1' and agentpayment.PaymentDate BETWEEN '".$startDate."' and '".$endDate."' and agentpayment.is_agent_change='0' $group $location";
			}else if($startDate=='' && $endDate!=''){
			   $where="Where customers.isPaidCustomer='1' and agentpayment.PaymentDate <='".$endDate."' and agentpayment.is_agent_change='0' $group $location";
			}else{
				$where="Where customers.isPaidCustomer='1' and agentpayment.is_agent_change='0' $group $location";
			}
			/*$SQL="SELECT customers.customerId, customers.firstName,customers.LastName, customers.initiationFee, agentpayment.PaymentDate, agentpayment.totalBurialFee,plans.planName, agentpayment.feeAmount, customers.initiationFee, agentpayment.totalBurialFee, agentpayment.feeAmount, groups.groupName,groups.state
FROM `agentpayment`
left JOIN customers ON agentpayment.customerId=customers.customerId
left join groups on groups.groupId=customers.groupId
left join plans on agentpayment.planId=plans.planId ".$where." ";	*/
		$SQL="SELECT customers.customerId, (case
WHEN (agents.middleName IS  not NULL) THEN (concat(agents.firstName,' ', agents.middleName,' ',agents.LastName))
  ELSE (concat(agents.firstName,' ',agents.LastName))
  END) as agentName, agents.city as agentCity,agents.state as agentstate,customers.firstName,customers.LastName, customers.initiationFee, agentpayment.PaymentDate, agentpayment.totalBurialFee,plans.planName, agentpayment.feeAmount,customers.state as state,
        customers.country,customers.zip,customers.city,agentpayment.PaymentMode,customers.initiationFee, agentpayment.totalBurialFee, agentpayment.feeAmount, groups.groupName,groups.state as state1
FROM `customers`
left JOIN agentpayment ON agentpayment.customerId=customers.customerId
left join groups on groups.groupId=customers.groupId
left join agents on agents.agentId=customers.agentId
left join plans on agentpayment.planId=plans.planId ".$where." ";
//echo $SQL;exit;
	    $sell_report = DB::select( DB::raw($SQL));
		$sell_report = DB::select( DB::raw($SQL));
		$total_monthly_member=DB::select( DB::raw("SELECT count(customerId) as totalCustomer
FROM `agentpayment`
left join plans ON agentpayment.planId=plans.planId

WHERE plans.planId ='1'"));
	$total_yearly_member=DB::select( DB::raw("SELECT count(customerId) as totalCustomer
FROM `agentpayment`
left join plans ON agentpayment.planId=plans.planId

WHERE plans.planId ='2'"));

$total_lifetime_member=DB::select( DB::raw("SELECT count(customerId) as totalCustomer
FROM `agentpayment`
left join plans ON agentpayment.planId=plans.planId

WHERE plans.planId ='4'  "));
$total_5yr_member=DB::select( DB::raw("SELECT count(customerId) as totalCustomer
FROM `agentpayment`
left join plans ON agentpayment.planId=plans.planId

WHERE plans.planId ='3'"));
$groupwise_total_member=DB::select( DB::raw("$SQL"));
	$groupwise_total_member=count(DB::select( DB::raw("$SQL")));
				  if($sell_report) {
           return response()->json([
             'status' =>  200,
             'sell_report' =>$sell_report,
			 'monthly_member'=>$total_monthly_member,
			 'yearly_member'=>$total_yearly_member,
			 'lifetime_member'=>$total_lifetime_member,
			 'Fiveyr_member'=>$total_5yr_member,
			 'groupwise_total_member' => $groupwise_total_member


           ]);

     }
	  else{
			 return response()->json([
             'status' =>  203,
             'sell_report'=>$sell_report

           ]);
		   }

  }
	/*sale report end*/
	/*is agent code start*/
  public function isAgentCode(Request $request){
	         // print $request->agentCode;exit;
	   $duplicateAgentCode = DB::table('agents')
                                 ->where( [
                                     'agentCode' => $request->agentCode,'isActive'=>'0'
                                 ] )->first();
                if ($duplicateAgentCode) {
				$agentName = 	DB::table('agents')->select('firstName','middleName','lastName','agentCode')
                                 ->where( [
                                     'agentCode' => $request->agentCode,'isActive'=>'0'
                                 ] )->first();

                    return response()->json([
              'status'=>'200',
			  'agentName'=>$agentName
			   ]);
                  }
				  else{

                    return response()->json([
              'status'=>'201',
			   ]);
                  }



  }
		/*isagent code end*/
		/*featching for promotion id start*/
  public function fetch_promotion_id($agentId){
	  $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agentId)->where ('enddate' ,NULL)->get();
		   // print_r($agent_Promotion_detail);exit;
		 $managerPormotion = DB::table('agentmanagers')->select('managerPromotionId','managerId')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->orderBy('id','desc')->limit(1)->get();
		    //print_r(   $managerPormotionId[0]->managerId);exit;
			  // print_r($managerPormotionId);exit;

		    if(@managerPormotion[0])
			{
				$managerPormotionId = $managerPormotion[0]->managerPromotionId;
				$managerId = $managerPormotion[0]->managerId;
				//echo "1";
				//echo $managerPormotionId[0]->managerId;
				//print_r($managerPormotion[0]);exit;
			}
			else {
				 $managerPormotionId = 0;
				 $managerId = 0;

			}
			//exit;

		   $manager_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId', $managerPormotionId)->get();
		   //print_r( $manager_Promotion_detail);exit;
		  $stateManagerPormotion = DB::table('agentmanagers')->select('stateManagerPromotionId','stateManagerId')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->orderBy('id','desc')->limit(1)->get();
		 // print_r($stateManagerPormotionId);exit;
		     if(@$stateManagerPormotion[0])
			 {
		    $stateManagerPormotionId = $stateManagerPormotion[0]->stateManagerPromotionId;
			$stateManagerId = $stateManagerPormotion[0]->stateManagerId;
			//echo"1";
			 }
			 else {
				$stateManagerPormotionId = 0;
				$stateManagerId =0;
				//echo"0";
			 }
			//exit;
		  /*$sate_mang__detail=DB::select( DB::raw("SELECT * FROM `agentPromotion` where agentId=".$stateManagerPormotionId." AND enddate IS null") );*/
		    $sate_mang__detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId',  $stateManagerPormotionId)->get();
			 return array('agentPormotionId'=>$agent_Promotion_detail[0]->agentPromotionId,'managerPormotionId'=>$managerPormotionId,'managerId'=>$managerId,'stateManagerPormotionId'=>$stateManagerPormotionId,'stateManagerId'=>$stateManagerId);
  }

	/*fetchinf for promotion id end*/
	/*fetch charge back commission start */
    public function fetch_charge_back_comm (Request $request){
	 //print_r($request->all())  ;exit;
	    $agentId   = $request->agentId;
	  if(is_numeric($agentId)AND $agentId >0){
		  //return $number;
		 // echo $agentId;exit;
		 //agent_paid_commission_details
		  $agentChargeBackComm = DB::select( DB::raw("Select customers.customerId,  concat(customers.firstName,' ',customers.LastName) customers_name,customers.clientType,plans.planName,agentpayment.feeAmount,agentpayment.Commission,agentpayment.Commission,agentpayable.agent_cherge_back,agentpayable.chageBackIntrest,agentpayable.agentChargebackAmount
FROM agentpayment
left join customers ON customers.customerId=agentpayment.customerId
left join agent_commission_details ON agentpayment.AgentId=agent_commission_details.agentId
left join plans ON plans.planId=customers.planId
left JOIN agentpayable on agentpayable.agentId=agentpayment.AgentId and agentpayable.customerId=agentpayment.customerId

WHERE  agentpayable.agentId=$agentId") );
  $total_commi =   DB::select( DB::raw("Select SUM(agentChargebackAmount) AS totalChargeBackAmount from agentpayable
WHERE agentpayable.agentId=$agentId") );
		   if ($agentChargeBackComm) {
                    return response()->json([
              'status'=>'200',
			   'agentChargeBackComm' => $agentChargeBackComm,
			   'totalChargeBackAmount' => $total_commi
			   ]);
                  }
				  else{

                    return response()->json([
              'status'=>'201',

			   ]);

	  }
	   }
	  else {
		  return response()->json([
              'status'=>'201',

			   ]);
	  }




	}
	/*fetch charge back commision end*/
	/*for random create number in database for opassword start*/
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
/*for random create number in database for opassword end*/
/*agent sell report start*/
public function agent_sell_report(Request $request)
  {
	   // print_r($request->all());exit;
		 $interval = $request->interval;
		 $agentId = $request->agentId['value'];
		 //echo $agentId;exit;
		date_default_timezone_set('Asia/Kolkata');
		 if($interval ==1)
		 {
			$startDate   =  'INTERVAL 1 DAY';
		 }else if($interval ==2){
			//$startDate   =  date("Y-m-d",strtotime("-30 day"));
			$startDate   =  'interval 1 month';
		 }
		 else if($interval ==3){

			//$startDate   =  date("Y-m-d",strtotime("-120 day"));
			$startDate   =  'interval 3 month';
		 }else{
			 $startDate   =  'interval 12 month';
		 }



			$SQL="SELECT agentpayment.feeAmount, agentpayment.newOrRenew, agentpayment.PaymentDate, plans.planName, concat(agents.firstName,' ', agents.lastName) as AgentName, agents.agentCode, concat(customers.firstName,' ', customers.LastName) as CustomerName,customers.clientType FROM agentpayment
LEFT JOIN plans ON agentpayment.planId = plans.planId
LEFT JOIN agents ON agentpayment.AgentId = agents.agentId
LEFT JOIN customers ON agentpayment.customerId = customers.customerId
WHERE agentpayment.PaymentMode !=''  AND agentpayment.AgentId=$agentId AND agentpayment.PaymentDate  > date_sub(now(),  $startDate)";
//echo $SQL;exit;
//echo $SQL;exit;
	    $agent_sell_report = DB::select( DB::raw($SQL));



				  if($agent_sell_report) {
           return response()->json([
             'status' =>  200,
             'agent_sell_report' =>$agent_sell_report,
           ]);

     }
	  else{
			 return response()->json([
             'status' =>  200,


           ]);
		   }

  }
	/*agent sale report end*/
		/*agent sale commission report start*/
	public function agent_sell_commi_rep(Request $request){

			//print_r($request->all());exit;

				$interval = $request->interval;
		 $agentId = $request->agentId;
		// echo $interval;exit;
		date_default_timezone_set('Asia/Kolkata');
		 if($interval ==1)
		 {
			$startDate   =  'INTERVAL 1 DAY';
		 }else if($interval ==2){
			//$startDate   =  date("Y-m-d",strtotime("-30 day"));
			$startDate   =  'interval 1 month';
		 }
		 else if($interval ==3){

			//$startDate   =  date("Y-m-d",strtotime("-120 day"));
			$startDate   =  'interval 3 month';
		 }else{
			 $startDate   =  'interval 12 month';
		 }



			/* $SQL="SELECT agentpayment.feeAmount, agentpayment.newOrRenew, agentpayment.PaymentDate, plans.planName, concat(agents.firstName,' ', agents.lastName) as AgentName, agents.agentCode, concat(customers.firstName,' ', customers.LastName) as CustomerName,customers.clientType FROM agentpayment
LEFT JOIN plans ON agentpayment.planId = plans.planId
LEFT JOIN agents ON agentpayment.AgentId = agents.agentId
LEFT JOIN customers ON agentpayment.customerId = customers.customerId
WHERE agentpayment.PaymentMode !=''  AND agentpayment.AgentId=$agentId AND agentpayment.PaymentDate  > date_sub(now(),  $startDate)"; */




		$sql  = "SELECT agents.agentCode as AgentId,concat(agents.firstName,' ', agents.lastName) as AgentName,SUM(agentpayment.feeAmount) as sales, round(SUM(CASE WHEN agentpayment.IsAdvance = '1' THEN agentpayment.Commission END),2) AS AdvanceCommission ,round( SUM(CASE WHEN agentpayment.IsAdvance = '0' THEN agentpayment.Commission END))AS Commission,round(SUM(agentpayment.Commission),2) as TotalCommission,round((SUM(agentpayment.feeAmount) - SUM(agentpayment.Commission)),2) as revenue FROM agentpayment LEFT JOIN agents ON agents.agentId=agentpayment.AgentId WHERE agentpayment.PaymentMode !='' AND agentpayment.PaymentDate  > date_sub(now(),  $startDate) GROUP BY agentpayment.AgentId ";
		//echo $sql;exit;
		  $agent_sell_report = DB::select( DB::raw($sql));
		    $totalSell = DB::select(DB::raw("SELECT SUM(feeAmount) as totalfee, SUM(Commission)as totalcommi,(SUM(feeAmount) - SUM(Commission)) as revenue from agentpayment "));
            //print_r($totalSell);exit;

				  if($totalSell) {
           return response()->json([
             'status' =>  200,
             'agent_sell_report' =>$agent_sell_report,
			 'totalsell' =>  $totalSell
           ]);

     }
	  else{
			 return response()->json([
             'status' =>  200,


           ]);
		   }

	}
	/*agent sale commision report end*/
	/*agent sale start*/
	 public function agentSell(Request $request)
  {
	   // print_r($request->all());exit;

		 $interval = $request->interval;
		 $agentId = $request->agentId;
		 //echo $agentId;exit;
		date_default_timezone_set('Asia/Kolkata');
		 if($interval ==1)
		 {
			$startDate   =  'INTERVAL 1 DAY';
		 }else if($interval ==2){
			//$startDate   =  date("Y-m-d",strtotime("-30 day"));
			$startDate   =  'interval 1 month';
		 }
		 else if($interval ==3){

			//$startDate   =  date("Y-m-d",strtotime("-120 day"));
			$startDate   =  'interval 3 month';
		 }else{
			 $startDate   =  'interval 12 month';
		 }



			$SQL="SELECT agentpayment.feeAmount, agentpayment.newOrRenew, agentpayment.PaymentDate, plans.planName, concat(agents.firstName,' ', agents.lastName) as AgentName, agents.agentCode, concat(customers.firstName,' ', customers.LastName) as CustomerName,customers.clientType FROM agentpayment
LEFT JOIN plans ON agentpayment.planId = plans.planId
LEFT JOIN agents ON agentpayment.AgentId = agents.agentId
LEFT JOIN customers ON agentpayment.customerId = customers.customerId
WHERE agentpayment.PaymentMode !=''  AND agentpayment.AgentId=$agentId AND agentpayment.PaymentDate  > date_sub(now(),  $startDate)";
//echo $SQL;exit;
//echo $SQL;exit;
	    $agentSell = DB::select( DB::raw($SQL));


		/* print_r($agentSell);
		exit; */

				  if($agentSell) {
           return response()->json([
             'status' =>  200,
             'agentSell' =>$agentSell,
           ]);

     }
	  else{
			 return response()->json([
             'status' =>  200,


           ]);
		   }

  }
/*agent sale end*/
/*group name details start*/
  public function groupNameDetails(Request $request){
		//print_r($request->all());exit;


	   $group_code=DB::select( DB::raw("select groups.groupId, groups.groupCode,groups.groupName, groups.created_at ,
(case when (group_wise_total_customer.total_customer <> 0) then group_wise_total_customer.total_customer else 0 end) AS total_customer,status
from groups
left join group_wise_total_customer ON group_wise_total_customer.groupId=groups.groupId"));

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
	/*group name details end*/

}
