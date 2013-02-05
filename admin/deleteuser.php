<? 
include('admin_header.php'); 

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> Delete User</h1>
        </div>
    </header>
 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

<? 
$UserID = (int) $_GET['UserID']; 
mysql_query("DELETE FROM `users` WHERE `UserID` = '$UserID' ") ; 
echo (mysql_affected_rows()) ? "Row deleted.<br /> " : "Nothing deleted.<br /> "; 
?> 

<a href='users.php'>Back To Listing</a>

            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 