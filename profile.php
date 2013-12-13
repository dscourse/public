<?php 
date_default_timezone_set('UTC');
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
    
        include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query);
		
        $uID = $_GET["u"];                      // The user ID from link
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        $userNav = $dscourse->UserInfo($userID); 
      
        // GET Info About This User
        $userInfo = $dscourse->UserInfo($uID);

        $courseData = $dscourse->GetUserCourses($uID, 'none');
	    $totalCourses = count($courseData);
	    $coursePrint = ''; 
	    for($i = 0; $i < $totalCourses; $i++) 
				{
				$cName 	= $courseData[$i]['courseName'];
				$cID	= $courseData[$i]['courseID'];
				$cRole	= $courseData[$i]['userRole'];
				$courseImage = $courseData[$i]['courseImage'];
				$coursePrint .='<tr><td courseID="'.$cID.'"><a href="course.php?c='.$cID.'"><img class="thumbSmall" src="'.$courseImage.'" />'.$cName.'</a> </td><td>'.$cRole.'</td></tr>'; 
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
					        icon:       '.',
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
            <div class="container-fluid pageMargin">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="#" id="userNav"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></a></li>
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

    <!-- Begin profile.php-->

        <header class="jumbotron subhead">
            <div class="container-fluid">
            	<div class="row-fluid">
			            <div class="span12">
			                <h1><span id="profileName"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></span> </h1>
			            </div>
            	</div>
            </div>
        </header>

    <div id="profilePage" class=" wrap page ">

        <div class="container-fluid">
            <div class="row-fluid" id="profileDetails">
                <div id="userInfoWrap">
                    <div class="span4 greenBox">
                        <div id="profilePicture"></div>

                        <div id="profileInfo">
                            <table class="table">
                                <tbody>
                                	<tr>
			            	<div id="userPicture"><img src="<?php  echo $userInfo['userPictureURL']; ?>"> </div>
                                	</tr>
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
                            <div><a href="editprofile.php?u=<?php echo $userID; ?>" id="editProfileButton" class="btn btn-block btn-info">Edit Profile and Email Notifications</a></div>
                        </div>

                        <div class=""></div>
                    </div><!-- end span4 -->

                    <div class="span8 greenBox">
                        <h2><?php  if($uID == $userID) { echo 'My'; } else { echo $userInfo['firstName'].'\'s'; } ?> Courses:</h2>
 
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
    ?>
</body>
</html>
