<?php

  //include('top.php');
	include('includes/dbconnect.php');
	include('includes/global.php');
	include('includes/functions.php');  
	
	//Date in YYYY-MM-DD Format
	$trade_date_to_process = previous_business_day();
	
	echo "Trade Date processed: ".$trade_date_to_process."<BR>";
	

////
//Check if trades exist and only then send emails
//If trades do not exist, there has been one or more errors in the import/upload of trades

		$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$trade_date_to_process."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_num_trades) ) {
			$numtrades_val = $row["numtrades"];
		}
	
	//echo $numtrades_val."<BR>";
							
	if ($numtrades_val > 0) {						
						
						//********************************************************************************************************************************************
						//Get Employee Accounts data in a local variable
						$result = mysql_query("SELECT acct_number FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());
						
						$i = 0;
						$arr_accounts = array();
						
							while ( $row = mysql_fetch_array($result) ) {
						 
										$arr_accounts[$i] = $row["acct_number"];
										$i = $i+1;
							}
						//print_r($arr_accounts);
						
						//Get Employee Names on account
						$result1 = mysql_query("SELECT acct_number, concat( acct_name1, ' , REP: ', acct_rep) as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());
						
						$i = 0;
						$arr_accountnames = array();
						
							while ( $row = mysql_fetch_array($result1) ) {
						 
										$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
										$i = $i+1;
							}
						//print_r($arr_accounts);
						
						//*********************************************************************************************************************************************
						
						$reportmailbody = '<tr>
						<td align="left" valign="top">
							<table width="100%" cellpadding="5" cellspacing="5" border="1">
								<tr valign="top">
								<td>';
						
										//$date = date("Y-m-d");
										//$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
						
										if ($trdm_trade_date != '') { 
										$str_trdm_trade_date = " where trdm_trade_date = '". $trdm_trade_date ."'";
										} else {
										$str_trdm_trade_date = " where trdm_trade_date = '". $trade_date_to_process ."'";
										}			  
						
										if ($trdm_symbol != '') { 
										$str_trdm_symbol = " and trdm_symbol = '".$trdm_symbol."'";
										} else {
										$str_trdm_symbol = " and trdm_symbol != '' and LENGTH(trdm_symbol) < 8";
										}			  
						
										if ($trdm_account_number != '') { 
										$str_trdm_account_number = " and trdm_account_number = '".$trdm_account_number."'";
										} else {
										$str_trdm_account_number = " and trdm_account_number not like '0000%'";
										}			  
						
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
																						 "ORDER BY trdm_symbol, trdm_trade_time";	
												
											//echo $query_statement;
											//exit;
											$result = mysql_query($query_statement) or die (mysql_error());
											
											$reportmailbody .= '<!--Table with thin cell border-->
											<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#FFFFFF">
												<tr>
												<td>
													<table class="tablewithdata" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
														<tr class="tableheading"> 
														<td >Acct.</td>  
														<td align="right">Symbol&nbsp;&nbsp;</td>
														<td >Description</td>
														<td >B/S</td>
														<td align="right">Qty.&nbsp;&nbsp;</td>
														<td align="right">Price&nbsp;&nbsp;</td>
														<td align="right">Total&nbsp;&nbsp;</td>
														<td align="right">Time&nbsp;&nbsp;</td>
														<td align="center" valign="middle" >NAME</td>
														</tr>';
											
											while ( $row = mysql_fetch_array($result) ) {
						
											if ($emp_trades != 1) {

													if (in_array($row["trdm_account_number"], $arr_accounts)) {
													$reportmailbody .= '<tr class="tablerowhighlight">';
													$reportmailbody .= '<td nowrap><u>'.$row["trdm_account_number"].'</u></td>
																							<td nowrap align="right"><u>'.$row["trdm_symbol"].'</u>&nbsp;&nbsp;</td>
																							<td nowrap><u>'.$row["trdm_sec_description"].'</u></td>
																							<td nowrap align="right"><u>'.offset_buy_sell(convert_buy_sell($row["trdm_buy_sell"])).'</u>&nbsp;&nbsp;&nbsp;</td>
																							<td nowrap align="right"><u>'.$row["trdm_quantity"].'</u>&nbsp;&nbsp;</td>
																							<td nowrap align="right"><u>'.$row["trdm_price"].'</u>&nbsp;&nbsp;</td>
																							<td nowrap align="right"><u>'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'</u>&nbsp;&nbsp;</td>
																							<td nowrap align="right"><u>'.$row["trdm_trade_time"].'</u>&nbsp;&nbsp;</td>';
						
													} else {
													$reportmailbody .= '<tr class="tablerow">';
													$reportmailbody .= '<td nowrap>'.$row["trdm_account_number"].'</td>
																							<td nowrap align="right">'.$row["trdm_symbol"].'&nbsp;&nbsp;</td>
																							<td nowrap>'.$row["trdm_sec_description"].'</td>
																							<td nowrap align="right">'.offset_buy_sell(convert_buy_sell($row["trdm_buy_sell"])).'&nbsp;&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.$row["trdm_quantity"].'&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.$row["trdm_price"].'&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.$row["trdm_trade_time"].'&nbsp;&nbsp;</td>';
													}
													
																							
													$reportmailbody .= '<TD nowrap>';
													
													if (in_array($row["trdm_account_number"], $arr_accounts)) {
													$reportmailbody .= '<a><u>'.$arr_accountnames[$row["trdm_account_number"]].'</u></a>';
													} else {
													$reportmailbody .= '&nbsp;';					
													}
													
													$reportmailbody .= '</TD>';
													$reportmailbody .= '</tr>';
												
						
											} else {
						
													if (in_array($row["trdm_account_number"], $arr_accounts)) {
													$reportmailbody .= '<tr class="tablerowhighlight">';
													}
													
													$reportmailbody .= '<td nowrap>'.$row["trdm_account_number"].'</td>
																							<td nowrap align="right">'.$row["trdm_symbol"].'&nbsp;&nbsp;</td>
																							<td nowrap>'.$row["trdm_sec_description"].'</td>
																							<td nowrap align="right">'.offset_buy_sell(convert_buy_sell($row["trdm_buy_sell"])).'&nbsp;&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.$row["trdm_quantity"].'&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.$row["trdm_price"].'&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'&nbsp;&nbsp;</td>
																							<td nowrap align="right">'.$row["trdm_trade_time"].'&nbsp;&nbsp;</td>';
													
													$reportmailbody .='<td nowrap>';
													
													if (in_array($row["trdm_account_number"], $arr_accounts)) {
													$reportmailbody .= '<a>'.$arr_accountnames[$row["trdm_account_number"]].'</a>';
													} else {
													$reportmailbody .= '&nbsp;';					
													}
													$reportmailbody .= '</td>';
						
													$reportmailbody .= '</tr>';
						
													} 
											
											}
								 
									$reportmailbody .= '</table>
																	</td>
																	</tr>
																</table>
																<!--Table with thin cell border ends-->
													</td>
													</tr>
												</table>
											</td>
											</tr>';
						
							//Sending link of Trades Report file via email
							echo "<BR>Creating Trades Report file...<BR>";
							//create_trade_report("pprasad@tocqueville.com","2Trades Report for (".$trade_date_to_process.")", $reportmailbody, "Trades Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a"));
							sys_mail("prasad_pravin@yahoo.com","Trades Report for (".$trade_date_to_process.")", $reportmailbody, "Trades Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a"));
							echo "<BR>Created Trades Report file successfully...<BR>";
							

							$linkmailbody = "Please click on the following link to access the Trades Report for ".$trade_date_to_process."\n";
							$linkmailbody .= "\n\nhttp://10.10.10.144/compliance/data/exports/Trades_Report_".$trade_date_to_process.".html";
							$linkmailbody .= "\n\n\nTDW Mailer";
							sleep(1);
							$var_value = $linkmailbody;
							//mail(email_report_to(),'Trades Report for Date ('.$trade_date_to_process.')',$var_value,"From: compliance@tocqueville.com <compliance@tocqueville.com>","-fcompliance@tocqueville.com");
							sleep(1);
						
							echo "Trade Report for trade date ".$trade_date_to_process." sent successfully via email!";
							
			} else {
			echo "Trade Report was not sent because no trades were found for trade date ".$trade_date_to_process."<BR>";
			echo "possibly because there were errors in the trade upload. Please try the trade upload again and if<BR>";
			echo "the problem persists please contact Technical Support at support@centersysgroup.com.<BR>";
			}
?>

