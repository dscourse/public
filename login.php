<?php 
	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "scripts/php/config.php"; 	

	if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))  
	{  
	     header('Location: index.php');   // If the user is actually logged in sends the user to home page
	} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Welcome to dscourse</title>
	<script type="text/javascript" src="assets/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	
	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />	
	<link href="assets/css/animate.css"  rel="stylesheet" type="text/css" />	

	<script type="text/javascript" >
		(function() { 														// Auto runs everything inside when this script is loaded
	
		$('#loginSubmit').live('click', function() {  					// Execute login function when clicked
			
			var username = $('#username').val();						// Gets username and password 
			var password  = $('#password').val(); 
			
			if (!username) {
				$('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a username</div>");	
			} else if (!password) {
				$('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a password</div>");	
			} else {
				$.ajax({													
					type: "POST",
					url: "scripts/php/auth.php",
					data: {
						username: username,
						password: password										// Sends the login data as array
					},
					  success: function(data) {	
							
					  		if (data == "redirect"){
					  			window.location.href = "index.php";	
					  		}else {						// If script ran successfully 
					    		$('#loginNotify').html(data);						// The error alerts will be printed here  
					    	}
					    }, 
					  error: function() {									// If php did not run 
							console.log("dscourse Log: There was a problem connecting to the login script. ");  
					  }
				});				
			}		 									
		});	
	
	})();																// End of self invoking anonymous function
	
		
	</script>
	
</head>
<body class="login-page">
<div class="container">
					
	<div class="row">
	 
	
	  <div class="span4 offset4 animated fadeInDown">
	  	<div class="page-header">
		    <h1>Login </h1>
		  </div>
		  
			<div class="well form-vertical ">
						<div id="loginNotify"> </div>	
							<div id="loginform">  
							<div id="username_div"> <label for="username">Email: </label><input type="text" name="username" id="username" /></div>
							<div id="password_div"><label for="username">Password: </label><input type="password" name="password" id="password" /></div>
							<p><button type="submit" id="loginSubmit" class="btn btn-primary"/>Login</button>      <a href="recover.php" id="recoverLink">  Forgot Password?</a> </p>
							</div>
		  </div>
		  <p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p>
		  <p>To try out the system please use the guest account or contact us to become a beta tester.</p>

						
		  
	</div>

			
		
</div>

</body>
</html>
