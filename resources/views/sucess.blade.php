<!-- Latest compiled and minified CSS -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<div style= "height:1024px">
<div class="jumbotron text-center" style= "height:1024px" >
			<div class="logo">
				
				<img src="{{URL::asset('/images/logo.png')}}" alt="Global Medevac" height="100" width="200" class="img-fluid">
			</div>
  <p> </p>
  <p> </p>
  <p class="lead"><strong>Your Payment Successfully Received...</strong></p>
  <p class="display-3">Thank you for your Global Medevac application.</br> Your information is being processed now and your official Global Medevac Membership Welcome Kit will be mailed out soon. </br>Once received, please remember to always keep your Global Medevac membership card with you.  Welcome to the family!!</p>
    
 <a href = "http://35.235.80.37:3000/admin-dashboard#/customer-management" >Back To Home</a>
</div>

<script>
$(document).ready(function(){
  $("p").click(function(){
    alert("The paragraph was clicked.");
  });
});
</script>
</div>