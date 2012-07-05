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
	<title>dscourse</title>

	
	<script type="text/javascript" src="assets/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="assets/js/fileuploader.js"></script>
	<script type="text/javascript" src="assets/js/jquery-ui-1.8.21.custom.min.js"></script>
		
	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />	


	<script type="text/javascript" src="scripts/js/helpers.js"></script>
	<script type="text/javascript" src="scripts/js/validation.js"></script>
	<script type="text/javascript" src="scripts/js/users.js"></script>

	<script type="text/javascript" src="scripts/js/dscourse.js"></script>
	

	<script type="text/javascript" src="scripts/js/view.js"></script>

<script type="text/javascript">
		var dscourse = new Dscourse();
		
</script> 

</head>
<body>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" id="homeNav">
		  dscourse
		</a>
	
		<ul class="nav">

		  <?php if ($_SESSION['status'] == "Administrator"){	?>											
			<li>
			<a id="usersNav">Users</a>  
		  </li>
		  <?php 	} ?>

		  <li>	
			<a id="coursesNav">Courses</a>  
		  </li> 
		  <li>	
			<a id="discussionsNav">Discussions</a>  
		  </li> 
		  
		</ul>
		
		<ul class="nav pull-right">
		 <li><a id="profileNav" userid="<?php echo $_SESSION['UserID']; ?>"><?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?>  </a></li>
		  <li><a href="scripts/php/logout.php">Logout</a></li>
		</ul>
				
    </div>
  </div>
</div>

<!-- End of header content-->


<!-- Begin home.php-->
<div id="homePage" class="container wrap page">
	<div class="page-header">
	  <h1>Dashboard<small> What's new and trending </small></h1> 
	</div>			
	<div class="row">
		<div class="span4">
			<div class="well">
				<h2>Dscourse</h2>
				<hr class="soften" />
				<p>Welcome to Dscourse development area. You are now in the admin view similar to a dashboard. After logging in users will be able to see an overview of their courses and discussions. This page provides a stream for discussion activities and to-do's, which users will be able to customize to their needs.</p>
			</div>		
		</div>

		<div class="span4">
			<div class="well">
				<h2>Discussions</h2>
				<hr class="soften" />
				<p>
					<ul class="unstyled discussionFeed" >
					<li> Mable Kinzie commented on <a href="#">"Stakeholder perspective of James Monroe" </a>  <em class="timeLog">3 hours ago.</em> </li>
					<li> Bill Ferster added a new link to discussion on <a href="#">Benefits of free software </a>  <em class="timeLog">18 hours ago.</em></li>
					<li> Gell Bull annotated your comment at <a href="#">"Where is 3D printing going?"</a> <em class="timeLog">2 days ago.</em></li>
					</ul>
				</p>
				<p class="pull-right"><a href="#"><em>See more </em></a></p>
			</div>		
		</div>
		
		<div class="span4">
			<div class="well">
				<h2>To-Dos</h2>
				<hr class="soften" />
				<p>
					<ul class="unstyled todoFeed" >
					<li> Provide initial response to document <a href="#">"Case overview" </a> by <em>August, 25.</em> </li>
					<li> <a href="#">Initiate </a> a class discussion with relevant topic. </li>
					<li> Bill Ferster asks you to clarify your comment starting with <em><a href="#">"That sounds wrong, ...."</a> </em></li>
					</ul>
				</p>
				<p class="pull-right"><a href="#"><em>See more </em></a></p>
			</div>		
		</div>
				
	</div>

</div><!-- end home-->



<!-- Begin users.php-->

<div id="usersPage" class="container wrap page">

	<div class="page-header">
	  <h1>Users <small> <a id="userListLink" class="headerLinks">User List</a> </small><small> <a id="addUserLink" class="linkGrey headerLinks">Add User</a> </small></h1> 
	</div>	
			
	<div class="row">	
		
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
				        <p class="help-inline"Provide the last name of the user. </p>
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
					 <div id="file-uploader">
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
		              
				        <p class="help-inline">Briefly introduce yourself. Please limit your text to 1000 characters.</p>
				      </div>
				    </div>	
					
					
				    <div class="control-group" id="facebookControl">
				      <label class="control-label" for="facebook">Facebook</label>
				      <div class="controls">
				       <div class="input-prepend">
		                <span class="add-on">f</span><input class="span2" id="facebook" name="facebook" size="16" type="text">
		              </div>
				        <p class="help-inline"> Facebook username</p>
				      </div>
				    </div>	
				    
				    
				    <div class="control-group" id="twitterControl">
				      <label class="control-label" for="twitter">Twitter</label>
				      <div class="controls">
				       <div class="input-prepend">
		                <span class="add-on">t</span><input class="span2" id="twitter" name="twitter" size="16" type="text">
		              </div>
				        <p class="help-inline">Your Twitter username</p>
				      </div>
				    </div>	

				    <div class="control-group" id="phoneControl">
				      <label class="control-label" for="phone">Phone</label>
				      <div class="controls">
				       <div class="input-prepend">
		                <span class="add-on">#</span><input class="span2" id="phone" name="phone" size="16" type="text">
		              </div>
  			          <p class="help-inline">Mobile phone number </p>
				      </div>
				    </div>	

				    <div class="control-group" id="websiteControl">
				      <label class="control-label" for="website">Website</label>
				      <div class="controls">
					    <div class="input-prepend">
		                <span class="add-on">url</span><input class="span2" id="website" name="website" size="16" type="text">
		              </div>				        
		              <p class="help-inline">Website </p>
				      </div>
				    </div>	
				    
					<div id="userButtonDiv"><button class="btn btn-primary" id="addUserButton">Add User</button></div>
					
				    				  
				</div>							
			</div>
		
		</div>

		
		<div class="span12">
			<div id="userList">
			
						<div class="span4 offset4" id="filterUser">				        
							<input type="text" class="span4" id="filterUserText" name="filterUserText" placeholder="Filter by name or email ...">
									<hr class="soften" />
						</div>
		
			    <table id="userTable" class="table table-striped table-bordered">
				    <thead>
					    <tr>
						    <th scope="col" id="first" width="20%">First Name</th>
						    <th scope="col" id="last" width="20%">Last Name</th>
						    <th scope="col" id="email" width="30%">Email Address </th>
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

