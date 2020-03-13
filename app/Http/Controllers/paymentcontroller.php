<?php

namespace App\Http\Controllers;
use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
class paymentcontroller extends Controller
{
    function index(Request $request){
		//print_r($request->paymentId);exit;
		$data = array('countperson' =>$request->countPerson,
		              'agentPaymentId'=>$request->paymentId,
					  'initianFee'=>$request->initianFee,
					  'isBurialFee'=>$request->burialFee,
					  'burialCity'=>$request->burialCity,
					  'burialState'=>$request->burialState,
		);
		/*$data = array('paymentId' => $request->paymentId,
		                      'planename' => $request->planename,
							  'firstName' => $request->firstName,
							  'lastName' => $request->lastName,
							  'fee' => $request->fee,
							  'tax' =>$request->countPerson*$request->burialFee
		                        );
								//print_r($paymentDetail);

		*/
		 $plans_fee=DB::select( DB::raw("SELECT c.customerId,c.firstName,c.LastName,c.middleName,c.email,c.burialFee,c.DOB,c.spouseDOB,
	  c.dependent1DOB,c.dependent2DOB,c.dependent3DOB,
	   c.seminarFee,c.initiationFee as modifyIniFee,
  	  DATE_FORMAT(c.created_at, '%m/%d/%Y') as membershipDate,c.clientType,AP.feeAmount, FBF.planName,
  	  FBF.frequency, FBF.initiatonFee,sum(AP.feeAmount+c.initiationFee) as initiationFee_with_total_amount,
  	  (AP.feeAmount) as initiationFee_with_out_total_amount
  FROM `agentpayment`As AP
  left join plans AS FBF ON AP.planId=FBF.planId
  left join customers AS c ON c.customerId=AP.customerId
  Where AP.paymentId=".$request->paymentId) );
		//print_r( $plans_fee);exit;
		//return view("payment",$paymentDetail);
		return view('payment')->with('data2',$plans_fee)->with('data', $data);
		  //return $view->with('data2', $plans_fee)->with('data', $data);
	}
	function store(Request $request){
		     //print('asdadsad');exit;
		    $agentPormotionId =0;
			$managerPormotionId=0;
			$stateManagerPormotionId=0;
			$CommisionFee=0;
			$CommisionFeeForManager=0;
			$stateManagerCommisionFee=0;
			$chargeBackCommision=0;
			$ChargeBackInterest=0;
            $ChargeBackInterestForManager=0;
            $ChargeBackInterestForStateManager=0;
			$chargeBackInstalment=0;
			$MonthCounter=0;
		 	$IsAdvance=0;
			$nextPaymentDate='';
			$total_earned_advance=0;
			$unearned_advance=0;
		    $total_earned_advanc_managere=0;
			$total_earned_advanc_stat_manager=0;
			$unearned_adv_stat_mang=0;
			$unearned_adv_mang=0;
		$agentLevel=DB::select( DB::raw("SELECT agentlevels.LevelName FROM `agentpayment`
left join agents ON agentpayment.AgentId=agents.agentId
left join agentlevels ON agents.levelID=agentlevels.levelID
WHERE paymentId=".$request->agentPaymentId));
      $memberId =   json_decode(DB::table('agentpayment')->select('customerId')->where('paymentId', $request->agentPaymentId)->get()); 
	  //print_r($memberId[0]->customerId);exit;
	 $burialArray = [
			 
			 'memberId'=>$memberId[0]->customerId,
			 'feePerPerson'=>$request->burialPerPerson,
			 'totalFee'=>$request->burialFee,
			 'burialCity'=>$request->burialCity,
			 'burialState'=>$request->burialState
        ];
	  
	  
	  $burialDone =DB::table('burialdetail')->insert($burialArray);
	  
	  
	  
	  /*
	   [burialCity] => city 
	   [burialState] => state
	  
	  */
	//print_r($memberId);exit;
	//get plan detail
	
	  $planId = json_decode(DB::table('agentpayment')->select('planId')->where('paymentId', $request-> agentPaymentId)->get());
		 $planDetails =  json_decode(DB::table('plans')->select('planId','frequency')->where('planId',$planId[0]->planId)->get());
		  // print($planDetails[0]->frequency);exit;
	  if($agentLevel[0]->LevelName==0){
		  //echo $agentLevel[0]->LevelName;
	      //die('Pradosh');
		  
		    $total_earned_advance =0;
		       $unearned_advance=0;
		       $total_earned_advanc_managere=0;
		       $total_earned_advanc_stat_manager=0;
			   $unearned_adv_stat_mang=0;
			   $unearned_adv_mang=0;
		  $agentId =  DB::table('agentpayment')->select('AgentId')->where('paymentId', $request->agentPaymentId)->get();
         $memberId =  DB::table('agentpayment')->select('customerId')->where('paymentId', $request->agentPaymentId)->get();
			//$memberId = $memberId[0]->customerId;
			//echo $memberId;exit;
			//print_r($memberId[0]->customerId);exit;
		
		  $memberType =  DB::table('customers')->select('clientType')->where('customerId', $memberId[0]->customerId)->get();
		 // print_r($memberType);exit;
		   $managerId = DB::table('agentpayment')->select('managerId')->where('paymentId', $request->agentPaymentId)->get();
		   $stateManagerId = DB::table('agentpayment')->select('stateManagerId')->where('paymentId', $request->agentPaymentId)->get();
		     //print_r( $managerId);exit;
		  $agentId = json_decode($agentId); 
		   
		 $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agentId[0]->AgentId)->where ('enddate' ,NULL)->get();
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
		    $sate_mang__detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId', $managerPormotionId)->get();
		    //print_r($sate_mang__detail);exit;
		  
		 	 if($memberType[0]->clientType == 'Individual' ) {
				  //$planFee	= json_decode(DB::table('plans')->select('fee')->where('planId', $planId[0]->planId)->get());
				 $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
			 }
           else	{
			     //$planFee	= json_decode(DB::table('plans')->select('familyFee')->where('planId', $planId[0]->planId)->get());
				 $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
		   }	
		
			 //monthly plan calculation//
			  if($planDetails[0]->frequency == 'Monthly'){
				
				        $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
					
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
						if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = '';
						}
						else {
							 $chargeback = '';
						}
                          $ChargeBackInterestForStateManager = 0;
						  //if()
						  $CommisionFee = '';
						  $chargeBackCommision = '' ;
						  $ChargeBackInterest= '';
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
						 $eff_man_comm_percent =$mngCommRate[0]->FirstYrComRate -  $agtFstCommRat[0]->FirstYrComRate;
						 $CommisionFeeForManager = '';
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;
                         
						  $ChargeBackInterestForManager ='';
					    }
						
						//comm calculation for state manager
						if(@$sate_mang__detail[0]==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
						  $ChargeBackInterestForStateManager = 0;
					      }
					  else {
						 // echo "0";
						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FirstYrComRate -  $mngCommRate[0]->FirstYrComRate;
						  $stateManagerCommisionFee = '';
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
						  $ChargeBackInterestForStateManager = '';
					  }
					  
				//exit;
						
			 
			
			  }
			  elseif($planDetails[0]->frequency == 'Annual'){
				          $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
						  //print_r( $agtFstCommRat);exit;
						   $chargeBackCommision = '' ;
			              $ChargeBackInterest= 0;
                          $ChargeBackInterestForManager = 0;
                          $ChargeBackInterestForStateManager=0;
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
						  if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = '';
						}
						else {
							 $chargeback = '';
						}
						
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						  $CommisionFee = '';
						  $IsAdvance='NO';
						
						
						//comm calculation for state manager
						
			  }
			   elseif($planDetails[0]->frequency == '5 Years' || $planId[0]->planId == 'LifeTime' ){
				  $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FiveYrLifeComRate')->where(   'LevelName', $agent_Promotion_detail[0]->level)->get());
						  $chargeBackCommision='';
			              $ChargeBackInterest='';
                          $ChargeBackInterestForManager='';
                          $ChargeBackInterestForStateManager='';
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
						 if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee*6;
						}
						else {
							 $chargeback = $planFee[0]->fee*6;
						}
						   //code  for 0 levid id agent
						   $IsAdvance='NO';
						  $CommisionFee = ($agtFstCommRat[0]->FiveYrLifeComRate/100)*$chargeback;
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
						 $eff_man_comm_percent =$mngCommRate[0]->FiveYrLifeComRate -  $agtFstCommRat[0]->FiveYrLifeComRate;
						 $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;

					    }
						
						//comm calculation for state manager
						if(@$sate_mang__detail[0] ==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
					      }
					  else {
						 // echo "0";
						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FiveYrLifeComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FiveYrLifeComRate -  $mngCommRate[0]->FiveYrLifeComRate;
						  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$chargeback;
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
					  }
			  }
			 
