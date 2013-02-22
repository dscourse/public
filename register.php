<?php 
date_default_timezone_set('America/New_York');
    define('MyConst', TRUE);    // Avoids direct access to config.php
    include "../config/config.php";   

    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))  
    {  
         header('Location: index.php');   // If the user is actually logged in sends the user to home page
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
        function RegisterValidate(){
	        var firstName = $('#firstName').val();                         
	        var lastName = $('#lastName').val();                         
	        var username = $('#username').val();                         
            var password  = $('#password').val(); 
            var password2  = $('#password2').val(); 
            var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;           
            
            var validate = true; 
            var message = ''; 
            
            if(!firstName){ message+= '<li>You need to enter a <b>First Name </b></li>';  		validate = false; }
            if(!lastName){ message += '<li>You need to enter your <b>Last Name</b></li>'; 		validate = false; }
            if(!username){ message += '<li>You need to enter your <b>email address</b></li>'; 	validate = false; }
            if(!password){ message += '<li>You need to enter a <b>password</b></li>'; 			validate = false; }
            if(password != password2){ message += '<li>Your passwords don\'t match. </li>';		validate = false; }
	        if(username.search(emailRegEx) == -1) { message += '<li>The email you entered is not valid. Please try again. </li>';		validate = false;     }
     
	        if(validate == false){
				$('#registerNotify').html("<div class=\"alert alert-error animated flash \"><p><b>Please correct the following errors: </b></p><ul id=\"errorList\"> <p>"+message+"<ul></p>");  		        
	        } else {
		        $.ajax({                                                    
                    type: "POST",
                    url: "php/auth.php",
                    data: {
                        username: username,
                        password: password,                                       // Sends the login data as array
                        firstName: firstName,
                        lastName : lastName, 
                        action : 'register'
                    },
                      success: function(data) {                            
                                $('#registerNotify').html(data);                        
                        }, 
                      error: function(data) {                                   // If php did not run 
                            console.log("dscourse Log: There was a problem connecting to the login script. ");  
                            console.log(data); 
                      }
                });      
	        }
        }
        
        $('#registerSubmit').live('click', function() {                    // Execute register function when clicked
            RegisterValidate();                                          
        }); 
        
        $(document).keypress(function(e) {									// Execute Register when press enter
            if(e.which == 13) {
                RegisterValidate();
            }
        });

    })();                                                               // End of self invoking anonymous function

        
    </script>
</head>

<body class="login-page">
    <div class="container">
        <div class="row">
            <div class="span4 offset4 animated fadeInDown authForms">
                <div class="page-header">
                    <h1>Register</h1>
                </div>

                <div class=" form-vertical ">
                    <div id="registerNotify"></div>

                    <div id="loginform">

                        <div id="firstName_div">
                            <label for="firstName">First Name:</label> <input type="text" name="firstName" id="firstName">
                        </div>

                        <div id="lastName_div">
                            <label for="lastName">Last Name:</label> <input type="text" name="lastName" id="lastName">
                        </div>
                        
                        <div id="username_div">
                            <label for="username">Email:</label> <input type="text" name="username" id="username">
                        </div>

                        <div id="password_div">
                            <label for="password">Password:</label> <input type="password" name="password" id="password">
                        </div>

                        <div id="password2_div">
                            <label for="password2">Retype Password:</label> <input type="password" name="password2" id="password2">
                        </div>

                        <p><button type="submit" id="registerSubmit" class="btn btn-primary">Register</button></p>
                    </div>
                </div>

                <p><i class="icon-arrow-left"></i> <a href="index.php">Back to the home page</a></p>

            </div>
        </div>
    </div>
</body>
</html>
