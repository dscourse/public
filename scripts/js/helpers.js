/*
 *  Site wide helper functions, load this first. 
 */

function getUrlVars() {								// Gets parameters from url. Usage : var first = getUrlVars()["id"];
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function truncateText(text){
	var length = 120;
	var myString = text;
	var myTruncatedString = myString.substring(0,length) + '... ';
	return myTruncatedString;
	
}


/***************** Save status functions  *************************/

var saveStatus = "saved"; 							// Global variable for save status. To be used for when user wants to leave without saving. 

function unsaved(message) 							// Function for displaying messages when users have not saved changes. 
{
			$('#saveCourses').show();
			$('#saveMessage').html(message).css('color', 'red');
			saveStatus = "unsaved"; 
}

function saved(message)								// Function for displaying messages when changes have been saved. 
{
			$('#saveCourses').hide();
			$('#saveMessage').html(message).css('color', 'green');
			saveStatus = "saved";
}

/***************** Clear Forms  *************************/

function clearUserForm(){
		$('#addUserForm').find('input:text, input:password, input:file, select, textarea').val('');
		$('#imgPath').html('');
}

function clearCourseForm(){
		$('#courseForm').find('input:text, input:password, input:file, select, textarea').val('');
		$('#imgPath').html('');
		$('#addPeopleBody').html('');
}

function clearDiscussionForm(){
		$('#discussionForm').find('input:text, input:password, input:file, select, textarea').val('');
		$('#addCoursesBody').html('');
}

function clearPostForm(){
		$('#commentWrap').find('input:text, input:password, input:file, select, textarea').val('');
}