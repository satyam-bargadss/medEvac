<!DOCTYPE html>

<?php
    //print_r($data2[0]->modifyIniFee);exit;
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
		 if($data['initianFee'] == 'false'){
		   $TAX = $data2[0]->modifyIniFee;

		 }
		 else{
			 $TAX =0;
		 }
		//print_r($data2);exit;
	    
		$toataAmont =$data2[0]->feeAmount+$burialFee+$data2[0]->seminarFee + $TAX;
		$sheduleAmount = $data2[0]->feeAmount+$burialFee;
		//echo $toataAmont;exit;
	//	$APILoginID  = '5uJ1aCSh55';
		//$SecureTransactionKey = 'Wq60m81KdD';
		$APILoginID =  '8B2C8C460E';
		$SecureTransactionKey = 'gkBAVA8EoG';
	    //$MerchantID    = "383572";
		//$totalamount = "{1375.23,1573.66,56.99,0|Total outstanding,Last statement balance,Minimum balance,Specify different amount};500.00";
		$totalamount =$toataAmont;
		  //$totalamount = 1;
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
<link rel="stylesheet" href="{{ URL::asset('dist/css/cccheck.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ URL::asset('dist/js/jquery.cccheck.js') }}"></script>
<script>

$(document).ready(function(){

    $("#manualForm1").hide();
	//("#manualPayment").hide();
				  $("#forteForm1").hide();
				   $("#forteCheck").hide();
	$( "#cc-number" ).keyup(function() {
		  if($( "#cc-number" ).val().match(/^4[0-9]{5}(?:[0-9]{3})?/)) {
          // alert('visa');
		   $('#card_type').val('visa');
             }
		  else if($( "#cc-number" ).val().match(/^5[1-5][0-9]{4}/)) {
           //alert('mastercard');
		    $('#card_type').val('MAST')
               }                      
    else if($( "#cc-number" ).val().match(/^3[47][0-9]{4}/)) {
           //alert('amex');
		   $('#card_type').val('amex')
}
else if($( "#cc-number" ).val().match(/^6(?:011|5[0-9]{2})[0-9]{4}/)) {
             // alert('discover');
		   $('#card_type').val('discover')
}
else {
	  $('#card_type').val('')
}

 
     });			  
				  
				  
				  
  });
