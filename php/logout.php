<?php define('MyConst', TRUE);	include "../../config/config.php"; 
	$_SESSION = array(); 
	session_destroy(); 
	setcookie('userCookieDscourse', '', time()-3600, '/');
?> 
<meta http-equiv="refresh" content="0;../index.php">