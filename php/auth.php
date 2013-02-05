<?php 

/*
/*  This file authenticates the user
/*  
/*
*/
	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "../../config/config.php"; 
	
  	$username = mysql_real_escape_string($_POST['username']);  
    $password = md5(mysql_real_escape_string($_POST['password']));  
    $autologin = $_POST['autologin']; 
  	
    $checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' AND Password = '".$password."'");  
    $something = ''; 
    if(mysql_num_rows($checklogin) == 1)  
    {  
        $row = mysql_fetch_array($checklogin);   
  
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
    else  
    {  
        echo "<div class=\"alert alert-error animated flash \">The password/username does not match our records. Please try again</div>"; 
    } 

    
/* End of file auth.php */