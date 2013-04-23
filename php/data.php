<?php 
/**
 *  The new and improved script for getting and saving data to the database. 
 *
 */
date_default_timezone_set('UTC');

ini_set('display_errors',1); 
 error_reporting(E_ALL);
	if (!defined('MyConst')) define('MyConst', TRUE);								// Avoids direct access to config.php
 	/*** Connect to Database ***/
	include "config.php"; 
	include_once "dscourse.class.php"; 
	include "simpleImage.class.php"; 
	
	mysql_query("SET time_zone = '+00:00'"); 
	
	$user_context = '';
	if(array_key_exists('lis_person_contact_email_primary', $_REQUEST)||!isset($_POST['action'])){
		$user_context = "LTI";
	}
if($user_context == ''){
 	$action	= $_POST['action'];									// What the ajax call asks the php to do. 
	$username = $_SESSION['Username'];
	$admin = $_SESSION['status'];								// Takes the user id so we know which user information to show
    $thisUser = $_SESSION['UserID'];
    header('Content-type: application/json');							// Set headers for transferring the data between php and ajax    

    // We need to escape entries
    
    if ($action == 'updateNetwork')
    {
    	UpdateNetwork();     
    }

    if ($action == 'editUserInfo') 
    {
    	EditUserInfo();     
    }    

    if ($action == 'addUsersToNetwork') 
    {
    	AddUsersToNetwork(); 
    } 

    if ($action == 'addCourse') 
    {
    	AddCourse(); 
    } 
    if ($action == 'editCourse') 
    {
    	EditCourse(); 
    } 
    if ($action == 'addDiscussion') 
    {
    	AddDiscussion(); 
    }     
    if ($action == 'editDiscussion') 
    {
    	editDiscussion(); 
    }  
    if ($action == 'getData') 
    {
    	GetData(); 
    } 
    if ($action == 'joinNetwork') 
    {
    	JoinNetwork(); 
    } 
    if ($action == 'addPost') 
    {
    	AddPost(); 
    }     
    if ($action == 'editPost') 
    {
    	EditPost(); 
    }  
    if ($action == 'checkNewPosts')
    {
    	CheckNewPosts();     
    }	
	if ($action == 'addLog')
    {
    	AddLog(); 
    }
	if ($action == 'saveOptions')
    {
    	SaveOptions(); 
    }
}
            
