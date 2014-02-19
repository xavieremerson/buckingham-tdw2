  <?
	if (isset($dtrd_id) and $dtrd_id != '') {
						$qry_del = "UPDATE otd_emp_trades_external set otd_isactive = 0
						 						WHERE auto_id = '".$dtrd_id."'";
						$result_del = mysql_query($qry_del) or die(tdw_mysql_error($qry_del));
						sys_message(1, "Trade deleted successfully.");
						unset($dtrd_id);
	}
	?>


	<? tsp(100, "Trades in External Accounts."); ?>
  	<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr class="ilt">
						  <td width="50">&nbsp;&nbsp;Del.</td>
							<td width="50">&nbsp;&nbsp;Edit</td>
							<td ts_type="date" width="100">&nbsp;&nbsp;Trade Date</td>
							<td width="60">&nbsp;&nbsp;Name</td>
							<td>&nbsp;&nbsp;Account</td> 
							<td width="60">&nbsp;&nbsp;Symbol</td>
							<td width="100">&nbsp;&nbsp;Buy/Sell</td>
							<td ts_type="money" width="100">&nbsp;&nbsp;Quantity</td>
							<td ts_type="money" width="100">&nbsp;&nbsp;Price</td>
							<td ts_type="money" width="100">&nbsp;&nbsp;Date Entered</td>
							<td>&nbsp;</td>
						</tr>
						<?
						if ($sel_emp == '_ALL_') {
							$str_emp = " and c.oac_emp_userid like '%' "	;					
						} else {
							$str_emp = " and c.oac_emp_userid like '%".$sel_emp."%' ";				
						}
						
						if ($sel_symbol == 'SYMBOL' or trim($sel_symbol) == '') {
							$str_symbol = " and a.otd_symbol like '%' ";
						} else {
							$str_symbol = " and a.otd_symbol like '%".trim($sel_symbol)."%' ";
						}
						
						$str_sql_select = "SELECT a. * , a.auto_id as tid, date_format(a.otd_last_edited_on, '%c/%e/%Y %h:%i%p' ) as date_added, b.Fullname, c.oac_account_number, c.oac_custodian
																FROM otd_emp_trades_external a, users b, oac_emp_accounts c
																WHERE a.otd_account_id = c.auto_id
																AND c.oac_emp_userid = b.ID
																AND a.otd_trade_date between '".$datefrom."' and '".$dateto."' ".$str_emp.$str_symbol."
																AND a.otd_isactive = 1
																ORDER BY a.auto_id DESC";
            //xdebug("str_sql_select",$str_sql_select);
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{
							if ($count_row_select%2) {
										$class_row_select = "trdark";
							} else { 
									$class_row_select = "trlight"; 
							} 
						?>
						<tr class="<?=$class_row_select?>"> 
						  <td width="50">&nbsp;&nbsp;
							<a 
							onMouseOver="window.status=''; return true" 
							onclick="javascript:return confirm('Are you sure you want to delete the following trade?\r\n\r\nTrade Date:\t<?=format_date_ymd_to_mdy($row_select["otd_trade_date"])?>\r\nName:\t\t<?=$row_select["Fullname"]?>\r\nSymbol = <?=$row_select["otd_symbol"]?>, B/S = <?=$row_select["otd_buysell"]?>, Quantity = <?=$row_select["otd_quantity"]?>, Price = <?=$row_select["otd_price"]?>')"
							href="<?=$PHP_SELF?>?dtrd_id=<?=$row_select["tid"]?>"
							>
							<img 
							src="images/themes/standard/delete.gif"
							/>
							</a>
							</td>
						  <td width="50">&nbsp;&nbsp;
							<a 
							onMouseOver="window.status=''; return true" 
							onClick="window.status=''; return true" 
							href="#"
							>
							<img 
							src="images/themes/standard/edit.gif" 
							onclick="javascript:CreateWnd('mod_ext_trades_edit.php?tid=<?=$row_select["tid"]?>&uid=<?=$user_id?>', 500, 300, false);" 
							/>
							</a>
							</td>
 							<td>&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["otd_trade_date"])?></td>
							<td>&nbsp;&nbsp;<?=$row_select["Fullname"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["oac_account_number"] . "  (".trim($row_select["oac_custodian"]).")"?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_symbol"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_buysell"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_quantity"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_price"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["date_added"]?></td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		<? tep(); ?>