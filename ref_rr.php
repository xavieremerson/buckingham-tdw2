<?
if ($_GET["action"]=="excel") {

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

	$output_filename = "rr_list.xls";
	$fp = fopen($exportlocation.$output_filename, "w");
	
	$str = '<html xmlns="http://www.w3.org/1999/xhtml">
					<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
					<body>';
	fputs ($fp, $str);
	
	
	$str = '<table width="900" border="1" cellspacing="0" cellpadding="0">
						<tr>
							<td width="30"><b>#</b></td>
							<td width="40"><b>RR#</b></td>
							<td width="275"><b>Name</b></td>
							<td width="180"><b>Role</b></td>
							<td width="230"><b>Primary Email</b></td>
							<td width="145"><b>Phone: Work</b></td>
						</tr>';
	fputs ($fp, $str);

	$qry_reps_primary = "select a.*, b.role_name 
											 FROM Users a, user_roles b
											 WHERE a.Role = b.role_auto_id
												 AND a.rr_num not like '%999%' 
												 AND rr_num is not null 
												 AND rr_num != '' 
												 AND user_isactive = 1 
											 ORDER BY rr_num";
	$result_reps_primary = mysql_query($qry_reps_primary) or die(tdw_mysql_error($qry_reps_primary));
	$count_row = 0;
	while ( $row = mysql_fetch_array($result_reps_primary) ) 
	{
		$str = '<tr>
							<td nowrap="nowrap">&nbsp; '.($count_row+1).' </td>
							<td>&nbsp; '.$row["rr_num"].'</td>
							<td>&nbsp; '.$row["Fullname"].'</td>
							<td>&nbsp; '.$row["role_name"].'</td>
							<td>&nbsp; '.trim($row["Email"]).'</td>
							<td> '.$row["Workphone"].'</td>
						</tr>';
		fputs ($fp, $str);
		$count_row = $count_row + 1;
	}

	$str = '</table>';
	fputs ($fp, $str);
								

  //NOW SHARED REP NUMBERS

	$str = '<br><br><table width="900" border="1" cellspacing="0" cellpadding="0">
						<tr>
							<td width="30"><b>#</b></td>
							<td width="40"><b>RR#</b></td>
							<td width="275"><b>Name(s)</b></td>
							<td width="180">&nbsp;</td>
							<td width="230">&nbsp;</td>
							<td width="145">&nbsp;</td>
						</tr>';
	fputs ($fp, $str);								
	
	$qry_shared_reps = "select distinct(srep_rrnum) from sls_sales_reps  where srep_isactive = 1 and srep_rrnum != '' order by srep_rrnum";
	$result_shared_reps = mysql_query($qry_shared_reps) or die(tdw_mysql_error($qry_shared_reps));
	$count_row = 0;
	while ( $row = mysql_fetch_array($result_shared_reps) ) 
	{
		$qry_shared_rep_users = "select srep_user_id from sls_sales_reps where srep_isactive = 1 and srep_rrnum = '".$row["srep_rrnum"]."'";
		$result_shared_rep_users = mysql_query($qry_shared_rep_users) or die(tdw_mysql_error($qry_shared_rep_users));
		$str_reps = "";
		while($row_shared_rep_users = mysql_fetch_array($result_shared_rep_users)) {
			$str_reps = get_user_by_id($row_shared_rep_users["srep_user_id"]) . " / " . $str_reps;
		}
			$str_reps = substr($str_reps,0,(strlen($str_reps)-2));

	$str = '<tr>
						<td nowrap="nowrap">&nbsp; '.($count_row+1).' </td>
						<td>&nbsp; '.$row["srep_rrnum"].'</td>
						<td>&nbsp; '.$str_reps.'</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';
	fputs ($fp, $str);								
	$count_row = $count_row + 1;
	}

	$str = '</table>';
	fputs ($fp, $str);
	
$str = '</body>
			</html>';
fputs ($fp, $str);

fclose($fp);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);

