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

$trade_date_to_process = previous_business_day();


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

//Create an array of account names and advisor code for lookup.
$qry_acct_adv = "select nadd_full_account_number, nadd_advisor from mry_nfs_nadd";
$result_acct_adv = mysql_query($qry_acct_adv) or die (tdw_mysql_error($qry_acct_adv));
$arr_acct_adv = array();
while ( $row_acct_adv = mysql_fetch_array($result_acct_adv) ) 
{
	$arr_acct_adv[strtoupper(trim($row_acct_adv["nadd_full_account_number"]))] = $row_acct_adv["nadd_advisor"];
}

//Process a certain date (generally the previous business day)
//$trade_date_to_process = previous_business_day();
//Now the above information is obtained from the including page.


							xdebug('trade_date_to_process',$trade_date_to_process);
							echo "\n";
							
							//exit;
							
							//begin track time taken for the day
							$timetrack=getmicrotime(); 
							
							$result_comm_flush = mysql_query("truncate table mry_emp_trades_temp") or die (mysql_error());
							echo "mry_emp_trades_temp is flushed and ready for the next set of data<br>";
              
							
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++						
						  //get only regular trades, not the cancelled trades, the cancelled trades will be 
							//processed in a separate section at the end of segment	
								$query_trades = "SELECT * 
																 FROM nfs_trades
																 WHERE trad_run_date = '".$trade_date_to_process."'
																 AND trad_branch = 'PDZ'
																 AND trad_cancel_code != '1'";
	  						xdebug ("query_trades",$query_trades);
  							$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								$countval = 1;
								while($row_trades = mysql_fetch_array($result_trades))
								{
									//get data to insert into temp table to process further
									$comm_trade_reference_number = 	trim($row_trades["trad_trade_reference_number"]);
									$trad_order_reference_number = 	trim($row_trades["trad_order_reference_number"]);
									$trad_full_account_number = 		trim($row_trades["trad_full_account_number"]);
									$trad_short_name = 							str_replace("'","",trim($row_trades["trad_short_name"]));
									$comm_rr = 											trim($row_trades["trad_registered_rep"]);
									$comm_trade_date = 							$row_trades["trad_trade_date"];
									$comm_run_date = 							  $row_trades["trad_run_date"];
									$comm_settle_date =             $row_trades["trad_settle_date"]; 
									
									$comm_order_time =              $row_trades["trad_trade_entry"];									
									$comm_exec_time =               $row_trades["trad_execution_time_a"];
									
									//xdebug("comm_order_time",$comm_order_time);
									//xdebug("comm_exec_time",$comm_exec_time);
									
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

                  if ($comm_cents_per_share > 10) {
									  $comm_cents_per_share = 0;
									}
									
									if (strlen($comm_symbol) == 0) {
										$comm_symbol = trim($row_trades["trad_cusip"]);
									}

									$qry_insert_trade = "insert into mry_emp_trades_temp(
																			comm_trade_reference_number,
																			comm_order_reference_number,
																			comm_rr, 
																			comm_trade_date, 
																			comm_run_date,
																			comm_settle_date, 
																			comm_order_time,
																			comm_exec_time,
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
																			"'".$trad_order_reference_number."',".
																			"'".$comm_rr."',".
																			"'".$comm_trade_date."',". 
																			"'".$comm_run_date."',". 
																			"'".$comm_settle_date."',". 
																			"'".$comm_order_time."',". 
																			"'".$comm_exec_time."',". 
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


											//_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_
											//PROCESS FOR TABLE: emp_employee_trades 
											//fields in table mry_comm_rr : 
											//comm_trade_reference_number  comm_rr  comm_trade_date  comm_advisor_code comm_advisor_name  
											//comm_account_name  comm_account_number  comm_symbol  comm_buy_sell  
											//comm_quantity  comm_price  comm_commission_code  comm_commission  comm_cents_per_share 
											$query_comm_trd =  "SELECT *
																					FROM mry_emp_trades_temp"; 
											$result_comm_trd = mysql_query($query_comm_trd) or die(mysql_error());
											
											while($row_comm_trd = mysql_fetch_array($result_comm_trd))
											{
												$qyery_insert_trade = "INSERT INTO emp_employee_trades 
																								(trad_reference_number,
																								trad_order_reference_number,
																								trad_rr,
																								trad_trade_date,
																								trad_run_date,
																								trad_settle_date,
																								trad_order_time,
																								trad_exec_time,
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
																								"'".$row_comm_trd["comm_order_reference_number"]."',".
																								"'".$row_comm_trd["comm_rr"]."',".
																								"'".$row_comm_trd["comm_trade_date"]."',".
																								"'".$row_comm_trd["comm_run_date"]."',".
																								"'".$row_comm_trd["comm_settle_date"]."',".
																								"'".$row_comm_trd["comm_order_time"]."',".
																								"'".$row_comm_trd["comm_exec_time"]."',".
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

							//// Processing from temporary table.
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							
							//+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C
							//Process the cancelled trades  (cancel code = 1)
							
							//get the cancelled trades
							$query_cancelled_trades = "SELECT * 
																				 FROM nfs_trades
																				 WHERE trad_run_date = '".$trade_date_to_process."'
																				 AND trad_branch = 'PDZ'
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
																							 WHERE trad_branch = 'PDZ'
																							 AND trad_cancel_code != '1'
																							 AND trad_trade_reference_number = '".$trad_trade_reference_number."'";
								$result_get_original_trade = mysql_query($query_get_original_trade) or die(tdw_mysql_error($query_get_original_trade));
								while($row_get_original_trade = mysql_fetch_array($result_get_original_trade))
								{
									//>>>>>>>>>>>> FIRST rep_comm_rr_trades		
									//The trad_is_cancelled field needs to be updated with the value 1 (Default is zero)			
									
									//IMPORTANT (set the trade_date to the run_date for the cancelled trade to show the date of cancel to back/office
									//in this case the run_date is the trade_date_to_process
									$query_update_cancel = "UPDATE emp_employee_trades
																							 SET trad_is_cancelled = 1,
																							     trad_run_date = '".$trade_date_to_process."'
																							 WHERE trad_reference_number = '".$trad_trade_reference_number."'";
									$result_update_cancel = mysql_query($query_update_cancel) or die(tdw_mysql_error($query_update_cancel));
								}
							}

						  echo " ". sprintf("%01.7f",((getmicrotime()-$timetrack)/1000))." s."; 						
							//+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C+C							
							ob_flush();
							flush();




//show page load time
	echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 						
?>