<?
include('includes/dbconnect.php');
include('includes/global.php'); 
include('includes/functions.php'); 

$trade_date_to_process = previous_business_day();



//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
}

//Get the Name/Address information into Memory Table for lookup purposes
$result_nadd_flush = mysql_query("truncate table mry_nfs_nadd") or die (mysql_error());
$result_nadd_populate = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (tdw_mysql_error("insert into mry_nfs_nadd select * from nfs_nadd"));

//Create an array of account names and advisor code for lookup.
$qry_acct_adv = "select nadd_full_account_number, nadd_advisor from mry_nfs_nadd";
$result_acct_adv = mysql_query($qry_acct_adv) or die (tdw_mysql_error($qry_acct_adv));
$arr_acct_adv = array();
while ( $row_acct_adv = mysql_fetch_array($result_acct_adv) ) 
{
	$arr_acct_adv[strtoupper(trim($row_acct_adv["nadd_full_account_number"]))] = $row_acct_adv["nadd_advisor"];
}
//show_array($arr_acct_adv);

$tmp_qry = "SELECT * 
						FROM `mry_comm_rr_trades` 
						WHERE `trad_advisor_code` = ''";
$tmp_result = mysql_query($tmp_qry) or die (tdw_mysql_error($tmp_qry));
while ( $row_result = mysql_fetch_array($tmp_result) ) {
	xdebug("row_result['trad_reference_number']",$row_result['trad_reference_number']);
	
  $qry_update = "update rep_comm_rr_trades set trad_advisor_code = '".$arr_acct_adv[strtoupper(trim($row_result['trad_account_number']))]."' 
	               where trad_reference_number = '".$row_result['trad_reference_number']."'";
	xdebug("qry_update",$qry_update);
	$tmp_result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));	
  $qry_update = "update mry_comm_rr_trades set trad_advisor_code = '".$arr_acct_adv[strtoupper(trim($row_result['trad_account_number']))]."' 
	               where trad_reference_number = '".$row_result['trad_reference_number']."'";
	xdebug("qry_update",$qry_update);
	$tmp_result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));	
}



?>