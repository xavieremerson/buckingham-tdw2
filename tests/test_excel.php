<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

$brk_month = 'Jan';
$brk_year = '2007';

//initiate page load time routine
$time=getmicrotime(); 

////
//function get user_id from rr_num
function get_userid_for_rr ($rr_num) {
	$qry_userid = "SELECT 
									ID 
									FROM users 
								WHERE rr_num = '".$rr_num."'";   
	$result_userid = mysql_query($qry_userid) or die(tdw_mysql_error($qry_userid));
	while($row_userid = mysql_fetch_array($result_userid)) {
		$user_id = $row_userid["ID"];
	}
	return $user_id;
}

//function get user_id from Initials
function get_userid_for_initials ($Initials) {
	$qry_userid = "SELECT 
									ID 
									FROM users 
								WHERE Initials = '".$Initials."'";   
	$result_userid = mysql_query($qry_userid) or die(tdw_mysql_error($qry_userid));
	while($row_userid = mysql_fetch_array($result_userid)) {
		$user_id = $row_userid["ID"];
	}
	return $user_id;
}

//function get sole rr_num from ID
function get_rr_num ($ID) {
	$qry_rr_num = "SELECT 
									rr_num 
									FROM users 
								WHERE ID = '".$ID."'";   
	$result_rr_num = mysql_query($qry_rr_num) or die(tdw_mysql_error($qry_rr_num));
	while($row_rr_num = mysql_fetch_array($result_rr_num)) {
		$rr_num = $row_rr_num["rr_num"];
	}
	return $rr_num;
}

//function get shared rr_num from client
function get_shared_rr_num ($intial_a, $initial_b) {
	$userid_a = get_userid_for_initials($intial_a);
	$userid_b = get_userid_for_initials($initial_b);
	$qry_shared_rr_num = "SELECT trim(srep_rrnum) as srep_rrnum 
												FROM sls_sales_reps
												WHERE srep_user_id ='".$userid_a."'
												AND srep_rrnum
												IN (
												SELECT trim(srep_rrnum) 
												FROM sls_sales_reps
												WHERE 
													srep_isactive = 1 
													AND srep_user_id ='".$userid_a."')";   
	$result_shared_rr_num = mysql_query($qry_shared_rr_num) or die(tdw_mysql_error($qry_shared_rr_num));
	while($row_shared_rr_num = mysql_fetch_array($result_shared_rr_num)) {
		$shared_rr_num = $row_shared_rr_num["srep_rrnum"];
	}
	return $shared_rr_num;
}

////
//Get dates for the selected brokerage month
$arr_brk_dates = get_commission_month_dates($brk_month,$brk_year);
$brk_start_date = $arr_brk_dates[0];
$brk_end_date = $arr_brk_dates[1];
xdebug("Process initiated at :",date('m/d/Y H:i:s a'));
xdebug("Start Date",$brk_start_date);
xdebug("End Date",$brk_end_date);

////
//Array of checks data for use within reps
			$arr_checks = array();
			$arr_checks_rr = array();
			$query_checks = "SELECT 
													chek_advisor,
													FORMAT(sum(chek_amount),2) as chek_amount,
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
							} elseif ($val_rr1 != '' AND $val_rr2 != ''){
							 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Shared Client<br>";
							 $arr_checks_rr[$row_checks["chek_advisor"]] = get_shared_rr_num ($val_rr1, $val_rr2);
							} else {
							 //echo $row_checks["chek_advisor"]." ".$val_rr1." ".$val_rr2." Non trading account<br>";
							}
						}
			 
			}
			show_array($arr_checks);
			show_array($arr_checks_rr);

////
// Function to get Name of Rep
function get_repname_by_rr_num ($rr_num) {
	if (substr($rr_num,0,1) == '0') {
		$qry_name = "SELECT Fullname from Users where rr_num = '".$rr_num."'";   
		$result_name = mysql_query($qry_name) or die(tdw_mysql_error($qry_name));
		while($row_name = mysql_fetch_array($result_name)) {
			$rr_name = $row_name["Fullname"];
		}
		return $rr_name;
	} else {
		$qry_id = "SELECT srep_user_id, srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_rrnum = '".$rr_num."'";
		//xdebug("qry_id",$qry_id);   
		$result_id = mysql_query($qry_id) or die(tdw_mysql_error($qry_id));
		while($row_id = mysql_fetch_array($result_id)) {
			$rr_id = $row_id["srep_user_id"];
			$qry_name = "SELECT Lastname from Users where ID = '".$rr_id."'";   
			//xdebug("qry_name",$qry_name);   
			$result_name = mysql_query($qry_name) or die(tdw_mysql_error($qry_name));
			while($row_name = mysql_fetch_array($result_name)) {
				$rr_name = $row_name["Lastname"];
				$out_rr_names = $rr_name . "/" .$out_rr_names;
			}
			if (substr($out_rr_names,strlen($out_rr_names)-1,strlen($out_rr_names)) == "/") {
			$out_rr_names = substr($out_rr_names,0,strlen($out_rr_names)-1);
			}
		}
		return $out_rr_names;
	}
}



