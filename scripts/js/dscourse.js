/*
 *  All Course related code
 */

function Dscourse () 
{


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
	this.courseDataStatus = 'empty';


// Discussions

	this.discussion = { }; 	
 
	this.courseList = [];
	this.courseListName = {};
	
	this.discussionDataStatus = 'empty';
	
// Posts
	this.post = { };

// Fix for multiple image uploads 
	this.imgUpload = '';
	
// Get all Data

	this.GetData();

}

Dscourse.prototype.GetData=function()
{
	// Get all data and populate in Json -- For courses and discussions, not for users
	
	var main = this;
	// Ajax call to get data and put all data into json object
		$.ajax({													// Add user to the database with php.
			type: "POST",
			url: "scripts/php/data.php",
			data: {
				action: 'getAll'
			},
			  success: function(data) {								// If addNewUser.php was successfully run, the output is printed on this page as notification. 
			  		main.data = data;
			  		main.listCourses('all');
			  		main.listDiscussions();						// Refresh list to show all discussions.
			  		main.ListUsers();
			  		
			  		main.courseDataStatus = 'loaded';
			  		console.log(data);
			  	}, 
			  error: function() {									// If there was an error
				 		console.log('There was an error talking to data.php');
			  }
		});	


}

Dscourse.prototype.ListUsers=function()
{
	var main = this;

	$('#userData').html(" ");
	var i, o; 
	for(i = 0; i < main.data.allUsers.length; i++ ){	// If view is not specified Construct the table for each element
		o =  main.data.allUsers[i] ;  			
			$('#userData').append(
			    	  		  "<tr>"
			    	  		+ "<td> <img class='userThumbS' src='" + o.userPictureURL +"' /><a class='showProfile' userid='" + o.UserID + "'>" + o.firstName + "</a></td>" 
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
			  	  	$('#profileWebsite').html(o.userWebsite);
			  	  	//$('#userStatus').html(singleUser.status);
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

/********** COURSES ****************/

Dscourse.prototype.getCourses=function()	// Gets course information
{	
	var main = this; 
	$.ajax({											// Ajax talking to the getCourses.php file												
			type: "POST",
			url: "scripts/php/getCourses.php",
			  success: function(data) {				// If connection is successful the data will be put into an object. 
			    	  main.allCourses = data;			// The data from php is now available to us as json object allCourses
			    	  main.courseDataStatus = 'loaded';
			    	  main.listCourses('all');				// run the function to show all courses.

			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to getCourses.php failed.");  
			  }
		});	
}



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
			            + "<td> " + truncateText(o.courseDescription)	+ "</td>" 
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
			            + "<td> " + truncateText(o.courseDescription)	+ "</td>" 
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
			saved('Your new course is added.');
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
			    	  
			    	  saved('Everything saved! ') 			// Remove save button and send save success message 
			    	  $('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
					$('#courseForm').find('input:text, input:password, input:file, select, textarea').val(''); // Fields are emptied to reuse
					$('#addPeopleBody').html('');
			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to saveCourses.php failed.");  
			  }
		});	
	
}



Dscourse.prototype.getName=function(id)
{
	var main = this;

	for(var n = 0; n < main.data.allUsers.length; n++){
		var userIDName = main.data.allUsers[n].UserID;
		if (userIDName == id)
			return main.data.allUsers[n].firstName + " " + main.data.allUsers[n].lastName;
	}	
}



Dscourse.prototype.getEmail=function()
{
	var main = this;

	for(var n = 0; n < main.data.allUsers.length; n++){
		var userIDName = main.data.allUsers[n].UserID;
		if (userIDName == id)
			return main.data.allUsers[n].username;
	}	
}


/********** DISCUSSIONS ****************/

