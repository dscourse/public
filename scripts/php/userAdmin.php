<?php 

/**
 *  The new and improved user management script. 
 *
 */

	define('MyConst', TRUE);									// Avoids direct access to config.php
	include "config.php"; 										// For database access 

	if (isset($_POST['user'])) 									// Get the user object and assign to array
	{
		$user 	= $_POST['user'];									
	}
	
	$action	= $_POST['action'];									// See what we're trying to do
	
	$currentUser = $_SESSION['UserID'];							// Get current logged in user id
	
	if ($_SESSION['status'] == 'Administrator') 				// Check if current logged in user has admin rights 
	{ 
		$admin = true; 
	} 
	else
	{
		$admin = false;	
	} 
	
	
	
	/************************************** Get All User Data   ********************************/

	if ($action == 'getAll'){ 
		
		if ($admin == true) {									// If the current user is admin
				
				header('Content-type: application/json');							// Set headers for transferring the data between php and ajax    
	
				$userData = mysql_query("SELECT * FROM users ORDER BY firstName ASC"); 			// Get all the data from the users table
				
				while($r = mysql_fetch_assoc($userData)) {					// Populate the rows for individual users
						
						$userJson[] = $r;									// Add row to array
				
				}
				
				echo json_encode($userJson);								// Send back array for json object
			}
			else 
			{
				echo "dscourse log: Current user does not have privilages.";
			}
				
	
	 }


	/************************************** Get Single User Data   ********************************/

	if ($action == 'getUser'){ 
			header('Content-type: application/json');					// Set headers for transferring the data between php and ajax    

			$profileData = mysql_query("SELECT * FROM `users` WHERE `UserID` = '".$user."' ");  // Get user data for the specific user
			
    		$row = mysql_fetch_assoc($profileData); 					// Put mysql results into an array 
    	
    		 $profile['firstName'] = $row['firstName'];									// Add  row to the array
			 $profile['lastName'] = $row['lastName'];
			 $profile['email'] = $row['username'];
			 $profile['about'] = $row['userAbout'];
			 $profile['facebook'] = $row['userFacebook'];
			 $profile['twitter'] = $row['userTwitter'];
			 $profile['phone'] = $row['userPhone'];
			 $profile['website'] = $row['userWebsite'];
			 $profile['status'] = $row['userStatus'];
			 $profile['image'] = $row['userPictureURL'];

			echo json_encode($profile);		
	
	 }	


	/************************************** Update Single User Data   ********************************/

	if ($action == 'updateUser'){ 
		$UserID = $user['UserID'];
	    $password = md5(mysql_real_escape_string($user['password']));  
	    $firstName = mysql_real_escape_string($user['firstName']);  
	    $lastName = mysql_real_escape_string($user['lastName']); 
	    $username = mysql_real_escape_string($user['username']);
	    $sysRole = $user['sysRole']; 
		$userPictureURL = $user['userPicture']; 
	    $userAbout = mysql_real_escape_string($user['userAbout']); 
	    $userFacebook = mysql_real_escape_string($user['userFacebook']); 
	    $userTwitter = mysql_real_escape_string($user['userTwitter']); 
	    $userPhone = mysql_real_escape_string($user['userPhone']); 
	    $userWebsite = mysql_real_escape_string($user['userWebsite']); 
	    $userStatus = $user['userStatus']; 
		
		$updateUserData = mysql_query("SELECT * FROM users WHERE UserID = '".$UserID."'"); 			
		
	   	if($updateUserData)  								// Check if database value was changed
	        {
	             $r = mysql_fetch_assoc($updateUserData);
				  if ($user['password'] == "") {
				 	$password = $r['password'];
				  } 
			}  
	        else  
	        { 
			            
	        }
	
		$updateUserQuery = mysql_query("UPDATE `users` SET  `firstName` =  '".$firstName."', `lastName` =  '".$lastName."', `username` =  '".$username."', `sysRole` =  '".$sysRole."', `password` =  '".$password."', `userFacebook` =  '".$userFacebook."', `userTwitter` =  '".$userTwitter."',  `userPhone` =  '".$userPhone."',  `userWebsite` =  '".$userWebsite."', `userStatus` =  '".$userStatus."', `userPictureURL` =  '".$userPictureURL."', `userAbout` =  '".$userAbout."'   WHERE  `UserID` = '".$UserID."' ");  			
				  
		    
		    if($updateUserQuery)  			// Check if database value was changed
	        {
	        	
            	echo "<div class=\"alert alert-success \"><button class=\"close\" data-dismiss=\"alert\">×</button><strong> Success! </strong> The user information has been updated.";			   
	        }  
	        else  
	        { 
	            echo "<div class=\"alert alert-error \"><button class=\"close\" data-dismiss=\"alert\">×</button><strong> Error! </strong> Sorry, the changes were not saved. Please refreh the page and try again."; 	        
	         } 	
	
	
	
	 }		

	/************************************** Add Single User Data   ********************************/

	if ($action == 'addUser'){ 
			$username = mysql_real_escape_string($user['username']);  
	    $password = md5(mysql_real_escape_string($user['password']));  
	    $firstName = mysql_real_escape_string($user['firstName']);  
	    $lastName = mysql_real_escape_string($user['lastName']); 
	    $sysRole = $user['sysRole']; 
	    $userPictureURL = mysql_real_escape_string($user['userPicture']); 
	    $userAbout = mysql_real_escape_string($user['userAbout']); 
	    $userFacebook = mysql_real_escape_string($user['userFacebook']); 
	    $userTwitter = mysql_real_escape_string($user['userTwitter']); 
	    $userPhone = mysql_real_escape_string($user['userPhone']); 
	    $userWebsite = mysql_real_escape_string($user['userWebsite']); 
	    $userStatus = $user['userStatus'];; 								// New user default status
	
	    
	    $checkusername = mysql_query("SELECT * FROM users WHERE username = '".$username."'");  

	    if(mysql_num_rows($checkusername) == 1)  
	    {  
       		 echo "<div class=\"alert alert-error \"><button class=\"close\" data-dismiss=\"alert\">×</button><strong> Error! </strong>This username is already taken. Please choose another.</div>"; 
        }  
        else  
        {  
        	$registerquery = mysql_query("INSERT INTO users (username, password, firstName, lastName, sysRole, userFacebook, userTwitter, userPhone, userWebsite, userStatus, userPictureURL, userAbout) VALUES('".$username."', '".$password."', '".$firstName."', '".$lastName."', '".$sysRole."', '".$userFacebook."', '".$userTwitter."', '".$userPhone."', '".$userWebsite."', '".$userStatus."', '".$userPictureURL."', '".$userAbout."')");  
        	if($registerquery)  
        	{  
            	echo "<div class=\"alert alert-success \"><button class=\"close\" data-dismiss=\"alert\">×</button><strong> Success! </strong> The account is created.";  
            }  
            else  
            {  
	            echo "<div class=\"alert alert-error \"><button class=\"close\" data-dismiss=\"alert\">×</button><strong> Error! </strong> Sorry, your registration failed. Please try again.";  
	        }  
	     }  
		

	
	 }	
	


		
		
/* End of file userAdmin.php */ 