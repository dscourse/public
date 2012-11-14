<?php 

/**
 *  The new and improved script for getting and saving data to the database. 
 *
 */

 	/*** Connect to Database ***/
	define('MyConst', TRUE);									// Avoids direct access to config.php
	include "config.php"; 


 	/*** Set global variables and header ***/
 	
 	$data	= Array();
 	
 	$data = Array(
	    "allCourses" => array(),
	    "allUsers"	 => array(),
	    "allDiscussions" => array()
	);


 	$action	= $_POST['action'];									// What the ajax call asks the php to do. 
	$username = $_SESSION['Username'];
	$admin = $_SESSION['status'];								// Takes the user id so we know which user information to show
    $thisUser = $_SESSION['UserID'];
    header('Content-type: application/json');							// Set headers for transferring the data between php and ajax    
 	

/* == ACTION INIT == */      


    if ($action == 'getAll')
    {
    
    	if($admin == 'Administrator'){
    
			    	$courseData = GetCourses();
			    	$data['allCourses'] =  $courseData;
			    	
			    	$courseListData = GetCourseList();
			    	$data['courseList'] =  $courseListData;
			    	
			    	$userData = GetUsers();
			    	$data['allUsers'] =  $userData;
			    	
			    	$discussionData = GetDiscussions();
			    	$data['allDiscussions'] =  $discussionData;
			    	
			    	$postData = GetPosts();
			    	$data['allPosts'] =  $postData;
			    	
			    	$noteData = GetNotes();
			    	$data['allNotes'] =  $noteData;
			 
			    	echo json_encode($data);								// Covert data into a json file.
	    
	    } else if ($admin == 'Participant'){

			    	$courseData = GetUserCourses();
			    	$data['allCourses'] =  $courseData;
			    	
			    	$courseListData = GetUserCourseList();
			    	$data['courseList'] =  $courseListData;
			    	
			    	$userData = GetUsers();
			    	$data['allUsers'] =  $userData;
			    	
			    	$discussionData = GetUserDiscussions($courseData);
			    	$data['allDiscussions'] =  $discussionData;
			    					    	
			    	$postData = GetUserPosts($discussionData);
			    	$data['allPosts'] =  $postData;
			    	
			    	echo json_encode($data);								// Covert data into a json file.
	    		    
		    
	    } else {
		    
	    }
	    
    }


if ($action == 'addCourse')
    {
    	AddCourse(); 
    
    }	
    
    
if ($action == 'updateCourse')
    {
    	UpdateCourse(); 
    
    }	

if ($action == 'saveDiscussions')
    {
    	SaveDiscussions(); 
    
    }	
 
 
 if ($action == 'addPost')
    {
    	AddPost(); 
    
    }	   

if ($action == 'checkNewPosts')
    {
    	CheckNewPosts(); 
    
    }	

if ($action == 'addLog')
    {
    	AddLog(); 
    
    }

if ($action == 'sendFeedback')
    {
    	// SendFeedback(); // Need to add this function
    
    }


if ($action == 'lastVisit')
    {
    	$visitLogs = LastVisit(); 
        echo json_encode($visitLogs);								// Covert data into a json file.

    }	

/* == FUNCTIONS == */      
    
function GetUsers()
{
					
				$userData = mysql_query("SELECT * FROM users ORDER BY firstName ASC"); 			// Get all the data from the users table
				
				while($r = mysql_fetch_assoc($userData)) {					// Populate the rows for individual users
						
						$users[] = $r;									// Add row to array
				
				}
				
					return $users;
			
	
}

function GetCourses()
{

 		$courseData = mysql_query("SELECT * FROM `courses` ORDER BY courseID DESC");  // Get everything 		   		
		
		while($r = mysql_fetch_assoc($courseData)) 
		{					
			$courses[] = $r; 								// Put mysql results into an array 
		}

		return $courses;
	
}

