<?php 

/**
 *  The new and improved script for getting and saving data to the database. 
 *
 */

 	/*** Connect to Database ***/
	define('MyConst', TRUE);									// Avoids direct access to config.php
	include "config.php"; 




 	$action	= 'checkNewPosts';									// What the ajax call asks the php to do. 
	header('Content-type: application/json');							// Set headers for transferring the data between php and ajax    
 	

if ($action == 'checkNewPosts')
    {
    	CheckNewPosts(); 
    
    }	



function CheckNewPosts()
{
	// Checks to see if there are new posts in this discussion, returns number
			$currentDiscussion =  40;
			$currenPosts =   '283,293,294,295,296,297,298,299,300,301,302,303,304';
			 
			$discussionGet = mysql_query("SELECT * FROM `discussions` WHERE `dID` = '".$currentDiscussion."'");  	// Get everything 
	
			$r = mysql_fetch_array($discussionGet); 
								
			$posts = $r['dPosts']; 	
			
			$postsArray = explode(",", $posts); 				
			$currentPostsArray = explode(",", $currentPosts); 

			$numNew = count($postsArray);
			$numOld = count($currentPostsArray);
			
			print_r($postsArray); 
			print_r($currentPostsArray); 
			
			echo 'New number: ' . $numNew;
			echo '-- Old number: ' . $numOld; 
			if($numNew > $numOld){
				echo '-- New - Old: ' . $numNew-$numOld; 
			} else {
				echo '-- Nothing' ;
			}
}
