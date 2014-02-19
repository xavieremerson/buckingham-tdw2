<style type="text/css">
<!--
.all_general {
	font-family: "Courier New", Courier, mono;
	font-size: 13px;
}
-->
</style>
<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
												
$date_start = $xstart;
$date_end   = $xend;

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//Getting settle dates for inclusion in the query AND condition.
//xdebug("date_start",$date_start);												
//xdebug("date_end",$date_end);

$z_settle_start = db_single_val("select max(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date = '".$date_start."'");
$z_settle_end = db_single_val("select max(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date = '".$date_end."'");

//xdebug("z_settle_start",$z_settle_start);												
//xdebug("z_settle_end",$z_settle_end);

$str_qry_append = " trad_settle_date between '".$z_settle_start."' and '".$z_settle_end."' ";

//exit;
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

?>

<body onLoad="window.print();" > 
<table width="502" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top"><img src="images/logo.gif"></td>
		<td valign="top">
		
		<?
echo "<a class='all_general'><b>Reconciliation Report<br>Trade Date ".format_date_ymd_to_mdy($date_start)." to ".format_date_ymd_to_mdy($date_end)."</b></a>";
?>

		
		</td>
	</tr>
</table>


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
						<td>&nbsp;&nbsp;<strong>TDW / NFS</strong></td>
						<td bgcolor="#666666" width="2"></td>
						<td>&nbsp;</td>
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
																											(trad_run_date between '".$date_start."' AND '".$date_end."') OR
																											(".$str_qry_append.")
																										)
																								GROUP BY trad_advisor_code ORDER BY trad_advisor_code";
												
								
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
																													(trad_run_date between '".$date_start."' AND '".$date_end."') OR
																													(".$str_qry_append.")
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
				<td valign="top">&nbsp;
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
																																(trad_run_date between '".$date_start."' AND '".$date_end."') OR
																																(".$str_qry_append.")
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
																																				(trad_run_date between '".$date_start."' AND '".$date_end."') OR
																																				(".$str_qry_append.")
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
																																(trad_run_date between '".$date_start."' AND '".$date_end."') OR
																																(".$str_qry_append.")
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
</TD></tr></table>	
</body>
<!-- ***************************** -->

