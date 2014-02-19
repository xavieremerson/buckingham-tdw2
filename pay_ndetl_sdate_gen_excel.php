<?
//echo "<font color='blue'>Module in <b>&beta;</b> Stage!</font><br>";

include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';


//show_array($_GET);
//exit;

/*
if ($tdw_user != 79) {
	echo "Files are currently being updated... Timeline for access noto currently available.";
	exit;
}
*/

include('pay_ndetl_sdate_functions.php');
include('pay_ndetl_sdate_queries.php');

//initiate page load time routine
$time=getmicrotime(); 

//function (this page debug)
function zdebug ($n,$v) {
	$x = 1;
	if ($x==1) {
		echo "<font color='green'>".$n . " = [".$v."]</font><br>"; 
	}
}

$arr_xl_cols = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$payout_multiplier        = round($percent_payout_comm/100,2);
$payout_multiplier_shared = round($percent_payout_comm/200,3);
$str_label_payout_rate    = round($percent_payout_comm,1)."% / ".round($percent_payout_comm/2,1)."%";

$arr_brk   = explode('^',$sel_month);
$brk_month = $arr_brk[0];
$brk_year  = $arr_brk[1];
xdebug("Selected Period",$arr_brk[0] . " " .$arr_brk[1]);

////
//Get dates for the selected brokerage month
$arr_brk_dates  = get_commission_month_dates($brk_month,$brk_year);
$brk_start_date = $arr_brk_dates[0];
$brk_end_date   = $arr_brk_dates[1];

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

xdebug("Process initiated at ",date('m/d/Y H:i:s a'));
xdebug("Brokerage Month Dates",$brk_start_date ." to " .$brk_end_date);
xdebug("Calendar Month Dates", $cal_start_date ." to " .$cal_end_date);

//??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
//This section caused serious problems. Has to be business day forward and not the following
/*
$qry = "select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date >='".$brk_start_date."' and trad_is_cancelled = 0";
$brk_start_settle_date = db_single_val($qry);
$qry = "select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$brk_end_date."' and trad_is_cancelled = 0";
$brk_end_settle_date = db_single_val($qry);
*/
$brk_start_settle_date = business_day_forward(strtotime($brk_start_date), 3); //business_day_forward MUST work with business days only. Changing function
$brk_end_settle_date = business_day_forward(strtotime($brk_end_date), 3);

xdebug("Settle Dates", $brk_start_settle_date ." to " .$brk_end_settle_date);
//??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????

//+ + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + +
//BASIC ERROR CHEKING PERFORMED HERE. MODULE WILL NOT RUN WITHOUT THIS PHASE RUNNING WITHOUT ERRORS
include('pay_ndetl_sdate_error_checks.php');
//+ + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + +

//= = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = =
//ROLLING MONTHS DATA
include('pay_ndetl_sdate_rolling_mos.php');
//= = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = =

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
			
	} elseif (substr($v,0,1)=='2' || substr($v,0,1)=='3') { //get shared rep data from shared rep table

		$qry_id = "SELECT srep_user_id, srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_rrnum = '".$v."'";
		$result_id = mysql_query($qry_id) or die(tdw_mysql_error($qry_id));
		while($row_id = mysql_fetch_array($result_id)) {

			if ($row_id["srep_user_id"] != '') {
				$master_arr_users[$row_id["srep_user_id"]] = $row_id["srep_user_id"];   
			}
		
		}
			
	} elseif (substr($v,0,1)=='4') { //error condition 300 cannot exist

			//$master_arr_users["ERR"] = "ERR"; 
			echo "<!--ERROR CONDITION IN REP LIST : CHECK " . $v . "--><BR>"; 
			  
	} elseif (substr($v,0,1)=='5') { //400 series rep numbers not decided yet

			//CHECK IF SOMETHING FURTHER NEEDS TO BE DONE HERE 10MAR 2008
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
//==============================================================================================================================================




//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//We give the path to our file here
//generate a random filename
$xlfilename = date('Y-m-d_h.i.s.a')."__".substr(md5(rand(1000000000,9999999999)),0,8).".xls";
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

//FORMATTING IN THE FOLLOWING FILE
include('pay_ndetl_sdate_gen_excel_format.php');

//show_array($master_arr_users_id_names);
//exit;
// + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + =
		foreach($master_arr_users_id_names as $k=>$v) {

				//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
				//get primary rep number and start to put clients
				$val_rr_num = get_rr_num ($k) ;
				//xdebug('val_rr_num',$val_rr_num);
				//now for this rr get sorted list of clients
				$arr_rel_clients = "";
				$arr_rel_clients = array();
				foreach($master_arr_commission_v2 as $ky=>$vy) {
					$arr_tmp = explode("^",$vy);
					//echo "[".$arr_tmp[0]."/".$val_rr_num."]<br>";
					if ($arr_tmp[0] == $val_rr_num) {
						$arr_rel_clients[] = $ky;
					}
				}
				sort($arr_rel_clients);
				//xdebug("name",$v);
				//show_array($arr_rel_clients);
				
				// ^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
				//Some names for REP NUMBERS
				$display_name = $v;
				// ^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
							
				$process_userid = $k;
				
				$wks =& $wkb->addWorksheet($display_name);
				//xdebug("Adding worksheet for",$display_name);
				$wks->setLandscape ();
				$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);

		
				//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEETS
				include('pay_ndetl_sdate_gen_excel_header.php');
				
				$wks->write(3, 2, "Sole");
				$wks->writeString(3, 3, ' '.$val_rr_num.' ');
				$wks->write(3, 4, $display_name,$format_data_3);

				$count_row_i = 4;
				$processed_client = array();
				
				if (count($arr_rel_clients) > 0) {
						foreach($arr_rel_clients as $kz=>$vz) {
								 foreach($master_arr_commission_v2 as $k_val=>$v_val) {
											if ($vz==$k_val) {
														$arr_process = explode("^",$v_val);
														if (trim($arr_process[1]) == '') {
															$clnt_name = $k_val; //Missing Client Name [Possibly a data error]
														}else {
															$clnt_name = $arr_process[1];
														}
														$wks->write($count_row_i, 4, $clnt_name,$format_data_1);
														$wks->writeNumber($count_row_i, 6, $arr_process[2],$format_currency_1);
														$wks->writeNumber($count_row_i, 7, $arr_process[3],$format_currency_1);
					
														//-----------------------------------------------------------------------------
														//process includes special payout rates
														if (in_array($k_val,$arr_sp_payout_clnt)) {
															$special_rate_label = sp_payout_rate($k_val, $process_userid, $arr_sp_payout)."%";
															$special_rate = sp_payout_rate($k_val, $process_userid, $arr_sp_payout)/100;
															//xdebug("special_rate",$special_rate);					
															$wks->write($count_row_i, 11, $special_rate_label,$format_currency_1);
															$wks->writeFormula($count_row_i, 8, '='.$arr_xl_cols[6].($count_row_i+1)."+".$arr_xl_cols[7].($count_row_i+1) ,$format_currency_1);
															$wks->writeFormula($count_row_i, 12, '='.$arr_xl_cols[8].($count_row_i+1)."*".$special_rate,$format_currency_1);
														} else {
															$wks->writeFormula($count_row_i, 8, '='.$arr_xl_cols[6].($count_row_i+1)."+".$arr_xl_cols[7].($count_row_i+1) ,$format_currency_1);
															$wks->writeFormula($count_row_i, 10, '='.$arr_xl_cols[8].($count_row_i+1)."*".$payout_multiplier,$format_currency_1);
														}
														//-----------------------------------------------------------------------------
														//get rolling 12 months data
														$rolling_total = $arr_process[5];
														if ($rolling_total >= 15000 or in_array($k_val,$arr_clients_cutoff_exceptions)) {
														$cond_format = $format_currency_1;
															//$wks->writeFormula($count_row_i, 15, '='.$arr_xl_cols[11].($count_row_i+1)."*1", $cond_format);
															$wks->writeFormula($count_row_i, 19, '=('.$arr_xl_cols[10].($count_row_i+1)."+".$arr_xl_cols[12].($count_row_i+1).")*1",$format_currency_1);
														} else {
														$cond_format = $format_currency_1;
															$wks->writeFormula($count_row_i, 14, '=('.$arr_xl_cols[10].($count_row_i+1)."+".$arr_xl_cols[12].($count_row_i+1).")*(-1)", $cond_format);
															$wks->writeFormula($count_row_i, 19, '=('.$arr_xl_cols[10].($count_row_i+1)."+".$arr_xl_cols[12].($count_row_i+1).")*0",$format_currency_1);
														}
														//-----------------------------------------------------------------------------
														
														$count_row_i = $count_row_i + 1;
											}
								 }
						}
						
				//begin totals
				//$wks->writeNumber($count_row_i, 6, $row_trades["for_sum_trad_commission"],$format_currency_2); CAUSED ERRORONEOUS NUMBER (INVESTIGATE)
				$wks->writeFormula($count_row_i, 6,  '=SUM('.$arr_xl_cols[6]."5".":".$arr_xl_cols[6].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 7,  '=SUM('.$arr_xl_cols[7]."5".":".$arr_xl_cols[7].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 8,  '=SUM('.$arr_xl_cols[8]."5".":".$arr_xl_cols[8].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 10, '=SUM('.$arr_xl_cols[10]."5".":".$arr_xl_cols[10].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 12, '=SUM('.$arr_xl_cols[12]."5".":".$arr_xl_cols[12].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 14, '=SUM('.$arr_xl_cols[14]."5".":".$arr_xl_cols[14].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 19, '=SUM('.$arr_xl_cols[19]."5".":".$arr_xl_cols[19].($count_row_i).")",$format_currency_2);
				
				$hold_for_grand_total = $count_row_i;
				$count_row_i = $count_row_i + 1;
				
				$wks->writeRow($count_row_i,0," ");
						
				} else {

				$wks->writeFormula($count_row_i, 6,  '=SUM('.$arr_xl_cols[6]."4".":".$arr_xl_cols[6].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 7,  '=SUM('.$arr_xl_cols[7]."4".":".$arr_xl_cols[7].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 8,  '=SUM('.$arr_xl_cols[8]."4".":".$arr_xl_cols[8].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 10, '=SUM('.$arr_xl_cols[10]."4".":".$arr_xl_cols[10].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 12, '=SUM('.$arr_xl_cols[12]."4".":".$arr_xl_cols[12].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 14, '=SUM('.$arr_xl_cols[14]."4".":".$arr_xl_cols[14].($count_row_i).")",$format_currency_2);
				$wks->writeFormula($count_row_i, 19, '=SUM('.$arr_xl_cols[19]."4".":".$arr_xl_cols[19].($count_row_i).")",$format_currency_2);
				
				$hold_for_grand_total = $count_row_i;
				$count_row_i = $count_row_i + 1;
				
				$wks->writeRow($count_row_i,0," ");
				}
				//end of clients from primary rep
				//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
							
							
				//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
				//Now get the shared reps for the primary rep
				//&& == && == && == && == && == && == && == && == && == && == && == && == && == && == && == && == && == &&
				$condition_for_shrd_totals = 0;

				$query_reps_shared = "SELECT distinct(srep_rrnum) as srep_rrnum
																FROM sls_sales_reps
																WHERE srep_isactive = 1 AND srep_user_id = '".$process_userid."' AND trim(srep_rrnum) != ''
																ORDER BY srep_rrnum";
				
				//xdebug("query_reps_shared",$query_reps_shared);
				$result_reps_shared = mysql_query($query_reps_shared) or die (tdw_mysql_error($query_reps_shared));
				$arr_val_reps_shared = "";
				$arr_val_reps_shared = array();
				while ($row_reps_shared = mysql_fetch_array($result_reps_shared) ) {
					$arr_val_reps_shared[] = $row_reps_shared["srep_rrnum"];
				}

				//if in fact that there is this array populated
				$arr_rel_shrd_clients = "";
				$arr_rel_shrd_clients = array();
				if (count($arr_val_reps_shared) > 0) {
					foreach($arr_val_reps_shared as $k_shrd=>$v_shrd) {
							foreach($master_arr_commission_v2 as $ky=>$vy) {
								$arr_tmp = explode("^",$vy);
								if ($arr_tmp[0] == $v_shrd) {
									$arr_rel_shrd_clients[] = $ky;
								}
							}
					}
				}
				
				if (count($arr_rel_shrd_clients) > 0) {
					asort($arr_rel_shrd_clients);
				
					$count_row_j = $count_row_i+1;
					$hold_count_j = $count_row_j;
					
					foreach($arr_rel_shrd_clients as $kz=>$vz) {
							 foreach($master_arr_commission_v2 as $k_val=>$v_val) {
										if ($vz==$k_val) {
													$arr_process = explode("^",$v_val);
													if (trim($arr_process[1]) == '') {
														$clnt_name = $k_val; //Missing Client Name [Possibly a data error]
													}else {
														$clnt_name = $arr_process[1];
													}
				
													$condition_for_shrd_totals = 1;
													
													$wks->write($count_row_j, 2, "Shrd");
													$wks->write($count_row_j, 3, ' '.$arr_process[0].' ');
													$wks->write($count_row_j, 4, get_repname_by_rr_num($arr_process[0]),$format_data_3);
						
													$count_row_k = $count_row_j+1;
															
													$wks->write($count_row_k, 2, " ");
													$wks->write($count_row_k, 3, ' '." ".' ');
													$wks->write($count_row_k, 4, $clnt_name,$format_data_1);
													
													//xdebug("Client/Comm.",$clnt_name."/".$arr_process[2]);
													$wks->writeNumber($count_row_k, 6, $arr_process[2],$format_currency_1);
																		
													$wks->writeNumber($count_row_k, 7, $arr_process[3],$format_currency_1);
				
													if (in_array($k_val,$arr_sp_payout_clnt)) {
														$special_rate_label = sp_payout_rate($k_val, $process_userid, $arr_sp_payout)."%";
														$special_rate = sp_payout_rate($k_val, $process_userid, $arr_sp_payout)/100;
														//xdebug("special_rate",$special_rate);					
														$wks->write($count_row_k, 11, $special_rate_label,$format_currency_1);
														//-----------------------------------------------------------------------------
														$wks->writeFormula($count_row_k, 8, '='.$arr_xl_cols[6].($count_row_k+1)."+".$arr_xl_cols[7].($count_row_k+1) ,$format_currency_1);
														$wks->writeFormula($count_row_k, 12, '='.$arr_xl_cols[8].($count_row_k+1)."*".$special_rate,$format_currency_1);
														//-----------------------------------------------------------------------------
													} else {
														//-----------------------------------------------------------------------------
														$wks->writeFormula($count_row_k, 8, '='.$arr_xl_cols[6].($count_row_k+1)."+".$arr_xl_cols[7].($count_row_k+1) ,$format_currency_1);
														$wks->writeFormula($count_row_k, 10, '='.$arr_xl_cols[8].($count_row_k+1)."*".$payout_multiplier_shared,$format_currency_1);
														//-----------------------------------------------------------------------------
													}
						
													//get rolling 12 months data
													$rolling_total = $arr_process[5];
													if ($rolling_total >= 15000 or in_array($k_val,$arr_clients_cutoff_exceptions)) {
													$cond_format = $format_currency_1;
														//$wks->writeFormula($count_row_k, 14, '='.$arr_xl_cols[11].($count_row_k+1)."*1", $cond_format);
														$wks->writeFormula($count_row_k, 19, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*1",$format_currency_1);
													} else {
													$cond_format = $format_currency_1;
														$wks->writeFormula($count_row_k, 14, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*(-1)", $cond_format);
														$wks->writeFormula($count_row_k, 19, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*0",$format_currency_1);
													}
																		
													//****
													$count_row_k = $count_row_k + 1;
	
													$count_row_j = $count_row_k;
													$count_row_j = $count_row_j + 1;
				
										}

							 }

						//Now totals for the shared rep data if any
						//put check condition here
					
					}
					
					if ($condition_for_shrd_totals == 1) {
						$wks->writeFormula($count_row_j, 19, '=SUM('.$arr_xl_cols[19].($hold_for_grand_total+1).":".$arr_xl_cols[19].($count_row_j).")",$format_currency_2);
					}

				} else { //Was creating problems in Office 2010 when no shared clients, minor bug.
								$count_row_j = $count_row_i;
					}
											
				
				$wks->printArea(0,0,$count_row_j,20);
				//xdebug("Name: ",$display_name);
				//xdebug("Row: ",$count_row_j);
				$wks->fitToPages(1,2);

		}
								
				//@@@
				
				//end main							
				//}
// + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + =
			//show_array($master_array_exception);
			//ADD MORE WORSHEETS HERE
			//include('pay_ndetl_sdate_gen_excel_more.php');
			

// We still need to explicitly close the workbook
$wkb->close();
//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<a href="http://192.168.20.63/tdw/fileserve_xls.php?l=data/xls/&f=<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br />
<?
xdebug("Process ". $rnd_process_id . " completed at ",date('m/d/Y H:i:s a'));

//show_array($arr_rcc);
?>