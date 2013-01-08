<? 
include('admin_header.php');

?>
    <header class="jumbotron subhead">
        <div class="container-fluid">
            <h1> Networks </h1>
            <a class='btn pull-right' href=addnetwork.php>New Row</a>
        </div>
    </header>
<? 
echo "<table border=1 >"; 
echo "<tr>"; 
echo "<td><b>NetworkID</b></td>"; 
echo "<td><b>NetworkName</b></td>"; 
echo "<td><b>NetworkDesc</b></td>"; 
echo "<td><b>NetworkCode</b></td>"; 
echo "<td><b>NetworkTime</b></td>"; 
echo "</tr>"; 
$result = mysql_query("SELECT * FROM `networks`") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td valign='top'>" . nl2br( $row['networkID']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['networkName']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['networkDesc']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['networkCode']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['networkTime']) . "</td>";  
echo "<td valign='top'><a href=editnetwork.php?networkID={$row['networkID']} class='btn btn-info'>Edit</a></td><td><a href=deletenetwork.php?networkID={$row['networkID']} class='btn btn-error'>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
?> 
     <div id="" class=" wrap page" >
        <div class="container-fluid">
            <div class="row-fluid">


            </div>
        </div><!-- close container -->
    </div><!-- end home-->
 
 
</body>
</html> 