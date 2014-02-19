<?php
include('top.php');
include('includes/functions.php');
	
if($preview)
{
	$acct_string = "";
	$tick_string = "";
	$list_string = "";
		
	for($i = 0; $i < count($acctSelected); $i++)
	{
		$acct_string = $acct_string . "|" . $acctSelected[$i]. "|";
	}

	for($i = 0; $i < count($tickSelected); $i++)
	{
		$tick_string = $tick_string . "|" . $tickSelected[$i]. "|";
	}

	for($i = 0; $i < count($listSelected); $i++)
	{
		$list_string = $list_string . "|" . $listSelected[$i]. "|";
	}


	$query_insert = "INSERT INTO arep_adhoc_reports(arep_report_type, arep_name, arep_user_id, arep_acct, arep_tick, arep_list, arep_format, arep_isactive) VALUES('2', '".$name."', '".$user_id."', '".$acct_string."', '".$tick_string."', '".$list_string."', '".$format."', '0')";
	$result_insert = mysql_query($query_insert) or die(mysql_error());
	
	$query_id = "SELECT max(arep_auto_id) AS id FROM arep_adhoc_reports";
	$result_id = mysql_query($query_id) or die(mysql_error());
	$row_id = mysql_fetch_array($result_id);
	
	$query_insert = "INSERT INTO rdat_report_data(rdat_user_id, rdat_repo_id, rdat_report_type, rdat_rmod_id, rdat_isactive) VALUES('".$user_id."', '".$row_id['id']."', '2', '".$format."', '0')";
	$result_insert = mysql_query($query_insert) or die(mysql_error());
}
	