function ValidatefrmChk() {
if(IsEmpty(document.frmChk.pg_total_amount,"Please enter the transaction amount"))
	return false;
if(IsNotValidAmount(document.frmChk.pg_total_amount,"Invalid transaction amount"))
	return false;
if(IsEmpty(document.frmChk.ecom_billto_postal_name_first,"Please enter your first name"))
	return false;
if(IsEmpty(document.frmChk.ecom_billto_postal_name_last,"Please enter your last name"))
	return false;
if(IsEmpty(document.frmChk.ecom_payment_check_trn,"Please enter your rounting number"))
	return false;
if(IsNotNumber(document.frmChk.ecom_payment_check_trn,"Please enter only digits for the  rounting number"))
	return false;
if(IsNotMaxLength(document.frmChk.ecom_payment_check_trn,9,"The rounting number should be 9 digits long"))
	return false;
if(IsEmpty(document.frmChk.ecom_payment_check_account,"Please enter your account number"))
	return false;
if(IsNotNumber(document.frmChk.ecom_payment_check_account,"Please enter only digits for the  account number"))
	return false;

if(document.frmChk.ecom_payment_check_checkno.value!=""){
if(IsNotNumber(document.frmChk.ecom_payment_check_checkno,"Please enter only digits for the check number"))
	return false;
}
return true;
}
function IsEmpty(textField,msg){
	var strValue=textField.value;
	for (var i = 0; i < strValue.length; i++)
	{
	var c = strValue.charAt(i);
	if ((c != ' ') && (c != '\n') && (c != '\t'))
	return false;
	}
	alert (msg);
	textField.focus();
	textField.select();
	return true;
}
function IsNotMaxLength(textField,maxlen,msg) {
  var strValue=textField.value;
  if (strValue.length < maxlen) {
    alert (msg);
    textField.focus();
	textField.select();
    return true;
  }
  return false;
}
function IsNotSelected(textField,msg) {
  if (textField.options[0].selected) {
    alert (msg);
    textField.focus();
    return true;
  }
  return false;
}
function IsNotNumber(textField,msg) {
  var digits = "0123456789";
  for (i=0; i<=textField.size; i++) {
     if (digits.indexOf(textField.value.charAt(i))== -1) {
       alert (msg);
       textField.focus();
       textField.select(); 
       return true;
     } 
  } 
  return false;
}
function IsNotValidAmount(textField,msg){
   var numValue=textField.value;
	if(isNaN(numValue))
	{
		alert (msg);
	    textField.focus();
	    textField.select(); 
	    return true;
	}
    return false;
}
function fixDecimal(DecimalNum) {
 strNum = "" + DecimalNum;
 if (strNum.indexOf('.') == -1)
  return strNum + '.00';
 seperation = strNum.length - strNum.indexOf('.');
 if (seperation > 3)
  return strNum.substring(0,strNum.length-seperation+3);
 else if (seperation == 2)
  return strNum + '0';
 return strNum;
}
function fixAmount()
{
if (!isNaN(document.frmCCP.pg_total_amount.value))
   document.frmCCP.pg_total_amount.value=fixDecimal(document.frmCCP.pg_total_amount.value)
}
//validate the fields in the form before submitting the data
function ValidatefrmCCP() {
if(IsEmpty(document.frmCCP.pg_total_amount,"Please enter the transaction amount"))
	return false;
if(IsNotValidAmount(document.frmCCP.pg_total_amount,"Invalid transaction amount"))
	return false;
/*
if(IsEmpty(document.frmCCP.ecom_billto_postal_name_first,"Please enter your first name"))
	return false;
if(IsEmpty(document.frmCCP.ecom_billto_postal_name_last,"Please enter your last name"))
	return false;
if(IsEmpty(document.frmCCP.ecom_billto_postal_postalcode,"Please enter your zip code"))
	return false;

if(IsNotNumber(document.frmCCP.ecom_billto_postal_postalcode,"Please enter only digits for the zip code"))
	return false;

if(IsNotMaxLength(document.frmCCP.ecom_billto_postal_postalcode,5,"The zip code should be 5 digits long"))
	return false;
*/
if(IsEmpty(document.frmCCP.ecom_payment_card_name,"Please enter the card holder name"))
	return false;
if(IsNotSelected(document.frmCCP.ecom_payment_card_type,"Please select the card type"))
	return false;
if(IsEmpty(document.frmCCP.ecom_payment_card_number,"Please enter the credit card number"))
	return false;
if((document.frmCCP.ecom_payment_card_number,"Please enter only digits for the credit card number"))
	return false;

if(IsNotSelected(document.frmCCP.ecom_payment_card_expdate_month,"Please select the month of the expiration date"))
	return false;
if(IsNotSelected(document.frmCCP.ecom_payment_card_expdate_year,"Please select the year of the expiration date"))
	return false;
	
//check to see if the expiration date is valid
todayDate=new Date()
dteYear=parseInt(document.frmCCP.ecom_payment_card_expdate_year.value,10)
dteMonth=parseInt(document.frmCCP.ecom_payment_card_expdate_month.value,10)-1
if((dteYear<todayDate.getFullYear())||((dteYear==todayDate.getFullYear())&&(dteMonth<todayDate.getMonth())))
{
	alert("Your credit card has already expired")
	return false;
}
return true;
}
//-->


 function PaymentMethod(paymentMethod)  {
                //var end = this.value;
				//$('#paymentMethod12')
	              $("#paymentMethod12").val(paymentMethod);
				//alert(paymentMethod);
				if(paymentMethod == "Card"  ){
				  // $("#manualForm1").hide();
				  // $("#forteForm1").show();
				   //("#manualPayment").hide();
				   $("#manualForm1").css("display", "none");
                  $("#forteForm1").css("display", "block");
				    $("#forteCheck").hide();
            }
			else if(paymentMethod == "Check" || paymentMethod == "Money"){
				 // $("#manualForm1").show();
				 // $("#forteForm1").hide();
				 
				    $("#manualForm1").css("display", "block");
                  $("#forteForm1").css("display", "none");
				   $("#manualPay").show();
                    $("#payroll").hide();
					  $("#forteCheck").hide();
			}
			else if(paymentMethod == "Payroll Deduction"){
				  // alert('hi');
				 // $("#manualForm1").hide();
				  $("#forteForm1").css("display", "none");
				   $("#manualForm1").css("display", "block");
				 // $("#manualPayment").show();
				        $("#payroll").show();
				  //$("#forteForm1").hide();
				   $("#payroll").show();
				   $("#manualPay").hide();
				   $("#forteCheck").hide();

			}
			else if( paymentMethod == "ACH"){
				  // alert('hi');
				 // $("#manualForm1").hide();
				  $("#forteForm1").css("display", "none");
				   $("#manualForm1").css("display", "none");
				 // $("#manualPayment").show();
				        $("#payroll").show();
				  //$("#forteForm1").hide();
				   $("#payroll").hide();
				   $("#manualPay").hide();
				   $("#forteCheck").show();
				  //forteCheck

			}
			
			else {
				 $("#manualForm1").hide();
			}

 }


