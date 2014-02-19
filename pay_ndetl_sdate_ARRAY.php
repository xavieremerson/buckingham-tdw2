<?
include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php');

include('pay_ndetl_sdate_functions.php');

include('pay_ndetl_sdate_rolling_mos.php');

$master_arr_reps = array();

$qry_trades = "SELECT trad_advisor_code, trad_rr, max( trad_advisor_name ) AS clnt_name, sum( trad_commission ) AS clnt_comm
								FROM mry_comm_rr_trades
								WHERE trad_settle_date
								BETWEEN '2008-01-02'
								AND '2008-01-25'
								AND trad_is_cancelled =0
								GROUP BY trad_advisor_code, trad_rr
								ORDER BY trad_advisor_code";
$result_trades = 	mysql_query($qry_trades) or die (tdw_mysql_error($qry_trades));
while ($row_trades = mysql_fetch_array($result_trades) ) 
{
		$arr_1_trades[$row_trades['trad_advisor_code']] = $row_trades['trad_rr']."^".$row_trades['clnt_name']."^".$row_trades['clnt_comm'];	
		$master_arr_reps[$row_trades['trad_rr']] = $row_trades['trad_rr'];
}							
show_array($arr_1_trades);
//==============================================================================================================================================

$qry_checks = "SELECT chek_advisor, sum( chek_amount ) AS chek_amount, sum( chek_amount ) AS for_sum_chek_amount
								FROM chk_chek_payments_etc
								WHERE chek_isactive =1
								AND chek_date
								BETWEEN '2008-01-01'
								AND '2008-01-31'
								GROUP BY chek_advisor
								ORDER BY chek_advisor";
$result_checks = 	mysql_query($qry_checks) or die (tdw_mysql_error($qry_checks));
while ($row_checks = mysql_fetch_array($result_checks) ) 
{
	$arr_checks[$row_checks["chek_advisor"]] = $row_checks["for_sum_chek_amount"];
	//now get the rep info for the advisor
	$query_rr_initials = "SELECT clnt_rr1, clnt_rr2
												FROM int_clnt_clients 
												WHERE clnt_code = '".$row_checks["chek_advisor"]."'";
	$result_rr_initials = mysql_query($query_rr_initials) or die (tdw_mysql_error($query_rr_initials));
	while($row_rr_initials = mysql_fetch_array($result_rr_initials)) {
		$val_rr1 = trim($row_rr_initials["clnt_rr1"]);
		$val_rr2 = trim($row_rr_initials["clnt_rr2"]);
		if ($val_rr1 != '' AND $val_rr2 == '') {
			 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Sole Client<br>";
			 //get rr_num for the sole account							  
			 $arr_checks_rr[$row_checks["chek_advisor"]] = get_rr_num (get_userid_for_initials($val_rr1));
			 $master_arr_reps[get_rr_num (get_userid_for_initials($val_rr1))] = get_rr_num (get_userid_for_initials($val_rr1));
		} elseif ($val_rr1 != '' AND $val_rr2 != ''){
			 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Shared Client<br>";
			 $arr_checks_rr[$row_checks["chek_advisor"]] = get_shared_rr_num ($val_rr1, $val_rr2);
			 $master_arr_reps[get_shared_rr_num ($val_rr1, $val_rr2)] = get_shared_rr_num ($val_rr1, $val_rr2);
		} else {
			 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Non trading account<br>"; 
			 $arr_checks_rr[$row_checks["chek_advisor"]] = "";
		}
	}
}

		//$arr_2_checks['chek_advisor']] = $row_checks['trad_rr']."^".$row_checks['clnt_name']."^".$row_trades['clnt_comm'];	

//show_array($arr_checks);
show_array($arr_checks_rr);
//show_array($master_arr_reps);

//==============================================================================================================================================


//find distinct users in checks and trades to have pages in report
$master_arr_users = array();
foreach ($master_arr_reps as $k=>$v) {
	if       (substr($v,0,1)=='0') { //single rep, get from user table
			
			$user_id = db_single_val("SELECT ID as single_val FROM users WHERE rr_num = '".$v."'");
			$master_arr_users[$user_id] = $user_id;   
			
	} elseif (substr($v,0,1)=='2') { //get shared rep data from shared rep table

		$qry_id = "SELECT srep_user_id, srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_rrnum = '".$v."'";
		$result_id = mysql_query($qry_id) or die(tdw_mysql_error($qry_id));
		while($row_id = mysql_fetch_array($result_id)) {

			$master_arr_users[$row_id["srep_user_id"]] = $row_id["srep_user_id"];   
		
		}
			
	} elseif (substr($v,0,1)=='3') { //error condition 300 cannot exist

			$master_arr_users["ERR"] = "ERR"; 
			echo "ERROR CONDITION IN REP LIST : CHECK " . $v . "<BR>"; 
			  
	} elseif (substr($v,0,1)=='4') { //400 series rep numbers not decided yet

			$master_arr_users["ERR"] = "ERR"; 
			echo "ERROR CONDITION IN REP LIST : CHECK " . $v . "<BR>"; 
	}		  
}

//show_array($master_arr_users);
$csv_user_id = implode(",",$master_arr_users);
//echo $csv_user_id;
//exit;
//==============================================================================================================================================

//Prepare a list of users with id, name etc for excel worksheets
$master_arr_users_id_names = array();
$qry_user_list = "SELECT ID, Fullname FROM users WHERE ID in (".$csv_user_id.") order by Lastname";
$result_user_list = mysql_query($qry_user_list) or die(tdw_mysql_error($qry_user_list));
while($row_user_list = mysql_fetch_array($result_user_list)) {
	$master_arr_users_id_names[$row_user_list['ID']] = $row_user_list['Fullname']; 	
}

show_array($master_arr_users_id_names);
exit;
//==============================================================================================================================================

?>