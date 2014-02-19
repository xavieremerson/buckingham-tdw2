<?php
include('top.php');
include('includes/functions.php'); 

$tdate = business_day_backward(strtotime("now()"), 1);

$query_trades_all = "SELECT trdm_auto_id, trdm_account_number, trdm_buy_sell, trdm_quantity, trdm_symbol, trdm_price, trdm_trade_date, trdm_trade_time FROM Trades_m WHERE trdm_trade_date = '".$tdate."' ORDER BY trdm_symbol ASC";
$result_trades_all = mysql_query($query_trades_all) or die(mysql_error());


if($flag1 == 1)
{
	$query_trades_part = "SELECT trdm_auto_id, trdm_account_number, trdm_buy_sell, trdm_quantity, trdm_symbol, trdm_price, trdm_trade_date, trdm_trade_time FROM Trades_m WHERE trdm_trade_date = '".$tdate."' AND trdm_symbol = '".$ticker."'";
	$result_trades_part = mysql_query($query_trades_part) or die(mysql_error());
	
	while ( $row_trades_part = mysql_fetch_array($result_trades_part)) 
	{
		$arr_trdm_trade[strtotime($row_trades_part["trdm_trade_time"])] = $row_trades_part["trdm_symbol"] . "|" .$row_trades_part["trdm_account_number"] .  "|" .$row_trades_part["trdm_quantity"] . "|" .$row_trades_part["trdm_price"] . "|" .$row_trades_part["trdm_buy_sell"]. "|" .$row_trades_part["trdm_trade_date"]. "|" .$row_trades_part["trdm_trade_time"];
		$time[] = strtotime($row_trades_part["trdm_trade_time"]);
	}	
	ksort($arr_trdm_trade);
	sort($time);

	/*
	echo "<BR>";
	print_r($arr_trdm_trade);
	echo "<BR>";
	echo count($time);
	*/
}

?>

