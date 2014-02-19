<?						
			
			$query_pos = "select
											pos_cusip, 
											min(pos_symbol) as symbol, 
											long_short, 
											transaction_number, acquired_date , quantity, price_base,
											market_value_net_issue, security_description, 
											reporting_date from pos_bcm_positions
											where reporting_date between '".$datefrom."' AND '".$dateto."' ". 
											" group by reporting_date, order by pos_symbol";

			//xdebug("query_pos",$query_pos);
			
		  $passtoexcel = md5(rand(100,999)).'^'.$rep_id.'^'.$datefrom.'^'.$dateto.'^'.$qry_string_symbol.'^'.$qry_string_client.'^'.$qry_string_rep;
									
			echo '
					<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
						<tr>
								<td align="left"><a class="links_temp" href="rep_all_rep_ca_exp_trade_excel.php?xl='.$passtoexcel.'" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a>&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong>'.$string_heading.'</strong>&nbsp;&nbsp;</font></td>
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
					<td ts_type="date" width="120">Acquired Date</td>
				  <td width="120">Transaction #</td>
				  <td width="100">Symbol</td>
					<td width="350">Security Desc.</td>
					<td width="56">Long/Short</td>
					<td width="80">Quantity</td>
					<td width="80">Price</td>
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
				echo 'dt ['.$count_row_pos.'] = "'.$row["reporting_date"].'^'.
							$row["acquired_date"].'^'.
							$row["transaction_number"].'^'.
							$row["pos_symbol"].'^'.
							$row["security_description"].'^'.
							$row["long_short"].'^'.
							$row["quantity"].'^'.
							$row["price_base"].'^'.
							$row["market_value_net_issue"].'"'.";\n";

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
										"<td>&nbsp;&nbsp;&nbsp;"+rowpos_array[4]+"</td>"+
										"<td align='right'>"+rowpos_array[5]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowpos_array[6]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowpos_array[7]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowpos_array[8]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>&nbsp;</td></tr>");
					}
					</script>
<?		
			echo '
				</tbody>
				</table>
				</td></tr></table>';

?>
