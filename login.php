<?php 
    define('MyConst', TRUE);    // Avoids direct access to config.php
    include "../config/config.php";   
	date_default_timezone_set('America/New_York');

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
                                window.location.href = "index.php"; 
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
        
        $(document).keypress(function(e) {
            if(e.which == 13) {
                Login();
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
                    <h1>Login</h1>
                </div>

                <div class="form-vertical">
                    <div id="loginNotify"></div>

                    <div id="loginform">
                        <div id="username_div">
                            <label for="username">Email:</label> <input type="text" name="username" id="username">
                        </div>

                        <div id="password_div">
                            <label for="password">Password:</label> <input type="password" name="password" id="password">
                        </div>

                        <p><label>&nbsp;</label><input type="checkbox" id="autologin" name="autologin" value="Yes"> Remember Me on this computer</p>

                        <p><button type="submit" id="loginSubmit" class="btn btn-primary">Login</button> <a href="recover.php" id="recoverLink">Forgot Password?</a></p>
                    </div>
                </div>

                <div class="link" > <i class="icon-arrow-left"></i> <a href="index.php">Back to the home page</a></div>

            </div>
        </div>
    </div>
</body>
</html>
