/*
 *  All Course related code
 */

function Dscourse() 
{

	// Global

	this.data 		   = new Array(); 
	this.data.allUsers	= new Array();
	this.data.allCourses = new Array();
	this.data.allDiscussions = new Array();
	this.data.allPosts = new Array();



	this.saveStatus = "saved"; 							// Global variable for save status. To be used for when user wants to leave without saving. 


	// Users 

	this.nameList = new Array ();
	this.nameListName = new Object;	
	
	
	// Courses 

	this.course = { }; 

	// Discussions

	this.discussion = { }; 	
 
	this.courseList = [];
	this.courseListName = {};
	this.courseListStatus = 'off';
	
	// Posts
	this.post = { };
	
	this.currentSelected = '';  		// Needed for selection
	this.currentStart = '';
	this.currentEnd = ''; 
	
	this.currentDrawing = ''; 			// The drawing data that will be saved to the database. 
	this.currentDrawData = ''; 			// this is used for displaying drawings; 
	this.currentMediaType = ''; 		// What kind of media should be displayed. 
	this.postMediaType = 'Web'; 	// Used while saving the media type data. 
	
	this.uParticipant = new Array; 	// Unique list of participants. 
	
	// timeline
	this.timelineMin = 0;
	this.timelineMax = 0;
	
	// Fix for multiple image uploads 
	this.imgUpload = '';
	
	
	
	// Get all Data
		
	this.GetData('getAll', 'load');
	
	
	
	 
	

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
			  $('#addUserLink').addClass('linkGrey');
			  $('#userListLink').removeClass('linkGrey');
			  dscourse.imgUpload = 'user';
			  break;
			case 'coursesNav':
			  $('#coursesPage').show();
			  dscourse.imgUpload = 'course';
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
			  $('#homePage').show();
			}
		});
 
			
		$('#homeNav').live('click', function () {						// Home link
				showHome();
		});
	
		$('.showProfile').live('click', function () {						// Home link
				$('.page').hide();
				var userid = $(this).attr('userid');
			  dscourse.UserProfile(userid);
			  $('#userInfoWrap').show(); // hide user info
			  $('#addUserForm').hide(); // make visible user edit form. 
			  $('#profilePage').show();		
			  $('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
	
		});    
		
		$('.courseLink').live('click', function () {						// Home link
			$('.page').hide();
			var courseid = $(this).attr('courseid');
			dscourse.getCourse(courseid);
					dscourse.listCourseDiscussions(courseid);
	
			$('#coursePage').show();
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
		});
	
		$('.discussionLink').live('click', function () {						// Discussion link
			
			var discID = $(this).attr('discID');
			dscourse.SingleDiscussion(discID);
			$('.page').hide();
			$('#footerFixed').hide();
			$('#discussionWrap').show();
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification

		});
		
			
		function showHome(){
			$('.page').hide();			
			$('#homePage').show();
			  $('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification
		}
	
	
		/************ Discussions  ******************/
	
		$('#discussionForm').hide();
		$('#addDiscussionView').addClass('linkGrey');
	
		$("#discussionStartDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
		$("#discussionOpenDate").datepicker({ dateFormat: "yy-mm-dd" });
		$("#discussionEndDate").datepicker({ dateFormat: "yy-mm-dd" });
	
		$("#courseStartDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
		$("#courseEndDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
	
	
			//$("#commentWrap").draggable({cancel : "div#commentArea"});									// Makes the comment posting tool draggable. Needs Jquery ui. 
			
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
	
	// IF the users wants to leave they get a message
	
			  // window.onbeforeunload = confirmExit;
			  function confirmExit()
			  {
			  	// Have a variable for unsaved changes. 
			    return "This will take you away from Dscourse.";
			    }
			    
			    
		$('#highlightShow').live('mouseup', function () {
			
			var spannedText = $(this).find('span').text(); 					//remove highlight from text
			$(this).find('span').replaceWith(spannedText); 								
			
			top.currentSelected = top.GetSelectedText(); 
			var element = document.getElementById("highlightShow");  
			top.currentStart = top.GetSelectedLocation(element).start; 
			top.currentEnd	= top.GetSelectedLocation(element).end;
			console.log('start: ' + top.currentStart + ' - End : ' + top.currentEnd); 
			$('#locationIDhidden').val(top.currentStart + ',' + top.currentEnd);	// Add location value to form value; 

			
			console.log('mouse up text is: ' + top.currentSelected);
			var replaceText = $('#highlightShow').html(); 
			var newSelected = '<span class="highlight">' + top.currentSelected + '</span>'; 
			var n = replaceText.replace(top.currentSelected, newSelected); 
			$('#highlightShow').html(n);									// add highlight to text. 
		});

		$('#discussionDivs').tooltip({ selector: "span" });  
		$('#participants').tooltip({ selector: "li" });  
		$('#shivaDrawPaletteDiv').tooltip({ selector: "button" });  

		$('.threadText').live('click', function (event) {
				//	event.preventDefault();
				event.stopPropagation();
				$('.threadText').removeClass('highlight');
				$('.threadText').find('span').removeClass('highlight');
				var postClickId = $(this).closest('div').attr('level');
				console.log('Post click ID: ' + postClickId);  
				dscourse.HighlightRelevant(postClickId);
				$(this).addClass('highlight');
			});


		$('.dCollapse > h4').live('click', function () {
			$(this).parent().find('.content').fadeToggle();
		});

		/* 
		// Not doing this for the moment. 
		
		$("#discussionWrap").scroll(function(){
				var scrollLocation = document.getElementById("discussionWrap").scrollTop; 
				if(scrollLocation > 125) {
				 
					$('#controlsWrap').appendTo($('#discussionWrap')); // remove #controlsWrap and append to discussionWrap
					$('#controlsWrap').addClass('controlWrapFixed'); // Set controlWrapFixed class to the #controlsWrap
				} else { 
					$('#controlsWrap').appendTo($('#discussionTop')); // append controlsWrap to the top of #discussionTop	
					$('#controlsWrap').removeClass('controlWrapFixed');// Remove class .controlWrapFixed 	
				}
			});
		*/
		
		
	
	});

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
	
	
	$('#firstName').live('change', function() { 				// User form validations
			checkFirstName();
			});
	$('#lastName').live('change', function() { 
			checkLastName();
			});		
	$('#email').live('change', function() { 
			checkEmail();
			});		
	$('#password').live('change', function() { 
			checkPassword();
			});				
	$('#facebook').live('change', function() { 
			checkFacebook();
			});		
	$('#twitter').live('change', function() { 
			checkTwitter();
			});		
	$('#phone').live('change', function() { 
			checkPhone();
			});		
	$('#website').live('change', function() { 
			checkWebsite();
			});		
	$('#userAbout').live('keyup', function() {  			// Checks if user writes more than 1000 characters in about.  	
			checkAbout();
	});
	
					
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
		$('#courseButtonDiv').html('<button class="btn btn-primary" id="courseFormSubmit">Add Course</button>');
	});
	
	$('#courseFormSubmit').live('click', function() {  				// View all archived courses 	
		top.addCourse();
		$('.headerTabs a').addClass('linkGrey');
		$('#allCoursesView').removeClass('linkGrey');
		$('#courseForm').hide();
		$('#courses').show();
		$('.page').hide();	
		$('#coursesPage').show();
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
		top.ClearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#saveCourses').live('click', function() {  				// View all archived courses 	
		top.saveCourses();
	});
	
	$('.editCourse').live('click', function() {  				// When edit button is clicked. 	
		$('#courses').fadeOut();
		$('#courseForm').delay(200).fadeIn();
		$('#courseButtonDiv').html('<button class="btn btn-primary" id="updateCourse">Update Course Information</button>');
		
		var courseID = $(this).attr("id");						// Get the id for this course. 
		top.editCourse(courseID);									// Edit the course with the specific id. 	
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification

	});			

	$('#updateCourse').live('click', function() {  				// When edit button is clicked. 			
		top.updateCourse();
		top.saveCourses();									// Edit the course with the specific id. 	
		top.listCourses('all');
		$('.headerTabs a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
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
		top.ClearDiscussionForm();
		$('.headerTabs a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#discussions').fadeOut();
		$('#discussionForm').delay(200).fadeIn();
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
		console.log('buttontype: ' + buttonType);
		console.log('default text: ' + checkDefault);
	
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
		}
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
		
		if(dStatus != 'closed'){
			$('#highlightDirection').hide();
			$('#highlightShow').hide();
			var postQuote = $(this).parent().children('.postMessageView').html();
			postQuote = $.trim(postQuote);
			console.log('postquote: ' + postQuote);
					
			var xLoc = e.pageX-80; 
			var yLoc = e.pageY+10; 
			$('#commentWrap').css({'top' : yLoc, 'left' : '30%'});
			$('.threadText').removeClass('highlight');		
			var postID = $(this).attr("postID");
			console.log('Post id i got is:'  + postID);
			if(postQuote != ''){
				$('#highlightDirection').show();
				$('#highlightShow').show().html(postQuote);
				}
			$('#postIDhidden').val(postID);			
			$('#overlay').show();
			$('#commentWrap').fadeIn('fast');
			$(this).parent('.threadText').addClass('highlight');
			$('#text').val('Your comment...');
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
		if(txt == 'Why do you agree?' || txt == 'Why do you agree?' || txt == 'Why do you disagree?' || txt == 'What is unclear?' || txt == 'Your comment...' ){
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

			
		$('#showtimeline').live('click', function () {
				$('#timeline').slideToggle();
				
				if($(this).hasClass('active') == true) {
						$(this).removeClass('active');
						$(this).html('<span class="typicn time "></span>  Show Timeline ');
					} else {
						$(this).addClass('active');	
						$(this).html('<span class="typicn time "></span>  Hide Timeline ');
					}
			});
		
		$('#showParticipants').live('click', function () {
				$('#participants').slideToggle();
				
				if($(this).hasClass('active') == true) {
						$(this).removeClass('active');
						$(this).html('<span class="typicn views "></span>  Show Heatmap ');
					} else {
						$(this).addClass('active');	
						$(this).html('<span class="typicn views "></span>  Hide Heatmap ');
					}
			});
		
		$('#media').live('click', function () {
				$('#mediaBox').show();				
			});

		$('#closeMedia').live('click', function () {
				$('#mediaBox').hide();
			});
		$('#closeMediaDisplay').live('click', function () {
				$('#mediaDisplay').hide();
			});			
			
		$('.hmButtons').live('click', function () {						// Heatmap buttons and functions
				var hmType = $(this).attr('heatmap'); 
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					top.Heatmap(hmType, 'remove');
				} else {
					$(this).addClass('active');
					top.Heatmap(hmType, 'add');
				}
			});

		$('.zButtons').live('click', function () {						// Zoom buttons and functions
				var zoomType = $(this).attr('zoom'); 
				console.log(zoomType);

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
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					top.UserHeatmap(uListID, 'remove');
				} else {
					$(this).addClass('active');
					top.UserHeatmap(uListID, 'add');
				}
			});

		$('.drawTypes').live('click', function () {						// User heatmap buttons and functions
				top.postMediaType = 'Web'; 
				
			$('#mediaWrap').html('<iframe id="node" src="http://www.viseyes.org/shiva/webpage.htm" width="100%" height="500" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>');
				
				$('.drawTypes').removeClass('active');
				var drawType = $(this).attr('id'); 
				// Draw(drawType); Old way of drawing
				
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
					default:
						type = 'webpage'; 
					}
				
				var html = 	'<iframe id="node" src="http://www.viseyes.org/shiva/'+ type + '.htm" width="100%" height="500" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>'; 
				$('#mediaWrap').html(html); 
				
				top.postMediaType = drawType; 
				console.log('the draw type i got is: ' + top.postMediaType);
				
				$(this).addClass('active');
			});

		$('#continuePost').live('click', function () {						// When user clicks to save draw data into post. 
				top.currentDrawing = ''; 
				ShivaMessage('node','GetJSON'); 
				$('#mediaBox').hide();
			}); 

		$('#drawCancel').live('click', function () {						// When user clicks to save draw data into post. 
				top.currentDrawing = ''; 
				$('#mediaBox').hide();
			}); 			
			
		$('.mediaMsg').live('click', function () {
				event.stopImmediatePropagation();

				var postId = $(this).closest('.threadText').attr('level'); 
				
				 top.currentDrawData = ''; 
				 top.currentMediaType = 'Web';
				 var i, o; 
				 for(i = 0; i < top.data.allPosts.length; i++){
					 o = top.data.allPosts[i];
					 
					 if(o.postID == postId){
					 	top.currentDrawData = o.postMedia; 
					 	top.currentMediaType = o.postMediaType; 
					 	
							var typeId = top.currentMediaType; 
							var type; 
								 switch(typeId)									// Get what kind of iframe this is
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
									default:
										type = 'webpage'; 
									}
								
								var html = 	'<iframe id="display" src="http://www.viseyes.org/shiva/'+ type + '.htm" width="100%" height="500" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>'; 
								
								//$('#mediaDisplayWrap').html(html); 
								 
								var iFrameName = type+'Frame';  
								var cmd ="PutJSON="+top.currentDrawData;
								document.getElementById(iFrameName).contentWindow.postMessage(cmd,"*");			
								$('#mediaDisplay').show();
								$('#webpageFrame').hide();
								$('#videoFrame').hide();
								$('#drawFrame').hide();
								$('#mapFrame').hide();
								
								
								$('#' + iFrameName).show();
			 	
					 }
				 }				
			}); 
			
					$('#hideRefreshMsg').live('click', function () {
						$('#checkNewPosts').hide('');
					});

			
									
					

} /* end function Dscourse



/***********************************************************************************************/ 
/*                						DATABASE FUNCTIONS 									   */
/***********************************************************************************************/ 

