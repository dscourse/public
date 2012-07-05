<?php 
	define('MyConst', TRUE);								// Avoids direct access to config.php
	include "scripts/php/config.php"; 

	if(empty($_SESSION['Username']))  						// Checks to see if user is logged in, if not sends the user to login.php
	{  
	    header('Location: login.php');
	    
	}  else {												// User is logged in, show page. 

	?>
	
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Discussion Overview</title>
	
	<script type="text/javascript" src="assets/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="assets/js/jquery-ui-1.8.21.custom.min.js"></script>
	
	<link href="assets/css/jquery-ui-1.8.16.custom.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="scripts/js/helpers.js"></script>
	<script type="text/javascript" src="scripts/js/validation.js"></script>
	<script type="text/javascript" src="scripts/js/discussions.js"></script>
	<script type="text/javascript" src="scripts/js/courses.js"></script>
	<script type="text/javascript" src="scripts/js/users.js"></script>

	<style type="text/css">
		
		.rounded-corners { -moz-border-radius:8px;-webkit-border-radius:8px;-khtml-border-radius:8px;border-radius:8px;border:1px solid;padding:8px; overflow: hidden;}
		#dragImage {
			background:#ccc url(assets/img/dots.png) no-repeat top left;
			width: 7px;
			height: 14px; 
			margin-left: 3px;
		}
		#sizeBut {cursor:move; margin-top:-10px;}
		.feedDiv {padding: 0 5px; margin-top: -5px;}
		.allPanes {font-size: 12px;}
		.threads {padding:10px 25px 0 25px !important;}
		.thread2{margin-left: 25px;}
		.thread3{margin-left: 50px;}
		#timePane {
				background: -webkit-linear-gradient(top, rgba(126,195,238,0.34) 0%, rgba(126,161,222,0.75) 100%);
				border: 1px solid #ccc;
				font-size: 12px;
		}
		.sayBut {
			border-radius: 10px;
			width: 20px;
			height: 20px;
			padding: 0 2px 0 1px; 
			font-size: 12px;
		}
		.boxHeaders {
			font-weight: bold;
			font-size: 16px;
			margin: 10px 10px 2px 30px;
		}
		.boxHeaders small {
			font-weight: normal;
			font-style: italic;
			font-size: 15px;
			margin-left: 15px;
		}
		.feedDiv{
			font-size: 12px;
		}
	</style>

	
</head>
<body>
<div id="overlay"></div>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="home.php">
		  dscourse
		</a>
		<ul class="nav">

		  <?php if ($_SESSION['status'] == "Administrator"){	?>											
			<li>
			<a href="users.php">Users</a>  
		  </li>
		  <?php 	} ?>

		  <li>	
			<a href="courses.php">Courses</a>  
		  </li> 
		  <li>	
			<a href="discussions.php">Discussions</a>  
		  </li>
		  <li class="active" >	
			<a href="#">Current Discussion</a>  
		  </li>
		  
		</ul>
		
		<ul class="nav pull-right">
		 <li><a href="profile.php?id=<?php echo $_SESSION['UserID']; ?>"><?php echo $_SESSION['firstName'] . " " .$_SESSION['lastName']; ?>  </a></li>
		  <li><a href="scripts/php/logout.php">Logout</a></li>
		</ul>
				
    </div>
  </div>
</div>


<div id="discussionWrap" >



