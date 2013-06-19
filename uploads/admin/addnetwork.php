<? 
include('admin_header.php');

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> Add Network</h1>
        </div>
    </header>
<? 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "INSERT INTO `networks` ( `networkName` ,  `networkDesc` ,  `networkCode` ,  `networkTime`  ) VALUES(  '{$_POST['networkName']}' ,  '{$_POST['networkDesc']}' ,  '{$_POST['networkCode']}' ,  '{$_POST['networkTime']}'  ) "; 
mysql_query($sql) or die(mysql_error()); 
echo "Added row.<br />"; 
echo "<a href='networks.php'>Back To Listing</a>"; 
} 
?>

<form action='' method='POST'> 
<p><b>NetworkName:</b><br /><input type='text' name='networkName'/> 
<p><b>NetworkDesc:</b><br /><textarea name='networkDesc'></textarea> 
<p><b>NetworkCode:</b><br /><input type='text' name='networkCode'/> 
<p><b>NetworkTime:</b><br /><input type='text' name='networkTime'/> 
<p><input type='submit' value='Add Row' class="btn" /><input type='hidden' value='1' name='submitted' /> 
</form> 
 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 