//We give the path to our file here

//generate a random filename
$xlfilename = date('Y-m-d_h.i.s.a')."__".md5(rand(1000000000,9999999999)).".xls";
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

$format_bold =& $wkb->addFormat();
$format_bold->setBold();

$format_heading =& $wkb->addFormat();
$format_heading->setBold();

/*
$format_title_1 =& $wkb->addFormat();
$format_title_1->setBold();
$format_title_1->setPattern(0);
$format_title_1->setFontFamily('Arial'); 
//$format_title_1->setTop(1); // Top border 
//$format_title_1->setBottom(1); // Bottom border 
$format_title_1->setSize('9');
$format_title_1->setBold(); 
//$format_title_1->setColor('red'); 
// And since our title is going to be so big, we'll merge a few cells to account for it.<br />
//$format_title_1->setAlign('merge');
$format_title->setAlign('center');
*/

$format_title_1 =& $wkb->addFormat();
$format_title_1->setBold();
$format_title_1->setPattern(0);
$format_title_1->setFontFamily('Arial'); 
$format_title_1->setSize('14');

$format_title_2 =& $wkb->addFormat();
$format_title_2->setBold();
$format_title_2->setPattern(0);
$format_title_2->setFontFamily('Arial'); 
$format_title_2->setSize('9');
$format_title_2->setAlign('center');


$format_title_3 =& $wkb->addFormat();
$format_title_3->setBold();
$format_title_3->setPattern(0);
$format_title_3->setFontFamily('Arial'); 
$format_title_3->setSize('8');
$format_title_3->setAlign('center');

$format_data_1 =& $wkb->addFormat();
$format_data_1->setPattern(0);
$format_data_1->setFontFamily('Arial Narrow'); 
$format_data_1->setSize('9');

$format_data_2 =& $wkb->addFormat();
$format_data_2->setPattern(0);
$format_data_2->setFontFamily('Arial'); 
$format_data_2->setSize('9');
$format_data_2->setAlign('right');

$format_data_3 =& $wkb->addFormat();
$format_data_3->setBold();
$format_data_3->setPattern(0);
$format_data_3->setFontFamily('Arial'); 
$format_data_3->setSize('9');