<div class="rounded-corners allPanes" id="newsPane" style="position: absolute; display: block; left: 8px; top: 72px; width: 339px; height: 277.7062937062937px; border: 1px solid rgb(153, 153, 153); background-image: -webkit-linear-gradient(top, rgb(255, 255, 255) 0%, rgb(237, 237, 237) 100%); background-position: initial initial; background-repeat: initial initial; ">
		<span class="boxHeaders">News Feed</span>
		<div class="feedDiv"> 	
			<hr class="soften">
			<p>
				</p><ul class="unstyled discussionFeed">
				<li> Mable Kinzie commented on <a href="#">"Stakeholder perspective of James Monroe" </a>  <em class="timeLog">3 hours ago.</em> </li>
				<li> Bill Ferster added a new link to discussion on <a href="#">Benefits of free software </a>  <em class="timeLog">18 hours ago.</em></li>
				<li> Glen Bull annotated your comment at <a href="#">"Where is 3D printing going?"</a> <em class="timeLog">2 days ago.</em></li>
				</ul>
			<p></p>
		<p class="pull-right"><a href="#"><em>See more </em></a></p>
	</div><div style="position:absolute;left:6px;top:6px;width:20px;height:20px;padding:0px;border:2px solid #1e5799;border-radius:20px;moz-border-radius;20px;color:#1e5799;text-align:center;line-height:20px;font-size:8px"><b>see</b><div></div></div></div>
	

	<div class="rounded-corners" id="discPane" style="position: absolute; top: 72px; left: 374px; width: 744px; height: 403px; border: 1px solid rgb(153, 153, 153); background-image: -webkit-linear-gradient(top, rgb(255, 255, 255) 0%, rgb(237, 237, 237) 100%); background-position: initial initial; background-repeat: initial initial; ">
		<span class="boxHeaders">Discussion<small>What are the stakeholder perspectives in the Burrough County case? </small></span> 
			
			<hr class="soften">
	<div id="discFeedDiv"></div><div style="position:absolute;left:6px;top:6px;width:20px;height:20px;padding:0px;border:2px solid #1e5799;border-radius:20px;moz-border-radius;20px;color:#1e5799;text-align:center;line-height:20px;font-size:8px"><b>see</b><div></div></div>
	
	<div id="discussionDivs">
						</p><ul class="unstyled discussionFeed">
				<li class="thread1 threads"> <b>Mable</b> said  I think the point Jake makes <a href="#"> here </a>truly captures the essence of what Mr. Stake's perception is on the topic. What are some of the concerns he is bringing up in the discussion?  <em class="timeLog">2 hours ago.</em><div class="btn btn-info sayBut">say</div> </li>
				<li class="thread2 threads"> <b>Bill </b> said: this is a very interesting point, but it's not exactly what I had in mind. What Ms. Jenkins adds to the table is her years of experience in the district... <div class="btn btn-info sayBut">say</div></li>
				<li class="thread1 threads"> <b>Glen </b> annotated <a href="#">your comment</a> at <a href="#">Possible issues regarding...</a>. Okay this sounds good but what about the growing problem of underfunding in schools. I think Jake had a resource on this... <em class="timeLog">2 days ago.</em><div class="btn btn-info sayBut">say</div></li>
				
				<li class="thread2 threads"> <b>Jake</b> replied to <a href="#">Glen</a>: There is a graph on the New York Times  <a href="#">website</a> that can give a better representation of the trend I was talking about. This is an important topic when we consider... <em class="timeLog">2 days ago.</em><div class="btn btn-info sayBut">say</div></li>
				
				<li class="thread3 threads"> <b> Sara</b> said: this graph is very helpful but we need to come back to the question of whether such large scale statistics are important in the case of the Burrough County. My take on this is that...  <em class="timeLog">2 days ago.</em><div class="btn btn-info sayBut">say</div></li>
				</ul>
			<p></p>
		
	</div>
	
	</div>

	<div class="rounded-corners" id="todoPane" style="position: absolute; display: block; width: 340px; top: 375.7062937062937px; left: 8px; height: 99.2937062937063px; border: 1px solid rgb(153, 153, 153); background-image: -webkit-linear-gradient(top, rgb(255, 255, 255) 0%, rgb(237, 237, 237) 100%); background-position: initial initial; background-repeat: initial initial; ">
			<span class="boxHeaders">To-Dos</span>
			
			<hr class="soften">
			<div class="feedDiv"><p>
				</p><ul class="unstyled todoFeed">
				<li> Provide initial response to document <a href="#">"Case overview" </a> by <em>August, 25.</em> </li>
				<li> <a href="#">Initiate </a> a class discussion with relevant topic. </li>
				<li> Bill Ferster asks you to clarify your comment starting with <em><a href="#">"That sounds wrong, ...."</a> </em></li>
				</ul>
			<p></p>
		<p class="pull-right"><a href="#"><em>See more </em></a></p>
	</div><div style="position:absolute;left:6px;top:6px;width:20px;height:20px;padding:0px;border:2px solid #1e5799;border-radius:20px;moz-border-radius;20px;color:#1e5799;text-align:center;line-height:20px;font-size:8px"><b>see</b><div></div></div></div>

	<div class="rounded-corners" id="timePane" style="position: absolute; height: 32px; top: 501px; left: 8px; width: 1110px; background-position: initial initial; background-repeat: initial initial; ">
	<div id="TimeSliderDiv">	</div>
	<div id="nowBar" style="position: absolute; height: 4px; top: 16px; width: 2px; background-color: #666; left: 315px; "></div><span id="nowText" style="position: absolute; top: 4px;  left: 306px; margin-top: -5px;">now<span></span></span><span id="beginText" style="position:absolute;left:48px;top:18px;"><b>6/17 12pm</b></span><span id="endText" style="position:absolute;left:545px;top:18px;"><b>6/25 12pm</b></span><span id="sliderText1" style="position: absolute; top: 33px; left: 196px; ">6/19 12pm</span><span id="sliderText2" style="position: absolute; top: 33px; left: 396px; ">6/23 12pm</span><div style="position:absolute;left:8px;top:12px;width:20px;height:20px;padding:0px;border:2px solid #fff;border-radius:20px;moz-border-radius;20px;text-align:center;line-height:20px;font-size:8px"><b>set</b><div></div></div></div>
	<div id="sizeBut" style="position: absolute; color: rgb(153, 153, 153); font-size: 16px; text-align: center; width: 12px; height: 12px; border-top-left-radius: 6px; border-top-right-radius: 6px; border-bottom-right-radius: 6px; border-bottom-left-radius: 6px; left: 362px; top: 366.7062937062937px; " class="ui-draggable"><div id="dragImage"></div></div>

