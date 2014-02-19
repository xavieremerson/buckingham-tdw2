<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = "EmployeeAccounts.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);

$str = '<table width="100%"  border="1" cellspacing="1" cellpadding="1">
				<tr>
              <td nowrap="nowrap"><strong>Acct. Num</strong></td>
              <td nowrap="nowrap"><strong>Employee Name [TDW]</strong></td>
              <td nowrap="nowrap"><strong>Name Address Line 1</strong></td>
              <td nowrap="nowrap"><strong>Name Address Line 2</strong></td>
              <td nowrap="nowrap"><strong>Name Address Line 3</strong></td>
              <td nowrap="nowrap"><strong>Name Address Line 4</strong></td>
              <td nowrap="nowrap"><strong>Name Address Line 5</strong></td>
              <td nowrap="nowrap"><strong>Name Address Line 6</strong></td>
              <td nowrap="nowrap"><strong>Establish Date</strong></td>
							<td nowrap="nowrap"><strong>Account Status</strong></td>
							<td nowrap="nowrap"><strong>Close Date</strong></td>
							<td nowrap="nowrap"><strong>Comments</strong></td>
					<td><strong>&nbsp;</strong></td>
			</tr>';
fputs ($fp, $str);

if ($mode == "active") {
	$str_filter = " where emp_acct_status = 1 ";
} else {
	$str_filter = "";
}

 $qry_clist = "SELECT * FROM emp_employee_accounts_master ".$str_filter." ORDER BY emp_acct_number"; 
 $result_clist = mysql_query($qry_clist) or die(tdw_mysql_error($qry_clist));
	

 while ($row = mysql_fetch_array($result_clist)) {

	if ($row["emp_acct_status"] == 1) { $str_active = "<font color='green'>ACTIVE</font>"; } else { $str_active = "<font color='red'>CLOSED</font>"; }
	
   $str = '<tr>
						<td>'. $row["emp_acct_number"] .'</td>
						<td>'. get_user_by_id($row["emp_user_id"]) .'</td>
						<td>'. $row["emp_name_and_address_1"] .'</td>
						<td>'. $row["emp_name_and_address_2"] .'</td>
						<td>'. $row["emp_name_and_address_3"] .'</td>
						<td>'. $row["emp_name_and_address_4"] .'</td>
						<td>'. $row["emp_name_and_address_5"] .'</td>
						<td>'. $row["emp_name_and_address_6"] .'</td>
						<td>'. format_date_ymd_to_mdy($row["emp_establish_date"]) .'</td>
						<td>'. $str_active .'</td>
						<td>'. format_date_ymd_to_mdy($row["emp_close_date"]) .'</td>
						<td>'. $row["emp_close_date"] .'</td>
						<td>&nbsp;</td>
					</tr>';
		fputs ($fp, $str);

	}

$str = '</table>
	</body>
</html>';
fputs ($fp, $str);


fclose($fp);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>