</script>

<!--<script type="text/javascript" src="https://api.forte.net/js/v1"></script>-->

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
	#pay{background:#F8F2E4;display:block;}
	.pl0{padding-left:0;}
	.pr0{padding-right:0;}
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


	<div class="form_body col-lg-7">
	<div>
   
  <div class="form-group">
    <label for="exampleInputEmail1">Member Name</label>
    <input type="text" name="membername" readonly class="form-control" value="<?php echo $data2[0]->firstName." ".$data2[0]->middleName.' '.$data2[0]->LastName?>" id="exampleInputEmail1" aria-describedby="emailHelp" >
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
    <label for="exampleInputEmail1">Membership Date</label>
    <input type="text" name="membershipDate" readonly class="form-control" value="<?php echo $data2[0]->membershipDate; ?>" >
  </div>
	<div class="form-group">
	  <label for="exampleInputPassword1">Payment Method</label>
		<select  name="paymentMethod" id="drop" class="slct form-control" onchange = "PaymentMethod(this.value)">
		<option value="" selected disabled>SELECT</option>
		<option value="Payroll Deduction">Payroll Deduction</option> 
		<option value="ACH">ACH/EFT</option>
		<option value="Card">Credit/Debit Card</option>
		<option value="Check">Check</option>
		<option value="Money">Money Order</option>
		</select>
	</div>
     <div class="form-group">
	    <label for="exampleInputPassword1">Total Amount</label>
	    <input type="text" readonly name="totalAmount" class="form-control"  value="<?php echo $toataAmont; ?>" >
	  </div>	
	</div>
</div>
<form class="form_body col-lg-5" action="{{ url('/store') }}" method="POST" id="manualForm1">
  {!! csrf_field() !!}
   <input type="hidden" name="burialFee" value="<?php echo $burialFee?>"/>
   <input type="hidden" name="burialPerPerson" value="<?php echo $burialPerPerson?>"/>
   <input type="hidden" name="burialCity"   value="<?php echo $burialCity?>"/>
   <input type="hidden" name="burialState" value="<?php echo $burialState?>"/>
   <input type="hidden" id="paymentMethod12" name="paymentMethod" value=""/>
   <input type="hidden" name="sheduleAmount" value="<?php echo $sheduleAmount?>"/>
   <input type="hidden" name="agentPaymentId" readonly class="form-control" value="<?php echo $agentPaymentId; ?>" >
   <input type="hidden" name="email" readonly class="form-control" value="<?php echo $data2[0]->email; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" >
     <input type="hidden" name="email" readonly class="form-control" value="<?php echo $data2[0]->email; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" >
	   <input type="hidden" name="email" readonly class="form-control" value="<?php echo $data2[0]->email; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" >
     <div>
	    <div class="form-group">
	   
	    <input type="hidden" name="totalAmount" class="form-control"  value="<?php echo $toataAmont; ?>" >
	  </div>
	  <span id="manualPay">
	   <div class="form-group">
	    <label for="exampleInputPassword1">Check/Money Order Payee Name</label>
	    <input type="text" name="payeeName" class="form-control"  value="" >
	  </div>
    
	   <div class="form-group">
	    <label for="exampleInputPassword1">Check/Money Order Number</label>
	    <input type="text" name="checkMoneyOrder" class="form-control"  value="" >
	  </div>
	  </span>
	  <span id="payroll" style="display:none;">
	       <div class="form-group">
				<label for="exampleInputPassword1">Note</label>
				<textarea rows="4" cols="50" class="form-control" name="payrollNote"> </textarea>
	     </div>
	  </span>
	  <div class="text-center">
	<button type="submit" name="submit" id="manualPayment" class="btn btn-primary btn-rounded">Submit</button>
	</div>
	</div>
  
