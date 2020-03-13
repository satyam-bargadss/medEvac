<!DOCTYPE html>
<?php

        //print_r($data2['planename']);
		$APILoginID  = '523YMcY0xe';
		$SecureTransactionKey = 'Q3IgVv2r8l2K';
	    //$MerchantID    = "383572";
		//$totalamount = "{1375.23,1573.66,56.99,0|Total outstanding,Last statement balance,Minimum balance,Specify different amount};500.00";
		$totalamount =$data2['fee'];
		$method = 'sale';
		$tax_amount=$data2['tax'];
		$version = '1.0';
		$ordernumber = 'A1WEWS234';
		date_default_timezone_set("America/Chicago");
		$unixtime = strtotime(gmdate('Y-m-d H:i:s'));
		$millitime = microtime(true) * 1000;
		$utc = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
		$data = "$APILoginID|$method|$version|$totalamount|$utc|$ordernumber||";
		//$data = "$APILoginID|$method|$version|$totalamount|$utc|$MerchantID|$ordernumber||";
		$hash = hash_hmac('md5',$data,$SecureTransactionKey);
		$schedule_start_date="10/23/2019";
?>
<head>   
<script>
function oncallback(e) {
        //$('#message').html(e.data);
		console.log(e.data);
		var data = JSON.parse(e.data);
	     console.log(data);
		 if(data.event == 'success'){
			$('#success').html('Thank You for  your  Payment');
			 $('#pay').hide();	
			 var timer = setTimeout(function() {
           // window.history.back();
		   window.location.href = 'http://localhost:3000/admin-dashboard#/customer-management';
        }, 3000);
            //$('#pay').hide();			
			 
		 }
		  if(data.event == 'failure'){
			  alert('hi')
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

<script type="text/javascript" src="https://sandbox.forte.net/checkout/v1/js"></script>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
</head>
<body>
<!--<div id="message" style="background-color:#e5e5e5"></div>-->
<div id="success" style="background-color:#e5e5e5"></div>
<span id="pay">
<div>Plane Name:<?php echo $data2['planename']; ?><div>
<div>First Name:<?php echo $data2['firstName']; ?><div>
<div>Last Name:<?php echo $data2['lastName']; ?><div>
<div>Plan Fee :<?php echo $data2['fee']; ?><div>
<div>Burial Fee :<?php echo $data2['fee']; ?><div>
<div>Seminar Fee :<?php echo $data2['fee']; ?><div>
	
     <!-- <button  
        api_login_id=<?php //echo $APILoginID;?>		
		version_number=<?php //echo $version;?>		
		callback="oncallback" 		
		method="<?php //echo $method;?>"
		total_amount="<?php //echo $totalamount;?>"
		utc_time=<?php //echo $utc;?>
		signature=<?php //echo $hash;?>		
		order_number=<?php //echo $ordernumber;?>		
		>Pay now</button>-->
		<button 		
        api_login_id=<?php echo $APILoginID;?>		
		version_number=<?php echo $version;?>		
		callback="oncallback"
        schedule_frequency=<?php echo "monthly" ?>		
		method=<?php echo $method;?>
		total_amount="<?php echo $totalamount;?>"
		utc_time=<?php echo $utc;?>
		tax_amount=<?php echo $tax_amount ?>
		signature=<?php echo $hash;?>	
        schedule_start_date=<?php echo $schedule_start_date?>		
		order_number=<?php echo $ordernumber;?>		
		>Pay now</button>
		
</span>
</body>
</html>
	 
 