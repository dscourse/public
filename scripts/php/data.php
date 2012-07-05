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
 	



    if ($action == 'getAll')
    {
    
    	$courseData = GetCourses();
    	$data['allCourses'] =  $courseData;
    	
    	$userData = GetUsers();
    	$data['allUsers'] =  $userData;
    	
    	$discussionData = GetDiscussions();
    	$data['allDiscussions'] =  $discussionData;
    	

    	echo json_encode($data);								// Covert data into a json file.
	    
	    
    }
	

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

 		$courseData = mysql_query("SELECT * FROM `courses` ");  // Get everything 		   		
		
		while($r = mysql_fetch_assoc($courseData)) 
		{					
			$courses[] = $r; 								// Put mysql results into an array 
		}

		return $courses;
	
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



function SaveCourses()
{


}


function SaveDiscussions()
{


}