<?php 

/*
/*  This file authenticates the user
/*  
/*
*/
	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "config.php"; 
	global $pdo;

	$action = $_POST['action']; 	

	$server = $_SERVER['HTTP_HOST']; 

if($action == 'login'){
	$username = mysql_real_escape_string($_POST['username']);  
    $password = md5(mysql_real_escape_string($_POST['password']));  
    $autologin = $_POST['autologin']; 
  	
	$checkusername = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
	$checkusername->execute(array(':username'=>$username, ':password'=>$password));  
    $checkusername = $checkusername->fetch();  
    //$checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' AND Password = '".$password."'");  
    $something = ''; 
    if(!empty($checkusername))  
    {  
 	   $row = $checkusername;   	  
       
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
    
	$checkusername = $pdo->prepare("SELECT * FROM users WHERE username = :username");
	$checkusername->execute(array(':username'=>$username));  
    $checkusername = $checkusername->fetch();  
    //$checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' ");  
    if(!empty($checkusername))  
    {  
			echo "<div class=\"alert alert-error animated flash \">There is already a user with this email. If you forgot your login you can <a href='recover.php'> recover </a> your password. Or try another email address.</div>"; 
    	
    } else {
    	
		$registerquery = $pdo->prepare("INSERT INTO users (username, password, firstName, lastName, sysRole) VALUES(:username, :password, :firstName, :lastName, 'inactive')");
		//$sql = "INSERT INTO `users` ( `username` ,  `password` ,  `firstName` ,  `lastName` ,  `userStatus`) VALUES(  '{$_POST['username']}' ,  '{$pass}' ,  '{$_POST['firstName']}' ,  '{$_POST['lastName']}' , 'inactive') "; 
		//$userNew = mysql_query($sql);
		if($registerquery->execute(array(':username'=>$_POST['username'],':password'=>$pass,':firstName'=>$_POST['firstName'],':lastName'=>$_POST['lastName'])))  
		{
			require_once '../mail/class.phpmailer.php';
			require_once '../mail/mail_init.php';
			$mail = new PHPMailer();
			$mail = mail_init($mail);
			//micro-templating
			$body = file_get_contents('../mail/templates/verify.html');
			$head = "Hi ". $_POST['firstName'];
			$msg = "Thank you for registering at dscourse. Please follow the link below to verify your email and start using the website. <br /><br /> http://".$server."/login.php?action=verify&user=".$username."&code=".$hash." <br /><br /> Let us know if you have any questions.";
			$body = str_replace('%head%',$head, $body);
			$body = str_replace('%msg%', $msg, $body);
			$mail->MsgHTML($body);
			$mail->Subject = 'Verify your email for dscourse.org';
			$mail->AddAddress($_POST['username']);
			
			// Send email
			/*
			 $link =  $_POST['username'].$pass; 
			 $hash = md5($link); 
			 $to = $_POST['username'];
			 $subject = "Verify your email for dscourse";
			 $body = "Hi ". $_POST['firstName'] .",\n\nThank you for registering at dscourse. Please follow the link below to verify your email and start using the website. \n\n http://".$server."/login.php?action=verify&user=".$username."&code=".$hash." \n\n Let us know if you have any questions.";
			 $headers = "From: admin@dscourse.org\r\n";
			 */
			 
			 if ($mail->Send()){
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
    echo "Hello"; 
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
			 $body = "Hi ". $row[3] .",\n\nThank you for registering at dscourse. Please follow the link below to verify your email and start using the website. \n\n http://".$server."/login.php?action=verify&user=".$username."&code=".$hash."  \n\n Let us know if you have any questions.";
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