?>
<tr>
	<td valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td valign="top">
				<!-- CONTENT BEGIN -->
					<?php
					if($flag == 2)
					{
						include('inc_rep_ad_hoc.php');
					}
					
					$i = 0;
					$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts WHERE acct_is_active = 1";
					$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
					
					while ( $row = mysql_fetch_array($result_query_accounts) ) 
					{
						$arr_accounts_match[$i] = $row["acct_number"];
						$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";
															
						if ($str_accounts_match =='') 
						{
							$str_accounts_match = "'".$row["acct_number"]."'";
						} 
						else 
						{
							$str_accounts_match = $str_accounts_match.",'".$row["acct_number"]."'";
						}
						$i = $i + 1;
					}
					
					$no_acct = 0;
					$no_tick = 0;
					$no_list = 0;
		
					//Date in YYYY-MM-DD Format
					$trade_date_to_process = previous_business_day();
					xdebug("Trade Date to process",$trade_date_to_process);

					//START IF 1
					if(count($acctSelected) > 0)
					{
						$acct_query = "acct_number = '1111112323111122331111111' ";
						for($a = 0; $a < count($acctSelected); $a++)
						{
							$acct_query.= " OR acct_number = '".$acctSelected[$a]."' "; 
						}
						
						$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts where acct_is_active = 1 and (".$acct_query.")";
						$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
						$i = 0;
						while ( $row = mysql_fetch_array($result_query_accounts) ) 
						{
							$arr_accounts_match[$i] = $row["acct_number"];
							$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";									
							if ($str_accounts_match =='') 
							{
								$str_accounts_match = "'".$row["acct_number"]."'";
							} 
							else 
							{
								$str_accounts_match = $str_accounts_match.",'".$row["acct_number"]."'";
							}
							$i = $i + 1;
						}
						
	
						//START IF 12
						// ACCOUNTS SELECTED
						$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and  trdm_account_number in (".$str_accounts_match.")";
						$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
					
						//START IF 13
						if(mysql_num_rows($result_trades_final) > 0)
						{
							$htmlfilebodydata .= '<tr> 
							<td colspan="8">&nbsp;</td>
							</tr>
							<tr> 
							<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>TRADES FROM SELECTED ACCOUNTS</b></font></td>
							</tr>
							<tr><td colspan="8">&nbsp;</td></tr>
							<tr> 
							<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;<u>Symbol</u>&nbsp;&nbsp;</font></td>
							<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
							</tr>';
		
							while ( $row_trades_final = mysql_fetch_array($result_trades_final) ) 
							{
								$htmlfilebodydata .='<tr> 
								<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_account_number"].'</font></td>
								<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;'.$row_trades_final["trdm_symbol"].'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row_trades_final["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_quantity"].'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_price"].'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row_trades_final["trdm_quantity"]*$row_trades_final["trdm_price"]).'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
								<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row_trades_final["trdm_account_number"]].'</font></TD>
								</tr>';
							}
						} // END IF 13
						else
						{
							$htmlfilebodydata .= '<tr> 
							<td colspan="8">&nbsp;</td>
							</tr>
							<tr> 
							<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b> NO TRADES </b> </font></td>
							</tr>';
						}
					} // END IF 1
					else
					{
						$no_acct = 1;
					}
						
					// START IF 2
					if(count($tickSelected) > 0)
					{
						$holding_proceed = 0;
						$symbol_list = "'sdfsdfsfsdf'";
						
						for($a = 0; $a < count($tickSelected); $a++)
						{
							$symbol_list.= ",'".$tickSelected[$a]."'"; 
						}
						
						
						// TICKERS SELECTED
						$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_list.")";
						$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
						
	/*					$i = 0;
						while ( $row = mysql_fetch_array($result_trades_final) ) 
						{
							$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts WHERE acct_number = '".$row['trdm_account_number']."' AND acct_is_active = 1";
							$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
							$row = mysql_fetch_array($result_query_accounts);

							$arr_accounts_match[$i] = $row["acct_number"];
							$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";									
							$i++;
						}
		*/
						
		//				$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
						//START IF 15
						if(mysql_num_rows($result_trades_final) > 0)
						{
							//LINE BREAK
							$htmlfilebodydata .= '<tr><td colspan="8"><br><br></td></tr>';
						
							$htmlfilebodydata .= '<tr> 
							<td colspan="8">&nbsp;</td>
							</tr>
							<tr> 
							<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>TRADES FROM SELECTED TICKERS</b></font></td>
							</tr>
							<tr><td colspan="8">&nbsp;</td></tr>
							<tr> 
							<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;<u>Symbol</u>&nbsp;&nbsp;</font></td>
							<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
							<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
							</tr>';
		
							while ( $row_trades_final = mysql_fetch_array($result_trades_final) ) 
							{
								$htmlfilebodydata .='<tr> 
								<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_account_number"].'</font></td>
								<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;'.$row_trades_final["trdm_symbol"].'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row_trades_final["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_quantity"].'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_price"].'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row_trades_final["trdm_quantity"]*$row_trades_final["trdm_price"]).'&nbsp;&nbsp;</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
								<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row_trades_final["trdm_account_number"]].'</font></TD>
								</tr>';
							}
						} // END IF 15
						else
						{
							$htmlfilebodydata .= '<tr> 
							<td colspan="8">&nbsp;</td>
							</tr>
							<tr> 
							<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b> NO TRADES </b> </font></td>
							</tr>';
						}
					} // END IF 2
					else
					{
						$no_tick = 1;
					}

					//START IF 3
					if(count($listSelected) > 0)
					{
						$s1_list = array();
						$s2_list = array();
						$a1_list = array();
						$a2_list = array();
						$u1_list = array();
						$u2_list = array();
						
						$s_proceed = 0;
						$a_proceed = 0;
						$u_proceed = 0;
						
						$query_list_system = "SELECT slis_auto_id, slis_list_type_id FROM slis_system_lists WHERE slis_isactive = '1' ";
						$result_list_system = mysql_query($query_list_system) or die(mysql_error());
						while($row_list_system = mysql_fetch_array($result_list_system))
						{
							$s1_list[] = $row_list_system['slis_list_type_id']; 
							$s2_list[] = $row_list_system['slis_auto_id'];
						}
			
						$query_list_admin = "SELECT alis_auto_id, alis_list_type_id FROM alis_admin_lists WHERE alis_isactive = '1' ";
						$result_list_admin = mysql_query($query_list_admin) or die(mysql_error());
						while($row_list_admin = mysql_fetch_array($result_list_admin))
						{
							$a1_list[] = $row_list_admin['alis_list_type_id'];
							$a2_list[] = $row_list_admin['alis_auto_id'];
						}
	
						$query_list_user = "SELECT usli_auto_id, usli_list_type_id FROM usli_user_lists WHERE usli_isactive = '1' AND usli_user_id = '".$user_id."'";
						$result_list_user = mysql_query($query_list_user) or die(mysql_error());
						while($row_list_user = mysql_fetch_array($result_list_user))
						{
							$u1_list[] = $row_list_user['usli_list_type_id'];
							$u2_list[] =  $row_list_user['usli_auto_id'];
						}
						
						$s_query = " slis_auto_id = 'sdfsfs' ";
						$a_query = " alis_auto_id = 'sdfsfs' ";
						$u_query = " usli_auto_id = 'sdfsfs' ";
						
						for($i = 0; $i < count($listSelected); $i++)
						{
							list($list_type, $auto_id) = explode(",", $listSelected[$i]);
							
							if(in_array($list_type, $s1_list) AND in_array($auto_id, $s2_list))
							{
								$s_query = $s_query. " OR slis_auto_id = '".$auto_id."' ";
								$s_proceed = 1;
							}
							
							if(in_array($list_type, $a1_list) AND in_array($auto_id, $a2_list))
							{
								$a_query = $a_query. " OR alis_auto_id = '".$auto_id."' ";
								$a_proceed = 1;
							}
							
							if(in_array($list_type, $u1_list) AND in_array($auto_id, $u2_list))
							{
								$u_query = $u_query. " OR usli_auto_id = '".$auto_id."' ";
								$u_proceed = 1;
							}
						}

						
/********************** START SYSTEM LISTS (MONEYMAKER, ANALYST, BANKER) ***************************************************************************************/
						// START IF 4
						if($s_proceed == 1)
						{
							$query_list_types = "SELECT slis_auto_id, slis_title_name FROM slis_system_lists WHERE (".$s_query.") AND slis_isactive = '1' ORDER BY slis_auto_id";
							$result_list_types = mysql_query($query_list_types) or die(mysql_error());
							//START WHILE 1
							while($row_list_types = mysql_fetch_array($result_list_types))
							{
								//START IF 5
								if($row_list_types['slis_auto_id'] != '4')
								{
									$query_symbols_on_list = "SELECT syll_symbol FROM syll_system_list_lists WHERE syll_id = '".$row_list_types['slis_auto_id']."' AND syll_isactive = 1";
									xdebug("query_symbols_on_list", $query_symbols_on_list );
									$result_symbols_on_list = mysql_query($query_symbols_on_list) or die (mysql_error());
	
									//START IF 6
									if(mysql_num_rows($result_symbols_on_list) > 0)
									{
										$i = 0;
										$symbol_string = '';
										while($row_symbols_on_list = mysql_fetch_array($result_symbols_on_list)) 
										{
											$symbols_on_list[$i] = $row_symbols_on_list["syll_symbol"];
											if ($symbol_string=='') 
											{
												$symbol_string = "'".$row_symbols_on_list["syll_symbol"]."'";
											} 
											else 
											{
												$symbol_string = $symbol_string.",'".$row_symbols_on_list["syll_symbol"]."'";
											}
											$i = $i + 1;
										}
										xdebug("symbol_string",$symbol_string);
										
								/*		if($no_acct == 1)
										{
											$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.")";
											$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
										
										 	$i = 0;
											while ( $row = mysql_fetch_array($result_query_trades_final) ) 
											{
											
												$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts WHERE acct_number = '".$row['trdm_account_number']."' AND acct_is_active = 1";
												$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
												
												while ( $row = mysql_fetch_array($result_query_accounts) ) 
												{
													$arr_accounts_match[$i] = $row["acct_number"];
													$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";									
													$i++;
												}
											}
											$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
										}
										else
										{
											$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.")";
											$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
										}
								*/
								
										$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' AND trdm_symbol in (".$symbol_string.") AND trdm_account_number in (".$str_accounts_match.")";
										$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());

										if(mysql_num_rows($result_query_trades_final) > 0)
										{
											$htmlfilebodydata .= '<tr> 
											<td colspan="8">&nbsp;</td>
											</tr>
											<tr> 
											<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['slis_title_name'].'</b></font></td>
											</tr>
											<tr> 
											<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
											<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;<u>Symbol</u>&nbsp;&nbsp;</font></td>
											<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
											<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
											<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
											<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
											<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
											<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
											</tr>';
																					
											while ( $row = mysql_fetch_array($result_query_trades_final) ) 
											{
												//$arr_accounts[$i] = $row["trdm_account_number"];
												
												$htmlfilebodydata .='<tr> 
												<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_account_number"].'</font></td>
												<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;'.$row["trdm_symbol"].'&nbsp;&nbsp;</font></td>
												<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
												<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_quantity"].'&nbsp;&nbsp;</font></td>
												<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_price"].'&nbsp;&nbsp;</font></td>
												<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'&nbsp;&nbsp;</font></td>
												<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
												<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row["trdm_account_number"]].'</font></TD>
												</tr>';
											}
										}
										else
										{
											$htmlfilebodydata .= '<tr> 
											<td colspan="8">&nbsp;</td>
											</tr>
											<tr> 
											<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['slis_title_name'].'</b> (No Trades)</font></td>
											</tr>';
										}
									} //END IF 6
									else 
									{
										//Add to content $rep_content_emp_trades (no trades)
										$htmlfilebodydata .= '<tr> 
										<td colspan="8">&nbsp;</td>
										</tr>
										<tr> 
										<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['slis_title_name'].'</b> (No Trades)</font></td>
										</tr>';
									}
								} // END IF 5
								else
								{
								/*
									$acct_query = "acct_number = '1111112323111122331111111' ";
									for($a = 0; $a < count($acctSelected); $a++)
									{
										$acct_query.= " OR acct_number = '".$acctSelected[$a]."' "; 
									}
								
									
									$symbol_string = "'sdfsdfsdf'";
									for($a = 0; $a < count($tickSelected); $a++)
									{
										$symbol_string = $symbol_string . ",'".$tickSelected[$a]."'"; 
									}
									*/
									$flag = 1;
									include('holding_period.php');
								}
							}// END WHILE 1
						} // END IF 4
