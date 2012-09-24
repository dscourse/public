<?php 
	define('MyConst', TRUE);								// Avoids direct access to config.php
	include "scripts/php/config.php"; 

	if(empty($_SESSION['Username']))  						// Checks to see if user is logged in, if not sends the user to login.php
	{  
	    header('Location: info.php');
	    
	}  else {												// User is logged in, show page. 

	?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>dscourse </title>
	<script type="text/javascript" src="assets/js/jquery-1.7.1.min.js"></script>  
	<script type="text/javascript" src="assets/js/bootstrap.js"></script>
	<script type="text/javascript" src="assets/js/jquery-ui-1.8.21.custom.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.scrollTo-min.js"></script>
	

	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />		
	<link href="assets/css/animate.css" media="screen" rel="stylesheet" type="text/css" />		

	<script type="text/javascript" src="scripts/js/validation.js"></script>
	<script type="text/javascript" src="scripts/js/users.js"></script>

	<script type="text/javascript" src="scripts/js/dscourse.js"></script>
	<script type="text/javascript" src="assets/js/fileuploader.js"></script>

<script type="text/javascript">
	
<?php echo "var currentUserStatus = '" .  $_SESSION['status'] . "';"; ?>
	<?php echo "var currentUserID = '" .  $_SESSION['UserID'] . "';"; ?>
	<?php echo "var dUserAgent = '" .  $_SERVER['HTTP_USER_AGENT'] . "';"; ?>
	
	
	var dscourse = new Dscourse();				// Fasten seat belts, dscourse is starting...
	
</script> 

</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
        <div class="container-fluid">
      <a class="brand" id="homeNav">
		  dscourse
		</a>
	
		<ul class="nav">

			<li>
			<a id="usersNav"></span>  Users</a>  
		  </li>

		  <li>	
			<a id="coursesNav">Courses</a>  
		  </li> 
		  <li>	
			<a id="discussionsNav">Discussions</a>  
		  </li> 
		  
		</ul>
		
		<ul class="nav pull-right">
		 
		 <li><a id="profileNav" userid="<?php echo $_SESSION['UserID']; ?>"><?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?>  </a></li>
		 <li><a id="helpNav">Help</a></li>
		 <li><a href="scripts/php/logout.php">Logout</a></li>
		</ul>
				
    </div>
  </div>
</div>

<!-- End of header content-->
<div id="overlay"></div>


<!-- Begin home.php-->
<div id="homePage" class=" wrap page" style="display: none;">
	  	<header class="jumbotron subhead">
		        <div class="container-fluid">
				<div id="homeWrapper">		    	  
			    	  <h1> dscourse<br />
			    	  <p class="lead">A revolutionary new discussion tool for online learning</p>
			    	  </h1> 
			    	  <p id="homeP">Dscourse is a new online discussion tool designed to bring back the affordances of real classroom discussions online by providing users multiple ways to contribute, view and work with posts. </p>
				</div>
		    	  	<div id="homeimg">
			    	  	<div id="myCarousel" class="carousel slide">
						  <!-- Carousel items -->
						  
						  <div class="carousel-inner">
						  
						    <div class="item active">
						    	<img src="assets/img/ds1.png" alt="dscourse1" class="img-polaroid" /> 
						    	<div class="carousel-caption">
			                      <h4>Posting with dscourse</h4>
			                      <p>Inside a discussion you can choose the types of responses you make, which is used to provide diverse methods of interaction. </p>
			                    </div>
						    </div>
						    
						    <div class="item">
						    	<img src="assets/img/ds2.png" alt="dscourse2" class="img-polaroid" />
						    	<div class="carousel-caption">
			                      <h4>Drawing and adding media</h4>
			                      <p>With dscourse your discussion contribution does not have to be in text. You can add drawings, annotated websites, documents, videos and even maps. </p>
			                    </div>
						    </div>
						    
						    <div class="item">
						    	<img src="assets/img/ds3.png" alt="dscourse3" class="img-polaroid" />
						    	<div class="carousel-caption">
			                      <h4>Course view</h4>
			                      <p>Discussions are listed within your course. They may be open to individual participation, or to the entire group.</p>
			                    </div>
						    </div>
						    
						  </div>
						  <!-- Carousel nav -->
						  <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
						  <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
					   </div>
		    	  
		    	  	</div>	
		    	  	

			    	  
		    	  </div>
		    	  		
	  	</header>
		    
	 <div class="container-fluid">

	<div class="row-fluid">
		<div class="span4">
			<div class="well">
				<h3>Welcome to dscourse</h3>
					<hr class="soften" />
				<p>  You are now using the <b>development version 1.0</b> as alpha users. You can create discussions within a course and contribute in diverse ways.  </p><p>Please check our brief screencast to get started on how to interact with the website. We also have help documentation. Check out information page for development plans and a roadmap of upcoming features. </p><p>If you have any questions or concerns feel free to contact us at: <a href="#">bferster - at - virginia.edu</a>    </p>
				<p> Details of the design can be found <a href=""> here</a>. </p>
			</div>	
				
		</div>

	<div class="span4">
				<div class="well">
					<h3>My Courses</h3>
					<hr class="soften" />
					<p>
						<ul class="unstyled" id="myCoursesHome" >
						</ul>
					</p>
					<p class="pull-right"><a href="index.php?page=courses"><em>See all </em></a></p>
				</div>		
			</div>
		
		<div class="span4">
			<div class="well">
				<h3>My Discussions</h3>
				<hr class="soften" />
				<p>
					<ul class="unstyled discussionFeed" >
					</ul>
				</p>
				<p class="pull-right"><a href="index.php?page=discussions"><em>See all </em></a></p>
			</div>		
		</div>
				
	</div>

</div> <!-- close container --> 

	
</div><!-- end home-->


<!-- Begin help Page-->
<div id="helpPage" class="wrap page" style="display: none;">
	  	<header class="jumbotron subhead">
		        <div class="container-fluid">
					  <h1> Help
					  <p class="lead">Tips and How-To's for using dscourse</p>
					  </h1> 
		    	  </div>
	  	</header>
		    
	 <div class="container-fluid">

	<div class="row-fluid">
		<div class="span8">

			<div class="">
				<h3>Navigation</h3>
				<p> Dscourse is a <b>one-page web application</b>. Clicking the back button on your browser will take you to the previous website but not to the previous page. Please use the links on the page to navigate around the website. You can see all your courses and discussions with the links at the top. </p>
				<hr class="soften" />
				<p><ul><li><b>Users</b> page provides a list of all users currently in the dscourse system. You can click on the user names to go to their profile pages.</li><br /><li><b>Courses</b> link will show the courses you are involved with whether as Instructor, TA or Student. These roles are assigned by the person creating the course.</li><br /> <li><b>Discussions</b> provides a list of all your discussions in all courses. Discussion are also accessible through the individual course pages.  </li></ul></p>
				<hr class="soften" />
				
			</div>	
				
		</div>

		<div class="span4">
			<div class="well">
				<h3>Roadmap for v2</h3>
				<p><em class="timeLog">Here's brief list of planned feature additions for release in version 2</em><p>
				<p>
					<ul class="unstyled" >
						<li> Breadcrumbs for navigation will enable going back and forth in the application without using browser buttons </li>
					</ul>
				</p>
			</div>	
				
		</div>
				
	</div>

</div> <!-- close container --> 

	
</div><!-- end help-->


<!-- Begin users.php-->

<div id="usersPage" class=" wrap page" style="display: none;">

	  	<header class="jumbotron subhead">
		        <div class="container-fluid">
		    	  <h1> Users </h1> 
		    	  <div class="headerTabs"> 
		    	  	<a id="userListLink" class="headerLinks">User List</a> 
		    	  	<?php 
		    	  	 if($_SESSION['status'] == 'Administrator') {
			    	  	echo '<a id="addUserLink" class="linkGrey headerLinks">Add User</a>';
		    	  	 } ?> 
		    	  	
		    	  	
		    	  	 
		    	  </div>
		    </div>	
	  	</header>
	
	 <div class="container-fluid">

			
	<div class="row-fluid">	
		
		<div class="span12">

		<div id="notify">  </div> <!-- Notifications for erros etc.  --> 
		
		<div id="addUserForm">
			
				<div class="form-horizontal well">
					
					<div id="userIDInput"></div>
					
				    <div class="control-group" id="firstNameControl">
				      <label class="control-label" for="firstName">First Name</label>
				      <div class="controls">
				        <input type="text" class="input-large" id="firstName" name="firstName">
				        <p class="help-inline">Provide the First Name of the user</p>
				      </div>
				    </div>

				    <div class="control-group" id="lastNameControl">
				      <label class="control-label" for="lastName">Last Name</label>
				      <div class="controls">
				        <input type="text" class="input-large" id="lastName" name="lastName">
				        <p class="help-inline">Provide the last name of the user. </p>
				      </div>
				    </div>

				    <div class="control-group" id="emailControl">
				      <label class="control-label" for="email">Email</label>
				      <div class="controls">
				        <input type="text" class="input-large" id="email" name="email">
				        <p class="help-inline">This will also be the username</p>
				      </div>
				    </div>				    				    

				    <div class="control-group" id="passwordControl">
				      <label class="control-label" for="password">Password</label>
				      <div class="controls">
				        <input type="password" class="input-large" id="password" name="password">
				        <p class="help-inline">Enter password. </p>
				      </div>
				    </div>
				    
				    <div class="control-group" id="sysControl">
				      <label class="control-label" for="sysRole">System Role</label>
				      <div class="controls">
				        <select id="sysRole" name="sysRole" class="span2">
			                <option value="Guest">Guest</option> <!-- Guest can only view -->
			                <option value="Participant">Participant</option> <!-- Participant can add a course or delete their own course -->
			                <option value="Administrator">Administrator</option> <!-- Administrator has full privileges  -->
			              </select>
				        <p class="help-inline">Defines the privileges of the user on the system (is not related to courses).</p>
				      </div>
				    </div>	
				    
				    <div class="control-group" id="userStatusControl">
				      <label class="control-label" for="userStatus">User Status</label>
				      <div class="controls">
				        <select id="userStatus" name="userStatus" class="span2">
			                <option value="active" >active</option> 
			                <option value="inactive">inactive</option> 
			                <option value="other">other</option> 
			              </select>
				        <p class="help-inline">Users need to be active to participate. </p>
				      </div>
				    </div>	
					
					
					<div class="control-group">
				      <label class="control-label" for="userPicture">User Picture</label>    
				      <div class="controls">
				      	<div id="imgPath"></div>
				      	<input type="hidden" name="userPicture" id="userPicture" value="assets/img/dscourse_logo4.png">				
					 <div id="file-uploader-user">
								<noscript>
								        <p>Please enable JavaScript to use file uploader.</p>
								        <!-- or put a simple form for upload here -->
								    </noscript>
						  </div>
						 <p class="help-inline">You can drag files to this space to add. Your image needs to be less than 1MB. </p>

				      </div>
				    </div>	
				    
					
					<div class="control-group" id="aboutControl">
				      <label class="control-label" for="about">About Me</label>
				      <div class="controls">
				       
		                <textarea class="span6 textareaFixed" id="userAbout" name="userAbout" ></textarea>
		              
				       <br /> <p class="help-inline">Briefly introduce yourself. Please limit your text to 1000 characters.</p>
				      </div>
				    </div>	
					
					
				    <div class="control-group" id="facebookControl">
				      <label class="control-label" for="facebook">Facebook</label>
				      <div class="controls">
				       <div class="input-prepend">
		                <span class="add-on">f</span><input class="span2" id="facebook" name="facebook" size="200" type="text">
		              </div>
				        <p class="help-inline"> Facebook username</p>
				      </div>
				    </div>	
				    
				    
				    <div class="control-group" id="twitterControl">
				      <label class="control-label" for="twitter">Twitter</label>
				      <div class="controls">
				       <div class="input-prepend">
		                <span class="add-on">t</span><input class="span2" id="twitter" name="twitter" size="200" type="text">
		              </div>
				        <p class="help-inline">Your Twitter username</p>
				      </div>
				    </div>	

				    <div class="control-group" id="phoneControl">
				      <label class="control-label" for="phone">Phone</label>
				      <div class="controls">
				       <div class="input-prepend">
		                <span class="add-on">#</span><input class="span2" id="phone" name="phone" size="200" type="text">
		              </div>
  			          <p class="help-inline">Mobile phone number </p>
				      </div>
				    </div>	

				    <div class="control-group" id="websiteControl">
				      <label class="control-label" for="website">Website</label>
				      <div class="controls">
					    <div class="input-prepend">
		                <span class="add-on">url</span><input class="span2" id="website" name="website" size="200" type="text">
		              </div>				        
		              <p class="help-inline">Website </p>
				      </div>
				    </div>	
				    
					<div id="userButtonDiv"><button class="btn btn-primary" id="addUserButton">Add User</button> <button class="btn btn-info id="cancelUser">Cancel</button></div>
					
				    				  
				</div>							
			</div>
		
		</div>

		
		<div class="span12">
			<div id="userList">
			
			<div class="span4 offset4" id="filterUser">				        
							<input type="text" class="input-xlarge" id="filterUserText" name="filterUserText" placeholder="Filter by first or last name or email ...">
									<hr class="soften" />
			</div>
		
			
				    <table id="userTable" class="table table-striped table-bordered">
					    <thead>
						    <tr>
							    <th scope="col" id="first" width="20%">First Name</th>
							    <th scope="col" id="last" width="20%">Last Name</th>
							    <th scope="col" id="userEmail" width="30%">Email Address </th>
							    <th scope="col" id="sysrole" width="10%">System Role</th>
							    <th scope="col" id="status" width="10%">Status</th>
							    <th scope="col" id="actions" width="10%">Edit</th>
						    </tr>
					    </thead>
					    <tbody id="userData">
	
						</tbody>
					</table>
					
				
			</div>
			
		
		</div>

	</div>

</div> <!-- close container --> 


</div><!-- End users -->


<!-- Begin courses.php-->


<div id="coursesPage" class=" wrap page" style="display: none;">
	
	  	<header class="jumbotron subhead">
		        <div class="container-fluid">
		    <h1>Courses  </h1> 
				  <div class="headerTabs">
					  <a id="allCoursesView" class="headerLinks">All Courses</a> 
					  <a id="activeCoursesView" class="headerLinks linkGrey">Active </a> 
					  <a id="archivedCoursesView" class="headerLinks linkGrey">Archived </a>
					  <a id="courseFormLink" class="headerLinks linkGrey">Add Course </a>
				  </div>
			
			</div>	
	  	</header>

	 <div class="container-fluid">
		
	<div class="row-fluid">

		<div class="span12">

		<div id="notifyCourse">  </div> <!-- Notifications for erros etc.  --> 
		
			<div>
				
				<!-- Course list layer -->
				<div id="courses">
					<table class="table table-striped">
						<thead>
							<tr>
								<th width="20%">Title </th>
					            <th width="30%">Description</th>
					            <th width="10%">Status</th>
					            <th width="20%">Instructors <span style="font-weight: normal;"> | TAs</span></th>
					            <th width="10%"># of Students</th>
								<th width="10%">Edit</th>
							</tr>
						</thead>
				        <tbody id="tablebody">

				        </tbody>
				     </table>
				</div>
				
				
				<!-- Course Form layer // Both ADD and EDIT use the same form  -->				
				<div id="courseForm">
					<div class="form-horizontal well">
					
						<div id="courseIDInput"></div>

					    <div class="control-group">
					      <label class="control-label" for="courseName">Course Name</label>
					      <div class="controls">
					        <input type="text" class="input-large" id="courseName" name="courseName">
					        <p class="help-inline">Enter a name for the course</p>
					      </div>
					    </div>	
					  				    
					     <div class="control-group">
					      <label class="control-label" for="courseDescription">Course Description</label>
					      	<div class="controls">
			                <textarea class="span6 textareaFixed" id="courseDescription" name="courseDescription"></textarea>
					        <p class="help-inline">Provide a summary for the course.</p>
					      </div>
					    </div>					
						
					    <div class="control-group">
					      <label class="control-label" for="courseStartDate">Course Start Date</label>
					      <div class="controls">
					        <input type="text" class="input-large" id="courseStartDate" name="courseStartDate">
					        <p class="help-inline">Format: YYYY-MM-DD </p>
					      </div>
					    </div>						
						
					    <div class="control-group">
					      <label class="control-label" for="courseEndDate">Course End Date</label>
					      <div class="controls">
					        <input type="text" class="input-large" id="courseEndDate" name="courseEndDate">
					        <p class="help-inline">Format: YYYY-MM-DD </p>
					      </div>
					    </div>	
					    					
					<div class="control-group">
				      <label class="control-label" for="courseImage">Course Image</label>    
				      <div class="controls">
				      	<div id="cimgPath"></div>
				      	<input type="hidden" name="courseImage" id="courseImage" value="/assets/img/dscourse_logo4.png">				
					 <div id="file-uploader-course">
								<noscript>
								        <p>Please enable JavaScript to use file uploader.</p>
								        <!-- or put a simple form for upload here -->
								    </noscript>
						  </div>
						 <p class="help-inline">You can drag files to this space to add. Your image needs to be less than 1MB. </p>

				      </div>
				    </div>	
				    
					    <div class="control-group">
					      <label class="control-label" for="courseURL">Course Website</label>
					      <div class="controls">
						    <div class="input-prepend">
			                <span class="add-on">url</span><input class="span2" id="courseURL" name="courseURL" size="500" type="text">
			              </div>				        
			              <p class="help-inline">If you have an external website for this course please enter it here. </p>
					      </div>
					    </div>
					    
					    <hr class="soften" />
					    <div class="row-fluid">
					    	
					    	<div class="span3">
						    <h3> Add People </h3>
						    <p>Start typing names. You will be able to change their role as Instructor, TA or Student. </p>
	
						    <p> <input type="text" class="input-large coursePeople" id="coursePeople" name="coursePeople" >
						    </p>
					    	</div>
						    
						    <div class="span8">
							    <table class="table">
								    <thead>
								    	<tr>
								    		<th width="20%">Name</th>
								    		<th width="20%">Email</th>
								    		<th width="50%">Role</th>
								    		<th width="5%">Remove</th>
								    	</tr>
								    </thead>
								    <tbody id="addPeopleBody">
								    	<!-- More rows will be added here -->
								    	
								    </tbody>
							    </table>
						    </div>   
					    </div>
					    <hr class="soften" />

					    <div id="courseButtonDiv"> </div>
					
					
					</div>
				</div>
				
				
			
				
			</div>	<!-- End span12 div -->	
		</div>	<!-- End row div -->

				
	</div> 

</div> <!-- close container --> 


</div><!-- end courses -->


<!-- Begin Discussions.php -->

<div id="discussionsPage" class=" wrap page" style="display: none;">
	
		  	<header class="jumbotron subhead">
		        <div class="container-fluid">
			<h1>Discussions</h1> 
			<div class="headerTabs"> 
			  		<a id="allDiscussionView" class="headerLinks">All Discussions</a> 
			  		<a id="addDiscussionView" class="headerLinks">Start New Discussion</a> 
		   
			  	</div>
			 </div>	
		  	</header>

	 <div class="container-fluid">
		
	<div class="row-fluid">
		<div class="span12 ">

			<!-- Discussion list layer -->
				<div id="discussions">
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="40%">Discussion Question </th>
					            <th width="30%">Course</th>
					            <th width="10%">Start Date</th>
								<th width="10%">End Date</th>
								<th width="10%">Edit</th>
							</tr>
						</thead>
				        <tbody id="tableBodyDiscussions">

				        </tbody>
				     </table>
				</div>
		
		
			<!-- Discussion Form layer // Both ADD and EDIT use the same form  -->				
			<div id="discussionForm">
				<div class="form-horizontal well">
				    <div class="control-group" id="discussionQuestionControl">
				      <label class="control-label" for="discussionQuestion">Discussion Question</label>
				      <div class="controls">
				        <input type="text" class="span8" id="discussionQuestion" name="discussionQuestion">
				        <p class="help-inline">Please provide a discussion question.</p>
				      </div>
				    </div>	
		
					<div class="control-group" id="discussionPromptControl">
				      <label class="control-label" for="discussionPrompt">Discussion Prompt</label>
				      <div class="controls">
				       
		                <textarea class="span6 textareaFixed" id="discussionPrompt" name="discussionPrompt" ></textarea>
		              
				        <p class="help-inline">If you like you can provide prompts to get into details or explain directions for the discussion. Please limit your text to 1000 characters.</p>
				      </div>
				    </div>
				    
				    <input id="discIdHidden" type="hidden" name="discIdHidden" value="">	
		

					    <div class="control-group" id="discussionStartControl">
					      <label class="control-label" for="discussionStartDate">Discussion Start Date</label>
					      <div class="controls">
					        <input type="text" class="input-small" id="discussionStartDate" name="discussionStartDate">
					        <p class="help-inline">Format: YYYY-MM-DD </p>
					      </div>
					    </div>						

					    <div class="control-group" id="discussionOpenControl">
					      <label class="control-label" for="discussionOpenDate">Discussion Open Date</label>
					      <div class="controls">
					        <input type="text" class="input-small" id="discussionOpenDate" name="discussionOpenDate">
					        <p class="help-inline">The date discussion opens to entire class. Format: YYYY-MM-DD </p>
					      </div>
					    </div>			
		
								
					    <div class="control-group" id="discussionEndControl">
					      <label class="control-label" for="discussionEndDate">Discussion End Date</label>
					      <div class="controls">
					        <input type="text" class="input-small" id="discussionEndDate" name="discussionEndDate">
					        <p class="help-inline">Format: YYYY-MM-DD </p>
					      </div>
					    </div>			
		

<hr class="soften" />
					    <div class="row-fluid">
					    	
					    	<div class="span3">
						    <h3> Courses </h3>
						    <p>Start typing course names that you would like this discussion to be associated with. Only active courses are listed. </p>
	
						    <p>
						    <div id="discInputDiv">
						    	<input type="text" class="input-large discussionCourses" id="discussionCourses" name="discussionCourses" >

						     </div>
						    </p>
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
								    	<!-- More rows will be added here -->
								    	
								    </tbody>
							    </table>
						    </div>   
					    </div>
					    <hr class="soften" />

					    <div id="discussionButtondiv"> <button class="btn btn-primary" id="discussionFormSubmit">Submit</button> <button class="btn btn-info" id="discussionFormCanel">Cancel</button></div>		
		
		
				</div>
			</div>
		
			
		</div>
				
	</div>

</div> <!-- close container --> 


</div><!-- end discussions -->



<!-- Begin profile.php-->

<div id="profilePage" class=" wrap page" style="display: none;">

	  	<header class="jumbotron subhead">
		        <div class="container-fluid">
			    <h1>
			    	<span id="profileName"></span>
			    	<small><span id="profileEmail"> </span></small>
			    	<button id="profilePageEdit" class="btn btn-info pull-right" profilePageID=""> Edit Profile </button>
			    </h1>
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
					            <td class="profileHead" >About Me:</td>
					            <td id="profileAbout1"></td>
					          </tr>
					          <tr>
					          	<td class="profileHead" >Facebook Account</td>
					            <td id="profileFacebook"></td>
					          </tr>
					          <tr>
					          	<td class="profileHead" >Twitter Account</td>
					            <td id="profileTwitter"></td>
					          </tr>
					          <tr>
					          	<td class="profileHead" >Phone Number</td>
					            <td id="profilePhone"></td>
					          </tr>
					          <tr>
					          	<td class="profileHead" >Website</td>
					            <td id="profileWebsite"></td>
					          </tr>
					        </tbody>
					     </table>
				    </div> 
				</div><!-- end span4 -->  
				<div class="span8 ">
						<h2>My Courses:</h2>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Course Title</th>
									<th>Role </th>
								</tr>
							</thead>
					        <tbody id="profileCourses">
					         
					        </tbody>
					      </table>
				 </div>
		 </div>   
		    
		    
		 </div><!-- end profileDetails-->

</div> <!-- close container --> 
   		      

</div><!-- end profile -->


<!-- Begin course.php -->

<div id="coursePage" class=" wrap page" style="display: none;">
		  	<header class="jumbotron subhead">
		        <div class="container-fluid">
		    	    <h1><span id="iCourseName">Course Name </span><small></small>
		    	    </h1>
		    	 </div>
		  	</header>

	 <div class="container-fluid">

	  
<div class="row-fluid">

	
	<div class="span4">
  
  		<h3>Course information:</h3>
  	
  		<div id="iCoursePicture"></div>
  		
  		<div class="well"> <p id="iCourseDescription"></p> </div>
		
		<div id="iCourseInfo"> 
		
			<table class="table">
			
			        <tbody>
			          <tr>
			          	<td class="profileHead" >Instructors:</td>
			            <td id="iCourseInstructors"></td>
			          </tr>
			          <tr>
			          	<td class="profileHead" >Teaching Assistants:</td>
			            <td id="iCourseTAs"></td>
			          </tr>
			    
			          <tr>
			            <td class="profileHead" >Start Date:</td>
			            <td id="iCourseStartDate"></td>
			          </tr>
			          <tr>
			          	<td class="profileHead" >End Date</td>
			            <td id="iCourseEndDate"></td>
			          </tr>
			        <tr>
			          	<td class="profileHead" >Course Website</td>
			            <td id="iCourseURL"></td>
			          </tr>
			        </tbody>
			    </table>
		</div> <!-- close iCourseInfo-->
		
				

	</div><!-- close span4 -->

	<div class="span8">
		
			
	
	<div id="courseDiscussions"> 
			<h3>Course Discussions</h3>
			
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="60%">Discussion Question </th>
					            <th width="20%">Status</th>
					            <th width="20%"># of Responses</th>
							</tr>
						</thead>
				        <tbody id="courseDiscussionsBody">

				        </tbody>
				     </table> 	
		</div>	 <!-- close courseDiscussions-->
		

	<div id="courseStudents"> 
			<h3>Students in this Course</h3>
			
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="60%">Name </th>
					            <th width="40%">Email Address</th>
							</tr>
						</thead>
				        <tbody id="courseStudentsBody">

				        </tbody>
				     </table> 	
		</div>	 <!-- close courseDiscussions-->
				
		
		
		<!-- Hiding the notes for version 1 
		<h3> Class Notes </h3>
		<div id="classNotes">
		
			
					<table class="table table-striped table-bordered">

				        <tbody id="courseNoteBody">

				        </tbody>
				     </table> 	
				
				
				<div id="classNoteForm" class="form-horizontal">
					<input id="noteAuthor" type="hidden" name="noteAuthor" value="<?php echo $_SESSION['UserID'];?>">
					<input id="noteType" type="hidden" name="noteType" value="course">
					<input id="noteSource" type="hidden" name="noteSource" value="">
					<textarea  id="inputNote" placeholder="..."></textarea>
					<button id="addNewNote" type="post" class="btn btn-info">Add New Note</button>

				</div>
			
			
					
			</div>

		
		</div> close classNotes --> 
		
		
	</div> <!-- close span8 -->

</div> <!-- close row -->

</div> <!-- close container --> 


 </div> <!-- close coursePage -->   
    
<!-- Begin individual discussion page -->


	
<div id="discussionWrap" class=" page" style="display: none;">

		  	<header class="jumbotron subhead">
		        <div class="container-fluid">
			    <h1>
			    	<span class="boxHeaders">
							
								<span id="dTitleView" ></span>
								<div class="sayBut2" postID="0">say</div> 
								<input id="dIDhidden" type="hidden" name="discID" value="">
	
						</span> 
			    </h1>
			    	
			    	
			    	<div id="controls" class="">
						<div class="btn-group" id="zoomButtons">
							  <button class="zButtons btn btn-small" zoom="in"> <span class="typicn zoomIn "> </span> </button>
							  <button class="zButtons btn btn-small" zoom="out"> <span class="typicn zoomOut "></span> </button>
							  <button class="zButtons btn btn-small" zoom="reset"> <span class="typicn expand "></span> </button>
						</div>
						
						<button id="showTimeline" class="btn btn-small btn-info"> <span class="typicn time "></span> Timeline </button>
						
						<button id="showParticipants" class="btn btn-small btn-warning"> <span class="typicn views "></span> Heatmap </button>
					</div> 
					
					
			     </div>
	    </header>

	
<div class="container-fluid">
	

	<div class="row-fluid" id="controlsRow">
		<div class="span12"  id="dFooter">
				
				<div id="controlsWrap" >

					<div id="timeline" class="">
						<div id="slider-range"><div id="dots"></div></div>
								<input type="text" id="amount"  />					
					</div>
					

					<div id="participants" class="">
						<span><small>Highlight posts by participant:</small></span>						
						<ul id="participantList">
						
						</ul>
					</div>
					
				</div>	
				
		</div>

	</div>	


		
	<div class="row-fluid" id="dRowMiddle">
	
	
		<div class="span4 well" id="dSidebar">
			  <div class="tab-pane active dCollapse" id="dInfoTab">
						<div id="discStatus" class="alert"></div>
					      <div class="content">
					      	<div id="dPromptView" ></div>
					      	<div id="dCourse"><b>Course:</b>  </div>
					      	<div id="dSDateView"><b>Start Date: </b>  </div>
					      	<div id="dODateView"><b>Open To Class: </b>  </div>
					      	<div id="dCDateView"><b>End Date: </b>  </div>
					      	
					      </div>
			  </div>
			  
			  
			  <div class="tab-pane dCollapse" id="dNewTab">
					    <h4><span class="typicn feed"> </span> Recent Activity</h4>			    
					      <div class="alert alert-info smallAlert"><span class="typicn info iconBall"></span>Click on the item below to go to post.</div>
					      <div class="content">
							<ul class=" discussionFeed" id="recentContent">
						
							</ul>
						</div>			  
			  
			  </div>

 
		</div> <!-- close span4 -->
	
		<div class="span8 "  id="dMain">
		
			<div id="discussionDivs" > 
				<div class="levelWrapper" level="0"></div>
			</div>
			
		</div><!-- close span8 -->
		
	</div> <!-- close row -->
					<div id="backTop"><span class="typicn up"> </span> top</div>

	
	<div id="commentWrap">
			<input id="postIDhidden" type="hidden" name="postIDhidden" value="">
			<input id="userIDhidden" type="hidden" name="userIDhidden" value="<?php echo $_SESSION['UserID'];?>">
			<div id="top">	
			<div id="quick">

				<div class="btn-group" id="postTypeID">
				  <button class="btn postTypeOptions active" id="comment" > <span class="typicn message "> </span> Comment</button>
				  <button class="btn postTypeOptions" id="agree" > <span class="typicn thumbsUp "></span> Agree</button>
				  <button class="btn postTypeOptions" id="disagree" > <span class="typicn thumbsDown "></span> Disagree</button>
				  <button class="btn postTypeOptions" id="clarify" > <span class="typicn unknown "></span> Ask to Clarify</button>
				  <button class="btn postTypeOptions" id="offTopic" > <span class="typicn forward "></span> Off Topic</button>
				</div>
				


					</div>
			</div>
			
			<div id="middle">
				<input id="locationIDhidden" type="hidden" name="locationIDhidden" value="">
				<div id="commentArea"> 
					<div id="highlightDirection">Select a specific segment of the text to reference it in your post. </div>
					<div id="highlightShow"></div>
					<div id="textError">If you are commenting you need to enter a comment.</div>
					<textarea id="text">Your comment...</textarea>	
				</div>
			
			<span id="media">  <span class="typicn tab "></span>  Add Media</span>

			</div>
			
			
						
			<div id="bottom">

				<div id="buttons">
					<input type="button" id="postCancel" class="buttons btn btn-info" value="Cancel">
					<input id="addPost" type="button" class="buttons btn btn-primary" value="Add to dscourse">
				</div>
			</div>

				
	</div> <!-- close commentWrap --> 


	<div id="mediaBox">
	<a class="close" data-dismiss="alert" href="#" id="closeMedia" >&times;</a>
		
		<div id="mediaTools">	
			<div id="drawGroup" class="btn-group">
				<button class="btn btn-small drawTypes" id="Web"><i class="icon-globe"></i> Link</button>
				<button class="btn btn-small drawTypes" id="Document"><i class="icon-file"></i>  Document</button>
				<button class="btn btn-small drawTypes" id="Video"><i class="icon-film"></i> Video</button>
				<button class="btn btn-small drawTypes active" id="Drawing"><i class="icon-edit"></i>  Drawing</button>
				<button class="btn btn-small drawTypes" id="Map"><i class="icon-map-marker"></i>Map</button>
			</div>
			<div id="mediaButtons" class="pull-right">	
				<button id="drawCancel" class="btn btn-info">Cancel</button>
				<button id="continuePost" class="btn btn-primary" >Continue posting</button>		
			</div>
		</div>
		
	
		<div id="mediaWrap">

		</div>	

		
	</div><!-- close mediabox --> 


	 

	<div id="displayFrame">
		<a class="close" href="#" id="closeMediaDisplay" >&times;</a> 
		<iframe id="display" src="http://www.viseyes.org/shiva/go.htm" width="100%" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>

	</div>
</div> <!-- close container --> 


</div><!-- End individual discussion page --> 


<script>
// Latest itiration of the shiva elements through iframe
var sampleData="{\"chartType\": \"BarChart\",\"areaOpacity\": \".3\",\"backgroundColor\": \"\",\"chartArea\": \"\",\"colors\": \"\",\"fontName\": \"Arial\",\"fontSize\": \"automatic\",\"hAxis\": \"\",\"legend\": \"right\",\"legendTextStyle\": \"\",\"height\": \"400\",\"isStacked\": \"true\",\"lineWidth\": \"2\",\"pointSize\": \"7\",\"series\": \"\",\"title\": \"\",\"titleTextStyle\": \"\",\"tooltipTextStyle\": \"\",	\"vAxis\": \"\",\"width\": \"600\",	\"dataSourceUrl\": \"https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key=0AsMQEd_YoBWldHZNbGU2czNfa004UmpzeC13MkZZb0E&output=html\",\"query\": \"\",\"shivaGroup\": \"Visualization\"}";

	$(document).ready(function() {
		if (window.addEventListener) 
				window.addEventListener("message",shivaMessageHandler,false);
			else
				window.attachEvent("message",shivaMessageHandler);
			});
		
	function shivaMessageHandler(e)
	{
		var msg="Unrecognized";
		if (e.data.indexOf("GetJSON=") == 0) 
			msg=e.data.substr(8);
		else if (e.data.indexOf("GetType=") == 0) 
			msg=e.data.substr(8);
		dscourse.currentDrawing = msg;
		console.log(dscourse.currentDrawing); 
	}
	
	function ShivaMessage(iFrameName,cmd) 
	{
	    if (cmd.indexOf("PutJSON") == 0)
	     	console.log(dscourse.currentDrawData);
	    	cmd+="="+dscourse.currentDrawData;
	    document.getElementById(iFrameName).contentWindow.postMessage(cmd,"*");
 	}
</script>

<script type="text/javascript">

/************* IMAGE UPLOADER ***********************/ 

			var userUploader = new qq.FileUploader ({
			    element: document.getElementById('file-uploader-user'),
			    action: 'scripts/php/imgUpload.php',
			    // additional data to send, name-value pairs
			    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'], 
			    sizeLimit: 100000, // max size 
			    debug: true
			});
			var courseUploader = new qq.FileUploader ({
			    element: document.getElementById('file-uploader-course'),
			    action: 'scripts/php/cImgUpload.php',
			    // additional data to send, name-value pairs
			    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'], 
			    sizeLimit: 100000, // max size 
			    debug: true
			});
			window.onload = userUploader;
			window.onload = courseUploader;

</script>
<script type="text/javascript" src="http://www.viseyes.org/shiva/SHIVA_Show.js"></script>
<script type="text/javascript" src="http://www.viseyes.org/shiva/SHIVA_Event.js"></script> 

</body>
</html>
<?php

	}  
	
?>