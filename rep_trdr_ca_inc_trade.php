			<?						


			//Show research link only for the symbols in the coverage universe
			$arr_cu = get_coverage_universe();

			//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
			//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
			//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
			if ($show_symbol != "Show All") {
				$qry_string_symbol = " AND a.trad_symbol = '".$show_symbol."' ";
			} else {
				$qry_string_symbol = "";
			}
			if ($show_client != "Show All") {
				$qry_string_client = " AND a.trad_advisor_code = '".$show_client."' ";
			} else {
				$qry_string_client = "";
			}
			$query_trades = "SELECT 
													a.trad_advisor_code,
													max(a.trad_rr) as trad_rr,
													a.trad_symbol,
													a.trad_buy_sell,
													DATE_FORMAT(a.trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(a.trad_advisor_name) as trad_advisor_name,
													sum(a.trad_quantity) as trd_quantity,													
													FORMAT(sum(a.trad_quantity),0) as trad_quantity,
													FORMAT(max(a.trade_price),4) as trade_price,
													FORMAT(sum(a.trad_commission),2) as trad_commission,
													sum(a.trad_commission) as for_sum_trad_commission,
													FORMAT(avg(a.trad_cents_per_share),3) as trad_cents_per_share 
												FROM mry_comm_rr_trades a,int_clnt_clients b, Users c 
												WHERE a.trad_advisor_code = b.clnt_code
												AND b.clnt_trader = c.Initials
												AND c.ID = '".$user_id."'  
												AND a.trad_is_cancelled = 0 
												AND a.trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .
												" GROUP BY a.trad_advisor_code, a.trad_symbol, a.trad_buy_sell, a.trad_trade_date 
												ORDER BY a.trad_advisor_name, a.trad_symbol, a.trad_buy_sell, a.trad_trade_date";
			
			//xdebug("query_trades",$query_trades);

			//xdebug("query_shared_rep_trades",$query_shared_rep_trades);
			$passtoexcel = md5(rand(100,999)).'^'.$rep_to_process.'^'.$user_id.'^'.$datefrom.'^'.$dateto.'^'.$qry_string_symbol.'^'.$qry_string_client;
			
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			if (empty_qry($result_trades)==0) {
			?>
				<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
					<tr>
						<td valign="top">				
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
								<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong><?=$string_heading?></strong></font></td>
							</tr>
						</table>
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
								<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#666666"><strong>No trades found.</strong></font></td>
							</tr>
						</table>			
						</td>
					</tr>
				</table>
			<?			
			} else {
			?>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top">				
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
									<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong><?=$string_heading?></strong></font></td>
								</tr>
						</table>
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
									<td align="left"><a class="links_temp" href="rep_trdr_ca_exp_trade_excel.php?xl=<?=$passtoexcel?>" target="_blank">GET TO EXCEL</a></td>
								</tr>
						</table>
						
				<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
					<tr>
						<td valign="top">				
						<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
							<table class="sortable" preserve_style="cell" width="100%" border="0" cellspacing="1" cellpadding="1">
							<thead class="datadisplay">
								<tr bgcolor="#CCCCCC">
									<td ts_type="date" width="80"> Trd Date</td>
									<td width="276">CLIENT</td>
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
								
								/*if ($row_trades["trad_advisor_name"] == '') {
									$show_trad_advisor_name = $row_trades["trad_advisor_code"];
								} else {
									$show_trad_advisor_name = $row_trades["trad_advisor_name"];
								}*/
								
				if ($row_trades["trad_advisor_name"] == '') {
				    $get_from_db = db_single_val("select max(clnt_name) as single_val from int_clnt_clients where clnt_code = '".$row_trades["trad_advisor_code"]."'");
					//xdebug("get_from_db",$get_from_db);
					if (trim($get_from_db) != "") {
						$show_trad_advisor_name = $get_from_db;
					} else {
						$show_trad_advisor_name = $row_trades["trad_advisor_code"];
					}
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
								$show_trad_rr =  $row_trades["trad_rr"];
								$show_trad_trade_date = format_date_ymd_to_mdy($row_trades["trad_trade_date"]);
								$show_trad_symbol = $row_trades["trad_symbol"];
								$show_trad_buy_sell = $row_trades["trad_buy_sell"];
								$show_trad_quantity = number_format($row_trades["trad_quantity"],0,'.',",");
								$show_trade_price = number_format($row_trades["trade_price"],2,'.',",");
								$show_trad_commission = number_format($row_trades["trad_commission"],2,'.',",");
								//$show_trad_cents_per_share = number_format($row_trades["trad_cents_per_share"],3,'.',",");
								
								$show_trad_cents_per_share = number_format(($row_trades["for_sum_trad_commission"]/$row_trades["trd_quantity"]),3,".","");								
									
								$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];
							
								if (in_array($row_trades["trad_symbol"],$arr_cu)) {
								$int_in_cu = 1;
								} else {
								$int_in_cu = 0;
								}
							
								echo 'dt ['.$count_row_trades.'] = "'.$row_trades["trad_trade_date"].'^'.
																													$show_trad_advisor_name.'^'.
																													$show_trad_rr.'^'.
																													$row_trades["trad_symbol"].'^'.
																													offset_buy_sell($row_trades["trad_buy_sell"]).'^'.
																													$row_trades["trad_quantity"].'^'.
																													$row_trades["trade_price"].'^'.
																													$row_trades["trad_commission"].'^'.
																													$show_trad_cents_per_share.'^'.
																													$int_in_cu.'"'.";\n";
																													//$row_trades["trad_cents_per_share"]
							
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
				</td>
			</tr>
			</table>
			<?
			}
			?>