function UpdateNetwork(){
 	/**
 	 * network
 	 * 
 	 * (default value: $_POST['network'])
 	 * 
 	 * @var string
 	 * @access public
 	 */
 	 
 	if(isset($_POST['network'])){ 
	 	$network = $_POST['network']; 
	 	$networkID 	 = $network['networkID'];
	 	$networkName = $network['networkName']; 
	 	$networkDesc = $network['networkDesc'];
	 	$networkType = $network['networkType']; 
	 	$networkUser = $network['networkUser'];
	 	$networkRole = $network['networkRole'];
	 	$networkCode = rand(100000, 1000000000);
 	} else {
	 	$networkName = $_POST['networkName']; 
	 	$networkDesc = $_POST['networkDesc']; 
	 	$networkType = $_POST['networkType']; 
	 	$networkID	 = $_POST['networkID']; 
 	}

 	
 	if($networkID == 0 ) {		// New network 
		// Add network
		$networkInsert = mysql_query("INSERT INTO networks (networkName, networkDesc, networkStatus, networkCode) VALUES('".$networkName."', '".$networkDesc."', '".$networkType."', '".$networkCode."')"); 
		$networkNewID = mysql_insert_id(); // Get the ID of the newly created network. 

	 	if($networkInsert){
		 	$message =  'Network insert worked.'.$networkID .'+'.$networkNewID;  
	 	} else {
		 	$message =  'Network insert failed.';
	 	}
	 	
		// Add user as admin		 	
		$networkUsersInsert = mysql_query("INSERT INTO networkUsers (networkID, userID, networkUserRole) VALUES(".$networkNewID.", '".$networkUser."', '".$networkRole."')"); 
	 	if($networkUsersInsert){
		 	$message =  'Network user insert worked.'.$networkID .'+'.$networkNewID;  
	 	} else {
		 	$message = 'Network user insert failed.';
	 	}
	 	
	 	echo json_encode($message); 

	 	
 	} else {					// Update network 
		$networkUpdate = mysql_query("UPDATE networks SET networkName = '".$networkName."', networkDesc = '".$networkDesc."', networkStatus = '".$networkType."'  WHERE networkID = '".$networkID."' "); // UPDATE
	  	$gotoPage = "../network.php?n=".$networkID."&m=11";  // All good
	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
 	}
 } 
 
 function EditUserInfo(){

	$UserID 		= $_POST['userEditID'];
	$firstName	    = $_POST['firstName'];
	$lastName	    = $_POST['lastName'];
	$userFacebook   = $_POST['facebook'];
	$userTwitter    = $_POST['twitter'];
	$userPhone	    = $_POST['phone'];
	$userWebsite    = $_POST['website'];
	$userAbout	    = $_POST['userAbout'];
	$userPicture 	= $_POST['userPictureURL'];

	// But if there is a new picture
	if($_FILES["userPicture"]) {
		// File upload
	 	$allowedExts = array("jpg", "jpeg", "gif", "png");
	 	$uploadedName = urlencode($_FILES["userPicture"]["name"]); 
		$extension = end(explode(".", $uploadedName));
		$uploadLocation = "../uploads/userImg/"; 
		$randomName = rand(100000, 100000000000000000);
		$newName = $randomName . '_r' . '.' . $extension ; 
		
		$message = ""; 
		
		if ((($_FILES["userPicture"]["type"] == "image/gif")
		|| ($_FILES["userPicture"]["type"] == "image/jpeg")
		|| ($_FILES["userPicture"]["type"] == "image/png")
		|| ($_FILES["userPicture"]["type"] == "image/pjpeg"))
		&& ($_FILES["userPicture"]["size"] < 2000000)
		&& in_array($extension, $allowedExts))
		  {
			  if ($_FILES["userPicture"]["error"] > 0) {
			    $message =  "Return Code: " . $_FILES["userPicture"]["error"] . "<br>";
			  } else {
	
			        if (file_exists("../uploads/userImg/" . $newName))
				      {
				      $message = $_FILES["userPicture"]["name"] . " already exists. ";
				      }
				    else
				      {
				      	## Image resize block
				      	   $image = new SimpleImage();
						   $image->load($_FILES["userPicture"]["tmp_name"]);
						   $image->resizeToWidth(400);
						   $image->save($_FILES["userPicture"]["tmp_name"]);
						## End of image resize block

					      move_uploaded_file($_FILES["userPicture"]["tmp_name"], $uploadLocation . $newName );
				    
				    // If things go well Write to the database 
						$userPicture =  'uploads/userImg/'. $newName ;
				    }
			    }
		  } else {
		  		$message =  "You uploaded an invalid file please try again: Invalid file";
		  	  	$gotoPage = "../profile.php?u=".$UserID."&m=".$message;  // All good
		  	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
		  }	  
	}


	// If there is a password 
	if(strlen($_POST['password']) === 0){
			// Update without password
			$userUpdate = mysql_query("UPDATE users SET firstName = '".$firstName."', lastName = '".$lastName."', userAbout = '".$userAbout."', userPictureURL = '".$userPicture."', userFacebook = '".$userFacebook."', userTwitter = '".$userTwitter."', userPhone = '".$userPhone."', userWebsite = '".$userWebsite."' WHERE UserID = '".$UserID."' "); // UPDATE

	} else {
			$userPassword 	= md5($_POST['password']);
			// Update with password 	
			$userUpdate = mysql_query("UPDATE users SET firstName = '".$firstName."', lastName = '".$lastName."', userAbout = '".$userAbout."', userPictureURL = '".$userPicture."', userFacebook = '".$userFacebook."', userTwitter = '".$userTwitter."', userPhone = '".$userPhone."', userWebsite = '".$userWebsite."', password = '".$userPassword."' WHERE UserID = '".$UserID."' "); // UPDATE	
	}


  	$gotoPage = "../profile.php?u=".$UserID."&m=1";  // All good
	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 

}
 
 function AddUsersToNetwork() {
	 $items 	= $_POST['items'];
	 $networkID = intval($_POST['networkID']);
	 $networkRole = 'member'; 
	 $totalitems= count($items);
	 $exists = 0; 
	 $added = 0; 
	 
	 for($i=0; $i < $totalitems; $i++){
	 	$item = intval($items[$i]); 
	 	// Check if already there
	 	$query = mysql_query("SELECT * FROM networkUsers WHERE networkID = '".$networkID."' AND userID = '".$item."'");
		$results = mysql_fetch_array($query); 
		if($results){
			$exists++; 
		} else {
			$networkUsersInsert = mysql_query("INSERT INTO networkUsers (networkID, userID, networkUserRole) VALUES(".$networkID.", '".$item."', '".$networkRole."')"); 
			$added++; 
		}
	 }
 }


