
/********************************* VALIDATION FOR USER.PHP *********************************/

function ValidateUsers(){
	var validationState = 'fail';

	 
	if(checkFirstName() == 'pass' && checkLastName() == 'pass' && checkEmail() == 'pass' && checkPassword() == 'pass' && checkAbout() == 'pass' && checkFacebook() == 'pass' && checkTwitter() == 'pass' && checkPhone() == 'pass' && checkWebsite() == 'pass'){
		validationState = 'pass';
	}
	
	return validationState;
}

function checkFirstName(){																//defines checkFirstName function
		var check = 'fail';
		var firstName = $('#firstName').val();											//get the first name input value
		console.log("The user's first name is:" + firstName);							//check the first name value for debugging
		var firstNameVal = new RegExp(/^[A-Za-z]{3,20}$/);								//RegEx check for first name
		if(firstName == ''){
			$('#firstNameControl').removeClass('success').addClass('error');				//add error class to control group
			$('#firstNameControl').find('.help-inline').html('Please enter your name using alphabetical characters (A-Z) only.');	//Feedback to user for resubmit if empty
		} else if (!firstNameVal.test(firstName)) {
			$('#firstNameControl').removeClass('success').addClass('error');				//add error class to control group
			$('#firstNameControl').find('.help-inline').html('Revise your entry to include at least 3 alphabetical characters (A-Z) only and resubmit.');//Feedback to user for resubmit 
		} else { 
			$('#firstNameControl').removeClass('error').addClass('success');				//for a valid submission, green text (success)
			$('#firstNameControl').find('.help-inline').html('Your entry has been successfully validated') 	//Feedback to user = Successful Validation
			check = 'pass';
		}
		return check; 
	}
		
		
function checkLastName(){														//defines checkLastName function		
		var check = 'fail';															
		var lastName = $('#lastName').val();											//get the last name input value
		console.log("The user's last name is:" + lastName);							//check the last name value for debugging
		var lastNameVal = new RegExp(/^[A-Za-z]{2,20}$/);								//RegEx check for last name
		if(!lastName){
			$('#lastNameControl').removeClass('success').addClass('error');				//add error class to control group (turns inline text red)
			$('#lastNameControl').find('.help-inline').html('Please enter your name using alphabetical characters (A-Z) only.');	//Feedback to user for resubmit if empty
		} else if (!lastNameVal.test(lastName)) {
			$('#lastNameControl').removeClass('success').addClass('error');				//add error class to control group (turns inline text red)
			$('#lastNameControl').find('.help-inline').html('Revise your entry to include at least 2  alphabetical characters (A-Z) only and resubmit.');//Feedback to user for resubmit 
		} else { 
			$('#lastNameControl').removeClass('error').addClass('success');				//for a valid submission (turns inline text green)
			$('#lastNameControl').find('.help-inline').html('Your entry has been successfully validated.') 	//Feedback to user = Successful Validation
			check = 'pass';	
		}
		return check; 

	}	
	
		
function checkEmail(){
		var check = 'fail';															
		var email = $('#email').val();													//get the e-mail input value
		console.log("The e-mail is:" + email);											//check the e-mail value for debugging
		var emailVal = new RegExp(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i);		//RegEx for E-mail
		if (!email){																	//if e-mail form is empty
			$('#emailControl').removeClass('success').addClass('error');				//add error class to control group
			$('#emailControl').find('.help-inline').html('Please enter a valid e-mail address.');	//Feedback to user for resubmit if empty
		} else if (!emailVal.test(email)) {													//RegEx check for e-mail
			$('#emailControl').removeClass('success').addClass('error');					//add error class to control group (turns inline text red)
			$('#emailControl').find('.help-inline').html('Revise your entry to include appropriate alphanumeric characters and symbols.');//Feedback to user for resubmit
		} else {
			$('#emailControl').removeClass('error').addClass('success');					//for a valid submission (turns inline text green)
				$('#emailControl').find('.help-inline').html('Your entry has been successfully validated.'); //Feedback to user = Successful Validation
			check = 'pass';

		}
		return check; 

	}

		
