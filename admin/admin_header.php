<?php 
ini_set('display_errors',1); 
error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php
  include "../php/config.php"; 
  
  if($_SESSION['status']  != 'Administrator'){
	   header('Location: ../index.php');                   // Not admin
  }
  
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dscourse Admin</title>  
 <script type="text/javascript" src="../js/jquery-1.7.1.min.js"> </script>
 <script type="text/javascript" src="../js/bootstrap.js"></script>
 <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
 <script type="text/javascript" src="../js/timeago.jquery.js"></script>
 <script type="text/javascript" src="../js/notification.js"></script>
 <script type="text/javascript" src="../js/validate.js"></script>
 <script type="text/javascript" src="../js/jquery.scrollTo-min.js"></script>
 <link href="../css/entypo.css" media="screen" rel="stylesheet" type="text/css">
 <link href="../css/bootstrap.css" media="screen" rel="stylesheet" type="text/css">
 <link href="../css/style.css" media="screen" rel="stylesheet" type="text/css">
 <link href="../css/animate.css" media="screen" rel="stylesheet" type="text/css">
 <link href="../css/notifications.css" media="screen" rel="stylesheet" type="text/css">
 <style>

 	.adminDashList {
	 	font-size: 30px; 
 	}
 	table {
	 	width: 100%;
	 	border: 1px solid #CCC;
 	}
 	table td {
	 	padding: 10px; 
 	}

 	form {
	 	width: 50%
 	}
 	form input {
	 	width: 100%;
 	}

 </style>
    </head>
<body>
    <div class="navbar navbar-fixed-top navbar-inverse">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="index.php" class="brand" id="homeNav">dscourse admin</a>
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><img class="thumbNav" src="<?php echo '../'.$userNav['userPictureURL']; ?>" />  <?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?> <b class="caret"></b> </a>

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