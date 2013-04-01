<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
	date_default_timezone_set('UTC');
    
	    include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query); 
		 
	    $userID = $_SESSION['UserID'];			// Allocate userID to use throughout the page
	     
	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
	    
/*  ------ MARKED FOR DELETION SINCE WE ARE REMOVING VISIBLE NETWORK COMPONENTS  -------
	    
	    // GET User Network List 
	    
	    $networkData = $dscourse->GetUserNetworks($userID);
	    $totalNetworks = count($networkData);
	    $networkPrint = ''; 	
	    if($totalNetworks > 0){
		    for($i = 0; $i < $totalNetworks; $i++) 
					{
					$nName 	= $networkData[$i]['networkName'];
					$nID	= $networkData[$i]['networkID'];
					$nDesc	= $networkData[$i]['networkDesc'];
					$nStatus =  $networkData[$i]['networkStatus'];
					$status = $dscourse->CheckNetworkAccess($userID, $nID); 
					$statusText = ''; $statusClass = ''; 
					if($status == 'owner'){
						$statusText = "Owner";
						$statusClass = 'success'; 
					} else if ($status == 'member'){
						$statusText = "Member";
						$statusClass = 'warning'; 					
					
					} else {
						$statusText = "Not Member";
						$statusClass = 'info'; 					
					}
										
					$networkPrint .='<tr class="'.$statusClass.'" networkID="'.$nID.'"><td><a href="network.php?n='.$nID.'">'.$nName.'</a></td><td> <i>'.$statusText.' </i> </td><td><span class="greyText">'.$nStatus.'<span></td></tr>'; 
					}		     
	    }	else {
		    $networkPrint .='<div class="alert alert-info">You are not part of any networks. Create a network or join one with the buttons below.</div>';  
	    }
*/

	    
	    $courseData = $dscourse->GetUserCourses($userID);
	    $totalCourses = count($courseData);
	    $coursePrint = ''; 
	    $discussionPrint = ''; 
	    $discussionCount = 'none'; 
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
					
					$courseNetworks = $dscourse->CourseNetworks($cID);
					if($courseNetworks){
						$coursePrint .='<li courseID="'.$cID.'"><a href="course.php?c='.$cID.'&n='.$courseNetworks[0]['networkID'].'"><img class="thumbSmall" src="'.$courseImage.'" />'.$cName.'</a>  <i>'.$cRole.'</i></li>'; 						
						// Get discussions for each course
						$discussionData = $dscourse->GetCourseDiscussions($cID);
						$totalDiscussions = count($discussionData);
						if($totalDiscussions > 0){ 
							$discussionCount = 'some'; 
							for($j = 0; $j < $totalDiscussions; $j++)
								{
									$discID = $discussionData[$j]['dID']; 
									$discussionName = $discussionData[$j]['dTitle'];  // Name
									$discussionPrint .='<li discID="'.$cID.'"><a href="discussion.php?d='.$discID.'&c='.$cID.'&n='.$courseNetworks[0]['networkID'].'">'.$discussionName.'</a></li>'; 
								}						
						}
					}
				}
				if($discussionCount == 'none'){
						$discussionPrint .= '<div class="alert alert-info">You are not part of any discussions yet.</div>'; 	

				} 	
			} else {
			    $coursePrint .= '<div class="alert alert-info">  You are not part of any courses yet. To start a course enter or create a network that you belong to and click Add Course.</div> '; 
						$discussionPrint .= '<div class="alert alert-info">You are not part of any discussions yet because you don\'t have any courses.</div>'; 	

			}				
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>dscourse</title>
    <?php include('php/header_includes.php');  ?>
    
	<script type="text/javascript" src="js/counter.js"></script>
    <script type="text/javascript">
		$(function(){
			// Add some global variables about current user if we need them:
		    <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
		    <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
		    <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>

			
/* ------ MARKED FOR DELETION SINCE WE ARE REMOVING VISIBLE NETWORK COMPONENTS  -------


			$('.addNetworkOpen').on('click', function () {
				$('#networkName').val(' '); // clear network Name
				$('#networkDesc').val(' '); // clear Network description
				$('#networkName').css('border-color', 'rgb(204, 204, 204)');
				$('label[for="networkName"]').css('color', '#3335');
			}); 
			$('.joinNetworkOpen').on('click', function () {
				$('#networkCode').val(' '); // clear Network code
			}); 
	
			$('#networkName').on('keyup',function(){
				if(!/^\s*$/.test($(this).val()) && $(this).val().length > 255){
					$(this).closest('.control-group').removeClass('error');
					$(this).closest('.control-group').addClass('success');
				}
				else{
					$(this).closest('.control-group').removeClass('success');	
					$(this).closest('.control-group').addClass('error');
				}
			});
			$('#networkDesc').on('keyup', function(){
				if(!/^\s*$/.test($(this).val())){
					if($(this).val().length > 500){
						$(this).closest('.control-group').removeClass('success');
						$(this).closest('.control-group').addClass('error');
						$(this).siblings('.help-inline').html('*required');
					}
					else{
						$(this).closest('.control-group').addClass('success');
						$(this).closest('.control-group').removeClass('error');
					}
				}
				else{
					$(this).closest('.control-group').removeClass('success');
					$(this).closest('.control-group').addClass('error');
					$(this).siblings('.help-inline').html('*required');
				}
			});
			//Word counter
			$("#networkDesc").counter({min:0, max:500});
			
			// Add Network when #addNetwork is clicked 
			$('#addNetwork').on('click', function () {
				var valid = true; 
				if($('#networkName').val()==' ' || $('#networkName').val()=='' || $('#networkName').val().length > 255){ 
					$('#networkName').closest('.control-group').addClass('error');
					$('#networkName').siblings('.help-inline').html('*required');
					valid = false;
				}
				if($("#networkDesc").val() == '' || $("#networkDesc").val() == ' ' || $('#networkDesc').val().length > 500){
					$("#networkDesc").closest('.control-group').addClass('error');
					$("#networkDesc").siblings('.help-inline').html('*required');
					valid = false;
				}
				if(!valid)
					return false;
				else{
				var networkID 	= 0; // This is a new network, there is no ID yet
				var networkName = $('#networkName').val(); // get network Name
				var networkDesc = $('#networkDesc').val(); // get Network description
				var networkType = $('#networkType').val(); // get Networktype
				var userID		= currentUserID; // get user ID
				var userRole	= 'owner'; 

				networkID =  parseFloat(networkID); 				
			 	var network = {
				 	networkID : networkID,
				 	networkName : networkName, 
				 	networkDesc : networkDesc,
				 	networkType: networkType,
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
				}
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
						  console.log(typeof data );
						  if(typeof data == 'number'){ 
						 	 window.location = 'network.php?n='+data+'&m=9';						  	
						  } else {
							$('#joinNetworkNotify').addClass('alert alert-warning').html(data);
			  
						  }
							    }, 
					  error: function(data) {					// If connection is not successful.  
						  window.location = 'index.php?m=e';
					  }
				});

			});
			
			// popovers for help
			$('#nRoleHelp').popover({
				trigger: 'hover',
				title : 'What\'s this?',
				content : 'Your role in this network defines what you can do. Owners can edit network settings and manage all components of the network. Members can create courses and use discussions. If network if public non-members can also view the network contents.' 
				}); 
			$('#nStatusHelp').popover({
				trigger: 'hover',
				title : 'What\'s this?',
				content : 'The status of the network defines who can view the network. Private networks can be viewed by members only. Public networks can be viewed by anyone' 							
			}); 
						 
*/

			
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

                <div class="span4">
                <h4> Placeholder </h4>

<!--  ------ MARKED FOR DELETION SINCE WE ARE REMOVING VISIBLE NETWORK COMPONENTS  -------
 
                     <div class="">
                        <h4> My Networks</h4>
                        <hr class="soften">

                        <table class="table table-bordered table-hover">
                        <thead> 
                        	<tr>
                        		<td> Network Name </td>
                        		<td> Your Role <span id="nRoleHelp"> <i class="icon-question-sign"></i> </span></td>
                        		<td> Network Status <span id="nStatusHelp"> <i  class="icon-question-sign"></i></span></td>
                        	</tr> 
                        </thead>
                        <tbody> 
                        	<?php echo $networkPrint; ?>
                        </tbody>
                        </table>

                        <p>
                        	<a href="#addNetworkModal" role="button" class="btn btn-small btn-success addNetworkOpen" data-toggle="modal"> <i class="icon-plus icon-white"></i> Create Network</a> <a href="#joinNetworkModal" role="button" class="btn btn-small btn-primary joinNetworkOpen" data-toggle="modal"><i class="icon-user icon-white"></i> Join Network</a> 
                        </p>

                    </div>
-->
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
                        <h4>My Discussions</h4>
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

	<!-- 
	------ MARKED FOR DELETION SINCE WE ARE REMOVING VISIBLE NETWORK COMPONENTS  -------
	<!-- Create Network Modal 
	
	<div id="addNetworkModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
	    <h3 id="myModalLabel">Create Network</h3>
	  </div>
	  <div class="modal-body">
	    <p>Networks are the wider environments that contain all the classes. Membership in networks is determined by the network administrators. Before you create your network make sure that your network does not already exist by going to Network list. </p>

		<div class="form-horizontal">
		  <div class="control-group">
		    <label class="control-label" for="networkName">Name of Network</label>
		    <div class="controls">
		      <input type="text" id="networkName" placeholder="">
		      <p class="help-inline"></p>
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label" for="networkDesc">Network Description</label>
		    <div class="controls">
		      <textarea rows="6" class="span4" id="networkDesc"></textarea>
		      <span class="wordCount"></span>
		      <p class="help-inline"></p>
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label" for="networkType">Network Access Type</label>
		    <div class="controls">
			    <select id="networkType" name="networkType">
				  <option value="private" selected>Private - Only members can view and create courses.</option>
				  <option value="public">Public  - Everyone can view, but only members can create courses .</option>
				</select>
				<p class="help-inline">Participation in discussions is set through course settings. </p>
		    </div>
		  </div>
		</div>	  
	  </div>
	  <div class="modal-footer">
	    <button class="btn btn-info" data-dismiss="modal" aria-hidden="true">Cancel</button>
	    <button class="btn btn-primary" id="addNetwork">Add Network</button>
	  </div>
	</div>



	<!-- Join Network Modal 
	<div id="joinNetworkModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal2Label" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
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
	    <button class="btn btn-info" data-dismiss="modal" aria-hidden="true">Cancel</button>
	    <button class="btn btn-primary" id="joinNetwork">Join Network</button>
	  </div>
	</div>
-->



</body>
</html>    
<?php
        
?>