function checkPassword(){
		var check = 'fail';															
		var password = $('#password').val();												//get the password input value
		console.log("The password is:" + password);										//check the password value for debugging to be sure this registers correctly
		var passwordVal = new RegExp(/^[A-Za-z0-9!@#$%^&*()_]{6,12}$/);					//RegEx check for password
		if (!password) {
			$('#passwordControl').removeClass('success').addClass('error');
				$('#passwordControl').find('.help-inline').html('Please enter a password to register your account.');	//reminds user to enter a password in the field
		} 
		else if (!passwordVal.test(password)) {
			$('#passwordControl').removeClass('success').addClass('error');								//add error class to control group (turns inline text red)
				$('#passwordControl').find('.help-inline').html('Password must be 6-12 characters in length.');  //informs user of password specifications
		} 
		else{
			$('#passwordControl').removeClass('error').addClass('success');
			$('#passwordControl').find('.help-inline').html('Your password has been successfully validated.');//Feedback to user = Successful validation
			check = 'pass';

		}
		return check; 
	}
	
		
function checkAbout(){
		var check = 'pass';															
				
		var aboutSize = $('#userAbout').val().length; 
			aboutSize = 1000 - aboutSize;						
			 if (aboutSize <0 ) 
			 {
					$('#aboutControl').addClass('error');
					$('#aboutControl').find('.help-inline').html('You have exceeded 1000 characters, please revise. You have: <strong>' + aboutSize + '</strong> characters left.');	 
					check = 'fail';	
			  }
			  else 
			  {
				  	$('#aboutControl').removeClass('error');	
				  	$('#aboutControl').find('.help-inline').html('Briefly introduce yourself. Please limit your text to 1000 characters. You have: <strong>' + aboutSize + ' </strong> characters left.');	 
			  } 
		return check; 

}
		
function checkFacebook()
	{
		var check = 'pass';															
	
		var facebook = $('#facebook').val();												//get the facebook input value
		console.log("Facebook username is" + facebook);										//debugging check
		var facebookVal = new RegExp(/^[A-Za-z0-9!@#$%^&*()_]{5,14}/);						//regex check for facebook username 
		if (facebook) {
			if (!facebookVal.test(facebook)) {												//if there is a value entered, check the format
				$('#facebookControl').removeClass('success').addClass('error');				//if value entered does not meet format criteria, change text color to red
					$('#facebookControl').find('.help-inline').html('Your Facebook name must be between 5-14 characters');	//informs user of facebook input specifications
					check = 'fail';
			}
		else{																			//if the value of facebook meets criteria
				$('#facebookControl').removeClass('error').addClass('success');				//Change text color to green
				$('#facebookControl').find('.help-inline').html('Your Facebook account has been successfully validated.'); //Feedback to user = Successful validation
			}
		}
		return check; 

	}	
		
function checkTwitter(){							
		var check = 'pass';															

		var twitter = $('#twitter').val();														//defines twitter
		console.log("Twitter:@" + twitter);														//debugging check
		var twitterVal = new RegExp(/^[A-Za-z0-9_]{0,20}/);										//regex check for twitter value
		if(twitter) { 																			//if there is a value entered, check the format
			if (!twitterVal.test(twitter)) {													//if the format doesn't meet format criteria, change text color to red
				$('#twitterControl').removeClass('success').addClass('error');					
				$('#twitterControl').find('.help-inline').html('Your Twitter name must be between 0-20 characters');//informs user of twitter input specifications
				check = 'fail';
				} 
			else{																				//if the value of twitter meets format criteria
				$('#twitterControl').removeClass('error').addClass('success');					//change the text to green
				$('#twitterControl').find('.help-inline').html('Your Twitter account has been successfully validated.');//feedback to user = Successful validation
				}	
			}
		return check; 

	}
	
function checkPhone(){
		var check = 'pass';															

		var phone = $('#phone').val();																//defines phone
		console.log("The phone number is:" + phone);												//debugging check
		var phoneVal = new RegExp(/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/);							//regex check for phone value (Based on U.S. 3+7 digit format)
		if(phone) {																					//if there is a phone input value
			if(!phoneVal.test(phone)) {																//test the phone input value against criteria 
				$('#phoneControl').removeClass('success').addClass('error');						//if the format doesn't meet critera, change text to red
				$('#phoneControl').find('.help-inline').html('Your phone number must be a valid 10 digit format');	//informs user of phone input value specifications
				check = 'fail';
				} 
		   	else{ 																				//if the value of phone meets the format criteria
				$('#phoneControl').removeClass('error').addClass('success');							//change the text to green
				$('#phoneControl').find('.help-inline').html('Your phone number has been successfully validated.');// feedback to user = Successful validation
			
		}
	}
		return check; 

	}
		
function checkWebsite(){
		var check = 'fail';															
		var website = $('#website').val();															//defines website
		console.log("The website url is " + website);												//debugging check
		var websiteVal = new RegExp(/((ftp|https?):\/\/)?(www\.)?[a-z0-9\-\.]{3,}\.[a-z]{3}$/);											//regex check for website (extremely lenient)		
		if (website) {																				//if a website value is entered
			if (!websiteVal.test(website)) { 														//test the website input value against criteria
				$('#websiteControl').removeClass('success').addClass('error');						//if the format doesn't meet criteria, change text to red
				$('#websiteControl').find('.help-inline').html('Your website must follow traditional format (e.g. http://www.abcdefg.com).'); //informs user of website input value specifications
				check = 'fail';
				} 	
			else{																					//if the value of website meets the format criteria
				$('#websiteControl').removeClass('error').addClass('success');						//change the text color to green
				$('#websiteControl').find('.help-inline').html('Your website has been successfully validated.');	//feedback to user = Successful validation
		}
	}
		return check; 

}

/********************************* VALIDATION FOR DISCUSSION.PHP *********************************/

function checkDiscussionQuestion(){	
		var dSize = $('#discussionQuestion').val().length; 						
			 if (dSize == 0) 
			 {
					$('#discussionQuestionControl').removeClass('success').addClass('error');
					$('#discussionQuestionControl').find('.help-inline').text('Please enter a discussion question');	 
			  }
			  else 
			  {
				  	$('#discussionQuestionControl').removeClass('error').addClass('success');	
					$('#discussionQuestionControl').find('.help-inline').text('That works.');	 
				  	
			  } 
}


function checkDiscussionPrompt(){		
		var pSize = $('#discussionPrompt').val().length;
			pSize = 1000 - pSize; 
			 if (pSize < 0) 
			 {
					$('#discussionPromptControl').addClass('error');
					$('#discussionPromptControl').find('.help-inline').html('You have exceeded 1000 characters, please revise. You have  <strong>' + pSize + '</strong> characters left.');	 
			  }
			  else 
			  {
				  	$('#discussionPromptControl').removeClass('error');	
				  	$('#discussionPromptControl').find('.help-inline').html('If you like you can provide prompts to get into details or explain directions for the discussion. Please limit your text to 1000 characters. You have <strong>' + pSize + '</strong> characters left.');	 
			  } 
}





























