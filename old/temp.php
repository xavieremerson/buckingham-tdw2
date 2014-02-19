<?php
//Get the argument passed (List Type)
// it is $listtype
echo "Listtype = " . $listtype;

	//Date in YYYY-MM-DD Format
	$trade_date_to_process = previous_business_day();
	xdebug("Trade Date to process",$trade_date_to_process);

////
// Check if trades exist and only then begin processing
// If trades do not exist, there has been one or more errors in the import/upload of trades

	$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$trade_date_to_process."'") or die (mysql_error());
	while ( $row = mysql_fetch_array($result_num_trades) ) {
		$numtrades_val = $row["numtrades"];
	}
	xdebug("numtrades_val",$numtrades_val);
	
	if ($numtrades_val > 0) {
	
		//// For each list type process the data.
		$arr_list_types = array($listtype);
		
		$arr_list_types_tables = array('watch' => 'lwat_watch_list', 'gray' => 'lgry_gray_list', 'restricted' => 'lres_restricted_list');
		$arr_list_names_label = array('watch' => 'WATCH LIST', 'gray' => 'GRAY LIST', 'restricted' =>'RESTRICTED LIST');						

		for ($i_list =0; $i_list < count($arr_list_types); $i_list++) {
			xdebug('arr_list_types',$arr_list_types[$i_list]);
	
			//******************************************************************************	
			//Get tickers on list
			$query_symbols_on_list = "SELECT list_symbol from ".$arr_list_types_tables[$arr_list_types[$i_list]]." where list_isactive = 1";
			xdebug("query_symbols_on_list", $query_symbols_on_list );
			$result_num_trades = mysql_query($query_symbols_on_list) or die (mysql_error());
			$i = 0;
			$symbol_string = '';
			while ( $row = mysql_fetch_array($result_num_trades) ) {
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
			while ( $row = mysql_fetch_array($result_query_trades) ) {
				$arr_accounts[$i] = $row["trdm_account_number"];
				if ($str_accounts =='') {
				$str_accounts = "'".$row["trdm_account_number"]."'";
				} else {
				$str_accounts = $str_accounts.",'".$row["trdm_account_number"]."'";
				}
				$i = $i + 1;
			}
			xdebug("str_accounts",$str_accounts);
			
			//Check this condition thoroughly later
			xdebug("i",$i);
			$proceed = 0;
			if ($i > 0) {
			$proceed = 1;
			} 
			xdebug("proceed",$proceed); 
			
						
			//******************************************************************************	
			//Find if there are employee trades in these tickers, given that there are trades
			//the tickers on the stock list.

			if ($proceed == 1) {
			
				$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts where acct_is_active = 1 and acct_number in (".$str_accounts.")";
				//xdebug("query_accounts",$query_accounts);
				$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
				$i = 0;
				while ( $row = mysql_fetch_array($result_query_accounts) ) {
					$arr_accounts_match[$i] = $row["acct_number"];
					
					$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";
					
					if ($str_accounts_match =='') {
					$str_accounts_match = "'".$row["acct_number"]."'";
					
					} else {
					$str_accounts_match = $str_accounts_match.",'".$row["acct_number"]."'";
					}
					$i = $i + 1;
				}
					xdebug("str_accounts_match",$str_accounts_match);
				
					xdebug("i",$i);
					$proceed_final = 0;
					if ($i > 0) {
					$proceed_final = 1;
					} 
					xdebug("proceed_final",$proceed_final);
				
			} else {
			 		$proceed_final = 0;
			}
			
			if ($proceed_final == 1) {
			//Add to content $rep_content_emp_trades
			
			$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") and  trdm_account_number in (".$str_accounts_match.")";
			echo $query_trades_final;
			$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
			
		
					//$arr_accounts[$i] = $row["trdm_account_number"];
					
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
			    
					while ( $row = mysql_fetch_array($result_query_trades_final) ) {

					if ($emp_trades != 1) {
							if (in_array($row["trdm_account_number"], $arr_accounts)) {
							echo '<tr class="tablerowhighlight">';
							} else {
							echo '<tr class="tablerow">';					
							}
							?>
							
							<td><?
							if (in_array($row["trdm_account_number"], $arr_accounts)) {
							echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
							} else {
							echo '&nbsp;';					
							}
							?>
							</td>
							
							<?
							if (in_array($row["trdm_account_number"], $arr_accounts)) {
							?>
							
							<?
							if (in_array($row["trdm_auto_id"], $arr_trade_id)) {
							?>
							<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_note.gif" border="0" alt="Add Note"></a></td>
							<?
							}
							else {
							?>
							<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/add_note.gif" border="0" alt="Add Note"></a></td>
							<?
							}							
							?>
							
							<?
							if (in_array($row["trdm_auto_id"], $arr_trade_id_acti)) {
							?>
							<td><a href="javascript:CreateWnd('av_flag.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_flag.gif" border="0" alt="Add Flag and Create Action Item"></a></td>
							<?
							}
							else {
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
							} else {
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

							} else {

							if (in_array($row["trdm_account_number"], $arr_accounts)) {
							echo '<tr class="tablerowhighlight">';
							?><td><?
							if (in_array($row["trdm_account_number"], $arr_accounts)) {
							echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
							} else {
							echo '&nbsp;';					
							}
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
							?>
							
          <?
					}
					?>					

			<!--<? if ($row["acct_name2"] != ''){echo $row["acct_name2"];} else{ echo "&nbsp;";} ?>-->

			<?php
			   
			    }
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
		<!--Table with thin cell border ends-->


			<?
														
			} else {
			//Add to content $rep_content_emp_trades (no trades)
			?>
														<tr> 
															<td colspan="8">&nbsp;</td>
														</tr>
														<tr> 
															<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$arr_list_names_label[$arr_list_types[$i_list]].'</b> (No Trades)</font></td>
														</tr>
			<?											
																	
			}
						
		} //End for loop for processing all lists.
			xdebug("Trade Report for trade date ".$trade_date_to_process." sent successfully via email!",0);
						
			} else {
			
			echo "Trade Report was not sent because no trades were found for trade date ".$trade_date_to_process."<BR>";
			echo "possibly because there were errors in the trade upload. Please try the trade upload again and if<BR>";
			echo "the problem persists please contact Technical Support at support@centersysgroup.com.<BR>";
			}
?>