</form>
<!-- onsubmit="return ValidatefrmCCP()"   -->
<form class="form_body col-lg-5" name="frmCCP" action="{{ url('/paymentSucess') }}" method="post"  id="forteForm1">
      {!! csrf_field() !!}
	    <input type="hidden" name="check" value="card"/>
	   <input type="hidden" name="plane_name" value="<?php echo $data2[0]->planName; ?>"/>
	   <input type="hidden" name="burialFee" value="<?php echo $burialFee?>"/>
   <input type="hidden" name="burialPerPerson" value="<?php echo $burialPerPerson?>"/>
   <input type="hidden" name="burialCity"   value="<?php echo $burialCity?>"/>
   <input type="hidden" name="burialState" value="<?php echo $burialState?>"/>
    <input type="hidden" name="sheduleAmount" value="<?php echo $sheduleAmount?>"/>
    <input type="hidden" name="first_name" readonly class="form-control" value="<?php echo $data2[0]->firstName ?>" >
	<input type="hidden" name="last_name" readonly class="form-control" value="<?php echo $data2[0]->LastName?>" >
   <input type="hidden" name="agentPaymentId" readonly class="form-control" value="<?php echo $agentPaymentId; ?>" >
    <div class="form-group"> 
     
	  <INPUT type="hidden" name="pg_total_amount" class="form-control" value="<?php echo $toataAmont; ?>" maxlength=20 onblur="return fixAmount()">
    </div>	
    <div class="form-group">
      <label for="exampleInputEmail1">Card Holder Name:</label> 
      <input class="form-control" name=ecom_payment_card_name required>
    </div>
    
	  <div class="form-group">
      <label for="exampleInputEmail1">Card Number:</label>
      <INPUT class="form-control" id="cc-number" name=ecom_payment_card_number maxlength=16 required >
    </div>
	<div class="form-group"> 
      <label for="exampleInputEmail1">Card Type:</label>
	  <input name ="card_type" id="card_type" readonly  type="text" class="form-control" value="" maxlength="16" required>
	  
	  
	  
      <!--<SELECT class="form-control" name=ecom_payment_card_type>
          <OPTION selected>-SELECT-</OPTION>
          <OPTION value="amex">American Express</OPTION>
          <OPTION value="disc">Discover</OPTION>
          <OPTION value="mast">MasterCard</OPTION>
          <OPTION value="visa">Visa</OPTION>
      </SELECT>-->
    </div>
    
    <div class="form-group"> 
       <label for="exampleInputEmail1">Expiration Date (month/year):</label><br>
	   <div class="row">
		  <div class="col-lg-6 pr0">
			<SELECT class="form-control" name=ecom_payment_card_expdate_month required>
			   <OPTION selected disabled>-Select-</OPTION>
			  <OPTION value="01">01</OPTION>
			  <OPTION value="02">02</OPTION>
			  <OPTION value="03">03</OPTION>
			  <OPTION value="04">04</OPTION>
			  <OPTION value="05">05</OPTION>
			  <OPTION value="06">06</OPTION>
			  <OPTION value="07">07</OPTION>
			  <OPTION value="08">08</OPTION>
			  <OPTION value="09">09</OPTION>
			  <OPTION value="10">10</OPTION>
			  <OPTION value="11">11</OPTION>
			  <OPTION value="12">12</OPTION>
			</SELECT>
		  </div>
		 <div class="col-lg-6">
			<SELECT class="form-control" name=ecom_payment_card_expdate_year required>
			  <OPTION selected disabled>-Select-</OPTION>
			  
			 <?php 
			      $year = date("Y"); 
			      for($i =0;$i<16;$i++){
			 ?>
			     <option><?php echo $year+$i ?></option> 
			   <?php
			  }												   
		   ?>
			</SELECT>
		 </div>
		
	 </div>
    </div>
	 <div class="form-group">
      <label for="exampleInputEmail1">CVV:</label>
        <INPUT class="form-control" name='cvv' maxlength=4 required>
    </div>
	<div class="checkbox">
     <label><input type="checkbox" name="auto_renew" id="auto_renew" value="1">&nbsp;&nbsp;<strong>Auto Renew.</strong></label>
    </div>
    <div class="form-group"> 
        <input type="submit" value="submit" class="btn btn-primary btn-rounded">
	
        <input type="hidden" name="pg_transaction_type" value="">
    </div>
