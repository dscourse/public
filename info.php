<!DOCTYPE html>
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="en" >
  <![endif]-->
  <!--[if gt IE 8]>
  <!-->
  <html class="no-js" lang="en" >
    <!--<![endif]-->

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Dscourse</title>

    <link rel="stylesheet" href="landing/css/normalize.css" />

    <link rel="stylesheet" href="landing/css/foundation.css" />
    <link rel="stylesheet" href="landing/css/styles.css" />
    <link rel="stylesheet" href="landing/css/general_foundicons.css">
    <!--[if lt IE 8]>
    <link rel="stylesheet" href="css/general_foundicons_ie7.css">
    <![endif]-->
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet" type="text/css">

    <script src="landing/js/vendor/custom.modernizr.js"></script>

<style>
.borderimg {
border: 1px solid #ccc;
box-shadow: 0 0 15px 2px #ccc;
}
.top-bar {
position: fixed;
top: 0px;
width: 100%;
z-index: 999;
box-shadow: 0 0 21px 7px #000;
height: 46px;
}
</style>
</head>
<body>
  <!-- Begin Menu Bar -->
      <nav class="top-bar">
        <ul class="title-area">
          <!-- Title Area -->
          <li class="name">
            <h1 >
              <a href="#" style="font-size: 30px !important">DSCOURSE</a>
            </h1>
          </li>
          <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
          <li class="toggle-topbar menu-icon">
            <a href="#">
              <span>Menu</span>
            </a>
          </li>
        </ul>

        <section class="top-bar-section">

          <!-- Right Nav Section -->
          <ul class="right">
            <li class="divider hide-for-small"></li>
            <li>
              <a href="http://dscourse.org/login.php" style="font-size: 18px" > Login </a>
            </li>
            <li class="divider"></li>

            <li class="divider show-for-small"></li>
            <!-- Button -->
            <li class="">
              <a class="" href="http://dscourse.org/register.php" style="font-size: 18px" > Register</a>
            </li>
          </ul>
        </section>
      </nav>
      <!-- End Top Navigation Bar -->

    <!-- Begin main Above the fold Header -->
    <div class="full-row grey"> 
<div style="width: 100%; text-align: center; background: url(img/white_blur.png) top center; "><img src="img/dlogo.png" alt="dlogo" width="500" height="225"> <br /> <h1 style="font-size: 91px; margin-top: -116px;">dscourse</h1></div>

      <!-- "full-row" class allows background to stretch to 100% width -->
      <div class="row header">
        <div class="large-10 large-offset-1 columns features-intro" style="text-align: center; margin-top: 0px; ">
          <!-- Main Heading -->
          <h1>A revolutionary new discussion tool for online learning</h1>
          <p class="main">
            Take your online discussions to the next level with a discussion tool built by educators.
          </p>
              
          <!-- Email Subscribe Form -->
          <div class="large-10 large-offset-1 columns">
            <div class="row collapse email">
              <form action="php/submit-sub.php" method="POST" id="email-form">
                <p class="top-email">Sign up to receive updates:</p>

                <div class="small-9 columns">
                  <input type="email" name="email" placeholder="your email address" /> 
                </div>

                <div class="small-3 columns">
                  <!-- Submit button -->
                  <input class="button postfix radius" type="submit" />              
                </div>
                <!-- Return a Thankyou message when the form is submitted -->
                <div id="success-message"></div>

              </form>
              <!-- End Email Row -->
            </div>
          </div>

        </div>

      <!-- End row -->
      </div>
      <!-- End "full-row" -->
    </div>

    <!-- Begin Features -->
    <div class="row features">

      <!-- Divided in to columns of 4 to create a layout of thirds -->
      <div class="large-4 columns">
        <!-- Feature name -->
        <h2>Use Anywhere</h2>
        <!-- Feature Description -->
        <p>
Dscourse is a web application and it will work on almost any operating system and tablets. All you need is an internet connection.        </p>
        <!-- Corresponding Image -->
        <img src="img/chromebook.jpeg" alt="">
        <!-- End Feature -->
      </div>

      <!-- Begin Feature 2 -->
      <div class="large-4 columns">
        <h2>Built for Discussion</h2>
        <p>