</div>		
<script>
	
	var userData=new Object;												// Holds user savable data
	userData.partitionX=.30;												// Default h partition
	userData.partitionY=.50;												// Default V partition
	userData.timeSlider1=.25;												// Time slider 1 value
	userData.timeSlider2=.75;												// Time slider 2 value
	var panelControl=null;													// Holds
	

	
	
	$(document).ready(function() {											// When doc loaded
		panelController=new PaneControl(56,userData);						// Init PaneControl							
		});		
		
	function PaneControl(top, userData)									// CONSTRUCTOR
	{		
		this.gutter=8;														// Gutter between panes
		top+=this.gutter;													// Shift top for gutter
		this.top=top;														// Set top
		var _this=this;														// Set this
		
		$("#sizeBut").draggable({ drag: function(event, ui) {				// Drag
				var bot=$("#discPane").outerHeight();						// Get bot
				var x=ui.position.left+3;									// X pos
				x=x*100/_this.wid;											// 0-100%
				var y=ui.position.top-top;									// Y pos
				y=y*100/(bot-top+(_this.top+_this.gutter));					// 0-100%
				_this.PositionFrames(x/100,y/100);							// Redraw			
				},  
			stop: function(event, ui) {										// Stop	
				_this.PositionFrames(userData.partitionX,userData.partitionY);	// Redraw			
				_this.Draw();												// Draw panes
		 		}
		 	});			
		$("#sizeBut").css({ width:'12px',height:'12px'});					// Set size
		$("#sizeBut").css({"border-radius":"6px","moz-border-radius":"6px"});// Set corners to make circle
		this.Draw();														// Draw panes
		this.PositionFrames(userData.partitionX,userData.partitionY);		// Set initial pane positioning
		}
	
	PaneControl.prototype.PositionFrames=function(cx, cy)				// POSITION THE PANES
	{
		var g=this.gutter;													// Current gutter
		this.wid=$(document).outerWidth();									// Browser with
		$("#newsPane").show();												// Make sure news feed is visible
		$("#todoPane").show();												// Make sure todo is visible
		$("#discPane").show();												// Make sure discussion is visible
		if (cy <= 0)														// Past top
			$("#newsPane").hide();											// Hide news feed
		if (cy >= 1)														// Past bottom
			$("#todoPane").hide();											// Hide todo 
		if (cx <= 0) {														// Past left
			$("#newsPane").hide();											// Hide news feed
			$("#todoPane").hide();											// Hide todo 
			}
		if (cx >= 1)														// Past bottom
			$("#discPane").hide();											// Hide discussion 
			
		cy=Math.min(Math.max(cy,0),1);										// Cap cy 0-1
		cx=Math.min(Math.max(cx,0),1);										// Cap cx 0-1
		this.SetUserData("partitionX",cx);	this.SetUserData("partitionY",cy);	// Save partition info
		var timeHgt=$("#timePane").outerHeight();							// Height of time pane
		var frameHgt=$(document).outerHeight()-g;							// Browser height
		var discHgt=Math.floor(frameHgt-this.top-timeHgt);					// Disussion area height
		var x=cx*this.wid;													// Center x point in pixels
		var y=this.top+(cy*discHgt);										// Center y point in pixels
		
		$("#sizeBut").css("left",x-4+"px");									// Set left
		$("#sizeBut").css("top",Math.min(Math.max(y-1,this.top+g-12),discHgt+this.top-g-3)+"px"); // Set top
		
		$("#newsPane").css("left",g+"px");									// Set NEWS left
		$("#newsPane").css("top",this.top+g+"px");							// Set top
		$("#newsPane").outerWidth(x-g-1);									// Set width
		$("#newsPane").outerHeight(y-this.top-(g*2));						// Set height

		$("#todopane").css("left",g+"px");									// Set TO DO left
		$("#todoPane").outerWidth(x-g);										// Set width
		$("#todoPane").css("top",y+"px");									// Set top
		$("#todoPane").outerHeight(discHgt-y+(this.top-0)-g); 				// Set height
		if (cy <= 0) {														// Past top
			$("#todoPane").css("top",y+g+"px");								// Set top
			$("#todoPane").outerHeight(discHgt-y+(this.top-0)-(g*2)); 		// Set height
			}

		$("#discPane").css("top",this.top+g+"px");							// Set DISC top
		$("#discPane").css("left",x+g+"px");								// Set left
		$("#discPane").outerWidth(this.wid-x-(g*2));						// Set width
		$("#discPane").outerHeight(discHgt-(g*2));							// Set height
			
		$("#timePane").css("top",(discHgt+this.top)+"px");					// Set TIME top
		$("#timePane").css("left",g+"px");									// Set left
		$("#timePane").outerWidth(this.wid-(g*2));							// Set width
	}
	
	
	PaneControl.prototype.Draw=function()								// DRAW ELEMENTS
	{
		this.DrawNews();													// Draw news feed pane
		this.DrawDisc();													// Draw discussion pane
		this.DrawToDo();													// Draw todo pane
		this.DrawTime();													// Draw time pane
	}		
	
	PaneControl.prototype.DrawNews=function()							// DRAW NEWS FEED PANE
	{
		var dd="#newsFeedDiv";												// Name of div
		if (!$(dd).length) {												// If div doesn't exist
			$("#newsPane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#newsPane").append(this.DrawSeeDot("see",20,6,6,"#1e5799"));	// Draw ses logo
			}
		$("#newsPane").css({border:"solid 1px #ccc",background:"#f9f9f9"});		
		}		

	PaneControl.prototype.DrawToDo=function()							// DRAW TO TO PANE
	{
		var dd="#todoFeedDiv";												// Name of div
		if (!$(dd).length) {												// If div doesn't exist
			$("#todoPane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#todoPane").append(this.DrawSeeDot("see",20,6,6,"#1e5799"));	// Draw ses logo
			}
		$("#todoPane").css({border:"solid 1px #ccc",background:"#f9f9f9"});		
	}		

	PaneControl.prototype.DrawDisc=function()							// DRAW DISC PANE
	{
		var dd="#discFeedDiv";												// Name of div
		if (!$(dd).length) {												// If div doesn't exist
			$("#discPane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#discPane").append(this.DrawSeeDot("see",20,6,6,"#1e5799"));	// Draw see logo
			}
		$("#discPane").css({border:"solid 1px #ccc",background:"#ededed"});		
		if ($.browser.mozilla)	
			$("#discPane").css("background","-moz-linear-gradient(top,#ffffff,#ededed)");
		else 
			$("#discPane").css("background","-webkit-linear-gradient(top, #ffffff 0%, #ededed 100%)")
	}		
	
	PaneControl.prototype.DrawTime=function()							// DRAW TIME PANE
	{
		var i,p,x,str;
		var now=new Date().getTime();										// Get today
		var startTime=new Date(now-4*24*60*60*1000);
		var endTime=new Date(now+4*24*60*60*1000);
		var _this=this;														// Set this
				

		var dd="#TimeSliderDiv";											// Name of slider
		if (!$(dd).length) {												// If slider doesn't exist
			$("#timePane").append("<div id='"+dd.substr(1)+"'/>");			// Add to pane
			$("#timePane").append("<div id='nowBar'       style='position:absolute;height:4px;top:16px;width:2px;background-color:#999'/>");
			$("#timePane").append("<span id='nowText'     style='position:absolute;top:4px; margin-top: -5px;'>now<span>");
			$("#timePane").append("<span id='beginText'   style='position:absolute;left:48px;top:18px;'><span>");
			$("#timePane").append("<span id='endText'     style='position:absolute;left:525px;top:18px;'><span>");
			$("#timePane").append("<span id='sliderText1' style='position:absolute;top:33px;'><span>");
			$("#timePane").append("<span id='sliderText2' style='position:absolute;top:33px;'><span>");
			$("#timePane").append(this.DrawSeeDot("set",20,8,12,"#fff"));	// Draw set logo
				}
		$(dd).css({ position:"absolute",width:"400px",left:"115px",top:"20px" });
		$("#beginText").html("<b>"+this.DateTimeString(startTime)+"</b>");	// Course start
		$("#endText").html("<b>"+this.DateTimeString(endTime)+"</b>");		// Course end
		var options=new Object();											// Holds slider options
		
		options.slide=function(event, ui) {									// Slider move handler
			var which="#sliderText1";										// Assume 1st
			var val=ui.values[0];											// Get 1st slider value
			if (ui.value != val) {											// If second slider
				which="#sliderText2";										// Set name
				val=ui.values[1];											// Use 2nd val
				}
			var p=new Date(startTime-0+((endTime-startTime)*(val/1000)));	// Calc new date
			var str=_this.DateTimeString(p);								// Make into string
			var x=str.length*4;												// Offset amount
			$(which).html(str);												// Set value
			$(which).css("left",event.clientX-x+"px");						// Position
			_this.SetUserData("timeSlider1",ui.values[0]/1000);				// Save slider 1
			_this.SetUserData("timeSlider2",ui.values[1]/1000);				// Save slider 2
			};
		options.max=1000;													// 0-1000
		options.range=true;													// Range mode
		options.values=[userData.timeSlider1*1000,userData.timeSlider2*1000];	// Set times
		$(dd).slider(options);												// Draw slider
		
		for (i=0;i<2;++i) {													// For each slider
			p=new Date(startTime-0+((endTime-startTime)*(options.values[i]/1000)));	// Calc new date
			str=this.DateTimeString(p);										// Make into string
			x=str.length*4;													// Offset amount
			p=(400*options.values[i]/1000)+96;								// Position
			$("#sliderText"+(i+1)).html(str);								// Set value
			$("#sliderText"+(i+1)).css("left",p+"px");						// Position
			}
		if ((now > startTime) && (now < endTime)) {							// In range
			p=((now-startTime)/(endTime-startTime)*400)+115-0;				// Calc now pos
			$("#nowBar").css("left",p+"px");								// Position
			$("#nowText").css("left",p-9+"px");								// Position
			}
	}		
	
	PaneControl.prototype.DateTimeString=function(time) 				// CONVERT DATE OBJECT TO SIMPLE STRING
	{
		var str=(time.getMonth()+1)+"/"+time.getDate()+" ";					// Month/day
		if (time.getHours() > 12)	str+=time.getHours()-12;				// Before noon
		else		 				str+=time.getHours();					// After noon
		if (time.getHours() > 11)	str+="pm";								// PM
		else 						str+="am";								// AM
		return str;															// Return "MO/DY HRap"
	}

	PaneControl.prototype.DrawSeeDot=function(text, size, x, y, col)	// DRAW CIRCLE DOT LOGO
	{
		var str="<div style='position:absolute;left:"+x+"px;top:"+y+"px;width:"+size+"px;height:"+size+"px;padding:0px;"
		str+="border:2px solid "+col+";border-radius:"+size+"px;moz-border-radius;"+size+"px;color:"+col;
		str+=" !important;text-align:center;line-height:"+size+"px;font-size:"+size/2.5+"px;'>";
		str+="<b>"+text+"</b><div>";
		return str;
	}

	PaneControl.prototype.SetUserData=function(key, value)				// SAVE USER DATA
	{
		userData[key]=value;
	}




