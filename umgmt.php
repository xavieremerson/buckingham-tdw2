<?php
//BRG
include('inc_header.php');
?>
			<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
			<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
			<!-- helper script that uses the calendar -->
			<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
			<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
			<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
<?
///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////
?>
<? tsp(100, "User Management"); ?>
<?	
		if($action == "remove")
		{
			$query_delete = "UPDATE Users SET user_isactive = '0' WHERE ID = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}
?>
    <form name="zfilter" id="zfilter" action="<?=$PHP_SELF?>" method="post">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td width="170">&nbsp;</td>
        <td width="200" class="quotes"><input name="show_deleted" type="checkbox" value="1" <? if($show_deleted) {echo " checked";}?> /> Show Deleted Users</td>
        <td width="200">
        		<select name="first_initial">
            <option value="">Filter by First Initial</option>
<?
foreach(range('A','Z') as $letter){
  echo '<option value="'.$letter.'"> '.$letter.'</option>';
}
?>       
        </select>    
        </td>
        <td width="150">
          <input type="image" src="images/lf_v1/form_submit.png">
        </td>
        <td width="14" align="center">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>  
    </form>

 <br />
    
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="28"></td>
							<td width="30"></td>
							<td width="180">Name</td>
							<td width="50">Initials</td>
							<td width="40">RR#</td>
							<td width="160">Role</td>
							<td width="230">Primary Email</td>
							<td width="130">Phone: Work</td>
							<td width="110">Login Expiry</td>
							<td>&nbsp;</td>
						</tr>
						<?
						
						if ($show_deleted) {
						  $str_1 = " (a.user_isactive = '1' or a.user_isactive = '0') ";
						} else {
						  $str_1 = " (a.user_isactive = '1') ";
						}

						if ($first_initial != "") {
						  $str_2 = " AND a.Fullname like '".$first_initial."%' ";
						} else {
						  $str_2 = " ";
						}

						
						$query_users = "SELECT a.ID, a.Username, a.Fullname, a.Email, a.Role, a.rr_num, a.Workphone, a.Mobilephone, a.Initials, a.user_isactive,
														DATE_FORMAT(a.login_expiry, '%m/%d/%Y') as login_expiry, 
														DATE_FORMAT(a.login_expiry, '%Y-%m-%d') as proc_login_expiry,
														b.role_name 
														FROM Users a, user_roles b
														WHERE a.Role = b.role_auto_id  
														AND ".$str_1.$str_2." order by a.Fullname";
						//echo $query_trades;
						$result = mysql_query($query_users) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
							if ($count_row%2) {
										$class_row = "trdark";
							} else { 
									$class_row = "trlight"; 
							} 
						?>
						<tr class="<?=$class_row?>">
 							<?
              if ($row["user_isactive"] == 1 ) {
							?>
              <td nowrap>&nbsp; <a href="<?=$PHP_SELF?>?type=<?=$type?>&action=remove&ID=<?=$row["ID"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n\n<?=str_replace("'","\'",$row["Fullname"])?>\n\nfrom the list?')"><img src="images/themes/standard/delete.gif" alt="Delete"></a>&nbsp; </td>
							<td nowrap>&nbsp; <a href="javascript:CreateWnd('umgmt_edit.php?ID=<?=$row["ID"]?>', 550, 450, false);"><img src="images/themes/standard/edit.gif" alt="Edit"></a>&nbsp; </td>
							<?
							} else {
							?>
              <td nowrap="nowrap">&nbsp;  </td>
              <td nowrap="nowrap">&nbsp;  </td>
              <?
							}
							?>
              <td>&nbsp; <?=$row["Fullname"]?></td>
							<td>&nbsp; <?=$row["Initials"]?></td>
							<td>&nbsp; <?=$row["rr_num"]?></td>
							<td>&nbsp; <?=$row["role_name"]?></td>
							<td>&nbsp; <?=trim($row["Email"])?></td>
							<td> <?=$row["Workphone"]?></td>
							<?
							if ( strtotime($row["proc_login_expiry"]) <  strtotime(date('Y-m-d')) ){
							?>
							<td><font color="#FF0000"><?=$row["login_expiry"]?></font></td>
							<?
							} else {
							?>
							<td><?=$row["login_expiry"]?></td>
							<?
							}
							?>
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
/////////////////////////////////////////////////END OF DELETE SECTION/////////////////////////////////////////////////

  include('inc_footer.php');
?>
