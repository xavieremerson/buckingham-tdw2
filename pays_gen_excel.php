<link rel="stylesheet" type="text/css" href="includes/styles.css">
<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

include('pays_functions.php');

$arr_debug_info = array();


//function (this page debug)
function zdebug ($n,$v) {
	$x = 1;
	if ($x==1) {
		echo "<font color='green'>".$n . " = [".$v."]</font><br>"; 
	}
}

//================================================================================================
//$debug_rr = '033'; //'033';
//$debug_uid = get_userid_for_rr ($debug_rr);
//$debug_client = '';
//$debug_sr = '243'; //'033';
//================================================================================================
 
$arr_xl_cols = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

//get values passed to this page
//show_array($_GET);

//$percent_payout_comm = 19;
//$sel_month = "Feb^2007";

$arr_brk = explode('^',$sel_month);
$brk_month = $arr_brk[0];
$brk_year = $arr_brk[1];

$payout_multiplier = round($percent_payout_comm/100,2);
$payout_multiplier_shared = round($percent_payout_comm/200,3);
$str_label_payout_rate = round($percent_payout_comm,1)."% / ".round($percent_payout_comm/2,1)."%";
//zdebug("str_label_payout_rate",$str_label_payout_rate);

xdebug("Selected Period",$arr_brk[0] . " " .$arr_brk[1]);

//initiate page load time routine
$time=getmicrotime(); 

//Create array of special payouts
$qry_sp_payout = "SELECT a.clnt_code, b.clnt_special_payout_rate
									FROM int_clnt_clients a, int_clnt_payout_rate b
									WHERE a.clnt_auto_id = b.clnt_auto_id
									AND b.clnt_default_payout !=1";
$result_sp_payout = mysql_query($qry_sp_payout) or die (tdw_mysql_error($qry_sp_payout));
$arr_clients = array();
while ( $row_sp_payout = mysql_fetch_array($result_sp_payout) ) 
{
	$arr_sp_payout[$row_sp_payout["clnt_code"]] = $row_sp_payout["clnt_special_payout_rate"]; 
	$arr_sp_payout_clnt[] = $row_sp_payout["clnt_code"];
}

//show_array($arr_sp_payout);

//Create array of cutoff exceptions
$qry_cutoff_exceptions = "SELECT a.clnt_code, b.clnt_default_n_months
													FROM int_clnt_clients a, int_clnt_payout_rate b
													WHERE a.clnt_auto_id = b.clnt_auto_id
													AND b.clnt_default_n_months !=1";
$result_cutoff_exceptions = mysql_query($qry_cutoff_exceptions) or die (tdw_mysql_error($qry_cutoff_exceptions));
$arr_clients_cutoff_exceptions = array();
while ( $row_cutoff_exceptions = mysql_fetch_array($result_cutoff_exceptions) ) 
{
	$arr_clients_cutoff_exceptions[] = $row_cutoff_exceptions["clnt_code"];
}
//show_array($arr_clients_cutoff_exceptions);

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"]; 
}


////
//Get dates for the selected brokerage month
$arr_brk_dates = get_commission_month_dates($brk_month,$brk_year);
$brk_start_date = $arr_brk_dates[0];
$brk_end_date = $arr_brk_dates[1];
xdebug("Process initiated at ",date('m/d/Y H:i:s a'));
xdebug("Start Date",$brk_start_date);
xdebug("End Date",$brk_end_date);


//??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
//This section caused serious problems. Has to be business day forward and not the following
/*
$qry = "select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date >='".$brk_start_date."' and trad_is_cancelled = 0";
$brk_start_settle_date = db_single_val($qry);
$qry = "select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$brk_end_date."' and trad_is_cancelled = 0";
$brk_end_settle_date = db_single_val($qry);
*/
$brk_start_settle_date = business_day_forward(strtotime($brk_start_date), 3);
$brk_end_settle_date = business_day_forward(strtotime($brk_end_date), 3);

xdebug("Settle Dates", $brk_start_settle_date ." to " .$brk_end_settle_date);
//??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????

