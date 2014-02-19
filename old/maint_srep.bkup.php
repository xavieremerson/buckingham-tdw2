<?php
//BRG
include('inc_header.php');
 

//show existing reps
	//fields are Role  Username  Password  Fullname  Firstname  Lastname  Middlename  
	//Email  Workphone  Mobilephone  Report_via_email  
	//Lastlogin  is_administrator  is_dept_compliance  
	//is_trade_approver  rr_num  user_isactive  login_expiry 
?>
<?
if ($type == "edit") {
?>
	<? table_start_percent(100, "Edit RR (".$repname.")"); ?>
	<!--TABLE EDIT START-->
	<!--TABLE EDIT END-->
	<? table_end_percent(); ?>
<?
}
?>
	<? table_start_percent(100, "RR Maintenance"); ?>
	<!--TABLE 1 START-->
	<table id="rep_table" width="100%"  border="0" cellspacing="1" cellpadding="1">
		  <thead class="datadisplay">
				<tr bgcolor="#333333" class="tblhead_a">
				  <th width="30">&nbsp;</th>
					<th width="30">&nbsp;</th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 2, false);" title="Registered Rep. Number">RR #</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 3, false);" title="Last Name">Last Name</a></th>
					<th width="80"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 4, false);" title="Buy/Sell">First Name</a></th>
					<th width="200"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 5, false);" title="Price">Full Name</a></th>
					<th width="300"> <a href="" onclick="this.blur(); return sortTable('repTblBdy', 6, false);" title="Price">Shared RR Numbers</a></th>
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
                $repname_esc = htmlspecialchars($row_show_rep["Fullname"], ENT_QUOTES);
				?>
				<tr<?=$class_row?>> 
					<td>&nbsp;<a href="<?=$PHP_SELF?>?type=<?=$type?>&action=remove&ID=<?=$row["ID"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n\n<?=str_replace("'","\'",$row_show_rep["Fullname"])?>\n\nfrom the system?')"><img src="images/themes/standard/delete.gif" alt="Delete"></a></td>
					<td>&nbsp;<a href="<?=$PHP_SELF?>?type=edit&ID=<?=$row["ID"]?>&repname=<?=$repname_esc?>"><img src="images/themes/standard/edit.gif" alt="Edit"></a></td>
					<td><?=$row_show_rep["rr_num"]?></td>
					<td><?=$row_show_rep["Lastname"]?></td>
					<td><?=$row_show_rep["Firstname"]?></td>
					<td><?=$row_show_rep["Fullname"]?></td>
					<td><?=$row_show_rep["rr_num"]?></td>
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