function AddCourse() { 
	$courseName  	=  $_POST['courseName'];
	$courseDesc  	=  $_POST['courseDescription'];
	$courseStart  	=  $_POST['courseStartDate'];
	$courseEnd  	=  $_POST['courseEndDate'];
	$courseURL  	=  $_POST['courseURL'];
	//$courseView	=  $_POST['viewOptions'];
	//$courseParticipate = $_POST['participateOptions'];
	
	$message = "";
	// But if there is a new picture
	if($_FILES["courseImage"]["size"] > 0 ) {
		// File upload
	 	$allowedExts = array("jpg", "jpeg", "gif", "png");
	 	$uploadedName = urlencode($_FILES["courseImage"]["name"]); 
		$extension = end(explode(".", $uploadedName));
		$uploadLocation = "../uploads/courseImg/"; 
		$randomName = rand(100000, 100000000000000000);
		$newName = $randomName . '_r' .'.' . $extension; 
		
		if ((($_FILES["courseImage"]["type"] == "image/gif")
		|| ($_FILES["courseImage"]["type"] == "image/jpeg")
		|| ($_FILES["courseImage"]["type"] == "image/png")
		|| ($_FILES["courseImage"]["type"] == "image/pjpeg"))
		&& ($_FILES["courseImage"]["size"] < 5242880)   // file size less than 5MB
		&& in_array($extension, $allowedExts))
		  {
			  if ($_FILES["courseImage"]["error"] > 0) {
			    $message =  "Return Code: " . $_FILES["courseImage"]["error"] . "<br>";
			  } else {
	
			        if (file_exists("../uploads/courseImg/" . $newName))
				      {
				      $message = $_FILES["courseImage"]["name"] . " already exists. ";
				      }
				    else
				      {
				    
					     ## Image resize block
				      	   $image = new SimpleImage();
						   $image->load($_FILES["courseImage"]["tmp_name"]);
						   $image->resizeToWidth(400);
						   $image->save($_FILES["courseImage"]["tmp_name"]);
						## End of image resize block
						
					      move_uploaded_file($_FILES["courseImage"]["tmp_name"], $uploadLocation . $newName);
				    
				    // If things go well Write to the database 
						$courseImage =  'uploads/courseImg/'. $newName;
				    }
			    }
		  } else {
		  		$message =  4;
		  	  	$gotoPage = "../addcourse.php?m=".$message;  
		  	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
		  }	  
	} else {
		$courseImage = $_POST['courseImageURL'];
	}
	
	// Add course to database
	$insertCourse = mysql_query("INSERT INTO courses (courseName, courseStartDate, courseEndDate, courseDescription, courseImage, courseURL) VALUES('".$courseName."', '".$courseStart."', '".$courseEnd."', '".$courseDesc."', '".$courseImage."', '".$courseURL."')"); 
	$courseID = mysql_insert_id(); 

	// Add Users to courses		
	if(isset($_POST['user'])){
		$user = $_POST['user'];
		$totalUser = count($user); 
		$i = 0; 
		while($i < $totalUser) {
			if($i%2 == 0){
					$CourseUserInsert = mysql_query("INSERT INTO courseRoles (courseID, userID, userRole) VALUES('".$courseID."', '".$user[$i]."', '".$user[$i+1]."')"); 
			}
			$i = $i+1; 
		}		
	}
	
	GenerateCodes($courseID);
	$message =  3;
	$gotoPage = "../course.php?c=".$courseID."&m=".$message;  // All good
	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
	 
}
 
