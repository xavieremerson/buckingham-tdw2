<?php
include('top.php');
include('includes/functions.php'); 
//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CREATE MECHANISM TO HANDLE IT.
//$trade_date_to_process = format_date_ymd_to_mdy(previous_business_day());
$trade_date_to_process = previous_business_day();
//$trade_date_to_process = '2004-02-20';
	
?>

<tr>
	<td valign="top">
		<!-- START TABLE 1 -->
		<table width="100%" border="0" cellspacing="0" cellpadding="10">
			<tr> 
				<td valign="top">
					<!-- CONTENT BEGIN -->
					<!-- START TABLE 2 -->
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr valign="top">
							<td valign="top">
								<? table_start_percent(50,"Historical Reports"); ?>
								<!-- START TABLE 3 -->
								<table width="100%" border="0" cellspacing="0" cellpadding="10">
									<tr> 
										<td>
											<? table_start_percent(50, "Trades Report"); ?>
											<!-- START TABLE 4 -->
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td valign="baseline">
													<form action="<?=$PHP_SELF?>?action=filter" id="filtertrade" method="post"> 
														<br>&nbsp;&nbsp;<select class="Text" name="trdm_trade_date1" size="1" >
														<option value="">&nbsp;&nbsp;&nbsp;TRADE DATE&nbsp;&nbsp;&nbsp;</option>
														<option value="">==========</option>
														<?
														
														$i = 1;
														while ($i < 90) 
														{
															$previoustime = time() - (60*60*24*$i);
															$previousday = date("Y-m-d", $previoustime);
															
															if (date("l", $previoustime) == "Sunday") 
															{
																$previoustime = time() - (60*60*24*($i+2));
																$previousday = date("Y-m-d", $previoustime);
																$i = $i+2 + 1;	
																if ( check_holiday($previousday) == 1) 
																{						
																	$previoustime = time() - (60*60*24*($i));
																	$previousday = date("Y-m-d", $previoustime);
																	$i = $i+1;	
																}
															} 
															elseif (date("l", $previoustime) == "Monday" and check_holiday($previousday) == 1) 
															{
																$previoustime = time() - (60*60*24*($i+3));
																$previousday = date("Y-m-d", $previoustime);
																$i = $i+3 + 1;	
															} 
															else 
															{
																$previousday = "ERROR!";
																$i = $i+1;						
															}
															?>
															<option value="<?=date("Y-m-d", time() - (60*60*24*($i-1)))?>"><?=date("m/d/Y", time() - (60*60*24*($i-1)))?></option>
														<?
														}						
														?>
														</select>
														<input class="Submit" name="submit1" type="submit" value="  Get Report  ">
													</form>
													</td>
												</tr>
											</table>
											<!-- END TABLE 4 -->
											<? table_end_percent(); ?>
										</td>
										</tr>
										<tr><td>&nbsp;</td></tr>
										<tr>
										<td>
											<? table_start_percent(50, "Compliance Report"); ?>
											<!-- START TABLE 5 -->
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td valign="baseline">
													<form action="<?=$PHP_SELF?>?action=filter" id="filtertrade" method="post"> 
														<br>&nbsp;&nbsp;<select class="Text" name="trdm_trade_date2" size="1" >
															<option value="">&nbsp;&nbsp;&nbsp;TRADE DATE&nbsp;&nbsp;&nbsp;</option>
															<option value="">==========</option>
															<?
															
															$i = 1;
															while ($i < 90) 
															{
																$previoustime = time() - (60*60*24*$i);
																$previousday = date("Y-m-d", $previoustime);
															
																if (date("l", $previoustime) == "Sunday") 
																{
																	$previoustime = time() - (60*60*24*($i+2));
																	$previousday = date("Y-m-d", $previoustime);
																	$i = $i+2 + 1;	
																	if ( check_holiday($previousday) == 1) 
																	{						
																		$previoustime = time() - (60*60*24*($i));
																		$previousday = date("Y-m-d", $previoustime);
																		$i = $i+1;	
																	}
																} 
																elseif (date("l", $previoustime) == "Monday" and check_holiday($previousday) == 1) 
																{
																	$previoustime = time() - (60*60*24*($i+3));
																	$previousday = date("Y-m-d", $previoustime);
																	$i = $i+3 + 1;	
																} 
																else 
																{
																	$previousday = "ERROR!";
																	$i = $i+1;						
																}
																?>
																<option value="<?=date("Y-m-d", time() - (60*60*24*($i-1)))?>"><?=date("m/d/Y", time() - (60*60*24*($i-1)))?></option>
																<?
															}						
															?>
														</select>
														<input class="Submit" name="submit2" type="submit" value="  Get Report  ">
													</form>
													</td>
												</tr>
											</table>
											<!-- END TABLE 5 -->
											<? table_end_percent(); ?>
										</td>
									</tr>
								</table>
								<!-- END TABLE 3 -->
							<? table_end_percent(); ?>
							</td>
						</tr>
						<tr>
							<td>
							
							<?php
							
							if($submit1)
							{
								//Date in YYYY-MM-DD Format
								$trade_date_to_process = previous_business_day();

								//Date in YYYY-MM-DD Format
								$trade_date_to_process = $trdm_trade_date1;

								function rep_header_emp_trades ($trade_date_to_process)
								{
								$str_rep_datetime = "Trades Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
								
								return '<html>
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
								<td><img src="../../images/compliancelogo.gif"></td>
								<td valign="top" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2"><b>CompSys v 2.0 (Demo)</b></font></td>
								</tr>
								<tr>
								<td colspan=2><img src="../../images/grey_red_bar.gif" border="0"></td>
								</tr>
								<tr>
								<td colspan=2><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$str_rep_datetime.'</font></td>
								</tr>
								<tr>
								<td colspan=2><img src="../../images/gray_black_bar.gif" border="0"></td>
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
									return 					'</table>
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
								$result1 = mysql_query("SELECT acct_number, concat( acct_name1, '(', acct_rep,')') as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());
								
								$i = 0;
								$arr_accountnames = array();
								
								while ( $row = mysql_fetch_array($result1) ) {
								
								$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
								$i = $i+1;
								}
								//print_r($arr_accounts);
								
								//*********************************************************************************************************************************************
								
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
								
								$htmlfilebodydata .= '<tr> 
								<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>  
								<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol&nbsp;&nbsp;</u></font></td>
								<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Description</u></font></td>
								<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>B/S</u></font></td>
								<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.&nbsp;&nbsp;</u></font></td>
								<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price&nbsp;&nbsp;</u></font></td>
								<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total&nbsp;&nbsp;</u></font></td>
								<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time&nbsp;&nbsp;</u></font></td>
								<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
								</tr>';
								
								while ( $row = mysql_fetch_array($result) ) {
								
								if ($emp_trades != 1) {
								
								if (in_array($row["trdm_account_number"], $arr_accounts)) {
								$htmlfilebodydata .= '<tr>';
								$htmlfilebodydata .= '<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_account_number"].'</u></font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_symbol"].'</u></font>&nbsp;&nbsp;</td>
								<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_sec_description"].'</u></font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.offset_buy_sell(convert_buy_sell($row["trdm_buy_sell"])).'</u></font>&nbsp;&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_quantity"].'</u></font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_price"].'</u></font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'</u></font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_trade_time"].'</u></font>&nbsp;&nbsp;</td>';
								
								} else {
								$htmlfilebodydata .= '<tr>';
								$htmlfilebodydata .= '<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_account_number"].'</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_symbol"].'</font>&nbsp;&nbsp;</td>
								<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_sec_description"].'</font></td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.offset_buy_sell(convert_buy_sell($row["trdm_buy_sell"])).'</font>&nbsp;&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_quantity"].'</font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_price"].'</font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'</font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_trade_time"].'</font>&nbsp;&nbsp;</td>';
								}
								$htmlfilebodydata .= '<TD nowrap>';
								
								if (in_array($row["trdm_account_number"], $arr_accounts)) {
								$htmlfilebodydata .= '<a><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$arr_accountnames[$row["trdm_account_number"]].'</u></font></a>';
								} else {
								$htmlfilebodydata .= '&nbsp;';					
								}
								$htmlfilebodydata .= '</TD>';
								$htmlfilebodydata .= '</tr>';
								} else {
								
								if (in_array($row["trdm_account_number"], $arr_accounts)) {
								$htmlfilebodydata .= '<tr>';
								}
								
								$htmlfilebodydata .= '<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_account_number"].'</font></td> 
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_symbol"].'&nbsp;&nbsp;</font></td>
								<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_sec_description"].'</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.offset_buy_sell(convert_buy_sell($row["trdm_buy_sell"])).'</font>&nbsp;&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_quantity"].'</font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_price"].'</font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'</font>&nbsp;&nbsp;</td>
								<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_trade_time"].'</font>&nbsp;&nbsp;</td>';
								
								$htmlfilebodydata .='<td nowrap>';
								if (in_array($row["trdm_account_number"], $arr_accounts)) {
								$reporthtmlfilebodydatamailbody .= '<a><font face="Courier New, Courier, mono" color="#000000" size="1">'.$arr_accountnames[$row["trdm_account_number"]].'</font></a>';
								} else {
								$htmlfilebodydata .= '&nbsp;';					
								}
								$htmlfilebodydata .= '</td>';
								$htmlfilebodydata .= '</tr>';
								} 
								}
								
								$htmlfilebodydata .= '</table>
								</td>
								</tr>
								</table>
								<!--Table with thin cell border ends-->
								</td>
								</tr>
								</table>
								</td>
								</tr>';
								
								//**************************************************************************************
								//CREATE HTML OUTPUT FILE
								// BEGIN 1						
								
								$htmlfiledata = rep_header_emp_trades($trade_date_to_process);
								$htmlfiledata .= $htmlfilebodydata;															
								$htmlfiledata .= rep_footer_emp_trades();
								
								$str_filename = "TRADES_".$trade_date_to_process.".html";
								$str_pdfname = "TRADES_".$trade_date_to_process.".pdf";
								
								$fp = fopen($exportlocation.$str_filename, "w");
								fputs ($fp, $htmlfiledata);
								fclose($fp);
								
								shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							
								xdebug("File Written and PDF Created",$str_filename);
								xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
								//echo '<a href="./data/exports/'.$str_pdfname.'">Click Here</a><br><br>';
								
								$mailsubject = "Trades Report for ". format_date_ymd_to_mdy($trade_date_to_process);
								$email_heading = "Trades Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
								xdebug("email_heading",$email_heading);
								$fileattach = $str_pdfname;
								$control_id = gen_control_number();
								html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
								?>
								
								<br>
								<table cellpadding="10" width="100%"><tr><td>
								<? table_start_percent(100,"Report Status"); ?>
								<a class="appmytext">
								<?=$email_heading?><br>
								The report (PDF Format) has been generated and a copy of it has been sent to your email account on file (<?=$user_email?>).<br><br>
								You may also access the report <a class="links10" href="./data/exports/<?=$str_pdfname?>" target="_blank">HERE</a>
								</a>
								<? table_end_percent(); ?>
								</td></tr></table>

										<!-- The following added so the top menu will be visible and not hidden behing the iframe -->
								<br> 
								
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
								{
									if ($trdm_trade_date1 == "") {
										?>
											<table cellpadding="10" width="100%"><tr><td>
											<? table_start_percent(100,"Report Status"); ?>
											<a class="appmytext"><br>
											No date selected. Please select date and then proceed.<br>
											</a>
											<? table_end_percent(); ?>
											</td></tr></table>
										<?
									} else {
										?>
											<table cellpadding="10" width="100%"><tr><td>
											<? table_start_percent(100,"Report Status"); ?>
											<a class="appmytext"><br>
											Trade Report was not sent because no trades were found for trade date <?=$trade_date_to_process?><BR>
									    possibly because there were errors in the trade upload. Please try the trade upload again and if<BR>
									    the problem persists please contact Technical Support at support@centersysgroup.com.<BR>
											</a>
											<? table_end_percent(); ?>
											</td></tr></table>
										<?
									}
								}
							}
							
							else if($submit2)
							{
								$trade_date_to_process = $trdm_trade_date2;
								function rep_header_emp_trades ($trade_date_to_process){
								$str_rep_datetime = "Compliance Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
								return '<html>
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
								<td><img src="../../images/compliancelogo.gif"></td>
								<td valign="top" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2"><b>CompSys v 2.0 (Demo)</b></font></td>
								</tr>
								<tr>
								<td colspan=2><img src="../../images/grey_red_bar.gif" border="0"></td>
								</tr>
								<tr>
								<td colspan=2><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$str_rep_datetime.'</font></td>
								</tr>
								<tr>
								<td colspan=2><img src="../../images/gray_black_bar.gif" border="0"></td>
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
								
								function rep_footer_emp_trades (){
								
								return 					'</table>
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
								
/********************** START SYSTEM LISTS (MONEYMAKER, ANALYST, BANKER) ***************************************************************************************/
						$query_list_types = "SELECT slis_auto_id, slis_title_name FROM slis_system_lists WHERE slis_isactive = '1' ORDER BY slis_auto_id";
						
						$result_list_types = mysql_query($query_list_types) or die(mysql_error());
						//START WHILE 1
						while($row_list_types = mysql_fetch_array($result_list_types))
						{
							//SKIP HOLDING PERIOD LIST
							//START IF 2
							if($row_list_types['slis_auto_id'] != 4)
							{
								//Get tickers on list
								$query_symbols_on_list = "SELECT syll_symbol FROM syll_system_list_lists WHERE syll_id = '".$row_list_types['slis_auto_id']."' AND syll_isactive = 1";
								xdebug("query_symbols_on_list", $query_symbols_on_list );
								$result_num_trades = mysql_query($query_symbols_on_list) or die (mysql_error());
							
								//START IF 6
								if(mysql_num_rows($result_num_trades) > 0)
								{
									$i = 0;
									$symbol_string = '';
									while($row = mysql_fetch_array($result_num_trades)) 
									{
										$symbols_on_list[$i] = $row["syll_symbol"];
										if ($symbol_string=='') 
										{
											$symbol_string = "'".$row["syll_symbol"]."'";
										} 
										else 
										{
											$symbol_string = $symbol_string.",'".$row["syll_symbol"]."'";
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
										{
											$str_accounts = "'".$row["trdm_account_number"]."'";
										} 
										else 
										{
											$str_accounts = $str_accounts.",'".$row["trdm_account_number"]."'";
										}
										$i = $i + 1;
									}
									xdebug("str_accounts",$str_accounts);
									
									//Check this condition thoroughly later
									xdebug("i",$i);
									$proceed = 0;
									if ($i > 0) 
									{
										$proceed = 1;
									} 
									xdebug("proceed",$proceed); 
												
									//******************************************************************************	
									//Find if there are employee trades in these tickers, given that there are trades
									//the tickers on the stock list.
									
									//START IF 4
									if ($proceed == 1) 
									{
										$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts where acct_is_active = 1 and acct_number in (".$str_accounts.")";
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
										xdebug("str_accounts_match",$str_accounts_match);
						
										xdebug("i",$i);
										$proceed_final = 0;
										if ($i > 0) 
										{
											$proceed_final = 1;
										} 
										xdebug("proceed_final",$proceed_final);
									} //END IF 4
									else 
									{
										$proceed_final = 0;
									}
									//START IF 5
									if ($proceed_final == 1) 
									{
										//Add to content $rep_content_emp_trades
										$htmlfilebodydata .= '<tr> 
										<td colspan="8">&nbsp;</td>
										</tr>
										<tr> 
										<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['slis_title_name'].'</b></font></td>
										</tr>
										<tr> 
										<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
										<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
										<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
										</tr>';
																				
										$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") and  trdm_account_number in (".$str_accounts_match.")";
										$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
										while ( $row = mysql_fetch_array($result_query_trades_final) ) 
										{
											//$arr_accounts[$i] = $row["trdm_account_number"];
											
											$htmlfilebodydata .='<tr> 
											<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_account_number"].'</font></td>
											<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_symbol"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_quantity"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_price"].'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'&nbsp;&nbsp;</font></td>
											<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
											<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row["trdm_account_number"]].'</font></TD>
											</tr>';
										}
									} //END IF 5
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
								} // END IF 6
								else
								{
									$htmlfilebodydata .= '<tr> 
									<td colspan="8">&nbsp;</td>
									</tr>
									<tr> 
									<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['slis_title_name'].'</b> (No Trades)</font></td>
									</tr>';
								}
							} // END IF 2
						} //END WHILE 1
/********************** END SYSTEM LISTS (MONEYMAKER, ANALYST, BANKER)  ************************************************************************************/
					
/********************** START ADMIN LISTS (RESTRICTED, WATCH, GRAY)  **************************************************************************************/
						$query_list_types = "SELECT alis_auto_id, alis_title_name FROM alis_admin_lists WHERE alis_isactive = '1' ORDER BY alis_auto_id";
						$result_list_types = mysql_query($query_list_types) or die(mysql_error());
						//START WHILE 1
						while($row_list_types = mysql_fetch_array($result_list_types))
						{
							//Get tickers on list
							$query_symbols_on_list = "SELECT adll_symbol FROM adll_admin_list_lists WHERE adll_id = '".$row_list_types['alis_auto_id']."' AND adll_isactive = 1";
							xdebug("query_symbols_on_list", $query_symbols_on_list );
							$result_num_trades = mysql_query($query_symbols_on_list) or die (mysql_error());
						
							//START IF 6
							if(mysql_num_rows($result_num_trades) > 0)
							{
								$i = 0;
								$symbol_string = '';
								while($row = mysql_fetch_array($result_num_trades)) 
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
								
								//Find if there are trades in these tickers
								$query_trades = "SELECT trdm_auto_id, trdm_account_number, trdm_symbol from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.")";
								$result_query_trades = mysql_query($query_trades) or die (mysql_error());
								$i = 0;
								while ( $row = mysql_fetch_array($result_query_trades) ) 
								{
									$arr_accounts[$i] = $row["trdm_account_number"];
									if ($str_accounts =='') 
									{
										$str_accounts = "'".$row["trdm_account_number"]."'";
									} 
									else 
									{
										$str_accounts = $str_accounts.",'".$row["trdm_account_number"]."'";
									}
									$i = $i + 1;
								}
								xdebug("str_accounts",$str_accounts);
								
								//Check this condition thoroughly later
								xdebug("i",$i);
								$proceed = 0;
								if ($i > 0) 
								{
									$proceed = 1;
								} 
								xdebug("proceed",$proceed); 
								
								//START IF 2
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
										{
											$str_accounts_match = "'".$row["acct_number"]."'";
										} 
										else 
										{
											$str_accounts_match = $str_accounts_match.",'".$row["acct_number"]."'";
										}
										$i = $i + 1;
									}
									xdebug("str_accounts_match",$str_accounts_match);
								} //ratan 	
					
								xdebug("i",$i);
								$proceed_final = 0;
								if ($i > 0) 
								{
									$proceed_final = 1;
								} 
								
								//START IF 3
								if ($proceed_final == 1) 
								{
									//Add to content $rep_content_emp_trades
									
									$htmlfilebodydata .= '<tr> 
									<td colspan="8">&nbsp;</td>
									</tr>
									<tr> 
									<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['alis_title_name'].'</b></font></td>
									</tr>
									<tr> 
									<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
									<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
									</tr>';
																			
									$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") and  trdm_account_number in (".$str_accounts_match.")";
									$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
				
									while ( $row = mysql_fetch_array($result_query_trades_final) ) 
									{
										$htmlfilebodydata .='<tr> 
										<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_account_number"].'</font></td>
										<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_symbol"].'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_quantity"].'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_price"].'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
										<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row["trdm_account_number"]].'</font></TD>
										</tr>';
									}
								} //END IF 3
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
							} // END IF 6
							else
							{
									$htmlfilebodydata .= '<tr> 
									<td colspan="8">&nbsp;</td>
									</tr>
									<tr> 
									<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_list_types['alis_title_name'].'</b> (No Trades)</font></td>
									</tr>';
							}
						} // END WHILE 1
