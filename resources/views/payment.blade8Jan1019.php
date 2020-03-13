<!DOCTYPE html>

<?php

       if($data['isBurialFee'] == 'false'){
		      $burialFee = 0;
              $burialPerPerson = 0;
              $burialCity  = '';
		      $burialState  = '';			  
			
		 }
		 else{
			 $burialFee = $data['countperson']*$data2[0]->burialFee; 
			 $burialPerPerson = $data2[0]->burialFee;
			  $burialCity  = $data['burialCity'];
		      $burialState  = $data['burialState'];	
		 }
		 //echo $burialFee;
		// exit;
	     $agentPaymentId = $data['agentPaymentId'];
		 if($data2[0]->planName == 'Monthly'){
			 $frequency = "monthly";
		 }
		 else if($data2[0]->planName == 'Annual'){
			  $frequency = "annually";
		 }
		 else {
			$frequency = 0;
		 }
		 if($data['initianFee']){
		   $TAX = $data2[0]->initiatonFee;

		 }
		 else{
			 $TAX =0;
		 }
		
	    
		$toataAmont =$data2[0]->feeAmount+$burialFee+$data2[0]->seminarFee + $TAX;
		//echo $toataAmont;exit;
	//	$APILoginID  = '5uJ1aCSh55';
		//$SecureTransactionKey = 'Wq60m81KdD';
		$APILoginID =  '8B2C8C460E';
		$SecureTransactionKey = 'gkBAVA8EoG';
	    //$MerchantID    = "383572";
		//$totalamount = "{1375.23,1573.66,56.99,0|Total outstanding,Last statement balance,Minimum balance,Specify different amount};500.00";
		//$totalamount =$toataAmont;
		  $totalamount = 1;
		$method = 'schedule';
		$paymentDate = $data2[0]->membershipDate;
		//$tax_amount=$data2['tax'];
		$version = '1.0';
		$ordernumber = 'A1WEWS234';
		date_default_timezone_set("Asia/Kolkata");
		$unixtime = strtotime(gmdate('Y-m-d H:i:s'));
		$millitime = microtime(true) * 1000;
		$utc = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
		$data = "$APILoginID|$method|$version|$totalamount|$utc|$ordernumber||";
		//$data = "$APILoginID|$method|$version|$totalamount|$utc|$MerchantID|$ordernumber||";
		$hash = hash_hmac('md5',$data,$SecureTransactionKey);
		$schedule_start_date= $data2[0]->membershipDate;

?>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
$(document).ready(function(){

     $("#fortePayment").hide();
  });




 function PaymentMethod(paymentMethod)  {
                //var end = this.value;
				//alert(paymentMethod);
				if(paymentMethod == "Card" || paymentMethod == "ACH" ){
				   $("#fortePayment").show();

				  $("#manualPayment").hide();
				  $("#manualPay").hide();
            }
			else {
				  $("#fortePayment").hide();

				  $("#manualPayment").show();
				  $("#manualPay").show();

			}

 }
function oncallback(e) {
        //$('#message').html(e.data);
		console.log(e.data);
		var data = JSON.parse(e.data);
	     console.log(data);
		 let agentPaymentId = '<?php echo $agentPaymentId;?>';
		 let customer_token  = data.customer_token;
		 let total_amount  = data.total_amount;
		 let method_used  = data.method_used;
		 let burialFee = '<?php echo $burialFee;?>';
		 let burialPerPerson = '<?php echo $burialPerPerson;?>';
		 let burialCity = '<?php echo $burialCity;?>';
		 let burialState = '<?php echo $burialState;?>';
		
		 if(data.event == 'success'){
			 /*
			$('#success').html('Thank You for  your  Payment');
			 $('#pay').hide();
			 var timer = setTimeout(function() {
           // window.history.back();
		   window.location.href = 'http://localhost:3000/admin-dashboard#/customer-management';
        }, 3000);
            //$('#pay').hide();
			*/


    //  $('#send_form').html('Sending..');
      $.ajax({
		  //url: 'http://34.94.193.181:8000/paymentSucess' ,
         // url: 'http://35.235.80.37:8000/paymentSucess' ,
		 url: 'http://192.168.0.215:8000/paymentSucess',
        type: "get",
        data: {'customer_token':customer_token,'total_amount':total_amount,'method_used':method_used,'agentPaymentId':agentPaymentId,'burialFee':burialFee,'burialPerPerson':burialPerPerson,'burialCity':burialCity,'burialState':burialState},
        success: function( response ) {
            /*$('#send_form').html('Submit');
            $('#res_message').show();
            $('#res_message').html(response.msg);
            $('#msg_div').removeClass('d-none');

            document.getElementById("contact_us").reset();
            setTimeout(function(){
            $('#res_message').hide();
            $('#msg_div').hide();
            },10000);
			*/
			 // var res = JSON.parse(response);
			//alert(response)
			if(response ==1){
	 window.location.href= 'http://192.168.0.215:3000/admin-dashboard#/customer-management';
	 // window.history.back(4);
	 //  window.location.href= 'http://34.94.147.238:3000/admin-dashboard#/customer-management';
	//window.location.href= 'http://35.235.80.37:3000/admin-dashboard#/customer-management';
			}
			else if(response ==0){
				 window.history.back();
			}
        }
      });


		 }
		  if(data.event == 'failure'){
			 // alert('hi')
			$('#success').html(data.response_description);
			$('#pay').hide();
			var timer =  setTimeout(function() {
            window.history.back();
        }, 3000);
            //$('#pay').hide();

		 }
    }
	//https://sandbox.forte.net/api/v3/js
	//https://sandbox.forte.net/checkout/v1/js
	//https://sandbox.forte.net/api/v2 for sandbox testing

