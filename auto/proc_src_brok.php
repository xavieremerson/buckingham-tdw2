<?
//Updated for brokerage month

/*
3/22/2006: Changed : not trad_trade_date but trad_run_date = '".$trade_date_to_process."'
4/4/2006 Review for errors in duplicate entries in level a (possibly erroneous trades)
*/
?>
<?
ini_set('max_execution_time', 7200);

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//initiate page load time routine
$time=getmicrotime(); 

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
$result_nadd_populate = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (mysql_error());

//Create an array of account names and advisor code for lookup.
$qry_acct_adv = "select nadd_full_account_number, nadd_advisor from mry_nfs_nadd";
$result_acct_adv = mysql_query($qry_acct_adv) or die (tdw_mysql_error($qry_acct_adv));
$arr_acct_adv = array();
while ( $row_acct_adv = mysql_fetch_array($result_acct_adv) ) 
{
	$arr_acct_adv[strtoupper(trim($row_acct_adv["nadd_full_account_number"]))] = $row_acct_adv["nadd_advisor"];
}

//Flush the data tables because this process starts from "DAY1"
$result_arc_comm_rr_flush         = mysql_query("truncate table arc_comm_rr")         or die (mysql_error());
$result_rep_comm_rr_level_0_flush = mysql_query("truncate table rep_comm_rr_level_0") or die (mysql_error());
$result_rep_comm_rr_level_a_flush = mysql_query("truncate table rep_comm_rr_level_a") or die (mysql_error());
$result_rep_comm_rr_level_b_flush = mysql_query("truncate table rep_comm_rr_level_b") or die (mysql_error());
$result_rep_comm_rr_trades_flush  = mysql_query("truncate table rep_comm_rr_trades")  or die (mysql_error());
  
