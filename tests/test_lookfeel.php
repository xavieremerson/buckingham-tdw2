<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html;"/>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?
include('includes/dbconnect.php');

////
// Create message with image depending on severity
// 1 = green, 2 = orange, 3 = red
   function sys_message($severity, $msgtext) {
	 
	 
	 //Rounded corner tables used across the application
		$table_start = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td width="7" height="7" background="images/tables/lt.jpg"></td>
				<td background="images/tables/ts.jpg"></td>
				<td width="7" height="7" background="images/tables/rt.jpg"></td>
			</tr>
			<tr> 
				<td width="7" background="images/tables/ls.jpg"></td>
				<td>';
		$table_end = '</td>
				<td width="7" background="images/tables/rs.jpg"></td>
			</tr>
			<tr> 
				<td width="7" height="7" background="images/tables/lb.jpg"></td>
				<td background="images/tables/bs.jpg"></td>
				<td width="7" height="7" background="images/tables/rb.jpg"></td>
			</tr>
		</table>';

   if ($severity == 1){
	 $imagefile = 'msg_success.gif';
 	 $varcssstyle = 'links10';
	 }
	 elseif ($severity == 2){
	 $imagefile = 'msg_warning.gif';
 	 $varcssstyle = 'links10';
	 }
	 elseif ($severity == 3){
	 $imagefile = 'msg_error.gif';
 	 $varcssstyle = 'links10';
	 }
	 else {
	 $imagefile = 'msg_warning.gif';
 	 $varcssstyle = 'msg_warning';
	 }
	 
	 
   echo '<tr><td valign="top">'.$table_start.'<img src="images/' . $imagefile . '"><a class="' . $varcssstyle . '">' . $msgtext . '</a>'.$table_end.'</td></tr>';
	 						 
	 }	 

sys_message(1,"This is a test");
sys_message(2,"This is a test");
sys_message(3,"This is a test");

?>
<?
////
// Give a meaningful error output
function tdw_db_error($qry) {
echo '<table class="msgtbl_3">
        <tr>
				   <td nowrap="nowrap"><font size="3"><strong>&raquo;</strong></font> <b>TDW Server encountered a serious data error</b>'."\n".'<br>Query: ' . $qry . '<br>'."\n".'Error: (' . mysql_errno() . ') ' . mysql_error() . '</td>
				</tr>
      </table>';
}
?>
<style type="text/css">
<!--
input.submit, a.button, input.button {
	border: 3px double #0179a5;
	/* #0092C9 */
	border-left-color: #54C9F5;
	border-top-color: #54C9F5;
	margin: 5px 5px 5px 0;
	color: #000099;
	height: auto;
	text-decoration: none;
	font-family: Helvetica, Arial, sans-serif;
	font-weight: bold;
	font-size: 12px;
	padding: 0.1ex 0;
	cursor: pointer;
	background: #00AFF0 url(images/button_bg.gif) top left repeat-x;
	text-decoration: none;
	font-stretch: ultra-expanded;
	letter-spacing: 1px;
}
.msgtbl_1 {
	border: 1px solid #B8F834;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #006633;
	background-color: #EFFBDF;
	white-space: nowrap;
	width: 100%;
	overflow: visible;
	position: absolute;
}

.msgtbl_2 {
	border: 1px solid #ffca29;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FF6600;
	background-color: #fff6e5;
	white-space: nowrap;
	width: 100%;
	overflow: visible;
	position: absolute;
}

.msgtbl_3 {
	border: 1px solid #ffabab;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FF0000;
	background-color: #fff5f5;
	white-space: nowrap;
	width: 100%;
	overflow: visible;
	position: absolute;
}

-->
</style>
<form>
<input class="submit" type="submit" name="test" value="test button" />
</form>

<table class="msgtbl_1">
<tr><td nowrap="nowrap"><font size="3"><strong>&raquo;</strong></font> this is a test</td></tr>
</table>
<br /><br />
<table class="msgtbl_2">
<tr><td nowrap="nowrap"><font size="3"><strong>&raquo;</strong></font> this is a test</td></tr>
</table>
<br /><br />
<table class="msgtbl_3">
<tr><td nowrap="nowrap"><font size="3"><strong>&raquo;</strong></font> this is a test</td></tr>
</table>
<br>
<br>
<?

	$qry_acct = "select naddx_short_name from mry_nfs_nadd where nadd_full_account_number = '".$acctnum."'";
	$result_acct = mysql_query($qry_acct) or die (tdw_db_error($qry_acct));
	while ( $row_acct = mysql_fetch_array($result_acct) ) 
	{
		$acctname = $row_acct["nadd_short_name"];
	}
?>
</body>



