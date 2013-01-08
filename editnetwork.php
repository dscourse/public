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

	    $nID = $_GET["n"]; 						// The network ID from link

	    $networkInfo = $dscourse->NetWorkInfo($nID);
                
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | Edit Network </title>
    
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
                    <li class="navLevel"><a href="network.php?n=<?php echo $nID; ?>" id="networkNav"><?php echo $networkInfo['networkName']; ?></a></li>
                </ul>

                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo $userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?> <b class="caret"></b> </a>

                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <li><a id="profileNav" userid="<?php echo $_SESSION['UserID']; ?>">Profile</a></li>

                            <li><a id="usersNav">Users</a></li>

                            <li><a id="helpNav">Help</a></li>

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
            <h1>Edit Network</h1>
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
                        <input type="hidden" name="action" value="updateNetwork">
                        <input type="hidden" name="networkID" value="<?php  echo $nID; ?>" >
                        
                        <div class="form-horizontal">
						  <div class="control-group">
						    <label class="control-label" for="networkName">Name of Network</label>
						    <div class="controls">
						      <input type="text" id="networkName" name="networkName" value="<?php  echo $networkInfo['networkName']; ?>">
						    </div>
						  </div>
						  <div class="control-group">
						    <label class="control-label" for="networkDesc">Network Description</label>
						    <div class="controls">
						      <textarea rows="6" class="span4" name="networkDesc" id="networkDesc"><?php  echo $networkInfo['networkDesc']; ?></textarea>
						    </div>
						  </div>
						</div>	 
                        

                            <hr class="soften">
                            <button type="submit" name="submitEditNetwork" id="submitEditNetwork" class="btn btn-primary pull-right">Edit Network </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php

                           }  
                                
                        ?>
        </div>
    </div>
</body>
</html>