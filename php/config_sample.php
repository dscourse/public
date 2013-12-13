<?php 

if(!defined('MyConst')){
	die('Direct access not permitted');
}   

if(!isset($_SESSION)) 
	session_start();  
  
$dbhost = ""; 
$port = "";		 			
$dbname = ""; 						
$dbuser = "";							   
$dbpass = ""; 							
 
mysql_connect("$dbhost:$port", $dbuser, $dbpass) or die("MySQL Error: " . mysql_error());  
mysql_select_db($dbname) or die("MySQL Error: " . mysql_error());  		    
$pdo = new PDO("mysql:host=$dbhost;port=$port;dbname=$dbname",$dbuser,$dbpass);

?>
