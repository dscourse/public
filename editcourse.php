<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
	date_default_timezone_set('UTC');
    
        include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query);
		if($preProcess['role'] != 'Instructor' && $preProcess['role'] != 'TA' ){
		     header('Location: index.php');                  
		     exit(); 
	    }
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        
        $cID = $_GET['c']; 
        $courseInfo = $dscourse->CourseInfo($cID);

        $userNav = $dscourse->UserInfo($userID); 

	    //$userCourseRole = $dscourse->UserCourseRole($cID, $userID); 
	    
        // Get Course Roles
        $courseRoles = $dscourse->CourseRoles($cID);
 	    $totalRoles = count($courseRoles);
	    $InstructorRows = '';
	    $TARows = ''; 
	    $StudentRows = '';  
		$ViewRows = '';
		$BlockedRows= '';
	    for($i = 0; $i < $totalRoles; $i++) 
				{
					$userID 	= $courseRoles[$i]['userID'];
					$userName	= $courseRoles[$i]['firstName'] . ' ' . $courseRoles[$i]['lastName'];
					$userRole 	= $courseRoles[$i]['userRole'];
					$userEmail	= $courseRoles[$i]['username'];
					switch ($userRole) {
					    case "Instructor":
					        $InstructorRows .= '<tr><td><input class="userinput" type="hidden" name="user[]" value="'.$userID.'">'.$userName.'</td><td>'.$userEmail.'</td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB active" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB" userid="'.$userID.'">Student</button><button type="button" class="btn roleB" userid="'.$userID.'">Viewer</button><button type="button" class="btn roleB" userid="'.$userID.'">Blocked</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Instructor"></div></td></tr>'; 
							break;
					    case "TA":
					        $TARows .='<tr><td><input class="userinput" type="hidden" name="user[]" value="'.$userID.'">'.$userName.'</td><td>'.$userEmail.'</td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB active" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB" userid="'.$userID.'">Student</button><button type="button" class="btn roleB" userid="'.$userID.'">Viewer</button><button type="button" class="btn roleB" userid="'.$userID.'">Blocked</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="TA"></div></td></tr>'; 
					        break;
					    case "Student":
					        $StudentRows .= '<tr><td><input  class="userinput" type="hidden" name="user[]" value="'.$userID.'">'.$userName.'</td><td>'.$userEmail.'</td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB active" userid="'.$userID.'">Student</button><button type="button" class="btn roleB" userid="'.$userID.'">Viewer</button><button type="button" class="btn roleB" userid="'.$userID.'">Blocked</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Student"></div></td></tr>'; 
							break;
						case "Viewer":
							$ViewRows .='<tr><td><input class="userinput" type="hidden" name="user[]" value="'.$userID.'">'.$userName.'</td><td>'.$userEmail.'</td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB" userid="'.$userID.'">Student</button><button type="button" class="btn roleB active" userid="'.$userID.'">Viewer</button><button type="button" class="btn roleB" userid="'.$userID.'">Blocked</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Viewer"></div></td></tr>'; 
						break;
						case "Blocked":
							$BlockedRows .= '<tr><td><input class="userinput" type="hidden" name="user[]" value="'.$userID.'">'.$userName.'</td><td>'.$userEmail.'</td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'.$userID.'">Instructor</button><button class="btn roleB" type="button" userid="'.$userID.'">TA</button><button type="button" class="btn roleB" userid="'.$userID.'">Student</button><button type="button" class="btn roleB" userid="'.$userID.'">Viewer</button><button type="button" class="btn roleB active" userid="'.$userID.'">Blocked</button><button class="btn roleB btn-warning" type="button" userid="'.$userID.'">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Blocked"></div></td></tr>'; 
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
   <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js" type="text/javascript"></script>
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
                $users = $dscourse->AllUsers(); 
                $totalUsers = count($users);
                for($i = 0; $i < $totalUsers; $i++) 
                        {                        
                            $uFirstName = $users[$i]['firstName'];
                            $uLastName  = $users[$i]['lastName'];
                            $uID        = $users[$i]['UserID'];
                            $uEmail     = $users[$i]['username'];
                        if($i == $totalUsers-1){ $comma = "";} else { $comma = ",";}
                        echo "{ value: '$uID', label : '$uFirstName $uLastName', email : '$uEmail'}".$comma; 
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
	            	var userExists = false; 
	            	$('.userinput').each(function(){
	            		var value = $(this).attr('value'); 
	            		if(ui.item.value == value){
	            			userExists = true; 
	            		}
	            		console.log(userExists);

	            	}); 
	            	if(!userExists){
						$('#addPeopleBody').append('<tr><td><input class="userinput" type="hidden" name="user[]" value="' + ui.item.value + '">' + ui.item.label + ' <\/td><td>' + ui.item.email  + ' <\/td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'+ ui.item.value + '">Instructor<\/button><button class="btn roleB" type="button" userid="'+ ui.item.value + '">TA<\/button><button type="button" class="btn active roleB" userid="'+ ui.item.value + '">Student</button> <button class=\"btn roleB\" type=\"button\" userid=\"'+ ui.item.value + '\">Viewer<\/button><button class=\"btn roleB\" type=\"button\" userid=\"'+ ui.item.value + '\">Blocked<\/button><button class="btn roleB btn-warning" type="button" userid="'+ ui.item.value + '">Delete</button><input type="hidden" name="user[]" class="userRoleInput" value="Student"></div></td></tr>'); // Build the row of users. 
						$( "#coursePeople" ).val('');
						console.log('done');	            	
	            	} else {
	            		alert('This user is already added'); 
	            	}
	                return false;
	            }
	        }); 
	        
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
	        }, "Please check the chronological order of your dates.");
	        
	         $('form[name="addCourseForm"]').validate({
	         	rules: {
	         		courseName: {
	         			required: true,
	         			maxlength: 255,
	         		},
	         		courseDescription: {
	         			required: true
	         		},
	         		courseStartDate: {
	         			logicalDate: true,
	         		},
	         		courseEndDate: {
	         			logicalDate: true,
	         		}
	         	},
	         	meheadessages: {
	         		courseName: {
	         			required: 'A course name is required.',
	         			maxlength: 'Please limit the length of your course name to 255 characters.'
	         		},
	         		courseDescription: 'A course description is required.'
	         	},
	         	highlight: function(item, label){
	         		$(item).closest('.control-group').removeClass('success');
					$(item).closest('.control-group').addClass('error');
				},
				success: function(label, item){
					$(item).closest('.control-group').removeClass('error');
					$(item).closest('.control-group').addClass('success');
				},
				errorPlacement: function(error, element){
					$(element).next('.help-inline').html(error);
				} 
	         });
	         $('#submitEditCourse').on('click', function(e){
	         	if(!$('form[name="addCourseForm"]').valid()){
				   	e.preventDefault();	
				   	$('html, body').animate({
	         			scrollTop: 0
	        		});
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
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID; ?>" id="coursesNav"><?php echo $courseInfo['courseName']; ?></a></li>
                </ul>

                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?> <b class="caret"></b> </a>

                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <li><a id="profileNav" href="profile.php?u=<?php echo $_SESSION['UserID']; ?>">Profile</a></li>

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
        </div>
    </header>

    <div id="addcoursePage" class=" wrap page formPage">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <div id="courseForm" class="formClass">
                        <form class="form-horizontal " name="addCourseForm" action="php/data.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="editCourse">
                        <input type="hidden" name="courseID" value="<?php  echo $cID; ?>" >
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
                            <hr class="soften" />
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
                                        	<?php echo $InstructorRows . $TARows . $StudentRows . $ViewRows . $BlockedRows; ?>
                                        	</tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="soften">
                            <div class="formButtonWrap">
	                             <a href="course.php?c=<?php echo $cID; ?>" class="btn">Cancel</a>
	                             <button type="submit" name="submitEditCourse" id="submitEditCourse" class="btn btn-primary pull-right">Save</button>
                            </div>
                        </form>
                        <div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
