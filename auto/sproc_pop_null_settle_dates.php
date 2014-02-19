<?
include('../includes/functions.php');
include('../includes/dbconnect.php');
include('../includes/global.php');

	$query_no_settle = "SELECT DISTINCT (
											  trad_reference_number
											) AS trad_reference_number
											FROM mry_comm_rr_trades
											WHERE trad_settle_date IS NULL 
											OR trad_settle_date = ''";
	$result_no_settle = mysql_query($query_no_settle) or tdw_mysql_error($query_no_settle);
	
	while($row_no_settle = mysql_fetch_array($result_no_settle)) {
	  
			$query_get_settle = "SELECT max( trad_settle_date ) AS trad_settle_date
														FROM nfs_trades
														WHERE trad_trade_reference_number = '".$row_no_settle["trad_reference_number"]."'
														GROUP BY trad_trade_reference_number";
														
			$result_get_settle = mysql_query($query_get_settle) or tdw_mysql_error($query_get_settle);
			
			while($row_get_settle = mysql_fetch_array($result_get_settle)) {
			  
				//update the rep and mry trades table
				//echo $row_no_settle["trad_reference_number"]."/".$row_get_settle["trad_settle_date"]."<br>";
				$query_update_mry_settle = "UPDATE mry_comm_rr_trades
																		SET trad_settle_date = '".$row_get_settle["trad_settle_date"]."'
																		WHERE trad_reference_number = '".$row_no_settle["trad_reference_number"]."'";
		    $result_update_mry_settle = mysql_query($query_update_mry_settle) or tdw_mysql_error($query_update_mry_settle);
				$query_update_rep_settle = "UPDATE rep_comm_rr_trades
																		SET trad_settle_date = '".$row_get_settle["trad_settle_date"]."'
																		WHERE trad_reference_number = '".$row_no_settle["trad_reference_number"]."'";
		    $result_update_rep_settle = mysql_query($query_update_rep_settle) or tdw_mysql_error($query_update_rep_settle);
				echo $row_no_settle["trad_reference_number"]."<br>".
				     $row_get_settle["trad_settle_date"]."<br>".
						 $query_update_mry_settle."<br>".
						 $query_update_rep_settle."<br>";
			}
	}
?>