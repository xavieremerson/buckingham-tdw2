<?php
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');

$date_processed = date('Y-m-d');
?>
<tr>
<td valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
		<!-- CONTENT BEGIN -->

<?php
$htmlfilebodydata .= '<tr> 
<td colspan="5">&nbsp;</td>
</tr>
<tr> 
<td width="60"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct. Rep.</u></font></td>
<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;&nbsp;<u>Acct. #</u></font></td>
<td width="200" align="left"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;&nbsp;<u>Acct. Name 1</u></font></td>
<td width="200" align="left"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;&nbsp;&nbsp;<u>Acct. Name 2</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Open Date</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
</tr>';

$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2, acct_open_date from Employee_accounts where acct_is_active = 1 order by acct_number";
xdebug("query_accounts",$query_accounts);
$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
$i = 0;
while ( $row = mysql_fetch_array($result_query_accounts) ) 
{
	$htmlfilebodydata .='<tr> 
	<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["acct_rep"].'</font></td>
	<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.$row["acct_number"].'&nbsp;&nbsp;</font></td>
	<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.$row["acct_name1"].'&nbsp;&nbsp;&nbsp;</font></td>
	<td nowrap align="left"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.$row["acct_name2"].'&nbsp;&nbsp;</font></td>
	<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_date_ymd_to_mdy($row["acct_open_date"]).'&nbsp;&nbsp;</font></td>
	</tr>';
}														

$htmlfilebodydata .= '<tr> 
<td colspan="5">&nbsp;</td>
</tr>
<tr> 
<td colspan="5"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.'- End of Report -'.'</b></font></td>
</tr>';

//**************************************************************************************
//CREATE HTML OUTPUT FILE
// BEGIN 1

$report_heading = "Employee Accounts Report";						
$htmlfiledata = rep_header_pdf($report_heading);
$htmlfiledata .= $htmlfilebodydata;															
$htmlfiledata .= rep_footer_pdf();

$str_filename = "CRON_EMPLOYEE_ACCOUNTS_".$date_processed.".html";
$str_pdfname = "CRON_EMPLOYEE_ACCOUNTS_".$date_processed.".pdf";

$fp = fopen($exportlocation.$str_filename, "w");

fputs ($fp, $htmlfiledata);

fclose($fp);

shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							

xdebug("File Written and PDF Created",$str_filename);

xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
//echo '<a href="./data/exports/'.$str_pdfname.'">Click Here</a><br><br>';

$mailsubject = "Employee Accounts Report ". $date_processed;
$email_heading = "Employee Accounts Report Generated on ".date("D, m/d/Y h:i a");
xdebug("email_heading",$email_heading);
$fileattach = $str_pdfname;
$control_id = gen_control_number();
html_emails('prasad_pravin@yahoo.com', $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 

function rep_header_pdf ($heading)
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

function rep_footer_pdf ()
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

