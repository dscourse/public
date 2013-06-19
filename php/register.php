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
	global $pdo;
	

if(!empty($_POST['emailRegister']) && !empty($_POST['passwordRegister']))  
{  
    $username = mysql_real_escape_string($_POST['emailRegister']);  
    $password = md5(mysql_real_escape_string($_POST['passwordRegister']));  
    $firstName = mysql_real_escape_string($_POST['firstName']);  
    $lastName = mysql_real_escape_string($_POST['lastName']); 
    $sysRole = "user"; 
      
	 $checkusername = $pdo->prepare("SELECT * FROM users WHERE username = :username");
	 $checkusername->execute(array(':username'=>$username));  
     $checkusername = $checkusername->fetch();  
  
     if(!empty($checkusername))  
     {  
        echo "<h1>Error</h1>";  
        echo "<p>Sorry, that username is taken. Please go back and try again.</p>";  
     }  
     else  
     {
     	$registerquery = $pdo->prepare("INSERT INTO users (username, password, firstName, lastName, sysRole) VALUES(:username, :password, :firstName, :lastName, :sysRole)");
        if($registerquery->execute(array(':username'=>$username,':password'=>$password,':firstName'=>$firstName,':lastName'=>$lastName,':sysRole'=>$sysRole)))  
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