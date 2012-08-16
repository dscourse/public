/*
 *  All Course related code
 */

function Dscourse () 
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
	
	this.currentSelected = '';  	// Needed for selection
	this.currentStart = '';
	this.currentEnd = ''; 
	
	this.uParticipant = new Array; 	// Unique list of participants. 
	
	// timeline
	this.timelineMin = 0;
	this.timelineMax = 0;
	
	// Fix for multiple image uploads 
	this.imgUpload = '';
	
	
	
	// Get all Data

	this.GetData();


	// All view related actions that need to be loaded in the beginning
	var top = this;
	
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
			  dscourse.imgUpload = 'user';
			  break;
			case 'coursesNav':
			  $('#coursesPage').show();
			  dscourse.imgUpload = 'course';
			  break;
			case 'discussionsNav':
			  $('#discussionsPage').show();
			  $('#allDiscussionView').removeClass('linkGrey');
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
	
			  window.onbeforeunload = confirmExit;
			  function confirmExit()
			  {
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

		$('.threadText').live('click', function (event) {
				//	event.preventDefault();
				event.stopPropagation();
				$('.threadText').removeClass('highlight');
				$('.threadText').find('span').removeClass('highlight');
				var postClickId = $(this).closest('div').attr('level'); 
				dscourse.HighlightRelevant(postClickId);
				$(this).addClass('highlight');
			});
		

		
		$('.dCollapse > h4').live('click', function () {
			$(this).parent().find('.content').fadeToggle();
		});

		
		
		
		
	
	});