/********************************************  END SYSTEM LISTS  ******************************************************************************************/					
						
/******************************************  START ADMIN LISTS  *******************************************************************************************/					
						// START IF 7
						if($a_proceed == 1)
						{
							$query = " adll_symbol = 'sdfsfsd' ";
							for($a = 0; $a < count($tickSelected); $a++)
							{
								$query = $query . "OR adll_symbol = '".$tickSelected[$a]."' "; 
							}
							
							$query_list_types = "SELECT alis_auto_id, alis_title_name FROM alis_admin_lists WHERE (".$a_query.") AND alis_isactive = '1' ORDER BY alis_auto_id";
							$result_list_types = mysql_query($query_list_types) or die(mysql_error());
							//START WHILE 2
							while($row_list_types = mysql_fetch_array($result_list_types))
							{
								$query_symbols_on_list = "SELECT adll_symbol FROM adll_admin_list_lists WHERE adll_id = '".$row_list_types['alis_auto_id']."' AND adll_isactive = 1";
								xdebug("query_symbols_on_list", $query_symbols_on_list );
								$result_symbols_on_list = mysql_query($query_symbols_on_list) or die (mysql_error());
						
								//START IF 8
								if(mysql_num_rows($result_symbols_on_list) > 0)
								{
									$i = 0;
									$symbol_string = '';
									while($row = mysql_fetch_array($result_symbols_on_list)) 
									{
										$symbols_on_list[$i] = $row["adll_symbol"];
										if ($symbol_string=='') 
										{
											$symbol_string = "'".$row["adll_symbol"]."'";
										} 
										else 
										{
											$symbol_string = $symbol_string.",'".$row["adll_symbol"]."'";
										}
										$i = $i + 1;
									}
									xdebug("symbol_string",$symbol_string);
						
						/*
									if($no_acct == 1)
									{		
										$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."'";
										$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
									
										$i = 0;
										while ( $row = mysql_fetch_array($result_query_trades_final) ) 
										{
											$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts WHERE acct_number = '".$row['trdm_account_number']."' AND acct_is_active = 1";
											$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
											
											while ( $row = mysql_fetch_array($result_query_accounts) ) 
											{
												$arr_accounts_match[$i] = $row["acct_number"];
												$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";									
												$i++;
											}
										}
										$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
									}
									else
									{
										$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."'";
										$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
									}
		*/							
		
									$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") AND trdm_account_number in (".$str_accounts_match.")";
									$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
									
									if(mysql_num_rows($result_query_trades_final) > 0)
									{
										$htmlfilebodydata .= '<tr> 
										<td colspan="8">&nbsp;</td>
										</tr>
										<tr> 
										<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['alis_title_name'].'</b></font></td>
										</tr>
										<tr> 
										<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;<u>Symbol</u>&nbsp;&nbsp;</font></td>
										<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
										</tr>';
																				
										while ( $row = mysql_fetch_array($result_query_trades_final) ) 
										{
											$htmlfilebodydata .='<tr> 
											<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_account_number"].'</font></td>
											<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;'.$row["trdm_symbol"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_quantity"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_price"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
											<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row["trdm_account_number"]].'</font></TD>
											</tr>';
										}
									}
									else
									{
										$htmlfilebodydata .= '<tr> 
										<td colspan="8">&nbsp;</td>
										</tr>
										<tr> 
										<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['alis_title_name'].'</b> (No Trades)</font></td>
										</tr>';
									}
								} // END IF 8
								else 
								{
									//Add to content $rep_content_emp_trades (no trades)
									$htmlfilebodydata .= '<tr> 
									<td colspan="8">&nbsp;</td>
									</tr>
									<tr> 
									<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['alis_title_name'].'</b> (No Trades)</font></td>
									</tr>';
								}
							} // END WHILE 2
						} // END IF 7
