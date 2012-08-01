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
		
	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />	

	<script type="text/javascript" src="scripts/js/validation.js"></script>
	<script type="text/javascript" src="scripts/js/users.js"></script>

	<script type="text/javascript" src="scripts/js/dscourse.js"></script>
	<script type="text/javascript" src="assets/js/fileuploader.js"></script>

<script type="text/javascript">
	
	var dscourse = new Dscourse();				// Fasten seat belts, dscourse is starting...

	
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
	  <h1>Dashboard<small> What's new and trending </small></h1> 
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
			<p id="iCourseDescription"></p>  	
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
</div>
    
    
<!-- Begin individual discussion page -->


	
<div id="discussionWrap" class="page">



	
<div class="rounded-corners allPanes" id="newsPane" style="position: absolute; display: block; left: 8px; top: 72px; width: 339px; height: 277.7062937062937px; border: 1px solid rgb(153, 153, 153); background-image: -webkit-linear-gradient(top, rgb(255, 255, 255) 0%, rgb(237, 237, 237) 100%); background-position: initial initial; background-repeat: initial initial; ">
		
		<span class="boxHeaders">News Feed</span>
		<div class="feedDiv"> 	
			<hr class="soften">
			<p>
				</p><ul class="unstyled discussionFeed">
				<li> Mable Kinzie commented on <a href="#">"Stakeholder perspective of James Monroe" </a>  <em class="timeLog">3 hours ago.</em> </li>
				<li> Bill Ferster added a new link to discussion on <a href="#">Benefits of free software </a>  <em class="timeLog">18 hours ago.</em></li>
				<li> Glen Bull annotated your comment at <a href="#">"Where is 3D printing going?"</a> <em class="timeLog">2 days ago.</em></li>
				</ul>
			<p></p>
		<p class="pull-right"><a href="#"><em>See more </em></a></p>
	</div><div style="position:absolute;left:6px;top:6px;width:20px;height:20px;padding:0px;border:2px solid #1e5799;border-radius:20px;moz-border-radius;20px;color:#1e5799;text-align:center;line-height:20px;font-size:8px"><b>see</b><div></div></div></div>
	

	<div class="rounded-corners" id="discPane" style="position: absolute; top: 72px; left: 374px; width: 744px; height: 403px; border: 1px solid rgb(153, 153, 153); background-image: -webkit-linear-gradient(top, rgb(255, 255, 255) 0%, rgb(237, 237, 237) 100%); background-position: initial initial; background-repeat: initial initial; ">
		<span class="boxHeaders">Discussion<small><span id="dTitleView" ></span> <div class="sayBut2" postID="0">say</div> <input id="dIDhidden" type="hidden" name="discID" value="">
