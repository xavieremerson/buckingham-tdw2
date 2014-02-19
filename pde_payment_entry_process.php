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

$payment_type = array();

$payment_type[1] = "Research - Research";
$payment_type[2] = "Research - Independent";
$payment_type[3] = "Research - Geneva";
$payment_type[4] = "Broker-to-Broker";
$payment_type[5] = "Trading 2";
$payment_type[6] = "Other";

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
}

if ($vdate != '') {
/*
xdebug("vdate",$vdate);
xdebug("vamount",$vamount);
xdebug("vclient",$vclient);
xdebug("vcomment",$vcomment);
xdebug("venteredby",$venteredby);
*/

$qry1 = "select * from int_clnt_clients where clnt_code = '".strtoupper($vclient)."'";
$result1 = mysql_query($qry1) or die(tdw_mysql_error($qry1));
while ( $row1 = mysql_fetch_array($result1) ) 
{
  $payout_and = $row1["clnt_rr1"]."#".$row1["clnt_rr2"];
}

$str_sql_insert = "INSERT INTO chk_chek_payments_etc 
										(auto_id,
										 chek_amount,
										 chek_type,
										 chek_advisor,
										 chek_reps_and,
										 chek_comments,
										 chek_date,
										 chek_entered_by,
										 chek_isactive) 
									 VALUES (
											NULL , 
											'".round($vamount,2)."', 
                      '".$vpaymenttype."', 
                      '".strtoupper($vclient)."',
                      '".$payout_and."', 
                      '".$vcomment."', 
                      '".format_date_mdy_to_ymd($vdate)."', 
                      '".$venteredby."', 
                      '1')";
//echo $str_sql_insert;
$result_insert = mysql_query($str_sql_insert) or die(tdw_mysql_error($str_sql_insert));
$success_str = "<img src='./images/blinkbox.gif' border='0'> Entered $".round($vamount,2). " for ".strtoupper($vclient);
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Last 20 Payments entered."); ?>
		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="80">Date</td>
							<td width="60">Client</td>
							<td width="220">Client Name</td> 
							<td width="120">Amount</td>
							<td width="200">Type</td>
							<td width="100">Comments</td>
							<td width="150">Entered By</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$str_sql_select = "SELECT a.*, b.Fullname 
																from chk_chek_payments_etc a, 
                                     Users b 
                                where a.chek_entered_by = b.ID 
																	and a.chek_isactive = 1
                                order by a.auto_id desc limit 20";
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
 							<td>&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["chek_date"])?></td>
							<td>&nbsp;&nbsp;<?=$row_select["chek_advisor"]?></td>
							<td>&nbsp;&nbsp;<?=trim($arr_clients[$row_select["chek_advisor"]])?></td>
							<td align="right"><?=number_format($row_select["chek_amount"],2,'.',",")?>&nbsp;</td>
							<td>&nbsp;&nbsp;<?=$payment_type[$row_select["chek_type"]]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["chek_comments"]?></td>
							<td>&nbsp;&nbsp;<?=trim($row_select["Fullname"])?></td>
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