Dscourse integrates many tools designed specifically for the needs of a classrooom discussion online, whether you have 5 or 500 students.          </p>
        <img src="img/shot1.png" alt="" class="borderimg">
        <!-- End Feature 2 -->
      </div>

      <!-- Begin Feature 3 -->
      <div class="large-4 columns">
        <h2>LTI Integration</h2>
        <p>
Using LTI standards, Dscourse can connect to many Learning Management Systems so you won't have to recreate your classroom environment.        </p>
        <img src="img/shot2.jpg" alt="" class="borderimg">
        <!-- End Feature 3 -->
      </div>

      <!-- End Row -->
    </div>

    <!-- Large Video Section -->
    <div class="full-row darkgrey">
      <div class="row">
        <div class="large-12 columns video">
          <h1>See how it works!</h1>

          <!-- In Description use <b></b> tags for emphasis -->
          <p>
            Watch our introduction video that walks through the essentials of using dscourse to get you started right away. 
            <p>

<iframe width="853" height="480" src="//www.youtube.com/embed/9bYoeA6Igz0?rel=0" frameborder="0" allowfullscreen></iframe>
					
            </div>
            <!-- End Row -->
          </div>
          <!-- End Full row section -->
        </div>

        <!-- Begin Large Feature Blocks -->
        <div class="full-row grey">
          <div class="row tour">
            <div class="small-12 columns">
              <!-- Main Title & Subtitle -->
              <h1>Features that will power your discussion</h1>
              <p class="header">
Explore the main features of Dscourse that will help your discussion flow. </p>
            </div>

            <!-- Begin Features Tour -->
            <!-- Feature 1 -->
            <!-- Large Image -->
            <div class="large-8 columns">
              <img src="img/shot3.png" class="borderimg">
            </div>
            <!-- Side Text -->
            <div class="large-4 columns description">
              <h2>Individual Discussion</h2>
              <p class="description">Dscourse allows instructors to interact individually with students before opening the discussion to the entire group. Works great for checking understanding. </p>
            </div>
            <!-- Horizontal Rule -->
            <hr>
            <!-- End Feature 1 -->

            <!-- Feature 2 -->
            <div class="large-4 columns description">
              <h2>Multiple ways to post</h2>
              <p>You can post with predefined categories such as Comment, Agree, Disagree, Clarify and Off Topic to contextualize your post and refine notifications. Dscourse also allows for <b>rich media attachments</b> from images, video embed, website embed and advanced drawing options. </p>
            </div>
            <div class="large-8 columns">
              <img src="img/shot4.png" class="borderimg">
              </div>
            <hr>
            <!-- End Feature 2 --> 
            <!-- Feature 3 -->
            <!-- Large Image -->
            <div class="large-8 columns">
              <img src="img/shot5.png" class="borderimg">
            </div>
            <!-- Side Text -->
            <div class="large-4 columns description">
              <h2>Timeline Slider</h2>
              <p class="description"> You can view all the conversations in dscourse in a timeline and see a visual representation of the progress of the discussion. You can also drag the timeline to eliminate all posts after a certain date to see a snapshot of the discussion at any given time. </p>
            </div>
            <!-- Horizontal Rule -->
            <hr>
            <!-- End Feature 3 -->

            <!-- Feature 4 -->
            <div class="large-4 columns description">
              <h2>Smart Search and Navigation</h2>
              <p>Even if your discussion grows up to hundreds of posts you can search for any text and find real-time locations. Our blue vertical navigation bar shows the relative locations and clicking on them will take you to the post right away. Dscourse discussions keep you in the same location and help you move around posts intuitively.   </p>
            </div>
            <div class="large-8 columns">
              <img src="img/shot6.png" class="borderimg">
              </div>
            <hr>
            <!-- End Feature 4 --> 
            <!-- Feature 5 -->
            <!-- Large Image -->
            <div class="large-8 columns">
              <img src="img/shot7.png" class="borderimg">
            </div>
            <!-- Side Text -->
            <div class="large-4 columns description">
              <h2>Connected Posts</h2>
              <p class="description"> There comes a time in your discussion where you want to synthesize several posts at different parts of the discussion. Dscourse allows you to make Connected Posts that link different posts together so that you can take your contributions to the meta level. </p>
            </div>
            <!-- Horizontal Rule -->
            <hr>
            <!-- End Feature 5 -->
            <!-- Feature 6 -->
            <div class="large-4 columns description">
              <h2>Email Notifications </h2>
              <p>With dscourse you can get email notifications in your inbox when specific types of replies are made to your box or someone mentions you in a post with preceding @ sign. Notifications are opt in and you can change your settings any time.  </p>
            </div>
            <div class="large-8 columns">
              <img src="img/shot8.png" class="borderimg">
              </div>
            <hr>
            <!-- End Feature 6 --> 
            <!-- Feature 7 -->
            <!-- Large Image -->
            <div class="large-8 columns">
              <img src="img/shot9.png" class="borderimg">
            </div>
            <!-- Side Text -->
            <div class="large-4 columns description">
              <h2>Shiva Tools </h2>
              <p class="description"> Dscourse integrates several of the SHIVA tools. You can attach documents, web pages, videos, create drawings and charts, as well as add maps. All features come with annotation tools, even the video. Check out more on the <a href="http://shiva.virginia.edu/"> SHIVA website</a>. </p>
            </div>
            <!-- Horizontal Rule -->
            <hr>
            <!-- End Feature 7 -->
            
          </div>
          <!-- End Row -->
        </div>