function trace(str) { console.log(str) };
	
	
</script>

<script>
	
	$('.sayBut').click(function(e){
			console.log(e);
			menuCSS.left=e.pageX + 'px';
			menuCSS.top=e.pageY + 'px';
			$('#overlay').show();
			new DSC_RadialMenu("",menuCSS,testMenu);
	});
	
	var menuCSS={ "background-color":"#006dcc", "border":".25px  #666 solid", left:"400px", top:"200px", width:"100px", "font-weight":"bold", "font-size":"15px", "color":"#fff", "z-index": "1099" };
	
	var testMenu={ lab:"Say",size:1, sub:[
					{ lab:"Comment",	size:.5, sub:[
						{ lab:"Respond",	size:.5, cb:function() { alert("Respond!!!"); }},
						{ lab:"Question",	size:.5, cb:function() { alert("Question???"); }},
						{ lab:"Summary",	size:.5, cb:function() { alert("Summary..."); }}
						]},
					{ lab:"Feedback",	size:.5, sub:[
						{ lab:"Agree",	size:.5, cb:function() { alert("Agree+++"); }},
						{ lab:"Clarify",	size:.5, cb:function() { alert("Clarify???"); }},
						{ lab:"Off-topic",	size:.5, cb:function() { alert("Off Topic *&^"); }}
						]},
					{ lab:"Media",	size:.5, sub:[
						{ lab:"Document",	size:.5, cb:function() { alert("Document []"); }},
						{ lab:"Web page",	size:.5, cb:function() { alert("Web-page ()"); }},
						{ lab:"Video",		size:.5, cb:function() { alert("Video >>"); }}
						]},
					{ lab:"Draw",		size:.5, sub:[
						{ lab:"White board",	size:.5, cb:function() { alert("Whiteboard!"); }},
						{ lab:"Concept map",	size:.5, cb:function() { alert("Concept map!"); }}
						]}
				]
				 };

	$(document).ready(function() {
		//new DSC_RadialMenu("",menuCSS,testMenu);
	});

