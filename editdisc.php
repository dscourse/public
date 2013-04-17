<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
	date_default_timezone_set('UTC');
    
        include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query);
		
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
       if(isset($_GET['d'])){                   // Check if discussion id is set. If not send them back to index
           $discID = $_GET['d']; 
           $discussionInfo = $dscourse->DiscussionInfo($discID);
           $dStartDate = explode(" ", $discussionInfo['dStartDate']); 
           $dOpenDate  = explode(" ", $discussionInfo['dOpenDate']);
           $dEndDate   = explode(" ", $discussionInfo['dEndDate']); 
           $dStartHour = explode(":", $dStartDate[1]); 
           $dOpenHour  = explode(":", $dOpenDate[1]);
           $dEndHour   = explode(":", $dEndDate[1]); 
       } else {
            header("Location: index.php");  
            exit(); 
       }        
        if(isset($_GET['c'])){
	        $cID = $_GET['c'];
	        $setCourseInfo = $dscourse->CourseInfo($cID); 
        }
                
       if(isset($_GET['n'])){                      // The network ID from link 
	        $nID = $_GET['n'];        
      	    // GET Info About This Network
      	    $networkInfo = $dscourse->NetWorkInfo($nID);
	      }

  	    $userCourseRole = $dscourse->UserCourseRole($cID, $userID); 
	    
	    if($userCourseRole[0] != 'Instructor' && $userCourseRole[0] != 'TA' ){
		     header('Location: index.php');                  
		     exit(); 
	    }  
	    
	     // Print discussion courses
	     $discussionCourses = $dscourse->GetDiscussionCourses($discID); 
 	     $totalCourses = count($discussionCourses);
 	     $setCoursePrint = ''; 
	     for($j = 0; $j < $totalCourses; $j++) 
	     {
	        $setCoursePrint .= '<tr id="' .$discussionCourses[$j]['courseID'].'" class="dCourseList"><td>' .$discussionCourses[$j]['courseName'].' </td><td><button class="btn removeCourses" courseID="'.$discussionCourses[$j]['courseID'].'">Remove</button> <input type="hidden" name="course[]" value="' .$discussionCourses[$j]['courseID'].'"><input class="deleteToggle" courseID="'.$discussionCourses[$j]['courseID'].'" type="hidden" name="course[]" value="no"></td></tr>';               		     
	     }

        
                
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | Edit discussion</title>
    
     <?php include('php/header_includes.php');  ?>
   
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js" type="text/javascript"></script>
    <script src="js/counter.js" type="text/javascript"></script>
    <script type="text/javascript">
    Array.prototype.transform = function(func){
    	var res = [];
    	for(var i=0; i<this.length; i++){
    		res.push(func(this[i]));
    	}
    	return res;
    }
    
    
