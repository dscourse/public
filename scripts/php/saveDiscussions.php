<?php

/*
 * Saves the changes to the discussions table
 * 
 */
 
 	define('MyConst', TRUE);	// Avoids direct access to config.php
	include "config.php"; 

	$discussions = $_POST['discussions'];	
	 
	$total = count($discussions);						// Go through all discussions to see if they need update or save. 
	for ($i = 0; $i < $total; $i++)
	{
		$dTitle 	= 	$discussions[$i]['dTitle'];	// Set variables for common fields
		$dPrompt	= 	$discussions[$i]['dPrompt'];
		$dStartDate	= 	$discussions[$i]['dStartDate'];
		$dEndDate		= 	$discussions[$i]['dEndDate'];		
		
		$dCourses		= 	$discussions[$i]['dCourses'];		
		$courseIDs = explode(",", $dCourses);
		$cTotal = count($courseIDs);						// Go through all discussions to see if they need update or save. 
		
		print_r($courseIDs);
		
		if($discussions[$i]['dID'])				// In this case do an update on the database
		{	
			
			
																
		} else {								// In this case insert new row 
			
			$addDiscussionQuery = mysql_query("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dEndDate) VALUES('".$dTitle."', '".$dPrompt."', '".$dStartDate."', '".$dEndDate."')"); 
			$id = mysql_insert_id();
			
			
			if($addDiscussionQuery)
			{
				echo " discussion: " . $dTitle . " added. "; 
				
				for($n = 0; $n < $cTotal; $n++){
					$courseUpdate = mysql_query(" UPDATE `courses` SET courseDiscussions = CONCAT(courseDiscussions, '," .$id . "') WHERE courseID = ".$courseIDs[$n]."  ");  // We make sure to update the courses as well.
		
		
		
				echo "Course ids " . $courseIDs[$n]; 
				}

				
				
				
			} else 
			{
				echo " discussion: " . $dTitle . " NOT added. "; 
				
			}
				
		}
		
	}
	
 
 
 
 
 
 /* End of file savediscussions.php   */
