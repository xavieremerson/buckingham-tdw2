<?php
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
<?
	
	//Date in YYYY-MM-DD Format
	$trade_date_to_process = previous_business_day();

////
//Check if trades exist and only then send emails
//If trades do not exist, there has been one or more errors in the import/upload of trades

	$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$trade_date_to_process."'") or die (mysql_error());
	while ( $row = mysql_fetch_array($result_num_trades) ) 
	{
		$numtrades_val = $row["numtrades"];
	}
								
	if ($numtrades_val > 0) 
	{						
		//********************************************************************************************************************************************
		//Get Employee Accounts data in a local variable
		$result = mysql_query("SELECT acct_number FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());
		
		$i = 0;
		$arr_accounts = array();
		
		while ( $row = mysql_fetch_array($result) ) 
		{
			$arr_accounts[$i] = $row["acct_number"];
			$i = $i+1;
		}
		
		//Get Employee Names on account
		$result1 = mysql_query("SELECT acct_number, concat( acct_name1, '(', acct_rep,')') as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());
		
		$i = 0;
		$arr_accountnames = array();
		
		while ( $row = mysql_fetch_array($result1) ) 
		{
			$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
			$i = $i+1;
		}
		//print_r($arr_accounts);
							
		//*********************************************************************************************************************************************
	
		//$date = date("Y-m-d");
		//$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
	
		if ($trdm_trade_date != '') 
		{ 
			$str_trdm_trade_date = " where trdm_trade_date = '". $trdm_trade_date ."'";
		} 
		else 
		{
			$str_trdm_trade_date = " where trdm_trade_date = '". $trade_date_to_process ."'";
		}			  
	
		if ($trdm_symbol != '') 
		{ 
			$str_trdm_symbol = " and trdm_symbol = '".$trdm_symbol."'";
		} 
		else 
		{
			$str_trdm_symbol = " and trdm_symbol != '' and LENGTH(trdm_symbol) < 8";
		}			  
	
		if ($trdm_account_number != '') 
		{ 
			$str_trdm_account_number = " and trdm_account_number = '".$trdm_account_number."'";
		} 
		else 
		{
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
			
		while ( $row = mysql_fetch_array($result) ) 
		{
			if ($emp_trades != 1) 
			{
				if (in_array($row["trdm_account_number"], $arr_accounts)) 
				{
					$htmlfilebodydata .= '<tr>';
					$htmlfilebodydata .= '<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_account_number"].'</u></font></td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_symbol"].'</u></font>&nbsp;&nbsp;</td>
					<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_sec_description"].'</u></font></td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">&nbsp;&nbsp;<u>'.convert_buy_sell($row["trdm_buy_sell"]).'</u></font>&nbsp;&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_quantity"].'</u></font>&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_price"].'</u></font>&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'</u></font>&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$row["trdm_trade_time"].'</u></font>&nbsp;&nbsp;</td>';
				} 
				else 
				{
					$htmlfilebodydata .= '<tr>';
					$htmlfilebodydata .= '<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_account_number"].'</font></td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_symbol"].'</font>&nbsp;&nbsp;</td>
					<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_sec_description"].'</font></td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">&nbsp;&nbsp;'.convert_buy_sell($row["trdm_buy_sell"]).'</font>&nbsp;&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_quantity"].'</font>&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_price"].'</font>&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'</font>&nbsp;&nbsp;</td>
					<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_trade_time"].'</font>&nbsp;&nbsp;</td>';
				}
				$htmlfilebodydata .= '<TD nowrap>';
				
				if (in_array($row["trdm_account_number"], $arr_accounts)) 
				{
					$htmlfilebodydata .= '<a><font face="Courier New, Courier, mono" color="#000000" size="1"><u>'.$arr_accountnames[$row["trdm_account_number"]].'</u></font></a>';
				} 
				else 
				{
					$htmlfilebodydata .= '&nbsp;';					
				}
				
				$htmlfilebodydata .= '</TD>';
				$htmlfilebodydata .= '</tr>';
			} 
			else 
			{
				if (in_array($row["trdm_account_number"], $arr_accounts)) 
				{
					$htmlfilebodydata .= '<tr>';
				}
				
				$htmlfilebodydata .= '<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_account_number"].'</font></td> 
				<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_symbol"].'&nbsp;&nbsp;</font></td>
				<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_sec_description"].'</td>
				<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">&nbsp;&nbsp;'.convert_buy_sell($row["trdm_buy_sell"]).'</font>&nbsp;&nbsp;&nbsp;</td>
				<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_quantity"].'</font>&nbsp;&nbsp;</td>
				<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_price"].'</font>&nbsp;&nbsp;</td>
				<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'</font>&nbsp;&nbsp;</td>
				<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["trdm_trade_time"].'</font>&nbsp;&nbsp;</td>';
				
				$htmlfilebodydata .='<td nowrap>';
				
				if (in_array($row["trdm_account_number"], $arr_accounts)) 
				{
					$reporthtmlfilebodydatamailbody .= '<a><font face="Courier New, Courier, mono" color="#000000" size="1">'.$arr_accountnames[$row["trdm_account_number"]].'</font></a>';
				} 
				else 
				{
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
		
		$str_filename = "CRON_TRADES_".$trade_date_to_process.".html";
		$str_pdfname = "CRON_TRADES_".$trade_date_to_process.".pdf";
		
		$fp = fopen($exportlocation.$str_filename, "w");
		fputs ($fp, $htmlfiledata);
		fclose($fp);
		
		shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							
		
		xdebug("File Written and PDF Created",$str_filename);
		
		xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
		//echo '<a href="./data/exports/'.$str_pdfname.'">Click Here</a><br><br>';
		
		$mailsubject = "Trades Report for ". $trade_date_to_process;
		$email_heading = "Trades Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
		xdebug("email_heading",$email_heading);
		$fileattach = $str_pdfname;
		$control_id = gen_control_number();
		$mailbodysubinfo = 'Trades Report';
		html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
	} 
	else 
	{
		echo "Trade Report was not sent because no trades were found for trade date ".$trade_date_to_process."<BR>";
		echo "possibly because there were errors in the trade upload. Please try the trade upload again and if<BR>";
		echo "the problem persists please contact Technical Support at support@centersysgroup.com.<BR>";
	}
	
function rep_header_emp_trades ($trade_date_to_process)
{
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
?>
		<!-- CONTENT END -->
		</td>
  </tr>
</table>
</td>
</tr>

