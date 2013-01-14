/*
 *  All Course related code 
 */
 
 // Search for "TODO" in page for items that need attention

function Dscourse() 
{

	// Set global variables 
	var top = this;								// For scope
	this.data = new Array(); 				// Main discussion data wrapper
	this.timelineMin = 0;					// Minimum value for the timeline
	this.timelineMax = 0;					// Maximum value for the timeline
	this.sPostID;							// Synthesis post id global
	this.sPostContent;						// Synthesis post content global
	this.uParticipant = new Array; 			// Unique list of participants. 
	this.post = { };
	this.currentSelected = '';  		// Needed for selection
	this.currentStart = '';
	this.currentEnd = ''; 	
	this.currentDrawing = ''; 			// The drawing data that will be saved to the database. 
	this.currentDrawData = ''; 			// this is used for displaying drawings; 
	this.currentMediaType = ''; 		// What kind of media should be displayed. 
	this.postMediaType = 'draw'; 	// Used while saving the media type data. 
	this.newPosts = ''; 	// A string of the posts for a discussion that are new when refreshed. This variable is used to transfer post ids between functions.  
	
	// Run initializing functions
	this.GetData(discID);					// Load all discussion related information
	this.DiscResize(); 	 

  jQuery("abbr.timeago").timeago();			// binds all abbr. tags with the timeago script. 

	/************************ DISCUSSION EVENTS  ************************/
	/* Make the commenting box draggable */
	$('#commentWrap').append($('<div>',{
		class: 'commentWrapHandle',
		css:{
			height: '30px',
			position: 'absolute',
			top: '-25px',
			boxShadow: '0 0 10px 0 #CCCCCC inset, 0 0 19px 0 #999999',
			left: '2px',
			right: '2px',
			backgroundColor: 'lightgray',
			borderTopRightRadius: '8px',
			borderTopLeftRadius: '8px',
		}
	}));
	$('#commentWrap').draggable({handle: '.commentWrapHandle'});			

	/* Add highlighted text to the comment */
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
		$('#highlightShow').html(n);
	});		

	/* Tooltips */
	$('#discussionDivs').tooltip({ selector: "span", placement: 'bottom' });  
	$('#participants').tooltip({ selector: "li" });  
	$('#shivaDrawPaletteDiv').tooltip({ selector: "button" });  

	/* When clicked on a post */
	$('.threadText').live('click', function (event) {
		event.stopPropagation();
		$('.threadText').removeClass('highlight');
		$('.threadText').find('span').removeClass('highlight');
		var postClickId = $(this).closest('div').attr('level');
		top.HighlightRelevant(postClickId);
		$(this).removeClass('agree disagree comment offTopic clarify').addClass('highlight');
		});

	/* When mouse hovers over the post */
	$('.threadText').live('mouseover', function (event) {
		event.stopImmediatePropagation();
			var postClickId = $(this).closest('div').attr('level');
			top.HighlightRelevant(postClickId);
		$(this).children('.sayBut2').show();
		if(!$(this).hasClass('lightHighlight')){	$(this).addClass('lightHighlight'); }

	});

	/* When mouse hovers out of the post */
	$('.threadText').live('mouseout', function (event) {
		event.stopImmediatePropagation();
		$(this).children('.sayBut2').hide();
		$(this).removeClass('lightHighlight'); 

	});

	/* When there are new posts and a refresh is required */
	$('.refreshBox').live('click', function () {
		$(this).hide();
		var discID = $(this).attr('discID');
		top.GetData(discID);  // We load our new discussion with all the posts up to date	
	});

	    // When the main window scrolls heatmap needs to redraw
	    $('#dMain').scroll(function() {
				  top.VerticalHeatmap();
				  top.DrawShape(); 	
		});
		
	/* Keyword search functionality within the discussion page */
	$('#keywordSearchText').live('keyup', function () {
		var searchText = $('#keywordSearchText').val();  // get contents of the box
		if(searchText.length > 0 && searchText != ' '){
			top.ClearVerticalHeatmap();
			//console.log('Search text: ' + searchText); // Works
			top.VerticalHeatmap('keyword', searchText);// Send text to the vertical heatmap app
		} else {
			top.ClearKeywordSearch('#dMain'); 
			top.ClearVerticalHeatmap();
		}
	});

	/* Adding a new post to the discussion */
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
	
	/* Adding a new synthesis post */
	$('#addSynthesisButton').live('click', function() {
		top.AddSynthesis();		 
	});
	
	/* When the comment box is clicked change the placeholder text */
	$('#text').live('click', function () {
		var value = $('#text').val(); 
		if (value == 'Why do you agree?' || value == 'Why do you disagree?' || value == 'What is unclear?' || value == 'Why is it off topic?' || value == 'Your comment...'){
			$('#text').val(''); 
		}
	});

	/* When say button is clicked comment box appears and related adjustments take place */	
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
			$('#commentWrap').css({'top' : '20%', 'left' : '30%'});
			$('.threadText').removeClass('highlight');		
			var postID = $(this).attr("postID");
			console.log(postID);
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

	/* When users cancels new post addition  */	
	$('#postCancel').live('click', function () {
		$('.threadText').removeClass('highlight');		
		$('#commentWrap').fadeOut();
		$('#overlay').hide();
		$('#shivaDrawDiv').hide();						
		$('#shivaDrawPaletteDiv').hide();		
		top.ClearPostForm();
	});

	/* When user decides to turn a comment into a synthesis post instead */
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
		top.ClearPostForm();
	});

	/* When user clicks on the posts inside synthesis */
 	$('.synthesisPosts').live('click', function (event) {  
		event.stopImmediatePropagation();
	 	var thisPost = $(this).attr('sPostID'); 
 		var postRef = 'div[level="'+ thisPost +'"]';
 		$('#dMain').scrollTo( $(postRef), 400 , {offset:-100});	
    	$(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', '#fff');$(this).dequeue();});    	
    	$('.synthesisPosts').css('background-color', '#FAFDF0') // Original background color 
    	$(this).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)');  // Change the background color of the clicked div as well. 
	});

	/* Single synthesis wrapper click event */
	$('.synthesisPost').live('click', function () {  
    	$(this).children('.synthesisPosts').fadeToggle(); 
	});

	/* Show which post is synthesized */		
	$('.SynthesisComponent').live('click', function () {
	 	var thisPost = $(this).attr('synthesisSource'); 
 		var postRef = '.synthesisPost[sPostID="'+ thisPost +'"]';
 		$('#dSidebar').scrollTo( $(postRef), 400 , {offset:-100});
    	$(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', 'whitesmoke');$(this).dequeue();}); 			
    	$('#dInfo').fadeOut(); // hide #dInfo
    	$('#dSynthesis').fadeIn(); // show #synthesis
	
	});
		
	/* When user cancels synthesis creation */
 	$('#cancelSynthesisButton').live('click', function () {
	 	$('#addSynthesis').slideUp('fast'); 
	});

	/* When user changes the option type for commenting */
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

	/* When posttype is collapes or opened up make style changes */	
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

	/* Add button effect to the post type information */		
	$('.postTypeWrap').live('mousedown', function () {				// This is just for style to make it look like a button. 
		$(this).addClass('buttonEffect');
	});
	
	$('.postTypeWrap').live('mouseup', function () {				 
		$(this).removeClass('buttonEffect');
	});

	/* When show timeline button is clicked */
	$('#showTimeline').live('click', function () {
		$('#timeline').slideToggle().queue(function () {top.DiscResize();top.VerticalHeatmap();$(this).dequeue();});	
		if($(this).hasClass('active') == true) {
				$(this).removeClass('active');
			} else {
				$(this).addClass('active');	
			}
	});

	/* When show synthesis button is clicked */	
	$('#showSynthesis').live('click', function () {
		$('#dInfo').fadeToggle(); // toggle hide sidebar content
		$('#dSynthesis').fadeToggle();
		if($(this).hasClass('active') == true) {
				$(this).removeClass('active');
			} else {
				$(this).addClass('active');	
			}				
	});

	/* When media button is clicked to show media options */	
	$('#media').live('click', function () {
		$('#commentWrap').hide();
		$('#mediaBox').show();
		var mHeight = $(window).height()-200 + 'px';		
		$('#mediaWrap').html('<iframe id="node" src="http://www.viseyes.org/shiva/draw.htm" width="100%" height="'+ mHeight +'" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>');					
		$('html, body').animate({scrollTop:0});	
	});

	/* Events to close the media section */	
	$('#closeMedia').live('click', function () {			
		$('#mediaBox').hide();
	 	$('#displayFrame').hide();
		$('#commentWrap').show();
	});

	$('#closeMediaDisplay').live('click', function () {
		$('#mediaDisplay').hide();
		$('#commentWrap').hide();
	 	$('#displayFrame').hide();
	});					

	/* User heatmap buttons */
	$('.uList').live('click', function () {						
		var uListID = $(this).attr('authorId'); 
			top.ClearVerticalHeatmap();
			top.VerticalHeatmap('user', uListID); 
	});

	/* The types of comments */	
	$('.drawTypes').live('click', function () {			// This needs to be readded - TODO					
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

	/* Continue to adding post when the drawing is done. Saves drawing data into the post */ 
	$('#continuePost').live('click', function () {						 
		top.currentDrawing = ''; 
		ShivaMessage('node','GetJSON'); 
		//console.log(top.currentDrawing);
		$('#mediaBox').hide();
		$('#commentWrap').show();
	}); 

	/* Cancel drawing */
	$('#drawCancel').live('click', function () {						 
		top.currentDrawing = ''; 
		$('#mediaBox').hide();
		$('#commentWrap').show();
	});

	/* Adding the shiva framework for chosen media type. This is important for media drawing */			
	$('.mediaMsg').live('click', function (event) {
		event.stopImmediatePropagation();
		var postId = $(this).closest('.threadText').attr('level'); 
		top.currentDrawData = ''; 
		top.currentMediaType = 'Draw';		
		var cmd;		
		$('#displayDraw').html('').append('<iframe id="displayIframe" src="http://www.viseyes.org/shiva/go.htm" width="100%" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>');
		var i, o;
		for(i = 0; i < top.data.posts.length; i++){
			 o = top.data.posts[i];
			 if(o.postID == postId){
			 	//console.log(o.postMedia); 
				cmd ="PutJSON="+o.postMedia;
				$('#displayFrame').show();
				$('html, body').animate({scrollTop:0});	
			 }
			 $('#displayIframe').load(function () { document.getElementById('displayIframe').contentWindow.postMessage(cmd,"*");	}).queue(function () {top.DiscResize(); top.VerticalHeatmap();$('#containerDiv').css('width', '100% !important'); $(this).dequeue();});		
		 }	  				
	}); 

	/* When items in the recent contents section are clicked */			
	 $('#recentContent li').live('click', function () {
 		var postID = $(this).attr('postid'); 
 		var postRef = 'div[level="'+ postID +'"]';
 		$('#dMain').scrollTo( $(postRef), 400 , {offset:-100});
    	$(postRef).removeClass('agree disagree comment offTopic clarify').addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', '#fff');$(this).dequeue();});
	 });

	/*  Hide refresh message. Is this not used anymore? Check TODO */
	$('#hideRefreshMsg').live('click', function () { 
		$('#checkNewPosts').hide('');
	});
	
	/*  Vertical Heatmap scrolling */
	$('.vHeatmapPoint').live('click', function () {
 		var postID = $(this).attr('divpostid'); 
 		var postRef = 'div[level="'+ postID +'"]';
 		$('#dMain').scrollTo( $(postRef), 400 , {offset:-100});
    	$(postRef).removeClass('agree disagree comment offTopic clarify').addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function () {$(this).removeClass('highlight animated flash').css('background-color', '#fff');$(this).dequeue();});
    	$('.vHeatmapPoint').removeClass('highlight');
    	$(this).addClass('highlight');
	 });

	/* Show synthesis post numbers next to post. Needs event control, hide and show propagate. TODO */	 
	 $('.synthesisWrap').live('mouseover', function (event) {
	 	$(this).children('span').fadeIn('slow'); 
	 }); 

	 $('.synthesisWrap').live('mouseout', function (event) {
	 	$(this).children('span').fadeOut('slow'); 
	 }); 


	$(window).resize(function() {			// When window size changes resize
		  top.DiscResize();
		  top.VerticalHeatmap(); 

		});

		
	
} // End function Dscourse() 



Dscourse.prototype.GetData=function(discID)
{
/* 
 *	Loads all the data needed for the discussion page  
 */

	var main = this;
	
	// Ajax call to get data and put all data into json object
		$.ajax({													// Add user to the database with php.
			type: "POST",
			url: "php/data.php",
			data: {
				action: 'getData',
				discID: discID
			},
			  success: function(data) {								// If addNewUser.php was successfully run, the output is printed on this page as notification. 
			  		main.data = data;
			  		main.SingleDiscussion(discID);  
			  		//console.log(main.data);
			  	}, 
			  error: function() {									// If there was an error
				 		//console.log('There was an error talking to data.php');
			  }
		});	

}

 Dscourse.prototype.SingleDiscussion=function(discID)	 			  // View for the Individual discussions. 
 {
/* 
 *	Prints out the components of the discussion page. Has multiple dependancies on other functions in Dscourse object.    
 */
 	    var main = this;
	 	$('.levelWrapper[level="0"]').html('');						// Empty the discussion div for refresh
 		var o, userRole, dStatus;	 	
 		o = main.data.discussion;									// We have one discussion data loaded for this discussion
		$('#dTitleView').html(o.dTitle);							// Discussion title
		$('#dPromptView').html('<b> Prompt: </b>' + o.dPrompt);		// Discussion prompt
		$('#dIDhidden').val(o.dID);									// discussion ID, we need this for form input when adding posts
		var dCourse = main.listDiscussionCourses(discID); 			// Courses that this discussion is under
		$('#dCourse').html('<b> Course: </b>' + dCourse);			// Print out the courses
		$('#dSDateView').html('<b> Start Date: </b>' + main.FormattedDate(o.dStartDate));	// Start Date
		$('#dODateView').html('<b> Open to Class: </b>' + main.FormattedDate(o.dOpenDate)); // Open date (when discussion opens to everyone)
		$('#dCDateView').html('<b> End Date: </b>' + main.FormattedDate(o.dEndDate));		// End date
		//main.CurrentDiscussion = o.dID;		 							// Set gloabl with current discussion. What is this for? 
		var shortName = main.truncateText(o.dTitle, 50);				// Get short name for the navigation
		$('#navLevel3').text(shortName).attr('dLinkID', o.dID).css({ 'display' : 'block'});  // Enter the shortname to the navigation

		// Get Discussion Status, can be one of three: all, student, closed.
		dStatus = main.DiscDateStatus(o.dID);				 
		
		// Print note for the page saying the discussion status
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
		//console.log(userRole);
			
		// Draw up posts and timeline
		if(main.data.posts){
			main.ListDiscussionPosts(dStatus, userRole, o.dID);				// Main function for listing discussions -- very important!	 					 			
			/* Check if timeline needs to be drawn  */
			if(dStatus == 'all' || dStatus == 'closed') {
				main.DrawTimeline();
			} else {
				$('#amount').val('Timeline is disabled.');
			}
		} else {
			$('.levelWrapper').append( 
				"<div id='nodisc' class='alert alert-info'> There are not posts in this discussion yet. Be the first one and add your voice by clicking on the <b>'Say'</b> button at the top (next to the discussion title)</div>"
			); 
		}
		

	 		
/*
	 	setInterval(function(){main.CheckNewPosts(discID, userRole, dStatus)},5000);
	 	main.AddLog('discussion',discID,'view',0,'');
*/	
}


Dscourse.prototype.ListDiscussionPosts=function(dStatus, userRole, discID)	 			  // View for the Individual discussions. 
 {
  /* 
  *	Lists posts for the discussion. This is the main function that builds the post view  
  */
	 var main = this;
	 
	 main.uParticipant = [];
	 $('.singleDot').remove();																	// Clear all dots in the timeline
	 main.timelineMin = 0; main.timelineMax = 0; 												// Clear timeline range
	 $('#participantList').html('<button class="btn disabled">Participants: </button>' );		// Clear participant list	 	 	 
	 var j, p, d, q, typeText, authorID, message, authorThumb;

	 for (j = 0; j < main.data.posts.length; j++){			// Go through all the posts
		 d = main.data.posts[j];		

		 /********** TIMELINE ***********/ 
		 var n = d.postTime; 
		  n = n.replace(/-/g, "/");							// Correst the time input format
		 var time = Date.parse(n);							// Parse for browser. 		 
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
			message =message.replace("\n","<br />");
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
			 	+ ' <button class="btn btn-small btn-success sayBut2" style="display:none" postID="'+ d.postID + '"><i class="icon-comment icon-white"></i> </button> '								 	
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
			 if ($(selector).length > 0){
				 //console.log(n);
				 var range = main.data.posts.length-8; 			// How many of the most recent we show + 1
				 var prettyTime = jQuery.timeago(d.postTime) ;
				 var shortMessage = main.truncateText(message, 60);
				 if(j > range) {					 // person + type + truncated comment + date
					 var activityContent = '<li postid="' + d.postID + '">' + main.getAuthorThumb(d.postAuthorId, 'tiny') + ' ' + authorID + ' ' + typeText + ' <b>' + shortMessage + '</b> ' + '<em class="timeLog">' + prettyTime + '<em></li> ';
					 $('#recentContent').prepend(activityContent);  
				 }
			 } 

			 /********** UNIQUE PARTICIPANTS SECTION ***********/
			 var arrayState = jQuery.inArray(d.postAuthorId, main.uParticipant); 	// Chech if author is in array
			 if(arrayState == -1) {								// if the post author is not already in the array
				 main.uParticipant.push(d.postAuthorId);		// add id to the array
			 }
		 
		} // end if showpost. 
	 } // End looping through posts 

	 main.timelineValue = main.timelineMax;						// Set the timeline value to the max. 
	 main.UniqueParticipants();
	    
     $(".postTypeView").draggable({
        start: function() {
            //console.log('Drag started');

            main.sPostID = $(this).attr('slevel'); // The id of the post
            main.sPostContent = $(this).parent().children('.postTextWrap').children('.postMessageView').html() // The content of the post
        },
        drag: function() {
            //console.log('Drag happening');
        },
        stop: function() {
            //console.log('Drag stopped!');
        }, 

		helper: function( event ) {
			var contents = $(this).html(); 
			return $( '<div style="font-size:50px; position: absolute; z-index: 1100">' + contents + ' </div>' );
		}

    });

	 $( "#synthesisDrop" ).droppable({
	        hoverClass: "sDropHover",
<<<<<<< HEAD
	        drop: function( event, ui ) {
	            $( this )
	                    .html( "Added!" );
	                   var shortText = main.truncateText(main.sPostContent, 100); 
	           $('#synthesisPostWrapper').prepend('<div sPostID="'+ main.sPostID +'" class=" synthesisPosts">' + shortText + ' <div>');  // Append original post
=======
	        drop: function( event, ui) {
	           $(this).html("Added!");
	           $('#synthesisPostWrapper').prepend('<div sPostID="'+ main.sPostID +'" class=" synthesisPosts">' + main.sPostContent + ' <div>');  // Append original post
>>>>>>> local
	    
	        }
	    });
	 main.DiscResize();
	 main.VerticalHeatmap(); 
}

Dscourse.prototype.AddPost=function(){
	
		 var main = this;
		 var currentDisc = $('#dIDhidden').val();
		 
		 // If there are no posts in this discussion create posts array
		 if(!main.data.posts){		 
		 	main.data.posts = new Array(); 
		 }
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
		console.log(postSelection); 
		if(postSelection == '0,0' ){				// fix for firefox and fool proofing in case nothing is actually selected. 
			postSelection = ''; 
		}
	
	// Get drawing value
		var postMedia; 
		postMedia  = main.currentDrawing; 
		
		postContext = ''; // this is used for the synthesis posts but needs a value here. 
			
	// Create post object and append it to allPosts
		
			post = {
				'postFromId': postFromId,
				'postAuthorId': postAuthorId,
				'postMessage': postMessage,
				'postType': postType,
				'postSelection': postSelection,
				'postMedia' : postMedia, 
				'postMediaType' :  main.postMediaType,
				'postContext' : postContext
				};
		
	// run Ajax to save the post object
	console.log(post);
	$.ajax({																						
			type: "POST",
			url: "php/data.php",
			data: {
				post: post,							
				action: 'addPost',
				currentDiscussion: currentDisc							
			},
			  success: function(data) {						// If connection is successful . 
			    	  post.postTime = main.GetCurrentDate();   
			    	  post.postID 	= data; 
			    	  main.data.posts.push(post); 
			    	  //console.log(data);
			    	  console.log(data);
			 	
			    	  $('.levelWrapper[level="0"]').html('');
			    	  main.SingleDiscussion(currentDisc);
			    	  main.DiscResize();
			    	  main.VerticalHeatmap();
			    	  var divHighlight = 'div[level="'+ data +'"]';
			    	  $(divHighlight).removeClass('agree disagree comment offTopic clarify').addClass('highlight animated flash'); 
			    	  $.scrollTo( $(divHighlight), 400 , {offset:-100});
			    	  //main.AddLog('discussion',currentDisc,'addPost',data,'')
			    }, 
			  error: function() {					// If connection is not successful.  
					//main.AddLog('discussion',currentDisc,'addPost','','Error: Dscourse Log: the connection to data.php failed. ')
					//console.log("Dscourse Log: the connection to data.php failed.");  
			  }
		});	
	
	
	
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
		//console.log('post context ' + postContext);

		
			
	// Create post object and append it to allPosts
	
			post = {
				'postFromId': postFromId,
				'postAuthorId': postAuthorId,
				'postMessage': postMessage,
				'postType': postType,
				'postSelection': postSelection,
				'postMediaType' :  main.postMediaType,
				'postMedia' : postMedia, 
				'postContext' : postContext
			};
		
		
	// run Ajax to save the post object
	
	$.ajax({																						
			type: "POST",
			url: "php/data.php",
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
			  error: function(data) {					// If connection is not successful.  
					console.log(data);
					console.log("Dscourse Log: the connection to data.php failed. Did not save synthesis");  
			  }
		});	
	
	
	
}


Dscourse.prototype.DrawTimeline=function()	 			  // Draw the timeline. 
 {
 /* 
  *	Draw the timeline for the selected discussion  
  */
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
	
	var j, d;
	for (j = 0; j < main.data.posts.length; j++){			// Go through all the posts
		d = main.data.posts[j];		
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

Dscourse.prototype.PostInSynthesis=function(postID)
{					
 /* 
  *	Checks to see if this posts is in a synthesis so a notification can be drawn next to the post.   
  */
  	
	 var main = this;
	 var output = ''; 
	 var count = 0; 	
	 var j, k, i, o; 
	 for(j = 0; j < main.data.posts.length; j++){				// Go through all the posts in this discussion
	 	k = main.data.posts[j];
	 	if(k.postContext){	 									// Check post context where synthesis information is
		 	var posts = k.postContext.split(",");				// Split post content into array
			for(i = 0 ; i < posts.length; i++){		 			// For each posts in the array
			 	o = posts[i]; 
			 	if(o == postID){								// check if this post is synthesis in the source post
				 	output  += '<span rel="tooltip" title="'+ main.getName(k.postAuthorId, 'first') + '  made a connection to this post. Click to view." class="SynthesisComponent hide" synthesisSource="'+ k.postID +'"><span class="typicn feed "></span></span>';
				 	count++;  
			 	}
			} 	
	   } 
	 }	 
	 if (count > 0){											// After collecting all the posts combine them into html output 
	 	output = '<span class="synthesisWrap"> <b>' + count +'</b> Connections ' + output + '</span>'; 
	 }
	 return output; 
}

Dscourse.prototype.ListSynthesisPosts=function(postList, sPostID){					// Populate unique participants.  
	
		 var main = this;
		 
		 var i, o, j, k;
		 var posts = postList.split(",");
		 
		 for(i = 0; i < posts.length; i++){
			 o = posts[i]; 
			 
			 for(j = 0; j < main.data.posts.length; j++){
			 	k = main.data.posts[j];
			 	if(k.postID == o){
				 	var postMessage = main.truncateText(k.postMessage, 100);
				 	$('.synthesisPost[sPostID="'+  sPostID + '"]').append('<div sPostID="'+ k.postID +'" class=" synthesisPosts hide"> ' + main.getAuthorThumb(k.postAuthorId, 'tiny') + ' ' + main.getName(k.postAuthorId) + ': <br /><span class="synMessage">'  + postMessage + ' </span><div>');  				 	
			 	}
			 }
		 }		 
}

Dscourse.prototype.ResponseCounters=function(postId)
{
 /* 
  *	Generates the html printout about how many responses each post has.    
  */	
	var main = this;		 
	var comment = 0;    var commentPeople = '';
	var agree 	= 0; 	 var agreePeople = '';
	var disagree = 0; 	 var disagreePeople = '';
	var clarify = 0;	 var clarifyPeople = '';
	var offTopic = 0; 	 var offTopicPeople = '';		 
	var i, o, commentText, text; 		 
	for(i = 0; i < main.data.posts.length; i++){
		o = main.data.posts[i];
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
	commentText = ' ', agreeText = ' ', disagreeText = ' ', clarifyText = ' ', offTopicText = ' '; 
	if(comment 	> 0){commentText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="comment" title="<b>Comments from: </b><br /> ' + commentPeople +'" > ' + comment 	+ '  <span class="typicn message "></span></span>  ';} 
	if(agree 	 	> 0){agreeText 		= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="agree" title="<b>People who agreed: </b><br /> ' + agreePeople + '"> ' + agree 	+ '  <span class="typicn thumbsUp "></span> </span> '	 ;}
	if(disagree	> 0){disagreeText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="disagree" title="<b>People who disagreed:</b><br /> ' + disagreePeople + '"> ' + disagree 	+ '  <span class="typicn thumbsDown "></span></span> ';}
	if(clarify 	> 0){clarifyText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="clarify" title="<b>People that asked to clarify:</b><br /> ' + clarifyPeople + '"> ' + clarify 	+ '  <span class="typicn unknown "></span></span> ' ;}
	if(offTopic 	> 0){offTopicText 	= '<span href="#" rel="tooltip" class="postTypeWrap" typeID="offTopic" title="<b>People that marked off topic: </b><br />' + offTopicPeople + '"> ' + offTopic 	+ '  <span class="typicn forward "></span> </span>  ' ;}	
	text =  commentText + agreeText + disagreeText + clarifyText + offTopicText ; 	
	return text; 
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

Dscourse.prototype.listDiscussionCourses=function(dID)	 			  
 {
 /* 
  *	Lists course names for the selected discussion  
  */
	var main = this;
	var o, print = '';; 
	for (var i = 0; i < main.data.courses.length; i++){
		o = main.data.courses[i]; 
		print += ' <span courseid="' + o.courseID + '">' + o.courseName + '</span>';
	}
	return print; 
}

Dscourse.prototype.HighlightRelevant=function(postID)
{					
/*   
 *  Highlights the relevant sections of host post when hovered over
 */
	 var main = this;	 
	 // First remove all highlights anywhere
	 $('.postTextWrap').find('.highlight').removeClass('highlight'); // Find all postTextWrap spans with class highlight and remove class highlight. 
	 
	 // get selection of this post ID 
	 var i, o, thisSelection, j, m, highlight, newHighlight, n, selector; 
	 for(i = 0; i < main.data.posts.length; i++){
		 o = main.data.posts[i];		 
		 if(o.postID == postID){
			 if(o.postSelection !== ""){ 								// If there is selection do highlighting
				 thisSelection = o.postSelection.split(",");
				 var num1 = parseInt(thisSelection[0]);
				 var num2 = parseInt(thisSelection[1]);
				 // var num3 = num2-num1;   // delete if substring() works. 
				 // find the selection in reference post 
				 for(j = 0; j < main.data.posts.length; j++){
				 	m = main.data.posts[j];				 	
				 	if(m.postID == o.postFromId){
					 	highlight = m.postMessage.substring(num1,num2); 
					 	newHighlight = '<span class="highlight">' + highlight + '</span>';
					 	n = m.postMessage.replace(highlight, newHighlight);
					 	selector = 'div[level="'+ o.postFromId +'"]'; 
					 	$(selector).children('.postTextWrap').children('.postMessageView').html(n);
				 	}
				 }				 
			 } else {
				// If there is no selection remove highlighting		-- Check This --TODO			
			 }			 
		 }		 
	 }
}

Dscourse.prototype.DiscDateStatus=function(dID)	 			  			
 {
 /* 
  *	Checks the date to see if the discussion is active, individual participation or closed.  
  */
  	    var main = this;
	    var dStatus;
	    // Get course dates: 
	    var o;	
	 		o = main.data.discussion;
	 		if(o.dID === dID ){
	 			// Compare dates of the discussion to todays date.  
			    var beginDate = new Date(o.dStartDate.split(' ').join('T'));	    
			    var openDate = new Date(o.dOpenDate.split(' ').join('T'));	    
			    var endDate = new Date(o.dEndDate.split(' ').join('T'));	    
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

Dscourse.prototype.UserCourseRole=function(dID, userID)	 			   
 {
 /* 
  *	Gets the role of the user in the discussion on this course.   
  */
	    var main = this;
	    var userRole = 'unrelated'; 

		var j, k;
			for(j = 0; j < main.data.users.length; j++){		// Loop through courses
				k = main.data.users[j];				
				userRole = k.userRole; 				
			}			
	   return userRole; 		  	    
 } 

Dscourse.prototype.getName=function(id, type)
{
 /* 
  *	Returns name of the user from ID   
  */
	var main = this;
	if(type == 'first') {
		for(var n = 0; n < main.data.users.length; n++){
			var userIDName = main.data.users[n].UserID;
			if (userIDName == id)
			return main.data.users[n].firstName;
		}	
	} else if(type == 'last') {
		for(var n = 0; n < main.data.users.length; n++){
			var userIDName = main.data.users[n].UserID;
			if (userIDName == id)
			return main.data.users[n].lastName;
		}	
	} else {
		for(var n = 0; n < main.data.users.length; n++){
			var userIDName = main.data.users[n].UserID;
			if (userIDName == id)
			return main.data.users[n].firstName + " " + main.data.users[n].lastName;
		}	
	}
}

Dscourse.prototype.getAuthorThumb=function(id, size)
{
 /* 
  *	Returns thumbnail html of the user from ID   
  */
	var main = this;
	for(var n = 0; n < main.data.users.length; n++){
		var userIDName = main.data.users[n].UserID;
		if (userIDName == id){		
			if(size == 'small'){
				return '<img class=userThumbSmall src=' + main.data.users[n].userPictureURL + ' />' ;
			} else if (size == 'tiny'){
				return '<img class=userThumbTiny src=' + main.data.users[n].userPictureURL + ' />' ;				
			}
		}
	}		
}


Dscourse.prototype.UniqueParticipants=function()
{					 
 /* 
  *	Returns html for unique participant buttons in the discussion Participant section.    
  */	
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

Dscourse.prototype.DiscResize=function()
{
 /* 
  * Resizes component widths and heights on the discussion page 
  */
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
	  $('#vHeatmap').css({'height' : height, 'overflow-y' : 'hidden', 'overflow-x' : 'hidden'});
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
							//console.log(divPosition);
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
		  var parentheight =$(this).children('.postTextWrap').height(); 
		  var thiswidth = parentwidth-42; 
		  $(this).css({'width': thiswidth+'px', 'padding-left': '40px' }); 
		  $(this).children('.postTypeView').css('width','20px');
		  $(this).children('.sayBut2').css({'width':'30px', 'margin-left' : '0px', 'height' : parentheight+10+'px'});
		  $(this).children('.responseWrap').css('width','40px');
		  $(this).children('.postTextWrap').css('width',thiswidth-110+'px');
	  });
}

Dscourse.prototype.ClearVerticalHeatmap=function()
{
 /* 
  * Clear heatmap for reuse 
  */	
	// Check to see how clearing will function, this is probably the place for it. 
	$('#vHeatmap').html('');
	$('#vHeatmap').append('<div id="scrollBox" style="overflow:hidden;"> </div>'); // Add scrolling tool

}	

Dscourse.prototype.VerticalHeatmap=function(mapType, mapInfo)
{
 /* 
  * Draw the components of the heatmap 
  */
	 var main = this;
	// View box calculations
	var boxHeight = $('#vHeatmap').height(); // Get height of the heatmap object
	var visibleHeight = $('#dMain').height();  // Get height of visible part of the main section
	var totalHeight = $('#dMain')[0].scrollHeight; // Get height for the entire main section
	
	// Size the box
	// That gives the right relative size to the box
	var scrollBoxHeight = visibleHeight * boxHeight / totalHeight; 

	// Scroll box to visible area
	var mainScrollPosition = $('#dMain').scrollTop(); 
	var boxScrollPosition = mainScrollPosition * boxHeight / totalHeight; 
	// Gives the correct scrolling location to the box 
	
	$('#scrollBox').css({height: scrollBoxHeight-7, marginTop : boxScrollPosition}); 

	if(mapType == 'user'){  	// if mapType is -user- mapInfo is the user ID
		$('.threadText').each(function(){  // Go through each post to see if postAuthorId in Divs is equal to the mapInfo
			var postAuthor = $(this).attr('postAuthorId'); 
			var postID = $(this).attr('level'); 
			if(postAuthor == mapInfo){
				var divPosition = $(this).position();	  // get the location of this div from the top
				
				// dynamically find. 
				var mainDivTop = $('#dMain').scrollTop();  
				//console.log('main div scroll: ' + mainDivTop); 
				//console.log(divPosition);
				var ribbonMargin = (divPosition.top+mainDivTop) * boxHeight / totalHeight; // calculate a yellow ribbon top for the vertical heatmap
					ribbonMargin = ribbonMargin; // this correction is for better alignment of the lines with the scroll box. 
					
					// There is an error when the #dMain layer is scrolled the position value is relative so we have minus figures.

				$('#vHeatmap').append('<div class="vHeatmapPoint" style="margin-top:'+ ribbonMargin + 'px" divPostID="'+ postID +'" ></div>'); // append the vertical heatmap with post id and author id information (don't forgetto create an onclick for this later on)
			 }
		}); 
	}

	if(mapType == 'keyword'){ // if mapType is -keyword- mapInfo is the text searched
		main.ClearKeywordSearch('#dMain'); 
		//console.log(mapInfo); // Works
		$('.threadText').each(function(){  // go through each post to see if the text contains the mapInfo text
			var postID = $(this).attr('level');
			var postContent =  $(this).children('.postTextWrap').children('.postMessageView').text();  // get post text
			postContent = postContent.toLowerCase(); // turn search items into lowercase
			var a=postContent.indicesOf(mapInfo);			
			// search for post text with the keyword text if there is a match get location information
			if(a != -1){
			    var divPosition = $(this).position();  // get the location of this div from the top
                //console.log(divPosition);
                var ribbonMargin = (divPosition.top) * boxHeight / totalHeight; // calculate a yellow ribbon top for the vertical heatmap
                $('#vHeatmap').append('<div class="vHeatmapPoint" style="margin-top:'+ ribbonMargin + 'px" divPostID="'+ postID +'" ></div>'); // append the vertical heatmap with post id and author id information (don't forgetto create an onclick for this later on)               
                $('.highlightblue').remove();
			    for(var i=0; i<a.length; a++){
			        var replaceText = $(this).children('.postTextWrap').children('.postMessageView').html(); 
				    // Find out if there is already a span for highlighting here				
				    var newSelected = '<search class="highlightblue">' + mapInfo + '</search>'; 
				    var n = replaceText.replace(RegExp(mapInfo, 'g'), newSelected); 
				    $(this).children('.postTextWrap').children('.postMessageView').html(n); 
				}
			}
		}); 
		
	}	
			  main.DrawShape();
}

String.prototype.indicesOf = function(key){
    if(this.indexOf(key)==-1)
        return -1;
    var instances = [];
    var str = this;
    for(var i=0; i<str.length; i++){
        var i = str.indexOf(key);
        if(i!=-1){
            if(i==str.lastIndexOf(key)){
                break;
            }
            instances.push(i);
            str = str.slice(i+key.length);
        }
    }
    return instances;
}

Dscourse.prototype.ClearKeywordSearch=function(selector)
{
 /* 
  * Clear keyword search properly 
  */
 	var main = this; 
	// remove search highlights		
	$(selector).find("search").each(function(index) {    // find search tag elements. For each
	    var text = $(this).html();				// get the inner html
	    $(this).replaceWith(text);			// replace it with the inner content. 
	});
}

Dscourse.prototype.DrawShape=function()	 			   
 {
 /* 
  * Draws the lines that connect scrollbox and the discussion window 
  */
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
  var scrollWidth = $('#vHeatmap').width();
  var correction = 27-scrollWidth; 
  var scrollBoxBottom = scrollBoxHeight + scrollBoxTop; // add the height to the top position to find the bottom. 
    // use getContext to use the canvas for rawing
    var ctx = canvas.getContext('2d');
    	// Clear the drawing
	    ctx.clearRect(0, 0, canvas.width, canvas.height);
	    // Options
	    ctx.lineCap = 'round';
	    ctx.lineWidth=2;
	    ctx.strokeStyle = 'rgb(179, 96, 64)';
	    // Top line
	    ctx.beginPath();
	    ctx.moveTo(scrollWidth+correction,scrollBoxTop+1);
	    ctx.lineTo(scrollWidth+26,1);
	    ctx.stroke();
	    ctx.closePath();
	    // Bottom line
	    ctx.beginPath();
	    ctx.moveTo(scrollWidth+correction,scrollBoxBottom+2);
	    ctx.lineTo(scrollWidth+26,linesHeight-1);
	    ctx.stroke();
	    ctx.closePath(); 
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
	d = (typeof date == "string")?new Date(date.split(' ').join('T')):new Date(date);				// Write out the date in readable form.
	//console.log(d);
	m = d.toDateString();
    curr_hour = d.getHours(); 
    dateString = m + '  ' + curr_hour + ':00';
    //console.log(dateString);
	return dateString;				 				    
}


Dscourse.prototype.FunctionTemplate=function()
{
 	var main = this; 
 
}

// SOMETHINGS BORROWED

Dscourse.prototype.GetCurrentDate=function()
{
	var x = new Date();
	var monthReplace = (x.getMonth() < 10) ? '0'+(x.getMonth()+1) : x.getMonth();
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