function GetUserCourses()								// Get only the courses for the user. 
{

 		$courseData = mysql_query("SELECT * FROM `courses` ORDER BY courseID DESC");  // Get everything 		   		
		
		$thisUser = intval($_SESSION['UserID']);
				 
		while($r = mysql_fetch_assoc($courseData)) 
		{					
			$includes = 'no';
			
			$inst = explode(",", $r['courseInstructors']);
				$instN = count($inst);
			
			if($instN > 0){
				for($i = 0; $i < $instN; $i++){
					
					if($inst[$i] == $thisUser){
					    $includes = 'yes';
				    }
					
				}
			}	
				
			$TAs = explode(",", $r['courseTAs']);
				$TAsN = count($TAs);

			if($TAsN > 0){
				
				for($j = 0; $j < $TAsN; $j++){
					
					if($TAs[$j] == $thisUser){
					    $includes = 'yes';
				    }
					
				}
			}	
				
			$students = explode(",", $r['courseStudents']);
				$studentsN = count($students);

			if($studentsN > 0){
				
				for($k = 0; $k < $studentsN; $k++){
					
					if($students[$k] == $thisUser){
					    $includes = 'yes';
				    }
					
				}
			}

			if($r['courseView'] == 'public'){
				$includes = 'yes';
			}
			
			if($includes == 'yes'){
				$courses[] = $r; 								// Put mysql results into an array 
			}
		}

		return $courses;
	
}

function GetCourseList()
{

 		$courseListData = mysql_query("SELECT courseID, courseName FROM `courses` ");   		   		
		$courseList = array();
		$course = array(); 
		
		while($r = mysql_fetch_assoc($courseListData)) 
		{					
			$course['value'] = $r['courseID'];
			$course['label'] = $r['courseName']; 								// Put mysql results into an array 
			array_push($courseList, $course);
		}

		return $courseList;
	
}

function GetUserCourseList()								// Get only the courses for the user. 
{

 		$courseListData = mysql_query("SELECT * FROM `courses` ");  		   		
		
		$thisUser = intval($_SESSION['UserID']);
		$courseList = array();
		$course = array(); 
				 
		while($r = mysql_fetch_assoc($courseListData)) 
		{					
			$includes = 'no';
			
			$inst = explode(",", $r['courseInstructors']);
				$instN = count($inst);
			
			if($instN > 0){
				for($i = 0; $i < $instN; $i++){
					
					if($inst[$i] == $thisUser){
					    $includes = 'yes';
				    }
					
				}
			}	
				
			$TAs = explode(",", $r['courseTAs']);
				$TAsN = count($TAs);

			if($TAsN > 0){
				
				for($j = 0; $j < $TAsN; $j++){
					
					if($TAs[$j] == $thisUser){
					    $includes = 'yes';
				    }
					
				}
			}	
				
			$students = explode(",", $r['courseStudents']);
				$studentsN = count($students);

			if($studentsN > 0){
				
				for($k = 0; $k < $studentsN; $k++){
					
					if($students[$k] == $thisUser){
					    $includes = 'yes';
				    }
					
				}
			}

			
			if($includes == 'yes'){
				$course['value'] = $r['courseID'];
				$course['label'] = $r['courseName']; 								// Put mysql results into an array 
				array_push($courseList, $course); 								// Put mysql results into an array 
			}
		}

		return $courseList;
	
}


function GetDiscussions()
{


 		$discussionData = mysql_query("SELECT * FROM `discussions` ORDER BY dID DESC ");  	// Get everything 
	
		while($r = mysql_fetch_assoc($discussionData)) 
		{					
			$discussions[] = $r; 									// Put mysql results into an array 
		}

		return $discussions;

}


function GetUserDiscussions($courses)
{
		$coursesN = count($courses);
		$discMerge = "";   
		for($i = 0; $i < $coursesN; $i++){
			$discMerge .= $courses[$i]['courseDiscussions']; 
		}
		
		$discArray = explode(",", $discMerge); 
		$discArrayN = count($discArray); 				

 		$discussionData = mysql_query("SELECT * FROM `discussions` ORDER BY dID DESC");  	// Get discussions 
	
		while($r = mysql_fetch_assoc($discussionData)) 
		{					
			for($j = 0; $j < $discArrayN; $j++){

				if($discArray[$j] != ""){				
					if($r['dID'] == $discArray[$j]){
						$discussions[] = $r; 
					}
				}	
			}
									// Put mysql results into an array 
		}

		return $discussions;

}

function CheckNewPosts()
{

	// Checks to see if there are new posts in this discussion, returns number
			$currentDiscussion =   $_POST['currentDiscussion'];
			$currentPosts =   $_POST['currentPosts'];
			 
			$discussionGet = mysql_query("SELECT * FROM `discussions` WHERE `dID` = '".$currentDiscussion."'");  	// Get everything 
	
			$r = mysql_fetch_array($discussionGet); 
								
			$posts = $r['dPosts']; 	
			
			$postsArray = explode(",", $posts); 				
			$currentPostsArray = explode(",", $currentPosts); 

			$numNew = count($postsArray);
			$numOld = count($currentPostsArray);
			
			$newposts = array(); 
			if($numNew > $numOld){
				$newposts['result'] = $numNew-$numOld;
				for($i = $numOld; $i <= $numNew-1; $i++ ){
					$newposts['posts'] .=   $postsArray[$i] . ',';
				}
			} else {
				$newposts['result'] = 0;
			}
			
    	echo json_encode($newposts);
}