/********************************************  END ADMIN LISTS  ***************************************************************************************/					

/********************************************  START USER LISTS  **************************************************************************************/					
						//START IF 9
						if($u_proceed == 1)
						{
							$query = " usll_symbol = 'sdfsfsd' ";
							for($a = 0; $a < count($tickSelected); $a++)
							{
								$query = $query . "OR usll_symbol = '".$tickSelected[$a]."' "; 
							}
							
							$query_user_list = "SELECT usli_auto_id, usli_title_name FROM usli_user_lists WHERE usli_user_id = '".$user_id."' AND (".$u_query.") AND usli_isactive = '1' ORDER BY usli_auto_id";
							$result_user_list = mysql_query($query_user_list) or die(mysql_error());
							//START WHILE 3
							while($row_user_list = mysql_fetch_array($result_user_list))
							{
								//Get tickers on list
								$query_user_symbols = "SELECT usll_symbol FROM usll_user_list_lists WHERE usll_list_id = '".$row_user_list['usli_auto_id']."' AND usll_isactive = 1";
								xdebug("query_user_symbols", $query_user_symbols );
								$result_user_symbols = mysql_query($query_user_symbols) or die(mysql_error());
							
								//START IF 11
								if(mysql_num_rows($result_user_symbols) > 0)
								{
									$i = 0;
									$symbol_string = '';
									while($row_user_symbols = mysql_fetch_array($result_user_symbols)) 
									{
										$symbols_on_list[$i] = $row_user_symbols["usll_symbol"];
										if ($symbol_string=='') 
										{
											$symbol_string = "'".$row_user_symbols["usll_symbol"]."'";
										} 
										else 
										{
											$symbol_string = $symbol_string.",'".$row_user_symbols["usll_symbol"]."'";
										}
										$i = $i + 1;
									}
									xdebug("symbol_string",$symbol_string);

/*
									if($no_acct == 1)
									{
										$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."'";
										$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
										
										$i = 0;
										while ( $row_trades_final = mysql_fetch_array($result_trades_final) ) 
										{
											$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts WHERE acct_number = '".$row_trades_final['trdm_account_number']."' AND acct_is_active = 1";
											$result_accounts = mysql_query($query_accounts) or die (mysql_error());
											
											while ( $row_accounts = mysql_fetch_array($result_accounts) ) 
											{
												$arr_accounts_match[$i] = $row_accounts["acct_number"];
												$arr_get_account_detail[$row_accounts["acct_number"]] = $row_accounts["acct_name1"]." (".$row_accounts["acct_rep"].")";									
												$i++;
											}
										}
										$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
									}
									else
									{
										$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and  trdm_account_number in (".$str_accounts_match.")";
										$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
									}
*/

									$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") AND trdm_account_number in (".$str_accounts_match.")";
									$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
								
									if(mysql_num_rows($result_trades_final) > 0)
									{
										$htmlfilebodydata .= '<tr> 
										<td colspan="8">&nbsp;</td>
										</tr>
										<tr> 
										<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_user_list['usli_title_name'].'</b></font></td>
										</tr>
										<tr> 
										<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;<u>Symbol</u>&nbsp;&nbsp;</font></td>
										<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
										</tr>';
					
										while ( $row_trades_final = mysql_fetch_array($result_trades_final) ) 
										{
											$htmlfilebodydata .='<tr> 
											<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_account_number"].'</font></td>
											<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;'.$row_trades_final["trdm_symbol"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row_trades_final["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_quantity"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_price"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row_trades_final["trdm_quantity"]*$row_trades_final["trdm_price"]).'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
											<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row_trades_final["trdm_account_number"]].'</font></TD>
											</tr>';
										}
									}
									else
									{
										$htmlfilebodydata .= '<tr> 
										<td colspan="8">&nbsp;</td>
										</tr>
										<tr> 
										<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_user_list['usli_title_name'].'</b> (No Trades)</font></td>
										</tr>';
									}
								} // END IF 11
								else
								{
									$htmlfilebodydata .= '<tr> 
									<td colspan="8">&nbsp;</td>
									</tr>
									<tr> 
									<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_user_list['usli_title_name'].'</b> (No Trades)</font></td>
									</tr>';
								}
							} // END WHILE 3
						} // END IF 9

