<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
	date_default_timezone_set('UTC');
    
   	//CHECK IF LTI
	$LTI = FALSE;
	
	include_once('php/dscourse.class.php');
	$launch = $dscourse->LTI("discussion");
	if($launch != FALSE){
		$LTI = TRUE;
	}
	$cID;
	$discId;
	$uId;
	$origin; 
	$courseInfo; 
	$discussionInfo;
	$discId;
	$preProcess;
	$query;
	
    if($LTI)                        // Checks to see if user is logged in, if not sends the user to login.php
    {
    	//exit("LTI");
    	//CREATE A SESSION
		$uId = $launch->user->attrs['uID'];
		
		$_SESSION['Username'] = strtolower($launch->user->attrs['username']); 
        $_SESSION['firstName'] = $launch->user->attrs['firstName'];
        $_SESSION['lastName'] = $launch->user->attrs['lastName'];   
        $_SESSION['LoggedIn'] = 1;  
        $_SESSION['status'] = 'Student';
        $_SESSION['UserID'] = $uId;
		
		$discId = $launch->props['discID'];
		$discussionInfo = $dscourse->DiscussionInfo($discId);
		$courseId = $launch->props['courseId']; 
		$courseInfo = $dscourse->CourseInfo($courseId);

		
		//FAKE THE REQUEST
		$_GET['d'] = $discId;
		$_GET['c'] = $courseId;
		$query = "/discussion.php?d=$discId&c=$courseId";
	}
	else{	
		$query = $_SERVER["REQUEST_URI"];
	}
	$preProcess = $dscourse->PreProcess($query);
	$crumbs = FALSE;
	if(isset($_SESSION['LTI']) && $_SESSION['LTI'] == "course"){
		$LTI = TRUE;
		$crumbs = TRUE;
	}
    $uId = $_SESSION['UserID'];           // Allocate userID to use throughout the page
    if(isset($_GET['d'])){                   // Check if discussion id is set. If not send them back to index
        $discId = $_GET['d']; 
        $discussionInfo = $dscourse->DiscussionInfo($discId); 
    } else {
        header("Location: index.php");  
        exit(); 
    }
       
    $cID = $_GET['c']; 
    $courseInfo = $dscourse->CourseInfo($cID);
	$userNav = $dscourse->UserInfo($uId); 

	//Try loading
	$load = $dscourse->LoadDiscussion($discId, $uId);
    if($load){
    	$discUsers = $dscourse->GetUsers($cID);
		$currentSession = session_id(); 
	   // Show content
		//get last view log time
		$lastView = $dscourse->GetLastView($discId,$uId);
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title> dscourse | <?php echo $discussionInfo['dTitle']; ?></title>
        <?php
		include ('php/header_includes.php');
  		?>
        <script type="text/javascript" src="js/dscourse.js"></script>
        <script type="text/javascript" src="js/mention.min.js" /></script>
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script type="text/javascript">
          // Add some global variables about current user if we need them:
			<?php echo "var lti = " . (($LTI) ? 'true' : 'false') . ";"; ?>
            <?php echo "var currentUserStatus = '" . $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" . $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" . $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
            <?php echo "var discID = " . $discId . ";"; ?>
            <?php echo "var currentSession = '" . $currentSession . "';"; ?>
            <?php echo "var settings = '".json_encode($preProcess) . "';";?>
            <?php echo "var discUsers = ".json_encode($discUsers).";";?>
            <?php echo "var lastView = '$lastView';";?> 
            <?php if(isset($_REQUEST['p'])){
            	$pID = $_REQUEST['p'];
				echo "var goTo='$pID';";
            }?>
    </script>
<style>
.synTop .userThumbTiny {
display: inline; 
}
</style>

</head>

<body>
	<?php 
	if(!$LTI || ($LTI && $crumbs)){ ?>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="<?php echo ($LTI)?"javascript:void(0)":"index.php";?>" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID ?>&lti=true" id="coursesNav"><?php echo $dscourse -> myTruncate($courseInfo['courseName'], 15, ' ', '...'); ?></a></li>
                    <?php if(!$LTI) { ?> <li class="navLevel"><a href="discussion.php?d=<?php echo $discId . '&c=' . $cID ?>" id="discussionNav"><?php echo $dscourse -> myTruncate($discussionInfo['dTitle'], 15, ' ', '...'); ?></a></li> <?php } ?>

                </ul>

              <?php if(!$LTI){ ?>
               <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " . $_SESSION['lastName']; ?> <b class="caret"></b> </a>

                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <li><a id="profileNav" href="profile.php?u=<?php echo $_SESSION['UserID']; ?>">Profile</a></li>
                            <li><a href="php/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
                <?php } ?>
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
        <header class="jumbotron subhead discjumbotron">
            <div class="container-fluid">
                <div class="btn-toolbar" id="toolbox">
                	<?php if(isset($preProcess['options']['useTimeline']) && $preProcess['options']['useTimeline'] == "Yes"){ ?>
                    <button id="showTimeline" class="btn btn-small btn-info">Timeline</button> <?php } ?>
                    <?php if(isset($preProcess['options']['useSynthesis']) && $preProcess['options']['useSynthesis'] == "Yes"){ ?>
                    <button id="showSynthesis" class="btn btn-small btn-warning">Connected Posts</button> <?php } ?>
                    <?php if($preProcess['status']=="OK"){ ?>
                    <button id="" class="btn btn-small btn-success sayBut2" postid="0"><i class="icon-comment icon-white"></i> Say</button> <input id="dIDhidden" type="hidden" name="discID" value="">
                    <?php } ?>
                    <div class="btn-group" id="participantList">
                        <button class="btn disabled ">Participants:</button>
                    </div>
                </div>

                <div class="form-search" id="keywordSearchDiv">
                    <input id="keywordSearchText" type="text" class="input-medium search-query" placeholder="Search in discussion">
                </div>
            </div>
        </header>


    <div id="discussionWrap" class="page" >

        <div class="container-fluid">
            <div class="row-fluid" id="controlsRow">
                <div class="span12" id="dFooter">
                    <div id="controlsWrap">
                    	<?php if(!isset($preProcess['options']['useTimeline']) || ($preProcess['options']['useTimeline'] == "Yes")){ ?>
                        <div id="timeline" class="">
                            <div id="slider-range">
                                <div id="dots"></div>
                            </div><input type="text" id="amount">
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="row-fluid" id="dRowMiddle">
                <div class="span4 ">
                    <div id="row-fluid">
                    	<?php if(!isset($preProcess['options']['showInfo']) || ($preProcess['options']['showInfo']=="Yes")){ ?>
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
                                <h4 id="recentPostsHeader"></h4>

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
						<?php } ?>
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
	} else {?>
		<div class="alert alert-danger"> You are not authorized to view this discussion. You may have been misdirected or entered a wrong link. Also even if a network is private the course creator may have limited access to course members. If this is an error please contact your site administrator. <a href="index.php"> Go back to dscourse </a> </div>
	<?php } ?>
<script type="text/javascript">
    // Latest itiration of 22the shiva elements through iframe
    var sampleData = "{\"chartType\": \"BarChart\",\"areaOpacity\": \".3\",\"backgroundColor\": \"\",\"chartArea\": \"\",\"colors\": \"\",\"fontName\": \"Arial\",\"fontSize\": \"automatic\",\"hAxis\": \"\",\"legend\": \"right\",\"legendTextStyle\": \"\",\"height\": \"400\",\"isStacked\": \"true\",\"lineWidth\": \"2\",\"pointSize\": \"7\",\"series\": \"\",\"title\": \"\",\"titleTextStyle\": \"\",\"tooltipTextStyle\": \"\", \"vAxis\": \"\",\"width\": \"600\", \"dataSourceUrl\": \"https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key=0AsMQEd_YoBWldHZNbGU2czNfa004UmpzeC13MkZZb0E&output=html\",\"query\": \"\",\"shivaGroup\": \"Visualization\"}";
    var dscourse;
    $(document).ready(function() {
	//set up settings
	settings = $.parseJSON(settings);

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