////////////////////////////////////////////////////////////////////////////////////////////////////////////
//   RADIAL MENU  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
function DSC_RadialMenu(container, format, data)						// CONSTRUCTOR
{
	var i;
	this.data=data;															// Set menu structure
	this.format=format;														// Set menu formatting
	this.container="#"+container;											// Container that hold menu
	this.cx=format.left.replace(/px/,"");									// Center x
	this.cy=format.top.replace(/px/,"");									// Center y
	this.rad=format.width.replace(/px/,"")/2;								// Radius
	if (!container)															// If no container defined
		this.container="body";												// Append to body
	$("#DSCMenuDiv").remove();												// Remove old one, if there
	var str="<div id='DSCMenuDiv' style='position:absolute;left:"+this.cx+"px;top:"+this.cy+"px'/>";
	$(this.container).append(str);											// Add menu div to container
	if (data)																// If a structure defined
		this.Draw(this.data,0,0,0);											// Draw it via recursion
	var thisObj=this;														// Point to this		
	
	for (i=1;i<=data.sub.length;++i) {										// For each submenu
		$("#DSCDotDiv-"+(100*i)).mouseover(function(e) {					// Over sub dot
			for (var j=1;j<=data.sub.length;++j)							// For each submenu
				thisObj.Color(100*j,(this.id.substr(10) != 100*j));			// Grey all other buttons
			thisObj.ShowSub(this.id.substr(10));							// Show sub-submenu
			});
		}	

	$("#DSCDotDiv-0").mouseover(function(e) {								// Over main dot
		thisObj.Init("first");												// Show first level
		});

	$("#DSCDotDiv-0").click(function(e) {									// Click on main dot
			thisObj.Init("close");											// Close all
		});

	$("#DSCDotDiv-0").hide();												// First dot is closed
	this.Init("open");														// Init with main dot showing
}

