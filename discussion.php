<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
	date_default_timezone_set('UTC');
    
   	//CHECK IF LTI
	$LTI = FALSE;
	$netId;
	$courseId;
	$discId;
	$uId;
	$origin; 
	if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(array_key_exists('HTTP_ORIGIN', $_SERVER)){
		$origin = $_SERVER['HTTP_ORIGIN'];
	}
	else if(array_key_exists('HTTP_REFERER', $_SERVER)){
		$ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		$scheme = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_SCHEME);
		$origin = $scheme.'://'.$ref;
	}
	else{
		$origin = array(0);
	}
	if(count($origin)>0){
		$LTI_allowed = array('https://collab.itc.virginia.edu'=>'UVa Collab', 'http://dev.canlead.net'=>'CANLEAD');
  		if(array_key_exists($origin, $LTI_allowed)){
  			$LTI = TRUE;
			include "lti.php";
			$postData =file_get_contents("php://input");
			$launch = parseLTIrequest($postData); 
			if(!$launch){
				header('Location: info.php'); 
			}
			//Step 1: CHECK if Network Exists=>networkId
			$n = $LTI_allowed[$origin];
			$net = mysql_query("SELECT * FROM networks WHERE networkName = '".$n."'");
			$a = mysql_fetch_assoc($net);
			if($net!=FALSE && empty($a)){ //Create the new network
				$networkCode = rand(100000, 1000000000);
				mysql_query("INSERT INTO networks (networkName, networkDesc, networkCode) VALUES('".$n."', 'The ".$n." network on dscourse', '".$networkCode."')"); 
				$netId = mysql_insert_id(); 
			}
			else{
				$netId = $a['networkID'];
			}
			//Step 2: CHECK if Course Exists=>networkId
			$c = $launch->params['courseName'];
			$course = mysql_query("SELECT * FROM courses WHERE courseName = '".$c."'");
			$a = mysql_fetch_assoc($course);
			if($course!=FALSE && empty($a)){ //Create the new course
				$year = date('y');
				$month = date('m');
				$day = date('d');
				$startDate = "20".$year."-".$month."-".$day;
				$closeDate = "20".($year+1)."-".$month."-".$day;
				mysql_query("INSERT INTO courses (courseName, courseStatus, courseStartDate, courseEndDate, courseDescription, courseView, courseParticipate) VALUES('".$c."', 'active', '".$startDate."', '".$closeDate."', '".$c." on dscourse', 'members', 'members')");
				$courseId = mysql_insert_id(); 
				//And add it to the network
				mysql_query("INSERT INTO networkCourses (courseID, networkID) VALUES ('".$courseId."', '".$netId."')");
			}
			else{
				$courseId = $a['courseID'];
			}
			//Step 3: CHECK if Discussion Exits=>courseId
			$d = $launch->params['discID'];
			$disc = mysql_query("SELECT * FROM discussions WHERE dTitle = '".$c."' AND dPrompt = '".$d."'");
			$a = mysql_fetch_assoc($disc);
			if($disc!=FALSE && empty($a)){ 
				$year = date('y');
				$month = date('m');
				$day = date('d');
				$startDate = "20".$year."-".$month."-".$day;			
				$openDate = "20".$year."-".$month."-".$day;
				$closeDate = "20".($year+1)."-".$month."-".$day;
				mysql_query("INSERT INTO discussions (dTitle, dPrompt, dStartDate, dOpenDate, dEndDate) VALUES('".$c."', '".$d."', '".$startDate."', '".$openDate."', '".$closeDate."')");
				$discId = mysql_insert_id(); 
				//And add it to the course
				mysql_query("INSERT INTO courseDiscussions (courseID, discussionID) VALUES ('".$courseId."', '".$discId."')");
			}
			else{
				$discId = $a['dID'];
			}
			//Step 4: CHECK if User exists=>username 
			$q = strtolower($launch->user->attrs['username']);
			$user = mysql_query("SELECT * FROM users WHERE username = '$q'");
			$u = mysql_fetch_assoc($user);
			if($user!=FALSE && empty($u)){
				$username = strtolower($launch->user->attrs['username']);
				$first = $launch->user->attrs['firstName'];
				$last = $launch->user->attrs['lastName'];
				mysql_query("INSERT INTO users (username, firstName, lastName, sysRole) VALUES ('$username', '$first', '$last', 'pariticipant')");
				$uId = mysql_insert_id(); 
			}
			else{
				$uId = $u['UserID'];
			}
			$netUser = mysql_query("SELECT * FROM networkUsers WHERE userID = '$uId' AND networkID = '$netId'");
			$nu = mysql_fetch_assoc($netUser);
			if($netUser!=FALSE && empty($nu)){
				mysql_query("INSERT INTO networkUsers (userID, networkID, networkUserRole) VALUES ('$uId', '$netId', 'member')");
			}
			$courseRole = mysql_query("SELECT * FROM courseRoles WHERE userID = '$uId' AND courseID = '$courseId'");
			$cr = mysql_fetch_assoc($courseRole);
			if($courseRole!=FALSE && empty($cr)){
				mysql_query("INSERT INTO courseRoles (userID, courseID, userRole) VALUES ('$uId', '$courseId', 'Student')");
			}
			//At this point we can be sure the network, course, discussion, and user exist in the DB
  		}
	}
	}
	
    if(!$LTI && empty($_SESSION['Username']))                        // Checks to see if user is logged in, if not sends the user to login.php
    {  
        // is cookie set? 
        if (isset($_COOKIE["userCookieDscourse"])){
             
             $getUserInfo = mysql_query("SELECT * FROM users WHERE UserID = '".$_COOKIE["userCookieDscourse"]."' ");  
  
            if(mysql_num_rows($getUserInfo) == 1)  
            {  
                $row = mysql_fetch_array($checklogin);   
          
                $_SESSION['Username'] = $username; 
                $_SESSION['firstName'] = $row[3]; 
                $_SESSION['lastName'] = $row[4];   
                $_SESSION['LoggedIn'] = 1;  
                $_SESSION['status'] = $row[5];
                $_SESSION['UserID'] = $row[0];  
            } else {
                echo "Error: Could not load user info from cookie.";
            }
            
        } else {
            header('Location: info.php');                   // Not logged and and does not have cookie
        }
        
    }else {
    	                                               // User is logged in, show page. 
     	include_once('php/dscourse.class.php');
        $courseInfo; 
		$networkInfo;
		$discussionInfo;
		$load;
		$discID;
	if(!$LTI){
       $userID = $_SESSION['UserID'];           // Allocate userID to use throughout the page
       if(isset($_GET['d'])){                   // Check if discussion id is set. If not send them back to index
           $discID = $_GET['d']; 
           $discussionInfo = $dscourse->DiscussionInfo($discID); 
       } else {
            header("Location: index.php");  
            exit(); 
       }
       
       $cID = $_GET['c']; 
       $courseInfo = $dscourse->CourseInfo($cID);
 
       $nID = $_GET['n'];
       $networkInfo = $dscourse->NetWorkInfo($nID);
   	   $load = $dscourse->LoadDiscussion($discID, $userID, $nID); 
	}
	else{
		//CREATE A SESSION
		$_SESSION['Username'] = strtolower($launch->user->attrs['username']); 
        $_SESSION['firstName'] = $launch->user->attrs['firstName'];
        $_SESSION['lastName'] = $launch->user->attrs['lastName'];   
        $_SESSION['LoggedIn'] = 1;  
        $_SESSION['status'] = 'Student';
        $_SESSION['UserID'] = $uId; 
		
		$discID = $discId;
		$discussionInfo = $dscourse->DiscussionInfo($discId); 
		$courseInfo = $dscourse->CourseInfo($courseId);
		$networkInfo = $dscourse->NetWorkInfo($netId);
		$load = $dscourse->LoadDiscussion($discId, $uId, $netId);
	}
       if($load){
	 		$currentSession = session_id(); 
	 		 // Show content

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title> dscourse | <?php echo $discussionInfo['dTitle']; ?></title>
        <?php
		include ('php/header_includes.php');
  ?>
        <script type="text/javascript" src="js/dscourse.js"></script>

    <script type="text/javascript">
<<<<<<< HEAD
        //LTI?
			<?php echo "var lti = " . (($LTI) ? 'true' : 'false') . ";"; ?>
                // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" . $_SESSION['status'] . "';"; ?><?php echo "var currentUserID = '" . $_SESSION['UserID'] . "';"; ?><?php echo "var dUserAgent = '" . $_SERVER['HTTP_USER_AGENT'] . "';"; ?><?php echo "var discID = " . $discID . ";"; ?><?php echo "var currentSession = '" . $currentSession . "';"; ?></script>
=======

            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
            <?php echo "var discID = " .  $discID . ";"; ?>
             <?php echo "var currentSession = '" .  $currentSession . "';"; ?>
             <?php echo "var courseView = '" . $courseInfo['courseView'] . "'; "; ?>
             <?php echo "var courseParticipate = '" . $courseInfo['courseParticipate'] . "'; "; ?>
             <?php echo "var userNetworkRole = '" . $dscourse->CheckNetworkAccess($userID, $nID) . "'; "; ?>
 
    </script>
>>>>>>> c3be92ec9ccc7b9148c319534def57f7edb71e65
</head>

<body>
	<?php 
	if(!$LTI){ ?>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="network.php?n=<?php echo $nID; ?>" id="networkNav"><?php echo $dscourse -> myTruncate($networkInfo['networkName'], 15, ' ', '...'); ?></a></li>
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID . '&n=' . $nID; ?>" id="coursesNav"><?php echo $dscourse -> myTruncate($courseInfo['courseName'], 15, ' ', '...'); ?></a></li>
                    <li class="navLevel"><a href="discussion.php?d=<?php echo $discID . '&c=' . $cID . '&n=' . $nID; ?>" id="discussionNav"><?php echo $dscourse -> myTruncate($discussionInfo['dTitle'], 15, ' ', '...'); ?></a></li>

                </ul>

                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " . $_SESSION['lastName']; ?> <b class="caret"></b> </a>

                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <li><a id="profileNav" href="profile.php?u=<?php echo $_SESSION['UserID']; ?>">Profile</a></li>

                            <li><a id="helpNav" href="help.php">Help</a></li>

                            <li><a href="php/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- End of header content-->
    <?php
		}
		else{
 ?>
		<div class="navbar navbar-fixed-top">
        	<div class="navbar-inner">
        	<div class="container-fluid">
        		<span class="brand" id="homeNav">dscourse</span>
        	</div>
        	</div>
    	</div><!-- End of header content-->	
	<?php } ?>
    <div id="discussionWrap" class=" page" >
        <header class="jumbotron subhead">
            <div class="container-fluid">
                <div class="btn-toolbar" id="toolbox">
                    <button id="showTimeline" class="btn btn-small btn-info">Timeline</button> <button id="showSynthesis" class="btn btn-small btn-warning">Connected Posts</button> <button id="" class="btn btn-small btn-success sayBut2" postid="0"><i class="icon-comment icon-white"></i> Say</button> <input id="dIDhidden" type="hidden" name="discID" value="">

                    <div class="btn-group" id="participantList">
                        <button class="btn disabled ">Participants:</button>
                    </div>
                </div>

                <div class="form-search" id="keywordSearchDiv">
                    <input id="keywordSearchText" type="text" class="input-medium search-query" placeholder="Search in discussion">
                </div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row-fluid" id="controlsRow">
                <div class="span12" id="dFooter">
                    <div id="controlsWrap">
                        <div id="timeline" class="">
                            <div id="slider-range">
                                <div id="dots"></div>
                            </div><input type="text" id="amount">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-fluid" id="dRowMiddle">
                <div class="span4 ">
                    <div id="row-fluid">
                        <div class="span11" id="dSidebar">
                            <div class="dCollapse" id="dInfo">
                                <span class="boxHeaders"><span id="dTitleView"></span></span><br /> <!--
                        <div class="sayBut2" postid="0">
                            say <input id="dIDhidden" type="hidden" name="discID" value="">
                        </div>
-->

                                <div id="discStatus" class="alert"></div>
								<?php if(!$LTI) { ?>
                                <div class="content">
                                    <div id="dPromptView"></div>

                                    <div id="dCourse">
                                        <b>Course:</b>
                                    </div>

                                    <div id="dSDateView">
                                        <b>Start Date:</b>
                                    </div>

                                    <div id="dODateView">
                                        <b>Open To Class:</b>
                                    </div>

                                    <div id="dCDateView">
                                        <b>End Date:</b>
                                    </div>

                                </div>
								<?php } ?>
                                <h4>Recent Posts</h4>

                                <div class="content">
                                    <ul class="discussionFeed" id="recentContent"></ul>
                                </div>
                            </div>

                            <div class="dCollapse hide" id="dSynthesis">
                                <div class="content">
                                    <div class="hide" id="addSynthesis">
                                        <input id="sPostIDhidden" type="hidden" name="sPostIDhidden" value=""> <input id="userIDhidden" type="hidden" name="userIDhidden" value="<?php echo $_SESSION['UserID']; ?>"> 
                                        <textarea id="synthesisText">
Your synthesis comment...
</textarea>

                                        <div id="synthesisDrop" class="alert alert-info">
                                            Drag and drop posts here to add to synthesis.
                                        </div>

                                        <div id="synthesisPostWrapper"></div>
                                        
                                        <input id="addSynthesisButton" type="button" class="buttons btn btn-primary" value="Add Post"> <input id="editSynthesisSaveButton" type="button" class="buttons btn btn-primary hide" value="Edit Post"> <input id="cancelSynthesisButton" type="button" class="buttons btn btn-info" value="Cancel">
                                        <hr class="soften">
                                    </div>

                                    <ul class="synthesisFeed" id="synthesisList">
	                                    <p id="synthesisHelpText"> There are no connected posts in this discussion yet. Click on the Say button anywhere in the discussion to create one. </p>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- close span11 -->

                        <div class="span1" id="vHeatmap">
                            <div id="scrollBox"></div>
                        </div>

                        <div id="lines">
                            <canvas id="cLines"></canvas>
                        </div>
                    </div><!-- close row-fluid -->
                </div><!-- close span4 -->

                <div class="span8 " id="dMain">
                    <div id="discussionDivs">
                        <div class="levelWrapper" level="0">
                        	<img src="img/ajax-loader.gif" alt="ajax-loader" width="32" height="32" style="margin-top: 35%; margin-left: 45%">
	                        <!-- Discussion gets built here.. -->
                        </div>
                    </div>
                </div><!-- close span8 -->
            </div><!-- close row -->

            <div id="commentWrap">
                <input id="postIDhidden" type="hidden" name="postIDhidden" value=""> <input id="userIDhidden" type="hidden" name="userIDhidden" value="<?php echo $_SESSION['UserID']; ?>">

                <div id="top">
                    <div id="quick">
                        <div class="btn-group" id="postTypeID">
                            <button class="btn postTypeOptions active" id="comment"> <span class="typicn message "></span>Comment</button> <button class="btn postTypeOptions" id="agree"> <span class="typicn thumbsUp "></span> Agree</button> <button class="btn postTypeOptions" id="disagree"> <span class="typicn thumbsDown "></span> Disagree</button> <button class="btn postTypeOptions" id="clarify"> <span class="typicn unknown "></span> Ask to Clarify</button> <button class="btn postTypeOptions" id="offTopic"> <span class="typicn forward "></span> Off Topic</button>
                        </div>
                    </div>
                </div>

                <div id="middle">
                    <input id="locationIDhidden" type="hidden" name="locationIDhidden" value="">

                    <div id="commentArea">
                        <div id="highlightDirection">
                            Select a specific segment of the text to reference it in your post.
                        </div>

                        <div id="highlightShow"></div>

                        <div id="textError">
                            If you are commenting you need to enter a comment.
                        </div>
                        <textarea id="text">
Your comment...
</textarea>
<div class="pull-right">Characters: <span id="charCount">0</span> <span id="charCountTotal"></span> </div>
                    </div><button id="media" class="btn btn-small btn-danger">Add Media</button> <button id="synthesize" class="btn btn-small btn-warning">Connect</button>
                </div>

                <div id="bottom">
                    <div id="buttons">
                        <input type="button" id="postCancel" class="buttons btn btn-small btn-info" value="Cancel"> <input id="addPost" type="button" class="buttons btn btn-small btn-primary" value="Add to dscourse">
                    </div>
                </div>
            </div><!-- close commentWrap -->

            <div id="mediaBox">
                <a class="close" data-dismiss="alert" href="#" id="closeMedia">&times;</a>

                <div id="mediaTools">
                    <div id="drawGroup" class="btn-group">
                        <button class="btn btn-small drawTypes" id="Web">Link</button> <button class="btn btn-small drawTypes" id="Document">Document</button> <button class="btn btn-small drawTypes" id="Video">Video</button> <button class="btn btn-small drawTypes active" id="Drawing">Drawing</button> <button class="btn btn-small drawTypes" id="Map">Map</button>
                    </div>

                    <div id="mediaButtons" class="pull-right">
                        <button id="drawCancel" class="btn btn-info">Cancel</button> <button id="continuePost" class="btn btn-primary">Continue posting</button>
                    </div>
                </div>

                <div id="mediaWrap"></div>
            </div><!-- close mediabox -->

            <div id="displayFrame">
                <a class="close" href="#" id="closeMediaDisplay">&times;</a>

                <div id="displayDraw"></div>
            </div>
        </div><!-- close container -->
    </div><!-- End individual discussion page -->

<div id="checkNewPost"></div>
    <?php

	} else {
	 		?>
<<<<<<< HEAD
	 		<div class="alert alert-danger"> You are not authorized to view this discussion. If this is an error please contact your site administrator. </div>
	 		<?php
				}
=======
	 		<div class="alert alert-danger"> You are not authorized to view this discussion. You may have been misdirected or entered a wrong link. Also even if a network is private the course creator may have limited access to course members. If this is an error please contact your site administrator. <a href="index.php"> Go back to dscourse </a> </div>
	 		<?php 
 		}       
       
       
