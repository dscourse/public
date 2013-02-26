<?php 
date_default_timezone_set('UTC');
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
        
        $uID = $_GET["u"];                      // The user ID from link
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        
        // GET Info About This User
        $userInfo = $dscourse->UserInfo($uID);

        $courseData = $dscourse->GetUserCourses($uID);
	    $totalCourses = count($courseData);
	    $coursePrint = ''; 
	    for($i = 0; $i < $totalCourses; $i++) 
				{
				$cName 	= $courseData[$i]['courseName'];
				$cID	= $courseData[$i]['courseID'];
				$cRole	= $courseData[$i]['userRole'];
				$courseImage = $courseData[$i]['courseImage'];
				$courseNetworks = $dscourse->CourseNetworks($cID); 				
				$coursePrint .='<tr><td courseID="'.$cID.'"><a href="course.php?c='.$cID.'&n='.$courseNetworks[0]['networkID'].'"><img class="thumbSmall" src="'.$courseImage.'" />'.$cName.'</a> </td><td>'.$cRole.'</td></tr>'; 
				}
		
        
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | <?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></title>
    
    <?php include('php/header_includes.php');  ?>
    
    <script type="text/javascript">
    $(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
 
            <?php  if(isset($_GET['m'])){ 
	            	
	            	$message = ''; 
	            	switch ($_GET['m']) {
					    case "1":
					        $message = 'All changes were saved successfully.  '; // do something
					        break;
					} 
            ?>
					$.notification ( 
					    {
					        content:    '<?php echo $message; ?> ',
					        timeout:    5000,
					        border:     true,
					        fill:       true,
					        showTime:   false,
					        icon:       'N',
					        color:      'green'
					    }
					);
            <?php } ?>
        
        }); 
    </script>
</head>

<body>
    
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel active"><a href="#" id="userNav"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></a></li>
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

    <!-- Begin profile.php-->

    <div id="profilePage" class=" wrap page">
        <header class="jumbotron subhead">
            <div class="container-fluid">
            	<div class="row-fluid">
		            <div class="span3">
			            	<div id="userPicture"><img src="<?php  echo $userInfo['userPictureURL']; ?>"> </div>
			            </div>
			            <div class="span9">
			                <h1><span id="profileName"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></span></h1><h1> <small><span id="profileEmail"><?php  echo $userInfo['username']; ?></span></small></h1>
			
			                <div id="editProfileButtons" class="pull-right">
			                    <a href="editprofile.php?u=<?php echo $userID; ?>" id="editProfileButton" class="btn">Edit Profile</a>
			                </div>
			            </div>
            	</div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row-fluid" id="profileDetails">
                <div id="userInfoWrap">
                    <div class="span4">
                        <div id="profilePicture"></div>

                        <div id="profileInfo">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="profileHead">About Me:</td>

                                        <td id="profileAbout1"><?php  echo $userInfo['userAbout']; ?></td>
                                    </tr>

                                    <tr>
                                        <td class="profileHead">Facebook Account</td>

                                        <td id="profileFacebook"><?php  echo $userInfo['userFacebook']; ?></td>
                                    </tr>

                                    <tr>
                                        <td class="profileHead">Twitter Account</td>

                                        <td id="profileTwitter"><?php  echo $userInfo['userTwitter']; ?></td>
                                    </tr>

                                    <tr>
                                        <td class="profileHead">Phone Number</td>

                                        <td id="profilePhone"><?php  echo $userInfo['userPhone']; ?></td>
                                    </tr>

                                    <tr>
                                        <td class="profileHead">Website</td>

                                        <td id="profileWebsite"><a href="<?php  echo $userInfo['userAbout']; ?>"><?php  echo $userInfo['userWebsite']; ?></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class=""></div>
                    </div><!-- end span4 -->

                    <div class="span8 ">
                        <h2>My Courses:</h2>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Course Title</th>

                                    <th>Role</th>
                                </tr>
                            </thead>
                            	<?php echo $coursePrint; ?>
                            <tbody id="profileCourses"></tbody>
                        </table>
                    </div>
                </div>
            </div><!-- end profileDetails-->
        </div><!-- close container -->
    </div><!-- end profile -->
    <?php

       }  
            
    ?>
</body>
</html>
