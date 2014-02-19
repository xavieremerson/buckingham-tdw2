


<?
//show_array($_GET);

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
//$rep_to_process = $rr_num;

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
											WHERE yrt_advisor_code  = '".$sel_client."'
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

tsp(100, "COMMISSIONS : As of ".format_date_ymd_to_mdy($trade_date_to_process));
?>

				<!-- START TABLE 3 -->
				<style type="text/css">
				tr.trlightx {
					font-family: Verdana, Arial, Helvetica, sans-serif;
					font-size: 12px;
					color: #000000;
					background-color: #FFFFFF;
				}
				tr.trdarkx {
					font-family: Verdana, Arial, Helvetica, sans-serif;
					font-size: 12px;
					color: #000000;
					background-color: #EEEEEE;
				}
				</style>
				
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#FFFFFF">
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td width="5">&nbsp;</td>
																<td width="100">
																<form name="datefilter" id="iddatefilter" action="">
																<select class="Text1" name="sel_client" size="1" >
																<option value="^NONE^">&nbsp;SELECT CLIENT&nbsp;</option>
																<option value="^NONE^">____________</option>
																<script type="text/javascript">
																var dc = new Array()
																<?
																//lkup clients does not show all clients. Query int_clnt_clients instead.
																$arr_final_list_clients = array();
																/*$query_sel_client = "SELECT comm_advisor_code, comm_advisor_name 
																											FROM lkup_clients
																											WHERE length(comm_advisor_name) > 4
																											AND comm_advisor_code != 'BUCK' and comm_advisor_code != 'FRIS'
																											ORDER BY comm_advisor_name, comm_advisor_code";*/
																$query_sel_client = "SELECT clnt_code as comm_advisor_code, clnt_name as comm_advisor_name
																											FROM int_clnt_clients 
																											WHERE clnt_status = 'A'
																											AND clnt_code != '----'
																											AND clnt_code != 'BUCK' and clnt_code != 'FRIS'
																											ORDER BY clnt_name, clnt_code";
																$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
																while($row_sel_client = mysql_fetch_array($result_sel_client)) {
																$arr_final_list_clients[$row_sel_client["comm_advisor_code"]] = $row_sel_client["comm_advisor_name"];
																}
																
																$qry_checks = "SELECT DISTINCT (a.chek_advisor), b.clnt_name
																								FROM chk_chek_payments_etc a, int_clnt_clients b
																								WHERE a.chek_advisor = b.clnt_code
																								AND length( trim( clnt_rr1 ) ) > 0
																								AND a.chek_advisor NOT IN ('TRA2', 'MISC')";
																							 
																$result_checks = mysql_query($qry_checks) or die(mysql_error());
																while($row_checks = mysql_fetch_array($result_checks)) {
																$arr_final_list_clients[$row_checks["chek_advisor"]] = $row_checks["clnt_name"];
																}
																
																ksort($arr_final_list_clients);
															
																$count_row_client = 0;
																foreach ($arr_final_list_clients as $val_code=>$val_name) {
																	echo 'dc ['.$count_row_client.'] = "'.$val_code.'^'.trim($val_name).'"'.";\n";
																	$count_row_client = $count_row_client + 1;
																}
																
																?>
																
																for (i=0;i<dc.length;i++)
																	{
																	var rowclient_array = new Array()
																	rowclient_array=dc[i].split("^");
																	document.write("<option value='"+rowclient_array[0]+"'>"+rowclient_array[1]+"</option>");
																	}
																</script>
																</select>
																</td> 
																<td width="15">&nbsp;</td>
																<td width="150">
																	<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup("divfrom");
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['datefilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																		<input type="image" src="images/lf_v1/form_submit.png">
																</td>
																		</form>
																<td width="14" align="center">&nbsp;</td>
																<!--<td width="100"><a href="rep_ca_container.php"><img src="images/lf_v1/clnt_activity.png" border="0"></a></td> -->
																<td align="center">&nbsp;</td>
															</tr>
														</table>
														</td> 
													</tr>
												</table>
										
										<?
										if ($sel_client AND $sel_client != '^NONE^') {
										
										if ($arr_clients[$sel_client]){
											$comm_advisor_name = $arr_clients[$sel_client];
										} else {
											$comm_advisor_name = $comm_advisor_code;
										}										

										function get_fullname_for_initials ($Initials) {
										  $initial_val = trim($Initials);
											if ($initial_val == '') {
											return 'NONE';
											} else {
											$user_fullname = db_single_val("SELECT Fullname as single_val FROM users WHERE Initials = '".$initial_val."'");   
											return $user_fullname;
											}
										}
									
										$query_get_client_info = "SELECT * from int_clnt_clients where clnt_code = '".$sel_client."'";
										$result_get_client_info = mysql_query($query_get_client_info) or die(mysql_error());
										$str_show_reps_traders = "";
										while($row_get_client_info = mysql_fetch_array($result_get_client_info)){
												$initials_rr1  = trim($row_get_client_info["clnt_rr1"]);			
												$initials_rr2  = trim($row_get_client_info["clnt_rr2"]);	
												
												//xdebug("initials_rr2",$initials_rr2);		
												
												$initials_trdr = $row_get_client_info["clnt_trader"];		
												if (trim(strlen($initials_rr2) < 2)) {
													$str_show_reps_traders = "Sales Rep. : ".get_fullname_for_initials($initials_rr1)."<br>&nbsp;&nbsp;Trader : ".get_fullname_for_initials(trim($initials_trdr));
												} else {
													$str_show_reps_traders = "Sales Rep. : ".get_fullname_for_initials($initials_rr1)." / ".get_fullname_for_initials($initials_rr2)."<br>&nbsp;&nbsp;Trader : ".get_fullname_for_initials(trim($initials_trdr));
												}
										}	
										?>
									<p class="ilt">&nbsp;&nbsp;Selected Client: <?=$comm_advisor_name?><br />&nbsp;&nbsp;<?=$str_show_reps_traders?>
									
									</p>	

										
										
										<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
											<tr>
												<td valign="top">		
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr> 
                            <td colspan="2" bgcolor="#ffffff" width="240"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
                            <td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
                            <td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
                            <td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
														<td bgcolor="#222222" align="center"><a class="tblhead_a">LAST YEAR</a></td>
                            <td bgcolor="#222222" align="center"><a class="tblhead_a">%</a></td>
                            <td bgcolor="#222222">&nbsp;</td>
                          <tr bgcolor="#333333"> 
                            <td width="240"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;CLIENT</a></td>
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
													$arr_relevant_primary_clients = array();
													$arr_relevant_primary_clients[$sel_client] = $sel_client; 
													
													//print_r($arr_relevant_primary_clients);
													//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//Get an array of relevant data for advisors by way of check payments.
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													
													$arr_check_data = array();
													
																$process_advisor = $sel_client;
															
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

													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\

													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													$query_level_a_adv = "SELECT DISTINCT(clnt_code) as comm_advisor_code
																								FROM int_clnt_clients 
																								WHERE clnt_code = '".$sel_client."'";
													//investigate SAC
													$result_level_a_adv = mysql_query($query_level_a_adv) or die(mysql_error());

													$level_a_count = 1; //for css style

													$mk_id_src = 1000;
													while($row_level_a_adv = mysql_fetch_array($result_level_a_adv))
													{

														$mk_id = $mk_id_src; //md5(rand(1000000000,9999999999));
														$mk_id_src = $mk_id_src + 1;
														$comm_advisor_code = $row_level_a_adv["comm_advisor_code"];
														if ($arr_clients[$comm_advisor_code]){
															$comm_advisor_name = $arr_clients[$comm_advisor_code];
														} else {
															$comm_advisor_name = $comm_advisor_code;
														}
														//get data for advisor
														$query_level_a_adv_date = "SELECT max(comm_trade_date) as comm_trade_date
																											FROM mry_comm_rr_level_a
																											WHERE comm_trade_date <= '".$trade_date_to_process."'
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
																											AND comm_trade_date = '".$adv_date_val."'";
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
																		}

																	} elseif ($adv_date_val != $trade_date_to_process AND $adv_date_val != '') { //data not available for trade_date_to_process
																			$query_level_ae = "SELECT * 
																												FROM mry_comm_rr_level_a
																												WHERE comm_advisor_code = '".$comm_advisor_code."'
																												AND comm_trade_date = '".$adv_date_val."'";
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
																$class_row = "trdarkx";
														} else { 
																$class_row = "trlightx"; 
														} 
													
													
															if (in_array($comm_advisor_code,$arr_relevant_primary_clients)) {

																						//capture these clients in an array
																						$arr_commission_clients[$comm_advisor_code] = $comm_advisor_code;
																						
																						//This gets the previous year data
																						$pytotal = get_previous_yr_data($comm_advisor_code);
																						
																						?>
																							<tr class="<?=$class_row?>" >
																								<td valign="middle">&nbsp;&nbsp;<a href='#'><img src="images/t12m.png" border="0" onclick="CreateWnd('chart_t12m.php?clnt=<?=$comm_advisor_code?>', 620, 330, false);"></a>
																								&nbsp;
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
																						}												
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
																								$class_row = "trdarkx";
																						} else { 
																								$class_row = "trlightx"; 
																						} 
												 
																						?>
																							<tr class="<?=$class_row?>"> 
																								<td valign="middle">&nbsp;&nbsp;<a href='#'><img src="images/t12m.png" border="0" onclick="CreateWnd('chart_t12m.php?clnt=<?=$comm_advisor_code?>', 620, 330, false);"></a>&nbsp;&nbsp;<?=$arr_clients[$comm_advisor_code]?></td>
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
																						
																							<? //TRYING SUBACCOUNT ?>
																							<tr class="trlightx" id="<?=$level_a_count?>" style="display=none; visibility=hidden"> 
																								<td colspan="15"> 
																								<?
																								$process_advisor_code_subacct = $comm_advisor_code;
																								//include('rep_if2y_src_inc_subacct.php');
																								?> 
																								</td>
																							</tr>
																							<?
													 }
												}													
												?>
                        </table>
												
												
												<?
												}
												?>
												
												
												
											</td>
										</tr>
									</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
							<tr id="pbd">
								<td height="80%">
								&nbsp;<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
								<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />								
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>