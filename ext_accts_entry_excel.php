<?
include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php');

$output_filename = rand(1,9).".xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);

		
$str = '<table width="100%"  border="1" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="36">&nbsp;&nbsp;<strong>#</strong></td>
              <td width="80">&nbsp;&nbsp;<strong>Status</strong></td>
              <td width="200">&nbsp;&nbsp;<strong>Employee</strong></td>
							<td width="150">&nbsp;&nbsp;<strong>Account</strong></td> 
							<td width="140">&nbsp;&nbsp;<strong>Custodian</strong></td>
							<td width="150">&nbsp;&nbsp;<strong>Record Last Edited</strong></td>
							<td width="150">&nbsp;&nbsp;<strong>Edited By</strong></td>
              <td width="300">&nbsp;&nbsp;<strong>Comment</strong></td>
						</tr>';
fputs ($fp, $str);


	$str_sql_select = "SELECT a. * , b.Fullname, c.Fullname as editedby
											FROM oac_emp_accounts a, users b, users c
											WHERE a.oac_emp_userid = b.ID
											and a.oac_last_edited_by = c.ID
											ORDER BY b.Fullname";

	$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

	$count_ = 1;
	while ( $row_select = mysql_fetch_array($result_select) ) 
	{							
		if ($row_select["oac_isactive"] == 1) {
			$str_status = '<font color="green">ACTIVE</font>';
		} else {
			$str_status = '<font color="orange">INACTIVE</font>';
		}
	
	$str = '<tr>
		<td>'.$count_.'</td>
		<td>'.$str_status.'</td> 
		<td>'.$row_select["Fullname"].'</td>
		<td>'.$row_select["oac_account_number"].'</td>
		<td>'.trim($row_select["oac_custodian"]).'</td>
		<td>'.date("m/d/Y h:ia",strtotime($row_select["oac_last_edited_on"])).'</td>
		<td>'.$row_select["editedby"].'</td>
		<td>'.str_replace("<br>"," ",$row_select["oac_comment"]).'</td>
	</tr>';
	fputs ($fp, $str);
	$count_ = $count_ + 1;
	}
	
$str = '</table>
	</body>
</html>';
	fputs ($fp, $str);


Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>