/*
 *  Views.js 
 * 
 * Contains jquery and javascript code for viewing different parts of the content, navigation etc. 
 * 
 * 
 */



$(document).ready(function() {										// Wait for everything to load. 

	
	/************ Navigations  ******************/
	showHome();
	
	var linkID;	
	$('.nav > li > a').live('click', function () {					// Show page contents depending on what link was clicked. 
		linkID = $(this).attr('id');
		$('.page').hide();	
		
		switch(linkID)
		{
		case 'usersNav':
		  $('#usersPage').show();
		  break;
		case 'coursesNav':
		  $('#coursesPage').show();
		  break;
		case 'discussionsNav':
		  $('#discussionsPage').show();
		  break;
		case 'profileNav':
		  var userid = $(this).attr('userid');
		  dscourse.UserProfile(userid);
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
		  $('#profilePage').show();
		  $('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification

	});    
	
	$('.courseLink').live('click', function () {						// Home link
		$('.page').hide();
		var courseid = $(this).attr('courseid');
		dscourse.getCourse(courseid);
		$('#coursePage').show();
		$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification

	});

	$('.discussionLink').live('click', function () {						// Home link
		$('.page').hide();
		$('#footerFixed').hide();
		$('#discussionWrap').show();
		$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification

	});
	
		
	function showHome(){
		$('.page').hide();			
		$('#homePage').show();
		viewUsers();
	}


	/************ Discussions  ******************/

	$('#discussionForm').hide();
	$('#addDiscussionView').addClass('linkGrey');

	$("#discussionStartDate").datepicker({ dateFormat: "yy-mm-dd" });			// Date picker jquery ui initialize for the date fields
	$("#discussionEndDate").datepicker({ dateFormat: "yy-mm-dd" });


});


/************ USERS  ******************/
		
	$('#addUserButton').live('click', function() {  		// Add user when Form is submitted
			addUser(); // Call the function
			viewUsers ();										// Refresh the list of users
		});
	
	$("#addUserLink").live('click', function() {					// User button and click events 
		$('#userListLink').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$("#userList").fadeOut();
		$("#addUserForm").delay(200).fadeIn();
		$('#userButtonDiv').html('<button class="btn btn-primary" id="addUserButton">Add User</button>');
		clearUserForm();	 // Fields are emptied to reuse
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
		$('#userButtonDiv').html('<button class="btn btn-primary" id="updateUser">Update User Information</button>');
		var courseID = $(this).attr("id");						// Get the id for this course. 
		editUser(courseID);										// Edit the course with the specific id. 	
		$('html, body').animate({scrollTop:0});					// The page scrolls to the top to see the notification
	});		
	
	$('#updateUser').live('click', function() {  				// When edit button is clicked. 	
		updateUser();											// Edit the course with the specific id.
		viewUsers();
		$("#addUserForm").fadeOut();
		$("#userList").delay(200).fadeIn();
		
		$('html, body').animate({scrollTop:0});					// The page scrolls to the top to see the notification
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



/************ COURSES ******************/


	$('#allCoursesView').live('click', function() {  				// View all courses 	
		dscourse.listCourses('all');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		clearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#activeCoursesView').live('click', function() {  				// View all active courses 	
		dscourse.listCourses('active');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		clearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#archivedCoursesView').live('click', function() {  				// View all archived courses 	
		dscourse.listCourses('archived');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		clearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#courseFormLink').live('click', function() {  				// View all archived courses 	
		$('#courseForm').find('input:text, input:password, input:file, select, textarea').val(''); // Fields are emptied to reuse
		$('#addPeopleBody').html('');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courses').fadeOut();
		$('#courseForm').delay(200).fadeIn();
		$('#courseButtonDiv').html('<button class="btn btn-primary" id="courseFormSubmit">Add Course</button>');
	});
	
	$('#courseFormSubmit').live('click', function() {  				// View all archived courses 	
		dscourse.addCourse();
		clearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#saveCourses').live('click', function() {  				// View all archived courses 	
		dscourse.saveCourses();
	});
	
	$('.editCourse').live('click', function() {  				// When edit button is clicked. 	
		$('#courses').fadeOut();
		$('#courseForm').delay(200).fadeIn();
		$('#courseButtonDiv').html('<button class="btn btn-primary" id="updateCourse">Update Course Information</button>');
		
		var courseID = $(this).attr("id");						// Get the id for this course. 
		dscourse.editCourse(courseID);									// Edit the course with the specific id. 	
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification

	});			

	$('#updateCourse').live('click', function() {  				// When edit button is clicked. 			
		dscourse.updateCourse();
		dscourse.saveCourses();									// Edit the course with the specific id. 	
		dscourse.listCourses('all');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		$('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
		clearCourseForm(); // Fields are emptied to reuse
		
	});	
	
	$('#roleButtons').button();
	$('.removePeople').live('click', function() {
				$(this).closest('tr').remove();
	});



/************ DISCUSSIONS ******************/


	$('#discussionFormSubmit').live('click', function() {  					
			dscourse.addDiscussion();
			clearDiscussionForm(); // Fields are emptied to reuse
		});


	$('#discussionQuestion').live('change', function() {  			  	
			checkDiscussionQuestion();
	});

	$('#discussionPrompt').live('keyup', function() {  			  	
			CheckDiscussionPrompt();
	});
	
	$('#allDiscussionView').live('click', function() {  					
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#discussionForm').fadeOut();
		$('#discussions').delay(300).fadeIn();
		clearDiscussionForm(); // Fields are emptied to reuse
	});
	
	$('#addDiscussionView').live('click', function() {  					
		clearDiscussionForm();
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#discussions').fadeOut();
		$('#discussionForm').delay(200).fadeIn();
	});

		$('.editDiscussion').live('click', function() {  				 	
		clearDiscussionForm();
		var dID = $(this).attr('id');
		$('small a').addClass('linkGrey');
		$('#discussions').fadeOut();
		$('#discussionForm').delay(200).fadeIn();
		dscourse.editDiscussion(dID);
	});
	
	$('.removeCourses').live('click', function() {
		  			$(this).closest('tr').remove();
		  });