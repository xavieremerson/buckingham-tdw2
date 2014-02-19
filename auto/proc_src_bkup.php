<?
ini_set('max_execution_time', 3600);
?>

<?
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//Create Lookup Array of Client Code / Client Name
	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (mysql_error());
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}

//Get the Name/Address information into Memory Table for lookup purposes
$result_nadd_flush = mysql_query("truncate table mry_nfs_nadd") or die (mysql_error());
$result_nadd_populate = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (mysql_error());

//Flush the data tables because this process starts from "DAY1"
$result_arc_comm_rr_flush         = mysql_query("truncate table arc_comm_rr")         or die (mysql_error());
$result_rep_comm_rr_level_a_flush = mysql_query("truncate table rep_comm_rr_level_a") or die (mysql_error());
$result_rep_comm_rr_level_b_flush = mysql_query("truncate table rep_comm_rr_level_b") or die (mysql_error());
$result_rep_comm_rr_trades_flush  = mysql_query("truncate table rep_comm_rr_trades")  or die (mysql_error());
  
//Process a certain date (generally the previous business day)
//$trade_date_to_process = previous_business_day();
for ($bizdays=1; $bizdays < 80; $bizdays++) {

			if (strtotime(business_day_forward(strtotime("2006-01-17"),$bizdays)) > strtotime("now")) {
				echo business_day_forward(strtotime("2006-01-17"),$bizdays)."<br>";
				echo "Exit condition met... Program exiting normally<br>";
				
			} else {
							$trade_date_to_process = business_day_forward(strtotime("2006-01-17"),$bizdays);
							xdebug('trade_date_to_process',$trade_date_to_process);
							
							$result_comm_arc = mysql_query("insert into arc_comm_rr select * from mry_comm_rr") or die(mysql_error());
							$result_comm_flush = mysql_query("truncate table mry_comm_rr") or die (mysql_error());
							echo "mry_comm_rr is flushed and ready for the next set of data<br>";
							//$trade_date_to_process = '2006-01-18';
							//ydebug('trade_date_to_process',$trade_date_to_process);
							
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++						
							//get the trade for this day //trad_run_date caused problems (CAROL)
						  //get only regular trades, not the cancelled trades, the cancelled trades will be 
							//processed in a separate section at the end of segment	 (CAROL)
								$query_trades = "SELECT * 
																 FROM nfs_trades
																 WHERE trad_trade_date = '".$trade_date_to_process."'
																 AND trad_branch = 'PDY'
																 AND trad_cancel_code != '1'";
																 
  							$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								$countval = 1;
								while($row_trades = mysql_fetch_array($result_trades))
								{
									//get data to insert into temp table to process further
									$comm_trade_reference_number = 	$row_trades["trad_trade_reference_number"];
									$trad_full_account_number = 		trim($row_trades["trad_full_account_number"]);
									$trad_short_name = 							trim($row_trades["trad_short_name"]);
									
									//This is being changed to trad_registered_rep
									//during reconciling found out that the rep who gets the commission is the trad_registered_rep
									//found that from the file provided by Lloyd Karp for FEB trades
									
									//$comm_rr = 											trim($row_trades["trad_rr_owning_rep"]);
									
									//NOT CHANGING ANYTHING YET.... PENDING MORE DISCOVERY (PRAVIN)
									$comm_rr = 											trim($row_trades["trad_rr_owning_rep"]);

									$comm_trade_date = 							$row_trades["trad_trade_date"];
									$comm_advisor_code = 						substr($row_trades["trad_short_name"],0,4);
									$comm_advisor_name = 						str_replace("'","\'",$arr_clients[substr($row_trades["trad_short_name"],0,4)]);
									$comm_account_name = 						str_replace("'","\'",get_account_name($row_trades["trad_full_account_number"]));
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
											
											
											//_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
											//PROCESS FOR TABLE: rep_comm_rr_level_b
											//get aggregated commission for each subaccount
											$query_comm_sa =  "SELECT comm_account_number , 
											                          sum( comm_commission ) as total_commission_sa, 
																								max( comm_advisor_code ) as comm_advisor_code_sa 
																					FROM mry_comm_rr
																					WHERE comm_rr = '".$row_rr["comm_rr"]."'
																					GROUP BY comm_account_number
																					ORDER BY comm_account_number"; 
											$result_comm_sa = mysql_query($query_comm_sa) or die(mysql_error());
											
											while($row_comm_sa = mysql_fetch_array($result_comm_sa))
											{
												$comm_account_number_sa = $row_comm_sa["comm_account_number"];
												$total_commission_sa = 	$row_comm_sa["total_commission_sa"];
												$comm_advisor_code_sa = $row_comm_sa["comm_advisor_code_sa"];
												//get mtd, qtd, ytd data for the current subaccount for the latest data point available
												
													//get latest date data point for the given rr and subaccount
													
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
															$is_same_year_sa = sameyear($latestdate_sa,$trade_date_to_process);
															$is_same_month_sa = samemonth($latestdate_sa,$trade_date_to_process);
															$is_same_qtr_sa = sameqtr($latestdate_sa,$trade_date_to_process);
															
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
									
												//insert into table rep_comm_rr_level_a
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
																								"'".$trade_date_to_process."',". 
																								"'".$comm_advisor_code_sa."',".  
																								"'".get_account_name($comm_account_number_sa)."',". 
																								"'".$comm_account_number_sa."',". 
																								"'".$total_commission_sa."',".   
																								"'".$insert_mtd_sa."',".   
																								"'".$insert_qtd_sa."',".   
																								"'".$insert_ytd_sa."')";
									
												$result_level_b_insert = mysql_query($sql_level_b_insert) or die(tdw_mysql_error($sql_level_b_insert));	
											}

											//_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_^_
									
											//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
											//PROCESS FOR TABLE: rep_comm_rr_level_a 
											//get aggregated commissions for each client/advisor
											$query_comm =  "SELECT comm_advisor_code , sum( comm_commission ) as total_commission 
																			FROM mry_comm_rr
																			WHERE comm_rr = '".$row_rr["comm_rr"]."'
																			GROUP BY comm_advisor_code
																			ORDER BY comm_advisor_code";
											$result_comm = mysql_query($query_comm) or die(mysql_error());
									
											while($row_comm = mysql_fetch_array($result_comm))
											{
												$total_commission  = $row_comm["total_commission"];
												$comm_advisor_code = $row_comm["comm_advisor_code"];
												
												$echo_str = $trade_date_to_process ."/" .$comm_advisor_code ."/". $total_commission."<br>";
												if ($row_rr["comm_rr"] == '035' AND $comm_advisor_code == '3CAP' AND $trade_date_to_process == '2006-02-28') { echo $echo_str."<br>";}
												//get mtd, qtd, ytd data for the current rr for the latest data point available
												
													//get latest date data point for the given rr and advisor
													
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
				
													//$echo_str = "Finding if data points exist for the RR/Advisor ".$row_rr["comm_rr"]."/".$row_comm["comm_advisor_code"]." for ".$trade_date_to_process."<br>";
													//if ($row_rr["comm_rr"] == '035' AND $comm_advisor_code == '3CAP') { echo $echo_str."<br>";}
													//xdebug("countval",$countval);
													
													//if data point exists then proceed with processing
													if ($countval > 0) { //values exist
															//echo "Processing where countval is more than 0<br>";
															$query_mqydate = "SELECT max(comm_trade_date)  AS comm_trade_date
																								FROM rep_comm_rr_level_a
																								WHERE comm_rr = '".$row_rr["comm_rr"]."'
																								AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'";
															//echo $query_mqydate."<br>";
															$result_mqydate = mysql_query($query_mqydate) or die(mysql_error());
															while($row_mqydate = mysql_fetch_array($result_mqydate))
															{
															//getting the latest date value
															$latestdate = $row_mqydate["comm_trade_date"];
	  													//xdebug ("latestdate",$latestdate);
															}
													
															$query_mqy = "SELECT * 
																		FROM rep_comm_rr_level_a
																		WHERE comm_rr = '".$row_rr["comm_rr"]."'
																		AND comm_advisor_code = '".$row_comm["comm_advisor_code"]."'
																		AND comm_trade_date = '".$latestdate."'";
															//echo $query_mqy."<br>";
															$result_mqy = mysql_query($query_mqy) or die(mysql_error());
															while($row_mqy = mysql_fetch_array($result_mqy))
															{
															$comm_mtd = $row_mqy["comm_mtd"];
															$comm_qtd = $row_mqy["comm_qtd"];
															$comm_ytd = $row_mqy["comm_ytd"];
															}
															
															//Process the numbers based on date logic
															$is_same_year = 	sameyear($latestdate,$trade_date_to_process);
															$is_same_month = 	samemonth($latestdate,$trade_date_to_process);
															$is_same_qtr = 		sameqtr($latestdate,$trade_date_to_process);
															
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
														//echo "Processing where countval is 0<br>";
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
																								"'".$trade_date_to_process."',". 
																								"'".$row_comm["comm_advisor_code"]."',".  
																								"'".str_replace("'","\'",$arr_clients[$row_comm["comm_advisor_code"]])."',". 
																								"'".$total_commission."',".   
																								"'".$insert_mtd."',".   
																								"'".$insert_qtd."',".   
																								"'".$insert_ytd."')";
									
												$result_level_a_insert = mysql_query($sql_level_a_insert) or die(tdw_mysql_error($sql_level_a_insert));	
												
												//echo "=>".$row_rr["comm_rr"]."=>".$row_comm["comm_advisor_code"]."=>".$row_comm["total_commission"]."<br>";
											}
											//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
							
								}
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							
							//+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C
							//Process the cancelled trades  (cancel code = 1)
							
							//get the cancelled trades
							$query_cancelled_trades = "SELECT * 
																				 FROM nfs_trades
																				 WHERE trad_trade_date = '".$trade_date_to_process."'
																				 AND trad_branch = 'PDY'
																				 AND trad_cancel_code = '1'";
																 
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
									//show information
									//echo "INFO: Reference Number [".$row_get_original_trade["trad_trade_reference_number"]."], Original Trade Date: [".$row_get_original_trade["trad_trade_date"]."], and cancelled on [".$trade_date_to_process."]<br>";
									//Tables impacted
								 	//rep_comm_rr_trades
									//rep_comm_rr_level_b 
									//rep_comm_rr_level_a 
									//Update these tables with relevant data/numbers
									
									//First rep_comm_rr_trades		
									//The trad_is_cancelled field needs to be updated with the value 1 (Default is zero)			
									$query_update_cancel = "UPDATE rep_comm_rr_trades
																							 SET trad_is_cancelled = 1 
																							 WHERE trad_reference_number = '".$trad_trade_reference_number."'";
									xdebug("query_update_cancel",$query_update_cancel);
									$result_update_cancel = mysql_query($query_update_cancel) or die(tdw_mysql_error($query_update_cancel));

									//Second rep_comm_rr_level_b
									//All commission numbers need to be updated
									//Need trade date, advisor, account, commission and rr
									//show_array($row_get_original_trade);
									//get the required values
									$ot_comm_trade_reference_number = 	$row_get_original_trade["trad_trade_reference_number"];
									$ot_trad_full_account_number = 			trim($row_get_original_trade["trad_full_account_number"]);
									$ot_trad_short_name = 							trim($row_get_original_trade["trad_short_name"]);
									$ot_comm_rr = 											trim($row_get_original_trade["trad_rr_owning_rep"]);
									$ot_comm_trade_date = 							$row_get_original_trade["trad_trade_date"];
									$ot_comm_advisor_code = 						substr($row_get_original_trade["trad_short_name"],0,4);
									$ot_comm_advisor_name = 						str_replace("'","\'",$arr_clients[substr($row_get_original_trade["trad_short_name"],0,4)]);
									$ot_comm_account_name = 						str_replace("'","\'",get_account_name($row_get_original_trade["trad_full_account_number"]));
									$ot_comm_account_number = 					trim($row_get_original_trade["trad_full_account_number"]);
									$ot_comm_symbol = 									trim($row_get_original_trade["trad_symbol"]);
									$ot_comm_buy_sell = 								trim($row_get_original_trade["trad_buy_sell"]);
									$ot_comm_quantity = 								$row_get_original_trade["trad_quantity"];
									$ot_comm_price = 										$row_get_original_trade["trad_price"];
									$ot_comm_commission_code = 					$row_get_original_trade["trad_commission_concession_code"];
									$ot_comm_commission = 							$row_get_original_trade["trad_trade_commission"];
									
									//This date data point needs to be adjusted for this cancelled trade.
									//There HAS TO BE a datapoint for this date so go ahead and reduce the number by this commission amount
									//echo "<b>Process this: ".$ot_comm_trade_date."</b><br>";
									$query_update_cancel_b = "UPDATE rep_comm_rr_level_b
																				 	  SET comm_total = (comm_total - ".$ot_comm_commission."), 
																							  comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																							  comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																							  comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																						WHERE comm_account_number = '".$ot_comm_account_number."'
																						AND comm_trade_date = '".$ot_comm_trade_date."'";
									 //echo $query_update_cancel_b."<br>";
									 $result_update_b =  mysql_query($query_update_cancel_b) or die (tdw_mysql_error($query_update_cancel_b));
									 
									 //Third rep_comm_rr_level_a
									$query_update_cancel_a = "UPDATE rep_comm_rr_level_a
																				 	  SET comm_total = (comm_total - ".$ot_comm_commission."), 
																							  comm_mtd = (comm_mtd - ".$ot_comm_commission."), 												
																							  comm_qtd = (comm_qtd - ".$ot_comm_commission."), 
																							  comm_ytd = (comm_ytd - ".$ot_comm_commission.")
																						WHERE comm_advisor_code = '".$ot_comm_advisor_code."'
																						AND comm_trade_date = '".$ot_comm_trade_date."'";
									 echo $query_update_cancel_a."<br>";
									 $result_update_a =  mysql_query($query_update_cancel_a) or die (tdw_mysql_error($query_update_cancel_a));

								}
																							 
							}
							
							//+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C							
			}
}

//Flush the memory tables used by production pages/app
$result_mry_comm_rr_level_a_flush = mysql_query("truncate table mry_comm_rr_level_a") or die (mysql_error());
$result_mry_comm_rr_level_b_flush = mysql_query("truncate table mry_comm_rr_level_b") or die (mysql_error());
$result_mry_comm_rr_trades_flush =  mysql_query("truncate table mry_comm_rr_trades") or die (mysql_error());

//Populate tables

$result_mry_comm_rr_level_a_populate = mysql_query("insert into mry_comm_rr_level_a select * from rep_comm_rr_level_a") or die (mysql_error());
$result_mry_comm_rr_level_b_populate = mysql_query("insert into mry_comm_rr_level_b select * from rep_comm_rr_level_b") or die (mysql_error());
$result_mry_comm_rr_trades_populate = mysql_query("insert into mry_comm_rr_trades select * from rep_comm_rr_trades") or die (mysql_error());


?>