<?

//there are performance issues with this module 
//took almost 10 seconds for the page to load
//have to cut it down to less than 0.1 seconds



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
										&nbsp;&nbsp;<a class="table_heading_text">Sales Rep : COMMISSIONS : As of <?=format_date_ymd_to_mdy($trade_date_to_process)?></a>
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
																<td width="80"><a href="javascript:expandall()"><img src="images/lf_v1/expand_all.png" border="0"></a></td>
																<td width="14" align="center">&nbsp;</td>
																<td width="100"><a href="javascript:collapseall()"><img src="images/lf_v1/collapse_all.png" border="0"></a></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="100"><a href="rep_ca_container.php"><img src="images/lf_v1/clnt_activity.png" border="0"></a></td>
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
                          <tr bgcolor="#333333"> 
                            <td width="280"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
                            <td width="60"><a class="tblhead_a">RR #</a> &nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a"><?=format_date_ymd_to_mdy($trade_date_to_process)?> ($)</a></td>
                            <td width="100" align="right"><a class="tblhead_a">MTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY MTD ($)&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">QTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY QTD ($)&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">YTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY YTD ($)&nbsp;</a></td>
                            <td>&nbsp;</td>
                          </tr>
													<?
													//set the running totals for this section
														$running_total_comm = 0;
														$running_total_mtd = 0;
														$running_total_qtd = 0;
														$running_total_ytd = 0;

													
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													$query_level_a_adv = "SELECT DISTINCT(nadd_advisor) as comm_advisor_code
																								FROM mry_nfs_nadd 
																								WHERE nadd_rr_owning_rep = '".$rep_to_process."'
																								AND nadd_branch = 'PDY'
																								AND nadd_advisor not like '&%' 
																								ORDER BY nadd_advisor"; 
																								
													//xdebug("query_level_a_adv",$query_level_a_adv);
													//investigate SAC
													$result_level_a_adv = mysql_query($query_level_a_adv) or die(mysql_error());

													$level_a_count = 1; //for css style

													while($row_level_a_adv = mysql_fetch_array($result_level_a_adv))
													{
														
														$comm_advisor_code = $row_level_a_adv["comm_advisor_code"];
														if ($arr_clients[$comm_advisor_code]){
															$comm_advisor_name = $arr_clients[$comm_advisor_code];
														} else {
															$comm_advisor_name = $comm_advisor_code;
														}
														//xdebug("comm_advisor_code",$comm_advisor_code);
														//get data for advisor
														$query_level_a_adv_date = "SELECT max(comm_trade_date) as comm_trade_date
																											FROM mry_comm_rr_level_a
																											WHERE comm_rr = '".$rep_to_process."'
																											AND comm_trade_date <= '".$trade_date_to_process."'
																											AND comm_advisor_code = '".$comm_advisor_code."'";
														//xdebug("query_level_a_adv_date",$query_level_a_adv_date);																											
														$result_level_a_adv_date = mysql_query($query_level_a_adv_date) or die(tdw_mysql_error($query_level_a_adv_date));

																while($row_level_a_adv_date = mysql_fetch_array($result_level_a_adv_date))
																{
																	$adv_date_val = $row_level_a_adv_date["comm_trade_date"];
																	//xdebug("adv_date_val",$adv_date_val);																											
																}
																
																//get data from rep_coom_level_a
																//fields are comm_rr  comm_trade_date  comm_advisor_code  comm_advisor_name  comm_total  comm_mtd  comm_qtd  comm_ytd 
																if ($adv_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																		$query_level_a = "SELECT * 
																											FROM mry_comm_rr_level_a
																											WHERE comm_rr = '".$rep_to_process."'
																											AND comm_advisor_code = '".$comm_advisor_code."'
																											AND comm_trade_date = '".$adv_date_val."'";
																		//xdebug("query_level_a",$query_level_a);
																		$result_level_a = mysql_query($query_level_a) or die(mysql_error());
																		while($row_level_a = mysql_fetch_array($result_level_a)) 
																		{
																			if ($row_level_a["comm_advisor_name"] == '') {
																				$show_advisor_name = $comm_advisor_code;
																			} else {
																				$show_advisor_name = $row_level_a["comm_advisor_name"];
																			}
																			$show_rr = $row_level_a["comm_rr"];
																			$show_previous_day_comm = number_format($row_level_a["comm_total"],2,'.',",");
																			$show_mtd = number_format($row_level_a["comm_mtd"],2,'.',",");
																			$show_qtd = number_format($row_level_a["comm_qtd"],2,'.',",");
																			$show_ytd = number_format($row_level_a["comm_ytd"],2,'.',",");
																			
																			$running_total_comm = $running_total_comm + $row_level_a["comm_total"];
																			
																			$running_total_mtd = $running_total_mtd + $row_level_a["comm_mtd"];
																			$running_total_qtd = $running_total_qtd + $row_level_a["comm_qtd"];
																			$running_total_ytd = $running_total_ytd + $row_level_a["comm_ytd"];
																			
																		}

																	} elseif ($adv_date_val != $trade_date_to_process AND $adv_date_val != '') { //data not available for trade_date_to_process
																			$query_level_ae = "SELECT * 
																												FROM mry_comm_rr_level_a
																												WHERE comm_rr = '".$rep_to_process."'
																												AND comm_advisor_code = '".$comm_advisor_code."'
																												AND comm_trade_date = '".$adv_date_val."'";
																			//xdebug("query_level_ae",$query_level_ae);
																			$result_level_ae = mysql_query($query_level_ae) or die(mysql_error());
																			while($row_level_ae = mysql_fetch_array($result_level_ae)) 
																			{
																				if ($row_level_ae["comm_advisor_name"] == '') {
																					$show_advisor_name = $comm_advisor_code;
																				} else {
																					$show_advisor_name = $row_level_ae["comm_advisor_name"];
																				}
																				$show_rr = $row_level_ae["comm_rr"];
																				$show_previous_day_comm = '<a class="display_zero">'."0.00"."</a>";
																				
																				//$running_total_comm = $running_total_comm + 0;
																				
																				$is_same_year = sameyear($adv_date_val,$trade_date_to_process);
																				$is_same_month = samemonth($adv_date_val,$trade_date_to_process);
																				$is_same_qtr = sameqtr($adv_date_val,$trade_date_to_process);
																				//xdebug("adv_date_val",$adv_date_val);
																				//xdebug("trade_date_to_process",$trade_date_to_process);
																				//xdebug("is_same_year",$is_same_year);
																				//xdebug("is_same_month",$is_same_month);
																				//xdebug("is_same_qtr",$is_same_qtr);
																	
																				if ($is_same_month == 1) {
																								$running_total_mtd = $running_total_mtd + $row_level_ae["comm_mtd"];
																								$show_mtd = number_format($row_level_ae["comm_mtd"],2,'.',",");
																				} else {
																								//$running_total_mtd = $running_total_mtd;
																								$show_mtd = '<a class="display_zero">'."0.00"."</a>";
																				}
																				
																				if ($is_same_qtr == 1) {
																								$running_total_qtd = $running_total_qtd + $row_level_ae["comm_qtd"];
																								$show_qtd = number_format($row_level_ae["comm_qtd"],2,'.',",");
																				} else {
																								//$running_total_qtd = $running_total_qtd;
																								$show_qtd = '<a class="display_zero">'."0.00"."</a>";
																				}
																				
																				if ($is_same_year == 1) {
																								$running_total_ytd = $running_total_ytd + $row_level_ae["comm_ytd"];
																								$show_ytd = number_format($row_level_ae["comm_ytd"],2,'.',",");
																				} else {
																								//$running_total_ytd = $running_total_ytd;
																								$show_ytd = '<a class="display_zero">'."0.00"."</a>";
																				}
																
																			}
																		} else { //no data exists for this client

																					$show_advisor_name = $comm_advisor_name;
																					$zero_string = '<a class="display_zero">'."0.00"."</a>";
																					$show_rr = $rep_to_process;
																					$show_previous_day_comm = $zero_string;
																					$show_mtd = $zero_string;
																					$show_qtd = $zero_string;
																					$show_ytd = $zero_string;
																		}

																		//}
													
														if ($level_a_count % 2) { 
																$class_row = "trdark";
														} else { 
																$class_row = "trlight"; 
														} 
													
													
															?>
																<tr class="<?=$class_row?>" onDblClick="javascript:showhidedetail(<?=$level_a_count?>)"> 
																	<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
																	<a href="javascript:showhidedetail(<?=$level_a_count?>)"><img id="img<?=$level_a_count?>" src="images/lf_v1/expand.png" border="0"></a> 
																	<?=$show_advisor_name?></td>
																	<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$show_rr?></td>
																	<td align="right"><?=$show_previous_day_comm?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td align="right"><?=$show_mtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td>&nbsp;</td>
																	<td align="right"><?=$show_qtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td>&nbsp;</td>
																	<td align="right"><?=$show_ytd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td>&nbsp;</td>
																	<td align="right">&nbsp;</td>
																</tr>
																<tr class="trlight" id="<?=$level_a_count?>" style="display=none; visibility=hidden"> 
																	<td colspan="10"> 
																	<?
																	$process_advisor_code_subacct = $comm_advisor_code;
																	include('rep_src_inc_subacct.php');
																	?> 
																	</td>
																</tr>
															<?
																$level_a_count = $level_a_count + 1;															
														}
													?>
                        </table>
												<table>
												   <tr class="display_totals"> 
                            <td width="280"><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
                            <td width="60"><div align="left">&nbsp;&nbsp;</div></td>
                            <td width="100" align="right"><?=number_format($running_total_comm,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_mtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_qtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_ytd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
			<tr id="shrd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
					<?
					include('rep_src_shared.php');
					?>
				</td>
			</tr>
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
					<?
					include('rep_src_inc_trade.php');
					?>
				</td>
			</tr>
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
					<?
					include('rep_src_inc_shared_trade.php');
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
