<?php 

ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
    date_default_timezone_set('UTC');
		
	//CHECK IF LTI
	$LTI = FALSE;
	
	include_once('php/dscourse.class.php');
	$from = "course";
	$launch = $dscourse->LTI($from);
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
		$query = "/course.php?c=$courseId";
	}
	else{	
		$query = $_SERVER["REQUEST_URI"];
	}
	$preProcess = $dscourse->PreProcess($query);	
	if(isset($_SESSION['LTI']) && $_SESSION['LTI']=="course"){
		$LTI = TRUE;
	}
	
        // User is logged in, show page. 
        $cID = $_GET["c"];                      // The course ID from link
        $courseInfo = $dscourse->CourseInfo($cID);

		
	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
       	$userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page

        $userNav = $dscourse->UserInfo($userID); 
		
		if($dscourse->LoadCourse($cID, $userID) == false ) {
	           header('Location: index.php');                   // The course is set up that this user can't view it. 
        }
        
        // Get Course Roles
        $courseRoles = $dscourse->CourseRoles($cID);
 	    $totalRoles = count($courseRoles);
	    $currentRole = $preProcess['role'];  // The role of the current user with the course
	    $Instructors = '';
	    $TAs = ''; 
	    $Students = '';  
	    for($i = 0; $i < $totalRoles; $i++) 
		{
			$cUserID 	= $courseRoles[$i]['userID'];
			$userName	= $courseRoles[$i]['firstName'] . ' ' . $courseRoles[$i]['lastName'];
			$userRole 	= $courseRoles[$i]['userRole'];
			$userImg	= $courseRoles[$i]['userPictureURL'];
			$userEmail	= $courseRoles[$i]['username'];
			switch ($userRole) {
			    case "Instructor":
			        $Instructors .= '<a href="profile.php?u='.$cUserID.'" >'.$userName.' </a><br />';// do something
			        break;
			    case "TA":
			        $TAs .= '<a href="profile.php?u='.$cUserID.'" >'.$userName.' </a><br />';// do something
			        break;
			    case "Student":
			        $Students .= '<tr><td><a href="profile.php?u='.$cUserID.'" ><img class="thumbSmall" src="'.$userImg.'" />  '.$userName.'</a> </td><td>'.$userEmail.'</td></tr>'; // do something
			        break;			 
			 }
		}

		//Analytics Variables
		$postType = array(
		    "comment" => 0,
		    "agree" => 0,
		    "disagree" => 0,
		    "clarify" => 0,
		    "offtopic" => 0,
		    "synthesis" => 0
		);

		$weekDay = array(
		    "Monday" => 0,
		    "Tuesday" => 0,
		    "Wednesday" => 0,
		    "Thursday" => 0,
		    "Friday" => 0,
		    "Saturday" => 0, 
		    "Sunday" => 0
		);					

		$dayHour = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		
		
		// Split course dates into weeks
		$beginDate = strtotime($courseInfo['courseStartDate']); 		// Get the course begin date
		$endDate   = strtotime($courseInfo['courseEndDate']);			// Get the course end date 
		$datediff = $endDate - $beginDate;					// Get difference between dates
		$totalDays = floor($datediff/(60*60*24));			// Count the total number of days in between
		if(($totalDays % 7) > 0) { $totalWeeks = ($totalDays/7)+1; } else { $totalWeeks = ($totalDays/7); };  // Get how many weeks there are in those days. 
		$allWeeks = array();
		for ($i1 = 0; $i1 < $totalWeeks; $i1++){
			array_push($allWeeks, 0);				
		} 

								
		// Get Course Discussions
		$courseDiscussions = $dscourse->GetCourseDiscussions($cID);   
 	    $totalDiscussions = count($courseDiscussions);
	    $discPrint = ''; 
	    $totalPosts = 0; 
	    for($j = 0; $j < $totalDiscussions; $j++) 
				{
					$discID 	= $courseDiscussions[$j]['dID'];
					$discName	= $courseDiscussions[$j]['dTitle'];
					$status = '<span style="color:#BD838F">Closed</span>'; 
					$discStatus = $dscourse->DiscussionStatus($discID);
					switch($discStatus)
					{
						case 'all':
							$status = '<span style="color:#74AA81">Open Posting</span>';
						break;	
						case 'student':
							$status = '<span style="color:#F3BC6A">Individual Posting</span>';
						break;
						case 'closed':
							$status = '<span style="color:#BD838F">Closed</span>';
						break;
					}
					  
					$numberofPosts= $dscourse->CountPosts($discID); 
					$totalPosts = $totalPosts + $numberofPosts; 
					if($currentRole == 'Instructor' || $currentRole == 'TA'){
						$discEdit = '<a href="editdisc.php?d='.$discID.'&c='.$cID.'" class="btn btn-info btn-small">Edit</a> ';  
					} 
					else {
						 $discEdit = ''; 
					}
					$lti = ($LTI)?"&lti=true":"";
					$discPrint .= '<tr><td><a href="discussion.php?d='.$discID.'&c='.$cID.$lti.'">'.$discName.'</a></td><td>'.$status.'</td><td>'.$numberofPosts.'</td><td>'.$discEdit.' </td></tr>'; 
		

					
					// Analytics loop for all posts
					$allPosts = $dscourse->GetDiscPosts($discID);
					$totalPostN = count($allPosts); 
					for($k = 0; $k < $totalPostN; $k++){
						
						// postType
						$type = $allPosts[$k]['postType']; 	
						switch ($type) {
						    case "comment":
						        $postType['comment']++; 
						        break;
						    case "agree":
						        $postType['agree']++; 
						        break;
						    case "disagree":
						        $postType['disagree']++; 
						        break;
						    case "clarify":
						        $postType['clarify']++; 
						        break;						
						    case "offTopic":
						        $postType['offtopic']++; 
						        break;
						    case "synthesis":
						        $postType['synthesis']++; 
						        break;					
						}
						
						// week day
						$day = date('l', strtotime($allPosts[$k]['postTime'])); 
						switch ($day) {
						    case "Monday":
						        $weekDay['Monday']++; 
						        break;
						    case "Tuesday":
						        $weekDay['Tuesday']++; 
						        break;
						    case "Wednesday":
						        $weekDay['Wednesday']++; 
						        break;
						    case "Thursday":
						        $weekDay['Thursday']++; 
						        break;						
						    case "Friday":
						        $weekDay['Friday']++; 
						        break;
						    case "Saturday":
						        $weekDay['Saturday']++; 
						        break;					
						    case "sunday":
						        $weekDay['Sunday']++; 
						        break;
						}
						
						// Hour of the day
						$hour = intval(date('H', strtotime($allPosts[$k]['postTime']))); 
						$dayHour[$hour]++; 	 				  
				  
						// Find which week this post belongs to and add one to that week
						$weekofCourse = strtotime($allPosts[$k]['postTime']) - $beginDate; // Subtract the time of the post from the beginning of course. 
						$weekofCourse = floor($weekofCourse/(60*60*24));			// Count the total number of days in between
						if(($weekofCourse % 7) > 0) { $weekNumber = ($weekofCourse/7)+1; } else { $weekNumber = ($weekofCourse/7); };  // Get how many weeks there are in those days. 
						$allWeeks[$weekNumber]++; 		  
				  }
		}
 // Load Course Options and Place them in Required Sections
 $courseOptions = $dscourse->LoadCourseOptions($cID); // This is all we need. The printout of sections are done below. 
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | <?php  echo $courseInfo['courseName'];  ?></title>
    
    <?php include("php/header_includes.php");  ?>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="excanvas.min.js"></script><![endif]-->
	<script type="text/javascript" src="js/jquery.flot.min.js"  /> </script>  
	<script type="text/javascript" src="js/jquery.flot.pie.min.js"  /> </script>  
	<script type="text/javascript" src="js/jquery.flot.categories.min.js"  /> </script>  

    <script type="text/javascript">
    $(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
             <?php echo "var settings = '".json_encode($preProcess) . "';";?>
 
			<?php 
			if(isset($_GET['m'])){
				?>
				$.notification ({
				        content:    '<?php echo $message['content']; ?>',
				        timeout:    5000,
				        border:     true,
				        icon:       '<?php echo $message['icon']; ?>',
				        color:      '<?php echo $message['color']; ?>',
				        error:      <?php echo $message['error']; ?>  
				     }); 
				<?php 				
			}
			?>	

			$('#studentFilter').on('keyup', function () {
				var keyword = $(this).val().toLowerCase();
				var trText, n; 
				$('#courseStudentsBody > tr').each(function(){
					trText = $(this).text().toLowerCase(); 
					n=trText.indexOf(keyword);
					if(n === -1){
						$(this).hide(); 
					} else {
						$(this).show();
					}
				});	
			}) ; 

          // CourseOptions
			$('#submitCourseOptions').on('click', function () {  // When save changes button is clicked.
				// go through all settings boxes to extract the relevant data
				var optionsType = 'course'; 
				var optionsTypeID = $('#courseID').val(); // Course number 
				var optionsName; 
				var allOptions = []; // array with all options
				var option = {};  // individual option object
				$('.saveOption').each(function () {
					optionsName = $(this).find('.controls').attr('id');  // Run through all components and get option
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
                            $('html, body').animate({
					            scrollTop : 0
					        }); 
					        $.notification ({
						        content:    'Course Setting Saved!',
						        timeout:    5000,
						        border:     true,
						        icon:       'N'
						     }); 
						}, 
					  error: function(data) {					// If connection is not successful.  

					  }
				});
				
           }); 
			$('div.btn-group .btn').click( function () { 
				$(this).siblings().removeClass('active'); 
				$(this).addClass('active'); 
			}); 
			
		var postTypeData = [
					{label: "Comment",  data: <?php echo $postType['comment']; ?>},
					{label: "Agree",  data: <?php echo $postType['agree']; ?>},
					{label: "Disagree",  data: <?php echo $postType['disagree']; ?>},
					{label: "Clarify",  data: <?php echo $postType['clarify']; ?>},
					{label: "Off Topic",  data: <?php echo $postType['offtopic']; ?>},
					{ label: "Connected Post",  data: <?php echo $postType['synthesis']; ?>}
					];
		var postTypeOptions	= { 
				series: {
			        pie: {
			            show: true,
			            innerRadius : 0.5
			        }
			    },
			    legend: {
				    container : '#postTypeLegend'
			    }
		    };					
											
			// Charts
			$.plot('#postTypeChart', postTypeData, postTypeOptions );					


		var weekDayData = [
					["Monday",   <?php echo $weekDay['Monday']; ?>		  ],
					["Tuesday",   <?php echo $weekDay['Tuesday']; ?>	  ],
					["Wednesday",   <?php echo $weekDay['Wednesday']; ?>  ],
					["Thursday", <?php echo $weekDay['Thursday']; ?>	  ],
					["Friday",   <?php echo $weekDay['Friday']; ?>		  ],
					["Saturday",   <?php echo $weekDay['Saturday']; ?>	  ],
					["Sunday", <?php echo $weekDay['Sunday']; ?>		  ]
					];
		var weekDayOptions	= { 
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: "center"
				}
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			},
			yaxis: {
				show: true,
				position: 'left',
				tickSize: 1,
				tickDecimals: 0,
				labelWidth: 10
			},
			grid : {
				borderWidth: 2,
				borderColor : '#ccc'
			}
	  };
					
											
			// Chart for WeekDay
			$.plot('#weekDayChart', [ weekDayData ] , weekDayOptions );					


		var dayHourData = [
				<?php 
					for($h = 0; $h <24; $h++){
						echo '["'. $h.'", '. $dayHour[$h].']'; 
						if($h < 23){
							echo ','; 
						}
					}
				?>
					];
		var dayHourOptions	= { 
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: "center"
				}
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			},
			yaxis: {
				show: true,
				position: 'left',
				tickSize: 1,
				tickDecimals: 0,
				labelWidth: 10
			},
			grid : {
				borderWidth: 2,
				borderColor : '#ccc'
			}
	  };
					
											
			// Chart for dayHour
			$.plot('#dayHourChart', [ dayHourData ] , dayHourOptions );					




		var weekCourseData = [
				<?php 
					$totalWeeksCount = count($allWeeks); 
					for($h1 = 0; $h1 < $totalWeeksCount; $h1++){
						echo '["'. $h1.'", '. $allWeeks[$h1].']'; 
						if($h1 < $totalWeeksCount-1){
							echo ','; 
						}
					}
				?>
					];
				
		var weekCourseOptions	= { 
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: "left"
				}
			},
			xaxis: {
				show: false
			},
			yaxis: {
				show: true,
				position: 'left',
				tickSize: 1,
				tickDecimals: 0,
				labelWidth: 10
			}, 
			grid : {
				borderWidth: 2,
				borderColor : '#ccc'
			}
	  };
					
											
			// Chart for dayHour
			$.plot('#weekCourseChart', [ weekCourseData ] , weekCourseOptions );					

			
        }); 
    </script>
