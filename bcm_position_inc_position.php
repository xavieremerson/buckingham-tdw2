<?						
			
			$query_pos = "select
											pos_cusip, 
											min(pos_symbol) as pos_symbol, 
											long_short, 
											transaction_number, 
											sum(quantity) as quantity, 
											avg(price_base) as price_base,
											sum(market_value_net_issue) as market_value_net_issue, 
											min(security_description) as security_description, 
											reporting_date from pos_bcm_positions
											where reporting_date between '".$datefrom."' AND '".$dateto."' ". 
											" group by reporting_date, pos_cusip, long_short order by pos_symbol";
											
			//xdebug("query_pos",$query_pos);
			
		  $passtoexcel = md5(rand(100,999)).'^'.$datefrom.'^'.$dateto;
									
			echo '
					<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
						<tr>
								<td align="left"><a class="links_temp" href="bcm_position_export_excel.php?xl='.$passtoexcel.'" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a>&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong>'.$string_heading.'</strong>&nbsp;&nbsp;</font></td>
								<td>&nbsp;</td>
							</tr>
					</table>';
?>

		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
			<tr>
				<td>		

		<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
		<table class="sortable" preserve_style="cell" width="100%" border="0"  cellspacing="1" cellpadding="1">
		  <thead class="datadisplay">
				<tr bgcolor="#cccccc">
					<td ts_type="date" width="120">Reporting Date</td>
				  <td width="200">Symbol</td>
					<td width="350">Security Desc.</td>
					<td width="56">Long/Short</td>
					<td ts_type="money" width="120">Quantity</td>
					<td ts_type="money" width="120">Price</td>
					<td ts_type="money" width="120">Market Value</td>
					<td align="right">&nbsp;</td>
				</tr>
  		</thead>
  		<tbody id="offTblBdy" class="datadisplay">

			<script type="text/javascript">
			var dt = new Array()

			<? 
			//This section populates the javascript array
			//Performance hurdles are taken care of
			$result_pos = mysql_query($query_pos) or die(tdw_mysql_error($query_pos));
			$count_row_pos = 0;
			while($row = mysql_fetch_array($result_pos))
			{
				
				if ($row["long_short"] == 'S') {
					$str_ls = '&nbsp;&nbsp;S';
				} else {
					$str_ls = $row["long_short"];
				}
				
				echo 'dt ['.$count_row_pos.'] = "'.format_date_ymd_to_mdy($row["reporting_date"]).'^'.
							$row["pos_symbol"].'^'.
							$row["security_description"].'^'.
							$str_ls.'^'.
							number_format($row["quantity"],0,"",",").'^'.
							number_format($row["price_base"],2,".",",").'^'.
							number_format($row["market_value_net_issue"],2,".",",").'"'.";\n";

				$count_row_pos = $count_row_pos + 1;
			}
			
?>		

				for (i=0;i<dt.length;i++)
					{
					var rowpos_array = new Array()
					var rowclass
					if (i%2 == 0) {
						rowclass = "trdark";
					} else {
						rowclass = "trlight";
					}
						
					  rowpos_array=dt[i].split("^");
										
						document.write(
										"<tr class='" + rowclass + "'>"+"<td>&nbsp;&nbsp;&nbsp;"+rowpos_array[0]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowpos_array[1]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowpos_array[2]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowpos_array[3]+"</td>"+
										"<td align='right'>"+rowpos_array[4]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowpos_array[5]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowpos_array[6]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>&nbsp;</td></tr>");
					}
					</script>
<?		
			echo '
				</tbody>
				</table>
				</td></tr></table>';

?>
