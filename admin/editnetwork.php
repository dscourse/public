<? 
include('admin_header.php');

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> Edit Network</h1>
        </div>
    </header>
 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

<? 
if (isset($_GET['networkID']) ) { 
$networkID = (int) $_GET['networkID']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "UPDATE `networks` SET  `networkName` =  '{$_POST['networkName']}' ,  `networkDesc` =  '{$_POST['networkDesc']}' ,  `networkCode` =  '{$_POST['networkCode']}' ,  `networkTime` =  '{$_POST['networkTime']}'   WHERE `networkID` = '$networkID' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited row.<br />" : "Nothing changed. <br />"; 
echo "<a href='networks.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `networks` WHERE `networkID` = '$networkID' ")); 
?>

<form action='' method='POST'> 
<p><b>NetworkName:</b><br /><input type='text' name='networkName' value='<?= stripslashes($row['networkName']) ?>' /> 
<p><b>NetworkDesc:</b><br /><textarea name='networkDesc'><?= stripslashes($row['networkDesc']) ?></textarea> 
<p><b>NetworkCode:</b><br /><input type='text' name='networkCode' value='<?= stripslashes($row['networkCode']) ?>' /> 
<p><b>NetworkTime:</b><br /><input type='text' name='networkTime' value='<?= stripslashes($row['networkTime']) ?>' /> 
<p><input type='submit' value='Edit Row' class="btn"/><input type='hidden' value='1' name='submitted' /> 
</form> 
<? } ?> 

            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 