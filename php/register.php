<?php

/* 
/* 		This file registers users 
/* 
/* 
/* 
/* 
*/ 

	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "config.php"; 
	
	

if(!empty($_POST['emailRegister']) && !empty($_POST['passwordRegister']))  
{  
    $username = mysql_real_escape_string($_POST['emailRegister']);  
    $password = md5(mysql_real_escape_string($_POST['passwordRegister']));  
    $firstName = mysql_real_escape_string($_POST['firstName']);  
    $lastName = mysql_real_escape_string($_POST['lastName']); 
    $sysRole = "user"; 
      
     $checkusername = mysql_query("SELECT * FROM users WHERE username = '".$username."'");  
 
  
     if(mysql_num_rows($checkusername) == 1)  
     {  
        echo "<h1>Error</h1>";  
        echo "<p>Sorry, that username is taken. Please go back and try again.</p>";  
     }  
     else  
     {  
        $registerquery = mysql_query("INSERT INTO users (username, password, firstName, lastName, sysRole) VALUES('".$username."', '".$password."', '".$firstName."', '".$lastName."', '".$sysRole."')");  
        if($registerquery)  
        {  
            echo "<h1>Success</h1>";  
            echo "<p>Your account was successfully created. Please <a href=\"login.php\">click here to login</a>.</p>";  
        }  
        else  
        {  
            echo "<h1>Error</h1>";  
            echo "<p>Sorry, your registration failed. Please go back and try again.</p>";  
        }  
     }  
}  
else  
{  

            echo "<h1>Ooops!</h1>";  
            echo "<p>Sorry, There was a problem.</p>"; 

}

/* End of file register.php */ 