function GenerateCodes($courseID){
	//generate view and register links
	$view = "";
	$reg = "";
	while($view==$reg){
		for($i=0;$i<8;$i++){
			$view.=mt_rand(0,9);
			$reg.=mt_rand(0,9);
		}
		$a = mysql_query("SELECT * FROM options WHERE optionsValue='$view'");
		if(count(mysql_fetch_array($a))!=0){
			$view = "";
			for($i=0;$i<8;$i++){
				$view.=mt_rand(0,9);
			}
			$a = mysql_query("SELECT * FROM options WHERE optionsValue='$view'");
		}
		$b = mysql_query("SELECT * FROM options WHERE optionsValue='$reg'");
		if(count(mysql_fetch_array($b))!=0){
			$reg = "";
			for($i=0;$i<8;$i++){
				$reg.=mt_rand(0,9);
			}
			$b = mysql_query("SELECT * FROM options WHERE optionsValue='$reg'");
		}
	}
	
	$a=mysql_query("INSERT INTO options (optionsType, optionsTypeID, optionsName, optionsValue, optionAttr) VALUES ('course', '$courseID', 'viewCode', '$view', '{\\\"active\\\":\\\"false\\\"}')");
	if($a==FALSE){
		exit("SQL syntax error 1");
	}
	$b=mysql_query("INSERT INTO options (optionsType, optionsTypeID, optionsName, optionsValue, optionAttr) VALUES ('course', '$courseID', 'registerCode', '$reg', '{\\\"active\\\":\\\"false\\\"}')");
 	if($b==FALSE){
 		exit("SQL syntax error 2");
 	}
} 
 
function EditCourse() { 
	$courseID		=  $_POST['courseID'];
	$courseName  	=  $_POST['courseName'];
	$courseDesc  	=  $_POST['courseDescription'];
	$courseStart  	=  $_POST['courseStartDate'];
	$courseEnd  	=  $_POST['courseEndDate'];
	$courseURL  	=  $_POST['courseURL'];
	
	$message = "";
	$courseImage = $_POST['courseImageURL'];
	
	// But if there is a new picture
	if($_FILES["editCourseImage"]["size"] > 0 ) {
		// File upload
	 	$allowedExts = array("jpg", "jpeg", "gif", "png");
		$extension = end(explode(".", $_FILES["editCourseImage"]["name"]));
		$uploadLocation = "../uploads/courseImg/"; 
		$randomName = rand(100000, 100000000000000000);
		$newName = $randomName . '.' . $extension; 
		
		if ((($_FILES["editCourseImage"]["type"] == "image/gif")
		|| ($_FILES["editCourseImage"]["type"] == "image/jpeg")
		|| ($_FILES["editCourseImage"]["type"] == "image/png")
		|| ($_FILES["editCourseImage"]["type"] == "image/pjpeg"))
		&& ($_FILES["editCourseImage"]["size"] < 5242880)	// File size less than 5MB
		&& in_array($extension, $allowedExts))
		  {
			  if ($_FILES["editCourseImage"]["error"] > 0) {
			    $message =  "Return Code: " . $_FILES["editCourseImage"]["error"] . "<br>";
			  } else {
	
			        if (file_exists("../uploads/courseImg/" . $newName))
				      {
				      $message = $_FILES["editCourseImage"]["name"] . " already exists. ";
				      }
				    else
				      {
				    move_uploaded_file($_FILES["editCourseImage"]["tmp_name"], $uploadLocation . $newName);
				    
				    // If things go well Write to the database 
						$courseImage =  'uploads/courseImg/'. $newName;
				    }
			    }
		  } else {
		  		$message =  "You uploaded an invalid file please try again. ";
		  	  	$gotoPage = "../editcourse.php?c=".$courseID."&m=".$message;  // All good
		  	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
		  	  	exit(); 
		  }	  
	} 
	
	// Add course to database
	$updateCourse = mysql_query("UPDATE courses SET courseName = '".$courseName."', courseStartDate = '".$courseStart."', courseEndDate = '".$courseEnd."', courseDescription = '".$courseDesc."', courseImage = '".$courseImage."', courseURL = '".$courseURL."'  WHERE courseID = '".$courseID."' "); // UPDATE


	// Change User Information		
	if(isset($_POST['user'])){
		$user  	=  $_POST['user'];
		$totalUser = count($user); 
		$i = 0; 
		while($i < $totalUser) {
			if($i%2 == 0){
					$query = mysql_query("SELECT * FROM courseRoles WHERE courseID = '".$courseID."' AND userID = '".$user[$i]."'");
					$results = mysql_fetch_array($query); 
					if($results){
							if($user[$i+1] == 'Delete'){
								$deleteQuery = mysql_query("DELETE FROM courseRoles WHERE courseID = '".$courseID."' AND userID = '".$user[$i]."'"); 
							} else {
								$CourseUserUpdate = mysql_query("UPDATE courseRoles SET userRole = '".$user[$i+1]."'  WHERE courseID = '".$courseID."' AND userID = '".$user[$i]."'"); // UPDATE							
							}
					} else {
						$CourseUserInsert = mysql_query("INSERT INTO courseRoles (courseID, userID, userRole) VALUES('".$courseID."', '".$user[$i]."', '".$user[$i+1]."')"); 
					}
			}
			$i = $i+1; 
		}
		
	}
	
  		$message =  10;
	  	$gotoPage = "../course.php?c=".$courseID."&m=".$message;  // All good
	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 

 
 }
 
