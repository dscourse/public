<?php 

/*
/*  This file adds user email to the newsletter table 
/*  
/*
*/
	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "config.php"; 
	

	
  	$email = mysql_real_escape_string($_POST['email']);  
  	
    $signupquery = mysql_query("INSERT INTO newsletter (email) VALUES('".$email."')");  
  
    if($signupquery)  
    {  

        echo "<p><div class=\"alert alert-success animated fadeIn \"><button class=\"close\" data-dismiss=\"alert\">×</button>Your email is recorded. You'll hear from us soon. Thanks!</div></p>"; 				// Sends back success message
          
    }  
    else  
    {  
        echo "<p><div class=\"alert alert-error animated fadeIn \"><button class=\"close\" data-dismiss=\"alert\">×</button>There was an error connecting. Please try again later. </div></p>"; 
    } 

    
/* End of file newsletter.php */