</head>

<body>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="<?php echo ($LTI)?"javascript:void(0)":"index.php";?>" class="brand" id="homeNav">dscourse</a> 
                
                <ul class="nav">
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID; echo ($LTI)?"&lti=true":""; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
                </ul>
                <ul class="nav pull-right">
                    <li class="dropdown">
                     <?php if(!$LTI){ ?>  <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?> <b class="caret"></b> </a> <?php } ?>

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


    <div id="overlay"></div>
    
        <header class="jumbotron subhead">
            <div class="container-fluid">
	            <div class="row-fluid">
	                <div class="span12">
							<h1><?php echo $courseInfo['courseName']; ?></h1>               
	                </div>
	            </div>             
            </div>
        </header>

     <div id="coursePage" class=" wrap page formPage" >

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span4 greenBox">

                    	<div id="iCoursePicture"><img src="<?php 
		            	
					if($courseInfo['courseImage'] != ''){
						$courseImage= $courseInfo['courseImage'];
					} else {
						$courseImage= 'img/course_default.jpg';					
					}
							            	
		            	echo $courseImage ?>" /> </div>

                    <div id="iCourseInfo">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="profileHead">About:</td>

                                    <td id="iCourseDescription"><p><?php echo $courseInfo['courseDescription']; ?></p></td>
                                </tr>                            
                            							

                                <tr>
                                    <td class="profileHead">Instructors:</td>

                                    <td id="iCourseInstructors"><?php echo $Instructors; ?></td>
                                </tr>
                                <tr>
                                    <td class="profileHead">Teaching Assistants:</td>

                                    <td id="iCourseTAs"><?php echo $TAs; ?></td>
                                </tr>

                                <tr>
                                    <td class="profileHead">Start Date:</td>

                                    <td id="iCourseStartDate"><?php echo date("l, F jS, Y",strtotime($courseInfo['courseStartDate'])); ?></td>
                                </tr>

                                <tr>
                                    <td class="profileHead">End Date</td>
                                    <td id="iCourseEndDate"><?php echo date("l, F jS, Y ",strtotime($courseInfo['courseEndDate'])); ?></td>
                                </tr>

                                <tr>
                                    <td class="profileHead">Course Website</td>

                                    <td id="iCourseURL"><?php echo $courseInfo['courseURL']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <div> 	<?php if($currentRole == 'Instructor' || $currentRole == 'TA'){ ?>
							    	<a href="editcourse.php?c=<?php echo $cID; ?>" id="editCourseButton" class="btn btn-block btn-info"><i class="icon-white icon-edit"></i> Edit Course</a>						    
							    <?php } ?>
							    </div>
                    </div><!-- close iCourseInfo-->
                </div><!-- close span4 -->

                <div class="span8 greenBox">
                    
					<ul class="nav nav-tabs" id="courseTabs">
					  <li class="active"><a href="#courseDiscussions" data-toggle="tab">Discussions</a></li>
					  <li><a href="#courseStudents" data-toggle="tab">Students</a></li>
					  <li><a href="#courseAnalytics" data-toggle="tab">Analytics</a></li>
					  
					  <?php if($currentRole == 'Instructor' || $currentRole == 'TA'){ ?> <li><a href="#courseSettings" data-toggle="tab">Settings</a></li> <?php } ?>
					</ul>
					 
					<div class="tab-content">
	                    <div id="courseDiscussions" class="tab-pane active">
	                        <div class="pull-right" style="margin-bottom:20px">
	                        <?php if(($currentRole != "Viewer") && ($currentRole == 'Instructor' || $currentRole == 'TA' || (isset($preProcess['options']['studentCreateDisc']) && $preProcess['options']['studentCreateDisc']=="Yes"))){ ?>
	
	                         <a href="adddisc.php?c=<?php echo $courseInfo['courseID']?>" id="addDiscussionView" class="btn btn-small btn-primary"> <i class="icon-white icon-plus"></i> Add Discussion</a>
	                         <?php }?>
	                         </div>
	
	                        <table class="table table-striped table-bordered">
	                            <thead>
	                                <tr>
	                                    <th width="60%">Discussion Question</th>
	
	                                    <th width="20%">Status</th>
	
	                                    <th width="10%">Posts</th>
	                                    <th width="10%"> </th>
	                                </tr>
	                            </thead>
	
	                            <tbody id="courseDiscussionsBody">
		                            	<?php echo $discPrint; ?>
	                            </tbody>
	                        </table>
	                    </div><!-- close courseDiscussions-->	


	                    <div id="courseStudents" class="tab-pane">
	                       <div class="center">
	                       			<input id="studentFilter" type="text" placeholder= "Find users..."/><a href="#addStudentInfo" data-toggle="modal"> How to Add Students </a>
	                       </div>
	
	                        <table class="table table-striped table-bordered">
	                            <thead>
	                                <tr>
	                                    <th width="60%">Name</th>
	
	                                    <th width="40%">Email Address</th>
	                                </tr>
	                            </thead>
	
	                            <tbody id="courseStudentsBody">
		                            <?php echo $Students; ?>
	                            </tbody>
	                        </table>
	                    </div><!-- close courseStudents-->
 
                    	
						  <div class="tab-pane" id="courseAnalytics">
					        <div class="container-fluid">
					            <div class="row-fluid">
