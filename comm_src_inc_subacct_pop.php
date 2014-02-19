<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>[TDW] Sub Account Details</title>
</head>
<?
//Since this is a AJAX requested page, all inputs to this page should be passed with the param string
//Also, all the relevant includes should be a part of this page including css, etc
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');

if ($_GET) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
//assign the variables here
$rep_to_process = $rr;
$trade_date_to_process = $dt;
$process_advisor_code_subacct = $adv;
} else {
$rep_to_process = "028";
$process_advisor_code_subacct = "MILP";
$trade_date_to_process = previous_business_day();
}


include('comm_src_inc_main_subaccount.php');

$arr_subacct = array();
$qry = "select 
					nadd_full_account_number,
					nadd_short_name 
				from mry_nfs_nadd 
				where nadd_advisor = '".$process_advisor_code_subacct."'
				order by nadd_short_name";
//xdebug("qry",$qry);
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while($row = mysql_fetch_array($result)) {
	$arr_subacct[$row["nadd_full_account_number"]] = $row["nadd_short_name"];
}
//show_array($arr_subacct);





?>

<link href="includes/styles.css" rel="stylesheet" type="text/css">
<!--onload="autofitIframe('ca_trades')"--> 

<style type="text/css">
<!--
.headrow {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #0000CC;
	background-color: #DDDDDD;
}
-->
</style>
<body>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr class="headrow"> 
		<td align="left">Subaccount Name</td>
		<td>&nbsp;&nbsp;Rep.</td>
		<td align="right">TDate&nbsp; &nbsp;</td>
		<td align="right">MTD&nbsp; &nbsp;</td>
		<td align="right">QTD&nbsp; &nbsp;</td>
		<td align="right">YTD&nbsp; &nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<?
$xcount = 2;
foreach($arr_subacct as $acctnum=>$acctname) {
	if ($xcount % 2) { 
			$class_row = "trdarksub";
	} else { 
			$class_row = "trlightsub"; 
	} 
?>
	<tr class="<?=$class_row?>"> 
		<td align="left">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$acctname?>&nbsp;&nbsp;</td>
		<td>&nbsp;&nbsp;<?=$rep_to_process?></td>
		<td align="right"><?=number_format($arr_day_comm[$acctnum],0,'.',",")?>&nbsp; &nbsp;</td>
		<td align="right"><?=number_format($arr_mtd_comm[$acctnum],0,'.',",")?>&nbsp; &nbsp;</td>
		<td align="right"><?=number_format($arr_qtd_comm[$acctnum],0,'.',",")?>&nbsp; &nbsp;</td>
		<td align="right"><?=number_format($arr_ytd_comm[$acctnum],0,'.',",")?>&nbsp; &nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<?
$xcount++;
}
?>
																				
	<tr> 
		<td width="150" align="left">&nbsp;</td>
		<td width="60">&nbsp;&nbsp;</td>
		<td width="70" align="right">&nbsp; &nbsp;</td>
		<td width="70" align="right">&nbsp; &nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="80"align="right">&nbsp; &nbsp;</td>
		<td width="70">&nbsp;</td>
		<td width="70" align="right">&nbsp; &nbsp;</td>
		<td>&nbsp;</td>
	</tr>

</table>
</body>