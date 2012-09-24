<?php 
	define('MyConst', TRUE);								// Avoids direct access to config.php
	include "scripts/php/config.php"; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Welcome to dscourse</title>
	<script type="text/javascript" src="assets/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	
	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/docs.css" media="screen" rel="stylesheet" type="text/css" />	
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />	
	
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-32396167-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

	</script>
	
	<script type="text/javascript">

		(function() { 													// Auto runs everything inside when this script is loaded
											
	
		$('#signUp').live('click', function() {  						// Execute newsletter sign up 
			
			var email = $('#newsletter').val();								// Gets email from form 
			
			if (!email) {
				$('#notify').html("<div class=\"alert alert-error animated flash \"><button class=\"close\" data-dismiss=\"alert\">&#120;</button>Please enter an email address.</div>");	
			} else {
				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				if (!filter.test(email)) {
					$('#notify').html("<div class=\"alert alert-error animated flash \"><button class=\"close\" data-dismiss=\"alert\">&#120;</button>Please provide a valid email address.</div>");	
				} else {
					$.ajax({													
						type: "POST",
						url: "scripts/php/newsletter.php",
						data: {
							email: email									
						},
						  success: function(data) {	
								
						  			$('#notify').html(data);						
						    
						    }, 
						  error: function() {								// If php did not run 
								console.log("dscourse Log: There was a problem connecting to the login script. ");  
						  }
					});		
					
				}				
								
			}		 									
		});	

 
		$('#loginSubmit').live('click', function() {  					// Execute login function when clicked
			
			var username = $('#username').val();						// Gets username and password 
			var password  = $('#password').val(); 
			
			if (!username) {
				$('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a username</div>");	
			} else if (!password) {
				$('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a password</div>");	
			} else {
				$.ajax({													
					type: "POST",
					url: "scripts/php/auth.php",
					data: {
						username: username,
						password: password										// Sends the login data as array
					},
					  success: function(data) {	
							
					  		if (data == "redirect"){
					  			window.location.href = "index.php";	
					  		}else {						// If script ran successfully 
					    		$('#loginNotify').html(data);						// The error alerts will be printed here  
					    	}
					    }, 
					  error: function() {									// If php did not run 
							console.log("dscourse Log: There was a problem connecting to the login script. ");  
					  }
				});				
			}		 									
		});	
	




	
	})();																// End of self invoking anonymous function
	
	</script>
	
</head>
<body class="index-page"  >

<header class=" masthead">
  <div class="inner">
 <div id="logo"><img src="assets/img/dscourse_logo3.png"></div> 
 	
    <h1>dscourse</h1>
    <p>A revolutionary new discussion tool for online learning</p>
    <p id="indexSubtext">dscourse is a project that aims to provide the next generation platform-agnostic discussion tool for online learning. The framework of the tool has been developed at the Curry School of Education and it's currently under development with pilot testing planned for Fall 2012. </p>    
    <p class="download-info"> 
    	<a class="btn btn-primary btn-large" href='login.php'>Sign in</a>
    	 
        <a href="#intro" class="btn btn-info btn-large">Learn More </a>
    </p>
    <p>
    	<div id="notify"></div>
		 
		 <div class="row">
			 <div class="span5 offset4 signup">
				 <div class="control-group form-inline ">
						      <div class="controls">
						       <div class="input-prepend">
				                <span class="add-on">@</span><input class="span3" id="newsletter" name="newsletter" size="16" type="text" placeholder="Sign up to receive email updates...">
				                </div>
				              
						        <button class="btn btn-info" id="signUp">Sign Up</button>
						      </div>
				</div>	
			 </div>
		 </div>





		  
    </p>
  </div>

<hr class="soften">
  </header>


<div class="container" id="home">	
	<div class="row index-text"  >


		
	 <div class="span10 offset1 well" >
          
	 		<h2 id="intro">Introduction</h2>
	 		<p>The goal of the discourse project is to take advantage of advances in mobile learning technologies to develop a discussion tool designed specifically for online courses. The need is great. Every online course is potentially a market for a discussion tool that effectively addresses this need.</p>

			<p> Current online discussions are constructed as a chronological record of posts over time. The instructor and related instructional objectives of the course are not represented in this model. In contrast, posts in discourse discussions will have a finer granularity that can be viewed by topic, context, author, or aggregated student annotations. The design specification for discourse is based on an academic year of research by Mable Kinzie and Bill Ferster, working in collaboration with graduate students participating in the instructional design sequence.</p>

			<p>This decoupling of contributions from the temporal order in which they are generated allows discussion from different instructional perspectives. Contributors may have a context in mind, such as a response to an instructor's prompt, contribution of a new idea to the discussion, or responding to another user's comment.</p>

			<p> discourse discussions will allow the instructor to encourage engagement with specific instructional materials and objectives. Rules of engagement define the way that instructors and students are notified within the discourse system. Collections of rules templates provide default rule sets for pedagogical situations such as a case study deconstruction, a brainstorming session, or a reading discussion. An instructor can quickly choose an appropriate template or modify it to meet special needs. Feedback is modeled after Facebook-style “liking” in a way that encourages thoughtful extensions of dialog that advance instructional objectives.</p>

			<p>What follows is a rough sketch of what this tool might look like in the midst of a discussion:</p>
	 		
	 		<h2 id='mockup'>Preliminary Mockup</h2>
	 <p align="center" ><img src="assets/img/screen.gif" width="800" height="533"></p>
      <ol>
        <li>The <em>topic</em> <em>bar</em> at the top contains the a selector to change the current topic being discussed (as assigned by the instructor). That topic constantly appears to keep the discussion on topic.<br>
          <br>
          </li>
        <li>The <em>newsfeed</em> <em>panel</em> is a Facebook/Twitter style newsfeed that displays events such as posts, instructor communications and other feedback in a compact linear format. Clicking on the <em>see</em> link displays that post in the <em>discussion</em> panel, where it can be read in context and responded to.<br>
          <br>
          </li>
        <li>The <em>todo</em> <em>panel</em> shows instructions from the instructor, reminders to respond to requested actions by other students, such as clarifications, polls, etc, and notices about posts from people whom the user "follows" (in the Twitter sense).<br>
          <br>
          </li>
        <li>The <em>discussion panel</em> is where the posts of the discussion are displayed and responded to. There are many ways to view the discussion, ranging from the traditional threaded discussion view to the concept-map like style illustrated above.<br>
          <br>
          </li>
        <li>The <em>time bar</em> at the bottom of the page contains controls to limit the display of posts by time; starting, ending, or both.<br> 
          The discussion can be played like a movie to get a sense of its progression, with all of the panels instantly updated.<br>
          <br>
          </li>
        <li>The encircled <em>see menu</em> <em>button</em> brings up ways to display various panels, controlling by person, topic, etc. See the section below on how the radial menus operate.<br>
          <br>
        </li>
        <li>The encircled <em>set menu</em> <em>button</em> at the bottom bring up allow the user to quickly save/restore the changes made to any of the circled menu icons. An option will enable personalization and user setttings.<br>
          <br>
        </li>
        <li>The sizes of all the panels can be changed, as desired by the user.<br>
        </li>
        </ol>
      <h2 id="say">Say: Contributing to the discussion</h2>
      <p>The instructor would typically start the discussion by posting the first comment. Students would then click on the <em>Say icon</em> to contribute a response to that post. This would bring up a radial menu (similar to those used in Prezi) that grows out of the say icon and blurs the background out:</p>
      <p><img src="assets/img/say1.gif" width="800" height="134"></p>
      <ol>
        <li> A radial menu surrounds the icon offering the various choices and subsequent sub-choices</li>
        <li>Student is encourged to highlight the section of the post they are commenting on. </li>
        <li>The type of response is selected (i.e. textual, feedback,  media, or whiteboard drawing)</li>
        <li>Response is written, and the menu disappears.</li>
        </ol>
      <p><strong>discourse</strong> tries to encourage interaction by making some kinds of feedback fast and easy to contribute, similar to Facebook's "Like" options:    </p>
      <p><img src="assets/img/say2.gif" width="800" height="164"></p>
      <ol>
        <li> A radial menu surrounds the icon offering the various kinds of feedback possible,</li>
        <li>Student is encouraged to highlight the section of the post they are commenting on.</li>
        <li>The type of response is selected (i.e. agree, clarify).</li>
        <li>Student is encouraged to say why they agree, rather than a simple "like."</li>
        </ol>
      <p>Other options for contributing include adding media (video clip, webpage, document, etc) or an interactive whiteboard that can be further annotated by other students:</p>
      <p><img src="assets/img/say3.gif" width="600" height="199"></p>
  <h2 id="see">See: Viewing  the discussion</h2>
The see icon appears on the discussion, newsfeed, and todo list panels to offer different ways to view the posts people have contributed:  <br>
<br>
<img src="assets/img/see.gif" width="686" height="150"><br>
    
      <ol>
        <li>Posts can be selected by who to include.</li>
        <li>Post can be selected using word matching and NLP-based topic semantic clustering.</li>
        <li>Posts can be viewed in a number of ways:<br>
          <br>
          </li>
        <ol>
          <li>As a traditional threaded discussion:</li>
          </ol>
        
          <p><img src="assets/img/conversation.gif" width="400" height="286"><br></p>
          
        <ol start="2">
          <li>As a concept map:</li>
          </ol>
        
          <p><img src="assets/img/conmap.gif" width="400" height="278"></p>
          
        <ol start="2">
          <li>Looking at posts referring to a document:</li>
          </ol>
        
          <p dir="ltr"><img src="assets/img/contxt.gif" width="400" height="279"></p>
        
        <ol start="4">
          <li>Looking at posts referring to a video clip:</li></ol></ol>
          
          <p><img src="assets/img/convid.gif" width="400" height="289"></p>
        
    
      <p>&nbsp;</p>
      <h2 id="contact">Contact</h2>
      <p><strong>For more information, please contact:</strong></p>
      
        <p> Bill Ferster<br>
          +1 540-592-7001<br>
          bferster - at - virginia.edu</p>
        
      <p>A Google Doc of the current working specification can be found <a href="https://docs.google.com/document/d/1uXooyVCtjiu85BXzIpMQrJDYux-QMexZfwcQ70iBJ1g/edit" target="_blank">here</a>.</p>
      <p>&copy;  2012 The University of Virginia</p>
      
        <p><strong></strong></p>
        <p><br>
        </p>
      
      	
	 
	 </div>
	</div>	 
			
</div> <!-- Ends container div --> 

	<!-- Login Modal -->
	<div class="modal hide fade" id="loginModal">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">x</button>
	    <h1>Login </h1>
		<p>If you have an account please login below. Registration is closed until the site is ready for public beta testing.</p>
		
	  </div>
	  <div class="modal-body center">
	      	<div class="form ">
				<div id="loginNotify"> </div>	
				<div id="username_div"> <label for="username">Email: </label><input type="text" name="username" id="username" /></div>
				<div id="password_div"><label for="username">Password: </label><input type="password" name="password" id="password" /></div>
				<p>
					<a href="recover.php" id="recoverLink">  Forgot Password?</a> 
				</p>
			</div>				
	    
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn" data-dismiss="modal">Close</a>
		<button type="submit" id="loginSubmit" class="btn btn-primary"/>Login</button>      
	  </div>
	</div>


<footer class="footer" id="footerFixed">
        <p><span id="footerBrand">dscourse</span> is a project of Curry School of Education, University of Virginia. </p>
</footer>

</body>
</html>