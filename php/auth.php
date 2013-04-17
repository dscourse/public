<?php 
/*
/*  This file authenticates the user
/*  
/*
*/
	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "../../config/config.php"; 

	$action = $_POST['action']; 	

if($action == 'login'){
	$username = mysql_real_escape_string($_POST['username']);  
    $password = md5(mysql_real_escape_string($_POST['password']));  
    $autologin = $_POST['autologin']; 
  	
    $checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' AND Password = '".$password."'");  
    $something = ''; 
    if(mysql_num_rows($checklogin) == 1)  
    {
 	   $row = mysql_fetch_array($checklogin);   	  
       
        if($row[11] == 'inactive'){
	         echo "<div class=\"alert alert-error animated flash \">This user is registered to the website but the email is not yet verified. Please check your email for the verification link. If you can't find the email we can <a href='#' id='reVerify'>send it again</a>. </div>"; 
        } else {
	        $_SESSION['Username'] = $username; 
	        $_SESSION['firstName'] = $row[3]; 
	        $_SESSION['lastName'] = $row[4];   
	        $_SESSION['LoggedIn'] = 1;  
			$_SESSION['status'] = $row[5];
			$_SESSION['UserID'] = $row[0];  	
			if($autologin == 'checked'){
				$expire=time()+60*60*24*10;
				setcookie("userCookieDscourse", $row[0] , $expire, '/');
			}	        
         echo  "redirect"; 										// Sends back a redirect message which the ajax will use to redirect to home.php         

        }
    }  
    else  
    {  
        echo "<div class=\"alert alert-error animated flash \">The password/username does not match our records. Please try again</div>"; 
    } 
}	

if($action == 'register'){
	foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
	$pass = md5($_POST['password']); 
	
	// check if username exists
	$username = $_POST['username'];  
    
    $checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' ");  
    if(mysql_num_rows($checklogin) == 1)  
    {  
			echo "<div class=\"alert alert-error animated flash \">There is already a user with this email. If you forgot your login you can <a href='recover.php'> recover </a> your password. Or try another email address.</div>"; 
    	
    } else {
		$sql = "INSERT INTO `users` ( `username` ,  `password` ,  `firstName` ,  `lastName` ,  `userStatus`) VALUES(  '{$_POST['username']}' ,  '{$pass}' ,  '{$_POST['firstName']}' ,  '{$_POST['lastName']}' , 'inactive') "; 
		$userNew = mysql_query($sql);
		if($userNew){
		
			// Send email
			 $link =  $_POST['username'].$pass; 
			 $hash = md5($link); 
			 $to = $_POST['username'];
			 $subject = "Verify your email for dscourse";
			 $body = "Hi ". $_POST['firstName'] .",\n\nThank you for registering at dscourse. Please follow the link below to verify your email and start using the website. \n\n http://localhost:8888/dscourse/login.php?action=verify&user=".$username."&code=".$hash." \n\n Let us know if you have any questions.";
			 $headers = "From: admin@dscourse.org\r\n";
			 if (mail($to, $subject, $body, $headers)) {
				 	echo "<div class=\"alert alert-success animated flash \">Almost there! We sent you an email to verify your email address. Please check your email and click on the verification link provided there.</div>"; 
			  } else {
				  	echo "<div class=\"alert alert-error animated flash \">Opps, something went wrong! It might be that our servers are done or the page did not load properly. Try submitting again.</div>"; 
			  }
		} else {
			echo "<div class=\"alert alert-error animated flash \">There was an error writing the user to the database. Please try again later.</div>"; 
		}	    
	    
    }
}	
if($action == 'reVerify'){
	$username = mysql_real_escape_string($_POST['username']);  
    $checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' ");  
    if(mysql_num_rows($checklogin) == 1)  
    {  
 	   $row = mysql_fetch_array($checklogin);   	  
       
        if($row[11] == 'inactive'){
			// Send email
			 $link =  $username.$row[2]; 
			 $hash = md5($link); 
			 $to = $username;
			 $subject = "Verify your email for dscourse";
			 $body = "Hi ". $row[3] .",\n\nThank you for registering at dscourse. Please follow the link below to verify your email and start using the website. \n\n http://localhost:8888/dscourse/login.php?action=verify&user=".$username."&code=".$hash." \n\n Let us know if you have any questions.";
			 $headers = "From: admin@dscourse.org\r\n";
			 if (mail($to, $subject, $body, $headers)) {
				 	echo "<div class=\"alert alert-success animated flash \">Almost there! We sent you an email to verify your email address. Please check your email and click on the verification link provided there.</div>"; 
			  } else {
				  	echo "<div class=\"alert alert-error animated flash \">Ooops, something went wrong! It might be that our servers are done or the page did not load properly. Try submitting again.</div>"; 
			  }			
        } else {
	         echo "<div class=\"alert alert-error animated flash \">This user has already verified. If you believe this is in error please contact us. </div>"; 
        }
    }  
    else  
    {  
        echo "<div class=\"alert alert-error animated flash \">The password/username does not match our records. Please try again</div>"; 
    } 
}	
/* End of file auth.php */