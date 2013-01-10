<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
    
    if(empty($_SESSION['Username']))                        // Checks to see if user is logged in, if not sends the user to login.php
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
        
    }  else {                                               // User is logged in, show page. 
        

       include_once('php/dscourse.class.php');
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
       if($load){
	 		 // Show content

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title> dscourse | <?php echo $discussionInfo['dTitle']; ?></title>
        <?php include('php/header_includes.php');  ?>
        <script type="text/javascript" src="js/dscourse.js"></script>

    <script type="text/javascript">

            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
            <?php echo "var discID = " .  $discID . ";"; ?> 
            
    $(function(){
    }); 
    </script>
</head>

<body>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="network.php?n=<?php echo $nID; ?>" id="networkNav"><?php echo $networkInfo['networkName']; ?></a></li>
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID.'&n='.$nID; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
                    <li class="navLevel"><a href="discussion.php?d=<?php echo $discID . '&c='. $cID. '&n='.$nID; ?>" id="discussionNav"><?php echo $discussionInfo['dTitle']; ?></a></li>

                </ul>

                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?> <b class="caret"></b> </a>

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
    
    <div id="discussionWrap" class=" page" >
        <header class="jumbotron subhead">
            <div class="container-fluid">
                <div class="btn-toolbar" id="toolbox">
                    <button id="showTimeline" class="btn btn-small btn-info">Timeline</button> <button id="showSynthesis" class="btn btn-small btn-warning">Connected Posts</button> <button id="" class="btn btn-small btn-success sayBut2" postid="0"> Say</button> <input id="dIDhidden" type="hidden" name="discID" value="">

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
                                <span class="boxHeaders"><span id="dTitleView"></span></span> <!--
                        <div class="sayBut2" postid="0">
                            say <input id="dIDhidden" type="hidden" name="discID" value="">
                        </div>
-->

                                <div id="discStatus" class="alert"></div>

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

                                    <div id="dInstView">
                                        <b>Instructor:</b><br>
                                    </div>

                                    <div id="dTAView">
                                        <b>Teaching Assistant:</b><br>
                                    </div>

                                    <div id="dStudentView">
                                        <b>Students:</b><br>
                                    </div>
                                </div>

                                <h4>Recent Posts</h4>

                                <div class="alert alert-info smallAlert">
                                    Click on the item below to go to post.
                                </div>

                                <div class="content">
                                    <ul class="discussionFeed" id="recentContent"></ul>
                                </div>
                            </div>

                            <div class="dCollapse hide" id="dSynthesis">
                                <div class="content">
                                    <div class="hide" id="addSynthesis">
                                        <input id="sPostIDhidden" type="hidden" name="sPostIDhidden" value=""> <input id="userIDhidden" type="hidden" name="userIDhidden" value="<?php echo $_SESSION['UserID'];?>"> 
                                        <textarea id="synthesisText">
Your synthesis comment...
</textarea>

                                        <div id="synthesisDrop" class="alert alert-info">
                                            Drag and drop posts here to add to synthesis.
                                        </div>

                                        <div id="synthesisPostWrapper"></div><input id="addSynthesisButton" type="button" class="buttons btn btn-primary" value="Add Post"> <input id="cancelSynthesisButton" type="button" class="buttons btn btn-info" value="Cancel">
                                        <hr class="soften">
                                    </div>

                                    <p>Connected posts include references to multiple posts at once. Below you can see all connected posts. Scroll for more, click on the posts to expand or collapse.</p>

                                    <ul class="synthesisFeed" id="synthesisList"></ul>
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
                        <div class="levelWrapper" level="0"></div>
                    </div>
                </div><!-- close span9 -->
            </div><!-- close row -->

            <div id="commentWrap">
                <input id="postIDhidden" type="hidden" name="postIDhidden" value=""> <input id="userIDhidden" type="hidden" name="userIDhidden" value="<?php echo $_SESSION['UserID'];?>">

                <div id="top">
                    <div id="quick">
                        <div class="btn-group" id="postTypeID">
                            <button class="btn postTypeOptions active" id="comment">Comment</button> <button class="btn postTypeOptions" id="agree">Agree</button> <button class="btn postTypeOptions" id="disagree">Disagree</button> <button class="btn postTypeOptions" id="clarify">Ask to Clarify</button> <button class="btn postTypeOptions" id="offTopic">Off Topic</button>
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
    <?php
	 		
 		} else {
	 		?>
	 		<div class="alert alert-danger"> You are not authorized to view this discussion. If this is an error please contact your site administrator. </div>
	 		<?php 
 		}       
       
       

   }  
            
    ?>  
 <script type="text/javascript">
// Latest itiration of 22the shiva elements through iframe
    var sampleData="{\"chartType\": \"BarChart\",\"areaOpacity\": \".3\",\"backgroundColor\": \"\",\"chartArea\": \"\",\"colors\": \"\",\"fontName\": \"Arial\",\"fontSize\": \"automatic\",\"hAxis\": \"\",\"legend\": \"right\",\"legendTextStyle\": \"\",\"height\": \"400\",\"isStacked\": \"true\",\"lineWidth\": \"2\",\"pointSize\": \"7\",\"series\": \"\",\"title\": \"\",\"titleTextStyle\": \"\",\"tooltipTextStyle\": \"\", \"vAxis\": \"\",\"width\": \"600\", \"dataSourceUrl\": \"https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key=0AsMQEd_YoBWldHZNbGU2czNfa004UmpzeC13MkZZb0E&output=html\",\"query\": \"\",\"shivaGroup\": \"Visualization\"}";

    $(document).ready(function() {


        if (window.addEventListener) 
                window.addEventListener("message",shivaMessageHandler,false);
            else
                window.attachEvent("message",shivaMessageHandler);
            });
        
    function shivaMessageHandler(e)
    {
        var msg="Unrecognized";
        if (e.data.indexOf("GetJSON=") == 0) 
            msg=e.data.substr(8);
        else if (e.data.indexOf("GetType=") == 0) 
            msg=e.data.substr(8);
        dscourse.currentDrawing = msg;
        console.log(dscourse.currentDrawing); 
    }

    function ShivaMessage(iFrameName,cmd) 
    {
        if (cmd.indexOf("PutJSON") == 0)
            console.log(dscourse.currentDrawData);
            cmd+="="+dscourse.currentDrawData;
        document.getElementById(iFrameName).contentWindow.postMessage(cmd,"*");
    }
    
                var dscourse = new Dscourse();              // Fasten seat belts, dscourse is starting...

    </script>   
</body>
</html>
