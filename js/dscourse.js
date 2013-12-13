/*
*  All Course related code
*/
// Search for "TODO" in page for items that need attention

function Dscourse(lti) {

    // Set global variables
    var top = this;
    // For scope
    this.data = new Array();
    // Main discussion data wrapper
    this.timelineMin = 0;
    // Minimum value for the timeline
    this.timelineMax = 0;
    // Maximum value for the timeline
    this.sPostID// Synthesis post id global
    this.sPostContent// Synthesis post content global
    this.uParticipant = new Array();
    this.colors = [];
    // Unique list of participants.
    this.post = { };
    this.currentSelected = '';
    // Needed for selection
    this.currentStart = '';
    this.currentEnd = '';
    this.currentDrawing = '';
    // The drawing data that will be saved to the database.
    this.currentDrawData = '';
    // this is used for displaying drawings;
    this.currentMediaType = '';
    // What kind of media should be displayed.
    this.postMediaType = 'draw';
    // Used while saving the media type data.
    this.newPosts = '';
    // A string of the posts for a discussion that are new when refreshed. This variable is used to transfer post ids between functions.
    var discSettings = $.parseJSON(settings)
    var options = settings.options;
    if(options.length >0 ){
        this.options = {
            charLimit : parseInt(options.charLimit),
            synthesis : (options.useSynthesis=="Yes")?true:false,
            infoPanel : (options.showInfo=="Yes")?true:false,
            media : true,
            timeline : (options.useTimeline=="Yes")?true:false
        };
    }
    else{
        this.options = {
            charLimit : 500,
            synthesis : true,
            infoPanel : true,
            media : true,
            timeline : true
        }
    }
    this.charCount = true;
    //lti
    this.lti = lti;
    this.init = true;
    //This is used to handle redrawing of the vertical heatmap when media displays get closed
    //Maybe it will be useful down the road as we start adding views? 
    this.activeFilter = "";

    // Run initializing functions
    this.GetData(discID);
    // Load all discussion related information
    this.DiscResize();
    
     window.onblur = function() { 
     	top.AddLog('discussion',discID,'WindowBlur',0,' ');    
     }
     

    jQuery("abbr.timeago").timeago();
    // binds all abbr. tags with the timeago script.
    
    /************************ DISCUSSION EVENTS  ************************/
    /* Make the commenting box draggable */
    $('#commentWrap').prepend($('<div>', {
        class : 'commentWrapHandle'
    }));
    $('#commentWrap').draggable({
        handle : '.commentWrapHandle'
    });

    /* Add highlighted text to the comment */
    $('#highlightShow').live('mouseup', function() {
        var spannedText = $(this).find('span').text();
        //remove highlight from text
        $(this).find('span').replaceWith(spannedText);
        top.currentSelected = top.GetSelectedText();
        var element = document.getElementById("highlightShow");
        var range = top.GetSelectedLocation(element);
        top.currentStart = range.start;
        top.currentEnd = range.end;
        $('#locationIDhidden').val(top.currentStart + ',' + top.currentEnd);
        // Add location value to form value;
        var replaceText = $('#highlightShow').html();
        var newSelected = '<span class="highlight">' + top.currentSelected + '</span>';
        var n = replaceText.substring(0,range.start)+newSelected+replaceText.substring(range.end);
        $('#highlightShow').html(n);
    });

    /* Tooltips */
    $('#discussionDivs').tooltip({
        selector : "span",
        placement : 'bottom',
        html : true
    });
    $('#participants').tooltip({
        selector : "button",
        html : true
    });
    $('#shivaDrawPaletteDiv').tooltip({
        selector : "button"
    });

    /* When clicked on a post */
    $('.threadText').live('click', function(event) {
        event.stopPropagation();
        $('.threadText').removeClass('highlight');
        $('.threadText').find('span').removeClass('highlight');
        var postClickId = $(this).closest('div').attr('level');
        top.HighlightRelevant(postClickId);
        $(this).removeClass('agree disagree comment offTopic clarify').addClass('highlight');
    });

    /* When mouse hovers over the post */
    $('.threadText').live('mouseover', function(event) {
        event.stopImmediatePropagation();
        var postClickId = $(this).closest('div').attr('level');
        top.HighlightRelevant(postClickId);
        if(settings.status=="OK"){
            $(this).children('.sayBut2').show();
            if (!$(this).hasClass('lightHighlight')) {
                $(this).addClass('lightHighlight');
            }
        }
        $(this).find('.deletePostButton').show();
        var aID = $(this).attr('postauthorid');
        var pID = $(this).attr('level');
        var time = top.GetUniformDate(top.data.posts.filter(function(a){return a.postID == pID})[0].postTime) > new Date().getTime() - (15000+1000*240);
        if((aID == currentUserID && time)|| settings.role=="Instructor"||settings.role=="TA")        
            $(this).find('.editPostButton').show();
    });

    /* When mouse hovers out of the post */
    $('.threadText').live('mouseout', function(event) {
        event.stopImmediatePropagation();
        $(this).children('.sayBut2').hide();
        $(this).removeClass('lightHighlight');
        $(this).find('.deletePostButton').hide();
        $(this).find('.editPostButton').hide();
    });

    /* When there are new posts and a refresh is required */
    $('.refreshBox').live('click', function() {
        $(this).hide();
        var discID = $(this).attr('discID');
        top.GetData(discID);
        // We load our new discussion with all the posts up to date
    });

    // When the main window scrolls heatmap needs to redraw
    $('#dMain').scroll(function() {
        top.VerticalHeatmap();
        top.DrawShape();
    });

    /* Keyword search functionality within the discussion page */
    $('#keywordSearchText').live('keyup', function() {
        var searchText = $('#keywordSearchText').val();
        // get contents of the box
        if (searchText.length > 0 && searchText != ' ') {
            top.ClearVerticalHeatmap();
            //console.log('Search text: ' + searchText); // Works
            top.VerticalHeatmap('keyword', searchText);
            // Send text to the vertical heatmap app
        } else {
            top.ClearKeywordSearch('#dMain');
            top.ClearVerticalHeatmap();
        }
    });

    /* Adding a new post to the discussion */
    $('#addPost').live('click', function() {
        if (!top.charCount) {
            alert('You can\'t post because you went above the character limit');
        } else {
            var checkDefault = $('#text').val();
            // Check to see if the user is adding default comment text.
            var buttonType = $('#postTypeID > .active').attr('id');
            // If comment button has class active
            if (buttonType == 'comment') {
                if (checkDefault == 'Your comment...' || checkDefault == '') {
                    $('#text').addClass('textErrorStyle');
                    $('#textError').show();
                } else {
                    postOK();
                }
            } else {
                postOK();
            }
        }

        // if checks out then do it.
        function postOK() {
            $('.threadText').removeClass('highlight');
            if (checkDefault == 'Why do you agree?' || checkDefault == 'Why do you disagree?' || checkDefault == 'What is unclear?' || checkDefault == 'Why is it off topic?') {
                $('#text').val(' ');
            }
            top.AddPost();
            // Function to add post
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

	$('#hideRefreshMsg').live('click', function () {
		$('#checkNewPost').hide('');
	});
	
    /* When the comment box is clicked change the placeholder text */
    $('#text').live('click', function() {
        var value = $('#text').val();
        if (value == 'Why do you agree?' || value == 'Why do you disagree?' || value == 'What is unclear?' || value == 'Why is it off topic?' || value == 'Your comment...') {
            $('#text').val('');
        }
    });

    $('#text').on('keyup', function() {
        var value = $('#text').val();
        var charLength = value.length;
        $('#charCount').html(charLength);
        if (charLength > top.options.charLimit) {
            $('#charCount').css('color', 'red');
            top.charCount = false;
        } else {
            $('#charCount').css('color', 'black');
            top.charCount = true;
        }
    });

    /* When say button is clicked comment box appears and related adjustments take place */
    $('.sayBut2').live('click', function(e) {
        var discID = $('#dIDhidden').val();
        var dStatus = top.DiscDateStatus(discID);
        var postID, participate; 
	        if (dStatus != 'closed') {
		        	participate = (settings.status=="OK")?true:false; 
			        // Check if participate value if anyone or network			
			        if(participate == true){
					    $('#highlightDirection').hide();
			            $('#highlightShow').hide();
			            var postQuote = $(this).parent().children('.postTextWrap').children('.postMessageView').html();
			            postQuote = $.trim(postQuote);
			            var xLoc = e.pageX - 80;
			            var yLoc = e.pageY + 10;
			            $('#commentWrap').css({
			                'top' : '20%',
			                'left' : '30%'
			            });
			            $('.threadText').removeClass('highlight');
			            postID = $(this).attr("postID");
			            console.log(postID);
			            if (postQuote != '') {
			                $('#highlightDirection').show();
			                $('#highlightShow').show().html(postQuote);
			            }
			            $('#postIDhidden').val(postID);
			            $('#overlay').show();
			            $('#commentWrap').fadeIn('fast');
			            $(this).parent('.threadText').removeClass('agree disagree comment offTopic clarify').addClass('highlight');
			            $('#text').val('Your comment...');
			            $.scrollTo($('#commentWrap'), 400, {
			                offset : -100
			            });
			              top.AddLog('discussion',discID,'SayButtonClicked',postID,' '); //postID is the parent post. 
			        }
	        } else {
	            alert('This discussion is closed.');
	        }
	        console.log(participate);
    });

    /* When users cancels new post addition  */
    $('#postCancel').live('click', function() {
        $('.threadText').removeClass('highlight');
        $('#commentWrap').fadeOut();
        $('#overlay').hide();
        $('#shivaDrawDiv').hide();
        $('#shivaDrawPaletteDiv').hide();
        var postID = $('#postIDhidden').val(); 
        top.AddLog('discussion',discID,'CancelPost',postID,' '); 
        top.ClearPostForm();
    });

    /* When user decides to turn a comment into a synthesis post instead */
    $('#synthesize').live('click', function() {
        $('#synthesisPostWrapper').html('');
        // Clear existing posts
        $('#synthesisText').val('');
        $('#addSynthesis').removeAttr('synthesisID');
        // Get rid of comment tab -- we need to be able to carry the content and info for this.
        $('.threadText').removeClass('highlight');
        $('#commentWrap').fadeOut();
        $('#overlay').hide();
        $('#shivaDrawDiv').hide();
        $('#shivaDrawPaletteDiv').hide();
        // Synthesis side
        var synthesisFromID = $('#postIDhidden').val();
        // Get post from ID to the global variable
        var synthesisComment = $('#text').val();
        // Get comment content to the global variable
        var postQuote = $('div[level="' + synthesisFromID + '"]').children('.postTextWrap').children('.postMessageView').html();
        // Get the post content
        if (postQuote) {
            postQuote = top.truncateText(postQuote, 30);
            // Shorten the comment to one line.
        }
        $('#addSynthesis').show();
        $('#editSynthesisSaveButton').hide();
        // show editSynthesisSaveButton
        $('#addSynthesisButton').show();
        // hide addSynthesisButton

        $('.dCollapse').hide();
        // hide every dCollapse
        var selector = '.dCollapse[id="dSynthesis"]';
        $(selector).slideDown();
        // show the item with dTab id

        $('#synthesisText').val(synthesisComment);
        // Carry over synthesis comment text
        $('#spostIDhidden').val('0');
        // Set default from id as 0, this can be overridden below

        $('#synthesisText').on('click', function() {
            if ($(this).val() == 'Your comment...')
                $(this).val('');
        });

        // Populate the fields for the synthesis if the source is not top level
        if (synthesisFromID != 0) {
            $('#sPostIDhidden').val(synthesisFromID);
            $('#synthesisPostWrapper').prepend('<div sPostID="' + synthesisFromID + '" class="synthesisPosts">' + postQuote + ' <div>');
            // Append original post
        }
        top.ClearPostForm();
    });

    /* When user clicks on the posts inside synthesis */
    $('.synthesisPosts').live('click', function(event) {
        event.stopImmediatePropagation();
        var thisPost = $(this).attr('sPostID');
        var postRef = 'div[level="' + thisPost + '"]';
        $('#dMain').scrollTo($(postRef), 400, {
            offset : -100
        });
        $(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function() {
            $(this).removeClass('highlight animated flash').css('background-color', '#fff');
            $(this).dequeue();
        });
        $('.synthesisPosts').css('background-color', '#FAFDF0')// Original background color
        $(this).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)');
        // Change the background color of the clicked div as well.
    });

    /* When user clicks on discuss this post on synthesis */
    $('.gotoSynthesis').live('click', function(event) {
        event.stopImmediatePropagation();
        var thisPost = $(this).attr('gotoID');
        var postRef = 'div[level="' + thisPost + '"]';
        $('#dMain').scrollTo($(postRef), 400, {
            offset : -100
        });
        $(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function() {
            $(this).removeClass('highlight animated flash').css('background-color', '#fff');
            $(this).dequeue();
        });
    });

    /* Adding a new synthesis post */
    $('#addSynthesisButton').live('click', function() {
        top.AddSynthesis();
    });

    /* Adding a new synthesis post */
    $('#editSynthesisSaveButton').live('click', function() {
        top.EditSynthesis();
    });

    /* Editing a new synthesis post */
    $('.editSynthesis').live('click', function(event) {
        event.preventDefault();
        if(settings.status!="OK")
            return false;
        $('#dSidebar').animate({
            scrollTop : 0
        });

        // -- show the synthesis form
        $('#synthesisPostWrapper').html('');
        // Clear existing posts
        $('#synthesisText').val('');
        // Clear the comment box
        $('#addSynthesis').show();
        // show the form
        $('#editSynthesisSaveButton').show();
        // show editSynthesisSaveButton
        $('#addSynthesisButton').hide();
        // hide addSynthesisButton
        // -- fill form with the post information
        var postID = $(this).attr('sPostID');
        for (var j = 0; j < top.data.posts.length; j++) {// Go through all the posts
            var d = top.data.posts[j];
            if (d.postID == postID) {
                $('#synthesisText').val(d.postMessage);
                // Clear the comment box
                $('#sPostIDhidden').val(d.postFromId);
                $('#addSynthesis').attr('synthesisID', postID);
                top.ListSynthesisPosts(d.postContext, d.postID, 'edit');
            }

        }
    });

    /* Removing a post from synthesis */
    $('.removeSynthesisPost').live('click', function(event) {
        event.preventDefault();
        $(this).parent().remove();
    });

    /* Single synthesis wrapper click event */
    $('.showPosts').live('click', function() {
        if ($(this).hasClass('on') == true) {
            $(this).parents('.synthesisPost').children('.synthesisPosts').fadeOut();
            // hide posts
            $(this).removeClass('on').addClass('off');
            // add class off
            $(this).text('Show Posts')
        } else {
            $(this).parents('.synthesisPost').children('.synthesisPosts').fadeIn();
            // show posts
            $(this).removeClass('off').addClass('on');
            // add class on
            $(this).text('Hide Posts')
        }

    });

    /* Show which post is synthesized */
    $('.SynthesisComponent').live('click', function() {
        var thisPost = $(this).attr('synthesisSource');
        var postRef = '.synthesisPost[sPostID="' + thisPost + '"]';
        $('#dSidebar').scrollTo($(postRef), 400, {
            offset : -100
        });
        $(postRef).addClass('animated flash').css('border', '3px solid red').delay(5000).queue(function() {
            $(this).removeClass('highlight animated flash').css('border', '1px solid #ddd');
            $(this).dequeue();
        });
        $('#dInfo').fadeOut();
        // hide #dInfo
        $('#dSynthesis').fadeIn();
        // show #synthesis

    });

    /* When user cancels synthesis creation */
    $('#cancelSynthesisButton').live('click', function() {
        $('#addSynthesis').slideUp('fast');
        top.AddLog('discussion',discID,'Cancelsynthesis',0,' '); 
    });

    /* When user changes the option type for commenting */
    $('.postTypeOptions').live('click', function() {
        $('.postTypeOptions').removeClass('active');
        $(this).addClass('active');
        var thisID = $(this).attr('id');
        var txt = $('#text').val();
        if (txt == 'Why do you agree?' || txt == 'Why do you agree?' || txt == 'Why do you disagree?' || txt == 'What is unclear?' || txt == 'Why is it off topic?' || txt == 'Your comment...') {// Check is the text is still the default text; we don't want to override what they wrote.
            switch(thisID)// Get what kind of post this is
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
    $('.postTypeWrap').live('click', function() {
        var currentType = $(this).attr('typeID');
        var thisLink = $(this).children('.typicn');
        currentType = '.threadText[postTypeID="' + currentType + '"]';
        var parentDiv = $(this).parent('div').parent('.threadText');
        $(parentDiv).children(currentType).fadeToggle('fast', function() {
        });
        if (thisLink.hasClass('grey-icons') == true) {
            thisLink.removeClass('grey-icons');
        } else {
            thisLink.addClass('grey-icons');
        }
    });

    /* Add button effect to the post type information */
    $('.postTypeWrap').live('mousedown', function() {// This is just for style to make it look like a button.
        $(this).addClass('buttonEffect');
    });

    $('.postTypeWrap').live('mouseup', function() {
        $(this).removeClass('buttonEffect');
    });

    /* When show timeline button is clicked */
    $('#showTimeline').live('click', function() {
        $('#timeline').slideToggle().queue(function() {
            top.DiscResize();
            top.VerticalHeatmap();
            $(this).dequeue();
        });
        if ($(this).hasClass('active') == true) {
            $(this).removeClass('active');
            top.AddLog('discussion',discID,'showTimeline',0,'Off');
        } else {
            $(this).addClass('active');
            top.AddLog('discussion',discID,'showTimeline',0,'On');
        }
        top.DiscResize();
        top.VerticalHeatmap();
    });

    /* When show synthesis button is clicked */
    $('#showSynthesis').live('click', function() {
        $('#dInfo').fadeToggle();
        // toggle hide sidebar content
        $('#dSynthesis').fadeToggle();
        if ($(this).hasClass('active') == true) {
            $(this).html('Connected Posts');
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-warning');
            $(this).removeClass('active');
            top.AddLog('discussion',discID,'showSynthesis',0,'Off');
        } else {
            $(this).html('Information');
            $(this).removeClass('btn-warning');
            $(this).addClass('btn-primary');
            $(this).addClass('active');
            top.AddLog('discussion',discID,'showSynthesis',0,'On');
        }
        if ($('dSynthesis').is(':visible')) {

        } else {

        }
    });

    /* When media button is clicked to show media options */
    $('#media').live('click', function() {
        $('#commentWrap').hide();
        $('#mediaBox').show();
        var mHeight = $(window).height() - 200 + 'px';
        $('#mediaWrap').html('<iframe id="node" src="http://www.viseyes.org/shiva/draw.htm" width="100%" height="' + mHeight + '" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>');
        $('html, body').animate({
            scrollTop : 0
        });
        var postID = $('#postIDhidden').val(); 
        top.AddLog('discussion',discID,'MediaButtonClicked',postID,' '); // id is for which post it is clicked. 
    });

    /* Events to close the media section */
    $('#closeMedia').live('click', function() {
        $('#mediaBox').hide();
        $('#displayFrame').hide();
        $('#commentWrap').show();
        top.AddLog('discussion',discID,'CloseMediaPost',postID,' '); // id is for which post it is clicked. 
    });

    $('#closeMediaDisplay').live('click', function() {
        if(top.activeFilter=="keyword"){
            if($("#keywordSearchText").val()!="")
                $("#keywordSearchText").blur();
        }
        else
             $('.uList').filter(function(){return $(this).attr('active')=="true";}).click(); 
      
        $('#commentWrap').hide();
        $('#displayFrame').hide();
        top.AddLog('discussion',discID,'CloseMediaDisplay',postID,' '); // id is for which post it is clicked. 
    });

    /* User heatmap buttons */
    $('.uList').live('click', function() {
        $('.uList').attr('active', false);
        $(this).attr('active', true);
        var uListID = $(this).attr('authorId');
        top.ClearVerticalHeatmap();
        top.VerticalHeatmap('user', uListID);
        top.AddLog('discussion',discID,'userImageVmap',uListID,' '); 
    });

    /* The types of comments */
    $('.drawTypes').live('click', function() {// This needs to be readded - TODO
        top.postMediaType = 'draw';
        var mHeight = $(window).height() - 200 + 'px';
        $('.drawTypes').removeClass('active');
        var drawType = $(this).attr('id');
        // New iframe way
        switch(drawType)// Get what kind of iframe this is
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
        var html = '<iframe id="node" src="http://www.viseyes.org/shiva/' + type + '.htm" width="100%" height="' + mHeight + '" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>';
        $('#mediaWrap').html(html);
        top.postMediaType = type;
        $(this).addClass('active');
    });

    /* Continue to adding post when the drawing is done. Saves drawing data into the post */
    $('#continuePost').live('click', function() {
        top.currentDrawing = '';
        ShivaMessage('node', 'GetJSON');
        //console.log(top.currentDrawing);
        $('#mediaBox').hide();
        $('#commentWrap').show();
        top.AddLog('discussion',discID,'MediaContinue',0,' ');
    });

    /* Cancel drawing */
    $('#drawCancel').live('click', function() {
        top.currentDrawing = '';
        $('#mediaBox').hide();
        $('#commentWrap').show();
    });

    /* Adding the shiva framework for chosen media type. This is important for media drawing */
    $('.mediaMsg').live('click', function(event) {
        event.stopImmediatePropagation();
        var postId = $(this).closest('.threadText').attr('level');
        top.currentDrawData = '';
        top.currentMediaType = 'Draw';
        var cmd;
        $('#displayDraw').html('').append('<iframe id="displayIframe" src="http://www.viseyes.org/shiva/go.htm" width="100%" frameborder="0" marginwidth="0" marginheight="0">Your browser does not support iframes. </iframe>');
        var i, o;
        for ( i = 0; i < top.data.posts.length; i++) {
            o = top.data.posts[i];
            if (o.postID == postId) {
                //console.log(o.postMedia);
                cmd = "PutJSON=" + o.postMedia;
                $('#displayFrame').show();
                $('html, body').animate({
                    scrollTop : 0
                });
            }
            $('#displayIframe').load(function() {
                document.getElementById('displayIframe').contentWindow.postMessage(cmd, "*");
            }).queue(function() {
                top.DiscResize();
                top.VerticalHeatmap();
                $('#containerDiv').css('width', '100% !important');
                $(this).dequeue();
            });
        }
    });

    /* When items in the recent contents section are clicked */
    $('#recentContent li').live('click', function() {
        var postID = $(this).attr('postid');
        var postRef = 'div[level="' + postID + '"]';
        $('#dMain').scrollTo($(postRef), 400, {
            offset : -100
        });
        $(postRef).removeClass('agree disagree comment offTopic clarify').addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function() {
            $(this).removeClass('highlight animated flash').css('background-color', '#fff');
            $(this).dequeue();
        });
    });

    /*  Hide refresh message. Is this not used anymore? Check TODO */
    $('#hideRefreshMsg').live('click', function() {
        $('#checkNewPosts').hide('');
    });

    /*  Vertical Heatmap scrolling */
    $('.vHeatmapPoint').live('click', function() {
        var postID = $(this).attr('divpostid');
        var postRef = 'div[level="' + postID + '"]';
        $('#dMain').scrollTo($(postRef), 400, {
            offset : -100
        });
        $(postRef).removeClass('agree disagree comment offTopic clarify').addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function() {
            $(this).removeClass('highlight animated flash').css('background-color', '#fff');
            $(this).dequeue();
        });
        $('.vHeatmapPoint').removeClass('highlight');
        $(this).addClass('highlight');
    });

    /* Show synthesis post numbers next to post. Needs event control, hide and show propagate. TODO */
    $('.synthesisWrap').live('mouseover', function(event) {
        $(this).children('span').fadeIn('slow');
    });

    $('.synthesisWrap').live('mouseout', function(event) {
        $(this).children('span').fadeOut('slow');
    });
    
    $('.synthesisWrap').on('childClick', function(item){
            console.log(item);
            var thisPost = item.attr('synthesissource');
            var postRef = '.synthesisPost[sPostID="' + thisPost + '"]';
            $('#dSidebar').scrollTo($(postRef), 400, {
                offset : -100
            });
            $(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function() {
                item.removeClass('highlight animated flash').css('background-color', 'whitesmoke');
                item.dequeue();
            });
            $('#dInfo').fadeOut();
            // hide #dInfo
            $('#dSynthesis').fadeIn();
            // show #synthesis
    });

    $(window).resize(function() {// When window size changes resize
        top.DiscResize();
        top.VerticalHeatmap();

    });
    
     //Mentions
     //prepare a list of users for the Mentions.js plugin
    var users = $.map(discUsers, 
        function(user){
            return {name: user['firstName']+' '+user['lastName'], username: user.username.split('@')[0]}  
        }
    );
    $('textarea').mention({
        queryBy: ['name'],
        sensitive: true,
        delimiter: '@',
        users: users,
        typeaheadOpts: {
            updater: function(item){
                        //most of this is borrowed, with some hacks
                        var data = this.query,
                        caratPos = this.$element[0].selectionStart,
                        i;
                        for (i = caratPos; i >= 0; i--) {
                            if (data[i] == '@') {
                                break;
                            }
                        }
                        var replace = data.substring(i, caratPos);
                        var textBefore = data.substring(0, i);
                        var textAfter = data.substring(caratPos);
                        var u = users.filter(function(a){
                            return a['username'] == item;
                        })[0];
                        data = textBefore + '@' + u['name'] + textAfter;
                        
                        this.tempQuery = data;

                        return data;
                    }
        }
    });
         
}// End function Dscourse()


Dscourse.prototype.GetData = function(discID) {
    /*
     *	Loads all the data needed for the discussion page
     */

    var main = this;

    // Ajax call to get data and put all data into json object
    $.ajax({// Add user to the database with php.
        type : "POST",
        url : "php/data.php",
        data : {
            action : 'getData',
            discID : discID
        },
        success : function(data) {// If addNewUser.php was successfully run, the output is printed on this page as notification.
            main.data = data;
            main.SingleDiscussion(discID);
            //console.log(main.data);
            main.AddLog('discussion',discID,'getData',0,' ') // Add Log
        },
        error : function(xhr, status) {// If there was an error
            console.log('There was an error talking to data.php');
            console.log(xhr);
            console.log(status);
        }
    });

}

Dscourse.prototype.SingleDiscussion = function(discID)// View for the Individual discussions.
{
    /*
     *	Prints out the components of the discussion page. Has multiple dependancies on other functions in Dscourse object.
     */
    var main = this;

    $('#charCountTotal').html('/ ' + main.options.charLimit);
    // Add character limit information to commentwrap
    $('.levelWrapper[level="0"]').html('');
    // Empty the discussion div for refresh
    var o, userRole, dStatus;
    o = main.data.discussion;
    // We have one discussion data loaded for this discussion
    $('#dTitleView').html(o.dTitle);
    // Discussion title
    $('#dPromptView').html('<b> Prompt: </b>' + o.dPrompt);
    // Discussion prompt
    $('#dIDhidden').val(o.dID);
    // discussion ID, we need this for form input when adding posts
    var dCourse = main.listDiscussionCourses(discID);
    // Courses that this discussion is under
    $('#dCourse').html('<b> Course: </b>' + dCourse);
    // Print out the courses
    $('#dSDateView').html('<b> Start Date: </b>' + main.FormattedDate(o.dStartDate));
    // Start Date
    $('#dODateView').html('<b> Open to Class: </b>' + main.FormattedDate(o.dOpenDate));
    // Open date (when discussion opens to everyone)
    $('#dCDateView').html('<b> End Date: </b>' + main.FormattedDate(o.dEndDate));
    // End date
    //main.CurrentDiscussion = o.dID;		 							// Set gloabl with current discussion. What is this for?
    var shortName = main.truncateText(o.dTitle, 50);
    // Get short name for the navigation
    $('#navLevel3').text(shortName).attr('dLinkID', o.dID).css({
        'display' : 'block'
    });
    // Enter the shortname to the navigation

    // Get Discussion Status, can be one of three: all, student, closed.
    dStatus = main.DiscDateStatus(o.dID);

    // Print note for the page saying the discussion status
    $('#discStatus').removeClass('label label-error label-warning label-success').html('');
    switch(dStatus) {
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
    if (main.data.posts) {
        main.ListDiscussionPosts(dStatus, userRole, o.dID);
        // Main function for listing discussions -- very important!
        /* Check if timeline needs to be drawn  */
        if (dStatus == 'all' || dStatus == 'closed') {
            main.DrawTimeline();
        } else {
            $('#amount').val('Timeline is disabled.');
        }
    } else {
        $('.levelWrapper').append("<div id='nodisc' class='alert alert-info'> There are not posts in this discussion yet. Be the first one and add your voice by clicking on the <b>'Say'</b> button at the top (next to the discussion title)</div>");
    }

    setInterval(function(){main.CheckNewPosts(discID, userRole, dStatus)},5000); // Checking for new posts... 

     main.AddLog('discussion',discID,'view',0,' ');
     
}

Dscourse.prototype.ListDiscussionPosts = function(dStatus, userRole, discID)// View for the Individual discussions.
{
    /*
     *	Lists posts for the discussion. This is the main function that builds the post view
     */
    var main = this;

    main.uParticipant = [];
    $('.singleDot').remove();
    // Clear all dots in the timeline
    main.timelineMin = 0;
    main.timelineMax = 0;
    // Clear timeline range
    $('#participantList').html('<button class="btn disabled">Participants: </button>');
    // Clear participant list
    var j, p, d, q, typeText, authorID, message, authorThumb, synthesisCount;

    //clear recent posts
    $('#recentContent').html('');
    var timeSince;
    if(lastView!="never")
        timeSince = main.GetUniformDate(lastView, false);
    else    
        timeSince = "never";        
    
    var colors = 0;
    
    for ( j = 0; j < main.data.posts.length; j++) {// Go through all the posts
        d = main.data.posts[j];

        /********** TIMELINE ***********/
        var n = d.postTime;
        n = n.replace(/-/g, "/");
        // Correst the time input format
        var time = Date.parse(n);
        // Parse for browser.
        if (main.timelineMin == 0) {// Check and set minimum value for time
            main.timelineMin = n;
        } else if (time < ((typeof main.timelineMin =="string")?new Date(main.timelineMin).getTime():main.timelineMin)) {
            main.timelineMin = n;
        }
        if (main.timelineMax == 0) {// Check and set maximum value for time
            main.timelineMax = n;
        } else if (time > ((typeof main.timelineMax =="string")?new Date(main.timelineMax).getTime():main.timelineMax)) {
            main.timelineMax = n;
        }
        // END TIMELINE
        
        /********** DISCUSSION SECTION ***********/
        var authorID = main.getName(d.postAuthorId, 'first');
        // Get Authors name
        authorIDfull = main.getName(d.postAuthorId);
        authorThumb = main.getAuthorThumb(d.postAuthorId, 'small');
        // get thumbnail html
        authorThumb += '  ' + authorIDfull;
        switch(d.postType)// Get what kind of post this is
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
        if (d.postMessage != ' ') {// Proper presentation of the message URL
            message = d.postMessage;
            //message = main.showURL(message);
            message = message.replace("\n", "<br />");
        } else {
            continue;
            // Hide the post if there is no text in the message
            switch(d.postType)// Get what kind of post this is
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

        var topLevelMessage = ' ';
        // Assign a class for top level messages for better organization.
        if (d.postFromId == '0') {
            topLevelMessage = 'topLevelMessage';
        }

        // Check if this post has selection
        var selection = '';
        /* if(d.postSelection.length > 1){
        selection = ' <span href="#" rel="tooltip" title="This post has highlighted a segment in the parent post. Click to view." class="selectionMsg" style="display:none;">a</span> ';
        } */

        // Check if this post has media assigned.
        var media = '';
        if (d.postMedia.length > 1) {
            media = '<span href="#" rel="tooltip" title="This post has a ' + d.postMediaType + ' media attachment. Click to view." class="mediaMsg"> ' + d.postMediaType + '  <span class="typicn tab "></span> </span> ';
        }

        var showPost = 'yes';
        var userRoleAuthor = main.UserCourseRole(discID, currentUserID);
        if (dStatus == 'student' && currentUserID != d.postAuthorId && userRoleAuthor == 'Student') {
            if (userRole == 'Student' || userRole == 'unrelated') {
                showPost = 'no';
            }
        }
        //console.log(showPost);
        //console.log(userRole);

        // Is this post part of any synthesis?
        var synthesis = '';
        synthesis = main.PostInSynthesis(d.postID);

        if (showPost == 'yes') {

            var selector = 'div[level="' + d.postFromId + '"]';
            var responses = main.ResponseCounters(d.postID);

            $(selector).append(// Add post data to the view
            		'<div class="threadText ' + topLevelMessage + '" level="' + d.postID + '" postTypeID="' + d.postType + '" postAuthorId="' + d.postAuthorId + '" time="' + time + ' ">'
            		+  '<div class="postTypeView" slevel="' + d.postID + '"> ' + typeText + '</div>' 
            		+  '<div class="postTextWrap">'
            			 + '<span class="postAuthorView" rel="tooltip"  title="' + authorThumb + '"> ' + authorID + '</span>' 
            			 + '<span class="postMessageView"> ' + message + '</span>'
            			 + ((userRole == 'Instructor' || userRole == "TA")?'<i class="icon-trash deletePostButton" style="float:right; position:relative;top: 3px"></i>':'') 
            			 + '<i class="icon-edit editPostButton" style="float:right; position: relative;top:3px; right:5px"></i>'
            			 + media + selection + synthesis 
            		 + '</div>' 
            		 + ' <button class="btn btn-small btn-success sayBut2" style="display:none" postID="' + d.postID + '"><i class="icon-comment icon-white"></i> </button> '
            		  + '<div class="responseWrap" >' + responses + '</div>' 
            		+ '</div>'
            );

            /********** SYNTHESIS POSTS ***********/
            if (d.postType == 'synthesis') {
                if ((currentUserID == d.postAuthorId) || (userRoleAuthor == 'Instructor' || userRoleAuthor == 'TA')) {
                    var editPostButton = '<button class="btn btn-small editSynthesis" sPostID="' + d.postID + '">Edit</button> ';
                } else {
                    var editPostButton = '';
                }

                $('#synthesisList').prepend('<div class="synthesisPost " sPostID="' + d.postID + '">' + editPostButton + '<span class="postAuthorView" rel="tooltip" > ' + authorThumb + '</span>' + '		<p class="synthesisP">' + message + '</p>' + '		<div class="synthesisButtonWrap"> <span class="gotoSynthesis synButton" gotoID="' + d.postID + '"> Go to Post </span><span class="showPosts synButton">Show Posts</span></div>' + '	</div>');
                main.ListSynthesisPosts(d.postContext, d.postID, 'add');
                synthesisCount = 'some';
            }

            /********** RECENT ACTIVITY SECTION ***********/
           //$('#recentContent').html('')
            /*if ($(selector).length > 0) {
                //console.log(n);
                var range = main.data.posts.length - 8;
                // How many of the most recent we show + 1
                var t = new Date(0);
                t.setUTCMilliseconds(main.GetUniformDate(d.postTime));
                var prettyTime = jQuery.timeago(t);
                var shortMessage = main.truncateText(message, 60);
                if (j > range) {// person + type + truncated comment + date
                    var activityContent = '<li postid="' + d.postID + '">' + main.getAuthorThumb(d.postAuthorId, 'tiny') + ' ' + authorID + ' ' + typeText + ' <b>' + shortMessage + '</b> ' + '<em class="timeLog">' + prettyTime + '<em></li> ';
                    $('#recentContent').prepend(activityContent);
                }
            }*/
            var pTime = main.GetUniformDate(d.postTime);
            //if this was posted since the user last viewed the discussion
            if(timeSince!="never" && pTime > timeSince){
                var t = new Date(0);
                t.setUTCMilliseconds(main.GetUniformDate(d.postTime));
                var prettyTime = jQuery.timeago(t);
                var shortMessage = main.truncateText(message, 60);
                var activityContent = '<li postid="' + d.postID + '">' + main.getAuthorThumb(d.postAuthorId, 'tiny') + ' ' + authorID + ' ' + typeText + ' <b>' + shortMessage + '</b> ' + '<em class="timeLog">' + prettyTime + '<em></li> ';
                $('#recentContent').prepend(activityContent);
            }
            else if(timeSince=="never" && $('#recentContent').children().length < 9){
                var t = new Date(0);
                t.setUTCMilliseconds(main.GetUniformDate(d.postTime));
                var prettyTime = jQuery.timeago(t);
                var shortMessage = main.truncateText(message, 60);
                var activityContent = '<li postid="' + d.postID + '">' + main.getAuthorThumb(d.postAuthorId, 'tiny') + ' ' + authorID + ' ' + typeText + ' <b>' + shortMessage + '</b> ' + '<em class="timeLog">' + prettyTime + '<em></li> ';
                $('#recentContent').prepend(activityContent);
            }
            /********** UNIQUE PARTICIPANTS SECTION ***********/
            var arrayState = jQuery.inArray(d.postAuthorId, main.uParticipant);
            // Chech if author is in array
            if (arrayState == -1) {// if the post author is not already in the array
                main.uParticipant.push(d.postAuthorId);
                // add id to the array
            }

        } // end if showpost.
    }// End looping through posts
    
    $('.deletePostButton').on('click', function(){
       var del = confirm("Are you sure you would like to delete this post? This option is irreversible."); 
       if(del){
            $.ajax({
                type: 'POST',
                url: 'php/data.php',
                data: {
                    action: 'delete',
                    context: 'post',
                    contextID: $(this).parent().parent().attr('level')
                },
                success: function(data){
                    //remove deleted post
                    main.data.posts = main.data.posts.filter(function(a){
                       return parseInt(a.postID) != data; 
                    });
                    //re-draw
                    var currentDisc = $('#dIDhidden').val();
                    main.SingleDiscussion(currentDisc);
                    main.DiscResize();
                    main.VerticalHeatmap();                 
                },
                error: function(xhr){
                    console.log(xhr);    
                }
            })    
       }
    });
    $('.deletePostButton').hide();
    $('.editPostButton').on('click', function(e){
        var parentPostID = $(this).parent().parent().attr('level');
        var parentPost = main.data.posts.filter(function(a){return a.postID == parentPostID})[0];
        /* can't put spans in a <textarea> 
        var colors = ['#FFFFB1', '#D8FFB1', '#B1FFB1', '#B1FFD8', '#B1FFFF', '#B1D8FF', '#B1B1FF', '#D8B1FF', '#FFB1FF', '#FFB1D8', '#FFB1B1', '#FFD8B1'];
        var highlights = $(this).closest('.threadText').find('.postTypeView').filter(function(){
            var a  = this;
            var those = main.data.posts.filter(function(b){
                return b.postID == $(a).attr('slevel')
            });
            return those[0].postSelection != '';
        }).map(function(){
            var a = this;
            return main.data.posts.filter(function(b){
               return b.postID==$(a).attr('slevel');
           }).map(function(a){
               var sel = a.postSelection.split(','); 
               return {start: sel[0], stop: sel[1]}
           }); 
        });
        $.each(highlights, function(i, val){
           parentPostMessage = parentPostMessage.substring(0,val.start)+
           '<span class="highlight" style="background-color:'+colors[i]+'">'
           +parentPostMessage.substring(val.start, val.stop)
           +'</span>'
           +parentPostMessage.substring(val.stop);
        });
        */
        var discID = $('#dIDhidden').val();
        var dStatus = main.DiscDateStatus(discID);
        var postID, participate, fromId; 
            if (dStatus != 'closed') {
                    participate = (settings.status=="OK")?true:false; 
                    // Check if participate value if anyone or network          
                    if(participate == true){
                        $('#highlightDirection').hide();
                        $('#highlightShow').hide();
                        var postQuote = $(this).parent().children('.postTextWrap').children('.postMessageView').html();
                        postQuote = $.trim(postQuote);
                        var xLoc = e.pageX - 80;
                        var yLoc = e.pageY + 10;
                        $('#commentWrap').css({
                            'top' : '20%',
                            'left' : '30%'
                        });
                        $('.threadText').removeClass('highlight');
                        
                        fromId = $(this).parent().parent().parent().attr('level'); 
                        postID =  "EDIT|||"+$(this).parent().parent().attr('level')+'|||'+fromId;
                        
                        if (postQuote != '') {
                            $('#highlightDirection').show();
                            $('#highlightShow').show().html(postQuote);
                        }
                        $('#postIDhidden').val(postID);
                        $('#overlay').show();
                        $('#commentWrap').fadeIn('fast');
                        $(this).parent('.threadText').removeClass('agree disagree comment offTopic clarify').addClass('highlight');
                        $('#text').val(parentPost.postMessage);
                        $.scrollTo($('#commentWrap'), 400, {
                            offset : -100
                        });
                        main.AddLog('discussion',discID,'SayButtonClicked',postID,' '); //postID is the parent post. 
                    }
            } else {
                alert('This discussion is closed.');
            }
    });        
    $('.editPostButton').hide();
    
    if($("#recentContent").children().length==0){
        $("#recentPostsHeader").html("Recent posts");
        $("#recentContent").append('<span>There are no new posts since you last visited</span>');
    }
    else if(!main.init){
        $("#recentPostsHeader").html("Recent posts");
    }
    else{
        //Build the recentPosts header
        if(lastView!='never')   
            $('#recentPostsHeader').html("Posts since you visited "+jQuery.timeago(new Date(main.GetUniformDate(lastView, false))));
        else{
            $('#recentPostsHeader').html("Posts since before you joined");
        }
    }
    if (synthesisCount == 'some') {
        $('#synthesisHelpText').hide();
    }

    main.timelineValue = main.timelineMax;
    // Set the timeline value to the max.
    $(".postTypeView").draggable({
        start : function() {
            //console.log('Drag started');

            main.sPostID = $(this).attr('slevel');
            // The id of the post
            main.sPostContent = $(this).parent().children('.postTextWrap').children('.postMessageView').html() // The content of the post
        },
        drag : function() {
            //console.log('Drag happening');
        },
        stop : function() {
            //console.log('Drag stopped!');
        },
        revert : 'invalid',
        containment : '#discussionWrap',
        helper : function(event) {
            var contents = $(this).siblings('.postTextWrap').html();
            return $('<div class="draggablePost">' + contents + ' </div>');
        },

        appendTo : '#discussionWrap'
    });
    $("#synthesisDrop").droppable({
        hoverClass : "sDropHover",
        tolerance : 'touch',
        drop : function(event, ui) {
        	main.AddLog('discussion',discID,'SynthesisDrop',main.sPostID,' '); 
            var shortText = main.truncateText(main.sPostContent, 100);
            var ids = [];
            $('#synthesisPostWrapper').children('.synthesisPosts').each(function() {
                ids.push($(this).attr('spostid'));
            });
            var instr = "Drag and drop posts here to add to synthesis.";
            var box = $(this);
            if (ids.indexOf(main.sPostID) == -1) {
                $('#synthesisPostWrapper').prepend('<div sPostID="' + main.sPostID + '" class=" synthesisPosts">' + shortText + ' <div>');
                // Append original post
                $(this).html("Added!");
            } else
                $(this).html('Already added!');
            window.setTimeout(function() {
                box.html(instr);
            }, 2000);
        }
    });
    main.DiscResize();
    main.VerticalHeatmap();
    if(typeof goTo != "undefined" && main.init){
        var p = $('.threadText[level='+goTo+']');
        p.click();
        $('#dMain').scrollTo(p, 400, {
            offset : -100
        });   
    }
}

Dscourse.prototype.AddPost = function() {

    var main = this;
    var currentDisc = $('#dIDhidden').val();

    // If there are no posts in this discussion create posts array
    if (!main.data.posts) {
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

    if (formVal !== undefined) {
        postType = formVal;
    }

    // locationIDhidden -- postSelection
    var postSelection = $('#locationIDhidden').val();
    console.log(postSelection);
    if (postSelection == '0,0') {// fix for firefox and fool proofing in case nothing is actually selected.
        postSelection = '';
    }

    // Get drawing value
    var postMedia;
    postMedia = (main.currentDrawing!="Unrecognized")?main.currentDrawing:"";

    var postContext = '';
    // this is used for the synthesis posts but needs a value here.

    // Create post object and append it to allPosts
    
    var post = {
        'postFromId' : postFromId,
        'postAuthorId' : postAuthorId,
        'postMessage' : postMessage.replace('\'', '\\\''),
        'postType' : postType,
        'postSelection' : postSelection,
        'postMedia' : postMedia,
        'postMediaType' : main.postMediaType,
        'postContext' : postContext
    };
    
     if(/EDIT/.test(postFromId)){
     	console.log('postfromID value: ' + postFromId);
        main.EditPost({
            postID: postFromId.split('|||')[1],
            postFromId : postFromId.split('|||')[2],
            postAuthorId : postAuthorId,
            postMessage : postMessage.replace('\'', '\\\''),
            postType : postType,
            postSelection : postSelection,
            postMedia : postMedia,
            postMediaType : main.postMediaType,
            postContext : postContext 
        }, currentDisc);       
    } else {
    // if the post is not edit then save new. 
		$.ajax({
        type : "POST",
        url : "php/data.php",
        data : {
            post : post,
            action : 'addPost',
            currentDiscussion : currentDisc
        },
        success : function(data) {// If connection is successful .
            post.postMessage = post.postMessage.replace(/\W/g,function(match){return match.replace('\\','');});
            console.log(post);
            post.postTime = main.GetCurrentDate();
            post.postID = data;
            main.data.posts.push(post);
            //console.log(data);
            console.log(data);

            $('.levelWrapper[level="0"]').html('');
            main.SingleDiscussion(currentDisc);
            main.DiscResize();
            main.VerticalHeatmap();
            var divHighlight = 'div[level="' + data + '"]';
            $(divHighlight).removeClass('agree disagree comment offTopic clarify').addClass('highlight animated flash');
            $.scrollTo($(divHighlight), 400, {
                offset : -100
            });
            main.AddLog('discussion',currentDisc,'addPost',data,' ');
            
            //extract mentions from the postBody
            var mentions = [];
            for(var i=0; i<discUsers.length;i++){
            var u = discUsers[i];
            if(RegExp('@'+u['firstName']+' '+u['lastName']).test(postMessage))
                mentions.push(u['UserID']);
            }
            $.ajax({
                type: 'POST',
                url: 'php/data.php',
                data:  {
                    post: post,
                    action: 'mention',
                    mentions: mentions
                },
                success: function(data){
                    console.log(data);
                },
                error: function(xhr){
                    console.log(xhr);
                }
            });
        },
        error : function(xhr) {// If connection is not successful.
            main.AddLog('discussion',currentDisc,'addPost','','Error: Dscourse Log: the connection to data.php failed. ');
            //console.log("Dscourse Log: the connection to data.php failed.");
        }
    });   
    
    
    }
    
    // run Ajax to save the post object
 
    main.init = false;
}

Dscourse.prototype.ClearPostForm = function() {
    var main = this;
    $('#commentWrap').find('input:text, input:password, input:file, select, textarea').val('');
    $('.postBoxRadio').removeAttr('checked');
    // Restore checked status to comment.
    $('#postTypeID > button').removeClass('active');
    $('#postTypeID > #comment').addClass('active');
    $('#highlightShow').html(' ');
    $('#text').removeClass('textErrorStyle');
    $('#textError').hide();
}

Dscourse.prototype.AddSynthesis = function() {// Revise for synthesis posts

    var main = this;

    main.sPostID = '';
    main.sPostContent = '';

    var currentDisc = $('#dIDhidden').val();

    // Get post values from the synthesis form.
    // postID -- postFromId
    var postFromId = $('#sPostIDhidden').val();
    // Done

    // author ID -- postAuthorId -- this is the session user
    var postAuthorId = $('#userIDhidden').val();
    // Done
    var postMessage = $('#synthesisText').val();
    // Done

    // type -- postType
    var postType = 'synthesis';

    // locationIDhidden -- postSelection
    var postSelection = ' ';
    // Not done but works.

    var postMedia = '';
    // Synthesis doesn't have media yet.

    var postContext = '';

    $('#synthesisPostWrapper > .synthesisPosts').each(function() {
        if (postContext.length > 0) {
            postContext += ',';
        }
        var thisPostID = $(this).attr('sPostID');
        postContext += thisPostID;

    });
    //console.log('post context ' + postContext);

    // Create post object and append it to allPosts

    post = {
        'postFromId' : postFromId,
        'postAuthorId' : postAuthorId,
        'postMessage' : postMessage,
        'postType' : postType,
        'postSelection' : postSelection,
        'postMediaType' : main.postMediaType,
        'postMedia' : postMedia,
        'postContext' : postContext
    };

    // run Ajax to save the post object

    $.ajax({
        type : "POST",
        url : "php/data.php",
        data : {
            post : post,
            action : 'addPost',
            currentDiscussion : currentDisc
        },
        success : function(data) {// If connection is successful .
            post.postTime = main.GetCurrentDate();
            post.postID = data;
            main.data.posts.push(post);

            $('#addSynthesis').slideUp('fast');   // Slide up the form, it will be cleared when new synthesis is created
            $('#synthesisList').html(' '); 			// Empty synthesis list
            $('.levelWrapper[level="0"]').html('');  // Empty discussion
            main.SingleDiscussion(currentDisc);		// Rebuild the page
            main.DiscResize();						// Rebuild the sizes of object
            main.VerticalHeatmap();					// Rebuild the heatmap
            var divHighlight = 'div[level="' + data + '"]';
            $(divHighlight).removeClass('agree disagree comment offTopic clarify').addClass('highlight animated flash');
            $.scrollTo($(divHighlight), 400, {
                offset : -100
            });
        },
        error : function(data) {// If connection is not successful.
            console.log(data);
            console.log("Dscourse Log: the connection to data.php failed. Did not save synthesis");
        }
    });
}

Dscourse.prototype.EditSynthesis = function() {// Revise for synthesis posts

    var main = this;

    main.sPostID = '';
    main.sPostContent = '';

    var currentDisc = $('#dIDhidden').val();
    var editPostID = $('#addSynthesis').attr('synthesisID');

    // Get post values from the synthesis form.
    // postID -- postFromId
    var postFromId = $('#sPostIDhidden').val();
    // Done

    // author ID -- postAuthorId -- this is the original poster
    var postAuthorId = main.data.posts.filter(function(item){
        return item.postID == editPostID;  
    }).pop().postAuthorId;
    // Done
    var postMessage = $('#synthesisText').val();
    // Done

    // type -- postType
    var postType = 'synthesis';

    // locationIDhidden -- postSelection
    var postSelection = ' ';
    // Not done but works.

    var postMedia = '';
    // Synthesis doesn't have media yet.

    var postContext = '';

    $('#synthesisPostWrapper > .synthesisPosts').each(function() {
        if (postContext.length > 0) {
            postContext += ',';
        }
        var thisPostID = $(this).attr('sPostID');
        postContext += thisPostID;

    });

    // Create post object and append it to allPosts

    post = {
        'postID' : editPostID,
        'postFromId' : postFromId,
        'postAuthorId' : postAuthorId,
        'postMessage' : postMessage,
        'postType' : postType,
        'postSelection' : postSelection,
        'postMediaType' : main.postMediaType,
        'postMedia' : postMedia,
        'postContext' : postContext
    };

    // run Ajax to save the post object

    $.ajax({
        type : "POST",
        url : "php/data.php",
        data : {
            post : post,
            action : 'editPost',
            currentDiscussion : currentDisc
        },
        success : function(data) {// If connection is successful .
            location.reload();
            /*
             $('#addSynthesis').slideUp('fast');   // Hide the form
             console.log('is this happening?');
             $('.levelWrapper[level="0"]').html(''); // redraw the discussion page at synthesis
             main.SingleDiscussion(currentDisc);
             main.DiscResize();
             main.VerticalHeatmap();
             */

        },
        error : function(data) {// If connection is not successful.
            console.log(data);
            console.log("Dscourse Log: the connection to data.php failed. Did not edit synthesis");
        }
    });
    main.AddLog('discussion',discID,'EditSynthesis',editPostID,' '); 

}

Dscourse.prototype.DrawTimeline = function()// Draw the timeline.
{
    /*
     *	Draw the timeline for the selected discussion
     */
    var main = this;

    // Let's make the step a division between the max and min numbers.
    var min = new Date(main.timelineMin).getTime();
    var max = new Date(main.timelineMax).getTime();
     var step = (max - min) / 100;

    // Create the Slider
    $("#slider-range").slider({// Create the slider
        range : "min",
        //step: step,
        value : max,
        min : min,
        max : max,
        slide : function(event, ui) {
            var date = main.GetUniformDate(ui.value);
            date = main.ToTimestamp(date);
	    date = main.FormattedDate(date); 
            $("#amount").val(date);
            $('.threadText').each(function(index) {
                var threadID = $(this).attr('time');
                if (threadID > ui.value) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
            main.ClearVerticalHeatmap();
            main.VerticalHeatmap('user', $('.uList[active="true"]').attr('authorid'));
        },
        stop : function() {

        }
    });

    // Show the value on the top div for reference.
    var initialDate = (main.ToTimestamp(main.GetUniformDate(main.timelineMax)));
    	initialDate = main.FormattedDate(initialDate); 

	$("#amount").val(initialDate);

    var j, d;
    for ( j = 0; j < main.data.posts.length; j++) {// Go through all the posts
        d = main.data.posts[j];
        //add dot on the timeline for this post
        var n = d.postTime;
        n = n.replace(/-/g, "/");
        //n = main.ParseDate(n, 'yyyy/mm/dd');
        var time = Date.parse(n);
        var minTime = main.timelineMin.replace(/-/g, "/");
            minTime = Date.parse(minTime);
        var maxTime = main.timelineMax.replace(/-/g, "/");
            maxTime = Date.parse(maxTime);
        var timeRange = maxTime - minTime;
        var dotDistance = ((time - minTime) * 100) / timeRange;
        console.log('n: ' + n + '   timeRange: ' + timeRange + '    timelineMax: ' + main.timelineMax + '     timelineMin: ' + main.timelineMin)
        var singleDotDiv = '<div class="singleDot" style="left: ' + dotDistance + '%; "></div>';
        $('#dots').append(singleDotDiv);
    }

}

Dscourse.prototype.PostInSynthesis = function(postID) {
    /*
     *	Checks to see if this posts is in a synthesis so a notification can be drawn next to the post.
     */

    var main = this;
    var output = '';
    var count = 0;
    var j, k, i, o;
    for ( j = 0; j < main.data.posts.length; j++) {// Go through all the posts in this discussion
        k = main.data.posts[j];
        if (k.postContext) {// Check post context where synthesis information is
            var posts = k.postContext.split(",");
            // Split post content into array
            for ( i = 0; i < posts.length; i++) {// For each posts in the array
                o = posts[i];
                if (o == postID) {// check if this post is synthesis in the source post
                    output += '<span rel="tooltip" title="' + main.getName(k.postAuthorId, 'first') + '  made a connection to this post. Click to view." class="SynthesisComponent hide" synthesisSource="' + k.postID + '"><span class="typicn feed "></span></span>';
                    count++;
                }
            }
        }
    }
    if (count > 1) {// After collecting all the posts combine them into html output
        $(output).off('click');
        output = '<span class="synthesisWrap"> <b>' + count + '</b> Connections ' + output + '</span>';
    } else if (count == 1) {
        $(output).on('click', function(e) {
            var thisPost = $(this).attr('synthesissource');
            var postRef = '.synthesisPost[sPostID="' + thisPost + '"]';
            $('#dSidebar').scrollTo($(postRef), 400, {
                offset : -100
            });
            $(postRef).addClass('animated flash').css('background-color', 'rgba(255,255,176,1)').delay(5000).queue(function() {
                $(this).removeClass('highlight animated flash').css('background-color', 'whitesmoke');
                $(this).dequeue();
            });
            $('#dInfo').fadeOut();
            // hide #dInfo
            $('#dSynthesis').fadeIn();
            // show #synthesis
        });
    }
    return output;
}

Dscourse.prototype.ListSynthesisPosts = function(postList, sPostID, role) {// Populate unique participants.

    var main = this;

    if (!role) {
        role = 'add';
    }

    var i, o, j, k;
    var posts = postList.split(",");

    for ( i = 0; i < posts.length; i++) {
        o = posts[i];

        for ( j = 0; j < main.data.posts.length; j++) {
            k = main.data.posts[j];
            if (k.postID == o) {
                var postMessage = main.truncateText(k.postMessage, 100);
                if (role == 'add') {
                    $('.synthesisPost[sPostID="' + sPostID + '"]').append('<div sPostID="' + k.postID + '" class=" synthesisPosts hide"> <div class="synTop">' + main.getAuthorThumb(k.postAuthorId, 'tiny') + ' ' + main.getName(k.postAuthorId) + '</div><div class="synMessage">' + postMessage + ' </div><div>');
                } else if (role == 'edit') {
                    console.log('role is edit');
                    $('#synthesisPostWrapper').append('<div sPostID="' + k.postID + '" class=" synthesisPosts hide"> <div class="synTop">' + main.getAuthorThumb(k.postAuthorId, 'tiny') + ' ' + main.getName(k.postAuthorId) + '</div><div class="synMessage">' + postMessage + ' </div><button class="btn btn-mini removeSynthesisPost">Remove</button><div>');
                    $('#synthesisPostWrapper').children('div').show();
                }
            }
        }
    }
}

Dscourse.prototype.ResponseCounters = function(postId) {
    /*
     *	Generates the html printout about how many responses each post has.
     */
    var main = this;
    var comment = 0;
    var commentPeople = '';
    var agree = 0;
    var agreePeople = '';
    var disagree = 0;
    var disagreePeople = '';
    var clarify = 0;
    var clarifyPeople = '';
    var offTopic = 0;
    var offTopicPeople = '';
    var i, o, commentText, text;
    for ( i = 0; i < main.data.posts.length; i++) {
        o = main.data.posts[i];
        if (o.postFromId == postId) {

            var postAuthor = main.getName(o.postAuthorId);

            switch(o.postType)// Get what kind of post this is
            {
                case 'agree':
                    var d1 = agreePeople.indexOf(postAuthor);
                    // Do not add if author already exists
                    if (d1 == -1) {
                        if (agreePeople.length > 0) {
                            agreePeople += '<br />';
                        }
                        agreePeople += postAuthor;
                    }
                    agree++;
                    break;
                case 'disagree':
                    var d2 = disagreePeople.indexOf(postAuthor);
                    // Do not add if author already exists
                    if (d2 == -1) {
                        if (disagreePeople.length > 0) {
                            disagreePeople += '<br />';
                        }
                        disagreePeople += postAuthor;
                    }
                    disagree++;
                    break;
                case 'clarify':
                    var d3 = clarifyPeople.indexOf(postAuthor);
                    // Do not add if author already exists
                    if (d3 == -1) {
                        if (clarifyPeople.length > 0) {
                            clarifyPeople += '<br />';
                        }
                        clarifyPeople += postAuthor;
                    }
                    clarify++;
                    break;
                case 'offTopic':
                    var d4 = offTopicPeople.indexOf(postAuthor);
                    // Do not add if author already exists
                    if (d4 == -1) {
                        if (offTopicPeople.length > 0) {
                            offTopicPeople += '<br />';
                        }

                        offTopicPeople += postAuthor;
                    }
                    offTopic++;
                    break;
                default:
                    var d5 = commentPeople.indexOf(postAuthor);
                    // Do not add if author already exists
                    if (d5 == -1) {
                        if (commentPeople.length > 0) {
                            commentPeople += '<br />';
                        }
                        commentPeople += postAuthor;
                    }
                    comment++;
            }
        }
    }
    commentText = ' ', agreeText = ' ', disagreeText = ' ', clarifyText = ' ', offTopicText = ' ';
    if (comment > 0) {
        commentText = '<span href="#" rel="tooltip" class="postTypeWrap" typeID="comment" title="<b>Comments from: </b><br /> ' + commentPeople + '" > ' + comment + '  <span class="typicn message "></span></span>  ';
    }
    if (agree > 0) {
        agreeText = '<span href="#" rel="tooltip" class="postTypeWrap" typeID="agree" title="<b>People who agreed: </b><br /> ' + agreePeople + '"> ' + agree + '  <span class="typicn thumbsUp "></span> </span> ';
    }
    if (disagree > 0) {
        disagreeText = '<span href="#" rel="tooltip" class="postTypeWrap" typeID="disagree" title="<b>People who disagreed:</b><br /> ' + disagreePeople + '"> ' + disagree + '  <span class="typicn thumbsDown "></span></span> ';
    }
    if (clarify > 0) {
        clarifyText = '<span href="#" rel="tooltip" class="postTypeWrap" typeID="clarify" title="<b>People that asked to clarify:</b><br /> ' + clarifyPeople + '"> ' + clarify + '  <span class="typicn unknown "></span></span> ';
    }
    if (offTopic > 0) {
        offTopicText = '<span href="#" rel="tooltip" class="postTypeWrap" typeID="offTopic" title="<b>People that marked off topic: </b><br />' + offTopicPeople + '"> ' + offTopic + '  <span class="typicn forward "></span> </span>  ';
    }
    text = commentText + agreeText + disagreeText + clarifyText + offTopicText;
    return text;
}

Dscourse.prototype.GetSelectedText = function()// Select text
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

Dscourse.prototype.GetSelectedLocation = function(element)// Data about begin and end of selection
{
    var main = this;

    var start = 0, end = 0;
    var sel, range, priorRange;
    if ( typeof window.getSelection != "undefined") {
        range = window.getSelection().getRangeAt(0);
        priorRange = range.cloneRange();
        priorRange.selectNodeContents(element);
        priorRange.setEnd(range.startContainer, range.startOffset);
        start = priorRange.toString().length;
        end = start + range.toString().length;
    } else if ( typeof document.selection != "undefined" && ( sel = document.selection).type != "Control") {
        range = sel.createRange();
        priorRange = document.body.createTextRange();
        priorRange.moveToElementText(element);
        priorRange.setEndPoint("EndToStart", range);
        start = priorRange.text.length;
        end = start + range.text.length;
    }
    return {
        start : start,
        end : end
    };
}

Dscourse.prototype.listDiscussionCourses = function(dID) {
    /*
     *	Lists course names for the selected discussion
     */
    var main = this;
    var o, print = '';
    var comma;
    for (var i = 0; i < main.data.courses.length; i++) {
        o = main.data.courses[i];
        if(i == 0){comma = ''} else {comma = ', '}; 
        print += comma + ' <span courseid="' + o.courseID + '">' + o.courseName + '</span>';
    }
    return print;
}

Dscourse.prototype.HighlightRelevant = function(postID) {
    /*
     *  Highlights the relevant sections of host post when hovered over
     */
    var main = this;
    // First remove all highlights anywhere
    $('.postTextWrap').find('.highlight').removeClass('highlight');
    // Find all postTextWrap spans with class highlight and remove class highlight.

    // get selection of this post ID
    var i, o, thisSelection, j, m, highlight, newHighlight, n, selector;
    var f = main.data.posts.filter(function(a){
        return a.postID == postID;
    });
    if(f.length > 0){
        o = f[0];
        if (o.postSelection !== "") {// If there is selection do highlighting
                thisSelection = o.postSelection.split(",");
                var num1 = parseInt(thisSelection[0]);
                var num2 = parseInt(thisSelection[1]);
                // var num3 = num2-num1;   // delete if substring() works.
                // find the selection in reference post
                var ref = main.data.posts.filter(function(a){
                    return a.postID == o.postFromId;
                })[0];
                highlight = ref.postMessage.substring(num1, num2);
                newHighlight = '<span class="highlight">' + highlight + '</span>';
                n = ref.postMessage.substring(0, num1)+newHighlight+ref.postMessage.substring(num2);
                selector = 'div[level="' + o.postFromId + '"]';
                $(selector).children('.postTextWrap').children('.postMessageView').html(n);
            } else {
                // If there is no selection remove highlighting     -- Check This --TODO
            }
    }
}

Dscourse.prototype.DiscDateStatus = function(dID) {
    /*
     *	Checks the date to see if the discussion is active, individual participation or closed.
     */
    var main = this;
    var dStatus;
    // Get course dates:
    var o;
    o = main.data.discussion;
    if (o.dID === dID) {
        // Compare dates of the discussion to todays date.
        var beginDate = main.GetUniformDate(o.dStartDate);
        var openDate = main.GetUniformDate(o.dOpenDate);
        var endDate = main.GetUniformDate(o.dEndDate);
        var currentDate = new Date().getTime();
        // Compare dates of the discussion to todays date. But first convert mysql dates into js
        if (currentDate >= beginDate && currentDate <= endDate) {// IF today's date bigger than start date and smaller than end date?
            if (currentDate <= openDate) {// If today's date smaller than Open Date
                dStatus = 'student';
                // The status is open to individual contribution
            } else {
                dStatus = 'all';
                // The status is open to everyone
            }
        } else {
            dStatus = 'closed';
            // The status is closed.
        }
        return dStatus;
    }
}

Dscourse.prototype.UserCourseRole = function(dID, userID) {
    /*
     *	Gets the role of the user in the discussion on this course.
     */
    var main = this;
    var userRole = 'unrelated';

    var j, k;
    for ( j = 0; j < main.data.users.length; j++) {// Loop through courses
        k = main.data.users[j];
        if (k.UserID == userID) {
            userRole = k.userRole;
        }
    }
    return userRole;
}

Dscourse.prototype.getName = function(id, type) {
    /*
     *	Returns name of the user from ID
     */
    var main = this;
    if (type == 'first') {
        for (var n = 0; n < main.data.users.length; n++) {
            var userIDName = main.data.users[n].UserID;
            if (userIDName == id)
                return main.data.users[n].firstName;
        }
    } else if (type == 'last') {
        for (var n = 0; n < main.data.users.length; n++) {
            var userIDName = main.data.users[n].UserID;
            if (userIDName == id)
                return main.data.users[n].lastName;
        }
    } else {
        for (var n = 0; n < main.data.users.length; n++) {
            var userIDName = main.data.users[n].UserID;
            if (userIDName == id)
                return main.data.users[n].firstName + " " + main.data.users[n].lastName;
        }
    }
}

Dscourse.prototype.getAuthorThumb = function(id, size) {
    /*
     *	Returns thumbnail html of the user from ID
     */
    var main = this;
    var l = main.data.users.length;
    for (var n = 0; n < l; n++) {
        if (main.colors.length==0){
            var hues = main.scatter(0,360, main.data.users.length);
            for(var i=0;i<main.data.users.length;i++){
                var fade = 0.25+(Math.random()*0.75);
                var color = d3.hsl(hues[i],1,fade);
                var font = d3.hsl(180+hues[i],1, Math.abs(fade-1));
                main.colors.push({color:color,font:font});     
            }
        }
    }
    for (var n = 0; n < main.data.users.length; n++) {
        var userIDName = main.data.users[n].UserID;
        var color = main.colors[n].color;
        var font = main.colors[n].font;
        if (userIDName == id) {
        	var name = main.data.users[n].firstName + " " + main.data.users[n].lastName; 
            var initials = main.Initials(name); 
            if (size == 'small') {
                if(main.data.users[n].userPictureURL){
               		return "<img class='userThumbSmall' src='" + main.data.users[n].userPictureURL + "' />";	                
                } else {
	                return "<div class='userThumbSmall' style='color:"+font+";background:"+color+"'> "+initials+" </div>"; 
                }
            } else if (size == 'tiny') {
                if(main.data.users[n].userPictureURL){
	                return "<img class='userThumbTiny' src='" + main.data.users[n].userPictureURL + "' />";
	            } else {
		            return "<div class='userThumbTiny' style=' color:"+font+";background:"+color+"'> "+initials+" </div>"; 
	            }
            }
        }
    }
}

Dscourse.prototype.UniqueParticipants = function() {
    /*
     *  Returns html for unique participant buttons in the discussion Participant section.
     */
    var main = this;
    var btn = $('<button>').addClass('uList');
    $('body').append(btn);
    var width = btn.width()+4;
    btn.remove();
    
    var maxWidth =  $('#keywordSearchDiv').position().left - ($('#participantList').position().left+$('#participantList').children().eq(0).width())-50;
    var maxIcons = Math.floor(maxWidth/width)-1;
    
    $('.uList').remove();
    var i, o, name, thumb, output;
    for ( i = 0; i < main.uParticipant.length; i++) {
        o = main.uParticipant[i];
        name = main.getName(o);
        thumb = main.getAuthorThumb(o, 'small');
        output = '<button class="btn uList" rel="tooltip" active="false" title="' + name + '" authorID="' + o + '">' + thumb + ' </button>';
        if(i < maxIcons)
            $('#participantList').append(output);
        else if(i==maxIcons){
            $('#participantList').append($('<button class="btn uList" rel="tooltip" active="false" style="height:30px;"><span style="text-align:center">ALL</span></button>').on('click', function(){
                $('#participantListOverflow').toggle();
            }));
            $('#toolbox').append($('<div>',{
                id: 'participantListOverflow'
            }).hide());
            $('#participantListOverflow').append(output);
        }
        else{
            $('#participantListOverflow').append(output);        
        }
    }
    if($('#participantListOverflow').length>0)
        $('#participantListOverflow').css({
                    position: 'absolute',
                    left: $('#participantList').children().eq(1).offset().left+'px',
                    width: maxWidth-width +'px',
                    height: 'auto',
                    zIndex: 1000
         });
}

Dscourse.prototype.DiscResize = function() {
    /*
     * Resizes component widths and heights on the discussion page
     */
    var main = this;
    var h, wHeight, nbHeight, jHeight, cHeight, height;
    // Get total height of the window with the helper function
    //if lti use viewport instead
    if(lti){
        wWidth = window.innerWidtht;
        wHeight = window.innerHeight;
    }
    else{ 
        wHeight = $(window).height();
        wWidth = $(window).width();
    }
    // Get height of #navbar
    nbHeight = $('.navbar').height();
    // Get height of jumbutron
    jHeight = 0; //$('#discussionWrap > header').height();  
    // Get height of #controlsWrap
    cHeight = $('#controlsWrap').height();
    // resize #dRowMiddle accordingly.
    height = wHeight - (nbHeight + jHeight + cHeight + 74);
    height = height + 'px';
    mHeight = wHeight - (nbHeight + jHeight + cHeight + 74);
    mHeight = -mHeight;
    $('#dSidebar').css({
        'height' : height,
        'overflow-y' : 'scroll',
        'overflow-x' : 'hidden'
    });
    $('#vHeatmap').css({
        'height' : height,
        'overflow-y' : 'hidden',
        'overflow-x' : 'hidden'
    });
    $('#dMain').css({
        'height' : height,
        'overflow-y' : 'scroll',
        'overflow-x' : 'hidden'
    });
    $('#dRowMiddle').css({
        'margin-top' : 10
    });
    //jHeight+30});
    $('#lines').css({
        'height' : height,
        'margin-top' : mHeight + 'px'
    });
    $('#mediaBox').css({
        'height' : wHeight - 100 + 'px'
    });
    $('#node').css({
        'height' : wHeight - 200 + 'px'
    });
    $('#homeWrapper').css({
        'width' : wWidth - 600 + 'px'
    });

    //=== CORRECT Vertical Heatmap bars on resize  ===
    // Each existing heatmap point needs to be readjusted in terms of height.
    // View box calculations
    var boxHeight = $('#vHeatmap').height();
    // Get height of the heatmap object
    var totalHeight = $('#dMain')[0].scrollHeight;
    // Get height for the entire main section

    $('.vHeatmapPoint').each(function() {
        var postValue = $(this).attr('divPostID');
        // get the divpostid value of this div

        var thisOne = $(this);
        // redraw the entire thing.
        $('.threadText').each(function() {// Go through each post to see if postAuthorId in Divs is equal to the mapInfo
            var postAuthor = $(this).attr('postAuthorId');
            var postID = $(this).attr('level');
            if (postID == postValue) {
                var divPosition = $(this).position();
                // get the location of this div from the top
                //console.log(divPosition);
                var ribbonMargin = (divPosition.top) * boxHeight / totalHeight;
                // calculate a yellow ribbon top for the vertical heatmap
                ribbonMargin = ribbonMargin;
                // this correction is for better alignment of the lines with the scroll box.

                // There is an error when the #dMain layer is scrolled the position value is relative so we have minus figures.

                $(thisOne).css('margin-top', ribbonMargin);
            }
        });
    });
    // ==  end correct vertical heatmap
    $('#displayFrame').css({
        'height' : wHeight - 100 + 'px'
    });
    $('#displayIframe').css({
        'height' : wHeight - 150 + 'px'
    });
    //Fixing the width of the threadtext
    $('.threadText').each(function() {
        var parentwidth = $(this).parent().width();
        var parentheight = $(this).children('.postTextWrap').height();
        var thiswidth = parentwidth - 42;
        $(this).css({
            'width' : thiswidth + 'px',
            'padding-left' : '40px'
        });
        $(this).children('.postTypeView').css('width', '20px');
        $(this).children('.sayBut2').css({
            'width' : '30px',
            'margin-left' : '0px',
            'height' : parentheight + 10 + 'px'
        });
        $(this).children('.responseWrap').css('width', '40px');
        $(this).children('.postTextWrap').css('width', thiswidth - 110 + 'px');
    });
    // main.AddLog('discussion',discID,'DiscResize',0,'Height: ' + wHeight + ' Width: ' +wWidth); This is not a good idea! It will use too much processing power
    main.UniqueParticipants();
}

Dscourse.prototype.ClearVerticalHeatmap = function() {
    /*
    * Clear heatmap for reuse
    */
    // Check to see how clearing will function, this is probably the place for it.
    $('#vHeatmap').html('');
    $('#vHeatmap').append('<div id="scrollBox"> </div>');
    // Add scrolling tool

}

Dscourse.prototype.VerticalHeatmap = function(mapType, mapInfo) {
    /*
     * Draw the components of the heatmap
     */

    var main = this;
    main.activeFilter = (typeof mapType !="undefined")?mapType: main.activeFilter;
    // View box calculations
    var boxHeight = $('#vHeatmap').height();
    // Get height of the heatmap object
    var visibleHeight = $('#dMain').height();
    // Get height of visible part of the main section
    var totalHeight = $('#dMain')[0].scrollHeight;
    // Get height for the entire main section

    // Size the box
    var scrollBoxHeight = visibleHeight * boxHeight / totalHeight;
    $('#scrollBox').css('height', scrollBoxHeight - 7);
    // That gives the right relative size to the box

    // Scroll box to visible area
    var mainScrollPosition = $('#dMain').scrollTop();
    var boxScrollPosition = mainScrollPosition * boxHeight / totalHeight;
    $('#scrollBox').css('margin-top', boxScrollPosition);
    // Gives the correct scrolling location to the box

    if (mapType == 'user') {// if mapType is -user- mapInfo is the user ID
        $('.threadText').filter(':visible').each(function() {// Go through each post to see if postAuthorId in Divs is equal to the mapInfo
            var postAuthor = $(this).attr('postAuthorId');
            var postID = $(this).attr('level');
            if (postAuthor == mapInfo) {
                var divPosition = $(this).position();
                // get the location of this div from the top

                // dynamically find.
                var mainDivTop = $('#dMain').scrollTop();
                //console.log('main div scroll: ' + mainDivTop);
                //console.log(divPosition);
                var ribbonMargin = (divPosition.top + mainDivTop) * boxHeight / totalHeight;
                // calculate a yellow ribbon top for the vertical heatmap
                //ribbonMargin = ribbonMargin; // this correction is for better alignment of the lines with the scroll box.

                // There is an error when the #dMain layer is scrolled the position value is relative so we have minus figures.

                $('#vHeatmap').append('<div class="vHeatmapPoint" style="margin-top:' + ribbonMargin + 'px" divPostID="' + postID + '" ></div>');
                // append the vertical heatmap with post id and author id information (don't forgetto create an onclick for this later on)
            }
        });
    }

    if (mapType == 'keyword') {// if mapType is -keyword- mapInfo is the text searched
        main.ClearKeywordSearch('#dMain');
        //console.log(mapInfo); // Works
        $('.threadText').each(function() {// go through each post to see if the text contains the mapInfo text
            var postID = $(this).attr('level');
            var postContent = $(this).children('.postTextWrap').children('.postMessageView').text();
            //search for keyword
            var a = postContent.search(RegExp(mapInfo, 'gi'));
            // search for post text with the keyword text if there is a match get location information
            if (a != -1) {
                var divPosition = $(this).position();
                // get the location of this div from the top
                //console.log(divPosition);
                var mainDivTop = $('#dMain').scrollTop();
                var ribbonMargin = (divPosition.top + mainDivTop) * boxHeight / totalHeight;
                // calculate a yellow ribbon top for the vertical heatmap
                $('#vHeatmap').append('<div class="vHeatmapPoint" style="margin-top:' + ribbonMargin + 'px" divPostID="' + postID + '" ></div>');
                // append the vertical heatmap with post id and author id information (don't forgetto create an onclick for this later on)
               }
                var replaceText = $(this).children('.postTextWrap').children('.postMessageView').html();
                if(!!replaceText){
                 // Find out if there is alreadt a span for highlighting here
                replaceText = replaceText.replace(/(?:<span class=\"highlightblue\">|<\/span>)/gi,"");
                replaceText = replaceText.replace(RegExp(mapInfo, 'gi'), function(capture){
                   return "<span class=\"highlightblue\">"+capture+"</span>"; 
                });
               // var newSelected = '<search class="highlightblue">' + mapInfo + '</search>';
                //var n = replaceText.replace(RegExp(mapInfo.replace(/[#-}]/gi, '\\$&'), 'g'), newSelected);
                $(this).children('.postTextWrap').children('.postMessageView').html(replaceText);
            }
        });

    }

    main.DrawShape();
    if(!mapInfo){mapInfo = 'null'}; if(!mapType){mapType = 'null'}; 
    //main.AddLog('discussion',discID,'verticalHeatmap',mapInfo,mapType); This is not a good idea! It will use too much processing power
}


 Dscourse.prototype.CheckNewPosts = function(discID, userRole, dStatus)					// Highlights the relevant sections of host post when hovered over
 {
 var main = this;
 
 var j;
 var posts = new Array(); 
  
	if(main.data.posts){ 
	
    for ( j = 0; j < main.data.posts.length; j++) {// Go through all the posts
        d = main.data.posts[j];
        	posts.push(d.postID); 
        }
         			 	
		$.ajax({																							
			type: "POST",
			url: "php/data.php",
			data: {
				currentDiscussion: discID,
				currentPosts: 	posts,						
				action: 'checkNewPosts'							
			},
			  success: function(data) {						// If connection is successful . 
					if(data.result > 0){
						$('#checkNewPost').addClass('animated flipInY');
						$('#checkNewPost').html('<div class="alert alert-success refreshBox" discID="' + discID + '">    <button id="hideRefreshMsg" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><span class="typicn refresh refreshIcon"></span> There are <b>' + data.result + '</b> new posts. Click to refresh!</span>'); 
					// Reload needs to happen when a button is clicked in the page. 
					} else {
						console.log('No new posts...');
					}
			    }, 
			  error: function(data) {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to data.php failed for Checking new posts."); 
					console.log(data); 
			  }
		});	
	    for ( j = 0; j < main.data.posts.length; j++) {// Go through all the posts
	        d = main.data.posts[j];
	        	posts.push(d.postID); 
	        }
	    }     			 	
			$.ajax({																							
				type: "POST",
				url: "php/data.php",
				data: {
					currentDiscussion: discID,
					currentPosts: 	posts,						
					action: 'checkNewPosts'							
				},
				  success: function(data) {						// If connection is successful . 
						if(data.result > 0){
							$('#checkNewPost').addClass('animated flipInY');
							$('#checkNewPost').html('<div class="alert alert-success refreshBox" discID="' + discID + '">    <button id="hideRefreshMsg"type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><span class="typicn refresh refreshIcon"></span> There are <b>' + data.result + '</b> new posts. Click to refresh!</span>'); 
						// Reload needs to happen when a button is clicked in the page. 
						} else {
							console.log('No new posts...');
						}
				    }, 
				  error: function(data) {					// If connection is not successful.  
						console.log("Dscourse Log: the connection to data.php failed for Checking new posts."); 
						console.log(data); 
				  }
			});	
 }
 

 Dscourse.prototype.AddLog=function(logPageType,logPageID,logAction,logActionID,logMessage)	 			   
 {
	 var main = this;
	 
	 var log = new Object();
	 
	 // Create object
	 log = {
				'logSessionID' : currentSession,
				'logUserID' : currentUserID,
				'logPageType': logPageType,
				'logPageID': logPageID,
				'logAction': logAction,
				'logActionID': logActionID,
				'logMessage': logMessage,
				'logUserAgent': dUserAgent  
			};
	console.log(log);
	// Write to database 		
	$.ajax({																						
			type: "POST",
			url: "php/data.php",
			data: {
				log: log,							
				action: 'addLog'
			},
			  success: function(data) {						// If connection is successful . 
			    	console.log("Dscourse Log: " + logPageType + ' ' + logAction + " event logged.");
			    	console.log(data); 
			    }, 
			  error: function(data) {					// If connection is not successful.  
					console.log("Dscourse Log: the connection to data.php failed for Add Log event: " + logPageType + ' ' + logAction + ". "); 
					console.log(data); 
			  }
		});	
	

}



Dscourse.prototype.ClearKeywordSearch = function(selector) {
    /*
     * Clear keyword search properly
     */
    var main = this;
    // remove search highlights
    /*$(selector).find("span").each(function(index) {// find search tag elements. For each
        var text = $(this).html();
        // get the inner html
        $(this).replaceWith(text);
        // replace it with the inner content.
    });*/
   $(selector).find('span:not(:has(*))').filter('.highlightblue').contents().unwrap();
}

Dscourse.prototype.DrawShape = function() {
    /*
     * Draws the lines that connect scrollbox and the discussion window
     */
    var main = this;
    // get the canvas element using the DOM
    var canvas = document.getElementById('cLines');
    var scrollBoxTop = $('#scrollBox').css('margin-top');
    scrollBoxTop = scrollBoxTop.replace('px', '');
    scrollBoxTop = Math.floor(scrollBoxTop);
    var scrollBoxHeight = $('#scrollBox').css('height');
    // find the height of scrollbox
    scrollBoxHeight = scrollBoxHeight.replace('px', '');
    scrollBoxHeight = Math.floor(scrollBoxHeight);
    var linesHeight = $('#lines').height();
    canvas.height = linesHeight;
    var scrollWidth = $('#vHeatmap').width();
    var correction = 27 - scrollWidth;
    var scrollBoxBottom = scrollBoxHeight + scrollBoxTop;
    // add the height to the top position to find the bottom.
    // use getContext to use the canvas for rawing
    var ctx = canvas.getContext('2d');
    // Clear the drawing
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    // Options
    ctx.lineCap = 'round';
    ctx.lineWidth = 2;
    ctx.strokeStyle = 'rgb(179, 96, 64)';
    // Top line
    ctx.beginPath();
    ctx.moveTo(scrollWidth + correction, scrollBoxTop + 1);
    ctx.lineTo(scrollWidth + 26, 1);
    ctx.stroke();
    ctx.closePath();
    // Bottom line
    ctx.beginPath();
    ctx.moveTo(scrollWidth + correction, scrollBoxBottom + 2);
    ctx.lineTo(scrollWidth + 26, linesHeight - 1);
    ctx.stroke();
    ctx.closePath();
}

Dscourse.prototype.truncateText = function(text, length) {

    if (text.length < length) {
        return text;
    } else {

        var myString = text;
        var myTruncatedString = myString.substring(0, length) + '... ';
        return myTruncatedString;

    }

}

Dscourse.prototype.FormattedDate = function(date) {
    
    date = date.replace(/\//g,'-');
    // Split timestamp into [ Y, M, D, h, m, s ]
	var b = date.split(/[- :]/);
	var date = new Date(b[0], b[1]-1, b[2], b[3], b[4], b[5]);

    var d, m, curr_hour, dateString;
    d = new Date(0);
    var sec = this.GetUniformDate(date);
    d.setUTCMilliseconds(sec);
    // Write out the date in readable form.
    console.log(date);
    m = d.toDateString();
    curr_hour = d.getHours();
    dateString = m + '  ' + curr_hour + ':00';
    //console.log(dateString);
    return dateString;
}

Dscourse.prototype.FunctionTemplate = function() {
    var main = this;
}

// SOMETHINGS BORROWED
Dscourse.prototype.GetCurrentDate = function() {
    var x = new Date();
    var monthReplace = (x.getUTCMonth() < 10) ? '0' + (x.getUTCMonth() + 1) : x.getUTCMonth();
    var dayReplace = (x.getUTCDate() < 10) ? '0' + x.getUTCDate() : x.getUTCDate();
    var dateNow = x.getUTCFullYear() + '-' + monthReplace + '-' + dayReplace + ' ' + x.getUTCHours() + ':' + x.getUTCMinutes() + ':' + x.getUTCSeconds();
    return dateNow;
}

Dscourse.prototype.ParseDate = function(input, format) {

    format = format || 'yyyy-mm-dd';
    // default format
    var parts = input.match(/(\d+)/g), i = 0, fmt = {};
    // extract date-part indexes from the format
    format.replace(/(yyyy|dd|mm)/g, function(part) {
        fmt[part] = i++;
    });

    return new Date(parts[fmt['yyyy']], parts[fmt['mm']] - 1, parts[fmt['dd']]);
}

Dscourse.prototype.GetUniformDate = function(date, off){
    if(typeof off == "undefined")
        off = true;
    var d = false;
    if(typeof date == "object"){
        d = date.getTime();
    }
    else if(typeof date == "number"){
        d = new Date(date).getTime();
    }
    else if(typeof date == "string"){
        date = date.replace(/-/g, '/');
        var chrome = /chrome/.test(navigator.userAgent.toLowerCase());
        if(($.browser.safari || $.browser.msie) && !chrome)
           d = new Date(date.split('-').join('/')).getTime();
        else if($.browser.webkit || $.browser.mozilla)
            d = new Date(date).getTime();
        else
            d = new Date(date.split(' ').join('T')).getTime();   
    }
    if(off){
        //convert to user's timezone
        var diff = new Date().getTimezoneOffset();
        d-=diff*60000;
    }
    return d;
}

Dscourse.prototype.ToTimestamp = function(epoch){
    var d = new Date(epoch);
    var y = d.getFullYear();
    var m = ("00"+(d.getMonth()+1).toString()).slice(-2);
    var da = ("00"+d.getDate().toString()).slice(-2);
    var h = ("00"+d.getHours().toString()).slice(-2);
    var mi = ("00"+d.getMinutes().toString()).slice(-2);
    var s = ("00"+d.getSeconds().toString()).slice(-2);
    
    return y+"-"+m+"-"+da+" "+h+":"+mi+":"+s;
}

Dscourse.prototype.Initials = function (fullname) {
	var matches = fullname.match(/\b(\w)/g);              
	var initials = matches.join('');                  
	return initials; 
}
Dscourse.prototype.scatter = function (start, stop, qty){
            //cover base case
            var res = [stop/2];
            var n = 2;
            while(res.length<qty){
                var step = stop/(Math.pow(2,n));
                var back = n-1;
                var uni = [];
                for(var i=0; i<back; i++){
                    var pos = res[(res.length-1)-i];
                    uni.push(pos-step);
                    uni.push(pos+step);  
                    if(res.length == qty)
                        break;   
                }
                res= res.concat(uni);       
                n++;
            }   
            return res;
}

Dscourse.prototype.EditPost= function(post, cDisc){
    var main = this;
//     $.post('php/data.php',{action:'editPost',post:post}, function(pID){
// console.log('hi---'); 
//         var oldPost = main.data.posts.filter(function(a){return a.postID == pID})[0];
//         var keys = Object.keys(post);
//         for(var i=0; i<keys.length; i++){
//             oldPost[keys[i]] = post[keys[i]];
//         }
//         main.SingleDiscussion(cDisc);
//         main.DiscResize();
//         main.VerticalHeatmap();
//     });
 
     $.ajax({// Add user to the database with php.
        type : "POST",
        url : "php/data.php",
        data : {
            action : 'editPost',
            post : post
        },
        success : function(pID) { 
	        for (var j = 0; j < main.data.posts.length; j++) {  // Go through all the posts
        			var o = main.data.posts[j]; 
        			if(o.postID == post.postID){
        				        console.log('========================================');
        				        console.log(o);
        				        console.log(post);
        				        o.postFromId		= post.postFromId,
								o.postAuthorId		= post.postAuthorId,
								o.postMessage		= post.postMessage,
								o.postType			= post.postType,
								o.postSelection		= post.postSelection,
								o.postMedia			= post.postMedia,
								o.postMediaType		= post.postMediaType,
								o.postContext		= post.postContext
        			}
        	}
        
        
// 			var oldPost = main.data.posts.filter(function(a){return a.postID == pID})[0];
// 			var keys = Object.keys(post);
// 						console.log(oldPost); 
// 
// 			for(var i=0; i<keys.length; i++){
// 				oldPost[keys[i]] = post[keys[i]];
// 			}
 
            $('.levelWrapper[level="0"]').html('');
		
			main.SingleDiscussion(cDisc);
			main.DiscResize();
			main.VerticalHeatmap();
            main.AddLog('discussion',cDisc,'editPost',pID,' ') // Add Log
        },
        error : function(xhr, status) {// If there was an error
            console.log('There was an error talking to data.php');
            console.log(xhr);
            console.log(status);
        }
    });   
    
}