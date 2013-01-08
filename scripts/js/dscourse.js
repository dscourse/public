/*
 *  All Course related code
 
 To-Dos:
 - Remove navigation link actions because user and discussion navigations are removed. 
 - Clean up events to reduce redundancies. 
 
 */

function Dscourse() 
{

	// Globals
	this.data 		   = new Array(); 
	this.data.allUsers	= new Array();
	this.data.allCourses = new Array();
	this.data.allDiscussions = new Array();
	this.data.allPosts = new Array();

	// Users 
	this.nameList = new Array ();
	this.nameListName = new Object;	
	
	// Courses 
	this.course = { }; 

	// Discussions
	this.discussion = { }; 	
	
	// Posts
	this.post = { };
	this.currentSelected = '';  		// Needed for selection
	this.currentStart = '';
	this.currentEnd = ''; 
	
	this.currentDrawing = ''; 			// The drawing data that will be saved to the database. 
	this.currentDrawData = ''; 			// this is used for displaying drawings; 
	this.currentMediaType = ''; 		// What kind of media should be displayed. 
	this.postMediaType = 'draw'; 	// Used while saving the media type data. 
	
	this.uParticipant = new Array; 	// Unique list of participants. 
	
	this.newPosts = ''; 	// A string of the posts for a discussion that are new when refreshed. This variable is used to transfer post ids between functions.  

	this.visitTime = ''; // The reason I'm setting this here is because of load order of the LastVisit function using ajax. 
	
	// Timeline
	this.timelineMin = 0;
	this.timelineMax = 0;
	
	// Fix for multiple image uploads 
	this.imgUpload = '';
	
	// Get all Data
	this.GetData('getAll', 'load');
	this.AddLog('load','','systemLoad','','User loaded system.')

	// Synthesis Post Globals
	this.sPostID;
	this.sPostContent; 

	// All view related actions that need to be loaded in the beginning
	var top = this;
	
	$(document).ready(function() {										// Wait for everything to load. 

		/************ Navigations  ******************/
 		

		var linkID;	
		$('.nav > li > a').live('click', function () {					// Show page contents depending on what link was clicked. 
			linkID = $(this).attr('id');
			$('.page').hide();	
			
			switch(linkID)
			{
			case 'usersNav':
			  $('#usersPage').show();
			  $("#addUserForm").fadeOut();
			  $("#userList").delay(200).fadeIn();
			  $('.headerTabs a').addClass('linkGrey');			  
			  $('#userListLink').removeClass('linkGrey');
			  dscourse.imgUpload = 'user';
			  break;
			case 'helpNav':
			  $('#helpPage').show();
			  break;
			case 'coursesNav':
			  $('#coursesPage').show();
			  dscourse.imgUpload = 'course';
			  $('#courseForm').fadeOut();
			  $('#courses').delay(300).fadeIn();
			  top.ClearCourseForm(); // Fields are emptied to reuse			  
			  $('.headerTabs a').addClass('linkGrey');			  
			  $('#allCoursesView').removeClass('linkGrey');
			  break;
			case 'discussionsNav':
			  $('#discussionsPage').show();
			  $('.headerTabs a').addClass('linkGrey');			  
			  $('#allDiscussionView').removeClass('linkGrey');
			  $('#discussionForm').fadeOut();
			  $('#discussions').delay(300).fadeIn();
			  break;
			case 'profileNav':
			  var userid = $(this).attr('userid');
			  dscourse.UserProfile(userid);	
			  dscourse.imgUpload = 'user';
			  $('#profilePage').show();
			  break;		  
			default:
			  top.showHome();
			}
		});
			
		$('#homeNav').live('click', function () {						// Home link
				top.showHome();
		});

		$('#helpNav').live('click', function () {						// Help link
			  $('.page').hide();
			  $('#helpPage').show();
		});

		$('#profileNav').live('click', function () {					// Home link
			  $('.page').hide();
			  var userid = $(this).attr('userid');
			  dscourse.UserProfile(userid);	
			  dscourse.imgUpload = 'user';
			  $('#profilePage').show();
		});
			
		$('#usersNav').live('click', function () {						// Home link
			  	$('.page').hide();			
			  $('#usersPage').show();
			  $("#addUserForm").fadeOut();
			  $("#userList").delay(200).fadeIn();
			  $('.headerTabs a').addClass('linkGrey');			  
			  $('#userListLink').removeClass('linkGrey');
			  dscourse.imgUpload = 'user';
		});
			
			
		$('.showProfile').live('click', function () {						// Home link
		  $('.page').hide();
		  var userid = $(this).attr('userid');
		  top.UserProfile(userid);
		  $('#userInfoWrap').show(); // hide user info
		  $('#addUserForm').hide(); // make visible user edit form. 
		  $('#profilePage').show();		
		  $('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
	
		});    
		
		$('.courseLink').live('click', function () {						// Home link
			$('.page').hide();
			var courseid = $(this).attr('courseid');
			top.getCourse(courseid);
			top.listCourseDiscussions(courseid);
			top.listCourseStudents(courseid);
			$('#coursePage').show();
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
		});
		
		$('#navLevel2').live('click', function () {						// Home link
			$('.page').hide();
			var courseid = $(this).attr('cLinkID');
			top.getCourse(courseid);
			top.listCourseDiscussions(courseid);
			top.listCourseStudents(courseid);
			$('#coursePage').show();
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
		});		
	
		$('.discussionLink').live('click', function () {						// Discussion link
			$('#recentContent').html('<li></li>');
			var discID = $(this).attr('discID');
			dscourse.SingleDiscussion(discID);
			$('.page').hide();
			$('#footerFixed').hide();
			$('#discussionWrap').show();
			top.DiscResize(); 
			top.VerticalHeatmap();
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
		});

		$('#navLevel3').live('click', function () {						// Discussion link for the top navigation bar. Replica of above. Make cleaner. 
			$('#recentContent').html('<li></li>');
			var discID = $(this).attr('dLinkID');
			dscourse.SingleDiscussion(discID);
			$('.page').hide();
			$('#footerFixed').hide();
			$('#discussionWrap').show();
			top.DiscResize();
			top.VerticalHeatmap();
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
		});

		// Scrolling to top on any page
		$('#backTop').live('click', function () {
				$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
		});

		$(window).scroll(function(){
				var scrollLocation = window.pageYOffset; 
				if(scrollLocation > 400) {
					$('#backTop').show(); 
				} else { 
					$('#backTop').hide(); 	
				}
			});
	
		// Window resize changes discussion elements. 
		$(window).resize(function() {				// if so, look for window resize event
			  top.DiscResize();
			  top.VerticalHeatmap();
			});


		/************ Discussions  ******************/
	
		$('#commentWrap').draggable();
		
		$('#discussionForm').hide();
	
		$("#discussionStartDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
		$("#discussionOpenDate").datepicker({ dateFormat: "yy-mm-dd" });
		$("#discussionEndDate").datepicker({ dateFormat: "yy-mm-dd" });
	
		$("#courseStartDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
		$("#courseEndDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
	
		//fixing the buttons for roles so we don't need bootstrap files. 
		$('#roleButtons .btn').live('click', function () {
			var buttonUserId = $(this).attr('userid');	
			var selectorText = '#roleButtons .btn[userid="' + buttonUserId + '"]';
			$(selectorText).removeClass('active');
			$(this).addClass('active');
		});

		$('.removePeople').live('click', function() {
			$(this).closest('tr').remove();
		});	
	
		$('#highlightShow').live('mouseup', function () {
			var spannedText = $(this).find('span').text(); 					//remove highlight from text
			$(this).find('span').replaceWith(spannedText); 								
			top.currentSelected = top.GetSelectedText(); 
			var element = document.getElementById("highlightShow");  
			top.currentStart = top.GetSelectedLocation(element).start; 
			top.currentEnd	= top.GetSelectedLocation(element).end;
			$('#locationIDhidden').val(top.currentStart + ',' + top.currentEnd);	// Add location value to form value; 
			var replaceText = $('#highlightShow').html(); 
			var newSelected = '<span class="highlight">' + top.currentSelected + '</span>'; 
			var n = replaceText.replace(top.currentSelected, newSelected); 
			$('#highlightShow').html(n);									// add highlight to text. 
		});

		$('#discussionDivs').tooltip({ selector: "span", placement: 'bottom' });  
		$('#participants').tooltip({ selector: "li" });  
		$('#shivaDrawPaletteDiv').tooltip({ selector: "button" });  

		$('.threadText').live('click', function (event) {
			event.stopPropagation();
			$('.threadText').removeClass('highlight');
			$('.threadText').find('span').removeClass('highlight');
			var postClickId = $(this).closest('div').attr('level');
			dscourse.HighlightRelevant(postClickId);
			$(this).removeClass('agree disagree comment offTopic clarify').addClass('highlight');
			});
			

		$('.threadText').live('mouseover', function (event) {
			event.stopImmediatePropagation();
			$(this).children('.sayBut2').show();
			/*
$('.threadText').find('span').removeClass('highlight');
			var postClickId = $(this).closest('div').attr('level');
			dscourse.HighlightRelevant(postClickId);
			$(this).children('.postTextWrap').children('.selectionMsg').show();
			if(!$(this).hasClass('highlight')){	$(this).addClass('lightHighlight'); }
*/
		});

		$('.threadText').live('mouseout', function (event) {
			event.stopImmediatePropagation();
			$(this).children('.sayBut2').hide();
			/*
$(this).children('.postTextWrap').children('.selectionMsg').hide();
			$(this).removeClass('lightHighlight'); 
*/
		});

		
		$('.refreshBox').live('click', function () {
			$(this).hide();
			var discID = $(this).attr('discID');
			top.GetData('getAll', 'refreshD', discID);  // We load our new discussion with all the posts up to date
	
			// Anything below GetData won't work because data is doing it's own thing, elements are not all loaded yet here. 
		});

	    $('.carousel').carousel('cycle');	// Image carousel at the home page. 

	    // When the main window scrolls heatmap needs to redraw
	    $('#dMain').scroll(function() {
				  top.VerticalHeatmap();
				  top.DrawShape(); 	
		});

			$('#keywordSearchText').live('keyup', function () {
			var searchText = $('#keywordSearchText').val();  // get contents of the box
			if(searchText.length > 0 && searchText != ' '){
				top.ClearVerticalHeatmap();
				console.log('Search text: ' + searchText); // Works
				top.VerticalHeatmap('keyword', searchText);// Send text to the vertical heatmap app
			} else {
				top.ClearKeywordSearch('#dMain'); 

			}
		});



	}); // End of window onload. 

/************ User Events  ******************/
		
	
	$("#addUserLink").live('click', function() {					// User button and click events 
		$('#userListLink').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$("#userList").fadeOut();
		$("#addUserForm").fadeIn();		
		$('#userButtonDiv').html('<button class="btn btn-primary" id="addUserButton">Add User</button> <button class="btn btn-info" id="cancelUser">Cancel</button>');
		top.ClearUserForm();	 // Fields are emptied to reuse
	});
	
	$("#userListLink").live('click', function() {
		$("#addUserForm").fadeOut();
		$("#userList").delay(200).fadeIn();
		$('#addUserLink').addClass('linkGrey');
		$(this).removeClass('linkGrey');
	});
	
	$('.editUser').live('click', function() {  					// When edit button is clicked. 	
		$("#userList").fadeOut();
		$("#addUserForm").delay(200).fadeIn();
		$('#userButtonDiv').html('<button class="btn btn-primary" id="updateUser">Update User Information</button> <button class="btn btn-info" id="cancelUser">Cancel</button>');
		var UserID1 = $(this).attr("id");						// Get the id for this course. 
		editUser(UserID1);										// Edit the course with the specific id. 	
		$('html, body').animate({scrollTop:0});					// The page scrolls to the top to see the notification
	});		

	$('#updateUser').live('click', function() {  				// When edit button is clicked. 	
		updateUser('all');											// Edit the course with the specific id.
		top.GetData();
		$("#addUserForm").fadeOut();
		$("#userList").delay(200).fadeIn();
		$('html, body').animate({scrollTop:0});					// The page scrolls to the top to see the notification
	});	
	
	$('#cancelUser').live('click', function() {  
		$("#addUserForm").fadeOut();
		$("#userList").delay(200).fadeIn();
		$('html, body').animate({scrollTop:0});		
	});
	
	// User form validations
	$('#firstName').live('change', function() { checkFirstName();});
	$('#lastName').live('change', function() { checkLastName();});		
	$('#email').live('change', function() { checkEmail();});		
	$('#password').live('change', function() { checkPassword();});				
	$('#facebook').live('change', function() { checkFacebook();});		
	$('#twitter').live('change', function() { checkTwitter();});		
	$('#phone').live('change', function() { checkPhone();});		
	$('#website').live('change', function() { checkWebsite();});		
	$('#userAbout').live('keyup', function() { checkAbout();});
	
	// Course Form Validations
	
	
	
	// Discussion Form Validations
	
	
	
	
					
	$('#filterUserText').live('keyup', function() { 					// Filtering users
		var searchTerm = $(this).val();
		filterUsers(searchTerm);
	});

	$('#profilePageEdit').live('click', function() {  				// Functions for user editing profile. 
		var thisUserID = $(this).attr('profilepageid');   // Get the userid from this button
		top.ClearUserForm(); 							// clear the user form. 
		$('#addUserForm').appendTo($('#profilePage'));// Append the user edit div to the user page 
		$('#userButtonDiv').html('<button class="btn btn-primary" id="updateSingleUser" thisUser="' + thisUserID + ' ">Update User Information</button> <button class="btn btn-info" id="cancelSingleUser">Cancel</button>');
		// change the submit button to something specific to individual user edit. 
 		$('#sysControl').hide();// Hide #sysControl - so user can't change her own system role. 
 		$('#userStatusControl').hide();// Hide #userStatusControl - so user can't change his own status. 
		$('#email').attr('disabled', 'disabled');
		$('#userInfoWrap').hide(); // hide user info
		$('#addUserForm').fadeIn(); // make visible user edit form. 
		editUser(thisUserID);// Apply the information to the empty boxes. 
		// Dont' forget to restore when the user goes to the users page to edit. 
	});
	
	$('#updateSingleUser').live('click', function() {  				// When edit button is clicked. 	
		updateUser('single');											// Edit the course with the specific id.
		var thisUserID = $(this).attr('thisUser');
		$("#addUserForm").hide();
		$('#userInfoWrap').fadeIn(); 	
		$('html, body').animate({scrollTop:0});					// The page scrolls to the top to see the notification
	});	
	
	$('#cancelSingleUser').live('click', function() {  
		$("#addUserForm").hide();
		$('#userInfoWrap').fadeIn(); 
		$('html, body').animate({scrollTop:0});		
	});



/************ Course Events ******************/


	$('#allCoursesView').live('click', function() {  				// View all courses 	
		top.listCourses('all');
		$('.headerTabs a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		top.ClearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#activeCoursesView').live('click', function() {  				// View all active courses 	
		top.listCourses('active');
		$('.headerTabs a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		top.ClearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#archivedCoursesView').live('click', function() {  				// View all archived courses 	
		top.listCourses('archived');
		$('.headerTabs a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		top.ClearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#courseFormLink').live('click', function() {  				// View all archived courses 	
		$('#courseForm').find('input:text, input:password, input:file, select, textarea').val(''); // Fields are emptied to reuse
		$('#addPeopleBody').html('');
		$('.headerTabs a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courses').fadeOut();
		$('#courseForm').delay(200).fadeIn();
		$('#courseButtonDiv').html('<button class="btn btn-primary" id="courseFormSubmit">Add Course</button> <button class="btn btn-info" id="cancelCourse">Cancel</button>');
	});
	
	$('#courseFormSubmit').live('click', function() {  				// View all archived courses 	
		// We need to validate course creation.
		top.addCourse();
		$('.headerTabs a').addClass('linkGrey');
		$('#allCoursesView').removeClass('linkGrey');
		$('#courseForm').hide();
		$('#courses').show();
		$('.page').hide();	
		$('#coursesPage').show();
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
		$('#notifyCourse').fadeIn().html("<div class=\"alert alert-success \"><strong> Success! </strong>Your changes were saved.</div>").delay(5000).fadeOut(400);					
		top.ClearCourseForm(); // Fields are emptied to reuse
	});


	$('.editCourse').live('click', function() {  				// When edit button is clicked. 	
		$('#courses').fadeOut();
		$('#courseForm').delay(200).fadeIn();
		$('#courseButtonDiv').html('<button class="btn btn-primary" id="updateCourse">Update Course Information</button> <button class="btn btn-info" id="cancelCourse">Cancel</button>');		
		var courseID = $(this).attr("id");						// Get the id for this course. 
		top.editCourse(courseID);									// Edit the course with the specific id. 	
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
	});			

	$('#updateCourse').live('click', function() {  				// When update button is clicked. 			
		top.updateCourse();
		$('.headerTabs a').addClass('linkGrey');
		$('#allCoursesView').removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
		$('#notifyCourse').fadeIn().html("<div class=\"alert alert-success \"><strong> Success! </strong>Your changes were saved.</div>").delay(5000).fadeOut(400);					
		top.ClearCourseForm(); // Fields are emptied to reuse
	});	

	$('#cancelCourse').live('click', function() {  				// When cancel button is clicked. 			
		$('.headerTabs a').addClass('linkGrey');
		$('#allCoursesView').removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
		top.ClearCourseForm(); // Fields are emptied to reuse
	});	
	
	$('#roleButtons').button();
	$('.removePeople').live('click', function() {
				$(this).closest('tr').remove();
	});


/************ Discussion Events ******************/


	$('#discussionFormSubmit').live('click', function() { 
		var discValState = ValidateDiscussions();						// Checks validation for discussion
		if(discValState == 'pass'){	
			top.addDiscussion();
			top.ClearDiscussionForm(); // Fields are emptied to reuse
			} 
		else if(discValState == 'fail'){	
			alert('Oops! It looks like you did not enter some information correctly. Check the error messages on the page for details.');
			}
		});

	$('#discussionFormUpdate').live('click', function() { 
		var discValState = ValidateDiscussions();						// Checks validation for discussion
		var discussionID = $('#discIdHidden').val();
		if(discValState == 'pass'){	
			top.updateDiscussion(discussionID);
			top.ClearDiscussionForm(); // Fields are emptied to reuse
			} 
		else if(discValState == 'fail'){	
			alert('Oops! It looks like you did not enter some information correctly. Check the error messages on the page for details.');
			}					
		});

	$('#discussionFormCancel').live('click', function() { 
		$('.headerTabs a').addClass('linkGrey');
		$('#allDiscussionView').removeClass('linkGrey');
		$('#discussionForm').fadeOut();
		$('#discussions').delay(300).fadeIn();
		top.ClearDiscussionForm(); // Fields are emptied to reuse
		});

	$('#discussionQuestion').live('change', function() {  			  	
		checkDiscussionQuestion();
	});

	$('#discussionPrompt').live('keyup', function() {  			  	
		checkDiscussionPrompt();
	});
	
	$('#allDiscussionView').live('click', function() {  					
		$('.headerTabs a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#discussionForm').fadeOut();
		$('#discussions').delay(300).fadeIn();
		top.ClearDiscussionForm(); // Fields are emptied to reuse
	});
	
	$('#addDiscussionView').live('click', function() {  					
		// Make this a popup. 
	});
	
	$('#createDhome').live('click', function() {  					
		$('.page').hide(); 
		$('#discussions').hide();			  
		top.ClearDiscussionForm();
		$('#discussionsPage').show();
		$('.headerTabs a').addClass('linkGrey');			  
		$('#discussionForm').fadeIn();
		$('#discussionButtondiv').html('<button class="btn btn-primary" id="discussionFormSubmit">Submit</button> <button class="btn btn-info" id="discussionFormCancel">Cancel</button>'); 
	});

	$('.editDiscussion').live('click', function() {  				 	
		top.ClearDiscussionForm();
		var dID = $(this).attr('id');
		$('.headerTabs a').addClass('linkGrey');
		$('#discussions').fadeOut();
		$('#discussionForm').delay(200).fadeIn();
		top.editDiscussion(dID);
		$('#discussionButtondiv').html('<button class="btn btn-primary" id="discussionFormUpdate">Update Discussion</button> <button class="btn btn-info" id="discussionFormCancel">Cancel</button>'); 
	});
	
	$('.removeCourses').live('click', function() {
		  			$(this).closest('tr').remove();
	});
		  
/************ Post Events ******************/


	$('#addPost').live('click', function() {
		var checkDefault = $('#text').val();				// Check to see if the user is adding default comment text. 
		var buttonType = $('#postTypeID > .active').attr('id');
		// If comment button has class active
		if(buttonType == 'comment'){
			if(checkDefault == 'Your comment...' || checkDefault == ''){
					$('#text').addClass('textErrorStyle');	
					$('#textError').show();
			} else {
				postOK();
			}
		} else {
			postOK();
		}
		
		// if checks out then do it. 
		function postOK() {
			$('.threadText').removeClass('highlight');	
			if(checkDefault == 'Why do you agree?' || checkDefault == 'Why do you disagree?' || checkDefault == 'What is unclear?' || checkDefault == 'Why is it off topic?' ){
				$('#text').val(' '); 
			}
			top.AddPost();										// Function to add post 
			var discussionID = $('#dIDhidden').val();
			$('#commentWrap').slideUp();
			$('#overlay').hide();
			$('#shivaDrawDiv').hide();						
			$('#shivaDrawPaletteDiv').hide();				
			top.ClearPostForm();
			$('.threadText').removeClass('yellow');
			top.DiscResize();
			top.VerticalHeatmap();
		}
		
	});
	
	$('#addSynthesisButton').live('click', function() {

		top.AddSynthesis();
		 
	});
	
	
	$('#text').live('click', function () {
		var value = $('#text').val(); 
		if (value == 'Why do you agree?' || value == 'Why do you disagree?' || value == 'What is unclear?' || value == 'Why is it off topic?' || value == 'Your comment...'){
			$('#text').val(''); 
		}
	});
	
	$('.sayBut2').live('click', function (e) {
		var discID = $('#dIDhidden').val();
		var dStatus = top.DiscDateStatus(discID);
		var userRole = top.UserCourseRole(discID, currentUserID);
		
		if(userRole == 'unrelated'){
			alert('Sorry, you are not part of this course and therefore can\'t post on this discussion.');
			return;
		}
		
		if(dStatus != 'closed'){
			$('#highlightDirection').hide();
			$('#highlightShow').hide();
			var postQuote = $(this).parent().children('.postTextWrap').children('.postMessageView').html();
			postQuote = $.trim(postQuote);
					
			var xLoc = e.pageX-80; 
			var yLoc = e.pageY+10; 
			$('#commentWrap').css({'top' : yLoc, 'left' : '30%'});
			$('.threadText').removeClass('highlight');		
			var postID = $(this).attr("postID");
			if(postQuote != ''){
				$('#highlightDirection').show();
				$('#highlightShow').show().html(postQuote);
				}
			$('#postIDhidden').val(postID);			
			$('#overlay').show();
			$('#commentWrap').fadeIn('fast');
			$(this).parent('.threadText').removeClass('agree disagree comment offTopic clarify').addClass('highlight');
			$('#text').val('Your comment...');
			$.scrollTo( $('#commentWrap'), 400 , {offset:-100});

		} else {
			alert('This discussion is closed.');
		}

	});
	
	$('#postCancel').live('click', function () {
		$('.threadText').removeClass('highlight');		
		$('#commentWrap').fadeOut();
		$('#overlay').hide();
		$('#shivaDrawDiv').hide();						
		$('#shivaDrawPaletteDiv').hide();		
		top.ClearPostForm();
	});
 
 	$('#synthesize').live('click', function () {
		$('#synthesisPostWrapper').html(''); // Clear existing posts 
		
		// Get rid of comment tab -- we need to be able to carry the content and info for this. 
		$('.threadText').removeClass('highlight');		
		$('#commentWrap').fadeOut();
		$('#overlay').hide();
		$('#shivaDrawDiv').hide();						
		$('#shivaDrawPaletteDiv').hide();		
		
		// Synthesis side 
		
		var synthesisFromID = $('#postIDhidden').val();		// Get post from ID to the global variable
		var synthesisComment = $('#text').val();	// Get comment content to the global variable
		var postQuote = $('div[level="'+ synthesisFromID +'"]').children('.postTextWrap').children('.postMessageView').html(); // Get the post content
		    if(postQuote){
		    	postQuote = top.truncateText(postQuote, 30);	// Shorten the comment to one line. 
		    }
		
		$('#addSynthesis').show(); 
		$('.dCollapse').hide(); 					// hide every dCollapse
		var selector = '.dCollapse[id="dSynthesis"]'; 
		$(selector).slideDown(); 				// show the item with dTab id
		
		// Populate the fields for the synthesis if the source is not top level
		if(synthesisFromID != 0){
			$('#synthesisText').val(synthesisComment);
			$('#spostIDhidden').val(synthesisFromID); 
			$('#synthesisPostWrapper').prepend('<div sPostID="'+ synthesisFromID +'" class="synthesisPosts">' + postQuote + ' <div>');  // Append original post
		}
		/*
$('.sidebarTabLink').removeClass('active'); 	// remove active from sidebarTabLink class objects
		$('.sidebarTabLink[id="dSynthesis"]').addClass('active');		

*/
		top.ClearPostForm();

	});

 	$('.synthesisPosts').live('click', function (event) {  // posts inside the synthesis
		event.stopImmediatePropagation();

	 	var thisPost = $(this).attr('sPostID'); 
 		var postRef = 'div[level="'+ thisPost +'"]';
 		$('#dMain').scrollTo( $(postRef), 400 , {offset:-100});	
    	$(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', '#fff');$(this).dequeue();});
    	
    	$('.synthesisPosts').css('background-color', '#FAFDF0') // Original background color 
    	$(this).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)');  // Change the background color of the clicked div as well. 
	});

	$('.synthesisPost').live('click', function () {  // Single synthesis wrapper
    	$(this).children('.synthesisPosts').fadeToggle(); 

	});

	
	$('.SynthesisComponent').live('click', function () {
	 	var thisPost = $(this).attr('synthesisSource'); 
 		var postRef = '.synthesisPost[sPostID="'+ thisPost +'"]';
 		console.log(postRef); 
 		$('#dSidebar').scrollTo( $(postRef), 400 , {offset:-100});
    	$(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', 'whitesmoke');$(this).dequeue();}); 			
    	$('#dInfo').fadeOut(); // hide #dInfo
    	$('#dSynthesis').fadeIn(); // show #synthesis
	
	});
	
	
 	$('#cancelSynthesisButton').live('click', function () {
	 	$('#addSynthesis').slideUp('fast'); 
	});

	
	$('#overlay').live('click', function () {
		$('.threadText').removeClass('highlight');		
		$('#commentWrap').fadeOut('fast');
		$('#overlay').hide();
		$('#shivaDrawDiv').hide();						
		$('#shivaDrawPaletteDiv').hide();				
		top.ClearPostForm();
	});

	$('.postTypeOptions').live('click', function () {
		$('.postTypeOptions').removeClass('active');
		$(this).addClass('active');
		var thisID = $(this).attr('id');
		var txt = $('#text').val(); 
		if(txt == 'Why do you agree?' || txt == 'Why do you agree?' || txt == 'Why do you disagree?' || txt == 'What is unclear?' || txt == 'Why is it off topic?' || txt == 'Your comment...' ){   // Check is the text is still the default text; we don't want to override what they wrote. 
			switch(thisID)									// Get what kind of post this is 
				{
				case 'agree':
				  $('#text').val('Why do you agree?');
				  break;
				case 'disagree':
				  $('#text').val('Why do you disagree?');
				  break;
				case 'clarify':
				  $('#text').val('What is unclear?');
				  break;
				case 'offTopic':
				  $('#text').val('Why is it off topic?');
				  break;		  
				default:
				  $('#text').val('Your comment...');
				}
			}
	});
	
	$('.postTypeWrap').live('click', function () {
		var currentType = $(this).attr('typeID'); 
		var thisLink = $(this).children('.typicn'); 
		currentType = '.threadText[postTypeID="' + currentType + '"]';
		var parentDiv = $(this).parent('div').parent('.threadText');

		$(parentDiv).children(currentType).fadeToggle('fast', function() {
				  });
		if(thisLink.hasClass('grey-icons') == true){
			   thisLink.removeClass('grey-icons');
		   } else {
			    thisLink.addClass('grey-icons');
		   }
	});
		
	$('.postTypeWrap').live('mousedown', function () {				// This is just for style to make it look like a button. 
		$(this).addClass('buttonEffect');
	});
	
	$('.postTypeWrap').live('mouseup', function () {				 
		$(this).removeClass('buttonEffect');
	});

	$('#showTimeline').live('click', function () {
		$('#timeline').slideToggle().queue(function () {top.DiscResize();top.VerticalHeatmap();$(this).dequeue();});	
		if($(this).hasClass('active') == true) {
				$(this).removeClass('active');
			} else {
				$(this).addClass('active');	
			}
	});
		
	$('#showSynthesis').live('click', function () {
		$('#dInfo').fadeToggle(); // toggle hide sidebar content
		$('#dSynthesis').fadeToggle();
		if($(this).hasClass('active') == true) {
				$(this).removeClass('active');
			} else {
				$(this).addClass('active');	
			}				
	});
	
	$('#media').live('click', function () {
		$('#commentWrap').hide();
		$('#mediaBox').show();
		var mHeight = $(window).height()-200 + 'px';		
		$('#mediaWrap').html('<iframe id="node" src="http://www.viseyes.org/shiva/draw.htm" width="100%" height="'+ mHeight +'" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>');					
		$('html, body').animate({scrollTop:0});	
	});
	
	$('#closeMedia').live('click', function () {			// ?? I'm not sure where this is called. Check!
		$('#mediaBox').hide();
	 	$('#displayFrame').hide();
		$('#commentWrap').show();
	});

	$('#closeMediaDisplay').live('click', function () {
		$('#mediaDisplay').hide();
		$('#commentWrap').hide();
	 	$('#displayFrame').hide();
	});			
		
	
	$('.zButtons').live('click', function () {						// Zoom buttons and functions
		var zoomType = $(this).attr('zoom'); 
		if(zoomType == 'in'){
				$('.levelWrapper').css( {'zoom' : '+=0.2', 'line-height' : '+=3'});
			} else if(zoomType == 'out') {
				$('.levelWrapper').css( {'zoom' : '-=0.2', 'line-height' : '-=3'} );
			} else if(zoomType == 'reset'){
				$('.levelWrapper').css( {'zoom' : '1.0', 'line-height' : '23px'} );
			}
	});

	$('.uList').live('click', function () {						// User heatmap buttons and functions
		var uListID = $(this).attr('authorId'); 
			top.ClearVerticalHeatmap();
			top.VerticalHeatmap('user', uListID); 
	});

	$('.drawTypes').live('click', function () {						// User heatmap buttons and functions
		top.postMediaType = 'draw'; 
		var mHeight = $(window).height()-200 + 'px';		
		$('.drawTypes').removeClass('active');
		var drawType = $(this).attr('id'); 
		// New iframe way 
		 switch(drawType)									// Get what kind of iframe this is
			{
			case 'Video':
			 	type = 'video';
			  break;
			case 'Drawing':
				type = 'draw';
			  break;		  
			case 'Map':
				type = 'map'; 
			  break;
			case 'Web':  	
				type = 'webpage'; 
			  break;
			default:
				type = 'draw'; 
			}
		var html = 	'<iframe id="node" src="http://www.viseyes.org/shiva/'+ type + '.htm" width="100%" height="'+ mHeight +'" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>'; 
		$('#mediaWrap').html(html); 
		top.postMediaType = type; 
		$(this).addClass('active');
	});

	$('#continuePost').live('click', function () {						// When user clicks to save draw data into post. 
		top.currentDrawing = ''; 
		ShivaMessage('node','GetJSON'); 
		$('#mediaBox').hide();
		$('#commentWrap').show();
	}); 

	$('#drawCancel').live('click', function () {						// When user clicks to save draw data into post. 
		top.currentDrawing = ''; 
		$('#mediaBox').hide();
		$('#commentWrap').show();

	}); 			
			
	$('.mediaMsg').live('click', function (event) {
		event.stopImmediatePropagation();
		var postId = $(this).closest('.threadText').attr('level'); 
		top.currentDrawData = ''; 
		top.currentMediaType = 'Draw';
		
		var cmd;
		
		$('#displayDraw').html('').append('<iframe id="displayIframe" src="http://www.viseyes.org/shiva/go.htm" width="100%" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>');
		var i, o;
		for(i = 0; i < top.data.allPosts.length; i++){
			 o = top.data.allPosts[i];
			 if(o.postID == postId){
			 	//top.currentDrawData = o.postMedia; 
			 	//top.currentMediaType = o.postMediaType; 
			 	console.log(o.postMedia); 
				cmd ="PutJSON="+o.postMedia;
//				document.getElementById('display').contentWindow.postMessage(cmd,"*");	
				$('#displayFrame').show();
				$('html, body').animate({scrollTop:0});	
			 }
			 $('#displayIframe').load(function () { document.getElementById('displayIframe').contentWindow.postMessage(cmd,"*");	}).queue(function () {top.DiscResize(); top.VerticalHeatmap();$('#containerDiv').css('width', '100% !important'); $(this).dequeue();});
		
		 }
	  				
	}); 
			
	 $('#recentContent li').live('click', function () {
 		var postID = $(this).attr('postid'); 
 		var postRef = 'div[level="'+ postID +'"]';
 		$('#dMain').scrollTo( $(postRef), 400 , {offset:-100});
    	$(postRef).removeClass('agree disagree comment offTopic clarify').addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', '#fff');$(this).dequeue();});
	 });

	$('#hideRefreshMsg').live('click', function () {
		$('#checkNewPosts').hide('');
	});


/*
	$('.sidebarTabLink').live('click', function () {	// when sidebarTabLink is clicked

		$('.dCollapse').hide(); 					// hide every dCollapse
		var dTab = $(this).attr('id'); 				// get the attribute of what is clicked and save to dTab variable
		var selector = '.dCollapse[id="' + dTab + '"]'; 
		console.log(selector);
		$(selector).show(); 				// show the item with dTab id
		$('.sidebarTabLink').removeClass('active'); 	// remove active from sidebarTabLink class objects
		$(this).addClass('active'); 				// Add active to this clicked object
	});
*/
	
	// Vertical Heatmap scrolling
	$('.vHeatmapPoint').live('click', function () {
 		var postID = $(this).attr('divpostid'); 
 		var postRef = 'div[level="'+ postID +'"]';
 		$('#dMain').scrollTo( $(postRef), 400 , {offset:-100});
    	$(postRef).removeClass('agree disagree comment offTopic clarify').addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', '#fff');$(this).dequeue();});
    	$('.vHeatmapPoint').removeClass('highlight');
    	$(this).addClass('highlight');
	 });
	 
	 $('.synthesisWrap').live('mouseover', function (event) {
	 	$(this).children('span').fadeIn('slow'); 
	 }); 

	 $('.synthesisWrap').live('mouseout', function (event) {
	 	$(this).children('span').fadeOut('slow'); 
	 }); 

} /* end function Dscourse  */


Dscourse.prototype.showHome=function() {

		var main = this;
		main.DiscResize();
		main.VerticalHeatmap();
		$('.page').hide();	
		$('#discussionFeedHome').html(' ');
		// Show the last three discussions for the user
		var i, o;
		if(main.data.allDiscussions) {
			for (i = 0; i < 5; i++){
	 			o = main.data.allDiscussions[i];
	 			// if there is o append to the set
				if(o){
				   $('#discussionFeedHome').append('<li><a class="discussionLink" discID="' + o.dID + '"> ' + o.dTitle + '  </a> under the course <b>' + main.listDiscussionCourses(o.dID) + '</b><br /> <em class="timeLog">Last edited: ' + main.PrettyDate(o.dChangeDate) + '</em> </li>');
				}
	 		 }
	 	} else {
				$('#discussionFeedHome').prepend('<li>You aren\'t part of any discussions yet. <a class="label label-info" id="createDhome">Create a Discussion </a></li>');
	 	}
	 	main.UserProfile(currentUserID);
		$('#homePage').show();
		$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
 }
		

/***********************************************************************************************/ 
/*                						DATABASE FUNCTIONS 									   */
/***********************************************************************************************/ 

Dscourse.prototype.GetData=function(action, load, loadID)
{
	var main = this;
	
	if(!action) {
		var action = 'getAll'; 
	}
	
	if(!load) {
		var load = 'noload'; 
	}
	
	// Ajax call to get data and put all data into json object
		$.ajax({													// Add user to the database with php.
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				action: action
			},
			  success: function(data) {								// If addNewUser.php was successfully run, the output is printed on this page as notification. 
			  		main.data = data;
			  		main.listCourses('all');
			  		main.listDiscussions();						// Refresh list to show all discussions.
			  		main.ListUsers();
			  		main.TypeAhead();	

		  			if(load == 'load'){
		  			// Get URL variables if any
						var page = main.getUrlVars()["page"];
						if(typeof page != undefined){
							$('.page').hide();	

							switch(page)
							{
							case 'users':
							  $('#usersPage').show();
							  dscourse.imgUpload = 'user';
							  break;
							case 'help':
							  $('#helpPage').show();
							  break;
							case 'courses':
							  $('#coursesPage').show();
							  dscourse.imgUpload = 'course';
							  $('#allCoursesView').removeClass('linkGrey');								  
							  break;
							case 'discussions':
							  $('#discussionsPage').show();
							  $('.headerTabs a').addClass('linkGrey');
							  $('#allDiscussionView').removeClass('linkGrey');
							  	$('#discussionForm').fadeOut();
							  	$('#discussions').delay(300).fadeIn();
							  break;
						    case 'discussion' : 	  
							  var dRefresh = main.getUrlVars()["idisc"];
							      dRefresh = parseFloat(dRefresh);
							      var thetype = typeof dRefresh; 
							main.SingleDiscussion(dRefresh);
							$('.page').hide();
							$('#discussionWrap').show();
							$('html, body').animate({scrollTop:0});	
							  break;
							case 'profile':
							  var userid = $(this).attr('userid');
							  dscourse.UserProfile(userid);	
							  dscourse.imgUpload = 'user';
							  $('#profilePage').show();
							  break;		  
							default:
							  main.showHome();
							}
						}
					} else if (load == 'refreshD') {						// Discussion Refresh
						main.SingleDiscussion(loadID);
						$('.page').hide();
						$('#discussionWrap').show();
						$('html, body').animate({scrollTop:0});
						
						//This highlights the new posts as new so people know which ones. The highlight effects remains for 60 seconds and then disappears. 
						var newDiscPosts = main.newPosts.split(",");
						var i, postRef;
						for(i = 0; i < newDiscPosts.length-1; i++){
							postRef = 'div[level="'+ newDiscPosts[i] +'"]';
						    $(postRef).removeClass('agree disagree comment offTopic clarify').addClass('highlight').delay(5000).queue(function () {$(this).removeClass('highlight');$(this).dequeue();})
						}	
					} else if (load == 'refreshCourses') {	
					  $('#coursesPage').show();
					  dscourse.imgUpload = 'course';
					  $('#courseForm').fadeOut();
					  $('#courses').delay(300).fadeIn();
					  top.ClearCourseForm(); // Fields are emptied to reuse			  
					  $('.headerTabs a').addClass('linkGrey');			  
					  $('#allCoursesView').removeClass('linkGrey');			  
				   } else if (load == 'refreshDiscussions') {	
					   main.listDiscussions();
					   $('#discussionsPage').show();
					   $('.headerTabs a').addClass('linkGrey');			  
					   $('#allDiscussionView').removeClass('linkGrey');
					   $('#discussionForm').fadeOut();
					   $('#discussions').delay(300).fadeIn();
					   $('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification 
					} else if (load == 'refreshUsers') {
					  $('#usersPage').show();
					  $("#addUserForm").fadeOut();
					  $("#userList").delay(200).fadeIn();
					  $('.headerTabs a').addClass('linkGrey');			  
					  $('#userListLink').removeClass('linkGrey');
					  dscourse.imgUpload = 'user';
					} else { 
						main.showHome();			
					}
			  	}, 
			  error: function() {									// If there was an error
				 		console.log('There was an error talking to data.php');
			  }
		});	
}

/***********************************************************************************************/ 
/*                						 USER FUNCTIONS 									   */
/***********************************************************************************************/


Dscourse.prototype.ListUsers=function()
{
	var main = this;
	
	main.nameList.length = 0; 
	$('#userData').html(" ");
	var i, o; 
	for(i = 0; i < main.data.allUsers.length; i++ ){	// If view is not specified Construct the table for each element
		o =  main.data.allUsers[i] ;  			
		
		var appendHTML = 	"<tr>"
			    	  		+ "<td> <img class='userThumbSmall' src='" + o.userPictureURL +"' /><a class='showProfile' userid='" + o.UserID + "'>" + o.firstName + "</a></td>" 
				            + "<td> " + o.lastName	+ "</td>" 
				            + "<td> " + o.username		+ "</td>" 
				            + "<td> " + o.sysRole	+ "</td>" 
				            + "<td> " + o.userStatus		+ "</td>" 
				            + "<td>"; 
				            if(currentUserStatus == 'Administrator'){
				            	appendHTML += " <button id='" + o.UserID		+ "' class='btn btn-info editUser'>Edit</button>";
				            }
			appendHTML 	 += "</td></tr>" ;

			$('#userData').append(appendHTML);
			    	  	nameListName = { value: o.UserID, label : o.firstName + " " + o.lastName, email : o.username}; 
			    	  	main.nameList.push(nameListName);
			}
}



Dscourse.prototype.UserProfile=function(id)
{
	var main = this; 
	var userInst = new Array();
	var userTA 	 = new Array();
	var userStudent = new Array();
	
	$('#profileCourses').html('');
	$('#myCoursesHome').html('');	
	var i, o; 
	for(i = 0; i < main.data.allUsers.length; i++ )	// If view is not specified Construct the table for each element
			{  
				o = main.data.allUsers[i];
				
				if(o.UserID === id) 
				{
					$('#profileName').html(o.firstName + " " + o.lastName + " ");
			  		$('#profileEmail').html("  " + o.username);
			  	  	$('#profileAbout1').html(o.userAbout);
			  	  	$('#profileFacebook').html(o.userFacebook);	
			  	  	$('#profileTwitter').html(o.userTwitter);
			  	  	$('#profilePhone').html(o.userPhone);	
			  	  	$('#profileWebsite').html('<a href="' + o.userWebsite + '">' + o.userWebsite + '</a>');
			  	  	//$('#userStatus').html(singleUser.status);
			  	  	$('#profilePageEdit').attr('profilePageID', o.UserID);
			  	  	$('#profilePicture').html("<img src=\"" + o.userPictureURL + "\" width=\"120\">");		
			  	  }						
	}
	
	var j, k, listInst;
	for(j = 0; j < main.data.allCourses.length; j++){
		k = main.data.allCourses[j];
		
		
		listInst = k.courseInstructors.split(",");					// Courses user is an instructor in
		var m;
		for (m = 0; m < listInst.length; m++){
			if(listInst[m] == id){
				$('#profileCourses').append(
					'<tr>'
					+ '	<td> <a class="courseLink" courseid="' + k.courseID +'">' + k.courseName + '</a></td>'
					+ '	<td>Instructor</td>'
					+ '</tr>'
				);

				$('#myCoursesHome').append(
					'<li>'
					+ '	 <a class="courseLink" courseid="' + k.courseID +'">' + k.courseName + '</a>'
					+ '	<br /><em class="timeLog">Instructor</em>'
					+ '</li>'
				);
				
				
			}
		}
		
		listTAs = k.courseTAs.split(",");					// Courses user is a TA in
		var n;
		for (n = 0; n < listTAs.length; n++){
			if(listTAs[n] == id){
				$('#profileCourses').append(
					'<tr>'
					+ '	<td> <a class="courseLink" courseid="' + k.courseID +'">' + k.courseName + '</a></td>'
					+ '	<td>Teaching Assistant</td>'
					+ '</tr>'
				);

				$('#myCoursesHome').append(
					'<li>'
					+ '	 <a class="courseLink" courseid="' + k.courseID +'">' + k.courseName + '</a>'
					+ '	<br /><em class="timeLog">Teaching Assistant</em>'
					+ '</li>'
				);

			}
		}


		listStudents = k.courseStudents.split(",");					// Courses user is a Student in
		var n;
		for (n = 0; n < listStudents.length; n++){
			if(listStudents[n] == id){
				$('#profileCourses').append(
					'<tr>'
					+ '	<td> <a class="courseLink" courseid="' + k.courseID +'">' + k.courseName + '</a></td>'
					+ '	<td>Student</td>'
					+ '</tr>'

				);
				
				$('#myCoursesHome').append(
					'<li>'
					+ '	 <a class="courseLink" courseid="' + k.courseID +'">' + k.courseName + '</a>'
					+ '	<br /><em class="timeLog">Student</em>'
					+ '</li>'
				);
				
			}
		}		
		
		
	}
				
}


/***********************************************************************************************/ 
/*                						COURSE FUNCTIONS 									   */
/***********************************************************************************************/

Dscourse.prototype.listCourses=function(view)
{	
		var main = this; 

		$('#tablebody').html(" ");					// Empty the table body
		var i, j, k, o, inst, fullName, tas, stu, stuNum; 
		
		var editable = 'no'; 
			if (main.data.allCourses){
			for(i = 0; i < main.data.allCourses.length; i++ )	// If view is not specified Construct the table for each element
			{    
				editable = 'no'; 
	
				o = main.data.allCourses[i];
		    	if (o.courseStatus == view) {
		    			inst = o.courseInstructors.split(",");
			    	  	fullName = "<strong>";
			    		for(j = 0; j < inst.length; j++){
			    			if (inst[j]){
				    			 fullName +=   main.getName(inst[j]) + "<br />";
				    			 if(inst[j] == currentUserID){ editable = 'yes';}
				    		}
			    		}
			    		
			    		fullName += "</strong>";
		    		
		    		
			    		tas = o.courseTAs.split(",");
			    	  	var TAName = "<em>";
			    		for(var k = 0; k < inst.length; k++){
			    			if (tas[i]){
				    			 TAName +=   main.getName(tas[i]) + "<br />";
				    			 if(tas[i] == currentUserID){ editable = 'yes';}

				    		}
			    		}
			    		TAName += "</em>";
			    		
			    
			    	stu = o.courseStudents.split(",");
			    	if (stu != ""){
				    	stuNum = stu.length;
			    	} else {
				    	stuNum = 0;
			    	}
			    	  			    		
			    		
		    	var appendHTML =  "<tr>"
		    	  		+ "<td> <a class='courseLink' courseid='" + o.courseID + "'> " + o.courseName			+ "</a></td>" 
			            + "<td> " + o.courseDescription	+ "</td>" 
			            + "<td> " + o.courseView		+ "</td>" 
			            + "<td><strong> " + fullName	+ "</strong><br/>" + TAName +" </td>" 
			            + "<td> " + stuNum + "</td>"
				        + "<td>"; 
				        
				        if(editable == 'yes' || currentUserStatus == 'Administrator'){
				        		appendHTML +=  "<button id='" + o.courseID		+ "' class='btn btn-info editCourse'>Edit</button>"; 
				        }
			       appendHTML +=  "</td></tr>"; 
		    			    			
		    	  	$('#tablebody').append(appendHTML);
	    	  
		    	  	//courseListCourse = { value: o.courseID, label : o.courseName}; 
			    	//  	main.courseList.push(courseListCourse);
			    	  

	    		} else if (view == 'all'){			// This is bad code, it repeats the entire top section. Need to find a better way. 
		    			editable = 'no'; 

		    			
		    			inst = o.courseInstructors.split(",");
			    	  	fullName = "<strong>";
			    		for(j = 0; j < inst.length; j++){
			    			if (inst[j]){
				    			 fullName +=   main.getName(inst[j]) + "<br />";
				    			 if(inst[j] == currentUserID){ editable = 'yes';}

				    		}
			    		}
			    		
			    		fullName += "</strong>";
		    		
		    		
			    		tas = o.courseTAs.split(",");
			    	  	var TAName = "<em>";
			    		for(var k = 0; k < inst.length; k++){
			    			if (tas[i]){
				    			 TAName +=   main.getName(tas[i]) + "<br />";
				    			 if(tas[i] == currentUserID){ editable = 'yes';}
				    		}
			    		}
			    		TAName += "</em>";
			    		
			    
			    	stu = o.courseStudents.split(",");
			    	if (stu != ""){
				    	stuNum = stu.length;
			    	} else {
				    	stuNum = 0;
			    	}
			    	  			    		
			    		
		    			    			
		    	var appendHTML2 =  "<tr>"
		    	  		+ "<td> <a class='courseLink' courseid='" + o.courseID + "'> " + o.courseName			+ "</a></td>" 
			            + "<td> " + o.courseDescription	+ "</td>" 
			            + "<td> " + o.courseView		+ "</td>" 
			            + "<td><strong> " + fullName	+ "</strong><br/>" + TAName +" </td>" 
			            + "<td> " + stuNum + "</td>"
				        + "<td>"; 
				        if(editable == 'yes' || currentUserStatus == 'Administrator'){
				        		appendHTML2 +=  "<button id='" + o.courseID		+ "' class='btn btn-info editCourse'>Edit</button>"; 
				        }
			       appendHTML2 +=  "</td></tr>"; 
		    			    			
		    	  	$('#tablebody').append(appendHTML2);
	    		}
	     }
	}
}

Dscourse.prototype.addCourse=function()
{			
			var main = this; 

			var courseInstructors 	= []; 
			var courseTAs 			= []; 
			var courseStudents 		= []; 
			var courseName 			= $('#courseName').val();				// Populates the fields needed for the database. 
			var courseDescription	= $('#courseDescription').val();
			var courseStartDate  	= $('#courseStartDate').val();
			var courseEndDate  		= $('#courseEndDate').val();
			var coursePicture  		= $('#courseImage').val();
			var courseURL   		= $('#courseURL').val();
			
			
			$('.roleB').each(function(index) {								// Fill in users to appropriate place. 
			   
			   if ($(this).hasClass('active') == true){
			   		
				    var roleName = $(this).text(); 
				    if (roleName == 'Instructor'){
				    		var data = $(this).attr('userid');
					    	courseInstructors.push(data); 
					    } 
					    else if (roleName == 'TA') 
					    {
					    	var data = $(this).attr('userid');
				    		courseTAs.push(data);
				    	} 
				    	else if (roleName == 'Student')
				    	{
				    	var data = $(this).attr('userid');
					    	courseStudents.push(data);
					    } else {
						    alert('Problem reading People.');
					    }
					}
				
			});
		
			courseInstructors = courseInstructors.toString(); 
			courseTAs = courseTAs.toString(); 
			courseStudents = courseStudents.toString()	; 
							
		course = {
				'courseName': courseName,
				'courseDescription': courseDescription,
				'courseInstructors': courseInstructors,
				'courseTAs': courseTAs,
				'courseStudents': courseStudents,
				'courseStartDate' : courseStartDate,
				'courseEndDate' :  courseEndDate,
				'coursePicture' :  coursePicture,
				'courseURL' :  courseURL
			};
			
	$.ajax({											// Ajax talking to the data.php file												
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				course: course,							//  course data is sent
				action: 'addCourse'							
			},
			  success: function(data) {						// If connection is successful . 
			    	  main.listCourses('all');					// Refresh list to show all courses.
			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to data.php failed for adding course.");  
			  }
		});		
		
	main.AddLog('course','','addCourse','','User created a course');				
			
}

Dscourse.prototype.editCourse=function(id)

{
			var main = this; 
			var i, o; 
			
			if (main.data.allCourses){
				for (i = 0; i < main.data.allCourses.length; i++ )
				{	
				o = main.data.allCourses[i];	
						
				if (id == o.courseID)					// Search for the object to edit
				{
					$('#courseName').val(o.courseName);				// Populates the fields needed for the form. 
					$('#courseDescription').val(o.courseDescription);
					$('#courseStartDate').val(o.courseStartDate);
					$('#courseEndDate').val(o.courseEndDate);
					$('#cimgPath').html('<img src="' + o.courseImage + '" alt="course image"/>');
					$('#courseImage').val(o.courseImage);
					$('#courseURL').val(o.courseURL);	
					$('#courseIDInput').html("<input type=\"hidden\" name=\"courseID\" id=\"courseID\" value=" + o.courseID + " />");
					
					
					var ci = o.courseInstructors.split(",");
					var ct = o.courseTAs.split(",");
					var cs = o.courseStudents.split(",");
					
					
					if (ci != ""){
						for (var m = 0; m < ci.length; m++){
							var userNameValue 	= main.getName(ci[m]);
							var userEmailValue 	= main.getEmail(ci[m]);
							$('#addPeopleBody').append('<tr><td>' + userNameValue + ' </td><td>' + userEmailValue  + ' </td><td><div class="btn-group" data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB active" userid="'+ ci[m] + '">Instructor</button><button class="btn roleB" userid="'+ ci[m] + '">TA</button><button class="btn roleB" userid="'+ ci[m] + '">Student</button></div></td><td><button class="btn removePeople">Remove</button>	</td></tr>'); // Build the row of users. 
							}
					}

					if (ct != ""){
						for (var n = 0;  n < ct.length; n++){
							var userNameValue 	= main.getName(ct[n]);
							var userEmailValue 	= main.getEmail(ct[n]);
							$('#addPeopleBody').append('<tr><td>' + userNameValue + ' </td><td>' + userEmailValue  + ' </td><td><div class="btn-group" data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" userid="'+ ct[n] + '">Instructor</button><button class="btn roleB active" userid="'+ ct[n] + '">TA</button><button class="btn roleB" userid="'+ ct[n] + '">Student</button></div></td><td><button class="btn removePeople">Remove</button>	</td></tr>'); // Build the row of users. 
							}
					}	
									
					if (cs != ""){
						for (var o = 0; o < cs.length; o++){
							var userNameValue 	= main.getName(cs[o]);
							var userEmailValue 	= main.getEmail(cs[o]);
							$('#addPeopleBody').append('<tr><td>' + userNameValue + ' </td><td>' + userEmailValue  + ' </td><td><div class="btn-group" data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" userid="'+ cs[o] + '">Instructor</button><button class="btn roleB" userid="'+ cs[o] + '">TA</button><button class="btn roleB active" userid="'+ cs[o] + '">Student</button></div></td><td><button class="btn removePeople">Remove</button>	</td></tr>'); // Build the row of users. 					
							}
					}
					
						main.AddLog('course','','editCourse',o.courseID,'User created a course');	
					break;
				}				
			}	
			}	

}


Dscourse.prototype.updateCourse=function()
{
	var main = this;

	var courseID = $('#courseID').val();
	var i, o;
	if (main.data.allCourses){
		for (i = 0; i < main.data.allCourses.length; i++ )
		{	
		o = main.data.allCourses[i];			
		
		if (courseID == o.courseID)	{
				var courseInstructors 	= []; 
				var courseTAs 			= []; 
				var courseStudents 		= [];
				o.courseName 			= $('#courseName').val();				// Populates the fields needed for the database. 
				o.courseStatus		= 'active';
				o.courseStartDate  	= $('#courseStartDate').val();
				o.courseEndDate  		= $('#courseEndDate').val();
				o.coursePicture  		= $('#courseImage').val();
				o.courseURL   		= $('#courseURL').val();
				o.courseDescription  	= $('#courseDescription').val();
			
				$('.roleB').each(function(index) {								// Fill in users to appropriate place. 
				   if ($(this).hasClass('active') == true){
				   		
					    var roleName = $(this).text(); 
					    if (roleName == 'Instructor'){
					    		var data = $(this).attr('userid');
						    	courseInstructors.push(data); 
						    } 
						    else if (roleName == 'TA') 
						    {
						    	var data = $(this).attr('userid');
					    		courseTAs.push(data);
					    	} 
					    	else if (roleName == 'Student')
					    	{
					    	var data = $(this).attr('userid');
						    	courseStudents.push(data);
						    } else {
							    alert('Problem reading People.');
						    }
						    console.log(data);
						}
				});
			
				courseInstructors = courseInstructors.toString(); 
				courseTAs = courseTAs.toString(); 
				courseStudents = courseStudents.toString()	; 
			
				o.courseInstructors	= courseInstructors;
				o.courseTAs			= courseTAs;
				o.courseStudents	= courseStudents;
			
					$.ajax({											// Ajax talking to the data.php file												
						type: "POST",
						url: "scripts/php/data.php",
						data: {
							course: o,							// All course data is sent
							action: 'updateCourse'							
						},
						  success: function(data) {						// If connection is successful . 
						    	  main.GetData('getAll','refreshCourses');
						    }, 
						  error: function() {					// If connection is not successful.  
								console.log("Dscourse Log: the connection to data.php failed for Saving courses.");  
						  }
					});	 // end ajax
			
			}	// end if
		} // end for loop
	}	// end if

} // end function



Dscourse.prototype.getCourse=function(cid)									// Gets individual course information
{    		
		var main = this;
		$('#iCoursePicture').html('');
			
			var i, j, k; 
			for (i = 0; i < main.data.allCourses.length; i++ )
			{	
				var o = main.data.allCourses[i];	
				if (o.courseID == cid){
				    $('#iCourseName').html(o.courseName);
			  	  	$('#iCourseDescription').html(o.courseDescription);
			  	  	$('#iCourseStartDate').html(o.courseStartDate);
			  	  	$('#iCourseEndDate').html(o.courseEndDate);	
			  	  	$('#iCourseURL').html('<a href="' + o.courseURL + '">' + o.courseURL + '</a>');
			  	  	if (o.courseImage){
			  	  		$('#iCoursePicture').html("<img src=\"" + o.courseImage + "\" width=\"240\">");									
			  	  		}
			  	  	var shortName = main.truncateText(o.courseName, 30);	
			  	  	$('#navLevel2').text(shortName).attr('cLinkID', cid).css('display', 'block'); 
			  	  	$('#navLevel3').hide(); 
			  	  				  	  	
			  	  	// Populate the notes
				 	/* 
				 	$('#courseNoteBody').html(" "); 
				 	
				 	var l;
				 	for (l = 0; l < main.data.allNotes.length; l++)
				 	{		
				 		var m = main.data.allNotes[l];
				 		console.log('Hi');
				 		if(m.noteType == 'course' && m.noteSource == o.courseID){
				 			console.log('hi');
				 			var authorIDfull = main.getName(m.noteAuthor); 
				 			var authorThumb = main.getAuthorThumb(m.noteAuthor, 'small');			// get thumbnail html
				 			var prettyTime = main.PrettyDate(m.noteTime);

				 		
					 		$('#courseNoteBody').append(
						    	  		  "<tr>"
						    	  		+ "<td>" + authorThumb + " <a class='showProfile' userid='" + m.noteAuthor + "'>" + authorIDfull + "</a></td>" 
							            + "<td>  " + m.noteText +" <i> " + prettyTime + "</i></td>" 
							            + "</tr>" 
						    	  	);
				 		}
				 		
					}			  	  	
			  	  	
			  	  	
			  	  	$('#noteSource').val(o.courseID);
			  	  		*/
			  	  	
			  	  	var singleTAs = []; 
			  	  	var TAstring = o.courseTAs;
			  	  	singleTAs = TAstring.split(",");
			  	  	var TANameString = ""; 
			  	  	for(j = 0; j < singleTAs.length; j++){
				  	  	TANameString += main.getName(singleTAs[j]); 
				  	  	if (j != singleTAs.length-1){
					  	  	TANameString += ", "
				  	  	}  	
			  	  	}
			  	  	$('#iCourseTAs').html(TANameString);


			  	  	var singleInstructors = [];
			  	  	var instString = o.courseInstructors;
			  	  	singleInstructors = instString.split(",");
			  	  	var instNameString = ""; 
			  	  	for(k = 0; k < singleInstructors.length; k++){
				  	  	instNameString += main.getName(singleInstructors[k]); 
				  	  	if (k != singleInstructors.length-1){
					  	  	instNameString += ", "
				  	  	} 			  	  	
				  	}
				  	$('#iCourseInstructors').html(instNameString);

				  }
			
		}	
		
		main.AddLog('course',cid,'view',0,'');
}



Dscourse.prototype.addNote=function()									// Add a note
{
		var main = this; 
		
		var note = new Object; 

		var noteText	= $('#inputNote').val();
		var noteAuthor	= $('#noteAuthor').val();
		var noteType	= $('#noteType').val();
		var noteSource	= $('#noteSource').val();
		
		
		note = {
				'noteText': noteText,
				'noteAuthor': noteAuthor,
				'noteType': noteType,
				'noteSource': noteSource
			};


}



/***********************************************************************************************/ 
/*                					DISCUSSION FUNCTIONS 									   */
/***********************************************************************************************/

 Dscourse.prototype.listDiscussions=function()	 			  // Show a table of all discussions
 {
	 var main = this;
	 
	 if(main.data.allDiscussions){
		 	$('#tableBodyDiscussions').html(" "); 
		 	
		 	var i;
		 	
		 	for (i = 0; i < main.data.allDiscussions.length; i++)
		 	{		
		 		var o = main.data.allDiscussions[i];
		 		// If current user is instructor or TA
		 		var userRole = main.UserCourseRole(o.dID, currentUserID); 
		 		var buttonShow;
		 		if(userRole == 'instructor' || userRole == 'TA' || currentUserStatus == 'Administrator'){
		 			buttonShow = "<button id='" + o.dID + "' class='btn btn-info editDiscussion'>Edit</button>"
		 		} else {
			 		buttonShow = ""; 
		 		}
		 		
		 		var rStartDate = main.rDate(o.dStartDate); var rEndDate = main.rDate(o.dEndDate); //Dates in readable format. 
		 		
				$('#tableBodyDiscussions').append(
				    	  		  "<tr>"
				    	  		+ "<td> <a class='discussionLink' discID='" + o.dID + "'> " + o.dTitle			+ " </a></td>" 
					            + "<td>  " + main.listDiscussionCourses(o.dID) +"</td>" 
					            + "<td> " + rStartDate + "</td>" 
					            + "<td> " + rEndDate + "</td>" 
						        + "<td> " + buttonShow + "</td>"
					            + "</tr>" 
				    	  	);
				
				
			}
			
		} else {
			$('#tableBodyDiscussions').html('<tr><td colspan=5 ><div class="alert alert-info">You are not part of any discussions yet. Create a discussion by clicking on the <b>"Start New Discussion"</b> tab at the top.</div></td></tr>');
		}
	
 }

 Dscourse.prototype.listCourseDiscussions=function(cid, action)	 			  // Listing Discussions of a course with the given cid. 
 {
	 var main = this;
	 
	
	if(!main.data.allDiscussions){
		 return;
	 }	
	  
	var o, m, i, j, k; 
	var n = new Array; 
	for (i = 0; i < main.data.allCourses.length; i++)
	{	
		o = main.data.allCourses[i];		
		if (o.courseID == cid){
				var ds = o.courseDiscussions;
				n=ds.split(",");
		}
	}
	
 	$('#courseDiscussionsBody').html(" ");

 	if(n.length > 1){
		for (j = 0; j < main.data.allDiscussions.length; j++)
		{
			m = main.data.allDiscussions[j]; 		
			for(k = 1;  k < n.length; k++)
			{ 
				if (m.dID == n[k]) {
				var totalPosts = main.TotalPostNumber(m.dID); 
				
					var status = main.DiscDateStatus(m.dID);
					switch(status)
					{
						case 'all':
							status = '<span style="color:#74AA81">Open Posting</span>';
						break;	
						case 'student':
							status = '<span style="color:#F3BC6A">Individual Posting</span>';
						break;
						case 'closed':
							status = '<span style="color:#BD838F">Closed</span>';
						break;
					}
						$('#courseDiscussionsBody').append(
				    	  		  "<tr>"
				    	  		+ "<td> <a class='discussionLink' discID='" + m.dID + "'> " + m.dTitle			+ " </a></td>" 
					            + "<td>" + status + " </td>" 					// Needs to chech if discussin is open to individual, closed, open to all etc. Gets this info from the dates.  
					            + "<td>  "  + totalPosts + "  </td>" 
					            + "</tr>" 
				    	  	);
				 
				 }
			}	
		}	
	} else {
		$('#courseDiscussionsBody').append(
	  		  "<tr>"
	  		+ "<td colspan=3><div class='alert alert-info'> There are no discussions under this course yet. Instructors and Teaching Assistant can start discussions with this course.</div></td>" 
            + "</tr>" 
	  	);	
	}
 }

 Dscourse.prototype.listCourseStudents=function(courseid)	 			  // Listing Discussions of a course with the given cid. 
 {
	 var main = this;
	 $('#courseStudentsBody').html('');

	var i, o, j, k, l; 
	
	var n = new Array; 
	for (i = 0; i < main.data.allCourses.length; i++)					// Find the course with this ID
	{	
		o = main.data.allCourses[i];		
		if (o.courseID == courseid){
				var ds = o.courseStudents;								// Extract student IDs to an array
				n=ds.split(",");
		}
	}
	
	if(n.length > 1) {
		for(j = 0; j < n.length; j++){										// Loop through students array
			for(k = 0; k < main.data.allUsers.length; k++){					// Loop through users
				l = main.data.allUsers[k];
				if(l.UserID == n[j]){										// If this student is one of the users
					$('#courseStudentsBody').append(						// Add student information
							  '<tr>'
							+ '	<td><img class="userThumbSmall" src="' + l.userPictureURL +'" /><a class="showProfile" userid="' + l.UserID + '">'+ l.firstName + ' ' + l.lastName +'</a> </td>'
					        + ' <td>'+  l.username +'</td>'
							+ '</tr>'
					);
				}
			}
		}
	} else {
		$('#courseStudentsBody').append(						// Add student information
							  '<tr>'
							+ '	<td colspan=2 ><div class="alert alert-info">There are no students in the class. Instructors or Teaching Assistants can add students by going back to Courses List and clicking on EDIT button for this course.</div></td>'
							+ '</tr>'
					);
		
	}	
		

	 
  }

 Dscourse.prototype.TotalPostNumber=function(discussionID)	 			  // Get total post numbers
 {
	var main = this;
	
	if(!main.data.allDiscussions){
		 return;
	 }	
	 
	var i, o, j, k, p;
	var c = 0;
	for(i = 0; i < main.data.allDiscussions.length; i++){					// Loop through courses
		o = main.data.allDiscussions[i];
		if(o.dID == discussionID){											// We found our course
				 var discPosts = o.dPosts.split(",");
 
				 for(k = 0; k < discPosts.length; k++){							// Take one post at a time
						 p = discPosts[k];
						 for (j = 0; j < main.data.allPosts.length; j++){			// Go through all the posts
							 d = main.data.allPosts[j];		
								 
							 if(d.postID == p){										// Find the post we want to get the details of 
										c++;
								 }	 
						 }
					 }
		}
	} 
	
	return c;
		
}

Dscourse.prototype.addDiscussion=function()	 			  // Add discussion
 {
	var main = this;		
	
	 
		var dCourses = [];
							
		$('.dCourseList').each(function(index) {
			var courseID = $(this).attr('id');
			dCourses.push(courseID);
		});
				
		var dCoursesString = dCourses.toString(); 
		
			dCourses.length = 0;											// Empty the array for reuse
			
			var dTitle 		= $('#discussionQuestion').val();				// Populates the fields needed for the database. 
			var dPrompt		= $('#discussionPrompt').val();
			var dStartDate  = $('#discussionStartDate').val() + ' ' + $('#sDateTime option:selected').val() + ':00' + ':00';
			var dOpenDate  	= $('#discussionOpenDate').val() + ' ' + $('#oDateTime option:selected').val() + ':00' + ':00';
			var dEndDate  	= $('#discussionEndDate').val() + ' ' + $('#eDateTime option:selected').val() + ':00' + ':00';
			
		
							
		discussion = {
				'dTitle': dTitle,
				'dPrompt': dPrompt,
				'dStartDate': dStartDate,
				'dOpenDate': dOpenDate,				
				'dEndDate': dEndDate,
				'dCourses': dCoursesString
			};
			
			main.data.allDiscussions.push(discussion);
			main.saveDiscussions();
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification

}

Dscourse.prototype.listDiscussionCourses=function(dID)	 			  // List the courses that discussion belongs to based on discussion id. 
 {
	var main = this;
	
			for (var i = 0; i < main.data.allCourses.length; i++){
			
					var courseNames = new Array;
					var dList = new Array();
					var o = main.data.allCourses[i];
					 		
					var ds    = o.courseDiscussions;
					dList     = ds.split(",");
			
					for (var c = 0; c < dList.length; c++){
						if (dList[c] == dID){
								courseNames.push(o.courseName);
								var courseNameString = courseNames.join(",");
								return ' <a class="courseLink" courseid="' + o.courseID + '">' + courseNameString + '</a>';
						}
					}
			}
}

 Dscourse.prototype.editDiscussion=function(id)	 			  // Edit view for updating 
 {
	var main = this;

 		var i, j, c;
	 	for (i = 0; i < main.data.allDiscussions.length; i++){
		 	var o = main.data.allDiscussions[i];

		 	if(o.dID == id){
				$('#discussionQuestion').val(o.dTitle);				// Populates the fields needed for the database. 
				$('#discussionPrompt').val(o.dPrompt);
				var sDateTime = o.dStartDate.split(" "); 
				var oDateTime = o.dOpenDate.split(" "); 
				var eDateTime = o.dEndDate.split(" "); 
				var sHour = sDateTime[1].split(":");
				var oHour = oDateTime[1].split(":");
				var eHour = eDateTime[1].split(":");
				
				$('#discussionStartDate').val(sDateTime[0]);
				$('#discussionOpenDate').val(oDateTime[0]);
				$('#discussionEndDate').val(eDateTime[0]);
				$('#sDateTime [value="' + sHour[0] +'"]').attr("selected", "selected");
				$('#oDateTime [value="' + oHour[0] +'"]').attr("selected", "selected");
				$('#eDateTime [value="' + eHour[0] +'"]').attr("selected", "selected");

			 	$('#discIdHidden').val(o.dID); 
			 	
			for (j = 0; j < main.data.allCourses.length; j++){
						var courseNames = new Array;
						var dList = new Array();
						
						var m = main.data.allCourses[j];
						var ds    = m.courseDiscussions;
						dList     = ds.split(",");
				
						for (c = 0; c < dList.length; c++){
							if (dList[c] == id){
									$('#addCoursesBody').append('<tr id="' + m.courseID + '" class="dCourseList"><td>' + m.courseName + ' </td><td><button class="btn removeCourses" >Remove</button>	</td></tr>');

							}
						}
				}
		 	}
	 	}
 }
 

 Dscourse.prototype.updateDiscussion=function(id)	 			 	 //Change updates to the discussion 
 {
	var main = this;
 
 		var i, j, c, o;
	 	for (i = 0; i < main.data.allDiscussions.length; i++){
		 	o = main.data.allDiscussions[i];
		 	if(o.dID == id){
			
				o.dTitle 		= $('#discussionQuestion').val();				// Populates the fields needed for the database. 
				o.dPrompt		= $('#discussionPrompt').val();

				o.dStartDate  = $('#discussionStartDate').val() + ' ' + $('#sDateTime option:selected').val() + ':00' + ':00';
				o.dOpenDate  	= $('#discussionOpenDate').val() + ' ' + $('#oDateTime option:selected').val() + ':00' + ':00';
				o.dEndDate  	= $('#discussionEndDate').val() + ' ' + $('#eDateTime option:selected').val() + ':00' + ':00';
								
				var dCourses = [];
							
				$('.dCourseList').each(function(index) {
					var courseID = $(this).attr('id');
					dCourses.push(courseID);
				});
						
				o.dCoursesString = dCourses.toString(); 
				dCourses.length = 0;
				main.saveDiscussions();
			}	
		}	 
	 
 }
 
 

Dscourse.prototype.saveDiscussions=function()	 	// Save Discussion
 {
	var main = this;

	$.ajax({														// Ajax talking to the saveDiscussions.php file												
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				discussions: main.data.allDiscussions,				// All discussion data is sent
				action: 'saveDiscussions'							
			},
			  success: function(data) {								// If connection is successful . 
			    	  main.GetData('getAll', 'refreshDiscussions');								// Get up to date info from server the discussion list
			    	  main.AddLog('data','','saveDiscussions','','Success: everything saved.')
			    }, 
			  error: function() {
			  		main.AddLog('data','','saveDiscussions','','Error: the connection to data.php failed.')					  
					console.log("dscourse Log: the connection to data.php failed.");  
			  }
		});	
	
}


 Dscourse.prototype.SingleDiscussion=function(discID)	 			  // View for the Individual discussions. 
 {
	    var main = this;
	 	$('.levelWrapper[level="0"]').html('');

 		var i, o, userRole, dStatus;
	 	for (i = 0; i < main.data.allDiscussions.length; i++){
	 		o = main.data.allDiscussions[i];
	 		if(o.dID == discID){
	 			$('#dTitleView').html(o.dTitle);
	 			$('#dPromptView').html('<b> Prompt: </b>' + o.dPrompt);
	 			$('#dIDhidden').val(o.dID);
	 			var dCourse = main.listDiscussionCourses(discID); 
	 			$('#dCourse').html('<b> Course: </b>' + dCourse);
	 			$('#dSDateView').html('<b> Start Date: </b>' + o.dStartDate);
	 			$('#dODateView').html('<b> Open to Class: </b>' + o.dOpenDate);
	 			$('#dCDateView').html('<b> End Date: </b>' + o.dEndDate);
	 			main.CurrentDiscussion = o.dID;	
	 			
	 			var shortName = main.truncateText(o.dTitle, 50);	
			  	$('#navLevel3').text(shortName).attr('dLinkID', o.dID).css({ 'display' : 'block'}); 
			  	  	
	 			
	 			// Get Discussion Status, can be one of three: all, student, closed.
	 			dStatus = main.DiscDateStatus(o.dID);				 
	 			
	 			// Note for the page
	 			$('#discStatus').removeClass('label label-error label-warning label-success').html(''); 
	 			switch(dStatus)									
					{
					case 'all':
						$('#discStatus').addClass('label label-success').html('Open to group participation');
						break;
					case 'student':
					  $('#discStatus').addClass('label label-warning').html('Open to individual participation');
					  break;
					case 'closed':
					  $('#discStatus').addClass('label label-important').html('This discussion is closed.');
					  break;
		  
					}
	 			
	 			// What is the role of the current user for this discussion?
	 			userRole = main.UserCourseRole(o.dID, currentUserID);	 			
	 			
	 			// Draw up posts and timeline
	 			if(o.dPosts){
	 				main.ListDiscussionPosts(o.dPosts, dStatus, userRole, o.dID);		 					 			
	 			} else {
		 			$('.levelWrapper').append( 
		 				"<div id='nodisc' class='alert alert-info'> There are not posts in this discussion yet. Be the first one and add your voice by clicking on the <b>'Say'</b> button at the top (next to the discussion title)</div>"
		 			); 
	 			}
	 			
	 			
	 			if(dStatus == 'all' || dStatus == 'closed') {
	 				main.DrawTimeline(o.dPosts);
	 			} else {
		 			$('#amount').val('Timeline is disabled.');
	 			}
	 			

	 			
	 		}
	 	
	 	}	
	 	setInterval(function(){main.CheckNewPosts(discID, userRole, dStatus)},5000);
	 	main.AddLog('discussion',discID,'view',0,'');
	 		
		
	
}

Dscourse.prototype.DrawTimeline=function(posts)	 			  // Draw the timeline. 
 {
	    var main = this;

	    	// Create the Slider
		    $( "#slider-range" ).slider({					// Create the slider
				range: "min",
				step: 360000,
				value: main.timelineMax,
				min: main.timelineMin,
				max: main.timelineMax,
				slide: function( event, ui ) {
					var date = main.FormattedDate(ui.value);
					$( "#amount" ).val(date);
					$('.threadText').each(function(index) {
						var threadID = $(this).attr('time'); 
						if(threadID > ui.value){
							$(this).hide();
						} else {
							$(this).show();
						}
					});
				}
			});
			
					
					
			// Show the value on the top div for reference. 
			var initialDate = main.FormattedDate(main.timelineMax);
			$( "#amount" ).val(initialDate);	
			
			
			// Draw the dots. 
			 var discPosts = posts.split(",");
			 console.log('timelineMin: ' + main.timelineMin + 'timelineMax : '+ main.timelineMax);
			 	 	 
			 var i, j, p, d;
			 for(i = 0; i < discPosts.length; i++){							// Take one post at a time
				 p = discPosts[i];
				 for (j = 0; j < main.data.allPosts.length; j++){			// Go through all the posts
					 d = main.data.allPosts[j];		
					 	 
					 if(d.postID == p){										// Find the post we want to get the details of 
		
						 	 //add dot on the timeline for this post
						 	  var n = d.postTime; 
						 	  n = n.replace(/-/g, "/");
						 	  //n = main.ParseDate(n, 'yyyy/mm/dd');

						 	  var time = Date.parse(n);
						 	 
							var timeRange = main.timelineMax-main.timelineMin;
							var dotDistance = ((time-main.timelineMin)*100)/timeRange;
							var singleDotDiv = '<div class="singleDot" style="left: ' + dotDistance + '%; "></div>'; 
							$('#dots').append(singleDotDiv); 
					}
				  }
			  }
}	


Dscourse.prototype.DiscDateStatus=function(dID)	 			  			// Get the status of the discussion depending on the date.
 {
	    var main = this;
	    var dStatus;
	    	    
	    // Get course dates: 
	    var i, o;
	 	for (i = 0; i < main.data.allDiscussions.length; i++)
	 	{		
	 		o = main.data.allDiscussions[i];
	 		if(o.dID === dID ){

	 			// Compare dates of the discussion to todays date.  
			    var beginDate = new Date(o.dStartDate);	    
			    var openDate = new Date(o.dOpenDate);	    
			    var endDate = new Date(o.dEndDate);	    
			    
			    var currentDate = new Date(); 
			    
			    if(currentDate >= beginDate && currentDate <= endDate) {// IF today's date bigger than start date and smaller than end date? 
				    	if(currentDate <= openDate) {    				// If today's date smaller than Open Date
			    		dStatus = 'student'; 							// The status is open to individual contribution
			    	} else { 
			    		dStatus = 'all'; 								// The status is open to everyone
			    	}
			    } else {   		
			     		dStatus = 'closed'; 							// The status is closed.
			     }
			     
			     
			     return dStatus; 
	     
	 		}
	 	}
   
 }

Dscourse.prototype.UserCourseRole=function(dID, userID)	 			  // Get User's role in a course for a specific discussion. 
 {
	    var main = this;
	    var userRole = 'unrelated'; 

		var j, k,l, listInst, listTAs, listStudents, discussions;
			for(j = 0; j < main.data.allCourses.length; j++){		// Loop through courses
				k = main.data.allCourses[j];
				
				discussions = k.courseDiscussions.split(",");		// For each course take the discussions it has
				
				for(l = 0; l < discussions.length; l++){			// Loop through course discussions
				
					if(discussions[l] == dID){						// Check if this discussion is part of that course 
				
						listInst = k.courseInstructors.split(",");		// Check if the user is among the instructors
						var m;
						for (m = 0; m < listInst.length; m++){
							if(listInst[m] == userID){
								userRole = 'instructor'; 
							}
						}
						
						listTAs = k.courseTAs.split(",");				// Check if the user is among the TAs
						var n;
						for (n = 0; n < listTAs.length; n++){
							if(listTAs[n] == userID){
								userRole = 'TA'; 
							}
						}
				
				
						listStudents = k.courseStudents.split(",");		// Check if the user is among the Students
						var p;
						for (p = 0; p < listStudents.length; p++){
							if(listStudents[p] == userID){
								userRole = 'student'; 
							}
						}
					}			
				}
				
			}
			
	   return userRole; 		  
	    
 }		


/***********************************************************************************************/ 
/*                					     POST FUNCTIONS 									   */
/***********************************************************************************************/

Dscourse.prototype.AddPost=function(){
	
		 var main = this;
		 var currentDisc = $('#dIDhidden').val();
		 
	// Get post values from the form.
		// postID -- postFromId
		var postFromId = $('#postIDhidden').val();	
		
		// author ID -- postAuthorId -- this is the session user
		var postAuthorId = $('#userIDhidden').val();			
		var postMessage = $('#text').val();	

		// type -- postType
		var postType = 'comment';	
		var formVal = $('#postTypeID > .active').attr('id');
		
		if(formVal !== undefined){
			postType = formVal;
		} 
	
		// locationIDhidden -- postSelection 
		var postSelection = $('#locationIDhidden').val();
		if(postSelection = '0,0' ){				// fix for firefox and fool proofing in case nothing is actually selected. 
			postSelection = ''; 
		}
	
	// Get drawing value
		var postMedia; 
		postMedia  = main.currentDrawing; 
			
	// Create post object and append it to allPosts
	
			post = {
				'postFromId': postFromId,
				'postAuthorId': postAuthorId,
				'postMessage': postMessage,
				'postType': postType,
				'postSelection': postSelection,
				'postMedia' : postMedia, 
				'postMediaType' :  main.postMediaType
			};
		
		
	// run Ajax to save the post object
	
	$.ajax({																						
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				post: post,							
				action: 'addPost',
				currentDiscussion: currentDisc							
			},
			  success: function(data) {						// If connection is successful . 
			    	  post.postID = data; 
			    	  post.postTime = main.GetCurrentDate();   
			    	  
			    	  main.data.allPosts.push(post); 
			    	  var i;
					 	for (i = 0; i < main.data.allDiscussions.length; i++)
					 	{		
					 		var o = main.data.allDiscussions[i];
					 		if(o.dID === currentDisc ){
					
							 	if(o.dPosts){
								 	o.dPosts += ",";
							 	}
							 	o.dPosts += data;
							 }
						}
			 	
			    	  $('.levelWrapper[level="0"]').html('');
			    	  main.SingleDiscussion(currentDisc);
			    	  main.DiscResize();
			    	  main.VerticalHeatmap();
			    	  var divHighlight = 'div[level="'+ data +'"]';
			    	  $(divHighlight).removeClass('agree disagree comment offTopic clarify').addClass('highlight animated flash'); 
			    	  $.scrollTo( $(divHighlight), 400 , {offset:-100});
			    	  main.AddLog('discussion',currentDisc,'addPost',data,'')
			    }, 
			  error: function() {					// If connection is not successful.  
					main.AddLog('discussion',currentDisc,'addPost','','Error: Dscourse Log: the connection to data.php failed. ')
					console.log("Dscourse Log: the connection to data.php failed.");  
			  }
		});	
	
	
	
}


Dscourse.prototype.AddSynthesis=function(){							// Revise for synthesis posts
	
		 var main = this;

		 main.sPostID = '';
		 main.sPostContent = ''; 
		 
		 var currentDisc = $('#dIDhidden').val();
		 
	// Get post values from the synthesis form.
		// postID -- postFromId
		var postFromId = $('#sPostIDhidden').val();					// Done
		
		// author ID -- postAuthorId -- this is the session user
		var postAuthorId = $('#userIDhidden').val();				// Done
		var postMessage = $('#synthesisText').val();				// Done

		// type -- postType
		var postType = 'synthesis';	
	
		// locationIDhidden -- postSelection 
		var postSelection = ' ' ;									// Not done but works.

		var postMedia = ''; 										// Synthesis doesn't have media yet. 

		var postContext = ''; 
		
		$('#synthesisPostWrapper > .synthesisPosts').each(function() { 
			if(postContext.length  > 0){
				postContext += ',';
			}
			var thisPostID = $(this).attr('sPostID'); 
			postContext += thisPostID; 

		}); 
		console.log('post context ' + postContext);

		
			
	// Create post object and append it to allPosts
	
			post = {
				'postFromId': postFromId,
				'postAuthorId': postAuthorId,
				'postMessage': postMessage,
				'postType': postType,
				'postSelection': postSelection,
				'postMedia' : postMedia, 
				'postContext' : postContext
			};
		
		
	// run Ajax to save the post object
	
	$.ajax({																						
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				post: post,							
				action: 'addPost',
				currentDiscussion: currentDisc							
			},
			  success: function(data) {						// If connection is successful . 
			    	  	 	$('#addSynthesis').slideUp('fast');   // Hide the form
			    	  	 	
			    	  	 	$('.levelWrapper[level="0"]').html(''); // redraw the discussion page at synthesis
			    	  	 	main.SingleDiscussion(currentDisc);
			    	  	 	main.DiscResize();
			    	  	 	main.VerticalHeatmap();
			   
			    /********** APPEND NEW SYNTHESIS POSTS ***********/
    					 authorID = main.getName(postAuthorId, 'first');
						 $('#synthesisList').prepend(
						  '<div class="synthesisPost well" sPostID="' + data + '">' 
						+  '<span class="postAuthorView" rel="tooltip" > ' + authorID + '</span>'
						+ '		<p>' + postMessage + '</p>' 
						+ '	</div>' 
						); 
						main.ListSynthesisPosts(postContext, data); 

			   
			    	  
			    	  
			    }, 
			  error: function() {					// If connection is not successful.  
					
					console.log("Dscourse Log: the connection to data.php failed. Did not save synthesis");  
			  }
		});	
	
	
	
}


 Dscourse.prototype.ListDiscussionPosts=function(posts, dStatus, userRole, discID)	 			  // View for the Individual discussions. 
 {
	 var main = this;
	 
	 main.uParticipant = [];
	 
	 
	 // Clear all dots
	 $('.singleDot').remove();
	 main.timelineMin = 0; main.timelineMax = 0; 
	 $('#participantList').html('    <button class="btn disabled">Participants: </button>' );
	 
	 var discPosts = posts.split(",");
	 	 	 
	 var i, j, p, d, q, typeText, authorID, message, authorThumb;
	 for(i = 0; i < discPosts.length; i++){							// Take one post at a time
		 p = discPosts[i];
		 for (j = 0; j < main.data.allPosts.length; j++){			// Go through all the posts
			 d = main.data.allPosts[j];		
			 	 
			 if(d.postID == p){										// Find the post we want to get the details of 

								 /********** TIMELINE ***********/ 
							 var n = d.postTime; 
							  n = n.replace(/-/g, "/");

							 var time = Date.parse(n);				// Parsing not working for firefox. 
							 //var time = new Date(n); 
							 
							 if(main.timelineMin == 0){							// Check and set minimum value for time
								 main.timelineMin = time;
							 } else if (time < main.timelineMin){
								 main.timelineMin = time;
								 }
							 
							 if(main.timelineMax == 0){							// Check and set maximum value for time
								 main.timelineMax = time;
							 } else if (time > main.timelineMax){
								 main.timelineMax = time; 
								 }
								 
							 // END TIMELINE
							 
							 
							/********** LAST VISIT ***********/	
							
							 
							 // END LAST VISIT
							 
							/********** DISCUSSION SECTION ***********/
																				// Prepare the data for display
							 authorID = main.getName(d.postAuthorId, 'first'); 			// Get Authors name			
							 authorIDfull = main.getName(d.postAuthorId); 
							 authorThumb = main.getAuthorThumb(d.postAuthorId, 'small');			// get thumbnail html
							 authorThumb += '  ' + authorIDfull; 
							 switch(d.postType)									// Get what kind of post this is 
								{
								case 'agree':
								  typeText = ' <span class="typicn thumbsUp "></span>';
								  break;
								case 'disagree':
								  typeText = ' <span class="typicn thumbsDown "></span>';
								  break;
								case 'clarify':
								  typeText = ' <span class="typicn unknown "></span>';
								  break;
								case 'offTopic':
								  typeText = ' <span class="typicn forward "></span>';
								  break;		  
								case 'synthesis':
								  typeText = ' <span class="typicn feed "></span>';
								  break;	
								default:
								  typeText = ' <span class="typicn message "></span>';
								}
			
			
							 if(d.postMessage != ' '){									// Proper presentation of the message URL
								message = d.postMessage ; 
								//message = main.showURL(message);
								message =message.replace("\n","<br /><br />");
							 } else {
								continue;  // Hide the post if there is no text in the message 
								switch(d.postType)									// Get what kind of post this is 
									{
									case 'agree':
									  message = '<em class="timeLog">agrees.</em> ';
									  break;
									case 'disagree':
									  message = '<em class="timeLog">disagrees. </em>';
									  break;
									case 'clarify':
									  message = '<em class="timeLog">asked to clarify </em>';
									  break;
									case 'offTopic':
									  message = '<em class="timeLog">marked as off topic </em>';
									  break;		  
									default:
									  message = ' ';
									}
							 }
							 							 
							 var topLevelMessage = ' ';								// Assign a class for top level messages for better organization.
							 if (d.postFromId == '0'){
								 topLevelMessage = 'topLevelMessage'; 
							 }
							 
							 
							 // Check if this post has selection
							 var selection = ''; 
							 /* if(d.postSelection.length > 1){
							 	selection = ' <span href="#" rel="tooltip" title="This post has highlighted a segment in the parent post. Click to view." class="selectionMsg" style="display:none;">a</span> ';
							 } */
							 
							 // Check if this post has media assigned. 
							 var media = ''; 
							 if(d.postMedia.length > 1){
							 	media = '<span href="#" rel="tooltip" title="This post has a ' + d.postMediaType + ' media attachment. Click to view." class="mediaMsg"> ' + d.postMediaType + '  <span class="typicn tab "></span> </span> ';
							 }
							 
							 
							 var showPost = 'yes';
							 var userRoleAuthor = main.UserCourseRole(discID, d.postAuthorId);  
							 if(dStatus == 'student' && currentUserID != d.postAuthorId && userRoleAuthor == 'student'){
							 	 	if(userRole == 'student' || userRole == 'unrelated'){
							 	 		showPost = 'no'; 
							 	 		}
							 }
							 
							 // Is this post part of any synthesis? 
							 var synthesis = ''; 
							 synthesis = main.PostInSynthesis(d.postID);  

							 
							 
							 if(showPost == 'yes') {

							 	var selector = 'div[level="'+ d.postFromId +'"]';
							 	var responses = main.ResponseCounters(d.postID);	
							 
								 $(selector).append(						// Add post data to the view
								 	  '<div class="threadText ' + topLevelMessage +'" level="'+ d.postID + '" postTypeID="'+ d.postType+ '" postAuthorId="' + d.postAuthorId + '" time="' + time + ' ">' 
								 	+  '<div class="postTypeView" slevel="'+ d.postID + '"> ' + typeText + '</div>'
								 	+  '<div class="postTextWrap">' 
								 	+  '<span class="postAuthorView" rel="tooltip"  title="' + authorThumb + '"> ' + authorID + '</span>'
								 	+  '<span class="postMessageView"> ' + message  + '</span>'
								 	+ media + selection + synthesis
								 	+  '</div>'	
								 	+ ' <button class="btn btn-small btn-success sayBut2" style="display:none" postID="'+ d.postID + '"><i class="icon-comment icon-white"></i> say</button> '								 	
								 	+ '<div class="responseWrap" >' + responses + '</div>' 
								 	
								 	+ '</div>'
								 );
								 
								 /********** SYNTHESIS POSTS ***********/
								if(d.postType == 'synthesis'){ 
									 $('#synthesisList').prepend(
									  '<div class="synthesisPost well" sPostID="' + d.postID + '">' 
									+  '<span class="postAuthorView" rel="tooltip" > ' + authorID + '</span>'
									+ '		<p>' + message + '</p>' 
									+ '	</div>' 
									); 
									main.ListSynthesisPosts(d.postContext, d.postID); 
								}
							 
								 /********** RECENT ACTIVITY SECTION ***********/
								 if ($(selector).length > 0){  								 // Check if the source element exists, (this is for hiding instructor posts in recent discussions)								
									 //var range = discPosts.length-8; 			// How many of the most recent we show + 1
									 main.LastVisit('discussion',discID, function(){
										var prettyTime = main.PrettyDate(d.postTime);
										var shortMessage = main.truncateText(message, 60);
										if(d.postTime > main.visitTime) {					 // person + type + truncated comment + date
											 console.log('post is after last visit'); 
											 var activityContent = '<li postid="' + d.postID + '">' + main.getAuthorThumb(d.postAuthorId, 'tiny') + ' ' + authorID + ' ' + typeText + ' <b>' + shortMessage + '</b> ' + '<em class="timeLog">' + prettyTime + '<em></li> ';
											 $('#recentContent').prepend(activityContent);  
									     }	 
									 });
									 
								     } 
								 
								 
								 
								 
								 /********** UNIQUE PARTICIPANTS SECTION ***********/
								 var arrayState = jQuery.inArray(d.postAuthorId, main.uParticipant); 	// Chech if author is in array
								 if(arrayState == -1) {								// if the post author is not already in the array
									 main.uParticipant.push(d.postAuthorId);		// add id to the array
								 }
							 
						} // end if showpost. 
				 
			 }	 
		 }
	 }


	 
	 main.timelineValue = main.timelineMax;						// Set the timeline value to the max. 
	 main.UniqueParticipants();
	    
         $(".postTypeView").draggable({
            start: function() {
                console.log('Drag started');

                main.sPostID = $(this).attr('slevel'); // The id of the post
                main.sPostContent = $(this).parent().children('.postTextWrap').children('.postMessageView').html() // The content of the post
            },
            drag: function() {
                console.log('Drag happening');
            },
            stop: function() {
                console.log('Drag stopped!');
            }, 

			helper: function( event ) {
				var contents = $(this).html(); 
				return $( '<div style="font-size:50px; position: absolute; z-index: 1100">' + contents + ' </div>' );
			}
 
        });

 $( "#synthesisDrop" ).droppable({
            hoverClass: "sDropHover",
            drop: function( event, ui ) {
                $( this )
                        .html( "Added!" );
               $('#synthesisPostWrapper').prepend('<div sPostID="'+ main.sPostID +'" class=" synthesisPosts">' + main.sPostContent + ' <div>');  // Append original post
        
            }
        });

}

Dscourse.prototype.PostInSynthesis=function(postID){					// Populate unique participants.  
	
		 var main = this;
		 var output = ''; 
		 var count = 0; 

		 var j, k, i, o; 
		 for(j = 0; j < main.data.allPosts.length; j++){
		 	k = main.data.allPosts[j];
		 	
		 	if(k.postContext){
		 	
			 	var posts = k.postContext.split(",");
	
				for(i = 0 ; i < posts.length; i++){		 	
				 	o = posts[i]; 
				 	if(o == postID){
					 	output  += '<span rel="tooltip" title="'+ main.getName(k.postAuthorId, 'first') + '  made a connection to this post. Click to view." class="SynthesisComponent hide" synthesisSource="'+ k.postID +'"><span class="typicn feed "></span></span>';
					 	count++ 
				 	}
				} 	
			 
		   } 
		 }
		 
		 if (count > 0){
		 	output = '<span class="synthesisWrap"> <b>' + count +'</b> Connections ' + output + '</span>'; 
		 }
		 return output; 

}
Dscourse.prototype.UniqueParticipants=function(){					// Populate unique participants.  
	
		 var main = this;
		 $('.uList').remove();
		 var i, o, name, thumb, output; 
		 for(i = 0; i < main.uParticipant.length; i++){
			 o = main.uParticipant[i]; 
			 name = main.getName(o); 
			 thumb = main.getAuthorThumb(o, 'small'); 
			 output = '<button class="btn uList" rel="tooltip" title="' + name + '" authorID="' + o +  '">' + thumb + ' </button>'; 
			 $('#participantList').append(output); 
		 }
		 
		 
}



Dscourse.prototype.ListSynthesisPosts=function(postList, sPostID){					// Populate unique participants.  
	
		 var main = this;
		 
		 var i, o, j, k;
		 var posts = postList.split(",");
		 
		 for(i = 0; i < posts.length; i++){
			 o = posts[i]; 
			 
			 for(j = 0; j < main.data.allPosts.length; j++){
			 	k = main.data.allPosts[j];
			 	if(k.postID == o){
				 	$('.synthesisPost[sPostID="'+  sPostID + '"]').append('<div sPostID="'+ k.postID +'" class=" synthesisPosts hide"> ' + main.getAuthorThumb(k.postAuthorId, 'tiny') + ' ' + main.getName(k.postAuthorId) + ': <br />'  + k.postMessage + ' <div>');  
				 	
			 	}
			 }
		 }		 
		 
		  
		 		 
		 
}

Dscourse.prototype.ResponseCounters=function(postId){
	
		 var main = this;
		 
		 var comment = 0;    var commentPeople = '';
		 var agree 	= 0; 	 var agreePeople = '';
		 var disagree = 0; 	 var disagreePeople = '';
		 var clarify = 0;	 var clarifyPeople = '';
		 var offTopic = 0; 	 var offTopicPeople = '';
		 
		 var i, o; 
		 
		  for(i = 0; i < main.data.allPosts.length; i++){
		  		o = main.data.allPosts[i];
		  		if(o.postFromId == postId){
		  		
			  		var postAuthor = main.getName(o.postAuthorId);
			  		
			  		switch(o.postType)									// Get what kind of post this is 
					{
					case 'agree': 						
						var d1 = agreePeople.indexOf(postAuthor); 		// Do not add if author already exists
						if(d1 == -1){
							if(agreePeople.length > 0){
								agreePeople += '<br />';  
							}						 
						  agreePeople += postAuthor ; 
						  }
					  agree++;
					  break;
					case 'disagree':
						var d2 = disagreePeople.indexOf(postAuthor); 		// Do not add if author already exists
						if(d2 == -1){
							if(disagreePeople.length > 0){
								disagreePeople += '<br />';  
							}						
						  disagreePeople += postAuthor; 
						  }					  
						disagree++;
					  break;
					case 'clarify':
						var d3 = clarifyPeople.indexOf(postAuthor); 		// Do not add if author already exists
						if(d3 == -1){
							if(clarifyPeople.length > 0){
								clarifyPeople += '<br />';  
							}						
						  clarifyPeople += postAuthor ; 
						  }
					  clarify++;					  
					  break;
					case 'offTopic':
						var d4 = offTopicPeople.indexOf(postAuthor); 		// Do not add if author already exists
						if(d4 == -1){
							if(offTopicPeople.length > 0){
								offTopicPeople += '<br />';  
							}
						
						  offTopicPeople += postAuthor; 
						  }
					  offTopic++;
					  break;		  
					default:

						var d5 = commentPeople.indexOf(postAuthor); 		// Do not add if author already exists
						if(d5 == -1){
							if(commentPeople.length > 0){
								commentPeople += '<br />';  
							}
					  		commentPeople += postAuthor; 
					  	}
					  comment++;
					}
			  		
		  		}
		  }	
		  var commentText = ' ', agreeText = ' ', disagreeText = ' ', clarifyText = ' ', offTopicText = ' '; 
		  if(comment 	> 0){commentText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="comment" title="<b>Comments from: </b><br /> ' + commentPeople +'" > ' + comment 	+ '  <span class="typicn message "></span></span>  ';} 
		  if(agree 	 	> 0){agreeText 		= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="agree" title="<b>People who agreed: </b><br /> ' + agreePeople + '"> ' + agree 	+ '  <span class="typicn thumbsUp "></span> </span> '	 ;}
		  if(disagree	> 0){disagreeText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="disagree" title="<b>People who disagreed:</b><br /> ' + disagreePeople + '"> ' + disagree 	+ '  <span class="typicn thumbsDown "></span></span> ';}
		  if(clarify 	> 0){clarifyText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="clarify" title="<b>People that asked to clarify:</b><br /> ' + clarifyPeople + '"> ' + clarify 	+ '  <span class="typicn unknown "></span></span> ' ;}
		  if(offTopic 	> 0){offTopicText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="offTopic" title="<b>People that marked off topic: </b><br />' + offTopicPeople + '"> ' + offTopic 	+ '  <span class="typicn forward "></span> </span>  ' ;}

		  var text =   commentText + agreeText + disagreeText + clarifyText + offTopicText ; 
		 
		 return text; 

}

Dscourse.prototype.HighlightRelevant=function(postID)					// Highlights the relevant sections of host post when hovered over 
{
	 var main = this;
	 
	 // get selection of this post ID 
	 var i, o, thisSelection, j, m, highlight, newHighlight, n, selector; 
	 for(i = 0; i < main.data.allPosts.length; i++){
		 o = main.data.allPosts[i];
		 
		 if(o.postID == postID){
			 if(o.postSelection !== ""){ 								// If there is selection do highlighting
				 thisSelection = o.postSelection.split(",");
				 var num1 = parseInt(thisSelection[0]);
				 var num2 = parseInt(thisSelection[1]);
				 // var num3 = num2-num1;   // delete if substring() works. 
				 // find the selection in reference post 
				 for(j = 0; j < main.data.allPosts.length; j++){
				 	m = main.data.allPosts[j];
				 	
				 	if(m.postID == o.postFromId){
					 	highlight = m.postMessage.substring(num1,num2); 
					 	newHighlight = '<span class="highlight">' + highlight + '</span>';
					 	n = m.postMessage.replace(highlight, newHighlight);
					 	selector = 'div[level="'+ o.postFromId +'"]'; 
					 	$(selector).children('.postTextWrap').children('.postMessageView').html(n);

				 	}
				 }
				 
			 } else {
				 													// If there is no selection remove highlighting
				 														
			 }
			 
		 }
		 
	 }
}

Dscourse.prototype.Heatmap=function(type, action)					// Highlights the relevant sections of host post when hovered over 
{
	 var main = this;

	 var selector = '.threadText[posttypeid="' + type + '"]'; 
	
		if(action == 'add'){ 

				$(selector).addClass(type);
			 } else if (action == 'remove'){
				$(selector).removeClass(type);

			 }	
	 
 }
 
 Dscourse.prototype.UserHeatmap=function(id, action)					// Highlights the relevant sections of host post when hovered over 
{
	 var main = this;
	 $('.threadText').css('background-color', '#fff'); 				// Reset background highlights 
	 
	 var selector = '.threadText[postAuthorId="' + id + '"]'; 
	 
	 if(action == 'add'){ 	
				$(selector).css('background-color', '#defcff');
			 } else if (action == 'remove'){
				 $(selector).css('background-color', '#fff');

			 }	
}


 Dscourse.prototype.CheckNewPosts=function(discID, userRole, dStatus)					// Highlights the relevant sections of host post when hovered over 
{
	 var main = this;
	 
	 
		var i, posts;
	 	for (i = 0; i < main.data.allDiscussions.length; i++)
	 	{		
	 		var o = main.data.allDiscussions[i];
	 		if(o.dID == discID ){
			 	posts = o.dPosts;
			 	
			 		
					$.ajax({																							
						type: "POST",
						url: "scripts/php/data.php",
						data: {
							currentDiscussion: discID,
							currentPosts: 	posts,						
							action: 'checkNewPosts'							
						},
						  success: function(data) {						// If connection is successful . 
								if(data.result > 0){
									$('#checkNewPosts').addClass('animated flipInY');
									$('#checkNewPosts').html('<div class="alert alert-success refreshBox" discID="' + discID + '">    <button id="hideRefreshMsg"type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><span class="typicn refresh refreshIcon"></span> There are <b>' + data.result + '</b> new posts. Click to refresh!</span>'); 
									main.newPosts = data.posts;
								} else {
									console.log('No new posts...');
								}
						    }, 
						  error: function() {					// If connection is not successful.  
								console.log("Dscourse Log: the connection to data.php failed for Checking new posts.");  
						  }
					});	
			 }
		}

		
		
		 
}



/***********************************************************************************************/ 
/*                					HELPER FUNCTIONS 									   */
/***********************************************************************************************/
 
 Dscourse.prototype.DrawShape=function()	 			   
 {
	 var main = this;





  // get the canvas element using the DOM
  var canvas = document.getElementById('cLines');
 
  var scrollBoxTop = $('#scrollBox').css('margin-top'); 
      scrollBoxTop = scrollBoxTop.replace('px','');
      scrollBoxTop = Math.floor(scrollBoxTop);
  
  var scrollBoxHeight = $('#scrollBox').css('height');  // find the height of scrollbox
      scrollBoxHeight = scrollBoxHeight.replace('px','');
      scrollBoxHeight = Math.floor(scrollBoxHeight);
  		
  var linesHeight = $('#lines').height(); 
  
  		canvas.height = linesHeight;
  
  var scrollBoxBottom = scrollBoxHeight + scrollBoxTop; // add the height to the top position to find the bottom. 
  
 
    // use getContext to use the canvas for rawing
    var ctx = canvas.getContext('2d');
 
    	// Clear the drawing
	    ctx.clearRect(0, 0, canvas.width, canvas.height);

	    // Options
	    ctx.lineCap = 'round';
	    ctx.lineWidth=3;
	    ctx.strokeStyle = '#ccc';
	    
	    // Top line
	    ctx.beginPath();
	    ctx.moveTo(25,scrollBoxTop+3);
	    ctx.lineTo(48,1);
	    ctx.stroke();
	    ctx.closePath();

	    // Bottom line
	    ctx.beginPath();
	    ctx.moveTo(25,scrollBoxBottom+2);
	    ctx.lineTo(48,linesHeight-3);
	    ctx.stroke();
	    ctx.closePath();
  
 
}
 
 
 Dscourse.prototype.ClearUserForm=function()	 			   
 {
	 var main = this;
	 
		$('#addUserForm').find('input:text, input:password, input:file, select, textarea').val('');
		$('#imgPath').html('');
		// Removing validation
		$('#firstNameControl').removeClass('success').removeClass('error').find('.help-inline').html('Provide the First Name of the user.'); 	
		$('#lastNameControl').removeClass('success').removeClass('error').find('.help-inline').html('Provide the Last Name of the user.'); 	
		$('#emailControl').removeClass('success').removeClass('error').find('.help-inline').html('This will also be the username.'); 	
		$('#passwordControl').removeClass('success').removeClass('error').find('.help-inline').html('Enter password.'); 	
		$('#aboutControl').removeClass('success').removeClass('error').find('.help-inline').html('Briefly introduce yourself. Please limit your text to 1000 characters.'); 	
		$('#facebookControl').removeClass('success').removeClass('error').find('.help-inline').html('Facebook username.'); 	
		$('#twitterControl').removeClass('success').removeClass('error').find('.help-inline').html('Twitte username.'); 	
		$('#phoneControl').removeClass('success').removeClass('error').find('.help-inline').html('Phone number.'); 	
		$('#websiteControl').removeClass('success').removeClass('error').find('.help-inline').html('Website.'); 	
	 
 }
 
 Dscourse.prototype.AddLog=function(logPageType,logPageID,logAction,logActionID,logMessage)	 			   
 {
	 var main = this;
	 
	 var log = new Object();
	 
	 // Create object
	 log = {
				'logUserID' : currentUserID,
				'logPageType': logPageType,
				'logPageID': logPageID,
				'logAction': logAction,
				'logActionID': logActionID,
				'logMessage': logMessage,
				'logUserAgent': dUserAgent  
			};
	// Write to database 		
	$.ajax({																						
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				log: log,							
				action: 'addLog'
			},
			  success: function() {						// If connection is successful . 
			    	console.log("Dscourse Log: " + logPageType + ' ' + logAction + " event logged."); 
			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to data.php failed for Add Log.");  
			  }
		});	
	

}


 Dscourse.prototype.LastVisit=function(logPageType,logPageID, callback)	 			   
 {
	 var main = this;
		 	 
	 	$.ajax({																						
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				logPageType: logPageType,
				logPageID: logPageID,
				logUserID: currentUserID,							
				action: 'lastVisit'
			},
			  success: function(data) {						// If connection is successful . 
			    	if(data){
				    	main.visitTime = data.logTime;
				    	callback();				    	
			    	}

			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to data.php failed for Last Visit.");  
			  }
		});	
		
  }

Dscourse.prototype.getName=function(id, type)
{
	var main = this;
	
	if(type == 'first') {

		for(var n = 0; n < main.data.allUsers.length; n++){
			var userIDName = main.data.allUsers[n].UserID;
			if (userIDName == id)
				return main.data.allUsers[n].firstName;
		}	
		
	} else if(type == 'last') {
		
		for(var n = 0; n < main.data.allUsers.length; n++){
			var userIDName = main.data.allUsers[n].UserID;
			if (userIDName == id)
				return main.data.allUsers[n].lastName;
		}	
		
	} else {
	
		for(var n = 0; n < main.data.allUsers.length; n++){
			var userIDName = main.data.allUsers[n].UserID;
			if (userIDName == id)
				return main.data.allUsers[n].firstName + " " + main.data.allUsers[n].lastName;
		}	
	}
}


Dscourse.prototype.getAuthorThumb=function(id, size)
{
	 var main = this;

	
		for(var n = 0; n < main.data.allUsers.length; n++){
			var userIDName = main.data.allUsers[n].UserID;
			if (userIDName == id)
			
				if(size == 'small'){
					return '<img class=userThumbSmall src=' + main.data.allUsers[n].userPictureURL + ' />' ;
				} else if (size == 'tiny'){
					return '<img class=userThumbTiny src=' + main.data.allUsers[n].userPictureURL + ' />' ;
					
				}
		}	
	
}

Dscourse.prototype.GetSelectedText=function()					// Select text
{
	 var main = this;

  	 var text; 
	 
	 if (window.getSelection) {
	        text = window.getSelection().toString();
	    } else if (document.selection && document.selection.type != "Control") {
	        text = document.selection.createRange().text;
	    }
  
	    return text; 
}

Dscourse.prototype.GetSelectedLocation=function(element)					// Data about begin and end of selection
{
	 var main = this;

    var start = 0, end = 0;
    var sel, range, priorRange;
    if (typeof window.getSelection != "undefined") {
        range = window.getSelection().getRangeAt(0);
        priorRange = range.cloneRange();
        priorRange.selectNodeContents(element);
        priorRange.setEnd(range.startContainer, range.startOffset);
        start = priorRange.toString().length;
        end = start + range.toString().length;
    } else if (typeof document.selection != "undefined" &&
            (sel = document.selection).type != "Control") {
        range = sel.createRange();
        priorRange = document.body.createTextRange();
        priorRange.moveToElementText(element);
        priorRange.setEndPoint("EndToStart", range);
        start = priorRange.text.length;
        end = start + range.text.length;
    }
    return {
        start: start,
        end: end
    };
}



Dscourse.prototype.getEmail=function(id)
{
	var main = this;

	for(var n = 0; n < main.data.allUsers.length; n++){
		var userIDName = main.data.allUsers[n].UserID;
		if (userIDName == id)
			return main.data.allUsers[n].username;
	}	
}


 Dscourse.prototype.ClearCourseForm=function()	 			   
 {
	 var main = this;
		$('#courseForm').find('input:text, input:password, input:file, select, textarea').val('');
		$('#cimgPath').html('');
		$('#addPeopleBody').html('');	 
 }
 	

 Dscourse.prototype.ClearDiscussionForm=function()	 			   
 {
	 var main = this;
		$('#discussionForm').find('input:text, input:password, input:file, select, textarea').val('');
		$('#discussionQuestionControl').removeClass('success').removeClass('error').find('.help-inline').html('Please provide a discussion question.'); 
		$('#addCoursesBody').html('');	 
 }
 	 
 Dscourse.prototype.ClearPostForm=function()	 			   
 {
	 var main = this;
		$('#commentWrap').find('input:text, input:password, input:file, select, textarea').val('');
		$('.postBoxRadio').removeAttr('checked'); 								// Restore checked status to comment. 
		$('#postTypeID > button').removeClass('active');
		$('#postTypeID > #comment').addClass('active');
		$('#highlightShow').html(' ');
		$('#text').removeClass('textErrorStyle');	
		$('#textError').hide();
 }
 	
// Function to get the links embedded in comments appear as links.
Dscourse.prototype.showURL=function(text)
{
	var main = this;
	
    var urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, function(url) {
        return '<a href="' + url + '">' + url + '</a>';
    })

/* Use example: 
   var text = "Find me at http://www.example.com and also at http://stackoverflow.com";
   var html = showURL(text);
*/
} 

Dscourse.prototype.getUrlVars=function() 		// Gets parameters from url. Usage : var first = getUrlVars()["id"];
{
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

Dscourse.prototype.truncateText=function(text, length)
{
	
	if(text.length < length ){
		return text; 
	} else {
	
		var myString = text;
		var myTruncatedString = myString.substring(0,length) + '... ';
		return myTruncatedString;
		
	}
	
}

Dscourse.prototype.FormattedDate=function(date)
{
	var d, m, curr_hour, dateString; 
	d = new Date(date);				// Write out the date in readable form.
	m = d.toDateString();
    curr_hour = d.getHours(); 
    dateString = m + '  ' + curr_hour + ':00';
	
	return dateString;				 

				    
}

Dscourse.prototype.DiscResize=function()
{

	var main = this;


  var h, wHeight, nbHeight, jHeight, cHeight, height; 
		  
		  // Get total height of the window with the helper function
		  wHeight = $(window).height(); 
		  wWidth = $(window).width();
		  
		  // Get height of #navbar
		  nbHeight = $('.navbar').height(); 

		  // Get height of jumbutron
		  jHeight = $('#discussionWrap > header').height(); 
		  
		  // Get height of #controlsWrap
		  cHeight = $('#controlsRow').height(); 
		  
		  // resize #dRowMiddle accordingly. 
		  height = wHeight - (nbHeight + jHeight + cHeight + 30); 

		  height = height + 'px';
		  mHeight = wHeight - (nbHeight + jHeight + cHeight + 30);
		  mHeight = -mHeight; 
		   
		  $('#dSidebar').css({'height' : height, 'overflow-y' : 'scroll', 'overflow-x' : 'hidden'});
		  $('#vHeatmap').css({'height' : height, 'overflow-y' : 'scroll', 'overflow-x' : 'hidden'});
		  $('#dMain').css({'height' : height, 'overflow-y' : 'scroll', 'overflow-x' : 'hidden'});
		  $('#dRowMiddle').css({'margin-top' : 10}); //jHeight+30});
		  $('#lines').css({'height' : height, 'margin-top' : mHeight + 'px'});

		  $('#mediaBox').css({'height': wHeight-100 + 'px'});
		  $('#node').css({'height': wHeight-200 + 'px'});

		  $('#homeWrapper').css({'width': wWidth-600 + 'px'});


		  //=== CORRECT Vertical Heatmap bars on resize  ===  
		  // Each existing heatmap point needs to be readjusted in terms of height. 
		  	// View box calculations
			var boxHeight = $('#vHeatmap').height(); // Get height of the heatmap object
			var totalHeight = $('#dMain')[0].scrollHeight; // Get height for the entire main section

		  $('.vHeatmapPoint').each(function() {
		  		var postValue = $(this).attr('divPostID'); // get the divpostid value of this div
		  		
		  		var thisOne = $(this); 
		  		// redraw the entire thing. 
		  				$('.threadText').each(function(){  // Go through each post to see if postAuthorId in Divs is equal to the mapInfo
							var postAuthor = $(this).attr('postAuthorId'); 
							var postID = $(this).attr('level'); 
							if(postID == postValue){
								var divPosition = $(this).position();  // get the location of this div from the top
								console.log(divPosition);
								var ribbonMargin = (divPosition.top) * boxHeight / totalHeight; // calculate a yellow ribbon top for the vertical heatmap
									ribbonMargin = ribbonMargin; // this correction is for better alignment of the lines with the scroll box. 
									
									// There is an error when the #dMain layer is scrolled the position value is relative so we have minus figures.
				
								$(thisOne).css('margin-top', ribbonMargin); 
										}
						}); 
		  });
		  // ==  end correct vertical heatmap


		  $('#displayFrame').css({'height': wHeight-100 + 'px'});
		  $('#displayIframe').css({'height': wHeight-150 + 'px'});
		  
		  //Fixing the width of the threadtext
		  $('.threadText').each(function() { 
			  var parentwidth = $(this).parent().width();
			  
			  var thiswidth = parentwidth-12; 
			  $(this).css('width',thiswidth+'px'); 
			  $(this).children('.postTypeView').css('width','20px');
			  $(this).children('.sayBut2').css('width','50px');
			  $(this).children('.responseWrap').css('width','40px');
			  $(this).children('.postTextWrap').css('width',thiswidth-110+'px');

		  });
		  

}

Dscourse.prototype.ClearVerticalHeatmap=function()
{
	// Check to see how clearing will function, this is probably the place for it. 
	$('#vHeatmap').html('');
	$('#vHeatmap').append('<div id="scrollBox"> </div>'); // Add scrolling tool

}	

Dscourse.prototype.VerticalHeatmap=function(mapType, mapInfo)
{

	 var main = this;

		
	// View box calculations
	var boxHeight = $('#vHeatmap').height(); // Get height of the heatmap object
	var visibleHeight = $('#dMain').height();  // Get height of visible part of the main section
	var totalHeight = $('#dMain')[0].scrollHeight; // Get height for the entire main section
	
	// Size the box
	var scrollBoxHeight = visibleHeight * boxHeight / totalHeight; 
	$('#scrollBox').css('height',scrollBoxHeight-7); // That gives the right relative size to the box

	// Scroll box to visible area
	var mainScrollPosition = $('#dMain').scrollTop(); 
	var boxScrollPosition = mainScrollPosition * boxHeight / totalHeight; 
	$('#scrollBox').css('margin-top',boxScrollPosition); // Gives the correct scrolling location to the box
	

	if(mapType == 'user'){  	// if mapType is -user- mapInfo is the user ID
		$('.threadText').each(function(){  // Go through each post to see if postAuthorId in Divs is equal to the mapInfo
			var postAuthor = $(this).attr('postAuthorId'); 
			var postID = $(this).attr('level'); 
			if(postAuthor == mapInfo){
				var divPosition = $(this).position();	  // get the location of this div from the top
				
				// dynamically find. 
				var mainDivTop = $('#dMain').scrollTop();  
				console.log('main div scroll: ' + mainDivTop); 
				console.log(divPosition);
				var ribbonMargin = (divPosition.top+mainDivTop) * boxHeight / totalHeight; // calculate a yellow ribbon top for the vertical heatmap
					ribbonMargin = ribbonMargin; // this correction is for better alignment of the lines with the scroll box. 
					
					// There is an error when the #dMain layer is scrolled the position value is relative so we have minus figures.

				$('#vHeatmap').append('<div class="vHeatmapPoint" style="margin-top:'+ ribbonMargin + 'px" divPostID="'+ postID +'" ></div>'); // append the vertical heatmap with post id and author id information (don't forgetto create an onclick for this later on)
						}
		}); 
	
		
	}
			
	
	if(mapType == 'keyword'){ // if mapType is -keyword- mapInfo is the text searched

		main.ClearKeywordSearch('#dMain'); 

		console.log(mapInfo); // Works
		$('.threadText').each(function(){  // go through each post to see if the text contains the mapInfo text
			var postID = $(this).attr('level');
			var postContent =  $(this).children('.postTextWrap').children('.postMessageView').text();  // get post text
			postContent = postContent.toLowerCase(); // turn search items into lowercase
			console.log('Post Content: ' + postContent);
			
			var a=postContent.indexOf(mapInfo);
			
			// search for post text with the keyword text
			// if there is a match get location information
			if(a != -1){
				var divPosition = $(this).position();  // get the location of this div from the top
				console.log(divPosition);
				var ribbonMargin = (divPosition.top) * boxHeight / totalHeight; // calculate a yellow ribbon top for the vertical heatmap
				$('#vHeatmap').append('<div class="vHeatmapPoint" style="margin-top:'+ ribbonMargin + 'px" divPostID="'+ postID +'" ></div>'); // append the vertical heatmap with post id and author id information (don't forgetto create an onclick for this later on)
				
				var replaceText = $(this).children('.postTextWrap').children('.postMessageView').html(); 
				// Find out if there is alreadt a span for highlighting here
				
				var newSelected = '<search class="highlightblue">' + mapInfo + '</search>'; 
				var n = replaceText.replace(mapInfo, newSelected); 
				$(this).children('.postTextWrap').children('.postMessageView').html(n); 

			}
		}); 
		
	}	
			


	// if mapType is -postType- mapInfo is the text of the type of post i.e. comment
		// go through each post to see if the post type is the mapInfo type
			// get the location of this div from the top
			// calculate a yellow ribbon top for the vertical heatmap
			// append the vertical heatmap with post id and author id information (don't forgetto create an onclick for this later on)
			// css the margin-top for the yellow ribbon
			// repeat for all posts.	

			  main.DrawShape();


}

Dscourse.prototype.ClearKeywordSearch=function(selector)
{
 	var main = this; 

	// remove search highlights		
	$(selector).find("search").each(function(index) {    // find search tag elements. For each
	    var text = $(this).html();				// get the inner html
	    $(this).replaceWith(text);			// replace it with the inner content. 
	});
}

Dscourse.prototype.TypeAhead=function()
{

 	var main = this; 
 	
 			
			$( "#coursePeople" ).autocomplete({
						minLength: 0,
						source: main.nameList,
						focus: function( event, ui ) {
							$( "#coursePeople" ).val( ui.item.label );
							return false;
						},
						select: function( event, ui ) {
							$('#addPeopleBody').append('<tr><td>' + ui.item.label + ' </td><td>' + ui.item.email  + ' </td><td><div class="btn-group" data-toggle="buttons-radio" id="roleButtons"><button class="btn roleB" userid="'+ ui.item.value + '">Instructor</button><button class="btn roleB" userid="'+ ui.item.value + '">TA</button><button class="btn active roleB" userid="'+ ui.item.value + '">Student</button></div></td><td><button class="btn removePeople">Remove</button>	</td></tr>'); // Build the row of users. 
							return false;
						}
					})
			

			$( "#discussionCourses" ).autocomplete({
						minLength: 0,
						source: main.data.courseList,
						focus: function( event, ui ) {
							$( "#discussionCourses" ).val( ui.item.label );
							return false;
						},
						select: function( event, ui ) {
							$('#addCoursesBody').append('<tr id="' + ui.item.value + '" class="dCourseList"><td>' + ui.item.label + ' </td><td><button class="btn removeCourses" >Remove</button>	</td></tr>'); 				// Build the row of courses. 
							$('.discussionCourses').val(' ').focus();
							return false;
						}
					})
 }

// SOMETHINGS BORROWED

/*
 * JavaScript Pretty Date
 * Copyright (c) 2011 John Resig (ejohn.org)
 * Licensed under the MIT and GPL licenses.
 */
Dscourse.prototype.PrettyDate=function(time)
{
	var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
		diff = (((new Date()).getTime() - date.getTime()) / 1000),
		day_diff = Math.floor(diff / 86400);
			
	if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
		return;
			
	return day_diff == 0 && (
			diff < 60 && "just now" ||
			diff < 120 && "1 minute ago" ||
			diff < 3600 && Math.floor( diff / 60 ) + " minutes ago" ||
			diff < 7200 && "1 hour ago" ||
			diff < 86400 && Math.floor( diff / 3600 ) + " hours ago") ||
		day_diff == 1 && "Yesterday" ||
		day_diff < 7 && day_diff + " days ago" ||
		day_diff < 31 && Math.ceil( day_diff / 7 ) + " weeks ago";
}

Dscourse.prototype.rDate=function(getDate)
{
	var rDate = new Date(getDate);
	var n = rDate.toDateString();
	return n; 
}

Dscourse.prototype.GetCurrentDate=function()
{
	var x = new Date();
	var monthReplace = (x.getMonth() < 10) ? '0'+x.getMonth() : x.getMonth();
	var dayReplace = (x.getDate() < 10) ? '0'+x.getDate() : x.getDate();
	var dateNow = x.getFullYear() + '-' + monthReplace + '-' + dayReplace + ' ' + x.getHours() + ':' + x.getMinutes() + ':' + x.getSeconds() ;
	
	return dateNow; 
}			    	 


Dscourse.prototype.ParseDate=function(input, format)
{
	
  format = format || 'yyyy-mm-dd'; // default format
  var parts = input.match(/(\d+)/g), 
      i = 0, fmt = {};
  // extract date-part indexes from the format
  format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; });

  return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]);
}



