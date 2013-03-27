<?php

ini_set('display_errors',1); 
 error_reporting(E_ALL);   
 
  define('MyConst', TRUE);                                // Avoids direct access to config.php

    include "../config/config.php"; 
	date_default_timezone_set('UTC');
	
	$location = "";

	if(isset($_GET['a'])){
		$code = $_GET['a'];
		$a = mysql_query("SELECT * FROM options where optionsValue = '$code'");
		$res = mysql_fetch_assoc($a);
		if(count($res)==0){
			header('Location: index.php');
		}
		else{
			$type = $res['optionsType'];
			$id = $res['optionsTypeID'];
			$code;
			if($type == "course"){
				$location.="course.php?";
				$code = 'c';
			}
			else if($type=="discussion"){
				$location.="discussion.php";
				$code = 'd';
			}
			$location.=$code."=".$id;
		}
	}
	else{
		header('Location: index.php');
	}
	
	header("Location: $location");
?>