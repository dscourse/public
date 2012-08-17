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
	<script type="text/javascript" src="assets/js/bootstrap-tooltip.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="assets/js/jquery-ui-1.8.21.custom.min.js"></script>
	<script type="text/javascript" src="http://www.viseyes.org/shiva/SHIVA_Show.js"></script>
	<script type="text/javascript" src="http://www.viseyes.org/shiva/SHIVA_Event.js"></script>

	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />
		

	<script type="text/javascript" src="scripts/js/validation.js"></script>
	<script type="text/javascript" src="scripts/js/users.js"></script>

	<script type="text/javascript" src="scripts/js/dscourse.js"></script>
	<script type="text/javascript" src="assets/js/fileuploader.js"></script>

<script type="text/javascript">
	
	var dscourse = new Dscourse();				// Fasten seat belts, dscourse is starting...
	
	var shiva = new SHIVA_Show('mediaWrap'); 
	
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
<div id="overlay"></div>


<!-- Begin home.php-->
<div id="homePage" class="container wrap page">
	<div class="page-header">
	  <h1> Dashboard<small> What's new and trending </small></h1> 
	</div>			
	<div class="row">
		<div class="span4">
			<div class="well">
				<h2>Dscourse</h2>
				<hr class="soften" />
				<p>Welcome to Dscourse development area.  You are now in the admin view similar to a dashboard. After logging in users will be able to see an overview of their courses and discussions. This page provides a stream for discussion activities and to-do's, which users will be able to customize to their needs.</p>
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
	  <h1>Users <small> <a id="userListLink" class="headerLinks">User List</a> <a id="addUserLink" class="linkGrey headerLinks">Add User</a> </small></h1> 
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
				    
					<div id="userButtonDiv"><button class="btn btn-primary" id="addUserButton">Add User</button> <button class="btn btn-info id="cancelUser">Cancel</button></div>
					
				    				  
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
	
						    <p>
						    <div id="discInputDiv"> </div>
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
	    <h1>
	    	<span id="profileName"></span>
	    	<small><span id="profileEmail"> </span></small>
	    	<button id="profilePageEdit" class="btn btn-info pull-right" profilePageID=""> Edit Profile </button>
	    </h1>
    </div>
    <div class="row" id="profileDetails">
		<div class="span4 offset4" id="notify"></div>
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
    </div><!-- end profileDetails-->

   		      

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
  	<h3>Course information:</h3>
  	<div id="iCoursePicture"></div>
  	<div> 
			<p id="iCourseDescription"></p>  	
		</div>
		
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
  <div class="span4 ">
		
		<div id="courseDiscussions"> 
			<h3>Course Discussions</h3>
			
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="80%">Discussion Question </th>
					            <th width="20%"># of Responses</th>
							</tr>
						</thead>
				        <tbody id="courseDiscussionsBody">

				        </tbody>
				     </table> 	
		</div>

</div>

  <div class="span4 ">
		
		<div id="courseDocuments"> 
			<h3>Course Documents</h3>
			
			<p> 
				<ul id="documentsList">
					<li>
						
					</li>
				</ul>
			</p>
					
		</div>

</div>


</div>
</div>
    
    
<!-- Begin individual discussion page -->


	
<div id="discussionWrap" class="container wrap page">

	
	  	<div class="page-header">
		    <h1>
		    	<span class="boxHeaders">
						
							<span id="dTitleView" ></span>
							<div class="sayBut2" postID="0">say</div> 
							<input id="dIDhidden" type="hidden" name="discID" value="">

					</span> 
		    </h1>
	    </div>
	
 
	
	<div class="row">
	
	
		<div class="span4">

			<div id="dInfo"> <!-- Only required for left/right tabs -->

					<div class="dCollapse">
					    <h4><span class="typicn info"> </span> Discussion information</h4>
					      <div class="content">
					      	<div id="dPromptView" ></div>
					      	<div id="dCourse"><b>Course:</b>  </div>
					      	<div id="dCourse"><b>Dates: </b>  </div>
					      </div>
					</div>  
					
					
					<div class="dCollapse">
					    <h4><span class="typicn feed"> </span> Recent Activity</h4>			    
					      <div class="content">
							<ul class=" discussionFeed" id="recentContent">
						
							</ul>
						</div>
					</div>  						
						
					<div class="dCollapse">					  
					    <h4><span class="typicn tick"> </span> To-Do</h4>
					      <div class="content">
						      <ul class=" todoFeed" >
							
						      </ul>
					      </div>
					</div>  
			    
			</div>
			
			
			
 
		</div>
	
		<div class="span8">
			<div id="controls" class="well">
			<div class="btn-group" id="heatmapButtons">
					  <button class="hmButtons btn btn-small disabled">  Heatmap </button>
					  <button class="hmButtons btn btn-small" heatmap="comment"> <span class="typicn message "> </span> </button>
					  <button class="hmButtons btn btn-small" heatmap="agree"> <span class="typicn thumbsUp "></span> </button>
					  <button class="hmButtons btn btn-small" heatmap="disagree"> <span class="typicn thumbsDown "></span></button>
					  <button class="hmButtons btn btn-small" heatmap="clarify"> <span class="typicn unknown "></span></button>
					  <button class="hmButtons btn btn-small" heatmap="offTopic"> <span class="typicn directions "></span></button>
					</div>
				<div class="btn-group" id="zoomButtons">
					  <button class="zButtons btn btn-small disabled"> </span> <div id="zoomText">Zoom</div> </button>
					  <button class="zButtons btn btn-small" zoom="in"> <span class="typicn zoomIn "> </span> </button>
					  <button class="zButtons btn btn-small" zoom="out"> <span class="typicn zoomOut "></span> </button>
					  <button class="zButtons btn btn-small" zoom="reset"> <span class="typicn expand "></span> </button>

					</div>
						
				<button id="showtimeline" class="btn btn-small"> <span class="typicn time "></span>  Show Timeline </button>	
				<button id="showParticipants" class="btn btn-small"> <span class="typicn group "></span>  Show Participants </button>	
			
			

			
			</div> 
					<div id="timeline" class="well">
						<p>
								<input type="text" id="amount"  />
						</p>					
						<div id="slider-range"><div id="dots"></div></div>
						
					</div>

					<div id="participants" class="well">

						<ul id="participantList">
						
						</ul>
						
					</div>
					
						
					<div id="discussionDivs"> 
						<div class="levelWrapper" level="0"></div>
					</div>
				
				</div>
		</div>
		
	</div>
	
				<hr class="soften" />

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
				  <button class="btn postTypeOptions" id="offTopic" > <span class="typicn directions "></span> Mark Off Topic</button>
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
			</div>
			<div id="bottom">
				<div id="bottomlinks">
					<span id="media" class="">  <span class="typicn tab "></span>  Add Media</span>
				</div>	
				<div id="buttons">
					<input type="button" id="postCancel" class="buttons btn btn-info" value="Cancel">
					<input id="addPost" type="button" class="buttons btn btn-primary" value="Add to dscourse">
				</div>
			</div>
			<div id="mediaBox">
				<div id="mediaTools">
					<div id="toolList" >

						<div class="btn-group" id="drawGroup">
							<button class="btn btn-small drawTypes active" id="Web page"><i class="icon-globe"></i> Web Page</button>
							<button class="btn btn-small drawTypes" id="Document"><i class="icon-file"></i> Document</button>
							<button class="btn btn-small drawTypes" id="Video"><i class="icon-film"></i> Video</button>
							<button class="btn btn-small drawTypes" id="Drawing"><i class="icon-edit"></i> Drawing</button>
							<button class="btn btn-small drawTypes" id="Map"><i class="icon-map-marker"></i> Map</button>
						</div>

					</div>
																<button class="btn btn-small btn-info" id="Edit"><i class="icon-pencil icon-white"></i> Annotate</button>

				</div>
				<div id="mediaWrap"></div>

			</div>
			
			
	</div> <!-- close commentWrap --> 


