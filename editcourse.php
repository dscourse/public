<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
    
    if(empty($_SESSION['Username']))                        // Checks to see if user is logged in, if not sends the user to login.php
    {  
        // is cookie set? 
        if (array_key_exists('userCookieDscourse', $_COOKIE)){
             
             $getUserInfo = mysql_query("SELECT * FROM users WHERE UserID = '".$_COOKIE["userCookieDscourse"]."' ");  
  
            if(mysql_num_rows($getUserInfo) == 1)  
            {  
                $row = mysql_fetch_array($getUserInfo);   
          
                $_SESSION['Username'] = $row[1]; 
                $_SESSION['firstName'] = $row[3]; 
                $_SESSION['lastName'] = $row[4];   
                $_SESSION['LoggedIn'] = 1;  
                $_SESSION['status'] = $row[5];
                $_SESSION['UserID'] = $row[0]; 
                header('Location: index.php'); 
                
            } else {
                echo "Error: Could not load user info from cookie.";
            }
            
        } else {

            header('Location: info.php');                   // Not logged and and does not have cookie
        
        }
        
    }  else {    

        include_once('php/dscourse.class.php');
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        
        $cID = $_GET['c']; 
        $courseInfo = $dscourse->CourseInfo($cID);

         $nID = $_GET["n"];                      // The course ID from link

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
    	<script src="js/counter.js" type="text/javascript"></script>
     <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
    <script type="text/javascript">
    //An auxillary function used to check whether or not a given course has an intructor/TA
    Array.prototype.search= function(item, func){
    	for(var i=0;i<this.length;i++){
    		if(func(item, this[i]))
    			return this[i]
    	}
    	return -1;
    }
    
$(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
            
            var nameList = [
                <?php 
                // Get people in this network 
                $networkUsers = $dscourse->NetworkUsers($courseInfo['networkID']); 
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
            
			
			$('#roleButtons .btn').live('click', function () {
				var buttonUserId = $(this).attr('userid');	
				var selectorText = '#roleButtons .btn[userid="' + buttonUserId + '"]';
				$(selectorText).removeClass('active');
				$(this).addClass('active');
				var role = $(this).text(); 
				$(this).siblings('.userRoleInput').val(role);
			});

			$("#courseStartDate").datepicker({ dateFormat: "yy-mm-dd", onSelect: function(){
				$('.hasDatepicker').trigger('blur');
			}});			// Date picker jquery ui initialize for the date fields
			$("#courseEndDate").datepicker({ dateFormat: "yy-mm-dd", onSelect: function(){
				$('.hasDatepicker').trigger('blur');
			}});			// Date picker jquery ui initialize for the date fields
	
			    
		    $( "#coursePeople" ).autocomplete({
	            minLength: 0,
	            source: nameList,
	            focus: function( event, ui ) {
	                $( "#coursePeople" ).val( ui.item.label );
	                return false;
	            },
	            select: function( event, ui ) {
	                $('#addPeopleBody').append('<tr><td><input type="hidden" name="user[]" value="' + ui.item.value + '">' + ui.item.label + ' <\/td><td>' + ui.item.email  + ' <\/td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'+ ui.item.value + '">Instructor<\/button><button class="btn roleB" type="button" userid="'+ ui.item.value + '">TA<\/button><button type="button" class="btn active roleB" userid="'+ ui.item.value + '">Student</button><button class="btn roleB btn-warning" type="button" userid="'+ ui.item.value + '">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Student"></div></td></tr>'); // Build the row of users. 
	                console.log('did this. ')
	                $( "#coursePeople" ).val('');
	                return false;
	            }
	        }); 
	        $('#courseDescription').counter({max:0});
	        
	        $.validator.addMethod("logicalDate", function(value, el){
	        	var one = false;
		        var two = false;
		        var ind = $(el).index('.hasDatepicker');
		        switch(ind){
		        	case 0:
		        		one = $(el).datepicker('getDate')!=null;
		        		two = $('#courseEndDate').datepicker('getDate')==null || ($(el).datepicker('getDate') <= $('#courseEndDate').datepicker('getDate'));
		        	break;
		        	case 1:
		        		one = $(el).datepicker('getDate')!=null;
		        		two = $('#courseStartDate').datepicker('getDate')==null || ($(el).datepicker('getDate') >= $('#courseStartDate').datepicker('getDate'));
		        	break;
		        }
		        return one&&two;
	        }, "Please make sure your dates make sense.");
	        
	         $('form[name="addCourseForm"]').validate({
	         	rules: {
	         		courseName: {
	         			required: true,
	         			maxlength: 255,
	         		},
	         		courseDescription: {
	         			required: true,
	         			maxlength: 500,
	         		},
	         		courseStartDate: {
	         			logicalDate: true,
	         		},
	         		courseEndDate: {
	         			logicalDate: true,
	         		}
	         	},
	         	messages: {
	         		courseName: 'A course name is required.',
	         		courseDescription: 'A course description is required.'
	         	},
	         	highlight: function(label){
	         		$(label).closest('.control-group').removeClass('success');
					$(label).closest('.control-group').addClass('error');
				},
				success: function(label){
					$(label).closest('.control-group').removeClass('error');
					$(label).closest('.control-group').addClass('success');
				},
				errorPlacement: function(error, element){
					$(element).next('.help-inline').html(error);
				} 
	         });
	         $('#submitEditCourse').on('click', function(e){
	         	if(!$('form[name="addCourseForm"]').valid()){
				   	e.preventDefault();	
				   	$('body').scrollTop(0);
				}
				var admin = $('#addPeopleBody').find('.btn').filter('.active').filter(function(){
					return $(this).html() != "Student";
				});
				if(admin.length == 0){
					e.preventDefault();
					alert('Every course must have at least one instructor or teaching assistant.');
				}
		   });
	        
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
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID; ?>&n=<?php echo $nID; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
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
            <h1>Edit Course</h1>
                 <div id="addCourseCancel" class="pull-right">
                    <a href="course.php?c=<?php echo $cID.'&n='.$nID; ?>" class="btn">Cancel</a>
                </div>
        </div>
    </header>

    <div id="addcoursePage" class=" wrap page">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span10 offset1">
                    <div id="courseForm">
                        <form class="form-horizontal well" name="addCourseForm" action="php/data.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="editCourse">
                        <input type="hidden" name="networkID" value="<?php  echo $nID; ?>" >
                        <input type="hidden" name="courseID" value="<?php  echo $cID; ?>" >
                            
                            <div class="alert alert-success">
                                This course is part of the <?php echo $networkInfo['networkName'];  ?> Network.
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseName">Course Name</label>

                                <div class="controls">
                                    <input type="text" class="input-large" id="courseName" name="courseName" value="<?php echo $courseInfo['courseName'];  ?>">

                                    <p class="help-inline">Enter a name for the course</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseDescription">Course Description</label>

                                <div class="controls">
                                    <textarea class="span6 textareaFixed" id="courseDescription" name="courseDescription"><?php echo $courseInfo['courseDescription'];  ?></textarea>
									<span class="wordCount"></span>
                                    <p class="help-inline">Provide a summary for the course.</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseStartDate">Course Start Date</label>

                                <div class="controls">
                                    <input type="text" class="input-small" id="courseStartDate" name="courseStartDate" value="<?php echo $courseInfo['courseStartDate'];  ?>">

                                    <p class="help-inline">Format: YYYY-MM-DD</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseEndDate">Course End Date</label>

                                <div class="controls">
                                    <input type="text" class="input-small" id="courseEndDate" name="courseEndDate" value="<?php echo $courseInfo['courseEndDate'];  ?>">

                                    <p class="help-inline">Format: YYYY-MM-DD</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="editCourseImage">Course Image</label>
                                
                                <div class="controls">
                                    <div id="imgPath"> <img src="<?php echo $courseInfo['courseImage'];  ?>" /></div>
                                    <div class="controls">
                                        <input type="hidden" name="courseImageURL" id="courseImageURL" value="<?php echo $courseInfo['courseImage'];  ?>"> <input type="file" name="editCourseImage" id="editCourseImage">
                                    </div>

                                    <p class="help-inline">Please select a file below 5MB and in gif, png or  jpeg formats. </p>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="courseURL">Course Website</label>

                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on">url</span><input class="span2" id="courseURL" name="courseURL" size="500" type="text" value="<?php echo $courseInfo['courseURL'];  ?>">
                                    </div>

                                    <p class="help-inline">If you have an external website for this course please enter it here.</p>
                                </div>
                            </div>
                             
                            <div class="control-group">
                                <label class="control-label" for="viewOptions">Who can see the course contents?</label>
                                <div class="controls">
									<label class="radio">
									<input type="radio" name="viewOptions" id="viewOptions1" value="members" <?php if($courseInfo['courseView'] == 'members'){ echo 'checked';}  ?>>
									  Instructors, TA's and Students
									</label>
									<label class="radio">
									  <input type="radio" name="viewOptions" id="viewOptions2" value="network" <?php if($courseInfo['courseView'] == 'network'){ echo 'checked';}  ?>>
									  Anyone in this network
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
                                                <th width="30%">Name</th>

                                                <th width="20%">Email</th>

                                                <th width="50%">Role</th>

                                            </tr>
                                        </thead>

                                        <tbody id="addPeopleBody">
                                        	<?php echo $InstructorRows . $TARows . $StudentRows; ?>
                                        	</tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="soften">
                            <button type="submit" name="submitEditCourse" id="submitEditCourse" class="btn btn-primary pull-right">Edit Course </button>
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
