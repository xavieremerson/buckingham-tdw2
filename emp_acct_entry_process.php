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

if (trim($emp_acct_number) != "") {

//print_r($_GET);

//exit;


	if (trim($emp_acct_number) != "") {
				
      $qry = "INSERT INTO emp_employee_accounts_master (
								auto_id,
								emp_user_id,
								emp_acct_number,
								emp_name_and_address_1,
								emp_name_and_address_2,
								emp_name_and_address_3,
								emp_name_and_address_4,
								emp_name_and_address_5,
								emp_name_and_address_6,
								emp_establish_date,
								emp_acct_status,
								emp_comments,
								emp_close_date,
								emp_closed_by,
								emp_last_edit_by,
								emp_last_edit_time,
								emp_last_edit_ip 
							)
							VALUES (
							NULL,
								'".$emp_user_id."',
								'".trim(strtoupper($emp_acct_number))."',
								'".trim(strtoupper(str_replace("'","\\'",$emp_name_and_address_1)))."',
								'".trim(strtoupper(str_replace("'","\\'",$emp_name_and_address_2)))."',
								'".trim(strtoupper(str_replace("'","\\'",$emp_name_and_address_3)))."',
								'".trim(strtoupper(str_replace("'","\\'",$emp_name_and_address_4)))."',
								'".trim(strtoupper(str_replace("'","\\'",$emp_name_and_address_5)))."',
								'".trim(strtoupper(str_replace("'","\\'",$emp_name_and_address_6)))."',
								'".format_date_mdy_to_ymd($emp_establish_date)."',
								1,
								'".str_replace("'","\\'",$emp_comments)."',
								NULL,
								NULL,
								'".$venteredby."',
								now(),
								'".$_SERVER['REMOTE_ADDR']."')";


			$result = mysql_query($qry) or die(tdw_mysql_error($qry));
			$str_status = "<font color='green'>Data saved.</font>";
		} else {
			$str_status = "<font color='red'>Data not saved. Account Number is missing. Please try again.</font>";
		}



$success_str = "<img src='./images/blinkbox.gif' border='0'> ".$str_status;
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "Employee Account List [ACTIVE]"); ?>
		
    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
					<tr>
							<td>Edit</td>
              <td nowrap="nowrap">Acct. Num</td>
              <td nowrap="nowrap">Employee Name [TDW]</td>
              <td nowrap="nowrap">Name Address Line 1</td>
              <td nowrap="nowrap">Name Address Line 2</td>
              <td nowrap="nowrap">Name Address Line 3</td>
              <td nowrap="nowrap">Name Address Line 4</td>
              <td nowrap="nowrap">Name Address Line 5</td>
              <td nowrap="nowrap">Name Address Line 6</td>
              <td nowrap="nowrap">Establish Date</td>
							<td>&nbsp;</td>
					</tr>

				 <?
				 $max_row_id = db_single_val("select max(auto_id) as single_val from emp_employee_accounts_master");
				
				 $qry_alist = "SELECT * FROM emp_employee_accounts_master  
											 where emp_acct_status = 1 
											 ORDER BY emp_name_and_address_1"; //bcm_cusip, bcm_datetime_stop 
				 $result_alist = mysql_query($qry_alist) or die(tdw_mysql_error($qry_alist));
					
				 $hold_symbol = "";
				 $count_row = 0;
				 while ($row = mysql_fetch_array($result_alist)) {
								if ($count_row%2 == 0) {
									$rowclass = " class=\"trlight\"";
								} else {
									$rowclass = " class=\"trdark\"";
								}
						?>
						<tr <?=$rowclass?>>
							<td><a href="javascript:CreateWnd('emp_acct_entry_edit.php?cid=<?=$row["auto_id"]?>&uid=<?=$uid?>', 745, 410, false);">
              			<img src="images/themes/standard/edit.gif" border="0" alt="Edit" />
                  </a>
              </td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=$row["emp_acct_number"]?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=get_user_by_id($row["emp_user_id"])?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=$row["emp_name_and_address_1"]?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=$row["emp_name_and_address_2"]?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=$row["emp_name_and_address_3"]?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=$row["emp_name_and_address_4"]?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=$row["emp_name_and_address_5"]?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=$row["emp_name_and_address_6"]?></td>
              <td nowrap="nowrap">&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["emp_establish_date"])?></td>
							<td>&nbsp;</td>
						</tr>
						<?
						$count_row++;
					}
					?>
					</table>
					</div>
					</div>
					<? tep(); ?>
          
		</td>
	</tr>
</table>
		<? tep(); ?>
		</body>
</html>