</form>
<form name="" class="form_body col-lg-5" action="{{ url('/paymentSucess') }}" method="post" id="forteCheck" onsubmit="return ValidatefrmChk()">
 {!! csrf_field() !!}
 
  <input type="hidden" name="plane_name" value="<?php echo $data2[0]->planName; ?>"/>
	   <input type="hidden" name="burialFee" value="<?php echo $burialFee?>"/>
	   <input type="hidden" name="check" value="check"/>
   <input type="hidden" name="burialPerPerson" value="<?php echo $burialPerPerson?>"/>
   <input type="hidden" name="burialCity"   value="<?php echo $burialCity?>"/>
   <input type="hidden" name="burialState" value="<?php echo $burialState?>"/>
    <input type="hidden" name="sheduleAmount" value="<?php echo $sheduleAmount?>"/>
    <input type="hidden" name="first_name" readonly class="form-control" value="<?php echo $data2[0]->firstName ?>" >
	<input type="hidden" name="last_name" readonly class="form-control" value="<?php echo $data2[0]->LastName?>" >
   <input type="hidden" name="agentPaymentId" readonly class="form-control" value="<?php echo $agentPaymentId; ?>" >
<P align=center>
	 
	<div class="form-group">
      
      <input class="form-control" type="hidden" name=pg_total_amount maxlength=20 value="<?php echo $toataAmont; ?>" readonly>
    </div>
	<div class="form-group">
      <label for="exampleInputEmail1">Account Holder First Name:</label> 
      <input class="form-control" name="account_holder_name" maxlength=20>
    </div>
    
	 <div class="form-group">
      <label for="exampleInputEmail1">Rounting Number:</label> 
      <input class="form-control"name=ecom_payment_check_trn maxlength=9>
    </div>
   <div class="form-group">
      <label for="exampleInputEmail1">Account Number:</label> 
      <input class="form-control" name=payment_check_account maxlength="17">
    </div>
   <div class="form-group">
      <label for="exampleInputEmail1">Account Type:</label> 
      <select name="payment_check_account_type" class="form-control">
          <option value="savings">Savings</option>
          <option value="checking">Checking</option>
        </select>
    </div>
        <input type=submit value="Complete Payment" class="btn btn-primary btn-rounded" >
           <input type="hidden" name="pg_transaction_type" value="">
</form>


<div class="clearfix"></div>
</span>
</div>
  		</div>
		</div>
	</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.79/jquery.form-validator.min.js"></script>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
 <script>
 /*
$("#forteForm1").validate({
	errorClass: 'errors',
           $( "#cc-number01" ).rules( "add", {
  required: true,
  minlength: 2,
  required: true,
  creditcard: true,
  minlength: 13,
  maxlength: 16,
  digits: true,
});
})
*/
$("#forteForm1").validate({
	
	  /*$( "#cc-number01" ).rules( "add", {
  required: true,
  minlength: 2,
  required: true,
  creditcard: true,
  minlength: 13,
  maxlength: 16,
  digits: true,
});	
*/
 rules: {
     ecom_payment_card_number: {
      required: true,
      minlength: 13,
     maxlength: 16,
     digits: true,
    }
  }
});

</script>
<style>
 label.error {
  color: #a94442;
  font-size:12px;
  font-weight:normal;
  border-color: #ebccd1;
  padding:1px 20px 1px 2px;
}
</style>
</body>
</html>
