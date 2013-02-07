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
			
			var d = new Date();
			$("#courseStartDate").datepicker({ dateFormat: "yy-mm-dd" }).datepicker('setDate',d);	// Date picker jquery ui initialize for the date fields
			d.setFullYear(d.getFullYear()+1);
			$("#courseEndDate").datepicker({ dateFormat: "yy-mm-dd" }).datepicker('setDate',d);			// Date picker jquery ui initialize for the date fields
	
			    
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
	                	$('#addPeopleBody').append('<tr><td><input type="hidden" name="user[]" value="' + ui.item.value + '">' + ui.item.label + ' <\/td><td>' + ui.item.email  + ' <\/td><td><div class="btn-group"  data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" type="button" userid="'+ ui.item.value + '">Instructor<\/button><button class="btn roleB" type="button" userid="'+ ui.item.value + '">TA<\/button><button type="button" class="btn active roleB" userid="'+ ui.item.value + '">Student</button><input type="hidden" name="user[]" class="userRoleInput" value="Student"></div></td><td><button class="btn removePeople" type="button">Remove</button> </td></tr>'); // Build the row of users. 
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
				}
	         });
	         $('#submitNewCourse').on('click', function(e){
	         	if(!$('form[name="addCourseForm"]').valid()){
	         		$('body').scrollTop(0);
				   	e.preventDefault();	
				var admin = $('#addPeopleBody').find('.btn').filter('.active').filter(function(){
					return $(this).index() != 2;
				});
				if(admin.length == 0){
					e.preventDefault();
					alert('Every course must have at least one instructor or teaching assistant.');
				}
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
		   var user = '<tr><td><input type=\"hidden\" name=\"user[]\" value=\"' + cur.value + '\">' + cur.label + ' <\/td><td>' + cur.email  + ' <\/td><td><div class=\"btn-group\"  data-toggle=\"buttons-radio\" id=\"roleButtons\"><button class=\"btn roleB\" type=\"button\" userid=\"'+ cur.value + '\">Instructor<\/button><button class=\"btn roleB\" type=\"button\" userid=\"'+ cur.value + '\">TA<\/button><button type=\"button\" class=\"btn active roleB\" userid=\"'+ cur.value + '\">Student</button><input type=\"hidden\" name=\"user[]\" class=\"userRoleInput\" value=\"Student\"></div></td><td><button class=\"btn removePeople\" type=\"button\">Remove</button> </td></tr>'
		   $('#addPeopleBody').append(user);	
		   $('#addPeopleBody').children().find('.btn').eq(0).click()
			
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