//+ + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + +
//BASIC ERROR CHEKING PERFORMED HERE. MODULE WILL NOT RUN WITHOUT THIS PHASE RUNNING WITHOUT ERRORS
include('pays_error_checks.php');
//+ + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + +

//####################################################################################################################
//####################################################################################################################
//Based on the Date Selection, get the rolling 12 months start and end date.
$back_brk_year = $brk_year-1;
//zdebug("Previous Year", $back_brk_year);
$arr_back_brk_dates = get_commission_month_dates($brk_month,$back_brk_year);

$back_brk_start_date = $arr_back_brk_dates[0];
$back_brk_end_date = $arr_back_brk_dates[1];

$back_brk_start_settle_date = db_single_val("select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$back_brk_start_date."' and trad_is_cancelled = 0");
$back_brk_end_settle_date = db_single_val("select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$back_brk_end_date."' and trad_is_cancelled = 0");

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Get dates for the selected calendar month
$arr_cal_dates  = get_calendar_month_dates($brk_month,$brk_year);
$cal_start_date = $arr_cal_dates[0];
$cal_end_date   = $arr_cal_dates[1];

////
function local_str_dates($check_basis, $cal_start_date, $cal_end_date, $brk_start_date, $brk_end_date) {
//xdebug("check_basis",$check_basis);
//xdebug("cal_start_date",$cal_start_date);
//xdebug("cal_end_date",$cal_end_date);
//xdebug("brk_start_date",$brk_start_date);
//xdebug("brk_end_date",$brk_end_date);

 //global $check_basis, $cal_start_date, $cal_end_date, $brk_start_date, $brk_end_date;
 	if ($check_basis == "B") {
	  return " '" . $brk_start_date . "' AND '" . $brk_end_date . "' ";
	} elseif ($check_basis == "C") {
	  return " '" . $cal_start_date . "' AND '" . $cal_end_date . "' ";
	} else {
		echo "ERROR: CHECK BASIS NOT SELECTED!";
	}
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//xdebug("Start 12 Settle Date",$back_brk_start_settle_date);
//xdebug("End 12 Settle Date",$back_brk_end_settle_date);

//zdebug("back_brk_start_date",$back_brk_start_date);
//zdebug("back_brk_end_date",$back_brk_end_date); 
//This is the date to work with, trades should be after this date.


//Get rolling 12 month data for all clients
$qry_rolling12 = "SELECT 
										trad_rr,
										trad_advisor_code,
										sum(trad_commission) as sum_trad_commission,
										max(trad_settle_date) as trad_settle_date
									FROM mry_comm_rr_trades 
									WHERE trad_is_cancelled = 0 
									AND trad_settle_date > '".$back_brk_end_settle_date."' AND trad_settle_date <='".$brk_end_settle_date."'
									GROUP BY trad_rr, trad_advisor_code 
									ORDER BY trad_advisor_code, trad_settle_date";
//xdebug("qry_rolling12",$qry_rolling12);
$result_rolling12 = mysql_query($qry_rolling12) or die (tdw_mysql_error($qry_rolling12));
$arr_nest_client_list = array();
while($row_rolling12 = mysql_fetch_array($result_rolling12)) {
			$arr_rcc[] = $row_rolling12["trad_rr"]."^".$row_rolling12["trad_advisor_code"]."^".$row_rolling12["sum_trad_commission"]."^".$row_rolling12["trad_settle_date"];
			$arr_nest_client_list[] = $row_rolling12["trad_advisor_code"];
}

//show_array($arr_rcc);

/*
Improving this with the following, if there more than one instances of the client in the array, assimilate the multiple
records to a single record with the sum of dollars and the single record woill be theone with the max of the dates.
*/
//Assimmilating  //NO CHECKS UP UNTIL NOW.
$arr_combined = array();
$i = 0;
foreach ($arr_rcc as $key=>$value) {
	//echo $value."<br>";
	
	//+++++++++++++++++++++++++++++++++++++++++++++++
	$tmp_store = explode("^",$value);
	$new_amount = 0;
	if ($tmp_store[1] == $hold_clnt_to_combine) {
	  //xdebug ("DUPES",$tmp_store[1]);	
		//echo "[".$i."][".$hold_clnt_to_combine."] / [".$hold_amnt_to_combine."]"."<br>";
		//echo $arr_combined[$i-1]."<br>";
		$arr_combined[$i-1] = $tmp_store[0]."^".$tmp_store[1]."^".($hold_amnt_to_combine + $tmp_store[2]);
		//echo $arr_combined[$i-1]."<br>";

		$hold_clnt_to_combine = $tmp_store[1];
		$hold_amnt_to_combine = ($hold_amnt_to_combine + $tmp_store[2]);

	} else {
	  //xdebug ("NO DUPES",$tmp_store[1]);	
			$arr_combined[$i] = $tmp_store[0]."^".$tmp_store[1]."^".$tmp_store[2];

		$hold_clnt_to_combine = $tmp_store[1];
		$hold_amnt_to_combine = $tmp_store[2];
   
	  $i = $i+1;
	}
	//+++++++++++++++++++++++++++++++++++++++++++++++
		
	//$i = $i+1;
}

//show_array($arr_combined);
//exit;

// 
//OUTSTANDING TODO THE  DATES BELOW SHOULD ME CALENDAR BASED NOT BROKERAGE BASED, FOR NOW, LETS SEE
//getting checks from the prior year period and assimiliating into the array created above.
$qry_rolling12_checks = "SELECT 
														chek_advisor,
														sum(chek_amount) as chek_amount,
														max(chek_date) as chek_date
													FROM chk_chek_payments_etc 
													WHERE chek_date > '".$back_brk_end_date."' AND chek_date <='".$brk_end_date."' 
													AND chek_isactive = 1 
													GROUP BY chek_advisor 
													ORDER BY chek_advisor";
//xdebug("qry_rolling12_checks",$qry_rolling12_checks);
$result_rolling12_checks = mysql_query($qry_rolling12_checks) or die (tdw_mysql_error($qry_rolling12_checks));
$arr_rolling12_checks = array();
$arr_rolling12_checks_date = array();
while($row_rolling12_checks = mysql_fetch_array($result_rolling12_checks)) {
			$arr_rolling12_checks[$row_rolling12_checks["chek_advisor"]] = $row_rolling12_checks["chek_amount"];
			$arr_rolling12_checks_date[$row_rolling12_checks["chek_advisor"]] = $row_rolling12_checks["chek_date"];
}

//show_array($arr_rolling12_checks);
//exit;

//Reassigning the array
$arr_rcc = $arr_combined;
//show_array($arr_rcc);
//exit;

//NOW ADD CHECKS
$arr_new_combined = array();
$arr_check_processed_list   = array();
foreach ($arr_rcc as $numindex=>$valstring) {

		 $tmp_store = explode("^",$valstring);
		 $newamount = $tmp_store[2];
		 foreach ($arr_rolling12_checks as $clnt=>$amt) {
				if ($tmp_store[1] == $clnt) {
				  //xdebug("clnt",$clnt."/".$tmp_store[2]."/".$amt);
					$newamount = $newamount + $amt;
	        $arr_check_processed_list[] = $tmp_store[1];
				}		 		
		 }
		
	 $arr_new_combined[] = $tmp_store[0]."^".$tmp_store[1]."^".$newamount;
}

//show_array($arr_new_combined);
//show_array($arr_check_processed_list);
//exit;

//now add checks only client to the combined list
$arr_delta = array();
foreach ($arr_rolling12_checks as $clnt=>$amt) {
  if (!in_array($clnt, $arr_check_processed_list)) {
		//get rr_num for client
		$qry = "select trim(clnt_rr1) as rr1, trim(clnt_rr2) as rr2 from int_clnt_clients where clnt_code = '".$clnt."'";
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		while($row = mysql_fetch_array($result)) {
			$rr1 = $row["rr1"];
			$rr2 = $row["rr2"];	
			if ($rr1 != '' OR $rr2 != '') {
				if ($rr2 == '') {
					//xdebug("clnt/rr1/rr2", $clnt."/".$rr1."/".$rr2);
					$tmp_rr_num = get_rr_num (get_userid_for_initials ($rr1));
					//xdebug("tmp_rr_num",$tmp_rr_num);	
					$arr_delta[] = $tmp_rr_num."^".$clnt."^".$amt;
				} else {
					//xdebug("clnt/rr1/rr2", $clnt."/".$rr1."/".$rr2);
					$tmp_rr_num = get_shared_rr_num ($rr1, $rr2);
					//xdebug("tmp_rr_num",$tmp_rr_num);				
					$arr_delta[] = $tmp_rr_num."^".$clnt."^".$amt;
				}
			}
		}
	}
}

//show_array($arr_delta);	

//now reassign the $arr_rcc which is below
$arr_rcc = array();
$arr_rcc = $arr_new_combined;

//now merge the arrays
$arr_merged = array_merge($arr_rcc, $arr_delta);
//show_array($arr_rcc);	
//exit;
//now reassign the $arr_rcc which is below
$arr_rcc = array();
$arr_rcc = $arr_merged;

//show_array($arr_rcc);
//xdebug("ADAG/030",$arr_nest_client["ADAG"]["030"]);	
//###################################################################################################
//####################################################################################################################
//####################################################################################################################


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$master_arr_reps = array();
$master_arr_commissions = array();
$master_arr_commissions_clients = array();
$qry_trades = "SELECT trad_advisor_code, trad_rr, max( trad_advisor_name ) AS clnt_name, sum( trad_commission ) AS clnt_comm
								FROM mry_comm_rr_trades
								WHERE trad_settle_date
								BETWEEN '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
								AND trad_is_cancelled =0
								GROUP BY trad_advisor_code, trad_rr
								ORDER BY trad_advisor_code";
//xdebug("qry_trades",$qry_trades);								
$result_trades = 	mysql_query($qry_trades) or die (tdw_mysql_error($qry_trades));
while ($row_trades = mysql_fetch_array($result_trades) ) 
{
		$master_arr_commissions[$row_trades['trad_advisor_code']] = $row_trades['trad_rr']."^".$row_trades['clnt_name']."^".$row_trades['clnt_comm'];	
		$master_arr_reps[$row_trades['trad_rr']] = $row_trades['trad_rr'];
		$master_arr_commissions_clients[$row_trades['trad_advisor_code']] = $row_trades['trad_advisor_code'];
}							
//show_array($master_arr_commissions);
//==============================================================================================================================================

$qry_checks = "SELECT chek_advisor, sum( chek_amount ) AS chek_amount, sum( chek_amount ) AS for_sum_chek_amount
								FROM chk_chek_payments_etc
								WHERE chek_isactive =1
								AND chek_date
								BETWEEN  ".local_str_dates($check_basis, $cal_start_date, $cal_end_date, $brk_start_date, $brk_end_date)."
								GROUP BY chek_advisor
								ORDER BY chek_advisor";
//xdebug("qry_checks",$qry_checks);
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
//show_array($arr_checks_rr);
//show_array($master_arr_reps);

//==============================================================================================================================================
//Combine the two arrays to get totals of checks and commissions
foreach($master_arr_commissions as $k=>$v) {
  //$master_arr_commissions[$row_trades['trad_advisor_code']] = $row_trades['trad_rr']."^".$row_trades['clnt_name']."^".$row_trades['clnt_comm'];	
	$arr_tmp = explode("^",$v);
	$val_sum = $arr_tmp[2];
	$chk_val = 0;
	foreach($arr_checks as $kx=>$vx) {
		if($k == $kx) {
		  $val_sum = $vx + $val_sum;
			$chk_val = $vx;
		}
	}
	$master_arr_commission_v1[$k] = $arr_tmp[0]."^".$arr_tmp[1]."^".$arr_tmp[2]."^".$chk_val."^".$val_sum;	
}
//show_array($master_arr_commission_v1);
//==============================================================================================================================================
//Assimmilate the checks only data to the main array
foreach($arr_checks as $k=>$v) {
  if (!in_array($k, $master_arr_commissions_clients)) {
		if ($arr_checks_rr[$k] != '') {
			//echo "not present ".$k."<br>";
	    $master_arr_commission_v1[$k] = $arr_checks_rr[$k]."^".$arr_clients[$k]."^".'0'."^".$v."^".$v;				
		}
	}
}
//show_array($master_arr_commission_v1);
//==============================================================================================================================================
//Apply Rolling 12 months where applicable
foreach($master_arr_commission_v1 as $k=>$v) {
	$arr_tmp = explode("^",$v);
	$rolling_n_mos = get_rcc ($arr_rcc, $arr_tmp[0], $k);
  if ($rolling_n_mos >= 15000 or in_array($k,$arr_clients_cutoff_exceptions)) {
		$master_arr_commission_v2[$k] = $arr_tmp[0]."^".$arr_tmp[1]."^".$arr_tmp[2]."^".$arr_tmp[3]."^".$arr_tmp[4]."^".$rolling_n_mos."^"."0";	
	} else {
		$master_arr_commission_v2[$k] = $arr_tmp[0]."^".$arr_tmp[1]."^".$arr_tmp[2]."^".$arr_tmp[3]."^".$arr_tmp[4]."^".$rolling_n_mos."^"."-1";	
	}
}
//show_array($master_arr_commission_v2);
//==============================================================================================================================================
//Apply Special Rates if applicable
/*
foreach($master_arr_commission_v2 as $k=>$v) {
	$arr_tmp = explode("^",$v);
	if (in_array($k,$arr_sp_payout_clnt)) {
		$special_rate_label = sp_payout_rate($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout)."%";
		$special_rate = sp_payout_rate($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout)/100;
	  $master_arr_commission_v3[$k] = $arr_tmp[0]."^".$arr_tmp[1]."^".$arr_tmp[2]."^".$arr_tmp[3]."^".$arr_tmp[4]."^".$arr_tmp[5]."^".$arr_tmp[6]."^".$special_rate_label."^".$special_rate;
	} else {
			if (substr($arr_tmp[0],0,1) == '0') {
	  		$master_arr_commission_v3[$k] = $arr_tmp[0]."^".$arr_tmp[1]."^".$arr_tmp[2]."^".$arr_tmp[3]."^".$arr_tmp[4]."^".$arr_tmp[5]."^".$arr_tmp[6]."^".''."^".$payout_multiplier;
			} else {
	  		$master_arr_commission_v3[$k] = $arr_tmp[0]."^".$arr_tmp[1]."^".$arr_tmp[2]."^".$arr_tmp[3]."^".$arr_tmp[4]."^".$arr_tmp[5]."^".$arr_tmp[6]."^".''."^".$payout_multiplier_shared;
			}
	}
}
show_array($master_arr_commission_v3);
*/
//==============================================================================================================================================
//find distinct users in checks and trades to have pages in report
$master_arr_users = array();
foreach ($master_arr_reps as $k=>$v) {
	if       (substr($v,0,1)=='0') { //single rep, get from user table
			
			$user_id = db_single_val("SELECT max(ID) as single_val FROM users WHERE rr_num = '".$v."'");
			if ($user_id != '') {
				$master_arr_users[$user_id] = $user_id;   
			}
			
	} elseif (substr($v,0,1)=='2') { //get shared rep data from shared rep table

		$qry_id = "SELECT srep_user_id, srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_rrnum = '".$v."'";
		$result_id = mysql_query($qry_id) or die(tdw_mysql_error($qry_id));
		while($row_id = mysql_fetch_array($result_id)) {

			if ($row_id["srep_user_id"] != '') {
				$master_arr_users[$row_id["srep_user_id"]] = $row_id["srep_user_id"];   
			}
		}
			
	} elseif (substr($v,0,1)=='3') { //error condition 300 cannot exist

			//$master_arr_users["ERR"] = "ERR"; 
			echo "<!--ERROR CONDITION IN REP LIST : CHECK " . $v . "--><BR>"; 
			  
	} elseif (substr($v,0,1)=='4') { //400 series rep numbers not decided yet

			//$master_arr_users["ERR"] = "ERR"; 
			//echo "ERROR CONDITION IN REP LIST : CHECK " . $v . "<BR>"; 
			
	}	else {
	//nothing
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

//show_array($master_arr_users_id_names);
//exit;


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//show_array($master_arr_commission_v2);

//GET UNIQUE REPS BOTH PRIMARY AND SHARED
$arr_unique_reps = array();
foreach ($master_arr_commission_v2 as $k=>$v) {
	$tmp_array = explode("^",$v);
	$arr_unique_reps[$tmp_array[0]] = $tmp_array[0];
}

//show_array($arr_unique_reps);
//exit;

//REPROCESS THE ARRAY $master_arr_commission_v2
//	rep				rep				userid			shared		name								comm						checks			std					str																special			rolling     ???      shared
//	067 = [		067		^		281			^		281		^		Michael Nolan		^		51078.00		^		7704		^		1951.8		^	 20% 20% 20% 20% 20% 20% 20%	^		9804.6	^		0		     ^		    ^		0	  ]
  
$master_arr_commission_v3 = array();
foreach($arr_unique_reps as $rep_k=>$rep_v){
	//initalizing fixed value vars
	$var_rep = $rep_v;
	$var_user_id = "";
	$var_users = "";
	$var_name = "";
	
	$var_arr_val_0 = $rep_v;
	
	$rep_userid = get_userid_for_rr ($rep_v);
	$var_arr_val_1 = $rep_userid;

	if ($rep_userid == "") {
		$var_arr_val_2 = get_user_id_for_shared_reps($rep_v);
	} else {
		$var_arr_val_2 = $process_userid;
	}

	$display_name = get_repname_by_rr_num($rep_v);	
	$var_arr_val_3 = $display_name;
	//xdebug("var_arr_val_3",$var_arr_val_3);
	
	
	$var_arr_val_4 = "";
	$var_arr_val_5 = "";
	$var_arr_val_6 = "";
	$var_arr_val_7 = "";
	$var_arr_val_8 = "";
	$var_arr_val_9 = "";
	$var_arr_val_10 = "";
	$var_arr_val_11 = "";
	
	foreach ($master_arr_commission_v2 as $k=>$v) {
	
					if ($k == 'JMPA') {	$arr_debug_info[$k] = $v;		}															

  //computing cumulative value vars
		$tmp_array = explode("^",$v);
		$temp_rep = $tmp_array[0];
		if ($rep_v == $temp_rep AND substr($rep_v,0,1) == '0' ) { //PRIMARY REPS
			//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			$var_arr_val_4 = $var_arr_val_4 + $tmp_array[2];
			$var_arr_val_5 = $var_arr_val_5 + $tmp_array[3];
			
			//-----------------------------------------------------------------------------
			//process includes special payout rates
			$tmp_val_for_rolling = 0;
			if (in_array($k,$arr_sp_payout_clnt)) {
				$special_rate = sp_payout_rate($k, $rep_userid, $arr_sp_payout)/100;
				$var_arr_val_8 = $var_arr_val_8 + (  ($tmp_array[2] + $tmp_array[3])*$special_rate  );
				$tmp_val_for_rolling = (  ($tmp_array[2] + $tmp_array[3])*$special_rate  );
			} else {
				$var_arr_val_6 = $var_arr_val_6 + (  ($tmp_array[2] + $tmp_array[3])*$payout_multiplier  );
				$tmp_val_for_rolling = (  ($tmp_array[2] + $tmp_array[3])*$payout_multiplier  );
			}
			//-----------------------------------------------------------------------------
			//get rolling 12 months data
			$rolling_total = $tmp_array[5];
			if ($rolling_total >= 15000 or in_array($k,$arr_clients_cutoff_exceptions)) {
				$var_arr_val_9 = $var_arr_val_9 + 0;
			} else {
			$cond_format = $format_currency_1;
				$var_arr_val_9 = $var_arr_val_9 + ($tmp_val_for_rolling*(-1));
			}
			//-----------------------------------------------------------------------------
			//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		} elseif ($rep_v == $temp_rep AND substr($rep_v,0,1) != '0' ) {
			//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			$var_arr_val_4 = $var_arr_val_4 + $tmp_array[2];
			$var_arr_val_5 = $var_arr_val_5 + $tmp_array[3];

			//	rep				rep				userid			shared		name								comm						checks			std					str																special			rolling     ???      shared
			//	067 = [		067		^		281			^		281		^		Michael Nolan		^		51078.00		^		7704		^		1951.8		^	 20% 20% 20% 20% 20% 20% 20%	^		9804.6	^		0		     ^		    ^		0	  ]

			//-----------------------------------------------------------------------------
			//process includes special payout rates
			//since more than one reps involved get individual values and then sum them up.
			if (in_array($k,$arr_sp_payout_clnt)) {
				$var_holdsum_val = "";
				//get the sum of the special payout rates for the client
				//xdebug("This applies to ".$k,$var_arr_val_2);
			
				$arr_rep_id_for_rate = explode("#",$var_arr_val_2);
				
				foreach($arr_rep_id_for_rate as $k_uid=>$v_uid) {
					$arr_uid_val = explode("|",$v_uid);
					$special_rate = sp_payout_rate($k, $arr_uid_val[0], $arr_sp_payout)/100;
					$var_holdsum_val = $var_holdsum_val + (  ($tmp_array[2] + $tmp_array[3])*$special_rate  );
				}
				$var_arr_val_8 = $var_arr_val_8 + $var_holdsum_val;
			} else {
				$var_arr_val_6 = $var_arr_val_6 + (  ($tmp_array[2] + $tmp_array[3])*$payout_multiplier  );
				if ($rep_v == '210') {	$arr_debug_info['var_arr_val_6'] = $var_arr_val_6;		}															
			}
			
			//-----------------------------------------------------------------------------
			//get rolling 12 months data
			$rolling_total = $tmp_array[5];
			
			if ($rep_v == '210') {	$arr_debug_info['rolling_total'] = $rolling_total;		}															
			
			if ($rolling_total >= 15000 or in_array($k,$arr_clients_cutoff_exceptions)) {
				$var_arr_val_9 = $var_arr_val_9 + 0;
				if ($rep_v == '210') {	$arr_debug_info['var_arr_val_9'] = $var_arr_val_9;		}															
			} else {
			$cond_format = $format_currency_1;
				//$var_arr_val_9 = $var_arr_val_9 + ($tmp_val_for_rolling*(-1));
				$var_arr_val_9 = $var_arr_val_9 + ((($tmp_array[2] + $tmp_array[3])*$payout_multiplier)*(-1));
				if ($rep_v == '210') {	$arr_debug_info['var_arr_val_9'] = $var_arr_val_9;		}															
			}
			
			//-----------------------------------------------------------------------------
			//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		} else {
		//do nothing for now
		
		}
		
		//=============================================================
		//putting the values in the new array
		$master_arr_commission_v3[$rep_v] = $var_arr_val_0."^".$var_arr_val_1."^".$var_arr_val_2."^".$var_arr_val_3."^".$var_arr_val_4."^".$var_arr_val_5."^".
		                              $var_arr_val_6."^".$var_arr_val_7."^".$var_arr_val_8."^".$var_arr_val_9."^".$var_arr_val_10."^".$var_arr_val_11;
																	
		if ($rep_v == '210') {
		$arr_debug_info[$rep_v] = $var_arr_val_0."^".$var_arr_val_1."^".$var_arr_val_2."^".$var_arr_val_3."^".$var_arr_val_4."^".$var_arr_val_5."^".
		                              $var_arr_val_6."^".$var_arr_val_7."^".$var_arr_val_8."^".$var_arr_val_9."^".$var_arr_val_10."^".$var_arr_val_11;
		}															
		//=============================================================		
	}	
}

//show_array($master_arr_commission_v3);
//xdebug("master_arr_commission_v3['035']",$master_arr_commission_v3['035']);
//exit;

$arr_rep_shared_number = array();
foreach($arr_unique_reps as $rep_k=>$rep_v){
	
		foreach ($master_arr_commission_v2 as $k=>$v) {
				//computing cumulative value vars
				$tmp_array = explode("^",$v);
				$temp_rep = $tmp_array[0];
				if ($rep_v == $temp_rep AND substr($rep_v,0,1) != '0' ) {

						$var_arr_val_2 = get_user_id_for_shared_reps($rep_v);

						//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
						//-----------------------------------------------------------------------------
						//process includes special payout rates
						//since more than one reps involved get individual values and then sum them up.

						//get rolling 12 months data and keep the multiplier handy for reapplication to master array 3
						$rolling_total = $tmp_array[5];
						$val_tmp_mult = 0;
						if ($rolling_total >= 15000 or in_array($k,$arr_clients_cutoff_exceptions)) {
							$val_tmp_mult = 1;
						} else {
							$val_tmp_mult = 0;
						}

						if (in_array($k,$arr_sp_payout_clnt)) {
							
								$arr_rep_id_for_rate = explode("#",$var_arr_val_2);
								
								foreach($arr_rep_id_for_rate as $k_uid=>$v_uid) {
										$arr_uid_val = explode("|",$v_uid);
										$special_rate = sp_payout_rate($k, $arr_uid_val[0], $arr_sp_payout)/100;
										//xdebug("special_rate",$special_rate);
										//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
										$arr_rep_shared_number[$arr_uid_val[1]] = $arr_rep_shared_number[$arr_uid_val[1]] + (  ($tmp_array[2] + $tmp_array[3])*$special_rate*$val_tmp_mult  );					
										//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
								}
						} else {
				
								$arr_rep_id_for_std_rate = explode("#",$var_arr_val_2);
								
								foreach($arr_rep_id_for_std_rate as $k_uid=>$v_uid) {
										$arr_uid_val = explode("|",$v_uid);
										//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
										$arr_rep_shared_number[$arr_uid_val[1]] = $arr_rep_shared_number[$arr_uid_val[1]] + (  ($tmp_array[2] + $tmp_array[3])*$payout_multiplier*(0.5)* $val_tmp_mult ) ;					
										//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
								}
						}
				//-----------------------------------------------------------------------------
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
				} else {
				//do nothing for now
				}
		}	
}
//show_array($arr_rep_shared_number);
//exit;
//show_array($master_arr_commission_v3);

//assimilate the data above to the master array
$master_arr_commission_v4 = array();
$prefinal_rep_list = array();
foreach($master_arr_commission_v3 as $fk=>$fv) {
	$prefinal_rep_list[$fk] = $fk;
	$tmp_array = array();
	//xdebug("fv",$fv);
	$tmp_array = explode("^",$fv);
	//show_array($tmp_array);
	$str_val_concat = $tmp_array[0]."^".$tmp_array[1]."^".$tmp_array[2]."^".$tmp_array[3]."^".$tmp_array[4]."^".$tmp_array[5]."^".$tmp_array[6]."^".$tmp_array[7]."^".$tmp_array[8]."^".$tmp_array[9]."^".$tmp_array[10]."^".($tmp_array[11] + $arr_rep_shared_number[$fk]);
	//xdebug("str_val_concat",$str_val_concat);
	$master_arr_commission_v4[$fk] = $str_val_concat;
}

//assimilate "shared only" data above to the master array
foreach($arr_rep_shared_number as $k=>$v) {
  if(!in_array($k,$prefinal_rep_list)) {
		$str_val_concat = $k."^".get_userid_for_rr ($k)."^".""."^".""."^".""."^".""."^".""."^".""."^".""."^".""."^".""."^".$v;
	  $master_arr_commission_v4[$k] = $str_val_concat;
	}
}
//show_array($master_arr_commission_v4);
//exit;

//==============================================================================================================================================
//>>>>>>>>>>>>> CODE REMOVED FROM HERE
//We give the path to our file here
//generate a random filename

$xlfilename = date('Y-m-d_h.i.s.a')."__".substr(md5(rand(1000000000,9999999999)),0,5).".xls";
//$xlfilename = "test.xls";
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

	//FORMATTING IN THE FOLLOWING FILE
	include('pays_gen_excel_format.php');

	$wks =& $wkb->addWorksheet("Payout Summary");
	$wks->setLandscape ();
	$wks->setMarginLeft(0.4);
	$wks->setMarginRight(0.4);
	$wks->setMarginTop(0.5);
	$wks->setMarginBottom(0.4);
	$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);
	
	$wks->setPaper(5);

	//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEET
	include('pays_excel_header.php');

			$arr_master_new = $master_arr_commission_v4;		
			//show_array($arr_master_new);
			//exit;
			include('pays_gen_excel_inc_1.php');
?>