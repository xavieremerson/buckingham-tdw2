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

$percent_payout_comm = 19;
$sel_month = "Feb^2007";

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
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

			//FORMATTING IN THE FOLLOWING FILE
			include('pay_summ_gen_excel_format.php');

			$wks =& $wkb->addWorksheet("Payout Summary");
			$wks->setLandscape ();
			$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);

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
							if ($process_userid == "") {
								$process_userid = get_user_id_for_shared_reps($row_trades["trad_rr"]);
							}
							
							//xdebug("process_userid",$process_userid);
							
							$wks->writeString($count_row_global, 2, $row_trades["trad_rr"]);
							//start constructing the string
							//$str_construct = $str_construct."^".$row_trades["trad_rr"];
							$str_construct = $row_trades["trad_rr"];
							$str_construct = $str_construct."^".$process_userid;

							$wks->write($count_row_global, 3, $display_name,$format_data_3);
							$str_construct = $str_construct."^".$display_name;

							$wks->writeNumber($count_row_global, 7, $row_trades["for_sum_trad_commission"],$format_currency_1);
							$str_construct = $str_construct."^".$row_trades["for_sum_trad_commission"];
							//$wks->writeRow(4,0," ");
							$sum_rr_check = 0;
							foreach ($arr_rr_clnt_chk as $k=>$v) {
								$detail = explode("^",$v);
								if ($detail[0]==$row_trades["trad_rr"]) {
								 $sum_rr_check = $sum_rr_check + $detail[2]; 
								}
							}
							//xdebug("sum_rr_check",$sum_rr_check);
							$wks->writeNumber($count_row_global, 8, $sum_rr_check, $format_currency_1);
							$str_construct = $str_construct."^".$sum_rr_check;

							$wks->writeFormula($count_row_global, 10, '='.$arr_xl_cols[7].($count_row_global+1)."+".$arr_xl_cols[8].($count_row_global+1) ,$format_currency_1);
							//$str_construct = $str_construct."^".'='.$arr_xl_cols[7].($count_row_global+1)."+".$arr_xl_cols[8].($count_row_global+1);
							
							//NEW SECTION
							$qry_client_comm = "SELECT trad_advisor_code, max(trad_advisor_name) as clnt_name , 
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
									
									$comm_total = $comm_total + $row_client_comm["clnt_comm"];

									//-----------------------------------------------------------------------------
									//xdebug ("checking client : ",$row_client_comm["trad_advisor_code"]);
									//Is there a check for this client?
									$hold_check_amount = 0;
									foreach ($arr_checks as $clnt=>$amt) {
									   if ($clnt == $row_client_comm["trad_advisor_code"]) {
										 	//echo "Client ".$clnt. " found with Check Amount = ". $amt."<br>";
											$comm_checks_total = $comm_checks_total + $arr_checks[$row_client_comm["trad_advisor_code"]];
											$hold_check_amount = $arr_checks[$row_client_comm["trad_advisor_code"]];
										 }
									}
									
									//-----------------------------------------------------------------------------
									//process includes special payout rates
									$tmp_payout = 0;
									if (in_array($row_client_comm["trad_advisor_code"],$arr_sp_payout_clnt)) {
									  $arr_get_sp_out = sp_payout_rate_alt($row_client_comm["trad_advisor_code"], $process_userid, $arr_sp_payout);
										$str_special = $str_special . "  " . $arr_get_sp_out[0];
										$special_rate = $arr_get_sp_out[1]/100;
										//xdebug("special_rate",$special_rate);					
										$comm_special_total = $comm_special_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate ); 
										$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $special_rate );
									} else {
										$comm_standard_total = $comm_standard_total + ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier ); 
										$tmp_payout = ( ($row_client_comm["clnt_comm"] + $hold_check_amount) * $payout_multiplier );
									}
									//-----------------------------------------------------------------------------

									//get rolling 12 months data
									if (in_array($row_client_comm["trad_advisor_code"],$arr_nest_client_list)) {
										$rolling_total = get_rcc ($arr_rcc, $row_trades["trad_rr"], $row_client_comm["trad_advisor_code"]);
										//xdebug("rolling_total".$row_client_comm["trad_advisor_code"],$rolling_total);					
									}
									if ($rolling_total > 15000 or in_array($row_client_comm["trad_advisor_code"],$arr_clients_cutoff_exceptions)) {
										//do nothing
									} else {
										$comm_less_than_cutoff = $comm_less_than_cutoff + ($tmp_payout * (-1));
									}
									//-----------------------------------------------------------------------------
									
							}
							
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
							$wks->writeNumber($count_row_global, 12, $comm_standard_total, $format_currency_1);
							$str_construct = $str_construct."^".$comm_standard_total;

							$wks->writeString($count_row_global, 13, $str_special,$format_data_4);
							$str_construct = $str_construct."^".$str_special;

							$wks->writeNumber($count_row_global, 14, $comm_special_total, $format_currency_1);
							$str_construct = $str_construct."^".$comm_special_total;


							$wks->writeNumber($count_row_global, 16, $comm_less_than_cutoff, $format_currency_1);
							$str_construct = $str_construct."^".$comm_less_than_cutoff;

							
							$wks->writeFormula($count_row_global, 21, "=".$arr_xl_cols[12].($count_row_global+1)."+".$arr_xl_cols[14].($count_row_global+1)."+".$arr_xl_cols[16].($count_row_global+1), $format_currency_1);
							//$str_construct = $str_construct."^"."=".$arr_xl_cols[12].($count_row_global+1)."+".$arr_xl_cols[14].($count_row_global+1)."+".$arr_xl_cols[16].($count_row_global+1);

							$arr_master[$row_trades["trad_rr"]] = $str_construct;

							//END DATA SECTION=============================================================================================							
							$count_row_global = $count_row_global + 1;
					}
			//end main							
			}

			//WRITING GRAND TOTALS
			$wks->writeFormula($count_row_global+1, 6, "=SUM(".$arr_xl_cols[6]."5".":".$arr_xl_cols[6].($count_row_global).")", $format_currency_4b);
			$wks->writeFormula($count_row_global+1, 7, "=SUM(".$arr_xl_cols[7]."5".":".$arr_xl_cols[7].($count_row_global).")", $format_currency_2);
			$wks->writeFormula($count_row_global+1, 8, "=SUM(".$arr_xl_cols[8]."5".":".$arr_xl_cols[8].($count_row_global).")", $format_currency_2);
			$wks->writeFormula($count_row_global+1, 10, "=SUM(".$arr_xl_cols[10]."5".":".$arr_xl_cols[10].($count_row_global).")", $format_currency_2);
			$wks->writeFormula($count_row_global+1, 12, "=SUM(".$arr_xl_cols[12]."5".":".$arr_xl_cols[12].($count_row_global).")", $format_currency_2);
			$wks->writeFormula($count_row_global+1, 14, "=SUM(".$arr_xl_cols[14]."5".":".$arr_xl_cols[14].($count_row_global).")", $format_currency_2);
			$wks->writeFormula($count_row_global+1, 16, "=SUM(".$arr_xl_cols[16]."5".":".$arr_xl_cols[16].($count_row_global).")", $format_currency_2);
			$wks->writeFormula($count_row_global+1, 21, "=SUM(".$arr_xl_cols[21]."5".":".$arr_xl_cols[21].($count_row_global).")", $format_currency_2);


			$wks->printArea(0,0,$count_row_global+2,23);
			$wks->fitToPages(1,1);
			

// We still need to explicitly close the workbook
$wkb->close();
//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
	echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<p class="ilt">The Summary Report has been formatted to print on Letter, Landscape and 1 Page Wide format.<br />
Should you want to print in a format other than this, please use Page Setup in Excel to get the desired print output.<br /></p>
<a href="http://192.168.20.63/tdw/data/xls/<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br /><br />
<?
xdebug("Process completed at :",date('m/d/Y H:i:s a'));
echo "RR^NAME^TOTALCOMM^TOTALCHECKS^STANDARDPAY^RATE^SPECIALPAY^ROLLING12MON"."<br>";
show_array($arr_master);
?>