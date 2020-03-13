<!-- Latest compiled and minified CSS -->
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<div style= "height:1024px">
<div class="jumbotron text-center" style= "height:1024px" >
			<div class="logo">
				<img src="images/logo.png" width="200px" height="100px" alt="Global Medevac" class="img-fluid">
			</div>
  <p> </p>
  <p> </p>
  <p class="lead"><strong> We regret!! Your payment unsuccessful.</strong></p>
  <p>Back to <a  href="http://35.235.80.37:3000/admin-dashboard#/customer-management" id="redirect" >Home</a></p>
  <p class="display-3"></p>
    
 
</div>
<script>
$(document).ready(function(){
  $("#redirect").click(function(){
	 // alert('hi it is test');
    $.ajax({url: "session_unset.php", success: function(result){
     window.location.href = "client-registration-form.php"
	// alert('form was submitted');
  }
});
});
});
</script>
</div>