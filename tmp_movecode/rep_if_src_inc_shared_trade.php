	<?						
	//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
	//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
	//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
	$query_trades_shared = "SELECT 
											a.trad_advisor_code,
											a.trad_symbol,
											a.trad_buy_sell,
											a.trad_rr as rep_num,
											max(a.trad_advisor_name) as trad_advisor_name,
											sum(a.trad_quantity) as trad_quantity,
											max(a.trade_price) as trade_price,
											sum(a.trad_commission) as trad_commission,
											avg(a.trad_cents_per_share) as trad_cents_per_share
										FROM mry_comm_rr_trades a, sls_sales_reps b
										WHERE b.srep_user_id = '".$user_id."'
										AND a.trad_rr = b.srep_rrnum
										AND a.trad_trade_date = '".$trade_date_to_process."' 
										AND a.trad_is_cancelled = 0
										AND b.srep_isactive = 1 
										GROUP BY a.trad_advisor_code, a.trad_symbol, a.trad_buy_sell, a.trad_rr
										ORDER BY a.trad_advisor_name, a.trad_symbol, a.trad_buy_sell";
	//xdebug("query_trades_shared",$query_trades_shared);
	$result_trades_shared = mysql_query($query_trades_shared) or die(tdw_mysql_error($query_trades_shared));
	$count_row_trades = 1;
	$running_trad_commission_total = 0;
	?>
	
				<?
				if (empty_qry($result_trades_shared) == 0) {
				?>
				<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
					<tr>
						<td valign="top">				
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
							<thead class="datadisplay"> <!--  class="datadisplay" -->
								<tr>
									<th colspan="10" align="left"><font face="Arial, Helvetica, sans-serif" size="2" color="#333333"><strong>Trades (Shared Rep.): <?=format_date_ymd_to_mdy($trade_date_to_process)?>&nbsp;&nbsp;&nbsp;&nbsp;(No trades.)</strong></font></th>
								</tr>
							</thead>
						</table>
						</td>
					</tr>
				</table>
				<?
				} else {
				?>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr bgcolor="#333333">
						<td align="left" class="tblheadx"><strong>&nbsp;Trades (Shared Rep.): <?=format_date_ymd_to_mdy($trade_date_to_process)?></strong></td>
					</tr>
					</table>

					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">				
							<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
								<thead class="datadisplay"> <!--  class="datadisplay" -->
									<tr bgcolor="#333333" class="tblhead_a">
										<th width="276"><a href="" onclick="this.blur(); return sortTable('offTblBdy', 0, false);" title="ADVISOR / CLIENT">ADVISOR / CLIENT</a></th>
										<th width="56"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 1, false);" title="RR #">RR #</a></th>
										<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 2, false);" title="Trade Date">Trade Date</a></th>
										<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 3, false);" title="Symbol">Symbol</a></th>
										<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 4, false);" title="Buy/Sell">B/S</a></th>
										<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 5, false);" title="Shares">Shares</a></th>
										<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 6, false);" title="Price">Price</a></th>
										<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 7, false);" title="Commission">Commission</a></th>
										<th width="100"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 8, false);" title="Comm./Shr. ($)">Comm./Shr. ($)</a></th>
										<th align="left"><a href="#" title="RESEARCH">&nbsp;&nbsp;&nbsp;RESEARCH</a></th>
									</tr>
								</thead>
								<tbody id="offTblBdy" class="datadisplay">
								<?
								while($row_trades_shared = mysql_fetch_array($result_trades_shared))
								{
									if ($count_row_trades % 2) { 
											$class_row = ' class="alternateRow"';
									} else { 
											$class_row = ''; 
									}
									//number_format($row_trades["trad_quantity"],2,'.',",");
									if ($row_trades_shared["trad_advisor_name"] == "") {
										$show_trad_advisor_name = $row_trades_shared["trad_advisor_code"];
									} else {
										$show_trad_advisor_name = $row_trades_shared["trad_advisor_name"];
									}
									$show_trad_rr = $row_trades_shared["rep_num"];
									//$show_trad_trade_date = format_date_ymd_to_mdy($trade_date_to_process);
									$show_trad_trade_date = $row_trades_shared["trad_trade_date"];
									$show_trad_symbol = $row_trades_shared["trad_symbol"];
									$show_trad_buy_sell = $row_trades_shared["trad_buy_sell"];
									$show_trad_quantity = number_format($row_trades_shared["trad_quantity"],0,'.',",");
									$show_trade_price = number_format($row_trades_shared["trade_price"],2,'.',",");
									$show_trad_commission = number_format($row_trades_shared["trad_commission"],2,'.',",");
									$show_trad_cents_per_share = number_format($row_trades_shared["trad_cents_per_share"],3,'.',",");	
									$running_trad_commission_total = $running_trad_commission_total + $row_trades_shared["trad_commission"];
								?>
									<tr<?=$class_row?>>
										<td><div align="left">&nbsp; &nbsp; &nbsp; <?=$show_trad_advisor_name?></div></td>
										<td>&nbsp;&nbsp;&nbsp;<?=$show_trad_rr?></td>
										<td>&nbsp;&nbsp;&nbsp;<?=$show_trad_trade_date?></td>
										<td>&nbsp;&nbsp;&nbsp;<?=$show_trad_symbol?></td>
										<td>&nbsp;&nbsp;&nbsp;<?=$show_trad_buy_sell?></td>
										<td align="right"><?=$show_trad_quantity?>&nbsp;&nbsp;&nbsp;</td>
										<td align="right"><?=$show_trade_price?>&nbsp;&nbsp;&nbsp;</td>
										<td align="right"><?=$show_trad_commission?>&nbsp;&nbsp;&nbsp;</td>
										<td align="right"><?=$show_trad_cents_per_share?>&nbsp;&nbsp;&nbsp;</td>
										<td align="right">&nbsp;<a href="http://192.168.20.65/icil/owa/list_results?in_doc_type=ALL&author=NONE&in_industry=NONE&tickers=<?=$show_trad_symbol?>&dated=<?=format_date_ymd_to_mdy(business_day_backward(strtotime(previous_business_day()),60))?>&dated2=<?=format_date_ymd_to_mdy(previous_business_day())?>" target="_blank"><img src="images/lf_v1/research.png" border="0" alt="Recent Research on <?=$show_trad_symbol?>"></a>&nbsp;&nbsp;&nbsp;</td>
									</tr>
								<?			
									$count_row_trades = $count_row_trades + 1;
								}
								?>
							</tbody>
									<tr bgcolor="#CCCCCC" class="display_totals">
										<td><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right"><?=number_format($running_trad_commission_total,2,'.',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
					
							</table>
							</td>
						</tr>
					</table>
				<?				
				}
				?>		
