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
        
        $cID = $_GET["c"];                      // The course ID from link
        $courseInfo = $dscourse->CourseInfo($cID);

        if(isset($_GET['n'])){                      // The network ID from link 
	        $nID = $_GET['n'];        
      	    // GET Info About This Network
      	    $networkInfo = $dscourse->NetWorkInfo($nID);
	      }

	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
	    	      
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        
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
					$discStatus = ''; //$dscourse->DiscussionStatus($discID);  
					$numberofPosts= ''; //$dscourse->CountPosts($discID); 
					if($currentRole == 'Instructor' || $currentRole == 'TA'){
						$discEdit = '<a href="editdisc.php?d='.$discID.'&c='.$cID.'&n='.$nID.'" class="btn btn-info btn-small">Edit</a> ';  
					} else { $discEdit = ''; }
					$discPrint .= '<tr><td><a href="discussion.php?d='.$discID.'&c='.$cID.'&n='.$nID.'"> '.$discName.'</a></td><td>'.$discStatus.'</td><td>'.$numberofPosts.'</td><td>'.$discEdit.' </td></tr>'; 
		}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | <?php  echo $courseInfo['courseName'];  ?></title>
    
    <?php include('php/header_includes.php');  ?>
    
    <script type="text/javascript">
    $(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
 
            <?php if(isset($_GET['m'])){
	            switch ($_GET['m']) {
				    case 'd':
				        echo "$.notification ( { title:'Done!', content:'Your discussion was added. ', timeout:5000, border:true, fill:true, icon:'N', color:'#333'});"; 
				        break;
				} 
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
                    <li class="navLevel"><a href="network.php?n=<?php echo $nID; ?>" id="networkNav"><?php echo $networkInfo['networkName']; ?></a></li>
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
                </ul>

                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?> <b class="caret"></b> </a>

                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <li><a id="profileNav" userid="<?php echo $_SESSION['UserID']; ?>">Profile</a></li>

                            <li><a id="usersNav">Users</a></li>

                            <li><a id="helpNav">Help</a></li>

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
		            	<div id="iCoursePicture"><img src="<?php echo $courseInfo['courseImage']; ?>" /> </div>
		            </div>
	                <div class="span9">
							<h1><?php echo $courseInfo['courseName']; ?></h1>
							<p><?php echo $courseInfo['courseDescription']; ?></p>
							<div id="editCourseButton" class="pull-right">
							    <a href="editcourse.php?c=<?php echo $cID.'&n='.$nID; ?>" id="editCourseButton" class="btn">Edit Course</a>
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

                                    <td id="iCourseStartDate"><?php echo $courseInfo['courseStartDate']; ?></td>
                                </tr>

                                <tr>
                                    <td class="profileHead">End Date</td>

                                    <td id="iCourseEndDate"><?php echo $courseInfo['courseEndDate']; ?></td>
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
                        <h3>Course Discussions <a href="adddisc.php?c=<?php echo $courseInfo['courseID'].'&n='.$nID; ?>" id="addDiscussionView" class="btn btn-small"> Add Discussion</a></h3>

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

       }  
            
    ?>
</body>
</html>
