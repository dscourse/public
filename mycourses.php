<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
	date_default_timezone_set('UTC');
    
	    include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$isIndex = FALSE; 
		$preProcess = $dscourse->PreProcess($query, $isIndex); 
	    $userID = $_SESSION['UserID'];			// Allocate userID to use throughout the page
	     
	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
	    
	    $courseData = $dscourse->GetUserCourses($userID);
	    $totalCourses = count($courseData);
	    $coursePrint = ''; 
		$courseQuery = mysql_query("SELECT * FROM courseRoles INNER JOIN courses ON courseRoles.courseID=courses.courseID WHERE userID = $userID");
		$courses = array();
		while($res = mysql_fetch_assoc($courseQuery)){
			array_push($courses, $res);
		}
		$filtered = $dscourse->SuperSort($courses, 
			function($entry){
				return !($entry['userRole']=="blocked" || $entry['userRole']=="Blocked");
			}, 
			function($a,$b){
				$d1=new DateTime($a['courseTime']);
				$d2=new DateTime($b['courseTime']);
				$inter= $d1->diff($d2);
				return $inter->invert == 0;
			},
		10);
		
		$totalCourses =count($filtered);
		$courseData = $filtered;
		
	    if($totalCourses > 0){	
		    for($i = 0; $i < $totalCourses; $i++) 
					{
					$cName 	= $courseData[$i]['courseName'];
					$cID	= $courseData[$i]['courseID'];
					$cRole	= $courseData[$i]['userRole'];
					$courseImage = $courseData[$i]['courseImage'];
					if($courseData[$i]['courseImage'] != ''){
						$courseImage= $courseData[$i]['courseImage'];
					} else {
						$courseImage= 'img/course_default.jpg';					
					}
					
						$coursePrint .='<li courseID="'.$cID.'"><a href="course.php?c='.$cID.'"><img class="thumbSmall" src="'.$courseImage.'" />'.$cName.'</a>  <i>'.$cRole.'</i></li>'; 						
						// Get discussions for each course
					
				}
	
			} else {
			    $coursePrint .= '<div class="alert alert-info">  You are not part of any courses yet. To start a course enter or create a network that you belong to and click Add Course.</div> '; 
			}				
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>dscourse | My Courses</title>
    <?php include('php/header_includes.php');  ?>
    
	<script type="text/javascript" src="js/counter.js"></script>
    <script type="text/javascript">
		$(function(){
			// Add some global variables about current user if we need them:
		    <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
		    <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
		    <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
			
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
	<style>

	</style>
    
</head>
<body>

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>
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
    
    <!-- Begin home.php-->
        <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> My Courses </h1>
            
        </div>
    </header>
        
    <div id="myCoursesPage" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

                <div class="span4">
                <h4> Placeholder </h4>

                </div>
                <div class="span8">
                
                    <div class="">
                        <h4>My Courses</h4>                         <a class="btn btn-info pull-right" href="addcourse.php"> Add Course </a>

                        <hr class="soften">
                        
                        <ul class="unstyled dashboardList" id="courseList">
                        <p><?php echo $coursePrint; ?></p>
                        </ul>

                    </div>
                 </div>
            </div>
        </div><!-- close container -->
    </div><!-- end home-->


</body>
</html>    
<?php
        
?>
