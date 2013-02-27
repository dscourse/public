<?php

/*
 * Dscourse php functions 
 *
 */
date_default_timezone_set('UTC');

class Dscourse {

	public function __construct() {
	
	}


	public function GetUserMini(){
			/*  
			 *  Gets only user ID and names
			 */		
				$userData = mysql_query("SELECT UserID, firstName, lastName, username FROM users ORDER BY firstName ASC"); 			// Get all the data from the users table
				
				while($r = mysql_fetch_assoc($userData)) {					// Populate the rows for individual users
						
						$users[] = $r;									// Add row to array
				
				}
				
				return $users;
		}
	
	
	public function GetUserNetworks($userID){
		/*  
		 *  Gets the list of networks the user belongs to
		 */		
		$query = mysql_query("SELECT * FROM networks INNER JOIN networkUsers ON networks.networkID = networkUsers.networkID WHERE networkUsers.userID = '".$userID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		// Get public networks 
		$query2 = mysql_query("SELECT * FROM networks WHERE networkStatus = 'public' " );

		$j = 0;
		while($row2 = mysql_fetch_array($query2)) :
			array_push($results, $row2);
			$j++;
		endwhile;

		return $results; 
	}

	public function CheckNetworkAccess($userID, $nID){
		/*  
		 *  Checks to see if the current user can access the network. Returns the user role. 
		 */		
		 $status = 'unset'; 

		 // Check if user is in network, set the user role
		$query = mysql_query("SELECT * FROM networks INNER JOIN networkUsers ON networks.networkID = networkUsers.networkID WHERE networkUsers.userID = '".$userID."' AND networkUsers.networkID = '".$nID."' ");
		$results = mysql_fetch_array($query);
		if($results['networkUserRole'] == 'owner'){
			$status = 'owner'; 			
		} else if($results['networkUserRole'] == 'member') {
			$status = 'member'; 						
		} else {
			 // Check if network is public 
			$query2 = mysql_query("SELECT * FROM networks WHERE networkID = '".$nID."' " );
			$results2 = mysql_fetch_array($query2); 
			if($results2['networkStatus'] == 'public'){
				$status = 'public'; 
			} else {
				$status = 'restricted'; 
				
			}						
		}
		return $status; 
	}

	public function NetworkInfo($nID){
		/*  
		 *  Gets information about the network
		 */
		$query = mysql_query("SELECT * FROM networks WHERE networkID = '".$nID."'");
		$results = mysql_fetch_array($query); 
		return $results; 
	}

	public function NetworkUsers($nID){
		/*  
		 *  Gets users in the network with their user information
		 */
		$query = mysql_query("SELECT * FROM networkUsers INNER JOIN users ON networkUsers.userID = users.UserID WHERE networkUsers.networkID = '".$nID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results; 
	}


	public function NetworkCourses($nID){
		/*  
		 *  Gets users in the network with their user information
		 */


		$query = mysql_query("SELECT * FROM networkCourses INNER JOIN courses ON networkCourses.courseID = courses.courseID WHERE networkCourses.networkID = '".$nID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results; 
	}

	public function IsUserInNetwork($nID, $userID){
		/*  
		 *  Gets users in the network with their user information
		 */
		$query = mysql_query("SELECT * FROM networkUsers WHERE networkID = '".$nID."' AND userID = '".$userID."' ");
		//$results = mysql_fetch_array($query); 
		$num_rows = mysql_num_rows($query);
		if($num_rows > 0){
			return true;
		} else {
			return false; 
		}
	}
		
	
	public function UserInfo($uID){
		/*  
		 *  Gets all the information about the user
		 */
		$query = mysql_query("SELECT * FROM users WHERE UserID = '".$uID."'");
		$results = mysql_fetch_array($query); 
		return $results; 
	}

	public function CourseInfo($cID){
		/*  
		 *  Gets all the information about the course
		 */
		$query = mysql_query("SELECT * FROM courses INNER JOIN networkCourses ON courses.courseID = networkCourses.courseID WHERE courses.courseID = '".$cID."'");
		$results = mysql_fetch_array($query); 
		return $results; 
	}

	public function DiscussionInfo($discID){
		/*  
		 *  Gets all the information about the course
		 */
		$query = mysql_query("SELECT * FROM discussions INNER JOIN courseDiscussions ON discussions.dID = courseDiscussions.discussionID WHERE courseDiscussions.discussionID = '".$discID."'");
		$results = mysql_fetch_array($query); 
		return $results; 
	}

	public function DiscussionStatus($discID){
		/*  
		 *  Checks to see the status of the discussion. 
		 */
		$query = mysql_query("SELECT * FROM discussions WHERE dID = '".$discID."'");
		$results = mysql_fetch_array($query); 

		$startDate = strtotime($results['dStartDate']); 
		$openDate  = strtotime($results['dOpenDate']) ; 
		$endDate   = strtotime($results['dEndDate'])  ; 
		$now	   = strtotime(date("Y-m-d H:i:s"));

		$dStatus = 'closed'; 
		if($now <= $endDate && $now >= $startDate ){
			if($now <= $openDate){
				$dStatus = 'student'; 
			} else {
				$dStatus = 'all'; 
			}
		} else {
			$dStatus = 'closed'; 
		}		

		return $dStatus; 
	}


	public function CourseRoles($cID){
		/*  
		 *  Gets users in the network with their user information
		 */
		$query = mysql_query("SELECT * FROM courseRoles INNER JOIN users ON courseRoles.userID = users.UserID WHERE courseRoles.courseID = '".$cID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results; 
	}

	public function CourseNetworks($cID){
		/*  
		 *  Gets the networks this course belongs to. 
		 */
		$query = mysql_query("SELECT * FROM networkCourses WHERE courseID = '".$cID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results; 
	}
	
	public function UserCourseRole($cID, $userID){
		/*  
		 *  Gets the course role of the user for specific course
		 */
		$query = mysql_query("SELECT userRole FROM courseRoles WHERE courseID = '".$cID."' AND userID = '".$userID."' ");
		$results = mysql_fetch_array($query); 
		return $results; 
	}

	public function GetUserCourses($userID){
		/*  
		 *  Gets the list of networks the user belongs to
		 */		
		$query = mysql_query("SELECT * FROM courses INNER JOIN courseRoles ON courses.courseID = courseRoles.courseID WHERE courseRoles.userID = '".$userID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results; 
	}

	public function GetCourseDiscussions($cID){
		/*  
		 *  Gets the list of discussions in this course
		 */		
		$query = mysql_query("SELECT * FROM courseDiscussions INNER JOIN discussions ON courseDiscussions.discussionID = discussions.dID WHERE courseDiscussions.courseID = '".$cID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results; 
	}

	public function GetDiscussionCourses($discID){
		/*  
		 *  Gets the list of discussions in this course
		 */		
		$query = mysql_query("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = '".$discID."'");
		$results = array(); 

		$i = 0;
		while($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results; 
	}

	public function LoadDiscussion($discID, $userID, $nID){
		/*  
		 *  Checks to see whether discussion should be loaded based on user status and discussion status  
		 */	
		 
        $load = false; // Default status is to not load the discussion

		$query = mysql_query("SELECT * FROM courseDiscussions  WHERE discussionID = '".$discID."'");		// Get courses this discussion belongs to

		$i = 0;
		while($row = mysql_fetch_array($query)) :															// For each of these courses... 
			$courseStatus = $this->UserCourseRole($row['courseID'], $userID);								// See if this user is in the course
			$courseInfo   = $this->CourseInfo($row['courseID']); 											// Who does this course allow for viewing? 
			$courseNetworks = $this->CourseNetworks($row['courseID']); 										// Which networks does this course belong to? 
			$totalNetworks = count($courseNetworks); 														// Count networks for a loop later
			switch ($courseInfo['courseView']) {															// Depending on who this course allows to be viewed by 
			    case "members":																				// If the course should be viewed only by members
			        if($courseStatus[0] == 'Student' || $courseStatus[0] == 'TA' || $courseStatus[0] == 'Instructor'){  // check if this user is a member
						$load = true; 
					} 
			        break;
			    case "network":																				// If the course should only be viewed by network members
			        for($j = 0; $j < $totalNetworks; $j++){
				        if($this->IsUserInNetwork($courseNetworks[$j]['networkID'], $userID)) {				// Check if this user is in any of the networks the course belongs to
					        $load = true; 
				        }
			        }
			        break;
			    case "everyone":																			// If this course can be viewed by everyone
			        $load = true; 																			// Just load everything
			        break;
			} 
			$i++;
		endwhile;
		
		return $load;  	
	}	
	

	public function CountPosts($discID){
		/*  
		 *  Counts total number of posts in discussion  
		 */

		$query = mysql_query("SELECT discussionPostID FROM discussionPosts WHERE discussionID = '".$discID."'");
		$num_rows = mysql_num_rows($query);

		return $num_rows;
				 			
	}

	public function Messages($m){
		/*  
		 *  Returns message content  
		 */
		 $message = array(); 
        $message['icon'] = "N";
        $message['color'] = "#333";
        $message['error'] = "false";
		 switch ($m) {
		    case 1:
		        $message['content'] = "Profile changes saved. ";
		        break;
		    case 2:
		        $message['content'] = "Changes to the discussion were saved. ";
		        break;
		    case 4:
		        $message['content'] = "You uploaded an invalid file please try again. ";
		        $message['color'] = "#999";
		        $message['error'] = "true";
		        break;
		    case 5:
		        $message['content'] = "You were added to the network.";
		        break;
		    case 6:
		        $message['content'] = "You are not part of that network.";
		        $message['icon'] = "A";
		        $message['color'] = "#999";
		        $message['error'] = "true";
		        break;
		    case 7:
		        $message['content'] = "Network changes are saved.";
		        $message['icon'] = "A";
		        break;
		    case 8:
		        $message['content'] = "Users were added to your network. ";
		        $message['icon'] = "A";
		        break;
		    case 9:
		        $message['content'] = "You were just added to this network. ";
		        $message['icon'] = "Q";
		        break;
		    case 10:
		        $message['content'] = "Course changes were saved ";
		        $message['icon'] = "Q";
		        break;
		    case 'e':
		        $message['content'] = "There was an error reaching the database. Your changes were not saved.";
		        $message['color'] = "#999";
		        $message['error'] = "true";
		        break;
		    case 'd':
		        $message['content'] = "Your discussion was added to this course.";
		        break;

		 }
		
		 return $message; 
	}

	
	// Original PHP code by Chirp Internet: www.chirp.com.au
	// Please acknowledge use of this code by including this header.
	// http://www.the-art-of-web.com/php/truncate/
	
	function myTruncate($string, $limit, $break=".", $pad="...")
	{
	  // return with no change if string is shorter than $limit
	  if(strlen($string) <= $limit) return $string;
	
	  // is $break present between $limit and the end of the string?
	  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
	    if($breakpoint < strlen($string) - 1) {
	      $string = substr($string, 0, $breakpoint) . $pad;
	    }
	  }
	
	  return $string;
	}
    

} // Closing class

$dscourse = new Dscourse(); 