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
    header('Content-type: application/json');							// Set headers for transferring the data between php and ajax    
 	

/* == ACTION INIT == */      


    if ($action == 'getAll')
    {
    
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
	    
	    
    }


if ($action == 'saveCourses')
    {
    	SaveCourses(); 
    
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

function GetCourseList()
{

 		$courseListData = mysql_query("SELECT courseID, courseName FROM `courses` ");  // Get everything 		   		
		
		while($r = mysql_fetch_assoc($courseListData)) 
		{					
			$courseList[] = $r; 								// Put mysql results into an array 
		}

		return $courseList;
	
}

function GetDiscussions()
{


 		$discussionData = mysql_query("SELECT * FROM `discussions` ");  	// Get everything 
	
		while($r = mysql_fetch_assoc($discussionData)) 
		{					
			$discussions[] = $r; 									// Put mysql results into an array 
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
			
			if($numNew > $numOld){
				echo $numNew-$numOld; 
			} else {
				echo 0;
			}
}

function GetPosts()
{


 		$postData = mysql_query("SELECT * FROM `posts` ORDER BY postTime ASC");  	// Get everything 
	
		while($r = mysql_fetch_assoc($postData)) 
		{					
			$posts[] = $r; 									// Put mysql results into an array 
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



function SaveCourses()
{


	$courses = $_POST['courses'];	
	 
	$total = count($courses);						// Go through all courses to see if they need update or save. 
	for ($i = 0; $i < $total; $i++)
	{
		$courseName 		= 	$courses[$i]['courseName'];	// Set variables for common fields
		$courseDescription	= 	$courses[$i]['courseDescription'];
		$courseInstructors	= 	$courses[$i]['courseInstructors'];
		$courseTAs			= 	$courses[$i]['courseTAs'];
		$courseStudents		= 	$courses[$i]['courseStudents'];
		$courseStartDate	= 	$courses[$i]['courseStartDate'];
		$courseEndDate		= 	$courses[$i]['courseEndDate'];
		$coursePicture		= 	$courses[$i]['coursePicture'];
		$courseURL			= 	$courses[$i]['courseURL'];
		
		
		if($courses[$i]['courseID'])				// In this case do an update on the database
		{	
			$courseID = $courses[$i]['courseID'];
			
				$updateCourseQuery = mysql_query("UPDATE `courses` SET  `courseName` =  '".$courseName."', `courseDescription` =  '".$courseDescription."', `courseInstructors` =  '".$courseInstructors."', `courseTAs` =  '".$courseTAs."', `courseStudents` =  '".$courseStudents."', `courseStartDate` =  '".$courseStartDate."', `courseEndDate` =  '".$courseEndDate."',  `courseImage` =  '".$coursePicture."',  `courseURL` =  '".$courseURL."', `courseTAs` =  '".$courseTAs."'   WHERE  `courseID` = '".$courseID."' ");  
				
				if($updateCourseQuery)
				{
					//echo " ID: " . $courseID . " updated. "; 
					
				} else 
				{
					//echo " ID " . $courseID . " NOT updated. ";
					
				}	
													
		} else {								// In this case insert new row 
			
			$addCourseQuery = mysql_query("INSERT INTO courses (courseName, courseDescription, courseInstructors, courseStatus, courseStartDate, courseEndDate, courseImage, courseURL, courseTAs, courseStudents) VALUES('".$courseName."', '".$courseDescription."', '".$courseInstructors."', 'active', '".$courseStartDate."', '".$courseEndDate."', '".$coursePicture."', '".$courseURL."', '".$courseTAs."', '".$courseStudents."')"); 
			
			if($addCourseQuery)
			{
				//echo " Course: " . $courseName . " added. "; 
				
			} else 
			{
				//echo " Course: " . $courseName . " NOT added. "; 
				
			}
				
		}
		
	}
	
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
												
			$addPostQuery = mysql_query("INSERT INTO posts (postFromId, postAuthorId, postMessage, postType, postSelection, postMedia, postMediaType) VALUES('".$postFromId."', '".$postAuthorId."', '".$postMessage."','".$postType."','".$postSelection."','".$postMedia."','".$postMediaType."')"); 
			
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





