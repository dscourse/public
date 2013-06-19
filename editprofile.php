<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
	date_default_timezone_set('UTC');
    
        include_once('php/dscourse.class.php');
		$query = $_SERVER["REQUEST_URI"];
		$preProcess = $dscourse->PreProcess($query);
		
        $uID = $_GET["u"];                      // The user ID from link
        
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        $userNav = $dscourse->UserInfo($userID); 
       
        // GET Info About This User
        $userInfo = $userNav;

		// GET notification settings
		$notifications = $dscourse->GetNotificationSettings($uID);
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | <?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></title>
    
    <?php include('php/header_includes.php');  ?>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js" type="text/javascript"></script>
    <script type="text/javascript">
    
$(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>

			$('form[name="editProfileForm"]').validate({
				rules: {
					firstName: {
						required: true,
						maxlength: 255
					}, 
					lastName: {
						required: true,
						maxlength: 255
					},
					email: {
						required: true,
						email: true
					}
				},
				messages : {
					firstName: "Please provide your first name",
					lastName: "Please provide your last name",
					email: "Please provide a valid email address"
				},
				highlight : function(item, label) {
					$(item).closest('.control-group').removeClass('success');
					$(item).closest('.control-group').addClass('error');
				},
				success : function(label, item) {
					$(item).closest('.control-group').removeClass('error');
					$(item).closest('.control-group').addClass('success');
				},
				errorPlacement : function(error, element) {
					$(element).siblings('.help-inline').html(error);
				}
			});

			$('#submitEditButton').on('click', function(){
				if(!$('form[name="editProfileForm"]').valid()){
					$('html, body').animate({
	         			scrollTop: 0
	         		});
					e.preventDefault();				
				}
			})
			
			
        
        }); 
    </script>
</head>

<body>

   
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="#" id="userNav"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></a></li>
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

        <header class="jumbotron subhead">
            <div class="container-fluid">
                <h1><span id="profileName"><?php  echo $userInfo['firstName'] . ' '.$userInfo['lastName'];  ?></span> <small><span id="profileEmail"><?php  echo $userInfo['username']; ?></span></small></h1>
            </div>
        </header>

    <div id="editProfilePage" class="wrap page formPage">

        <div class="container-fluid" id="editLayer">
            <div class="row-fluid" id="profileDetails">
                <form name="editProfileForm" action="php/data.php" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <div class="span10 offset1 formClass">
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
                                <div id="imgPath"><img src="<?php  echo $userInfo['userPictureURL']; ?>" ></div><input type="hidden" name="userPictureURL" id="userPictureURL" value="<?php  echo $userInfo['userPictureURL']; ?>"> <input type="file" name="userPicture" id="userPicture">
                            </div>

                            <p class="help-inline">Upload a new image to change your picture. Please select a file below 5MB and in gif, png or  jpeg formats. </p>
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
                 <div class="formButtonWrap"> 
                 	<a href="profile.php?u=<?php echo $userID; ?>" id="cancelEditButton" class="btn">Cancel</a> <button id="submitEditButton" type="submit" class="btn btn-primary">Save</button>
                 </div> 
            </div>
			 <div class="span8 well offset1">
				<div class="control-group" id="notificationControl">
				<label class="control-label">Send me an email to let me know when someone </label>	
                <div class="controls">
     				<label class="checkbox">
      				<input type="checkbox" name="comment" value="1" <?php echo ($notifications['comment'])?'checked':'';?>>
     					comments on
    				</label>
    				<label class="checkbox">
      				<input type="checkbox" name="agree" value="1" <?php echo ($notifications['agree'])?'checked':'';?>>
      					agrees with
    				</label>
    				<label class="checkbox">
      				<input type="checkbox" name="disagree" value="1" <?php echo ($notifications['disagree'])?'checked':'';?>>
      					disagrees with
    				</label>
    				<label class="checkbox">
      				<input type="checkbox" name="clarify" value="1" <?php echo ($notifications['clarify'])?'checked':'';?>>
      					asks me to clarify
    				</label>
    				<label class="checkbox">
      				<input type="checkbox" name="offTopic" value="1" <?php echo ($notifications['offTopic'])?'checked':'';?>>
      					marks as off topic
    				</label>
            	</div>
            	<label class="offset3">a post I've made.</label>	
            	<br />
            	<label class="control-label offset">Or mentions me in a post</label>
      				<input style="margin-left:20px" type="checkbox" name="mention" value="1" <?php echo ($notifications['mention'])?'checked':'';?>>
            </div>
			</div>
            <div class="span2">
                <a href="profile.php?u=<?php echo $userID; ?>" id="cancelEditButton" class="btn">Cancel</a> <button id="submitEditButton" type="submit" class="btn">Save</button>
            </div>
        </form>	
        </div>
    </div><!-- close container -->
   </div> <!-- end edit profile -->
</body>
</html>
