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
	    $userID = $_SESSION['UserID'];			// Allocate userID to use throughout the page
	     
	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
	    
	    
	    // GET User Network List 
	    
	    $networkData = $dscourse->GetUserNetworks($userID);
	    $totalNetworks = count($networkData);
	    $networkPrint = ''; 		
	    for($i = 0; $i < $totalNetworks; $i++) 
				{
				$nName 	= $networkData[$i]['networkName'];
				$nID	= $networkData[$i]['networkID'];
				$nDesc	= $networkData[$i]['networkDesc'];
				
				$networkPrint .='<div class="alert" networkID="'.$nID.'"><a href="network.php?n='.$nID.'">'.$nName.'</a></div>'; 
				}
	    
	    $courseData = $dscourse->GetUserCourses($userID);
	    $totalCourses = count($courseData);
	    $coursePrint = ''; 
	    $discussionPrint = ''; 		
	    for($i = 0; $i < $totalCourses; $i++) 
				{
				$cName 	= $courseData[$i]['courseName'];
				$cID	= $courseData[$i]['courseID'];
				$cRole	= $courseData[$i]['userRole'];
				$courseImage = $courseData[$i]['courseImage'];
				$courseNetworks = $dscourse->CourseNetworks($cID); 				
				$coursePrint .='<li courseID="'.$cID.'"><a href="course.php?c='.$cID.'&n='.$courseNetworks[0]['networkID'].'"><img class="thumbSmall" src="'.$courseImage.'" />'.$cName.'</a>  <i>'.$cRole.'</i></li>'; 
				
				// Get discussions for each course
				$discussionData = $dscourse->GetCourseDiscussions($cID);
				$totalDiscussions = count($discussionData); 
				for($j = 0; $j < $totalDiscussions; $j++)
					{
						$discID = $discussionData[$j]['dID']; 
						$discussionName = $discussionData[$j]['dTitle'];  // Name
						$discussionPrint .='<li discID="'.$cID.'"><a href="discussion.php?d='.$discID.'&c='.$cID.'&n='.$courseNetworks[0]['networkID'].'">'.$discussionName.'</a></li>'; 
						
					}
				
				}				
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>dscourse</title>
    

    <?php include('php/header_includes.php');  ?>


    <script type="text/javascript">
		$(function(){
			// Add some global variables about current user if we need them:
		    <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
		    <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
		    <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
			
			// Add Network when #addNetwork is clicked 
			$('#addNetwork').on('click', function () {
				var networkID 	= 0; // This is a new network, there is no ID yet
				var networkName = $('#networkName').val(); // get network Name
				var networkDesc = $('#networkDesc').val(); // get Network description
				var userID		= currentUserID; // get user ID
				var userRole	= 'owner'; 

				networkID =  parseFloat(networkID); 				
			 	var network = {
				 	networkID : networkID,
				 	networkName : networkName, 
				 	networkDesc : networkDesc,
				 	networkUser : userID,
				 	networkRole : userRole	 	
			 	};
			 	
			 	$.ajax({											// Ajax talking to the data.php file												
					type: "POST",
					url: "php/data.php",
					data: {
						network: network,							
						action: 'updateNetwork'							
					},
					  success: function(data) {						// If connection is successful . 							
						  window.location = 'index.php?m=5';
							    }, 
					  error: function(data) {					// If connection is not successful.  
						  window.location = 'index.php?m=e';
					  }
				});
				
			}); 

			// Join Network when #joinNetwork is clicked 
			$('#joinNetwork').on('click', function () {
				var networkCode =  $('#networkCode').val();
				var userID		= currentUserID; // get user ID
			 	$.ajax({											// Ajax talking to the data.php file												
					type: "POST",
					url: "php/data.php",
					data: {
						networkCode: networkCode,
						userID : userID,							
						action: 'joinNetwork'							
					},
					  success: function(data) {						// If connection is successful . 							
						  $('#joinNetworkNotify').addClass('alert alert-warning').html(data);
							    }, 
					  error: function(data) {					// If connection is not successful.  
						  window.location = 'index.php?m=e';
					  }
				});

			}); 

			
			<?php 
			if(isset($_GET['m'])){
				?>
				$.notification ({
				        content:    '<?php echo $message; ?>',
				        timeout:    5000,
				        border:     true,
				        fill:       true
				     }); 
				<?php 
				
			}
			
			
			?>	
		
		
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
            <p> <a role="button" class="btn btn-small"> Read Documentation </a> <a role="button" class="btn btn-small"> Support Discussion </a> </p>
            
        </div>
    </header>
        
    <div id="homePage" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

                <div class="span4">
                

 
                     <div class="">
                        <h4> My Networks</h4>
                        <hr class="soften">

                        <p><?php echo $networkPrint; ?></p>

                        <p>
                        	<a href="#addNetworkModal" role="button" class="btn btn-small btn-success" data-toggle="modal"> <i class="icon-plus icon-white"></i> Create Network</a> <a href="#joinNetworkModal" role="button" class="btn btn-small btn-primary" data-toggle="modal"><i class="icon-user icon-white"></i> Join Network</a> 
                        </p>

                    </div>
                </div>
                <div class="span4">
                
                    <div class="">
                        <h4>My Courses</h4>
                        <hr class="soften">
                        
                        <ul class="unstyled dashboardList" id="courseList">
                        <p><?php echo $coursePrint; ?></p>
                        </ul>

                    </div>
                 </div>
                <div class="span4">                                                          
                    <div class="">
                        <h4>Recent Discussions</h4>
                        <hr class="soften">

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

	<!-- Create Network Modal -->
	<div id="addNetworkModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 id="myModalLabel">Create Network</h3>
	  </div>
	  <div class="modal-body">
	    <p>Networks are the wider environments that contain all the classes. Membership in networks is determined by the network administrators. Before you create your network make sure that your network does not already exist by going to Network list. </p>

		<div class="form-horizontal">
		  <div class="control-group">
		    <label class="control-label" for="networkName">Name of Network</label>
		    <div class="controls">
		      <input type="text" id="networkName" placeholder="">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label" for="networkDesc">Network Description</label>
		    <div class="controls">
		      <textarea rows="6" class="span4" id="networkDesc"></textarea>
		    </div>
		  </div>
		</div>	  
	  </div>
	  <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
	    <button class="btn btn-primary" id="addNetwork">Add Network</button>
	  </div>
	</div>



	<!-- Join Network Modal -->
	<div id="joinNetworkModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal2Label" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 id="modal2Label">Join Network</h3>
	  </div>
	  <div class="modal-body">
	    <div id="joinNetworkNotify"> </div>
	    <p>If you are given the network code you can join the network. Enter the code below. </p>
		<div class="form-horizontal">
		  <div class="control-group">
		    <label class="control-label" for="networkCode">Network Code</label>
		    <div class="controls">
		      <input type="text" id="networkCode" placeholder="">
		    </div>
		  </div>		  
		</div>	  
	  </div>
	  <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
	    <button class="btn btn-primary" id="joinNetwork">Join Network</button>
	  </div>
	</div>




</body>
</html>    
    
    
<?php

   }  
        
?>