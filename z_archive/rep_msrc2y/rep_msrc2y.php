<?

$arr_commission_clients = array();

//*********************************************************************************************
//Create Lookup Array of Client Code / Client Name

	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}
//*********************************************************************************************



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
													AND a.user_isactive = 1
													AND a.Role > 2
													AND a.Role < 5
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

tsp(100,"Sales Rep : COMMISSIONS : As of ".format_date_ymd_to_mdy($trade_date_to_process)." for ".$rep_name);
?>
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0"> <!-- bgcolor="#CCCCCC"-->
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
																								AND a.Role > 2
																								AND a.Role < 5
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
																<!--<td width="14" align="center">&nbsp;</td>
																<td width="80"><a href="javascript:expandall()"><img src="images/lf_v1/expand_all.png" border="0"></a></td>
																<td width="14" align="center">&nbsp;</td>
																<td width="100"><a href="javascript:collapseall()"><img src="images/lf_v1/collapse_all.png" border="0"></a></td>
																<td width="10" align="center">&nbsp;</td>-->
															</tr>
														</table>
														</td> 
													</tr>
												</table>
								                    
										<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
											<tr>
												<td valign="top">		
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr> 
                            <td colspan="2" bgcolor="#ffffff" width="240"><a class="ghm">&nbsp;&nbsp;&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
                            <td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
                            <td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
                            <td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
														<td bgcolor="#222222" align="center"><a class="tblhead_a">LAST YEAR</a></td>
                            <td bgcolor="#222222" align="center"><a class="tblhead_a">%</a></td>
                            <td bgcolor="#222222">&nbsp;</td>
                          <tr bgcolor="#333333"> 
                            <td width="240"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CLIENT (PRIMARY)</a></td>
                            <td width="40"><a class="tblhead_a">&nbsp;&nbsp;RR</a></td>
                            <td width="70" align="right"><a class="tblhead_a"><?=substr(format_date_ymd_to_mdy($trade_date_to_process),0,5)?>&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
                            <td width="70" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="80" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#222222" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#555555" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#888888" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#222222" align="right"><a class="tblhead_a"> </a></td>
                            <td width="50" bgcolor="#222222" align="center"><a class="tblhead_a"> of LY </a></td>
                            <td>&nbsp;</td>
                          </tr>

													<?
													//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													//Get lookup relevant client codes from client master (internal) for verification
													$qry_relevant_primary_clients = "SELECT DISTINCT (a.clnt_code) as relevant_primary_client
																														FROM int_clnt_clients a, Users b
																														WHERE a.clnt_rr1 = b.Initials
																														AND a.clnt_rr2 = ''
																														AND b.rr_num = '".$rep_to_process."'";
													$result_relevant_primary_clients = mysql_query($qry_relevant_primary_clients) or die (tdw_mysql_error($qry_relevant_primary_clients));
													$arr_relevant_primary_clients = array();
													while ( $row_relevant_primary_clients = mysql_fetch_array($result_relevant_primary_clients) ) 
													{
														$arr_relevant_primary_clients[$row_relevant_primary_clients["relevant_primary_client"]] = $row_relevant_primary_clients["relevant_primary_client"];
													}
													
													//print_r($arr_relevant_primary_clients);
													//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													
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
													//echo "<!--";
													//show_array($arr_check_data);
													//echo "-->";
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\

													
													//set the running totals for this section
														$running_total_comm = 0;
														$running_total_mtd = 0;
														$running_total_qtd = 0;
														$running_total_ytd = 0;

													
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													$query_level_a_adv = "SELECT DISTINCT(a.nadd_advisor) as comm_advisor_code
																								FROM mry_nfs_nadd a, int_clnt_clients b 
																								WHERE a.nadd_rr_owning_rep = '".$rep_to_process."'
																								AND a.nadd_advisor = b.clnt_code
																								AND a.nadd_branch = 'PDY'
																								AND a.nadd_advisor not like '&%' 
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

															//capture these clients in an array
															$arr_commission_clients[$comm_advisor_code] = $comm_advisor_code;
													
															//This gets the previous year data
															$pytotal = get_previous_yr_data($comm_advisor_code);
													
															?>
															
																<tr class="<?=$class_row?>" onDblClick="javascript:showhidedetail(<?=$level_a_count?>)"> 
																	<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
																	<!--<a href="javascript:showhidedetail(<?=$level_a_count?>)"><img id="img<?=$level_a_count?>" src="images/lf_v1/expand.png" border="0"></a>--> 
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
																	<td align="right"><?=show_numbers($show_qtd + get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers($show_ytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=number_format($pytotal,0,'',',')?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers(mkpercent($show_ytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3), $pytotal))?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
																	//include('rep_msrc_inc_subacct.php');
																	?> 
																	</td>
																</tr>
															<?
																$level_a_count = $level_a_count + 1;															
														}
												
												//now process clients which have just checks and no commissions
												$arr_checkonly_clients = array();
												foreach ($arr_relevant_primary_clients as $key => $value) {
													 if (!in_array($value, $arr_commission_clients)) {
													 $arr_checkonly_clients[$value] = $value;	
													 $comm_advisor_code = $value;
	
														//This gets the previous year data
														$pytotal = get_previous_yr_data($comm_advisor_code);
	
																						if ($level_a_count % 2) { 
																								$class_row = "trdark";
																						} else { 
																								$class_row = "trlight"; 
																						} 
												 
																						?>
																							<tr class="<?=$class_row?>"> 
																								<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$arr_clients[$comm_advisor_code]?></td>
																								<td>&nbsp;&nbsp;<?=$show_rr?></td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0)?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 1))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_code, $arr_check_data, 1))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_code, $arr_check_data, 2))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(0 + get_checks_data ($comm_advisor_code, $arr_check_data, 3))?>&nbsp;&nbsp;</td>
																								<td align="right"><?=number_format($pytotal,0,'',',')?>&nbsp;&nbsp;</td>
																								<td align="right"><?=show_numbers(mkpercent($show_ytd + get_checks_data ($comm_advisor_code, $arr_check_data, 3), $pytotal))?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
                            <td width="70" align="right"><?=show_numbers($total_grand_qtd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_grand_ytd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right">&nbsp;</td>
                            <td width="50" align="right">&nbsp;</td>
                            <td>&nbsp;</td>                          
														</tr>
												</table>
											</td>
										</tr>
									</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
							<tr id="shrd"> <!--  style="display=none; visibility=hidden" -->
								<td>
									<br>
									<?
									include('rep_msrc2y_shared.php');
									?>
								</td>
							</tr>
							<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
								<td>
									<br>
									<?
									include('rep_msrc2y_inc_trade.php');
									?>
								</td>
							</tr>
							<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
								<td>
									<br>
									<?
									include('rep_msrc2y_inc_shared_trade.php');
									?>
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
<?
tep();
?>