function AddDiscussion(){
	// get all elements from post
		$dTitle		=  $_POST['discussionQuestion'] ;
		$dPrompt	=  $_POST['discussionPrompt']	 ;
		$dStartDate	=  $_POST['discussionStartDate'] ." " .$_POST['sDateTime'] . ":00:00"; 
		$dOpenDate	=  $_POST['discussionOpenDate']  ." " .$_POST['oDateTime'] . ":00:00";
		$dEndDate	=  $_POST['discussionEndDate']   ." " .$_POST['eDateTime'] . ":00:00"; 
		$courseID	=  $_POST['courseID']; 
		$networkID	=  $_POST['networkID'];
		
		// Add row to discussions table
		$discInsert = mysql_query("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES('".$dTitle."', '".$dPrompt."', '".$dStartDate."', '".$dOpenDate."', '".$dEndDate."')"); 
		$discID = mysql_insert_id(); 	
	
	// Add row to coursediscussions table
		$courses	=  $_POST['course'] ;
		$totalCourses = count($courses); 
		$i = 0; 
		while($i < $totalCourses) {
				$discCourseInsert = mysql_query("INSERT INTO courseDiscussions (courseID, discussionID) VALUES('".$courses[$i]."', '".$discID."')"); 
			$i = $i+1; 
		}
  		$message =  'd';
	  	$gotoPage = "../course.php?c=".$courseID."&n=".$networkID."&m=".$message;  // All good
	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
				
}

function EditDiscussion(){
	// get all elements from post
		$dTitle		=  $_POST['discussionQuestion'] ;
		$dPrompt	=  $_POST['discussionPrompt']	 ;
		$dStartDate	=  $_POST['discussionStartDate'] ." " .$_POST['sDateTime'] . ":00:00"; 
		$dOpenDate	=  $_POST['discussionOpenDate']  ." " .$_POST['oDateTime'] . ":00:00";
		$dEndDate	=  $_POST['discussionEndDate']   ." " .$_POST['eDateTime'] . ":00:00"; 
		$discID		=  $_POST['discID'];
		$courseID	= $_POST['courseID']; 
		$networkID	= $_POST['networkID'];
		// Add row to discussions table
		$discInsert = mysql_query("UPDATE discussions SET dTitle = '".$dTitle."', dPrompt = '".$dPrompt."', dStartDate = '".$dStartDate."', dOpenDate = '".$dOpenDate."', dEndDate = '".$dEndDate."' WHERE dID = '".$discID."'"); 
	
	// Delete all rows from coursediscussion table that has this discussion
	
	
	// Add rows to coursediscussions table
		$courses	=  $_POST['course'] ;
		$totalCourses = count($courses); 
		$i = 0; 

		while($i < $totalCourses) {
			if($i%2 == 0){
				$b = $i+1; 
				if($courses[$b] == 'yes'){
					$discCourseDelete = mysql_query("DELETE FROM courseDiscussions WHERE courseID = '".$courses[$i]."' AND discussionID = '".$discID."' "); 
				} elseif ($courses[$b] == 'add') {
					$discCourseInsert = mysql_query("INSERT INTO courseDiscussions (courseID, discussionID) VALUES('".$courses[$i]."', '".$discID."')"); 
				}
			}
			$i = $i+1; 
		}

		
 		$message = '2' ;
	  	$gotoPage = "../course.php?c=".$courseID;  // All good
	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 

}

