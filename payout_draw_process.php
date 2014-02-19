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


//Create Lookup Array of Client Code / Client Name
$qry_users = "select ID, Fullname from users";
$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));
$arr_users = array();
while ( $row_users = mysql_fetch_array($result_users) ) 
{
	$arr_users[$row_users["ID"]] = $row_users["Fullname"];
}

if ($vrep != '') {
/*
xdebug("vdate",$vdate);
xdebug("vamount",$vamount);
xdebug("vclient",$vclient);
xdebug("vcomment",$vcomment);
xdebug("venteredby",$venteredby);
*/

$result_update = mysql_query("update payout_draw set payout_isactive = 0, payout_edited_on = now() where user_id = '".$vrep."'");

$str_sql_insert = "INSERT INTO payout_draw 
										(auto_id,
										 user_id,
										 payout_draw,
										 payout_edited_by,
										 payout_edited_on,
										 payout_comment,
										 payout_isactive) 
									 VALUES (
											NULL , 
											'".$vrep."', 
                      '".$vamount."', 
                      '".$venteredby."',
                      now(), 
                      '".$vcomment."', 
                      '1')";
//echo $str_sql_insert;
$result_insert = mysql_query($str_sql_insert) or die(tdw_mysql_error($str_sql_insert));
$success_str = "<img src='./images/blinkbox.gif' border='0'> Entered $".round($vamount,2). " for ".strtoupper($arr_users[$vrep]);
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Draw Data."); ?>
		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td width="200">Rep. / Trader</td>
							<td width="200">Draw Amount</td>
							<td width="200">Comments</td>
							<td width="200">Entered / Edited By</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$str_sql_select = "SELECT a.auto_id, a.user_id, a.payout_draw, a.payout_edited_by, a.payout_edited_on, a.payout_comment, a.payout_isactive, 
																concat(b.Firstname, ' ', b.Lastname ) as rep_name
																FROM payout_draw a, Users b 
																WHERE a.payout_isactive = 1 
                                AND a.user_id = b.ID 
                                order by b.Lastname";
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
 							<td>&nbsp;&nbsp;<?=$row_select["rep_name"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["payout_draw"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["payout_comment"]?></td>
 							<td>&nbsp;&nbsp;<?=$arr_users[$row_select["payout_edited_by"]]?></td>
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