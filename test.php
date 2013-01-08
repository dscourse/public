<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
    
    if(empty($_SESSION['Username']))                        // Checks to see if user is logged in, if not sends the user to login.php
    {  
        // is cookie set? 
        if (isset($_COOKIE["userCookieDscourse"])){
             
             $getUserInfo = mysql_query("SELECT * FROM users WHERE UserID = '".$_COOKIE["userCookieDscourse"]."' ");  
  
            if(mysql_num_rows($getUserInfo) == 1)  
            {  
                $row = mysql_fetch_array($checklogin);   
          
                $_SESSION['Username'] = $username; 
                $_SESSION['firstName'] = $row[3]; 
                $_SESSION['lastName'] = $row[4];   
                $_SESSION['LoggedIn'] = 1;  
                $_SESSION['status'] = $row[5];
                $_SESSION['UserID'] = $row[0];  
            } else {
                echo "Error: Could not load user info from cookie.";
            }
            
        } else {
        
            header('Location: info.php');                   // Not logged and and does not have cookie
        
        }
        
    }  else {                                               // User is logged in, show page. 
        

       include_once('php/dscourse.class.php');
       $userID = $_SESSION['UserID'];           // Allocate userID to use throughout the page
  
// TEST HERE
  			
  			
  			
  			
  			
// END TEST     
  
  
  
  
  
  }