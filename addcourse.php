<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php";
	date_default_timezone_set('UTC');
    
        include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query);

	    if(isset($_GET['m'])){
		  $m = $_GET['m'];
		  $message = $dscourse->Messages($m);    
	    }
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        $userNav = $dscourse->UserInfo($userID); 
   
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | Add Course</title>
    
    <?php include('php/header_includes.php');  ?>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js" type="text/javascript"></script>
  	<script src="js/counter.js" type="text/javascript"></script>
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
	                // Get all usersr
                $allUsers = $dscourse->AllUsers();  // AllUsers is a function in dscourse.class.php
                $totalUsers = count($allUsers);
                for($i = 0; $i < $totalUsers; $i++) 
                        {
                            $uFirstName = $allUsers[$i]['firstName'];
                            $uLastName  = $allUsers[$i]['lastName'];
                            $uID        = $allUsers[$i]['UserID'];
                            $uEmail     = $allUsers[$i]['username'];
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
			
			var d = new Date();
			// Date picker jquery ui initialize for the date fields
			$("#courseStartDate").datepicker({ dateFormat: "yy-mm-dd", onSelect: function(){
				$('.hasDatepicker').trigger('blur');
			}}).datepicker('setDate',d);	
			d.setFullYear(d.getFullYear()+1);
			$("#courseEndDate").datepicker({ dateFormat: "yy-mm-dd", onSelect: function(){
				$('.hasDatepicker').trigger('blur');
			}
			}).datepicker('setDate',d);			// Date picker jquery ui initialize for the date fields
	
			    
		    $( "#coursePeople" ).autocomplete({
	            minLength: 0,
	            source: nameList,
	            focus: function( event, ui ) {
	                $( "#coursePeople" ).val( ui.item.label );
	                return false;
	            },
	            select: function( event, ui ) {
	            	var id = $(this).attr('userid');
						if($('#addPeopleBody').find('input[value="'+ui.item.value+'"]').length>0){
						alert("This user is already a member of the course.");
						return false;
					}
					else{
	            		console.log(ui)
	                	$('#addPeopleBody').append('<tr><td><input type="hidden" name="user[]" value="' + ui.item.value + '">' + ui.item.label + ' <\/td><td>' + ui.item.email  + ' <\/td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'+ ui.item.value + '">Instructor<\/button><button class="btn roleB" type="button" userid="'+ ui.item.value + '">TA<\/button><button type="button" class="btn active roleB" userid="'+ ui.item.value + '">Student</button><button class=\"btn roleB\" type=\"button\" userid=\"'+ ui.item.value + '\">Viewer<\/button><input type="hidden" name="user[]" class="userRoleInput" value="Student"></div></td><td><button class="btn removePeople" type="button">Remove</button> </td></tr>'); // Build the row of users. 
	                	console.log('did this. ')
	                	$( "#coursePeople" ).val('');
	                	return false;
	                }
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
	         	messages: {
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
	         $('#submitNewCourse').on('click', function(e){
	         	if(!$('form[name="addCourseForm"]').valid()){
	         		$('html, body').animate({
	         			scrollTop: 0
	         		});
				   	e.preventDefault();	
				}
				var admin = $('#addPeopleBody').find('.btn').filter('.active').filter(function(){
					return $(this).html() != "Student";
				});
				if(admin.length == 0){
					e.preventDefault();
					alert('Every course must have at least one instructor or teaching assistant.');
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
			  //make the current user default instructor 
           var cur = nameList.search(currentUserID, function(item,obj){
					if(obj.value==item)
						return true;
					return false;
		   });
		   var user = '<tr><td><input type=\"hidden\" name=\"user[]\" value=\"' + cur.value + '\">' + cur.label + ' <\/td><td>' + cur.email  + ' <\/td><td><div class=\"btn-group\"  data-toggle=\"buttons-radio\" id=\"roleButtons\"><button class=\"btn roleB\" type=\"button\" userid=\"'+ cur.value + '\">Instructor<\/button><button class=\"btn roleB\" type=\"button\" userid=\"'+ cur.value + '\">TA<\/button><button type=\"button\" class=\"btn active roleB\" userid=\"'+ cur.value + '\">Student</button><button class=\"btn roleB\" type=\"button\" userid=\"'+ cur.value + '\">Viewer<\/button><input type=\"hidden\" name=\"user[]\" class=\"userRoleInput\" value=\"Student\"></div></td><td><button class=\"btn removePeople\" type=\"button\">Remove</button> </td></tr>'
		   $('#addPeopleBody').append(user);	
		   $('#addPeopleBody').children().find('.btn').eq(0).click();
});                        
    </script>
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
        </div>
    </header>

    <div id="addcoursePage" class=" wrap page formPage">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span10 offset1">
                    <div id="courseForm" class="formClass">
                        <form class="form-horizontal" name="addCourseForm" action="php/data.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="addCourse">


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
                                    <textarea class="span6 textareaFixed" id="courseDescription" name="courseDescription"></textarea>
									<span class="wordCount"></span>
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

                                    <p class="help-inline">Please select a file below 5MB and in gif, png or  jpeg formats.</p>
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
							<div class="formButtonWrap">
									<a href="index.php" class="btn">Cancel</a>
							        <button type="submit" name="submitNewCourse" id="submitNewCourse" class="btn btn-primary">Add Course </button>
							</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