Dscourse.prototype.GetData=function(action, load)
{
	// Get all data and populate in Json -- For courses and discussions, not for users

	
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
							console.log('page is: ' + page); 
							if(typeof page != undefined){
								$('.page').hide();	

								switch(page)
								{
								case 'users':
								  $('#usersPage').show();
								  dscourse.imgUpload = 'user';
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
								      console.log('drefresh: '+ dRefresh); 
								      var thetype = typeof dRefresh; 
								      console.log('the type: ' + thetype);
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
								  $('#homePage').show();
								}
							
							} else { 
								showHome();			
							}
							
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
	console.log('Current user status ' + currentUserStatus); // Check if global variable is read, this is set in index.php
	
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
	
	var i, o; 
	for(i = 0; i < main.data.allUsers.length; i++ )	// If view is not specified Construct the table for each element
			{  
				o = main.data.allUsers[i];
				
				if(o.UserID === id) 
				{
					console.log(o);
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
		
		if (main.data.allCourses){
			for(i = 0; i < main.data.allCourses.length; i++ )	// If view is not specified Construct the table for each element
			{    	
				o = main.data.allCourses[i];
		    	if (o.courseStatus == view) {
		    			inst = o.courseInstructors.split(",");
			    	  	fullName = "<strong>";
			    		for(j = 0; j < inst.length; j++){
			    			if (inst[j]){
				    			 fullName +=   main.getName(inst[j]) + "<br />";
				    		}
			    		}
			    		
			    		fullName += "</strong>";
		    		
		    		
			    		tas = o.courseTAs.split(",");
			    	  	var TAName = "<em>";
			    		for(var k = 0; k < inst.length; k++){
			    			if (tas[i]){
				    			 TAName +=   main.getName(tas[i]) + "<br />";
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
			            + "<td> " + o.courseStatus		+ "</td>" 
			            + "<td><strong> " + fullName	+ "</strong><br/>" + TAName +" </td>" 
			            + "<td> " + stuNum + "</td>"
				        + "<td>"; 
				        if(currentUserStatus == 'Administrator'){
				        		appendHTML +=  "<button id='" + o.courseID		+ "' class='btn btn-info editCourse'>Edit</button>"; 
				        }
			       appendHTML +=  "</td></tr>"; 
		    			    			
		    	  	$('#tablebody').append(appendHTML);
	    	  

	    		} else if (view == 'all'){			// This is bad code, it repeats the entire top section. Need to find a better way. 
		    			inst = o.courseInstructors.split(",");
			    	  	fullName = "<strong>";
			    		for(j = 0; j < inst.length; j++){
			    			if (inst[j]){
				    			 fullName +=   main.getName(inst[j]) + "<br />";
				    		}
			    		}
			    		
			    		fullName += "</strong>";
		    		
		    		
			    		tas = o.courseTAs.split(",");
			    	  	var TAName = "<em>";
			    		for(var k = 0; k < inst.length; k++){
			    			if (tas[i]){
				    			 TAName +=   main.getName(tas[i]) + "<br />";
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
			            + "<td> " + o.courseStatus		+ "</td>" 
			            + "<td><strong> " + fullName	+ "</strong><br/>" + TAName +" </td>" 
			            + "<td> " + stuNum + "</td>"
				        + "<td>"; 
				        if(currentUserStatus == 'Administrator'){
				        		appendHTML2 +=  "<button id='" + o.courseID		+ "' class='btn btn-info editCourse'>Edit</button>"; 
				        }
			       appendHTML2 +=  "</td></tr>"; 
		    			    			
		    	  	$('#tablebody').append(appendHTML2);
		    	  		
		    	  		courseListCourse = { value: o.courseID, label : o.courseName}; 
			    	  	main.courseList.push(courseListCourse);
			    	  

					
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
				    console.log($(this).text());
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
			
			main.data.allCourses.push(course);
			main.saveCourses();
}

Dscourse.prototype.editCourse=function(id)

{
			var main = this; 
			var i; 
			
			if (main.data.allCourses){
				for (i = 0; i < main.data.allCourses.length; i++ )
				{	
				var o = main.data.allCourses[i];	
						
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
					
					console.log('CI is: ' + ci);
					console.log('CT is: ' + ct);
					console.log('CS is: ' + cs);

					
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
										
				}				
			}	
			}	
}



Dscourse.prototype.updateCourse=function()
{
	var main = this;

	var courseID = $('#courseID').val();

	if (main.data.allCourses){
		for (i = 0; i < main.data.allCourses.length; i++ )
		{	
		var o = main.data.allCourses[i];			
		
		if (courseID == o.courseID)							{
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
					    console.log($(this).text());
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
				o.courseStudents		= courseStudents;
			
		}	
	
	}
	}	

}



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
			  	  	main.Trace(o.courseImage);
			  	  	
			  	  	
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
}



Dscourse.prototype.saveCourses=function()									// Sends the new data into the database
{
		var main = this; 
		
		$.ajax({											// Ajax talking to the saveCourses.php file												
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				courses: main.data.allCourses,							// All course data is sent
				action: 'saveCourses'							
			},
			  success: function(data) {						// If connection is successful . 
			    	  console.log(data);
			    	  main.GetData();							// Get up to date info from server the course list
			    	  main.listCourses('all');					// Refresh list to show all courses.
			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to data.php failed for Saving courses.");  
			  }
		});	
	
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
		

// Get data from the form --done
// Validate text field --done
// add data to an object --done
//ajax call to the data.php file
// run function to reload the notes.  


}



/***********************************************************************************************/ 
/*                					DISCUSSION FUNCTIONS 									   */
/***********************************************************************************************/

 Dscourse.prototype.listDiscussions=function()	 			  // Show a table of all discussions
 {
	 var main = this;
	 	
 	console.log(main.data.allDiscussions);

 	$('#tableBodyDiscussions').html(" "); 
 	
 	var i;
 	for (i = 0; i < main.data.allDiscussions.length; i++)
 	{		
 		var o = main.data.allDiscussions[i];
 		// If current user is instructor or TA
 		var userRole = main.UserCourseRole(o.dID, currentUserID); 
 		var buttonShow;
 		if(userRole == 'instructor' || userRole == 'TA'){
 			buttonShow = "<button id='" + o.dID + "' class='btn btn-info editDiscussion'>Edit</button>"
 		} else {
	 		buttonShow = ""; 
 		}
 		
		$('#tableBodyDiscussions').append(
		    	  		  "<tr>"
		    	  		+ "<td> <a class='discussionLink' discID='" + o.dID + "'> " + o.dTitle			+ " </a></td>" 
			            + "<td>  " + main.listDiscussionCourses(o.dID) +"</td>" 
			            + "<td> " + o.dStartDate		+ "</td>" 
			            + "<td> " + o.dEndDate + "</td>" 
				        + "<td> " + buttonShow + "</td>"
			            + "</tr>" 
		    	  	);
		
		
	}
	
 }

 Dscourse.prototype.listCourseDiscussions=function(cid, action)	 			  // Listing Discussions of a course with the given cid. 
 {
	 var main = this;
	 
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
	
	for (j = 0; j < main.data.allDiscussions.length; j++)
	{
		m = main.data.allDiscussions[j]; 		
		for(k = 1;  k < n.length; k++)
		{ 
			
			if (m.dID == n[k]) {
			var totalPosts = main.TotalPostNumber(m.dID); 
			
					$('#courseDiscussionsBody').append(
			    	  		  "<tr>"
			    	  		+ "<td> <a class='discussionLink' discID='" + m.dID + "'> " + m.dTitle			+ " </a></td>" 
				            + "<td>Ongoing</td>" 					// Needs to chech if discussin is open to individual, closed, open to all etc. Gets this info from the dates.  
				            + "<td>  "  + totalPosts + "  </td>" 
				            + "</tr>" 
			    	  	);
			 
			 }
		}	
		
	}	
	
 }

 Dscourse.prototype.TotalPostNumber=function(discussionID)	 			  // Get total post numbers
 {
	var main = this;
	
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
			console.log(courseID);
		});
				
		var dCoursesString = dCourses.toString(); 

		console.log(dCoursesString);
		
			dCourses.length = 0;											// Empty the array for reuse
			
			var dTitle 		= $('#discussionQuestion').val();				// Populates the fields needed for the database. 
			var dPrompt		= $('#discussionPrompt').val();
			var dStartDate  = $('#discussionStartDate').val();
			var dOpenDate  	= $('#discussionOpenDate').val();
			var dEndDate  	= $('#discussionEndDate').val();
			
		
							
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
								return courseNameString;
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
				$('#discussionStartDate').val(o.dStartDate);
				$('#discussionOpenDate').val(o.dOpenDate);
				$('#discussionEndDate').val(o.dEndDate);
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
				o.dStartDate  = $('#discussionStartDate').val();
				o.dOpenDate  	= $('#discussionOpenDate').val();
				o.dEndDate  	= $('#discussionEndDate').val();
				
				console.log('The id is: ' + o.dID);
				var dCourses = [];
							
				$('.dCourseList').each(function(index) {
					var courseID = $(this).attr('id');
					dCourses.push(courseID);
					console.log(courseID);
				});
						
				o.dCoursesString = dCourses.toString(); 
		
				
				dCourses.length = 0;
			
				main.saveDiscussions();
				$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification

			}	
		}	 
	 
 }
 
 

Dscourse.prototype.saveDiscussions=function()	 	// Save Discussion
 {
	var main = this;

	$.ajax({												// Ajax talking to the saveDiscussions.php file												
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				discussions: main.data.allDiscussions,							// All discussion data is sent
				action: 'saveDiscussions'							
			},
			  success: function(data) {							// If connection is successful . 
			    	  console.log(data);
			    	  main.GetData();							// Get up to date info from server the discussion list
			    	  main.listDiscussions();						// Refresh list to show all discussions.
			    	  
			    	  main.saved('Everything saved! ') 				// Remove save button and send save success message 
			    	  $('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
			    	  main.ClearDiscussionForm();
			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("dscourse Log: the connection to saveCourses.php failed.");  
			  }
		});	
	
}


 Dscourse.prototype.SingleDiscussion=function(discID)	 			  // View for the Individual discussions. 
 {
	    var main = this;
	 	$('.levelWrapper[level="0"]').html('');

 		var i;
	 	for (i = 0; i < main.data.allDiscussions.length; i++){
	 		o = main.data.allDiscussions[i];
	 		if(o.dID == discID){
	 			$('#dTitleView').html(o.dTitle);
	 			console.log('D Title :' + o.dTitle);
	 			$('#dPromptView').html('<b> Prompt: </b>' + o.dPrompt);
	 			$('#dIDhidden').val(o.dID);
	 			var dCourse = main.listDiscussionCourses(discID); 
	 			$('#dCourse').html('<b> Course: </b>' + dCourse);
	 			$('#dSDateView').html('<b> Start Date: </b>' + o.dStartDate);
	 			$('#dODateView').html('<b> Open to Class: </b>' + o.dOpenDate);
	 			$('#dCDateView').html('<b> End Date: </b>' + o.dEndDate);
	 			$('#refreshDiv').html('<a href="index.php?page=discussion&idisc=' + o.dID+ '" id="refreshLink" class="btn btn-small btn-success"> <span class="typicn refresh "></span>  Refresh </a>')
	 			main.CurrentDiscussion = o.dID;	
	 			console.log("Curent Discussion ID: " + main.CurrentDiscussion);
	 			
	 			// Get Discussion Status, can be one of three: all, student, closed.
	 			var dStatus = main.DiscDateStatus(o.dID);				 
	 			console.log('Discussion status is: ' + dStatus);
	 			
	 			// Note for the page
	 			$('#discStatus').removeClass('alert-error alert-warning alert-success').html(''); 
	 			switch(dStatus)									
					{
					case 'all':
						$('#discStatus').addClass('alert-success').html('This discussion is open to group participation');
						break;
					case 'student':
					  $('#discStatus').addClass('alert-warning').html('This discussion is in individual participation mode.');
					  break;
					case 'closed':
					  $('#discStatus').addClass('alert-error').html('This discussion is not open to participation at this time.');
					  break;
		  
					}
	 			
	 			// What is the role of the current user for this discussion?
	 			var userRole = main.UserCourseRole(o.dID, currentUserID);
	 			console.log('User role is: ' + userRole); 
	 			
	 			// Draw up posts and timeline
	 			main.ListDiscussionPosts(o.dPosts, dStatus, userRole, o.dID);
	 			
	 			if(dStatus == 'all' || dStatus == 'closed') {
	 				main.DrawTimeline(o.dPosts);
	 			}
	 		}
	 	
	 	}	
	 	setInterval(function(){main.CheckNewPosts(discID)},5000);
 
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
			
					
					
			// Show the value
			var initialDate = main.FormattedDate(main.timelineMax);
			$( "#amount" ).val(initialDate);	
			
			// Draw the dots. 
			
				 var discPosts = posts.split(",");
				 	 	 
				 var i, j, p, d;
				 for(i = 0; i < discPosts.length; i++){							// Take one post at a time
					 p = discPosts[i];
					 for (j = 0; j < main.data.allPosts.length; j++){			// Go through all the posts
						 d = main.data.allPosts[j];		
						 	 
						 if(d.postID == p){										// Find the post we want to get the details of 
			
							 	 //add dot on the timeline for this post
							 	  var n = d.postTime; 
							 	  var time = Date.parse(n);
				
								var timeRange = main.timelineMax-main.timelineMin;
								var dotDistance = ((time-main.timelineMin)*100)/timeRange;
								var singleDotDiv = '<div class="singleDot" style="left: ' + dotDistance + '%; "></div>'; 
								$('#dots').append(singleDotDiv); 

			
			
						}
					  }
				  }

}	


Dscourse.prototype.DiscDateStatus=function(dID)	 			  // Draw the timeline. 
 {
	    var main = this;
	    var dStatus;
	    console.log('Status dID is: ' + dID);
	    	    
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
			    
			    if(currentDate >= beginDate && currentDate <= endDate) {			// IF today's date bigger than start date and smaller than end date? 
			    	if(currentDate <= openDate) {    // If today's date smaller than Open Date
			    		dStatus = 'student'; // The status is open to individual contribution
			    	} else { 
			    		dStatus = 'all'; // The status is open to everyone
			    	}
			    } else {   		
			     		dStatus = 'closed'; // The status is closed.
			     }
			     
			     console.log(
			     	" - Begin Date: " + beginDate + 
			     	" - Open Date: " + openDate + 
			     	" - End Date: " + endDate + 
			     	" - Current Date: " + currentDate  
			     )
			     return dStatus; 
	     
	 		}
	 	}
   
 }

