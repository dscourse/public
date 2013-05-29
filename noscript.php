<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
	date_default_timezone_set('UTC');
    
    if(empty($_SESSION['Username']))                        // Checks to see if user is logged in, if not sends the user to login.php
    {  
        // is cookie set? 
        if (array_key_exists('userCookieDscourse', $_COOKIE)){
             
             $getUserInfo = mysql_query("SELECT * FROM users WHERE UserID = '".$_COOKIE["userCookieDscourse"]."' ");  
  
            if(mysql_num_rows($getUserInfo) == 1)  
            {  
                $row = mysql_fetch_array($getUserInfo);   
          
                $_SESSION['Username'] = $row[1]; 
                $_SESSION['firstName'] = $row[3]; 
                $_SESSION['lastName'] = $row[4];   
                $_SESSION['LoggedIn'] = 1;  
                $_SESSION['status'] = $row[5];
                $_SESSION['UserID'] = $row[0]; 
                header('Location: index.php'); 
                
            } else {
                echo "Error: Could not load user info from cookie.";
            }
            
        } else {

            header('Location: info.php');                   // Not logged and and does not have cookie
        
        }
        
    }  else {  
        include_once('php/dscourse.class.php');
        
	    	      
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        $userNav = $dscourse->UserInfo($userID); 
        

		

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse </title>
    
    <?php include('php/header_includes.php');  ?>
    

</head>

<body>


     <div id="noscript" class=" wrap page formPage" >


        <div class="container-fluid">
            <div class="row-fluid">
            	<div class="span6 offset3 center">
	            	<h1><img src="img/access.png" alt="access" width="64" height="64"></h1>
	            	<h3>Javascript is disabled!</h3>
	            	<p>  Unfortunately it looks like your javascript is disabled. Dscourse is a web application that relies heavily on javascript and would not function as intended when javascript is turned off. Please turn javascript on and click <a href="http://www.dscourse.org"> <b>here</b> </a> to go back to dscourse. 
	            </div>

            </div><!-- close row -->
        </div><!-- close container -->
    </div><!-- close noscript -->


        <?php

       }  
            
    ?>
</body>
</html>
