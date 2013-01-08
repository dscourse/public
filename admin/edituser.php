<? 
include('admin_header.php'); 

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> Edit User</h1>
        </div>
    </header>
 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

<? 
if (isset($_GET['UserID']) ) { 
$UserID = (int) $_GET['UserID']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "UPDATE `users` SET  `username` =  '{$_POST['username']}' ,  `password` =  '{$_POST['password']}' ,  `firstName` =  '{$_POST['firstName']}' ,  `lastName` =  '{$_POST['lastName']}' ,  `sysRole` =  '{$_POST['sysRole']}' ,  `userFacebook` =  '{$_POST['userFacebook']}' ,  `userTwitter` =  '{$_POST['userTwitter']}' ,  `userPhone` =  '{$_POST['userPhone']}' ,  `userWebsite` =  '{$_POST['userWebsite']}' ,  `userPictureURL` =  '{$_POST['userPictureURL']}' ,  `userStatus` =  '{$_POST['userStatus']}' ,  `userProfile` =  '{$_POST['userProfile']}' ,  `userAbout` =  '{$_POST['userAbout']}' ,  `userRecovery` =  '{$_POST['userRecovery']}' ,  `userRecoveryTime` =  '{$_POST['userRecoveryTime']}'   WHERE `UserID` = '$UserID' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited row.<br />" : "Nothing changed. <br />"; 
echo "<a href='users.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `users` WHERE `UserID` = '$UserID' ")); 
?>

<form action='' method='POST'> 
<p><b>Username:</b><br /><input type='text' name='username' value='<?= stripslashes($row['username']) ?>' /> 
<p><b>Password:</b><br /><input type='text' name='password' value='<?= stripslashes($row['password']) ?>' /> 
<p><b>FirstName:</b><br /><input type='text' name='firstName' value='<?= stripslashes($row['firstName']) ?>' /> 
<p><b>LastName:</b><br /><input type='text' name='lastName' value='<?= stripslashes($row['lastName']) ?>' /> 
<p><b>SysRole:</b><br /><input type='text' name='sysRole' value='<?= stripslashes($row['sysRole']) ?>' /> 
<p><b>UserFacebook:</b><br /><input type='text' name='userFacebook' value='<?= stripslashes($row['userFacebook']) ?>' /> 
<p><b>UserTwitter:</b><br /><input type='text' name='userTwitter' value='<?= stripslashes($row['userTwitter']) ?>' /> 
<p><b>UserPhone:</b><br /><input type='text' name='userPhone' value='<?= stripslashes($row['userPhone']) ?>' /> 
<p><b>UserWebsite:</b><br /><input type='text' name='userWebsite' value='<?= stripslashes($row['userWebsite']) ?>' /> 
<p><b>UserPictureURL:</b><br /><input type='text' name='userPictureURL' value='<?= stripslashes($row['userPictureURL']) ?>' /> 
<p><b>UserStatus:</b><br /><input type='text' name='userStatus' value='<?= stripslashes($row['userStatus']) ?>' /> 
<p><b>UserProfile:</b><br /><textarea name='userProfile'><?= stripslashes($row['userProfile']) ?></textarea> 
<p><b>UserAbout:</b><br /><textarea name='userAbout'><?= stripslashes($row['userAbout']) ?></textarea> 
<p><b>UserRecovery:</b><br /><input type='text' name='userRecovery' value='<?= stripslashes($row['userRecovery']) ?>' /> 
<p><b>UserRecoveryTime:</b><br /><input type='text' name='userRecoveryTime' value='<?= stripslashes($row['userRecoveryTime']) ?>' /> 
<p><input type='submit' value='Edit Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 
<? } ?> 


            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 