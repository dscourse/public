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

/* MARK FOR REMOVAL
	    $nID = $_GET["n"];                      // The course ID from link

        $networkInfo = $dscourse->NetWorkInfo($courseInfo['networkID']);
*/

        $userCourseRole = $dscourse->UserCourseRole($cID, $userID);          // Get user role
        if($userCourseRole[0] != 'Instructor' && $userCourseRole[0] != 'TA' ){ // Check if user can edit this page. We need to put this into the preprocessor
	             header('Location: index.php');                  
	             exit(); 
	        }




/*  MARK FOR REMOVAL
        // Get Course Roles
        $courseRoles = $dscourse->CourseRoles($cID);
        $totalRoles = count($courseRoles);
        $InstructorRows = '';
        $TARows = ''; 
        $StudentRows = '';  
        for($i = 0; $i < $totalRoles; $i++) 
                {
                    $userID     = $courseRoles[$i]['userID'];
                    $userName   = $courseRoles[$i]['firstName'] . ' ' . $courseRoles[$i]['lastName'];
                    $userRole   = $courseRoles[$i]['userRole'];
                    $userEmail  = $courseRoles[$i]['username'];
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
*/
 
 // Load Course Options and Place them in Required Sections
 $courseOptions = $dscourse->LoadCourseOptions($cID); // This is all we need. The printout of sections are done below. 
                
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | Edit Course</title><?php include('php/header_includes.php');  ?>
    <script type="text/javascript">


    $(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>

            // Turn codes on and off. 
			$('#submitCourseOptions').on('click', function () {  // When save changes button is clicked.
				// go through all settings boxes to extract the relevant data
				var optionsType = 'course'; 
				var optionsTypeID = $('#courseID').val(); // Course number 
				var optionsName; 
				var allOptions = []; // array with all options
				var option = {};  // individual option object
				$('.saveOption').each(function () {
					optionsName = $(this).find('.controls').attr('id');  // Run through all components and get option
					console.log(optionsName);
					option = {}; 
					switch (optionsName)
					{
						case 'charLimit':
						  option.optionsName = 'charLimit'; 
						  option.optionsValue = $('#charLimitInput').val(); 						  
						  break;
						case 'useTimeline':
						  option.optionsName = 'useTimeline'; 
						  option.optionsValue = $('#useTimeline').find('.active').text(); 						  
						  break;
						case 'useSynthesis':
						  option.optionsName = 'useSynthesis'; 
						  option.optionsValue = $('#useSynthesis').find('.active').text();  						  
						  break;
						case 'showInfo':
						  option.optionsName = 'showInfo'; 
						  option.optionsValue = $('#showInfo').find('.active').text();  						  
						  break;
						case 'studentCreateDisc':
						  option.optionsName = 'studentCreateDisc'; 
						  option.optionsValue = $('#studentCreateDisc').find('.active').text();  						  
						  break;
						case 'viewCode':
						  option.optionsName = 'viewCode'; 
						  option.optionsValue = '<?php echo $dscourse->OptionValue($courseOptions, 'viewCode'); ?>'; 
						  option.optionsAttr = $('#viewCode').find('.active').text();  						  
						  break;
						case 'registerCode':
						  option.optionsName = 'registerCode'; 
						  option.optionsValue = '<?php echo $dscourse->OptionValue($courseOptions, 'registerCode'); ?>';
						  option.optionsAttr = $('#registerCode').find('.active').text();  						  
						  break;
						}
						allOptions.push(option); 
				});
				
				$.ajax({											// Ajax talking to the data.php file												
					type: "POST",
					url: "php/data.php",
					data: {
						optionsType: optionsType,
						optionsTypeID: optionsTypeID,
						optionsData : allOptions,
						action: 'saveOptions'							
					},
					  success: function(data) {						// If connection is successful . 							
						  	alert('Your changes have been successfully saved!');
						}, 
					  error: function(data) {					// If connection is not successful.  

					  }
				});
				
           }); 
				$('div.btn-group .btn').click( function () { 
					$(this).siblings().removeClass('active'); 
					$(this).addClass('active'); 
				});              
    });                        
    </script>
</head>

