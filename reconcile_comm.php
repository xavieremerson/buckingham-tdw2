<?
if ($x) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)

			//if brokerage month is selected use that info to create dateto and datefrom values
			if($_POST["sel_month"] == '') {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;
				
			} else {
				// ^ caused problems, had to escape it				
				$arr_split_input = split("\^", $sel_month);
				$arr_dates = get_commission_month_dates($arr_split_input[0],$arr_split_input[1]);
				$sel_datefrom = format_date_ymd_to_mdy($arr_dates[0]);
				$sel_dateto = format_date_ymd_to_mdy($arr_dates[1]);
			}
			  $proceed = 1;

/*xdebug("sel_datefrom",$sel_datefrom);
xdebug("sel_dateto",$sel_dateto);*/
//exit;
} else {

			$trade_date_to_process = previous_business_day();
			
			$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
			$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);
			$proceed = 0;
}
?>

<style type="text/css">
<!--
.all_general {
	font-family: "Courier New", Courier, mono;
	font-size: 13px;
}
-->
</style>
<SCRIPT>
<!--
function doBlink() {
	var blink = document.all.tags("BLINK")
	for (var i=0; i<blink.length; i++)
		blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "" 
}

function startBlink() {
	if (document.all)
		setInterval("doBlink()",1000)
}
window.onload = startBlink;
// -->
</SCRIPT>
<?
//Reconcile data between current DOS ansd NFS

