<?
//BRG
include('inc_header.php');
?>

<?
	tsp(100, "RR Maintenance"); 
?>
	<script language="JavaScript" src="includes/js/popup.js"></script>
	<!--TABLE 1 START-->
	<table id="rep_table" width="100%"  border="0" cellspacing="1" cellpadding="1">
		  <thead class="datadisplay">
				<tr bgcolor="#333333" class="tblhead_a">
				  <!--<th width="30">&nbsp;</th>-->
					<th width="30">&nbsp;</th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 1, false);" title="Registered Rep. Number">RR #</a></th>
					<th width="200"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 2, false);" title="Full Name">Full Name</a></th>
					<th>&nbsp;</th>
				</tr>
  		</thead>
  		<tbody id="repTblBdy" class="datadisplay">

<?
//Now get shared Reps List
$qry_shared_reps = "select distinct(srep_rrnum) from sls_sales_reps  where srep_rrnum != '' and srep_isactive = 1 order by srep_rrnum";
$result_shared_reps = mysql_query($qry_shared_reps) or die(tdw_mysql_error($qry_shared_reps));
$count_rep = 1;
while($row_shared_reps = mysql_fetch_array($result_shared_reps)) {

	if ($count_rep % 2) { 
			$class_row = ' class="alternateRow"';
	} else { 
			$class_row = ''; 
	}

		$qry_shared_rep_users = "select srep_user_id from sls_sales_reps where srep_isactive = 1 and srep_rrnum = '".$row_shared_reps["srep_rrnum"]."'";
    $result_shared_rep_users = mysql_query($qry_shared_rep_users) or die(tdw_mysql_error($qry_shared_rep_users));
		$str_reps = "";
		while($row_shared_rep_users = mysql_fetch_array($result_shared_rep_users)) {
			$str_reps = get_user_by_id($row_shared_rep_users["srep_user_id"]) . " / " . $str_reps;
		}
	?>
				<tr<?=$class_row?>> 
					<!--
					<td>&nbsp;<a href="<?=$PHP_SELF?>?type=delete&action=remove&ID=<?=$row_show_rep["ID"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n\n<?=str_replace("'","\'",$row_show_rep["Fullname"])?>\n\nfrom the system?')"><img src="images/themes/standard/delete.gif" alt="Delete"></a></td>
					-->
					<td>&nbsp;<a href='javascript:CreateWnd("maint_rr_edit.php?srep_num=<?=$row_shared_reps["srep_rrnum"]?>&user_id=<?=$user_id?>", 420, 170, false);'><img src="images/themes/standard/edit.gif" alt="Edit"></a></td>
					<td><?=$row_shared_reps["srep_rrnum"]?></td>
					<td><?=substr($str_reps,0,strlen($str_reps)-3)?></td>
					<td>&nbsp;</td>
				</tr>
	<?


$count_rep = $count_rep + 1;
}
?>			
		</tbody>
	</table>
	<!-- TABLE 2 END -->
	<? tep(); ?>



<?
include('inc_footer.php');
?>