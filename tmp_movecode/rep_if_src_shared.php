<?
//some variables used down below
$arr_commission_shrd_clients = array();
?>

<!-- <table width="100%" cellpadding="1" cellspacing="1">
		<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td> -->
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">  
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr> 
                            <td colspan="2" bgcolor="#ffffff" width="240"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
                            <td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
                            <td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
                            <td bgcolor="#222222" colspan="6" align="center"><a class="tblhead_a">T O T A L</a></td>
                            <td bgcolor="#222222">&nbsp;</td>
                          <tr bgcolor="#333333"> 
                            <td width="240"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
                            <td width="40"><a class="tblhead_a">&nbsp;&nbsp;RR</a></td>
                            <td width="70" align="right"><a class="tblhead_a"><?=substr(format_date_ymd_to_mdy($trade_date_to_process),0,5)?>&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
                            <td width="70" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="80" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#222222" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#222222" align="right"><a class="tblhead_a">MTD LY&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">QTD LY&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#222222" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#222222" align="right"><a class="tblhead_a">YTD LY&nbsp;&nbsp;</a></td>
                            <td>&nbsp;</td>
                          </tr>
													
													<?
													//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													//Get lookup relevant client codes from client master (internal) for verification
													$qry_relevant_shared_clients = "SELECT DISTINCT (a.clnt_code) as relevant_shared_client
																														FROM int_clnt_clients a, Users b
																													WHERE (
																																		(
																																		a.clnt_rr1 = b.Initials
																																		AND a.clnt_rr2 != ''
																																		)
																																OR  (
																																		a.clnt_rr2 = b.Initials
																																		AND a.clnt_rr1 != ''
																																		)
																																)
																																AND b.rr_num = '".$rep_to_process."'";

													$result_relevant_shared_clients = mysql_query($qry_relevant_shared_clients) or die (tdw_mysql_error($qry_relevant_shared_clients));
													$arr_relevant_shared_clients = array();
													while ( $row_relevant_shared_clients = mysql_fetch_array($result_relevant_shared_clients) ) 
													{
														$arr_relevant_shared_clients[$row_relevant_shared_clients["relevant_shared_client"]] = $row_relevant_shared_clients["relevant_shared_client"];
													}
													//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//Get an array of relevant data for advisors by way of check payments.
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													
													$arr_check_data_shrd = array();
													
													$query_adv_checks_shrd = "SELECT DISTINCT (nadd_advisor) AS advisor_code, b.srep_rrnum AS shared_rep_num
																														FROM mry_nfs_nadd a, sls_sales_reps b
																														WHERE a.nadd_rr_owning_rep = b.srep_rrnum
																														AND nadd_advisor != 'XXXX'
																														AND b.srep_user_id = '".$user_id."'
																														AND b.srep_isactive = 1 
																														GROUP BY a.nadd_advisor, b.srep_rrnum
																														ORDER BY a.nadd_advisor";
																								
													$result_adv_checks_shrd = mysql_query($query_adv_checks_shrd) or die(tdw_mysql_error($query_adv_checks_shrd));
													while($row_adv_checks_shrd = mysql_fetch_array($result_adv_checks_shrd))
															{
																$process_advisor_shrd = $row_adv_checks_shrd["advisor_code"];
															
																$query_get_adv_checks_shrd = "SELECT max(chk_check_date) as chk_check_date 
																													FROM chk_totals_level_a 
																													WHERE chk_advisor_code = '".$process_advisor_shrd."'";
																$result_get_adv_checks_shrd = mysql_query($query_get_adv_checks_shrd) or die(tdw_mysql_error($query_get_adv_checks_shrd));
		
																		while($row_get_adv_checks_shrd = mysql_fetch_array($result_get_adv_checks_shrd))
																		{
																			$adv_date_val_shrd = $row_get_adv_checks_shrd["chk_check_date"];
																		}
																		
																//get data from chk_totals_level_a
																//fields are chk_check_date  chk_advisor_code  chk_advisor_name  chk_total  chk_mtd  chk_qtd  chk_ytd  chk_isactive
																if ($adv_date_val_shrd == $trade_date_to_process) { //data available for trade_date_to_process
																		$query_level_a_shrd = "SELECT * 
																											FROM chk_totals_level_a
																											WHERE chk_check_date = '".$adv_date_val_shrd."'
																											AND chk_advisor_code = '".$process_advisor_shrd."'";
																		$result_level_a_shrd = mysql_query($query_level_a_shrd) or die(mysql_error());
																		while($row_level_a_shrd = mysql_fetch_array($result_level_a_shrd)) 
																		{
																			$show_check_mtd_shrd = $row_level_a_shrd["chk_mtd"];
																			$show_check_qtd_shrd = $row_level_a_shrd["chk_qtd"];
																			$show_check_ytd_shrd = $row_level_a_shrd["chk_ytd"];
																			$str_adv_data_shrd = $process_advisor_shrd."#".$show_check_mtd_shrd."#".$show_check_qtd_shrd."#".$show_check_ytd_shrd;
																			$arr_check_data_shrd[$process_advisor_shrd] = $str_adv_data_shrd;
																		}

																} elseif ($adv_date_val_shrd != $trade_date_to_process AND $adv_date_val_shrd != '') { //data not available for trade_date_to_process
																			$query_level_ae_shrd = "SELECT * 
																											FROM chk_totals_level_a
																											WHERE chk_check_date = '".$adv_date_val_shrd."'
																											AND chk_advisor_code = '".$process_advisor_shrd."'";
																			$result_level_ae_shrd = mysql_query($query_level_ae_shrd) or die(mysql_error());
																			while($row_level_ae_shrd = mysql_fetch_array($result_level_ae_shrd)) 
																			{
																				$is_same_year = samebrokyear($adv_date_val_shrd,$trade_date_to_process);
																				$is_same_month = samebrokmonth($adv_date_val_shrd,$trade_date_to_process);
																				$is_same_qtr = samebrokqtr($adv_date_val_shrd,$trade_date_to_process);
																				if ($is_same_month == 1) {
																								$show_check_mtd_shrd = $row_level_ae_shrd["chk_mtd"];
																				} else {
																								$show_check_mtd_shrd = 0;
																				}
																				
																				if ($is_same_qtr == 1) {
																								$show_check_qtd_shrd = $row_level_ae_shrd["chk_qtd"];
																				} else {
																								$show_check_qtd_shrd = 0;
																				}
																				
																				if ($is_same_year == 1) {
																								$show_check_ytd_shrd = $row_level_ae_shrd["chk_ytd"];
																				} else {
																								$show_check_ytd_shrd = 0;
																				}
																				
																			$str_adv_data_shrd = $process_advisor_shrd."#".$show_check_mtd_shrd."#".$show_check_qtd_shrd."#".$show_check_ytd_shrd;
																			$arr_check_data_shrd[$process_advisor_shrd] = $str_adv_data_shrd;
																			}
																		} else { //no data exists for this client
																			$str_adv_data_shrd = $process_advisor_shrd."#0#0#0";
																			$arr_check_data_shrd[$process_advisor_shrd] = $str_adv_data_shrd;
																		}

													}
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//set the running totals for this section
														$running_total_shrd_comm = 0;
														$running_total_shrd_mtd = 0;
														$running_total_shrd_qtd = 0;
														$running_total_shrd_ytd = 0;

													//get all advisors for the selected rr
													//changed the query above to reflect shared rep clients based on nfs_nadd
													$query_level_a_shared_reps_adv = "SELECT DISTINCT (
																														nadd_advisor
																														) AS comm_advisor_code, b.srep_rrnum AS shared_rep_num
																														FROM mry_nfs_nadd a, sls_sales_reps b
																														WHERE a.nadd_rr_owning_rep = b.srep_rrnum
																														AND nadd_advisor != 'XXXX'
																														AND b.srep_user_id = '".$user_id."'
																														AND b.srep_isactive = 1 
																														GROUP BY a.nadd_advisor, b.srep_rrnum
																														ORDER BY a.nadd_advisor";		
																																									
													$result_level_a_shared_reps_adv = mysql_query($query_level_a_shared_reps_adv) or die(mysql_error());

													$level_a_shrd_count = 1000; //for css style

													while($row_level_a_shared_reps_adv = mysql_fetch_array($result_level_a_shared_reps_adv))
													{
														$comm_advisor_shrd_code = $row_level_a_shared_reps_adv["comm_advisor_code"];
														if ($arr_clients[$comm_advisor_shrd_code]){
															$comm_advisor_shrd_name = $arr_clients[$comm_advisor_shrd_code];
														} else {
															$comm_advisor_shrd_name = $comm_advisor_shrd_code;
														}
														//also getting the shared rep number
														$comm_shared_rep_num = $row_level_a_shared_reps_adv["shared_rep_num"];
														//get data for advisor
														$query_level_a_adv_shrd_date = "SELECT max(comm_trade_date) as comm_trade_date
																														FROM mry_comm_rr_level_a
																														WHERE comm_trade_date <= '".$trade_date_to_process."'
																														AND comm_rr = '".$comm_shared_rep_num."'  
																														AND comm_advisor_code = '".$comm_advisor_shrd_code."'";
																														
														$result_level_a_adv_shrd_date = mysql_query($query_level_a_adv_shrd_date) or die(tdw_mysql_error($query_level_a_adv_shrd_date));
														while($row_level_a_adv_shrd_date = mysql_fetch_array($result_level_a_adv_shrd_date))
														{
															$adv_shrd_date_val = $row_level_a_adv_shrd_date["comm_trade_date"];
														}
														
														//get data from rep_coom_level_a
														//fields are comm_rr  comm_trade_date  comm_advisor_code  comm_advisor_name  comm_total  comm_mtd  comm_qtd  comm_ytd 
														//xdebug("adv_date_val",$adv_date_val);
														//xdebug("trade_date_to_process",$trade_date_to_process);
														if ($adv_shrd_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																$query_level_a_shrd  =  "SELECT * 
																												FROM mry_comm_rr_level_a
																												WHERE comm_advisor_code = '".$comm_advisor_shrd_code."'
																												AND comm_rr = '".$comm_shared_rep_num."' 
																												AND comm_trade_date = '".$adv_shrd_date_val."'";
																//xdebug("query_level_a_shrd",$query_level_a_shrd);
																$result_level_a_shrd = mysql_query($query_level_a_shrd) or die(mysql_error());
																while($row_level_a_shrd = mysql_fetch_array($result_level_a_shrd)) 
																{

																	if ($row_level_a_shrd["comm_advisor_name"] == '') {
																	$show_shrd_advisor_name = $comm_advisor_shrd_code;
																	} else {
																	$show_shrd_advisor_name = $row_level_a_shrd["comm_advisor_name"];
																	}
																	$show_shrd_rr = $comm_shared_rep_num; //$row_level_a_shrd["comm_rr"];
																	$show_shrd_previous_day_comm = $row_level_a_shrd["comm_total"];
																	$show_shrd_mtd = $row_level_a_shrd["comm_mtd"];
																	$show_shrd_qtd = $row_level_a_shrd["comm_qtd"];
																	$show_shrd_ytd = $row_level_a_shrd["comm_ytd"];
																	
																	$running_total_shrd_comm = $running_total_shrd_comm + $row_level_a_shrd["comm_total"];
																	$running_total_shrd_mtd = $running_total_shrd_mtd + $row_level_a_shrd["comm_mtd"];
																	$running_total_shrd_qtd = $running_total_shrd_qtd + $row_level_a_shrd["comm_qtd"];
																	$running_total_shrd_ytd = $running_total_shrd_ytd + $row_level_a_shrd["comm_ytd"];
	
																	//xdebug("case 1: running_total_shrd_mtd",$running_total_shrd_mtd);

																}
														} elseif ($adv_shrd_date_val != $trade_date_to_process and $adv_shrd_date_val != '') { //data not available for trade_date_to_process
																$query_level_ae_shrd = "SELECT * 
																												FROM mry_comm_rr_level_a
																												WHERE comm_advisor_code = '".$comm_advisor_shrd_code."'
																												AND comm_rr = '".$comm_shared_rep_num."' 
																												AND comm_trade_date = '".$adv_shrd_date_val."'";
																//xdebug("query_level_ae_shrd",$query_level_ae_shrd);
																$result_level_ae_shrd = mysql_query($query_level_ae_shrd) or die(mysql_error());
																//xdebug("countval",mysql_num_rows($result_level_ae_shrd));
																while($row_level_ae_shrd = mysql_fetch_array($result_level_ae_shrd)) 
																{
																	//xdebug("comm_qtd",$row_level_ae_shrd["comm_qtd"]);
																	if ($row_level_ae_shrd["comm_advisor_name"] == '') {
																	$show_shrd_advisor_name = $comm_advisor_shrd_code;
																	} else {
																	$show_shrd_advisor_name = $row_level_ae_shrd["comm_advisor_name"];
																	}
																	$show_shrd_rr = $row_level_ae_shrd["comm_rr"];
																	$show_shrd_previous_day_comm = 0;
																	
																	//$show_shrd_qtd = number_format($row_level_ae_shrd["comm_qtd"],2,'.',",");
																	//$show_shrd_ytd = number_format($row_level_ae_shrd["comm_ytd"],2,'.',",");

																	$running_total_shrd_comm = $running_total_shrd_comm + 0;
																	
																	$is_same_year = samebrokyear($adv_shrd_date_val,$trade_date_to_process);
																	$is_same_month = samebrokmonth($adv_shrd_date_val,$trade_date_to_process);
																	$is_same_qtr = samebrokqtr($adv_shrd_date_val,$trade_date_to_process);
																	
																	if ($is_same_month == 1) { 
																					//xdebug("case 2a: row_level_ae_shrd['comm_mtd']",$row_level_ae_shrd["comm_mtd"]);
																					$show_shrd_mtd = $row_level_ae_shrd["comm_mtd"];
																					//xdebug("case 2a: show_shrd_mtd",$show_shrd_mtd);
																					$running_total_shrd_mtd = $running_total_shrd_mtd + $row_level_ae_shrd["comm_mtd"];
																	} else {
																					//xdebug("case 2a: running_total_shrd_mtd",$running_total_shrd_mtd);
																					$show_shrd_mtd = 0;
																					$running_total_shrd_mtd = $running_total_shrd_mtd;
																	}
																	//xdebug("case 2: running_total_shrd_mtd",$running_total_shrd_mtd);  
																	
																	if ($is_same_qtr == 1) {
																					$running_total_shrd_qtd = $running_total_shrd_qtd + $row_level_ae_shrd["comm_qtd"];
																					$show_shrd_qtd = $row_level_ae_shrd["comm_qtd"];
																					//xdebug("case if: running_total_shrd_qtd",$running_total_shrd_qtd);  
																	} else {
																					$running_total_shrd_qtd = $running_total_shrd_qtd;
																					$show_shrd_qtd = 0;
																					//xdebug("case else: running_total_shrd_qtd",$running_total_shrd_qtd);  
																	}
																	
																	if ($is_same_year == 1) {
																					$running_total_shrd_ytd = $running_total_shrd_ytd + $row_level_ae_shrd["comm_ytd"];
																					$show_shrd_ytd = $row_level_ae_shrd["comm_ytd"];
																	} else {
																					$running_total_shrd_ytd = $running_total_shrd_ytd;
																					$show_shrd_ytd = 0;
																	}
																
																}
														} else { //no data available yet for the clients			
														//!!!!!!!!!!!!!!!!!!!
																	//xdebug("case 3: adv_shrd_date_val",$adv_shrd_date_val);
																					$show_shrd_advisor_name = $comm_advisor_shrd_name;
																					$zero_string = 0;
																					$show_shrd_rr = $comm_shared_rep_num;
																					$show_shrd_previous_day_comm = $zero_string;
																					$show_shrd_mtd = $zero_string;
																					$show_shrd_qtd = $zero_string;
																					$show_shrd_ytd = $zero_string;
														}
													
														if ($level_a_shrd_count % 2) { 
																$class_row = "trdark";
														} else { 
																$class_row = "trlight"; 
														} 
																					//xdebug("case 2b: show_shrd_mtd",$show_shrd_mtd);
																					//xdebug("case 2b: comm_advisor_shrd_name",$comm_advisor_shrd_name);
															if (in_array($comm_advisor_shrd_code,$arr_relevant_shared_clients)) {
																						//capture these clients in an array
																						$arr_commission_shrd_clients[$comm_advisor_shrd_code] = $comm_advisor_shrd_code;
																						?>
																							<tr class="<?=$class_row?>"> <!-- onDblClick="javascript:showhidedetail(<?=$level_a_shrd_count?>)"-->
																								<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
																								<!--<a href="javascript:showhidedetail(<?=$level_a_shrd_count?>)"><img id="img<?=$level_a_shrd_count?>" src="images/lf_v1/expand.png" border="0"></a> -->
																								<?=$show_shrd_advisor_name?></td>
																								<td>&nbsp;&nbsp;<?=$show_shrd_rr?></td>
																								<td align="right"><?=show_numbers($show_shrd_previous_day_comm)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers($show_shrd_mtd)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers($show_shrd_qtd)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers($show_shrd_ytd)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers($show_shrd_mtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right"><?=show_numbers($show_shrd_qtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right"><?=show_numbers($show_shrd_ytd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right">&nbsp;</td>
																							</tr>
																							<?
																							$total_pbd_shrd = $total_pbd_shrd + $show_shrd_previous_day_comm;
																							$total_mtd_shrd = $total_mtd_shrd + $show_shrd_mtd;
																							$total_qtd_shrd = $total_qtd_shrd + $show_shrd_qtd;
																							$total_ytd_shrd = $total_ytd_shrd + $show_shrd_ytd;
																							$total_cmtd_shrd = $total_cmtd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1); 
																							$total_cqtd_shrd = $total_cqtd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2); 
																							$total_cytd_shrd = $total_cytd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3); 
																							$total_grand_mtd_shrd = $total_grand_mtd_shrd + $show_shrd_mtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1); 
																							$total_grand_qtd_shrd = $total_grand_qtd_shrd + $show_shrd_qtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2); 
																							$total_grand_ytd_shrd = $total_grand_ytd_shrd + $show_shrd_ytd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3); 
																							?>
																							
																							<tr class="trlight" id="<?=$level_a_shrd_count?>" style="display=none; visibility=hidden"> 
																								<td colspan="16"> 
																								<?
																								$process_shared_advisor_code_subacct = $comm_advisor_shrd_code;
																								//xdebug("process_shared_advisor_code_subacct",$process_shared_advisor_code_subacct);
																								include('rep_src_inc_shared_subacct.php');
																								?> 
																								</td>
																							</tr>
																						<?
																							$level_a_shrd_count = $level_a_shrd_count + 1;
																}															
														}
												//now process clients which have just checks and no commissions
												$arr_checkonly_shrd_clients = array();
												foreach ($arr_relevant_shared_clients as $key => $value) {
													 if (!in_array($value, $arr_commission_shrd_clients)) {
													 $arr_checkonly_shrd_clients[$value] = $value;	
													 $comm_advisor_shrd_code = $value;
	
	
																						if ($level_a_shrd_count % 2) { 
																								$class_row = "trdark";
																						} else { 
																								$class_row = "trlight"; 
																						} 
												 
																						?>
																							<tr class="<?=$class_row?>"> <!-- onDblClick="javascript:showhidedetail(<?=$level_a_shrd_count?>)"-->
																								<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$arr_clients[$comm_advisor_shrd_code]?></td>
																								<td>&nbsp;&nbsp;<?=$show_shrd_rr?></td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right">&nbsp;</td>
																							</tr>
																							<?
																							$total_pbd_shrd = $total_pbd_shrd + 0;
																							$total_mtd_shrd = $total_mtd_shrd + 0;
																							$total_qtd_shrd = $total_qtd_shrd + 0;
																							$total_ytd_shrd = $total_ytd_shrd + 0;
																							$total_cmtd_shrd = $total_cmtd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1); 
																							$total_cqtd_shrd = $total_cqtd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2); 
																							$total_cytd_shrd = $total_cytd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3); 
																							$total_grand_mtd_shrd = $total_grand_mtd_shrd + 0 + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1); 
																							$total_grand_qtd_shrd = $total_grand_qtd_shrd + 0 + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2); 
																							$total_grand_ytd_shrd = $total_grand_ytd_shrd + 0 + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3); 
																							?>
																						<?
																							$level_a_shrd_count = $level_a_shrd_count + 1;
													 }
												}													
													?>
                        </table>
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
												   <tr class="display_totals"> 
                            <td width="240" align="left">&nbsp;&nbsp;TOTALS:</td>
                            <td width="40">&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_pbd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_mtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_qtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_ytd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cmtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cqtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cytd_shrd)?>&nbsp;&nbsp;</td>
 														<td width="70" align="right"><?=show_numbers($total_grand_mtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right">&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_grand_qtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right">&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_grand_ytd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right">&nbsp;</td>
                            <td>&nbsp;</td>                          
														</tr>
												</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
<!-- 						</td>
						</tr>
					</table>
				</td>
			</tr>
		</table> -->