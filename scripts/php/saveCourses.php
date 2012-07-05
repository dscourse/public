<?php

/*
 * Saves the changes to the courses table
 * 
 */
 
 	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "config.php"; 

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
					echo " ID: " . $courseID . " updated. "; 
					
				} else 
				{
					echo " ID " . $courseID . " NOT updated. ";
					
				}	
													
		} else {								// In this case insert new row 
			
			$addCourseQuery = mysql_query("INSERT INTO courses (courseName, courseDescription, courseInstructors, courseStatus, courseStartDate, courseEndDate, courseImage, courseURL, courseTAs, courseStudents) VALUES('".$courseName."', '".$courseDescription."', '".$courseInstructors."', 'active', '".$courseStartDate."', '".$courseEndDate."', '".$coursePicture."', '".$courseURL."', '".$courseTAs."', '".$courseStudents."')"); 
			
			if($addCourseQuery)
			{
				echo " Course: " . $courseName . " added. "; 
				
			} else 
			{
				echo " Course: " . $courseName . " NOT added. "; 
				
			}
				
		}
		
	}
	
 
 
 
 
 
 /* End of file saveCourses.php   */
