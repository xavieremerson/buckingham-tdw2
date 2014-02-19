<table width="100%" cellpadding="1" cellspacing="1">
		<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">  
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr bgcolor="#333333"> 
                            <td width="280"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (SHARED)</a></td>
                            <td width="60"><a class="tblhead_a">RR #</a> &nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a"><?=format_date_ymd_to_mdy($trade_date_to_process)?> ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">MTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY MTD ($)</a></td>
                            <td width="100" align="right"><a class="tblhead_a">QTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY QTD ($)</a></td>
                            <td width="100" align="right"><a class="tblhead_a">YTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY YTD ($)</a></td>
                            <td>&nbsp;</td>
                          </tr>
													<?
													//set the running totals for this section
														$running_total_shrd_comm = 0;
														$running_total_shrd_mtd = 0;
														$running_total_shrd_qtd = 0;
														$running_total_shrd_ytd = 0;

													
													//get all shared reps for the given rep
													//205	214	215	225	228	231	237
													
													
													//get all advisors for the selected rr
													/*
													$query_level_a_shared_reps_adv = "SELECT a.comm_advisor_code AS comm_advisor_code, b.srep_rrnum AS shared_rep_num
																														FROM mry_comm_rr_level_a a, sls_sales_reps b
																														WHERE a.comm_rr = b.srep_rrnum
																														AND a.comm_trade_date <= '".$trade_date_to_process."'
																														AND b.srep_user_id = '".$user_id."'
																														GROUP BY a.comm_advisor_code, b.srep_rrnum
																														ORDER BY a.comm_advisor_code";
													*/
													//changed the query above to reflect shared rep clients based on nfs_nadd
													$query_level_a_shared_reps_adv = "SELECT DISTINCT (
																														nadd_advisor
																														) AS comm_advisor_code, b.srep_rrnum AS shared_rep_num
																														FROM mry_nfs_nadd a, sls_sales_reps b
																														WHERE a.nadd_rr_owning_rep = b.srep_rrnum
																														AND b.srep_user_id = '".$rep_id."'
																														AND b.srep_isactive = 1 
																														GROUP BY a.nadd_advisor, b.srep_rrnum
																														ORDER BY a.nadd_advisor";		
																																									
													//xdebug("query_level_a_shared_reps_adv",$query_level_a_shared_reps_adv);
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
														//xdebug("query_level_a_adv_shrd_date",$query_level_a_adv_shrd_date);
																														
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
																xdebug("query_level_a_shrd",$query_level_a_shrd);
																$result_level_a_shrd = mysql_query($query_level_a_shrd) or die(mysql_error());
																while($row_level_a_shrd = mysql_fetch_array($result_level_a_shrd)) 
																{

																	if ($row_level_a_shrd["comm_advisor_name"] == '') {
																	$show_shrd_advisor_name = $comm_advisor_shrd_code;
																	} else {
																	$show_shrd_advisor_name = $row_level_a_shrd["comm_advisor_name"];
																	}
																	$show_shrd_rr = $comm_shared_rep_num; //$row_level_a_shrd["comm_rr"];
																	$show_shrd_previous_day_comm = number_format($row_level_a_shrd["comm_total"],2,'.',",");
																	$show_shrd_mtd = number_format($row_level_a_shrd["comm_mtd"],2,'.',",");
																	$show_shrd_qtd = number_format($row_level_a_shrd["comm_qtd"],2,'.',",");
																	$show_shrd_ytd = number_format($row_level_a_shrd["comm_ytd"],2,'.',",");
																	
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
																	$show_shrd_previous_day_comm = '<a class="display_zero">'."0.00"."</a>";
																	
																	//$show_shrd_qtd = number_format($row_level_ae_shrd["comm_qtd"],2,'.',",");
																	//$show_shrd_ytd = number_format($row_level_ae_shrd["comm_ytd"],2,'.',",");

																	$running_total_shrd_comm = $running_total_shrd_comm + 0;
																	
																	$is_same_year = sameyear($adv_shrd_date_val,$trade_date_to_process);
																	$is_same_month = samemonth($adv_shrd_date_val,$trade_date_to_process);
																	$is_same_qtr = sameqtr($adv_shrd_date_val,$trade_date_to_process);
																	
																	if ($is_same_month == 1) { 
																					//xdebug("case 2a: row_level_ae_shrd['comm_mtd']",$row_level_ae_shrd["comm_mtd"]);
																					$show_shrd_mtd = number_format($row_level_ae_shrd["comm_mtd"],2,'.',",");
																					//xdebug("case 2a: show_shrd_mtd",$show_shrd_mtd);
																					$running_total_shrd_mtd = $running_total_shrd_mtd + $row_level_ae_shrd["comm_mtd"];
																	} else {
																					//xdebug("case 2a: running_total_shrd_mtd",$running_total_shrd_mtd);
																					$show_shrd_mtd = '<a class="display_zero">'."0.00"."</a>";
																					$running_total_shrd_mtd = $running_total_shrd_mtd;
																	}
																	//xdebug("case 2: running_total_shrd_mtd",$running_total_shrd_mtd);  
																	
																	if ($is_same_qtr == 1) {
																					$running_total_shrd_qtd = $running_total_shrd_qtd + $row_level_ae_shrd["comm_qtd"];
																					$show_shrd_qtd = number_format($row_level_ae_shrd["comm_qtd"],2,'.',",");
																					//xdebug("case if: running_total_shrd_qtd",$running_total_shrd_qtd);  
																	} else {
																					$running_total_shrd_qtd = $running_total_shrd_qtd;
																					$show_shrd_qtd = '<a class="display_zero">'."0.00"."</a>";
																					//xdebug("case else: running_total_shrd_qtd",$running_total_shrd_qtd);  
																	}
																	
																	if ($is_same_year == 1) {
																					$running_total_shrd_ytd = $running_total_shrd_ytd + $row_level_ae_shrd["comm_ytd"];
																					$show_shrd_ytd = number_format($row_level_ae_shrd["comm_ytd"],2,'.',",");
																	} else {
																					$running_total_shrd_ytd = $running_total_shrd_ytd;
																					$show_shrd_ytd = '<a class="display_zero">'."0.00"."</a>";
																	}
																
																}
														} else { //no data available yet for the clients			
														//!!!!!!!!!!!!!!!!!!!
																	//xdebug("case 3: adv_shrd_date_val",$adv_shrd_date_val);
																					$show_shrd_advisor_name = $comm_advisor_shrd_name;
																					$zero_string = '<a class="display_zero">'."0.00"."</a>";
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
															?>
																<tr class="<?=$class_row?>" onDblClick="javascript:showhidedetail(<?=$level_a_shrd_count?>)"> 
																	<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
																	<a href="javascript:showhidedetail(<?=$level_a_shrd_count?>)"><img id="img<?=$level_a_shrd_count?>" src="images/lf_v1/expand.png" border="0"></a> 
																	<?=$show_shrd_advisor_name?></td>
																	<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$show_shrd_rr?></td>
																	<td align="right"><?=$show_shrd_previous_day_comm?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td align="right"><?=$show_shrd_mtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td>&nbsp;</td>
																	<td align="right"><?=$show_shrd_qtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td>&nbsp;</td>
																	<td align="right"><?=$show_shrd_ytd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td>&nbsp;</td>
																	<td align="right">&nbsp;</td>
																</tr>
																<tr class="trlight" id="<?=$level_a_shrd_count?>" style="display=none; visibility=hidden"> 
																	<td colspan="10"> 
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
													?>
                        </table>
												<table>
												   <tr class="display_totals"> 
                            <td width="280"><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
                            <td width="60"><div align="left">&nbsp;&nbsp;</div></td>
                            <td width="100" align="right"><?=number_format($running_total_shrd_comm,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_shrd_mtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_shrd_qtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_shrd_ytd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
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
					</table>
				</td>
			</tr>
		</table>