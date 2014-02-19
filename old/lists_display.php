<?
include('top.php');
include('includes/functions.php');

$tdate = previous_business_day ();
$query_symb = '';

//Get Employee Names on account
$result1 = mysql_query("SELECT acct_number, concat( acct_name1, ', ', acct_name2, ' <BR>(Rep: ', acct_rep, ')' ) as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());

$i = 0;
$arr_accountnames = array();
while ( $row = mysql_fetch_array($result1) ) 
{
	$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
	$i = $i+1;
}

//Get Trade_id from comments/notes table in an array
$result = mysql_query("SELECT distinct nadd_trade_id as trade_id from nadd_add_notes where nadd_is_active = 1") or die (mysql_error());
$i = 0;
$arr_trade_id = array();

while ( $row = mysql_fetch_array($result) ) 
{
	$arr_trade_id[$i] = $row["trade_id"];
	$i = $i+1;
}

//Get Trade_id from action items table in an array
$result = mysql_query("SELECT distinct acti_trade_id as trade_id from acti_action_item_flag where acti_is_active = 1") or die (mysql_error());
$i = 0;
$arr_trade_id_acti = array();

while ( $row = mysql_fetch_array($result) ) 
{
	$arr_trade_id_acti[$i] = $row["trade_id"];
	$i = $i+1;
}

		if($list_type == '1')
		{
			$query_list_types = "SELECT slis_title_name AS name FROM slis_system_lists WHERE slis_auto_id = '".$type."' AND slis_isactive = '1'";
		}
		else
		if($list_type == '2')
		{
			$query_list_types = "SELECT alis_title_name AS name  FROM alis_admin_lists WHERE alis_auto_id = '".$type."' AND alis_isactive = '1'";
		}
		else
		if($list_type == '3')
		{
			$query_list_types = "SELECT usli_title_name AS name FROM usli_user_lists WHERE usli_auto_id = '".$type."' AND usli_isactive = '1'";
		}
		$result_list_types = mysql_query($query_list_types) or die(mysql_error());
		$row_list_types = mysql_fetch_array($result_list_types);
?>

<tr>
<td height="100%" width="100%" valign="top">

<table cellpadding="4" width="100%"><tr><td>

<? table_start_percent(100, $row_list_types['name']); ?>

<?
$date = date("Ymd");
$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());