Dscourse.prototype.getCourseList=function()				// Gets course information, this is for creating the course list for adding discussions into them. 
{	
	var main = this;

	$.ajax({											// Ajax talking to the getCourses.php file												
			type: "POST",
			url: "scripts/php/getCourses.php",
			  success: function(data) {				// If connection is successful the data will be put into an object. 
			    	  
			    	  $.each(data, function(index, element) {	// If view is not specified Construct the table for each element
			    			main.courseListName = { ID: element.courseID, Name : element.courseName}; 
				    	  	main.courseList.push(courseListName);
				    	  });	
			    	   
			    }, 
			  error: function() {					// If connection is not successful.  
					console.log("dscourse Log: the connection to getCourses.php failed.");  
			  }
		});	
} 

 
 
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
			$('#courseDiscussionsBody').append(
			    	  		  "<tr>"
			    	  		+ "<td> " + m.dTitle			+ "</td>" 
				            + "<td>  45 </td>" 
				            + "<td> <em>'It seems slightly better done than the...' by Mable Kinzie </td>" 
				            + "</tr>" 
			    	  	);
			 }
		}	
		
	}	
	
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
			saved('Your discussion was saved.');
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
				saved('Your discussion was saved.');
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
			    	  
			    	  saved('Everything saved! ') 				// Remove save button and send save success message 
			    	  $('html, body').animate({scrollTop:0});		// The page scrolls to the top to see the notification
			    	  clearDiscussionForm();
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
	 		}
	 	
	 	}	 
}


/********** POSTS ****************/


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
		var postMessage = $('#text').val();	
		console.log('Post message : ' + postMessage);

		// type -- postType
		var postType = 'comment';	
		var formVal = $('input[type=radio]:checked').val();
		
		if(formVal !== undefined){
			postType = formVal;
		} 
		console.log('Post id : ' + postType);
	
	// Create post object and append it to allPosts
	
			post = {
				'postFromId': postFromId,
				'postAuthorId': postAuthorId,
				'postMessage': postMessage,
				'postType': postType,
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
		
		var i;
	 	for (i = 0; i < main.data.allDiscussions.length; i++)
	 	{		
	 		var o = main.data.allDiscussions[i];
	 		if(o.dID === currentDisc ){
		 		var discPosts = o.dPosts.split(",");
		 		discPosts.push(pID); 
		 		discPostList = discPosts.toString(); 
		 		o.dPosts = discPostList;
		 		
		 		$.ajax({												// Ajax talking to the saveDiscussions.php file												
					type: "POST",
					url: "scripts/php/saveDiscussions.php",
					data: {
						discussions: main.data.allDiscussions							// All discussion data is sent
													
					},
					  success: function(data) {							// If connection is successful . 
					    	console.log(data);
					    	//main.data.allPosts.push(post); 
					    	main.GetData();
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

	 var discPosts = posts.split(",");
	 var i, j, p, d, typeText, authorID, message;
	 for(i = 1; i < discPosts.length; i++){							// Take one post at a time
		 p = discPosts[i];
		 for (j = 0; j < main.data.allPosts.length; j++){			// Go through all the posts
			 d = main.data.allPosts[j];			 
			 if(d.postID == p){										// Find the post we want to get the details of 
			 														// Prepare the data for display
				 authorID = main.getName(d.postAuthorId); 			// Get Authors name			
				 switch(d.postType)									// Get what kind of post this is 
					{
					case 'agree':
					  typeText = ' agreed';
					  break;
					case 'disagree':
					  typeText = ' disagreed';
					  break;
					case 'clarify':
					  typeText = ' asked to clarify';
					  break;
					case 'offTopic':
					  typeText = ' marked as off topic';
					  break;		  
					default:
					  typeText = ' commented';
					}
				 if(d.postMessage){									// Proper presentation of the message
					message = ' "' + d.postMessage + '".'; 
				 }
				 
				 console.log('author id: ' + authorID + ' - typetext: ' + typeText + ' - message: ' + message);	// Check to see if all is well
				 
				 var selector = 'div[level="'+ d.postFromId +'"]';	
				 $(selector).append(						// Add post data to the view
				 	  '<div class="threadText" level="'+ d.postID + '">' 
				 	+  '<span class="postAuthorView"> ' + authorID + '</span>'
				 	+  '<span class="postTypeView"> ' + typeText + '</span>'
				 	+  '<span class="postMessageView"> ' + message  + '</span>'
				 	+ ' <div class="sayBut2" postID="'+ d.postID + '">say</div>'	
				 	+ '</div>'
				 );
			 }	 
		 }
	 }
}





