<body>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse  </a>

                <ul class="nav">
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
                </ul>

                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>"> <?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?></a>

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

            <p>Customize your course and general discussion settings.</p>

            <div id="addCourseCancel" class="pull-right">
                <a href="course.php?c=<?php echo $cID;?>" class="btn">Back</a>
                <button type="submit" name="submitCourseOptions" id="submitCourseOptions" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </header>

    <div id="courseSettingsPage" class=" wrap page">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span6 form-horizontal">
                 <h3>Discussion Settings</h3>
                 <hr class="soften" />
                 <p> These settings will apply to all discussions within this course </p>
                            <input type="hidden" name="action" value="courseSettings"> 
                            <input type="hidden" name="courseID" id="courseID" value="<?php  echo $cID; ?>">
                    
                            <div class="control-group settingBox saveOption">
                                <label class="control-label" for="charLimit">Discussion Post Character Limit</label>
                                <div class="controls" id="charLimit">
                                    <input type="text" class="input-small" id="charLimitInput" name="charLimit" value="<?php  echo $dscourse->OptionValue($courseOptions, 'charLimit');?>">
                                    <p class="help-inline">Enter a maximum character limit for the discussion. Users won't be able to post beyond this limit. To allow unlimited characters please enter 0.</p>
                                </div>
                            </div>
                            
                            <div class="control-group settingBox saveOption">
                                <label class="control-label" for="useTimeline">Use Timeline</label>
                                <div class="controls" id="useTimeline">
									<div class="btn-group" data-toggle="buttons-radio" >
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'useTimeline') == 'Yes'){echo 'active'; };?>" id="useTimelineYes">Yes</button>
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'useTimeline') == 'No' ){echo 'active'; };?>" id="useTimelineNo">No</button>
									</div>
                                </div>
                            </div>
                            
                            <div class="control-group settingBox saveOption">
                                <label class="control-label" for="useSynthesis">Use Connected Posts</label>
                                <div class="controls" id="useSynthesis">
									<div class="btn-group" data-toggle="buttons-radio" >
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'useSynthesis') == 'Yes'){echo 'active'; };?>" id="useSynthesisYes">Yes</button>
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'useSynthesis') == 'No' ){echo 'active'; };?>" id="useSynthesisNo">No</button>
									</div>
                                </div>
                            </div>
                            <div class="control-group settingBox saveOption">
                                <label class="control-label" for="showInfo">Show Information Panel</label>
                                <div class="controls" id="showInfo">
									<div class="btn-group" data-toggle="buttons-radio" >
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'showInfo') == 'Yes'){echo 'active'; };?>" id="showInfoYes">Yes</button>
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'showInfo') == 'No' ){echo 'active'; };?>" id="showInfoNo">No</button>
									</div>
                                </div> 
                            </div>
                            <div class="control-group settingBox saveOption">
                                <label class="control-label" for="studentCreateDisc">Allow Students to Create Discussions</label>
                                <div class="controls" id="studentCreateDisc">
									<div class="btn-group" data-toggle="buttons-radio" >
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'studentCreateDisc') == 'Yes'){echo 'active'; };?>" id="studentCreateDiscYes">Yes</button>
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'studentCreateDisc') == 'No' ){echo 'active'; };?>" id="studentCreateDiscNo">No</button>
									</div>
                                </div>
                            </div>                            
                </div>
                <div class="span6">
	                <h3> Course Codes</h3>
	                <hr class="soften" />
	                <p> The people in your course can view and participate in your course and their permissions can be set in course edit page. The codes below allow you to provide further options. </p>
                        <div class="control-group settingBox saveOption">
                                <label class="control-label" for="viewCode"><b>URL to View Course</b></label>
                                <div class="controls" id="viewCode">
                                    <input type="text" class="span10 disabled" id="viewCodeInput" name="viewCode" value="<?php  echo "http://".$_SERVER['HTTP_HOST'].join("/",array_slice(explode('/',$_SERVER['REQUEST_URI']),0,-1))."/router.php?a=".$dscourse->OptionValue($courseOptions, 'viewCode');?>" readonly>
									<div class="btn-group" data-toggle="buttons-radio" >
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'viewCode', 'viewAttr') == 'On'){echo 'active'; };?>" id="viewCodeOn">On</button>
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'viewCode', 'viewAttr') == 'Off' ){echo 'active'; };?>" id="viewCodeOff">Off</button>
									</div>
                                    <p class="help-inline">Anyone with this link will be able to view course contents and discussions but not participate. If they are not a member of dscourse they will need to create an account. This code will work only when the toggle is ON.</p>
                                </div>
                        </div>

                        <div class="control-group settingBox saveOption">
                                <label class="control-label" for="registerCode"><b>URL to Register for Course</b></label>
                                <div class="controls" id="registerCode">
                                    <input type="text" class="span10 disabled" id="registerCodeInput" name="registerCode" value="<?php echo "http://".$_SERVER['HTTP_HOST'].join("/",array_slice(explode('/',$_SERVER['REQUEST_URI']),0,-1))."/router.php?a=".$dscourse->OptionValue($courseOptions, 'registerCode');  ?>" disabled>
									<div class="btn-group" data-toggle="buttons-radio" >
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'registerCode', 'registerAttr') == 'On'){echo 'active'; };?>" id="registerCodeOn">On</button>
									  <button type="button" class="btn <?php  if($dscourse->OptionValue($courseOptions, 'registerCode', 'registerAttr') == 'Off' ){echo 'active'; };?>" id="registerCodeOff">Off</button>
									</div>
                                    <p class="help-inline">Anyone with this link will join your course as students. If they don't have a dscourse account they will be asked to create one and get automatically added to this course. This code will work only when the toggle is ON.</p>
                                </div>
                        </div>
	                
                </div>
            </div>

        </div>
    </div>
</body>
</html>