Dscourse.prototype.UserCourseRole=function(dID, userID)	 			  // Draw the timeline. 
 {
	    var main = this;
	    var userRole = 'unrelated'; 

		var j, k,l, listInst, listTAs, listStudents, discussions;
			for(j = 0; j < main.data.allCourses.length; j++){		// Loop through courses
				k = main.data.allCourses[j];
				
				discussions = k.courseDiscussions.split(",");		// For each course take the discussions it has
				
				for(l = 0; l < discussions.length; l++){			// Loop through course discussions
				
					if(discussions[l] == dID){						// Check if this discussion is part of that course 
				
						listInst = k.courseInstructors.split(",");	// Check if the user is among the instructors
						var m;
						for (m = 0; m < listInst.length; m++){
							if(listInst[m] == userID){
								userRole = 'instructor'; 
							}
						}
						
						listTAs = k.courseTAs.split(",");			// Check if the user is among the TAs
						var n;
						for (n = 0; n < listTAs.length; n++){
							if(listTAs[n] == userID){
								userRole = 'TA'; 
							}
						}
				
				
						listStudents = k.courseStudents.split(",");	// Check if the user is among the Students
						var p;
						for (p = 0; p < listStudents.length; p++){
							if(listStudents[p] == userID){
								userRole = 'student'; 
							}
						}
					}			
				}
				
			}
	   return userRole; 		// This is what we need from this function.  
	    
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
		console.log('Post id : ' + postFromId);
		
		// author ID -- postAuthorId -- this is the session user
		var postAuthorId = $('#userIDhidden').val();	
		console.log('Author Id : ' + postAuthorId);
		
		// message -- postMessage
		var postMessage = ''; 
		postMessage = $('#text').val();	
		console.log('Post message : ' + postMessage);

		// type -- postType
		var postType = 'comment';	
		var formVal = $('#postTypeID > .active').attr('id');
		
		if(formVal !== undefined){
			postType = formVal;
		} 
		console.log('Post id : ' + postType);
	
		// locationIDhidden -- postSelection 
		var postSelection = $('#locationIDhidden').val();
	
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
			    	  //console.log('Returned from addpost() : ' + data);
			    	  post.postID = data; 
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

			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to data.php failed.");  
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
	 $('#recentContent').html(' ');
	 $('#participantList').html(' ' );
	 
	 var discPosts = posts.split(",");
	 	 	 
	 var i, j, p, d, q, typeText, authorID, message, authorThumb;
	 for(i = 0; i < discPosts.length; i++){							// Take one post at a time
		 p = discPosts[i];
		 for (j = 0; j < main.data.allPosts.length; j++){			// Go through all the posts
			 d = main.data.allPosts[j];		
			 	 
			 if(d.postID == p){										// Find the post we want to get the details of 

							 
				 
							 
							 /********** TIMELINE ***********/ 
							 var n = d.postTime; 
							 console.log('Post time is: ' + n); 
							 var time = Date.parse(n);				// Parsing not working for firefox. 
							 //var time = new Date(n); 
							 console.log('Time: ' + time);
							 
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
								default:
								  typeText = ' <span class="typicn message "></span>';
								}
			
			
							 if(d.postMessage != ' '){									// Proper presentation of the message URL
								message = d.postMessage ; 
								//message = main.showURL(message);
								message =message.replace("\n","<br /><br />");
							 } else {
								 message = '';
								 
							 }
							 
							 console.log('author id: ' + authorID + ' - typetext: ' + typeText + ' - message: ' + message);	// Check to see if all is well
							 
							 var topLevelMessage = ' ';								// Assign a class for top level messages for better organization.
							 if (d.postFromId == '0'){
								 topLevelMessage = 'topLevelMessage'; 
							 }
							 
							 
							 // Check if this post has media assigned. 
							 var media = ''; 
							 if(d.postMedia.length > 1){
							 	media = '<span href="#" rel="tooltip" title="This post has media attachment. Click to view." class="mediaMsg"> ' + d.postMediaType + '  <span class="typicn tab "></span> </span> ';
							 }
							 
							 
							 var showPost = 'yes';
							 var userRoleAuthor = main.UserCourseRole(discID, d.postAuthorId);  
							 console.log('User role of the author of this post: ' + userRoleAuthor); 
							 if(dStatus == 'student' && currentUserID != d.postAuthorId && userRoleAuthor == 'student'){
							 	 	if(userRole == 'student' || userRole == 'unrelated'){
							 	 		showPost = 'no'; 
							 	 		}
							 }
							 

							 
							 if(showPost == 'yes') {

							 	var selector = 'div[level="'+ d.postFromId +'"]';
							 	var responses = main.ResponseCounters(d.postID);	
							 
								 $(selector).append(						// Add post data to the view
								 	  '<div class="threadText ' + topLevelMessage +'" level="'+ d.postID + '" postTypeID="'+ d.postType+ '" postAuthorId="' + d.postAuthorId + '" time="' + time + ' ">' 
								 	+  '<span class="postTypeView"> ' + typeText + '</span>'
								 	+  '<span class="postTextWrap">' 
								 	+  '<span class="postAuthorView" rel="tooltip"  title="' + authorThumb + '"> ' + authorID + '</span>'
								 	+  '<span class="postMessageView"> ' + message  + '</span>'
								 	+ media
								 	+ ' <div class="sayBut2" postID="'+ d.postID + '">say</div> '
								 	+  '</span>'	
								 	
								 	+ '<div id="responseWrap" >' + responses + '</div>' 
								 	
								 	+ ' </div><div style="clear: both;"></div>'
								 );
							 
							 
								 /********** RECENT ACTIVITY SECTION ***********/
								 var range = discPosts.length-6; 			// How many of the most recent we show + 1
								 var prettyTime = main.PrettyDate(d.postTime);
								 var shortMessage = main.truncateText(message, 100);
								 if(i > range) {					 // person + type + truncated comment + date
									 var activityContent = '<li>' + main.getAuthorThumb(d.postAuthorId, 'tiny') + ' ' + authorID + ' ' + typeText + ' <b>' + shortMessage + '</b> ' + '<em class="timeLog">' + prettyTime + '<em></li> ';
									 $('#recentContent').prepend(activityContent);   
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
			 $('#heatmapButtons').append(output); 
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
				 console.log(num1 + ' ' + num2);
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
	 

	 // get postFromID
	 
	 // Find the selection in the postfromID text
	 // Replace the selection with <span class="highlight"> text </span>
	 
	 
}

Dscourse.prototype.Heatmap=function(type, action)					// Highlights the relevant sections of host post when hovered over 
{
	 var main = this;

	 var selector = '.threadText[posttypeid="' + type + '"]'; 
	 var typeColor;
	
		if(action == 'add'){ 
	 				switch(type)									// Get what kind of post this is 
							{
							case 'agree':
							  typeColor = '#efffef';
							  break;
							case 'disagree':
							  typeColor = '#ffebeb';
							  break;
							case 'clarify':
							  typeColor = '#ffd6ff';
							  break;
							case 'offTopic':
							  typeColor = '#fff4e1';
							  break;		  
							default:
							  typeColor = '#e9e9ff';
							}
				$(selector).css('background-color', typeColor);
			 } else if (action == 'remove'){
				 		$(selector).css('background-color', '#fff');

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


 Dscourse.prototype.CheckNewPosts=function(currentDisc)					// Highlights the relevant sections of host post when hovered over 
{
	 var main = this;

		var i, posts;
	 	for (i = 0; i < main.data.allDiscussions.length; i++)
	 	{		
	 		var o = main.data.allDiscussions[i];
	 		if(o.dID == currentDisc ){
			 	posts = o.dPosts;
			 	
			 		
					$.ajax({																							
						type: "POST",
						url: "scripts/php/data.php",
						data: {
							currentDiscussion: currentDisc,
							currentPosts: 	posts,						
							action: 'checkNewPosts'							
						},
						  success: function(data) {						// If connection is successful . 
								if(data > 0){
									$('#checkNewPosts').addClass('animated flash');
									$('#checkNewPosts').html('<div class="alert alert-success">    <button id="hideRefreshMsg"type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><span class="typicn refresh " style="font-size: 15px; font-weight: bold;"></span> There are <b>' + data + '</b> new posts. Please refresh the page. </span>'); 
									console.log('Refresh message, total new posts is: ' + data);
								} else {
									console.log('Nothing new under the sun...');
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
  
	    console.log('spittin out: ' + text);
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

Dscourse.prototype.unsaved=function(message)
{
	var main = this;
	
	$('#saveCourses').show();
	$('#saveMessage').html(message).css('color', 'red');
	main.saveStatus = "unsaved"; 	

}

Dscourse.prototype.saved=function(message)
{
	var main = this;
	
	$('#saveCourses').hide();
	$('#saveMessage').html(message).css('color', 'green');
	main.saveStatus = "saved";
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
	
	var myString = text;
	var myTruncatedString = myString.substring(0,length) + '... ';
	return myTruncatedString;
	
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

Dscourse.prototype.TypeAhead=function()
{

 	var main = this; 
 	
 			
			$( "#coursePeople" ).autocomplete({
						minLength: 0,
						source: dscourse.nameList,
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
						source: dscourse.courseList,
						focus: function( event, ui ) {
							$( "#discussionCourses" ).val( ui.item.label );
							return false;
						},
						select: function( event, ui ) {
							console.log('value is: ' + ui.item.value + ' and text is: ' + ui.item.label);
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



Dscourse.prototype.Trace=function(variable)
{
	
	console.log('Dscourse log:: ' + variable + ' = ["variable"]');
	

}








