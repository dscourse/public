<?php
/*
 * Dscourse php functions that do not use the ajax calls. These are incorporated as methods under the Dscourse class which gets assigned on the php pages.  
 *
 */
date_default_timezone_set('UTC');
class Dscourse {
	
	public function __construct() {

	}

	/*
	 *  Gets only user ID and names
	 */	 
	public function GetUserMini() {
		global $pdo;
		$userData = $pdo->query("SELECT UserID, firstName, lastName, username, userPictureURL FROM users ORDER BY firstName ASC");
		// Get all the data from the users table
		$users = $userData->fetchAll();
		return $users;
	}

	/*
	 *  Gets users in the network with their user information
	 */
	public function GetUsers($cID) {
		global $pdo;
		$users = $pdo->prepare("SELECT users.UserID, users.username, users.firstName, users.lastName FROM users INNER JOIN courseRoles ON users.UserID = courseRoles.userID WHERE courseRoles.courseID = :cID");
		$users->execute(array(':cID'=>$cID));
		$results = $users->fetchAll();
		return $results;
	}

	/*
	 *  Gets all users in the system
	 */
	public function AllUsers() {
		global $pdo;
		$users = $pdo->query("SELECT * FROM users"); 
		$results = $users->fetchAll();
		return $results;
	}

	/*
	 *  Gets all courses
	 */	
	public function AllCourses() {
		global $pdo;
		$courses = $pdo->query("SELECT * FROM courses"); 
		$results = $courses->fetchAll();
		return $results;
	}

	/*
	 *  Gets all the information about the user
	 */
	public function UserInfo($uID) {
		global $pdo;
		$stmt = $pdo->prepare("SELECT * FROM users WHERE UserID = :uID");
		$stmt->execute(array(':uID'=>$uID));
		$results = $stmt->fetch();
		return $results;
	}

	/*
	 *  Gets all the information about the course
	 */
	public function CourseInfo($cID) {
		global $pdo;
		 $courseInfo = $pdo->prepare("SELECT * FROM courses WHERE courseID = :cID");
		 $courseInfo->execute(array(':cID'=>$cID));
		$results = $courseInfo->fetch();
		return $results;
	}

	/*
	 *  Gets all the information about the course
	 */
	public function DiscussionInfo($discID) {
		global $pdo;
		$discInfo = $pdo->prepare("SELECT * FROM discussions INNER JOIN courseDiscussions ON discussions.dID = courseDiscussions.discussionID WHERE courseDiscussions.discussionID = :discID"); 
		$discInfo->execute(array(':discID'=>$discID));
		$results = $discInfo->fetch();
		return $results;
	}