tsp(100, "COMMISSIONS RECONCILIATION");
?>

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
																					<form name="comm_reconcile" id="idcomm_reconcile" action="" method="post">
																					<tr>
																							<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																							<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																							<SCRIPT LANGUAGE="JavaScript">
																							var calfrom = new CalendarPopup("divfrom");
																							calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																							var calto = new CalendarPopup("divto");
																							calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																							</SCRIPT>																
																						<td width="5">&nbsp;</td>
																						<td width="10">From:</td>
																						<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" readonly size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
																						<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['comm_reconcile'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																						<td width="5">&nbsp;</td>
																						<td width="10">To:</td>
																						<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" readonly size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
																						<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['comm_reconcile'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																						<td width="10" align="center">&nbsp;</td>
																						<td width="10" align="center">OR</td>
																						<td width="10" align="center">&nbsp;</td>
																						<td width="100">																
																						<select class="Text1" name="sel_month" size="1" >
																						<option value="">&nbsp;BROKERAGE MONTH&nbsp;&nbsp;</option>
																						<option value="">_______________</option>
																						<?
																						echo create_commission_month();
																						?>
																						</select>
																						</td>
																						<td width="5">&nbsp;</td>
																						<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																						<td>&nbsp;</td>
																					</tr>
																				</form>															
																				</table>
																				</td> 
																			</tr>
																		</table>
																		<!-- END TABLE 4 -->
													</td>
												</tr>
												</table>
												<!-- END TABLE 3 -->
												</td>
											</tr>
											<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
												<td>
												
												<?
												if ($proceed == 1) {
												
												$date_start = format_date_mdy_to_ymd($sel_datefrom);
												$date_end   = format_date_mdy_to_ymd($sel_dateto);

												//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
												//Getting settle dates for inclusion in the query AND condition.
												//xdebug("date_start",$date_start);												
												//xdebug("date_end",$date_end);
												
												
												$z_settle_start = db_single_val("select max(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date = '".$date_start."'");
												//IF THE MONTH IS DECEMBER THEN THE START DATE SHOULD BE THE NEXT MONDAY AFTER THE LAST FRIDAY.
												if ($z_settle_start == "") {
													$new_date_start = business_day_forward(strtotime($date_start), 1);
													//xdebug("new_date_start",$new_date_start);
													$z_settle_start = db_single_val("select max(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date = '".$new_date_start."'");
												}
												
												$z_settle_end =   db_single_val("select max(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date = '".$date_end."'");
												
												//xdebug("z_settle_start",$z_settle_start);												
												//xdebug("z_settle_end",$z_settle_end);
												//exit;
												
												
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//MADE THIS CHANGE BECAUSE 10/9/2009 and 10/12/2009 HAD THE SAME SETTLE DATE (CHANGED ON 10/13/20008)
												//$str_qry_append = " trad_settle_date between '".$z_settle_start."' and '".$z_settle_end."' ";
												//$str_qry_append = " OR ( trad_settle_date between '".$z_settle_start."' and '".$z_settle_end."' ) ";
												$str_qry_append = " ";
                        
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
												//  ".$str_qry_append.")
												//exit;
												//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

												//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
												$time_alpha=getmicrotime();
												//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++

												?>
												 <!-- ***************************** -->
												<?
												//get data like all rep client activity
												
												
												//$datefrom = previous_business_day();
												//$dateto   = previous_business_day();
												
												
												echo "<a class='all_general'>&nbsp;Report for Trade Date ".format_date_ymd_to_mdy($date_start)." to ".format_date_ymd_to_mdy($date_end)."</a>";
												echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
												?>
												<a href="reconcile_comm_print.php?xstart=<?=$date_start?>&xend=<?=$date_end?>" target="_blank" class="links12blue"><u>PRINT</u></a>&nbsp;&nbsp;&nbsp;&nbsp;
											<br>



												<?	
												//get all NFS trades
															//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
															//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
															//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
												?>
												<table border="0" cellpadding="4" cellspacing="0"><tr><TD>
															<table border="0" cellspacing="1" cellpadding="0">
																	<tr>
																		<td colspan="3"><hr size="2" noshade color="#000066"></td>
																	</tr>
																	<tr>
																		<td>&nbsp;&nbsp;<strong>NFS</strong></td>
																		<td bgcolor="#666666" width="2"></td>
																		<td>&nbsp;&nbsp;</td>
																	</tr>
																	<tr>
																		<td colspan="3"><hr size="2" noshade color="#000066"></td>
																	</tr>
												      <?
												      //high level numbers by advisor and total commission dollars
															$query_adv_level = "SELECT  
																									trad_advisor_code,
																									DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
																									max(trad_advisor_name) as trad_advisor_name,
																									FORMAT(sum(trad_commission),2) as trad_commission,
																									sum(trad_commission) as xfor_sum_trad_commission,
																									max(trad_rr) as trad_rr 
																								FROM mry_comm_rr_trades 
																								WHERE trad_is_cancelled = 0 
																								AND 
																								    (
																											(trad_run_date between '".$date_start."' AND '".$date_end."') 
																											".$str_qry_append."
																										)
																								GROUP BY trad_advisor_code ORDER BY trad_advisor_code";
															//xdebug("query_adv_level",$query_adv_level);	
															//exit;						 		
															$result_adv_level = mysql_query($query_adv_level) or die(tdw_mysql_error($query_adv_level));
															$count_clients = 0;
															$arr_clients = array();
															while($row_adv_level = mysql_fetch_array($result_adv_level))
															{
															$arr_clients[$count_clients] = $row_adv_level["trad_advisor_code"];
															$count_clients = $count_clients + 1;
															?>
															<tr>
																<td valign="top">
																<hr size="1" noshade color="#000066">
																	<table class="all_general">
																			<tr>
																			<td width="80">Trade Date</td>
																			<td width="50">Client</td>
																			<td width="30">Rep</td>
																			<td width="60">Symbol</td>
																			<td width="20">B/S</td>
																			<td width="60" align="right">Qty.</td>
																			<td width="60" align="right">Price</td>
																			<td width="60" align="right">Comm.</td>
																			</tr>							
																	<?
																				$running_total_adv = 0;
																				$query_trades = "SELECT 
																													trad_advisor_code,
																													trad_symbol,
																													trad_buy_sell,
																													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
																													trad_trade_date as compare_trade_date,
																													trad_run_date as compare_run_date,
																													max(trad_advisor_name) as trad_advisor_name,
																													FORMAT(sum(trad_quantity),0) as trad_quantity,
																													FORMAT(max(trade_price),2) as trade_price,
																													FORMAT(sum(trad_commission),2) as trad_commission,
																													sum(trad_commission) as for_sum_trad_commission,
																													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
																													max(trad_rr) as trad_rr,
																													trad_is_cancelled 
																												FROM mry_comm_rr_trades 
																												WHERE trad_is_cancelled < 2 and trad_advisor_code = '".$row_adv_level["trad_advisor_code"]."'  
																												AND 
																												(
																													(trad_run_date between '".$date_start."' AND '".$date_end."') 
																													".$str_qry_append."
																												)
																												GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_run_date, trad_trade_date,trad_is_cancelled 
																												ORDER BY trad_advisor_name, trad_trade_date, sum(trad_commission)";
																												//removed from order by clause :  trad_symbol, trad_buy_sell, trad_trade_date
																				$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
																				while($row_trades = mysql_fetch_array($result_trades)) {
																			?>
																			<tr>
																			<?
																				if ($row_trades["trad_is_cancelled"] == 0) {
																								if ($row_trades["compare_trade_date"] == $row_trades["compare_run_date"]) {
																										$running_total_adv = $running_total_adv + $row_trades["for_sum_trad_commission"];
																										$running_total_adv_level = $running_total_adv_level + $row_trades["for_sum_trad_commission"];
																						?>
																									<td><?=$row_trades["trad_trade_date"]?></td>
																									<td><?=$row_trades["trad_advisor_code"]?></td>
																									<td><?=$row_trades["trad_rr"]?></td>
																									<td><?=$row_trades["trad_symbol"]?></td>
																									<td><?=$row_trades["trad_buy_sell"]?></td>
																									<td align="right"><?=$row_trades["trad_quantity"]?></td>
																									<td align="right"><?=$row_trades["trade_price"]?></td>
																									<td align="right"><?=$row_trades["trad_commission"]?></td>
																						<?
																								} else {
																								
																										if (
																													($date_start != $date_end AND strtotime($row_trades["trad_trade_date"]) >= strtotime($date_start))
																													OR
																													($row_trades["trad_trade_date"] == format_date_ymd_to_mdy($date_start))
																												) 
																										{
																											if ($row_trades["trad_advisor_code"] == 'CRON') {
																											//xdebug($row_trades["trad_advisor_code"],$row_trades["trad_commission"]);
																											}
																											$running_total_adv = $running_total_adv + $row_trades["for_sum_trad_commission"];
																											$running_total_adv_level = $running_total_adv_level + $row_trades["for_sum_trad_commission"];
																										}

																						?>
																										<td><strong><u><i><?=$row_trades["trad_trade_date"]?></i></u></strong></td>
																										<td><i><?=$row_trades["trad_advisor_code"]?></i></td>
																										<td><i><?=$row_trades["trad_rr"]?></i></td>
																										<td><i><?=$row_trades["trad_symbol"]?></i></td>
																										<td><i><?=$row_trades["trad_buy_sell"]?></i></td>
																										<td align="right"><i><?=$row_trades["trad_quantity"]?></i></td>
																										<td align="right"><i><?=$row_trades["trade_price"]?></i></td>
																										<td align="right"><i><?=$row_trades["trad_commission"]?></i></td>
																						<?
																								}
																				} else {
																				?>
																						<td><strike><?=$row_trades["trad_trade_date"]?></strike></td>
																						<td><strike><?=$row_trades["trad_advisor_code"]?></strike></td>
																						<td><strike><?=$row_trades["trad_rr"]?></strike></td>
																						<td><strike><?=$row_trades["trad_symbol"]?></strike></td>
																						<td><strike><?=$row_trades["trad_buy_sell"]?></strike></td>
																						<td align="right"><strike><?=$row_trades["trad_quantity"]?></strike></td>
																						<td align="right"><strike><?=$row_trades["trade_price"]?></strike></td>
																						<td align="right"><strike><?=$row_trades["trad_commission"]?></strike></td>
																						
																				<?
																				}
																			?>
																			</tr>							
																			<?
																				}
																			?>
																			<tr>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td></td>
																			<td align="right"></td>
																			<td align="right"></td>
																			<td align="right"><strong><?=number_format($running_total_adv ,2)?></strong></td>
																			<? $compare_val_nfs = $running_total_adv; ?>
																			</tr>							
																	</table>
															
																</td>
																<td bgcolor="#666666" width="2"></td>
																<td valign="top">
																	<hr size="1" noshade color="#000066">
																</td>			
															</tr>
															
															<?			
															}
															?>
												<tr>
													<td valign="top">
													<hr size="1" noshade color="#000066">
														<table class="all_general">
																<tr>
																	<td>&nbsp;</td>
																</tr>
														</table>
													</td>
													<td bgcolor="#666666" width="2"></td>
													<td>&nbsp;</td>
												</tr>
												
											<?
											//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
											//Show the commission numbers where there is a cancel in NFS but no entry in dos for the cancel date

											?>
												<tr>
													<td valign="top">
													<hr size="1" noshade color="#000066">
														<table class="all_general">
																<tr>
																	<td>
																	<?
																	//initialize array of the clients in NFS
																	$arr_nfs_clients = array();
																	$count_nfs_clients = 0;
																	
																	$qry_distinct_adv_codes = "SELECT 
																																distinct(trad_advisor_code)
																															FROM mry_comm_rr_trades 
																															WHERE trad_is_cancelled = 1
																															AND 
																															(
																																(trad_run_date between '".$date_start."' AND '".$date_end."') 
																																".$str_qry_append."
																															)";	
																	$result_distinct_adv_codes = mysql_query($qry_distinct_adv_codes) or die(tdw_mysql_error($qry_distinct_adv_codes));
																	while($row_distinct_adv_codes = mysql_fetch_array($result_distinct_adv_codes)) {
																		$arr_nfs_clients[$count_nfs_clients] = $row_distinct_adv_codes["trad_advisor_code"];
																		$count_nfs_clients = $count_nfs_clients + 1;									
																	}
																	
																	//show_array($arr_nfs_clients);
																	//show_array($arr_clients_dos);
																	

																			foreach ($arr_nfs_clients as $nfs_client)
																			{
																					$qry_data_shown = "SELECT trad_advisor_code
																																			FROM mry_comm_rr_trades 
																																			WHERE trad_is_cancelled = 0
																																			AND trad_advisor_code = '".$nfs_client."'  
																																			AND 
																																			(
																																				(trad_run_date between '".$date_start."' AND '".$date_end."') 
																																				".$str_qry_append."
																																			)";	
																					$result_data_shown = mysql_query($qry_data_shown) or die(tdw_mysql_error($qry_data_shown));
																					$num_qry_data_shown = mysql_num_rows($result_data_shown);
																						if ($num_qry_data_shown == 0) {
																						//echo "Value: " . $nfs_client . "<br/>";		
																						
																						$query_cancels = "SELECT 
																																trad_advisor_code,
																																trad_symbol,
																																trad_buy_sell,
																																DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
																																trad_trade_date as compare_trade_date,
																																trad_run_date as compare_run_date,
																																FORMAT(sum(trad_quantity),0) as trad_quantity,
																																FORMAT(max(trade_price),2) as trade_price,
																																FORMAT(sum(trad_commission),2) as trad_commission,
																																sum(trad_commission) as for_sum_trad_commission,
																																FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
																																max(trad_rr) as trad_rr,
																																trad_is_cancelled 
																															FROM mry_comm_rr_trades 
																															WHERE trad_is_cancelled = 1 and trad_advisor_code = '".$nfs_client."'  
																															AND 
																															(
																																(trad_run_date between '".$date_start."' AND '".$date_end."') 
																																".$str_qry_append."
																															)
																															GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_run_date, trad_trade_date,trad_is_cancelled 
																															ORDER BY trad_advisor_name, trad_trade_date, sum(trad_commission)";
																							$result_cancels = mysql_query($query_cancels) or die(tdw_mysql_error($query_cancels));
																							echo '<table class="all_general">';
																							while($row_cancels = mysql_fetch_array($result_cancels)) {
																							?>
																								<tr>
																								<td width="80"><strike><?=$row_cancels["trad_trade_date"]?></strike></td>
																								<td width="50"><strike><?=$row_cancels["trad_advisor_code"]?></strike></td>
																								<td width="30"><strike><?=$row_cancels["trad_rr"]?></strike></td>
																								<td width="60"><strike><?=$row_cancels["trad_symbol"]?></strike></td>
																								<td width="20"><strike><?=$row_cancels["trad_buy_sell"]?></strike></td>
																								<td width="60" align="right"><strike><?=$row_cancels["trad_quantity"]?></strike></td>
																								<td width="60" align="right"><strike><?=$row_cancels["trade_price"]?></strike></td>
																								<td width="60" align="right"><strike><?=$row_cancels["trad_commission"]?></strike></td>
																								</tr>
																							<?
																							}
																							echo '</table>';
																						}
																			}
																		?>
																					
																				<!-- Showing clients in NFS which don't exist in DOS -->
																				<hr size="3" noshade color="#ffffff">
																	</td>
																</tr>
														</table>
													</td>
													<td bgcolor="#666666" width="2"></td>
													<td valign="top">&nbsp;</td>
												</tr>
											<?

											//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
											?>
												
												<tr>
													<td colspan="3"><hr size="2" noshade color="#000066"></td>
												</tr>
												<tr>
												<td align="right">
													<table width="100%">
														<tr>
															<td align="left">&nbsp;</td>
															<td align="right"><strong><?=number_format($running_total_adv_level ,2)?></strong>&nbsp;&nbsp;</td>
														</tr>
													</table>
												</td>
												<td bgcolor="#666666" width="2"></td>
												<td align="right">&nbsp;</td>
												</tr>
												<tr>
													<td colspan="3"><hr size="2" noshade color="#000066"></td>
												</tr>
												</table>
												</td>
												</tr>
												</table>	
												<!-- ***************************** -->
												<?
												}
												?>

<?
tep();
?>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
<?
//exit;
?>