</script>

<script type="text/javascript" src="https://api.forte.net/js/v1"></script>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<style>
	.form_bg{
		box-shadow:0 0 8px #101010;
		margin-bottom:80px;
	}
	.header_part{
		background:#9e1c20;
		text-align:center;
		margin:0;
		position:relative;
	}
	.header_part h2{
		text-align:center;
		padding-bottom:20px;
		padding-top:20px;
		font-weight:bold;
		margin-bottom:0;
		color:#fff;
		font-family:"Roboto", "Helvetica", "Arial", sans-serif;
	}
	.form_body{
		background:#F8F2E4;
		padding:20px;
	}
	.btn{
		background:#999;
		margin:0.3125rem 1px 1.5rem;
		font-weight:400;
		line-height:1.428571;
		letter-spacing:0;
		outline:0;
		will-change:box-shadow, transform;
		box-shadow:0 2px 5px 0 rgba(0,0,0,.16), 0 2px 10px 0 rgba(0,0,0,.12);
		padding:.84rem 2.14rem;
		font-size:1.5rem;
		transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
		border:0;
		border-radius:.125rem;
		text-transform:uppercase;
		white-space:normal;
		word-wrap:break-word;
		font-family:"Roboto", "Helvetica", "Arial", sans-serif;
	}
	.btn:hover, .btn:focus, .btn:active{
		box-shadow:0 5px 11px 0 rgba(0,0,0,.18), 0 4px 15px 0 rgba(0,0,0,.15);
		outline:0;
		background-color:#919191;
		text-decoration:none;
	}
	.form-control{
		transition:background 0s ease-out;
		border-radius:5px;
		height:36px;
		font-size:14px;
		border:1px solid #ced4da;
		margin-bottom:.5rem;
		padding:0.8rem;
		color:#000;
		box-shadow:inset 1px 1px 3px #c1c1c1 !important;
		font-weight:400;
		margin:0;
		font-family:"Roboto", "Helvetica", "Arial", sans-serif;
	}
	.form-control:focus{
		border:1px solid #ced4da;
	}
	#drop{
		-webkit-appearance: none;
  -moz-appearance: none;
  -ms-appearance: none;
  appearance: none;
  background-image: none;
	}
	/* Remove IE arrow */
	#drop::-ms-expand {
	  display: none;
	}
	.select{
		position:relative;
		display:flex;
		overflow:hidden;
	}
	select{
		flex:1;
		padding:0 .5rem;
		cursor:pointer;
	}
	.select:after{
		content:'\2038';
		position:absolute;
		top:26px;
		right:6px;
		padding:0 1rem;
		cursor:pointer;
		pointer-events:none;
		transform:rotate(180deg) scale(3,2.4);
		-webkit-transition: .25s all ease;
  	-o-transition: .25s all ease;
  	transition: .25s all ease;
	}
</style>
</head>
<body>
<div class ="container">
	<div class="col-lg-12">
		<div class="form_bg">
				<div class="header_part">
					<h2>Payment</h2>
				</div>
