<?php

namespace App\Http\Controllers\API;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Agent;
//use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
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
	public function index(Request $request)
    {
		if($request->isActive != '')
		{
		$agents = DB::select( DB::raw("SELECT ag.agentId,ag.agentCode, CONCAT(ag.firstName,' ',ag.LastName) as agent_name, ag.email,ag.cellPhone, date_format(ag.dob,'%m-%d-%Y') as dob, (al.LevelName) AS levelID, awc.total_commission,ag.isActive
FROM `agents` ag
Left join `agentpayment` Agpt on ag.agentId=Agpt.agentId
left join `agents` AGM ON AGM.AgentId=Agpt.managerId
LEFT join `agent_wise_commission` awc ON awc.agentId=ag.agentId
LEFT join `agentlevels` al ON ag.levelID=al.levelID 
where ag.isActive=$request->isActive
group by ag.agentId
Order BY ag.agentId DESC  ") );
		}
		else
		{
			 $agents = DB::select( DB::raw("SELECT ag.agentId,ag.agentCode, CONCAT(ag.firstName,' ',ag.LastName) as agent_name, ag.email,ag.cellPhone, date_format(ag.dob,'%m-%d-%Y') as dob,(al.LevelName) AS levelID, awc.total_commission,ag.isActive
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

	public function agentPaymentSchedule(Request $request){

		$agents = DB::select( DB::raw("SELECT customerId,customerName,client_type,membership_plan,fees,groupCode,agent_commision,agent_chargeBack_commision,agent_interest,renewal_commision,manager_Commision,manager_interest,state_manager_commission,state_manager_interest,earned_commission
 FROM `agent_commission_details` AS a where agentId=".$request->id) );

	   $agents_details=DB::select( DB::raw("SELECT C.agentCode, CONCAT(C.firstName,' ',C.LastName) As agentName, al.LevelName AS agentLevel, CONCAT(C.address1,', ',C.city,'-',C.zip) As agentAddress,ap.total_commission
FROM `agents` as C
Left Join agentlevels as al ON al.levelID=C.levelID
Left join agent_wise_commission as ap on ap.AgentId=C.agentId
WHERE C.agentId=".$request->id) );

		$total_customer=DB::select( DB::raw("SELECT count(customerId)total_customer FROM `agentpayment` WHERE `AgentId` =".$request->id) );
		return response()->json([
        'agents' =>$agents,
		'agentDetails' =>$agents_details,
		'total_customer' =>$total_customer
      ]);

	}

	public function agentpayNow(Request $request){

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

     /**
      * Show the form for creating a new resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function register(Request $request)
     {
       /* DB::beginTransaction();
   			try { */
      // print_r($request->all());exit;
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
         $postArray = [
		   'agentCode' => $request->agentCode,
           'firstName'  => $request->firstname,
           'lastName'      => $request->lastname,
           'email'      => $request->email ,
		   'levelID' =>$request->agentLevel,
           'city'      => $request->city,
		   'city1'      => $request->city1,
		   'state' => $request->state_s,
		   'state1' => $request->state_s1,
		   'dob'=>$request->agenDateOfBirth,
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
           'password'=>$request->password,
           'isActive'  =>$request->isActive,
           'created_at' =>Carbon::now(),
           'bank_name' =>$request->bankname,
		   'paymentMethod'=>$request->paymentMethod,
           'account_name' =>$request->accountname,
		   'account_number'=>$request->accountnumber,
		   'note'=>$request->note
         ];
		 /*echo "<pre>";
		 print_r($postArray);
		 die('aaaa');*/
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
           'enddate'=>null
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
				    'agentid' => $agentPromotionId,
				    'managerId' =>$managerPromotionId,
				    'stateManagerId' => $stateManagerPromotionId
				 ];

         DB::table('agentmanagers')->insert( $agentManager );

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
   /* Start Agent Details for react agentEdit Page*/
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

$agent=DB::select( DB::raw("SELECT b.*,al.*,a.*,(SELECT awp.agentId
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
Where b.agentId=$request->agentId and ap.enddate IS null") );

		  /* $agentPayment = DB::select( DB::raw("SELECT agentpayment.Commission,agentpayment.PaymentMode,
		   agentpayment.feeAmount,agentpayment.naration
		   from agentpayment
Where AgentId=".$request->agentId) );*/

$agentManager =  DB::select( DB::raw("SELECT awp.agentName,awp.agentCode
FROM `agentmanagers` as am
LEFT JOIN `agentwisePromotioncode` as awp ON awp.agentPromotionId=am.managerId
WHERE am.agentId IN (SELECT am.agentId
FROM `agentwisePromotioncode` as awp
LEFT JOIN agentmanagers as am ON am.agentId=awp.agentPromotionId
where awp.agentId=$request->agentId)"));

$stateManager =DB::select( DB::raw("SELECT awp.agentName,awp.agentCode
FROM `agentmanagers` as am
LEFT JOIN `agentwisePromotioncode` as awp ON awp.agentPromotionId=am.stateManagerId
WHERE am.agentId IN (SELECT am.agentId
FROM `agentwisePromotioncode` as awp
LEFT JOIN agentmanagers as am ON am.agentId=awp.agentPromotionId
where awp.agentId=$request->agentId)"));

//
$agentLevel=DB::select( DB::raw("SELECT `levelID`,`LevelName` FROM `agentlevels`") );


		   if($agent)
			  return response()->json([
        'status'=>'200',
			  'agent'=>$agent,
			  'agentManager'=>$agentManager,
			  'stateManager'=>$stateManager,
        'agentLevel'=>$agentLevel

           ]);
     }
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
	  public function memberWiseCommision()
	 {
		 /*$commisionByMember = DB::table('agentpayment')
		    ->select('customers.customerId','customers.firstName','customers.LastName', 'plans.planName',
			'agentpayment.CustomerFee','agents.firstName as aFirstName',
			  'agents.lastName as aLastName','agentpayment.Commission' )
            ->Join('agents', 'agents.agentId', '=', 'agentpayment.AgentId')
			->Join('customers', 'agents.agentId', '=', 'customers.agentId' OR 'customers', 'agents.agentId', '=', 'customers.agentId')

			->Join('plans', 'customers.planId', '=', 'plans.planId')
            ->get();

			$commisionByMember = DB::select( DB::raw("select c.customerId,CONCAT(c.firstName,' ',c.LastName) as customer_name, p.planName, AP.CustomerFee,
			  CONCAT(A.firstName,' ',A.lastName) as agent_name, AP.Commission as agent_commission,
			  APt.Commission as manager_commission from customers c inner join agentpayment AP
			  on AP.AgentId=c.agentId inner join agentpayment APt ON APt.AgentId=c.agent_manager
			  inner join plans p on p.planId=c.planId inner join agents as A ON A.agentId=c.agentId
			  inner join agents as Ag ON Ag.agentId=c.agent_manager") );

			  */
			 /* $commisionByMember = DB::select( DB::raw("select c.customerId,CONCAT(c.firstName,' ',c.LastName) as customer_name, p.planName, AP.feeAmount,
			  CONCAT(A.firstName,' ',A.lastName) as agent_name, AP.Commission as agent_commission,AP.chargeBackCommision as agent_advance_commission,
			  AP.managerCommission as manager_commission from agentpayment AP
			  inner join  customers c on AP.customerId=c.customerId
			  inner join plans p on p.planId=AP.planId
			  inner join agents as A ON A.agentId=AP.AgentId ") );
			  */
			  /* $agentCommision = DB::select( DB::raw("SELECT A.agentId, CONCAT(A.firstName,' ',A.LastName) As agentName,
			  A.levelID, AL.FirstYrComRate,ATF.PayDate,
			  (case when (AWC.total_commission <>0) THEN AWC.total_commission ELSE 0 END) payablecommission ,
			  (case when (ATF.adhocPayment <>0) THEN ATF.adhocPayment ELSE 0 END) paidcommission ,
			  SUM(AWC.total_commission - ATF.adhocPayment) AS AdjustedComm
 			  FROM agents as A
			  LEFT JOIN agent_wise_commission as AWC ON A.agentId = AWC.AgentId
              LEFT JOIN agentlevels as AL ON AL.levelID = A.levelID
			  LEFT JOIN agenttotalfee as ATF ON ATF.AgentId =A.agentId") ); */
			  $agentCommision = DB::select( DB::raw("SELECT (@a:=@a+1) AS serial_number,A.agentCode, CONCAT(A.firstName,' ',A.LastName)
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
			  ") );
			 if($agentCommision)
		   {
			  return response()->json([

			  'status'=>'200',
			  'agentCommision'=>$agentCommision,
			   ]);
		   }
		   else {
			    return response()->json([
				 'status'=>'202',

			  'message'=>'something went wrong'
			   ]);
		   }
	 }
  // This is used for agent Update
	 public function updateagent(Request $request){

	   /*echo "<pre>";
	   print_r($request->all());
     die('aaaa');*/
	   //echo $request->agentId;exit;
	   $postArray = [
	          'firstName' =>$request->firstName,
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
			   'note' =>$request->note,
        'levelID'=>$request->agentLevel
			 ];
       $agentPaimentDone =DB::table('agents')->where('agentId',$request->agentId)->update($postArray);
       $agent_promotion_EndDate=$agentCommision = DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentId' and `agentPromotion`.`enddate` IS NULL") );
       if($agent_promotion_EndDate){
         foreach ($agent_promotion_EndDate as $key => $value) {
           // code...
           //echo $value->agentPromotionId;
           $postArray = [
      			  'enddate' =>date('Y-m-d')
      			 ];
             $agentPromotionEndDate=DB::table('agentPromotion')->where('agentPromotionId',$value->agentPromotionId)->update($postArray);
             $agentPromotion=[
              'agentId'=>$request->agentId,
              'level'=>$request->agentLevel
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
       $agent_manager_promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentManager' and `agentPromotion`.`enddate` IS NULL") );
       foreach($agent_manager_promotion_id as $key => $value) {
         // code...
         $agentManagerPromotionId=$value->agentPromotionId;
       }
       $agent_state_manager_promotion_id=DB::select( DB::raw("SELECT agentPromotionId FROM `agentPromotion` WHERE `agentPromotion`.`agentId`='$request->agentStateManager' and `agentPromotion`.`enddate` IS NULL") );
       foreach($agent_state_manager_promotion_id as $key => $value) {
         // code...
         $agentStateManagerPromotionId=$value->agentPromotionId;
       }
       //agentId
       $agentManagerPromotion=[
        'agentId'=>$agentPromotionId,
        'managerId'=>$agentManagerPromotionId,
        'stateManagerId'=>$agentStateManagerPromotionId
      ];
       $NewagentPromotionID=DB::table('agentmanagers')->insert($agentManagerPromotion);
     if($NewagentPromotionID)
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

	  //
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
	   public function agentStatus(Request $request)
       {
			$agentStatus = DB::table('agents')->select('agentId','isActive')->where('agentId',$request->agentId)->get();
			  if($agentStatus) {
				   return response()->json([
					 'status' =>  200,
					 'agentStatus' => $agentStatus,


				   ]);

                 }
	   }
	   public function agentCodeSearch(Request $request)
       {
			 $duplicateAgentCode = DB::table('agents')
                                 ->where( [
                                     'agentCode' => $request->agentCode,
                                 ] )->first();
                if (  $duplicateAgentCode ) {
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
	   //This  is use for agent promotion table
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
	   

}
