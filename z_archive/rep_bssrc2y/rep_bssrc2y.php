<?
//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

		////
		// Create percentage with appropriate format
		function mkpercent($a, $b) {
			if ($a == 0) { return 0; }
		  elseif ($b == 0) { return '---'; }
			else { return number_format(($a*100/$b),0,'',','); }		
		}

    ////
		// Get date in previous year (input and output format: yyyy-mm-dd)
		function get_date_previous_year($dateval) {
		$arr_date = explode("-",$dateval);
		$retval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];
		return $retval;
		}
		
		////
		// Get data in previous year (input and output format: yyyy-mm-dd)
		function get_previous_yr_data($clntval) {
		global $arr_prev_year;
			 if ($arr_prev_year[$clntval] == "") {
					$pyc = "";
					return $pyc;
			 } else {
					$pyc = $arr_prev_year[$clntval];
					return $pyc;
			 }
		 }	
		
		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year = "SELECT yrt_advisor_code, yrt_commission 
											FROM yrt_yearly_total_lookup
											WHERE yrt_rr  = '".$rep_to_process."'
											AND yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}
		
		//print_r($arr_prev_year);

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
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
			document.getElementById(trid).src='<?=$_site_url?>rep_bssrc2y_if_prep.php?ifid='+trid+'&rep_num='+rrnum+'&tdate='+tdate;
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
			document.getElementById(trid).src='http://192.168.20.78/tdw/rep_bssrc2y_shrd_detail.php?ifid='+trid+'&rep_id='+rrid+'&tdate='+tdate;
			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
		}		
	} 
} 
</script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--
function populate_div(divid,rrid,tdate) {
  var trid;
	trid = 'div_'+ divid; 

	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(trid).style.getAttribute("visibility") == "" || document.getElementById(trid).style.getAttribute("visibility") == "hidden" ) {
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/collapse.png';
			if (document.getElementById(trid).innerHTML == "") {

					AjaxRequest.get(
						{
							'url':'rep_bssrc2y_shrd_detail.php?ifid='+trid+'&rep_id='+rrid+'&tdate='+tdate
							,'onSuccess':function(req){ document.getElementById(trid).innerHTML=req.responseText; }
							,'onError':function(req){ document.getElementById(trid).innerHTML='Error receiving data.';}
						}
					);

			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
		}		
	} 

}
-->
</script>