DSC_RadialMenu.prototype.Init=function(mode) 							//	INIT BUTTON STATE
{
	var i;
	var thisObj=this;														// Point to this		
	var data=this.data;														// Point to data
	for (i=1;i<=this.data.sub.length;++i) {									// For each submenu
		$("#DSCTreeDiv-"+(100*i)).hide();									// Hide trees	
		thisObj.Color(100*i,false);											// All buttons colored
		if (mode == "first")												// If showing 1st level
			$("#DSCDotDiv-"+(100*i)).fadeIn(500);							// Show them	
		else																// Hiding everything
			$("#DSCDotDiv-"+(100*i)).hide();								// Hide them	
		if (data.sub[i-1].sub) {											// If sub-submenus
			for (j=1;j<=data.sub[i-1].sub.length;++j)						// For each sub-submenu
				$("#DSCDotDiv-"+((100*i)+j)).hide();						// Hide them	
			}
		}
	if (mode == "open")	{													// If opening
		$("#DSCDotDiv-0").show("scale",{}, 400);
		$('#overlay').fadeIn('slow');
		}								// Zoom in
	else if (mode == "close"){												// If closing
		$("#DSCDotDiv-0").hide("scale",{}, 400);
		$('#overlay').fadeOut('slow');
		}									// Hide them	
}