// function for getting only the content user needs; i.e. courses, discussions, posts

function GetPosts()
{

 		 
 		$postData = mysql_query("SELECT * FROM `posts` ORDER BY postTime ASC");  	// Get everything 
	
		while($r = mysql_fetch_assoc($postData)) 
		{					
			$posts[] = $r; 									// Put mysql results into an array 
		}

		return $posts;

}

function GetUserPosts($discs)
{
		$discsN = count($discs);
		$postMerge = "";   
		for($i = 0; $i < $discsN; $i++){
			$postMerge .= $discs[$i]['dPosts']; 
		}
		
		$postArray = explode(",", $postMerge); 
		$postArrayN = count($postArray); 	
		
 		$postData = mysql_query("SELECT * FROM `posts` ORDER BY postTime ASC");  	// Get everything 
	
		while($r = mysql_fetch_assoc($postData)) 
		{					
			
			for($j = 0; $j < $postArrayN; $j++){
				
				if($postArray[$j] != ""){				
					if($r['postID'] == $postArray[$j]){
						$posts[] = $r; 		 
					}
				}	
			}
			
		}

		return $posts;

}


function GetNotes()
{
	 	 
	 	$noteData = mysql_query("SELECT * FROM `notes` ORDER BY noteTime ASC");  	// Get everything 
	
		while($r = mysql_fetch_assoc($noteData)) 
		{					
			$notes[] = $r; 									// Put mysql results into an array 
		}

		return $notes;

}



function UpdateCourse()
{

	$course = $_POST['course'];	

		$courseName 		= 	$course['courseName'];	// Set variables for common fields
		$courseDescription	= 	$course['courseDescription'];
		$courseInstructors	= 	$course['courseInstructors'];
		$courseTAs			= 	$course['courseTAs'];
		$courseStudents		= 	$course['courseStudents'];
		$courseStartDate	= 	$course['courseStartDate'];
		$courseEndDate		= 	$course['courseEndDate'];
		$coursePicture		= 	$course['coursePicture'];
		$courseURL			= 	$course['courseURL'];
		$courseID 			= 	$course['courseID'];
			
		$updateCourseQuery = mysql_query("UPDATE `courses` SET  `courseName` =  '".$courseName."', `courseDescription` =  '".$courseDescription."', `courseInstructors` =  '".$courseInstructors."', `courseTAs` =  '".$courseTAs."', `courseStudents` =  '".$courseStudents."', `courseStartDate` =  '".$courseStartDate."', `courseEndDate` =  '".$courseEndDate."',  `courseImage` =  '".$coursePicture."',  `courseURL` =  '".$courseURL."', `courseTAs` =  '".$courseTAs."'   WHERE  `courseID` = '".$courseID."' ");  
						
	echo 0;


}


function AddCourse()
{


	$course = $_POST['course'];	
	 
	$total = count($course);						// Go through all courses to see if they need update or save. 

		$courseName 		= 	$course['courseName'];	// Set variables for common fields
		$courseDescription	= 	$course['courseDescription'];
		$courseInstructors	= 	$course['courseInstructors'];
		$courseTAs			= 	$course['courseTAs'];
		$courseStudents		= 	$course['courseStudents'];
		$courseStartDate	= 	$course['courseStartDate'];
		$courseEndDate		= 	$course['courseEndDate'];
		$coursePicture		= 	$course['coursePicture'];
		$courseURL			= 	$course['courseURL'];
		
										// In this case insert new row 
			
		$addCourseQuery = mysql_query("INSERT INTO courses (courseName, courseDescription, courseInstructors, courseStatus, courseStartDate, courseEndDate, courseImage, courseURL, courseTAs, courseStudents) VALUES('".$courseName."', '".$courseDescription."', '".$courseInstructors."', 'active', '".$courseStartDate."', '".$courseEndDate."', '".$coursePicture."', '".$courseURL."', '".$courseTAs."', '".$courseStudents."')"); 
		

echo 0;

						
}



