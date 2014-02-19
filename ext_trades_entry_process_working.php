<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<script type="text/javascript" src="includes/sortable/sortable_us.js"></script>

<style type="text/css">
<!--
a img {
	border: 0;
}
table.sortable {
	border-spacing: 0;
	border-style: solid;
	border-color: #aaa;
	border-width: 1px;
	border-collapse: collapse;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	width: 100%;
	margin: 0 auto;
}

table.sortable th, table.sortable td {
	text-align: left;
	padding: 2px 4px 2px 4px;
	border-style: solid;
	border-color: #aaa;
	font-size: 11px;
}
table.sortable th {
	border-width: 1px 0px 1px 0px;
	background-color: #ccc;
}
table.sortable th a {
	text-decoration: none;
	color: #000;
}
table.sortable td {
	border-width: 0px;
}
table.sortable tr.odd td {
	background-color: #fff;
}
table.sortable tr.even td {
	background-color: #ddd;
}
table.sortable tr.sortbottom td {
	border-width: 1px 0px 1px 0px;
	background-color: #ccc;
	font-weight: bold;
}
-->
</style>

</head>
<body>
<?
include('includes/dbconnect.php');
include('includes/functions.php');

if ($vdate != '') {

/*
xdebug("vdate",$vdate);
xdebug("vaccount",$vaccount);
xdebug("vbuysell",$vbuysell);
xdebug("vsymbol",$vsymbol);
xdebug("vquantity",$vquantity);
xdebug("vprice",$vprice);
xdebug("venteredby",$venteredby);
*/

$str_sql_insert = "INSERT INTO otd_emp_trades_external 
										( auto_id,
											otd_account_id,
											otd_trade_date,
											otd_buysell,
											otd_symbol,
											otd_quantity,
											otd_price,
											otd_entered_by,
											otd_last_edited_by,
											otd_last_edited_on,
											otd_isactive) 
									VALUES (
									NULL , 
									'".$vaccount."', 
									'".format_date_mdy_to_ymd($vdate)."', 
									'".$vbuysell."', 
									'".strtoupper($vsymbol)."', 
									'".$vquantity."', 
									'".$vprice."', 
									'".$venteredby."', 
									'".$venteredby."', 
									now(), 
									'1'
									)";

//echo $str_sql_insert;
$result_insert = mysql_query($str_sql_insert) or die(tdw_mysql_error($str_sql_insert));
$success_str = "<img src='./images/blinkbox.gif' border='0'> Entered ".strtoupper($vsymbol). "(".$vbuysell.": ".$vquantity." @ $".$vprice.")";
}
?>

					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? table_start_percent(100, "Last 20 Trades entered."); ?>
		<div id="content_trd">
					<table class="sortable" id="testid">
						<tr>
						  <th width="100">&nbsp;&nbsp;Trade Date</th>
							<th width="200">&nbsp;&nbsp;Name</th>
							<th class="unsortable">&nbsp;&nbsp;Account</th> 
							<th width="100">&nbsp;&nbsp;Symbol</th>
							<th width="100">&nbsp;&nbsp;Buy/Sell</th>
							<th width="100">&nbsp;&nbsp;Quantity</th>
							<th width="100">&nbsp;&nbsp;Price</th>
							<th class="unsortable">&nbsp;</th>
						</tr>
						<?
						$str_sql_select = "SELECT a. * , b.Fullname, c.oac_account_number, c.oac_custodian
																FROM otd_emp_trades_external a, users b, oac_emp_accounts c
																WHERE a.otd_account_id = c.auto_id
																AND c.oac_emp_userid = b.ID
																ORDER BY a.auto_id DESC limit 20";
            //xdebug("str_sql_select",$str_sql_select);
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{
							if ($count_row_select%2) {
										$class_row_select = "trdark";
							} else { 
									$class_row_select = "trlight"; 
							} 
						?>
						<tr> 
 							<td>&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["otd_trade_date"])?></td>
							<td>&nbsp;&nbsp;<?=$row_select["Fullname"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["oac_account_number"] . "  (".trim($row_select["oac_custodian"]).")"?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_symbol"]?></td>
							<td>&nbsp;&nbsp;<?=offset_buy_sell($row_select["otd_buysell"])?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_quantity"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_price"]?></td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>
		</div>
		<? table_end_percent(); ?>
		</body>
</html>