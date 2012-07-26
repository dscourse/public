/*
 *	Javascript for User Management related functions. 
 *  We are not using json objects in this file since user management is not used often and it's more secure this way.  
 * 
 *  Coded for dscourse. University of Virginia, Curry School of Education
 * 
 *	To-dos: 
 * 	1. Current changes for activate and deactivate refreshes entire table. Faster method will be needed in the future. 
 *
*/

var singleUser = {};
var nameList = [];
var nameListName = {}; 
			
(function() { 													// Auto runs everything inside when this script is loaded
		
		
		$('#addUserButton').live('click', function() {  		// Add user when Form is submitted
			
			var valState = ValidateUsers();						// Checks validation
			console.log('Val State is : ' + valState);
			
			if(valState == 'pass'){			
				var firstName = $('#firstName').val();				// Populates the fields needed for the database. Validation is done through Crystal's code. 
				var lastName  = $('#lastName').val();
				var username  = $('#email').val();
				var sysRole   = $('#sysRole option:selected').val();
				var password  = $('#password').val();
				var facebook  = $('#facebook').val();
				var userPicture  = $('#userPicture').val();
				var userAbout  = $('#userAbout').val();
				var twitter   = $('#twitter').val();
				var phone 	  = $('#phone').val();
				var website   = $('#website').val();	
				var status   = $('#userStatus').val();
					
				addUser(firstName, lastName, username, password, sysRole, facebook, twitter, phone, website, status, userPicture, userAbout); // Call the function
				dscourse.GetData (); // Refresh the list of users
			} else {
				alert('Oops! It looks like you did not enter some information correctly. Check the error messages on the page for details.');
			}									
		});
		
				
})();															// End of self invoking anonymous function


function addUser(firstName, lastName, username, password, sysRole, facebook, twitter, phone, website, status, userPicture, userAbout) 
{ 																// Adds a new user to the database
		
		user = {
			'firstName': firstName,
			'lastName': lastName,
			'username': username,
			'password': password,
			'sysRole' : sysRole,
			'userFacebook' :  facebook,
			'userPicture' :  userPicture,
			'userAbout' :  userAbout,
			'userTwitter' : twitter,
			'userPhone' : phone,
			'userWebsite' : website, 
			'userStatus' : status
		};
		
		
		$.ajax({													// Add user to the database with php.
			type: "POST",
			url: "scripts/php/userAdmin.php",
			//dataType: 'json',										// not using json data type works here but save this for reference 
			data: {
				user: user,
				action: 'addUser'
			},
			  success: function(data) {								// If addNewUser.php was successfully run, the output is printed on this page as notification. 
			  	console.log("Data is" + data);
			    $('#notify').fadeIn().html(data).delay(5000).fadeOut(400);							// The notify div is filled with the notification
			    $('html, body').animate({scrollTop:0}, 'slow');		// The page scrolls to the top to see the notification
			    clearUserForm(); // Fields are emptied to reuse
	
			  }, 
			  error: function() {									// If there was an error
				  $('#notify').fadeIn().html("<div class=\"alert alert-error \"><strong> Error! </strong>We couldn't complete your request, please try again later.</div>").delay(5000).fadeOut(400);					// There was an error accessing addNewUser.php.
				  $('html, body').animate({scrollTop:0}, 'slow');		// Auto scroll top to see the error
			  }
		});
}



function filterUsers(searchTerm)
{
				$('#userData').html('');
				var userDataState = 'empty'; 
				searchTerm=searchTerm.toLowerCase();
				
				var i, o; 
				for(i = 0; i < dscourse.data.allUsers.length; i++){
					o = dscourse.data.allUsers[i];
					
					var	termFirst = o.firstName.toLowerCase();
					var a=termFirst.indexOf(searchTerm);
					
					var	termLast = o.lastName.toLowerCase();
					var b=termLast.indexOf(searchTerm);

					var	termUser = o.username.toLowerCase();
					var c=termUser.indexOf(searchTerm);
										
					if ( a != -1 || b != -1 || c != -1)
						{
						$('#userData').append(
				    	  		  "<tr>"
				    	  		+ "<td> <a href='profile.php?id=" + o.UserID + "'>" + o.firstName			+ "</a></td>" 
					            + "<td> " + o.lastName	+ "</td>" 
					            + "<td> " + o.username		+ "</td>" 
					            + "<td> " + o.sysRole	+ "</td>" 
					            + "<td> " + o.userStatus		+ "</td>"
					            + "<td> <button id='" + o.UserID		+ "' class='btn btn-info editUser'>Edit</button></td>"
					            + "</tr>" 
				    	  	);
				    	  userDataState = 'set';
				    	 }

				   }
			     
			     if (userDataState == 'empty')
					{
						$('#userData').append('<tr><td colspan=6> No records matched your query.</td></tr>');

					}

}




function editUser(id)											// shows user details in the form
{
		$.each(dscourse.data.allUsers, function(index, element) {	// If view is not specified Construct the table for each element
			
			if (id == element.UserID)					// Search for the object to edit
			{
				$('#firstName').val(element.firstName);				// Populates the fields needed for the form. 
				$('#lastName').val(element.lastName);
				$('#email').val(element.username);
				$('#sysRole [value="' + element.sysRole +'"]').attr("selected", "selected");
				$('#userStatus [value="' + element.userStatus +'"]').attr("selected", "selected");
				$('#userPicture').val(element.userPictureURL);
				$('#userAbout').val(element.userAbout);
				$('#facebook').val(element.userFacebook);
				$('#twitter').val(element.userTwitter);	
				$('#phone').val(element.userPhone);
				$('#website').val(element.userWebsite);
				$('#imgPath').html("<img src='" + element.userPictureURL + "' width='120' />");
				$('#userIDInput').html("<input type=\"hidden\" name=\"userID\" id=\"userID\" value=" + element.UserID + " />");
				
				console.log("User status: " + element.userStatus);
			};				
		});	
}

function updateUser() 
{
			var userID = $('#userID').val();


			var firstName = $('#firstName').val();				
			var lastName  = $('#lastName').val();
			var username  = $('#email').val();
			var sysRole   = $('#sysRole option:selected').val();
			var password  = $('#password').val();
			var facebook  = $('#facebook').val();
			var userPicture  = $("input[name='userPicture']").val();
			var userAbout  = $('#userAbout').val();
			var twitter   = $('#twitter').val();
			var phone 	  = $('#phone').val();
			var website   = $('#website').val();	
			var status   = $('#userStatus option:selected').val();
			
			console.log(userPicture);
			
			user = {
				'UserID': userID,
				'firstName': firstName,
				'lastName': lastName,
				'username': username,
				'password': password,
				'sysRole' : sysRole,
				'userFacebook' :  facebook,
				'userPicture' :  userPicture,
				'userAbout' :  userAbout,
				'userTwitter' : twitter,
				'userPhone' : phone,
				'userWebsite' : website, 
				'userStatus' : status
			};
			
			$.ajax({													// Add user to the database with php.
				type: "POST",
				url: "scripts/php/userAdmin.php",
				//dataType: 'json',										// not using json data type works here but save this for reference 
				data: {
					user: user,
					action: 'updateUser'
				},
				  success: function(data) {								 
				    $('#notify').fadeIn().html(data).delay(5000).fadeOut(400);							// The notify div is filled with the notification		
				  }, 
				  error: function() {									// If there was an error
					  $('#notify').fadeIn().html("<div class=\"alert alert-error \"><strong> Error! </strong>We couldn't complete your request, please try again later.</div>").delay(5000).fadeOut(400);					
				  }
			});


}































/* End of file "scripts/userlist2.js"  */