function SaveDiscussions()
{

	$discussions = $_POST['discussions'];	
	 
	$total = count($discussions);						// Go through all discussions to see if they need update or save. 
	for ($i = 0; $i < $total; $i++)
	{
		$dTitle 	= 	$discussions[$i]['dTitle'];	// Set variables for common fields
		$dPrompt	= 	$discussions[$i]['dPrompt'];
		$dStartDate	= 	$discussions[$i]['dStartDate'];
		$dEndDate	= 	$discussions[$i]['dEndDate'];		
		$dOpenDate	= 	$discussions[$i]['dOpenDate'];		
		$dPosts		=   $discussions[$i]['dPosts'];
		
		$dCourses		= 	$discussions[$i]['dCourses'];		
		$courseIDs = explode(",", $dCourses);
		$cTotal = count($courseIDs);						// Go through all discussions to see if they need update or save. 
		
		//print_r($courseIDs);
		if(isset($discussions[$i]['dID']))				// In this case do an update on the database
		{	
					$dID 		= 	$discussions[$i]['dID'];

			
				$updateDiscussionQuery = mysql_query("UPDATE `discussions` SET  `dTitle` =  '".$dTitle."', `dPrompt` =  '".$dPrompt."', `dStartDate` =  '".$dStartDate."', `dEndDate` =  '".$dEndDate."', `dOpenDate` =  '".$dOpenDate."', `dPosts` =  '".$dPosts."' WHERE `dID` = '".$dID."' ");  
				
				if($updateDiscussionQuery)
				{
					// echo " ID: " . $dID . " updated. "; 
					
				} else 
				{
					// echo " ID " . $dID . " NOT updated. ";
					
				}	
			
																
		} else {								// In this case insert new row 
			
			$addDiscussionQuery = mysql_query("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES('".$dTitle."', '".$dPrompt."', '".$dStartDate."', '".$dOpenDate."', '".$dEndDate."')"); 
			$id = mysql_insert_id();
			
			
			if($addDiscussionQuery)
			{
				// echo " discussion: " . $dTitle . " added. "; 
				
				for($n = 0; $n < $cTotal; $n++){
					$courseUpdate = mysql_query(" UPDATE `courses` SET courseDiscussions = CONCAT(courseDiscussions, '," .$id . "') WHERE courseID = ".$courseIDs[$n]."  ");  // We make sure to update the courses as well.
		
				// echo "Course ids " . $courseIDs[$n]; 
				
				}

				
				
				
			} else 
			{
				// echo " discussion: " . $dTitle . " NOT added. "; 
				
			}
				
		}
		
	}

	echo 0; 
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
			
			echo $postID; 				// Send it back to the dscourse.js
				


			// Then save the post id to the discussion
			$currentDiscussion =   $_POST['currentDiscussion'];

			 
			$discussionGet = mysql_query("SELECT * FROM `discussions` WHERE `dID` = '".$currentDiscussion."'");  	// Get everything 
	
			$r = mysql_fetch_array($discussionGet); 
								
			$posts = $r['dPosts']; 									
			
			
			$posts = $posts . ',' . $postID; 

			
			$discussionSave = mysql_query("UPDATE `discussions` SET  `dPosts` =  '".$posts."' WHERE `dID` = '".$currentDiscussion."' ");  

			

}

function AddLog()
{
			$log = $_POST['log'];

				$logUserID	= $log[logUserID];
				$logPageType= $log[logPageType];
				$logPageID	= $log[logPageID];
				$logAction	= $log[logAction];
				$logActionID= $log[logActionID];
				$logMessage	= $log[logMessage];
				$logUserAgent	= $log[logUserAgent];
																
			$addLogQuery = mysql_query("INSERT INTO logs (logUserID, logPageType,logPageID, logAction, logActionID, logMessage, logUserAgent) VALUES('".$logUserID."', '".$logPageType."', '".$logPageID."','".$logAction."','".$logActionID."','".$logMessage."','".$logUserAgent."')"); 
						
				
}

function LastVisit(){

	$logPageType 	=   $_POST['logPageType'];
	$logPageID 		=   $_POST['logPageID'];
	$logUserID 		=   $_POST['logUserID'];
			 
	$getLogQuery = mysql_query("SELECT * FROM `logs` WHERE `logPageType` = '".$logPageType."' AND  `logPageID` = '".$logPageID."' AND `logUserID` = '".$logUserID."' ORDER BY logTime DESC ");  	

	while($r = mysql_fetch_assoc($getLogQuery)) 
		{					
			$logs[] = $r; 									// Put mysql results into an array 
		}
	
	return $logs[0]; 
}




