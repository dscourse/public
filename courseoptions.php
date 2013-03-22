<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
	date_default_timezone_set('UTC');
    
		include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query);
		
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        
        $cID = $_GET['c']; 
        $courseInfo = $dscourse->CourseInfo($cID);

        //$nID = $_GET["n"];                      // The course ID from link

	    $networkInfo = $dscourse->NetWorkInfo($courseInfo['networkID']);

	    $userCourseRole = $dscourse->UserCourseRole($cID, $userID); 
	    
	    if($userCourseRole[0] != 'Instructor' && $userCourseRole[0] != 'TA' ){
		     header('Location: index.php');                  
		     exit(); 
	    }

        // Get Course Roles
        $courseRoles = $dscourse->CourseRoles($cID);
 	    $totalRoles = count($courseRoles);
	    $InstructorRows = '';
	    $TARows = ''; 
	    $StudentRows = '';  
	    for($i = 0; $i < $totalRoles; $i++) 
				{
					$userID 	= $courseRoles[$i]['userID'];
					$userName	= $courseRoles[$i]['firstName'] . ' ' . $courseRoles[$i]['lastName'];
					$userRole 	= $courseRoles[$i]['userRole'];
					$userEmail	= $courseRoles[$i]['username'];
					switch ($userRole) {
					    case "Instructor":
					        $InstructorRows .= '<tr><td><input type="hidden" name="user[]" value="'.$userID.'">'.$userName.' </td><td>'.$userEmail.' </td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB active" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB" userid="'.$userID.'">Student</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Instructor"></div></td></tr>'; 
					        break;
					    case "TA":
					        $TARows .= '<tr><td><input type="hidden" name="user[]" value="'.$userID.'">'.$userName.' </td><td>'.$userEmail.' </td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB active" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB" userid="'.$userID.'">Student</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="TA"></div></td></tr>'; 
					        break;
					    case "Student":
					        $StudentRows .= '<tr><td><input type="hidden" name="user[]" value="'.$userID.'">'.$userName.' </td><td>'.$userEmail.' </td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB active" userid="'.$userID.'">Student</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Student"></div></td></tr>'; 
					        break;
					}
		} 
                
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | Edit Course</title>
    
    <?php include('php/header_includes.php');  ?>
    <script type="text/javascript">

    
$(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
            
           
	        
});                        
    </script>
</head>

<body>
    
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
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

    <!-- Begin addcourse.php-->

    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1>Course Settings</h1>
            <p> Customize your course and general discussion settings. </p>
                 <div id="addCourseCancel" class="pull-right">
                    <a href="course.php?c=<?php echo $cID?>" class="btn">Cancel</a>
                </div>
        </div>
    </header>

    <div id="addcoursePage" class=" wrap page">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span8 offset2">
                    <div id="settingsForm">
                        <form class="form-vertical well" name="addCourseForm" action="php/data.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="editCourse">
                        <input type="hidden" name="courseID" value="<?php  echo $cID; ?>" >
 
                        <h3>Permissions<h3>
                        <p> Please define permissions for different user groups for your course.</p>
                             <div class="control-group">
                                <label class="control-label" for="viewOptions">Teaching Assistants</label>
                                <div class="controls">
									<label class="radio">
									<input type="radio" name="viewOptions" id="viewOptions1" value="members" <?php if($courseInfo['courseView'] == 'members'){ echo 'checked';}  ?>>
									  Can view course and all discussions
									</label>
									<label class="radio">
									  <input type="radio" name="viewOptions" id="viewOptions2" value="network" <?php if($courseInfo['courseView'] == 'network'){ echo 'checked';}  ?>>
									  Can edit course and discussion  
									</label>
									<label class="radio">
									  <input type="radio" name="viewOptions" id="viewOptions3" value="everyone" <?php if($courseInfo['courseView'] == 'everyone'){ echo 'checked';}  ?>>
									  Anyone with an account on dscourse
									</label>                                    
                                </div>
                            </div>                           
 
                             <div class="control-group">
                                <label class="control-label" for="participateOptions">Who can Participate in the discussions?</label>
                                <div class="controls">
									<label class="radio">
									<input type="radio" name="participateOptions" id="participateOptions1" value="members" <?php if($courseInfo['courseParticipate'] == 'members'){ echo 'checked';}  ?>>
									  Instructors, TA's and Students
									</label>
									<label class="radio">
									  <input type="radio" name="participateOptions" id="participateOptions2" value="network" <?php if($courseInfo['courseParticipate'] == 'network'){ echo 'checked';}  ?>>
									  Anyone in this network
									</label>
									<label class="radio">
									  <input type="radio" name="participateOptions" id="participateOptions3" value="everyone" <?php if($courseInfo['courseParticipate'] == 'everyone'){ echo 'checked';}  ?>>
									  Anyone with an account on dscourse
									</label>                                    
                                </div>
                            </div>                            
                            
                            
                            <hr class="soften" />                           
                        <div class="control-group">
                                <label class="control-label" for="charLimit">Discussion Post Character Limit</label>
                                <div class="controls">
                                    <input type="text" class="input-small" id="charLimit" name="charLimit" value="500">
                                    <p class="help-inline">Enter a maximum character limit for the discussion. Users won't be able to post beyond this limit. To allow unlimited characters please enter 0. </p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="useTimeline">Use Timeline</label>
                                <div class="controls">
									<label class="radio"> <input type="radio" name="useTimeline" id="useTimelineYes" value="Yes" checked> Yes </label>
									<label class="radio"> <input type="radio" name="useTimeline" id="useTimelineNo" value="No" >No</label>                                   
                                </div>
                            </div>  

                            <div class="control-group">
                                <label class="control-label" for="useSynthesis">Use Connected Posts</label>
                                <div class="controls">
									<label class="radio"> <input type="radio" name="useSynthesis" id="useSynthesisYes" value="Yes" checked> Yes </label>
									<label class="radio"> <input type="radio" name="useSynthesis" id="useSynthesisNo" value="No" >No</label>                                   
                                </div>
                            </div> 


                            <div class="control-group">
                                <label class="control-label" for="showInfo">Show Information Panel</label>
                                <div class="controls">
									<label class="radio"> <input type="radio" name="showInfo" id="showInfoYes" value="Yes" checked> Yes </label>
									<label class="radio"> <input type="radio" name="showInfo" id="showInfoNo" value="No" >No</label>                                   
                                </div>
                            </div> 

                            <div class="control-group">
                                <label class="control-label" for="studentCreateDisc">Allow Students to Create Discussions</label>
                                <div class="controls">
									<label class="radio"> <input type="radio" name="studentCreateDisc" id="studentCreateDiscYes" value="Yes" > Yes </label>
									<label class="radio"> <input type="radio" name="studentCreateDisc" id="studentCreateDiscNo" value="No" checked>No</label>                                   
                                </div>
                            </div> 
                            
                            <hr class="soften">
                            <button type="submit" name="submitCourseOptions" id="submitCourseOptions" class="btn btn-primary pull-right">Submit Changes </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
