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

	//Hash holding lists
	$arr_lists['Watch List'] = 'lwat_watch_list';
	$arr_lists['Gray List'] = 'lgry_gray_list';
	$arr_lists['Restricted List'] = 'lres_restricted_list';

	foreach($arr_lists as $key => $value) 
	{
		//echo $key . " ===> ". $value."<br>";
		////
		//Process each of the lists
		
		$result_listitems = mysql_query("SELECT list_id,list_symbol,list_description,DATE_FORMAT(list_date_added, '%m/%d/%y') as list_date_added, TO_DAYS(NOW()) - TO_DAYS(list_date_added) + 1 as 'list_days_on_list' FROM ".$value." where list_isactive = '1'") or die (mysql_error());
		
		$htmlfilebodydata .= '<tr><td colspan="5" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$key.'</font><font color="#ffffff" size="3">_________________________________________________________________</font></td></tr>';									
		$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
		$htmlfilebodydata .= '<tr> 
		<td align="left" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
		<td align="left" nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Description</u></font></td>
		<td nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Date Added</u></font></td>
		<td nowrap align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Days on List</u>&nbsp;&nbsp;</font></td>
		<td>&nbsp;</td>
		</tr>';
		
		while ( $row = mysql_fetch_array($result_listitems) ) 
		{
			$htmlfilebodydata .= '<tr>';
			$htmlfilebodydata .=   '<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_symbol"].'</font></td>
			<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_description"].'</font>&nbsp;&nbsp;</td>
			<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_date_added"].'</font></td>
			<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_days_on_list"].'</font>&nbsp;&nbsp;&nbsp;</td>';
			$htmlfilebodydata .=   '<td>&nbsp;</td>
			</tr>';
		}
		$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
	}

	//-----------------------------------------------------------------------------------------------------------
	
		//Hash holding lists
	$arr_lists_a['Marketmaker Stock List'] = 'lmkt_mktmaker_stock_list';
	$arr_lists_a['Analyst Stock List'] = 'lana_analyst_stock_list';
	$arr_lists_a['Banker Stock List'] = 'lban_banker_stock_list';

	foreach($arr_lists_a as $key => $value) 
	{
		//echo $key . " ===> ". $value."<br>";
		////
		//Process each of the lists
		
		$str_query = "SELECT a.list_symbol, a.list_description, DATE_FORMAT(a.list_date_added, '%m/%d/%y') as list_date_added, TO_DAYS(NOW()) - TO_DAYS(a.list_date_added) + 1 as 'list_days_on_list', b.acct_name1 FROM ".$value." a, Employee_accounts b where a.acct_auto_id = b.acct_auto_id and a.list_isactive = '1'";
		//echo $str_query."<br>";
		$result_listitems = mysql_query($str_query) or die (mysql_error());
		
		$htmlfilebodydata .= '<tr><td colspan="5"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$key.'</font></td></tr>';									
		$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
		$htmlfilebodydata .= '<tr> 
		<td align="left" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
		<td align="left" nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Description</u></font></td>
		<td align="left" nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Name</u></font></td>
		<td nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Date Added</u></font></td>
		<td nowrap align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Days on List</u>&nbsp;&nbsp;</font></td>
		</tr>';
		
		while ( $row = mysql_fetch_array($result_listitems) ) 
		{
			$htmlfilebodydata .= '<tr>';
			$htmlfilebodydata .= '<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_symbol"].'</font></td>
			<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_description"].'</font>&nbsp;&nbsp;</td>
			<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["acct_name1"].'</font></td>
			<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_date_added"].'</font></td>
			<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["list_days_on_list"].'</font>&nbsp;&nbsp;&nbsp;</td>';
			$htmlfilebodydata .= '</tr>';
		}
		$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
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

$str_filename = "CRON_STOCK_LISTS_".$trade_date_to_process.".html";
$str_pdfname = "CRON_STOCK_LISTS_".$trade_date_to_process.".pdf";

$fp = fopen($exportlocation.$str_filename, "w");
fputs ($fp, $htmlfiledata);
fclose($fp);
shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							

xdebug("File Written and PDF Created",$str_filename);
xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
//echo '<a href="./data/exports/'.$str_pdfname.'">Click Here</a><br><br>';

$mailsubject = "Stock Lists Report for ". $trade_date_to_process;
$email_heading = "Stock Lists Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
xdebug("email_heading",$email_heading);
$fileattach = $str_pdfname;
$control_id = gen_control_number();
//html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 

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
	<td valign="top" width="630">
	<!-- Begin Stock List Data -->
	<table width="630"  border="0" cellspacing="1" cellpadding="1">';
}

function rep_footer_emp_trades ()
{
	return '</table>
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
