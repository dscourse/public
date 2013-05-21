<?php
	function mail_init($mail) {
		date_default_timezone_set('UTC');
		//Tell PHPMailer to use SMTP
		$mail -> IsSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail -> SMTPDebug = 1;
		//Ask for HTML-friendly debug output
		$mail -> Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail -> Host = "mail.virginia.edu";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail -> Port = 25;
		//Whether to use SMTP authentication
		$mail -> SMTPAuth = false;
		//Set who the message is to be sent from
		$mail -> SetFrom('no-reply@dscourse.org', 'dscourse.org');
		//Set an alternative reply-to address
		$mail -> AddReplyTo('no-reply@dscourse.org', 'no-reply');
		//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
		return $mail;
	}
?>