/******************************************  END USER LISTS  *******************************************************************************************/					
					} // END IF 3
					//START ELSE
					else
					{
						//NO ACCOUNTS AND NO TICKERS SELECTED
						if($no_acct == 1 AND $no_tick == 1)
						{
							$htmlfilebodydata .= '<tr> 
							<td colspan="8">&nbsp;</td>
							</tr>
							<tr> 
							<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b> NOTHING SELECTED </b> </font></td>
							</tr>';
						
						}

						$no_list = 1;
					} // END IF 3
								
					//CREATE HTML OUTPUT FILE
					// BEGIN 1
					$htmlfiledata = rep_header_emp_trades($trade_date_to_process);
					$htmlfiledata .= $htmlfilebodydata;															
					$htmlfiledata .= rep_footer_emp_trades();
					$str_filename = "DYNAMIC_".$trade_date_to_process.".html";
					$str_pdfname = "DYNAMIC_".$trade_date_to_process.".pdf";
					$fp = fopen($exportlocation.$str_filename, "w");
					fputs ($fp, $htmlfiledata);
					fclose($fp);
						
					//PDF FORMAT
					if($format == '2')
					{
						shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							
						xdebug("File Written and PDF Created",$str_filename);
						$fileattach = $str_pdfname;
						
						xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
						$mailsubject = str_replace("\\","",$name)." Report for ". $trade_date_to_process;
						$email_heading = str_replace("\\","",$name)." Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
						xdebug("email_heading",$email_heading);
						$control_id = gen_control_number();
						$mailbodysubinfo = str_replace("\\","",$name).' Report';
						html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
					}
					else
					//HTML FORMAT
					if($format == '3')
					{
						$fileattach = $str_filename;

						xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
						$mailsubject = str_replace("\\","",$name)." Report for ". $trade_date_to_process;
						$email_heading = str_replace("\\","",$name)." Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
						xdebug("email_heading",$email_heading);
						$control_id = gen_control_number();
						$mailbodysubinfo = str_replace("\\","",$name).' Report';
						html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
					}
					//LINK FORMAT
					else
					{
						$htmlfiledata = rep_header_emp_trades($trade_date_to_process);
						$htmlfiledata .= $htmlfilebodydata;															
						$htmlfiledata .= rep_footer_emp_trades();
						
						$fileattach = '';
						
						xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
						$mailsubject = str_replace("\\","",$name)." Report for ". $trade_date_to_process;
						$email_heading = str_replace("\\","",$name)." Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
						xdebug("email_heading",$email_heading);
						$control_id = gen_control_number();
						$mailbodysubinfo = str_replace("\\","",$name).' Report <BR><BR>';
						$mailbodysubinfo.= $htmlfiledata;
						html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
					}
					?>
		
					<? echo $table_start; ?>
					<p class="links12"><?=$email_heading?></p>
					<!-- <p class="links12">The report (PDF Format) has been generated and a copy of it has been sent to your email account on file (<?=$user_email?>).</p> -->
					<p class="links12">You may also access the report <a href="./data/exports/<?=$str_pdfname?>" target="_blank">HERE</a></p>
					<? echo $table_end; ?>
					<!-- The following added so the top menu will be visible and not hidden behing the iframe -->
					<br>
			
					<? echo $table_start;
					//PDF FRAME
					if($format == '2')
					{
					?>
						<script language="javascript">
						//Specify display mode (0 or 1)
						//0 causes document to be displayed in an inline frame, while 1 in a new browser window
						var displaymode=0
						//if displaymode=0, configure inline frame attributes (ie: dimensions, intial document shown
						
						var iframecode='<iframe id="external" style="width:100%;height:600px" src="./data/exports/<?=$str_pdfname?>"></iframe>'
						/////DO NOT EDIT BELOW HERE////////////
						if (displaymode==0)
						document.write(iframecode)
						//-->
						</script>
					<?
					}
					else
					//HTML FRAME 
					if($format == '3')
					{
					?>
						<script language="javascript">
						//Specify display mode (0 or 1)
						//0 causes document to be displayed in an inline frame, while 1 in a new browser window
						var displaymode=0
						//if displaymode=0, configure inline frame attributes (ie: dimensions, intial document shown
						
						var iframecode='<iframe id="external" style="width:100%;height:600px" src="./data/exports/<?=$str_filename?>"></iframe>'
						/////DO NOT EDIT BELOW HERE////////////
						if (displaymode==0)
						document.write(iframecode)
						//-->
						</script>
					<?
					}
					//LINK FRAME
					else
					{
					?>
						<script language="javascript">
						//Specify display mode (0 or 1)
						//0 causes document to be displayed in an inline frame, while 1 in a new browser window
						var displaymode=0
						//if displaymode=0, configure inline frame attributes (ie: dimensions, intial document shown
						
						var iframecode='<iframe id="external" style="width:100%;height:600px" src="./data/exports/<?=$str_filename?>"></iframe>'
						/////DO NOT EDIT BELOW HERE////////////
						if (displaymode==0)
						document.write(iframecode)
						//-->
						</script>
					<?
					}
					echo $table_end; 
					?>
					<br>						
					<?
					xdebug("Trade Report for trade date ".$trade_date_to_process." sent successfully via email!",0);
					?>
					<!-- CONTENT END -->
					
				</td>
			</tr>
		</table>
	</td>
