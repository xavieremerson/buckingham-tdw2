<?
//BRG
include('inc_header.php');
 

//show existing reps
	//fields are Role  Username  Password  Fullname  Firstname  Lastname  Middlename  
	//Email  Workphone  Mobilephone  Report_via_email  
	//Lastlogin  is_administrator  is_dept_compliance  
	//is_trade_approver  rr_num  user_isactive  login_expiry 
?>
<?
if ($action == "remove") {
  $query_del_rep = "DELETE from sls_sales_reps where srep_user_id='".$ID."'";
  $result_del_rep = mysql_query($query_del_rep) or die(mysql_error());
  $query_del_rep2 = "DELETE from tmp_users where ID='".$ID."'";
  $result_del_rep2 = mysql_query($query_del_rep2) or die(mysql_error());
}

	table_start_percent(100, "RR Maintenance"); ?>
	<script language="JavaScript" src="includes/js/popup.js"></script>
	<!--TABLE 1 START-->
	<table id="rep_table" width="100%"  border="0" cellspacing="1" cellpadding="1">
		  <thead class="datadisplay">
				<tr bgcolor="#333333" class="tblhead_a">
				  <!--<th width="30">&nbsp;</th>-->
					<th width="30">&nbsp;</th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 1, false);" title="Registered Rep. Number">RR #</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 2, false);" title="Last Name">Last Name</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 3, false);" title="First Name">First Name</a></th>
					<th width="200"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 4, false);" title="Full Name">Full Name</a></th>
					<th width="300"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 5, false);" title="Shared RR Numbers">Shared RR Numbers</a></th>
					<th>&nbsp;</th>
				</tr>
  		</thead>
  		<tbody id="repTblBdy" class="datadisplay">
			<?
			$query_show_rep = "SELECT * from users where Role = 3 and user_isactive = '1' order by Lastname";
			$result_show_rep = mysql_query($query_show_rep) or die(mysql_error());
			$count_rep = 1;
			while($row_show_rep = mysql_fetch_array($result_show_rep))
			{
				//xdebug('row__show_rep["Fullname"]',$row_show_rep["Fullname"]);
				if ($count_rep % 2) { 
						$class_row = ' class="alternateRow"';
				} else { 
						$class_row = ''; 
				}
				// HTML encode names like Kevin O'Gorman for display in anchor tag
                $repname_esc = htmlspecialchars($row_show_rep["Fullname"], ENT_QUOTES);
				?>
				<tr<?=$class_row?>> 
					<!--<td>&nbsp;<a href="<?=$PHP_SELF?>?type=delete&action=remove&ID=<?=$row_show_rep["ID"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n\n<?=str_replace("'","\'",$row_show_rep["Fullname"])?>\n\nfrom the system?')"><img src="images/themes/standard/delete.gif" alt="Delete"></a></td>-->
					<td>&nbsp;<a href='javascript:CreateWnd("maint_srep_edit.php?ID=<?=$row_show_rep["ID"]?>&repname=<?=$repname_esc?>", 420, 300, false);'><img src="images/themes/standard/edit.gif" alt="Edit"></a></td>
					<td><?=$row_show_rep["rr_num"]?></td>
					<td><?=$row_show_rep["Lastname"]?></td>
					<td><?=$row_show_rep["Firstname"]?></td>
					<td><?=$row_show_rep["Fullname"]?></td>
                    <?
					$query_show_rrnum = "SELECT srep_rrnum from sls_sales_reps where srep_user_id='".$row_show_rep['ID']."' order by srep_rrnum";
					$result_show_rrnum = mysql_query($query_show_rrnum) or die(mysql_error());
					$str_concatenated = "";
					$count_rrnum = 1;
					while($row_show_rrnum = mysql_fetch_array($result_show_rrnum))
					{
						if ($count_rrnum > 1 and $str_concatenated != NULL)
						{
							$str_concatenated .=  ", ";
						}
						$str_concatenated .=  $row_show_rrnum['srep_rrnum'];
						$count_rrnum++;
					}
					?>		
					<td><?=$str_concatenated?></td>
					<td>&nbsp;</td>
				</tr>
				<?
			$count_rep = $count_rep + 1;
			}
			?>
		</tbody>
	</table>
	<!-- TABLE 2 END -->
	<? table_end_percent(); ?>

<?
include('inc_footer.php');
?>