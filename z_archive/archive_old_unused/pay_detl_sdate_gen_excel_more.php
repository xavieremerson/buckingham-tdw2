<?
foreach($master_array_exception as $km=>$vm) {

							//%%%%%%%%%%%%%%%%
							//Some names for REP NUMBERS
							if ($vm == '999' OR get_user_by_id($vm)=='') {
								$display_name = "999";
							} else {
								$display_name = get_user_by_id($vm);
							  //xdebug("display_name",$display_name);
							//%%%%%%%%%%%%%%%%
							
							$process_userid = $vm;
							
							$wks =& $wkb->addWorksheet($display_name);
							$wks->setLandscape ();
							$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);

		
							//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEETS
							include('pay_detl_sdate_gen_excel_header.php');
							
							$wks->write(3, 2, "Shrd");
							$wks->writeString(3, 3, ' '.$row_trades["trad_rr"].' ');
							$wks->write(3, 4, $display_name,$format_data_3);

							$count_row_i = 4;							
							$condition_for_shrd_totals = 0;

							$user_id = $vm;
								
							$query_reps_shared = "SELECT srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_user_id = '".$user_id."' AND srep_rrnum != ''";
							//xdebug("query_reps_shared",$query_reps_shared);
							$result_reps_shared = mysql_query($query_reps_shared) or die (tdw_mysql_error($query_reps_shared));
							$str_reps_shared = "";
							while ($row_reps_shared = mysql_fetch_array($result_reps_shared) ) {
								$str_reps_shared = $row_reps_shared["srep_rrnum"]."|". $str_reps_shared;
							}
							
							
							//xdebug("str_reps_shared",$str_reps_shared);
							//Create the SQL String
							$arr_shared_reps = array();
							$arr_shared_reps = explode("|", $str_reps_shared);
							
							$str_sql_clause = '';
							foreach($arr_shared_reps as $key=>$value) {
									if ($value != '') {
										$str_sql_clause .= " OR trad_rr = '".$value."'";
									}
							}

							//xdebug("str_sql_clause",$str_sql_clause);

							$hold_for_grand_total = $count_row_i+1;

							$query_trades_shared = "SELECT 
																				trad_rr,
																				FORMAT(sum(trad_commission),2) as trad_commission,
																				sum(trad_commission) as for_sum_trad_commission
																			FROM mry_comm_rr_trades 
																			WHERE trad_is_cancelled = 0 
																			AND trad_settle_date between '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
																			AND trad_rr not like '0%'
																			AND (trad_rr = '1234567890' ".$str_sql_clause.")  												
																			GROUP BY trad_rr 
																			ORDER BY trad_rr";
							//xdebug("query_trades_shared",$query_trades_shared);
							$result_trades_shared = mysql_query($query_trades_shared) or die (tdw_mysql_error($query_trades_shared));
							$count_row_j = $count_row_i+1;
							$hold_count_j = $count_row_j;
							while ($row_trades_shared = mysql_fetch_array($result_trades_shared) ) 
							{
								$condition_for_shrd_totals = 1;
								
								$wks->write($count_row_j, 2, "Shrd");
								$wks->write($count_row_j, 3, ' '.$row_trades_shared["trad_rr"].' ');
								$wks->write($count_row_j, 4, get_repname_by_rr_num($row_trades_shared["trad_rr"]),$format_data_3);



								//$wks->writeNumber($count_row_j, 6, $row_trades_shared["for_sum_trad_commission"],$format_currency_1);
								
								//$count_row_j = $count_row_j + 1;
								//$wks->writeRow($count_row_j,0," ");
										//@@@
										$qry_client_comm_s = "SELECT trad_advisor_code, max(trad_advisor_name) as clnt_name , 
																					sum(trad_commission) as clnt_comm 
																					FROM mry_comm_rr_trades 
																					WHERE trad_settle_date BETWEEN '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
																					AND trad_rr = '".$row_trades_shared["trad_rr"]."'
																					AND trad_is_cancelled = 0
																					GROUP BY trad_advisor_code 
																					ORDER BY trad_advisor_code";
										$result_client_comm_s = mysql_query($qry_client_comm_s) or die (tdw_mysql_error($qry_client_comm_s));
										$count_row_k = $count_row_j+1;
										$processed_client_shared = array();
										while ($row_client_comm_s = mysql_fetch_array($result_client_comm_s) ) 
										{
												$processed_client_shared[] = $row_client_comm_s["trad_advisor_code"];

												$wks->write($count_row_k, 2, " ");
												$wks->write($count_row_k, 3, ' '." ".' ');
												$wks->write($count_row_k, 4, $row_client_comm_s["clnt_name"],$format_data_1);
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
										 if (!in_array($clnt,$processed_client_shared) AND $rr == $row_trades_shared["trad_rr"]) {
													if ($arr_clients[$clnt] == '') {
														$clnt_name = "[".$clnt."]";
													} else {
														$clnt_name = $arr_clients[$clnt];
													}
											$wks->write($count_row_k, 4, $clnt_name,$format_data_1);
											$wks->writeNumber($count_row_k, 7, $arr_checks[$clnt],$format_currency_1);
											$wks->writeFormula($count_row_k, 8, '='.$arr_xl_cols[6].($count_row_k+1)."+".$arr_xl_cols[8].($count_row_k+1) ,$format_currency_1);
											$wks->writeFormula($count_row_k, 10, '='.$arr_xl_cols[9].($count_row_k+1)."*".$payout_multiplier_shared,$format_currency_1);
		
											
											//PUT SPECIAL RATE LOGIC HERE
											//TODO
											
											
											//get rolling 12 months data
											$rolling_total = 0;
											//if (in_array($clnt,$arr_nest_client_list)) {
												$rolling_total = get_rcc ($arr_rcc, $rr, $clnt);
											//}
													if ($rolling_total > 15000 or in_array($clnt,$arr_clients_cutoff_exceptions)) {
												$cond_format = $format_currency_1;
												$wks->writeFormula($count_row_k, 19, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*1",$format_currency_2);
											} else {
												$cond_format = $format_currency_1;
												$wks->writeFormula($count_row_k, 14, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*(-1)", $cond_format);
												$wks->writeFormula($count_row_k, 19, '=('.$arr_xl_cols[10].($count_row_k+1)."+".$arr_xl_cols[12].($count_row_k+1).")*0",$format_currency_2);
											}
		
		
											$count_row_k = $count_row_k + 1;
										 }
									}
									//~~~~
										$count_row_j = $count_row_k;
										$count_row_j = $count_row_j + 1;
								}
							}	
							
							//Now totals for the shared rep data if any
							//put check condition here
							if ($condition_for_shrd_totals == 1) {
								//$wks->writeFormula($count_row_j, 6,  '=SUM('.$arr_xl_cols[6].($hold_count_j+1).":".$arr_xl_cols[6].($count_row_j).")",$format_currency_2);
								//$wks->writeFormula($count_row_j, 7,  '=SUM('.$arr_xl_cols[8].($hold_count_j+1).":".$arr_xl_cols[8].($count_row_j).")",$format_currency_2);
								//$wks->writeFormula($count_row_j, 8,  '=SUM('.$arr_xl_cols[9].($hold_count_j+1).":".$arr_xl_cols[9].($count_row_j).")",$format_currency_2);
								//$wks->writeFormula($count_row_j, 10, '=SUM('.$arr_xl_cols[11].($hold_count_j+1).":".$arr_xl_cols[11].($count_row_j).")",$format_currency_2);
								//$wks->writeFormula($count_row_j, 14, '=SUM('.$arr_xl_cols[15].($hold_count_j+1).":".$arr_xl_cols[15].($count_row_j).")",$format_currency_2);
								//$wks->writeFormula($count_row_j, 19, '=SUM('.$arr_xl_cols[20].($hold_count_j+1).":".$arr_xl_cols[20].($count_row_j).")",$format_currency_2);
								//end if (for rrnum check)
								$wks->writeFormula($count_row_j, 19, '=SUM('.$arr_xl_cols[19].($hold_for_grand_total+1).":".$arr_xl_cols[19].($count_row_j).")",$format_currency_2);
								//$hold_for_grand_total = $count_row_i;
						  }
							
							$wks->printArea(0,0,$count_row_j,21);
							$wks->fitToPages(1,2);
}
?>