function GetData(){
		$discID	=  $_POST['discID'];
		$courses = array();
		$users = array();
		$cID;
		
		// Get Discussion information
 		$discussionData = mysql_query("SELECT * FROM `discussions` WHERE dID = '".$discID."' ");  // Get everything 		   				
		$discussion =  mysql_fetch_assoc($discussionData);

		// Get courses for this discussion
		$courseData = mysql_query("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = '".$discID."'");
		while($r = mysql_fetch_assoc($courseData)) 
		{					
			$courses[] = $r; 								// Put mysql results into an array 
			$cID = $r['courseID']; 							// Get course ID for each course. We need this to get to all the other information about users, and networks. 
		}
		// Get Users Who posted in the discussion whether they are in the course or not
		$userData = mysql_query("SELECT DISTINCT postAuthorId FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = '".$discID."'");
		while($s = mysql_fetch_assoc($userData)) 
				{			
					$singleUser = mysql_query("SELECT * FROM users WHERE UserID = '".$s['postAuthorId']."'");
					$oneuser = mysql_fetch_array($singleUser); 		
					$users[] = $oneuser;
				}
		// Get all the users in the course but add them to data if they are not already there		

		$userData2 = mysql_query("SELECT * FROM courseRoles INNER JOIN users ON courseRoles.userID = users.UserID WHERE courseRoles.courseID = '".$cID."' ");
		while($t = mysql_fetch_assoc($userData2)) 
				{					
					$users[] = $t;
				}
		// Get Networks
		/*$networksData = mysql_query("SELECT * FROM networkCourses INNER JOIN networks ON networkCourses.networkID = networks.networkID WHERE networkCourses.courseID = '".$cID."'");
		while($u = mysql_fetch_assoc($networksData)) 
				{					
					$networks[] = $u;
				}	 
		}*/
		
		// Get posts within this discussion as well as a list of users who posted so far. 
		$data = array();
		$postData = mysql_query("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = '".$discID."'");
		$num_rows = mysql_num_rows($postData);
		if($num_rows > 0){
			$posts = array(); 	
			$i = 0;
			while($row = mysql_fetch_array($postData)) :
				array_push($posts, $row);  // Add to the array of posts
				$i++;
			endwhile;
			$data['posts'] =  $posts;		 
		} 
		$data['discussion'] = $discussion;	
		$data['courses'] = $courses; 
		$data['users'] = $users; 		 
		echo json_encode($data);			// Covert data into a json file.
}

function JoinNetwork() {
		$networkCode	=  intval($_POST['networkCode']);
		$userID			=  $_POST['userID'];

		$networkData = mysql_query("SELECT * FROM networks  WHERE networkCode = ".$networkCode."");
		$results = mysql_fetch_array($networkData); 
		$num_rows = mysql_num_rows($networkData);
		if($num_rows > 0){
			$networkID = $results['networkID']; 
				$networkUserData = mysql_query("SELECT * FROM networkUsers  WHERE networkID = '".$networkID."' AND userID = '".$userID."' ");
				$num_rows2 = mysql_num_rows($networkUserData);
				if($num_rows2 > 0){
					$message =  "You already seem to be in this network.";
				} else {
					$networkUsersInsert = mysql_query("INSERT INTO networkUsers (networkID, userID, networkUserRole) VALUES(".$networkID.", '".$userID."', 'member')"); 
					  	$message = intval($results['networkID']); 
					}
		} else {
			$message = "Sorry this code is not in our system."; 
		}
		echo json_encode($message); 
}

function AddPost()
{
			// Save post first
			$post = $_POST['post'];
			
			$postFromId		= 	$post['postFromId'];
			$postAuthorId	= 	$post['postAuthorId'];
			$postMessage	= 	$post['postMessage'];
			$postType		= 	$post['postType'];
			$postSelection	= 	$post['postSelection'];			
			$postMedia		= 	$post['postMedia'];
			$postMediaType  = 	$post['postMediaType'];
			$postContext	= 	$post['postContext'];
			
			$addPostQuery = mysql_query("INSERT INTO posts (postFromId, postAuthorId, postMessage, postType, postSelection, postMedia, postMediaType, postContext) VALUES('".$postFromId."', '".$postAuthorId."', '".$postMessage."','".$postType."','".$postSelection."','".$postMedia."','".$postMediaType."','".$postContext."')"); 
			
			$postID = mysql_insert_id();
			
			echo json_encode($postID);
			
			// Then save the post id to the discussion
			$currentDiscussion =   $_POST['currentDiscussion'];
			 
			$addPosttoDiscussion = mysql_query("INSERT INTO discussionPosts (discussionID, postID) VALUES(".$currentDiscussion.", '".$postID."')");  			 		

}

