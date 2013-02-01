<?php define('MyConst', TRUE);	include "../../config/config.php"; 
	$_SESSION = array(); 
	session_destroy(); 
	setcookie('userCookieDscourse', '', time()-3600, '/');
	header("Location: info.php");
?> 
