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
			} else {
				$qry_string_rep = "";
			}
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
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .$qry_string_rep .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
			$passtoexcel = $query_trades;
?>


<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
	<tr>
		<td valign="top">				
		<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
			<tr>
					<td align="left"><a class="links_temp" href="rep_all_rep_ca_exp_trade_excel.php?qry_string=<?=$passtoexcel?>" target="_blank">GET TO EXCEL</a>&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong><?=$string_heading?></strong>&nbsp;&nbsp;</font></td>
					<td>
					
					</td>
				</tr>
		</table>
		<?
		if ($show_symbol != "Show All") {
		?>
		<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
			<tr>
					<td align="right">
					<a class="links11" href="#"><?=$show_symbol?> (<?=get_company_name($show_symbol)?>) Rating: STRONG BUY (02/20/2004) Target: $60</a> | 
					<a class="links11" href="http://192.168.20.65/icil/owa/list_results?in_doc_type=ALL&author=NONE&in_industry=NONE&tickers=<?=$show_symbol?>&dated=<?=format_date_ymd_to_mdy(business_day_backward(strtotime(previous_business_day()),60))?>&dated2=<?=format_date_ymd_to_mdy(previous_business_day())?>" target="_blank">Recent Research on <?=$show_symbol?></a>
					</td>
				</tr>
		</table>
		<?
		}
		?>
		<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
		  <thead class="datadisplay"> <!--  class="datadisplay" -->
				<tr bgcolor="#333333" class="tblhead_a">
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 0, false);" title="Trade Date">Trade Date</a></th>
				  <th width="276"><a href="" onclick="this.blur(); return sortTable('offTblBdy', 1, false);" title="ADVISOR / CLIENT">ADVISOR / CLIENT</a></th>
					<th width="56"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 2, false);" title="RR #">RR #</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 3, false);" title="Symbol">Symbol</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 4, false);" title="Buy/Sell">B/S</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 5, false);" title="Shares">Shares</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 6, false);" title="Price">Price</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 7, false);" title="Commission">Commission</a></th>
					<th width="100"> <a href="" onclick="this.blur(); return sortTable('offTblBdy', 8, false);" title="Comm./Shr. ($)">Comm./Shr. ($)</a></th>
					<th><a href="#" title="RESEARCH">&nbsp;&nbsp;&nbsp;RESEARCH</a></th>
				</tr>
  		</thead>
  		<tbody id="offTblBdy" class="datadisplay">
			<? 
 			//xdebug("query_trades",$query_trades);
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			$count_row_trades = 1;
			$running_trad_commission_total = 0;
			while($row_trades = mysql_fetch_array($result_trades))
			{
				if ($count_row_trades % 2) { 
						$class_row = ' class="alternateRow"';
				} else { 
						$class_row = ''; 
				}
				//number_format($row_trades["trad_quantity"],2,'.',",");
				
				if ($row_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
				
				$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];

				echo '<tr'.$class_row.'>
					<td>&nbsp;&nbsp;&nbsp;'.$row_trades["trad_trade_date"].'</td>
					<td><div align="left">&nbsp; &nbsp; &nbsp; '.$show_trad_advisor_name.'</div></td>
					<td>&nbsp;&nbsp;&nbsp;'.$row_trades["trad_rr"].'</td>
					<td>&nbsp;&nbsp;&nbsp;'.$row_trades["trad_symbol"].'</td>
					<td>&nbsp;&nbsp;&nbsp;'.$row_trades["trad_buy_sell"].'</td>
					<td align="right">'.$row_trades["trad_quantity"].'&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$row_trades["trade_price"].'&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$row_trades["trad_commission"].'&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$row_trades["trad_cents_per_share"].'&nbsp;&nbsp;&nbsp;</td>
					<td align="right">&nbsp;<a href="http://192.168.20.65/icil/owa/list_results?in_doc_type=ALL&author=NONE&in_industry=NONE&tickers='.$show_trad_symbol.'&dated='.format_date_ymd_to_mdy(business_day_backward(strtotime(previous_business_day()),60)).'&dated2='.format_date_ymd_to_mdy(previous_business_day()).'" target="_blank"><img src="images/lf_v1/research.png" border="0" alt="Recent Research on '.$show_trad_symbol.'"></a>&nbsp;&nbsp;&nbsp;</td>
				</tr>';

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
