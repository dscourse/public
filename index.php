<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
	date_default_timezone_set('UTC');
    
	    include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query, TRUE); 
	    $userID = $_SESSION['UserID'];			// Allocate userID to use throughout the page
        $userNav = $dscourse->UserInfo($userID); 
	     
	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
	    $actions = $dscourse->GetRecentActivity($userID, 8);
		
	   // $courseData = $dscourse->GetUserCourses($userID);
	    //$totalCourses = count($courseData);
	    $coursePrint = ''; 
	    $discussionPrint = ''; 
		//$courseData = $dscourse->GetUserCourses($userID);
		//$totalCourses =count($courseData);
		
		$info = $dscourse->GetIndexInfo($userID, 8);
		$totalCourses = count($info['courseList']);
		$totalDiscussions = count($info['discList']);
		$courseData = $info['courseList'];
	    if($totalCourses > 0){	
		    for($i = 0; $i < $totalCourses; $i++){
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
			}
		} else {
		    $coursePrint .= '<div class="alert alert-info">  You are not part of any courses yet. To start a course enter or create a network that you belong to and click Add Course.</div> '; 
		}
		if($totalDiscussions> 0){
			$discussionData = $info['discList'];
			foreach($discussionData as $d){
				$discID = $d['dID']; 
				$cID = $d['courseID'];
				$discussionName = $d['dTitle'];  // Name
				$discussionPrint .='<li discID="'.$cID.'"><a href="discussion.php?d='.$discID.'&c='.$cID.'">'.$discussionName.'</a></li>'; 
			}						
		}
		else{
			$discussionPrint .= '<div class="alert alert-info">You are not part of any discussions yet because you don\'t have any courses.</div>'; 	
		}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>dscourse</title>
<noscript>
<meta http-equiv=refresh content="0; URL=noscript.php" />
</noscript>
    <?php include('php/header_includes.php');  ?>
    
	<script type="text/javascript" src="js/counter.js"></script>
    <script type="text/javascript">
		$(function(){
			// Add some global variables about current user if we need them:
		    <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
		    <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
		    <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
		    <?php echo "var actions = ".json_encode($actions).";"; ?>
			
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

			if(actions.length > 0){
				$('#noPosts').remove(); 
			}
			$.each(actions, function(i, val){
				var msg = [val.agentLabel, val.action+"ed", "in your", val.context, val.contextLabel].join(" ");
				
				$('#newsFeed').append("<li actionsIndex=\""+i+"\">"+msg+"<br /><a href=\""+val.actionPath+"\">Click to view</a></li>");	
			});		
		}); 
	</script>
	<style>
		.jumbotron {
			height: 170px;
			padding-top: 60px;
		}
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
            <h1> Welcome to Dscourse </h1>
            <p>dscourse is a project that aims to provide the next generation platform-agnostic discussion tool for online learning. You are using stable version 1.2; which means you will not lose any data but functional errors may occur from time to time. In such instances please go to the support discussion for help. If you are new to dscourse please read our documentation. </p>
            <p> <a href="help.php" class="btn btn-small"> Read Documentation </a> <a role="button" class="btn btn-small"> Support Discussion </a> </p>
            
        </div>
    </header>
        
    <div id="homePage" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

                <div class="span4 greenBox indexBox">
                	<div class="">
                		<h4 class="lightBox"> News Feed </h4>
                		<p> </p>
                		<ul class="unstyled dashboardList" id="newsFeed">
                			<p id="noPosts"> There hasn't been any posting since your last visit! </p> 
                        </ul>
					 </div>
                </div>
                <div class="span4 greenBox indexBox">
                    <div class="">
                        <h4 class="lightBox">My Courses</h4>     <a class="lightBoxLink pull-right" href="addcourse.php"><i class="icon-plus "></i>  Add Course </a>

                        
                        <ul class="unstyled dashboardList" id="courseList">
                        <p><?php echo $coursePrint; ?></p>
                        <li class="lightBoxListEnd"> <a href="profile.php?u=<?php echo $_SESSION['UserID']; ?>" class="pull-right"> See all...</a> </li>
                        </ul>

                    </div>
                </div>
                <div class="span4 greenBox indexBox">                                                          
                    <div class="">
                        <h4 class="lightBox">My Discussions</h4>

                        <p></p>

                        <ul class="unstyled dashboardList" id="discussionList">
	                        <?php echo $discussionPrint; ?>
                        </ul>
                        <p>

                    </div>
                </div>
            </div>
        </div><!-- close container -->
    </div><!-- end home-->


</body>
</html>    
<?php
        
?>