		   $email = $request->email;
		 $today =strtotime(date("Y-m-d"));
     if($request->planeName == 'Monthly'){
       $date = date('Y-m-d', strtotime('+1 month', $today));
       $monthcounter=1;
      }else{
       $date = date('Y-m-d', strtotime('+12 month', $today));
       $monthcounter=12;
      }
        //$stateManagerPormotionId

			$postArray = [
			  'PaymentMode' => $request->paymentMethod,
			  'PaymentDate'=>Carbon::now(),
			  'ModDate'=>Carbon::now(),
			  'payeeName'=>$request->payeeName,
			  'orderNumber'=>$request->checkMoneyOrder,
			  'checkMoneyDate'=>$request->checkDate,
              'recurringPaymentDate'=>$date,
		     'totalBurialFee'=>$request->burialFee,
			 'agentPromotionId'=>$agentPormotionId,
			 'managerPromotionId'=>$managerPormotionId,
			 'stateManagerPromotionId'=>$stateManagerPormotionId,
			 'Commission'=>0,
			 'managerCommission'=>0,
			 'stateManagerCommission'=>0,
			 'chargeBackCommision'=>0,
			 'ChargeBackInterest'=>0,
             'ChargeBackInterestForManager'=>0,
             'ChargeBackInterestForStateManager'=>0,
			 'chargeBackInstalment'=>null,
			 'IsAdvance'=>$IsAdvance,
			 'newOrRenew'=>'NEW',
			 'naration' =>$request->payrollNote,
			 'paymentletterDate'=>$nextPaymentDate,
			 'total_earned_advance' =>$total_earned_advance,
			'unearned_advance' => $unearned_advance,
			'total_earned_advanc_managere' =>$total_earned_advanc_managere,
			'total_earned_advanc_stat_manager' =>$total_earned_advanc_stat_manager,
			'unearned_adv_stat_mang' =>$unearned_adv_stat_mang,
			'unearned_adv_mang' => $unearned_adv_mang,
			//'1%interest_payable' =>0
			
        ];
       // echo "<pre>"; print_r($postArray);exit;
      $agentPaimentDone =DB::table('agentpayment')->where('paymentId',$request->agentPaymentId)->update($postArray);
      //$getCustomerId=$customers=DB::select( DB::raw("select customerId from agentpayment WHERE paymentId =$request->agentPaymentId") );