/************ User Events  ******************/
		
	
	$("#addUserLink").live('click', function() {					// User button and click events 
		$('#userListLink').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$("#userList").fadeOut();
		$("#addUserForm").delay(200).fadeIn();
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
		updateUser();											// Edit the course with the specific id.
		dscourse.GetData();
		$("#addUserForm").fadeOut();
		$("#userList").delay(200).fadeIn();
		
		$('html, body').animate({scrollTop:0});					// The page scrolls to the top to see the notification
	});	
	
	$('#cancelUser').live('click', function() {  
		//dscourse.GetData();
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



/************ Course Events ******************/


	$('#allCoursesView').live('click', function() {  				// View all courses 	
		top.listCourses('all');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		top.ClearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#activeCoursesView').live('click', function() {  				// View all active courses 	
		top.listCourses('active');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		top.ClearCourseForm(); // Fields are emptied to reuse
	});
	
	$('#archivedCoursesView').live('click', function() {  				// View all archived courses 	
		top.listCourses('archived');
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#courseForm').fadeOut();
		$('#courses').delay(300).fadeIn();
		top.ClearCourseForm(); // Fields are emptied to reuse
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
		top.addCourse();
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
		$('small a').addClass('linkGrey');
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


	$('#discussionQuestion').live('change', function() {  			  	
			checkDiscussionQuestion();
	});

	$('#discussionPrompt').live('keyup', function() {  			  	
			checkDiscussionPrompt();
	});
	
	$('#allDiscussionView').live('click', function() {  					
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#discussionForm').fadeOut();
		$('#discussions').delay(300).fadeIn();
		top.ClearDiscussionForm(); // Fields are emptied to reuse
	});
	
	$('#addDiscussionView').live('click', function() {  					
		top.ClearDiscussionForm();
		$('small a').addClass('linkGrey');
		$(this).removeClass('linkGrey');
		$('#discussions').fadeOut();
		$('#discussionForm').delay(200).fadeIn();
	});

		$('.editDiscussion').live('click', function() {  				 	
		top.ClearDiscussionForm();
		var dID = $(this).attr('id');
		$('small a').addClass('linkGrey');
		$('#discussions').fadeOut();
		$('#discussionForm').delay(200).fadeIn();
		top.editDiscussion(dID);
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
			$('#commentWrap').fadeOut('fast');
			$('#overlay').hide();
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
		$('#highlightDirection').hide();
		$('#highlightShow').hide();
		var postQuote = $(this).parent().children('.postMessageView').html();
		postQuote = $.trim(postQuote);
		console.log('postquote: ' + postQuote);
		var xLoc = 30; //e.pageX; 
		var yLoc = e.pageY-75; 
		$('#commentWrap').css({'top' : yLoc, 'left' : '50%'});
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
		$(this).parent().addClass('highlight');
		$('#text').val('Your comment...');

	});
	
	$('#postCancel').live('click', function () {
		$('.threadText').removeClass('highlight');		
		$('#commentWrap').fadeOut('fast');
		$('#overlay').hide();
		top.ClearPostForm();
		
	});

	$('#overlay').live('click', function () {
		$('.threadText').removeClass('highlight');		
		$('#commentWrap').fadeOut('fast');
		$('#overlay').hide();
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
				var parentDiv = $(this).parent('div');
				$(parentDiv).children(currentType).fadeToggle('fast', function() {

						  });
				if(thisLink.hasClass('grey-icons') == true){
					  console.log('has grey icons.'); 
					   thisLink.removeClass('grey-icons');
				   } else {
					  console.log('Does not have grey icons.'); 
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
						$(this).html('<span class="typicn group "></span>  Show Participants ');
					} else {
						$(this).addClass('active');	
						$(this).html('<span class="typicn group "></span>  Hide Participants ');
					}
			});
		
		$('#media').live('click', function () {
				$('#mediaBox').slideToggle();			
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
						$('.levelWrapper').css( 'zoom', '+=0.2' );
					} else if(zoomType == 'out') {
						$('.levelWrapper').css( 'zoom', '-=0.2' );
					} else if(zoomType == 'reset'){
						$('.levelWrapper').css( 'zoom', '1.0' );

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
				$('.drawTypes').removeClass('active');
				var drawType = $(this).attr('id'); 
				Draw(drawType);
				$(this).addClass('active');
			});
			
		$('#Edit').live('click', function () {						// User heatmap buttons and functions
				shivaLib.Annotate();
				var offset = $('#mediaWrap').offset();
				var leftP = offset.left + 'px'; 
				var topP = offset.top + 'px'; 
				$('#shivaDrawDiv').css({'top' : topP, 'left' : leftP});
			});

} /* end function Dscourse



/***********************************************************************************************/ 
/*                						DATABASE FUNCTIONS 									   */
/***********************************************************************************************/ 

Dscourse.prototype.GetData=function(action)
{
	// Get all data and populate in Json -- For courses and discussions, not for users

	
	var main = this;
	
	if(!action) {
		var action = 'getAll'; 
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

	$('#userData').html(" ");
	var i, o; 
	for(i = 0; i < main.data.allUsers.length; i++ ){	// If view is not specified Construct the table for each element
		o =  main.data.allUsers[i] ;  			
			$('#userData').append(
			    	  		  "<tr>"
			    	  		+ "<td> <img class='userThumbSmall' src='" + o.userPictureURL +"' /><a class='showProfile' userid='" + o.UserID + "'>" + o.firstName + "</a></td>" 
				            + "<td> " + o.lastName	+ "</td>" 
				            + "<td> " + o.username		+ "</td>" 
				            + "<td> " + o.sysRole	+ "</td>" 
				            + "<td> " + o.userStatus		+ "</td>"
				            + "<td> <button id='" + o.UserID		+ "' class='btn btn-info editUser'>Edit</button></td>"
				            + "</tr>" 
			    );
			    	  	nameListName = { ID: o.UserID, Name : o.firstName + " " + o.lastName, Email : o.username}; 
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
			    	  			    		
			    		
		    			    			
		    	  	$('#tablebody').append(
		    	  		  "<tr>"
		    	  		+ "<td> <a class='courseLink' courseid='" + o.courseID + "'> " + o.courseName			+ "</a></td>" 
			            + "<td> " + o.courseDescription	+ "</td>" 
			            + "<td> " + o.courseStatus		+ "</td>" 
			            + "<td><strong> " + fullName	+ "</strong><br/>" + TAName +" </td>" 
			            + "<td> " + stuNum + "</td>"
				        + "<td> <button id='" + o.courseID		+ "' class='btn btn-info editCourse'>Edit</button></td>"
			            + "</tr>" 
		    	  	);
	    	  

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
			    	  			    		
			    		
		    			    			
		    	  	$('#tablebody').append(
		    	  		  "<tr>"
		    	  		+ "<td> <a class='courseLink' courseid='" + o.courseID + "'> " + o.courseName			+ "</a></td>" 
			            + "<td> " + o.courseDescription	+ "</td>" 
			            + "<td> " + o.courseStatus		+ "</td>" 
			            + "<td><strong> " + fullName	+ "</strong><br/>" + TAName +" </td>" 
			            + "<td> " + stuNum + "</td>"
				        + "<td> <button id='" + o.courseID		+ "' class='btn btn-info editCourse'>Edit</button></td>"
			            + "</tr>" 
		    	  	);
		    	  		
		    	  		courseListCourse = { ID: o.courseID, Name : o.courseName}; 
			    	  	main.courseList.push(courseListCourse);
	    		}
	     }
		
	}
	
					   $('#discInputDiv').html('<input type="text" class="input-large discussionCourses" id="discussionCourses" name="discussionCourses" >');
					   console.log(main.courseList);
					   $('.discussionCourses').typeahead({
							source: main.courseList,				// The source, 
							matchProp: 'Name',							// Match to this
							sortProp: 'Name',							// Sort by 
							valueProp: 'ID',							// The content of the val variable below comes from this attribute
							itemSelected: function(item, val, text) {
								console.log('value is: ' + val + ' and text is: ' + text);
								$('#addCoursesBody').append('<tr id="' + val + '" class="dCourseList"><td>' + text + ' </td><td><button class="btn removeCourses" >Remove</button>	</td></tr>'); 				// Build the row of courses. 
								$('.discussionCourses').val(' ').focus();
							}
						});
		
 
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
			main.saved('Your new course is added.');
			$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification


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
					$('#imgPath').html('<img src="' + o.courseImage + '" alt="course image"/>');
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
			url: "scripts/php/saveCourses.php",
			data: {
				courses: main.data.allCourses							// All course data is sent
											
			},
			  success: function(data) {						// If connection is successful . 
			    	  console.log(data);
			    	  main.GetData();							// Get up to date info from server the course list
			    	  main.listCourses('all');					// Refresh list to show all courses.
			    	  
			    	  main.saved('Everything saved! ') 			// Remove save button and send save success message 
			    	  $('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
					$('#courseForm').find('input:text, input:password, input:file, select, textarea').val(''); // Fields are emptied to reuse
					$('#addPeopleBody').html('');
			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to saveCourses.php failed.");  
			  }
		});	
	
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
		$('#tableBodyDiscussions').append(
		    	  		  "<tr>"
		    	  		+ "<td> <a class='discussionLink' discID='" + o.dID + "'> " + o.dTitle			+ " </a></td>" 
			            + "<td>  " + main.listDiscussionCourses(o.dID) +"</td>" 
			            + "<td> " + o.dStartDate		+ "</td>" 
			            + "<td> " + o.dEndDate + "</td>" 
				        + "<td> <button id='" + o.dID + "' class='btn btn-info editDiscussion'>Edit</button></td>"
			            + "</tr>" 
		    	  	);
		
		
	}
	
 }

 Dscourse.prototype.listCourseDiscussions=function(cid)	 			  // Listing Discussions of a course with the given cid. 
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
			var dEndDate  	= $('#discussionEndDate').val();
			
		
							
		discussion = {
				'dTitle': dTitle,
				'dPrompt': dPrompt,
				'dStartDate': dStartDate,
				'dEndDate': dEndDate,
				'dCourses': dCoursesString
			};
			
			main.data.allDiscussions.push(discussion);
			main.saveDiscussions();
			main.saved('Your discussion is created.');
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
				$('#discussionEndDate').val(o.dEndDate);
			 	
			 	
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
				o.dEndDate  	= $('#discussionEndDate').val();
				
				var dCourses = [];
							
				$('.dCourseList').each(function(index) {
					var courseID = $(this).attr('id');
					dCourses.push(courseID);
					console.log(courseID);
				});
						
				o.dCoursesString = dCourses.toString(); 
		
				
				dCourses.length = 0;
			
				main.saveDiscussions();
				main.saved('Your discussion was saved.');
				$('html, body').animate({scrollTop:0});			// The page scrolls to the top to see the notification

			}	
		}	 
	 
 }
 
 