<?
tsp(100,"Business Summary : As of ".format_date_ymd_to_mdy($trade_date_to_process));
?>
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

										<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
											<tr>
												<td valign="top">		
                        <table width="100%" border="0" cellspacing="1" cellpadding="0">
                          <tr> 
                            <td bgcolor="#ffffff" width="240"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
                            <td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
                            <td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
                            <td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
														<td bgcolor="#222222" align="center"><a class="tblhead_a">LAST YEAR</a></td>
                            <td bgcolor="#222222" align="center"><a class="tblhead_a">%</a></td>
                            <td bgcolor="#222222">&nbsp;</td>
                          <tr bgcolor="#333333"> 
                            <td width="240"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
                            <td width="70" align="right"><a class="tblhead_a"><?=substr(format_date_ymd_to_mdy($trade_date_to_process),0,5)?>&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#222222" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#555555" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#888888" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#222222" align="right"><a class="tblhead_a"> </a></td>
                            <td width="50" bgcolor="#222222" align="center"><a class="tblhead_a"> of LY </a></td>
                            <td>&nbsp;</td>
                          </tr>
												</table>
												</td>
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
																
														//get data from rep_com_level_0
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
																	$show_previous_day_comm = number_format($row_level_0["comm_total"],0,'.',",");
																	$show_mtd = number_format($row_level_0["comm_mtd"],0,'.',",");
																	$show_qtd = number_format($row_level_0["comm_qtd"],0,'.',",");
																	$show_ytd = number_format($row_level_0["comm_ytd"],0,'.',",");
																	
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
																		
																		$is_same_year =  samebrokyear ($rep_date_val,$trade_date_to_process);
																		$is_same_month = samebrokmonth($rep_date_val,$trade_date_to_process);
																		$is_same_qtr =   samebrokqtr  ($rep_date_val,$trade_date_to_process);
																		//xdebug("adv_date_val",$adv_date_val);
																		//xdebug("trade_date_to_process",$trade_date_to_process);
																		//xdebug("is_same_year",$is_same_year);
																		//xdebug("is_same_month",$is_same_month);
																		//xdebug("is_same_qtr",$is_same_qtr);
															
																		if ($is_same_month == 1) {
																						$running_total_mtd = $running_total_mtd + $row_level_0e["comm_mtd"];
																						$show_mtd = number_format($row_level_0e["comm_mtd"],0,'.',",");
																		} else {
																						$running_total_mtd = $running_total_mtd;
																						$show_mtd = '<a class="display_zero">'."0"."</a>";
																		}
																		
																		if ($is_same_qtr == 1) {
																						$running_total_qtd = $running_total_qtd + $row_level_0e["comm_qtd"];
																						$show_qtd = number_format($row_level_0e["comm_qtd"],0,'.',",");
																		} else {
																						$running_total_qtd = $running_total_qtd;
																						$show_qtd = '<a class="display_zero">'."0"."</a>";
																		}
																		
																		if ($is_same_year == 1) {
																						$running_total_ytd = $running_total_ytd + $row_level_0e["comm_ytd"];
																						$show_ytd = number_format($row_level_0e["comm_ytd"],0,'.',",");
																		} else {
																						$running_total_ytd = $running_total_ytd;
																						$show_ytd = '<a class="display_zero">'."0"."</a>";
																		}
														
																	}
																} else { //no data exists for this client

																			$zero_string = '<a class="display_zero">'."0"."</a>";
																			$show_rr = $rep_to_process;
																			$show_previous_day_comm = $zero_string;
																			$show_mtd = $zero_string;
																			$show_qtd = $zero_string;
																			$show_ytd = $zero_string;
																}
																
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//Get an array of relevant data for advisors by way of check payments.
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													
													$arr_check_data = array();
													
													$query_adv_checks = "SELECT DISTINCT (a.clnt_code) as advisor_code
																														FROM int_clnt_clients a, Users b
																														WHERE a.clnt_rr1 = b.Initials
																														AND a.clnt_rr2 = ''
																														AND b.rr_num = '".$rep_to_process."'";
																								
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
																		$result_level_a = mysql_query($query_level_a) or die(mysql_error());
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
																			$result_level_ae = mysql_query($query_level_ae) or die(mysql_error());
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
																			//echo $process_advisor."=>".$str_adv_data."<br>";
																			}
																		} else { //no data exists for this client
																			$str_adv_data = $process_advisor."#0#0#0";
																			$arr_check_data[$process_advisor] = $str_adv_data;
																		}

													}
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
																

														?>
														<table width="100%" border="0" cellspacing="1" cellpadding="0" >  <!--class="tbl_test" -->
															<tr class="trlight" onDblClick="javascript:sh_level2('<?=$mk_id?>','<?=$show_rr?>','<?=$trade_date_to_process?>')"> 
																<td width="240" valign="left">&nbsp;
																<a href="javascript:sh_level2('<?=$mk_id?>','<?=$show_rr?>','<?=$trade_date_to_process?>')">
																<img id="img<?=$mk_id?>" src="images/lf_v1/expand.png" border="0"></a> 
																<?=$row_get_reps["rep_name"]?> (Acct Rep: <?=$show_rr?>)</td>
																<td width="70" align="right"><?=$show_previous_day_comm?>&nbsp;</td>
																<td width="100" align="right"><?=$show_mtd?>&nbsp;</td>
																<td width="100" align="right"><?=$show_qtd?>&nbsp;</td>
																<td width="100" align="right"><?=$show_ytd?>&nbsp;</td>
																<td width="70" align="right">999</td>
																<td width="70" align="right">999&nbsp;</td>
																<td width="70" align="right">999&nbsp;</td>
																<td width="70" align="right">999&nbsp;</td>
																<td width="70" align="right">999&nbsp;</td>
																<td width="80" align="right">999&nbsp;</td>
																<td width="80" align="right">999&nbsp;</td>
																<td width="50" align="right">999&nbsp;</td>
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
														
																	<table width="100%" border="0" cellspacing="1" cellpadding="0">
																		<tr class="trlight" onDblClick="javascript:sh_level2s('<?=$mk_sid?>','<?=$srep_user_id?>','<?=$trade_date_to_process?>')"> 
																			<td width="240" valign="left">&nbsp;
																			<a href="javascript:populate_div('<?=$mk_sid?>','<?=$srep_user_id?>','<?=$trade_date_to_process?>')">
																			<img id="img<?=$mk_sid?>" src="images/lf_v1/expand.png" border="0"></a> 
																			<?=$row_get_reps["rep_name"]?> (Shared)</td>
																			<td width="70" align="right"><?=number_format($shrd_running_total_comm,0,'.',",")?>&nbsp;</td>
																			<td width="100" align="right"><?=number_format($shrd_running_total_mtd,0,'.',",")?>&nbsp;</td>
																			<td width="100" align="right"><?=number_format($shrd_running_total_qtd,0,'.',",")?>&nbsp;</td>
																			<td width="100" align="right"><?=number_format($shrd_running_total_ytd,0,'.',",")?>&nbsp;</td>
																			<td width="70" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="70" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="70" align="right">999&nbsp;</td>
																			<td width="70" align="right">999&nbsp;</td>
																			<td width="70" align="right">999&nbsp;</td>
																			<td width="80" align="right">999&nbsp;</td>
																			<td width="80" align="right">999&nbsp;</td>
																			<td width="50" align="right">999&nbsp;</td>
																			<td>&nbsp;</td>
																		</tr>
																	  <tr>
																			<td colspan="14">
																	    <div name="div_<?=$mk_sid?>" id="div_<?=$mk_sid?>"></div>
																			</td>
																		</tr>
																	</table>
<!--																		 	<iframe name="if_<?=$mk_sid?>" src="" width="100%" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" style="visibility:hidden; display=none"></iframe>
 -->																	
														<?
														}
                            //_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@

													} //end while looking for reps
												
     										?>										
												  <hr width="100%" size="2" noshade color="#660000">
		 											<table width="100%" border="0" cellspacing="1" cellpadding="0">
												   <tr class="display_totals"> 
                            <td width="240"><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
                            <td width="70" align="right"><?=number_format($running_total_comm,0,'.',",")?>&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_mtd,0,'.',",")?>&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_qtd,0,'.',",")?>&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_ytd,0,'.',",")?>&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="70" align="right">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="50">&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
												</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
<?
tep();
?>
<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