	/*
	 *  Checks to see the status of the discussion.
	 */
	public function DiscussionStatus($discID) {
		global $pdo;
		$discStatus = $pdo->prepare("SELECT * FROM discussions WHERE dID = :discID");
		$discStatus->execute(array(':discID'=>$discID));
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

	/*
	 *  Gets users in the network with their user information
	 */
	public function CourseRoles($cID) {
		global $pdo;
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
	
	/*
	 *  Gets the posts from this discussion.
	 */
	public function GetDiscPosts($discID) {
		global $pdo;
		 $dPosts = $pdo->prepare("SELECT * FROM discussionPosts INNER JOIN posts ON discussionPosts.postID = posts.postID WHERE discussionPosts.discussionID = :discID");
		$dPosts->execute(array(':discID'=>$discID));
		$posts = $dPosts->fetchAll();
		return $posts; 
	}

	/*
	 *  Gets the course role of the user for specific course
	 */
	public function UserCourseRole($cID, $userID) {
		global $pdo;
		$stmt= $pdo->prepare("SELECT userRole FROM courseRoles WHERE courseID = :cID AND userID = :userID");
		$stmt->execute(array(':cID'=>$cID,':userID'=>$userID));
		$results = $stmt->fetch();
		return $results;
	}

	/*
	 *  Checks to see if this course should be shown to the current user in different parts of the site (i.e. as listing in network.php or individual course page)
	 */
	public function LoadCourse($cID, $userID) {
		global $pdo;
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

	/*
	 *  Fetch a list of all discussions for a given user, sort them by how recently they were viewed
	 */
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

	/*
	 *  Get a list of discussions and courses that are shown in the index.php page.
	 */	
	public function GetIndexInfo($uID, $cLimit=-1, $dLimit=-1){
		global $pdo;
		$cList = array();
		$dList = array();
		//Get a list of all the user's courses
		$courses = $pdo->prepare("SELECT * FROM courseRoles INNER JOIN courses ON courseRoles.courseID = courses.courseID WHERE userID = :uID AND (courseRoles.userRole != 'Blocked' AND courseRoles.userRole != 'Delete')");
		$courses->execute(array(':uID'=>$uID));
		//prepared statement for getting last view
		$lastView = $pdo->prepare("SELECT logs.logTime FROM logs WHERE logAction = 'view' AND logUserID = :uID AND logPageID IN(SELECT discussionID from courseDiscussions WHERE courseID = :c) ORDER BY logTime DESC LIMIT 1");
		while($row = $courses->fetch()){
			$last = $row['courseRoleTime'];
			$c = $row['courseID'];
			$lastView->execute(array(':c'=>$c, ':uID'=>$uID));
			$info = $lastView->fetch();
			if(count($info) > 0){
				$last = $info['logTime'];
			}
			array_push($cList, $row + array('lastView'=>$last));
		}
		usort($cList, function($a,$b){
			return strtotime($b['lastView']) - strtotime($a['lastView']);
		});
		//Get a list of all the user's discussions
		$discs = $pdo->prepare("SELECT * FROM `discussions` INNER JOIN `courseDiscussions` ON `discussions`.`dID` = `courseDiscussions`.`discussionID` WHERE `dID` IN (SELECT `discussionID` FROM `courseDiscussions` WHERE `courseID` IN (SELECT `courseID` FROM `courseRoles` WHERE `userID` = :uID AND (courseRoles.userRole != 'Blocked' AND courseRoles.userRole != 'Delete')))");
		$discs->execute(array(':uID'=>$uID));
		//prepared statement for getting last view
		$lastView = $pdo->prepare("SELECT logTime FROM logs WHERE logPageID = :pID AND logUserID = :uID AND logAction='view' ORDER BY logTime DESC LIMIT 1");
		while($d = $discs->fetch()){
			$last = 0;
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

	/*
	 *  Gets the list of discussions in this course
	 */
	public function GetCourseDiscussions($cID) {
		global $pdo;
		 $stmt = $pdo->prepare("SELECT * FROM courseDiscussions INNER JOIN discussions ON courseDiscussions.discussionID = discussions.dID WHERE courseDiscussions.courseID = :cID");
		 $stmt->execute(array(':cID'=>$cID));
		$results = $stmt->fetchAll();
		return $results;
	}

	/*
	 *  Gets the list of discussions in this course
	 */
	public function GetDiscussionCourses($discID) {
		global $pdo;
		$stmt = $pdo->prepare("SELECT * FROM courseDiscussions INNER JOIN courses ON courseDiscussions.courseID = courses.courseID WHERE courseDiscussions.discussionID = :discID");
		$stmt->execute(array(':discID'=>$discID));
		$results = $stmt->fetchAll();
		return $results;
	}

	/*
	 *  Checks to see whether discussion should be loaded based on user status and discussion status
	 */
	public function LoadDiscussion($discID, $userID) {
		global $pdo;
		$load = false;
		// Default status is to not load the discussion
		$stmt = $pdo->prepare("SELECT * FROM courseDiscussions  WHERE discussionID = :discID");
		$stmt->execute(array(':discID'=>$discID));
		// Get courses this discussion belongs to
		$i = 0;
		while ($row = $stmt->fetch()) :// For each of these courses...
			$courseStatus = $this -> UserCourseRole($row['courseID'], $userID);
			// See if this user is in the course
			$courseInfo = $this -> CourseInfo($row['courseID']);
			// Who does this course allow for viewing?
			if ($courseStatus[0] == 'Student' || $courseStatus[0] == 'TA' || $courseStatus[0] == 'Instructor' || $courseStatus[0] == 'Viewer' || $courseStatus[0] == 'Member') {// check if this user is a member
				$load = true;
			}
			$i++;
		endwhile;

		return $load;
	}

	/*
	 *  Counts total number of posts in discussion
	 */
	public function CountPosts($discID) {
		global $pdo;
		$stmt = $pdo->prepare("SELECT COUNT(discussionPostID) FROM discussionPosts WHERE discussionID = :discID");
		$stmt->execute(array(':discID'=>$discID));
		$num_rows = $stmt->fetch();
		return $num_rows[0];
	}

	/*
	 *  Gets all course options
	 */
	public function LoadCourseOptions($cID) {
		global $pdo;
		 $ops = $pdo->prepare("SELECT * FROM options WHERE optionsType = 'course' AND optionsTypeID = :cID");
		 $ops->execute(array(':cID'=>$cID));
		if (!$ops) {
			return 'empty';
		} else {
			$results = $ops->fetchAll();
			$results;
		}
	}

	/*
	 *  Prints out the value of options. Needs array to include the option name as given
	 */
	public function OptionValue($array, $optionName, $attr = '') {
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

	/*
	 *  Returns message content
	 */
	public function Messages($m) {
		$message = array();
		$message['icon'] = "N";  // icon characters: http://www.fonts2u.com/entypo.font
		$message['color'] = "#333";
		$message['error'] = "false";
		switch ($m) {
			case 1 :
				$message['content'] = "Profile changes saved. ";
				$message['icon'] = ".";
				break;
			case 2 :
				$message['content'] = "Changes to the discussion were saved. ";
				$message['icon'] = "9";
				break;
			case 3 : 
				$message['content'] = "Course is created.";
				$message['icon'] = "l";
				break;
			case 4 :
				$message['content'] = "You uploaded an invalid file please try again. ";
				$message['color'] = "#999";
				$message['error'] = "true";
				$message['icon'] = "X";
				break;
			case 10 :
				$message['content'] = "Course changes were saved ";
				$message['icon'] = "W";
				break;
			case 'e' :
				$message['content'] = "There was an error reaching the database. Your changes were not saved.";
				$message['color'] = "#999";
				$message['error'] = "true";
				$message['icon'] = "X";
				break;
			case 'd' :
				$message['content'] = "Your discussion was added to this course.";
				$message['icon'] = ":";
				break;
		}
		return $message;
	}

	/*
	 *  Truncates string
	 */
	function myTruncate($string, $limit, $break = ".", $pad = "...") {
	// Original PHP code by Chirp Internet: http://www.the-art-of-web.com/php/truncate/
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

	/*
	 *  Checks permissions at entry to the site
	 */
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
				$cookie = $pdo->prepare("SELECT * FROM users WHERE UserID = :id");
				$cookie->execute(array(':id'=>$_COOKIE['userCookieDscourse']));
				$res = $cookie->fetch();
				if (!empty($res)) {
					$row = $res;
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
		$role = $pdo->prepare("SELECT * FROM courseRoles WHERE userID = :uID AND courseID = :cID");
		$role->execute(array(':uID'=>$uID, ':cID'=>$cID));
		//$a = mysql_query("SELECT * FROM courseRoles WHERE userID = '$uID' AND courseID = '$cID'");
		$res = $role->fetch();
		//$res = mysql_fetch_assoc($a);
		if (empty($res)) {
			$cMember = FALSE;
		} else {
			$cMember = TRUE;
			$role = $res['userRole'];
			if($role == "blocked"){
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

	/*
	 *  This function is moved to data.php
	 */	
	// public function GetRecentActivity($user = null, $length = 20){
// 		global $pdo;
// 		
// 		if($user==null){
// 			$user = ((isset($_SESSION['UserID']))?$_SESSION['UserID']:FALSE);
// 			}
// 		if($user==FALSE){
// 			return -1;
// 			}
// 		
// 		$actions = array();
// 		
// 		$courses = $pdo->prepare("SELECT courses.courseID, courses.courseName FROM courses INNER JOIN courseRoles on courses.courseID = courseRoles.courseID WHERE courseRoles.userRole != 'Blocked' AND courseRoles.userID = :user");
// 		if(!$courses->execute(array(':user'=>$user)))
// 			return -1;
// 		// now we have an array of [courseID, courseName]
// 		//prepare some statements for the big loop
// 		$discs = $pdo->prepare("SELECT dID, dTitle, courseDiscussions.courseDiscussionTime FROM discussions INNER JOIN courseDiscussions on discussions.dID = courseDiscussions.discussionID WHERE courseDiscussions.courseID = :cID");
// 		$last = $pdo->prepare("SELECT logTime from logs WHERE logAction = 'view' AND logUserID = :user AND logPageID = :dID ORDER BY logTime DESC LIMIT 1");
// 		$posts = $pdo->prepare("SELECT postAuthorID, postMessage, postTime, users.firstName, postID, postType FROM posts INNER JOIN users ON posts.postAuthorID = users.userID WHERE postID IN (SELECT logActionID FROM logs INNER JOIN discussionPosts ON discussionPosts.postID = logs.logActionID WHERE logs.logAction = 'addPost' AND logs.logTime > :lastView AND logs.logPageID = :dID) ");
// 		while($row = $courses->fetch()){
// 			$cID = $row['courseID'];
// 			if(!$discs->execute(array(':cID'=>$cID)))
// 				return -1;
// 			while($d_row = $discs->fetch()){
// 				$dID = $d_row['dID'];
// 				//find out when the user last visited this disucssion
// 				$last->execute(array(':user'=>$user,':dID'=>$dID));
// 				$lastView = $last->fetch();
// 				$lastView = $lastView['logTime'];
// 				if(empty($lastView)){
// 					$lastView = $d_row['courseDiscussionTime'];					
// 				}
// 				$posts->execute(array(':lastView'=>$lastView,':dID'=>$dID));
// 				while($p_row = $posts->fetch()){
// 					$path = "/discussion.php?c=".$cID."&d=".$dID."&p=".$p_row['postID'];
// 					array_push($actions, array("action" => 'post',"actionTime" =>$p_row['postTime'], "actionType" =>$p_row['postType'], "context" =>'discussion', "contextLabel" =>$d_row['dTitle'] ,"contextID" =>$dID, "agentLabel" => $p_row['firstName'], "agentID" =>$p_row['postAuthorID'], "content" =>substr($p_row['postMessage'],0,120), "actionPath" =>$path)); 
// 				}
// 			}
// 		}
// 		usort($actions, function($a,$b){
// 			return strtotime($b['actionTime']) - strtotime($a['actionTime']);
// 		});
// 		$actions = array_slice($actions,0,$length);
// 		
// 		return $actions;
// 	}


	/*
	 *  Get the last time the logged in user viewed a discussion
	 */
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

	/*
	 *  Load user's settings for notifications 
	 */	
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