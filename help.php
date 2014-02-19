<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TDW HELP</title>
<style type="text/css">
<!--
.tbl_help {
	padding: 4px;
	border: 1px solid #333333;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000033;
	background-color: #F9F9FF;
	height: 180px;
}
-->
</style>
</head>
<?
//$item = 1;
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');

	$qry = "SELECT help_title, help_detail 
					FROM help_data
					WHERE help_auto_id = '".$item."'";
	$result = mysql_query($qry) or die (tdw_mysql_error($qry));
	while ( $row = mysql_fetch_array($result) ) 
	{
		$help_title = $row["help_title"];
		$help_detail = $row["help_detail"];
	}

?>
<body leftmargin="2" topmargin="2" rightmargin="2" bottommargin="2" onclick="window.close()">
<table width="400" height="200" border="0" cellpadding="0" cellspacing="0">
  <tr bgcolor="#000000">
    <td height="32" width="32"><img src="images/help_3232.png" border="0"></td>
    <td width="362">&nbsp;&nbsp;<font color="#FFFFFF" size="+1" face="Verdana"><?=$help_title?></font></td>
  </tr>
  <tr>
    <td colspan="2" valign="top"><table width="100%" class="tbl_help">
        <tr>
          <td valign="top"><br /><p align="justify">
					<?=$help_detail?></p></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
