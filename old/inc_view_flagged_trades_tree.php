<?php

// SHOW FLAGGED TRADES FOR USER
 //include('includes/functions.php');
 include('includes/dbconnect.php');
	//Date in YYYY-MM-DD format
	$trade_date_to_process = previous_business_day();
?>

<?
//**********************************************************************
//Get Employee Accounts data in a local variable
$result = mysql_query("SELECT acct_number FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());
$i = 0;
$arr_accounts = array();
	while ( $row = mysql_fetch_array($result) ) 
	{
		$arr_accounts[$i] = $row["acct_number"];
		$i = $i+1;
	}
//**********************************************************************
//Get Employee Names on account
$result1 = mysql_query("SELECT acct_number, concat( acct_name1, ', ', acct_name2, ' <BR>(Rep: ', acct_rep, ')' ) as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());

$i = 0;
$arr_accountnames = array();

while ( $row = mysql_fetch_array($result1) ) 
{
	$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
	$i = $i+1;
}
//**********************************************************************	
//Get Trade_id from comments/notes table in an array
$result = mysql_query("SELECT distinct nadd_trade_id as trade_id from nadd_add_notes where nadd_is_active = 1") or die (mysql_error());
$i = 0;
$arr_trade_id = array();

while ( $row = mysql_fetch_array($result) ) 
{
	$arr_trade_id[$i] = $row["trade_id"];
	$i = $i+1;
}
//**********************************************************************
//Get Trade_id from action items table in an array
$result = mysql_query("SELECT distinct acti_trade_id as trade_id from acti_action_item_flag where acti_is_active = 1") or die (mysql_error());
$i = 0;
$arr_trade_id_acti = array();

while ( $row = mysql_fetch_array($result) ) 
{
	$arr_trade_id_acti[$i] = $row["trade_id"];
	$i = $i+1;
}
//***********************************************************************

echo $table_start_h100;

	include('includes/dbconnect.php');
	$date = date("Ymd");
	$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());

	/*
	if ($trdm_trade_date != '') { 
	$str_trdm_trade_date = " where trdm_trade_date = '". $trdm_trade_date ."'";
	} else {
	$str_trdm_trade_date = " where trdm_trade_date = '". $trade_date_to_process ."'";
	}			  
	*/
		
	$str_trdm_trade_date = 'WHERE ';
	
	if ($trdm_symbol != '') 
	{ 
	//$str_trdm_symbol = " and trdm_symbol = '".$trdm_symbol."'";
	$str_trdm_symbol = "  and trdm_symbol = '".$symbolval."'";
	} 
	else 
	{
	//$str_trdm_symbol = " and trdm_symbol != '' and LENGTH(trdm_symbol) < 8";
	//$str_trdm_symbol = " and trdm_symbol = '".$symbolval."'";
	}			  

	if ($trdm_account_number != '') 
	{ 
		$str_trdm_account_number = "  trdm_account_number = '".$trdm_account_number."'";
	} 
	else
	{
		$str_trdm_account_number = " trdm_account_number not like '0000%' ";
	}			  


    $query_count = "SELECT distinct a.acti_user_id FROM acti_action_item_flag a , Trades_m b " . $str_trdm_trade_date . $str_trdm_symbol .
					$str_trdm_account_number . " and a.acti_user_id = ". $user_id . " AND a.acti_trade_id = b.trdm_auto_id ".
					" ORDER BY trdm_symbol, trdm_trade_time";
					
	$result_count = mysql_query($query_count) or die(mysql_error());
	$row_count = mysql_numrows($result_count);

	//START IF
	if($row_count > 0)
	{
		$query_statement = "SELECT  distinct a.acti_user_id,	
							b.trdm_auto_id, 
							b.trdm_account_number, 
							b.trdm_trade_date, 
							b.trdm_settle_date, 
							abs(round(b.trdm_quantity,0)) as 'trdm_quantity',
							round(b.trdm_price,2) as 'trdm_price',
							b.trdm_buy_sell,
							UPPER(b.trdm_symbol) as 'trdm_symbol',
							b.trdm_sec_description,
							b.trdm_trade_time
			 FROM acti_action_item_flag a , Trades_m b " . $str_trdm_trade_date . $str_trdm_symbol .
			 $str_trdm_account_number . " and a.acti_user_id = ". $user_id . " AND a.acti_trade_id = b.trdm_auto_id ".
			 " ORDER BY trdm_symbol, trdm_trade_time";
						
		$result = mysql_query($query_statement) or die (mysql_error());		
		?>
		<!--Table with thin cell border-->
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC"><tr><td>
		<!-- class="tablewithdata" -->
		<table class="sortable" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="0">
		<!-- class="tableheading12" -->
		<tr bgcolor="#CCCCCC"> 
			<td height="20" width="25" align="center" valign="middle" >&nbsp;</td>
			<td width="18"><img src="images/add_note_heading.gif" border="0" alt="Add Note">N</td>  
			<td width="18"><img src="images/flag_heading.gif" border="0" alt="Add Flag and Create Action Item">F</td>  
			<td >Account #</td>  
			<td >Symbol</td> 
			<td >Description</td>
			<td >B/S</td>
			<td >Quantity</td>
			<td >Price</td>
			<td >Trade Date</td>
			<td >Time</td>
		</tr>
		
		<?
		//START WHILE
		while ( $row = mysql_fetch_array($result) ) 
		{
			//START IF 1
			if ($emp_trades != 1) 
			{
				if (in_array($row["trdm_account_number"], $arr_accounts)) 
				{
					echo '<tr class="tablerowhighlight">';
				} 
				else 
				{ 
					echo '<tr class="tablerow">';					
				}
				?>
				
				<td><?
				if (in_array($row["trdm_account_number"], $arr_accounts)) 
				{
					echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
				} 
				else 
				{
					echo '&nbsp;';					
				}
				?>
				</td>
					
				<?
				//START IF 2
				if (in_array($row["trdm_account_number"], $arr_accounts))
				{
					if (in_array($row["trdm_auto_id"], $arr_trade_id)) 
					{
						?>
						<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_note.gif" border="0" alt="Add Note"></a></td>
						<?
					}
					else 
					{
						?>
						<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/add_note.gif" border="0" alt="Add Note"></a></td>
						<?
					}							
					?>
					
					<?
					if (in_array($row["trdm_auto_id"], $arr_trade_id_acti)) 
					{
						?>
						<td><a href="javascript:CreateWnd('av_flag.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_flag.gif" border="0" alt="Add Flag and Create Action Item"></a></td>
						<?
					}
					else 
					{
						?>
						<td><a href="javascript:CreateWnd('av_flag.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/add_flag.gif" border="0" alt="Add Flag and Create Action Item"></a></td>
						<?
					}							
					?>
						<!-- <td><img src="images/add_flag.gif" border="0" alt="Flag and Create Action Item"></td>  -->							
						<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_account_number"]?></a></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);"><?=$row["trdm_symbol"]?></a></td>
						<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></a></td>
						<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</a></td>
						<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</a></td>
						<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</a></td>
						<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</a></td>
						<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</a></td>
				<?
				} //END IF 2
				else 
				{
				?>
						<td>&nbsp;</td>
						<td>&nbsp;</td>							
						<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_account_number"]?></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);"><?=$row["trdm_symbol"]?></a></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></td>
						<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</td>
						<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</td>
						<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</td>
						<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</td>
				<?
				}
				?>
			</tr>
			<?
			} //END IF 1
			else 
			{
				if (in_array($row["trdm_account_number"], $arr_accounts)) 
				{
					echo '<tr class="tablerowhighlight">';
					?><td><?
					if (in_array($row["trdm_account_number"], $arr_accounts)) 
					{
						echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
					} 
					else 
					{
						echo '&nbsp;';					
					}
					?>
					</td>
					<td><img src="images/add_note.gif" border="0" alt="Add Note"></td>
					<td><img src="images/add_flag.gif" border="0" alt="Flag and Create Action Item"></td>							
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()">&nbsp;&nbsp;&nbsp;&nbsp;<a class="emptrades"><?=$row["trdm_account_number"]?></a></td> 
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_symbol"]?></a></td>
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></a></td>
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</a></td>
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</a></td>
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</a></td>
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</a></td>
					<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</a></td>
				</tr>
		<?
				} 
			}
		}// END WHILE
		?>
			</table>

			<script language="JavaScript">
				<!--
				//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
				tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
				// -->
			</script>
		</td>
		</tr>
		</table>
<?	
	} //END IF 
	else
	{
		echo "<Br><p class='Contact'><B>&nbsp;&nbsp;&nbsp;No trades found!</B></p>";
	}

	echo $table_end_h100; 
?>


