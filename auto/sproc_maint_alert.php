<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

	ydebug("\n".'Process Start Time', date('m/d/Y H:i:s a'));

////
// Check for clients in mry_comm_rr_trades where there are mismatched client/advisor codes
																$query_sel_client = "SELECT trad_advisor_code, max( trad_advisor_name ) as trad_advisor_name 
																											FROM mry_comm_rr_trades
																											WHERE trad_advisor_code NOT LIKE '&%'
																											GROUP BY trad_advisor_code
																											ORDER BY trad_advisor_name, trad_advisor_code";
																$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
																while($row_sel_client = mysql_fetch_array($result_sel_client))
																{
																	if ($row_sel_client["trad_advisor_name"] == '') {
																	$display_val_client = $row_sel_client["trad_advisor_code"];
																	} else {
																	$display_val_client = $row_sel_client["trad_advisor_name"];
																	}
																	echo $row_sel_client["trad_advisor_code"]."> ".$display_val_client."<br>";
																}



 	  ydebug('Process Finish Time', date('m/d/Y H:i:s a'));

?>