</div><!-- End individual discussion page --> 

<script>
// Shiva media drawing tools. 
	var shivaLib=null;
	
	$(document).ready(function() {
   		shivaLib=new SHIVA_Show("mediaWrap");
		Draw("Web page");
		});

function Draw(val)
{
	var options=new Object();
	$("#mediaWrap").empty();
	$("#mediaWrap").width(505);
	$("#mediaWrap").height(400);
	$("#mediaWrap").css("background-color","transparent");
	switch(val) {
		case "Web page":
   			options={ "shivaGroup":"Webpage","url":"http://www.viseyes.org" };
    		break;
		case "Document":
   			options={ "shivaGroup":"Webpage","url":"http://www.viseyes.org/VisualEyesProjectGuide.pdf" };
    		break;
		case "Drawing":
   			options={ "shivaGroup":"Webpage","url":"" };
   			break;
 		case "Video":
			 options={
				"dataSourceUrl": "zDZFcDGpL4U",
				"start": "0:0",
				"end": "",
				"autoplay": "false",
				"volume": "50",
				"height": "310",
				"width": "505",
				"duration": "?",
				"ud": "false",
				"shivaMod": "Tue, 31 Jul 2012",
				"shivaGroup": "Video"
				};
    		break;
 		case "Map":
			 options={
				"mapcenter": "38.03,-78.48,11",
				"draggable": "false",
				"height": "400",
				"width": "505",
				"mapTypeId": "Roadmap",
				"scrollwheel": "true",
				"overviewMapControl": "false",
				"panControl": "false",
				"streetViewControl": "false",
				"mapTypeControl": "true",
				"zoomControl": "true",
				"controlbox": "false",
				"ud": "false",
				"shivaMod": "Tue, 31 Jul 2012",
				"shivaGroup": "Map"
				};
			break;
   			} 
  	shivaLib.Draw(options);
}

	
</script>

<script type="text/javascript">

/************* TYPEAHEAD ***********************/ 

$(function() {

	  		$('.coursePeople').typeahead({
				source: dscourse.nameList,							// The source, it's defined in users.js
				matchProp: 'Name',							// Match to this
				sortProp: 'Name',							// Sort by 
				valueProp: 'ID',							// The content of the val variable below comes from this attribute
				itemSelected: function(item, val, text) {
					var i; 
					for(i = 0; i < dscourse.data.allUsers.length; i++ ){
						o = dscourse.data.allUsers[i];
						if (o.UserID == val) {
							var currentEmail = o.username;	// We get the username info, we can get any other information as well here
						
							$('#addPeopleBody').append('<tr><td>' + text + ' </td><td>' + currentEmail  + ' </td><td><div class="btn-group" data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" userid="'+ val + '">Instructor</button><button class="btn roleB" userid="'+ val + '">TA</button><button class="btn active roleB" userid="'+ val + '">Student</button></div></td><td><button class="btn removePeople">Remove</button>	</td></tr>'); // Build the row of users. 
						
						
						}
					}
					
					$('.coursePeople').val(' ').focus();
				}
			}); 
				

								
					   	  		


});

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
			    action: 'scripts/php/cimgUpload.php',
			    // additional data to send, name-value pairs
			    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'], 
			    sizeLimit: 100000, // max size 
			    debug: true
			});
			window.onload = userUploader;
			window.onload = courseUploader;

</script>

</body>
</html>
<?php

	}  
	
?>