<form action="piggyback.php" name="trades" method="post">
<!-- TABLE 1 START -->
<table width="100%" cellpadding="1", cellspacing="0">
	<tr>
		<td valign="top"  width="50%">
			<!--TABLE 2 START-->
			<?
			table_start_percent(100, "All Trades");
			?>
			<table class="sortable"  id="accounts_table"  width="100%"  border="0" cellspacing="1" bgcolor="#CCCCCC" >
				<tr valign="top">
					<td align="center" valign="middle">#</td>
					<td align="left" nowrap>TICKER</td>
					<td align="left" nowrap>QTY.</td>
					<td align="left" nowrap>PRICE</td>
					<td align="left" nowrap>B/S</td>
					<td align="left" nowrap>TRADE-DATE</td>
					<td align="left" nowrap>TRADE-TIME</td>
				</tr>
				<?
				$emp_accts = array();
				$query_emp = "SELECT acct_number, acct_name1 FROM Employee_accounts";
				$result_emp = mysql_query($query_emp) or die(mysql_error());
				
				while($row_emp = mysql_fetch_array($result_emp))
				{
					$emp_accts[] = $row_emp["acct_number"];	
				}
			
				$row_count = 1;
				while ( $row_trades_all = mysql_fetch_array($result_trades_all) ) 
				{
					$row_class = 'tablerow';
					if(in_array($row_trades_all["trdm_account_number"],$emp_accts))
					{
						$row_class = 'tablerow1';
					}
				?>
				<tr class="<?=$row_class?>" onClick="javascript:parent.location.href='<?php_self?>?flag1=1&ticker=<?=$row_trades_all['trdm_symbol']?>'"> 
					<td align="left" valign="middle"><?=$row_count?></td>
					<td><a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row_trades_all["trdm_symbol"]?>', 500, 200, false);"><?=$row_trades_all["trdm_symbol"]?></a></td>
					<td><?=$row_trades_all["trdm_quantity"]?></td>
					<td><?=$row_trades_all["trdm_price"]?></td>
					<td><? if($row_trades_all["trdm_buy_sell"] == 'by' OR $row_trades_all["trdm_buy_sell"] == 'Buy') echo 'Buy'; if($row_trades_all["trdm_buy_sell"] == 'sl' OR $row_trades_all["trdm_buy_sell"] == 'Sell') echo 'Sell'; ?></td>
					<td><?=format_date_ymd_to_mdy($row_trades_all["trdm_trade_date"])?></td>
					<td><?=$row_trades_all["trdm_trade_time"]?>&nbsp;</td>
				</tr>
				<?php
				$row_count++;
				}
				?>
			</table>
			<?
			table_end_percent();
			?>

			<!-- TABLE 2 END -->
		</td>
		<td>&nbsp;&nbsp;&nbsp;</td>
		<td valign="top"  width="50%">
			<!--TABLE 3 START-->
			<?
			if($flag1 == 1)
			{
			?>	
			<?
			table_start_percent(100, "All Trades");
			?>

			<table class="sortable"  id="accounts_table"  width="100%"  border="0" cellspacing="1" bgcolor="#CCCCCC">
		
				<tr valign="top">
					<td align="center" valign="middle">#</td>
					<td align="left" nowrap>TICKER</td>
					<td align="left" nowrap>ACCT #</td>
					<td align="left" nowrap>QTY.</td>
					<td align="left" nowrap>PRICE</td>
					<td align="left" nowrap>B/S</td>
					<td align="left" nowrap>TRADE-DATE</td>
					<td align="left" nowrap>TRADE-TIME</td>
				</tr>
				<?
				$row_count = 1;
				for($i = 0; $i < count($time); $i++)
				{
					list($a,$b,$c,$d,$e,$f,$g) = explode("|",$arr_trdm_trade[$time[$i]]);
				
					$emp_accts = array();
					$query_emp = "SELECT acct_number, acct_name1 FROM Employee_accounts WHERE acct_number = '".$b."'";
					$result_emp = mysql_query($query_emp) or die(mysql_error());
					
					while($row_emp = mysql_fetch_array($result_emp))
					{
						$emp_accts[$row_emp["acct_number"]] = $row_emp["acct_name1"];	
					}
				
					$row_class = 'tablerow';
					if(array_key_exists($b,$emp_accts))
					{
						$row_class = 'tablerow1';
					}
				?>
				<tr class="<?=$row_class?>"> 
					<td align="left" valign="middle" onMouseover="ddrivetip('<?=$emp_accts[$row_trades_part["trdm_account_number"]]?>','white', 150)"; onMouseout="hideddrivetip()"><?=$row_count?></td>
					<td <? if(array_key_exists($b,$emp_accts)){?>onMouseover="ddrivetip('<?=$emp_accts[$b]?>','white', 150)"; onMouseout="hideddrivetip()"<? }?>><?=$a?></td>
					<td <? if(array_key_exists($b,$emp_accts)){?>onMouseover="ddrivetip('<?=$emp_accts[$b]?>','white', 150)"; onMouseout="hideddrivetip()"<? }?>><?=$b?></td>
					<td <? if(array_key_exists($b,$emp_accts)){?>onMouseover="ddrivetip('<?=$emp_accts[$b]?>','white', 150)"; onMouseout="hideddrivetip()"<? }?>><?=$c?></td>
					<td <? if(array_key_exists($b,$emp_accts)){?>onMouseover="ddrivetip('<?=$emp_accts[$b]?>','white', 150)"; onMouseout="hideddrivetip()"<? }?>><?=$d?></td>
					<td <? if(array_key_exists($b,$emp_accts)){?>onMouseover="ddrivetip('<?=$emp_accts[$b]?>','white', 150)"; onMouseout="hideddrivetip()"<? }?>><? if($e == 'by' OR $e == 'Buy') echo 'Buy'; if($e == 'sl' OR $e == 'Sell') echo 'Sell'; ?></td>
					<td <? if(array_key_exists($b,$emp_accts)){?>onMouseover="ddrivetip('<?=$emp_accts[$b]?>','white', 150)"; onMouseout="hideddrivetip()"<? }?>><?=format_date_ymd_to_mdy($f)?></td>
					<td <? if(array_key_exists($b,$emp_accts)){?>onMouseover="ddrivetip('<?=$emp_accts[$b]?>','white', 150)"; onMouseout="hideddrivetip()"<? }?>><?=$g?>&nbsp;</td>
				</tr>
				<?php
				$row_count++;
				}
				?>
			</table>
			<?
				}
			table_end_percent();
			?>

			<!-- TABLE 3 END -->
		
			<script language="JavaScript">
			<!--
				///////////////////////tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
				tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
			// -->
			</script>
		</td>
	</tr>
</table>
<!-- TABLE 1 END -->
<!--<p class="LocOps"><a class="LocOps" href="addaccount.php">Add Account</a> : <?php if ($SortBy == "Name1") { ?><b>Sorted By Name1</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>?SortBy=Name1">Sort By Name1</a><?php } ?> : <?php if (!$SortBy) { ?><b>Sorted By Name2</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>">Sort By Name2</a><?php } ?></p>-->


</form>
<?
include('bottom.php');
?>
