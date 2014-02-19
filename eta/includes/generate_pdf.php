<?
require('includes/global.php');
function pdf_header_portrait ($report_title)
{
	//$str_rep_datetime = "Compliance Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
	
	return 
	'<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
	<!-- MEDIA TOP 0.1in --> 
	<!-- MEDIA BOTTOM 0.5in --> 
	<!-- MEDIA LANDSCAPE NO --> 
	<!-- MEDIA LEFT 0.5in --> 
	<!-- MEDIA RIGHT 0.5in --> 
	<!-- MEDIA SIZE "Letter" --> 
	<!-- MEDIA TYPE "Plain" -->	
	<table width="640" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" height="14" bgcolor="#FFFFFF">
	<table width="640">
	<tr>
	<td colspan=2><img src="../../images/pdf/portrait_top_bar.gif" border="0"></td>
	</tr>
	<tr>
	<td colspan=2><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$report_title.'</font></td>
	</tr>
	<tr>
	<td colspan=2><img src="../../images/pdf/portrait_bottom_bar.gif" border="0"></td>
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
	<table width="100%"  border="0" cellspacing="1" cellpadding="1"><tr><td>';
}

function pdf_footer_portrait ()
{
	return 	
	'</td></tr></table>
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

function pdf_header_landscape ($report_title)
{
	//$str_rep_datetime = "Compliance Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
	
	return 
	'<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
	<!-- MEDIA TOP 0.5in --> 
	<!-- MEDIA BOTTOM 0.5in --> 
	<!-- MEDIA LANDSCAPE YES --> 
	<!-- MEDIA LEFT 0.5in --> 
	<!-- MEDIA RIGHT 0.5in --> 
	<!-- MEDIA SIZE "Letter" --> 
	<!-- MEDIA TYPE "Plain" -->	
	<table width="670" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" height="14" bgcolor="#FFFFFF">
	<table width="670">
	<tr>
	<td colspan=2><img src="../../images/pdf/landscape_top_bar.gif" border="0"></td>
	</tr>
	<tr>
	<td colspan=2><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$report_title.'</font></td>
	</tr>
	<tr>
	<td colspan=2><img src="../../images/pdf/landscape_bottom_bar.gif" border="0"></td>
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
	<table width="100%"  border="0" cellspacing="1" cellpadding="1"><tr><td>';
}

function pdf_footer_landscape ()
{
	return 	
	'</td></tr></table>
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