<?

//there are performance issues with this module 
//took almost 10 seconds for the page to load
//have to cut it down to less than 0.1 seconds

//some variables used down below
$arr_commission_clients = array();

if ($datefilterval) {
//xdebug('datefilterval',$datefilterval);
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
//xdebug('trade_date_to_process',$trade_date_to_process);
} else {
$trade_date_to_process = previous_business_day();
//xdebug('trade_date_to_process',$trade_date_to_process);
}
//$rep_to_process = '035'; //'028';
$rep_to_process = $rr_num;

//Create Lookup Array of Client Code / Client Name
	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}
?>

	
<table width="100%" cellpadding="1" cellspacing="1">
		<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr> 
										<td height="20" valign="middle" background="images/tables3/header_bk.jpg">
										&nbsp;&nbsp;<a class="table_heading_text">Trader (<?=$userfullname?>) : COMMISSIONS : As of <?=format_date_ymd_to_mdy($trade_date_to_process)?></a>
										</td>
									</tr>
									<tr> 
										<td valign="middle">			
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td>&nbsp;</td>
																<td width="150">
																	<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup("divfrom");
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																<form name="datefilter" id="iddatefilter" action="">
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['datefilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																		<input type="image" src="images/lf_v1/form_submit.png">
																		</form>
																</td>
																<td width="14" align="center">&nbsp;</td>
																<!-- <td width="80"><a href="javascript:expandall()"><img src="images/lf_v1/expand_all.png" border="0"></a></td>
																<td width="14" align="center">&nbsp;</td>
																<td width="100"><a href="javascript:collapseall()"><img src="images/lf_v1/collapse_all.png" border="0"></a></td>
																<td width="10" align="center">&nbsp;</td> -->
																<td width="100"><a href="rep_trdr_ca_container.php"><img src="images/lf_v1/clnt_activity.png" border="0"></a></td>
																<!--
																<td width="10" align="center">&nbsp;</td>
																<td width="100"><img src="images/lf_v1/excel_out.png" border="0"></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="100"><img src="images/lf_v1/pdf_gen.png" border="0"></td>
																-->
															</tr>
														</table>
														</td> 
													</tr>
												</table>
								                    
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
                            <td width="70" bgcolor="#555555" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#555555" align="right"><a class="tblhead_a">QTD LY&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#888888" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#888888" align="right"><a class="tblhead_a">YTD LY&nbsp;&nbsp;</a></td>
                            <td>&nbsp;</td>
                          </tr>
													<?
													//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													//Get lookup relevant client codes from client master (internal) for verification
													$qry_relevant_primary_clients = "SELECT DISTINCT (a.clnt_code) as relevant_primary_client
																														FROM int_clnt_clients a, Users b
																														WHERE a.clnt_trader = b.Initials
																														AND b.ID = '".$user_id."'";
													$result_relevant_primary_clients = mysql_query($qry_relevant_primary_clients) or die (tdw_mysql_error($qry_relevant_primary_clients));
													$arr_relevant_primary_clients = array();
													while ( $row_relevant_primary_clients = mysql_fetch_array($result_relevant_primary_clients) ) 
													{
														$arr_relevant_primary_clients[$row_relevant_primary_clients["relevant_primary_client"]] = $row_relevant_primary_clients["relevant_primary_client"];
													}
													//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//Get an array of relevant data for advisors by way of check payments.
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													
													$arr_check_data = array();
													
													$query_adv_checks = "SELECT DISTINCT (a.clnt_code) as advisor_code
																														FROM int_clnt_clients a, Users b
																														WHERE a.clnt_trader = b.Initials
																														AND b.ID = '".$user_id."'
																														ORDER BY a.clnt_code";
																								
													$result_adv_checks = mysql_query($query_adv_checks) or die(tdw_mysql_error($query_adv_checks));
													while($row_adv_checks = mysql_fetch_array($result_adv_checks))
															{
																$process_advisor = $row_adv_checks["advisor_code"];
															
																$query_get_adv_checks = "SELECT max(chk_check_date) as chk_check_date 
																													FROM chk_totals_level_a 
																													WHERE chk_advisor_code = '".$process_advisor."'";
																$result_get_adv_checks = mysql_query($query_get_adv_checks) or die(tdw_mysql_error($query_get_adv_checks));
		
																		while($row_get_adv_checks = mysql_fetch_array($result_get_adv_checks))
																		{
																			$adv_date_val = $row_get_adv_checks["chk_check_date"];
																		}
																		
																//get data from chk_totals_level_a
																//fields are chk_check_date  chk_advisor_code  chk_advisor_name  chk_total  chk_mtd  chk_qtd  chk_ytd  chk_isactive
																if ($adv_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																		$query_level_a = "SELECT * 
																											FROM chk_totals_level_a
																											WHERE chk_check_date = '".$adv_date_val."'
																											AND chk_advisor_code = '".$process_advisor."'";
																		$result_level_a = mysql_query($query_level_a) or die(tdw_mysql_error($query_level_a));
																		while($row_level_a = mysql_fetch_array($result_level_a)) 
																		{
																			$show_check_mtd = $row_level_a["chk_mtd"];
																			$show_check_qtd = $row_level_a["chk_qtd"];
																			$show_check_ytd = $row_level_a["chk_ytd"];
																			$str_adv_data = $process_advisor."#".$show_check_mtd."#".$show_check_qtd."#".$show_check_ytd;
																			$arr_check_data[$process_advisor] = $str_adv_data;
																		}

																} elseif ($adv_date_val != $trade_date_to_process AND $adv_date_val != '') { //data not available for trade_date_to_process
																			$query_level_ae = "SELECT * 
																											FROM chk_totals_level_a
																											WHERE chk_check_date = '".$adv_date_val."'
																											AND chk_advisor_code = '".$process_advisor."'";
																			$result_level_ae = mysql_query($query_level_ae) or die(tdw_mysql_error($query_level_ae));
																			while($row_level_ae = mysql_fetch_array($result_level_ae)) 
																			{
																				$is_same_year = samebrokyear($adv_date_val,$trade_date_to_process);
																				$is_same_month = samebrokmonth($adv_date_val,$trade_date_to_process);
																				$is_same_qtr = samebrokqtr($adv_date_val,$trade_date_to_process);
																				if ($is_same_month == 1) {
																								$show_check_mtd = $row_level_ae["chk_mtd"];
																				} else {
																								$show_check_mtd = 0;
																				}
																				
																				if ($is_same_qtr == 1) {
																								$show_check_qtd = $row_level_ae["chk_qtd"];
																				} else {
																								$show_check_qtd = 0;
																				}
																				
																				if ($is_same_year == 1) {
																								$show_check_ytd = $row_level_ae["chk_ytd"];
																				} else {
																								$show_check_ytd = 0;
																				}
																				
																			$str_adv_data = $process_advisor."#".$show_check_mtd."#".$show_check_qtd."#".$show_check_ytd;
																			$arr_check_data[$process_advisor] = $str_adv_data;
																			}
																		} else { //no data exists for this client
																			$str_adv_data = $process_advisor."#0#0#0";
																			$arr_check_data[$process_advisor] = $str_adv_data;
																		}

													}
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\

													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													$query_level_a_adv = "SELECT DISTINCT(a.nadd_advisor) as comm_advisor_code
																								FROM mry_nfs_nadd a, int_clnt_clients b, Users c
																								WHERE a.nadd_branch = 'PDY'
																								AND a.nadd_advisor not like '&%'
																								AND a.nadd_advisor = b.clnt_code
																								AND b.clnt_trader = c.Initials
																								AND c.ID = '".$user_id."'
																								ORDER BY b.clnt_name";
																								
																								
													//investigate SAC
													$result_level_a_adv = mysql_query($query_level_a_adv) or die(tdw_mysql_error($query_level_a_adv));

													$level_a_count = 1; //for css style
													
													

													while($row_level_a_adv = mysql_fetch_array($result_level_a_adv))
													{

														$comm_advisor_code = $row_level_a_adv["comm_advisor_code"];
														if ($arr_clients[$comm_advisor_code]){
															$comm_advisor_name = $arr_clients[$comm_advisor_code];
														} else {
															$comm_advisor_name = $comm_advisor_code;
														}
														
														//Get the data for each rep in the advisor 
														$qry_multiple_rep = "SELECT distinct(concat(comm_advisor_code,'^',comm_rr)) as advisor_rep
																									FROM mry_comm_rr_level_a
																									WHERE comm_advisor_code = '".$comm_advisor_code."'
																									AND comm_ytd > 0
																									ORDER BY comm_rr";
														$result_multiple_rep = mysql_query($qry_multiple_rep) or die(tdw_mysql_error($qry_multiple_rep));
														$adv_rep_count = 0;
														$arr_adv_rep = array();
														while($row_multiple_rep = mysql_fetch_array($result_multiple_rep)) {
															$arr_adv_rep[$adv_rep_count] = $row_multiple_rep["advisor_rep"];
															$adv_rep_count = $adv_rep_count + 1;
														}

														foreach ($arr_adv_rep as $key => $value) {
    												
															$arr_adv_rep_to_process = explode('^', $value);
																
																				//get data for advisor
																				$query_level_a_adv_date = "SELECT max(comm_trade_date) as comm_trade_date
																																	FROM mry_comm_rr_level_a
																																	WHERE comm_trade_date <= '".$trade_date_to_process."'
																																	AND comm_rr = '".$arr_adv_rep_to_process[1]."'
																																	AND comm_advisor_code = '".$comm_advisor_code."'";
																																	
																				$result_level_a_adv_date = mysql_query($query_level_a_adv_date) or die(tdw_mysql_error($query_level_a_adv_date));
						
																						while($row_level_a_adv_date = mysql_fetch_array($result_level_a_adv_date))
																						{
																							$adv_date_val = $row_level_a_adv_date["comm_trade_date"];
																						}
																						
																						//get data from rep_coom_level_a
																						//fields are comm_rr  comm_trade_date  comm_advisor_code  comm_advisor_name  comm_total  comm_mtd  comm_qtd  comm_ytd 
																						if ($adv_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																								$query_level_a = "SELECT * 
																																	FROM mry_comm_rr_level_a
																																	WHERE comm_advisor_code = '".$comm_advisor_code."'
																																	AND comm_rr = '".$arr_adv_rep_to_process[1]."'
																																	AND comm_trade_date = '".$adv_date_val."'";
																								$result_level_a = mysql_query($query_level_a) or die(tdw_mysql_error($query_level_a));
																								while($row_level_a = mysql_fetch_array($result_level_a)) 
																								{
																									if ($row_level_a["comm_advisor_name"] == '') {
																										$show_advisor_name = $comm_advisor_code;
																									} else {
																										$show_advisor_name = $row_level_a["comm_advisor_name"];
																									}
																									$show_rr = $row_level_a["comm_rr"];
																									$show_previous_day_comm = $row_level_a["comm_total"];
																									$show_mtd = $row_level_a["comm_mtd"];
																									$show_qtd = $row_level_a["comm_qtd"];
																									$show_ytd = $row_level_a["comm_ytd"];
																								}
						
																							} elseif ($adv_date_val != $trade_date_to_process AND $adv_date_val != '') { //data not available for trade_date_to_process
																									$query_level_ae = "SELECT * 
																																		FROM mry_comm_rr_level_a
																																		WHERE comm_advisor_code = '".$comm_advisor_code."'
																																	AND comm_rr = '".$arr_adv_rep_to_process[1]."'
																																		AND comm_trade_date = '".$adv_date_val."'";
																									$result_level_ae = mysql_query($query_level_ae) or die(tdw_mysql_error($query_level_ae));
																									while($row_level_ae = mysql_fetch_array($result_level_ae)) 
																									{
																										if ($row_level_ae["comm_advisor_name"] == '') {
																											$show_advisor_name = $comm_advisor_code;
																										} else {
																											$show_advisor_name = $row_level_ae["comm_advisor_name"];
																										}
																										$show_rr = $arr_adv_rep_to_process[1]; //$row_level_ae["comm_rr"];
																										$show_previous_day_comm = 0;
																										
																										$is_same_year = samebrokyear($adv_date_val,$trade_date_to_process);
																										$is_same_month = samebrokmonth($adv_date_val,$trade_date_to_process);
																										$is_same_qtr = samebrokqtr($adv_date_val,$trade_date_to_process);
																							
																										if ($is_same_month == 1) {
																														$show_mtd = $row_level_ae["comm_mtd"];
																										} else {
																														$show_mtd = 0;
																										}
																										
																										if ($is_same_qtr == 1) {
																														$show_qtd = $row_level_ae["comm_qtd"];
																										} else {
																														$show_qtd = 0;
																										}
																										
																										if ($is_same_year == 1) {
																														$show_ytd = $row_level_ae["comm_ytd"];
																										} else {
																														$show_ytd = 0;
																										}
																						
																									}
																								} else { //no data exists for this client
						
																											$show_advisor_name = $comm_advisor_name;
																											$zero_string = '<a class="display_zero">'."0"."</a>";
																											$show_rr = $rep_to_process;
																											$show_previous_day_comm = 0;
																											$show_mtd = 0;
																											$show_qtd = 0;
																											$show_ytd = 0;
																								}
						
																								//}
																			
																				if ($level_a_count % 2) { 
																						$class_row = "trdark";
																				} else { 
																						$class_row = "trlight"; 
																				} 
																			
																			
																					if (in_array($comm_advisor_code,$arr_relevant_primary_clients)) {
																												//capture these clients in an array
																												$arr_commission_clients[$comm_advisor_code] = $comm_advisor_code;
																												?>
																													<tr class="<?=$class_row?>" onDblClick="javascript:sh_level2('<?=$mk_id?>','<?=$show_rr?>','<?=$trade_date_to_process?>','<?=$comm_advisor_code?>')"> 
																														<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
																														<?=$show_advisor_name?></td>
																														<td>&nbsp;&nbsp;<?=$show_rr?></td>
																														<td align="right"><?=show_numbers($show_previous_day_comm)?>&nbsp;&nbsp;</td>
																														<td align="right"><?=show_numbers($show_mtd)?>&nbsp;&nbsp;</td>
																														<td align="right"><?=show_numbers($show_qtd)?>&nbsp;&nbsp;</td>
																														<td align="right"><?=show_numbers($show_ytd)?>&nbsp;&nbsp;</td>
																														<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 1))?>&nbsp;&nbsp;</td>
																														<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																														<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																														<td align="right"><?=show_numbers($show_mtd + get_checks_data ($comm_advisor_code, $arr_check_data, 1))?>&nbsp;&nbsp;</td>
																														<td align="right">&nbsp;</td>
																														<td align="right"><?=show_numbers($show_qtd + get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																														<td align="right">&nbsp;</td>
																														<td align="right"><?=show_numbers($show_ytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																														<td align="right">&nbsp;</td>
																														<td align="right">&nbsp;</td>
																													</tr>
																													<?
																													$total_pbd = $total_pbd + $show_previous_day_comm;
																													$total_mtd = $total_mtd + $show_mtd;
																													$total_qtd = $total_qtd + $show_qtd;
																													$total_ytd = $total_ytd + $show_ytd;
																													$total_cmtd = $total_cmtd + get_checks_data ($comm_advisor_code, $arr_check_data, 1); 
																													$total_cqtd = $total_cqtd + get_checks_data ($comm_advisor_code, $arr_check_data, 2); 
																													$total_cytd = $total_cytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3); 
																													$total_grand_mtd = $total_grand_mtd + $show_mtd + get_checks_data ($comm_advisor_code, $arr_check_data, 1); 
																													$total_grand_qtd = $total_grand_qtd + $show_qtd + get_checks_data ($comm_advisor_code, $arr_check_data, 2); 
																													$total_grand_ytd = $total_grand_ytd + $show_ytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3); 
																													
																												}	
																					$level_a_count = $level_a_count + 1;											
															}
														}
												//now process clients which have just checks and no commissions
												$arr_checkonly_clients = array();
												foreach ($arr_relevant_primary_clients as $key => $value) {
													 if (!in_array($value, $arr_commission_clients)) {
													 $arr_checkonly_clients[$value] = $value;	
													 $comm_advisor_code = $value;
	
	
																						if ($level_a_count % 2) { 
																								$class_row = "trdark";
																						} else { 
																								$class_row = "trlight"; 
																						} 
												 
																						?>
																							<tr class="<?=$class_row?>"> 
																								<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$arr_clients[$comm_advisor_code]?></td>
																								<td>&nbsp;&nbsp;<?=$show_rr?></td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 1))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_code, $arr_check_data, 1))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																								<td align="right">&nbsp;</td>
																								<td align="right">&nbsp;</td>
																							</tr>
																							<?
																							$total_pbd = $total_pbd + 0;
																							$total_mtd = $total_mtd + 0;
																							$total_qtd = $total_qtd + 0;
																							$total_ytd = $total_ytd + 0;
																							$total_cmtd = $total_cmtd + get_checks_data ($comm_advisor_code, $arr_check_data, 1); 
																							$total_cqtd = $total_cqtd + get_checks_data ($comm_advisor_code, $arr_check_data, 2); 
																							$total_cytd = $total_cytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3); 
																							$total_grand_mtd = $total_grand_mtd + 0 + get_checks_data ($comm_advisor_code, $arr_check_data, 1); 
																							$total_grand_qtd = $total_grand_qtd + 0 + get_checks_data ($comm_advisor_code, $arr_check_data, 2); 
																							$total_grand_ytd = $total_grand_ytd + 0 + get_checks_data ($comm_advisor_code, $arr_check_data, 3); 


																							$level_a_count = $level_a_count + 1;			
													 }
												}													
												?>
                        </table>
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
												   <tr class="display_totals"> 
                            <td width="240" align="left">&nbsp;&nbsp;TOTALS:</td>
                            <td width="40">&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_pbd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_mtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_qtd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_ytd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cmtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cqtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cytd)?>&nbsp;&nbsp;</td>
 														<td width="70" align="right"><?=show_numbers($total_grand_mtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right">&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_grand_qtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right">&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_grand_ytd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right">&nbsp;</td>
                            <td>&nbsp;</td>                          
														</tr>
												</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
				</td>
			</tr>
			<tr id="shrd"> <!--  style="display=none; visibility=hidden" -->
				<td>
				</td>
			</tr>
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
					<?
					include('rep_trdr_inc_trade.php');
					?>
				</td>
			</tr>
		</table>
		</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
					<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
