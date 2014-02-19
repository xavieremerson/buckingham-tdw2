<?php
include('top.php');
include('includes/functions.php');?>
<tr>
<td valign="top">
		<table cellpadding="5"><tr><td>
			<? table_start(1020, "Open Action Items"); ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
		<!-- ADMIN CONTENT BEGIN -->
		
<?
	$query_count = "SELECT acti_id FROM acti_action_item_flag WHERE acti_datetime_closed = '' and acti_user_id = ".$user_id;
		$result_count = mysql_query($query_count) or die (mysql_error());
		$row_count = mysql_numrows($result_count);
		
		//START IF 1
		if($row_count > 0)
		{
			$query_statement = "SELECT acti_id, acti_trade_id, acti_comments FROM acti_action_item_flag WHERE acti_datetime_closed = '' and acti_user_id = ".$user_id;		
			$result = mysql_query($query_statement) or die (mysql_error());
?>
			<!--Table with thin cell border-->
			<!-- START TABLE 1 -->
			<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
				<tr>
					<td>
					<!-- class="tablewithdata" -->
					<!-- START TABLE 2 -->
					<table class="sortable" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="0">
					<!-- class="tableheading12" -->
						<tr bgcolor="#CCCCCC"> 
							<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;#</td>  
							<td>&nbsp;Send Msg.</td>
							<td>&nbsp;Close Item</td> 
							<td>&nbsp;&nbsp;&nbsp;&nbsp;Action Items</td>
						</tr>
			
			<?
			$count = 1;
			
			//START WHILE 1
			while ( $row = mysql_fetch_array($result) ) 
			{		
				$query_trade_data = "SELECT trdm_auto_id, trdm_account_number, trdm_buy_sell, trdm_quantity, trdm_symbol, trdm_price, trdm_trade_date, trdm_trade_time 
									 FROM Trades_m WHERE trdm_auto_id = ".$row["acti_trade_id"];
									 
				$result_trade_data =  mysql_query($query_trade_data) or die (mysql_error());
				$row_trade_data = mysql_fetch_array($result_trade_data);
				
			?>
				<tr class="tablerow">
					<td ><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$count?></a></td>
					
					<td align="center"><a href="javascript:CreateWnd('mail_trade_data.php?trade_id=<?=$row["acti_trade_id"]?>&user_id=<?=$user_id?>&acti_id=<?=$row["acti_id"]?>', 500, 500, false);"><img src="images/send_msg.gif" border="0"></a></td>
					<td align="center"><a href="javascript:CreateWnd('av_flag_close.php?trade_id=<?=$row["acti_trade_id"]?>&user_id=<?=$user_id?>&acti_id=<?=$row["acti_id"]?>', 360, 150, false);"><img src="images/close.gif" border="0"></a></td>
					<td>
						<!-- START TABLE 3 -->
						<table>
							<tr>
								<td>
									&nbsp;&nbsp;&nbsp;&nbsp;<a class="emptrades"><?=$row["acti_comments"]?></a>
								</td>
							</tr>
							<tr>
								<td>
									<!-- START TABLE 4 -->
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
											<td align="center"><a class="emptrades" href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row_trade_data["trdm_symbol"]?>', 500, 200, false);"><u><?=$row_trade_data["trdm_symbol"]?></u></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_price"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_trade_date"]?></a>&nbsp;&nbsp;</td>
											<td align="center"><a class="emptrades"><?=$row_trade_data["trdm_trade_time"]?></a>&nbsp;&nbsp;</td>
										</tr>
									</table>
									<!-- END TABLE 4 -->
								<td>
							</tr>
	
						</table>
						<!-- END TABLE 3 -->						
					</td>	
				</tr>
			<?
			$count++;
			} //END WHILE 1
			?>
			</table>
			<!-- END TABLE 2 -->
			
			 <script language="JavaScript">
			<!--
				//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
				tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
			// -->
			</script>
			
			</td>
			</tr>
			</table>
			<!-- END TABLE 1 -->
	<?
		}//END IF 1
		else
		{
			echo "<Br><p class='Contact'><B>&nbsp;&nbsp;&nbsp;No Open Action Items found!</B></p>";
		}		
		?>
		
		<!-- ADMIN CONTENT END -->
		</td>
  </tr>
</table>
<? table_end();  ?>
</td></tr></table>
</td>
</tr>

<?php
	
include('bottom.php');
	 
?>

