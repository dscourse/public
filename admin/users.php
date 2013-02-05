<? 
include('admin_header.php');

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> Users</h1>
            <a class="btn pull-right" href=newuser.php>New Row</a>
        </div>
    </header>
 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">
<?php
 
echo "<table border=1 >"; 
echo "<tr>"; 
echo "<td><b></b></td>"; 
echo "<td><b>Username</b></td>"; 
echo "<td><b>FirstName</b></td>"; 
echo "<td><b>LastName</b></td>"; 
echo "<td><b>SysRole</b></td>"; 
echo "<td><b>UserFacebook</b></td>"; 
echo "<td><b>UserTwitter</b></td>"; 
echo "<td><b>UserPhone</b></td>"; 
echo "<td><b>UserWebsite</b></td>"; 
echo "<td><b>UserPictureURL</b></td>"; 
echo "<td><b>UserStatus</b></td>"; 
echo "<td><b>UserProfile</b></td>"; 
echo "<td><b>UserAbout</b></td>"; 
echo "<td><b>UserRecovery</b></td>"; 
echo "<td><b>UserRecoveryTime</b></td>"; 
echo "</tr>"; 
$result = mysql_query("SELECT * FROM `users`") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td valign='top'>" . nl2br( $row['UserID']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['username']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['firstName']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['lastName']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['sysRole']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userFacebook']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userTwitter']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userPhone']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userWebsite']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userPictureURL']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userStatus']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userProfile']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userAbout']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userRecovery']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['userRecoveryTime']) . "</td>";  
echo "<td valign='top'><a href=edituser.php?UserID={$row['UserID']} class='btn btn-info'>Edit</a></td>";  //<td><a href=deleteuser.php?UserID={$row['UserID']} class='btn btn-error'>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
echo ""; 
?>

            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 