</div><!-- End users -->


<!-- Begin courses.php-->


<div id="coursesPage" class="container wrap page">
	<div class="page-header">
	  <h1>Courses
		  <small> 
		  <a id="allCoursesView" class="headerLinks">All Courses</a> 
		  <a id="activeCoursesView" class="headerLinks linkGrey">Active </a> 
		  <a id="archivedCoursesView" class="headerLinks linkGrey">Archived </a>
		  <a id="courseFormLink" class="headerLinks linkGrey">Add Course </a>
		  <div class="pull-right animated flash"> <span class="headerText" id="saveMessage"> </span>
			  <button id="saveCourses" class="btn btn-small btn-warning"> Save now </button>
		  </div>
		  

		  </small>
	  </h1> 
	</div>	
	

			
	<div class="row">

		<div class="span12">
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
					   
					   <?php /*  
					    <div class="control-group">
					      <label class="control-label" for="courseInstructors">Course Instructors</label>
					      <div class="controls">
					        <input type="text" class="input-large" id="courseInstructors" name="courseInstructors">
					        <p class="help-inline">Enter at least one name, you can add more in the course page. </p>
					      </div>
					    </div>	
	
					    <div class="control-group">
					      <label class="control-label" for="courseTAs">Course TAs</label>
					      <div class="controls">
					        <input type="text" class="input-large" id="courseTAs" name="courseTAs">
					        <p class="help-inline">Optional, you can add and change these values in the course page.  </p>
					      </div>
					    </div>	
					    */ ?>
					    
					    
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
				      	<div id="imgPath"></div>
				      	<input type="hidden" name="courseImage" id="courseImage" value="/assets/img/dscourse_logo4.png">				
					 <div id="file-uploader">
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
			                <span class="add-on">url</span><input class="span2" id="courseURL" name="courseURL" size="16" type="text">
			              </div>				        
			              <p class="help-inline">If you have an external website for this course please enter it here. </p>
					      </div>
					    </div>
					    
					    <hr class="soften" />
					    <div class="row">
					    	
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

					    <div id="courseButtonDiv"> <button class="btn btn-primary" id="courseFormSubmit">Submit</button></div>
					
					
					</div>
				</div>
				
				
			
				
			</div>	<!-- End span12 div -->	
		</div>	<!-- End row div -->

				
	</div> 

</div><!-- end courses -->


<!-- Begin Discussions.php -->

<div id="discussionsPage" class="container wrap page">
	<div class="page-header">
	  <h1>Discussions
	  	<small> 
	  		<a id="allDiscussionView" class="headerLinks">All Discussions</a> 
	  		<a id="addDiscussionView" class="headerLinks">Start New Discussion</a> 
	  		<div class="pull-right animated flash"> <span class="headerText" id="saveMessage"> </span>
	  		</div>
   
	  	</small>
	  </h1> 
	</div>	
	

			
	<div class="row">
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
		

					    <div class="control-group" id="discussionStartControl">
					      <label class="control-label" for="discussionStartDate">Discussion Start Date</label>
					      <div class="controls">
					        <input type="text" class="input-large" id="discussionStartDate" name="discussionStartDate">
					        <p class="help-inline">Format: YYYY-MM-DD </p>
					      </div>
					    </div>						
						
					    <div class="control-group" id="discussionEndControl">
					      <label class="control-label" for="discussionEndDate">Discussion End Date</label>
					      <div class="controls">
					        <input type="text" class="input-large" id="discussionEndDate" name="discussionEndDate">
					        <p class="help-inline">Format: YYYY-MM-DD </p>
					      </div>
					    </div>			
		

