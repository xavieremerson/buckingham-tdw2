<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
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
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Last 20 Trades entered."); ?>
		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr class="ilt">
						  <td width="100">&nbsp;&nbsp;Trade Date</td>
							<td width="200">&nbsp;&nbsp;Name</td>
							<td width="300">&nbsp;&nbsp;Account</td> 
							<td width="60">&nbsp;&nbsp;Symbol</td>
							<td width="100">&nbsp;&nbsp;Buy/Sell</td>
							<td width="100">&nbsp;&nbsp;Quantity</td>
							<td width="100">&nbsp;&nbsp;Price</td>
							<td width="200">&nbsp;&nbsp;Date Entered</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$str_sql_select = "SELECT a. * , date_format(a.otd_last_edited_on, '%c/%e/%Y %h:%i%p' ) as date_added, b.Fullname, c.oac_account_number, c.oac_custodian
																FROM otd_emp_trades_external a, users b, oac_emp_accounts c
																WHERE a.otd_account_id = c.auto_id
																AND c.oac_emp_userid = b.ID
																AND a.otd_isactive = 1
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
						<tr class="<?=$class_row_select?>"> 
 							<td>&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["otd_trade_date"])?></td>
							<td>&nbsp;&nbsp;<?=$row_select["Fullname"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["oac_account_number"] . "  (".trim($row_select["oac_custodian"]).")"?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_symbol"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_buysell"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_quantity"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["otd_price"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["date_added"]?></td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		<? tep(); ?>
		</body>
</html>