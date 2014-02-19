<?php
//BRG
include('../inc_header.php');
  
include("../includes/class.pagination.php");

//Date in YYYY-MM-DD format
//$trade_date_to_process = previous_business_day();
$trade_date_to_process = '2006-01-18';

?>
	<table width="100%" cellpadding="4" cellspacing="4">
		<tr>
			<td>
			</td>
		</tr>
		<tr>
			<td>
	<? table_start_percent(100, "Sales Rep. Commissions"); ?>
			
			<?php
			include('../includes/dbconnect.php');
					  
			$date = date("Ymd");
			$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
				
			$query_trades = "SELECT trad_auto_id, 
				trad_full_account_number, 
				trad_trade_date, 
				trad_settle_date, 
				abs(round(trad_quantity,0)) as 'trad_quantity',
				round(trad_price,2) as 'trad_price',
				trad_buy_sell,
				UPPER(trad_symbol) as 'trad_symbol',
				trad_sec_desc_1,
				trad_execution_time
				FROM nfs_trades where trad_registered_rep = "."'".$rr_num."'" . $str_trad_trade_date .
				" ORDER BY trad_symbol, trad_execution_time";
				
				//echo $query_trades;
				
				$tradespage = new Pagination;

				$tradespage->sql = $query_trades; // the (basic) sql statement (use the SQL whatever you like)
				$result = $tradespage->get_page_result(); // result set
				$num_rows = $tradespage->get_page_num_rows(); // number of records in result set 
				$nav_links = $tradespage->navigation(" | ", "currentStyle"); // the navigation links (define a CSS class selector for the current link)
				$nav_info = $tradespage->page_info("to"); // information about the number of records on page ("to" is the text between the number)
				$simple_nav_links = $tradespage->back_forward_link(true); // the navigation with only the back and forward links, use true to use images
				$total_recs = $tradespage->get_total_rows(); // the total number of records

				//" ORDER BY trad_symbol, trad_execution_time LIMIT ".$start. "," . $per_page;
				//  ORDER BY trad_symbol, trad_execution_time LIMIT ".'0'. "," . '40';
				//" ORDER BY trad_symbol, trad_execution_time LIMIT ".$start. "," . $end;
				
				$result = mysql_query($query_trades) or die (mysql_error());
			?>
			
			<tr>
				<td valign="top">
				<?php echo "<a class=\"pagination\">&nbsp;&nbsp;Records ".$nav_info." of ".$total_recs."</a>"; ?>
					<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
								<table class="sortable" id="src_table"  width="100%"  border="0" cellspacing="1" cellpadding="0">
									<!-- class="tableheading12" -->
									<tr bgcolor="#CCCCCC"> 
										<td width="100">&nbsp;&nbsp;&nbsp;&nbsp;Account #</td>  
										<td width="60">Symbol</td> 
										<td width="200">Description</td>
										<td width="60">B/S</td>
										<td width="60">Quantity</td>
										<td width="100">Price</td>
										<td width="100">Trade Date</td>
										<td >Time</td>
									</tr>
			
									<?			    
									//for START
									for ($i = 0; $i < $num_rows; $i++) {
										$trad_full_account_number = mysql_result($result, $i, "trad_full_account_number");
										$trad_symbol = mysql_result($result, $i, "trad_symbol");
										$trad_sec_desc_1 = mysql_result($result, $i, "trad_sec_desc_1");
										$trad_buy_sell = mysql_result($result, $i, "trad_buy_sell");
										$trad_quantity = mysql_result($result, $i, "trad_quantity");
										$trad_price = mysql_result($result, $i, "trad_price");
										$trad_trade_date = mysql_result($result, $i, "trad_trade_date");
										$trad_execution_time = mysql_result($result, $i, "trad_execution_time");
									?>
										<tr class="tablerow">
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_full_account_number?></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$trad_symbol?>', 500, 200, false);"><?=$trad_symbol?></a></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_sec_desc_1?></td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_buy_sell?>&nbsp;&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_quantity?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_price?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($trad_trade_date)?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_execution_time?>&nbsp;&nbsp;</td>
										</tr>
									<?
									}//for END
									?>
									</table>
									<!-- END TABLE 4 -->
									<script language="JavaScript">
									<!--
										tigra_tables('src_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
									// -->
									</script>
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
				</td>
			</tr>
		</table>
		
<table><tr><td valign="bottom">
<?
		echo $nav_links;
?>
</td></tr></table>
	<!-- END TABLE 2 -->
	<!--Table with thin cell border ends-->
	<? table_end_percent(); ?>
		</td></tr></table>
<?php
  include('../inc_footer.php');
?>
