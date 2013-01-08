<? 
include('admin_header.php');

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> Delete Network</h1>
        </div>
    </header>
 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">

<? 
$networkID = (int) $_GET['networkID']; 
mysql_query("DELETE FROM `networks` WHERE `networkID` = '$networkID' ") ; 
echo (mysql_affected_rows()) ? "Row deleted.<br /> " : "Nothing deleted.<br /> "; 
?> 

<a href='networks.php'>Back To Listing</a>

            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 