<div class="full-row grey">
          <div class="row tour" style="text-align: center;">

            <div class="large-10 large-offset-1 columns ">
              <h2 style="margin-top: 20px; ">Want to learn more? </h2>
              <p>
                <b>Sign up now and test it yourself. </b>
                Dscourse is open to interested parties to try out the software and use in their class or project. If you have questions or comments you will be able to use our support forums. 
              </p>
              <!-- Button -->
              <a href="http://dscourse.org/login.php" class=" button">Try it now!</a>
              <!-- End CTA -->
            <!-- End Row -->
          </div>
          <!-- End Grey Container -->
        </div>
</div>

        <!-- Footer -->
 <div class="full-row darkgrey footer">
          <div class="row">
            <!-- Begin Footer Links, Repeat this Block -->
            <div class="large-8 columns">
              <h5>Disclaimer</h5>
              <ul class="footer-nav">
                <li>Dscourse is a proof-of-concept project aimed to improve online discussions. This website exists with the purpose of demonstration and is not a final product aimed at general use. This service is provided "as is" and dscourse.org or University of Virginia cannot be held responsible for any loss of data on this site and cannot guarantee safety of sensitive information. </li>
              </ul>
            </div>


            <div class="large-4 columns">
              <h5>Get in Touch </h5>
              <ul class="footer-nav">
                <li>For any questions or comments please send an email to <b>Bill Ferster</b>: <br /> bferster - at - virginia.edu </li>
              </ul>
            </div>
            
            
            <div class="dark-hr"></div>
            <!-- End Row -->
          </div>
          <!-- End Container -->
        </div>       
        
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>

        <!-- Ajax Form -->
      <script type="text/javascript">
    $(document).ready(function() { 
    // bind form using ajaxForm 
    $('#email-form').ajaxForm({ 
        resetForm: true,

        // target identifies the element(s) to update with the server response 
        target: '#success-message', 
 
        // success identifies the function to invoke when the server response 
        // has been received; here we apply a fade-in effect to the new content 
        success: function() { 
            $('#success-message').fadeIn('slow'); 
        } 
    }); 
});
    </script>

      <script src="landing/js/foundation.min.js"></script>
      <script src="landing/js/foundation/foundation.js"></script>		   
      <script src="landing/js/foundation/foundation.dropdown.js"></script>				   
      <script src="landing/js/foundation/foundation.forms.js"></script>		   
      <script src="landing/js/foundation/foundation.placeholder.js"></script>		   
      <script src="landing/js/foundation/foundation.topbar.js"></script>				   
      <script src="landing/js/jquery.form.js"></script> 


      <script>$(document).foundation();</script>
  </body>
</html>