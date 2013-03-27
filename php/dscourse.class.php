<?php

/*
 * Dscourse php functions
 *
 */
date_default_timezone_set('UTC');

class Dscourse {

	public function __construct() {

	}

	public function GetUserMini() {
		/*
		 *  Gets only user ID and names
		 */
		$userData = mysql_query("SELECT UserID, firstName, lastName, username FROM users ORDER BY firstName ASC");
		// Get all the data from the users table

		while ($r = mysql_fetch_assoc($userData)) {// Populate the rows for individual users

			$users[] = $r;
			// Add row to array

		}

		return $users;
	}

	public function GetUserNetworks($userID) {
		/*
		 *  Gets the list of networks the user belongs to
		 */
		$query = mysql_query("SELECT * FROM networks INNER JOIN networkUsers ON networks.networkID = networkUsers.networkID WHERE networkUsers.userID = '" . $userID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		// Get public networks
		$query2 = mysql_query("SELECT * FROM networks WHERE networkStatus = 'public' ");

		$j = 0;
		while ($row2 = mysql_fetch_array($query2)) :
			array_push($results, $row2);
			$j++;
		endwhile;

		return $results;
	}

	public function CheckNetworkAccess($userID, $nID) {
		/*
		 *  Checks to see if the current user can access the network. Returns the user role.
		 */
		$status = 'unset';

		// Check if user is in network, set the user role
		$query = mysql_query("SELECT * FROM networks INNER JOIN networkUsers ON networks.networkID = networkUsers.networkID WHERE networkUsers.userID = '" . $userID . "' AND networkUsers.networkID = '" . $nID . "' ");
		$results = mysql_fetch_array($query);
		if ($results['networkUserRole'] == 'owner') {
			$status = 'owner';
		} else if ($results['networkUserRole'] == 'member') {
			$status = 'member';
		} else {
			// Check if network is public
			$query2 = mysql_query("SELECT * FROM networks WHERE networkID = '" . $nID . "' ");
			$results2 = mysql_fetch_array($query2);
			if ($results2['networkStatus'] == 'public') {
				$status = 'public';
			} else {
				$status = 'restricted';

			}
		}
		return $status;
	}

	public function NetworkInfo($nID) {
		/*
		 *  Gets information about the network
		 */
		$query = mysql_query("SELECT * FROM networks WHERE networkID = '" . $nID . "'");
		$results = mysql_fetch_array($query);
		return $results;
	}

	public function NetworkUsers($nID) {
		/*
		 *  Gets users in the network with their user information
		 */
		$query = mysql_query("SELECT * FROM networkUsers INNER JOIN users ON networkUsers.userID = users.UserID WHERE networkUsers.networkID = '" . $nID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function AllUsers() {
		/*
		 *  Gets all users in the system
		 */
		$query = mysql_query("SELECT * FROM users ");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function NetworkCourses($nID) {
		/*
		 *  Gets users in the network with their user information
		 */

		$query = mysql_query("SELECT * FROM networkCourses INNER JOIN courses ON networkCourses.courseID = courses.courseID WHERE networkCourses.networkID = '" . $nID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function AllCourses() {
		/*
		 *  Gets all courses
		 */

		$query = mysql_query("SELECT * FROM courses ");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function IsUserInNetwork($nID, $userID) {
		/*
		 *  Gets users in the network with their user information
		 */
		$query = mysql_query("SELECT * FROM networkUsers WHERE networkID = '" . $nID . "' AND userID = '" . $userID . "' ");
		//$results = mysql_fetch_array($query);
		$num_rows = mysql_num_rows($query);
		if ($num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function UserInfo($uID) {
		/*
		 *  Gets all the information about the user
		 */
		$query = mysql_query("SELECT * FROM users WHERE UserID = '" . $uID . "'");
		$results = mysql_fetch_array($query);
		return $results;
	}

	public function CourseInfo($cID) {
		/*
		 *  Gets all the information about the course
		 */
		$query = mysql_query("SELECT * FROM courses INNER JOIN networkCourses ON courses.courseID = networkCourses.courseID WHERE courses.courseID = '" . $cID . "'");
		$results = mysql_fetch_array($query);
		return $results;
	}

	public function DiscussionInfo($discID) {
		/*
		 *  Gets all the information about the course
		 */
		$query = mysql_query("SELECT * FROM discussions INNER JOIN courseDiscussions ON discussions.dID = courseDiscussions.discussionID WHERE courseDiscussions.discussionID = '" . $discID . "'");
		$results = mysql_fetch_array($query);
		return $results;
	}

	public function DiscussionStatus($discID) {
		/*
		 *  Checks to see the status of the discussion.
		 */
		$query = mysql_query("SELECT * FROM discussions WHERE dID = '" . $discID . "'");
		$results = mysql_fetch_array($query);

		$startDate = strtotime($results['dStartDate']);
		$openDate = strtotime($results['dOpenDate']);
		$endDate = strtotime($results['dEndDate']);
		$now = strtotime(date("Y-m-d H:i:s"));

		$dStatus = 'closed';
		if ($now <= $endDate && $now >= $startDate) {
			if ($now <= $openDate) {
				$dStatus = 'student';
			} else {
				$dStatus = 'all';
			}
		} else {
			$dStatus = 'closed';
		}

		return $dStatus;
	}

	public function CourseRoles($cID) {
		/*
		 *  Gets users in the network with their user information
		 */
		$query = mysql_query("SELECT * FROM courseRoles INNER JOIN users ON courseRoles.userID = users.UserID WHERE courseRoles.courseID = '" . $cID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function CourseNetworks($cID) {
		/*
		 *  Gets the networks this course belongs to.
		 */
		$query = mysql_query("SELECT * FROM networkCourses INNER JOIN networks ON networkCourses.networkID = networks.networkID WHERE networkCourses.courseID = '" . $cID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function UserCourseRole($cID, $userID) {
		/*
		 *  Gets the course role of the user for specific course
		 */
		$query = mysql_query("SELECT userRole FROM courseRoles WHERE courseID = '" . $cID . "' AND userID = '" . $userID . "' ");
		$results = mysql_fetch_array($query);
		return $results;
	}

	public function LoadCourse($cID, $userID) {
		/*
		 *  Checks to see if this course should be shown to the current user in different parts of the site (i.e. as listing in network.php or individual course page)
		 */

		$load = false;
		// default is to not show it

		$courseStatus = $this -> UserCourseRole($cID, $userID);
		// See if this user is in the course
		$courseInfo = $this -> CourseInfo($cID);
		// Who does this course allow for viewing?
		$courseNetworks = $this -> CourseNetworks($cID);
		// Which networks does this course belong to?
		$totalNetworks = count($courseNetworks);
		// Count networks for a loop later
		switch ($courseInfo['courseView']) {// Depending on who this course allows to be viewed by
			case "members" :
				// If the course should be viewed only by members
				if ($courseStatus[0] == 'Student' || $courseStatus[0] == 'TA' || $courseStatus[0] == 'Instructor') {// check if this user is a member
					$load = true;
				}
				break;
			case "network" :
				// If the course should only be viewed by network members
				for ($j = 0; $j < $totalNetworks; $j++) {
					if ($this -> IsUserInNetwork($courseNetworks[$j]['networkID'], $userID)) {// Check if this user is in any of the networks the course belongs to
						$load = true;
					}
					if ($courseNetworks[$j]['networkStatus'] == "public") {
						$load = true;
					}
				}
				break;
			case "everyone" :
				// If this course can be viewed by everyone
				$load = true;
				// Just load everything
				break;
		}

		return $load;
	}

	public function GetUserCourses($userID) {
		/*
		 *  Gets the list of networks the user belongs to
		 */
		$query = mysql_query("SELECT * FROM courses INNER JOIN courseRoles ON courses.courseID = courseRoles.courseID WHERE courseRoles.userID = '" . $userID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function GetCourseDiscussions($cID) {
		/*
		 *  Gets the list of discussions in this course
		 */
		$query = mysql_query("SELECT * FROM courseDiscussions INNER JOIN discussions ON courseDiscussions.discussionID = discussions.dID WHERE courseDiscussions.courseID = '" . $cID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function GetDiscussionCourses($discID) {
		/*
		 *  Gets the list of discussions in this course
		 */
		$query = mysql_query("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = '" . $discID . "'");
		$results = array();

		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;

		return $results;
	}

	public function LoadDiscussion($discID, $userID) {
		/*
		 *  Checks to see whether discussion should be loaded based on user status and discussion status
		 */

		$load = false;
		// Default status is to not load the discussion

		$query = mysql_query("SELECT * FROM courseDiscussions  WHERE discussionID = '" . $discID . "'");
		// Get courses this discussion belongs to

		$i = 0;
		while ($row = mysql_fetch_array($query)) :// For each of these courses...
			$courseStatus = $this -> UserCourseRole($row['courseID'], $userID);
			// See if this user is in the course
			$courseInfo = $this -> CourseInfo($row['courseID']);
			// Who does this course allow for viewing?
			//$courseNetworks = $this->CourseNetworks($row['courseID']); 										// Which networks does this course belong to?
			//$totalNetworks = count($courseNetworks); 														// Count networks for a loop later
			switch ($courseInfo['courseView']) {// Depending on who this course allows to be viewed by
				case "members" :
					// If the course should be viewed only by members
					if ($courseStatus[0] == 'Student' || $courseStatus[0] == 'TA' || $courseStatus[0] == 'Instructor') {// check if this user is a member
						$load = true;
					}
					break;
				/*case "network":																				// If the course should only be viewed by network members
				 for($j = 0; $j < $totalNetworks; $j++){
				 if($this->IsUserInNetwork($courseNetworks[$j]['networkID'], $userID)) {				// Check if this user is in any of the networks the course belongs to
				 $load = true;
				 }
				 if($courseNetworks[$j]['networkStatus'] == "public" ){
				 $load = true;
				 }
				 }
				 break;*/
				case "everyone" :
					// If this course can be viewed by everyone
					$load = true;
					// Just load everything
					break;
			}
			$i++;
		endwhile;

		return $load;
	}

	public function CountPosts($discID) {
		/*
		 *  Counts total number of posts in discussion
		 */

		$query = mysql_query("SELECT discussionPostID FROM discussionPosts WHERE discussionID = '" . $discID . "'");
		$num_rows = mysql_num_rows($query);

		return $num_rows;

	}

	public function Messages($m) {
		/*
		 *  Returns message content
		 */
		$message = array();
		$message['icon'] = "N";
		$message['color'] = "#333";
		$message['error'] = "false";
		switch ($m) {
			case 1 :
				$message['content'] = "Profile changes saved. ";
				break;
			case 2 :
				$message['content'] = "Changes to the discussion were saved. ";
				break;
			case 4 :
				$message['content'] = "You uploaded an invalid file please try again. ";
				$message['color'] = "#999";
				$message['error'] = "true";
				break;
			case 5 :
				$message['content'] = "You were added to the network.";
				break;
			case 6 :
				$message['content'] = "You are not part of that network.";
				$message['icon'] = "A";
				$message['color'] = "#999";
				$message['error'] = "true";
				break;
			case 7 :
				$message['content'] = "Network changes are saved.";
				$message['icon'] = "A";
				break;
			case 8 :
				$message['content'] = "Users were added to your network. ";
				$message['icon'] = "A";
				break;
			case 9 :
				$message['content'] = "You were just added to this network. ";
				$message['icon'] = "Q";
				break;
			case 10 :
				$message['content'] = "Course changes were saved ";
				$message['icon'] = "Q";
				break;
			case 'e' :
				$message['content'] = "There was an error reaching the database. Your changes were not saved.";
				$message['color'] = "#999";
				$message['error'] = "true";
				break;
			case 'd' :
				$message['content'] = "Your discussion was added to this course.";
				break;
			case 11 :
				$message['content'] = "Changes to the network were saved. ";
				break;
		}

		return $message;
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

	public function PreProcess($query) {
		//1. CHECK STATUS
		$status = "OK";
		if (empty($_SESSION['Username']))// Checks to see if user is logged in, if not sends the user to login.php
		{
			// is cookie set?
			if (array_key_exists('userCookieDscourse', $_COOKIE)) {
				$getUserInfo = mysql_query("SELECT * FROM users WHERE UserID = '" . $_COOKIE["userCookieDscourse"] . "' ");
				if (mysql_num_rows($getUserInfo) == 1) {
					$row = mysql_fetch_array($getUserInfo);
					$_SESSION['Username'] = $row[1];
					$_SESSION['firstName'] = $row[3];
					$_SESSION['lastName'] = $row[4];
					$_SESSION['LoggedIn'] = 1;
					$_SESSION['status'] = $row[5];
					$_SESSION['UserID'] = $row[0];
					header('Location: index.php');

				} else {
					echo "Error: Could not load user info from cookie.";
				}
			} else {
				header('Location: info.php');
				// Not logged and and does not have cookie
			}
		}

		//
		$query = ltrim($query, '/');
		$parts = explode('?',$query);
		$location = $parts[0];
		$location = explode('/', $location); 
		$location = array_pop($location);
		$args = array();
		foreach(explode("&", $parts[1]) as $part){
			$sides = explode("=",$part);
			$args[$sides[0]]= $sides[1];
		}
		
		$member = FALSE;
		$viewer = FALSE;
		$regRequired = FALSE;
		$uID = $_SESSION['UserID'];
		$cID = $args['c'];
		
		//we only need to protect courses and discussions
		if($location=="course.php"||$location=="discussion.php"){
		//2. Check User Status Relevant to Resource	
			$a = mysql_query("SELECT * FROM courseRoles WHERE userID = '$uID' AND courseID = '$cID'");
			if(count(mysql_fetch_array($a))==0){
				$member = FALSE;
			}
			else{
				$member = TRUE;
			}
		//3. Check User Permission (based on qs)
			if(isset($args['a'])){
				$accessCode = $args['a'];
				$a = mysql_query("SELECT * FROM options WHERE optionsName = 'viewCode' OR optionsName = 'registerCode' AND optionsTypeID = '$accessCode' ");
				if(count(mysql_fetch_array($a))==0){
					$viewer = FALSE;
					$regRequired = FALSE;
				}
				else{
					$res = mysql_fetch_assoc($a);
					if($res['optionsName']=="viewCode"){
						$viewer = TRUE;
					}
					else if($res['optionsName']=="registerCode"){
						$regRequired = TRUE;
					}
				}
			}
		}
		if(!$member){
			if($viewer){
				$status = "view";
			}
			if($regRequired){
				$status = "register";
			}
			else{
				$status = "error";
			}
		}
		
		return array("status" => $status);
	}

	public function LTI() {
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
				$origin = $_SERVER['HTTP_ORIGIN'];
			} else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
				$ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
				$scheme = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_SCHEME);
				$origin = $scheme . '://' . $ref;
			} else if (array_key_exists('ext_sakai_server', $_POST)) {
				$origin = $_POST['ext_sakai_server'];
			} 

			if (strlen($origin) > 0) {
				$LTI_allowed = array('https://collab.itc.virginia.edu' => 'UVa Collab', 'http://dev.canlead.net' => 'CANLEAD', 'http://www.imsglobal.org' => 'IMS Test Harness');
				if (array_key_exists($origin, $LTI_allowed)) {
					$LTI = TRUE;
					include "lti.php";
					$postData = file_get_contents("php://input");
					$launch = parseLTIrequest($postData);
					if (!$launch) {
						return FALSE;
					}
					/*//Step 1: CHECK if Network Exists=>networkId
					$n = $LTI_allowed[$origin];
					$net = mysql_query("SELECT * FROM networks WHERE networkName = '" . $n . "'");
					$a = mysql_fetch_assoc($net);
					if ($net != FALSE && empty($a)) {//Create the new network
						$networkCode = rand(100000, 1000000000);
						mysql_query("INSERT INTO networks (networkName, networkDesc, networkCode) VALUES('" . $n . "', 'The " . $n . " network on dscourse', '" . $networkCode . "')");
						$netId = mysql_insert_id();
					} else {
						$netId = $a['networkID'];
					}*/
					//Step 2: CHECK if Course Exists=>networkId
					$c = $launch -> params['courseName'];
					$course = mysql_query("SELECT * FROM courses WHERE courseName = '" . $c . "'");
					$a = mysql_fetch_assoc($course);
					if ($course != FALSE && empty($a)) {//Create the new course
						$year = date('y');
						$month = date('m');
						$day = date('d');
						$startDate = "20" . $year . "-" . $month . "-" . $day;
						$closeDate = "20" . ($year + 1) . "-" . $month . "-" . $day;
						mysql_query("INSERT INTO courses (courseName, courseStatus, courseStartDate, courseEndDate, courseDescription, courseView, courseParticipate) VALUES('" . $c . "', 'active', '" . $startDate . "', '" . $closeDate . "', '" . $c . " on dscourse', 'members', 'members')");
						$courseId = mysql_insert_id();
						//And add it to the network
						//mysql_query("INSERT INTO networkCourses (courseID, networkID) VALUES ('" . $courseId . "', '" . $netId . "')");
					} else {
						$courseId = $a['courseID'];
					}
					//Step 3: CHECK if Discussion Exits=>courseId
					$d = $launch -> params['discID'];
					$disc = mysql_query("SELECT * FROM discussions WHERE dTitle = '" . $c . "' AND dPrompt = '" . $d . "'");
					$a = mysql_fetch_assoc($disc);
					if ($disc != FALSE && empty($a)) {
						$year = date('y');
						$month = date('m');
						$day = date('d');
						$startDate = "20" . $year . "-" . $month . "-" . $day;
						$openDate = "20" . $year . "-" . $month . "-" . $day;
						$closeDate = "20" . ($year + 1) . "-" . $month . "-" . $day;
						mysql_query("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES('" . $c . "', '" . $d . "', '" . $startDate . "', '" . $openDate . "', '" . $closeDate . "')");
						$discId = mysql_insert_id();
						//And add it to the course
						mysql_query("INSERT INTO courseDiscussions (courseID, discussionID) VALUES ('" . $courseId . "', '" . $discId . "')");
					} else {
						$discId = $a['dID'];
					}
					//Step 4: CHECK if User exists=>username
					$q = strtolower($launch -> user -> attrs['username']);
					$user = mysql_query("SELECT * FROM users WHERE username = '$q'");
					$u = mysql_fetch_assoc($user);
					if ($user != FALSE && empty($u)) {
						//Create user if necessary
						$username = strtolower($launch -> user -> attrs['username']);
						$first = $launch -> user -> attrs['firstName'];
						$last = $launch -> user -> attrs['lastName'];
						mysql_query("INSERT INTO users (username, firstName, lastName, sysRole) VALUES ('$username', '$first', '$last', 'Pariticipant')");
						$uId = mysql_insert_id();
					} else {
						$uId = $u['UserID'];
					}
					/*$netUser = mysql_query("SELECT * FROM networkUsers WHERE userID = '$uId' AND networkID = '$netId'");
					$nu = mysql_fetch_assoc($netUser);
					if ($netUser != FALSE && empty($nu)) {
						mysql_query("INSERT INTO networkUsers (userID, networkID, networkUserRole) VALUES ('$uId', '$netId', 'member')");
					}*/
					$role = $launch -> user -> attrs['role'];
					$courseRole = mysql_query("SELECT * FROM courseRoles WHERE userID = '$uId' AND courseID = '$courseId'");
					$cr = mysql_fetch_assoc($courseRole);
					if ($courseRole != FALSE && empty($cr)) {
						mysql_query("INSERT INTO courseRoles (userID, courseID, userRole) VALUES ('$uId', '$courseId', 'Student')");
					}
					//in either case update the user's courseRole to the incoming role
					$role = $launch -> user -> attrs['role'];
					if ($role == "Instructor") {
						$role = "Instructor";
					} else {
						$role = "Student";
					}
					$t = mysql_query("UPDATE courseRoles SET userRole = '$role' WHERE userID = $uId AND courseId = '$courseId'");
					//At this point we can be sure the network, course, discussion, and user exist in the DB
					$launch->user->attrs['uID'] = $uId;
					$launch->props['discID']  = $discId;
					$launch->props['courseId'] = $courseId; 
					return $launch;
				}
			}
		}
	}

}// Closing class

$dscourse = new Dscourse();