>>>>>>> c3be92ec9ccc7b9148c319534def57f7edb71e65

				}
    ?>  
 <script type="text/javascript">
    // Latest itiration of 22the shiva elements through iframe
    var sampleData = "{\"chartType\": \"BarChart\",\"areaOpacity\": \".3\",\"backgroundColor\": \"\",\"chartArea\": \"\",\"colors\": \"\",\"fontName\": \"Arial\",\"fontSize\": \"automatic\",\"hAxis\": \"\",\"legend\": \"right\",\"legendTextStyle\": \"\",\"height\": \"400\",\"isStacked\": \"true\",\"lineWidth\": \"2\",\"pointSize\": \"7\",\"series\": \"\",\"title\": \"\",\"titleTextStyle\": \"\",\"tooltipTextStyle\": \"\", \"vAxis\": \"\",\"width\": \"600\", \"dataSourceUrl\": \"https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key=0AsMQEd_YoBWldHZNbGU2czNfa004UmpzeC13MkZZb0E&output=html\",\"query\": \"\",\"shivaGroup\": \"Visualization\"}";
    var dscourse;
    $(document).ready(function() {

        if (window.addEventListener)
            window.addEventListener("message", shivaMessageHandler, false);
        else
            window.attachEvent("message", shivaMessageHandler);

        dscourse = new Dscourse(lti);
        // Fasten seat belts, dscourse is starting...
    });

    function shivaMessageHandler(e) {
        var msg = "Unrecognized";
        if (e.data.indexOf("GetJSON=") == 0)
            msg = e.data.substr(8);
        else if (e.data.indexOf("GetType=") == 0)
            msg = e.data.substr(8);
        dscourse.currentDrawing = msg;
        console.log(dscourse.currentDrawing);
    }

    function ShivaMessage(iFrameName, cmd) {
        if (cmd.indexOf("PutJSON") == 0)
            console.log(dscourse.currentDrawData);
        cmd += "=" + dscourse.currentDrawData;
        document.getElementById(iFrameName).contentWindow.postMessage(cmd, "*");
    }
    </script>   
</body>
</html>
