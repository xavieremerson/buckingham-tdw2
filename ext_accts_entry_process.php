<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
</head>
<script language="JavaScript" src="includes/js/popup.js"></script>
<body>
<?
include('includes/dbconnect.php');
include('includes/functions.php');

//xdebug("user_id",$_GET["user_id"]);


if ($vemployee != '') {
/*

xdebug("vemployee",$vemployee);
xdebug("vaccount",$vaccount);
xdebug("vcustodian",$vcustodian);
exit;
xdebug("vsymbol",$vsymbol);
xdebug("vquantity",$vquantity);
xdebug("vprice",$vprice);
xdebug("venteredby",$venteredby);

*/

$str_sql_insert =   "INSERT INTO oac_emp_accounts ( auto_id,
											oac_emp_userid,
											oac_custodian,
											oac_account_number,
											oac_acct_close_date,
											oac_entered_by,
											oac_last_edited_by,
											oac_last_edited_on,
											oac_isactive ) 
											VALUES (
											NULL,
											'".$vemployee."',
											'".$vcustodian."',
											'".$vaccount."', 
											NULL,
											'".$venteredby."',
											'".$venteredby."', 
											NOW(),
											'1'
											)";


//echo $str_sql_insert;
$result_insert = mysql_query($str_sql_insert) or die(tdw_mysql_error($str_sql_insert));

$employee_name = db_single_val("select Fullname as single_val from users where ID = ".$vemployee);
$success_str = "<img src='./images/blinkbox.gif' border='0'> Entered ".strtoupper($employee_name). "(".$vaccount." @ ".$vcustodian.")";
}
?>
<style type="text/css">
<!--
/* Sortable tables */
table.sortable thead {
	background-color:#d5e2f9;
	color:#003399;
	font-weight: bold;
	cursor:pointer;
	cursor:hand;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style>			
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Employee Accounts (External)"); ?>

		
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<script language="JavaScript" src="includes/sortable2/sorttable.js" type="text/javascript"></script>
					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="4">
						<tr>
						  <td width="20" class="sorttable_nosort">&nbsp;&nbsp;Edit</td>
              <td width="60">&nbsp;&nbsp;Status</td>
              <td width="150">&nbsp;&nbsp;Employee</td>
							<td width="150">&nbsp;&nbsp;Account</td> 
							<td width="140">&nbsp;&nbsp;Custodian</td>
							<td width="150">&nbsp;&nbsp;Record Last Edited</td>
							<td width="150">&nbsp;&nbsp;Edited By</td>
              <td width="300" class="sorttable_nosort">&nbsp;&nbsp;Comment</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$str_sql_select = "SELECT a. * , b.Fullname, c.Fullname as editedby
																FROM oac_emp_accounts a, users b, users c
																WHERE a.oac_emp_userid = b.ID
																and a.oac_last_edited_by = c.ID
																ORDER BY b.Fullname";
            //xdebug("str_sql_select",$str_sql_select); //AND oac_isactive = 1
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{
							if ($count_row_select%2) {
										$class_row_select = "trdark";
							} else { 
									$class_row_select = "trlight"; 
							} 
							
							if ($row_select["oac_isactive"] == 1) {
								$str_status = '<font color="green">ACTIVE</font>';
							} else {
								$str_status = '<font color="orange">INACTIVE</font>';
							}
						?>
						<tr class="<?=$class_row_select?>">
              <td style="cursor:pointer;cursor:hand;" align="right" 
                  onclick="CreateWnd('ext_accts_edit_submodal.php?user_id=<?=$user_id?>&acctid=<?=$row_select["auto_id"]?>', 480, 320, false);">EDIT</td>
              <td><?=$str_status?></td> 
							<td><?=$row_select["Fullname"]?></td>
							<td><?=$row_select["oac_account_number"]?></td>
							<td><?=trim($row_select["oac_custodian"])?></td>
							<td sorttable_customkey="<?=date("YmdHis",strtotime($row_select["oac_last_edited_on"]))?>"><?=date("m/d/Y h:ia",strtotime($row_select["oac_last_edited_on"]))?></td>
							<td><?=$row_select["editedby"]?></td>
              <td><?=$row_select["oac_comment"]?></td>
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