<!--<div id="message" style="background-color:#e5e5e5"></div>-->
<div id="success" style="background-color:#e5e5e5"></div>
<span id="pay">

<form  action="{{ url('/store') }}" method="POST">
	<div class="form_body">
	<div class="col-lg-7">
     {!! csrf_field() !!}
   <input type="hidden" name="burialFee" value="<?php echo $burialFee?>"/>
   <input type="hidden" name="burialPerPerson" value="<?php echo $burialPerPerson?>"/>
   <input type="hidden" name="burialCity"   value="<?php echo $burialCity?>"/>
   <input type="hidden" name="burialState" value="<?php echo $burialState?>"/>
   <input type="hidden" name="agentPaymentId" readonly class="form-control" value="<?php echo $agentPaymentId; ?>" >
  <div class="form-group">
    <label for="exampleInputEmail1">Member Name</label>
    <input type="text" name="membername" readonly class="form-control" value="<?php echo $data2[0]->firstName." ".$data2[0]->LastName?>" id="exampleInputEmail1" aria-describedby="emailHelp" >
  </div>
  <!--<div class="form-group">
    <label for="exampleInputEmail1">Member Id</label>
    <input type="email" class="form-control" value="<?php //echo $data2[0]->id; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" >-->

  <div class="form-group">
    <label for="exampleInputEmail1">Email Address</label>
    <input type="text" name="email" readonly class="form-control" value="<?php echo $data2[0]->email; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" >
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Plan Name</label>
    <input type="text" name="planeName" readonly class="form-control" value="<?php echo $data2[0]->planName; ?>" >
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Payment Method</label>
<br/>
<div class="select">
	<select  name="paymentMethod" id="drop" class="slct form-control" onchange = "PaymentMethod(this.value)">
	<option value="Payroll Deduction">Payroll Deduction</option>
	<option value="ACH">ACH/EFT</option>
	<option value="Card">Credit/Debit Card</option>
	<option value="Check">Check</option>
	<option value="Money">Money Order</option>
	</select>
</div>
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Membership Date</label>
    <input type="text" name="membershipDate" readonly class="form-control" value="<?php echo $data2[0]->membershipDate; ?>" >
  </div>
	<button type="submit" name="submit" id="manualPayment" class="btn btn-primary btn-rounded">Submit</button>
	<button id="fortePayment" class="btn btn-primary btn-rounded"
        api_login_id=<?php echo $APILoginID;?>
		version_number=<?php echo $version;?>
		callback="oncallback"
        schedule_frequency=<?php echo $frequency ?>
		method=<?php echo $method;?>
		total_amount="<?php echo $totalamount;?>"
		utc_time=<?php echo $utc;?>
		schedule_quantity="1"
		signature=<?php echo $hash;?>
		order_number=<?php echo $ordernumber;?>
		>Pay now</button>
	</div>
	<div class="col-lg-5">
	  <div class="form-group">
	    <label for="exampleInputPassword1">Total Amount</label>
	    <input type="text" name="totalAmount" class="form-control"  value="<?php echo $toataAmont; ?>" >
	  </div>
	  <Span id="manualPay">
	   <div class="form-group">
	    <label for="exampleInputPassword1">Check/Money Order Payee Name</label>
	    <input type="text" name="payeeName" class="form-control"  value="" >
	  </div>
	   <div class="form-group">
	    <label for="exampleInputPassword1">Check/Money Order Number</label>
	    <input type="text" name="checkMoneyOrder" class="form-control"  value="" >
	  </div>
	  </span>
	</div>
	<div class="clearfix"></div>
</div>
</form>

<!--<div>Plane Name:<?php //echo $data2['planename']; ?><div>
<div>First Name:<?php //echo $data2['firstName']; ?><div>
<div>Last Name:<?php //echo $data2['lastName']; ?><div>
<div>Plan Fee :<?php //echo $data2['feeAmount']; ?><div>-->


     <!-- <button
        api_login_id=<?php //echo $APILoginID;?>
		version_number=<?php //echo $version;?>
		callback="oncallback"
		method="<?php //echo $method;?>"
		total_amount="<?php //echo $totalamount;?>"
		utc_time=<?php //echo $utc;?>
		signature=<?php //echo $hash;?>
         tax_amount=<?php //echo $tax_amount ?>
		order_number=<?php //echo $ordernumber;?>
		>Pay now</button>-->


</span>
</div>
  		</div>
		</div>
	</div>
</body>
</html>
