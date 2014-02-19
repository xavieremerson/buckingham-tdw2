<?
include('top.php');
?>		

<?
echo $table_start;

		$query_count = "SELECT acti_trade_id FROM acti_action_item_flag WHERE acti_datetime_closed != '' and acti_user_id = ".$user_id;
		$result_count = mysql_query($query_count) or die (mysql_error());
		$row_count = mysql_numrows($result_count);
		
		//START IF 2
		if($row_count > 0)
		{
			$query_statement = "SELECT acti_trade_id, acti_comments, acti_closing_comments FROM acti_action_item_flag WHERE acti_datetime_closed != '' and acti_user_id = ".$user_id;		
			$result = mysql_query($query_statement) or die (mysql_error());
		?>
		<!--Table with thin cell border-->
		<!-- START TABLE 5 -->
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
				<!-- class="tablewithdata" -->
				<!-- START TABLE 6 -->
				<table class="sortable" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="0">
				<!-- class="tableheading12" -->
					<tr bgcolor="#CCCCCC"> 
						<td align="center">#</td>  
						<td>&nbsp;&nbsp;&nbsp;&nbsp;Action Items</td> 
					</tr>
			<?
			$count = 1;
			 
			//START WHILE 2
			while ( $row = mysql_fetch_array($result) ) 
			{
				$query_trade_data = "SELECT trdm_auto_id, trdm_account_number, trdm_buy_sell, trdm_quantity, trdm_symbol, trdm_price, trdm_trade_date, trdm_trade_time 
									 FROM Trades_m WHERE trdm_auto_id = ".$row["acti_trade_id"];
									 
				$result_trade_data =  mysql_query($query_trade_data) or die (mysql_error());
				$row_trade_data = mysql_fetch_array($result_trade_data);
	
			?>
				<tr class="tablerow">
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$count?></a></td>
					<td>
						<!-- START TABLE 7 -->
						<table>
							<tr>
								<td>
									&nbsp;&nbsp;&nbsp;&nbsp;<a class="emptrades"><?=$row["acti_closing_comments"]?></a>
								</td>
							</tr>
							<tr>
								<td>
									<!-- START TABLE 8 -->
									<table>
										<tr>
											<td align="center"><a class="emptrades">ACCT</a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades">B/S</a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades">QTY</a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades">SYMB</a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades">PRC</a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades">TRD DATE</a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades">TRD TIME</a>&nbsp;&nbsp;</td>
										</tr>
										<tr>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_account_number"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_buy_sell"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_quantity"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_symbol"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_price"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_trade_date"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_trade_time"]?></a>&nbsp;&nbsp;</td>
										</tr>
									</table>
									<!-- END TABLE 8 -->
								<td>
							</tr>
						</table>
						<!-- END TABLE 7 -->						
					</td>	
				</tr>
			<?
			$count++;
			} //END WHILE 2
			?>
			</table>
			<!-- END TABLE 6 -->
			
			 <script language="JavaScript">
			<!--
				//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
				tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
			// -->
			</script>
			
			</td>
			</tr>
			</table>
			<!-- END TABLE 5 -->
	<?
		}//END IF 2
		else
		{
			echo "<Br><p class='Contact'><B>&nbsp;&nbsp;&nbsp;No Closed Action Items found!</B></p>";
		}	

echo $table_end;
?>

<?
include('bottom.php');
?>