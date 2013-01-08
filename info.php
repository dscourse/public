<?php 
    define('MyConst', TRUE);                                // Avoids direct access to config.php
    include "../config/config.php"; 

?>
<!DOCTYPE html>

<html lang="en">
<head>
    <title>Welcome to dscourse</title>
    <script type="text/javascript" src="js/jquery-1.7.1.min.js">
</script>
    <script type="text/javascript" src="js/bootstrap.js">
</script>
    <link href="css/bootstrap.css" media="screen" rel="stylesheet" type="text/css">
    <link href="css/style.css" media="screen" rel="stylesheet" type="text/css">
    <script type="text/javascript">

        (function() {                                                   // Auto runs everything inside when this script is loaded
                                            

        $('#signUp').live('click', function() {                         // Execute newsletter sign up 
            
            var email = $('#newsletter').val();                             // Gets email from form 
            
            if (!email) {
                $('#notify').html("<div class=\"alert alert-error animated flash \"><button class=\"close\" data-dismiss=\"alert\">&#120;<\/button>Please enter an email address.<\/div>");   
            } else {
                var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if (!filter.test(email)) {
                    $('#notify').html("<div class=\"alert alert-error animated flash \"><button class=\"close\" data-dismiss=\"alert\">&#120;<\/button>Please provide a valid email address.<\/div>");    
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
                          error: function() {                               // If php did not run 
                                console.log("dscourse Log: There was a problem connecting to the login script. ");  
                          }
                    });     
                    
                }               
                                
            }                                           
        }); 


        $('#loginSubmit').live('click', function() {                    // Execute login function when clicked
            
            var username = $('#username').val();                        // Gets username and password 
            var password  = $('#password').val(); 
            
            if (!username) {
                $('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a username<\/div>");   
            } else if (!password) {
                $('#loginNotify').html("<div class=\"alert alert-error animated flash \">Please enter a password<\/div>");   
            } else {
                $.ajax({                                                    
                    type: "POST",
                    url: "scripts/php/auth.php",
                    data: {
                        username: username,
                        password: password                                      // Sends the login data as array
                    },
                      success: function(data) { 
                            
                            if (data == "redirect"){
                                window.location.href = "index.php"; 
                            }else {                     // If script ran successfully 
                                $('#loginNotify').html(data);                       // The error alerts will be printed here  
                            }
                        }, 
                      error: function() {                                   // If php did not run 
                            console.log("dscourse Log: There was a problem connecting to the login script. ");  
                      }
                });             
            }                                           
        }); 

    })();                                                               // End of self invoking anonymous function

    </script>

    <style>
    	body {
	    	background: #fff url(img/dusk.jpg) top right repeat-x;
	    	background-attachment:fixed;    	
	    }
	    .well {
		    background: rgba(255,255,255,0.59);
		    box-shadow: 0px 0px 11px -2px #333;
		    -webkit-box-shadow: 0px 0px 11px -2px #333;
		    -moz-box-shadow: 0px 0px 11px -2px #333;
			border: none; 
	    }
    </style>
</head>


<body class="index-page">
    <header class=" masthead">
        <div class="inner">
            <div id="logo"><img src="img/dlogo.png"></div>

            <h1>dscourse</h1>

            <h2>A revolutionary new discussion tool for online learning</h2>

            <p id="indexSubtext">dscourse is a project that aims to provide the next generation platform-agnostic discussion tool for online learning. The framework of the tool has been developed at the Curry School of Education and it's currently under development with pilot testing planned for Fall 2012.</p>

            <p class="download-info"><a class="btn btn-primary btn-large" href='login.php'>Sign in</a> <a href="#intro" class="btn btn-info btn-large">Learn More</a></p>

            <p></p>

            <div id="notify"></div>

            <div class="row">
                <div class="span5 offset4 signup">
                    <div class="control-group form-inline ">
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">@</span><input class="span3" id="newsletter" name="newsletter" size="16" type="text" placeholder="Sign up to receive email updates...">
                            </div><button class="btn btn-info" id="signUp">Sign Up</button>
                        </div>
                    </div>
                </div>
            </div>
            <p>
        </div>
    </header>

    <div class="container" id="home">
        <div class="row index-text">
            <div class="span10 offset1 well">
                <h2 id="intro">Introduction</h2>

                <p>The goal of the discourse project is to take advantage of advances in mobile learning technologies to develop a discussion tool designed specifically for online courses. The need is great. Every online course is potentially a market for a discussion tool that effectively addresses this need.</p>

                <p>Current online discussions are constructed as a chronological record of posts over time. The instructor and related instructional objectives of the course are not represented in this model. In contrast, posts in discourse discussions will have a finer granularity that can be viewed by topic, context, author, or aggregated student annotations. The design specification for discourse is based on an academic year of research by Mable Kinzie and Bill Ferster, working in collaboration with graduate students participating in the instructional design sequence.</p>

                <p>This decoupling of contributions from the temporal order in which they are generated allows discussion from different instructional perspectives. Contributors may have a context in mind, such as a response to an instructor's prompt, contribution of a new idea to the discussion, or responding to another user's comment.</p>

                <p>discourse discussions will allow the instructor to encourage engagement with specific instructional materials and objectives. Rules of engagement define the way that instructors and students are notified within the discourse system. Collections of rules templates provide default rule sets for pedagogical situations such as a case study deconstruction, a brainstorming session, or a reading discussion. An instructor can quickly choose an appropriate template or modify it to meet special needs. Feedback is modeled after Facebook-style “liking” in a way that encourages thoughtful extensions of dialog that advance instructional objectives.</p>





                <h2 id="say">Say: Contributing to the discussion</h2>

                <p>The instructor would typically start the discussion by posting the first comment. Students would then click on the <em>Say icon</em> to contribute a response to that post. This would bring up a radial menu (similar to those used in Prezi) that grows out of the say icon and blurs the background out:</p>



                <p><strong>discourse</strong> tries to encourage interaction by making some kinds of feedback fast and easy to contribute, similar to Facebook's "Like" options:</p>



                <p>Other options for contributing include adding media (video clip, webpage, document, etc) or an interactive whiteboard that can be further annotated by other students:</p>


                <h2 id="see">See: Viewing the discussion</h2>The see icon appears on the discussion, newsfeed, and todo list panels to offer different ways to view the posts people have contributed:<br>
                <br>



                <p>&nbsp;</p>

                <h2 id="contact">Contact</h2>

                <p><strong>For more information, please contact:</strong></p>

                <p>Bill Ferster<br>
                +1 540-592-7001<br>
                bferster - at - virginia.edu</p>

                <p>A Google Doc of the current working specification can be found <a href="https://docs.google.com/document/d/1uXooyVCtjiu85BXzIpMQrJDYux-QMexZfwcQ70iBJ1g/edit" target="_blank">here</a>.</p>

                <p>&copy; 2012 The University of Virginia</p>

                <p></p>
                <hr class="soften" />
                <p><span id="footerBrand">dscourse</span> is a project of Curry School of Education, University of Virginia.</p>
                
                <p><br></p>
            </div>
        </div>
    </div><!-- Ends container div -->


</body>
</html>
