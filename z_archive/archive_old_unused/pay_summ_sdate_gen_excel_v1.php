<link rel="stylesheet" type="text/css" href="includes/styles.css">
<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

include('pay_summ_functions.php');
 
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
//xdebug("str_label_payout_rate",$str_label_payout_rate);

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
//exit;

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
xdebug("Process initiated at :",date('m/d/Y H:i:s a'));
xdebug("Start Date",$brk_start_date);
xdebug("End Date",$brk_end_date);

//##############################################################################
//Based on the Date Selection, get the rolling 12 months start and end date.
$back_brk_year = $brk_year-1;
//xdebug("Previous Year", $back_brk_year);
$arr_back_brk_dates = get_commission_month_dates($brk_month,$back_brk_year);
$back_brk_start_date = $arr_back_brk_dates[0];
$back_brk_end_date = $arr_back_brk_dates[1];
//xdebug("back_brk_start_date",$back_brk_start_date);
//xdebug("back_brk_end_date",$back_brk_end_date); //This is the date to work with, trades
                                                //should be after this date.
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
//xdebug("qry_rolling12_checks",$qry_rolling12_checks);																															
$result_rolling12_checks = mysql_query($qry_rolling12_checks) or die (tdw_mysql_error($qry_rolling12_checks));
$arr_rolling12_checks = array();
while($row_rolling12_checks = mysql_fetch_array($result_rolling12_checks)) {
			$arr_rolling12_checks[$row_rolling12_checks["chek_advisor"]] = $row_rolling12_checks["chek_amount"];
}

//Assimmilating
$arr_combined = array();
foreach ($arr_rcc as $key=>$value) {
	//echo $value."<br>";
	$tmp_store = explode("^",$value);
	$new_amount = 0;
	foreach ($arr_rolling12_checks as $clnt=>$amt) {
			if ($clnt == $tmp_store[1]) {
				 	$new_amount = $tmp_store[2]+$amt;		
					//xdebug("Adding to comm data for ".$tmp_store[1],$amt);
			}
	}
	if ($new_amount != 0) {
		$arr_combined[] = $tmp_store[0]."^".$tmp_store[1]."^".$new_amount;
	} else {
		$arr_combined[] = $tmp_store[0]."^".$tmp_store[1]."^".$tmp_store[2];
	}
}

//now add checks only client to the combined list
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
$arr_rcc = $arr_combined;

//now merge the arrays
$arr_merged = array_merge($arr_rcc, $arr_delta);
//show_array($arr_rcc);	
//exit;
//now reassign the $arr_rcc which is below
$arr_rcc = array();
$arr_rcc = $arr_merged;

