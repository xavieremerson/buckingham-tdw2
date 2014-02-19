	<?						
			//Show research link only for the symbols in the coverage universe
			$arr_cu = get_coverage_universe();

	//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
	//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
	//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
	$query_trades = "SELECT 
										a.trad_advisor_code,
										a.trad_symbol,
										a.trad_buy_sell,
										max(a.trad_rr) as trad_rr,
										DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
										max(a.trad_advisor_name) as trad_advisor_name,
										FORMAT(sum(a.trad_quantity),0) as trad_quantity,
										FORMAT(max(a.trade_price),4) as trade_price,
										FORMAT(sum(a.trad_commission),2) as trad_commission,
										sum(a.trad_commission) as for_sum_trad_commission,
										FORMAT(avg(a.trad_cents_per_share),3) as trad_cents_per_share 
									FROM mry_comm_rr_trades a, int_clnt_clients b, Users c  
									WHERE a.trad_trade_date = '".$trade_date_to_process."'   
									AND trad_is_cancelled = 0 
									AND a.trad_advisor_code = b.clnt_code
									AND b.clnt_trader = c.Initials
									AND c.ID = '".$trdr_user_id."'
									GROUP BY a.trad_advisor_code, a.trad_symbol, a.trad_buy_sell
									ORDER BY a.trad_advisor_name, a.trad_symbol, a.trad_buy_sell";										
										
	//xdebug("query_trades",$query_trades);
	$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
	$count_row_trades = 1;
	$running_trad_commission_total = 0;
	?>
	
				<?
				if (empty_qry($result_trades) == 0) {
				?>
				<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
					<tr>
						<td valign="top">				
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
							<thead class="datadisplay"> <!--  class="datadisplay" -->
								<tr>
									<th colspan="10" align="left"><font face="Arial, Helvetica, sans-serif" size="2" color="#333333"><strong>Trades: <?=format_date_ymd_to_mdy($trade_date_to_process)?>&nbsp;&nbsp;&nbsp;&nbsp;(No trades.)</strong></font></th>
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
						<td align="left" class="tblheadx"><strong>&nbsp;Trades: <?=format_date_ymd_to_mdy($trade_date_to_process)?></strong></td>
					</tr>
					</table>
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
						<tr>
							<td valign="top">		
							<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>		
							<table class="sortable" preserve_style="cell" width="100%" border="0" cellspacing="1" cellpadding="1">
								<thead class="datadisplay">
									<tr bgcolor="#CCCCCC">
									<td ts_type="date" width="90"> Trade Date</td>
									<td width="276">Client</td>
									<td width="56"> RR #</td>
									<td width="80"> Symbol</td>
									<td width="80"> B/S</td>
									<td ts_type="money" width="80"> Shares</td>
									<td ts_type="money" width="80"> Price</td>
									<td ts_type="money" width="80"> Commission</td>
									<td ts_type="money" width="100"> Comm./Shr.</td>
									<td>&nbsp;&nbsp;&nbsp;RESEARCH</td>
									</tr>
								</thead>
 								<tbody id="offTblBdy" class="datadisplay">
								<script type="text/javascript">
								var dt = new Array()

								<?
								$count_row_trades = 0;
								$running_trad_commission_total = 0;
								while($row_trades = mysql_fetch_array($result_trades))
								{

								if ($row_trades["trad_advisor_name"] == '') {
									$show_trad_advisor_name = $row_trades["trad_advisor_code"];
								} else {
									$show_trad_advisor_name = $row_trades["trad_advisor_name"];
								}
								
								$show_trad_rr = $rep_to_process;
								$show_trad_trade_date = format_date_ymd_to_mdy($row_trades["trad_trade_date"]);
								$show_trad_symbol = $row_trades["trad_symbol"];
								$show_trad_buy_sell = $row_trades["trad_buy_sell"];
								$show_trad_quantity = number_format($row_trades["trad_quantity"],0,'.',",");
								$show_trade_price = number_format($row_trades["trade_price"],2,'.',",");
								$show_trad_commission = number_format($row_trades["trad_commission"],2,'.',",");
								$show_trad_cents_per_share = number_format($row_trades["trad_cents_per_share"],3,'.',",");	
								$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];

								if (in_array($row_trades["trad_symbol"],$arr_cu)) {
								$int_in_cu = 1;
								} else {
								$int_in_cu = 0;
								}
							
								echo 'dt ['.$count_row_trades.'] = "'.$row_trades["trad_trade_date"].'^'.
																													$show_trad_advisor_name.'^'.
																													$row_trades["trad_rr"].'^'.
																													$row_trades["trad_symbol"].'^'.
																													offset_buy_sell($row_trades["trad_buy_sell"]).'^'.
																													$row_trades["trad_quantity"].'^'.
																													$row_trades["trade_price"].'^'.
																													$row_trades["trad_commission"].'^'.
																													$row_trades["trad_cents_per_share"].'^'.
																													$int_in_cu.'"'.";\n";
							
								$count_row_trades = $count_row_trades + 1;
							}
							?>
									var datefromString = '<?=date('m-d-Y',time() - (60*60*24*90))?>';
									var datetoString = '<?=date('m-d-Y')?>';

									for (i=0;i<dt.length;i++)
									{
									var rowtrades_array = new Array()
									var rowclass
									var research_link
									if (i%2 == 0) {
										rowclass = " class=\"trdark\"";
									} else {
										rowclass = " class=\"trlight\"";
									}
									
									rowtrades_array=dt[i].split("^");
									
									if (rowtrades_array[9]==1) {
									research_link = "<a href='http://192.168.20.63/rv/sr/?in_ticker="+
														rowtrades_array[3]+
														"&datefrom=" + datefromString + "&dateto=" + datetoString +
														"'" +
														" target='_blank'><img src='images/lf_v1/research.png' border='0' alt='Recent Research on "+
														rowtrades_array[3]+
														"'></a>";
									} else {
									research_link = "";
									}

										document.write(
														"<tr" + rowclass + ">"+"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[0]+"</td>"+
														"<td><div align='left'>&nbsp; &nbsp; &nbsp; "+rowtrades_array[1]+"</div></td>"+
														"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[2]+"</td>"+
														"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[3]+"</td>"+
														"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[4]+"</td>"+
														"<td align='right'>"+rowtrades_array[5]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>"+rowtrades_array[6]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>"+rowtrades_array[7]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>"+rowtrades_array[8]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>&nbsp;" + research_link + "&nbsp;&nbsp;&nbsp;</td></tr>");
									}
									</script>
						</tbody>
						<tfoot>
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
							</tfoot>
							</table>
							</td>
						</tr>
					</table>
				<?				
				}
				?>