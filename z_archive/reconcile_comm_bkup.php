<?
if ($x) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)

			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;
			$proceed = 1;

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
<?
//Reconcile data between current DOS ansd NFS
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
										&nbsp;&nbsp;<a class="table_heading_text">COMMISSIONS RECONCILIATION</a>
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
																						<td width="5">&nbsp;</td>
																						<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																						<td width="10" align="center">&nbsp;</td>
																						<td width="10" align="center">&nbsp;</td>
																						<td>&nbsp;</td>
																					</tr>
																				</form>															
																				</table>
																				</td> 
																			</tr>
																			<tr>
																					<td>
																					<a class="links10top">Please Note: Data in Commissions Manager <u>MUST BE</u> saved and backed up for it to appear in the reconciliation report.</a> 
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

												
												//flush the temp dos table
												$result_flushtable = mysql_query("truncate table mry_dos_commission") or die (mysql_error());

												# Constants for dbf field types
												define ('BOOLEAN_FIELD',   'L');
												define ('CHARACTER_FIELD', 'C');
												define ('DATE_FIELD',      'D');
												define ('NUMBER_FIELD',    'N');
												
												# Constants for dbf file open modes
												define ('READ_ONLY',  '0');
												define ('WRITE_ONLY', '1');
												define ('READ_WRITE', '2');
												
												# Path to dbf file
												//$db_file = '\\\\bucknypdc3\\buck\\CommMgr\\TRADING.DBF';
												$db_file = '\\\\bucknypdc3\\buck\\CommMgr\\COMMSION.DBF';
												
												# open dbf file for reading and writing
												$id = @ dbase_open ($db_file, READ_ONLY)
													 or die ("Could not open dbf file <i>$db_file</i>."); 
												
												# find the number of fields (columns) and rows in the dbf file
												$num_fields = dbase_numfields ($id);
												$num_rows   = dbase_numrecords($id);
												
												
												# Loop through the entries in the dbf file
												for ($i=1; $i <= $num_rows; $i++) {
													 $arr_row = dbase_get_record_with_names ($id,$i);
												
													 foreach ($arr_row as $colname => $colval) {
															 if ($colname == 'CUST_CODE') {
																$custcode = $colval;
															 }
															 if ($colname == 'TRADE_DATE') {
																$tradedate = substr($colval,0,4)."-".substr($colval,4,2)."-".substr($colval,6,2);
															 }
															 if ($colname == 'COMM_AMT') {
																$commamt = $colval;
															 }
															 if ($colname == 'deleted') {
																$deleted = $colval;
																	if ($deleted == 0 && $tradedate >= $date_start && $tradedate <= $date_end)  {
																		$qry_insert_dos =  "INSERT INTO mry_dos_commission (clnt_trade_date,clnt_code,clnt_commission) 
																													VALUES (
																													'".$tradedate."', 
																													'".$custcode."', 
																													'".$commamt."'
																													)"; 
																		$result_insert_dos = mysql_query($qry_insert_dos) or die (tdw_mysql_error($qry_insert_dos));
																	} else {
																		//echo "ignore this record<br>";
																	}
															 }
														}
												} 
												
												# close the dbf file
												dbase_close($id);
												
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
												<a href="reconcile_comm_dos2excel.php?xstart=<?=$date_start?>&xend=<?=$date_end?>" target="_blank" class="links12blue"><u>GET DOS TO EXCEL</u></a><br>
												<?
												?>
												
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
																		<td>&nbsp;&nbsp;<strong>DOS</strong></td>
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
																								AND trad_trade_date between '".$date_start."' AND '".$date_end."'".
																								" GROUP BY trad_advisor_code ORDER BY trad_advisor_code";
																								
																				
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
																													max(trad_advisor_name) as trad_advisor_name,
																													FORMAT(sum(trad_quantity),0) as trad_quantity,
																													FORMAT(max(trade_price),2) as trade_price,
																													FORMAT(sum(trad_commission),2) as trad_commission,
																													sum(trad_commission) as for_sum_trad_commission,
																													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
																													max(trad_rr) as trad_rr 
																												FROM mry_comm_rr_trades 
																												WHERE trad_is_cancelled = 0 and trad_advisor_code = '".$row_adv_level["trad_advisor_code"]."'  
																												AND trad_trade_date between '".$date_start."' AND '".$date_end."'".
																												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
																												ORDER BY trad_advisor_name, sum(trad_commission)";
																												//removed from order by clause :  trad_symbol, trad_buy_sell, trad_trade_date
																				$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
																				while($row_trades = mysql_fetch_array($result_trades)) {
																				$running_total_adv = $running_total_adv + $row_trades["for_sum_trad_commission"];
																				$running_total_adv_level = $running_total_adv_level + $row_trades["for_sum_trad_commission"];
																				
																			?>
												
																			<tr>
																			<td><?=$row_trades["trad_trade_date"]?></td>
																			<td><?=$row_trades["trad_advisor_code"]?></td>
																			<td><?=$row_trades["trad_rr"]?></td>
																			<td><?=$row_trades["trad_symbol"]?></td>
																			<td><?=$row_trades["trad_buy_sell"]?></td>
																			<td align="right"><?=$row_trades["trad_quantity"]?></td>
																			<td align="right"><?=$row_trades["trade_price"]?></td>
																			<td align="right"><?=$row_trades["trad_commission"]?></td>
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
																	<table class="all_general">
																		<tr>
																			<td width="50">Client</td>
																			<td width="30">Commission</td>
																		</tr>
												
																		<?
																		//DOS COMMISSIONS
																		$total_dos = 0;
																		$query_dos = "SELECT * 
																									FROM mry_dos_commission 
																									WHERE clnt_code = '".$row_adv_level["trad_advisor_code"]."' order by clnt_commission ";
																		$result_dos = mysql_query($query_dos) or die(tdw_mysql_error($query_dos));
																		while($row_dos = mysql_fetch_array($result_dos)) {
																		$total_dos = $total_dos + $row_dos["clnt_commission"];
																		$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
																		?>
																		<tr>
																			<td><?=$row_dos["clnt_code"]?></td>
																			<td align="right"><?=number_format($row_dos["clnt_commission"],2)?></td>
																		</tr>
																		<?
																		}
																		?>
																		<tr>
																			<td></td>
																			<td align="right">
																			<?
																			$diff_val = abs($total_dos - $compare_val_nfs);
																			if ($diff_val == 0) {
																			echo '<strong>'.number_format($total_dos,2).'</strong>';
																			} else {
																			echo '<strong><font color="FF0000"><u>'.number_format($total_dos,2).'</u></font></strong>';
																			}
																			?>
																			</td>
																		</tr>
																	</table>
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
													<td>
													<!-- Showing clients in DOS which don't exist in NFS -->
																	<hr size="3" noshade color="#0000ff">
																	<table class="all_general">
																		<tr>
																			<td width="50"><strong>Client</strong></td>
																			<td width="30"><strong>Commission</strong></td>
																		</tr>
												
																		<?
																		//DOS COMMISSIONS
																		$total_dos = 0;
																		$query_dos = "SELECT * 
																									FROM mry_dos_commission order by clnt_code";
																		$result_dos = mysql_query($query_dos) or die(tdw_mysql_error($query_dos));
																		while($row_dos = mysql_fetch_array($result_dos)) {
																		//$total_dos = $total_dos + $row_dos["clnt_commission"];
																		//$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
																			if (in_array($row_dos["clnt_code"],$arr_clients)) {
																			//do nothing
																			} else {
																			$total_dos = $total_dos + $row_dos["clnt_commission"];
																			$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
																		?>
																		<tr>
																			<td><?=$row_dos["clnt_code"]?></td>
																			<td align="right"><?=number_format($row_dos["clnt_commission"],2)?></td>
																		</tr>
																		<?
																		 	}
																		}
																		?>
																		<tr>
																			<td><strong></strong></td>   
																			<td align="right">
																			<?
																			echo '<strong><font color="0000ff"><u>'.number_format($total_dos,2).'</u></font></strong>';
																			?>
																			</td>
																		</tr>
																	</table>
													</td>
												</tr>
												
												
												
												
												
												
												
												<tr>
													<td colspan="3"><hr size="2" noshade color="#000066"></td>
												</tr>
												<tr>
												<td align="right">
													<table width="100%">
														<tr>
															<td align="left">Difference: <?=number_format(abs($grand_total_dos-$running_total_adv_level),2)?></td>
															<td align="right"><strong><?=number_format($running_total_adv_level ,2)?></strong>&nbsp;&nbsp;</td>
														</tr>
													</table>
												</td>
												<td bgcolor="#666666" width="2"></td>
												<td align="right">
												<strong><?=number_format($grand_total_dos,2)?></strong></td>
												</tr>
												<tr>
													<td colspan="3"><hr size="2" noshade color="#000066"></td>
												</tr>
												</table>
												</TD></tr></table>	
												<!-- ***************************** -->
												<?
												}
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
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
