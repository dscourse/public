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
include_once "config.php"; 
include_once "dscourse.class.php"; 
include "simpleImage.class.php"; 
$pdo->query("SET time_zone = '+00:00'"); 

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
	if ($action == 'mention'){
		Mention();
	}
	if ($action == 'delete'){
		Delete();
	}
	if ($action == 'archive'){
		Archive();
	}
}
 ///Obsolete???           
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
 	global $pdo;

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
		  } /*else {
		  		$message =  "10";
		  	  	$gotoPage = "../profile.php?u=".$UserID."&m=".$message;  // All good
		  	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
		  }	 */ 
	}
	$query ="UPDATE users SET firstName = :firstName, lastName = :lastName, userAbout = :userAbout, userPictureURL = :userPicture, userFacebook = :userFacebook, userTwitter = :userTwitter, userPhone = :userPhone, userWebsite = :userWebsite WHERE UserID = :UserID";
	$params = array(':firstName'=>$firstName,':lastName'=>$lastName,':userAbout'=>$userAbout,':userPicture'=>$userPicture,':userFacebook'=>$userFacebook,':userTwitter'=>$userTwitter,':userPhone'=>$userPhone,':userWebsite'=>$userWebsite,':UserID'=>$UserID);
	$stmt = $pdo->prepare($query); // UPDATE
	// If there is a password 
	if(strlen($_POST['password']) === 0){
			// Update without password
			//$userUpdate = mysql_query("UPDATE users SET firstName = '".$firstName."', lastName = '".$lastName."', userAbout = '".$userAbout."', userPictureURL = '".$userPicture."', userFacebook = '".$userFacebook."', userTwitter = '".$userTwitter."', userPhone = '".$userPhone."', userWebsite = '".$userWebsite."' WHERE UserID = '".$UserID."' "); // UPDATE
			$stmt->execute($params);
	} else {
			$userPassword 	= md5($_POST['password']);
			$params[':password'] = $userPassword;
			// Update with password 
			$parts = explode('WHERE', $query);
			$parts[0].= "password = :password ";
			$query = join('WHERE', $parts);	
			//$userUpdate = mysql_query("UPDATE users SET firstName = '".$firstName."', lastName = '".$lastName."', userAbout = '".$userAbout."', userPictureURL = '".$userPicture."', userFacebook = '".$userFacebook."', userTwitter = '".$userTwitter."', userPhone = '".$userPhone."', userWebsite = '".$userWebsite."', password = '".$userPassword."' WHERE UserID = '".$UserID."' "); // UPDATE	
			$stmt = $pdo->prepare($query);
			$stmt->execute($params);
	}
	
	$notifications = array("comment", "agree", "disagree","clarify", "offTopic", "mention");
	$stmt = $pdo->prepare("SELECT optionsID FROM options WHERE optionsType='user' AND optionsTypeID = :UserID AND optionsName = :option");
	$up = $pdo->prepare("UPDATE options SET optionsValue = :val WHERE optionsID = :oID");
	$in = $pdo->prepare("INSERT INTO options(optionsType, optionsTypeID, optionsName, optionsValue) VALUES('user', :UserID, :option, :val)");
	foreach($notifications as $param){
		$params = array(':UserID'=>$UserID,':option'=>"notify_on_$param");
		$val = 0;
		if(isset($_REQUEST[$param])){
			$val = $_REQUEST[$param];	
		}
		//check for old entries
		//$old = mysql_query("SELECT optionsID FROM options WHERE optionsType='user' AND optionsTypeID = $UserID AND optionsName = 'notify_on_$param'");
		$stmt->execute($params);
		$res = $stmt->fetch();
		if(!empty($res)){
			$oID = $res['optionsID'];
			$up->execute(array(':val'=>$val,':oID'=>$oID));	
			//mysql_query("UPDATE options SET optionsValue = $val WHERE optionsID = $oID");		
		}
		else{
			$in->execute($params + array(':val'=>$val));
			//mysql_query("INSERT INTO options(optionsType, optionsTypeID, optionsName, optionsValue) VALUES('user', $UserID, 'notify_on_$param', $val)");
		}
	}
  	$gotoPage = "../profile.php?u=".$UserID."&m=1";  // All good
	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
}
 ///Obsolete???
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
	global $pdo;
	 
	$courseName  	=  $_POST['courseName'];
	$courseDesc  	=  $_POST['courseDescription'];
	$courseStart  	=  $_POST['courseStartDate'];
	$courseEnd  	=  $_POST['courseEndDate'];
	$courseURL  	=  $_POST['courseURL'];
	$courseImage = '';
	
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
		  	  	header("Location: ". $gotoPage);  // Take the user to the page according to the result. 
		  }	  
	} else {
		$courseImage = $_POST['courseImageURL'];
	}
	
	// Add course to database
	$stmt = $pdo->prepare("INSERT INTO courses (courseName, courseStartDate, courseEndDate, courseDescription, courseImage, courseURL) VALUES(:courseName, :courseStart, :courseEnd, :courseDesc, :courseImage, :courseURL)");
	$stmt->execute(array(':courseName'=>$courseName, ':courseStart'=>$courseStart, ':courseEnd'=>$courseEnd, ':courseDesc'=>$courseDesc, ':courseImage'=>$courseImage, ':courseURL'=>$courseURL));
	$courseID = $pdo->lastInsertId();
	
	// Add Users to courses		
	if(isset($_POST['user'])){
		$user = $_POST['user'];
		$totalUser = count($user); 
		$i = 0; 
		$stmt = $pdo->prepare("INSERT INTO courseRoles (courseID, userID, userRole) VALUES(:cID, :uID, :role)");
		$params = array(':cID'=>$courseID);
		while($i < $totalUser) {
			if($i%2 == 0){
				$stmt->execute($params+array(':uID'=>$user[$i],':role'=>$user[$i+1]));
				//$CourseUserInsert = mysql_query("INSERT INTO courseRoles (courseID, userID, userRole) VALUES('".$courseID."', '".$user[$i]."', '".$user[$i+1]."')"); 
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
	global $pdo;
	//generate view and register links
	$view = "";
	$reg = "";
	
	$v = $pdo->prepare("SELECT * FROM options WHERE optionsValue=:view");
	$r = $pdo->prepare("SELECT * FROM options WHERE optionsValue=:reg");
	while($view==$reg){
		for($i=0;$i<8;$i++){
			$view.=mt_rand(0,9);
			$reg.=mt_rand(0,9);
		}
		//$a = mysql_query("SELECT * FROM options WHERE optionsValue='$view'");
		$v->execute(array(':view'=>$view));
		if(count($v->fetch())!=0){
			$view = "";
			for($i=0;$i<8;$i++){
				$view.=mt_rand(0,9);
			}
			$v->execute(array(':view'=>$view));
		}
		//$b = mysql_query("SELECT * FROM options WHERE optionsValue='$reg'");
		$r->execute(array(':reg'=>$reg));
		if(count($r->fetch())!=0){
			$reg = "";
			for($i=0;$i<8;$i++){
				$reg.=mt_rand(0,9);
			}
			//$b = mysql_query("SELECT * FROM options WHERE optionsValue='$reg'");
			$r->execute(array(':reg'=>$reg));
		}
	}
	$stmt = $pdo->prepare("INSERT INTO options (optionsType, optionsTypeID, optionsName, optionsValue, optionAttr) VALUES ('course', :courseID, :type, :view, '{\\\"active\\\":\\\"false\\\"}')");
	$stmt->execute(array(':courseID'=>$courseID,':view'=>$view, ':type'=>'viewCode'));
	//$a=mysql_query("INSERT INTO options (optionsType, optionsTypeID, optionsName, optionsValue, optionAttr) VALUES ('course', '$courseID', 'viewCode', '$view', '{\\\"active\\\":\\\"false\\\"}')");
	$stmt->execute(array(':courseID'=>$courseID,':view'=>$reg, ':type'=>'registerCode'));
	//$b=mysql_query("INSERT INTO options (optionsType, optionsTypeID, optionsName, optionsValue, optionAttr) VALUES ('course', '$courseID', 'registerCode', '$reg', '{\\\"active\\\":\\\"false\\\"}')");
} 
 
function EditCourse() {
	global $pdo;	
	 
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
	$stmt = $pdo->prepare("UPDATE courses SET courseName = :courseName, courseStartDate = :courseStart, courseEnd = :courseEnd, courseDescription = :courseDesc, courseImage = :courseImage, courseURL = :courseURL  WHERE courseID = :courseID");
	$params = array(':courseName'=>$courseName,':courseStart'=>$courseStart,':courseEnd'=>$courseEnd, ':courseDesc'=>$courseDesc,':courseImage'=>$courseImage, ':courseURL'=>$courseURL,':courseID'=>$courseID);
	$stmt->execute($params);	// Change User Information		
	if(isset($_POST['user'])){
		$user  	=  $_POST['user'];
		$totalUser = count($user); 
		$i = 0; 
		$role = $pdo->prepare("SELECT * FROM courseRoles WHERE courseID = :courseID AND userID = :userID");
		$delete = $pdo->prepare("DELETE FROM courseRoles WHERE courseID = :courseID AND userID = :userID");
		$update = $pdo->prepare("UPDATE courseRoles SET userRole = :role  WHERE courseID = :courseID AND userID = :userID");
		while($i < $totalUser) {
			if($i%2 == 0){
					$params = array(':courseID'=>$courseID,':userID'=>$user[$i]);
					$role->execute($params);
					//$results = mysql_fetch_array($query);
					$results = $role->fetch();
					if(!empty($results)){
							if($user[$i+1] == 'Delete'){
								$delete->execute($params);
							} else {
								$params[':role'] = $user[$i+1];
								$update->execute($params);
							}
					} else {
						$params[':role'] = $user[$i+1];
						$stmt = $pdo->prepare("INSERT INTO courseRoles (courseID, userID, userRole) VALUES(:courseID, :userID, :role)");
						$stmt->execute($params);
					}
			}
			$i = $i+1; 
		}
	}
	
	$message =  10;
	$gotoPage = "../course.php?c=".$courseID."&m=".$message;  // All good
	header("Location: ". $gotoPage);  // Take the user to the page according to the result. 
 }
 
function AddDiscussion(){
	global $pdo;
	// get all elements from post
		$dTitle		=  $_POST['discussionQuestion'] ;
		$dPrompt	=  $_POST['discussionPrompt']	 ;
		$dStartDate	=  $_POST['discussionStartDate'] ." " .$_POST['sDateTime'] . ":00:00"; 
		$dOpenDate	=  $_POST['discussionOpenDate']  ." " .$_POST['oDateTime'] . ":00:00";
		$dEndDate	=  $_POST['discussionEndDate']   ." " .$_POST['eDateTime'] . ":00:00"; 
		$courseID	=  $_POST['courseID']; 
		//$networkID	=  $_POST['networkID'];
		
		$stmt = $pdo->prepare("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES(:dTitle, :dPrompt, :dStartDate, :dOpenDate, :dEndDate)");
		$params = array(':dTitle'=>$dTitle, ':dPrompt'=>$dPrompt, ':dStartDate'=>$dStartDate, ':dOpenDate'=>$dOpenDate, ':dEndDate'=>$dEndDate);
		$stmt->execute($params);
		$discID = $pdo->lastInsertId();
		/*
		// Add row to discussions table
		$discInsert = mysql_query("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES('".$dTitle."', '".$dPrompt."', '".$dStartDate."', '".$dOpenDate."', '".$dEndDate."')"); 
		$discID = mysql_insert_id(); 	
		*/
		// Add row to coursediscussions table
		$courses	=  $_POST['course'] ;
		$totalCourses = count($courses); 
		$i = 0; 
		$stmt = $pdo->prepare("INSERT INTO courseDiscussions (courseID, discussionID) VALUES(:courseID, :discID)");
		while($i < $totalCourses) {
			$stmt->execute(array(':courseID'=>$courses[$i], ':discID'=>$discID));
			//$discCourseInsert = mysql_query("INSERT INTO courseDiscussions (courseID, discussionID) VALUES('".$courses[$i]."', '".$discID."')"); 
			$i = $i+1; 
		}
  		$message =  'd';
	  	$gotoPage = "../course.php?c=".$courseID."&n=".$networkID."&m=".$message;  // All good
	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 
}

function EditDiscussion(){
	global $pdo;
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
		$stmt = $pdo->prepare("UPDATE discussions SET dTitle = :dTitle, dPrompt = :dPrompt, dStartDate = :dStartDate, dOpenDate = :dOpenDate, dEndDate = :dEndDate WHERE dID = :discID"); 
		$stmt->execute(array(':dTitle'=>$dTitle,':dPrompt'=>$dPrompt,':dStartDate'=>$dStartDate,':dEndDate'=>$dEndDate, ':discID'=>$discID));
		//$discInsert = mysql_query("UPDATE discussions SET dTitle = '".$dTitle."', dPrompt = '".$dPrompt."', dStartDate = '".$dStartDate."', dOpenDate = '".$dOpenDate."', dEndDate = '".$dEndDate."' WHERE dID = '".$discID."'"); 
	
	// Delete all rows from coursediscussion table that has this discussion
	
	
	// Add rows to coursediscussions table
		$courses	=  $_POST['course'] ;
		$totalCourses = count($courses); 
		$i = 0; 

		while($i < $totalCourses) {
			if($i%2 == 0){
				$b = $i+1; 
				$params = array(':courseID'=>$courses[$i], ':discID'=>$discID);
				if($courses[$b] == 'yes'){
					$stmt = $pdo->prepare("DELETE FROM courseDiscussions WHERE courseID = :courseID AND discussionID = :discID");
					$stmt->exectue($params);
					//$discCourseDelete = mysql_query("DELETE FROM courseDiscussions WHERE courseID = '".$courses[$i]."' AND discussionID = '".$discID."' "); 
				} elseif ($courses[$b] == 'add') {
					$stmt = $pdo->prepare("INSERT INTO courseDiscussions (courseID, discussionID) VALUES(:courseID, :discID)");
					$stmt->execute($params);
					//$discCourseInsert = mysql_query("INSERT INTO courseDiscussions (courseID, discussionID) VALUES('".$courses[$i]."', '".$discID."')"); 
				}
			}
			$i = $i+1; 
		}

		
 		$message = '2' ;
	  	$gotoPage = "../course.php?c=".$courseID;  // All good
	  	header("Location: ". $gotoPage);  // Take the user to the page according to te result. 

}

function GetData(){
	global $pdo;
		$discID	=  $_POST['discID'];
		$courses = array();
		$users = array();
		$cID;
		$params = array(':discID'=>$discID);
		$stmt = $pdo->prepare('SELECT * FROM discussions WHERE dID = :discID');
		$stmt->execute($params);
		$discussion =  $stmt->fetch();
		
		/*
		// Get Discussion information
 		$discussionData = mysql_query("SELECT * FROM `discussions` WHERE dID = '".$discID."' ");  // Get everything 		   				
		$discussion =  mysql_fetch_assoc($discussionData);
		*/	
		
		$stmt = $pdo->prepare("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = :discID");
		$stmt->execute($params);
		while($row = $stmt->fetch()){
			$courses[] = $row;
			$cID = $row['courseID'];
		}
		
		/*
		// Get courses for this discussion
		$courseData = mysql_query("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = '".$discID."'");
		while($r = mysql_fetch_assoc($courseData)) 
		{					
			$courses[] = $r; 								// Put mysql results into an array 
			$cID = $r['courseID']; 							// Get course ID for each course. We need this to get to all the other information about users, and networks. 
		}
		 */
		$stmt = $pdo->prepare("SELECT DISTINCT postAuthorId FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = :discID");
		$stmt->execute($params);
		$inner = $pdo->prepare("SELECT * FROM users WHERE UserID = :id");
		while($s = $stmt->fetch()){
			$inner->execute(array(':id'=>$s['postAuthorId']));
			//$oneuser = mysql_fetch_array($singleUser);\
			$u = $inner->fetch(); 		
			$users[] = $u;
		} 
		
		/*
		// Get Users Who posted in the discussion whether they are in the course or not
		$userData = mysql_query("SELECT DISTINCT postAuthorId FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = '".$discID."'");
		while($s = mysql_fetch_assoc($userData)) 
				{			
					$singleUser = mysql_query("SELECT * FROM users WHERE UserID = '".$s['postAuthorId']."'");
					$oneuser = mysql_fetch_array($singleUser); 		
					$users[] = $oneuser;
				}
		 */
		  
		$params = array(':courseID'=>$cID);
		$stmt = $pdo->prepare("SELECT * FROM courseRoles INNER JOIN users ON courseRoles.userID = users.UserID WHERE courseRoles.courseID = :courseID");  
		$stmt->execute($params);
		while($t = $stmt->fetch()){
			$users[] = $t;	
		}
		
		/*
		// Get all the users in the course but add them to data if they are not already there		
		$userData2 = mysql_query("SELECT * FROM courseRoles INNER JOIN users ON courseRoles.userID = users.UserID WHERE courseRoles.courseID = '".$cID."' ");
		while($t = mysql_fetch_assoc($userData2)) 
				{					
					$users[] = $t;
				}
		 */
		// Get Networks
		/*$networksData = mysql_query("SELECT * FROM networkCourses INNER JOIN networks ON networkCourses.networkID = networks.networkID WHERE networkCourses.courseID = '".$cID."'");
		while($u = mysql_fetch_assoc($networksData)) 
				{					
					$networks[] = $u;
				}	 
		}*/
		
		$params = array(':discID'=>$discID);
		$stmt = $pdo->prepare("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = :discID AND discussionPosts.postStatus != 'deleted' ");
		$stmt->execute($params);
		if($stmt->rowCount() > 0){
			$posts = array(); 	
			$i = 0;
			while($row = $stmt->fetch()){
				array_push($posts, $row);  // Add to the array of posts
				$i++;
			}
			$data['posts'] =  $posts;		
		}
		/*
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
		*/
		$data['discussion'] = $discussion;	
		$data['courses'] = $courses; 
		$data['users'] = $users; 		
		echo json_encode($data);			// Covert data into a json file.
}

///Obsolete???
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
	global $pdo;
		ignore_user_abort(true);
		set_time_limit(0);

		ob_start();
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
			
			$stmt = $pdo->prepare("INSERT INTO posts (postFromId, postAuthorId, postMessage, postType, postSelection, postMedia, postMediaType, postContext) VALUES(:postFromId, :postAuthorId, :postMessage, :postType, :postSelection,:postMedia,:postMediaType ,:postContext)"); 
			$stmt->execute(array(':postFromId'=>$postFromId,':postAuthorId'=>$postAuthorId, ':postMessage'=>$postMessage, ':postType'=>$postType, ':postSelection'=>$postSelection, ':postMedia'=>$postMedia, ':postMediaType'=>$postMediaType,':postContext'=>$postContext));
			//$addPostQuery = mysql_query("INSERT INTO posts (postFromId, postAuthorId, postMessage, postType, postSelection, postMedia, postMediaType, postContext) VALUES('".$postFromId."', '".$postAuthorId."', '".$postMessage."','".$postType."','".$postSelection."','".$postMedia."','".$postMediaType."','".$postContext."')"); 
			//$postID = mysql_insert_id();
			$postID = $pdo->lastInsertId();
			
			$res = json_encode($postID);
			echo $res;
			header('Connection: close');
			header('Content-Length: '.ob_get_length());
			ob_end_flush();
			ob_flush();
			flush();
			
			// Then save the post id to the discussion
			$currentDiscussion =   $_POST['currentDiscussion'];
			$addPost = $pdo->prepare("INSERT INTO discussionPosts (discussionID, postID) VALUES(:discID, :postID)");  
			$addPost->execute(array(':discID'=>$currentDiscussion, ':postID'=>$postID));
			//$addPosttoDiscussion = mysql_query("INSERT INTO discussionPosts (discussionID, postID) VALUES(".$currentDiscussion.", '".$postID."')");  			 		
			
			//check notifications
			//need postFrom, postType, postAuthor
			$author = $pdo->prepare("SELECT postAuthorId FROM posts WHERE postID = :postFromId");
			$author->execute(array(':postFromId'=>$postFromId));
			//$q= "SELECT postAuthorId FROM posts WHERE postID = $postFromId";
			//$fromAuthor = mysql_query("SELECT postAuthorId FROM posts WHERE postID = $postFromId");
			//$fromAuthor = mysql_fetch_assoc($fromAuthor);
			$fromAuthor = $author->fetch();
			$fromAuthor = $fromAuthor['postAuthorId'];

			$options = $pdo->prepare("SELECT * FROM options WHERE optionsType = 'user' AND optionsTypeID = :fromAuthor AND optionsName = :notifyOn");			
			$options->execute(array(':fromAuthor'=>$fromAuthor, ':notifyOn'=>"notify_on_$postType"));
			//$q = "SELECT * FROM options WHERE optionsType = 'user' AND optionsTypeID = $fromAuthor AND optionsName = 'notify_on_$postType'";
			//$res = mysql_query($q);
			//while($row = mysql_fetch_assoc($res)){
			$linkInfo = $pdo->prepare("SELECT courseDiscussions.discussionID, courseDiscussions.courseID FROM discussionPosts INNER JOIN courseDiscussions on discussionPosts.discussionID = courseDiscussions.discussionID WHERE discussionPosts.discussionID in (SELECT discussionID FROM discussionPosts WHERE postID = :postID) LIMIT 1");		
			$userFrom = $pdo->prepare("SELECT username, firstName FROM users WHERE userID = :postAuthorId");		
			$fromInfo = $pdo->prepare("SELECT username, firstName FROM users WHERE userID = :fromAuthor");
			while($row = $options->fetch()){
				if($row['optionsValue']){
					$act = "";
					$generic = " a post";
					switch($postType){
						case 'comment':
							$act = "commented on".$generic;
						break;
						case 'agree':
							$act = "agreed with".$generic;
						break;
						case 'disagree':
							$act = "disagreed with".$generic;
						break;
						case 'clarify':
							$act = "asked you to clarify".$generic;
						break;
						case 'offTopic':
							$act = "marked ".$generic." as off topic";
						break;
					}
					$truncated = myTruncate($postMessage, 100, $break = " ", $pad = "..."); 
					$link= "";
					if(isset($_SERVER["HTTP_HOST"])){
						$host = $_SERVER["HTTP_HOST"];
						$path = '/discussion.php';
						$query = "?";
						$linkInfo->execute(array(':postID'=>$postID));
						//$d = mysql_query("SELECT courseDiscussions.discussionID, courseDiscussions.courseID FROM discussionPosts INNER JOIN courseDiscussions on discussionPosts.discussionID = courseDiscussions.discussionID WHERE discussionPosts.discussionID in (SELECT discussionID FROM discussionPosts WHERE postID = $postID) LIMIT 1");
						//$info = mysql_fetch_assoc($d);
						$info = $linkInfo->fetch();
						$dID = $info['discussionID'];
						$cID = $info['courseID'];
						$query.="d=$dID&c=$cID&p=$postID";
						$link = 'http://'.$host.$path.$query;
					}
					
					//$userFrom = mysql_query("SELECT username, firstName FROM users WHERE userID = $postAuthorId");	
					//$ufrom = mysql_fetch_assoc($userFrom);
					$userFrom->execute(array(':postAuthorId'=>$postAuthorId));
					$uFrom = $userFrom->fetch();
					$from  = $uFrom['firstName'];
					$fromUsername = $uFrom['username'];
					
					//$e = mysql_query("SELECT username, firstName FROM users WHERE userID = $fromAuthor");	
					//$user = mysql_fetch_assoc($e);
					$fromInfo->execute(array(':fromAuthor'=>$fromAuthor));
					$user = $fromInfo->fetch();
					$email = $user['username'];
					$name = $user['firstName'];

					require_once '../mail/class.phpmailer.php';
					require_once '../mail/mail_init.php';
					$mail = new PHPMailer();
					$mail = mail_init($mail);
					//micro-templating
					$body = file_get_contents('../mail/templates/notify.html');
					$head = "Hi $name, <br /> $from($fromUsername) $act in one of your discussions:";
					$body = str_replace('%head%',$head,$body);
					$body = str_replace('%msg%', $truncated, $body);
					$body = str_replace('%link%', $link, $body);
					$mail->MsgHTML($body);
					$mail->Subject = 'Notification from dscourse.org';
					$mail->AddAddress($email, $name);
					
					if(!$mail->Send()){
						echo $mail->ErrorInfo;
					}
				}
			}
}

function Mention(){
	global $pdo;	
	
	$post = $_POST['post'];
			
	$postFromId		= 	$post['postFromId'];
	$postAuthorId	= 	$post['postAuthorId'];
	$postMessage	= 	$post['postMessage'];
	$postType		= 	$post['postType'];
	$postSelection	= 	$post['postSelection'];			
	$postMedia		= 	$post['postMedia'];
	$postMediaType  = 	$post['postMediaType'];
	$postContext	= 	$post['postContext'];
	$postID = $post['postID'];
	
	$truncated = myTruncate($postMessage, 100, $break = " ", $pad = "..."); 
	$link= "";
	if(isset($_SERVER["HTTP_HOST"])){
		$host = $_SERVER["HTTP_HOST"];
		$path = '/discussion.php';
		$query = "?";
		$stmt = $pdo->prepare("SELECT courseDiscussions.discussionID, courseDiscussions.courseID FROM discussionPosts INNER JOIN courseDiscussions on discussionPosts.discussionID = courseDiscussions.discussionID WHERE discussionPosts.discussionID in (SELECT discussionID FROM discussionPosts WHERE postID = :postID) LIMIT 1");
		$stmt->execute(array(':postID'=>$postID));
		//$d = mysql_query("SELECT courseDiscussions.discussionID, courseDiscussions.courseID FROM discussionPosts INNER JOIN courseDiscussions on discussionPosts.discussionID = courseDiscussions.discussionID WHERE discussionPosts.discussionID in (SELECT discussionID FROM discussionPosts WHERE postID = $postID) LIMIT 1");
		//$info = mysql_fetch_assoc($d);
		$info = $stmt->fetch();
		$dID = $info['discussionID'];
		$cID = $info['courseID'];
		$query.="d=$dID&c=$cID&p=$postID";
		$link = 'http://'.$host.$path.$query;
	}
	$stmt = $pdo->prepare("SELECT username, firstName FROM users WHERE userID = :postAuthorId");
	$stmt->execute(array(':postAuthorId'=>$postAuthorId));
	//$userFrom = mysql_query("SELECT username, firstName FROM users WHERE userID = $postAuthorId");	
	//$ufrom = mysql_fetch_assoc($userFrom);
	$ufrom = $stmt->fetch();
	$from  = $ufrom['firstName'];
	$fromUsername = $ufrom['username'];
	
	$mentions = $_POST['mentions'];
	$m = join(', ', $mentions);
	$stmt= $pdo->prepare("SELECT optionsTypeID FROM options WHERE optionsType = 'user' AND optionsTypeID IN (:m) AND optionsName = 'notify_on_mention' AND optionsValue = 1");
	$stmt->execute(array(':m'=>$m));
	//$q = mysql_query("SELECT optionsTypeID FROM options WHERE optionsType = 'user' AND optionsTypeID IN $m AND optionsName = 'notify_on_mention' AND optionsValue = 1");
	//while($row = mysql_fetch_assoc($q)){
	echo json_encode($m);	
	header('Connection: close');
	header('Content-Length: '.ob_get_length());
	ob_end_flush();
	ob_flush();
	flush();
			
	$uInfo = $pdo->prepare("SELECT username, firstName FROM users WHERE userID =:uID");	
	while($row = $stmt->fetch()){		
		//$e = mysql_query("SELECT username, firstName FROM users WHERE userID =".$row['optionsTypeID']);	
		$uInfo->execute(array(':uID'=>$row['optionsTypeID']));
		//$user = mysql_fetch_assoc($e);
		$user = $uInfo->fetch();
		$email = $user['username'];
		$name = $user['firstName'];

		require_once '../mail/class.phpmailer.php';
		require_once '../mail/mail_init.php';
		$mail = new PHPMailer();
		$mail = mail_init($mail);
		$body = file_get_contents('../mail/templates/notify.html');
		$head = "Hi $name, <br /> $from($fromUsername) mentioned you in one of their posts:";
		$body = str_replace('%head%',$head, $body);
		$body = str_replace('%msg%', $truncated, $body);
		$body = str_replace('%link%', $link, $body);
		$mail->MsgHTML($body);
		$mail->Subject = 'Notification from dscourse.org';
		$mail->AddAddress($email, $name);
			
		$mail->Send();
	}
}

function EditPost()
{
	global $pdo;
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
																		
			$stmt = $pdo->prepare("UPDATE posts SET  postFromId = :postFromId, postAuthorId = :postAuthorId, postMessage = :postMessage, postType  = :postType, postSelection  = :postSelection, postMedia  = :postMedia, postMediaType = :postMediaType, postContext  = :postContext  WHERE postID  = :postID");
			$stmt->execute(array(':postFromId'=>$postFromId,':postAuthorId'=>$postAuthorId, ':postMessage'=>$postMessage, ':postType'=>$postType, ':postSelection'=>$postSelection, ':postMedia'=>$postMedia, ':postMediaType'=>$postMediaType,':postContext'=>$postContext, ':postID'=>$postID));
			//$editPostQuery = mysql_query("UPDATE posts SET  postFromId = '".$postFromId."', postAuthorId = '".$postAuthorId."', postMessage = '".$postMessage."', postType  = '".$postType."', postSelection  = '".$postSelection."', postMedia  = '".$postMedia."', postMediaType = '".$postMediaType."', postContext  = '".$postContext."'  WHERE postID  = '".$postID."' "); 
}



function CheckNewPosts()
{
	global $pdo;
	// Checks to see if there are new posts in this discussion, returns number
			$currentDiscussion =   $_POST['currentDiscussion'];
			$currentPosts =   $_POST['currentPosts'];
		// Get posts within this discussion
		$stmt = $pdo->prepare("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = :currentDisc AND discussionPosts.postStatus != 'deleted'");
		$stmt->execute(array(':currentDisc'=>$currentDiscussion));	
			//$postData = mysql_query("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = '".$currentDiscussion."'");
			//$num_rows = mysql_num_rows($postData);
			$num_rows = $stmt->rowCount();
			$posts = array(); 	
			if($num_rows > 0){
				$i = 0;
				//while($row = mysql_fetch_array($postData)) :
				while($row = $stmt->fetch()) :
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
	global $pdo;
	
			$log = $_POST['log'];
			
				$logUserID	= $log['logUserID'];
				$logPageType= $log['logPageType'];
				$logPageID	= $log['logPageID'];
				$logAction	= $log['logAction'];
				$logActionID= $log['logActionID'];
				$logMessage	= $log['logMessage'];
				$logUserAgent	= $log['logUserAgent'];
				$logSessionID   = $log['logSessionID']; 
			
			$stmt = $pdo->prepare("INSERT INTO logs (logUserID, logPageType,logPageID, logAction, logActionID, logMessage, logUserAgent, logSessionID) VALUES(:logUserID, :logPageType, :logPageID, :logAction, :logActionID, :logMessage, :logUserAgent, :logSessionID)"); 
			//$addLogQuery = mysql_query("INSERT INTO logs (logUserID, logPageType,logPageID, logAction, logActionID, logMessage, logUserAgent, logSessionID) VALUES('$logUserID', '$logPageType', '$logPageID', '$logAction', '$logActionID', '$logMessage', '$logUserAgent', '$logSessionID')"); 
			if($stmt->execute(array(':logUserID'=>$logUserID,':logPageType'=>$logPageType, ':logPageID'=>$logPageID,':logAction'=>$logAction,':logActionID'=>$logActionID,':logMessage'=>$logMessage,':logUserAgent'=>$logUserAgent,':logSessionID'=>$logSessionID))){
				echo 1; 
			} else {
				echo 0; 
			}						
				
}
			
function SaveOptions()
{
	global $pdo;
			// Get option data
			$optionsType = $_POST['optionsType'];
			$optionsTypeID = $_POST['optionsTypeID'];
			$options =  $_POST['optionsData'] ;			
			$totalOptions = count($options); 
			$optionsAttr;

			$checkops = $pdo->prepare("SELECT * FROM options WHERE optionsType = :optionsType AND optionsTypeID = :optionsTypeID AND optionsName = :optionsName"); 
			$update = $pdo->prepare("UPDATE options SET  optionsValue  = :optionsValue, optionAttr  = :optionsAttr  WHERE optionsType = :optionsType AND optionsTypeID = :optionsTypeID AND optionsName = :optionsName"); 						
			$add = $pdo->prepare("INSERT INTO options (optionsType, optionsTypeID,optionsName, optionsValue, optionAttr) VALUES(:optionsType, :optionsTypeID, :optionsName, :optionsValue, :optionsAttr)"); 
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
				$params = array(':optionsType'=>$optionsType,':optionsTypeID'=>$optionsTypeID, ':optionsName'=>$options[$i]['optionsName']);
				
				// Check if that option exists in the database									
				//$checkoption = mysql_query("SELECT * FROM options WHERE optionsType = '".$optionsType."' AND optionsTypeID = '".$optionsTypeID."' AND optionsName = '".$options[$i]['optionsName']."' "); 
				$checkops->execute($params);
				//$num_rows = mysql_num_rows($checkoption);
				$num_rows = $checkops->rowCount();
				if($num_rows == 1){     // if options does not exist create the option row
					//$update = mysql_query("UPDATE options SET  optionsValue  = '".$options[$i]['optionsValue']."', optionAttr  = '".$optionsAttr."'  WHERE optionsType = '".$optionsType."' AND optionsTypeID = '".$optionsTypeID."' AND optionsName = '".$options[$i]['optionsName']."' "); 				
					$update->execute($params + array(':optionsValue'=>$options[$i]['optionsValue'],':optionsAttr'=>$optionsAttr));
				} else if ($num_rows == 0){  // if option exists edit the option
					//$add = mysql_query("INSERT INTO options (optionsType, optionsTypeID,optionsName, optionsValue, optionAttr) VALUES('".$optionsType."', '".$optionsTypeID."', '".$options[$i]['optionsName']."','".$options[$i]['optionsValue']."','".$optionsAttr."')"); 
					$add->execute($params+array(':optionsValue'=>$options[$i]['optionsValue'],':optionsAttr'=>$optionsAttr));
				}
			
			}
}
function Delete(){
	global $pdo;
	
	$id = $_POST['contextID'];
	$context = $_POST['context'];
	$params = array(':id'=>$id);
	switch($context){
		case 'course':
			$del = $pdo->prepare("UPDATE courses SET courseStatus='deleted' WHERE courseID = :id");
			$del->execute($params);
			echo 'http://' . $_SERVER['HTTP_HOST']. '/index.php';
		break;
		case 'post':
			$del = $pdo->prepare("UPDATE discussionPosts SET postStatus = 'deleted' WHERE postID = :id");
			$del->execute($params);
			//Also set the postFrom ID to the next highest node in the tree or 0
			
			echo $id;
		break; 
	}
}
function Archive(){
	global $pdo;
	
	$context = $_POST['context'];
	switch($context){
		case 'course':
			$ar = $pdo->prepare("UPDATE courses SET courseStatus='archived' WHERE courseID = :id");
			if(!$ar->execute(array(':id'=>$id))){
				echo -1;
				return;
			}	
			else {
				echo 1;
				return;
			}	
		break;
	}
}

// Original PHP code by Chirp Internet: www.chirp.com.au
	// Please acknowledge use of this code by including this header.
	// http://www.the-art-of-web.com/php/truncate/

	function myTruncate($string, $limit, $break = ".", $pad = "...") {
		// return with no change if string is shorter than $limit
		if (strlen($string) <= $limit)
			return $string;

		// is $break present between $limit and the end of the string?
		if (false !== ($breakpoint = strpos($string, $break, $limit))) {
			if ($breakpoint < strlen($string) - 1) {
				$string = substr($string, 0, $breakpoint) . $pad;
			}
		}

		return $string;
	}