</small></span> <p><div id="dPromptView" ></div></p>
			
	<div id="discFeedDiv"></div><div style="position:absolute;left:6px;top:6px;width:20px;height:20px;padding:0px;border:2px solid #1e5799;border-radius:20px;moz-border-radius;20px;color:#1e5799;text-align:center;line-height:20px;font-size:8px"><b>see</b><div></div></div>
	<div id="discussionDivs"> 
		<div class="levelWrapper" level="0">

		</div>
	</div>
	
	</div>

	<div class="rounded-corners" id="todoPane" style="position: absolute; display: block; width: 340px; top: 375.7062937062937px; left: 8px; height: 99.2937062937063px; border: 1px solid rgb(153, 153, 153); background-image: -webkit-linear-gradient(top, rgb(255, 255, 255) 0%, rgb(237, 237, 237) 100%); background-position: initial initial; background-repeat: initial initial; ">
			<span class="boxHeaders">To-Dos</span>
			
			<hr class="soften">
			<div class="feedDiv"><p>
				</p><ul class="unstyled todoFeed">
				<li> Provide initial response to document <a href="#">"Case overview" </a> by <em>August, 25.</em> </li>
				<li> <a href="#">Initiate </a> a class discussion with relevant topic. </li>
				<li> Bill Ferster asks you to clarify your comment starting with <em><a href="#">"That sounds wrong, ...."</a> </em></li>
				</ul>
			<p></p>
		<p class="pull-right"><a href="#"><em>See more </em></a></p>
	</div><div style="position:absolute;left:6px;top:6px;width:20px;height:20px;padding:0px;border:2px solid #1e5799;border-radius:20px;moz-border-radius;20px;color:#1e5799;text-align:center;line-height:20px;font-size:8px"><b>see</b><div></div></div></div>

	<div class="rounded-corners" id="timePane" style="position: absolute; height: 32px; top: 501px; left: 8px; width: 1110px; background-position: initial initial; background-repeat: initial initial; ">
	<div id="TimeSliderDiv">	</div>
	<div id="nowBar" style="position: absolute; height: 4px; top: 16px; width: 2px; background-color: #666; left: 315px; "></div><span id="nowText" style="position: absolute; top: 4px;  left: 306px; margin-top: -5px;">now<span></span></span><span id="beginText" style="position:absolute;left:48px;top:18px;"><b>6/17 12pm</b></span><span id="endText" style="position:absolute;left:545px;top:18px;"><b>6/25 12pm</b></span><span id="sliderText1" style="position: absolute; top: 33px; left: 196px; ">6/19 12pm</span><span id="sliderText2" style="position: absolute; top: 33px; left: 396px; ">6/23 12pm</span><div style="position:absolute;left:8px;top:12px;width:20px;height:20px;padding:0px;border:2px solid #fff;border-radius:20px;moz-border-radius;20px;text-align:center;line-height:20px;font-size:8px"><b>set</b><div></div></div></div>
	<div id="sizeBut"  class="ui-draggable"><div id="dragImage"></div></div>


	<div id="commentWrap">
			<input id="postIDhidden" type="hidden" name="postIDhidden" value="">
			<input id="userIDhidden" type="hidden" name="userIDhidden" value="<?php echo $_SESSION['UserID'];?>">
			<div id="top">	
			<div id="quick">

				<div class="btn-group" id="postTypeID">
				  <button class="btn postTypeOptions active" id="comment" > <i class="icon-comment"></i> Comment</button>
				  <button class="btn postTypeOptions" id="agree" > <i class="icon-thumbs-up"></i> Agree</button>
				  <button class="btn postTypeOptions" id="disagree" > <i class="icon-thumbs-down"></i> Disagree</button>
				  <button class="btn postTypeOptions" id="clarify" > <i class="icon-question-sign"></i> Ask to Clarify</button>
				  <button class="btn postTypeOptions" id="offTopic" > <i class="icon-share-alt"></i> Mark Off Topic</button>
				</div>
					</div>
			</div>
			
			<div id="middle">
				<div id="highlightShow"></div>
				<textarea id="text">Your comment...</textarea>	
				<div id="bottomlinks">
					<ul>
					<li><div id="media"></div>  <a href="#"> Add Media</a></li>
					<li><div id="draw"> </div> <a href="#">Add Drawing </a></li>
					</ul>
				</div>
			</div>
			<div id="bottom">	
				<div id="buttons">
					<input type="button" id="postCancel" class="buttons btn btn-info" value="Cancel">
					<input id="addPost" type="button" class="buttons btn btn-primary" value="Add to dscourse">
				</div>
			</div>
			
	</div> <!-- close commentWrap --> 


</div><!-- End individual discussion page --> 

