<?
include('../includes/dbconnect.php');
include('../includes/functions.php'); 
include('../includes/global.php'); 


	$qry_acv = "SELECT  trad_trade_reference_number, max(trad_buy_sell) as b_s from
											nfs_trades group by trad_trade_reference_number";
											
	$result_acv = mysql_query($qry_acv) or die (tdw_mysql_error($qry_acv));
	while ( $row_acv = mysql_fetch_array($result_acv) ) 
	{
	$qry_upd = "update rep_comm_rr_trades set trad_buy_sell = '".$row_acv["b_s"]."' 
	            where trad_reference_number = '".$row_acv["trad_trade_reference_number"]."'";
	$result_upd = mysql_query($qry_upd) or die (tdw_mysql_error($qry_upd));
	echo $row_acv["trad_trade_reference_number"]."__".$row_acv["b_s"]."<br>";
	}


?>
