<?php 
ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "php/config.php"; 
	date_default_timezone_set('UTC');
    
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
        
	    	      
        $userID = $_SESSION['UserID'];          // Allocate userID to use throughout the page
        $userNav = $dscourse->UserInfo($userID); 
        

		

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | Documentation </title>
    
    <?php include('php/header_includes.php');  ?>
    
    <script type="text/javascript">
    $(function(){
            // Add some global variables about current user if we need them:
            <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
            <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
            <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
        
        }); 
    </script>
</head>

<body>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse</a>

                <ul class="nav">
                    <li class="navLevel"><a href="help.php" id="helpNav">Documentation</a></li>
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


    <div id="overlay"></div>
    
        <header class="jumbotron subhead">
            <div class="container-fluid">
	            <div class="row-fluid">
	                <div class="span12">
							<h1>Documentation</h1>
							<p>Learn more about how to use dscourse</p>
              
	                </div>
	            </div>             
            </div>
        </header>

     <div id="helpPage" class=" wrap page" >


        <div class="container-fluid">
            <div class="row-fluid">

                <div class="span8">
                    <div class="">
                        <h3>Navigation</h3>

                        <p>Dscourse is a <b>one-page web application</b>. Clicking the back button on your browser will take you to the previous website but not to the previous page. Please use the links on the page to navigate around the website. You can see all your courses and discussions with the links at the top.</p>
                        <hr class="soften">

                        <p></p>

                        <ul>
                            <li><b>Users</b> page provides a list of all users currently in the dscourse system. You can click on the user names to go to their profile pages.</li>

                            <li style="list-style: none"><br></li>

                            <li><b>Courses</b> link will show the courses you are involved with whether as Instructor, TA or Student. These roles are assigned by the person creating the course.</li>

                            <li style="list-style: none"><br></li>

                            <li><b>Discussions</b> provides a list of all your discussions in all courses. Discussion are also accessible through the individual course pages.</li>
                        </ul>
                        <p>
                        <hr class="soften">
                    </div>
                </div>

                <div class="span4">
                    <div class="well">
                        <h3>Roadmap for v2</h3>

                        <p><em class="timeLog">Here's brief list of planned feature additions for release in version 2</em></p>

                        <p></p>

                        <p></p>

                        <ul class="unstyled">
                            <li>Breadcrumbs for navigation will enable going back and forth in the application without using browser buttons</li>
                        </ul>
                        <p>
                    </div>
                </div>


            </div><!-- close row -->
        </div><!-- close container -->
    </div><!-- close helpPage -->


        <?php

       }  
            
    ?>
</body>
</html>
