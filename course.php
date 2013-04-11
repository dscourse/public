<?php 

ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
    date_default_timezone_set('UTC');
	
        // User is logged in, show page. 
        include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query);
				
        $cID = $_GET["c"];                      // The course ID from link
        $courseInfo = $dscourse->CourseInfo($cID);
		
	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
       	$userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
		
		if($dscourse->LoadCourse($cID, $userID) == false ) {
	           header('Location: index.php');                   // The course is set up that this user can't view it. 
        }
        
        // Get Course Roles
        $courseRoles = $dscourse->CourseRoles($cID);
 	    $totalRoles = count($courseRoles);
	    $currentRole = '';  // The role of the current user with the course
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
			if($cUserID == $userID){
				 $currentRole = $userRole;
			}
		}
		
		// Get Course Discussions
		$courseDiscussions = $dscourse->GetCourseDiscussions($cID);        
 	    $totalDiscussions = count($courseDiscussions);
	    $discPrint = ''; 
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
					if($currentRole == 'Instructor' || $currentRole == 'TA'){
						$discEdit = '<a href="editdisc.php?d='.$discID.'&c='.$cID.'" class="btn btn-info btn-small">Edit</a> ';  
					} else { $discEdit = ''; }
					$discPrint .= '<tr><td><a href="discussion.php?d='.$discID.'&c='.$cID.'"> '.$discName.'</a></td><td>'.$status.'</td><td>'.$numberofPosts.'</td><td>'.$discEdit.' </td></tr>'; 
		}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | <?php  echo $courseInfo['courseName'];  ?></title>
    
    <?php include("php/header_includes.php");  ?>
    
    <script type="text/javascript">
    $(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
             <?php echo "var accessStatus = '".json_encode($preProcess) . "';";?>
 
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
        
        }); 
    </script>
</head>

<body>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID . "&n=" .$nID  ; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
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


    <div id="overlay"></div>
    
     <div id="coursePage" class=" wrap page" >
        <header class="jumbotron subhead">
            <div class="container-fluid">
	            <div class="row-fluid">
		            <div class="span3">
		            	<div id="iCoursePicture"><img src="<?php 
		            	
					if($courseInfo['courseImage'] != ''){
						$courseImage= $courseInfo['courseImage'];
					} else {
						$courseImage= 'img/course_default.jpg';					
					}
							            	
		            	echo $courseImage ?>" /> </div>
		            </div>
	                <div class="span9">
							<h1><?php echo $courseInfo['courseName']; ?></h1>
							<p><?php echo $courseInfo['courseDescription']; ?></p>
							<div id="editCourseButton" class="pull-right">
							    <?php if($currentRole == 'Instructor' || $currentRole == 'TA'){ ?>
							    	<a href="editcourse.php?c=<?php echo $cID; ?>" id="editCourseButton" class="btn">Edit Course</a>
									<a href="courseoptions.php?c=<?php echo $cID; ?>" id="courseOptionsButton" class="btn">Course Settings</a>
						    
							    <?php } ?>
							</div>                
	                </div>
	            </div>             
            </div>
        </header>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span4">

                    

                    <div id="iCourseInfo">
                        <table class="table">
                            <tbody>
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
                    </div><!-- close iCourseInfo-->
                </div><!-- close span4 -->

                <div class="span8">
                    <div id="courseDiscussions">
                        <h3>Course Discussions
                        <?php if(!$currentRole == "Viewer" && ($currentRole == 'Instructor' || $currentRole == 'TA' || $preProcess['options']['studentCreateDisc']=="Yes")){ ?>

                         <a href="adddisc.php?c=<?php echo $courseInfo['courseID']?>" id="addDiscussionView" class="btn btn-small"> Add Discussion</a>
                         <?php }?>
                         </h3>

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

                    <div id="courseStudents">
                        <h3>Students in this Course</h3>

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
                    </div><!-- close courseDiscussions-->

                </div><!-- close span8 -->
            </div><!-- close row -->
        </div><!-- close container -->
    </div><!-- close coursePage -->


        <?php

            
    ?>
</body>
</html>
