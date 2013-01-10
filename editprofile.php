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

<!-- Begin Edit Profile-->

    <div id="editProfilePage" class="wrap page">
        <header class="jumbotron subhead">
            <div class="container-fluid">
                <h1><span id="profileName"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></span> <small><span id="profileEmail"><?php  echo $userInfo['username']; ?></span></small></h1>
            </div>
        </header>

        <div class="container-fluid" id="editLayer">
            <div class="row-fluid" id="profileDetails">
                <form name="editProfileForm" action="php/data.php" method="post" enctype="multipart/form-data" class="form-horizontal ">
                    <div class="span8 well offset1">
                        <input type="hidden" name="action" value="editUserInfo">
                        <input type="hidden" name="userEditID" value="<?php  echo $userID; ?>" >
                        
                        <div class="control-group" id="firstNameControl">
                            <label class="control-label" for="firstName">First Name</label>
                            <div class="controls">
                                <input type="text" class="input-large" id="firstName" name="firstName" value="<?php  echo $userInfo['firstName']; ?>">

                                <p class="help-inline">Enter your first name</p>
                            </div>
                        </div>

                        <div class="control-group" id="lastNameControl">
                            <label class="control-label" for="lastName">Last Name</label>
                            <div class="controls">
                                <input type="text" class="input-large" id="lastName" name="lastName" value="<?php  echo $userInfo['lastName']; ?>">
                                <p class="help-inline">Provide the last name of the user.</p>
                            </div>
                        </div>

                        <div class="control-group" id="emailControl">
                            <label class="control-label" for="email">Email</label>

                            <div class="controls">
                                <input type="text" class="input-large" id="email" name="email" disabled value="<?php  echo $userInfo['username']; ?>">

                                <p class="help-inline">You can't change your email.</p>
                            </div>
                        </div>

                        <div class="control-group" id="passwordControl">
                            <label class="control-label" for="password">Password</label>

                            <div class="controls">
                                <input type="password" class="input-large" id="password" name="password">

                                <p class="help-inline">Leave blank if you don't want to change your password.</p>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="userPicture">User Picture</label>

                            <div class="controls">
                                <div id="imgPath"><img src="<?php  echo $userInfo['userPictureURL']; ?>"></div><input type="hidden" name="userPictureURL" id="userPictureURL" value="<?php  echo $userInfo['userPictureURL']; ?>"> <input type="file" name="userPicture" id="userPicture">
                            </div>

                            <p class="help-inline">Upload a new image to change your picture.</p>
                        </div>
                    
	                    <div class="control-group" id="aboutControl">
                        <label class="control-label" for="UserAbout">About Me</label>

                        <div class="controls">
                            <textarea class="span6 textareaFixed" id="userAbout" name="userAbout"><?php  echo $userInfo['userAbout']; ?></textarea><br>
                            <p class="help-inline">Briefly introduce yourself. Please limit your text to 1000 characters.</p>
                        </div>
                    </div>
	
	                    <div class="control-group" id="facebookControl">
                        <label class="control-label" for="facebook">Facebook</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">f</span><input class="span2" id="facebook" name="facebook" size="200" type="text" value="<?php  echo $userInfo['userFacebook']; ?>">
                            </div>
                            <p class="help-inline">Facebook username</p>
                        </div>
                    </div>
	
	                    <div class="control-group" id="twitterControl">
                        <label class="control-label" for="twitter">Twitter</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">t</span><input class="span2" id="twitter" name="twitter" size="200" type="text" value="<?php  echo $userInfo['userTwitter']; ?>">
                            </div>
                            <p class="help-inline">Your Twitter username</p>
                        </div>
                    </div>
	
	                    <div class="control-group" id="phoneControl">
                        <label class="control-label" for="phone">Phone</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">#</span><input class="span2" id="phone" name="phone" size="200" type="text" value="<?php  echo $userInfo['userPhone']; ?>"">
                            </div>
                            <p class="help-inline">Mobile phone number</p>
                        </div>
                    </div>
	
	                    <div class="control-group" id="websiteControl">
                        <label class="control-label" for="website">Website</label>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">url</span><input class="span2" id="website" name="website" size="200" type="text" value="<?php  echo $userInfo['userWebsite']; ?>">
                            </div>
                            <p class="help-inline">Website</p>
                        </div>
                    </div>
            </div>

            <div class="span2">
                <a href="profile.php?u=<?php echo $userID; ?>" id="cancelEditButton" class="btn">Cancel</a> <button id="submitEditButton" type="submit" class="btn">Submit</button>
            </div>
        </form>	
          
        </div>
    </div><!-- close container -->
   </div> <!-- end edit profile -->
    <?php

               }  
                    
            ?>
</body>
</html>
