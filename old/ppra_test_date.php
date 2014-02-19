<?
include('includes/dbconnect.php');

$test = mysql_query("select DATE_FORMAT(date1,'%b %D, %Y') as date1 from ztest") or die (mysql_error());

while ( $row = mysql_fetch_array($test) ) {
			
					echo $row["date1"];
					echo "<br>".date("Y-m-d h:m:i");
			
					}


?>