<hr class="soften" />
					    <div class="row">
					    	
					    	<div class="span3">
						    <h3> Courses </h3>
						    <p>Start typing course names that you would like this discussion to be associated with. Only active courses are listed. </p>
	
						    <p> <input type="text" class="input-large discussionCourses" id="discussionCourses" name="discussionCourses" >
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

					    <div id="discussionButtondiv"> <button class="btn btn-primary" id="discussionFormSubmit">Submit</button></div>		
		
		
				</div>
			</div>
		
			
		</div>
				
	</div>

</div><!-- end discussions -->



<!-- Begin profile.php-->

<div id="profilePage" class="container wrap page">

  	<div class="page-header">
    <h1><span id="profileName"></span><small><span id="profileEmail"> </span></small>

    </h1>
  </div>
  
<div class="row">

	<div class="span4 offset4" id="notify"></div>
  <div class="span4">
  	
  	<div id="profilePicture"></div>
  	<div id="profileInfo"> <table class="table">
		
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
  </div>
  <div class="span8 ">
		<div> 
			<h2>My Courses:</h2>
			<table class="table table-striped">
		
		        <tbody>
		          <tr>
		            <td>Course Title </td>
		            <td>Other information about the course</td>
		            <td>More stuff</td>
		          </tr>
		          <tr>
		            <td>Course Title </td>
		            <td>Other information about the course</td>
		            <td>More stuff</td>
		          </tr>
		          <tr>
		            <td>Course Title </td>
		            <td>Other information about the course</td>
		            <td>More stuff</td>
		          </tr>
		        </tbody>
		      </table>
  	
   </div>
</div>

</div>
</div><!-- end profile -->


<!-- Begin course.php -->

<div id="coursePage" class="container wrap page">
	
	  	<div class="page-header">
		    <h1><span id="iCourseName">Course Name </span><small></small>
		    </h1>
	    </div>
	  
<div class="row">

	<div class="span4 offset4" id="notify"></div>
  <div class="span4">
  	
  	<div id="iCoursePicture"></div>
  	<div id="iCourseInfo"> <table class="table">
		
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
   </div>
  </div>
  <div class="span8 ">
		<div> 
			<h3>About this course:</h3>
			<p id="courseDescription"></p>  	
		</div>
		<div id="courseDiscussions"> 
			<h3>Discussions in this course:</h3>
			
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="60%">Discussion Question </th>
					            <th width="20%"># of Responses</th>
								<th width="20%">Last contribution</th>
							</tr>
						</thead>
				        <tbody id="courseDiscussionsBody">

				        </tbody>
				     </table> 	
		</div>

</div>


</div>
</div><!-- end course -->


<!-- Footer -->
<footer class="footer" id="footerFixed">
        <p><span id="footerBrand">dscourse</span> is a project of Curry School of Education, University of Virginia. </p>
</footer>



<script type="text/javascript">
				  // Typeahead bootstrap stuff for courses.php
$(function() {

 	waitForFnc();
	
	function waitForFnc(){
	  if(typeof nameList == "undefined"){
	    window.setTimeout(waitForFnc,50);
	  }
	  else{
	  		$('.coursePeople').typeahead({
				source: nameList,							// The source, it's defined in users.js
				matchProp: 'Name',							// Match to this
				sortProp: 'Name',							// Sort by 
				valueProp: 'ID',							// The content of the val variable below comes from this attribute
				itemSelected: function(item, val, text) {
					$.each(allUsers, function(index, element) {		//Go through each person to get their usernae
						if (element.UserID == val) {
							var currentEmail = element.username;	// We get the username info, we can get any other information as well here
						
							$('#addPeopleBody').append('<tr><td>' + text + ' </td><td>' + currentEmail  + ' </td><td><div class="btn-group" data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" userid="'+ val + '">Instructor</button><button class="btn roleB" userid="'+ val + '">TA</button><button class="btn active roleB" userid="'+ val + '">Student</button></div></td><td><button class="btn removePeople">Remove</button>	</td></tr>'); // Build the row of users. 
						
						
						}
					});
					
					
					$('.coursePeople').val(' ').focus();
				}
			}); 
	  }
	}

 	waitForFncD();
		
	function waitForFncD(){
	  if(typeof courseList == "undefined"){
	    window.setTimeout(waitForFnc,50);
	  }
	  else{
	  		$('.discussionCourses').typeahead({
				source: courseList,							// The source, it's defined in courses.js
				matchProp: 'Name',							// Match to this
				sortProp: 'Name',							// Sort by 
				valueProp: 'ID',							// The content of the val variable below comes from this attribute
				itemSelected: function(item, val, text) {
					$('#addCoursesBody').append('<tr id="' + val + '" class="dCourseList"><td>' + text + ' </td><td><button class="btn removeCourses" >Remove</button>	</td></tr>'); 				// Build the row of users. 
					$('.discussionCourses').val(' ').focus();
				}
			}); 
	  }
	}

});
</script>



</body>
</html>
<?php

	}  
	
?>