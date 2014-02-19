<?php

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 
	
$qry_slx = "SELECT * from slx_temp";
$result_slx = mysql_query($qry_slx) or die (mysql_error());	 
									
while ( $row_slx = mysql_fetch_array($result_slx) ) 
	{
		if ($prev_processed != $row_slx["company"]) {

		xdebug("company", $row_slx["company"]);
		echo "Possible Matches: <br>";
		
		$qry_nadd = "SELECT nadd_rr_exec_rep, nadd_short_name, nadd_address_line_1 from nfs_nadd where upper(nadd_address_line_1) like '%".str_replace("'","\'",substr($row_slx["company"],0,6))."%' or upper(nadd_short_name) like '".str_replace("'","\'",substr($row_slx["company"],0,4))."%'";
		$result_nadd = mysql_query($qry_nadd) or die (mysql_error());	 
				
		while ( $row_nadd = mysql_fetch_array($result_nadd) ) 
			{
				echo "[".$row_nadd["nadd_rr_exec_rep"] ."][".$row_nadd["nadd_short_name"] ."][".$row_nadd["nadd_address_line_1"]."]<br>";
			}
		echo "<br><br>";
		}
	$prev_processed = $row_slx["company"];
	}

?>