<script type="text/javascript">
	
	var userData=new Object;												// Holds user savable data
	userData.partitionX=.20;												// Default h partition
	userData.partitionY=.50;												// Default V partition
	userData.timeSlider1=.25;												// Time slider 1 value
	userData.timeSlider2=.75;												// Time slider 2 value
	var panelControl=null;													// Holds
	

	
	
	$(document).ready(function() {											// When doc loaded
		panelController=new PaneControl(56,userData);						// Init PaneControl							
		});		
		
	function PaneControl(top, userData)									// CONSTRUCTOR
	{		
		this.gutter=8;														// Gutter between panes
		top+=this.gutter;													// Shift top for gutter
		this.top=top;														// Set top
		var _this=this;														// Set this
		
		$("#sizeBut").draggable({ drag: function(event, ui) {				// Drag
				var bot=$("#discPane").outerHeight();						// Get bot
				var x=ui.position.left+3;									// X pos
				x=x*100/_this.wid;											// 0-100%
				var y=ui.position.top-top;									// Y pos
				y=y*100/(bot-top+(_this.top+_this.gutter));					// 0-100%
				_this.PositionFrames(x/100,y/100);							// Redraw			
				},  
			stop: function(event, ui) {										// Stop	
				_this.PositionFrames(userData.partitionX,userData.partitionY);	// Redraw			
				_this.Draw();												// Draw panes
		 		}
		 	});			
		$("#sizeBut").css({ width:'12px',height:'12px'});					// Set size
		$("#sizeBut").css({"border-radius":"6px","moz-border-radius":"6px"});// Set corners to make circle
		this.Draw();														// Draw panes
		this.PositionFrames(userData.partitionX,userData.partitionY);		// Set initial pane positioning
		}
	
	PaneControl.prototype.PositionFrames=function(cx, cy)				// POSITION THE PANES
	{
		var g=this.gutter;													// Current gutter
		this.wid=$(document).outerWidth();									// Browser with
		$("#newsPane").show();												// Make sure news feed is visible
		$("#todoPane").show();												// Make sure todo is visible
		$("#discPane").show();												// Make sure discussion is visible
		if (cy <= 0)														// Past top
			$("#newsPane").hide();											// Hide news feed
		if (cy >= 1)														// Past bottom
			$("#todoPane").hide();											// Hide todo 
		if (cx <= 0) {														// Past left
			$("#newsPane").hide();											// Hide news feed
			$("#todoPane").hide();											// Hide todo 
			}
		if (cx >= 1)														// Past bottom
			$("#discPane").hide();											// Hide discussion 
			
		cy=Math.min(Math.max(cy,0),1);										// Cap cy 0-1
		cx=Math.min(Math.max(cx,0),1);										// Cap cx 0-1
		this.SetUserData("partitionX",cx);	this.SetUserData("partitionY",cy);	// Save partition info
		var timeHgt=$("#timePane").outerHeight();							// Height of time pane
		var frameHgt=$(document).outerHeight()-g;							// Browser height
		var discHgt=Math.floor(frameHgt-this.top-timeHgt);					// Disussion area height
		var x=cx*this.wid;													// Center x point in pixels
		var y=this.top+(cy*discHgt);										// Center y point in pixels
		
		$("#sizeBut").css("left",x-4+"px");									// Set left
		$("#sizeBut").css("top",Math.min(Math.max(y-1,this.top+g-12),discHgt+this.top-g-3)+"px"); // Set top
		
		$("#newsPane").css("left",g+"px");									// Set NEWS left
		$("#newsPane").css("top",this.top+g+"px");							// Set top
		$("#newsPane").outerWidth(x-g-1);									// Set width
		$("#newsPane").outerHeight(y-this.top-(g*2));						// Set height

		$("#todopane").css("left",g+"px");									// Set TO DO left
		$("#todoPane").outerWidth(x-g);										// Set width
		$("#todoPane").css("top",y+"px");									// Set top
		$("#todoPane").outerHeight(discHgt-y+(this.top-0)-g); 				// Set height
		if (cy <= 0) {														// Past top
			$("#todoPane").css("top",y+g+"px");								// Set top
			$("#todoPane").outerHeight(discHgt-y+(this.top-0)-(g*2)); 		// Set height
			}

		$("#discPane").css("top",this.top+g+"px");							// Set DISC top
		$("#discPane").css("left",x+g+"px");								// Set left
		$("#discPane").outerWidth(this.wid-x-(g*2));						// Set width
		$("#discPane").outerHeight(discHgt-(g*2));							// Set height
			
		$("#timePane").css("top",(discHgt+this.top)+"px");					// Set TIME top
		$("#timePane").css("left",g+"px");									// Set left
		$("#timePane").outerWidth(this.wid-(g*2));							// Set width
	}
	
	
	PaneControl.prototype.Draw=function()								// DRAW ELEMENTS
	{
		this.DrawNews();													// Draw news feed pane
		this.DrawDisc();													// Draw discussion pane
		this.DrawToDo();													// Draw todo pane
		this.DrawTime();													// Draw time pane
	}		
	
	PaneControl.prototype.DrawNews=function()							// DRAW NEWS FEED PANE
	{
		var dd="#newsFeedDiv";												// Name of div
		if (!$(dd).length) {												// If div doesn't exist
			$("#newsPane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#newsPane").append(this.DrawSeeDot("see",20,6,6,"#1e5799"));	// Draw ses logo
			}
		$("#newsPane").css({border:"solid 1px #ccc",background:"#f9f9f9"});		
		}		

	PaneControl.prototype.DrawToDo=function()							// DRAW TO TO PANE
	{
		var dd="#todoFeedDiv";												// Name of div
		if (!$(dd).length) {												// If div doesn't exist
			$("#todoPane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#todoPane").append(this.DrawSeeDot("see",20,6,6,"#1e5799"));	// Draw ses logo
			}
		$("#todoPane").css({border:"solid 1px #ccc",background:"#f9f9f9"});		
	}		

	PaneControl.prototype.DrawDisc=function()							// DRAW DISC PANE
	{
		var dd="#discFeedDiv";												// Name of div
		if (!$(dd).length) {												// If div doesn't exist
			$("#discPane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#discPane").append(this.DrawSeeDot("see",20,6,6,"#1e5799"));	// Draw see logo
			}
		$("#discPane").css({border:"solid 1px #ccc",background:"#ededed"});		
		if ($.browser.mozilla)	
			$("#discPane").css("background","-moz-linear-gradient(top,#ffffff,#ededed)");
		else 
			$("#discPane").css("background","-webkit-linear-gradient(top, #ffffff 0%, #ededed 100%)")
	}		
	
	PaneControl.prototype.DrawTime=function()							// DRAW TIME PANE
	{
		var i,p,x,str;
		var now=new Date().getTime();										// Get today
		var startTime=new Date(now-4*24*60*60*1000);
		var endTime=new Date(now+4*24*60*60*1000);
		var _this=this;														// Set this
				

		var dd="#TimeSliderDiv";											// Name of slider
		if (!$(dd).length) {												// If slider doesn't exist
			$("#timePane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#timePane").append("<div id='nowBar'       style='position:absolute;height:4px;top:16px;width:2px;background-color:#999'/>");
			$("#timePane").append("<span id='nowText'     style='position:absolute;top:4px; margin-top: -5px;'>now<span>");
			$("#timePane").append("<span id='beginText'   style='position:absolute;left:48px;top:18px;'><span>");
			$("#timePane").append("<span id='endText'     style='position:absolute;left:525px;top:18px;'><span>");
			$("#timePane").append("<span id='sliderText1' style='position:absolute;top:33px;'><span>");
			$("#timePane").append("<span id='sliderText2' style='position:absolute;top:33px;'><span>");
			$("#timePane").append(this.DrawSeeDot("set",20,8,12,"#fff"));	// Draw set logo
				}
		$(dd).css({ position:"absolute",width:"400px",left:"115px",top:"20px" });
		$("#beginText").html("<b>"+this.DateTimeString(startTime)+"</b>");	// Course start
		$("#endText").html("<b>"+this.DateTimeString(endTime)+"</b>");		// Course end
		var options=new Object();											// Holds slider options
		
		options.slide=function(event, ui) {									// Slider move handler
			var which="#sliderText1";										// Assume 1st
			var val=ui.values[0];											// Get 1st slider value
			if (ui.value != val) {											// If second slider
				which="#sliderText2";										// Set name
				val=ui.values[1];											// Use 2nd val
				}
			var p=new Date(startTime-0+((endTime-startTime)*(val/1000)));	// Calc new date
			var str=_this.DateTimeString(p);								// Make into string
			var x=str.length*4;												// Offset amount
			$(which).html(str);												// Set value
			$(which).css("left",event.clientX-x+"px");						// Position
			_this.SetUserData("timeSlider1",ui.values[0]/1000);				// Save slider 1
			_this.SetUserData("timeSlider2",ui.values[1]/1000);				// Save slider 2
			};
		options.max=1000;													// 0-1000
		options.range=true;													// Range mode
		options.values=[userData.timeSlider1*1000,userData.timeSlider2*1000];	// Set times
		$(dd).slider(options);												// Draw slider
		
		for (i=0;i<2;++i) {													// For each slider
			p=new Date(startTime-0+((endTime-startTime)*(options.values[i]/1000)));	// Calc new date
			str=this.DateTimeString(p);										// Make into string
			x=str.length*4;													// Offset amount
			p=(400*options.values[i]/1000)+96;								// Position
			$("#sliderText"+(i+1)).html(str);								// Set value
			$("#sliderText"+(i+1)).css("left",p+"px");						// Position
			}
		if ((now > startTime) && (now < endTime)) {							// In range
			p=((now-startTime)/(endTime-startTime)*400)+115-0;				// Calc now pos
			$("#nowBar").css("left",p+"px");								// Position
			$("#nowText").css("left",p-9+"px");								// Position
			}
	}			
	
	PaneControl.prototype.DateTimeString=function(time) 				// CONVERT DATE OBJECT TO SIMPLE STRING
	{
		var str=(time.getMonth()+1)+"/"+time.getDate()+" ";					// Month/day
		if (time.getHours() > 12)	str+=time.getHours()-12;				// Before noon
		else		 				str+=time.getHours();					// After noon
		if (time.getHours() > 11)	str+="pm";								// PM
		else 						str+="am";								// AM
		return str;															// Return "MO/DY HRap"
	}

	PaneControl.prototype.DrawSeeDot=function(text, size, x, y, col)	// DRAW CIRCLE DOT LOGO
	{
		var str="<div style='position:absolute;left:"+x+"px;top:"+y+"px;width:"+size+"px;height:"+size+"px;padding:0px;"
		str+="border:2px solid "+col+";border-radius:"+size+"px;moz-border-radius;"+size+"px;color:"+col;
		str+=" !important;text-align:center;line-height:"+size+"px;font-size:"+size/2.5+"px;'>";
		str+="<b>"+text+"</b><div>";
		return str;
	}

	PaneControl.prototype.SetUserData=function(key, value)				// SAVE USER DATA
	{
		userData[key]=value;
	}




function trace(str) { console.log(str) };
	
	
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