$format_currency_1 =& $wkb->addFormat();
$format_currency_1->setPattern(0);
$format_currency_1->setFontFamily('Arial'); 
$format_currency_1->setSize('9');
$format_currency_1->setNumFormat('#,###.00;(#,###.00)');



			$query_trades = "SELECT 
													trad_rr,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$brk_start_date."' AND '".$brk_end_date."'
												AND trad_rr like '0%'												
												GROUP BY trad_rr 
												ORDER BY trad_rr";
			//xdebug("query_trades",$query_trades);
			//exit;
			
			$result_trades = mysql_query($query_trades) or die (tdw_mysql_error($query_trades));

			while ($row_trades = mysql_fetch_array($result_trades) ) 
			{

					$wks =& $wkb->addWorksheet(get_repname_by_rr_num($row_trades["trad_rr"]));
					// Couple of empty cells to make it look better
					$wks->write(0, 1, "", $format_title);
					$wks->write(1, 1, "", $format_title);
					
					
					$wks->setColumn(0, 0, 4);
					$wks->setColumn(1, 1, 0.2);
					$wks->setColumn(2, 2, 4);
					$wks->setColumn(3, 3, 4);
					$wks->setColumn(4, 4, 25);
					$wks->setColumn(5, 5, 0.2);
					$wks->setColumn(6, 9, 11);
					$wks->setColumn(10, 10, 0.2);
					$wks->setColumn(11, 11, 11);
					$wks->setColumn(12, 12, 8);
					$wks->setColumn(13, 13, 11);
					$wks->setColumn(14, 14, 0.2);
					$wks->setColumn(15, 17, 11);
					$wks->setColumn(18, 18, 8);
					$wks->setColumn(19, 19, 0.2);
					$wks->setColumn(20, 20, 11);
					$wks->setColumn(21, 21, 0.2);
					$wks->setColumn(22, 22, 11);
					
					$wks->write(0, 2, "February 2007", $format_title_1);
					$wks->mergeCells(0,2,0,4);
					$wks->write(0, 11, "Gross Payout", $format_title_2);
					$wks->mergeCells(0,11,0,13);
					$wks->write(1, 2, "TYPE", $format_title_2);
					$wks->write(1, 3, "REP", $format_title_2);
					$wks->write(1, 4, "NAME", $format_title_2);
					$wks->write(1, 11, "Standard", $format_title_2);
					$wks->write(1, 12, "Special", $format_title_2);
					$wks->write(1, 15, "Adjustments", $format_title_2);
					$wks->mergeCells(1,15,1,18);
					$wks->write(1, 20, "Pay Out For:", $format_title_2);
					$wks->write(1, 22, "Pay Out For:", $format_title_2);
					$wks->write(2, 6, "Sole???", $format_title_3);
					$wks->write(2, 7, "Other", $format_title_3);
					$wks->write(2, 8, "Checks", $format_title_3);
					$wks->write(2, 9, "Total", $format_title_3);
					$wks->write(2, 11, "19%/9.5%", $format_title_3);
					$wks->write(2, 12, "Rate", $format_title_3);
					$wks->write(2, 13, "Amount", $format_title_3);
					$wks->write(2, 15, "Rolling 12 mos.", $format_title_3);
					$wks->write(2, 16, "TW", $format_title_3);
					$wks->write(2, 17, "Other", $format_title_3);
					$wks->write(2, 18, "FootNote#", $format_title_3);
					$wks->write(2, 20, "February 2007", $format_title_2);
					$wks->write(2, 22, "YTD 2007", $format_title_2);

					$wks->write(3, 2, "Sole");
					$wks->writeString(3, 3, ' '.$row_trades["trad_rr"].' ');
					$wks->write(3, 4, get_repname_by_rr_num($row_trades["trad_rr"]),$format_data_3);
					$wks->writeNumber(3, 6, $row_trades["for_sum_trad_commission"],$format_currency_1);
					$wks->writeRow(4,0," ");

					$qry_client_comm = "SELECT trad_advisor_code, max(trad_advisor_name) as clnt_name , 
															sum(trad_commission) as clnt_comm 
															FROM mry_comm_rr_trades 
															WHERE trad_trade_date BETWEEN '".$brk_start_date."' AND '".$brk_end_date."'
															AND trad_rr = '".$row_trades["trad_rr"]."'
															AND trad_is_cancelled != 1
															GROUP BY trad_advisor_code 
															ORDER BY trad_advisor_code";
					$result_client_comm = mysql_query($qry_client_comm) or die (tdw_mysql_error($qry_client_comm));
					$count_row_i = 5;
					while ($row_client_comm = mysql_fetch_array($result_client_comm) ) 
					{
					$wks->write($count_row_i, 4, $row_client_comm["clnt_name"],$format_data_1);
					$wks->writeNumber($count_row_i, 6, $row_client_comm["clnt_comm"],$format_currency_1);
					
						if (in_array()) {
						
						}
					$count_row_i = $count_row_i + 1;
					}

					//$wks->writeRow($count_row_i+2,0,"x ");
					
					 //Now get the shared reps for the primary rep
						$query_userid = "SELECT ID from users where rr_num = '".$row_trades["trad_rr"]."'";
						$result_userid = mysql_query($query_userid) or die (tdw_mysql_error($query_userid));
							while ($row_userid = mysql_fetch_array($result_userid) ) {
							$user_id = $row_userid["ID"];
							}
							$query_reps_shared = "SELECT srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_user_id = '".$user_id."' AND srep_rrnum != ''";
							//xdebug("user_id",$user_id);
							$result_reps_shared = mysql_query($query_reps_shared) or die (tdw_mysql_error($query_reps_shared));
								$str_reps_shared = "";
								while ($row_reps_shared = mysql_fetch_array($result_reps_shared) ) {
								$str_reps_shared = $row_reps_shared["srep_rrnum"]."|". $str_reps_shared;
								}
								//xdebug("str_reps_shared",$str_reps_shared);
								
								//Create the SQL String
								$arr_shared_reps = explode("|", $str_reps_shared);
								 
								$str_sql_clause = '';
								foreach($arr_shared_reps as $key=>$value) {
									if ($value != '') {
									$str_sql_clause .= " OR trad_rr = '".$value."'";
									}
								}
								//xdebug("str_sql_clause",$str_sql_clause);
								$query_trades_shared = "SELECT 
																					trad_rr,
																					FORMAT(sum(trad_commission),2) as trad_commission,
																					sum(trad_commission) as for_sum_trad_commission
																				FROM mry_comm_rr_trades 
																				WHERE trad_is_cancelled != 1 
																				AND trad_trade_date between '".$brk_start_date."' AND '".$brk_end_date."'
																				AND trad_rr not like '0%'
																				AND (trad_rr = '1234567890' ".$str_sql_clause.")  												
																				GROUP BY trad_rr 
																				ORDER BY trad_rr";
								//xdebug("query_trades_shared",$query_trades_shared);
								$result_trades_shared = mysql_query($query_trades_shared) or die (tdw_mysql_error($query_trades_shared));
								$count_row_j = $count_row_i+1;
								while ($row_trades_shared = mysql_fetch_array($result_trades_shared) ) 
								{
									$wks->write($count_row_j, 2, "Shrd");
									$wks->write($count_row_j, 3, ' '.$row_trades_shared["trad_rr"].' ');
									$wks->write($count_row_j, 4, get_repname_by_rr_num($row_trades_shared["trad_rr"]),$format_data_3);
									$wks->writeNumber($count_row_j, 6, $row_trades_shared["for_sum_trad_commission"],$format_currency_1);
									
									$count_row_j = $count_row_j + 1;
									$wks->writeRow($count_row_j,0," ");
									//@@@
											$qry_client_comm_s = "SELECT trad_advisor_code, max(trad_advisor_name) as clnt_name , 
																						sum(trad_commission) as clnt_comm 
																						FROM mry_comm_rr_trades 
																						WHERE trad_trade_date BETWEEN '".$brk_start_date."' AND '".$brk_end_date."'
																						AND trad_rr = '".$row_trades_shared["trad_rr"]."'
																						AND trad_is_cancelled != 1
																						GROUP BY trad_advisor_code 
																						ORDER BY trad_advisor_code";
											$result_client_comm_s = mysql_query($qry_client_comm_s) or die (tdw_mysql_error($qry_client_comm_s));
											$count_row_k = $count_row_j+1;
											while ($row_client_comm_s = mysql_fetch_array($result_client_comm_s) ) 
											{
												$wks->write($count_row_k, 2, " ");
												$wks->write($count_row_k, 3, ' '." ".' ');
												$wks->write($count_row_k, 4, $row_client_comm_s["clnt_name"],$format_data_1);
												$wks->writeNumber($count_row_k, 6, $row_client_comm_s["clnt_comm"],$format_currency_1);
												$count_row_k = $count_row_k + 1;
											}
									//@@@
									$count_row_j = $count_row_k;
									$count_row_j = $count_row_j + 1;
								}
							
}