      $customer_id = DB::table('agentpayment')->select('agentpayment.customerId as customer_id')
      ->where('agentpayment.paymentId', $request->agentPaymentId)
      ->get();
	 // print_r($customer_id);exit;
      $CustomerpostArray = [
      'isPaidCustomer' =>'1'
     ];
     $clientPaimentDone =DB::table('customers')->where('customerId',$customer_id[0]->customer_id)->update($CustomerpostArray);
	 	//print_r( $clientPaimentDone); exit;
     if($agentPaimentDone)
		   {
			   //echo"clientPaimentDone";exit;
			   if($request->email !='')
						 {
							 /*
                          Mail::raw('Thank You for Payment!', function($message)    use ($email)
                          {

                              $message->subject('Welcome message!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						  */
						  }
			   //echo "<script> window.history.go(-4);</script>"; 
			    return view('sucess');
		   }
		   else
		   {
			  // echo"test";exit;
			  return response()->json([
              'status'=>'202',
			  'msg'=>'Error'
			 ]);
		   }
		   
		  
		  
	  }
	 //LEVEL ZERO CODE END     HERE
	  
	  
	  
	  else{
		  
		                   $total_earned_advance = 0;
							$unearned_advance = 0;
							$total_earned_advanc_managere = 0;
							$total_earned_advanc_stat_manager = 0;
							$unearned_adv_stat_mang = 0;
							$unearned_adv_mang = 0;
		 //LEVEL OTHER THAN ZERO START
		   $agentId =  DB::table('agentpayment')->select('AgentId')->where('paymentId', $request->agentPaymentId)->get();
         $memberId =  DB::table('agentpayment')->select('customerId')->where('paymentId', $request->agentPaymentId)->get();
			//$memberId = $memberId[0]->customerId;
			//echo $memberId;exit;
			//print_r($memberId[0]->customerId);exit;
		
		  $memberType =  DB::table('customers')->select('clientType')->where('customerId', $memberId[0]->customerId)->get();
		 // print_r($memberType);exit;
		   $managerId = DB::table('agentpayment')->select('managerId')->where('paymentId', $request->agentPaymentId)->get();
		   $stateManagerId = DB::table('agentpayment')->select('stateManagerId')->where('paymentId', $request->agentPaymentId)->get();
		     //print_r( $managerId);exit;
		  $agentId = json_decode($agentId); 
		   
		 $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agentId[0]->AgentId)->where ('enddate' ,NULL)->get();
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
		    //print_r($sate_mang__detail);exit;
		 // $planId = json_decode(DB::table('agentpayment')->select('planId')->where('paymentId', $request->agentPaymentId)->get());
		 // $planDetails =  json_decode(DB::table('plans')->select('planId,frequency')->where('planId',$planId[0]->planId)->get());
		  // print_r($planDetails);exit;
		 	 if($memberType[0]->clientType == 'Individual' ) {
				  //$planFee	= json_decode(DB::table('plans')->select('fee')->where('planId', $planId[0]->planId)->get());
				  $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
			 }
           else	{
			     //$planFee	= json_decode(DB::table('plans')->select('familyFee')->where('planId', $planId[0]->planId)->get());
				 $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
		   }	
               // echo "<pre>";		   
          	 // print_r($planFee[0]['fee']);exit;
		  
		
			 //monthly plan calculation//
			  if($planDetails[0]->frequency == 'Monthly'){
				         // $agent_Promotion_detail[0]->agentPromotionId;
                        $overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverrideCommisionAnual')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->get());	
						   if($overrideCommAnual[0]->OverrideCommisionAnual){
							   $FirstYearCommission = $overrideCommAnual[0]->OverrideCommisionAnual;
						   }
						   else {
							    $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
								$FirstYearCommission= $agtFstCommRat[0]->FirstYrComRate;
							   
						   }
                        //print_r($overrideCommAnual);exit;   						
				        //$agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
					
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
						if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee*6;
						  $planFee  = $planFee[0]->fee;
						}
						else {
							 $chargeback = $planFee[0]->fee*6;
							 $planFee  = $planFee[0]->fee;
						}
                          $ChargeBackInterestForStateManager = 0;
						  //if()
						    $chargeBackInstalment = 1;
						  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
					       $total_earned_advance = ($FirstYearCommission/100)* $planFee*$chargeBackInstalment;
						  $unearned_advance =  $CommisionFee - $total_earned_advance;
						  
						  $chargeBackCommision =  $CommisionFee ;
						  $ChargeBackInterest= '';
						 
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
                          $total_earned_advanc_managere = ($eff_man_comm_percent/100)* $planFee*$chargeBackInstalment;
						  $unearned_adv_mang =  $CommisionFeeForManager -  $total_earned_advanc_managere;
						  $ChargeBackInterestForManager = $CommisionFeeForManager*0.01*5;
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
						 // $ChargeBackInterestForStateManager = $stateManagerCommisionFee*0.01*5;
						  $ChargeBackInterestForStateManager = 0;
						  $total_earned_advanc_stat_manager = ($eff_stateman_comm_percent/100)* $planFee*$chargeBackInstalment;
						  $unearned_adv_stat_mang =$stateManagerCommisionFee - $total_earned_advanc_stat_manager;
					  }
					  
				//exit;
						
			 
			
			  }
			  elseif($planDetails[0]->frequency == 'Annual'){
				  
				  
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
						  if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee;
						}
						else {
							 $chargeback = $planFee[0]->fee;
						}
						
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
			   elseif($planDetails[0]->frequency == '5 Years' || $planDetails[0]->frequency == 'Lifetime' ){
				     
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
						 if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee;
						}
						else {
							 $chargeback = $planFee[0]->fee;
						}
						   //code  for 0 levid id agent
						   $IsAdvance='NO';
						    $chargeBackInstalment = '';
							if($planId[0]->planId == 3){
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
			 
		   $email = $request->email;
		 $today =strtotime(date("Y-m-d"));
     if($request->planeName == 'Monthly'){
       $date = date('Y-m-d', strtotime('+1 month', $today));
       $monthcounter=1;
      }else{
       $date = date('Y-m-d', strtotime('+12 month', $today));
       $monthcounter=12;
      }
        //$stateManagerPormotionId
	   $postArray = [
			  'PaymentMode' =>$request->paymentMethod,
			  'PaymentDate'=>Carbon::now(),
			  'ModDate'=>Carbon::now(),
			  'payeeName'=>$request->payeeName,
			  'orderNumber'=>$request->checkMoneyOrder,
			  'checkMoneyDate'=>$request->checkDate,
              'recurringPaymentDate'=>$date,
		     'totalBurialFee'=>$request->burialFee,
			 'agentPromotionId'=>$agentPormotionId,
			 'managerPromotionId'=>$managerPormotionId,
			 'stateManagerPromotionId'=>$stateManagerPormotionId,
			 'Commission'=>$CommisionFee,
			 'managerCommission'=>$CommisionFeeForManager,
			 'stateManagerCommission'=>$stateManagerCommisionFee,
			 'chargeBackCommision'=>$chargeBackCommision,
			 'ChargeBackInterest'=>$ChargeBackInterest,
             'ChargeBackInterestForManager'=>$ChargeBackInterestForManager,
             'ChargeBackInterestForStateManager'=>$ChargeBackInterestForStateManager,
			 'chargeBackInstalment'=>$chargeBackInstalment,
			  'MonthCounter' => $MonthCounter,
			 'IsAdvance'=>$IsAdvance,
			 'newOrRenew'=>'NEW',
			 'naration' =>$request->payrollNote,
			 'paymentletterDate'=>$nextPaymentDate,
			 'total_earned_advance' =>$total_earned_advance,
			'unearned_advance' => $unearned_advance,
			'total_earned_advanc_managere' =>$total_earned_advanc_managere,
			'total_earned_advanc_stat_manager' =>$total_earned_advanc_stat_manager,
			'unearned_adv_stat_mang' =>$unearned_adv_stat_mang,
			'unearned_adv_mang' => $unearned_adv_mang,
			//'1%interest_payable' =>0
			 
			
        ];
		//$agentArray = 
       //echo "<pre>"; print_r($postArray);exit;
      $agentPaimentDone =DB::table('agentpayment')->where('paymentId',$request->agentPaymentId)->update($postArray);
      
	  $getCustomerId=$customers=DB::select( DB::raw("select customerId from agentpayment WHERE paymentId =$request->agentPaymentId") );

      $customer_id = DB::table('agentpayment')->select('agentpayment.customerId as customer_id')
      ->where('agentpayment.paymentId', $request->agentPaymentId)
      ->get();
      $CustomerpostArray = [
      'isPaidCustomer' =>'1'
     ];
	
     $clientPaimentDone =DB::table('customers')->where('customerId',$customer_id[0]->customer_id)->update($CustomerpostArray);
	
     if($clientPaimentDone)
		   {
			  /* if($request->email !='')
						  {
                          Mail::raw('Thank You for Payment!', function($message)    use ($email)
                          {

                              $message->subject('Welcome message!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						  } */
			  // echo "<script> window.history.go(-4);</script>";
			   return view('sucess');
		   }
		   else
		   {
			  return response()->json([
              'status'=>'202',
			  'msg'=>'Error'
			 ]);
		   }
	
	 }
	}
	function paymentSucess(Request $request){
                   // print_r($request->all());exit;
		 // $URL = 'https://api.forte.net';	//live		
	     $URL = 'https://sandbox.forte.net/api';//sandbox
	  
	  //$URL = ' https://www.forte.net/payment-gateway';
	  //$url = 'https://api.forte.net';
		$AccountID  = 'act_383572';
		$LocationID = 'loc_244363';
	    $APIKey = '563b59201ed8da9f9874f246fc44b62d';//sandbox
	  	$SecureTransactionKey = '91fb895a47e2765c5436fd9f4498d180';	//sandbox
	    // $APIKey = '3b6322cdfd84db83c04e106d3bef32e9';//live
		//$SecureTransactionKey = '0c1eff89b5900c0d6c038b35c41a2ddd';//live
         $agentPormotionId =0;
			$managerPormotionId=0;
			$stateManagerPormotionId=0;
			$CommisionFee=0;
			$CommisionFeeForManager=0;
			$stateManagerCommisionFee=0;
			$chargeBackCommision=0;
			$ChargeBackInterest=0;
            $ChargeBackInterestForManager=0;
            $ChargeBackInterestForStateManager=0;
			$chargeBackInstalment=0;
			$MonthCounter=0;
		 	$IsAdvance=0;
			$nextPaymentDate='';
			$total_earned_advance=0;
			$unearned_advance=0;
		    $total_earned_advanc_managere=0;
			$total_earned_advanc_stat_manager=0;
			$unearned_adv_stat_mang=0;
			$unearned_adv_mang=0;
	$auth_token = base64_encode($APIKey.':'.$SecureTransactionKey);
	  if($_POST){
		// print_r($_POST);exit;
		 
		if($request->check =='check')
		{
			$payment_mode = 'Bank Account';
			$pg_total_amount =$request->pg_total_amount;
		 $sheduleAmount = $request->sheduleAmount;
		 $first_name = $request->first_name;
		 $last_name = $request->last_name;
		 $postalcode = $request->pg_total_amount;
		 $account_holder_name = $request->account_holder_name;
		 //$card_holoder_name = $request->account_holder_name;
		 $routing_number = $request->ecom_payment_check_trn;
		 $account_numer =  $request->payment_check_account;
		 $account_type =  $request->payment_check_account_type;
		 $plane_name = $request->plane_name;		 
		 
		 $customerId = uniqid();
		 $auto_renew  = $request->auto_renew;
		 $create_customer = array(
            "first_name"=>"$first_name",
            "last_name" =>"$last_name",
          "customer_id" =>$customerId,

		  );
			/*
			$pg_total_amount =$request->pg_total_amount;
		 $sheduleAmount = $request->sheduleAmount;
		 $first_name = $request->first_name;
		 $last_name = $request->last_name;
		 //$postalcode = $request->pg_total_amount;
		 $card_holoder_name = $request->account_holder_name;
		 $e_card_type = $request->card_type;
		 $payment_card_number = $request->ecom_payment_card_number;
		 $cvv = $request->cvv;
		 $plane_name = $request->plane_name;
		 $ecom_payment_card_expdate_month = $request->ecom_payment_card_expdate_month;
		 $ecom_payment_card_expdate_year = $request->ecom_payment_card_expdate_year;
		 $customerId = uniqid();
		 $auto_renew  = $request->auto_renew;
		 $create_customer = array(
            "first_name"=>"$first_name",
            "last_name" =>"$last_name",
          "customer_id" =>$customerId,

		  );
		  
		   $customerId = uniqid();
		   $create_customer = array(
            "first_name"=>"$first_name",
            "last_name" =>"$last_name",
          "customer_id" =>$customerId,

		  );
		  */
		}
		else {
		 $payment_mode = 'Card';
		 $pg_total_amount =$request->pg_total_amount;
		 $sheduleAmount = $request->sheduleAmount;
		 $first_name = $request->first_name;
		 $last_name = $request->last_name;
		 //$postalcode = $request->pg_total_amount;
		
		  $card_holoder_name = $request->ecom_payment_card_name; 
		 $e_card_type = $request->card_type;
		 $payment_card_number = $request->ecom_payment_card_number;
		 $cvv = $request->cvv;
		 $plane_name = $request->plane_name;
		 $ecom_payment_card_expdate_month = $request->ecom_payment_card_expdate_month;
		 $ecom_payment_card_expdate_year = $request->ecom_payment_card_expdate_year;
		 $customerId = uniqid();
		 $auto_renew  = $request->auto_renew;
		 $create_customer = array(
            "first_name"=>"$first_name",
            "last_name" =>"$last_name",
          "customer_id" =>$customerId,

		  );
		  
		}
	 }
	 //create customer url 
	  $create_customer =  json_encode($create_customer);
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
	//echo "</pre>";print_r($decoded_customber);exit;
		 $forte_customber_id = $decoded_customber->customer_token;
	 //end of customer
	 //$pg_total_amount
	 
	  if($request->check =='check')
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
		else{
			$raw = array(
	  "action"=>"sale","authorization_amount"=>$pg_total_amount,"order_number"=>"$plane_name","subtotal_amount"=>$pg_total_amount,"billing_address"=>
	  array("first_name"=>"$first_name","last_name"=>"$last_name"),
	  "card"=>array("card_type"=>"$e_card_type","name_on_card"=>"$card_holoder_name","account_number"=>"$payment_card_number","expire_month"=>"$ecom_payment_card_expdate_month","expire_year"=>"$ecom_payment_card_expdate_year","card_verification_value"=>"$cvv"));
	  $raw_to_send = json_encode($raw,true);
		}
	  
	 // print_r($raw_to_send);exit;
	
	$service_url = $URL.'/v2/accounts/'.$AccountID.'/locations/'.$LocationID.'/transactions/';
	
	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
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
	//echo"<pre>";
	//print_r($decoded);exit;
	$transcation_id = $decoded->transaction_id;
	//echo"<pre>";
	//print_r($decoded);exit;
	//print_r();
	//header("Location: https://sandbox.forte.net/API/v2/paymethods/mth_iqZ3P89osEaJflBfQ3_KJw/settlements"); 

	//echo '<br> end here';exit;
	//echo $decoded->response->response_code;
	
	   if($decoded->response->response_code == "A01")
	   {
		 //set sedular 
		 
            if($auto_renew  ==1)
			 
			 {
				/*
				
				

	$decoded_paymethod = json_decode($curl_response);
	
	
	//print_r($decoded_paymethod);exit;
	*/
	//payment method token create 
	
   $payment_method = array (
   "notes"=>"$plane_name",
   "card"=>array("name_on_card"=>$card_holoder_name,"card_type"=>"$request->card_type","account_number"=>"$payment_card_number","expire_month"=>$ecom_payment_card_expdate_month,"expire_year"=>$ecom_payment_card_expdate_year,"card_verification_value"=>"$cvv"));
    $payment_method = json_encode($payment_method);
     //print_r($payment_method);exit;
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
	  //echo "<pre>";
	//  print_r($decoded_paymemt_response);exit;
	  $payment_token = $decoded_paymemt_response->paymethod_token;
	//payment token code end

	 $one_advance_date =  date('Y-m-d', strtotime("+30 day"));
	$sheduler = array(
  "action" =>"sale",
  "schedule_quantity" => "11",
  "schedule_frequency" => "monthly",
  "schedule_amount"=>  $sheduleAmount,
  "schedule_start_date"=>$one_advance_date,
  "reference_id"=>"Global-123",
  "order_number"=>" $plane_name",
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
	
	$transcation_id = $decoded->transaction_id;
	//print_r($decoded);exit;
	
	  }


         		 
		   
		   
		   
		   
		   
		   
		   
	
					$agentLevel=DB::select( DB::raw("SELECT agentlevels.LevelName FROM `agentpayment`
left join agents ON agentpayment.AgentId=agents.agentId
left join agentlevels ON agents.levelID=agentlevels.levelID
WHERE paymentId=".$request->agentPaymentId));
 $memberId =   json_decode(DB::table('agentpayment')->select('customerId')->where('paymentId', $request->agentPaymentId)->get()); 
	  //print_r($memberId[0]->customerId);exit;
	 $burialArray = [
			 
			 'memberId'=>$memberId[0]->customerId,
			 'feePerPerson'=>$request->burialPerPerson,
			 'totalFee'=>$request->burialFee,
			 'burialCity'=>$request->burialCity,
			 'burialState'=>$request->burialState
        ];
	  
	  
	  $burialDone = DB::table('burialdetail')->insert($burialArray);
	//print_r($agentLevel);exit;
	//plan detail outside if else condition
	$planId = json_decode(DB::table('agentpayment')->select('planId')->where('paymentId', $request->agentPaymentId)->get());
	$planDetails =  json_decode(DB::table('plans')->select('planId','frequency')->where('planId',$planId[0]->planId)->get());
	  if($agentLevel[0]->LevelName==0){
		  //echo $agentLevel[0]->LevelName;
	      //die('Pradosh');
		  $total_earned_advance =0;
		       $unearned_advance=0;
		       $total_earned_advanc_managere=0;
		       $total_earned_advanc_stat_manager=0;
			   $unearned_adv_stat_mang=0;
			   $unearned_adv_mang=0;
		  $agentId =  DB::table('agentpayment')->select('AgentId')->where('paymentId', $request->agentPaymentId)->get();
         $memberId =  DB::table('agentpayment')->select('customerId')->where('paymentId', $request->agentPaymentId)->get();
			//$memberId = $memberId[0]->customerId;
			//echo $memberId;exit;
			//print_r($memberId[0]->customerId);exit;
		
		  $memberType =  DB::table('customers')->select('clientType')->where('customerId', $memberId[0]->customerId)->get();
		 // print_r($memberType);exit;
		   $managerId = DB::table('agentpayment')->select('managerId')->where('paymentId', $request->agentPaymentId)->get();
		   $stateManagerId = DB::table('agentpayment')->select('stateManagerId')->where('paymentId', $request->agentPaymentId)->get();
		     //print_r( $managerId);exit;
		  $agentId = json_decode($agentId); 
		   
		 $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agentId[0]->AgentId)->where ('enddate' ,NULL)->get();
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
		    $sate_mang__detail = DB::table('agentPromotion')->select('*')->where('agentPromotionId', $managerPormotionId)->get();
		    //print_r($sate_mang__detail);exit;
		  
		 	 if($memberType[0]->clientType == 'Individual' ) {
				  //$planFee	= json_decode(DB::table('plans')->select('fee')->where('planId', $planId[0]->planId)->get());
				  $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
			 }
           else	{
			     //$planFee	= json_decode(DB::table('plans')->select('familyFee')->where('planId', $planId[0]->planId)->get());
				 $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
		   }	
               // echo "<pre>";		   
          	 // print_r($planFee[0]['fee']);exit;
		  
		
			 //monthly plan calculation//
			  if($planDetails[0]->frequency == 'Monthly'){
				
				        $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
					
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
						if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = '';
						}
						else {
							 $chargeback = '';
						}
                          $ChargeBackInterestForStateManager = 0;
						  //if()
						  $CommisionFee = '';
						  $chargeBackCommision = '' ;
						  $ChargeBackInterest= '';
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
						 $eff_man_comm_percent =$mngCommRate[0]->FirstYrComRate -  $agtFstCommRat[0]->FirstYrComRate;
						 $CommisionFeeForManager = '';
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;
                         
						  $ChargeBackInterestForManager ='';
					    }
						
						//comm calculation for state manager
						if(@$sate_mang__detail[0]==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
						  $ChargeBackInterestForStateManager = 0;
					      }
					  else {
						 // echo "0";
						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FirstYrComRate -  $mngCommRate[0]->FirstYrComRate;
						  $stateManagerCommisionFee = '';
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
						  $ChargeBackInterestForStateManager = '';
					  }
					  
				//exit;
						
			 
			
			  }
			  elseif($planDetails[0]->frequency == 'Annual'){
				          $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
						  //print_r( $agtFstCommRat);exit;
						   $chargeBackCommision = '' ;
			              $ChargeBackInterest= 0;
                          $ChargeBackInterestForManager = 0;
                          $ChargeBackInterestForStateManager=0;
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
						  if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = '';
						}
						else {
							 $chargeback = '';
						}
						
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						  $CommisionFee = '';
						  $IsAdvance='NO';
						
						
						//comm calculation for state manager
						
			  }
			   elseif($planDetails[0]->frequency == '5 Years' || $planDetails[0]->frequency == 'LifeTime' ){
				  $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FiveYrLifeComRate')->where(   'LevelName', $agent_Promotion_detail[0]->level)->get());
						  $chargeBackCommision='';
			              $ChargeBackInterest='';
                          $ChargeBackInterestForManager='';
                          $ChargeBackInterestForStateManager='';
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
						 if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee*6;
						}
						else {
							 $chargeback = $planFee[0]->fee*6;
						}
						   //code  for 0 levid id agent
						   $IsAdvance='NO';
						  $CommisionFee = ($agtFstCommRat[0]->FiveYrLifeComRate/100)*$chargeback;
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
						 $eff_man_comm_percent =$mngCommRate[0]->FiveYrLifeComRate -  $agtFstCommRat[0]->FiveYrLifeComRate;
						 $CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;

					    }
						
						//comm calculation for state manager
						if(@$sate_mang__detail[0] ==''){
						 // echo "1";
						  $stateManagerCommisionFee = 0;
						  $stateManagerPormotionId = 0;
					      }
					  else {
						 // echo "0";
						 $staMngCommiRate = json_decode(DB::table('agentlevels')->select('FiveYrLifeComRate')->where('LevelName', $sate_mang__detail[0]->level)->get());
						  $eff_stateman_comm_percent = $staMngCommiRate[0]->FiveYrLifeComRate -  $mngCommRate[0]->FiveYrLifeComRate;
						  $stateManagerCommisionFee = ($eff_stateman_comm_percent/100)*$chargeback;
                          $stateManagerPormotionId = $sate_mang__detail[0]->agentPromotionId;
					  }
			  }
			 
		   $email = $request->email;
		 $today =strtotime(date("Y-m-d"));
     if($request->planeName == 'Monthly'){
       $date = date('Y-m-d', strtotime('+1 month', $today));
       $monthcounter=1;
      }else{
       $date = date('Y-m-d', strtotime('+12 month', $today));
       $monthcounter=12;
      }
        //$stateManagerPormotionId

			$postArray = [
			  'PaymentMode' =>$payment_mode,
			   'isAutoRenew'=>$auto_renew,
			  'PaymentDate'=>Carbon::now(),
			  'ModDate'=>Carbon::now(),
			  'payeeName'=>'',
			  'orderNumber'=>'',
			  'checkMoneyDate'=>'',
              'recurringPaymentDate'=>$date,
		     'totalBurialFee'=>$request->burialFee,
			 'agentPromotionId'=>$agentPormotionId,
			 'managerPromotionId'=>$managerPormotionId,
			 'stateManagerPromotionId'=>$stateManagerPormotionId,
			 'Commission'=>0,
			 'managerCommission'=>0,
			 'stateManagerCommission'=>0,
			 'chargeBackCommision'=>0,
			 'ChargeBackInterest'=>0,
             'ChargeBackInterestForManager'=>0,
             'ChargeBackInterestForStateManager'=>0,
			 'chargeBackInstalment'=>null,
			 'IsAdvance'=>$IsAdvance,
			 'paymentletterDate'=>$nextPaymentDate,
			 'total_earned_advance' =>$total_earned_advance,
			'unearned_advance' => $unearned_advance,
			'total_earned_advanc_managere' =>$total_earned_advanc_managere,
			'total_earned_advanc_stat_manager' =>$total_earned_advanc_stat_manager,
			'unearned_adv_stat_mang' =>$unearned_adv_stat_mang,
			'unearned_adv_mang' => $unearned_adv_mang,
			//'1%interest_payable' =>0
			
        ];
       // echo "<pre>"; print_r($postArray);exit;
		//echo $request->agentPaymentId;exit;
      $agentPaimentDone =DB::table('agentpayment')->where('paymentId',$request->agentPaymentId)->update($postArray);
      //$getCustomerId=$customers=DB::select( DB::raw("select customerId from agentpayment WHERE paymentId =$request->agentPaymentId") );

      $customer_id = DB::table('agentpayment')->select('agentpayment.customerId as customer_id')
      ->where('agentpayment.paymentId', $request->agentPaymentId)
      ->get();
      $CustomerpostArray = [
      'isPaidCustomer' =>'1'
     ];
     $clientPaimentDone =DB::table('customers')->where('customerId',$customer_id[0]->customer_id)->update($CustomerpostArray);
	 	 
     if($clientPaimentDone)
		   {	
			/* echo "<pre>";
			print_r($clientPaimentDone);
			die('aaaaaaaa'); */
			  /* if($request->email !='')
						  {
                          Mail::raw('Thank You for Payment!', function($message)    use ($email)
                          {

                              $message->subject('Welcome message!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						  } */
			   //$staus =1;
			      //echo $staus;
				  echo "<script> window.history.go(-4);</script>";
				   return view('sucess');
		   }
		   else
		   {
			//$staus =0;
			// echo "<script> window.history.go(-2);</script>";
			  return view('failure');
			 // return view('sucess');
			    //  echo $staus;
		   }
		   
		  
		  
	  }else{
		        $total_earned_advance =0;
		       $unearned_advance=0;
		       $total_earned_advanc_managere=0;
		       $total_earned_advanc_stat_manager=0;
			   $unearned_adv_stat_mang=0;
			   $unearned_adv_mang=0;
		   $agentId =  DB::table('agentpayment')->select('AgentId')->where('paymentId', $request->agentPaymentId)->get();
         $memberId =  DB::table('agentpayment')->select('customerId')->where('paymentId', $request->agentPaymentId)->get();
			//$memberId = $memberId[0]->customerId;
			//echo $memberId;exit;
			//print_r($memberId[0]->customerId);exit;
		
		  $memberType =  DB::table('customers')->select('clientType')->where('customerId', $memberId[0]->customerId)->get();
		 // print_r($memberType);exit;
		   $managerId = DB::table('agentpayment')->select('managerId')->where('paymentId', $request->agentPaymentId)->get();
		   $stateManagerId = DB::table('agentpayment')->select('stateManagerId')->where('paymentId', $request->agentPaymentId)->get();
		     //print_r( $managerId);exit;
		  $agentId = json_decode($agentId); 
		   
		 $agent_Promotion_detail = DB::table('agentPromotion')->select('*')->where('agentId', $agentId[0]->AgentId)->where ('enddate' ,NULL)->get();
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
		    //print_r($sate_mang__detail);exit;
		  $planId = json_decode(DB::table('agentpayment')->select('planId')->where('paymentId', $request->agentPaymentId)->get());
		 	 if($memberType[0]->clientType == 'Individual' ) {
				  //$planFee	= json_decode(DB::table('plans')->select('fee')->where('planId', $planId[0]->planId)->get());
				  $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
			 }
           else	{
			     //$planFee	= json_decode(DB::table('plans')->select('familyFee')->where('planId', $planId[0]->planId)->get());
				 $planFee	= json_decode(DB::table('agentpayment')->select('feeAmount as fee')->where('paymentId', $request->agentPaymentId)->get());
		   }	
               // echo "<pre>";		   
          	 // print_r($planFee[0]['fee']);exit;
		  
		
			 //monthly plan calculation//
			 if($planDetails[0]->frequency == 'Monthly'){
				         // $agent_Promotion_detail[0]->agentPromotionId;
                        $overrideCommAnual	= 	json_decode(DB::table('agentPromotion')->select('OverrideCommisionAnual')->where('agentPromotionId', $agent_Promotion_detail[0]->agentPromotionId)->get());	
						   if($overrideCommAnual[0]->OverrideCommisionAnual){
							   $FirstYearCommission = $overrideCommAnual[0]->OverrideCommisionAnual;
						   }
						   else {
							    $agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
								$FirstYearCommission= $agtFstCommRat[0]->FirstYrComRate;
							   
						   }
                        //print_r($overrideCommAnual);exit;   						
				        //$agtFstCommRat = json_decode(DB::table('agentlevels')->select('FirstYrComRate')->where('LevelName', $agent_Promotion_detail[0]->level)->get());
					
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 month", $time));
						if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee*6;
						  $planFee  = $planFee[0]->fee;
						}
						else {
							 $chargeback = $planFee[0]->fee*6;
							 $planFee  = $planFee[0]->fee;
						}
                          $ChargeBackInterestForStateManager = 0;
						  //if()
							  $chargeBackInstalment =1;
			                  $MonthCounter=1;
						  $CommisionFee = ($FirstYearCommission/100)*$chargeback;						  
						  $ChargeBackInterest= 0;
						  $agentPormotionId = $agent_Promotion_detail[0]->agentPromotionId;
						  $total_earned_advance = ($FirstYearCommission/100)* $planFee*$chargeBackInstalment;
						  $unearned_advance =  $CommisionFee - $total_earned_advance;
						  //$chargeBackCommision = $unearned_advance ;
						 $chargeBackCommision = $CommisionFee;
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
							 $total_earned_advanc_managere = ($eff_man_comm_percent/100)* $planFee*$chargeBackInstalment;
						  $unearned_adv_mang =  $CommisionFeeForManager -  $total_earned_advanc_managere;
							 
						 }
						 else{
							 $CommisionFeeForManager =0;
						 }
						 //$CommisionFeeForManager = ($eff_man_comm_percent/100)*$chargeback;
						 $managerPormotionId = $manager_Promotion_detail[0]->agentPromotionId;
                         
						  //$ChargeBackInterestForManager = $CommisionFeeForManager*0.01*6;
						  $ChargeBackInterestForManager = 0;
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
						 // $ChargeBackInterestForStateManager = $stateManagerCommisionFee*0.01*6;
						 $ChargeBackInterestForStateManager =0;
						  $total_earned_advanc_stat_manager = ($eff_stateman_comm_percent/100)* $planFee*$chargeBackInstalment;
						  $unearned_adv_stat_mang =$stateManagerCommisionFee - $total_earned_advanc_stat_manager;
					  }
					  
				//exit;
						
			 
			
			  }
			  elseif($planDetails[0]->frequency == 'Annual'){
				  
				  
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
						  $chargeBackInstalment ='';
			                  $MonthCounter=12;
                          $ChargeBackInterestForManager = 0;
                          $ChargeBackInterestForStateManager=0;
						  $time = strtotime(date("y-m-d"));				 
						  $nextPaymentDate = date("Y-m-d", strtotime("+1 Year", $time));
						  if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee;
						}
						else {
							 $chargeback = $planFee[0]->fee;
						}
						
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
			   elseif($planDetails[0]->frequency == '5 Years' || $planDetails[0]->frequency == 'LifeTime' ){
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
						 if($memberType[0]->clientType == 'Individual' ) {
						  $chargeback = $planFee[0]->fee;
						}
						else {
							 $chargeback = $planFee[0]->fee;
						}
						   //code  for 0 levid id agent
						   $IsAdvance='NO';
						  $CommisionFee = ($FirstYearCommission/100)*$chargeback;
						  $chargeBackInstalment ='';
			                if($planId[0]->planId ==3){  
							  $MonthCounter=60;
							}else{
								$MonthCounter='';
							}
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
			 
		   $email = $request->email;
		 $today =strtotime(date("Y-m-d"));
     if($planDetails[0]->frequency == 'Monthly'){
       $date = date('Y-m-d', strtotime('+1 month', $today));
       $monthcounter=1;
      }else{
       $date = date('Y-m-d', strtotime('+12 month', $today));
       $monthcounter=12;
      }
        //$stateManagerPormotionId
		 $today =strtotime(date("Y-m-d"));
            if($planDetails[0]->frequency == 'Monthly'){
             $date = date('Y-m-d', strtotime('+1 month', $today));
            $monthcounter=1;
              }else{
           $date = date('Y-m-d', strtotime('+12 month', $today));
           $monthcounter=12;
          }
	   $postArray = [
			  
			   'PaymentMode' => $payment_mode,
			   'isAutoRenew'=>$auto_renew,
			  'PaymentDate'=>Carbon::now(),
			  'ModDate'=>Carbon::now(),
			  'payeeName'=>$request->payeeName,
			  'orderNumber'=>$request->checkMoneyOrder,
			  'checkMoneyDate'=>$request->checkDate,
              'recurringPaymentDate'=>$date,
		     'totalBurialFee'=>$request->burialFee,
			 'agentPromotionId'=>$agentPormotionId,
			 'managerPromotionId'=>$managerPormotionId,
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
			  'newOrRenew'=>'NEW',
			 'IsAdvance'=>$IsAdvance,
			 'paymentletterDate'=>$nextPaymentDate,
			 'recurringPaymentDate'=>$date,	
			 'transction_id' => $transcation_id,
		    'customer_id'  => $forte_customber_id,
			'total_earned_advance' =>$total_earned_advance,
			'unearned_advance' => $unearned_advance,
			'total_earned_advanc_managere' =>$total_earned_advanc_managere,
			'total_earned_advanc_stat_manager' =>$total_earned_advanc_stat_manager,
			'unearned_adv_stat_mang' =>$unearned_adv_stat_mang,
			'unearned_adv_mang' => $unearned_adv_mang,
			//'1%interest_payable' =>0
			
        ];
		
      // echo "<pre>"; print_r($postArray);exit;
      $agentPaimentDone =DB::table('agentpayment')->where('paymentId',$request->agentPaymentId)->update($postArray);
      
	  $getCustomerId=$customers=DB::select( DB::raw("select customerId from agentpayment WHERE paymentId =$request->agentPaymentId") );

      $customer_id = DB::table('agentpayment')->select('agentpayment.customerId as customer_id')
      ->where('agentpayment.paymentId', $request->agentPaymentId)
      ->get();
      $CustomerpostArray = [
      'isPaidCustomer' =>'1'
     ];
	
     $clientPaimentDone =DB::table('customers')->where('customerId',$customer_id[0]->customer_id)->update($CustomerpostArray);
	
     if($clientPaimentDone)
		   {
			 /*  if($request->email !='')
						  {
                          Mail::raw('Thank You for Payment!', function($message)    use ($email)
                          {

                              $message->subject('Welcome message!');
                              $message->from('globalmedivac@gmail.com', 'Global Medivac');
                              $message->to($email);
                          });
						  } */
			 //  echo "<script> window.history.go(-4);</script>";
			  return view('sucess');
			    // $staus =1;
			      //echo $staus;
		   }
		   else
		   {
			   /*
			  return response()->json([
              'status'=>'202',
			  'msg'=>'Error'
			 ]);
			 */
			    return view('failure');
			  //  echo "<script> window.history.go(-2);</script>";
		   }
	
	     }
	   }
        else {
			
			   //echo "<script> window.history.go(-4);</script>";
			      return view('failure');
			  //  return view('sucess');
			 // not sure
		}	   
	}

}
