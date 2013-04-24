<?php 
    define('MyConst', TRUE);    // Avoids direct access to config.php
    include "php/config.php";   
	date_default_timezone_set('UTC');

	if(isset($_GET['r'])){
		$redirect = $_GET['r']; 
	} else {
		$redirect = 'index.php'; 
	}

    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))  
    {  
         header('Location: index.php');   // If the user is actually logged in sends the user to home page
    } 

    if(isset($_GET['action'])){
	    $action = $_GET['action'];
	    
	    if($action == 'verify'){
		    $username = $_GET['user']; 	// Get the data from URL
		    $code = $_GET['code'];
		    $message = " "; 

		   $checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' ");  
		    if(mysql_num_rows($checklogin) == 1)  					// Check if user exists
		    {  
		 	   $row = mysql_fetch_array($checklogin);   	  
		       
		        if($row[11] == 'inactive'){							// Check if user is already verified
					// Send email
					 $link =  $username.$row[2]; 
					 $hash = md5($link); 
					 if($hash == $code){							// Check if verification code is correct
						$verifyUpdate = mysql_query("UPDATE users SET userStatus = 'active' WHERE Username = '".$username."' ");  // Update the data table						
						 if ($verifyUpdate) {
							 	$message = "<div class=\"alert alert-success animated flash \"><b>Verification Complete!<b> <br /> Please login below.   </div>"; 
						  } else {
							  	$message = "<div class=\"alert alert-error animated flash \">Opps, something went wrong! It might be that our servers are done or the page did not load properly. Try submitting again.</div>"; 
						  }

					 } else {
			         	$message =  "<div class=\"alert alert-error animated flash \"> The code for this verification is wrong. Please check the URL and go back to the email you received. If you can't locate the email we can <a href='#' id='reVerify'>send it again</a>. </div>"; 
					 }
						
		        } else {
			         $message =  "<div class=\"alert alert-error animated flash \">This user has already verified. If you believe this is in error please contact us. </div>"; 
		        }
		    }  
		    else  
		    {  
		        $message = "<div class=\"alert alert-error animated flash \">This user doesn't seem to exist.</div>"; 
		    } 
	    }
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html lang="en">
<head>
    <title>Welcome to dscourse</title>
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link href="css/bootstrap.css" media="screen" rel="stylesheet" type="text/css">
    <link href="css/style.css" media="screen" rel="stylesheet" type="text/css">
    <link href="css/animate.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">
    (function() {                                                       // Auto runs everything inside when this script is loaded

        
        function Login(){
            var username = $('#username').val();                        // Gets username and password 
            var password  = $('#password').val(); 
            var autologin  = $('#autologin').attr('checked'); 
            console.log(autologin);
            
            if (!username) {
                $('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a username<\/div>");   
            } else if (!password) {
                $('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a password<\/div>");   
            } else {
                $.ajax({                                                    
                    type: "POST",
                    url: "php/auth.php",
                    data: {
                        username: username,
                        password: password,                                       // Sends the login data as array
                        autologin: autologin,
                        action : 'login'
                    },
                      success: function(data) { 
                            
                            if (data == "redirect"){
                                window.location.href = '<?php echo $redirect ?>'; 
                            }else {                     // If script ran successfully 
                                $('#loginNotify').html(data);                       // The error alerts will be printed here  
                            }
                        }, 
                      error: function() {                                   // If php did not run 
                            console.log("dscourse Log: There was a problem connecting to the login script. ");  
                      }
                });             
            }   
            
        }
        
        $('#loginSubmit').live('click', function() {                    // Execute login function when clicked
            Login();                                          
        }); 

        $('#reVerify').live('click', function() {                    // Execute login function when clicked
            var username = $('#username').val();                        // Gets username and password 
	        console.log(username);
	        $.ajax({                                                    
                    type: "POST",
                    url: "php/auth.php",
                    data: {
                        username: username,
                        action : 'reVerify'
                    },
                      success: function(data, status) {                             
                            $('#loginNotify').html(data);                       // The error alerts will be printed here                              
                            console.log('This happened!');
                        }, 
                      error: function() {                                   // If php did not run 
                            console.log("dscourse Log: There was a problem connecting to the login script to verify user. ");  
                      }
                });                                                             
        }); 
                
        $(document).keypress(function(e) {
            if(e.which == 13) {
                Login();
            }
        });

    })();                                                               // End of self invoking anonymous function

        
    </script>
    <style >
    	body {
	    	background: #f5f5f5;
    	} 
    </style>
</head>

<body class="login-page">
    <div class="container">
        <div class="row">
            <div class="span4 offset4 animated fadeInDown authForms">
                <div class="page-header">
                    <h1>Login</h1>
                </div>

                <div class="form-vertical">
                    <div id="loginNotify"><?php if(isset($message)){ echo $message; } ?></div>

                    <div id="loginform">
                        <div id="username_div">
                            <label for="username">Email:</label> <input type="text" name="username" id="username" value="<?php if(isset($_GET['user'])){ echo $_GET['user']; }?>">
                        </div>

                        <div id="password_div">
                            <label for="password">Password:</label> <input type="password" name="password" id="password">
                        </div>

                        <p><label>&nbsp;</label><input type="checkbox" id="autologin" name="autologin" value="Yes"> Remember Me on this computer</p>

                        <p><button type="submit" id="loginSubmit" class="btn btn-primary">Login</button> <a href="recover.php" id="recoverLink">Forgot Password?</a></p>
                    
                    
                    </div>
                    <div id="registerLink"> Not a member yet?  <a href="register.php"> Register Here </a> </div>
                    
                </div>

                <div class="link" > <i class="icon-arrow-left"></i> <a href="index.php">Back to the home page</a></div>

            </div>
        </div>
    </div>
</body>
</html>
