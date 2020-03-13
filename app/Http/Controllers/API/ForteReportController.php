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

class ForteReportController extends Controller
{
          public $URL = 'https://sandbox.forte.net/api';
		    //public $URL = 'https://api.forte.net';
		  //$URL = ' https://www.forte.net/payment-gateway';
		  //$url = 'https://api.forte.net';
		    public $AccountID  = 'act_383572';
		    public $LocationID = 'loc_244363';
		      public $APIKey = '563b59201ed8da9f9874f246fc44b62d';//sandbox
		      public $SecureTransactionKey = '91fb895a47e2765c5436fd9f4498d180';//sandbox	
		   // public $APIKey = '3b6322cdfd84db83c04e106d3bef32e9';//live
		  //  public $SecureTransactionKey = '0c1eff89b5900c0d6c038b35c41a2ddd';//live
		  //public $auth_token = base64_encode($APIKey.':'.$SecureTransactionKey);
	/*forty transactions report start*/
      public function forte_transcition_report(Request $request){
		        // print_r($request->startDate);exit;
				
//$your_date = date("m-d-y", strtotime($date));
              if($request->startDate != ''){
				  
				   $three_mon_earlier = date("Y-m-d", strtotime($request->startDate));
			  }
			  else {
				   $three_mon_earlier  =  date("Y-m-d",strtotime("-4 day"));
			  }
			  if($request->endDate != ''){
				   $today  =  date("Y-m-d",strtotime($request->endDate));
			  }
			  else {
				 // $three_mon_earlier  =  date("Y-m-d",strtotime("-4 day"));
				  $today =  date("Y-m-d");
			  }
		     
			  //echo" $today";
			 
			  // echo"  $three_mon_earlier";exit;
		      $URL = $this->URL;
			  //echo $url;exit;
			  $AccountID = $this->AccountID;
			  $LocationID = $this->LocationID;
			  $APIKey  = $this->APIKey;
			  $SecureTransactionKey  = $this->SecureTransactionKey;
			  //$auth_token = $this->auth_token;
			  $auth_token = base64_encode($APIKey.':'.$SecureTransactionKey);
			 // ?filter=start_received_date+eq+'2018-01-01'+and+end_received_date+eq+'2018-08-03'
		    $transcition =  $URL.'/v2/locations/'.$LocationID."/transactions?filter=start_received_date+eq+'.$three_mon_earlier.'+and+end_received_date+eq+'.$today.'";
			$curl = curl_init($transcition);
			//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$auth_token,
										 'X-Forte-Auth-Account-Id: '.$AccountID,
									 'Content-Type: application/json'));		
			//curl_setopt($curl, CURLOPT_POST,1);
			//curl_setopt($curl, CURLOPT_POSTFIELDS,$create_customer); 
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
	   // print_r('Curl error: ' . curl_error($curl) . '<br>');
	   
	    return response()->json([
				 'status'=>'200',
				 'transction_details'=>'',
				]);
	}
	else {
		curl_close($curl);
		//print_r($curl_response);

		$decoded_customber = json_decode($curl_response); 
		//echo "<pre>";
		//print_r($decoded_customber);exit;
		$transction_details = $decoded_customber->results;
		//echo "<pre>";
		//print_r($transction_details);exit;
		$array_size = count($transction_details);
		
		foreach ($transction_details as $k => $v) {
            // echo $k ;echo "</br>";
	foreach($transction_details[$k] as $key =>$val){
				if($key == 'billing_address') {
        //unset($transction_details[$k]->$key);
		//echo "$key";exit;
		foreach($transction_details[$k]->$key as $key1 =>$val1)
		     if($key1 == 'first_name' ){
				 $transction_details[$k]->first_name =$val1;
			 }
			 else if($key1 == 'last_name'){
				 $transction_details[$k]->last_name =$val1;
			 }
			 if($key == 'billing_address') {
        unset($transction_details[$k]->$key);
		//echo "$key";exit;
			 }
		
		
    }
	else if ($key == 'card'){
		 unset($transction_details[$k]->$key); 
	}
	else if ($key == 'links'){
		 unset($transction_details[$k]->$key);
	}
	else if ($key == 'response'){
		 unset($transction_details[$k]->$key);
	}
	else if ($key == 'echeck'){
		 unset($transction_details[$k]->$key);
	}
	                //echeck
				// echo $key ;echo "</br>";
				// $cus_trans_detail[$key][] = $val;
			 }
			 
        }
		//exit;
		 //unset($transction_details[0]);
		//echo "<pre>";
		//print_r($transction_details);exit;
		 return response()->json([
				 'status'=>'200',
				 'transction_details'=>$transction_details,
				]);
	  }
	 }
	 /*forty transction report end*/
	     // forte shedule report start
	   public function forte_shedule_report(Request $request){
		       // print_r($request->customer_id);exit;
		      $today =  date("Y-m-d");
			  //echo" $today";
			  $three_mon_earlier  =  date("Y-m-d",strtotime("-4 day"));
			   //echo"  $three_mon_earlier";exit;
		      $URL = $this->URL;
			  //echo $url;exit;
			  $AccountID = $this->AccountID;
			  $LocationID = $this->LocationID;
			  $APIKey  = $this->APIKey;
			  $SecureTransactionKey  = $this->SecureTransactionKey;
			  //$auth_token = $this->auth_token;
			  $auth_token = base64_encode($APIKey.':'.$SecureTransactionKey);
			 // ?filter=start_received_date+eq+'2018-01-01'+and+end_received_date+eq+'2018-08-03'
		       $customertoken = $request->customer_id;
			//$schedules = $URL.'/v2/locations/'.$LocationID."/schedules";
			//$schedules = $URL.'/v2/locations/'.$LocationID.'/customers/'.$customertoken.'/schedules';
		     	//{{baseURI}}/organizations/org_{{organizationID}}/locations/loc_{{locationID}}/customers/cst_{{customertoken}}/scheduleitems?filter=schedule_item_status+eq+'scheduled'
				
			$schedules = $URL.'/v2/locations/'.$LocationID.'/customers/'.$customertoken.'/scheduleitems?filter=schedule_item_status+eq+scheduled';
			$curl = curl_init($schedules);
			//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$auth_token,
										 'X-Forte-Auth-Account-Id: '.$AccountID,
									 'Content-Type: application/json'));		
			//curl_setopt($curl, CURLOPT_POST,1);
			//curl_setopt($curl, CURLOPT_POSTFIELDS,$create_customer); 
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
	   // print_r('Curl error: ' . curl_error($curl) . '<br>');
	   
	    return response()->json([
				 'status'=>'200',
				 'transction_details'=>'',
				]);
	}
	else {
		curl_close($curl);
		//print_r($curl_response);

		$decoded_customber = json_decode($curl_response); 
		//echo "<pre>";
		//print_r($decoded_customber);exit;
		$transction_details = $decoded_customber->results;
		//echo "<pre>";
		//print_r($transction_details);exit;
		//$array_size = count($transction_details);
		
		foreach ($transction_details as $k => $v) {
            // echo $k ;echo "</br>";
	foreach($transction_details[$k] as $key =>$val){
				if($key == 'links') {
        unset($transction_details[$k]->$key);
		
		
	      }
    }
		
   }
			 
        }
		//echo"<pre>";
		//print_r($transction_details);exit;
		 return response()->json([
				 'status'=>'200',
				 'transction_details'=>$transction_details,
				]);
	  
	 }
	 /*forte schedule report end*/

}
?>