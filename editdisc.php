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
   

    
    <script type="text/javascript">
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
                        echo $comma . "{ 'value' : '".$networkCourses[$j]['courseID']."', 'label' : '".$networkCourses[$j]['courseName']."'}"; 
                        $courseCount++;                                                                                                     
                    } 

                }
        }
                ?>
            ];
            
            $('.removeCourses').live('click', function(event) {
                event.preventDefault();
                currentVal = $(this).siblings('.deleteToggle').val(); // get the delete toggle value
                console.log(currentVal); 
                if(currentVal == 'no'){ // if no 
                	 $(this).siblings('.deleteToggle').val('yes');// toggle value to yes
                	 $(this).addClass('btn-warning');// add class btn-warning
               } else if(currentVal == 'yes'){ // if yes 
               		$(this).siblings('.deleteToggle').val('yes'); // toggle value to no
               		$(this).removeClass('btn-warning');// remove class btn warning
               } else if(currentVal == 'add'){
	               	$(this).closest('tr').remove(); // remove this row 
               }
            });

            $("#discussionStartDate").datepicker({ dateFormat: "yy-mm-dd" });           // Date picker jquery ui initialize for the date fields
            $("#discussionOpenDate").datepicker({ dateFormat: "yy-mm-dd" });
            $("#discussionEndDate").datepicker({ dateFormat: "yy-mm-dd" });

                
            $( "#discussionCourses" ).autocomplete({
                        minLength: 0,
                        source: courseList,
                        focus: function( event, ui ) {
                            $( "#discussionCourses" ).val( ui.item.label );
                            return false;
                        },
                        select: function( event, ui ) {
                            $('#addCoursesBody').append('<tr id="' + ui.item.value +'" class="dCourseList"><td>'+ ui.item.label + ' </td><td><button class="btn removeCourses" courseID="' + ui.item.value + '">Remove</button> <input type="hidden" name="course[]" value="' + ui.item.value + '"> <input class="deleteToggle" courseID="' + ui.item.value + '" type="hidden" name="course[]" value="add"></td></tr>');               		     
                            $('.discussionCourses').val(' ').focus();
                            return false;
                        }
                    }); 
            $('#sDateTime > option[value="<?php echo $dStartHour[0]; ?>"]').attr('selected', 'selected');     
            $('#oDateTime > option[value="<?php echo $dOpenHour[0]; ?>"]').attr('selected', 'selected');     
            $('#eDateTime > option[value="<?php echo $dEndHour[0]; ?>"]').attr('selected', 'selected');     
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
                <h1>Edit Discussion <a href="course.php?c=<?php echo $cID.'&n='.$nID; ?>"  id="editDiscussionCancel" class="btn pull-right">Cancel</a></h1>
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
</textarea>

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

                                    <p></p>

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
                                <button class="btn btn-primary" id="discussionFormSubmit">Submit</button> <button type="button" class="btn btn-info" id="discussionFormCanel">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- close container -->
    </div><!-- end discussions -->
    <?php

                               }  
                                    
                            ?>
</body>
</html>
