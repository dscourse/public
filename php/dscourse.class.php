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
		global $pdo;
		/*
		 *  Gets only user ID and names
		 */
		//$userData = mysql_query("SELECT UserID, firstName, lastName, username, userPictureURL FROM users ORDER BY firstName ASC");
		$userData = $pdo->query("SELECT UserID, firstName, lastName, username, userPictureURL FROM users ORDER BY firstName ASC");
		// Get all the data from the users table
		$users = $userData->fetchAll();
		/*while ($r = mysql_fetch_assoc($userData)) {// Populate the rows for individual users
			$users[] = $r;
			// Add row to array
		}*/
		return $users;
	}

	///Obsolete???
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
	///Obsolete???
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
	///Obsolete???
	public function NetworkInfo($nID) {
		/*
		 *  Gets information about the network
		 */
		$query = mysql_query("SELECT * FROM networks WHERE networkID = '" . $nID . "'");
		$results = mysql_fetch_array($query);
		return $results;
	}
	
	public function GetUsers($cID) {
		global $pdo;
		/*
		 *  Gets users in a specific course
		 */
		$users = $pdo->prepare("SELECT users.UserID, users.username, users.firstName, users.lastName FROM users INNER JOIN courseRoles ON users.UserID = courseRoles.userID WHERE courseRoles.courseID = :cID");
		$users->execute(array(':cID'=>$cID));
		$results = $users->fetchAll();
		return $results;
	}
	
	public function AllUsers() {
		global $pdo;
		/*
		 *  Gets all users in the system
		 */
		$users = $pdo->query("SELECT * FROM users"); 
		//$query = mysql_query("SELECT * FROM users");
		//$results = array();

		$results = $users->fetchAll();
		/*
		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;
		*/
		return $results;
	}
	///Obsolete???
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
		global $pdo;
		/*
		 *  Gets all courses
		 */
		$courses = $pdo->query("SELECT * FROM courses"); 
		//$query = mysql_query("SELECT * FROM courses ");
		//$results = array();
		$results = $courses->fetchAll();
		/*
		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;
		*/
		return $results;
	}
	///Obsolete???
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
		global $pdo;
		/*
		 *  Gets all the information about the user
		 */
		//$query = mysql_query("SELECT * FROM users WHERE UserID = '" . $uID . "'");
		$stmt = $pdo->prepare("SELECT * FROM users WHERE UserID = :uID");
		$stmt->execute(array(':uID'=>$uID));
		//$results = mysql_fetch_array($query);
		$results = $stmt->fetch();
		return $results;
	}

	public function CourseInfo($cID) {
		global $pdo;
		/*
		 *  Gets all the information about the course
		 */
		 $courseInfo = $pdo->prepare("SELECT * FROM courses WHERE courseID = :cID");
		 $courseInfo->execute(array(':cID'=>$cID));
		//$query = mysql_query("SELECT * FROM courses WHERE courseID = '" . $cID . "'");
		//$results = mysql_fetch_array($query);
		$results = $courseInfo->fetch();
		return $results;
	}

	public function DiscussionInfo($discID) {
		global $pdo;
		/*
		 *  Gets all the information about the course
		 */
		$discInfo = $pdo->prepare("SELECT * FROM discussions INNER JOIN courseDiscussions ON discussions.dID = courseDiscussions.discussionID WHERE courseDiscussions.discussionID = :discID"); 
		$discInfo->execute(array(':discID'=>$discID));
		//$query = mysql_query("SELECT * FROM discussions INNER JOIN courseDiscussions ON discussions.dID = courseDiscussions.discussionID WHERE courseDiscussions.discussionID = '" . $discID . "'");
		//$results = mysql_fetch_array($query);
		$results = $discInfo->fetch();
		return $results;
	}

	public function DiscussionStatus($discID) {
		global $pdo;
		/*
		 *  Checks to see the status of the discussion.
		 */
		$discStatus = $pdo->prepare("SELECT * FROM discussions WHERE dID = :discID");
		//$query = mysql_query("SELECT * FROM discussions WHERE dID = '" . $discID . "'");
		$discStatus->execute(array(':discID'=>$discID));
		//$results = mysql_fetch_array($query);
		$results = $discStatus->fetch();
		
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
		global $pdo;
		/*
		*  Gets users in the network with their user information
		*/
		$cRoles = $pdo->prepare("SELECT * FROM courseRoles INNER JOIN users ON courseRoles.userID = users.UserID WHERE courseRoles.courseID = :cID");
		$cRoles->execute(array(':cID'=>$cID));
		
		$results = $cRoles->fetchAll();
		//This is kind of a hack, but works
		$set = array();
		for($i=0;$i<count($results); $i++){
			if(in_array($results[$i]['userID'], $set)){
				$results = array_slice($results,0,$i)+array_slice($results,$i);
			}
			else{
				array_push($set, $results[$i]['userID']);
			}
		}
		return $results;
	}
	
	///Obsolete???
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

	public function GetDiscPosts($discID) {
		global $pdo;
		/*
		 *  Gets the posts from this discussion.
		 */
		 $dPosts = $pdo->prepare("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = :discID");
		//$postData = mysql_query("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = '".$discID."'");
		$dPosts->execute(array(':discID'=>$discID));
		//$num_rows = mysql_num_rows($postData);
		$posts = $dPosts->fetchAll();
		/*		
		$posts = array(); 	
		if($num_rows > 0){
			$i = 0;
			while($row = mysql_fetch_array($postData)) :
				array_push($posts, $row);  // Add to the array of posts
				$i++;
			endwhile;
		}
		 */ 
		return $posts; 
	}

	public function UserCourseRole($cID, $userID) {
		global $pdo;
		/*
		 *  Gets the course role of the user for specific course
		 */
		$stmt= $pdo->prepare("SELECT userRole FROM courseRoles WHERE courseID = :cID AND userID = :userID");
		$stmt->execute(array(':cID'=>$cID,':userID'=>$userID));
		//$query = mysql_query("SELECT userRole FROM courseRoles WHERE courseID = '$cID' AND userID = '$userID'");
		//$results = mysql_fetch_array($query);
		$results = $stmt->fetch();
		return $results;
	}

	public function LoadCourse($cID, $userID) {
		global $pdo;
		/*
		 *  Checks to see if this course should be shown to the current user in different parts of the site (i.e. as listing in network.php or individual course page)
		 */

		$load = false;
		// default is to not show it

		$courseStatus = $this -> UserCourseRole($cID, $userID);
		// See if this user is in the course
		$courseInfo = $this -> CourseInfo($cID);
		if ($courseStatus[0] == 'Student' || $courseStatus[0] == 'TA' || $courseStatus[0] == 'Instructor' || $courseStatus[0] =='Viewer') {// check if this user is a member
			$load = true;
		}
		return $load;
	}
	
	//Fetch a list of all discussions for a given user, sort them by how recently they were viewed
	public function GetUserCourses($uID, $limit=8){
		global $pdo;
		
		$discussions = $pdo->prepare("SELECT * FROM courseRoles INNER JOIN courses ON courseRoles.courseID=courses.courseID WHERE userID = :uID AND courseRoles.userRole != 'Blocked'");
		$discussions->execute(array(':uID'=>$uID));
		$filtered = array();
		//prepared statement for getting last view
		$lastView = $pdo->prepare("SELECT logTime FROM logs WHERE logAction = 'view' AND logPageID IN(SELECT discussionID from courseDiscussions WHERE courseID = :c) ORDER BY logTime DESC LIMIT 1");
		while($row = $discussions->fetch()){
			$last = $row['courseRoleTime'];
			$c = $row['courseID'];
			$lastView->execute(array(':c'=>$c));
			if(!count($r= $lastView->fetch())>0){
				$last = $r['logTime'];
			}
			array_push($filtered, $row + array('lastView'=>$last));
		}
		usort($filtered, function($a,$b){
			return strtotime($b['lastView']) - strtotime($a['lastView']);
		});
		if($limit!="none")
			$filtered = array_slice($filtered,0,$limit);
		return $filtered;
	}
	
	public function GetIndexInfo($uID, $cLimit=-1, $dLimit=-1){
		global $pdo;
		
		$cList = array();
		$dList = array();
		//Get a list of all the user's courses
		$courses = $pdo->prepare("SELECT * FROM courseRoles INNER JOIN courses ON courseRoles.courseID=courses.courseID WHERE userID = :uID AND courseRoles.userRole != 'Blocked'");
		$courses->execute(array(':uID'=>$uID));
		//prepared statement for getting last view
		$lastView = $pdo->prepare("SELECT logs.logTime FROM logs WHERE logAction = 'view' AND logUserID = :uID AND logPageID IN(SELECT discussionID from courseDiscussions WHERE courseID = :c) ORDER BY logTime DESC LIMIT 1");
		while($row = $courses->fetch()){
			$last = $row['courseRoleTime'];
			$c = $row['courseID'];
			$lastView->execute(array(':c'=>$c, ':uID'=>$uID));
			$info = $lastView->fetch();
			if(!empty($info)){
				$last = $info['logTime'];
			}
			array_push($cList, $row + array('lastView'=>$last));
		}
		usort($cList, function($a,$b){
			return strtotime($b['lastView']) - strtotime($a['lastView']);
		});
		//Get a list of all the user's discussions
		$discs = $pdo->prepare("SELECT * FROM `discussions` INNER JOIN `courseDiscussions` ON `discussions`.`dID` = `courseDiscussions`.`discussionID` WHERE `dID` IN (SELECT `discussionID` FROM `courseDiscussions` WHERE `courseID` IN (SELECT `courseID` FROM `courseRoles` WHERE `userID` = :uID))");
		$discs->execute(array(':uID'=>$uID));
		//prepared statement for getting last view
		$lastView = $pdo->prepare("SELECT logTime FROM logs WHERE logPageID = :pID AND logUserID = :uID AND logAction='view' ORDER BY logTime DESC LIMIT 1");
		while($d = $discs->fetch()){
			$last = $d['dChangeDate'];
			$dID = $d['dID'];
			$lastView->execute(array(':pID'=>$dID,':uID'=>$uID));
			$info = $lastView->fetch();
			if(count($info) > 0){
				$last = $info['logTime'];
			}
			array_push($dList, $d+array('lastView'=>$last));					
		}
		usort($dList, function($a,$b){
			return strtotime($b['lastView']) - strtotime($a['lastView']);
		});
		if($cLimit>0)
			$cList = array_slice($cList,0,$cLimit);
		if($dLimit>0)
			$dList = array_slice($dList,0,$dLimit);
		return array('courseList'=>$cList,'discList'=>$dList);
	}

	public function GetCourseDiscussions($cID) {
		global $pdo;
		/*
		 *  Gets the list of discussions in this course
		 */
		 $stmt = $pdo->prepare("SELECT * FROM courseDiscussions INNER JOIN discussions ON courseDiscussions.discussionID = discussions.dID WHERE courseDiscussions.courseID = :cID");
		 $stmt->execute(array(':cID'=>$cID));
		 
		//$query = mysql_query("SELECT * FROM courseDiscussions INNER JOIN discussions ON courseDiscussions.discussionID = discussions.dID WHERE courseDiscussions.courseID = '" . $cID . "'");
		//$results = array();
		$results = $stmt->fetchAll();
		/*
		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;
		*/
		return $results;
	}

	public function GetDiscussionCourses($discID) {
		global $pdo;
		/*
		 *  Gets the list of discussions in this course
		 */
		$stmt = $pdo->prepare("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = :discID");
		$stmt->execute(array(':discID'=>$discID));
		//$query = mysql_query("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = '" . $discID . "'");
		//$results = array();
		$results = $stmt->fetchAll();
		/*
		$i = 0;
		while ($row = mysql_fetch_array($query)) :
			array_push($results, $row);
			$i++;
		endwhile;
		*/
		return $results;
	}

	public function LoadDiscussion($discID, $userID) {
		global $pdo;
		/*
		 *  Checks to see whether discussion should be loaded based on user status and discussion status
		 */
		$load = false;
		// Default status is to not load the discussion
		$stmt = $pdo->prepare("SELECT * FROM courseDiscussions  WHERE discussionID = :discID");
		$stmt->execute(array(':discID'=>$discID));
		$i = 0;
		//while ($row = mysql_fetch_array($query)) :// For each of these courses...
		while ($row = $stmt->fetch()) :// For each of these courses...
			$courseStatus = $this -> UserCourseRole($row['courseID'], $userID);
			// See if this user is in the course
			$courseInfo = $this -> CourseInfo($row['courseID']);
			//see if the courseRole is OK
			if ($courseStatus[0] == 'Student' || $courseStatus[0] == 'TA' || $courseStatus[0] == 'Instructor' || $courseStatus[0] == 'Viewer' || $courseStatus[0] == 'Member') {// check if this user is a member
				$load = true;
			}
			$i++;
		endwhile;

		return $load;
	}

	public function CountPosts($discID) {
		global $pdo;
		/*
		 *  Counts total number of posts in discussion
		 */

		$stmt = $pdo->prepare("SELECT COUNT(discussionPostID) FROM discussionPosts WHERE discussionID = :discID");
		//$query = mysql_query("SELECT discussionPostID FROM discussionPosts WHERE discussionID = '" . $discID . "'");
		$stmt->execute(array(':discID'=>$discID));
		//$num_rows = mysql_num_rows($query);
		$num_rows = $stmt->fetch();
		return $num_rows[0];

	}

	public function LoadCourseOptions($cID) {
		global $pdo;
		/*
		 *  Gets all course options
		 */
		 $ops = $pdo->prepare("SELECT * FROM options WHERE optionsType = 'course' AND optionsTypeID = :cID");
		 $ops->execute(array(':cID'=>$cID));
		//$query = mysql_query("SELECT * FROM options WHERE optionsType = 'course' AND optionsTypeID = '" . $cID . "'");
		if (!$ops) {
			return 'empty';
		} else {
			$results = $ops->fetchAll();
			/*$results = array();
			$i = 0;
			while ($row = mysql_fetch_array($query)) :
				array_push($results, $row);
				$i++;
			endwhile;
			 * */
			return $results;
		}
	}

	public function OptionValue($array, $optionName, $attr = '') {
		/*
		 *  Prints out the value of options. Needs array to include the option name as given
		 */
		//if(!isset($attr)){ $attr = ''; };  // If attr is not set
		$value = '';
		$default = TRUE;
		if (!empty($array)) {
			$total = count($array);
			for ($i = 0; $i < $total; $i++) {
				if ($array[$i]['optionsName'] == $optionName) {
					$value = $array[$i]['optionsValue'];
					$default = FALSE;
					if ($attr == 'viewAttr' || $attr == 'registerAttr') {
						$attrs = json_decode($array[$i]['optionAttr']);
						//return $attrs;
						if ($attrs -> {'active'} == 'true') {
							$value = 'On';
						} else {
							$value = 'Off';
						}
					}
				}
			}
		}

		if ($default) {// If we need to load defaults
			switch ($optionName) {
				case 'useTimeline' :
					$value = 'Yes';
					//Defaults for each value. We need to hardcode and it needs to fit what the options page prints.
					break;
				case 'charLimit' :
					$value = '500';
					//Defaults for each value. We need to hardcode and it needs to fit what the options page prints.
					break;
				case 'useSynthesis' :
					$value = 'Yes';
					//Defaults for each value. We need to hardcode and it needs to fit what the options page prints.
					break;
				case 'showInfo' :
					$value = 'Yes';
					//Defaults for each value. We need to hardcode and it needs to fit what the options page prints.
					break;
				case 'studentCreateDisc' :
					$value = 'No';
					//Defaults for each value. We need to hardcode and it needs to fit what the options page prints.
					break;
			}
			switch ($attr) {
				case 'viewAttr' :
					$value = 'Off';
					//Defaults for each value. We need to hardcode and it needs to fit what the options page prints.
					break;
				case 'registerAttr' :
					$value = 'Off';
					//Defaults for each value. We need to hardcode and it needs to fit what the options page prints.
					break;
			}
		}
		return $value;
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
			case 3 : 
				$message['content'] = "Course is created.";
				$message['icon'] = "A";
				$message['color'] = "#999";
				$message['error'] = "true";
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

	public function PreProcess($query, $isIndex = FALSE) {
		global $pdo;
		
		$query = ltrim($query, '/');
		$parts = explode('?', $query);
		$q = "";
		$args = array();
		if (isset($parts[1])) {
			$q = "?" . $parts[1];
			foreach (explode("&", $parts[1]) as $part) {
				$sides = explode("=", $part);
				$args[$sides[0]] = $sides[1];
			}
		}
		//1. CHECK STATUS
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
					header('Location: index.php'.$q);
				} else {
					echo "Error: Could not load user info from cookie.";
				}
			} else {
					
				if($isIndex){
					header('Location: info.php' . $q);
					
				} else {
					header('Location: login.php?r=' . $query . '');					
				}						
				exit;
				// Not logged and and does not have cookie
			}
		}
		//___________________________________________________
		//At this point we can assume the user is logged in, that they are a dscourse member, and that the account is active
		$dsMember = TRUE;
		$cMember = FALSE;
		$uID = $_SESSION['UserID'];
		$role = "";
		$viewer = FALSE;
		$register = FALSE;
		$courseOptions = array();
		
		$roles = array("Instructor", "TA", "Student", "Viewer", "Blocked");
	
		//Check what the user is trying to access
		$location = $parts[0];
		$location = explode('/', $location);
		$location = array_pop($location);
		
		if($isIndex || $location == "addcourse.php" || preg_match('/profile/i', $location)){
			return;
		}
		
		$cID = $args['c'];
		// Check courseRole 
		$roleQuery = $pdo->prepare("SELECT * FROM courseRoles WHERE userID = :uID AND courseID = :cID");
		$roleQuery->execute(array(':uID'=>$uID, ':cID'=>$cID));
		//$a = mysql_query("SELECT * FROM courseRoles WHERE userID = '$uID' AND courseID = '$cID'");
		$res = $roleQuery->fetch();
		//$res = mysql_fetch_assoc($a);
		if (empty($res)) {
			$role = "none";
			$cMember = FALSE;
		} else {
			$cMember = TRUE;
			$role = $res['userRole'];
			if(preg_match('/blocked/i', $role)){
				header('Location: info.php');
			}
		}

		//we only need to protect courses and discussions
		if ($location == "course.php" || $location == "discussion.php") {
			//exit("OK");
			//2. Check User Permission (based on qs)
			if (isset($args['a'])) {
				$accessCode = $args['a'];
				//Check if a code exists
				//if yes then check courseRole, and see if it matches the code type
				//if a courseRole doesn't already exist, add it
				//promotion is allowed for all but blocked users
				$perm = $pdo->prepare("SELECT * FROM options WHERE optionsValue = :accessCode");
				$perm->execute(array(':accessCode'=>$accessCode));
				$res = $perm->fetch();
				if ($res) {
					$attrs = json_decode($res['optionAttr'], TRUE);
					$params = array(':cID'=>$cID,':uID'=>$uID);
					if ($res['optionsName'] == "viewCode") {
						if ($attrs['active'] == 'true') {
							$viewer = TRUE;
						} else {
							$viewer = FALSE;
						}
						if($viewer){
							if(!$cMember){
								$view = $pdo->prepare("INSERT INTO courseRoles (courseID, userID, userRole) VALUES (:cID, :uID, 'Viewer')"); 		
								$view->execute($params);
								$cMember = TRUE;
								$role = "Viewer";
							}
						}
					} else if ($res['optionsName'] == "registerCode") {
						if ($attrs['active'] == 'true') {
							$register = TRUE;
						} else {
							$register = FALSE;
						}
						if($register){
							if(!$cMember){
								$reg = $pdo->prepare("INSERT INTO courseRoles (courseID, userID, userRole) VALUES (:cID, :uID, 'Student')");
								$reg->execute($params);
								$cMember = TRUE;
							}
							else{
								if(array_search($role, $roles) > 2){
									$up = $pdo->prepare("UPDATE courseRoles SET userRole='Student' WHERE (courseID=:cID AND userID=:uID)");
									$up->execute($params);
									$role = "Student";
								}
							}
						}
					}
				}
			}
			
			//also get options
			$ops = $pdo->prepare("SELECT * from options WHERE NOT (optionsName = 'viewCode' OR optionsName = 'registerCode')  AND optionsTypeID = :cID");
			$ops->execute(array(':cID'=>$cID));
			//$a = mysql_query("SELECT * from options WHERE NOT (optionsName = 'viewCode' OR optionsName = 'registerCode')  AND optionsTypeID = '$cID'");
			while ($res = $ops->fetch()) {
				$courseOptions[$res['optionsName']] = $res['optionsValue'];
			}
			if(empty($courseOptions)){
				$f = file_get_contents("php/courseOptions.json");
				$defaults = json_decode($f, true);
				$courseOptions = $defaults;				
			}
			if(!$cMember){
				//User lacks valid access code and is not a course member
				header("Location: info.php");
			}
			else{
				//user is a course member
				if($role=="Viewer")
					$viewer=TRUE;
			}
		}
		//Resolve these variables into a single, meaningful status (and options)
		$status = "";
		//if not a course member
		if(!$viewer){
			$status = "OK";
		}
		else {
			$status = "VIEW";
		}
	
		return array("status"=>$status, "role"=>$role, "options"=>$courseOptions);
	}

	public function LTI($from) {
		global $pdo;
		//we serve two flavors of LTI
		//1. Enter through disc, get one continuous discussion		
		//2. Enter through course, get course-level access
			
		if($_SERVER['REQUEST_METHOD'] == "POST"){
				$courseId;
				$LTI = TRUE;
				include_once "lti.php";
				$launch = parseLTIrequest(http_build_query($_REQUEST));
				if (!$launch) {
					return FALSE;
				}
				//include_once "data.php";
				// CHECK if User exists
				$uId;
				$q = strtolower($launch -> user -> attrs['username']);
				$user = $pdo->prepare("SELECT * FROM users WHERE username = :q");
				$user->execute(array(':q'=>$q));
				//$user = mysql_query("SELECT * FROM users WHERE username = '$q'");
				//$u = mysql_fetch_assoc($user);
				$u = $user->fetch();
				if (!$u) {
					//Create user if necessary
					$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
					$dummy = "";
					for($i=0;$i<6;$i++){
						$dummy.=$chars[rand(0,count($chars)-1)];
					}
					$username = strtolower($launch -> user -> attrs['username']);
					$first = $launch -> user -> attrs['firstName'];
					$last = $launch -> user -> attrs['lastName'];
					$pass = md5(mysql_real_escape_string($dummy));
					$create = $pdo->prepare("INSERT INTO users (username, password, firstName, lastName, sysRole, userStatus) VALUES (:username, :pass, :first, :last, 'Member', 'active')");
					//mysql_query("INSERT INTO users (username, password, firstName, lastName, sysRole, userStatus) VALUES ('$username', '$pass', '$first', '$last', 'Member', 'active')");
					$create->execute(array(':username'=>$username,':pass'=>$pass, ':first'=>$first,':last'=>$last));
					$uId = $pdo->lastInsertId();
					
					require_once '../mail/class.phpmailer.php';
					require_once '../mail/mail_init.php';
					$mail = new PHPMailer();
					$mail = mail_init($mail);
					$body = file_get_contents('../mail/templates/ltiregister.html');
					$head = "Hi $first,";
					$msg = "An account on the dscourse discussion platform has been automatically created for you. A temporary password has been set up with your account. Your temporary password is: $dummy. \n\n Please change this password next time you log on to dscourse. Please not that any time you enter dscourse through your institution's LMS you will not need to use a password. Thank you!";
					$body = str_replace('%head%',$head, $body);
					$body = str_replace('%message%', $msg, $body);
					$mail->MsgHTML($body);
					$mail->Subject = 'Welcome to dscourse.org';
					$mail->AddAddress($username, $first.' '.$last);
					
					//$to = $username;
			 		//$subject = "Welcome to dscourse";
					//$msg = "An account on the dscourse discussion platform has been automatically created for you. A temporary password has been set up with your account. Your temporary password is: $dummy. \n\n Please change this password next time you log on to dscourse. Thank you!";
					//$headers = "From: admin@dscourse.org";
		 			if(!$mail->Send()){
						echo $mail->ErrorInfo;
					}
				} else {
					$uId = $u['UserID'];
				}
				//CHECK if Course Exists
				$c = $launch -> params['courseName'];
				$hash = $launch->params['courseHash'];
				$course = $pdo->query("SELECT * FROM courses WHERE courseHash = '$hash'");
				//$course = mysql_query("SELECT * FROM courses WHERE courseHash = '$hash'");
				$a = $course->fetch(); 
				if ($course != FALSE && empty($a)) {//Create the new course
					$year = date('y');
					$month = date('m');
					$day = date('d');
					$startDate = "20" . $year . "-" . $month . "-" . $day;
					$closeDate = "20" . ($year + 1) . "-" . $month . "-" . $day;
					$create = $pdo->prepare("INSERT INTO courses (courseName, courseHash, courseStatus, courseStartDate, courseEndDate, courseDescription, courseView, courseParticipate) VALUES(:course, :hash, 'active', :startDate, :closeDate, :description, 'members', 'members')");
					//mysql_query("INSERT INTO courses (courseName, courseHash, courseStatus, courseStartDate, courseEndDate, courseDescription, courseView, courseParticipate) VALUES('$c', '$hash', 'active', '$startDate', '$closeDate', '$c on dscourse', 'members', 'members')");
					$create->execute(array(':course'=>$c,':hash'=>$hash,':startDate'=>$startDate,':closeDate'=>$closeDate,':description'=>"$c on dscourse.org"));
					$courseId = $pdo->lastInsertId();
					GenerateCodes($courseId);
					//And add it to the network
					//mysql_query("INSERT INTO networkCourses (courseID, networkID) VALUES ('" . $courseId . "', '" . $netId . "')");
					//Intialize default options 
					$ops = array("charLimit"=>500,"useTimeline"=>"Yes","useSynthesis"=>"Yes","showInfo"=>"Yes",	"studentCreateDisc"=>"Yes");	
					$options = $pdo->prepare("INSERT INTO options (optionsType, optionsTypeID ,optionsName ,optionsValue, optionAttr) VALUES('course', :courseId, :op, ':val, '')");
					foreach($ops as $op=>$val){
						//$q = mysql_query("INSERT INTO options (optionsType, optionsTypeID ,optionsName ,optionsValue, optionAttr) VALUES('course', $courseId, '$op', '$val', '')");
						$options->execute(array(':courseId'=>$courseId,':op'=>$op,':val'=>$val));
					}
				} else {
					$courseId = $a['courseID'];
				}
				//Make sure userRole is correct
				$role = $launch -> user -> attrs['role'];
				$courseRole = $pdo->prepare("SELECT * FROM courseRoles WHERE userID = :uID AND courseID = :courseId");
				$params = array(':uId'=>$uId, ':courseId'=>$courseId);
				$courseRole->execute($params);
				//$courseRole = mysql_query("SELECT * FROM courseRoles WHERE userID = '$uId' AND courseID = '$courseId'");
				$cr = $courseRole->fetch();
				if (empty($cr)) {
					$addRole = $pdo->prepare("INSERT INTO courseRoles (userID, courseID, userRole) VALUES (:uID, :courseID, 'Student')");
					$addRole->execute($params);
					//$q= mysql_query("INSERT INTO courseRoles (userID, courseID, userRole) VALUES ($uId, $courseId, 'Student')");					
				}
				//in either case update the user's courseRole to the incoming role
				$role = $launch -> user -> attrs['role'];
				if ($role == "Instructor") {
					$role = "Instructor";
				} else {
					$role = "Student";
				}
				$up = $pdo->prepare("UPDATE courseRoles SET userRole = :role WHERE userID = :uId AND courseId = :courseId"); 
				$up->execute($params+array(':role'=>$role));
				//$t = mysql_query("UPDATE courseRoles SET userRole = '$role' WHERE userID = $uId AND courseId = '$courseId'"); 
				//Only if entering throught discussion.php; otherwise users should maunally create discussions
				if($from == "discussion"){
					//CHECK if Discussion Exits
					$d = $launch -> params['discID'];
					$disc = $pdo->prepare("SELECT * FROM discussions WHERE dTitle = :c AND dPrompt = :d");
					$disc->execute(array(':c'=>$c,':d'=>$d));
					//$disc = mysql_query("SELECT * FROM discussions WHERE dTitle = '" . $c . "' AND dPrompt = '" . $d . "'");
					$a = $disc->fetch();
					if (empty($a)) {
						$year = date('y');
						$month = date('m');
						$day = date('d');
						$startDate = "20" . $year . "-" . $month . "-" . $day;
						$openDate = "20" . $year . "-" . $month . "-" . $day;
						$closeDate = "20" . ($year + 1) . "-" . $month . "-" . $day;
						$addDisc = $pdo->prepare("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES(:c, :d, :startDate, :openDate, :closeDate)");
						$addDisc->execute(array(':c'=>$c, ':d'=>$d, ':startDate'=>$startDate,':openDate'=>$openDate,':closeDate'=>$closeDate));
						//mysql_query("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES('" . $c . "', '" . $d . "', '" . $startDate . "', '" . $openDate . "', '" . $closeDate . "')");
						$discId = $pdo->lastInsertId();
						//And add it to the course
						$addToCourse = $pdo->prepare("INSERT INTO courseDiscussions (courseID, discussionID) VALUES (:courseId, :discId)");
						$addToCourse->execute(array(':courseId'=>$courseId,':discId'=>$discId));
						//mysql_query("INSERT INTO courseDiscussions (courseID, discussionID) VALUES ('" . $courseId . "', '" . $discId . "')");
					} else {
						$discId = $a['dID'];
					}
				}
				else{
					$discId = -1;
				}
				//At this point we can be sure the network, course, discussion, and user exist in the DB
				$launch -> user -> attrs['uID'] = $uId;
				$launch -> props['discID'] = $discId;
				$launch -> props['courseId'] = $courseId;
				$_SESSION['LTI'] = $from;
				return $launch;
			}
		return FALSE;
	}

	public function SuperSort($source, $filter = FALSE, $comparator, $limit = FALSE) {
		$result = array();
		foreach ($source as $entry) {
			if ($filter === FALSE || $filter($entry)) {
				array_push($result, $entry);
			}
		}
		usort($result, $comparator);
		if ($limit !== FALSE)
			$result = array_slice($result, 0, $limit);
		return $result;
	}
	
	public function GetRecentActivity($user = null, $length = 20){
		global $pdo;
		
		if($user==null){
			$user = ((isset($_SESSION['UserID']))?$_SESSION['UserID']:FALSE);
			}
		if($user==FALSE){
			return -1;
			}
		
		$actions = array();
		
		$courses = $pdo->prepare("SELECT courses.courseID, courses.courseName FROM courses INNER JOIN courseRoles on courses.courseID = courseRoles.courseID WHERE courseRoles.userRole != 'Blocked' AND courseRoles.userID = :user");
		if(!$courses->execute(array(':user'=>$user)))
			return -1;
		// now we have an array of [courseID, courseName]
		//prepare some statements for the big loop
		$discs = $pdo->prepare("SELECT dID, dTitle, courseDiscussions.courseDiscussionTime FROM discussions INNER JOIN courseDiscussions on discussions.dID = courseDiscussions.discussionID WHERE courseDiscussions.courseID = :cID");
		$last = $pdo->prepare("SELECT logTime from logs WHERE logAction = 'view' AND logUserID = :user AND logPageID = :dID ORDER BY logTime DESC LIMIT 1");
		$posts = $pdo->prepare("SELECT postAuthorID, postMessage, postTime, users.firstName, postID, postType FROM posts INNER JOIN users ON posts.postAuthorID = users.userID WHERE postID IN (SELECT logActionID FROM logs INNER JOIN discussionPosts ON discussionPosts.postID = logs.logActionID WHERE logs.logAction = 'addPost' AND logs.logTime > :lastView AND logs.logPageID = :dID)");
		while($row = $courses->fetch()){
			$cID = $row['courseID'];
			if(!$discs->execute(array(':cID'=>$cID)))
				return -1;
			while($d_row = $discs->fetch()){
				$dID = $d_row['dID'];
				//find out when the user last visited this disucssion
				$last->execute(array(':user'=>$user,':dID'=>$dID));
				$lastView = $last->fetch();
				$lastView = $lastView['logTime'];
				if(empty($lastView)){
					$lastView = $d_row['courseDiscussionTime'];					
				}
				$posts->execute(array(':lastView'=>$lastView,':dID'=>$dID));
				while($p_row = $posts->fetch()){
					$path = "/discussion.php?c=".$cID."&d=".$dID."&p=".$p_row['postID'];
					array_push($actions, array("action" => 'post',"actionTime" =>$p_row['postTime'], "actionType" =>$p_row['postType'], "context" =>'discussion', "contextLabel" =>$d_row['dTitle'] ,"contextID" =>$dID, "agentLabel" => $p_row['firstName'], "agentID" =>$p_row['postAuthorID'], "content" =>substr($p_row['postMessage'],0,120), "actionPath" =>$path)); 
				}
			}
		}
		usort($actions, function($a,$b){
			return strtotime($b['actionTime']) - strtotime($a['actionTime']);
		});
		$actions = array_slice($actions,0,$length);
		
		return $actions;
	}

	public function GetLastView($disc, $user){
		global $pdo;
		
		$last = $pdo->prepare("SELECT logTime FROM logs WHERE logUserID = :uID AND logPageID = :discID AND logAction = 'view' ORDER BY logTime DESC LIMIT 1");
		$last->execute(array(':uID'=>$user, ':discID'=>$disc));
		$result = $last->fetch();
		if(empty($result)){
			$result['logTime']='never';
		}
		return $result['logTime'];
	}
	
	public function GetNotificationSettings($uID){
		global $pdo;
		
		$settings = $pdo->prepare("SELECT * FROM options WHERE optionsType = 'user' AND optionsTypeID = :uID");
		$settings->execute(array(':uID'=>$uID));
		
		$triggers = array('comment','agree','disagree','clarify','offTopic', 'mention');
		$notifications = array();
		while($row = $settings->fetch()){
			$opt = $row['optionsName'];
			$opt = explode('_',$opt);
			$opt = $opt[2];
			$notifications[$opt] = $row['optionsValue'];	
		}
		foreach($triggers as $trigger){
			if(!isset($notifications[$trigger]))
				$notifications[$trigger] = 0;
		}
		return $notifications;
	}
	
}// Closing class

$dscourse = new Dscourse();
?>