Dscourse.prototype.saveDiscussions=function()	 	// Save Discussion
 {
	var main = this;

	$.ajax({												// Ajax talking to the saveDiscussions.php file												
			type: "POST",
			url: "scripts/php/saveDiscussions.php",
			data: {
				discussions: main.data.allDiscussions							// All discussion data is sent
											
			},
			  success: function(data) {							// If connection is successful . 
			    	  console.log(data);
			    	  main.GetData();							// Get up to date info from server the discussion list
			    	  main.listDiscussions();						// Refresh list to show all discussions.
			    	  
			    	  main.saved('Everything saved! ') 				// Remove save button and send save success message 
			    	  $('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
			    	  top.ClearDiscussionForm();
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
	 			$('#dPromptView').html('<b> Prompt: </b>' + o.dPrompt);
	 			$('#dIDhidden').val(o.dID);
	 			main.CurrentDiscussion = o.dID;	
	 			console.log("Curent Discussion ID: " + main.CurrentDiscussion);
	 			main.ListDiscussionPosts(o.dPosts);
	 			main.DrawTimeline(o.dPosts);
	 		}
	 	
	 	}	 
}

Dscourse.prototype.DrawTimeline=function(posts)	 			  // Draw the timeline. 
 {
	    var main = this;

	    	// Create the Slider
		    $( "#slider-range" ).slider({					// Create the slider
				range: "min",
				step: 21600000,
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

		

/***********************************************************************************************/ 
/*                					     POST FUNCTIONS 									   */
/***********************************************************************************************/

Dscourse.prototype.AddPost=function(){
	
		 var main = this;
		 var currentDisc; 

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
	
	// Create post object and append it to allPosts
	
			post = {
				'postFromId': postFromId,
				'postAuthorId': postAuthorId,
				'postMessage': postMessage,
				'postType': postType,
				'postSelection': postSelection
			};
		
		
	// run Ajax to save the post object
	
	$.ajax({																						
			type: "POST",
			url: "scripts/php/posts.php",
			data: {
				post: post,							
				action: 'addPost'							
			},
			  success: function(data) {						// If connection is successful . 
			    	  console.log(data);
			    	  addPostDisc(data);
	

			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to posts.php failed.");  
			  }
		});	
	
	function addPostDisc(pID){
		// add post id to the relevant discussion section
		currentDisc = $('#dIDhidden').val();
		
		post.postID = pID; 
		
		var i;
	 	for (i = 0; i < main.data.allDiscussions.length; i++)
	 	{		
	 		var o = main.data.allDiscussions[i];
	 		if(o.dID === currentDisc ){
	
			 	if(o.dPosts){
				 	o.dPosts += ",";
			 	}
			 	o.dPosts += pID;

		 		
		 		$.ajax({												// Ajax talking to the saveDiscussions.php file												
					type: "POST",
					url: "scripts/php/saveDiscussions.php",
					data: {
						discussions: main.data.allDiscussions							// All discussion data is sent
													
					},
					  success: function(data) {							// If connection is successful . 
					    	console.log(data);
					    	main.data.allPosts.push(post); 
					    	$('.levelWrapper[level="0"]').html('');
					    	main.SingleDiscussion(currentDisc);
						    }, 
					  error: function() {					// If connection is not successful.  
							console.log("dscourse Log: the connection to saveDiscussions.php failed.");  
					  }
				});	
	

	 		}
	 	}
 	}
	
	
}


 Dscourse.prototype.ListDiscussionPosts=function(posts)	 			  // View for the Individual discussions. 
 {
	 var main = this;
	 
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
				 var time = Date.parse(n);
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
					  typeText = ' agreed: ';
					  break;
					case 'disagree':
					  typeText = ' disagreed: ';
					  break;
					case 'clarify':
					  typeText = ' asked to clarify: ';
					  break;
					case 'offTopic':
					  typeText = ' marked as off topic: ';
					  break;		  
					default:
					  typeText = ' commented: ';
					}
				 
				 if(d.postMessage != ' '){									// Proper presentation of the message URL
					message = d.postMessage ; 
					message = main.showURL(message);
				 } else {
					 message = '';
				 }
				 
				 console.log('author id: ' + authorID + ' - typetext: ' + typeText + ' - message: ' + message);	// Check to see if all is well
				 
				 var topLevelMessage = ' ';								// Assign a class for top level messages for better organization.
				 if (d.postFromId == '0'){
					 topLevelMessage = 'topLevelMessage'; 
				 }
				 
				 var selector = 'div[level="'+ d.postFromId +'"]';
				 var responses = main.ResponseCounters(d.postID);	
				 $(selector).append(						// Add post data to the view
				 	  '<div class="threadText ' + topLevelMessage +'" level="'+ d.postID + '" postTypeID="'+ d.postType+ '" postAuthorId="' + d.postAuthorId + '" time="' + time + ' ">' 
				 	+  '<span class="postAuthorView" rel="tooltip"  title="' + authorThumb + '"> ' + authorID + '</span>'
				 	+  '<span class="postTypeView"> ' + typeText + '</span>'
				 	+  '<span class="postMessageView"> ' + message  + '</span>'
				 	+ ' <div class="sayBut2" postID="'+ d.postID + '">say</div> '
				 	+ responses 
				 	+ ' </div>'
				 );
				 
				 
				 /********** RECENT ACTIVITY SECTION ***********/
				 var range = discPosts.length-9; 			// How many of the most recent we show + 1
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
				 
				 	
				 
			 }	 
		 }
	 }
	 
	 main.timelineValue = main.timelineMax;						// Set the timeline value to the max. 
	 main.UniqueParticipants();
}


Dscourse.prototype.UniqueParticipants=function(){					// Populate unique participants.  
	
		 var main = this;
		 var i, o, name, thumb, output; 
		 for(i = 0; i < main.uParticipant.length; i++){
			 o = main.uParticipant[i]; 
			 name = main.getName(o); 
			 thumb = main.getAuthorThumb(o, 'small'); 
			 output = '<li class="uList" rel="tooltip" title="' + name + '" authorID="' + o +  '">' + thumb + ' </li>'; 
			 $('#participantList').append(output); 
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
		  if(comment 	> 0){commentText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="comment" title="<b>Comments from: </b><br /> ' + commentPeople +'" > ' + comment 	+ '  <span class="typicn message "> </span></span>  ';} 
		  if(agree 	 	> 0){agreeText 		= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="agree" title="<b>People who agreed: </b><br /> ' + agreePeople + '"> ' + agree 	+ '  <span class="typicn thumbsUp "> </span> </span> '	 ;}
		  if(disagree	> 0){disagreeText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="disagree" title="<b>People who disagreed:</b><br /> ' + disagreePeople + '"> ' + disagree 	+ '  <span class="typicn thumbsDown "> </span></span> ';}
		  if(clarify 	> 0){clarifyText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="clarify" title="<b>People that asked to clarify:</b><br /> ' + clarifyPeople + '"> ' + clarify 	+ '  <span class="typicn unknown "> </span></span> ' ;}
		  if(offTopic 	> 0){offTopicText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="offTopic" title="<b>People that marked off topic: </b><br />' + offTopicPeople + '"> ' + offTopic 	+ '  <span class="typicn directions "> </span> </span>  ' ;}

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
					 	$(selector).children('.postMessageView').html(n);

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
		$('#imgPath').html('');
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