$(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>

            
            var courseList = [<?php 
                // Get people in this network 
        $userNetworks = $dscourse->GetUserNetworks($userID);
        $totalNetworks = count($userNetworks); 
        $courseCount = 0; 
        for($i = 0; $i < $totalNetworks; $i++) 
        {
            $networkCourses = $dscourse->NetworkCourses($userNetworks[$i]['networkID']);
            $courseTotal = count($networkCourses);
            for($j = 0; $j < $courseTotal; $j++) 
                {   
                    $roleCheck = $dscourse->UserCourseRole($networkCourses[$j]['courseID'], $userID);
                    if($roleCheck[0] == 'Instructor' || $roleCheck[0] == 'TA'){
                        if($courseCount == 0){ $comma = "";} else { $comma = ",";}                        
                        echo  $comma . "{ 'value' : '".$networkCourses[$j]['courseID']."', 'label' : '".addslashes($networkCourses[$j]['courseName'])."'}"; 
                        $courseCount++;                                                                                                     
                    } 

                }
        }
                ?>
            ];
            
            var associatedCourses = $.makeArray($('.dCourseList')).transform(function(a){
            	return $(a).attr('id');
            });
            
            $('.removeCourses').live('click', function(event) {
                event.preventDefault();
                var func = (associatedCourses.indexOf($(this).attr('id'))==-1)?"no":"yes";
                $(this).siblings('.deleteToggle').val(func);
                $(this).closest('tr').hide();
				/*                
                var currentVal = $(this).siblings('.deleteToggle').val(); // get the delete toggle value
                if(currentVal == 'no'){ // if no 
                	 $(this).siblings('.deleteToggle').val('yes');// toggle value to yes
                	 $(this).addClass('btn-warning');// add class btn-warning
                	 alert("Every discussion must be associated with at least one course.")
               } else if(currentVal == 'yes'){ // if yes 
               		$(this).siblings('.deleteToggle').val('yes'); // toggle value to no
               		$(this).removeClass('btn-warning');// remove class btn warning
               } else if(currentVal == 'add'){
	               	$(this).closest('tr').remove(); // remove this row 
               }
               */
            });

            $("#discussionStartDate").datepicker({ dateFormat: "yy-mm-dd"});          
            $("#discussionOpenDate").datepicker({ dateFormat: "yy-mm-dd"});
            $("#discussionEndDate").datepicker({ dateFormat: "yy-mm-dd"});
			
			$('.hasDatepicker').datepicker("option", "onSelect", function(a, datepicker){
				$('.hasDatepicker').trigger('blur');
			});
			$('.hasDatepicker').next('select').on('change', function(){
				$('.hasDatepicker').trigger('blur');
			});
			
			$.each([$('#sDateTime'),$('oDateTime'),$('eDateTime')], function(i,val){
				val.on('change', function(){
					$('.hasDatepicker').trigger('blur');
				});
			});
				
			$('#discussionPrompt').counter({max:500});
                
            $( "#discussionCourses" ).autocomplete({
                        minLength: 0,
                        source: courseList,
                        focus: function( event, ui ) {
                            $( "#discussionCourses" ).val( ui.item.label );
                            return false;
                        },
                        select: function( event, ui ) {
                        	var old =$('#addCoursesBody').children().filter(function(i){
                        		return $('#addCoursesBody').children().eq(i).attr('id') == ui.item.value;
                        	});
                        	if(old.length > 0){
                        		if(old.filter(':hidden').length>0){
                        			var func = (associatedCourses.indexOf(ui.item.value)>=0)?"no":"add";
                        			var cur = old.filter(':hidden').filter(function(){
                        				return $(this).attr('id') == ui.item.value;
                        			});
                        			cur.show();
                        			cur.find('.deleteToggle').val(func);
                        		}
                        		else{
                        			alert("This course has already been added.");
								}		
                        	}
                        	else{
                        		var func = (associatedCourses.indexOf(ui.item.value)>=0)?"no":"add";
                           		$('#addCoursesBody').append('<tr id="' + ui.item.value +'" class="dCourseList"><td>'+ ui.item.label + ' </td><td><button class="btn removeCourses" courseID="' + ui.item.value + '">Remove</button> <input type="hidden" name="course[]" value="' + ui.item.value + '"> <input class="deleteToggle" courseID="' + ui.item.value + '" type="hidden" name="course[]" value='+func+'></td></tr>');               		     
                            	
                           }
                           $('.discussionCourses').val(' ').focus();
                           return false;
                        }
                    }); 
                    
            //validation for Jquery Datepickers         
           $.validator.addMethod("logicalDate", function(value, el){
				var ind = $(el).index('.hasDatepicker');
		        var valid = false;
		        var start = $('#discussionStartDate').datepicker('getDate');
		        start.setHours($('#discussionStartDate').next('select').val());
		        var open = $("#discussionOpenDate").datepicker('getDate');
		        open.setHours($("#discussionOpenDate").next('select').val());
		        var close = $("#discussionEndDate").datepicker('getDate');
		        close.setHours($("#discussionEndDate").next('select').val());
				switch(ind){ 
					case 0:
						valid = start <= open && start < close;
					break;
				    case 1:
				    	valid = open >= start && open <= close;
					break
					case 2:
						valid = close >= open && close > start;
					break;
				  }
				return valid;
		   }, function(a,b){
		   	//Insert conditional message logic here
		   	console.log(a);
		   	console.log(b);
		   	return "Please check the chronological order of your dates.";
		   });
		   //general form validation rules/messages         
           $('form[name="editDiscussionForm"]').validate({
				rules: {
					discussionQuestion: {
						required: true,
						maxlength: 255,
					},
					discussionPrompt: {
						required: true,
						maxlength: 500,
					},
					discussionStartDate: {
						logicalDate: true	
					},
					discussionOpenDate: {
						logicalDate: true
					},
					discussionEndDate: {
						logicalDate: true
					}
				},
				 messages : {
                            discussionQuestion : {
                            	required: "A discussion question is required.",
                            	maxlength: "Please limit the length of your discussion question to 255 characters."
                            }, 
                            discussionPrompt : {
                            	required: "A discussion prompt is required.",
                            	maxlength: "Please limit the length of your discussion prompt to 500 characters."
                            }
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
					$(element).siblings('.help-inline').html(error);
				} 
		   });
		   $('#discussionFormSubmit').on('click', function(e){
			   if(!$('form[name="editDiscussionForm"]').valid()){
			  		 $('html, body').animate({
	         			scrollTop: 0
	         		});
					if($('.dCourseList:visible').length == 0)
						$('#discAddCourseLabel').html('A discussion must be linked to at least one course.').css('color', 'red');
					else
						$('#discAddCourseLabel').html('').css('color', '#333');
			   	e.preventDefault();	
			   }
			   else if($('.dCourseList:visible').length == 0){
			   		alert("Every discussion must be associated with at least one course.");
			   		e.preventDefault();	
			   }
			   else{
			   //fix dates
							var data = {
								'discussionStartDate': $('#discussionStartDate').val(),
								'sDateTime' : $('#sDateTime').val(),
								'discussionOpenDate': $('#discussionOpenDate').val(),
								'oDateTime' : $('#oDateTime').val(),
								'discussionEndDate' : $('#discussionEndDate').val(),
								'eDateTime' : $('#eDateTime').val()
							}
                            var s = data['discussionStartDate'].split('-');
                            var start = new Date();
                            start.setFullYear(s[0]);
                            start.setMonth(s[1] - 1);
                            start.setDate(s[2]);
                            start.setHours(data['sDateTime']);
                            start.setMinutes(0);
                            var o = data['discussionOpenDate'].split('-');
                            var open = new Date();
                            open.setFullYear(o[0]);
                            open.setMonth(o[1] - 1);
                            open.setDate(o[2]);
                            open.setHours(data['oDateTime']);
                            open.setMinutes(0);
                            var e = data['discussionEndDate'].split('-');
                            var end = new Date();
                            end.setFullYear(e[0]);
                            end.setMonth(e[1] - 1);
                            end.setDate(e[2]);
                            end.setHours(data['eDateTime']);
                            end.setMinutes(0);

                            var off = new Date().getTimezoneOffset();
                            start.setMinutes(start.getMinutes() + off);
                            open.setMinutes(open.getMinutes() + off);
                            end.setMinutes(end.getMinutes() + off);
                            
                            $('#discussionEndDate').val(end.getFullYear()+'-'+(end.getMonth()+1)+'-'+end.getDate());
                            $('#eDateTime').val(end.getHours());
                             $('#discussionStartDate').val(start.getFullYear()+'-'+(start.getMonth()+1)+'-'+start.getDate());
                            $('#sDateTime').val(start.getHours());
                             $('#discussionOpenDate').val(open.getFullYear()+'-'+(open.getMonth()+1)+'-'+open.getDate());
                            $('#oDateTime').val(open.getHours());
                        }
		   });
		   
		    $('#sDateTime > option[value="<?php echo $dStartHour[0]; ?>"]').attr('selected', 'selected');     
           $('#oDateTime > option[value="<?php echo $dOpenHour[0]; ?>"]').attr('selected', 'selected');     
           $('#eDateTime > option[value="<?php echo $dEndHour[0]; ?>"]').attr('selected', 'selected');   
		   
		   //fix dates
			var data = {
				'discussionStartDate': $('#discussionStartDate').val(),
				'sDateTime' : $('#sDateTime').val(),
				'discussionOpenDate': $('#discussionOpenDate').val(),
				'oDateTime' : $('#oDateTime').val(),
				'discussionEndDate' : $('#discussionEndDate').val(),
				'eDateTime' : $('#eDateTime').val()
				}
                            var s = data['discussionStartDate'].split('-');
                            var start = new Date();
                            start.setFullYear(s[0]);
                            start.setMonth(s[1] - 1);
                            start.setDate(s[2]);
                            start.setHours(data['sDateTime']);
                            start.setMinutes(0);
                            var o = data['discussionOpenDate'].split('-');
                            var open = new Date();
                            open.setFullYear(o[0]);
                            open.setMonth(o[1] - 1);
                            open.setDate(o[2]);
                            open.setHours(data['oDateTime']);
                            open.setMinutes(0);
                            var e = data['discussionEndDate'].split('-');
                            var end = new Date();
                            end.setFullYear(e[0]);
                            end.setMonth(e[1] - 1);
                            end.setDate(e[2]);
                            end.setHours(data['eDateTime']);
                            end.setMinutes(0);

                            var off = new Date().getTimezoneOffset();
                            start.setMinutes(start.getMinutes() - off);
                            open.setMinutes(open.getMinutes() - off);
                            end.setMinutes(end.getMinutes() - off);
                            
                            $('#discussionEndDate').val(end.getFullYear()+'-'+(end.getMonth()+1)+'-'+end.getDate());
                            $('#eDateTime').val(end.getHours());
                             $('#discussionStartDate').val(start.getFullYear()+'-'+(start.getMonth()+1)+'-'+start.getDate());
                            $('#sDateTime').val(start.getHours());
                             $('#discussionOpenDate').val(open.getFullYear()+'-'+(open.getMonth()+1)+'-'+open.getDate());
                            $('#oDateTime').val(open.getHours());
		  
});                        
    </script>