</tr>

<?
if($flag != 2)
{
?>
<tr>
	<td align="right">
		<? echo $table_start; ?>
		
		<table align="right">
			<tr>
				<form action="ad_hoc_rep.php" method="post" enctype="multipart/form-data" name="frm_edit">
				<td align="right">
					<input name="name" type="hidden" value="<?=$name?>">
					<input name="edit" class="Submit" type="submit" value=" Edit ">
				</td>
				</form>
				
				<td align="right">&nbsp;</td>
				
				<form action="ad_hoc_rep.php" method="post" enctype="multipart/form-data" name="frm_save">
				<td align="right">
					<input name="save" class="Submit" type="submit" value=" Save " onClick="alert('Report Saved!'); ">
				</td>
				</form>
			</tr>
		</table>
		<? echo $table_end; ?>
	</td>
</tr>
<?
}
?>

<?
function rep_header_emp_trades ($trade_date_to_process)
{
	$str_rep_datetime = str_replace("\\","",$name)." Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
	
	return 
	'<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
	<!-- MEDIA BOTTOM 0.5in --> 
	<!-- MEDIA LANDSCAPE "NO" --> 
	<!-- MEDIA LEFT 0.5in --> 
	<!-- MEDIA RIGHT 0.5in --> 
	<!-- MEDIA SIZE "Letter" --> 
	<!-- MEDIA TOP 0.1in --> 
	<!-- MEDIA TYPE "Plain" -->	
	<table width="640" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" height="14" bgcolor="#FFFFFF">
	<table width="640">
	<tr> 
	<td><img src="http://192.168.1.252/dev/demo_compliance/images/compliancelogo.gif"></td>
	<td valign="top" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2"><b>CompSys v 2.0 (Demo)</b></font></td>
	</tr>
	<tr>
	<td colspan=2><img src="http://192.168.1.252/dev/demo_compliance/images/grey_red_bar.gif" border="0"></td>
	</tr>
	<tr>
	<td colspan=2><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$str_rep_datetime.'</font></td>
	</tr>
	<tr>
	<td colspan=2><img src="http://192.168.1.252/dev/demo_compliance/images/gray_black_bar.gif" border="0"></td>
	</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td valign="top">
	<!-- Begin Trades Data -->
	<table width="100%"  border="0" cellspacing="1" cellpadding="1">';
}

function rep_footer_emp_trades ()
{
	return 	
	'</table>
	<!-- End Trades Data -->
	</td>
	</tr>
	
	<tr>
	<td valign="top" bgcolor="#FFFFFF"><tr>
	<td align="left" valign="top">
	</td>
	</tr>
	<tr>
	<td>
	</td>
	</tr>
	</table>
	</body>
	</html>';
}		

?>

<?php
include('bottom.php');
?>