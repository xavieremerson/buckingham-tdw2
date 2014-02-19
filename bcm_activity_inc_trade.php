&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong><?=$string_heading?></strong>&nbsp;&nbsp;</font>
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
			<tr>
				<td>		
		<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
		<table class="sortable" preserve_style="cell" width="100%" border="0"  cellspacing="1" cellpadding="1">
		  <thead class="datadisplay">
				<tr bgcolor="#cccccc">
					<td ts_type="date" width="85">Trd Date</td>
				  <td ts_type="date" width="85">Proc. Dt.</td>
					<td width="56">Broker</td>
					<td width="80">Symbol</td>
					<td width="60">B/S</td>
					<td ts_type="money" width="60">Quantity</td>
					<td ts_type="money" width="60">Price</td>
					<td ts_type="money" width="50">Comm.</td>
					<td ts_type="money" width="100">Net Money</td>
					<td width="80">Trd Time</td>
					<td width="20">PM</td>
					<td width="80">Emp./Client</td>
					<td width="80">EmpAlloc</td>
					<td width="80">Trade ID</td>
					<td width="70">Trd TS</td>
					<td width="70">First Ex.</td>
					<td width="70">Last Ex.</td>
				  <td width="110">CXL</td>
					<td>&nbsp;</td>
				</tr>
  		</thead>
  		<tbody id="offTblBdy" class="datadisplay">
						
			<script type="text/javascript">
			var dt = new Array()

			<? 
			//This section populates the javascript array
			//Performance hurdles are taken care of
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			
			$count_row_trades = 0;
			$running_trad_commission_total = 0;
			$running_quantity_total = 0;
			while($row_trades = mysql_fetch_array($result_trades))
			{
								
				$running_trad_commission_total = $running_trad_commission_total + $row_trades["oth_commission"];
				$running_net_money_total = $running_net_money_total + $row_trades["oth_net_money"]; 


/*auto_id  oth_trade_date  oth_process_date  oth_original_trade_id  oth_broker  oth_buysell  oth_symbol  oth_quantity  oth_price  oth_commission  oth_net_money  oth_trade_time  oth_pm_code  oth_emp_client  oth_emp_alloc  oth_trade_id  oth_trade_ts  oth_first_exec  oth_last_exec  oth_isactive 
*/				
				echo 'dt ['.$count_row_trades.'] = "'.format_date_ymd_to_mdy($row_trades["oth_trade_date"]).'^'.
																							format_date_ymd_to_mdy($row_trades["oth_process_date"]).'^'.
																							$row_trades["oth_broker"].'^'.
																							$row_trades["oth_symbol"].'^'.
																							$row_trades["oth_buysell"].'^'.
																							number_format($row_trades["oth_quantity"],0,"",",").'^'.
																							number_format($row_trades["oth_price"],2,".",",").'^'.
																							number_format($row_trades["oth_commission"],2,".",",").'^'.
																							number_format($row_trades["oth_net_money"],2,".",",").'^'.
																							date('h:i:a',strtotime($row_trades["oth_trade_time"])).'^'.
																							$row_trades["oth_pm_code"].'^'.
																							$row_trades["oth_emp_client"].'^'.
																							$row_trades["oth_emp_alloc"].'^'.
																							$row_trades["oth_trade_id"].'^'.
																							date('h:ia',strtotime($row_trades["oth_trade_ts"])).'^'.
																							date('h:ia',strtotime($row_trades["oth_first_exec"])).'^'.
																							date('h:ia',strtotime($row_trades["oth_last_exec"])).'^'.
																							$row_trades["oth_original_trade_id"].'"'.";\n";

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
						rowclass = "trdark";
					} else {
						rowclass = "trlight";
					}
					
					rowtrades_array=dt[i].split("^");					
					
						document.write(
										"<tr class='" + rowclass + "'>"+
										"<td>&nbsp;"+rowtrades_array[0]+"</td>"+
										"<td>&nbsp;"+rowtrades_array[1]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[2]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[3]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[4]+"</td>"+
										"<td align='right'>"+rowtrades_array[5]+"&nbsp;</td>"+
										"<td align='right'>"+rowtrades_array[6]+"&nbsp;</td>"+
										"<td align='right'>"+rowtrades_array[7]+"&nbsp;</td>"+
										"<td align='right'>"+rowtrades_array[8]+"&nbsp;</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[9]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[10]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[11]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[12]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[13]+"&nbsp;</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[14]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[15]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[16]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[17]+"</td>"+
										"<td align='right'>&nbsp;</td></tr>");
					}
					</script>
<?		
			echo '
				</tbody>
				<tfoot>
					<tr bgcolor="#CCCCCC" class="display_totals">
						<td><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;&nbsp;&nbsp;&nbsp; '.number_format($running_trad_commission_total,2,'.',',').'&nbsp;</td>
						<td align="right">'.number_format($running_net_money_total,2,'.',',').'&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tfoot>
				</table>
				</td></tr></table>';

$str_timedebug .= "<br>Trades: " .sprintf("%01.4f",((getmicrotime()-$arr_timedebug[$str_timecount])/1000))." s.";
$str_timecount++;
$arr_timedebug[$str_timecount]=getmicrotime();

?>
