<?
echo "<font color='red'>Module in <b>&alpha;</b> Stage!</font><br>";

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

include('pay_detl_sdate_functions.php');

include('pay_detl_sdate_queries.php');

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
xdebug("check_basis",$check_basis);
xdebug("cal_start_date",$cal_start_date);
xdebug("cal_end_date",$cal_end_date);
xdebug("brk_start_date",$brk_start_date);
xdebug("brk_end_date",$brk_end_date);

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

$qry = "select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date >='".$brk_start_date."' and trad_is_cancelled = 0";
$brk_start_settle_date = db_single_val($qry);
$qry = "select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$brk_end_date."' and trad_is_cancelled = 0";
$brk_end_settle_date = db_single_val($qry);

xdebug("Settle Dates", $brk_start_settle_date ." to " .$brk_end_settle_date);

//+ + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + +
//BASIC ERROR CHEKING PERFORMED HERE. MODULE WILL NOT RUN WITHOUT THIS PHASE RUNNING WITHOUT ERRORS
include('pay_detl_sdate_error_checks.php');
//+ + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + + & & + +

//= = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = =
//ROLLING MONTHS DATA
include('pay_detl_sdate_rolling_mos.php');
//= = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = = | | = =

////?
//Array of checks data for use within reps
$arr_checks = array();
$arr_checks_rr = array();
$arr_checks_rr_client = array();
/*
$query_checks = "SELECT 
										chek_advisor,
										sum(chek_amount) as chek_amount,
										sum(chek_amount) as for_sum_chek_amount
									FROM chk_chek_payments_etc 
									WHERE chek_isactive = 1 
									AND chek_date between '".$brk_start_date."' AND '".$brk_end_date."'
									GROUP BY chek_advisor 
									ORDER BY chek_advisor";
*/
$query_checks = "SELECT 
										chek_advisor,
										sum(chek_amount) as chek_amount,
										sum(chek_amount) as for_sum_chek_amount
									FROM chk_chek_payments_etc 
									WHERE chek_isactive = 1 
									AND chek_date between ".local_str_dates($check_basis, $cal_start_date, $cal_end_date, $brk_start_date, $brk_end_date)."
									GROUP BY chek_advisor 
									ORDER BY chek_advisor";

//xdebug("query_checks special B/C",$query_checks);	
$result_checks = mysql_query($query_checks) or die (tdw_mysql_error($query_checks));
while($row_checks = mysql_fetch_array($result_checks)) {
	$arr_checks[$row_checks["chek_advisor"]] = $row_checks["for_sum_chek_amount"];
	//now get the rep info for the advisor
	$query_rr_initials = "SELECT clnt_rr1, clnt_rr2
												FROM int_clnt_clients 
												WHERE clnt_code = '".$row_checks["chek_advisor"]."'";
	$result_rr_initials = mysql_query($query_rr_initials) or die (tdw_mysql_error($query_rr_initials));
	while($row_rr_initials = mysql_fetch_array($result_rr_initials)) {
		$val_rr1 = str_replace(" ","",$row_rr_initials["clnt_rr1"]);
		$val_rr2 = str_replace(" ","",$row_rr_initials["clnt_rr2"]);
		if ($val_rr1 != '' AND $val_rr2 == '') {
			 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Sole Client<br>";
			 //get rr_num for the sole account							  
			 $arr_checks_rr[$row_checks["chek_advisor"]] = get_rr_num (get_userid_for_initials($val_rr1));
		} elseif ($val_rr1 != '' AND $val_rr2 != ''){
			 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Shared Client<br>";
			 $arr_checks_rr[$row_checks["chek_advisor"]] = get_shared_rr_num ($val_rr1, $val_rr2);
		} else {
			 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Non trading account<br>"; 
		}
	}
}
//show_array($arr_checks);
//show_array($arr_checks_rr);

//We give the path to our file here
//generate a random filename
$xlfilename = date('Y-m-d_h.i.s.a')."__".md5(rand(1000000000,9999999999)).".xls";
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

//FORMATTING IN THE FOLLOWING FILE
include('pay_detl_sdate_gen_excel_format.php');

//=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*			
//GETTING PRIMARY REP. INFORMATION FROM TRADES
$arr_primary_list = array();
$query_trades = "SELECT 
										a.trad_rr,
										sum(a.trad_commission) as for_sum_trad_commission
									FROM mry_comm_rr_trades a, users b 
									WHERE a.trad_is_cancelled = 0 
									AND a.trad_settle_date between '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
									AND a.trad_rr like '0%'
									AND a.trad_rr = b.rr_num
									AND (b.Role = 3 or b.Role = 4)
									GROUP BY a.trad_rr 
									ORDER BY b.Role, b.Lastname";