<!-- 
					                <div class="span4 analyticsBox">
						                <h5><span class="analyticsN">13</span> Active Users</h5>
					                </div>
 -->
					                
					                <div class="span6 analyticsBox">
						                <h5><span class="analyticsN"><?php echo $totalDiscussions; ?></span> Discussions</h5>
					                </div>

					                <div class="span6 analyticsBox">
						                <h5><span class="analyticsN"><?php echo $totalPosts; ?></span> Posts</h5>
					                </div>
					            </div>
					            <hr class="soften" />
					            <div class="row-fluid">
						            <div class="span12 analyticsBox">
						                <h5>Weekly Post Count</h5>

						                <div id="weekCourseChart" class="" style="width: 600px;height:200px"></div>

					                </div>
					            </div>
					            <hr class="soften" />
					            <div class="row-fluid">
						            <div class="span6 analyticsBox">
						                <h5>Post in Time of Day</h5>
						                <div id="dayHourChart" style="width:300px;height:200px"></div>

					                </div>
						            <div class="span6 analyticsBox">
						                <h5>Post in Day of Week</h5>
						                <div id="weekDayChart" style="width:300px;height:200px"></div>

					                </div>
					            </div>
					            <hr class="soften" />
					            <div class="row-fluid">
<!-- 
						            <div class="span6 analyticsBox">
						                <h5>Most Active Students</h5>
					                </div>
 -->
						            <div class="span12 analyticsBox">
						                <h5>Frequency of Type of Comment</h5>
						                <div id="postTypeChart" style="width:300px;height:200px; float: left;"></div>
						                <div id="postTypeLegend" style="float:left;"> </div>
					                </div>
					            </div>
					            <hr class="soften" />
					            <!-- 