//Process a certain date (generally the previous business day)
//$trade_date_to_process = previous_business_day();
for ($bizdays=1; $bizdays < 200; $bizdays++) {

			if (strtotime(business_day_forward(strtotime("2006-01-17"),$bizdays)) > strtotime("now")) {
				echo business_day_forward(strtotime("2006-01-17"),$bizdays)."<br>";
				echo "Exit condition met... Program exiting normally<br>";
				
			} else {

							$trade_date_to_process = business_day_forward(strtotime("2006-01-17"),$bizdays);
							xdebug('trade_date_to_process',$trade_date_to_process);
							echo "<hr>";

							//begin track time taken for the day
							$timetrack=getmicrotime(); 
							
							$result_comm_arc = mysql_query("insert into arc_comm_rr select * from mry_comm_rr") or die(mysql_error());
							$result_comm_flush = mysql_query("truncate table mry_comm_rr") or die (mysql_error());
							echo "mry_comm_rr is flushed and ready for the next set of data<br>";

							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++						
						  //get only regular trades, not the cancelled trades, the cancelled trades will be 
							//processed in a separate section at the end of segment	
								$query_trades = "SELECT * 
																 FROM nfs_trades
																 WHERE trad_run_date = '".$trade_date_to_process."'
																 AND trad_branch = 'PDY'
																 AND trad_cancel_code != '1'";
	  						//xdebug ("query_trades",$query_trades);
  							$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								$countval = 1;
								while($row_trades = mysql_fetch_array($result_trades))
								{
									//get data to insert into temp table to process further
									$comm_trade_reference_number = 	trim($row_trades["trad_trade_reference_number"]);
									$trad_full_account_number = 		trim($row_trades["trad_full_account_number"]);
									$trad_short_name = 							str_replace("'","",trim($row_trades["trad_short_name"]));
									$comm_rr = 											trim($row_trades["trad_registered_rep"]);
									$comm_trade_date = 							$row_trades["trad_trade_date"];
									$comm_run_date = 							  $row_trades["trad_run_date"];
									$comm_advisor_code = 						$arr_acct_adv[strtoupper(trim($row_trades["trad_full_account_number"]))];
									$comm_advisor_name = 						str_replace("'","\'",$arr_clients[substr($row_trades["trad_short_name"],0,4)]);
									$comm_account_name = 						str_replace("'","",get_account_name($row_trades["trad_full_account_number"])); //stupid single quote
									$comm_account_number = 					trim($row_trades["trad_full_account_number"]);
									$comm_symbol = 									trim($row_trades["trad_symbol"]);
									$comm_buy_sell = 								trim($row_trades["trad_buy_sell"]);
									$comm_quantity = 								$row_trades["trad_quantity"];
									$comm_price = 									$row_trades["trad_price"];
									$comm_commission_code = 				$row_trades["trad_commission_concession_code"];
									$comm_commission = 							$row_trades["trad_trade_commission"];
									
									if ($row_trades["trad_commission_concession_code"] == 3) { //This indicates cents/share
										$comm_cents_per_share = $row_trades["trad_trade_commission"]/$row_trades["trad_quantity"];
									} else {
										$comm_cents_per_share = 0;
									}
							
									$qry_insert_trade = "insert into mry_comm_rr(
																			comm_trade_reference_number,
																			comm_rr, 
																			comm_trade_date, 
																			comm_run_date, 
																			comm_advisor_code,
																			comm_advisor_name, 
																			comm_account_name, 
																			comm_account_number, 
																			comm_symbol, 
																			comm_buy_sell, 
																			comm_quantity, 
																			comm_price, 
																			comm_commission_code, 
																			comm_commission, 
																			comm_cents_per_share)
																			values(".
																			"'".$comm_trade_reference_number."',".
																			"'".$comm_rr."',".
																			"'".$comm_trade_date."',". 
																			"'".$comm_run_date."',". 
																			"'".$comm_advisor_code."',". 
																			"'".$comm_advisor_name."',". 
																			"'".$comm_account_name."',". 
																			"'".$comm_account_number."',". 
																			"'".$comm_symbol."',". 
																			"'".$comm_buy_sell."',".
																			"'".$comm_quantity."',". 
																			"'".$comm_price."',". 
																			"'".$comm_commission_code."',". 
																			"'".$comm_commission."',". 
																			"'".$comm_cents_per_share."')";
																			
									$result_insert_trade = mysql_query($qry_insert_trade) or die(tdw_mysql_error($qry_insert_trade));
									$countval = $countval + 1;
								}
								echo "Data inserted to temporary table for further processing.<br>";
								
							//// Processing from temporary table.
							
							//Get unique RR from table
								$query_rr = "SELECT distinct(comm_rr) from mry_comm_rr order by comm_rr"; 
								$result_rr = mysql_query($query_rr) or die(mysql_error());
								while($row_rr = mysql_fetch_array($result_rr))
								{

											//_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_
											//PROCESS FOR TABLE: rep_comm_rr_trades 
											//fields in table mry_comm_rr : 
											//comm_trade_reference_number  comm_rr  comm_trade_date  comm_advisor_code comm_advisor_name  
											//comm_account_name  comm_account_number  comm_symbol  comm_buy_sell  
											//comm_quantity  comm_price  comm_commission_code  comm_commission  comm_cents_per_share 
											$query_comm_trd =  "SELECT *
																					FROM mry_comm_rr
																					WHERE comm_rr = '".$row_rr["comm_rr"]."'"; 
											$result_comm_trd = mysql_query($query_comm_trd) or die(mysql_error());
											
											while($row_comm_trd = mysql_fetch_array($result_comm_trd))
											{
												$qyery_insert_trade = "INSERT INTO rep_comm_rr_trades 
																								(trad_reference_number,
																								trad_rr,
																								trad_trade_date,
																								trad_run_date,
																								trad_advisor_code,
																								trad_advisor_name,
																								trad_account_name,
																								trad_account_number,
																								trad_symbol,
																								trad_buy_sell,
																								trad_quantity,
																								trade_price,
																								trad_commission,
																								trad_cents_per_share
																								) VALUES (".
																								"'".$row_comm_trd["comm_trade_reference_number"]."',".
																								"'".$row_comm_trd["comm_rr"]."',".
																								"'".$row_comm_trd["comm_trade_date"]."',".
																								"'".$row_comm_trd["comm_run_date"]."',".
																								"'".$row_comm_trd["comm_advisor_code"]."',".
																								"'".str_replace("'","\'",$row_comm_trd["comm_advisor_name"])."',". 
																								"'".str_replace("'","\'",$row_comm_trd["comm_account_name"])."',".
																								"'".$row_comm_trd["comm_account_number"]."',". 
																								"'".$row_comm_trd["comm_symbol"]."',". 
																								"'".$row_comm_trd["comm_buy_sell"]."',". 
																								"'".$row_comm_trd["comm_quantity"]."',".
																								"'".$row_comm_trd["comm_price"]."',". 
																								"'".$row_comm_trd["comm_commission"]."',". 
																								"'".$row_comm_trd["comm_cents_per_share"]."')";
												$result_insert_trade = mysql_query($qyery_insert_trade) or die(tdw_mysql_error($qyery_insert_trade));
																								
											}
											
											//_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_
											
											//_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++
											//PROCESS FOR TABLE: rep_comm_rr_level_0
											//get aggregated commission for each rep number (this feeds the management (business summary interface)
											
											//ordering the statement below by trade date because with run date there
											//may be older trade dates which MUST be processed before the newer ones.
                      xdebug("Processsing Rep: ",$row_rr["comm_rr"]);											
											$query_comm_rep =  "SELECT comm_trade_date, 
											                          sum( comm_commission ) as total_commission_rep 
																					FROM mry_comm_rr
																					WHERE comm_rr = '".$row_rr["comm_rr"]."'
																					GROUP BY comm_trade_date  
																					ORDER BY comm_trade_date"; 
										
											$result_comm_rep = mysql_query($query_comm_rep) or die(mysql_error());
											
											while($row_comm_rep = mysql_fetch_array($result_comm_rep))
											{
												$total_commission_rep = 	$row_comm_rep["total_commission_rep"];
												$comm_trade_date_rep  =   $row_comm_rep["comm_trade_date"];
												
												// ><><><>< breaking this section into two subsections for processing trades older than previous business day (as of trades)
												if ($comm_trade_date_rep != $trade_date_to_process) {  //section 1 of 2
												
													//find if data point exists
													$qry_check_rep = "SELECT count(*) as countval_rep 
																						FROM rep_comm_rr_level_0 
																						WHERE comm_rr = '".$row_rr["comm_rr"]."'";
													$result_check_rep = mysql_query($qry_check_rep) or die(mysql_error());
													while($row_check_rep = mysql_fetch_array($result_check_rep))
															{
															$countval_rep = $row_check_rep["countval_rep"];
															}
													
													//if data point exists then proceed with processing
													if ($countval_rep > 0) { //values exist
															$query_mqydate_rep = "SELECT max(comm_trade_date) AS comm_trade_date
																										FROM rep_comm_rr_level_0
																										WHERE comm_rr = '".$row_rr["comm_rr"]."'";

															$result_mqydate_rep = mysql_query($query_mqydate_rep) or die(mysql_error());
															while($row_mqydate_rep = mysql_fetch_array($result_mqydate_rep))
															{
															//getting the latest date value
															$latestdate_rep = $row_mqydate_rep["comm_trade_date"];
															}
															
															//trifurcating here for conditions of trade date =, less than or more than
															if (strtotime($latestdate_rep) == strtotime($comm_trade_date_rep)) { //date equal
															
																				$query_mqy_rep = "SELECT * 
																													FROM rep_comm_rr_level_0
																													WHERE comm_rr = '".$row_rr["comm_rr"]."'
																													AND comm_trade_date = '".$latestdate_rep."'";
																				$result_mqy_rep = mysql_query($query_mqy_rep) or die(mysql_error());
																				while($row_mqy_rep = mysql_fetch_array($result_mqy_rep))
																				{
																					$comm_total_rep = $row_mqy_rep["comm_total"];
																					$comm_mtd_rep =   $row_mqy_rep["comm_mtd"];
																					$comm_qtd_rep =   $row_mqy_rep["comm_qtd"];
																					$comm_ytd_rep =   $row_mqy_rep["comm_ytd"];
																				}

																				$update_total_rep = $comm_total_rep + $total_commission_rep;
																				$update_mtd_rep =   $comm_mtd_rep + $total_commission_rep;
																				$update_qtd_rep =   $comm_qtd_rep + $total_commission_rep;
																				$update_ytd_rep =   $comm_ytd_rep + $total_commission_rep;						 
														
																				//UPDATE table rep_comm_rr_level_0
																				$sql_level_0_update = "UPDATE rep_comm_rr_level_0
																															SET
																																comm_total = '".$update_total_rep."', 
																																comm_mtd = '".$update_mtd_rep."', 
																																comm_qtd = '".$update_qtd_rep."', 
																																comm_ytd = '".$update_ytd_rep."'
																															WHERE comm_rr = '".$row_rr["comm_rr"]."'
																															AND comm_trade_date = '".$comm_trade_date_rep."'";
																				//IMP: 
																				//xdebug("sql_level_0_update",sql_level_0_update);
																				$result_level_0_update = mysql_query($sql_level_0_update) or die(tdw_mysql_error($sql_level_0_update));	

															} elseif (strtotime(latestdate_rep) < strtotime(comm_trade_date_rep)) { //date less than
	
																				$query_mqy_rep = "SELECT * 
																												FROM rep_comm_rr_level_0
																												WHERE comm_rr = '".$row_rr["comm_rr"]."'
																												AND comm_trade_date = '".$latestdate_rep."'";
																				$result_mqy_rep = mysql_query($query_mqy_rep) or die(mysql_error());
																				while($row_mqy_rep = mysql_fetch_array($result_mqy_rep))
																				{
																					$comm_mtd_rep = $row_mqy_rep["comm_mtd"];
																					$comm_qtd_rep = $row_mqy_rep["comm_qtd"];
																					$comm_ytd_rep = $row_mqy_rep["comm_ytd"];
																				}
																				
																				//Process the numbers based on date logic
																				$is_same_year_rep = samebrokyear($latestdate_rep,$trade_date_to_process);
																				$is_same_month_rep = samebrokmonth($latestdate_rep,$trade_date_to_process);
																				$is_same_qtr_rep = samebrokqtr($latestdate_rep,$trade_date_to_process);
																				
																				if ($is_same_year_rep == 1) {
																						if ($is_same_month_rep == 1) {
																								$insert_mtd_rep = $comm_mtd_rep + $total_commission_rep;
																								$insert_qtd_rep = $comm_qtd_rep + $total_commission_rep;
																								$insert_ytd_rep = $comm_ytd_rep + $total_commission_rep;						 
																						} else {
																								if ($is_same_qtr_rep == 1) {
																									$insert_mtd_rep = $total_commission_rep;
																									$insert_qtd_rep = $comm_qtd_rep + $total_commission_rep;
																									$insert_ytd_rep = $comm_ytd_rep + $total_commission_rep;						 
																								} else {
																									$insert_mtd_rep = $total_commission_rep;
																									$insert_qtd_rep = $total_commission_rep;
																									$insert_ytd_rep = $comm_ytd_rep + $total_commission_rep;						 
																								}
																						}
																				} else {
																						$insert_mtd_rep = $total_commission_rep;
																						$insert_qtd_rep = $total_commission_rep;
																						$insert_ytd_rep = $total_commission_rep;						 
																				}
														
																				//insert into table rep_comm_rr_level_b
																				$sql_level_0_insert = "INSERT INTO rep_comm_rr_level_0 
																															( comm_rr , 
																																comm_trade_date , 
																																comm_total , 
																																comm_mtd , 
																																comm_qtd , 
																																comm_ytd) 
																															VALUES (".
																																"'".$row_rr["comm_rr"]."',".
																																"'".$comm_trade_date_rep."',". 
																																"'".$total_commission_rep."',".   
																																"'".$insert_mtd_rep."',".   
																																"'".$insert_qtd_rep."',".   
																																"'".$insert_ytd_rep."')";
																				//IMP: 
																				//xdebug("sql_level_0_insert",sql_level_0_insert);
																				$result_level_0_insert = mysql_query($sql_level_0_insert) or die(tdw_mysql_error($sql_level_0_insert));	
														
															} else {//date more than (update all records from trade date onwards)

																				$query_mqy_rep = "SELECT * 
																													FROM rep_comm_rr_level_0
																													WHERE comm_rr = '".$row_rr["comm_rr"]."'
																													AND comm_trade_date >= '".$latestdate_rep."'";
																				$result_mqy_rep = mysql_query($query_mqy_rep) or die(mysql_error());
																				while($row_mqy_rep = mysql_fetch_array($result_mqy_rep))
																				{
																					$comm_total_rep = $row_mqy_rep["comm_total"];
																					$comm_mtd_rep =   $row_mqy_rep["comm_mtd"];
																					$comm_qtd_rep =   $row_mqy_rep["comm_qtd"];
																					$comm_ytd_rep =   $row_mqy_rep["comm_ytd"];
																					
																					$comm_trade_date_rep_new = $row_mqy_rep["comm_trade_date"];

																					$update_total_rep = $comm_total_rep + $total_commission_rep;
																					$update_mtd_rep =   $comm_mtd_rep + $total_commission_rep;
																					$update_qtd_rep =   $comm_qtd_rep + $total_commission_rep;
																					$update_ytd_rep =   $comm_ytd_rep + $total_commission_rep;						 
															
																					//UPDATE table rep_comm_rr_level_b
																					$sql_level_0_update = "UPDATE rep_comm_rr_level_0
																																SET
																																	comm_total = '".$update_total_rep."', 
																																	comm_mtd = '".$update_mtd_rep."', 
																																	comm_qtd = '".$update_qtd_rep."', 
																																	comm_ytd = '".$update_ytd_rep."'
																																WHERE comm_rr = '".$row_rr["comm_rr"]."'
																																AND comm_trade_date = '".$comm_trade_date_rep_new."'";
																					//IMP: 
																				  //xdebug("sql_level_0_update",$sql_level_0_update);
																					$result_level_0_update = mysql_query($sql_level_0_update) or die(tdw_mysql_error($sql_level_0_update));	
																				}

																		}
														} else { //value does not exist (fresh insert needed)
																				
																				//insert into table rep_comm_rr_level_b
																				$sql_level_0_insert = "INSERT INTO rep_comm_rr_level_0 
																															( comm_rr , 
																																comm_trade_date , 
																																comm_total , 
																																comm_mtd , 
																																comm_qtd , 
																																comm_ytd) 
																															VALUES (".
																																"'".$row_rr["comm_rr"]."',".
																																"'".$comm_trade_date_rep."',". 
																																"'".$total_commission_rep."',".   
																																"'".$total_commission_rep."',".   
																																"'".$total_commission_rep."',".   
																																"'".$total_commission_rep."')";
																				//xdebug("sql_level_0_insert",$sql_level_0_insert);
																				//IMP: 
																				$result_level_0_insert = mysql_query($sql_level_0_insert) or die(tdw_mysql_error($sql_level_0_insert));	
														} 
												} else { //section 2 of 2,  previous section was as-of-trades, this is previous business day trades

													//find if data point exists
													$qry_check_rep = "SELECT count(*) as countval_rep 
																						FROM rep_comm_rr_level_0 
																						WHERE comm_rr = '".$row_rr["comm_rr"]."'";
													$result_check_rep = mysql_query($qry_check_rep) or die(mysql_error());
													while($row_check_rep = mysql_fetch_array($result_check_rep))
															{
															$countval_rep = $row_check_rep["countval_rep"];
															}
													
													//if data point exists then proceed with processing
													if ($countval_rep > 0) { //values exist
															$query_mqydate_rep = "SELECT max(comm_trade_date)  AS comm_trade_date
																										FROM rep_comm_rr_level_0
																										WHERE comm_rr = '".$row_rr["comm_rr"]."'";

															$result_mqydate_rep = mysql_query($query_mqydate_rep) or die(mysql_error());
															while($row_mqydate_rep = mysql_fetch_array($result_mqydate_rep))
															{
															//getting the latest date value
															$latestdate_rep = $row_mqydate_rep["comm_trade_date"];
															}
													
															$query_mqy_rep = "SELECT * 
																								FROM rep_comm_rr_level_0
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_trade_date = '".$latestdate_rep."'";

															$result_mqy_rep = mysql_query($query_mqy_rep) or die(mysql_error());
															while($row_mqy_rep = mysql_fetch_array($result_mqy_rep))
															{
															$comm_mtd_rep = $row_mqy_rep["comm_mtd"];
															$comm_qtd_rep = $row_mqy_rep["comm_qtd"];
															$comm_ytd_rep = $row_mqy_rep["comm_ytd"];
															}
															
															//Process the numbers based on date logic
															$is_same_year_rep = samebrokyear($latestdate_rep,$trade_date_to_process);
															$is_same_month_rep = samebrokmonth($latestdate_rep,$trade_date_to_process);
															$is_same_qtr_rep = samebrokqtr($latestdate_rep,$trade_date_to_process);
															
															if ($is_same_year_rep == 1) {
																	if ($is_same_month_rep == 1) {
																			$insert_mtd_rep = $comm_mtd_rep + $total_commission_rep;
																			$insert_qtd_rep = $comm_qtd_rep + $total_commission_rep;
																			$insert_ytd_rep = $comm_ytd_rep + $total_commission_rep;						 
																	} else {
																			if ($is_same_qtr_rep == 1) {
																				$insert_mtd_rep = $total_commission_rep;
																				$insert_qtd_rep = $comm_qtd_rep + $total_commission_rep;
																				$insert_ytd_rep = $comm_ytd_rep + $total_commission_rep;						 
																			} else {
																				$insert_mtd_rep = $total_commission_rep;
																				$insert_qtd_rep = $total_commission_rep;
																				$insert_ytd_rep = $comm_ytd_rep + $total_commission_rep;						 
																			}
																	}
															} else {
																	$insert_mtd_rep = $total_commission_rep;
																	$insert_qtd_rep = $total_commission_rep;
																	$insert_ytd_rep = $total_commission_rep;						 
															}
									
													} else { //rep/advisor have no prior entry, no data points exists, just insert data

														$insert_mtd_rep = 0;
														$insert_qtd_rep = 0;
														$insert_ytd_rep = 0;						 
									
														$insert_mtd_rep = $total_commission_rep;
														$insert_qtd_rep = $total_commission_rep;
														$insert_ytd_rep = $total_commission_rep;						 
													}
									
												//insert into table rep_comm_rr_level_0
												$sql_level_0_insert = "INSERT INTO rep_comm_rr_level_0 
																							( comm_rr , 
																								comm_trade_date , 
																								comm_total , 
																								comm_mtd , 
																								comm_qtd , 
																								comm_ytd) 
																							VALUES (".
																								"'".$row_rr["comm_rr"]."',".
																								"'".$comm_trade_date_rep."',". 
																								"'".$total_commission_rep."',".   
																								"'".$insert_mtd_rep."',".   
																								"'".$insert_qtd_rep."',".   
																								"'".$insert_ytd_rep."')";
											  //xdebug("sql_level_0_insert",$sql_level_0_insert);
												//IMP: need to insert the trade date (even for as of trades)
												$result_level_0_insert = mysql_query($sql_level_0_insert) or die(tdw_mysql_error($sql_level_0_insert));	
												}
											}

											//_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++_++

											//_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
											//PROCESS FOR TABLE: rep_comm_rr_level_b
											//get aggregated commission for each subaccount
											
											//ordering the statement below by trade date because with run date there
											//may be older trade dates which MUST be processed before the newer ones.
											$query_comm_sa =  "SELECT comm_account_number,
																								comm_trade_date, 
											                          sum( comm_commission ) as total_commission_sa, 
																								max( comm_advisor_code ) as comm_advisor_code_sa 
																					FROM mry_comm_rr
																					WHERE comm_rr = '".$row_rr["comm_rr"]."'
																					GROUP BY comm_account_number, comm_trade_date  
																					ORDER BY comm_trade_date"; 
										
											$result_comm_sa = mysql_query($query_comm_sa) or die(mysql_error());
											
											while($row_comm_sa = mysql_fetch_array($result_comm_sa))
											{
												$comm_account_number_sa = $row_comm_sa["comm_account_number"];
												$total_commission_sa = 	$row_comm_sa["total_commission_sa"];
												$comm_advisor_code_sa = $row_comm_sa["comm_advisor_code_sa"];
												$comm_trade_date_sa = $row_comm_sa["comm_trade_date"];
												
												//breaking this section into two subsections for processing trades older than previous business day (as of trades)
												if ($comm_trade_date_sa != $trade_date_to_process) {  //section 1 of 2
												
													//find if data point exists
													$qry_check_sa = "SELECT count(*) as countval_sa 
																						FROM rep_comm_rr_level_b 
																						WHERE comm_rr = '".$row_rr["comm_rr"]."' 
																				 		AND comm_account_number = '".$comm_account_number_sa."'";
													$result_check_sa = mysql_query($qry_check_sa) or die(mysql_error());
													while($row_check_sa = mysql_fetch_array($result_check_sa))
															{
															$countval_sa = $row_check_sa["countval_sa"];
															}
													
													//if data point exists then proceed with processing
													if ($countval_sa > 0) { //values exist
															$query_mqydate_sa = "SELECT max(comm_trade_date) AS comm_trade_date
																										FROM rep_comm_rr_level_b
																										WHERE comm_rr = '".$row_rr["comm_rr"]."'
																										AND comm_account_number = '".$comm_account_number_sa."'";
															//echo $query_mqydate_sa."<br>";
															$result_mqydate_sa = mysql_query($query_mqydate_sa) or die(mysql_error());
															while($row_mqydate_sa = mysql_fetch_array($result_mqydate_sa))
															{
															//getting the latest date value
															$latestdate_sa = $row_mqydate_sa["comm_trade_date"];
															}
															
															//trifurcating here for conditions of trade date =, less than or more than
															if (strtotime($latestdate_sa) == strtotime($comm_trade_date_sa)) { //date equal
															
																				$query_mqy_sa = "SELECT * 
																													FROM rep_comm_rr_level_b
																													WHERE comm_rr = '".$row_rr["comm_rr"]."'
																													AND comm_account_number = '".$comm_account_number_sa."'
																													AND comm_trade_date = '".$latestdate_sa."'";
																				$result_mqy_sa = mysql_query($query_mqy_sa) or die(mysql_error());
																				while($row_mqy_sa = mysql_fetch_array($result_mqy_sa))
																				{
																					$comm_total_sa = $row_mqy_sa["comm_total"];
																					$comm_mtd_sa =   $row_mqy_sa["comm_mtd"];
																					$comm_qtd_sa =   $row_mqy_sa["comm_qtd"];
																					$comm_ytd_sa =   $row_mqy_sa["comm_ytd"];
																				}

																				$update_total_sa = $comm_total_sa + $total_commission_sa;
																				$update_mtd_sa =   $comm_mtd_sa + $total_commission_sa;
																				$update_qtd_sa =   $comm_qtd_sa + $total_commission_sa;
																				$update_ytd_sa =   $comm_ytd_sa + $total_commission_sa;						 
														
																				//UPDATE table rep_comm_rr_level_b
																				$sql_level_b_update = "UPDATE rep_comm_rr_level_b
																															SET
																																comm_total = '".$update_total_sa."', 
																																comm_mtd = '".$update_mtd_sa."', 
																																comm_qtd = '".$update_qtd_sa."', 
																																comm_ytd = '".$update_ytd_sa."'
																															WHERE comm_rr = '".$row_rr["comm_rr"]."'
																															AND comm_trade_date = '".$comm_trade_date_sa."'
																															AND comm_account_number = '".$comm_account_number_sa."'";
																				//IMP: 
																				$result_level_b_update = mysql_query($sql_level_b_update) or die(tdw_mysql_error($sql_level_b_update));	
			
															} elseif (strtotime(latestdate_sa) < strtotime(comm_trade_date_sa)) { //date less than
	
																				$query_mqy_sa = "SELECT * 
																												FROM rep_comm_rr_level_b
																												WHERE comm_rr = '".$row_rr["comm_rr"]."'
																												AND comm_account_number = '".$comm_account_number_sa."'
																												AND comm_trade_date = '".$latestdate_sa."'";
																				$result_mqy_sa = mysql_query($query_mqy_sa) or die(mysql_error());
																				while($row_mqy_sa = mysql_fetch_array($result_mqy_sa))
																				{
																					$comm_mtd_sa = $row_mqy_sa["comm_mtd"];
																					$comm_qtd_sa = $row_mqy_sa["comm_qtd"];
																					$comm_ytd_sa = $row_mqy_sa["comm_ytd"];
																				}
																				
																				//Process the numbers based on date logic
																				$is_same_year_sa = samebrokyear($latestdate_sa,$trade_date_to_process);
																				$is_same_month_sa = samebrokmonth($latestdate_sa,$trade_date_to_process);
																				$is_same_qtr_sa = samebrokqtr($latestdate_sa,$trade_date_to_process);
																				
																				if ($is_same_year_sa == 1) {
																						if ($is_same_month_sa == 1) {
																								$insert_mtd_sa = $comm_mtd_sa + $total_commission_sa;
																								$insert_qtd_sa = $comm_qtd_sa + $total_commission_sa;
																								$insert_ytd_sa = $comm_ytd_sa + $total_commission_sa;						 
																						} else {
																								if ($is_same_qtr_sa == 1) {
																									$insert_mtd_sa = $total_commission_sa;
																									$insert_qtd_sa = $comm_qtd_sa + $total_commission_sa;
																									$insert_ytd_sa = $comm_ytd_sa + $total_commission_sa;						 
																								} else {
																									$insert_mtd_sa = $total_commission_sa;
																									$insert_qtd_sa = $total_commission_sa;
																									$insert_ytd_sa = $comm_ytd_sa + $total_commission_sa;						 
																								}
																						}
																				} else {
																						$insert_mtd_sa = $total_commission_sa;
																						$insert_qtd_sa = $total_commission_sa;
																						$insert_ytd_sa = $total_commission_sa;						 
																				}
														
																				//insert into table rep_comm_rr_level_b
																				$sql_level_b_insert = "INSERT INTO rep_comm_rr_level_b 
																															( comm_rr , 
																																comm_trade_date , 
																																comm_advisor_code , 
																																comm_account_name ,
																																comm_account_number ,
																																comm_total , 
																																comm_mtd , 
																																comm_qtd , 
																																comm_ytd) 
																															VALUES (".
																																"'".$row_rr["comm_rr"]."',".
																																"'".$comm_trade_date_sa."',". 
																																"'".$comm_advisor_code_sa."',".  
																																"'".str_replace("'","",get_account_name($comm_account_number_sa))."',". 
																																"'".$comm_account_number_sa."',". 
																																"'".$total_commission_sa."',".   
																																"'".$insert_mtd_sa."',".   
																																"'".$insert_qtd_sa."',".   
																																"'".$insert_ytd_sa."')";
																				//IMP: 
																				$result_level_b_insert = mysql_query($sql_level_b_insert) or die(tdw_mysql_error($sql_level_b_insert));	
														
															} else {//date more than (update all records from trade date onwards)

																				$query_mqy_sa = "SELECT * 
																													FROM rep_comm_rr_level_b
																													WHERE comm_rr = '".$row_rr["comm_rr"]."'
																													AND comm_account_number = '".$comm_account_number_sa."'
																													AND comm_trade_date >= '".$latestdate_sa."'";
																				$result_mqy_sa = mysql_query($query_mqy_sa) or die(mysql_error());
																				while($row_mqy_sa = mysql_fetch_array($result_mqy_sa))
																				{
																					$comm_total_sa = $row_mqy_sa["comm_total"];
																					$comm_mtd_sa =   $row_mqy_sa["comm_mtd"];
																					$comm_qtd_sa =   $row_mqy_sa["comm_qtd"];
																					$comm_ytd_sa =   $row_mqy_sa["comm_ytd"];
																					
																					$comm_trade_date_sa_new = $row_mqy_sa["comm_trade_date"];

																					$update_total_sa = $comm_total_sa + $total_commission_sa;
																					$update_mtd_sa =   $comm_mtd_sa + $total_commission_sa;
																					$update_qtd_sa =   $comm_qtd_sa + $total_commission_sa;
																					$update_ytd_sa =   $comm_ytd_sa + $total_commission_sa;						 
															
																					//UPDATE table rep_comm_rr_level_b
																					$sql_level_b_update = "UPDATE rep_comm_rr_level_b
																																SET
																																	comm_total = '".$update_total_sa."', 
																																	comm_mtd = '".$update_mtd_sa."', 
																																	comm_qtd = '".$update_qtd_sa."', 
																																	comm_ytd = '".$update_ytd_sa."'
																																WHERE comm_rr = '".$row_rr["comm_rr"]."'
																																AND comm_trade_date = '".$comm_trade_date_sa_new."'
																																AND comm_account_number = '".$comm_account_number_sa."'";
																					//IMP: 
																					$result_level_b_update = mysql_query($sql_level_b_update) or die(tdw_mysql_error($sql_level_b_update));	
																				}

																		}
														} else { //value does not exist (fresh insert needed)
																				
																				//insert into table rep_comm_rr_level_b
																				$sql_level_b_insert = "INSERT INTO rep_comm_rr_level_b 
																															( comm_rr , 
																																comm_trade_date , 
																																comm_advisor_code , 
																																comm_account_name ,
																																comm_account_number ,
																																comm_total , 
																																comm_mtd , 
																																comm_qtd , 
																																comm_ytd) 
																															VALUES (".
																																"'".$row_rr["comm_rr"]."',".
																																"'".$comm_trade_date_sa."',". 
																																"'".$comm_advisor_code_sa."',".  
																																"'".str_replace("'","",get_account_name($comm_account_number_sa))."',". 
																																"'".$comm_account_number_sa."',". 
																																"'".$total_commission_sa."',".   
																																"'".$total_commission_sa."',".   
																																"'".$total_commission_sa."',".   
																																"'".$total_commission_sa."')";
																				//IMP: 
																				$result_level_b_insert = mysql_query($sql_level_b_insert) or die(tdw_mysql_error($sql_level_b_insert));	
														} 
												} else { //section 2 of 2,  previous section was as-of-trades, this is previous business day trades

													//find if data point exists
													$qry_check_sa = "SELECT count(*) as countval_sa 
																						FROM rep_comm_rr_level_b 
																						WHERE comm_rr = '".$row_rr["comm_rr"]."' 
																				 		AND comm_account_number = '".$comm_account_number_sa."'";
													$result_check_sa = mysql_query($qry_check_sa) or die(mysql_error());
													while($row_check_sa = mysql_fetch_array($result_check_sa))
															{
															$countval_sa = $row_check_sa["countval_sa"];
															}
													//xdebug("countval_sa",$countval_sa);
													
													//if data point exists then proceed with processing
													if ($countval_sa > 0) { //values exist
															$query_mqydate_sa = "SELECT max(comm_trade_date)  AS comm_trade_date
																										FROM rep_comm_rr_level_b
																										WHERE comm_rr = '".$row_rr["comm_rr"]."'
																										AND comm_account_number = '".$comm_account_number_sa."'";
															//echo $query_mqydate_sa."<br>";
															$result_mqydate_sa = mysql_query($query_mqydate_sa) or die(mysql_error());
															while($row_mqydate_sa = mysql_fetch_array($result_mqydate_sa))
															{
															//getting the latest date value
															$latestdate_sa = $row_mqydate_sa["comm_trade_date"];
															}
													
															$query_mqy_sa = "SELECT * 
																								FROM rep_comm_rr_level_b
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_account_number = '".$comm_account_number_sa."'
																								AND comm_trade_date = '".$latestdate_sa."'";
															//echo $query_mqy_sa."<br>";
															$result_mqy_sa = mysql_query($query_mqy_sa) or die(mysql_error());
															while($row_mqy_sa = mysql_fetch_array($result_mqy_sa))
															{
															$comm_mtd_sa = $row_mqy_sa["comm_mtd"];
															$comm_qtd_sa = $row_mqy_sa["comm_qtd"];
															$comm_ytd_sa = $row_mqy_sa["comm_ytd"];
															}
															
															//Process the numbers based on date logic
															$is_same_year_sa = samebrokyear($latestdate_sa,$trade_date_to_process);
															$is_same_month_sa = samebrokmonth($latestdate_sa,$trade_date_to_process);
															$is_same_qtr_sa = samebrokqtr($latestdate_sa,$trade_date_to_process);
															
															if ($is_same_year_sa == 1) {
																	if ($is_same_month_sa == 1) {
																			$insert_mtd_sa = $comm_mtd_sa + $total_commission_sa;
																			$insert_qtd_sa = $comm_qtd_sa + $total_commission_sa;
																			$insert_ytd_sa = $comm_ytd_sa + $total_commission_sa;						 
																	} else {
																			if ($is_same_qtr_sa == 1) {
																				$insert_mtd_sa = $total_commission_sa;
																				$insert_qtd_sa = $comm_qtd_sa + $total_commission_sa;
																				$insert_ytd_sa = $comm_ytd_sa + $total_commission_sa;						 
																			} else {
																				$insert_mtd_sa = $total_commission_sa;
																				$insert_qtd_sa = $total_commission_sa;
																				$insert_ytd_sa = $comm_ytd_sa + $total_commission_sa;						 
																			}
																	}
															} else {
																	$insert_mtd_sa = $total_commission_sa;
																	$insert_qtd_sa = $total_commission_sa;
																	$insert_ytd_sa = $total_commission_sa;						 
															}
									
													} else { //rep/advisor have no prior entry, no data points exists, just insert data
									
														$insert_mtd_sa = $total_commission_sa;
														$insert_qtd_sa = $total_commission_sa;
														$insert_ytd_sa = $total_commission_sa;						 
													}
									
												//insert into table rep_comm_rr_level_b
												$sql_level_b_insert = "INSERT INTO rep_comm_rr_level_b 
																							( comm_rr , 
																								comm_trade_date , 
																								comm_advisor_code , 
																								comm_account_name ,
																								comm_account_number ,
																								comm_total , 
																								comm_mtd , 
																								comm_qtd , 
																								comm_ytd) 
																							VALUES (".
																								"'".$row_rr["comm_rr"]."',".
																								"'".$comm_trade_date_sa."',". 
																								"'".$comm_advisor_code_sa."',".  
																								"'".str_replace("'","",get_account_name($comm_account_number_sa))."',". 
																								"'".$comm_account_number_sa."',". 
																								"'".$total_commission_sa."',".   
																								"'".$insert_mtd_sa."',".   
																								"'".$insert_qtd_sa."',".   
																								"'".$insert_ytd_sa."')";
												//IMP: need to insert the trade date (even for as of trades)
												$result_level_b_insert = mysql_query($sql_level_b_insert) or die(tdw_mysql_error($sql_level_b_insert));	

												}
											}

											//_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
									
											//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
											//PROCESS FOR TABLE: rep_comm_rr_level_a 
											//get aggregated commissions for each client/advisor
											//since as-of_trades are also included, order by trade date for proper processing
											$query_comm =  "SELECT 	comm_advisor_code ,
																							comm_trade_date,
																							sum( comm_commission ) as total_commission 
																			FROM mry_comm_rr
																			WHERE comm_rr = '".$row_rr["comm_rr"]."'
																			GROUP BY comm_advisor_code, comm_trade_date
																			ORDER BY comm_trade_date";
											$result_comm = mysql_query($query_comm) or die(tdw_mysql_error($query_comm));
									
											while($row_comm = mysql_fetch_array($result_comm))
											{
												$total_commission  = $row_comm["total_commission"];
												$comm_advisor_code = $row_comm["comm_advisor_code"];
												$comm_trade_date_a = $row_comm["comm_trade_date"];

												//breaking this section into two subsections for processing trades older than previous business day (as of trades)
												if ($comm_trade_date_a != $trade_date_to_process) {  //section 1 of 2

														//find if data point exists
														$qry_check = "SELECT count(*) as countval 
																					FROM rep_comm_rr_level_a 
																					WHERE comm_rr = '".$row_rr["comm_rr"]."' 
																					AND comm_advisor_code = '".$comm_advisor_code."'";
														$result_check = mysql_query($qry_check) or die(mysql_error());
														while($row_check = mysql_fetch_array($result_check))
																{
																$countval = $row_check["countval"];
																}
																
													//if data point exists then proceed with processing
													if ($countval > 0) { //values exist
															
															$query_mqydate = "SELECT max(comm_trade_date)  AS comm_trade_date
																								FROM rep_comm_rr_level_a
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'";
															$result_mqydate = mysql_query($query_mqydate) or die(mysql_error());
															while($row_mqydate = mysql_fetch_array($result_mqydate))
															{
															//getting the latest date value
															$latestdate = $row_mqydate["comm_trade_date"];
															}
															
															//trifurcating for as-of-trades on conditions = , > and <

															if (strtotime($latestdate) < strtotime($comm_trade_date_a)) { //insert routine

																	$query_mqy = "SELECT * 
																								FROM rep_comm_rr_level_a
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'
																								AND comm_trade_date = '".$latestdate."'";
																	$result_mqy = mysql_query($query_mqy) or die(mysql_error());
																	while($row_mqy = mysql_fetch_array($result_mqy))
																	{
																		$comm_mtd = $row_mqy["comm_mtd"];
																		$comm_qtd = $row_mqy["comm_qtd"];
																		$comm_ytd = $row_mqy["comm_ytd"];
																	}
																	
																	//Process the numbers based on date logic
																	$is_same_year = 	samebrokyear($latestdate,$trade_date_to_process);
																	$is_same_month = 	samebrokmonth($latestdate,$trade_date_to_process);
																	$is_same_qtr = 		samebrokqtr($latestdate,$trade_date_to_process);
																	
																	if ($is_same_year == 1) {
																			if ($is_same_month == 1) {
																					$insert_mtd = $comm_mtd + $total_commission;
																					$insert_qtd = $comm_qtd + $total_commission;
																					$insert_ytd = $comm_ytd + $total_commission;						 
																			} else {
																					if ($is_same_qtr == 1) {
																						$insert_mtd = $total_commission;
																						$insert_qtd = $comm_qtd + $total_commission;
																						$insert_ytd = $comm_ytd + $total_commission;						 
																					} else {
																						$insert_mtd = $total_commission;
																						$insert_qtd = $total_commission;
																						$insert_ytd = $comm_ytd + $total_commission;						 
																					}
																			}
																	} else {
																			$insert_mtd = $total_commission;
																			$insert_qtd = $total_commission;
																			$insert_ytd = $total_commission;						 
																	}
											
																	//insert into table rep_comm_rr_level_a
																	$sql_level_a_insert = "INSERT INTO rep_comm_rr_level_a 
																												( comm_rr , 
																													comm_trade_date , 
																													comm_advisor_code , 
																													comm_advisor_name , 
																													comm_total , 
																													comm_mtd , 
																													comm_qtd , 
																													comm_ytd) 
																												VALUES (".
																													"'".$row_rr["comm_rr"]."',".
																													"'".$comm_trade_date_a."',". 
																													"'".$row_comm["comm_advisor_code"]."',".  
																													"'".str_replace("'","\'",$arr_clients[$row_comm["comm_advisor_code"]])."',". 
																													"'".$total_commission."',".   
																													"'".$insert_mtd."',".   
																													"'".$insert_qtd."',".   
																													"'".$insert_ytd."')";
											
																	$result_level_a_insert = mysql_query($sql_level_a_insert) or die(tdw_mysql_error($sql_level_a_insert));	

															} elseif (strtotime($latestdate) == strtotime($comm_trade_date_a)) { //update routine

																	$query_mqy = "SELECT * 
																								FROM rep_comm_rr_level_a
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'
																								AND comm_trade_date = '".$latestdate."'";
																	
																	$result_mqy = mysql_query($query_mqy) or die(mysql_error());
																	while($row_mqy = mysql_fetch_array($result_mqy))
																	{
																		$comm_total = $row_mqy["comm_total"];
																		$comm_mtd   = $row_mqy["comm_mtd"];
																		$comm_qtd   = $row_mqy["comm_qtd"];
																		$comm_ytd   = $row_mqy["comm_ytd"];

																		$comm_trade_date_new   = $row_mqy["comm_trade_date"];
																	}
																	
																	$update_total = $comm_total + $total_commission;
																	$update_mtd = $comm_mtd + $total_commission;
																	$update_qtd = $comm_qtd + $total_commission;
																	$update_ytd = $comm_ytd + $total_commission;						 

																	//insert into table rep_comm_rr_level_a
																	$sql_level_a_update = "UPDATE rep_comm_rr_level_a
																												 SET
																													comm_total = '".$update_total."', 
																													comm_mtd = '".$update_mtd."', 
																													comm_qtd = '".$update_qtd."', 
																													comm_ytd = '".$update_ytd."'
																												WHERE	comm_rr = '".$row_rr["comm_rr"]."'
																													AND comm_trade_date = '".$comm_trade_date_new."' 
																													AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'";
											
																	$result_level_a_update = mysql_query($sql_level_a_update) or die(tdw_mysql_error($sql_level_a_update));	
															
															} else { // > condition, update all dates going forward
																	$query_mqy = "SELECT * 
																								FROM rep_comm_rr_level_a
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'
																								AND comm_trade_date >= '".$latestdate."'";
																	
																	$result_mqy = mysql_query($query_mqy) or die(mysql_error());
																	while($row_mqy = mysql_fetch_array($result_mqy))
																	{
																		$comm_total = $row_mqy["comm_total"];
																		$comm_mtd   = $row_mqy["comm_mtd"];
																		$comm_qtd   = $row_mqy["comm_qtd"];
																		$comm_ytd   = $row_mqy["comm_ytd"];

																		$comm_trade_date_new   = $row_mqy["comm_trade_date"];
																	
																		$update_total = $comm_total + $total_commission;
																		$update_mtd = $comm_mtd + $total_commission;
																		$update_qtd = $comm_qtd + $total_commission;
																		$update_ytd = $comm_ytd + $total_commission;						 
	
																		//insert into table rep_comm_rr_level_a
																		$sql_level_a_update = "UPDATE rep_comm_rr_level_a
																													 SET
																														comm_total = '".$update_total."', 
																														comm_mtd = '".$update_mtd."', 
																														comm_qtd = '".$update_qtd."', 
																														comm_ytd = '".$update_ytd."'
																													WHERE	comm_rr = '".$row_rr["comm_rr"]."'
																														AND comm_trade_date = '".$comm_trade_date_new."' 
																														AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'";
												
																		$result_level_a_update = mysql_query($sql_level_a_update) or die(tdw_mysql_error($sql_level_a_update));	
																	}
															}
													} else { // value does not exist, fresh insert required
																	
																	//insert into table rep_comm_rr_level_a
																	$sql_level_a_insert = "INSERT INTO rep_comm_rr_level_a 
																												( comm_rr , 
																													comm_trade_date , 
																													comm_advisor_code , 
																													comm_advisor_name , 
																													comm_total , 
																													comm_mtd , 
																													comm_qtd , 
																													comm_ytd) 
																												VALUES (".
																													"'".$row_rr["comm_rr"]."',".
																													"'".$comm_trade_date_a."',". 
																													"'".$row_comm["comm_advisor_code"]."',".  
																													"'".str_replace("'","\'",$arr_clients[$row_comm["comm_advisor_code"]])."',". 
																													"'".$total_commission."',".   
																													"'".$total_commission."',".   
																													"'".$total_commission."',".   
																													"'".$total_commission."')";
											
																	$result_level_a_insert = mysql_query($sql_level_a_insert) or die(tdw_mysql_error($sql_level_a_insert));	
													}

												} else { //section 2 of 2
												
															//find if data point exists
															$qry_check = "SELECT count(*) as countval 
																						FROM rep_comm_rr_level_a 
																						WHERE comm_rr = '".$row_rr["comm_rr"]."' 
																						AND comm_advisor_code = '".$comm_advisor_code."'";
															$result_check = mysql_query($qry_check) or die(mysql_error());
															while($row_check = mysql_fetch_array($result_check))
																	{
																	$countval = $row_check["countval"];
																	}
						
															//if data point exists then proceed with processing
															if ($countval > 0) { //values exist
																	$query_mqydate = "SELECT max(comm_trade_date)  AS comm_trade_date
																										FROM rep_comm_rr_level_a
																										WHERE comm_rr = '".$row_rr["comm_rr"]."'
																										AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'";
																	$result_mqydate = mysql_query($query_mqydate) or die(mysql_error());
																	while($row_mqydate = mysql_fetch_array($result_mqydate))
																	{
																	//getting the latest date value
																	$latestdate = $row_mqydate["comm_trade_date"];
																	}
															
																	$query_mqy = "SELECT * 
																								FROM rep_comm_rr_level_a
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'
																								AND comm_trade_date = '".$latestdate."'";

																	$result_mqy = mysql_query($query_mqy) or die(mysql_error());
																	while($row_mqy = mysql_fetch_array($result_mqy))
																	{
																		$comm_mtd = $row_mqy["comm_mtd"];
																		$comm_qtd = $row_mqy["comm_qtd"];
																		$comm_ytd = $row_mqy["comm_ytd"];
																	}
																	
																	//Process the numbers based on date logic
																	$is_same_year = 	samebrokyear($latestdate,$trade_date_to_process);
																	$is_same_month = 	samebrokmonth($latestdate,$trade_date_to_process);
																	$is_same_qtr = 		samebrokqtr($latestdate,$trade_date_to_process);
																	
																	if ($is_same_year == 1) {
																			if ($is_same_month == 1) {
																					$insert_mtd = $comm_mtd + $total_commission;
																					$insert_qtd = $comm_qtd + $total_commission;
																					$insert_ytd = $comm_ytd + $total_commission;						 
																			} else {
																					if ($is_same_qtr == 1) {
																						$insert_mtd = $total_commission;
																						$insert_qtd = $comm_qtd + $total_commission;
																						$insert_ytd = $comm_ytd + $total_commission;						 
																					} else {
																						$insert_mtd = $total_commission;
																						$insert_qtd = $total_commission;
																						$insert_ytd = $comm_ytd + $total_commission;						 
																					}
																			}
																	} else {
																			$insert_mtd = $total_commission;
																			$insert_qtd = $total_commission;
																			$insert_ytd = $total_commission;						 
																	}
											
															} else { //rep/advisor have no prior entry, no data points exists, just insert data
																$insert_mtd = $total_commission;
																$insert_qtd = $total_commission;
																$insert_ytd = $total_commission;						 
															}
											
														//insert into table rep_comm_rr_level_a
														$sql_level_a_insert = "INSERT INTO rep_comm_rr_level_a 
																									( comm_rr , 
																										comm_trade_date , 
																										comm_advisor_code , 
																										comm_advisor_name , 
																										comm_total , 
																										comm_mtd , 
																										comm_qtd , 
																										comm_ytd) 
																									VALUES (".
																										"'".$row_rr["comm_rr"]."',".
																										"'".$comm_trade_date_a."',". 
																										"'".$row_comm["comm_advisor_code"]."',".  
																										"'".str_replace("'","\'",$arr_clients[$row_comm["comm_advisor_code"]])."',". 
																										"'".$total_commission."',".   
																										"'".$insert_mtd."',".   
																										"'".$insert_qtd."',".   
																										"'".$insert_ytd."')";
											
														$result_level_a_insert = mysql_query($sql_level_a_insert) or die(tdw_mysql_error($sql_level_a_insert));	

												}
													
											}
											//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
							
								}
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							
							//+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C
							//Process the cancelled trades  (cancel code = 1)
							
							//get the cancelled trades
							$query_cancelled_trades = "SELECT * 
																				 FROM nfs_trades
																				 WHERE trad_run_date = '".$trade_date_to_process."'
																				 AND trad_branch = 'PDY'
																				 AND trad_cancel_code = '1'";
							//xdebug("query_cancelled_trades",$query_cancelled_trades);
							$result_cancelled_trades = mysql_query($query_cancelled_trades) or die(tdw_mysql_error($query_cancelled_trades));
							$countcancelval = 1;
							while($row_cancelled_trades = mysql_fetch_array($result_cancelled_trades))
							{
								//get the reference number
								$trad_trade_reference_number = $row_cancelled_trades["trad_trade_reference_number"];
								
								//get the original trade
								$query_get_original_trade = "SELECT * 
																							 FROM nfs_trades
																							 WHERE trad_branch = 'PDY'
																							 AND trad_cancel_code != '1'
																							 AND trad_trade_reference_number = '".$trad_trade_reference_number."'";
								$result_get_original_trade = mysql_query($query_get_original_trade) or die(tdw_mysql_error($query_get_original_trade));
								while($row_get_original_trade = mysql_fetch_array($result_get_original_trade))
								{
									//echo "INFO: Reference Number [".$row_get_original_trade["trad_trade_reference_number"]."], Original Trade Date: [".$row_get_original_trade["trad_trade_date"]."], and cancelled on [".$trade_date_to_process."]<br>";
									//Tables impacted
								 	//rep_comm_rr_trades
									//rep_comm_rr_level_b 
									//rep_comm_rr_level_a 
									//Update these tables with relevant data/numbers
									
									//>>>>>>>>>>>> FIRST rep_comm_rr_trades		
									//The trad_is_cancelled field needs to be updated with the value 1 (Default is zero)			
									
									//IMPORTANT (set the trade_date to the run_date for the cancelled trade to show the date of cancel to back/office
									//in this case the run_date is the trade_date_to_process
									$query_update_cancel = "UPDATE rep_comm_rr_trades
																							 SET trad_is_cancelled = 1,
																							     trad_run_date = '".$trade_date_to_process."'
																							 WHERE trad_reference_number = '".$trad_trade_reference_number."'";
									$result_update_cancel = mysql_query($query_update_cancel) or die(tdw_mysql_error($query_update_cancel));

									$ot_comm_trade_reference_number = 	$row_get_original_trade["trad_trade_reference_number"];
									$ot_trad_full_account_number = 			trim($row_get_original_trade["trad_full_account_number"]);
									$ot_trad_short_name = 							trim($row_get_original_trade["trad_short_name"]);
									$ot_comm_rr = 											trim($row_get_original_trade["trad_registered_rep"]);
									$ot_comm_trade_date = 							$row_get_original_trade["trad_trade_date"];
									$ot_comm_advisor_code = 						$arr_acct_adv[$row_get_original_trade["trad_full_account_number"]];
									$ot_comm_advisor_name = 						str_replace("'","\'",$arr_clients[substr($row_get_original_trade["trad_short_name"],0,4)]);
									$ot_comm_account_name = 						str_replace("'","\'",get_account_name($row_get_original_trade["trad_full_account_number"]));
									$ot_comm_account_number = 					trim($row_get_original_trade["trad_full_account_number"]);
									$ot_comm_symbol = 									trim($row_get_original_trade["trad_symbol"]);
									$ot_comm_buy_sell = 								trim($row_get_original_trade["trad_buy_sell"]);
									$ot_comm_quantity = 								$row_get_original_trade["trad_quantity"];
									$ot_comm_price = 										$row_get_original_trade["trad_price"];
									$ot_comm_commission_code = 					$row_get_original_trade["trad_commission_concession_code"];
									$ot_comm_commission = 							$row_get_original_trade["trad_trade_commission"];
									
									//>>>>>>>>>>>> SECOND rep_comm_rr_level_b
									//All commission numbers need to be updated
									//Need trade date, advisor, account, commission and rr
									//This date data point needs to be adjusted for this cancelled trade.
									//There HAS TO BE a datapoint for this date so go ahead and reduce the number by this commission amount
									
									$query_update_cancel_b = "UPDATE rep_comm_rr_level_b
																				 	  SET comm_total = (comm_total - ".$ot_comm_commission."), 
																							  comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																							  comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																							  comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																						WHERE comm_account_number = '".$ot_comm_account_number."'
																						AND comm_trade_date = '".$ot_comm_trade_date."'
																						AND comm_rr = '".$ot_comm_rr."'";
									 $result_update_b =  mysql_query($query_update_cancel_b) or die (tdw_mysql_error($query_update_cancel_b));

									 //if last day of month do not change the mtd
									 //process for the next 4 business days 
									 for ($bd=1; $bd<5; $bd++) {
											$process_cancel_for_date = business_day_forward(strtotime($ot_comm_trade_date), $bd);
											$is_same_year_bd = 	  samebrokyear($process_cancel_for_date,$ot_comm_trade_date);
											$is_same_month_bd = 	samebrokmonth($process_cancel_for_date,$ot_comm_trade_date);
											$is_same_qtr_bd = 		samebrokqtr($process_cancel_for_date,$ot_comm_trade_date);
									 				
																	if ($is_same_year_bd == 1) {
																			if ($is_same_month_bd == 1) {
																				 $query_update_cancel_b = "UPDATE rep_comm_rr_level_b
																																SET comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																																		comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																																		comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																WHERE comm_account_number = '".$ot_comm_account_number."'
																																AND comm_rr = '".$ot_comm_rr."'
																																AND comm_trade_date = '".$process_cancel_for_date."'";
																			} else {
																					if ($is_same_qtr_bd == 1) {
																				 $query_update_cancel_b = "UPDATE rep_comm_rr_level_b
																																SET comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																																		comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																WHERE comm_account_number = '".$ot_comm_account_number."'
																																AND comm_rr = '".$ot_comm_rr."'
																																AND comm_trade_date = '".$process_cancel_for_date."'";
																					} else {
																				 $query_update_cancel_b = "UPDATE rep_comm_rr_level_b
																																SET comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																WHERE comm_account_number = '".$ot_comm_account_number."'
																																AND comm_rr = '".$ot_comm_rr."'
																																AND comm_trade_date = '".$process_cancel_for_date."'";
																					}
																			}
																	} else {
																			 //Do nothing
																  }						 
																 //xdebug("query_update_cancel_b",$query_update_cancel_b);
																 $result_update_b =  mysql_query($query_update_cancel_b) or die (tdw_mysql_error($query_update_cancel_b));
									 
									 }
									 
									 
									//>>>>>>>>>>>> THIRD rep_comm_rr_level_0
									//All commission numbers need to be updated
									//Need trade date, advisor, account, commission and rr
									//This date data point needs to be adjusted for this cancelled trade.
									//There HAS TO BE a datapoint for this date so go ahead and reduce the number by this commission amount
									
									$query_update_cancel_0 = "UPDATE rep_comm_rr_level_0
																				 	  SET comm_total = (comm_total - ".$ot_comm_commission."), 
																							  comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																							  comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																							  comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																						WHERE comm_trade_date = '".$ot_comm_trade_date."'
																						AND comm_rr = '".$ot_comm_rr."'";
									 $result_update_0 =  mysql_query($query_update_cancel_0) or die (tdw_mysql_error($query_update_cancel_0));

									 //if last day of month do not change the mtd
									 //process for the next 4 business days 
									 for ($bd=1; $bd<5; $bd++) {
											$process_cancel_for_date = business_day_forward(strtotime($ot_comm_trade_date), $bd);
											$is_same_year_bd = 	  samebrokyear($process_cancel_for_date,$ot_comm_trade_date);
											$is_same_month_bd = 	samebrokmonth($process_cancel_for_date,$ot_comm_trade_date);
											$is_same_qtr_bd = 		samebrokqtr($process_cancel_for_date,$ot_comm_trade_date);
									 				
																	if ($is_same_year_bd == 1) {
																			if ($is_same_month_bd == 1) {
																				 $query_update_cancel_0 = "UPDATE rep_comm_rr_level_0
																																SET comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																																		comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																																		comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																WHERE comm_rr = '".$ot_comm_rr."'
																																AND comm_trade_date = '".$process_cancel_for_date."'";
																			} else {
																					if ($is_same_qtr_bd == 1) {
																				 $query_update_cancel_0 = "UPDATE rep_comm_rr_level_0
																																SET comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																																		comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																WHERE comm_rr = '".$ot_comm_rr."'
																																AND comm_trade_date = '".$process_cancel_for_date."'";
																					} else {
																				 $query_update_cancel_0 = "UPDATE rep_comm_rr_level_0
																																SET comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																WHERE comm_rr = '".$ot_comm_rr."'
																																AND comm_trade_date = '".$process_cancel_for_date."'";
																					}
																			}
																	} else {
																			 //Do nothing
																  }						 
																 //xdebug("query_update_cancel_b",$query_update_cancel_b);
																 $result_update_0 =  mysql_query($query_update_cancel_0) or die (tdw_mysql_error($query_update_cancel_0));
									 
									 }

									 //>>>>>>>>>>>>> FOURTH rep_comm_rr_level_a
									 //correction: cancel logic updated
									 
									 //for the trade date of the cancel trade apply the following
									 $query_update_cancel_a = "UPDATE rep_comm_rr_level_a
																				 	  SET comm_total = (comm_total - ".$ot_comm_commission."), 
																							  comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																							  comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																							  comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																						WHERE comm_advisor_code = '".$ot_comm_advisor_code."'
																						AND comm_rr = '".$ot_comm_rr."'
																						AND comm_trade_date = '".$ot_comm_trade_date."'";
  								 if ($ot_comm_advisor_code == 'SENT') {
									 //xdebug("query_update_cancel_a",$query_update_cancel_a);
									 }
									 $result_update_a =  mysql_query($query_update_cancel_a) or die (tdw_mysql_error($query_update_cancel_a));
									 
									 //for the trade date ahead of the cancel trade apply the following
									 //changed logic go forward 4 business days
									 for ($bd=1; $bd<5; $bd++) {
											$process_cancel_for_date = business_day_forward(strtotime($ot_comm_trade_date), $bd);
											$is_same_year_bd = 	  samebrokyear($process_cancel_for_date,$ot_comm_trade_date);
											$is_same_month_bd = 	samebrokmonth($process_cancel_for_date,$ot_comm_trade_date);
											$is_same_qtr_bd = 		samebrokqtr($process_cancel_for_date,$ot_comm_trade_date);
									 				
																	if ($is_same_year_bd == 1) {
																			if ($is_same_month_bd == 1) {
																				 $query_update_cancel_a = "UPDATE rep_comm_rr_level_a
																																	SET comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																																			comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																																			comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																	WHERE comm_advisor_code = '".$ot_comm_advisor_code."'
																																	AND comm_rr = '".$ot_comm_rr."'
																																	AND comm_trade_date = '".$process_cancel_for_date."'";
																			} else {
																					if ($is_same_qtr_bd == 1) {
																				 $query_update_cancel_a = "UPDATE rep_comm_rr_level_a
																																	SET comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																																			comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																	WHERE comm_advisor_code = '".$ot_comm_advisor_code."'
																																	AND comm_rr = '".$ot_comm_rr."'
																																	AND comm_trade_date = '".$process_cancel_for_date."'";
																					} else {
																				 $query_update_cancel_a = "UPDATE rep_comm_rr_level_a
																																	SET comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																																	WHERE comm_advisor_code = '".$ot_comm_advisor_code."'
																																	AND comm_rr = '".$ot_comm_rr."'
																																	AND comm_trade_date = '".$process_cancel_for_date."'";
																					}
																			}
																	} else {
																			 //Do nothing
																  }						 
																 if ($ot_comm_advisor_code == 'SENT') {
																 //xdebug("query_update_cancel_a",$query_update_cancel_a);
																 }
																 $result_update_a =  mysql_query($query_update_cancel_a) or die (tdw_mysql_error($query_update_cancel_a));
									 		}

								}
																							 
							}
							
						  echo " ". sprintf("%01.7f",((getmicrotime()-$timetrack)/1000))." s."; 						
							//+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C							
			}
}

//Flush the memory tables used by production pages/app
$result_mry_comm_rr_level_0_flush = mysql_query("truncate table mry_comm_rr_level_0") or die (mysql_error());
$result_mry_comm_rr_level_a_flush = mysql_query("truncate table mry_comm_rr_level_a") or die (mysql_error());
$result_mry_comm_rr_level_b_flush = mysql_query("truncate table mry_comm_rr_level_b") or die (mysql_error());
$result_mry_comm_rr_trades_flush =  mysql_query("truncate table mry_comm_rr_trades") or die (mysql_error());

//Populate tables

$result_mry_comm_rr_level_0_populate = mysql_query("insert into mry_comm_rr_level_0 select * from rep_comm_rr_level_0") or die (mysql_error());
$result_mry_comm_rr_level_a_populate = mysql_query("insert into mry_comm_rr_level_a select * from rep_comm_rr_level_a") or die (mysql_error());
$result_mry_comm_rr_level_b_populate = mysql_query("insert into mry_comm_rr_level_b select * from rep_comm_rr_level_b") or die (mysql_error());
$result_mry_comm_rr_trades_populate = mysql_query("insert into mry_comm_rr_trades select * from rep_comm_rr_trades") or die (mysql_error());


//show page load time
	echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 						
?>