/*

$wks->write(2, 8, "COMMISSIONS", $format_heading);
$wks->write(2, 11, "Concession/", $format_heading);
$wks->write(2, 14, "C H E C K S", $format_heading);



$wks->write(3, 3, "Type", $format_heading);
$wks->write(3, 4, "Rep", $format_heading);
$wks->write(3, 5, "Name", $format_heading);
$wks->write(3, 6, "");
$wks->write(3, 7, "Sole", $format_heading);
$wks->write(3, 8, "");
$wks->write(3, 9, "Shared", $format_heading);
$wks->write(3, 10, "");
$wks->write(3, 11, "Facilitation", $format_heading);
$wks->write(3, 12, "");
$wks->write(3, 13, "Sole", $format_heading);
$wks->write(3, 14, "");
$wks->write(3, 15, "Shared", $format_heading);
$wks->write(3, 16, "");
$wks->write(3, 17, "20%", $format_heading);
$wks->write(3, 18, "10%", $format_heading);
$wks->write(3, 19, "22%", $format_heading);
$wks->write(3, 20, "Other", $format_heading);
$wks->write(3, 21, "Adj.", $format_heading);
$wks->write(3, 22, "");
$wks->write(3, 23, "");


			$query_trades = "SELECT 
													trad_rr,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$brk_start_date."' AND '".$brk_end_date."'
												AND trad_rr like '0%'												
												GROUP BY trad_rr 
												ORDER BY trad_rr";
			//xdebug("query_trades",$query_trades);
			//exit;
			
			$result_trades = mysql_query($query_trades) or die (tdw_mysql_error($query_trades));

			$i = 5;
			while ($row_trades = mysql_fetch_array($result_trades) ) 
			{
				$wks->write($i, 3, "Sole");
				$wks->write($i, 4, ' '.$row_trades["trad_rr"].' ');
				$wks->write($i, 5, get_repname_by_rr_num($row_trades["trad_rr"]),$format_bold);
				$wks->write($i, 7, $row_trades["for_sum_trad_commission"]);
				$i++;


			}
*/
// We still need to explicitly close the workbook
$wkb->close();
//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
	echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<a href="http://192.168.20.63/tdw/data/xls/<?=$xlfilename?>" target="_blank">Get Excel File</a>