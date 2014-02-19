<link rel="stylesheet" type="text/css" href="includes/styles.css">
<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

include('pay_nsumm_sdate_functions.php');

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
//xdebug("Start Date",$brk_start_date);
//xdebug("End Date",$brk_end_date);

$qry = "select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date >='".$brk_start_date."' and trad_is_cancelled = 0";
//xdebug("Start Settle Date",$qry);
$brk_start_settle_date = db_single_val($qry);
$brk_end_settle_date = db_single_val("select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$brk_end_date."' and trad_is_cancelled = 0");

xdebug("Settle Date Start",$brk_start_settle_date);
xdebug("Settle Date End",$brk_end_settle_date);

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
										sum(trad_commission) as sum_trad_commission
									FROM mry_comm_rr_trades 
									WHERE trad_is_cancelled = 0 
									AND trad_settle_date > '".$back_brk_end_settle_date."' AND trad_settle_date <='".$brk_end_settle_date."'
									GROUP BY trad_rr, trad_advisor_code 
									ORDER BY trad_advisor_code";
									//									AND trad_rr like '0%'		REMOVED THIS! NEED ALL RR's including SHARED

//zdebug("qry_rolling12",$qry_rolling12);
$result_rolling12 = mysql_query($qry_rolling12) or die (tdw_mysql_error($qry_rolling12));
$arr_nest_client_list = array();
while($row_rolling12 = mysql_fetch_array($result_rolling12)) {
			$arr_rcc[] = $row_rolling12["trad_rr"]."^".$row_rolling12["trad_advisor_code"]."^".$row_rolling12["sum_trad_commission"];
			$arr_nest_client_list[] = $row_rolling12["trad_advisor_code"];
}

//getting checks from the prior year period and assimiliating into the array created above.
$qry_rolling12_checks = "SELECT 
														chek_advisor,
														sum(chek_amount) as chek_amount
													FROM chk_chek_payments_etc 
													WHERE chek_date > '".$back_brk_end_date."' AND chek_date <='".$brk_end_date."' 
													AND chek_isactive = 1 
													GROUP BY chek_advisor 
													ORDER BY chek_advisor";
//zdebug("qry_rolling12_checks",$qry_rolling12_checks);																															
$result_rolling12_checks = mysql_query($qry_rolling12_checks) or die (tdw_mysql_error($qry_rolling12_checks));
$arr_rolling12_checks = array();
while($row_rolling12_checks = mysql_fetch_array($result_rolling12_checks)) {
			$arr_rolling12_checks[$row_rolling12_checks["chek_advisor"]] = $row_rolling12_checks["chek_amount"];
}

//Assimmilating for 12 months (Checks and Commissions)
$arr_combined = array();
foreach ($arr_rcc as $key=>$value) {
	//echo $value."<br>";
	$tmp_store = explode("^",$value);
	$new_amount = 0;
	foreach ($arr_rolling12_checks as $clnt=>$amt) {
			if ($clnt == $tmp_store[1]) {
				 	$new_amount = $tmp_store[2]+$amt;		
					//zdebug("Adding to comm data for ".$tmp_store[1],$amt);
			}
	}
	if ($new_amount != 0) {
		$arr_combined[] = $tmp_store[0]."^".$tmp_store[1]."^".$new_amount;
	} else {
		$arr_combined[] = $tmp_store[0]."^".$tmp_store[1]."^".$tmp_store[2];
	}
}

//now add checks only client to the combined array
$arr_delta = array();
foreach ($arr_rolling12_checks as $clnt=>$amt) {
  if (!in_array($clnt, $arr_nest_client_list)) {
		//get rr_num for client
		$qry = "select trim(clnt_rr1) as rr1, trim(clnt_rr2) as rr2 from int_clnt_clients where clnt_code = '".$clnt."'";
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		while($row = mysql_fetch_array($result)) {
			$rr1 = $row["rr1"];
			$rr2 = $row["rr2"];	
			if ($rr1 != '' OR $rr2 != '') {
				if ($rr2 == '') {
					$tmp_rr_num = get_rr_num (get_userid_for_initials ($rr1));
					//zdebug("tmp_rr_num",$tmp_rr_num);	
					//zdebug("clnt/rr1/rr2", $clnt."/".$rr1."/".$rr2);
					$arr_delta[] = $tmp_rr_num."^".$clnt."^".$amt;
				} else {
					$tmp_rr_num = get_shared_rr_num ($rr1, $rr2);
					//zdebug("tmp_rr_num",$tmp_rr_num);				
					//zdebug("clnt/rr1/rr2", $clnt."/".$rr1."/".$rr2);
					$arr_delta[] = $tmp_rr_num."^".$clnt."^".$amt;
				}
			}
		}
	}
}

//show_array($arr_delta);	

//now reassign the $arr_rcc which is below
$arr_rcc = array();
$arr_rcc = $arr_combined;

//now merge the arrays
$arr_merged = array_merge($arr_rcc, $arr_delta);
//show_array($arr_rcc);	
//exit;
//now reassign the $arr_rcc which is below
$arr_rcc = array();
$arr_rcc = $arr_merged;

//zdebug("ADAG/030",$arr_nest_client["ADAG"]["030"]);	
//####################################################################################################################
//####################################################################################################################

////?
//Array of checks data for use within reps
$arr_checks = array();
$arr_checks_rr = array();
$arr_checks_rr_client = array();
$query_checks = "SELECT 
										chek_advisor,
										sum(chek_amount) as chek_amount,
										sum(chek_amount) as for_sum_chek_amount
									FROM chk_chek_payments_etc 
									WHERE chek_isactive = 1 
									AND chek_date BETWEEN  ".local_str_dates($check_basis, $cal_start_date, $cal_end_date, $brk_start_date, $brk_end_date)."
									GROUP BY chek_advisor 
									ORDER BY chek_advisor";
//zdebug("query_checks",$query_checks);
$result_checks = mysql_query($query_checks) or die (tdw_mysql_error($query_checks));
while($row_checks = mysql_fetch_array($result_checks)) {
			$arr_checks[$row_checks["chek_advisor"]] = $row_checks["for_sum_chek_amount"];
			//now get the rep info for the advisor
			
			//zdebug("Client",$row_checks["chek_advisor"]);
			//zdebug("Check Amount",$row_checks["for_sum_chek_amount"]);
			
			$query_rr_initials = "SELECT clnt_rr1, clnt_rr2
														FROM int_clnt_clients 
														WHERE clnt_code = '".$row_checks["chek_advisor"]."'";
														
			$result_rr_initials = mysql_query($query_rr_initials) or die (tdw_mysql_error($query_rr_initials));
			while($row_rr_initials = mysql_fetch_array($result_rr_initials)) {
					$val_rr1 = trim($row_rr_initials["clnt_rr1"]);
					$val_rr2 = trim($row_rr_initials["clnt_rr2"]);
					//echo "[".$val_rr1."][".$val_rr2."][".$row_checks["chek_advisor"]."]<br>";
					if ($val_rr1 != '' AND $val_rr2 == '') {
						 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Sole Client<br>";
						 //get rr_num for the sole account							  
						 $arr_checks_rr[$row_checks["chek_advisor"]] = get_rr_num (get_userid_for_initials($val_rr1));
						 $arr_rr_clnt_chk[] = get_rr_num (get_userid_for_initials($val_rr1))."^".$row_checks["chek_advisor"]."^".$row_checks["for_sum_chek_amount"];
					} elseif ($val_rr1 != '' AND $val_rr2 != ''){
						 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Shared Client<br>";
						 $arr_checks_rr[$row_checks["chek_advisor"]] = get_shared_rr_num ($val_rr1, $val_rr2);
						 $arr_rr_clnt_chk[] = get_shared_rr_num ($val_rr1, $val_rr2)."^".$row_checks["chek_advisor"]."^".$row_checks["for_sum_chek_amount"];
					} else {
						 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Non trading account<br>";
					}
			}
 
}
//show_array($arr_checks);
//show_array($arr_checks_rr);
//show_array($arr_rr_clnt_chk);

//echo get_shared_rr_num ('**','**');

//exit;

//We give the path to our file here
//generate a random filename

$xlfilename = date('Y-m-d_h.i.s.a')."__".md5(rand(1000000000,9999999999)).".xls";
//$xlfilename = "test.xls";
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

			//FORMATTING IN THE FOLLOWING FILE
			include('pay_summ_gen_excel_format.php');

			$wks =& $wkb->addWorksheet("Payout Summary");
			$wks->setLandscape ();
			$wks->setMarginLeft(0.4);
			$wks->setMarginRight(0.4);
			$wks->setMarginTop(0.5);
			$wks->setMarginBottom(0.4);
			$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);
			
			$wks->setPaper(5);

			//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEET
			include('pay_summ_gen_excel_header.php');

      //Master Array keyed with trad_rr
			$arr_master = array();
			$str_construct = "";

			$query_trades = "SELECT 
													trad_rr,
													sum(trad_commission) as for_sum_trad_commission
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_settle_date between '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
												GROUP BY trad_rr 
												ORDER BY trad_rr";
												//AND a.trad_rr like '0%'
			//zdebug("query_trades",$query_trades);
			$result_trades = mysql_query($query_trades) or die (tdw_mysql_error($query_trades));

			$count_row_global = 4;
			while ($row_trades = mysql_fetch_array($result_trades) ) 
			{
			//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
			//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
			//HANDLING EACH PRIMARY RR
			
			//start main
					//SOME RR's excluded
					if ($row_trades["trad_rr"] == '000') {
					   //do nothing
					} else {

							//%%%%%%%%%%%%%%%%
							//Some names for REP NUMBERS
							if ($row_trades["trad_rr"] == '091') {
								$display_name = "BRG";
							} elseif ($row_trades["trad_rr"] == '999'){ 
								$display_name = "CenterSys";
							} else {
								$display_name = get_repname_by_rr_num($row_trades["trad_rr"]);
							}
							//%%%%%%%%%%%%%%%%
		
							if ($row_trades["trad_rr"] == $debug_rr) {
								zdebug("Rep. Name",$display_name);
							}

							//DATA SECTION=============================================================================================							
							
							//reset $str_construct to null
							$str_construct = "";
							$process_userid = get_userid_for_rr ($row_trades["trad_rr"]);

							if (substr($row_trades["trad_rr"],0,1)!='0') {
							//zdebug("Processing Shared Rep: ", $row_trades["trad_rr"]);
							}
							
							//zdebug("process_userid",$process_userid);
							//start constructing the string
							$str_construct = $row_trades["trad_rr"];
							$str_construct = $str_construct."^".$process_userid;

							if ($row_trades["trad_rr"] == $debug_rr) {
								zdebug("UserIDNum",$process_userid);
							}

							if ($process_userid == "") {
								$new_process_userid = get_user_id_for_shared_reps($row_trades["trad_rr"]);
							} else {
								$new_process_userid = $process_userid;
							}
							$str_construct = $str_construct."^".$new_process_userid;
							if ($row_trades["trad_rr"] == $debug_rr) {
								zdebug("Alt. UserIDNum",$new_process_userid);
							}
														
							$str_construct = $str_construct."^".$display_name;
							$str_construct = $str_construct."^".$row_trades["for_sum_trad_commission"];
							if ($row_trades["trad_rr"] == $debug_rr) {
								zdebug("Sum Trade Commission",$row_trades["for_sum_trad_commission"]);
							}


							$sum_rr_check = 0;
							foreach ($arr_rr_clnt_chk as $k=>$v) {
								$detail = explode("^",$v);
								if ($detail[0]==$row_trades["trad_rr"]) {
								 $sum_rr_check = $sum_rr_check + $detail[2]; 
								}
							}

							$str_construct = $str_construct."^".$sum_rr_check;
							if ($row_trades["trad_rr"] == $debug_rr) {
								zdebug("Sum RR Checks",number_format($sum_rr_check,2,'.',','));
							}

							//NEW SECTION
							//PROCESSING EACH CLIENT IN THE RR's GROUP.
							$qry_client_comm = "SELECT trad_advisor_code, 
																	sum(trad_commission) as clnt_comm 
																	FROM mry_comm_rr_trades 
																	WHERE trad_settle_date BETWEEN '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
																	AND trad_rr = '".$row_trades["trad_rr"]."'
																	AND trad_is_cancelled = 0
																	GROUP BY trad_advisor_code 
																	ORDER BY trad_advisor_code";
							
							$result_client_comm = mysql_query($qry_client_comm) or die (tdw_mysql_error($qry_client_comm));
							$count_row_i = 4;
							$processed_client = array();
							
							$comm_total = 0;
							$comm_checks_total = 0;
							$comm_standard_total = 0;
							$comm_special_total = 0;
							$comm_less_than_cutoff = 0;
							$str_special = "";
							$str_individual = "";		
							
							$tmp_payout_indiv = "";
							$comm_special_total_rr1 = 0;
							$tmp_payout_rr1 = 0;
							$comm_special_total_rr2 = 0;
							$tmp_payout_rr2 = 0;
							
							$comm_standard_total_rr1 = 0;
							$comm_standard_total_rr1 = 0;

							$arr_tmp_payout_indiv = array();
												
							while ($row_client_comm = mysql_fetch_array($result_client_comm) ) 
							{
									
									$processed_client[] = $row_client_comm["trad_advisor_code"];

									$comm_total = $comm_total + $row_client_comm["clnt_comm"];
									if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
									  zdebug("Total Commission from Client : [".$debug_client."]", $comm_total);
									}

									//-----------------------------------------------------------------------------
									//Is there a check for this client?
									$hold_check_amount = 0;
									foreach ($arr_checks as $clnt=>$amt) {
									   if ($clnt == $row_client_comm["trad_advisor_code"]) {
											$comm_checks_total = $comm_checks_total + $arr_checks[$clnt];
											$hold_check_amount = $arr_checks[$clnt];
										 }
									}
									if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
									  zdebug("Total Checks from Client : [".$row_client_comm["trad_advisor_code"]."]", $comm_checks_total);
									}
									
									//-----------------------------------------------------------------------------
									//process includes special payout rates
									//CAPTURE DATA IN ARRAY IF SHARED REP, CAPTURE NUMBERS FOR CONSTITUENTS OF SHARED REP BY USERID
									//DATA INCONSISTENCY WITH AIMA
									$tmp_payout = 0;
									$tmp_payout_indiv = "";
									if (substr($row_trades["trad_rr"],0,1)=='0') { //primary rep

											if (in_array($row_client_comm["trad_advisor_code"],$arr_sp_payout_clnt)) {
												$arr_get_sp_out = sp_payout_rate_alt($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout);
												$str_special = $str_special . "  " . $arr_get_sp_out[0];
												$special_rate = $arr_get_sp_out[1]/100;
												$comm_special_total = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate ); 
												$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate );

												if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
													zdebug("Special Payout for Primary Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $tmp_payout);
												}												
												
											} else {
												$comm_standard_total = $comm_standard_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier ); 
												$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier );
												
												if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
													zdebug("Regular Payout for Primary Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $tmp_payout);
												}												
											}

									} else { //shared rep

	
											if (in_array($row_client_comm["trad_advisor_code"],$arr_sp_payout_clnt)) {
											
															if ($row_trades["trad_rr"] == $debug_sr) {
																zdebug("Processing Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", "<<");
															}												
												
															$arr_get_sp_out = sp_payout_rate_alt($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout);
															$str_special = $str_special . "  " . $arr_get_sp_out[0];
															$special_rate = $arr_get_sp_out[1]/100;
			
															if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
																zdebug("Special Rate for Shared Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $special_rate);
															}												
			
															$comm_special_total = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate ); 
															$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate );
			
															if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
																zdebug("Special Payout for Shared Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $tmp_payout);
															}												
			
															//Get the payout rates for the individuals and apply to the totals for 'em
															//zdebug('Processing individuals for Shared:',$arr_sp_payout[$row_client_comm["trad_advisor_code"]]);
															$arr_each_rep = explode("#",$arr_sp_payout[$row_client_comm["trad_advisor_code"]]);	
															$arr_rr1 = explode("^",$arr_each_rep[0]);
															$arr_rr2 = explode("^",$arr_each_rep[1]);

															//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
															//APPLYING 12 MONTH LOGIC HERE
															if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
																$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
																
																if ($row_client_comm["trad_advisor_code"]==$debug_client) {
																	zdebug("Rolling Total : ".$row_client_comm["trad_advisor_code"],$rolling_total);	
																}				
															}

															if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
																$comm_special_total_rr1 = $comm_special_total_rr1 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr1[1]/100) ); 
																$tmp_payout_rr1 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr1[1]/100) );
																$comm_special_total_rr2 = $comm_special_total_rr2 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr2[1]/100) ); 
																$tmp_payout_rr2 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr2[1]/100) );
															} else {
																//$comm_special_total_rr1 = $comm_special_total_rr1 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr1[1]/100)*(-1) ); 
																//$tmp_payout_rr1 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr1[1]/100)*(-1) );
																//$comm_special_total_rr2 = $comm_special_total_rr2 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr2[1]/100)*(-1) ); 
																//$tmp_payout_rr2 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr2[1]/100)*(-1) );
															}
															//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
															

															
															if ($arr_rr2[0] == $debug_uid) {
																zdebug("Special Payout for Shared Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $tmp_payout_rr1);
															}												
			
															//$tmp_payout_indiv = $arr_rr1[0].">".$comm_special_total_rr1.">".$tmp_payout_rr1."#".$arr_rr2[0].">".$comm_special_total_rr2.">".$tmp_payout_rr2."#".$tmp_payout_indiv;
															$tmp_payout_indiv = $arr_rr1[0].">".$comm_special_total_rr1.">".$tmp_payout_rr1."#".$arr_rr2[0].">".$comm_special_total_rr2.">".$tmp_payout_rr2."#".$tmp_payout_indiv;
															
															$arr_tmp_payout_indiv[] = $tmp_payout_indiv;
															if ($row_trades["trad_rr"] == $debug_sr) {
																zdebug("String Output", $tmp_payout_indiv);
															}												
												
											} else {

															if ($row_trades["trad_rr"] == $debug_sr) {
																zdebug("Processing Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", "<<");
															}												

															$comm_standard_total = $comm_standard_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier ); 
															$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier );
															
															if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
																zdebug("Standard Payout for Shared Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $tmp_payout);
																zdebug(" > Commission : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $row_client_comm["clnt_comm"]);
																zdebug(" > Checks : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $hold_check_amount);
																zdebug(" > Payout Multiplier : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $payout_multiplier);												
															}												
			
															$str_userid = get_user_id_for_shared_reps ($row_trades["trad_rr"]);
															if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
																zdebug("Users for Shared Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $str_userid);
															}												
															
															$arr_userid = explode("#",$str_userid);
															$arr_rr1 = explode("|",$arr_userid[0]);
															$arr_rr2 = explode("|",$arr_userid[1]);
															//show_array($arr_rr1);
															//show_array($arr_rr2);

															//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
															//APPLYING 12 MONTH LOGIC HERE
															if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
																$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
																
																if ($row_client_comm["trad_advisor_code"]==$debug_client) {
																	zdebug("Rolling Total : ".$row_client_comm["trad_advisor_code"],$rolling_total);	
																}				
															}

															if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
																$comm_standard_total_rr1 = $comm_standard_total_rr1 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 ); 
																$tmp_payout_rr1 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 );
																$comm_standard_total_rr2 = $comm_standard_total_rr2 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 ); 
																$tmp_payout_rr2 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 );
															} else {
																//$comm_standard_total_rr1 = $comm_standard_total_rr1 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 * (-1)); 
																//$tmp_payout_rr1 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 * (-1));
																//$comm_standard_total_rr2 = $comm_standard_total_rr2 + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 * (-1)); 
																//$tmp_payout_rr2 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 * (-1));
															}
															//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

															if ($debug_client != '' and $debug_client == $row_client_comm["trad_advisor_code"]) {
																zdebug("Standard Payout for Shared Client : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $tmp_payout_rr1);
																zdebug(" > Commission : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $row_client_comm["clnt_comm"]);
																zdebug(" > Checks : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $hold_check_amount);
																zdebug(" > Payout Multiplier : [".$row_client_comm["trad_advisor_code"]."/".$row_trades["trad_rr"]."]", $payout_multiplier * 0.5);												
															}												
					
															$tmp_payout_indiv = $arr_rr1[0].">".$comm_standard_total_rr1.">".$tmp_payout_rr1."#".$arr_rr2[0].">".$comm_standard_total_rr2.">".$tmp_payout_rr2."#".$tmp_payout_indiv;
															$arr_tmp_payout_indiv[] = $tmp_payout_indiv;

															if ($row_trades["trad_rr"] == $debug_sr) {
																zdebug("String Output", $tmp_payout_indiv);
															}												
											}
									}
									//-----------------------------------------------------------------------------

									//zdebug("PROCESSING CLIENT for ROLLING 12",$row_client_comm["trad_advisor_code"]);

									//get rolling 12 months data
									//CAPTURE DATA FOR CONSTITUENTS OF SHARED REP WHERE APPLICABLE (NOT DONE YET ..  moved shared rep code above)
									if (substr($row_trades["trad_rr"],0,1)=='0') {
												if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
													$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
												}
												if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
													//do nothing
												} else {
													$comm_less_than_cutoff = $comm_less_than_cutoff + ($tmp_payout * (-1));
												}
									} else {
												if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
													$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
													
													if ($row_client_comm["trad_advisor_code"]==$debug_client) {
														zdebug("Rolling Total : ".$row_client_comm["trad_advisor_code"],$rolling_total);	
													}				
												}
												if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
													//do nothing
												} else {
													$comm_less_than_cutoff = $comm_less_than_cutoff + ($tmp_payout * (-1));
													
													//adjust the values for indivudual reps
													
													if ($row_client_comm["trad_advisor_code"]==$debug_client) {
														zdebug("Less than cutoff : ".$row_client_comm["trad_advisor_code"],$comm_less_than_cutoff);	
													}				
													//zdebug("comm_less_than_cutoff",$comm_less_than_cutoff);	
												}
									}
									//-----------------------------------------------------------------------------
									
							}
							
							
							//CONSIDER SHARED REPS FOR CASE BELOW AND PROCESS ACCORDINGLY	 					
							//find more clients (checks) which are not processed above (THIS IS NOT DONE YET : PP : OCT 08 2007)
							foreach ($arr_checks_rr as $clnt=>$rr) {
								 if (!in_array($clnt,$processed_client) AND $rr == $row_trades["trad_rr"]) {
											if ($arr_clients[$clnt] == '') {
												$clnt_name = "[".$clnt."]";
											} else {
												$clnt_name = $arr_clients[$clnt];
											}
											
									
									$comm_total = $comm_total + $arr_checks[$clnt];
									if ($row_trades["trad_rr"]==$debug_rr) {
										zdebug("Total Commission (after checks only client) : ".$clnt_name,$comm_total);	
									}				

									$tmp_payout1 = 0;
									if (in_array($clnt,$arr_sp_payout_clnt)) {
									  $arr_get_sp_out = sp_payout_rate_alt($clnt, $process_userid, $arr_sp_payout);
										$str_special = $str_special . "  " . $arr_get_sp_out[0];
										
										$special_rate = $arr_get_sp_out[1]/100;
										if ($clnt==$debug_client) {
											zdebug("Special Rate for this Checks Only Client : ".$clnt_name,$comm_total);	
										}
														
										$comm_special_total = $comm_special_total + ( $arr_checks[$clnt] * $special_rate ); 
										$tmp_payout1 = ( $arr_checks[$clnt] * $special_rate );

									} else {

										$comm_standard_total = $comm_standard_total + ( $arr_checks[$clnt] * $payout_multiplier ); 
										$tmp_payout1 = ( $arr_checks[$clnt] * $payout_multiplier );

									}

									//get rolling 12 months data
									$rolling_total = 0;
									//if (in_array($clnt,$arr_nest_client_list)) {
										$rolling_total = get_rcc ($arr_rcc, $rr, $clnt);
									//}
									if ($rolling_total > 15000 or in_array($clnt,$arr_clients_cutoff_exceptions)) {
											//Do nothing									
									} else {
											$comm_less_than_cutoff = $comm_less_than_cutoff + ($tmp_payout1 * (-1));
									}
									
									

								 }
							}

							//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
							//Creating combined string tmp_payout_indiv_new from array arr_tmp_payout_indiv
							$tmp_rr_1 = "";
							$tmp_rr_2 = "";
							$tmp_sum_11 = 0;
							$tmp_sum_12 = 0;
							$tmp_sum_21 = 0;
							$tmp_sum_22 = 0;
							//first get reps from the array
							foreach ($arr_tmp_payout_indiv as $k=>$v) {
								if ($k == 0) {
									$arr_v = explode("#",$v);
									foreach ($arr_v as $k1=>$v1) {
										if ($row_trades["trad_rr"]==$debug_sr) {
											zdebug("v1",$v1);	
										}				
										if ($k1 == 0) {
											$tmp_rr_1 = substr($v1,0,3);
										}
										if ($k1 == 1) {
											$tmp_rr_2 = substr($v1,0,3);
										}
									}
								}
							}
							//then add up the values for each
							foreach ($arr_tmp_payout_indiv as $k=>$v) {
									$arr_v = explode("#",$v);
									foreach ($arr_v as $k1=>$v1) {
										if ($v1 != '') {
												if ($row_trades["trad_rr"]==$debug_sr) {
													zdebug("v1 used in calculation",$v1);	
												}				
												$arr_v1 = explode(">",$v1);
												if ($arr_v1[0] == $tmp_rr_1) {
													$tmp_sum_11 = $tmp_sum_11 + $arr_v1[1];
													$tmp_sum_12 = $tmp_sum_12 + $arr_v1[2];
												} 
												if ($arr_v1[0] == $tmp_rr_2) {
													$tmp_sum_21 = $tmp_sum_21 + $arr_v1[1];
													$tmp_sum_22 = $tmp_sum_22 + $arr_v1[2];
												} 
										}
									}
							}

							//Constructing string tmp_payout_indiv_new
							$tmp_payout_indiv_new = $tmp_rr_1.">".$tmp_sum_11.">".$tmp_sum_12."#".$tmp_rr_2.">".$tmp_sum_21.">".$tmp_sum_22."#";

							if ($row_trades["trad_rr"]==$debug_sr) {
								zdebug("tmp_rr_1",$tmp_rr_1);	
								zdebug("tmp_rr_2",$tmp_rr_2);	
								zdebug("tmp_payout_indiv_new",$tmp_payout_indiv_new);	
							}				
							//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
							


							//zdebug("S/Sp/Cutoff",$comm_standard_total."/".$comm_special_total."/".$comm_less_than_cutoff);
							$str_construct = $str_construct."^".$comm_standard_total;
							$str_construct = $str_construct."^".$str_special;
							$str_construct = $str_construct."^".$comm_special_total;
							$str_construct = $str_construct."^".$comm_less_than_cutoff;
							//$str_construct = $str_construct."^".$tmp_payout_indiv;
							$str_construct = $str_construct."^".$tmp_payout_indiv_new;

							if ($row_trades["trad_rr"] == $debug_sr) {
								zdebug("Comm. Standard Total",number_format($comm_standard_total,2,'.',','));
								zdebug("Special Payout",$str_special);
								zdebug("Comm. Special Total",number_format($comm_special_total,2,'.',','));
								zdebug("Less than Cutoff",number_format($comm_less_than_cutoff,2,'.',','));
								//zdebug("TMP Payout",number_format($tmp_payout_indiv,2,'.',','));
								zdebug("TMP Payout",number_format($tmp_payout_indiv_new,2,'.',','));
								zdebug("String So Far",$str_construct);
							}

							$arr_master[$row_trades["trad_rr"]] = $str_construct;

							//END DATA SECTION=============================================================================================							
							$count_row_global = $count_row_global + 1;
					}
			//end main							
			}


			//RE-PROCESS ARRAY [PRIMARY REPS ARE IN THE 0XX SERIES
			$existing_uids = array();
			foreach($arr_master as $k=>$v) {
				if (substr($k,0,1) == '0') {
					//capture uids in array
					$existing_uids[] = get_userid_for_rr ($k);
					if ($k == $debug_rr) {
						zdebug("Re processing Array",get_userid_for_rr ($k));
					}
				}	
			}
			
			//show_array($existing_uids);
			//show_array($arr_master);

			$arr_nonexist_uid = array();
			foreach($arr_master as $k=>$v) {
				if (substr($k,0,1) != '0') {
					//capture uids in array
					$str_uids = get_user_id_for_shared_reps ($k);
					//echo $str_uids;
					$arr_uids = explode("#",$str_uids);
					
					$arr_uid_1 = explode("|",$arr_uids[0]);
					$uid_1 = $arr_uid_1[0];
					$arr_uid_2 = explode("|",$arr_uids[1]);
					$uid_2 = $arr_uid_2[0];
					
					if ($uid_1 == $debug_uid OR $uid_2 == $debug_uid) {
						zdebug("Primary Rep. ".$debug_rr. " is a part of", $k);
					}
					
					//zdebug("uid_1",$uid_1);
					//zdebug("uid_2",$uid_2);
					if (!in_array($uid_1,$existing_uids)) {
					 //echo get_user_by_id ($uid_1). " not in main list<br>";
					 $arr_nonexist_uid[$uid_1] = get_user_by_id ($uid_1);
					}
					if (!in_array($uid_2,$existing_uids)) {
					 //echo get_user_by_id ($uid_2). " not in main list<br>";
					 $arr_nonexist_uid[$uid_2] = get_user_by_id ($uid_2);
					}
				}	
			}
			
			//show_array($arr_nonexist_uid);
			
			foreach($arr_nonexist_uid as $k=>$v) {
				$nonexisting_uids[] = $k;
				//echo $k . " = ".get_user_by_id ($k)."<br>";
				//ABOVE ARE THE TRADERS ETC
			}

			//show_array($nonexisting_uids);
			
			$arr_complete_sole = array_merge($existing_uids, $nonexisting_uids);
			
			//show_array($arr_complete_sole);
			//show_array($arr_master);
			//$arr_master looks like this.
			/*
			001 = [001^95^95^David Keidan^403708.00^0^0^ 0%^0^0^]
			012 = [012^218^218^Scott Brunner^254546.58^8000^0^^0^0^]
			028 = [028^234^234^Fran Mccartan^98823.04^25000^0^^0^0^]
			030 = [030^230^230^Mark Gronet^203032.88^90937^0^^0^0^]
			031 = [031^238^238^Andrew Sinclair^143970.34^23000^0^^0^0^]
			032 = [032^221^221^Robert Donner^169480.51^118200.65^0^^0^0^]
			033 = [033^219^219^Brian Colburn^182006.28^99291^0^^0^0^]
			034 = [034^236^236^Jill Rosenberg^154605.23^7000^0^^0^0^]
			035 = [035^225^225^Tom Leddy^195371.40^15000^0^^0^0^]
			036 = [036^226^226^Marc Luchansky^201975.80^19830^0^^0^0^]
			038 = [038^240^240^Howard Turetsky^205665.90^75000^0^^0^0^]
			039 = [039^233^233^John Maguire^255288.66^22390^0^ 5%^4379.004^0^]
			040 = [040^235^235^Doug Rogers^530623.32^123396.6^0^^0^0^]
			042 = [042^239^239^Vincent Sullivan^147636.00^0^0^ 50%^73818^0^]
			044 = [044^228^228^Bob Efstathiou^416824.60^0^0^^0^0^]
			045 = [045^298^298^Anne Williams^2207.25^0^0^^0^0^]
			046 = [046^276^276^Kevin O'Gorman^200464.50^45000^0^^0^0^]
			048 = [048^220^220^John Davey^55052.88^3044.91^0^^0^0^]
			060 = [060^245^245^Anthony Sutera^8019.00^0^0^^0^0^]
			067 = [067^281^281^Michael Nolan^115218.25^105000^0^ 20% 20% 20% 20% 20% 20% 20% 20%^36507.45^0^]
			068 = [068^296^296^Matthew Jacob^1864.00^0^0^ 30.00%^247.2^0^]
			091 = [091^288^288^BRG^106794.53^0^0^^0^0^]
			214 = [214^^233|039#221|032^Maguire/Donner^25483.10^0^0^^0^0^233>0>0#221>0>0#]
			216 = [216^^248|049#218|012^Stephens/Brunner^5000.00^0^0^^0^0^248>0>0#218>0>0#]
			217 = [217^^186|025#235|040^Gasner/Rogers^12285.25^0^0^^0^0^186>0>0#235>0>0#]
			220 = [220^^243|061#230|030^Crowley/Gronet^16066.00^0^0^ 9.5%/ 19%^4578.81^0^230>7631.35>3052.54#243>6105.08>1526.27#]
			224 = [224^^235|040#228|044^Rogers/Efstathiou^50649.00^0^0^^0^0^235>0>0#228>0>0#]
			225 = [225^^240|038#218|012^Turetsky/Brunner^6765.00^0^0^^0^0^240>0>0#218>0>0#]
			229 = [229^^241|063#236|034^Gault/Rosenberg^13000.00^0^0^^0^0^241>0>0#236>0>0#]
			232 = [232^^244|065#236|034^Demartini/Rosenberg^14660.00^0^0^^0^0^244>0>0#236>0>0#]
			240 = [240^^225|035#276|046^Leddy/O'Gorman^10218.00^0^0^^0^0^225>0>0#276>0>0#]
			241 = [241^^245|060#95|001^Sutera/Keidan^76580.00^0^0^ 9.5%/ 0%^7275.1^0^95>7275.1>0#245>14550.2>7275.1#]
			243 = [243^^219|033#234|028^Colburn/Mccartan^30618.93^0^0^ 5%/ 19%^5491.2^0^219>9838.4>4347.2#234>6635.2>1144#]
			246 = [246^^230|030#213|024^Gronet/Weintraub^672.00^0^0^^0^0^230>0>0#213>0>0#]
			247 = [247^^238|031#226|036^Sinclair/Luchansky^19596.40^0^0^ 9.5%/ 9.5%^3723.316^0^226>5584.974>1861.658#238>5584.974>1861.658#]
			248 = [248^^234|028#238|031^Mccartan/Sinclair^2000.00^0^0^^0^0^234>0>0#238>0>0#]			
			*/
			
			//NEW MASTER ARRAY
			$arr_master_new = array();
			
			//create an array entry for nonexisting UIDs in the master
			foreach ($nonexisting_uids as $key_1=>$val_1) {
					//echo "Processing UID ".$val_1."<br>";
					$sumval_for_uid = 0;
					foreach($arr_master as $key_2=>$val_2) {
							if(substr($key_2,0,1) != '0') {					
							//zdebug("val_2", $val_2);
							$process = explode("^",$val_2);
							$process_new = explode("#",$process[10]);
									foreach ($process_new as $key_3=>$val_3){
											if ($val_3 != "") {
  												//zdebug($val_1.' val to process', $val_3);
													$process_final = explode(">",$val_3);
															if ($process_final[0] == $val_1) {
																	$sumval_for_uid = $sumval_for_uid + $process_final[2];
															}
											}
									} 
							}
					}
					
					//zdebug("Val for UID ".$val_1 , $sumval_for_uid);
					
					$arr_master_new[get_rr_num($val_1)] = get_rr_num($val_1)."^".$val_1."^".$val_1."^".get_user_by_id ($val_1)."^".'0'."^".'0'."^".'0'."^".''."^".''."^".''."^".''."^".$sumval_for_uid;

			}
			
						
			//PROCESS ARRAY MASTER SOLES INTO NEW ARRAY MASTER
					foreach($arr_master as $key_1=>$val_1) {
							if(substr($key_1,0,1) == '0') {					
									if ($key_1==$debug_rr) {
										zdebug("Temp String for Sole RR : ".$debug_rr, $val_1);	
									}
									$process_sole = explode("^",$val_1);

									$sumval_for_uid = 0;
									foreach($arr_master as $key_2=>$val_2) {
											if(substr($key_2,0,1) != '0') {					
													$process = explode("^",$val_2);
													if ($key_2==$debug_sr and $key_1 == $debug_rr) {
														zdebug("Showing array for shared rep $debug_sr", $val_2);	
														show_array($process);
													}
													$process_new = explode("#",$process[10]);

															if ($key_2==$debug_sr and $key_1 == $debug_rr) {
																zdebug("Showing array for shared rep $debug_sr", $val_2);	
																show_array($process_new);
															}
															foreach ($process_new as $key_3=>$val_3){
																	if ($val_3 != "") {
																			//zdebug($val_1.' val to process', $val_3);
																			$process_final = explode(">",$val_3);
																			if ($process_final[0] == $process_sole[1]) {
																					//$sumval_for_uid = $sumval_for_uid + $process_final[2];
																					$sumval_for_uid = $sumval_for_uid + $process_final[2];
																					//======================================================
																					//Changed the above line. It was picking up the wrong 
																					//value in the shared total for the rep. e.g. Colburn
																					//whho had two clients, one with special payout.
																					//======================================================
																			}
																	}
															} 
												}
									 }
								
								 //zdebug("Val for UID ".$process[1] , $sumval_for_uid);
					
								 $arr_master_new[$key_1] = $process_sole[0]."^".
																					$process_sole[1]."^".
																					$process_sole[2]."^".
																					$process_sole[3]."^".
																					$process_sole[4]."^".
																					$process_sole[5]."^".
																					$process_sole[6]."^".
																					$process_sole[7]."^".
																					$process_sole[8]."^".
																					$process_sole[9]."^".
																					''."^".
																					$sumval_for_uid;
									if ($key_1==$debug_uid or 1==1) {
										//zdebug("Final String for Sole RR : ".$debug_rr, $arr_master_new[$key_1]);	
									}

																												
							 }
					 }
						

			
			//PROCESS ARRAY MASTER SHARED INTO NEW ARRAY MASTER
					foreach($arr_master as $key_1=>$val_1) {
							if(substr($key_1,0,1) != '0') {					
							$process_shared = explode("^",$val_1);
								//echo "Processing REP ".$process_shared[0]."<br>";
								$arr_master_new[$key_1] = $process_shared[0]."^".
																					$process_shared[1]."^".
																					$process_shared[2]."^".
																					$process_shared[3]."^".
																					$process_shared[4]."^".
																					$process_shared[5]."^".
																					$process_shared[6]."^".
																					$process_shared[7]."^".
																					$process_shared[8]."^".
																					$process_shared[9];
							}
					}
			
			//show_array($arr_master_new);
			//include('pay_summ_gen_excel_inc_1.php');
			include('pay_nsumm_sdate_gen_excel_inc_1.php');
?>