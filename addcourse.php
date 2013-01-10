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


	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
	    
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        
        $nID = $_GET['n']; 
        $networkInfo = $dscourse->NetWorkInfo($nID);
                
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | Add Course</title>
    
    <?php include('php/header_includes.php');  ?>
    
    <script type="text/javascript">
$(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
            
            var nameList = [
                <?php 
                // Get people in this network 
                $networkUsers = $dscourse->NetworkUsers($nID); 
                $totalUsers = count($networkUsers);
                for($i = 0; $i < $totalUsers; $i++) 
                        {
                        
                            $uFirstName = $networkUsers[$i]['firstName'];
                            $uLastName  = $networkUsers[$i]['lastName'];
                            $uID        = $networkUsers[$i]['UserID'];
                            $uEmail     = $networkUsers[$i]['username'];
                        if($i == $totalUsers-1){ $comma = "";} else { $comma = ",";}
                        echo '{ value: '.$uID.', label : "'.$uFirstName. ' ' .$uLastName.'", email : "'.$uEmail.'"}'.$comma; 
                        } 
                ?>  
            ];
            
           $('.removePeople').live('click', function() {
				$(this).closest('tr').remove();
			});	
			
			$('#roleButtons .btn').live('click', function () {
				var buttonUserId = $(this).attr('userid');	
				var selectorText = '#roleButtons .btn[userid="' + buttonUserId + '"]';
				$(selectorText).removeClass('active');
				$(this).addClass('active');
				var role = $(this).text(); 
				$(this).siblings('.userRoleInput').val(role);
			});

			$("#courseStartDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
			$("#courseEndDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
	
			    
		    $( "#coursePeople" ).autocomplete({
	            minLength: 0,
	            source: nameList,
	            focus: function( event, ui ) {
	                $( "#coursePeople" ).val( ui.item.label );
	                return false;
	            },
	            select: function( event, ui ) {
	                $('#addPeopleBody').append('<tr><td><input type="hidden" name="user[]" value="' + ui.item.value + '">' + ui.item.label + ' <\/td><td>' + ui.item.email  + ' <\/td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'+ ui.item.value + '">Instructor<\/button><button class="btn roleB" type="button" userid="'+ ui.item.value + '">TA<\/button><button type="button" class="btn active roleB" userid="'+ ui.item.value + '">Student</button><input type="hidden" name="user[]" class="userRoleInput" value="Student"></div></td><td><button class="btn removePeople" type="button">Remove</button> </td></tr>'); // Build the row of users. 
	                console.log('did this. ')
	                $( "#coursePeople" ).val('');
	                return false;
	            }
	        }); 
	        
	      <?php 
			if(isset($_GET['m'])){
				?>
				$.notification ({
				        content:    '<?php echo $message; ?>',
				        timeout:    5000,
				        border:     true,
				        fill:       true,
				        error:      true
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
                    <li class="navLevel"><a href="network.php?n=<?php echo $nID; ?>" id="networkNav"><?php echo $networkInfo['networkName']; ?></a></li>
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
            <h1>Add a new Course</h1>
                 <div id="addCourseCancel" class="pull-right">
                    <a href="network.php?n=<?php echo $nID; ?>" class="btn">Cancel</a>
                </div>
        </div>
    </header>

    <div id="addcoursePage" class=" wrap page">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span10 offset1">
                    <div id="courseForm">
                        <form class="form-horizontal well" name="addCourseForm" action="php/data.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="addCourse">
                        <input type="hidden" name="networkID" value="<?php  echo $nID; ?>" >
                            
                            <div class="alert alert-success">
                                This course will be part of the <?php echo $networkInfo['networkName'];  ?> Network.
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseName">Course Name</label>

                                <div class="controls">
                                    <input type="text" class="input-large" id="courseName" name="courseName">

                                    <p class="help-inline">Enter a name for the course</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseDescription">Course Description</label>

                                <div class="controls">
                                    <textarea class="span6 textareaFixed" id="courseDescription" name="courseDescription">
</textarea>

                                    <p class="help-inline">Provide a summary for the course.</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseStartDate">Course Start Date</label>

                                <div class="controls">
                                    <input type="text" class="input-small" id="courseStartDate" name="courseStartDate">

                                    <p class="help-inline">Format: YYYY-MM-DD</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseEndDate">Course End Date</label>

                                <div class="controls">
                                    <input type="text" class="input-small" id="courseEndDate" name="courseEndDate">

                                    <p class="help-inline">Format: YYYY-MM-DD</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseImage">Course Image</label>

                                <div class="controls">
                                    <div class="controls">
                                        <input type="hidden" name="courseImageURL" id="courseImageURL" value="/assets/img/dscourse_logo4.png"> <input type="file" name="courseImage" id="courseImage">
                                    </div>

                                    <p class="help-inline">Add an image to the course description.</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseURL">Course Website</label>

                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on">url</span><input class="span2" id="courseURL" name="courseURL" size="500" type="text">
                                    </div>

                                    <p class="help-inline">If you have an external website for this course please enter it here.</p>
                                </div>
                            </div>
                             
                            <div class="control-group">
                                <label class="control-label" for="viewOptions">Who can see the course contents?</label>
                                <div class="controls">
									<label class="radio">
									<input type="radio" name="viewOptions" id="viewOptions1" value="members" checked>
									  Instructors, TA's and Students
									</label>
									<label class="radio">
									  <input type="radio" name="viewOptions" id="viewOptions2" value="network">
									  Anyone in this network
									</label>
									<label class="radio">
									  <input type="radio" name="viewOptions" id="viewOptions3" value="everyone">
									  Anyone with an account on dscourse
									</label>                                    
                                </div>
                            </div>                           
 
                             <div class="control-group">
                                <label class="control-label" for="participateOptions">Who can Participate in the discussions?</label>
                                <div class="controls">
									<label class="radio">
									<input type="radio" name="participateOptions" id="participateOptions1" value="members" checked>
									  Instructors, TA's and Students
									</label>
									<label class="radio">
									  <input type="radio" name="participateOptions" id="participateOptions2" value="network">
									  Anyone in this network
									</label>
									<label class="radio">
									  <input type="radio" name="participateOptions" id="participateOptions3" value="everyone">
									  Anyone with an account on dscourse
									</label>                                    
                                </div>
                            </div>                            
                            


                            <hr class="soften">

                            <div class="row-fluid">
                                <div class="span3">
                                    <h3>Add People</h3>

                                    <p>Start typing names. You will be able to change their role as Instructor, TA or Student.</p>

                                    <p><input type="text" class="input-large coursePeople" id="coursePeople" name="coursePeople"></p>
                                </div>

                                <div class="span8">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th width="20%">Name</th>

                                                <th width="20%">Email</th>

                                                <th width="50%">Role</th>

                                                <th width="5%">Remove</th>
                                            </tr>
                                        </thead>

                                        <tbody id="addPeopleBody"></tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="soften">
                            <button type="submit" name="submitNewCourse" id="submitNewCourse" class="btn btn-primary pull-right">Add Course </button>
                        </form>
                    </div>
                </div>
            </div><?php

                           }  
                                
                        ?>
        </div>
    </div>
</body>
</html>