/************************ END ADMIN LISTS (RESTRICTED, WATCH, GRAY) *****************************************************************************/
				
							
/*************************************** START USER LISTS *****************************************************************************/
						$query_user_list = "SELECT usli_auto_id, usli_title_name FROM usli_user_lists WHERE usli_user_id = '".$user_id."' AND usli_isactive = '1'";
						$result_user_list = mysql_query($query_user_list) or die(mysql_error());
						//START WHILE 1
						while($row_user_list = mysql_fetch_array($result_user_list))
						{
							//GET TICKERS ON LIST
							$query_user_symbols = "SELECT usll_symbol FROM usll_user_list_lists WHERE usll_list_id = '".$row_user_list['usli_auto_id']."' AND usll_isactive = '1'";
							$result_user_symbols = mysql_query($query_user_symbols) or die(mysql_error());
						
							xdebug("query_user_symbols", $query_user_symbols );
						
							//START IF 6
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
								
								//FIND IF THERE ARE TRADES IN THESE TICKERS
								$query_trades = "SELECT trdm_auto_id, trdm_account_number, trdm_symbol from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.")";
								$result_trades = mysql_query($query_trades) or die (mysql_error());
								$i = 0;
								while ( $row_trades = mysql_fetch_array($result_trades) ) 
								{
									$arr_accounts[$i] = $row_trades["trdm_account_number"];
									if ($str_accounts =='') 
									{
										$str_accounts = "'".$row_trades["trdm_account_number"]."'";
									} 
									else 
									{
										$str_accounts = $str_accounts.",'".$row_trades["trdm_account_number"]."'";
									}
									$i = $i + 1;
								}
								xdebug("str_accounts",$str_accounts);
	
								//CHECK THIS CONDITION THOROUGHLY LATER
								xdebug("i",$i);
								$proceed = 0;
								if ($i > 0) 
								{
									$proceed = 1;
								} 
								xdebug("proceed",$proceed); 
								
								//START IF 2
								if ($proceed == 1) 
								{
									$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts where acct_is_active = 1 and acct_number in (".$str_accounts.")";
									$result_accounts = mysql_query($query_accounts) or die (mysql_error());
									$i = 0;
									while ( $row_accounts = mysql_fetch_array($result_accounts) ) 
									{
										$arr_accounts_match[$i] = $row_accounts["acct_number"];
										
										$arr_get_account_detail[$row_accounts["acct_number"]] = $row_accounts["acct_name1"]." (".$row_accounts["acct_rep"].")";
										
										if ($str_accounts_match =='') 
										{
											$str_accounts_match = "'".$row_accounts["acct_number"]."'";
										} 
										else 
										{
											$str_accounts_match = $str_accounts_match.",'".$row_accounts["acct_number"]."'";
										}
										$i = $i + 1;
									}
									xdebug("str_accounts_match",$str_accounts_match);
								} // END IF 2
								
								xdebug("i",$i);
								$proceed_final = 0;
								if ($i > 0) 
								{
									$proceed_final = 1;
								} 
								xdebug("proceed_final",$proceed_final);

								//START IF 3
								if ($proceed_final == 1) 
								{
									//Add to content $rep_content_emp_trades
									$htmlfilebodydata .= '<tr> 
									<td colspan="8">&nbsp;</td>
									</tr>
									<tr> 
									<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_user_list['usli_title_name'].'</b></font></td>
									</tr>
									<tr> 
									<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
									<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
									<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
									</tr>';
																			
									$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") and  trdm_account_number in (".$str_accounts_match.")";
									$result_trades_final = mysql_query($query_trades_final) or die (mysql_error());
				
									while ( $row_trades_final = mysql_fetch_array($result_trades_final) ) 
									{
										$htmlfilebodydata .='<tr> 
										<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_account_number"].'</font></td>
										<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_symbol"].'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row_trades_final["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_quantity"].'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_price"].'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row_trades_final["trdm_quantity"]*$row_trades_final["trdm_price"]).'&nbsp;&nbsp;</font></td>
										<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_trades_final["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
										<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row_trades_final["trdm_account_number"]].'</font></TD>
										</tr>';
									}
								} //END IF 3
								else 
								{
									//Add to content $rep_content_emp_trades (no trades)
									$htmlfilebodydata .= '<tr> 
									<td colspan="8">&nbsp;</td>
									</tr>
									<tr> 
									<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_user_list['usli_title_name'].'</b> (No Trades)</font></td>
									</tr>';
								}
							} //END IF 6
							else
							{
									$htmlfilebodydata .= '<tr> 
								<td colspan="8">&nbsp;</td>
								</tr>
								<tr> 
								<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$row_user_list['usli_title_name'].'</b> (No Trades)</font></td>
								</tr>';
							}
						} // END WHILE 1