//absolutely positively exit after this step.
exit;

} else {

//========================================================================================================
			include('inc_header.php');
			?>
			&nbsp;&nbsp;<a href="<?=$PHP_SELF?>?action=excel" target="_blank"><img src="images/lf_v1/exp2excel.png" alt="Export to Excel" border="0"></a><br>
			<? tsp(100, "List of Registered Representatives [PRIMARY]"); ?>
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td>
								<!--TABLE 2 START-->
								<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
			
								<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
									<tr>
										<td width="30">#</td>
										<td width="40">RR#</td>
										<td width="180">Name</td>
										<td width="160">Role</td>
										<td width="230">Primary Email</td>
										<td width="130">Phone: Work</td>
										<td>&nbsp;</td>
									</tr>
									<?
			
									$qry_reps_primary = "select a.*, b.role_name 
																			 FROM Users a, user_roles b
																			 WHERE a.Role = b.role_auto_id
																				 AND a.rr_num not like '%999%' 
																				 AND rr_num is not null 
																				 AND rr_num != '' 
																				 AND user_isactive = 1 
																			 ORDER BY rr_num";
									$result_reps_primary = mysql_query($qry_reps_primary) or die(tdw_mysql_error($qry_reps_primary));
									$count_row = 0;
									while ( $row = mysql_fetch_array($result_reps_primary) ) 
									{
										if ($count_row%2) {
													$class_row = "trdark";
										} else { 
												$class_row = "trlight"; 
										} 
									?>
									<tr class="<?=$class_row?>">
										<td nowrap="nowrap">&nbsp; <?=($count_row+1)?> </td>
										<td>&nbsp; <?=$row["rr_num"]?></td>
										<td>&nbsp; <?=$row["Fullname"]?></td>
										<td>&nbsp; <?=$row["role_name"]?></td>
										<td>&nbsp; <?=trim($row["Email"])?></td>
										<td> <?=$row["Workphone"]?></td>
										<td></td>
									</tr>
									<?php
									$count_row = $count_row + 1;
									}
									?>
								</table>
							</td>
						</tr>
					</table>
				
					<? tep(); ?>
			
			<br>
			<br>
			
			<? tsp(100, "List of SHARED REP. NUMBERS"); ?>
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td>
								<!--TABLE 2 START-->
								<!--<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>-->
			
								<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
									<tr>
										<td width="30">#</td>
										<td width="40">RR#</td>
										<td width="180">Name(s)</td>
										<td>&nbsp;</td>
									</tr>
									<?
			
									$qry_shared_reps = "select distinct(srep_rrnum) from sls_sales_reps  where srep_isactive = 1 and srep_rrnum != '' order by srep_rrnum";
									$result_shared_reps = mysql_query($qry_shared_reps) or die(tdw_mysql_error($qry_shared_reps));
									$count_row = 0;
									while ( $row = mysql_fetch_array($result_shared_reps) ) 
									{
									
										$qry_shared_rep_users = "select srep_user_id from sls_sales_reps where srep_isactive = 1 and srep_rrnum = '".$row["srep_rrnum"]."'";
										$result_shared_rep_users = mysql_query($qry_shared_rep_users) or die(tdw_mysql_error($qry_shared_rep_users));
										$str_reps = "";
										while($row_shared_rep_users = mysql_fetch_array($result_shared_rep_users)) {
											$str_reps = get_user_by_id($row_shared_rep_users["srep_user_id"]) . " / " . $str_reps;
										}
										
											$str_reps = substr($str_reps,0,(strlen($str_reps)-2));
			
										if ($count_row%2) {
													$class_row = "trdark";
										} else { 
												$class_row = "trlight"; 
										} 
									?>
									<tr class="<?=$class_row?>">
										<td nowrap="nowrap">&nbsp; <?=($count_row+1)?> </td>
										<td>&nbsp; <?=$row["srep_rrnum"]?></td>
										<td>&nbsp; <?=$str_reps?></td>
										<td></td>
									</tr>
									<?php
									$count_row = $count_row + 1;
									}
									?>
								</table>
							</td>
						</tr>
					</table>
				
					<? tep(); ?>
			
					
			<?
				include('inc_footer.php');
//========================================================================================================
}
?>