<div class="row-fluid">
						            <div class="span12 analyticsBox">
						                <h5>Word Cloud</h5>
					                </div>
					            </div>
 -->
					        </div>
							  
						  </div>
						  
						  <?php if($currentRole == 'Instructor' || $currentRole == 'TA'){ ?> 
						  <div class="tab-pane" id="courseSettings">
						    <div id="courseSettingsPage">
						        <div class="container-fluid">
						            <div class="row-fluid">
						                <div class="span12 form-horizontal">
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
						            </div>
						            <div class="row-fluid">
  
						                <div class="span12">
							                <h3> Course Codes</h3>
							                <hr class="soften" />
							                <p> The people in your course can view and participate in your course and their permissions can be set in course edit page. The codes below allow you to provide further options. </p>
						                        <div class="control-group settingBox saveOption">
						                                <label class="control-label" for="viewCode"><b>URL to View Course</b></label>
						                                <div class="controls" id="viewCode">
						                                    <input type="text" class="span10 disabled" id="viewCodeInput" name="viewCode" value="<?php  echo "http://".$_SERVER['HTTP_HOST'].join("/",array_slice(explode('/',$_SERVER['REQUEST_URI']),0,-1))."/go.php?a=".$dscourse->OptionValue($courseOptions, 'viewCode');?>" readonly>
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
						                                    <input type="text" class="span10 disabled" id="registerCodeInput" name="registerCode" value="<?php echo "http://".$_SERVER['HTTP_HOST'].join("/",array_slice(explode('/',$_SERVER['REQUEST_URI']),0,-1))."/go.php?a=".$dscourse->OptionValue($courseOptions, 'registerCode');  ?>" disabled>
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





				            <div id="addCourseCancel" class="pull-right">
				                <button type="submit" name="submitCourseOptions" id="submitCourseOptions" class="btn btn-primary">Save Changes</button>
				            </div>

						  </div>
						  <?php }?>

					</div>
					 

                </div><!-- close span8 -->
            </div><!-- close row -->
        </div><!-- close container -->
    </div><!-- close coursePage -->
    
    
   <!-- Modal -->
<div id="addStudentInfo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addStudentInfoLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="addStudentInfoLabel">How to Add Students to your course</h3>
  </div>
  <div class="modal-body">
    <h4>If student has dscourse account</h4>
    <p> If the student is a member of dscourse you can add them to your class and assign any role you like. You can also block students from viewing your content this way. The student needs to already have an account on dscourse for this to work. </p>
    <p> To add multiple students close this dialog box and click the Edit Course button in the course page. You can make changes to course roles and add more students from there.</p>
    <h4>If student doesn't have dscourse account</h4>
    <p> We have provided a quick way for you to allow students to register and become a member of the course themselves. </p>
    <p> To do this close this dialog and select Settings in the course page. You will see in that form a link for you to give your students that will allow them to register to dscourse and also register for your course. You need to turn this option ON for the link to take effect. After that all you need to do is email the link to students. This link will work for any student who has the link so be careful in disseminating the link and only share with your students.</p>
<hr class="soften">
<p><span class="label label-warning">Remember!</span> You can always see a list of your students and change their role in Course Edit page. </p>

  </div>

</div>

 
</body>
</html>