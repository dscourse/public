<?php 
	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "../config/config.php"; 	

	if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))  
	{  
	     header('Location: home.php');   // If the user is actually logged in sends the user to home page
	} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Recover forgotten password</title>
	<script type="text/javascript" src="assets/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	
	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />	
	<link href="assets/css/animate.css"  rel="stylesheet" type="text/css" />	
	

</head>
<body class="recover-page">
<div class="container">
					
	<div class="row">
	 
<?php 

/**
 *  This file recovers the user password by sending an email and reading recovery code. 
 *  
 *
*/


	   

if (isset($_POST['emailRecover']))
  {
  	$username = mysql_real_escape_string($_POST['emailRecover']); 
  
  //////////////////////////////////////**  SENDING THE EMAIL   **/////////////////////////////////////////// 

  // First check if this email is in fact in our system
      $checkEmailRecover = mysql_query("SELECT * FROM users WHERE Username = '".$username."'");  
  
    if(mysql_num_rows($checklogin) == 1)  
    { 
		    // Generate recovery code 
		    $code = mt_rand(10000, 100000000);    
		    
		    // Write the code and the current timestamp to database (timestamp is for expiring the link 
		    $recoverquery = mysql_query("UPDATE `users` SET  `userRecovery` =  \"".$code."\", userRecoveryTime = '".time()."' WHERE  `username` =\"".$_POST['emailRecover']."\"");  
		    
		    if($recoverquery)  			// Check if database value was changed
	        {  
	             
			    //send email
			      $headers .= "Content-type: text/html\r\n"; 
				  $email = $_POST['emailRecover'] ;
				  $subject = "Reset your Dscourse password" ;
				  $message = " You requested your password to be changed. If you forgot your password click on the link below to set a new password. If this email was not sent by you, you don't need to take any action, your password is not reset until you click the link: <a href=\"http://localhost:8888/dscourse/recover.php?code=".$code."&user=".$email."\" > Reset Password </a>. Mbr /> This link will expire in 24 hours.   ";
				  mail($email, "Subject: $subject",
				  $message, "From: admin@dscourse.com", $headers);
				  
				 	echo '	  	  <div class="span4 offset4"> 		';
					echo '		  	<div class="page-header"> 		';
					echo '			    <h1>Recover Password </h1> 	';
					echo '			  </div>			  			';
					echo '				<div class="well form-vertical">';
					echo '					<div class=\"alert alert-success animated flash \"><p><strong> Success! </strong></p> Your password has been successfully changed. Please check your email for further action. </div>';
					echo '				<p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p></div>';
				  
	        }  
	        else  
	        {  
					echo '	  	  <div class="span4 offset4"> 		';
					echo '		  	<div class="page-header"> 		';
					echo '			    <h1>Recover Password </h1> 	';
					echo '			  </div>			  			';
					echo '				<div class="well form-vertical">';
					echo '					<p><h2> Error! </h2> There was a problem with changing the value. Please try again later, or contact an administrator.</p>';
					echo '				<p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p></div>';	        
			} 

				 	

	    }
	  else
	    {
	    
					echo '	  	  <div class="span4 offset4"> 		';
					echo '		  	<div class="page-header"> 		';
					echo '			    <h1>Recover Password </h1> 	';
					echo '			  </div>			  			';
					echo '				<div class="well form-vertical">';
					echo '					<div class="alert alert-error animated flash "><p><strong> Error! </strong></p> We could not find this email in our system. Please try again. </div>';
					echo '		<form method="POST" action="recover.php" name="recoverform" id="recoverform">';
					echo '		<div id="emailRecover_div"> <label for="emailRecover">Email Address: </label><input type="text" name="emailRecover" id="emailRecover" /></div>';
					echo '		<p><button type="submit" id="recoverSubmit" class="btn btn-info"/>Send Email</button></p>';
					echo '	</form>		';
					echo '				</div>';
				
					echo '	<p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p></div>  ';
	
	    }

  } elseif(isset($_GET['code']) && isset($_GET['user'])) {								// If the code is in the URL initiate the change
	  
	   //////////////////////////////////////**  SHOW CHANGE PASSWORD FORM  **/////////////////////////////////////////// 


	  	// Put the links into variables 
	  	
	  	$linkcode = $_GET['code'];
	  	$user = $_GET['user'];
	  	
	  	// Check the link code with the database, if valid show the box to change password, if not throw error
	  	$checkTimeQuery = mysql_query("SELECT * FROM `users` WHERE `userRecovery` = \"".$linkcode."\" ");  
		    
		    if(mysql_num_rows($checkTimeQuery) == 1)  			
	        { 	
	        	//Check if link has expired 
	        	$row = mysql_fetch_array($checkTimeQuery);
	        	$currentTime = time();  
	        	$diff = $currentTime - $row[14];
	        	if ($diff < 86400) {													// Link expires in 24 hours = 86400 seconds
		        	


echo <<<EOT
	  	  <div class="span4 offset4">
	  	<div class="page-header">
		    <h1>Create New Password </h1>
		  </div>
		  
			<div class="well form-vertical">
<form method="post" action="recover.php" name="registerform" id="registerform">  
<div id="passwordRegister_div"><label for="passwordRecover">Password: </label><input type="password" name="passwordRecover" id="passwordRecover" /></div>
<div id="passwordRegister2_div"><label for="passwordRecover2">Re-EnterPassword: </label><input type="password" name="passwordRecover" id="passwordRecover" /></div>
<input type="userEmail" name="userEmail" id="userEmail" value="$user" />
<p><button id="PasswordSubmit" class="btn btn-success">Change Password</button></p>		
</form>
</div>
EOT;
      	
	        	} else {
		        	
					echo '	  	  <div class="span4 offset4"> 		';
					echo '		  	<div class="page-header"> 		';
					echo '			    <h1>Recover Password </h1> 	';
					echo '			  </div>			  			';
					echo '				<div class="well">';
					echo '					<p> <h2> Error! </h2> Your link has expired. <a href="recover.php" >Use the form again</a> to send a new key.</p><p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p>';
					echo '				</div>';
		        	
	        	}

	        
	        } else {
		        
		        	echo '	  	  <div class="span4 offset4"> 		';
					echo '		  	<div class="page-header"> 		';
					echo '			    <h1>Recover Password </h1> 	';
					echo '			  </div>			  			';
					echo '				<div class="well ">';
					echo '					<p><h2> Error! </h2> Sorry we could not find that link. <a href="recover.php" >Use the form again</a> or check your email.</p> <p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p>';
					echo '				</div>';
		        
	        }
	  	
  } elseif (isset($_POST['passwordRecover'])) {  
  	
  		  //////////////////////////////////////**  CHANGE THE PASSWORD  **/////////////////////////////////////////// 

  		
  		$password = md5(mysql_real_escape_string($_POST['passwordRecover']));
  		
  		$resetpassquery = mysql_query("UPDATE `users` SET  `password` =  \"".$password."\" WHERE  `username` =\"".$_POST['userEmail']."\"");
  		
  		if($resetpassquery)  
        {  
		        	echo '	  	  <div class="span4 offset4"> 		';
					echo '		  	<div class="page-header"> 		';
					echo '			    <h1>Recover Password </h1> 	';
					echo '			  </div>			  			';
					echo '				<div class="well "><div class=\"alert alert-success animated flash \">';
					echo '					<p><h2> Success! </h2> Your password has been changed. </p>';
					echo '				<p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p> </div></div>';
        }  
        else  
        {  
             		echo '	  	  <div class="span4 offset4"> 		';
					echo '				<div class="well "><div class=\"alert alert-error animated flash \">';
					echo '					<p><h2> Error! </h2> Sorry, there was a problem. Try again by going through the email link. </p>';
					echo '				<p><a href=\"login.php\"> Login now.</a>.</p> </div></div>'; 
        }  

  		
  		
  	} else {

	   //////////////////////////////////////**  IF EMPTY SUBMISSION  **/////////////////////////////////////////// 
	  ?>
	  
	  	  <div class="span4 offset4">
	  	<div class="page-header">
		    <h1>Recover Password </h1>
		  </div>
		  
			<div class="well form-vertical">
					  	<p>Enter your email, we will send you instructions to recover your password.</p>
						<div id="recoverNotify">
							<?php 
								 if (isset($_GET['recoverSubmit'])) 
								 {
								 	 echo "<div class=\"alert alert-error animated flash \">Please enter an email.</div>"; 
								 } 
							?>
						</div>	
						<form method="POST" action="recover.php" name="recoverform" id="recoverform">
							<div id="emailRecover_div"> <label for="emailRecover">Email Address: </label><input type="text" name="emailRecover" id="emailRecover" /></div>
							<p><button type="submit" id="recoverSubmit" class="btn btn-info"/>Send Email</button></p>
						</form>		
						</div>  
									  <p><a href="index.php"><i class="icon-arrow-left"></i> Back to the home page </a></p>

	</div>


			
<?php 
	 
  }

?>

</div>

</body>
</html>