//xdebug("ADAG/030",$arr_nest_client["ADAG"]["030"]);	
//##############################################################################

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
									AND chek_date between '".$brk_start_date."' AND '".$brk_end_date."'
									GROUP BY chek_advisor 
									ORDER BY chek_advisor";
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
			//xdebug("query_trades",$query_trades);
			$result_trades = mysql_query($query_trades) or die (tdw_mysql_error($query_trades));

			$count_row_global = 4;
			while ($row_trades = mysql_fetch_array($result_trades) ) 
			{
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


							//DATA SECTION=============================================================================================							
							
							//reset $str_construct to null
							$str_construct = "";
							$process_userid = get_userid_for_rr ($row_trades["trad_rr"]);

							if (substr($row_trades["trad_rr"],0,1)!='0') {
							//xdebug("Processing Shared Rep: ", $row_trades["trad_rr"]);
							}
							
							//xdebug("process_userid",$process_userid);
							//start constructing the string
							$str_construct = $row_trades["trad_rr"];
							$str_construct = $str_construct."^".$process_userid;

							if ($process_userid == "") {
								$new_process_userid = get_user_id_for_shared_reps($row_trades["trad_rr"]);
							} else {
								$new_process_userid = $process_userid;
							}
							$str_construct = $str_construct."^".$new_process_userid;
														
							$str_construct = $str_construct."^".$display_name;
							$str_construct = $str_construct."^".$row_trades["for_sum_trad_commission"];

							$sum_rr_check = 0;
							foreach ($arr_rr_clnt_chk as $k=>$v) {
								$detail = explode("^",$v);
								if ($detail[0]==$row_trades["trad_rr"]) {
								 $sum_rr_check = $sum_rr_check + $detail[2]; 
								}
							}
							//xdebug("sum_rr_check",$sum_rr_check);
							$str_construct = $str_construct."^".$sum_rr_check;

							//NEW SECTION
							$qry_client_comm = "SELECT trad_advisor_code, 
																	sum(trad_commission) as clnt_comm 
																	FROM mry_comm_rr_trades 
																	WHERE trad_settle_date BETWEEN '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
																	AND trad_rr = '".$row_trades["trad_rr"]."'
																	AND trad_is_cancelled = 0
																	GROUP BY trad_advisor_code 
																	ORDER BY trad_advisor_code";
							//xdebug("qry_client_comm",$qry_client_comm);
							
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

							
							
												
							while ($row_client_comm = mysql_fetch_array($result_client_comm) ) 
							{
									$processed_client[] = $row_client_comm["trad_advisor_code"];

									$comm_total = $comm_total + $row_client_comm["clnt_comm"];

									//-----------------------------------------------------------------------------
									//xdebug ("checking client : ",$row_client_comm["trad_advisor_code"]);
									//Is there a check for this client?
									$hold_check_amount = 0;
									foreach ($arr_checks as $clnt=>$amt) {
									   if ($clnt == $row_client_comm["trad_advisor_code"]) {
										 	//echo "Client ".$clnt. " found with Check Amount = ". $amt."<br>";
											$comm_checks_total = $comm_checks_total + $arr_checks[$clnt];
											$hold_check_amount = $arr_checks[$clnt];
										 }
									}
									
									//-----------------------------------------------------------------------------
									//process includes special payout rates
									//CAPTURE DATA IN ARRAY IF SHARED REP, CAPTURE NUMBERS FOR CONSTITUENTS OF SHARED REP BY USERID
									//DATA INCONSISTENCY WITH AIMA
									$tmp_payout = 0;
									$tmp_payout_indiv = "";
									if (substr($row_trades["trad_rr"],0,1)=='0') {
											if (in_array($row_client_comm["trad_advisor_code"],$arr_sp_payout_clnt)) {
												$arr_get_sp_out = sp_payout_rate_alt($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout);
												//show_array($arr_get_sp_out);
												$str_special = $str_special . "  " . $arr_get_sp_out[0];
												$special_rate = $arr_get_sp_out[1]/100;
												//xdebug("clnt/special_rate/user_id",$row_client_comm["trad_advisor_code"]."/".$special_rate."/".$process_userid);					
												$comm_special_total = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate ); 
												$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate );
											} else {
												$comm_standard_total = $comm_standard_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier ); 
												$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier );
											}
									} else {
											if (in_array($row_client_comm["trad_advisor_code"],$arr_sp_payout_clnt)) {
												$arr_get_sp_out = sp_payout_rate_alt($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout);
												//show_array($arr_get_sp_out);
												$str_special = $str_special . "  " . $arr_get_sp_out[0];
												$special_rate = $arr_get_sp_out[1]/100;
												//xdebug("clnt/special_rate/user_id",$row_client_comm["trad_advisor_code"]."/".$special_rate."/".$process_userid);					
												$comm_special_total = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate ); 
												$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate );
												//@#@#
												//Get the payout rates for the individuals and apply to the totals for 'em
												//xdebug('Processing individuals for Shared:',$arr_sp_payout[$row_client_comm["trad_advisor_code"]]);
												$arr_each_rep = explode("#",$arr_sp_payout[$row_client_comm["trad_advisor_code"]]);	
												$arr_rr1 = explode("^",$arr_each_rep[0]);
												$arr_rr2 = explode("^",$arr_each_rep[1]);
												//show_array($arr_rr1);
												//show_array($arr_rr2);
												$comm_special_total_rr1 = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr1[1]/100) ); 
												$tmp_payout_rr1 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr1[1]/100) );
												$comm_special_total_rr2 = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr2[1]/100) ); 
												$tmp_payout_rr2 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * ($arr_rr2[1]/100) );
												$tmp_payout_indiv = $arr_rr1[0].">".$comm_special_total_rr1.">".$tmp_payout_rr1."#".$arr_rr2[0].">".$comm_special_total_rr2.">".$tmp_payout_rr2."#".$tmp_payout_indiv;
												//xdebug("tmp_payout_indiv",$tmp_payout_indiv);
												//@#@#
											} else {
												$comm_standard_total = $comm_standard_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier ); 
												$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier );
												//%^%^
												//xdebug("shared rep",$row_trades["trad_rr"]);
												$str_userid = get_user_id_for_shared_reps ($row_trades["trad_rr"]);
												////xdebug("str_userid",$str_userid);
												$arr_userid = explode("#",$str_userid);
												//show_array($arr_userid);
												//xdebug('Processing individuals for Shared:',$str_userid);
												$arr_rr1 = explode("|",$arr_userid[0]);
												$arr_rr2 = explode("|",$arr_userid[1]);
												//show_array($arr_rr1);
												//show_array($arr_rr2);
												$comm_special_total_rr1 = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 ); 
												$tmp_payout_rr1 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 );
												$comm_special_total_rr2 = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 ); 
												$tmp_payout_rr2 = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier * 0.5 );
												$tmp_payout_indiv = $arr_rr1[0].">".$comm_special_total_rr1.">".$tmp_payout_rr1."#".$arr_rr2[0].">".$comm_special_total_rr2.">".$tmp_payout_rr2."#".$tmp_payout_indiv;
												//xdebug("tmp_payout_indiv",$tmp_payout_indiv);
												//%^%^
											}
									}
									//-----------------------------------------------------------------------------

									//get rolling 12 months data
									//CAPTURE DATA FOR CONSTITUENTS OF SHARED REP WHERE APPLICABLE (NOT DONE YET)
									if (substr($row_trades["trad_rr"],0,1)=='0') {
												if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
													$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
													////xdebug("rolling_total".$row_client_comm["trad_advisor_code"],$rolling_total);					
												}
												if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
													//do nothing
												} else {
													$comm_less_than_cutoff = $comm_less_than_cutoff + ($tmp_payout * (-1));
												}
									} else {
												if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
													$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
													//xdebug("rolling_total".$row_client_comm["trad_advisor_code"],$rolling_total);					
												}
												if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
													//do nothing
												} else {
													$comm_less_than_cutoff = $comm_less_than_cutoff + ($tmp_payout * (-1));
												}
									}
									//-----------------------------------------------------------------------------
									
							}
							
							
							//CONSIDER SHARED REPS FOR CASE BELOW AND PROCESS ACCORDINGLY	 (NOT DONE YET)					
							//find more clients (checks) which are not processed above
							foreach ($arr_checks_rr as $clnt=>$rr) {
								 if (!in_array($clnt,$processed_client) AND $rr == $row_trades["trad_rr"]) {
											if ($arr_clients[$clnt] == '') {
												$clnt_name = "[".$clnt."]";
											} else {
												$clnt_name = $arr_clients[$clnt];
											}
											
									$comm_total = $comm_total + $arr_checks[$clnt];

									$tmp_payout1 = 0;
									if (in_array($clnt,$arr_sp_payout_clnt)) {
									  $arr_get_sp_out = sp_payout_rate_alt($clnt, $process_userid, $arr_sp_payout);
										$str_special = $str_special . "  " . $arr_get_sp_out[0];
										$special_rate = $arr_get_sp_out[1]/100;

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
							//xdebug("S/Sp/Cutoff",$comm_standard_total."/".$comm_special_total."/".$comm_less_than_cutoff);
							$str_construct = $str_construct."^".$comm_standard_total;
							$str_construct = $str_construct."^".$str_special;
							$str_construct = $str_construct."^".$comm_special_total;
							$str_construct = $str_construct."^".$comm_less_than_cutoff;

							$str_construct = $str_construct."^".$tmp_payout_indiv;

							$arr_master[$row_trades["trad_rr"]] = $str_construct;

							//END DATA SECTION=============================================================================================							
							$count_row_global = $count_row_global + 1;
					}
			//end main							
			}


			//RE-PROCESS ARRAY
			$existing_uids = array();
			foreach($arr_master as $k=>$v) {
				if (substr($k,0,1) == '0') {
					//capture uids in array
					$existing_uids[] = get_userid_for_rr ($k);
				}	
			}
			
			//show_array($existing_uids);
			
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
					//xdebug("uid_1",$uid_1);
					//xdebug("uid_2",$uid_2);
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
			}

			//show_array($nonexisting_uids);
			
			$arr_complete_sole = array_merge($existing_uids, $nonexisting_uids);
			//show_array($arr_complete_sole);
			
			//NEW MASTER ARRAY
			$arr_master_new = array();
			
			//create an array entry for nonexisting UIDs in the master
			foreach ($nonexisting_uids as $key_1=>$val_1) {
					//echo "Processing UID ".$val_1."<br>";
					$sumval_for_uid = 0;
					foreach($arr_master as $key_2=>$val_2) {
							if(substr($key_2,0,1) != '0') {					
							//xdebug("val_2", $val_2);
							$process = explode("^",$val_2);
							$process_new = explode("#",$process[10]);
									foreach ($process_new as $key_3=>$val_3){
											if ($val_3 != "") {
  												//xdebug($val_1.' val to process', $val_3);
													$process_final = explode(">",$val_3);
															if ($process_final[0] == $val_1) {
																	$sumval_for_uid = $sumval_for_uid + $process_final[2];
															}
											}
									} 
							}
					}
					
					//xdebug("Val for UID ".$val_1 , $sumval_for_uid);
					
					$arr_master_new[get_rr_num($val_1)] = get_rr_num($val_1)."^".$val_1."^".$val_1."^".get_user_by_id ($val_1)."^".'0'."^".'0'."^".'0'."^".''."^".''."^".''."^".''."^".$sumval_for_uid;

			}
			
						
			//PROCESS ARRAY MASTER SOLES INTO NEW ARRAY MASTER
					foreach($arr_master as $key_1=>$val_1) {
							if(substr($key_1,0,1) == '0') {					
							//xdebug("val_2", $val_2);
							$process_sole = explode("^",$val_1);
								//echo "Processing UID ".$process_sole[1]."<br>";
								$sumval_for_uid = 0;
								foreach($arr_master as $key_2=>$val_2) {
										if(substr($key_2,0,1) != '0') {					
										//xdebug("val_2", $val_2);
										$process = explode("^",$val_2);
										$process_new = explode("#",$process[10]);
												foreach ($process_new as $key_3=>$val_3){
														if ($val_3 != "") {
																//xdebug($val_1.' val to process', $val_3);
																$process_final = explode(">",$val_3);
																		if ($process_final[0] == $process_sole[1]) {
																				$sumval_for_uid = $sumval_for_uid + $process_final[2];
																		}
														}
												} 
										}
								}
								
								//xdebug("Val for UID ".$process[1] , $sumval_for_uid);
					
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

			//put these values in temp db table
			$qry = "truncate table mry_tmp_process";
			$result_truncate = mysql_query($qry) or die (tdw_mysql_error($qry));
			
			foreach($arr_complete_sole as $k=>$v) {
				$qry = "insert into mry_tmp_process(val) values('".$v."')";
				$result_insert = mysql_query($qry) or die (tdw_mysql_error($qry));
			}

			$final_user_processed = array();
  		//CAPTURE SUBTOTAL ROW IN ARRAY TO GET GRAND TOTAL
			$arr_subtotal_row = array();

			//SECTION 1 START

			$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
										FROM Users a, mry_tmp_process b
										WHERE a.ID = b.val
										AND (a.Role = 3 or a.Role = 5)
										AND (a.ID != 288)
										ORDER BY Lastname, Role";
			$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));

			$count_row_xls = 3;

			while ($row_users = mysql_fetch_array($result_users) )  {
					//THIS SECTION TAKES THE MASTER ARRAY AND WRITE TO EXCEL
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if ($data[1] == $row_users["ID"]) {
							
									$final_user_processed[] = $row_users["ID"];
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, $row_users["lastname"]);

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			
			}


									//SECTION 1 SUBSECTION START

									$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
																FROM Users a, mry_tmp_process b
																WHERE a.ID = b.val
																AND (a.Role != 3 AND a.Role != 5 AND a.Role != 4)
																AND (a.ID != 288)
																ORDER BY Lastname, Role";
									$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));
												
									while ($row_users = mysql_fetch_array($result_users) )  {
											//THIS SECTION TAKES THE MASTER ARRAY AND WRITE TO EXCEL
											foreach($arr_master_new as $k=>$v) {
												$data = explode("^",$v);
													if ($data[1] == $row_users["ID"]) {
													
															$final_user_processed[] = $row_users["ID"];
															//show_array($data);
															$wks->writeString($count_row_xls, 2, $data[0]);
															$wks->write($count_row_xls, 3, $data[3],$format_data_3);
															$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
															
															if ($data[5] != 0) {
																$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
															}
															
															$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
															$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
															if ($data[8] > 0){
																$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
															}
															
															if ($data[9]){
																$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
															}
															
															$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																																		 "+".$arr_xl_cols[11].($count_row_xls+1).
																																		 "+".$arr_xl_cols[13].($count_row_xls+1).
																																		 "+".$arr_xl_cols[14].($count_row_xls+1).
																																		 "+".$arr_xl_cols[15].($count_row_xls+1).
																																		 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
															if ($data[11] != 0) {
																$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
															}
															$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
															$wks->writeString($count_row_xls, 26, $row_users["lastname"]);
						
															$count_row_xls = $count_row_xls + 1;
													}
								
											}			
									}

									
									//SECTION 1 SUBSECTION END

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5]."4".":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6]."4".":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8]."4".":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10]."4".":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11]."4".":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13]."4".":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19]."4".":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20]."4".":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21]."4".":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls = $count_row_xls;


			//SECTION 1 END
			
									
			//SECTION 2 START
			$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
										FROM Users a, mry_tmp_process b
										WHERE a.ID = b.val
										AND (a.Role = 4)
										AND (a.ID != 288)
										ORDER BY Lastname, Role";
			$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));

			while ($row_users = mysql_fetch_array($result_users) )  {
					//THIS SECTION TAKES THE MASTER ARRAY AND WRITE TO EXCEL
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if ($data[1] == $row_users["ID"]) {
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, $row_users["lastname"]);

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			
			}

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5].$hold_count_row_xls.":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6].$hold_count_row_xls.":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8].$hold_count_row_xls.":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10].$hold_count_row_xls.":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11].$hold_count_row_xls.":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13].$hold_count_row_xls.":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19].$hold_count_row_xls.":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20].$hold_count_row_xls.":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21].$hold_count_row_xls.":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls_new = $count_row_xls;

			//SECTION 2 END
			
			//SECTION 3 BEGIN
			$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
										FROM Users a, mry_tmp_process b
										WHERE a.ID = b.val
										AND (a.ID = 288)
										ORDER BY Lastname, Role";
			$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));

			while ($row_users = mysql_fetch_array($result_users) )  {
					//THIS SECTION TAKES THE MASTER ARRAY AND WRITE TO EXCEL
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if ($data[1] == $row_users["ID"]) {
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, $row_users["lastname"]);

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			
			}

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5].$hold_count_row_xls_new.":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6].$hold_count_row_xls_new.":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8].$hold_count_row_xls_new.":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10].$hold_count_row_xls_new.":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11].$hold_count_row_xls_new.":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13].$hold_count_row_xls_new.":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19].$hold_count_row_xls_new.":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20].$hold_count_row_xls_new.":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21].$hold_count_row_xls_new.":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls_new_2 = $count_row_xls;
			
			//SECTION 3 END
			
			//SECTION 4 BEGIN
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if (substr($data[0],0,1) != '0') {
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									//$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, "");

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5].$hold_count_row_xls_new_2.":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6].$hold_count_row_xls_new_2.":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8].$hold_count_row_xls_new_2.":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10].$hold_count_row_xls_new_2.":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11].$hold_count_row_xls_new_2.":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13].$hold_count_row_xls_new_2.":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19].$hold_count_row_xls_new_2.":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									//$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20].$hold_count_row_xls_new_2.":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									//$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21].$hold_count_row_xls_new_2.":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls_new_3 = $count_row_xls;
			
			//SECTION 4 END
			
			
			//GRAND TOTAL LINE
			
			$wks->write($hold_count_row_xls_new_3, 3, "Grand Total", $format_data_3);
			$wks->writeFormula($hold_count_row_xls_new_3, 5,  "=".$arr_xl_cols[5].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[5].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[5].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[5].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 6,  "=".$arr_xl_cols[6].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[6].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[6].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[6].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 8,  "=".$arr_xl_cols[8].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[8].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[8].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[8].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 10,  "=".$arr_xl_cols[10].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[10].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[10].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[10].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 11,  "=".$arr_xl_cols[11].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[11].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[11].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[11].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 13,  "=".$arr_xl_cols[13].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[13].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[13].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[13].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 19,  "=".$arr_xl_cols[19].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[19].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[19].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[19].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 20,  "=".$arr_xl_cols[20].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[20].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[20].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[20].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 21,  "=".$arr_xl_cols[21].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[21].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[21].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[21].($arr_subtotal_row[3]+1), $format_currency_2);
																												
			




			$wks->printArea(0,0,$hold_count_row_xls_new_3+2,26);
			$wks->fitToPages(1,1);
			

// We still need to explicitly close the workbook
$wkb->close();
//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
	echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<p class="ilt">Following is the preformatting for printing the Summary Report<br />
- LEGAL<br />
- Landscape<br />
- 1 Page Wide by 1 Page Tall 
<br />
Should you want to print in a format other than this, please use Page Setup in Excel to get the desired print output.<br /></p>
<a href="http://192.168.20.63/tdw/data/xls/<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br /><br />
<?
xdebug("Process completed at :",date('m/d/Y H:i:s a'));
//echo "RR^NAME^TOTALCOMM^TOTALCHECKS^STANDARDPAY^RATE^SPECIALPAY^ROLLING12MON"."<br>";
//show_array($arr_master);
//show_array($arr_sp_payout);
//show_array(sp_payout_rate_alt('AIMA', '', $arr_sp_payout));
?>