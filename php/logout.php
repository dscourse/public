<?php define('MyConst', TRUE);	include "./../config/config.php"; $_SESSION = array(); session_destroy(); 
	setcookie('userCookieDscourse', '', time()-60*60*24*365);
?> 
<meta http-equiv="refresh" content="0;../index.php">