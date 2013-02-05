<? 
include('admin_header.php'); 

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> New User</h1>
        </div>
    </header>
 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

<?php
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "INSERT INTO `users` ( `username` ,  `password` ,  `firstName` ,  `lastName` ,  `sysRole` ,  `userFacebook` ,  `userTwitter` ,  `userPhone` ,  `userWebsite` ,  `userPictureURL` ,  `userStatus` ,  `userProfile` ,  `userAbout` ,  `userRecovery` ,  `userRecoveryTime`  ) VALUES(  '{$_POST['username']}' ,  '{$_POST['password']}' ,  '{$_POST['firstName']}' ,  '{$_POST['lastName']}' ,  '{$_POST['sysRole']}' ,  '{$_POST['userFacebook']}' ,  '{$_POST['userTwitter']}' ,  '{$_POST['userPhone']}' ,  '{$_POST['userWebsite']}' ,  '{$_POST['userPictureURL']}' ,  '{$_POST['userStatus']}' ,  '{$_POST['userProfile']}' ,  '{$_POST['userAbout']}' ,  '{$_POST['userRecovery']}' ,  '{$_POST['userRecoveryTime']}'  ) "; 
mysql_query($sql) or die(mysql_error()); 
echo "Added row.<br />"; 
echo "<a href='users.php'>Back To Listing</a>"; 
} 
?>

<form action='' method='POST'> 
<p><b>Username:</b><br /><input type='text' name='username'/> 
<p><b>Password:</b><br /><input type='text' name='password'/> 
<p><b>FirstName:</b><br /><input type='text' name='firstName'/> 
<p><b>LastName:</b><br /><input type='text' name='lastName'/> 
<p><b>SysRole:</b><br /><input type='text' name='sysRole'/> 
<p><b>UserFacebook:</b><br /><input type='text' name='userFacebook'/> 
<p><b>UserTwitter:</b><br /><input type='text' name='userTwitter'/> 
<p><b>UserPhone:</b><br /><input type='text' name='userPhone'/> 
<p><b>UserWebsite:</b><br /><input type='text' name='userWebsite'/> 
<p><b>UserPictureURL:</b><br /><input type='text' name='userPictureURL'/> 
<p><b>UserStatus:</b><br /><input type='text' name='userStatus'/> 
<p><b>UserProfile:</b><br /><textarea name='userProfile'></textarea> 
<p><b>UserAbout:</b><br /><textarea name='userAbout'></textarea> 
<p><b>UserRecovery:</b><br /><input type='text' name='userRecovery'/> 
<p><b>UserRecoveryTime:</b><br /><input type='text' name='userRecoveryTime'/> 
<p><input type='submit' value='Add Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 

            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 