<style type="text/css">
<!--
.all_general {
	font-family: "Courier New", Courier, mono;
	font-size: 13px;
}
-->
</style>

<!--<body onLoad="window.print();" > -->
<body> 
<table width="502" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top"><img src="images/logo.gif"></td>
		<td valign="top">
		
		<a class='all_general'><b>Report: Registered Representatives<br>Created On: <?=date('m/d/Y')?></b></a>
		
		</td>
	</tr>
</table>
<hr align="left" width="650" size="2" noshade="noshade" color="#000066" />
<table border="0" cellpadding="4" cellspacing="0"><tr><TD>
			<table width="645" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" class="all_general">
					<tr>
						<td width="85">&nbsp;&nbsp;<strong>RR</strong></td>
						<td width="544">&nbsp;&nbsp;<strong>Name(s)</strong></td>
					</tr>
<?
//BRG
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
 
$qry_reps_primary = "select rr_num, Fullname from users where rr_num not like '%999%' and rr_num is not null and rr_num != '' and user_isactive = 1 order by rr_num";
$result_reps_primary = mysql_query($qry_reps_primary) or die(tdw_mysql_error($qry_reps_primary));

while($row_reps_primary = mysql_fetch_array($result_reps_primary)) {
?>
					<tr>
						<td width="85">&nbsp;&nbsp;<?=$row_reps_primary["rr_num"]?></td>
						<td>&nbsp;&nbsp;<?=$row_reps_primary["Fullname"]?></td>
					</tr>
<?
}
?>
					<tr>
						<td width="85">&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;</td>
					</tr>
<?
//Now get shared Reps List
$qry_shared_reps = "select distinct(srep_rrnum) from sls_sales_reps  where srep_isactive = 1 and srep_rrnum != '' order by srep_rrnum";
$result_shared_reps = mysql_query($qry_shared_reps) or die(tdw_mysql_error($qry_shared_reps));
while($row_shared_reps = mysql_fetch_array($result_shared_reps)) {

		$qry_shared_rep_users = "select srep_user_id from sls_sales_reps where srep_isactive = 1 and srep_rrnum = '".$row_shared_reps["srep_rrnum"]."'";
    $result_shared_rep_users = mysql_query($qry_shared_rep_users) or die(tdw_mysql_error($qry_shared_rep_users));
		$str_reps = "";
		while($row_shared_rep_users = mysql_fetch_array($result_shared_rep_users)) {
			$str_reps = get_user_by_id($row_shared_rep_users["srep_user_id"]) . " / " . $str_reps;
		}
	?>
					<tr>
						<td width="85">&nbsp;&nbsp;<?=$row_shared_reps["srep_rrnum"]?></td>
						<td>&nbsp;&nbsp;<?=substr($str_reps,0,strlen($str_reps)-3)?></td>
					</tr>
	<?
}
//show existing reps
	//fields are Role  Username  Password  Fullname  Firstname  Lastname  Middlename  
	//Email  Workphone  Mobilephone  Report_via_email  
	//Lastlogin  is_administrator  is_dept_compliance  
	//is_trade_approver  rr_num  user_isactive  login_expiry 
?>
					</table>
				</TD>
			</tr>
		</table>