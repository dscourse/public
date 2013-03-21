<?php

function hmacsha1($key, $data) {//snippet written by Kellan Elliott-McCrea; http://laughingmeme.org/
	$blocksize = 64;
	$hashfunc = 'sha1';
	if (strlen($key) > $blocksize)
		$key = pack('H*', $hashfunc($key));
	$key = str_pad($key, $blocksize, chr(0x00));
	$ipad = str_repeat(chr(0x36), $blocksize);
	$opad = str_repeat(chr(0x5c), $blocksize);
	$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
	return $hmac;
}
class User{
	public $attrs = array('firstName'=>'lis_person_name_given','lastName'=>'lis_person_name_family','username'=>'lis_person_contact_email_primary', 'role'=>'roles');
}
class LTILaunch{
	public $params = array('discID'=>'resource_link_id','courseName'=>'context_title','courseId'=>'context_id');	
	public $user;
}

function parseLTIrequest($postData){
$logfile = "LTIlog.txt";
$fh = fopen($logfile, 'a') or die("can't open file");
fwrite($fh, "\n\n---------------------------------------------------------------\n");
foreach ($_SERVER as $h => $v)
	if (preg_match('/HTTP_(.+)/', $h, $hp))
		fwrite($fh, "$h = $v\n");
fwrite($fh, "\r\n");
fwrite($fh, file_get_contents('php://input'));
//YOU MUST manually configure the following 2 variables
// endpoint is the location of your JS
// secret is the OAuth secret established between the LTI provider and consumer
$secret = "secret&";
$sentSig = "";
//BUILDING THE OAUTH BASE STRING
//assume POST, get base URL
$baseString = "POST&" . rawurlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . "&";
//grab POST args
$postArgs = explode("&", preg_replace("/[+]/m", "%20", $postData));
//sort and normalize
sort($postArgs);
//remove signature
for ($i = 0; $i < count($postArgs); $i++) {
	$arg = explode("=", $postArgs[$i]);
	if ($arg[0] == "oauth_signature") {
		$sentSig = urldecode($arg[1]);
		unset($postArgs[$i]);
	}
}
$baseString = $baseString . rawurlencode(join("&", $postArgs));
$sig = base64_encode(hmacsha1($secret, $baseString));
$msg = "";
if (strcmp($sentSig, $sig)==0) {
	$msg .= "Validation success";
	$launch = new LTILaunch();
	$opts = array_keys($launch->params);
	for($i=0;$i<count($opts);$i++){
		$arg = $launch->params[$opts[$i]]; 
		$launch->params[$opts[$i]] = $_POST[$arg];
	}
	$user = new User();
	$opts = array_keys($user->attrs);
	for($i=0;$i<count($opts);$i++){
		$arg = $user->attrs[$opts[$i]]; 
		$user->attrs[$opts[$i]] = $_POST[$arg];
	}
	$launch->user=$user;
	return $launch;
} else {
	$msg .= "Validation fail <br /> Expected signature base string: ".$baseString . "<br />";
	exit($msg);
	return FALSE;
}
fclose($fh);
}
?>