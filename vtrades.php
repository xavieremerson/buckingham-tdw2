<?php
//BRG
include('inc_header.php');
  
include("includes/class.pagination.php");

//Date in YYYY-MM-DD format
$trade_date_to_process = previous_business_day();
//$trade_date_to_process = '2006-01-18';

?>
	<table width="100%" cellpadding="4" cellspacing="4"><tr><td>
	<? table_start_percent(100, "Filter"); ?>

		<!-- START TABLE 1 -->
		<table class="tablewithdata" width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<form action="<?=$PHP_SELF?>?action=filter" id="filtertrade" method="get"> 
				<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<select class="Text" name="trad_trade_date" size="1" >
				<option value="">&nbsp;TRADE DATE</option>
				<option value="">=======</option>
				<?
						
				$i = 1;
				while ($i < 90) 
				{
					$previoustime = time() - (60*60*24*$i);
					$previousday = date("Y-m-d", $previoustime);

					if (date("l", $previoustime) == "Sunday") 
					{
						$previoustime = time() - (60*60*24*($i+2));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+2 + 1;	
						if ( check_holiday($previousday) == 1) 
						{						
							$previoustime = time() - (60*60*24*($i));
							$previousday = date("Y-m-d", $previoustime);
							$i = $i+1;	
						}
					} 
					elseif (date("l", $previoustime) == "Monday" and check_holiday($previousday) == 1) 
					{
						$previoustime = time() - (60*60*24*($i+3));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+3 + 1;	
					} 
					else 
					{
						$previousday = "ERROR!";
						$i = $i+1;						
					}
				?>
				<option value="<?=date("Y-m-d", time() - (60*60*24*($i-1)))?>"><?=date("m-d-Y", time() - (60*60*24*($i-1)))?></option>
				<?
				}						
				?>
				</select>

				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<select class="Text" name="trad_symbol" size="1" >
				<option value="">&nbsp;SYMBOL&nbsp;</option>
				<option value="">======</option>
				<?
				$query_statement = "SELECT DISTINCT (
									trad_symbol
									)
									FROM `nfs_trades`  
									 WHERE trad_trade_date > '". date("Y-m-d", time() - (60*60*24*30)) ."' 
									 AND trad_symbol != ''
									 AND LENGTH(trad_symbol) < 8
									 ORDER BY trad_symbol";	
				
				echo "<BR><BR> $query_statement1  " . $query_statement;

				
				$result = mysql_query($query_statement) or die (mysql_error());
				
				while ( $row = mysql_fetch_array($result) )
				{
					?> 
					<option value="<?=$row["trad_symbol"]?>"><?=$row["trad_symbol"]?></option>
					<?
				}						
					?>
				
				</select>

				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<select class="Text" name="trad_full_account_number" size="1" >
				<option value="">&nbsp;ACCT. #&nbsp;</option>
				<option value="">======</option>
				<?
				$query_statement = "SELECT distinct (trad_full_account_number) as trad_full_account_number
														 FROM `nfs_trades` 
														 where trad_trade_date > '". date("Y-m-d", time() - (60*60*24*30)) ."' 
														 and trad_symbol != ''
														 ORDER BY trad_full_account_number";	

				//echo "<BR><BR> $query_statement2  " . $query_statement;

				$result = mysql_query($query_statement) or die (mysql_error());
				while ( $row = mysql_fetch_array($result) ) 
				{
					?>
					<option value="<?=$row["trad_full_account_number"]?>"><?=$row["trad_full_account_number"]?></option>
					<?
				}						
				?>
				
				</select>

				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<INPUT type=image name="submit1" src="images/submit_buttons/apply_filter.jpg" border="0">
			  </td>
				</form>
			</tr>
		</table>
		<!-- END TABLE 1 -->
	<? table_end_percent(); ?>
	</td></tr>
	<tr><td>
	<? table_start_percent(100, "Trades"); ?>
			
			<?php
			include('includes/dbconnect.php');
					  
			$date = date("Ymd");
			$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
			?>
				
			<?
			if ($trad_trade_date != '') 
			{ 
				$str_trad_trade_date = " where trad_trade_date = '". $trad_trade_date ."'";
			} 
			else 
			{
				$str_trad_trade_date = " where trad_trade_date = '". $trade_date_to_process ."'";
			}			  
	
			if ($trad_symbol != '') 
			{ 
				$str_trad_symbol = " and trad_symbol = '".$trad_symbol."'";
			} 
			else 
			{
				$str_trad_symbol = " and trad_symbol != '' and LENGTH(trad_symbol) < 8";
			}			  
	
			if ($trad_full_account_number != '') 
			{ 
				$str_trad_full_account_number = " and trad_full_account_number = '".$trad_full_account_number."'";
			} 
			else 
			{
				$str_trad_full_account_number = " and trad_full_account_number not like '0000%' ";
			}
			
			
			$query_trades = "SELECT trad_auto_id, 
				trad_full_account_number,
				trad_registered_rep, 
				date_format(trad_trade_date,'%m/%d/%Y') as trad_trade_date, 
				trad_settle_date, 
				format(trad_quantity,0) as trad_quantity,
				format(trad_price,2) as trad_price,
				format(trad_trade_commission,2) as trad_trade_commission,
				trad_buy_sell,
				UPPER(trad_symbol) as 'trad_symbol',
				trad_sec_desc_1,
				trad_execution_time
				FROM nfs_trades " . $str_trad_trade_date . 
				$str_trad_symbol .
				$str_trad_full_account_number .
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
				//ORDER BY trad_symbol, trad_execution_time LIMIT ".'0'. "," . '40';
				//" ORDER BY trad_symbol, trad_execution_time LIMIT ".$start. "," . $end;
				
				$result = mysql_query($query_trades) or die (mysql_error());
			?>
			
			<tr>
				<td valign="top">
				<?php echo "<a class=\"pagination\">&nbsp;&nbsp;Records ".$nav_info." of ".$total_recs."</a>"; ?>
					<!-- START TABLE 3 -->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
								<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="0">
									<!-- class="tableheading12" -->
									<tr bgcolor="#CCCCCC"> 
										<td width="80">Account #</td>  
										<td width="60">RR</td> 										
										<td width="60">Symbol</td> 
										<td width="200">Description</td>
										<td width="40">B/S</td>
										<td ts_type="money" width="60">Quantity</td>
										<td ts_type="money" width="75">Price</td>
										<td ts_type="money" width="75">Comm.</td>
										<td ts_type="euro_date" width="100">Trade Date</td>
										<td width="60">Time</td>
										<td> </td>
									</tr>
			
									<?			    
									//for START
									for ($i = 0; $i < $num_rows; $i++) {
										$trad_full_account_number = mysql_result($result, $i, "trad_full_account_number");
										$trad_symbol = mysql_result($result, $i, "trad_symbol");
										$trad_sec_desc_1 = mysql_result($result, $i, "trad_sec_desc_1");
										$trad_buy_sell = offset_buy_sell(mysql_result($result, $i, "trad_buy_sell"));
										$trad_quantity = mysql_result($result, $i, "trad_quantity");
										$trad_price = mysql_result($result, $i, "trad_price");
										$trad_trade_date = mysql_result($result, $i, "trad_trade_date");
										$trad_execution_time = mysql_result($result, $i, "trad_execution_time");
										$trad_trade_commission = mysql_result($result, $i, "trad_trade_commission");
										$trad_registered_rep = mysql_result($result, $i, "trad_registered_rep");

										if ($i%2) {
													$class_row = "trdark";
										} else { 
												$class_row = "trlight"; 
										} 
									?>
										<tr class="<?=$class_row?>">
											<td>&nbsp; <?=$trad_full_account_number?></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_registered_rep?></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$trad_symbol?>', 500, 200, false);"><?=$trad_symbol?></a></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_sec_desc_1?></td>
											<td>&nbsp;<?=$trad_buy_sell?></td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_quantity?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_price?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_trade_commission?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_trade_date?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$trad_execution_time?>&nbsp;&nbsp;</td>
											<td align="right"> </td>
										</tr>
									<?
									}//for END
									?>
									</table>
									<!-- END TABLE 4 -->
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
  include('inc_footer.php');
?>
