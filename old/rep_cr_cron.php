<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php'); 
?>
<tr>
<td valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
		<!-- CONTENT BEGIN -->

<?php

	//Date in YYYY-MM-DD Format
	$trade_date_to_process = previous_business_day();

////
// Check if trades exist and only then begin processing
// If trades do not exist, there has been one or more errors in the import/upload of trades

	$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$trade_date_to_process."'") or die (mysql_error());
	while ( $row = mysql_fetch_array($result_num_trades) ) 
	{
		$numtrades_val = $row["numtrades"];
	}
		
	if ($numtrades_val > 0) 
	{
		//// For each list type process the data.
		//START RESTRICTED, WATCH, GRAY LIST
		$arr_list_types = array('watch', 'gray', 'restricted');
		$arr_list_types_tables = array('watch' => 'lwat_watch_list', 'gray' => 'lgry_gray_list', 'restricted' => 'lres_restricted_list');
		$arr_list_names_label = array('watch' => 'WATCH LIST', 'gray' => 'GRAY LIST', 'restricted' =>'RESTRICTED LIST');						

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
				if ($symbol_string=='') 
				{
					$symbol_string = "'".$row["list_symbol"]."'";
				} 
				else 
				{
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
				
				xdebug("i",$i);
				$proceed_final = 0;
				if ($i > 0) 
				{
					$proceed_final = 1;
				} 
				xdebug("proceed_final",$proceed_final);
			} 
			else 
			{
			 	$proceed_final = 0;
			}
			
			if ($proceed_final == 1) 
			{
				//Add to content $rep_content_emp_trades
				
				$htmlfilebodydata .= '<tr> 
				<td colspan="8">&nbsp;</td>
				</tr>
				<tr> 
				<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$arr_list_names_label[$arr_list_types[$i_list]].'</b></font></td>
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
			} 
			else 
			{
				//Add to content $rep_content_emp_trades (no trades)
				$htmlfilebodydata .= '<tr> 
				<td colspan="8">&nbsp;</td>
				</tr>
				<tr> 
				<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$arr_list_names_label[$arr_list_types[$i_list]].'</b> (No Trades)</font></td>
				</tr>';
			}
		} 
		//End for loop for processing all lists.
		//END RESTRICTED, WATCH, GRAY LIST
		
		include('holding_period.php');
		
		//START MONEYMAKER, ANALYST, BANKER LIST
		$arr_list_types = array('marketmaker', 'analyst', 'banker');
		$arr_list_types_tables = array('marketmaker' => 'lmkt_mktmaker_stock_list', 'analyst' => 'lana_analyst_stock_list', 'banker' => 'lban_banker_stock_list');
		$arr_list_names_label = array('marketmaker' => 'MARKETMAKER STOCK LIST', 'analyst' =>'ANALYST STOCK LIST', 'banker' => 'BANKER STOCK LIST');						

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
				if ($symbol_string=='') 
				{
					$symbol_string = "'".$row["list_symbol"]."'";
				} 
				else 
				{
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
				
				xdebug("i",$i);
				$proceed_final = 0;
				if ($i > 0) 
				{
					$proceed_final = 1;
				} 
				xdebug("proceed_final",$proceed_final);
			} 
			else 
			{
			 	$proceed_final = 0;
			}
			
			if ($proceed_final == 1) 
			{
				//Add to content $rep_content_emp_trades
				
				$htmlfilebodydata .= '<tr> 
				<td colspan="8">&nbsp;</td>
				</tr>
				<tr> 
				<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$arr_list_names_label[$arr_list_types[$i_list]].'</b></font></td>
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
			} 
			else 
			{
				//Add to content $rep_content_emp_trades (no trades)
				$htmlfilebodydata .= '<tr> 
				<td colspan="8">&nbsp;</td>
				</tr>
				<tr> 
				<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$arr_list_names_label[$arr_list_types[$i_list]].'</b> (No Trades)</font></td>
				</tr>';
			}
		} 
		//End for loop for processing all lists.
		//END MONEYMAKER, ANALYST, BANKER LIST

		//**************************************************************************************
		//CREATE HTML OUTPUT FILE
		// BEGIN 1						
		
		$htmlfiledata = rep_header_emp_trades($trade_date_to_process);
								
		$htmlfiledata .= $htmlfilebodydata;															
							
		$htmlfiledata .= rep_footer_emp_trades();
		
		$str_filename = "CRON_COMPLIANCE_".$trade_date_to_process.".html";
		$str_pdfname = "CRON_COMPLIANCE_".$trade_date_to_process.".pdf";
		
		$fp = fopen($exportlocation.$str_filename, "w");
		
		fputs ($fp, $htmlfiledata);
		
		fclose($fp);
		
		shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							
		
		xdebug("File Written and PDF Created",$str_filename);
		
		xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
		//echo '<a href="./data/exports/'.$str_pdfname.'">Click Here</a><br><br>';
		
		$mailsubject = "Compliance Report for ". $trade_date_to_process;
		$email_heading = "Compliance Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
		xdebug("email_heading",$email_heading);
		$fileattach = $str_pdfname;
		$control_id = gen_control_number();
		$mailbodysubinfo = 'Compliance Report';
		//html_emails('prasad_pravin@yahoo.com', $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
		html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
		
		
		
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
	} 
	else 
	{
		echo "Trade Report was not sent because no trades were found for trade date ".$trade_date_to_process."<BR>";
		echo "possibly because there were errors in the trade upload. Please try the trade upload again and if<BR>";
		echo "the problem persists please contact Technical Support at support@centersysgroup.com.<BR>";
	}

function rep_header_emp_trades ($trade_date_to_process)
{
	//$str_rep_datetime = "Compliance Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
	
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

	return 	'</table>
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

		<!-- CONTENT END -->
		</td>
  </tr>
</table>
</td>
</tr>