$result_trades = mysql_query($query_trades) or die (tdw_mysql_error($query_trades));

while ($row_trades = mysql_fetch_array($result_trades) ) 
{
		if ($row_trades["trad_rr"] != '001') {
			$arr_primary_list[] = get_userid_for_rr($row_trades["trad_rr"]);
		}
}		
//=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*			
// + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + = + =
//Putting Trades Commissions
			$query_trades = "SELECT 
													a.trad_rr,
													sum(a.trad_commission) as for_sum_trad_commission
												FROM mry_comm_rr_trades a, users b 
												WHERE a.trad_is_cancelled = 0 
												AND a.trad_settle_date between '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
												AND a.trad_rr like '0%'
												AND a.trad_rr = b.rr_num
												AND (b.Role = 3 or b.Role = 4)
												GROUP BY a.trad_rr 
												ORDER BY b.Role, b.Lastname";

			$result_trades = mysql_query($query_trades) or die (tdw_mysql_error($query_trades));

			while ($row_trades = mysql_fetch_array($result_trades) ) 
			{
			//start main
					//SOME RR's excluded
					if ($row_trades["trad_rr"] == '001') {
					   //do nothing
					} else {
							//POPULATE ARRAY (PRIMARY REPS)
							$arr_primary_list[$row_trades["trad_rr"]] = $row_trades["trad_rr"];
					
							// ^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
							//Some names for REP NUMBERS
							if ($row_trades["trad_rr"] == '091') {
								$display_name = "BRG";
							} elseif ($row_trades["trad_rr"] == '999'){ 
								$display_name = "CenterSys";
							} else {
								$display_name = get_repname_by_rr_num($row_trades["trad_rr"]);
							}
							// ^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
							
							$process_userid = get_userid_for_rr ($row_trades["trad_rr"]);
							
							$wks =& $wkb->addWorksheet($display_name);
							$wks->setLandscape ();
							$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);

		
							//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEETS
							include('pay_detl_sdate_gen_excel_header.php');
							
							$wks->write(3, 2, "Sole");
							$wks->writeString(3, 3, ' '.$row_trades["trad_rr"].' ');
							$wks->write(3, 4, $display_name,$format_data_3);

							//xdebug("display_name",$display_name);

							$qry_client_comm = "SELECT trad_advisor_code, max(trad_advisor_name) as clnt_name , 
																	sum(trad_commission) as clnt_comm 
																	FROM mry_comm_rr_trades 
																	WHERE trad_settle_date BETWEEN '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
																	AND trad_rr = '".$row_trades["trad_rr"]."'
																	AND trad_is_cancelled = 0
																	GROUP BY trad_advisor_code 
																	ORDER BY trad_advisor_code";
							//xdebug("qry_client_comm",$qry_client_comm);
							//if ($row_trades["trad_rr"] == 
							
							
							$result_client_comm = mysql_query($qry_client_comm) or die (tdw_mysql_error($qry_client_comm));
							$count_row_i = 4;
							$processed_client = array();
							while ($row_client_comm = mysql_fetch_array($result_client_comm) ) 
							{
									$processed_client[] = $row_client_comm["trad_advisor_code"];

									if ($row_client_comm["clnt_name"] == '') {
										if ($arr_clients[$row_client_comm["trad_advisor_code"]] == '') {
											$clnt_name = "[".$row_client_comm["trad_advisor_code"]."]";
										} else {
											$clnt_name = $arr_clients[$row_client_comm["trad_advisor_code"]];
										}
									} else {
										$clnt_name = $row_client_comm["clnt_name"];
									}
									$wks->write($count_row_i, 4, $clnt_name,$format_data_1);
									$wks->writeNumber($count_row_i, 6, $row_client_comm["clnt_comm"],$format_currency_1);
									
									//-----------------------------------------------------------------------------
									//PROCESSING CHECKS FOR THIS CLIENT
									//xdebug ("checking client : ",$row_client_comm["trad_advisor_code"]);
									foreach ($arr_checks as $clnt=>$amt) {
									   if ($clnt == $row_client_comm["trad_advisor_code"]) {
										 	//echo "Client ".$clnt. " found with Check Amount = ". $amt."<br>";
											//echo $count_row_i."<br>";
											$wks->writeNumber($count_row_i, 7, $arr_checks[$row_client_comm["trad_advisor_code"]],$format_currency_1);
											//The fuckin' column above was mistyped as 8 instead of 7. Damn!!!!
											//Missing checks problem resolved.
										 }
									}
									//-----------------------------------------------------------------------------
									//process includes special payout rates
									if (in_array($row_client_comm["trad_advisor_code"],$arr_sp_payout_clnt)) {
										$special_rate_label = sp_payout_rate($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout)."%";
										$special_rate = sp_payout_rate($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout)/100;
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
									if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
									$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
										//xdebug("rolling_total".$row_client_comm["trad_advisor_code"],$rolling_total);					
									}
									if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
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
							
							//# - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - #
							//find more clients (checks) which are not processed above
							foreach ($arr_checks_rr as $clnt=>$rr) {
								 if (!in_array($clnt,$processed_client) AND $rr == $row_trades["trad_rr"]) {
											if ($arr_clients[$clnt] == '') {
												$clnt_name = "[".$clnt."]";
											} else {
												$clnt_name = $arr_clients[$clnt];
											}
									$wks->write($count_row_i, 4, $clnt_name,$format_data_1);
									$wks->writeNumber($count_row_i, 7, $arr_checks[$clnt],$format_currency_1);
									
									if ($row_trades["trad_rr"] == $debug_rr) {
										zdebug("clnt_name",$clnt_name);
										zdebug("Currently looking to process",$clnt);
									}
									
									if (in_array($clnt,$arr_sp_payout_clnt)) {
	
										if ($row_trades["trad_rr"] == $debug_rr) {
											zdebug("Special Payout Client : $clnt_name",$row_client_comm["trad_advisor_code"]);
										}
										$special_rate_label = sp_payout_rate($clnt, $process_userid, $arr_sp_payout)."%";
										$special_rate = sp_payout_rate($clnt, $process_userid, $arr_sp_payout)/100;
										//xdebug("special_rate",$special_rate);					
										//$wks->writeFormula($count_row_i, 8, '='.$arr_xl_cols[6].($count_row_i+1)."+".$arr_xl_cols[7].($count_row_i+1) ,$format_currency_1);
										$wks->write($count_row_i, 11, $special_rate_label,$format_currency_1);
										$wks->writeFormula($count_row_i, 8, '='.$arr_xl_cols[6].($count_row_i+1)."+".$arr_xl_cols[7].($count_row_i+1) ,$format_currency_1);
										$wks->writeFormula($count_row_i, 12, '='.$arr_xl_cols[8].($count_row_i+1)."*".$special_rate,$format_currency_1);
									} else {
										$wks->writeFormula($count_row_i, 8, '='.$arr_xl_cols[6].($count_row_i+1)."+".$arr_xl_cols[7].($count_row_i+1) ,$format_currency_1);
										$wks->writeFormula($count_row_i, 10, '='.$arr_xl_cols[8].($count_row_i+1)."*".$payout_multiplier,$format_currency_1);
									}

									//get rolling 12 months data
									$rolling_total = 0;
									//if (in_array($clnt,$arr_nest_client_list)) {
										$rolling_total = get_rcc ($arr_rcc, $rr, $clnt);
										//xdebug("clnt",$clnt."/".$rr."/".$rolling_total);
									//}
									if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
											//$wks->writeFormula($count_row_i, 15, '='.$arr_xl_cols[11].($count_row_i+1)."*1", $cond_format);
											$wks->writeFormula($count_row_i, 19, '=('.$arr_xl_cols[10].($count_row_i+1)."+".$arr_xl_cols[12].($count_row_i+1).")*1",$format_currency_1);
									} else {
											$wks->writeFormula($count_row_i, 14, '=('.$arr_xl_cols[10].($count_row_i+1)."+".$arr_xl_cols[12].($count_row_i+1).")*(-1)", $cond_format);
											$wks->writeFormula($count_row_i, 19, '=('.$arr_xl_cols[10].($count_row_i+1)."+".$arr_xl_cols[12].($count_row_i+1).")*0",$format_currency_1);
									}


									$count_row_i = $count_row_i + 1;
								 }
							}
							//# - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - # - #
							//end of clients from primary rep
							
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
							
							//&& == && == && == && == && == && == && == && == && == && == && == && == && == && == && == && == && == &&
						  //Now get the shared reps for the primary rep
							$condition_for_shrd_totals = 0;

							$query_userid = "SELECT ID from users where rr_num = '".$row_trades["trad_rr"]."'";
							$result_userid = mysql_query($query_userid) or die (tdw_mysql_error($query_userid));
								
							while ($row_userid = mysql_fetch_array($result_userid) ) {
								$user_id = $row_userid["ID"];
							}
							
							//HAVE TO INCLUDE CHECKS ONLY SHARED INSTANCES	
							//$query_reps_shared = "SELECT srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_user_id = '".$user_id."' AND srep_rrnum != ''";
							$query_reps_shared = "SELECT distinct(a.srep_rrnum) as srep_rrnum
																			from sls_sales_reps a,
																			mry_comm_rr_trades b 
																			WHERE b.trad_settle_date BETWEEN '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
																				AND a.srep_rrnum = b.trad_rr
																				AND b.trad_is_cancelled = 0
																			  AND a.srep_isactive = 1 AND a.srep_user_id = '".$user_id."' AND a.srep_rrnum != ''
																			ORDER BY a.srep_rrnum";
							
							//xdebug("query_reps_shared",$query_reps_shared);
							$result_reps_shared = mysql_query($query_reps_shared) or die (tdw_mysql_error($query_reps_shared));
							$str_reps_shared = "";
							while ($row_reps_shared = mysql_fetch_array($result_reps_shared) ) {
								$str_reps_shared = $row_reps_shared["srep_rrnum"]."|". $str_reps_shared;
							}

							//get all shared rep numbers for the user_id and then check if there are remaining chacks only for that rep number.
							$qry_checks_only_shared = "select srep_rrnum from sls_sales_reps where srep_user_id = '".$user_id."' and srep_isactive = 1"; 
							$result_checks_only_shared = mysql_query($qry_checks_only_shared) or die (tdw_mysql_error($qry_checks_only_shared));
							while ($row_checks_only_shared = mysql_fetch_array($result_checks_only_shared) ) {
								//just check if there is a check for this shared rep and thus making it valid entry
									if (in_array($row_checks_only_shared["srep_rrnum"],$arr_checks_rr)) {
										$str_reps_shared = $row_checks_only_shared["srep_rrnum"]."|". $str_reps_shared;
									}
							}

							//xdebug("str_reps_shared",$str_reps_shared);

							//Create the SQL String
							$arr_shared_reps = explode("|", $str_reps_shared);
							sort($arr_shared_reps);
							//xdebug("count",count($arr_shared_reps));
							
							//CHECK HERE FOR SHARED REP NUMBERS WHICH ARE NOT ON THE PRIMARY LIST, THEY HAVE TO HAVE WORKSHEETS
							foreach ($arr_shared_reps as $k=>$v) {
							//arr_primary_list
									$arr_tmp_userids = get_userid_for_shared_rr($v);
									//show_array($arr_tmp_userids);
									
									foreach($arr_tmp_userids as $kx=>$vx) {
										if (in_array($vx,$arr_primary_list)) {
  										//nothing
										}	else {
											$master_array_exception[$vx] = $vx;
										}							
									}
							}
							
							$str_sql_clause = '';
							foreach($arr_shared_reps as $key=>$value) {
									if ($value != '') {
										$str_sql_clause .= " OR trad_rr = '".$value."'";
									}
							}
							
							if (count($arr_shared_reps)>1) {
							
									$count_row_j = $count_row_i+1;
									$hold_count_j = $count_row_j;
									
								  foreach($arr_shared_reps as $no_index=>$shrd_rep_num_val) {
							
									if (strlen(trim($shrd_rep_num_val)) > 1) { //checking if rep num val is there 
										$condition_for_shrd_totals = 1;
										
										$wks->write($count_row_j, 2, "Shrd");
										$wks->write($count_row_j, 3, ' '.$shrd_rep_num_val.' ');
										$wks->write($count_row_j, 4, get_repname_by_rr_num($shrd_rep_num_val),$format_data_3);
		
												//@@@
												$qry_client_comm_s = "SELECT trad_advisor_code, max(trad_advisor_name) as clnt_name , 
																							sum(trad_commission) as clnt_comm 
																							FROM mry_comm_rr_trades 
																							WHERE trad_settle_date BETWEEN '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
																							AND trad_rr = '".$shrd_rep_num_val."'
																							AND trad_is_cancelled = 0
																							GROUP BY trad_advisor_code 
																							ORDER BY trad_advisor_code";
												
												$result_client_comm_s = mysql_query($qry_client_comm_s) or die (tdw_mysql_error($qry_client_comm_s));
												$count_row_k = $count_row_j+1;
												$processed_client_shared = array();
												while ($row_client_comm_s = mysql_fetch_array($result_client_comm_s) ) 
												{
														$processed_client_shared[] = $row_client_comm_s["trad_advisor_code"];
														
														if (trim($row_client_comm_s["clnt_name"]) == '') {
															$disp_name = $row_client_comm_s["trad_advisor_code"];
														} else {
															$disp_name = $row_client_comm_s["clnt_name"];
														}
														
														$wks->write($count_row_k, 2, " ");
														$wks->write($count_row_k, 3, ' '." ".' ');
														$wks->write($count_row_k, 4, $disp_name,$format_data_1);
														$wks->writeNumber($count_row_k, 6, $row_client_comm_s["clnt_comm"],$format_currency_1);
														
														//****
															//-----------------------------------------------------------------------------
															//xdebug ("checking client : ",$row_client_comm["trad_advisor_code"]);
															//Is there a check for this client?
															foreach ($arr_checks as $clnt=>$amt) {
																 if ($clnt == $row_client_comm_s["trad_advisor_code"]) {
																	//echo "Client ".$clnt. " found with Check Amount = ". $amt."<br>";
																	$wks->writeNumber($count_row_k, 6, $arr_checks[$row_client_comm_s["trad_advisor_code"]],$format_currency_1);
																 }
															}
		
															if (in_array($row_client_comm_s["trad_advisor_code"],$arr_sp_payout_clnt)) {
																$special_rate_label = sp_payout_rate($row_client_comm_s["trad_advisor_code"], $process_userid, $arr_sp_payout)."%";
																$special_rate = sp_payout_rate($row_client_comm_s["trad_advisor_code"], $process_userid, $arr_sp_payout)/100;
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
															if (in_array($row_client_comm_s["trad_advisor_code"],$arr_nest_client_list)) {
															$rolling_total = get_rcc ($arr_rcc, $row_trades_shared["trad_rr"], $row_client_comm_s["trad_advisor_code"]);
															}
															if ($rolling_total > 15000 or in_array($row_client_comm_s["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
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
												}
												//@@@
												
											//~~~~
											//find more clients (checks) which are not processed above
											foreach ($arr_checks_rr as $clnt=>$rr) {
												 if (!in_array($clnt,$processed_client_shared) AND $rr == $shrd_rep_num_val) {
															if ($arr_clients[$clnt] == '') {
																$clnt_name = "[".$clnt."]";
															} else {
																$clnt_name = $arr_clients[$clnt];
																//xdebug("clnt_name",$clnt_name);
															}
													$wks->write($count_row_k, 4, $clnt_name,$format_data_1);
													$wks->writeNumber($count_row_k, 7, $arr_checks[$clnt],$format_currency_1);
													$wks->writeFormula($count_row_k, 8, '='.$arr_xl_cols[6].($count_row_k+1)."+".$arr_xl_cols[7].($count_row_k+1) ,$format_currency_1);
													$wks->writeFormula($count_row_k, 10, '='.$arr_xl_cols[8].($count_row_k+1)."*".$payout_multiplier_shared,$format_currency_1);
				
													
													//PUT SPECIAL RATE LOGIC HERE  [HAVE TO DO]
													//TODO
													
													
													//get rolling 12 months data
													$rolling_total = 0;
													//if (in_array($clnt,$arr_nest_client_list)) {
														$rolling_total = get_rcc ($arr_rcc, $rr, $clnt);
													//}
															if ($rolling_total > 15000 or in_array($clnt,$arr_clients_cutoff_exceptions)) {
														$cond_format = $format_currency_1;
														$wks->writeFormula($count_row_k, 19, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*1",$format_currency_1);
													} else {
														$cond_format = $format_currency_1;
														$wks->writeFormula($count_row_k, 14, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*(-1)", $cond_format);
														$wks->writeFormula($count_row_k, 19, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*0",$format_currency_1);
													}
													$count_row_k = $count_row_k + 1;
												 }
											}
											//~~~~
												$count_row_j = $count_row_k;
												$count_row_j = $count_row_j + 1;
										}
									}	


								}
							}

							//Now totals for the shared rep data if any
							//put check condition here
							if ($condition_for_shrd_totals == 1) {
								$wks->writeFormula($count_row_j, 19, '=SUM('.$arr_xl_cols[19].($hold_for_grand_total+1).":".$arr_xl_cols[19].($count_row_j).")",$format_currency_2);
						  }
							
							$wks->printArea(0,0,$count_row_j,21);
							$wks->fitToPages(1,2);
			//end main							
			}
			
			//show_array($master_array_exception);
			//ADD MORE WORSHEETS HERE
			include('pay_detl_sdate_gen_excel_more.php');
			

// We still need to explicitly close the workbook
$wkb->close();
//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<a href="http://192.168.20.63/tdw/data/xls/<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br />
<?
xdebug("Process ". $rnd_process_id . " completed at ",date('m/d/Y H:i:s a'));

//show_array($arr_rcc);
?>