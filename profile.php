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
        
        $uID = $_GET["u"];                      // The user ID from link
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        
        // GET Info About This User
        $userInfo = $dscourse->UserInfo($uID);

        
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

            var dscourse = new Dscourse();              // Fasten seat belts, dscourse is starting...
 
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
    
    	<?php include('php/navbar_includes.php'); ?>

    <!-- Begin profile.php-->

    <div id="profilePage" class=" wrap page">
        <header class="jumbotron subhead">
            <div class="container-fluid">
                <h1><span id="profileName"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></span> <small><span id="profileEmail"><?php  echo $userInfo['username']; ?></span></small></h1>

                <div id="editProfileButtons" class="pull-right">
                    <a href="editprofile.php?u=<?php echo $userID; ?>" id="editProfileButton" class="btn">Edit Profile</a>
                </div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row-fluid" id="profileDetails">
                <div id="userInfoWrap">
                    <div class="span4">
                        <div id="profilePicture"><img src="<?php  echo $userInfo['userPictureURL']; ?>"></div>

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