</head>

<body>
            <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="course.php?c=<?php echo $cID.'&n='.$nID; ?>" id="coursesNav"><?php echo $setCourseInfo['courseName']; ?></a></li>
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
    
    <!-- Begin Discussions.php -->

    <div id="addDiscussionPage" class=" wrap page">
        <header class="jumbotron subhead">
            <div class="container-fluid">
                <h1>Edit Discussion </h1>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12 ">
                    <div id="discussionForm">
                        <form class="form-horizontal well" name="editDiscussionForm" action="php/data.php" method="post" >
                        <input type="hidden" name="action" value="editDiscussion">
                        <input type="hidden" name="discID" value="<?php echo $discID; ?>">
                        <input type="hidden" name="courseID" value="<?php echo $cID; ?>"> 
                        <input type="hidden" name="networkID" value="<?php echo $nID; ?>"> 
 
                            <div class="control-group" id="discussionQuestionControl">
                                <label class="control-label" for="discussionQuestion">Discussion Question</label>

                                <div class="controls">
                                    <input type="text" class="span8" id="discussionQuestion" name="discussionQuestion" value="<?php echo $discussionInfo['dTitle']; ?>">

                                    <p class="help-inline">Please provide a discussion question.</p>
                                </div>
                            </div>

                            <div class="control-group" id="discussionPromptControl">
                                <label class="control-label" for="discussionPrompt">Discussion Prompt</label>

                                <div class="controls">
                                    <textarea class="span6 textareaFixed" id="discussionPrompt" name="discussionPrompt"><?php echo $discussionInfo['dPrompt']; ?></textarea> 
                                    <span class= "wordCount"></span>
                                   	<p class="help-inline">If you like you can provide prompts to get into details or explain directions for the discussion. Please limit your text to 1000 characters.</p>
                                </div>
                            </div>

                            <div class="control-group" id="discussionStartControl">
                                <label class="control-label" for="discussionStartDate">Discussion Start Date</label>

                                <div class="controls">
                                    <input type="text" class="input-small" id="discussionStartDate" name="discussionStartDate" value="<?php echo $dStartDate[0]; ?>"> 
                                    <select id="sDateTime" name="sDateTime" class=" select input-small">
                                        <option value="01">
                                            1 am
                                        </option>

                                        <option value="02">
                                            2 am
                                        </option>

                                        <option value="03">
                                            3 am
                                        </option>

                                        <option value="04">
                                            4 am
                                        </option>

                                        <option value="05">
                                            5 am
                                        </option>

                                        <option value="06">
                                            6 am
                                        </option>

                                        <option value="07">
                                            7 am
                                        </option>

                                        <option value="08">
                                            8 am
                                        </option>

                                        <option value="09">
                                            9 am
                                        </option>

                                        <option value="10">
                                            10 am
                                        </option>

                                        <option value="11">
                                            11 am
                                        </option>

                                        <option value="12">
                                            12 pm
                                        </option>

                                        <option value="13">
                                            1 pm
                                        </option>

                                        <option value="14">
                                            2 pm
                                        </option>

                                        <option value="15">
                                            3 pm
                                        </option>

                                        <option value="16">
                                            4 pm
                                        </option>

                                        <option value="17">
                                            5 pm
                                        </option>

                                        <option value="18">
                                            6 pm
                                        </option>

                                        <option value="19">
                                            7 pm
                                        </option>

                                        <option value="20">
                                            8 pm
                                        </option>

                                        <option value="21">
                                            09 pm
                                        </option>

                                        <option value="22">
                                            10 pm
                                        </option>

                                        <option value="23">
                                            11 pm
                                        </option>

                                        <option value="24">
                                            12 am
                                        </option>
                                    </select>

                                    <p class="help-inline">Format: YYYY-MM-DD</p>
                                </div>
                            </div>

                            <div class="control-group" id="discussionOpenControl">
                                <label class="control-label" for="discussionOpenDate">Discussion Open Date</label>

                                <div class="controls">
                                    <input type="text" class="input-small" id="discussionOpenDate" name="discussionOpenDate" value="<?php echo $dOpenDate[0]; ?>">
                                     <select id="oDateTime" name="oDateTime" class=" select input-small">
                                        <option value="01">
                                            1 am
                                        </option>

                                        <option value="02">
                                            2 am
                                        </option>

                                        <option value="03">
                                            3 am
                                        </option>

                                        <option value="04">
                                            4 am
                                        </option>

                                        <option value="05">
                                            5 am
                                        </option>

                                        <option value="06">
                                            6 am
                                        </option>

                                        <option value="07">
                                            7 am
                                        </option>

                                        <option value="08">
                                            8 am
                                        </option>

                                        <option value="09">
                                            9 am
                                        </option>

                                        <option value="10">
                                            10 am
                                        </option>

                                        <option value="11">
                                            11 am
                                        </option>

                                        <option value="12">
                                            12 pm
                                        </option>

                                        <option value="13">
                                            1 pm
                                        </option>

                                        <option value="14">
                                            2 pm
                                        </option>

                                        <option value="15">
                                            3 pm
                                        </option>

                                        <option value="16">
                                            4 pm
                                        </option>

                                        <option value="17">
                                            5 pm
                                        </option>

                                        <option value="18">
                                            6 pm
                                        </option>

                                        <option value="19">
                                            7 pm
                                        </option>

                                        <option value="20">
                                            8 pm
                                        </option>

                                        <option value="21">
                                            09 pm
                                        </option>

                                        <option value="22">
                                            10 pm
                                        </option>

                                        <option value="23">
                                            11 pm
                                        </option>

                                        <option value="24">
                                            12 am
                                        </option>
                                    </select>

                                    <p class="help-inline">The date discussion opens to entire class. Format: YYYY-MM-DD</p>
                                </div>
                            </div>

                            <div class="control-group" id="discussionEndControl">
                                <label class="control-label" for="discussionEndDate">Discussion End Date</label>

                                <div class="controls">
                                    <input type="text" class="input-small" id="discussionEndDate" name="discussionEndDate" value="<?php echo $dEndDate[0]; ?>">
                                    <select id="eDateTime" name="eDateTime" class=" select input-small">
                                        <option value="01">
                                            1 am
                                        </option>

                                        <option value="02">
                                            2 am
                                        </option>

                                        <option value="03">
                                            3 am
                                        </option>

                                        <option value="04">
                                            4 am
                                        </option>

                                        <option value="05">
                                            5 am
                                        </option>

                                        <option value="06">
                                            6 am
                                        </option>

                                        <option value="07">
                                            7 am
                                        </option>

                                        <option value="08">
                                            8 am
                                        </option>

                                        <option value="09">
                                            9 am
                                        </option>

                                        <option value="10">
                                            10 am
                                        </option>

                                        <option value="11">
                                            11 am
                                        </option>

                                        <option value="12">
                                            12 pm
                                        </option>

                                        <option value="13">
                                            1 pm
                                        </option>

                                        <option value="14">
                                            2 pm
                                        </option>

                                        <option value="15">
                                            3 pm
                                        </option>

                                        <option value="16">
                                            4 pm
                                        </option>

                                        <option value="17">
                                            5 pm
                                        </option>

                                        <option value="18">
                                            6 pm
                                        </option>

                                        <option value="19">
                                            7 pm
                                        </option>

                                        <option value="20">
                                            8 pm
                                        </option>

                                        <option value="21">
                                            09 pm
                                        </option>

                                        <option value="22">
                                            10 pm
                                        </option>

                                        <option value="23">
                                            11 pm
                                        </option>

                                        <option value="24">
                                            12 am
                                        </option>
                                    </select>

                                    <p class="help-inline">Format: YYYY-MM-DD</p>
                                </div>
                            </div>
                            <hr class="soften">

                            <div class="row-fluid">
                                <div class="span3">
                                    <h3>Courses</h3>

                                    <p>Start typing course names that you would like this discussion to be associated with. Only active courses are listed.</p>

                                    <p id="discAddCourseLabel"></p>

                                    <div id="discInputDiv">
                                        <input type="text" class="input-large discussionCourses" id="discussionCourses" name="discussionCourses">
                                    </div>

                                    <p></p>
                                </div>

                                <div class="span8">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th width="80%">Course Title</th>

                                                <th width="20%">Remove</th>
                                            </tr>
                                        </thead>

                                        <tbody id="addCoursesBody">
	                                        <?php echo $setCoursePrint; ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="soften">

                            <div id="discussionButtondiv">
                                <button class="btn btn-primary" id="discussionFormSubmit">Submit</button> <a href="course.php?c=<?php echo $cID.'&n='.$nID; ?>"  id="editDiscussionCancel" class="btn">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- close container -->
    </div><!-- end discussions -->
</body>
</html>