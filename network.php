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
	    
	    $nID = $_GET["n"]; 						// The network ID from link

	    
	    
	    $userID = $_SESSION['UserID'];			// Allocate userID to use throughout the page
	    
	    // GET Info About This Network
	    $networkInfo = $dscourse->NetWorkInfo($nID);

	    // GET the People in this Network
	    $peopleinNetwork =  $dscourse->NetworkUsers($nID);
	    $totalPeople = count($peopleinNetwork);
	    $peopleListPrint = ''; 
	    for($i = 0; $i < $totalPeople; $i++) 
				{
					$pFirstName = $peopleinNetwork[$i]['firstName'];
					$pLastName	= $peopleinNetwork[$i]['lastName'];
					$pID		= $peopleinNetwork[$i]['UserID'];
					$pPictureURL= $peopleinNetwork[$i]['userPictureURL'];
				$peopleListPrint .='<li> <a href="profile.php?u='.$pID.'" ><img class="thumbSmall" src="'.$pPictureURL.'" /> '.$pFirstName.' '.$pLastName.'</a></li>'; 
				} 
		//Courses in this network
	    $coursesinNetwork =  $dscourse->NetworkCourses($nID);
	    $totalCourses = count($coursesinNetwork);
	    $courseListPrint = ''; 
	    for($i = 0; $i < $totalCourses; $i++) 
				{
					$courseName = $coursesinNetwork[$i]['courseName'];
					$courseID	= $coursesinNetwork[$i]['courseID'];
					$courseImage = $coursesinNetwork[$i]['courseImage'];
				$courseListPrint .='<li> <a href="course.php?c='.$courseID.'&n='.$nID.'" ><img class="thumbSmall" src="'.$courseImage.'" /> '.$courseName.' </a></li>'; 
				} 		
		
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>dscourse | <?php echo $networkInfo['networkName'];  ?> </title>
    
    <?php include('php/header_includes.php');  ?>
	
	<script type="text/javascript">
		$(function(){
			// Add some global variables about current user if we need them:
		    <?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
		    <?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
		    <?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
		    
		    var nameList = [
				<?php 
			    // Get list of all users
			    $usersMini = $dscourse->GetUserMini();
			    $totalUsers = count($usersMini);
			    for($i = 0; $i < $totalUsers; $i++) 
						{
							$uFirstName = $usersMini[$i]['firstName'];
							$uLastName	= $usersMini[$i]['lastName'];
							$uID		= $usersMini[$i]['UserID'];
							$uEmail		= $usersMini[$i]['username'];
						echo '{ value: '.$uID.', label : "'.$uFirstName. ' ' .$uLastName.'", email : "'.$uEmail.'"},'; 
						} 
				?> 		
		    ]; 				
		  

		    // Bind autocomplete to the input id
		    $( "#userSelect" ).autocomplete({
					minLength: 0,
					source: nameList,
					focus: function( event, ui ) {
						$( "#userSelect" ).val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						$('#addUserBody').append('<tr class="userRow" userID="' + ui.item.value + '"><td>' + ui.item.label + ' </td><td>' + ui.item.email  + ' </td><td><button class="btn removeUser">Remove</button>	</td></tr>'); // Build the row of users. 
						$( "#userSelect" ).val('');
						return false;
					}
				}) 
			
		$('.removeUser').on('click', function() {
			$(this).closest('tr').remove();
		});	

		$('#savePeopleToNetwork').on('click', function() {
				var items = [];
				var value; 
				var networkID = <?php echo $nID; ?>// get network id 
				$('.userRow').each(function(){
					value = $(this).attr('userID'); 
					items.push(value);
					console.log(value); 
				}); // get each userID
				$.ajax({										// Ajax talking to the data.php file												
					type: "POST",
					url: "php/data.php",
					data: {
						items: items,
						networkID : networkID,							//   data is sent
						action: 'addUsersToNetwork'							
					},
					  success: function(data) {						// If connection is successful . 
	 					var location = 'network.php?n=' + networkID ; // create location with course id number
						window.location = location;							  
					    }, 
					  error: function(data) {					// If connection is not successful.  
							console.log(data);  // Notification: "Dscourse Log: the connection to data.php failed for adding users to network. Please contact dscourse staff. ");  
					  }
				});	

		});	
		

            <?php if(isset($_GET['m'])){
	            switch ($_GET['m']) {
				    case 2:
				        echo "$.notification ( { title:'Done!', content:'Network changes are saved. ', timeout:5000, border:true, fill:true, icon:'N', color:'#333'});"; 
				        break;
					case 1:
				        echo "$.notification ( { title:'Done!', content:'Users were added to your network. ', timeout:5000, border:true, fill:true, icon:'N', color:'#333'});"; 
					        break;
					    } 
            }            
            ?>

		
		
            $('#showCode').on('click', function() {
	            	$('#networkCode').fadeToggle(); 
	            	var text = $(this).text(); 
	            	if(text == 'Show'){
		            	$(this).text('Hide');
	            	} else {
		            	$(this).text('Show');
	            	}
            }); 
		
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
      <!-- Begin Network Page-->

    <div id="helpPage" class="wrap page" >
        <header class="jumbotron subhead">
            <div class="container-fluid">
                <h1><?php echo $networkInfo['networkName']; ?> </h1>

                <h4><?php echo $networkInfo['networkDesc']; ?></h4>
                <h5>Secret network Code: <button id="showCode" class="btn btn-small">Show</button> <span id="networkCode" class="alert" style="display:none"><b> <?php echo $networkInfo['networkCode']; ?> </b></span> <i> Users can join your network with this code. Please keep the code from public viewing.</i> </h5>
                 <div id="editNetworkButton" class="pull-right">
                    <a href="editnetwork.php?n=<?php echo $nID; ?>" id="editNetworkButton" class="btn">Edit Network</a>
                </div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span4">
                    <div class="">
                        <h3>Courses in this Network <a href="addcourse.php?n=<?php echo $nID; ?>"  class="btn btn-small" ><i class="icon-list-add"></i> Add</a></h3>

                        <hr class="soften">
                             <input type="text" class="input-large" id="filterCourseText" name="filterCourseText" placeholder="Filter by name...">
                    
                        <ul class="unstyled dashboardList">
                            <?php echo $courseListPrint; ?>
                        </ul>
                        
                    </div>
                </div>

                <div class="span4">
                    <div class="">
                        <h3>People in this Network <a href="#addUsertoNetwork" role="button" class="btn btn-small " data-toggle="modal"><i class="icon-user-add"></i> Add</a></h3>
                        <hr class="soften">
                         <input type="text" class="input-large" id="filterUserText" name="filterUserText" placeholder="Filter by name or email ...">
                        <ul class="unstyled dashboardList">
                            <?php echo $peopleListPrint; ?>
                        </ul>
                        
                    </div>
                </div>

                <div class="span4">
                    <div class="well">
                        <h3>Network Activity</h3>


                        <ul class="unstyled">
                            <li>Coming soon...</li>
                        </ul>
                        
                    </div>
                </div>
                                
            </div>
        </div><!-- close container -->
    </div><!-- end networkp page-->


<!-- Modal -->
<div id="addUsertoNetwork" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"> 
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Add Users to Network</h3>
  </div>
  <div class="modal-body">
    <p>
	    <input type="text" name="userSelect" id="userSelect">
    </p>
    	 <table class="table">
            <thead>
                <tr>
                    <th width="40%">Name</th>

                    <th width="40%">Email</th>

                    <th width="20%">Remove</th>
                </tr>
            </thead>
	    	<tbody id="addUserBody">
	            <!-- More rows will be added here -->
	        </tbody>
    	 </table>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button id="savePeopleToNetwork" class="btn btn-primary">Save changes</button>
  </div>
</div>


</body>
</html>    
    
    
<?php

   }  
        
?>