DSC_RadialMenu.prototype.ShowSub=function(id) 							//	SHOW SUB-SUBMENU
{
	var i,j;
	for (i=1;i<=this.data.sub.length;++i) {									// For each submenu
		if (this.data.sub[i-1].sub) {										// If sub-submenus
			if (Math.floor(id/100) == i)									// If the selected one
				$("#DSCTreeDiv-"+(100*i)).fadeIn(0);						// Show them
			else															// Others
				$("#DSCTreeDiv-"+(100*i)).hide();							// Hide them	
			for (j=1;j<=this.data.sub[i-1].sub.length;++j) {				// For each sub-submenu
				if (Math.floor(id/100) == i)								// If the selected one
					$("#DSCDotDiv-"+((100*i)+j)).fadeIn(0);					// Show them
				else														// Others
					$("#DSCDotDiv-"+((100*i)+j)).hide();					// Hide them	
				}
			}
		}
}

DSC_RadialMenu.prototype.Color=function(id, gray) 						//	COLOR DOT
{
	var o=$("#DSCDotDiv-"+id);												// ID of base dot
	if (gray)																// If graying it
		o.css({border:"solid 1px #ddd",										// Gray rim
		background:"#eee",													// Gray interior
		"text-shadow":"0 0px 0px"											// No shadow
		});
	else{																	// Colored dot
		background:this.format["background-color"],
		o.css({border:"solid 1px rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25)",			// Gradient
		"text-shadow":"0 1px 1px rgba(0,0,0,.5)",
		});
		if ($.browser.mozilla)												
			o.css("background","-moz-linear-gradient(top,#5BC0DE,#2F96B4)");
		else 
			o.css("background","-webkit-linear-gradient(top,#5BC0DE,#2F96B4)")
		}
}

