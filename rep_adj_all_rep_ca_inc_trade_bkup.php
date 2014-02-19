<?						
			//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
			//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
			//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
			if ($show_symbol != "Show All") {
				$qry_string_symbol = " AND trad_symbol = '".$show_symbol."' ";
			} else {
				$qry_string_symbol = "";
			}
			if ($show_client != "Show All") {
				$qry_string_client = " AND trad_advisor_code = '".$show_client."' ";
			} else {
				$qry_string_client = "";
			}
			if ($show_rep != "Show All") {
				$qry_string_rep = " AND trad_rr = '".$show_rep."' ";
				$rep_id = $rep_id;
			} else {
				$qry_string_rep = "";
			}
			
			//There is a know issue that since some clients have multiple RRs, e.g. GART the data shown gets max(rr)
			//which means the totals will be accurate but the rr agains the client will be inaccurate.
			
			//fixing the query (excel) to account for the incorrect subtotals by rr (carol)
			
			$query_trades_group = "SELECT 
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
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .$qry_string_rep .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";

			$query_trades_singleton = "SELECT 
													trad_advisor_code,
													trad_symbol,
													trad_buy_sell,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(trad_advisor_name) as trad_advisor_name,
													trad_quantity,
													trade_price,
													trad_commission,
													trad_commission) as for_sum_trad_commission,
													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
													max(trad_rr) as trad_rr 
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .$qry_string_rep .
												"ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
			
			//xdebug("query_trades",$query_trades);
			//$passtoexcel = $query_trades;
			
			$query_shared_rep_trades = "SELECT 
													a.trad_advisor_code,
													a.trad_symbol,
													a.trad_buy_sell,
													DATE_FORMAT(a.trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(a.trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(a.trad_quantity),0) as trad_quantity,
													FORMAT(max(a.trade_price),2) as trade_price,
													FORMAT(sum(a.trad_commission),2) as trad_commission,
													sum(a.trad_commission) as for_sum_trad_commission,
													FORMAT(avg(a.trad_cents_per_share),3) as trad_cents_per_share,
													max(a.trad_rr) as trad_rr
												FROM mry_comm_rr_trades a, sls_sales_reps b
												WHERE a.trad_rr = b.srep_rrnum 
												AND b.srep_user_id = '".$rep_id."'
												AND trad_is_cancelled = 0 
												AND b.srep_isactive = 1 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";	
			
echo '
<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
	<tr>
		<td valign="top">				
		';
?>

		<?
		echo '		
		<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
		  <thead class="datadisplay"> <!--  class="datadisplay" -->
				<tr bgcolor="#333333" class="tblhead_a">
					<th width="80"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 0, false);" title="Trade Date">Trade Date</a></th>
				  <th width="276"><a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 1, false);" title="ADVISOR / CLIENT">ADVISOR / CLIENT</a></th>
					<th width="56"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 2, false);" title="RR #">RR #</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 3, false);" title="Symbol">Symbol</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 4, false);" title="Buy/Sell">B/S</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 5, false);" title="Shares">Shares</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 6, false);" title="Price">Price</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 7, false);" title="Commission">Commission</a></th>
					<th width="100"> <a href="" onclick="this.blur(); return sortTable(\'offTblBdy\', 8, false);" title="Comm./Shr. ($)">Comm./Shr. ($)</a></th>
					<th><a href="#" title="RESEARCH">&nbsp;&nbsp;Apply Adjustment</a></th>
				</tr>
  		</thead>
  		<tbody id="offTblBdy" class="datadisplay">';
			
			?>
			
			<script type="text/javascript">
				var displaytrades = new Array()

			<? 
			//This section populates the javascript array
			//Performance hurdles are taken care of
			$result_trades = mysql_query($query_trades_group) or die(tdw_mysql_error($query_trades_group));
			$result_shared_rep_trades = mysql_query($query_shared_rep_trades) or die(tdw_mysql_error($query_shared_rep_trades));
			
			$count_row_trades = 0;
			$running_trad_commission_total = 0;
			while($row_trades = mysql_fetch_array($result_trades))
			{
				
				if ($row_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
				
				$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];

				echo 'displaytrades ['.$count_row_trades.'] = "'.$row_trades["trad_trade_date"].'^'.
																												$show_trad_advisor_name.'^'.
																												$row_trades["trad_rr"].'^'.
																												$row_trades["trad_symbol"].'^'.
																												offset_buy_sell($row_trades["trad_buy_sell"]).'^'.
																												$row_trades["trad_quantity"].'^'.
																												$row_trades["trade_price"].'^'.
																												$row_trades["trad_commission"].'^'.
																												$row_trades["trad_cents_per_share"].'"'.";\n";

				$count_row_trades = $count_row_trades + 1;
			}
			
										while($row_shared_rep_trades = mysql_fetch_array($result_shared_rep_trades))
							{
								
								if ($row_shared_rep_trades["trad_advisor_name"] == '') {
									$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_code"];
								} else {
									$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_name"];
								}
								
								$show_trad_rr = $row_shared_rep_trades["trad_rr"];;
								$show_trad_trade_date = format_date_ymd_to_mdy($row_shared_rep_trades["trad_trade_date"]);
								$show_trad_symbol = $row_shared_rep_trades["trad_symbol"];
								$show_trad_buy_sell = $row_shared_rep_trades["trad_buy_sell"];
								$show_trad_quantity = number_format($row_shared_rep_trades["trad_quantity"],0,'.',",");
								$show_trade_price = number_format($row_shared_rep_trades["trade_price"],2,'.',",");
								$show_trad_commission = number_format($row_shared_rep_trades["trad_commission"],2,'.',",");
								$show_trad_cents_per_share = number_format($row_shared_rep_trades["trad_cents_per_share"],3,'.',",");	
								$running_trad_commission_total = $running_trad_commission_total + $row_shared_rep_trades["for_sum_trad_commission"];
							
								echo 'displaytrades ['.$count_row_trades.'] = "'.$row_shared_rep_trades["trad_trade_date"].'^'.
																													$show_trad_advisor_name.'^'.
																													$row_shared_rep_trades["trad_rr"].'^'.
																													$row_shared_rep_trades["trad_symbol"].'^'.
																													offset_buy_sell($row_shared_rep_trades["trad_buy_sell"]).'^'.
																													$row_shared_rep_trades["trad_quantity"].'^'.
																													$row_shared_rep_trades["trade_price"].'^'.
																													$row_shared_rep_trades["trad_commission"].'^'.
																													$row_shared_rep_trades["trad_cents_per_share"].'"'.";\n";
							
								$count_row_trades = $count_row_trades + 1;
							}

?>		

				for (i=0;i<displaytrades.length;i++)
					{
					var rowtrades_array = new Array()
					var rowclass
					if (i%2 == 0) {
						rowclass = " class=\"alternateRow\"";
					} else {
						rowclass = "";
					}
					
					rowtrades_array=displaytrades[i].split("^");
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
									"<td align='right'>&nbsp;&nbsp;&nbsp;</td></tr>");
					}
					</script>
<?		
			echo '
				</tbody>
					<tr bgcolor="#CCCCCC" class="display_totals">
						<td><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.number_format($running_trad_commission_total,2,'.',',').'&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>';

?>