/************************************** END USER LISTS *****************************************************************************/
	
							
/***************************************  HOLDING PERIOD LIST PROCESSING  *****************************************************************************/
						include('holding_period.php');
/******************************************************************************************************************************************************/
								
								//**************************************************************************************
								//CREATE HTML OUTPUT FILE
								// BEGIN 1						
								
								$htmlfiledata = rep_header_emp_trades($trade_date_to_process);
								$htmlfiledata .= $htmlfilebodydata;															
								$htmlfiledata .= rep_footer_emp_trades();
								$str_filename = "TREMP_".$trade_date_to_process.".html";
								$str_pdfname = "TREMP_".$trade_date_to_process.".pdf";
								
								$fp = fopen($exportlocation.$str_filename, "w");
								fputs ($fp, $htmlfiledata);
								fclose($fp);
								shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							
								xdebug("File Written and PDF Created",$str_filename);
								xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
								//echo '<a href="./data/exports/'.$str_pdfname.'">Click Here</a><br><br>';
								
								$mailsubject = "Compliance Report for ". format_date_ymd_to_mdy($trade_date_to_process);
								$email_heading = "Compliance Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
								xdebug("email_heading",$email_heading);
								$fileattach = $str_pdfname;
								$control_id = gen_control_number();
								//html_emails('prasad_pravin@yahoo.com', $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
								html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
								?>
								
								<br>
								<table cellpadding="10" width="100%"><tr><td>
								<? table_start_percent(100,"Report Status"); ?>
								<a class="appmytext">
								<?=$email_heading?><br>
								The report (PDF Format) has been generated and a copy of it has been sent to your email account on file (<?=$user_email?>).<br><br>
								You may also access the report <a class="links10" href="./data/exports/<?=$str_pdfname?>" target="_blank">HERE</a>
								</a>
								<? table_end_percent(); ?>
								</td></tr></table>

										<!-- The following added so the top menu will be visible and not hidden behing the iframe -->
								<br> 
								
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
								// END 1
								//Sending link of Trades Report file via email
								//echo "<BR>Creating Trades Report file...<BR>";
								//create_trade_report("pprasad@tocqueville.com","2Trades Report for (".$trade_date_to_process.")", $reportmailbody, "Trades Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a"));
								//sys_mail("prasad_pravin@yahoo.com","Trades Report for (".$trade_date_to_process.")", $reportmailbody, "Trades Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a"));
								//echo "<BR>Created Trades Report file successfully...<BR>";
								
								/*
								$linkmailbody = "Please click on the following link to access the Trades Report for ".$trade_date_to_process."\n";
								$linkmailbody .= "\n\nhttp://10.10.10.144/compliance/data/exports/Trades_Report_".$trade_date_to_process.".html";
								$linkmailbody .= "\n\n\nCompliance System Mailer";
								sleep(1);
								$var_value = $linkmailbody;
								//mail(email_report_to(),'Trades Report for Date ('.$trade_date_to_process.')',$var_value,"From: compliance@tocqueville.com <compliance@tocqueville.com>","-fcompliance@tocqueville.com");
								sleep(1);
								*/
								xdebug("Trade Report for trade date ".$trade_date_to_process." sent successfully via email!",0);
								
								
								} 
								else 
								{
									if ($trdm_trade_date1 == "") {
										?>
											<table cellpadding="10" width="100%"><tr><td>
											<? table_start_percent(100,"Report Status"); ?>
											<a class="appmytext"><br>
											No date selected. Please select date and then proceed.<br>
											</a>
											<? table_end_percent(); ?>
											</td></tr></table>
										<?
									} else {
										?>
											<table cellpadding="10" width="100%"><tr><td>
											<? table_start_percent(100,"Report Status"); ?>
											<a class="appmytext"><br>
											Trade Report was not sent because no trades were found for trade date <?=$trade_date_to_process?><BR>
									    possibly because there were errors in the trade upload. Please try the trade upload again and if<BR>
									    the problem persists please contact Technical Support at support@centersysgroup.com.<BR>
											</a>
											<? table_end_percent(); ?>
											</td></tr></table>
										<?
									}
								}
								
								}
								else
								$trade_date_to_process = date("Y-m-d");
								
							?>
							</td>
						</tr>
					</table>
					<!-- END TABLE 2 -->
					<!-- CONTENT END -->
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
	</td>
</tr>

<?php
include('bottom.php');
?>