DSC_RadialMenu.prototype.Draw=function(dat, x, y, id) 					//	DRAW DOT
{
	var i,j;
	if (!dat)																// No data
		return;																// Quit
	var thisObj=this;														// Point to this		
	var r=this.rad*dat.size;												// Scaled radius	
	var fs=this.format["font-size"].replace(/px/,"")*dat.size;				// Scaled font size										
	var str="<div id='DSCDotDiv-"+id+"' style='position:absolute;text-align:center'>";		// Div start
	str+=dat.lab+"</div>";													// Add label and end div
	$("#DSCMenuDiv").append(str);											// Add menu div
	$("#DSCDotDiv-"+id).mouseover(function(){$(this).css("color","yellow") });	
	$("#DSCDotDiv-"+id).mouseout(function(){$(this).css("color","white") });	
	var o=$("#DSCDotDiv-"+id);												// ID of base dot
	o.css(this.format);														// Set format
	var x1=x-r;			var y1=y-r;											// Position											
	if (id%100) {															// If 2nd selector
		o.css({"border-radius":"8px","moz-border-radius":"8px"});			// Set corners to make circle
		o.css("font-size",fs+"px");											// Adjust font size
		o.css({ width:(r*3)+"px",height:r/2+"px"});							// Set size
		}
	else{
		o.css({ width:(r+r)+"px",height:(r+r)+"px"});						// Set size
		o.css({"border-radius":r+"px","moz-border-radius":r+"px"});			// Set corners to make circle
		o.css("line-height",(r+r)+"px");									// Center vertically
		}
	o.css({border:"solid 1px "+this.format["background-color"],				// Gradient
		background:this.format["background-color"],
		"text-shadow":"0 1px 1px rgba(0,0,0,.5)",
		"box-shadow":"0 4px 8px rgba(0,0,0,.3)",
		});
	if ($.browser.mozilla)	
		o.css("background","-moz-linear-gradient(top,#5BC0DE,#2F96B4)");
	else 
		o.css("background","-webkit-linear-gradient(top,#5BC0DE,#2F96B4)")
	o.css({left:x1+"px",top:y1+"px"});										// Set position
	if (id == 0)															// If main dot
		o.css("font-size",fs*2+"px");										// Double font size
	else																	// A sub dor=t
		o.css("font-size",fs+"px");											// Adjust font size
	if ((dat.sub) && (id == 0)) {											// If first ring of sub menus												
		var d=Number(r+(r/2)+12);											// Diameter
		var a=-(Math.PI/2);													// Start at top (radians)
		var step=(2*Math.PI)/(Math.PI*2*d/(r+24));							// Step size
		for (i=1;i<=dat.sub.length;++i)	{									// For each sub menu
			x1=x+d*Math.cos(a);			y1=x+d*Math.sin(a);					// Circle around
			a+=step;														// Next angle
			this.Draw(dat.sub[i-1],x1,y1,(100*i));							// Draw it recursively
			}
		}	
	else if ((dat.sub) && (id != 0)) {										// If 2nd ring of sub menus												
		x1=(r*6.5);															// To the right
		y1=(dat.sub.length-3)*r/-2;								
		var base="<div style='position:absolute;background-color:"+this.format["background-color"]+";";	
		$("#DSCDotDiv-"+id).append("<div id='DSCTreeDiv-"+id+"' style='position:absolute;top:0px'/>");
		str=base+"left:"+(r+r)+"px;top:"+r+"px;height:3px;width:"+(x1-r-r-12-x)+"px'/>";
		$("#DSCTreeDiv-"+id).append(str);									// Add first line
		str=base+"left:"+(x1-12-x)+"px;top:"+y1+"px;width:3px;height:"+((dat.sub.length-1)*r)+"px'/>";
		$("#DSCTreeDiv-"+id).append(str);									// Add first line

		for (j=0;j<dat.sub.length;++j)	{									// For each sub menu
			str=base+"left:"+(x1-12-x)+"px;top:"+y1+"px;height:3px;width:12px'/>";
			$("#DSCTreeDiv-"+id).append(str);								// Add it
			y1+=r;															// Advance down
			}
		$("#DSCTreeDiv-"+id).hide();										// Hide dot
		y1=y-((dat.sub.length*r)/2)+r+fs;									// Top
		for (j=0;j<dat.sub.length;++j)	{									// For each sub menu
			this.Draw(dat.sub[j],x1,y1,(id+(j+1)));							// Draw it recursively
			str=base+"left:"+(x1-12)+"px;top:"+(j*r)+"px;height:3px;width:12px'/>";
			y1+=r;															// Stack vertically
			}
		}

	$("#DSCDotDiv-"+id).hide();												// Hide dot
	dat=this.data;															// Point at data set
	
	$("#DSCDotDiv-"+id).click(function(e) {									// Click on main dot
		var a=Math.floor((this.id.substr(10)/100))-1;						// First layer		
		if (a < 0)															// First dot
			return;															// Quit
		var b=(this.id.substr(10)%100)-1;									// Second layer		
		if ((b < 0) && (dat.sub[a].cb))										// First level dot w/ cb
			dat.sub[a].cb();												// Run callback
		else if ((b != -1) && (dat.sub[a].sub[b].cb))						// 2nd level dot w/ cb
			dat.sub[a].sub[b].cb();											// Run callback
			thisObj.Init("close");											// Close all
		});
}

	
	
</script>

</body>
</html>
<?php

	}  
	
?>