function EditPost()
{
			// Save post first
			$post = $_POST['post'];
			
			$postID			= 	$post['postID'];
			$postFromId		= 	$post['postFromId'];
			$postAuthorId	= 	$post['postAuthorId'];
			$postMessage	= 	$post['postMessage'];
			$postType		= 	$post['postType'];
			$postSelection	= 	$post['postSelection'];			
			$postMedia		= 	$post['postMedia'];
			$postMediaType  = 	$post['postMediaType'];
			$postContext	= 	$post['postContext'];
																		
			$editPostQuery = mysql_query("UPDATE posts SET  postFromId = '".$postFromId."', postAuthorId = '".$postAuthorId."', postMessage = '".$postMessage."', postType  = '".$postType."', postSelection  = '".$postSelection."', postMedia  = '".$postMedia."', postMediaType = '".$postMediaType."', postContext  = '".$postContext."'  WHERE postID  = '".$postID."' "); 
									
}



function CheckNewPosts()
{
	// Checks to see if there are new posts in this discussion, returns number
			$currentDiscussion =   $_POST['currentDiscussion'];
			$currentPosts =   $_POST['currentPosts'];
	// Get posts within this discussion
			$postData = mysql_query("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = '".$currentDiscussion."'");
			$num_rows = mysql_num_rows($postData);
			$posts = array(); 	
			if($num_rows > 0){
				$i = 0;
				while($row = mysql_fetch_array($postData)) :
					array_push($posts, $row);
					$i++;
				endwhile;
			} 
			$numNew = count($posts);
			$numOld = count($currentPosts);
			
			$newposts = array(); 
			if($numNew > $numOld){
				$newposts['result'] = $numNew-$numOld;
			} else {
				$newposts['result'] = 0;
			}
    	echo json_encode($newposts);
}


function AddLog()
{
			$log = $_POST['log'];
				$logUserID	= $log['logUserID'];
				$logPageType= $log['logPageType'];
				$logPageID	= $log['logPageID'];
				$logAction	= $log['logAction'];
				$logActionID= $log['logActionID'];
				$logMessage	= $log['logMessage'];
				$logUserAgent	= $log['logUserAgent'];
				$logSessionID   = $log['logSessionID']; 
																
			$addLogQuery = mysql_query("INSERT INTO logs (logUserID, logPageType,logPageID, logAction, logActionID, logMessage, logUserAgent, logSessionID) VALUES('$logUserID', '$logPageType', '$logPageID', '$logAction', '$logActionID', '$logMessage', '$logUserAgent', '$logSessionID')"); 
			if($addLogQuery){
				echo 1; 
			} else {
				echo 0; 
			}						
				
}
			
function SaveOptions()
{
			// Get option data
			$optionsType = $_POST['optionsType'];
			$optionsTypeID = $_POST['optionsTypeID'];
			$options =  $_POST['optionsData'] ;			
			$totalOptions = count($options); 

			for($i = 0; $i < $totalOptions; $i++) {   // Loop through individual options
				if(empty($options[$i]['optionsAttr'])){
					$optionsAttr = ' '; 
				} else {
					if($options[$i]['optionsName'] == 'viewCode' || $options[$i]['optionsName'] == 'registerCode'){
						if($options[$i]['optionsAttr'] == 'On'){
							$optionsAttr = '{ "active" : "true"}'; 
						} else {
							$optionsAttr = '{ "active" : "false"}'; 
						}
					} else {
						$optionsAttr = $options[$i]['optionsAttr']; 
					}
				}
				
				// Check if that option exists in the database									
				$checkoption = mysql_query("SELECT * FROM options WHERE optionsType = '".$optionsType."' AND optionsTypeID = '".$optionsTypeID."' AND optionsName = '".$options[$i]['optionsName']."' "); 

				$num_rows = mysql_num_rows($checkoption);
				if($num_rows == 1){     // if options does not exist create the option row
					$update = mysql_query("UPDATE options SET  optionsValue  = '".$options[$i]['optionsValue']."', optionAttr  = '".$optionsAttr."'  WHERE optionsType = '".$optionsType."' AND optionsTypeID = '".$optionsTypeID."' AND optionsName = '".$options[$i]['optionsName']."' "); 				
				} else if ($num_rows == 0){  // if option exists edit the option
					$add = mysql_query("INSERT INTO options (optionsType, optionsTypeID,optionsName, optionsValue, optionAttr) VALUES('".$optionsType."', '".$optionsTypeID."', '".$options[$i]['optionsName']."','".$options[$i]['optionsValue']."','".$optionsAttr."')"); 
				}

			}
}