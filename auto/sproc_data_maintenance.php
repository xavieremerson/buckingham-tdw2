<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

/*
//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!";
		exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!";
	}
  echo "<br>Proceeding after holiday/weekend check....";
//====================================================================================================
*/

//////////////////////////////////////////////////////////////////////////////////////////////////////
//CENTS PER SHARE UPDATED WHERE ZERO
$qry_cps = "update rep_comm_rr_trades set trad_cents_per_share = (trad_commission/trad_quantity) where trad_cents_per_share = 0";
$result_cps = mysql_query($qry_cps) or die (tdw_mysql_error($qry_cps));

$qry_cps = "update mry_comm_rr_trades set trad_cents_per_share = (trad_commission/trad_quantity) where trad_cents_per_share = 0";
$result_cps = mysql_query($qry_cps) or die (tdw_mysql_error($qry_cps));

//////////////////////////////////////////////////////////////////////////////////////////////////////
//POPULATE ADVISOR NAMES IN TRADES DATA
	
//Create Lookup Array of Client Code / Client Name
	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}


//Create Lookup Array of Client Code / Client Name
	$qry_accts = "select trim(nadd_full_account_number) as nadd_full_account_number, substring(nadd_advisor,1,4) as clnt_code  from mry_nfs_nadd";
	$result_accts = mysql_query($qry_accts) or die (tdw_mysql_error($qry_accts));
	$arr_accts = array();
	while ( $row_accts = mysql_fetch_array($result_accts) ) 
	{
		$arr_accts[$row_accts["nadd_full_account_number"]] = $row_accts["clnt_code"];
	}


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Clean rep_comm_rr_trades / mry_comm_rr_trades tables
$qry_wrong_advisor_code = "SELECT * FROM rep_comm_rr_trades 
                           where trad_advisor_code not in (select clnt_code from int_clnt_clients)
													 and trad_advisor_code not in ('ROBX','GARX') 
													 and trad_is_cancelled = 0
													 and trad_trade_date > '2007-01-01'"; 
$result_wrong_advisor_name = mysql_query($qry_wrong_advisor_code) or die (tdw_mysql_error($qry_wrong_advisor_code));
while ( $row = mysql_fetch_array($result_wrong_advisor_name) ) 
{
	ydebug("Client Code", $row["trad_reference_number"]."/".$row["trad_advisor_code"]."/".$row["trad_account_number"]."/".$arr_accts[$row["trad_account_number"]]);
	
		$qry_update = "update rep_comm_rr_trades set trad_advisor_code = '".$arr_accts[$row["trad_account_number"]]."' where trad_reference_number = '".$row["trad_reference_number"]."'";
		//ydebug("qry_update",$qry_update);
		$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
		$qry_update = "update mry_comm_rr_trades set trad_advisor_code = '".$arr_accts[$row["trad_account_number"]]."' where trad_reference_number = '".$row["trad_reference_number"]."'";
		//ydebug("qry_update",$qry_update);
		$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));

}


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// GOOD
//Clean rep_comm_rr_trades / mry_comm_rr_trades tables
$qry_null_advisor_name = "SELECT * FROM rep_comm_rr_trades where trad_advisor_name = '' and trad_is_cancelled = 0"; 
$result_null_advisor_name = mysql_query($qry_null_advisor_name) or die (tdw_mysql_error($qry_null_advisor_name));
$i = 0;
while ( $row = mysql_fetch_array($result_null_advisor_name) ) 
{
	ydebug($i . " detail", $row["trad_reference_number"]." ".$row["trad_advisor_code"]." ".$row["trad_trade_date"]);
	ydebug("Mapped Client Name", $arr_clients[$row["trad_advisor_code"]]);
	
	if ($arr_clients[$row["trad_advisor_code"]]!= '') {
		$qry_update = "update rep_comm_rr_trades set trad_advisor_name = '".$arr_clients[$row["trad_advisor_code"]]."' where trad_reference_number = '".$row["trad_reference_number"]."'";
		ydebug("qry_update",$qry_update);
		$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
		$qry_update = "update mry_comm_rr_trades set trad_advisor_name = '".$arr_clients[$row["trad_advisor_code"]]."' where trad_reference_number = '".$row["trad_reference_number"]."'";
		ydebug("qry_update",$qry_update);
		$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
	}
	
	$i = $i + 1;
	
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++





















/*
//Clean rep_comm_rr_level_a / mry_comm_rr_level_a tables
$qry_null_advisor_name = "SELECT * FROM rep_comm_rr_level_a where comm_advisor_name = ''"; 
$result_null_advisor_name = mysql_query($qry_null_advisor_name) or die (tdw_mysql_error($qry_null_advisor_name));
$i = 0;
while ( $row = mysql_fetch_array($result_null_advisor_name) ) 
{
	ydebug($i . " detail", $row["comm_auto_id"]." ".$row["comm_advisor_code"]." ".$row["comm_trade_date"]);
	ydebug("Mapped Client Name", $arr_clients[$row["comm_advisor_code"]]);
	
	if ($arr_clients[$row["comm_advisor_code"]]!= '') {
		$qry_update = "update rep_comm_rr_level_a set comm_advisor_name = '".$arr_clients[$row["comm_advisor_code"]]."' where comm_auto_id = '".$row["comm_auto_id"]."'";
		ydebug("qry_update",$qry_update);
		//$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
		$qry_update = "update mry_comm_rr_level_a set comm_advisor_name = '".$arr_clients[$row["comm_advisor_code"]]."' where comm_auto_id = '".$row["comm_auto_id"]."'";
		ydebug("qry_update",$qry_update);
		//$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
	}

	$i = $i + 1;
	
}
*/
?>