//***********************************************************
//  CREATE CONDITION TO FILTER OUT TRADES IN LIST OF STOCKS
//***********************************************************
	$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$tdate."'") or die (mysql_error());
	
	
	while ( $row = mysql_fetch_array($result_num_trades) ) 
	{
		$numtrades_val = $row["numtrades"];
	}
	xdebug("numtrades_val",$numtrades_val);

	//START OF IF 1
	if ($numtrades_val > 0) 
	{
		//START IF 2
		if($type != '4')
		{
			//SYSTEM LISTS
			if($list_type == '1')
			{
				$query_list_types = "SELECT slis_auto_id, slis_title_name FROM slis_system_lists WHERE slis_auto_id = '".$type."' AND slis_isactive = '1'";
				
				$result_list_types = mysql_query($query_list_types) or die(mysql_error());
				//START WHILE 1
				while($row_list_types = mysql_fetch_array($result_list_types))
				{
					$query_get_symbol = "SELECT syll_symbol FROM syll_system_list_lists WHERE syll_id = '".$row_list_types['slis_auto_id']."' AND syll_isactive = '1'";
					$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
					
					$i = 0;
					$symbol_string = '';
					while ($row_get_symbol = mysql_fetch_array($result_get_symbol)) 
					{
						$symbols_on_list[$i] = $row_get_symbol["syll_symbol"];
						if ($symbol_string=='') 
						{
							$symbol_string = "'".$row_get_symbol["syll_symbol"]."'";
						} 
						else 
						{
							$symbol_string = $symbol_string.",'".$row_get_symbol["syll_symbol"]."'";
						}
						$i = $i + 1;
					}
					xdebug("symbol_string",$symbol_string);
				} //END WHILE 1
			}
			else //END SYSTEM LISTS
			//ADMIN LISTS
			if($list_type == '2')
			{
				$query_list_types = "SELECT alis_auto_id, alis_title_name FROM alis_admin_lists WHERE alis_auto_id = '".$type."' AND alis_isactive = '1'";				
				$result_list_types = mysql_query($query_list_types) or die(mysql_error());
				//START WHILE 2
				while($row_list_types = mysql_fetch_array($result_list_types))
				{
					$query_get_symbol = "SELECT adll_symbol FROM adll_admin_list_lists WHERE adll_id = '".$row_list_types['alis_auto_id']."' AND adll_isactive = '1'";
					$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
					
					$i = 0;
					$symbol_string = '';
					while ($row_get_symbol = mysql_fetch_array($result_get_symbol)) 
					{
						$symbols_on_list[$i] = $row_get_symbol["adll_symbol"];
						if ($symbol_string=='') 
						{
							$symbol_string = "'".$row_get_symbol["adll_symbol"]."'";
						} 
						else 
						{
							$symbol_string = $symbol_string.",'".$row_get_symbol["adll_symbol"]."'";
						}
						$i = $i + 1;
					}
					xdebug("symbol_string",$symbol_string);
				} //END WHILE 2			
			} //END ADMIN LISTS
			else
			//USER LISTS
			if($list_type == '3')
			{
				$query_list_types = "SELECT usli_auto_id, usli_title_name FROM usli_user_lists WHERE usli_auto_id = '".$type."' AND usli_isactive = '1'";
				$result_list_types = mysql_query($query_list_types) or die(mysql_error());
				//START WHILE 3
				while($row_list_types = mysql_fetch_array($result_list_types))
				{
					$query_get_symbol = "SELECT usll_symbol FROM usll_user_list_lists WHERE usll_list_id = '".$row_list_types['usli_auto_id']."' AND usll_isactive = '1'";
					$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
					
					$i = 0;
					$symbol_string = '';
					while ($row_get_symbol = mysql_fetch_array($result_get_symbol)) 
					{
						$symbols_on_list[$i] = $row_get_symbol["usll_symbol"];
						if ($symbol_string=='') 
						{
							$symbol_string = "'".$row_get_symbol["usll_symbol"]."'";
						} 
						else 
						{
							$symbol_string = $symbol_string.",'".$row_get_symbol["usll_symbol"]."'";
						}
						$i = $i + 1;
					}
					xdebug("symbol_string",$symbol_string);
				} //END WHILE 3			
			} // END USER LISTS

			//START IF 5
			if($symbol_string != '')
			{
				//******************************************************************************	
				//Find if there are trades in these tickers
				$query_trades = "SELECT trdm_auto_id, trdm_account_number, trdm_symbol from Trades_m where trdm_trade_date ='".$tdate."' and trdm_symbol in (".$symbol_string.")";
				xdebug("query_trades",$query_trades);
				$result_query_trades = mysql_query($query_trades) or die (mysql_error());
				$i = 0;
				while ( $row = mysql_fetch_array($result_query_trades) ) 
				{
					$arr_accounts[$i] = $row["trdm_account_number"];
					if ($str_accounts =='') 
						$str_accounts = "'".$row["trdm_account_number"]."'";
					else 
						$str_accounts = $str_accounts.",'".$row["trdm_account_number"]."'";
					
					$i = $i + 1;
				}
				xdebug("str_accounts",$str_accounts);
					
				//Check this condition thoroughly later
				xdebug("i",$i);
				$proceed = 0;
				if ($i > 0) 
					$proceed = 1;
					
				xdebug("proceed",$proceed); 
				//******************************************************************************	
				//Find if there are employee trades in these tickers, given that there are trades
				//the tickers on the stock list.
		
				//START IF 3
				if ($proceed == 1) 
				{
					$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts where acct_is_active = 1 and acct_number in (".$str_accounts.")";
					//xdebug("query_accounts",$query_accounts);
					$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
					$i = 0;
					while ( $row = mysql_fetch_array($result_query_accounts) ) 
					{
						$arr_accounts_match[$i] = $row["acct_number"];
						$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";
						
						if ($str_accounts_match =='') 
							$str_accounts_match = "'".$row["acct_number"]."'";						
						else 
							$str_accounts_match = $str_accounts_match.",'".$row["acct_number"]."'";
						
						$i = $i + 1;
					}
					xdebug("str_accounts_match",$str_accounts_match);
		
					xdebug("i",$i);
					$proceed_final = 0;
					if ($i > 0) 
						$proceed_final = 1;
					xdebug("proceed_final",$proceed_final);
				} //END IF 3
				else 
					$proceed_final = 0;
		
				//START IF 4
				if ($proceed_final == 1) 
				{
					//Add to content $rep_content_emp_trades
					
					$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$tdate."' and trdm_symbol in (".$symbol_string.") and  trdm_account_number in (".$str_accounts_match.")";
					//echo $query_trades_final;
					//$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
		
					//***********************************************************
					//  END CREATE CONDITION TO FILTER OUT TRADES IN LIST OF STOCKS
					//***********************************************************
					$query_statement = "SELECT 	trdm_auto_id, 
										trdm_account_number, 
										trdm_trade_date, 
										trdm_settle_date, 
										abs(round(trdm_quantity,0)) as 'trdm_quantity',
										round(trdm_price,2) as 'trdm_price',
										trdm_buy_sell,
										UPPER(trdm_symbol) as 'trdm_symbol',
										trdm_sec_description,
										trdm_trade_time
						 FROM Trades_m " . $str_trdm_trade_date . 
						 $str_trdm_symbol .
						 $str_trdm_account_number .
						 " ORDER BY trdm_symbol, trdm_trade_time";	
						
						$result = mysql_query($query_trades_final) or die (mysql_error());
	
						?>
							<!--Table with thin cell border-->
							<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
								<tr>
									<td>
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
								//START OF WHILE 4
								while ( $row = mysql_fetch_array($result) ) 
								{
									//START OF IF 6
									if ($emp_trades != 1) 
									{
										if (in_array($row["trdm_account_number"], $arr_accounts)) 
											echo '<tr class="tablerowhighlight">';
										else 
											echo '<tr class="tablerow">';					
										?>
												<td>
										<?
										if (in_array($row["trdm_account_number"], $arr_accounts)) 
											echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
										else 
											echo '&nbsp;';					
										?>
												</td>
											
										<?
										//START IF 7
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
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<a  class="emptrades" href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);"><?=$row["trdm_symbol"]?></a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</a></td>
										<?
										} 
										//END IF 7
										else 
										{
										?>
												<td>&nbsp;</td>
												<td>&nbsp;</td>							
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_account_number"]?></td>
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<a class="emptrades" href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);"><?=$row["trdm_symbol"]?></a></td>
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
									} // END OF IF 6
									else 
									{
										if (in_array($row["trdm_account_number"], $arr_accounts)) 
										{
											echo '<tr class="tablerowhighlight">';
											?>
												<td>
											<?
											if (in_array($row["trdm_account_number"], $arr_accounts)) 
												echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
											else 
												echo '&nbsp;';					
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
								   ?>					
							 <!--<? if ($row["acct_name2"] != ''){echo $row["acct_name2"];} else{ echo "&nbsp;";} ?>-->
							<?php
						} //END OF WHILE 4
					?>
							<script language="JavaScript">
								<!--
									//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
									tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
								// -->
							</script>
									</table>
								</td>
							</tr>
						</table>
								
						<?
					} // END IF 4
					else
					{
						echo "<Br><p class='Contact'><B>&nbsp;&nbsp;&nbsp;No trades found!</B></p>";
					}
				} // END IF 5
				else
				{
					echo "<Br><p class='Contact'><B>&nbsp;&nbsp;&nbsp;No trades found!</B></p>";
				}
			} //END IF 2
			//START ELSE 1
			else
			{
				$hdate =  business_day_backward(strtotime("now"), (20+1));
				$arr_acctnames = array();
				$arr_accounts = array();
				
				$query = "trdm_account_number = '3123123123131' ";
				$qry_acctnames = "SELECT acct_number, acct_name1 FROM Employee_accounts";
				$result_acctnames = mysql_query($qry_acctnames) or die(mysql_error());
				while($row= mysql_fetch_array($result_acctnames))
				{
					$arr_acctnames[$row["acct_number"]] = $row["acct_name1"];
					$arr_accounts[] = $row["acct_number"];
					$query = $query . " OR trdm_account_number = '" . $row["acct_number"] . "' ";
				}
				?>
					<!--Table with thin cell border-->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td>
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
					//GET ALL THE TRADES FROM LAST BUSINESS DAY THAT WERE SELL 
					$get_sell_trades = "SELECT * FROM Trades_m WHERE trdm_trade_date = '".$tdate."' AND (trdm_buy_sell = 'sl' OR trdm_buy_sell = 'Sell') AND (".$query.")";
					$result_sell_trades = mysql_query($get_sell_trades) or die(mysql_error());
				
					//START WHILE 2
					while($row_sell_trades = mysql_fetch_array($result_sell_trades))
					{
						$get_buy_trades = "SELECT * FROM Trades_m WHERE trdm_account_number = '".$row_sell_trades["trdm_account_number"]."' AND trdm_symbol = '".$row_sell_trades["trdm_symbol"]."' AND (trdm_buy_sell = 'by' OR trdm_buy_sell = 'Buy') AND trdm_trade_date >= '".$hdate."'";
						$result_buy_trades = mysql_query($get_buy_trades) or die(mysql_error());
						//START WHILE 3
						while($row_buy_trades = mysql_fetch_array($result_buy_trades))
						{
							if (in_array($row_buy_trades["trdm_account_number"], $arr_accounts)) 
								echo '<tr class="tablerowhighlight">';
							else 
								echo '<tr class="tablerow">';					
							?>
									<td>
							<?
							if (in_array($row_buy_trades["trdm_account_number"], $arr_accounts)) 
								echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row_buy_trades["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
							else 
								echo '&nbsp;';					
							?>
									</td>
								
							<?
								if (in_array($row_buy_trades["trdm_auto_id"], $arr_trade_id)) 
								{
									?>
									<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row_buy_trades["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_note.gif" border="0" alt="Add Note"></a></td>
									<?
								}
								else 
								{
									?>
									<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row_buy_trades["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/add_note.gif" border="0" alt="Add Note"></a></td>
									<?
								}							
								?>
								<?
								if (in_array($row_buy_trades["trdm_auto_id"], $arr_trade_id_acti)) 
								{
									?>
									<td><a href="javascript:CreateWnd('av_flag.php?trade_id=<?=$row_buy_trades["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_flag.gif" border="0" alt="Add Flag and Create Action Item"></a></td>
									<?
								}
								else 
								{
									?>
									<td><a href="javascript:CreateWnd('av_flag.php?trade_id=<?=$row_buy_trades["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/add_flag.gif" border="0" alt="Add Flag and Create Action Item"></a></td>
									<?
								}							
								?>
			
									<!-- <td><img src="images/add_flag.gif" border="0" alt="Flag and Create Action Item"></td>  -->							
									<td onMouseover="ddrivetip('<?=$arr_accountnames[$row_buy_trades["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row_buy_trades["trdm_account_number"]?></a></td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row_buy_trades["trdm_symbol"]?>', 500, 200, false);"><?=$row_buy_trades["trdm_symbol"]?></a></td>
									<td onMouseover="ddrivetip('<?=$arr_accountnames[$row_buy_trades["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row_buy_trades["trdm_sec_description"]?></a></td>
									<td onMouseover="ddrivetip('<?=$arr_accountnames[$row_buy_trades["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row_buy_trades["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</a></td>
									<td onMouseover="ddrivetip('<?=$arr_accountnames[$row_buy_trades["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row_buy_trades["trdm_quantity"]?>&nbsp;&nbsp;</a></td>
									<td onMouseover="ddrivetip('<?=$arr_accountnames[$row_buy_trades["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row_buy_trades["trdm_price"]?>&nbsp;&nbsp;</a></td>
									<td onMouseover="ddrivetip('<?=$arr_accountnames[$row_buy_trades["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_buy_trades["trdm_trade_date"])?>&nbsp;&nbsp;</a></td>
									<td onMouseover="ddrivetip('<?=$arr_accountnames[$row_buy_trades["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row_buy_trades["trdm_trade_time"]?>&nbsp;&nbsp;</a></td>
								</tr>
								<?
							} //END WHILE 3
						} //END WHILE 2
					?>
						<script language="JavaScript">
						<!--
						//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
						tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
						// -->
						</script>

						</table>
					</td>
				</tr>
			</table>
					
				<?
				}// END ELSE 1
			} //END IF 1
			else
			{
			?>
				<Br><p class="Contact"><B>&nbsp;&nbsp;&nbsp;No trades found!</B></p>	
			<?
			}
			?>
		<? table_end_percent(); ?>
		</td></tr></table>
	</td>
</tr>
<?
include('bottom.php');
?>