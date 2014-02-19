<?php
	include('includes/functions.php');
	include('includes/dbconnect.php');
	$trade_date_to_process = previous_business_day();
	
	//insert into the mlis table a blank record with appropriate trade date!!!!!!
	
	
	
	$listvals = array('watch','gray','restricted');
	
	foreach($listvals as $xval) {
	$listtype = $xval;
	

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

?>

<? echo $table_start_h100; ?>

<?php
	include('includes/dbconnect.php');
	$date = date("Ymd");

//***********************************************************
//  CREATE CONDITION TO FILTER OUT TRADES IN LIST OF STOCKS
//***********************************************************
	$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$trade_date_to_process."'") or die (mysql_error());
	while ( $row = mysql_fetch_array($result_num_trades) ) 
	{
		$numtrades_val = $row["numtrades"];
	}
	xdebug("numtrades_val",$numtrades_val);

	//START OF IF 1
	if ($numtrades_val > 0) 
	{
		//// For each list type process the data.
		$arr_list_types = array($listtype);
		$arr_list_types_tables = array('watch' => 'lwat_watch_list', 'gray' => 'lgry_gray_list', 'restricted' => 'lres_restricted_list');
		$arr_list_names_label = array('watch' => 'WATCH LIST', 'gray' => 'GRAY LIST', 'restricted' =>'RESTRICTED LIST');						

			//START OF FOR 1
			for ($i_list =0; $i_list < count($arr_list_types); $i_list++) 
			{
				xdebug('arr_list_types',$arr_list_types[$i_list]);
				//******************************************************************************	
				//Get tickers on list
				$query_symbols_on_list = "SELECT list_symbol from ".$arr_list_types_tables[$arr_list_types[$i_list]]." where list_isactive = 1";
				xdebug("query_symbols_on_list", $query_symbols_on_list );
				$result_num_trades = mysql_query($query_symbols_on_list) or die (mysql_error());
				$i = 0;
				$symbol_string = '';
				while ( $row = mysql_fetch_array($result_num_trades) ) 
				{
					$symbols_on_list[$i] = $row["list_symbol"];
						if ($symbol_string=='') {
						$symbol_string = "'".$row["list_symbol"]."'";
						} else {
						$symbol_string = $symbol_string.",'".$row["list_symbol"]."'";
						}
					$i = $i + 1;
				}
				xdebug("symbol_string",$symbol_string);
					
				//******************************************************************************	
				//Find if there are trades in these tickers
				$query_trades = "SELECT trdm_auto_id, trdm_account_number, trdm_symbol from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.")";
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
				} 
				else 
					$proceed_final = 0;
		
				//START OF IF 2
				if ($proceed_final == 1) 
				{
					//Add to content $rep_content_emp_trades
					
					$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") and  trdm_account_number in (".$str_accounts_match.")";
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
						$countxyz = mysql_num_rows($result) 	;
						
						//update table mlis set val based on listtype and trade date
						
						echo $countxyz . "=============================================";
							//}
				
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
							    //START OF WHILE 1
								while ( $row = mysql_fetch_array($result) ) 
								{
									//START OF IF 3
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
										if (in_array($row["trdm_account_number"], $arr_accounts)) 
										{
											if (1==1) 
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
											if (2==2) 
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
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 350, 230, false);"><?=$row["trdm_symbol"]?></a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</a></td>
										<?
										} 
										else 
										{
										?>
												<td>&nbsp;</td>
												<td>&nbsp;</td>							
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_account_number"]?></td>
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 350, 230, false);"><?=$row["trdm_symbol"]?></a></td>
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></td>
												<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</td>
												<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</td>
												<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</td>
												<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</td>
												<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</td>							<?
										}
										?>
											</tr>
										<?
									} // END OF IF 3
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
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 350, 230, false);">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_symbol"]?></a></td>
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
						} //END OF WHILE 1
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
					}// END OF IF 2
					else
					{
					?>
						<Br><p class="Contact"><B>&nbsp;&nbsp;&nbsp;No trades found!</B></p>	
					<?
					}
				} // END OF FOR 1
			} // END OF IF 1
			?>
	<? echo $table_end_h100; ?>
	
	<?
	
	}
	
	?>