<?
//show_array($_POST);
if ($_POST) {
//xdebug('datefilterval',$datefilterval);
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
$arr_repinfo = split('\^',$sel_rep);
$rep_to_process = $arr_repinfo[0];
$rep_id = $arr_repinfo[1];
$rep_name = $arr_repinfo[2];
//xdebug('trade_date_to_process',$trade_date_to_process);
//xdebug('rep_to_process',$rep_to_process);
//xdebug('rep_id',$rep_id);
} else {
$trade_date_to_process = previous_business_day();

	$qry_get_rep_default = "SELECT
														a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
														from users a, mry_comm_rr_trades b
													WHERE a.rr_num = b.trad_rr
													AND b.trad_rr like '0%'
													GROUP BY b.trad_rr
													ORDER BY a.Lastname LIMIT 1";
	$result_get_rep_default = mysql_query($qry_get_rep_default) or die (tdw_mysql_error($qry_get_rep_default));
	while ( $row_get_rep_default = mysql_fetch_array($result_get_rep_default) )
					{
						$sel_rep = $row_get_rep_default["trad_rr"]."^".$row_get_rep_default["ID"];
						$rep_name = $row_get_rep_default["rep_name"] . "&nbsp; &nbsp;". "(".$row_get_rep_default["rr_num"].")";
					}
	$arr_repinfo = split('\^',$sel_rep);
	$rep_to_process = $arr_repinfo[0];
	$rep_id = $arr_repinfo[1];
//xdebug('trade_date_to_process',$trade_date_to_process);
}
//$rep_to_process = '035'; //'028';

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
// PROCESS TO CHECK PREVIOUS YEAR NUMBERS
// This process should only kick-off when the current date is 2007-01-18
$date_considered = previous_business_day(); //'2007-01-10';
if (strtotime($date_considered) > strtotime('2007-01-09') ) {

// WILL CREATE DATASET FOR PREVIOUS YEAR UNDER THIS CONDITION ONLY
		////
		// Get date in previous year (input and output format: yyyy-mm-dd)
		function get_date_previous_year($dateval) {
		$arr_date = explode("-",$dateval);
		$retval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];
		return $retval;
		}
		
		////
		// Get data in previous year (input and output format: yyyy-mm-dd)
		function get_previous_yr_data($clntval, $dateval, $arr_prev_year, $arr_prev_year_detail) {
		
		//global $arr_prev_year, $arr_prev_year_detail;
		
		 if ($arr_prev_year[$clntval] == "") {
				$arr_out[0] = "";
				$arr_out[1] = "";
				$arr_out[2] = "";
			return $arr_out;
			} else {
				$arr_date = explode("-",$dateval);
				$matchval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];	
		
				$date_old = date('Y-m-d', strtotime($arr_prev_year[$clntval]));
				$date_new = date('Y-m-d', strtotime($matchval));
		
				//xdebug('matchval',$matchval);
				//xdebug('arr_prev_year[$clntval]',$arr_prev_year[$clntval]);
				
				if (samebrokmonth($date_old, $date_new)==1) {
				$arr_out[0] = $arr_prev_year_detail[$clntval][0];
				} else {
				$arr_out[0] = "";
				}
				
				if (samebrokqtr($date_old, $date_new)==1) {
				$arr_out[1] = $arr_prev_year_detail[$clntval][1];
				} else {
				$arr_out[1] = "";
				}
		
				$arr_out[2] = $arr_prev_year_detail[$clntval][2];
			}
			return $arr_out;
		}	
		
		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year = "SELECT comm_advisor_code, max( comm_trade_date ) as comm_trade_date 
											FROM mry_comm_rr_level_a
											WHERE comm_rr = '".$rep_to_process."'
											AND comm_trade_date <= '".$previous_year_date."'
											AND EXTRACT(YEAR FROM comm_trade_date) = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY comm_advisor_code
											ORDER BY comm_advisor_code";
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		$arr_prev_year_detail = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["comm_advisor_code"]] = $row_prev_year["comm_trade_date"];
		
			$qry_prev_year_detail = "SELECT * 
																 FROM mry_comm_rr_level_a
																WHERE comm_advisor_code = '".$row_prev_year["comm_advisor_code"]."'
																AND comm_trade_date = '".$row_prev_year["comm_trade_date"]."'
																AND comm_rr = '".$rep_to_process."'";
																	
			//xdebug('qry_prev_year_detail',$qry_prev_year_detail);
			$result_prev_year_detail = mysql_query($qry_prev_year_detail) or die (tdw_mysql_error($qry_prev_year_detail));
			$arr_prev_year_detail_data = array();
			while ( $row_prev_year_detail = mysql_fetch_array($result_prev_year_detail) ) 
			{
			 $arr_prev_year_detail_data[0] = $row_prev_year_detail["comm_mtd"]; 
			 $arr_prev_year_detail_data[1] = $row_prev_year_detail["comm_qtd"]; 
			 $arr_prev_year_detail_data[2] = $row_prev_year_detail["comm_ytd"]; 
			}
			
			$arr_prev_year_detail[$row_prev_year["comm_advisor_code"]] = $arr_prev_year_detail_data;
		}

} 
//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^



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
										&nbsp;&nbsp;<a class="table_heading_text">Sales Rep : COMMISSIONS : As of <?=format_date_ymd_to_mdy($trade_date_to_process)?> for <?=$rep_name?></a>
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
																<td width="200">
																<form name="selectionfilter" id="idselectionfilter" action="" method="post">
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
																									from users a, mry_comm_rr_trades b
																								WHERE a.rr_num = b.trad_rr
																								AND b.trad_rr like '0%'
																								GROUP BY b.trad_rr
																								ORDER BY a.Lastname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																?>
																<select name="sel_rep" class="Text1">
																<?
																while ( $row = mysql_fetch_array($result_get_reps) )
																				{
																					?> 
																					<option value="<?=$row["trad_rr"]."^".$row["ID"]."^".$row["rep_name"]."&nbsp; &nbsp; (".$row["rr_num"].")"?>"<? if ($row["ID"] == $rep_id) {echo " selected";} ?>><?=$row["rep_name"]?>&nbsp; &nbsp; (<?=$row["rr_num"]?>)</option>
																					<?
																				}
																?>
																</select>
																</td>
																<td width="5">&nbsp;</td>
																<td width="150">
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup();
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['selectionfilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																		<input type="image" src="images/lf_v1/form_submit.png">
																		</form>
																</td>
																<td width="14" align="center">&nbsp;</td>
																<td width="80"><a href="javascript:expandall()"><img src="images/lf_v1/expand_all.png" border="0"></a></td>
																<td width="14" align="center">&nbsp;</td>
																<td width="100"><a href="javascript:collapseall()"><img src="images/lf_v1/collapse_all.png" border="0"></a></td>
																<td width="10" align="center">&nbsp;</td>
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
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//Get an array of relevant data for advisors by way of check payments.
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													
													$arr_check_data = array();
													
													$query_adv_checks = "SELECT DISTINCT(nadd_advisor) as advisor_code
																								FROM mry_nfs_nadd 
																								WHERE nadd_rr_owning_rep = '".$rep_to_process."'
																								AND nadd_branch = 'PDY'
																								AND nadd_advisor not like '&%' 
																								ORDER BY nadd_advisor";
																								
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
																			}
																		} else { //no data exists for this client
																			$str_adv_data = $process_advisor."#0#0#0";
																			$arr_check_data[$process_advisor] = $str_adv_data;
																		}

													}
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\

													
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
																			$show_previous_day_comm = $row_level_a["comm_total"];
																			$show_mtd = $row_level_a["comm_mtd"];
																			$show_qtd = $row_level_a["comm_qtd"];
																			$show_ytd = $row_level_a["comm_ytd"];
																			
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
																				$show_previous_day_comm = 0;
																				
																				$running_total_comm = $running_total_comm + 0;
																				
																				$is_same_year = samebrokyear($adv_date_val,$trade_date_to_process);
																				$is_same_month = samebrokmonth($adv_date_val,$trade_date_to_process);
																				$is_same_qtr = samebrokqtr($adv_date_val,$trade_date_to_process);
																				//xdebug("adv_date_val",$adv_date_val);
																				//xdebug("trade_date_to_process",$trade_date_to_process);
																				//xdebug("is_same_year",$is_same_year);
																				//xdebug("is_same_month",$is_same_month);
																				//xdebug("is_same_qtr",$is_same_qtr);
																	
																				if ($is_same_month == 1) {
																								$running_total_mtd = $running_total_mtd + $row_level_ae["comm_mtd"];
																								$show_mtd = $row_level_ae["comm_mtd"];
																				} else {
																								$running_total_mtd = $running_total_mtd;
																								$show_mtd = 0;
																				}
																				
																				if ($is_same_qtr == 1) {
																								$running_total_qtd = $running_total_qtd + $row_level_ae["comm_qtd"];
																								$show_qtd = $row_level_ae["comm_qtd"];
																				} else {
																								$running_total_qtd = $running_total_qtd;
																								$show_qtd = 0;
																				}
																				
																				if ($is_same_year == 1) {
																								$running_total_ytd = $running_total_ytd + $row_level_ae["comm_ytd"];
																								$show_ytd = $row_level_ae["comm_ytd"];
																				} else {
																								$running_total_ytd = $running_total_ytd;
																								$show_ytd = 0;
																				}
																
																			}
																		} else { //no data exists for this client

																					$show_advisor_name = $comm_advisor_name;
																					$zero_string = 0;
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
													
													  //This gets the previous year data
														$arr_prev_yr = get_previous_yr_data($comm_advisor_code, $trade_date_to_process, $arr_prev_year, $arr_prev_year_detail);
														//show_array($arr_prev_yr);
													
															?>
																<tr class="<?=$class_row?>" onDblClick="javascript:showhidedetail(<?=$level_a_count?>)"> 
																	<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
																	<a href="javascript:showhidedetail(<?=$level_a_count?>)"><img id="img<?=$level_a_count?>" src="images/lf_v1/expand.png" border="0"></a> 
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
																	<td align="right"><?=$arr_prev_yr[0]?>&nbsp;</td>
																	<td align="right"><?=show_numbers($show_qtd + get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=$arr_prev_yr[0]?>&nbsp;</td>
																	<td align="right"><?=show_numbers($show_ytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=$arr_prev_yr[0]?>&nbsp;</td>
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
																
																?>
																<tr class="trlight" id="<?=$level_a_count?>" style="display=none; visibility=hidden"> 
																	<td colspan="16"> 
																	<?
																	$process_advisor_code_subacct = $comm_advisor_code;
																	include('rep_msrc_inc_subacct.php');
																	?> 
																	</td>
																</tr>
															<?
																$level_a_count = $level_a_count + 1;															
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
					<br>
					<?
					include('rep_msrc_shared.php');
					?>
				</td>
			</tr>
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
					<?
					include('rep_msrc_inc_trade.php');
					?>
				</td>
			</tr>
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
					<?
					include('rep_msrc_inc_shared_trade.php');
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