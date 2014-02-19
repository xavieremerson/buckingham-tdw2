<?
//show_array($_POST);
if ($_POST) {
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
$arr_repinfo = split('\^',$sel_rep);
$rep_to_process = $arr_repinfo[0];
$rep_id = $arr_repinfo[1];
} else {
$trade_date_to_process = previous_business_day();
}
?>
<script language="JavaScript" type="text/JavaScript">
//pass variables as separate args
function sh_level2(divid,rrnum,tdate) {
  var trid;
	trid = 'if_'+ divid; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(trid).style.getAttribute("visibility") == "" || document.getElementById(trid).style.getAttribute("visibility") == "hidden" ) {
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/collapse.png';
			if (document.getElementById(trid).src == "") {
			document.getElementById(trid).src='http://192.168.20.63/tdw/rep_bssrc_if_prep.php?ifid='+trid+'&rep_num='+rrnum+'&tdate='+tdate;
			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
		}		
	} 
	else { 
		if (document.layers) { // Netscape 4 
			alert("Netscape 4");
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 
} 
</script>
<script language="JavaScript" type="text/JavaScript">
//pass variables as separate args
function sh_level2s(divid,rrid,tdate) { //show/hide level2 shared rep
  var trid;
	trid = 'if_'+ divid; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(trid).style.getAttribute("visibility") == "" || document.getElementById(trid).style.getAttribute("visibility") == "hidden" ) {
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/collapse.png';
			if (document.getElementById(trid).src == "") {
			document.getElementById(trid).src='http://192.168.20.63/tdw/rep_bssrc_shrd_detail.php?ifid='+trid+'&rep_id='+rrid+'&tdate='+tdate;
			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
		}		
	} 
	else { 
		if (document.layers) { // Netscape 4 
			alert("Netscape 4");
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 
} 
</script>

<table width="100%" cellpadding="1" cellspacing="1">
		<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr> 
										<td height="20" valign="middle" background="images/tables3/header_bk.jpg">
										&nbsp;&nbsp;<a class="table_heading_text">Business Summary : As of <?=format_date_ymd_to_mdy($trade_date_to_process)?></a>
										</td>
									</tr>
									<tr> 
										<td valign="middle">			
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0">
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td>&nbsp;</td>
																<form name="selectionfilter" id="idselectionfilter" action="" method="post">
																<td width="5">&nbsp;</td>
																<td width="150">
																	<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup("divfrom");
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['selectionfilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																		<input type="image" src="images/lf_v1/form_submit.png">
																		</form>
																</td>
															</tr>
														</table>

												<table width="100%" border="0" cellspacing="1" cellpadding="0">
                          <tr bgcolor="#333333"> 
                            <td valign="bottom" width="340"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;REGISTERED REPS.</a></td>
                            <td valign="bottom" width="100" align="right"><a class="tblhead_a"><?=format_date_ymd_to_mdy($trade_date_to_process)?> ($)</a></td>
                            <td valign="bottom" width="100" align="right"><a class="tblhead_a">MTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td valign="bottom" width="100" align="right"><a class="tblhead_a">LY MTD ($)</a></td>
                            <td valign="bottom" width="100" align="right"><a class="tblhead_a">QTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td valign="bottom" width="100" align="right"><a class="tblhead_a">LY QTD ($)</a></td>
                            <td valign="bottom" width="100" align="right"><a class="tblhead_a">YTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td valign="bottom" width="100" align="right"><a class="tblhead_a">LY YTD ($)</a></td>
                            <td valign="bottom">&nbsp;</td>
                          </tr>
												</table>
												<?
												//set the running totals for this section
												$running_total_comm = 0;
												$running_total_mtd  = 0;
												$running_total_qtd  = 0;
												$running_total_ytd  = 0;
												?>

												<?
												//get the names of registered reps which have active trades and have it ordered by lastname
												$qry_get_reps = "SELECT
																						a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
																						from users a, mry_comm_rr_trades b
																					WHERE a.rr_num = b.trad_rr
																					AND b.trad_rr like '0%'
																					GROUP BY b.trad_rr
																					ORDER BY a.Lastname";
													$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
													while($row_get_reps = mysql_fetch_array($result_get_reps))
													{
													$mk_id = md5(rand(1000000000,9999999999));
													//for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
													$rep_to_process = $row_get_reps["rr_num"];
													$srep_user_id = $row_get_reps["ID"]; 													
													?>
													<table width="100%">
														<tr>
															<td class="name_heading"><?=$row_get_reps["rep_name"]?></td>
														</tr>
													</table>
													<?
														//get data for primary rep
														$query_level_0_rep_date = "SELECT max(comm_trade_date) as comm_trade_date
																											FROM mry_comm_rr_level_0
																											WHERE comm_rr = '".$rep_to_process."'
																											AND comm_trade_date <= '".$trade_date_to_process."'";
														//xdebug("query_level_0_rep_date",$query_level_0_rep_date);																											
														$result_level_0_rep_date = mysql_query($query_level_0_rep_date) or die(tdw_mysql_error($query_level_0_rep_date));
														while($row_level_0_rep_date = mysql_fetch_array($result_level_0_rep_date))
														{
															$rep_date_val = $row_level_0_rep_date["comm_trade_date"];
														}
																
														//get data from rep_coom_level_0
														//fields are comm_rr  comm_trade_date  comm_total  comm_mtd  comm_qtd  comm_ytd 
														if ($rep_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																$query_level_0 = "SELECT * 
																									FROM mry_comm_rr_level_0
																									WHERE comm_rr = '".$rep_to_process."'
																									AND comm_trade_date = '".$rep_date_val."'";
																//xdebug("query_level_0",$query_level_0);
																$result_level_0 = mysql_query($query_level_0) or die(mysql_error());
																while($row_level_0 = mysql_fetch_array($result_level_0)) 
																{
																	$show_rr = $row_level_0["comm_rr"];
																	$show_previous_day_comm = number_format($row_level_0["comm_total"],2,'.',",");
																	$show_mtd = number_format($row_level_0["comm_mtd"],2,'.',",");
																	$show_qtd = number_format($row_level_0["comm_qtd"],2,'.',",");
																	$show_ytd = number_format($row_level_0["comm_ytd"],2,'.',",");
																	
																	$running_total_comm = $running_total_comm + $row_level_0["comm_total"];
																	
																	$running_total_mtd = $running_total_mtd + $row_level_0["comm_mtd"];
																	$running_total_qtd = $running_total_qtd + $row_level_0["comm_qtd"];
																	$running_total_ytd = $running_total_ytd + $row_level_0["comm_ytd"];
																	
																}

															} elseif ($rep_date_val != $trade_date_to_process AND $rep_date_val != '') { //data not available for trade_date_to_process
																	$query_level_0e = "SELECT * 
																										FROM mry_comm_rr_level_0
																										WHERE comm_rr = '".$rep_to_process."'
																										AND comm_trade_date = '".$rep_date_val."'";
																	//xdebug("query_level_0e",$query_level_0e);
																	$result_level_0e = mysql_query($query_level_0e) or die(mysql_error());
																	while($row_level_0e = mysql_fetch_array($result_level_0e)) 
																	{
																		$show_rr = $row_level_0e["comm_rr"];
																		$show_previous_day_comm = '<a class="display_zero">'."0.00"."</a>";
																		
																		$running_total_comm = $running_total_comm + 0;
																		
																		$is_same_year = sameyear($rep_date_val,$trade_date_to_process);
																		$is_same_month = samemonth($rep_date_val,$trade_date_to_process);
																		$is_same_qtr = sameqtr($rep_date_val,$trade_date_to_process);
																		//xdebug("adv_date_val",$adv_date_val);
																		//xdebug("trade_date_to_process",$trade_date_to_process);
																		//xdebug("is_same_year",$is_same_year);
																		//xdebug("is_same_month",$is_same_month);
																		//xdebug("is_same_qtr",$is_same_qtr);
															
																		if ($is_same_month == 1) {
																						$running_total_mtd = $running_total_mtd + $row_level_0e["comm_mtd"];
																						$show_mtd = number_format($row_level_0e["comm_mtd"],2,'.',",");
																		} else {
																						$running_total_mtd = $running_total_mtd;
																						$show_mtd = '<a class="display_zero">'."0.00"."</a>";
																		}
																		
																		if ($is_same_qtr == 1) {
																						$running_total_qtd = $running_total_qtd + $row_level_0e["comm_qtd"];
																						$show_qtd = number_format($row_level_0e["comm_qtd"],2,'.',",");
																		} else {
																						$running_total_qtd = $running_total_qtd;
																						$show_qtd = '<a class="display_zero">'."0.00"."</a>";
																		}
																		
																		if ($is_same_year == 1) {
																						$running_total_ytd = $running_total_ytd + $row_level_0e["comm_ytd"];
																						$show_ytd = number_format($row_level_0e["comm_ytd"],2,'.',",");
																		} else {
																						$running_total_ytd = $running_total_ytd;
																						$show_ytd = '<a class="display_zero">'."0.00"."</a>";
																		}
														
																	}
																} else { //no data exists for this client

																			$zero_string = '<a class="display_zero">'."0.00"."</a>";
																			$show_rr = $rep_to_process;
																			$show_previous_day_comm = $zero_string;
																			$show_mtd = $zero_string;
																			$show_qtd = $zero_string;
																			$show_ytd = $zero_string;
																}

?>
<table width="100%" >
	<tr class="trlight" onDblClick="javascript:sh_level2('<?=$mk_id?>','<?=$show_rr?>','<?=$trade_date_to_process?>')"> 
		<td width="340" valign="left">&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="javascript:sh_level2('<?=$mk_id?>','<?=$show_rr?>','<?=$trade_date_to_process?>')">
		<img id="img<?=$mk_id?>" src="images/lf_v1/expand.png" border="0"></a> 
		<?=$row_get_reps["rep_name"]?> (Acct Rep: <?=$show_rr?>)</td>
		<td width="100" align="right"><?=$show_previous_day_comm?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100" align="right"><?=$show_mtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100" align="right"><?=$show_qtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100" align="right"><?=$show_ytd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width="100" align="right">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
<iframe name="if_<?=$mk_id?>" src="" width="100%" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" style="visibility:hidden; display=none"></iframe>

														<?
                            //_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@
														//get shared rep data (sls_sales_reps)
														//fields are  srep_user_id  srep_rrnum  srep_percent
														
														//initialize running total for shared rep
															$shrd_running_total_comm = 0;
															$shrd_running_total_mtd  = 0;
															$shrd_running_total_qtd  = 0;
															$shrd_running_total_ytd  = 0;
															$show_row = 0;

														$qry_get_shared_reps = "SELECT
																											srep_user_id,srep_rrnum,srep_percent
																											from sls_sales_reps
																										WHERE srep_user_id = '".$srep_user_id."'
																										AND srep_isactive = 1 
																										ORDER BY srep_rrnum";
														$result_get_shared_reps = mysql_query($qry_get_shared_reps) or die (tdw_mysql_error($qry_get_shared_reps));
														
														while($row_get_shared_reps = mysql_fetch_array($result_get_shared_reps))
														{
															$show_row = 1;
															$mk_sid = md5(rand(1000000000,9999999999));
															//for trades for shared rep, do a reverse lookup in the users table to get the id and then the shared reps
															$srep_to_process = $row_get_shared_reps["srep_rrnum"];
																	
															//get data for primary rep
															$query_level_0_rep_date = "SELECT max(comm_trade_date) as comm_trade_date
																												FROM mry_comm_rr_level_0
																												WHERE comm_rr = '".$srep_to_process."'
																												AND comm_trade_date <= '".$trade_date_to_process."'";
															//xdebug("query_level_0_rep_date",$query_level_0_rep_date);																											
															$result_level_0_rep_date = mysql_query($query_level_0_rep_date) or die(tdw_mysql_error($query_level_0_rep_date));
															while($row_level_0_rep_date = mysql_fetch_array($result_level_0_rep_date))
																{
																	$rep_date_val = $row_level_0_rep_date["comm_trade_date"];
																}
																
															//get data from rep_coom_level_0
															//fields are comm_rr  comm_trade_date  comm_total  comm_mtd  comm_qtd  comm_ytd 
															if ($rep_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																	$query_level_0 = "SELECT * 
																										FROM mry_comm_rr_level_0
																										WHERE comm_rr = '".$srep_to_process."'
																										AND comm_trade_date = '".$rep_date_val."'";
																	//xdebug("query_level_0",$query_level_0);
																	$result_level_0 = mysql_query($query_level_0) or die(mysql_error());
																	while($row_level_0 = mysql_fetch_array($result_level_0)) 
																	{
																		$show_rr = $row_level_0["comm_rr"];

																		$shrd_running_total_comm = $shrd_running_total_comm + ($row_level_0["comm_total"]);
																		$shrd_running_total_mtd  = $shrd_running_total_mtd + ($row_level_0["comm_mtd"]);
																		$shrd_running_total_qtd  = $shrd_running_total_qtd + ($row_level_0["comm_qtd"]);
																		$shrd_running_total_ytd  = $shrd_running_total_ytd + ($row_level_0["comm_ytd"]);

																		$running_total_comm = $running_total_comm + ($row_level_0["comm_total"]/2);
																		$running_total_mtd = $running_total_mtd + ($row_level_0["comm_mtd"]/2);
																		$running_total_qtd = $running_total_qtd + ($row_level_0["comm_qtd"]/2);
																		$running_total_ytd = $running_total_ytd + ($row_level_0["comm_ytd"]/2);

																	}
	
																} elseif ($rep_date_val != $trade_date_to_process AND $rep_date_val != '') { //data not available for trade_date_to_process
																		$query_level_0e = "SELECT * 
																											FROM mry_comm_rr_level_0
																											WHERE comm_rr = '".$srep_to_process."'
																											AND comm_trade_date = '".$rep_date_val."'";
																		//xdebug("query_level_0e",$query_level_0e);
																		$result_level_0e = mysql_query($query_level_0e) or die(mysql_error());
																		while($row_level_0e = mysql_fetch_array($result_level_0e)) 
																		{
																			$show_rr = $row_level_0e["comm_rr"];
																			
																			$shrd_running_total_comm = $shrd_running_total_comm + 0;
																			$running_total_comm = $running_total_comm + 0;
																			
																			$is_same_year = sameyear($rep_date_val,$trade_date_to_process);
																			$is_same_month = samemonth($rep_date_val,$trade_date_to_process);
																			$is_same_qtr = sameqtr($rep_date_val,$trade_date_to_process);
																
																			if ($is_same_month == 1) {
																							$shrd_running_total_mtd = $shrd_running_total_mtd + $row_level_0e["comm_mtd"];
																							$running_total_mtd = $running_total_mtd + ($row_level_0["comm_mtd"]/2);
																			} else {
																							$shrd_running_total_mtd = $shrd_running_total_mtd;
																			}
																			
																			if ($is_same_qtr == 1) {
																							$shrd_running_total_qtd = $shrd_running_total_qtd + $row_level_0e["comm_qtd"];
																							$running_total_qtd = $running_total_qtd + ($row_level_0["comm_qtd"]/2);
																			} else {
																							$shrd_running_total_qtd = $shrd_running_total_qtd;
																			}
																			
																			if ($is_same_year == 1) {
																							$shrd_running_total_ytd = $shrd_running_total_ytd + $row_level_0e["comm_ytd"];
																							$running_total_ytd = $running_total_ytd + ($row_level_0["comm_ytd"]/2);
																			} else {
																							$shrd_running_total_ytd = $shrd_running_total_ytd;
																			}
															
																		}
																	} else { //no data exists for this client
	
																				$zero_string = '<a class="display_zero">'."0.00"."</a>";
																				$show_rr = $rep_to_process;
																				$show_previous_day_comm = $zero_string;
																				$show_mtd = $zero_string;
																				$show_qtd = $zero_string;
																				$show_ytd = $zero_string;
																	}
															} // end while looking for shared reps				
														
														if ($show_row == 1) {
														?>
														
																	<table width="100%">
																		<tr class="trlight" onDblClick="javascript:sh_level2s('<?=$mk_sid?>','<?=$srep_user_id?>','<?=$trade_date_to_process?>')"> 
																			<td width="340" valign="left">&nbsp;&nbsp;&nbsp;&nbsp;
																			<a href="javascript:sh_level2s('<?=$mk_sid?>','<?=$srep_user_id?>','<?=$trade_date_to_process?>')">
																			<img id="img<?=$mk_sid?>" src="images/lf_v1/expand.png" border="0"></a> 
																			<?=$row_get_reps["rep_name"]?> (Shared)</td>
																			<td width="100" align="right"><?=number_format($shrd_running_total_comm,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right"><?=number_format($shrd_running_total_mtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right"><?=number_format($shrd_running_total_qtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right"><?=number_format($shrd_running_total_ytd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right">&nbsp;</td>
																			<td>&nbsp;</td>
																		</tr>
																	</table>
																	<iframe name="if_<?=$mk_sid?>" src="" width="100%" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" style="visibility:hidden; display=none"></iframe>
														<?
														}
                            //_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@

													} //end while looking for reps
												
     										?>										
												  <hr width="100%" size="2" noshade color="#660000">
		 											<table border="0" width="100%">
												   <tr class="display_totals"> 
                            <td width="340"><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
                            <td width="100" align="right"><?=number_format($running_total_comm,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_mtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_qtd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_ytd,2,'.',",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
						</td>
					</tr>
				</table>
									<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
