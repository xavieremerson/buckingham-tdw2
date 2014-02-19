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

?>
<body leftmargin="2" topmargin="2" rightmargin="2" bottommargin="2" onclick="window.close()">
<table width="400" height="200" border="0" cellpadding="0" cellspacing="0">
  <tr bgcolor="#000000">
    <td height="32" width="32"><img src="images/help_3232.png" border="0"></td>
    <td width="362">&nbsp;&nbsp;<font color="#FFFFFF" size="+1" face="Verdana">Build/Populate Memory</font></td>
  </tr>
  <tr>
    <td colspan="2" valign="top"><table width="100%" class="tbl_help">
        <tr>
          <td valign="top"><br />
<?
/**/

//Flush the memory tables used by production pages/app
$result_mry_comm_rr_level_0_flush = mysql_query("truncate table mry_comm_rr_level_0") or die (mysql_error());
$result_mry_comm_rr_level_a_flush = mysql_query("truncate table mry_comm_rr_level_a") or die (mysql_error());
$result_mry_comm_rr_level_b_flush = mysql_query("truncate table mry_comm_rr_level_b") or die (mysql_error());
$result_mry_comm_rr_trades_flush  = mysql_query("truncate table mry_comm_rr_trades") or die (mysql_error());
$result_mry_nfs_nadd_flush        = mysql_query("truncate table mry_nfs_nadd") or die (mysql_error());
echo "Memory Areas flushed. [Precautionary Measure]<br>";

//Populate tables
$result_mry_comm_rr_level_0_populate = mysql_query("insert into mry_comm_rr_level_0 select * from rep_comm_rr_level_0") or die (mysql_error());
$result_mry_comm_rr_level_a_populate = mysql_query("insert into mry_comm_rr_level_a select * from rep_comm_rr_level_a") or die (mysql_error());
$result_mry_comm_rr_level_b_populate = mysql_query("insert into mry_comm_rr_level_b select * from rep_comm_rr_level_b") or die (mysql_error());
$result_mry_comm_rr_trades_populate  = mysql_query("insert into mry_comm_rr_trades select * from rep_comm_rr_trades") or die (mysql_error());
$result_mry_nfs_nadd_populate        = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (mysql_error());
echo "Memory Areas populated./n<br>";
?>					
					</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>