<?

  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

if ($mod_request=='save_parent_child') {

	//$result = mysql_query("truncate table pay_analyst_users");
 
 	//print_r($_POST);
	//show_array($_GET);
	
	if ($selectParent == "") {
	  sys_message(3, "No employee selected to which you want to add additional people who will enter preapproval requests on behalf of.");
	} else {
	  if (count($userSelected)>0) {
				$result = mysql_query("delete from etpa_on_behalf where etpa_parent_id = '".$selectParent."'");
				foreach($userSelected as $k=>$v) {
					$qry = "INSERT INTO etpa_on_behalf 
										(
											auto_id, 
											etpa_parent_id, 
											etpa_child_id,
											etpa_entered_by, 
											etpa_date_processed, 
											is_active  
										) VALUES 
										( NULL ,
										 '".$selectParent."',
										 '".$v."',
										 '".$user_id."',
										 now(),
										 '1')";
					//xdebug("qry",$qry);
					$result = mysql_query($qry);
				}

			} else {
	      sys_message(3, "No data to save.");
			}
	}
?>
				<br />&nbsp;&nbsp;<a class="ilt">Select Employee getting approvals on behalf</a>
        <table>
				<tr> 
					<td>
					<select class="Text" name="selectUser" multiple size="8" style="width: 200" onDblClick="move(selectUser, userSelected)">
					<?
								 $qry_result = "select `ID`, `Fullname` from users where `user_isactive` = 1 and `is_login_acct`= 1 
								 								and ID not in (select etpa_child_id from etpa_on_behalf where etpa_parent_id = '".$selectParent."')  
																order by Firstname";
								
								 $result = mysql_query($qry_result);
								 while ( $row = mysql_fetch_array($result) ) 
									{
								 		echo '<option value="' . $row["ID"] . '">' . $row["Fullname"] . '</option>'."\n";
									}
					 ?>							
					</select>
					</td>
					<td>
					<input class="Submit" onclick="move(selectUser, userSelected)" type="button" value="&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;">
					<br>
					<input class="Submit" onclick="move(userSelected,  selectUser)" type="button" value="&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;">
					</td>
					<td>
					<select class="Text" name="userSelected" multiple size="8" style="width: 200" onDblCLick="move(userSelected,  selectUser)">
					<?
								 $qry_result = "select `ID`, `Fullname` from users where `user_isactive` = 1 and `is_login_acct`= 1 
								 								and ID in (select etpa_child_id from etpa_on_behalf where etpa_parent_id = '".$selectParent."')  
																order by Firstname";
								 $result = mysql_query($qry_result);
								 while ( $row = mysql_fetch_array($result) ) 
									{
								 		echo '<option value="' . $row["ID"] . '">' . $row["Fullname"] . '</option>'."\n";
									}
					 ?>							
					</select>
					</td>
  			</tr>
				<tr>
				<td align="left" class="csys_regtext">&nbsp;</td>
				<td colspan="2" align="left"><input class="Submit" name="Save" type="button" value="Save" onclick="javascript:select_all(userSelected);process_data()"></td>
				</tr>
			</table>
<?
	    if (count($userSelected)>0) {
				sys_message(1, "Data saved successfully.");
			}
?>
			<br />
      <hr size="1" noshade color="#0099FF" />
      &nbsp;&nbsp;<a class="ilt">Employee and People who can enter preapprovals on behalf of the employee.</a>
      <hr size="1" noshade color="#0099FF" />
      <table class="ilt">
<?
			//==============================================================================================
			$qry_show = "select a.etpa_parent_id, a.etpa_child_id, b.Fullname as parent_name, c.Fullname as child_name from etpa_on_behalf a
									 left join users b on a.etpa_parent_id = b.ID 
									 left join users c on a.etpa_child_id = c.ID
									 order by b.Fullname, c.Fullname"; 
			$result_show = mysql_query($qry_show);
			$cnt = 1;
			$old_val = "xxx";
			
			while ( $row = mysql_fetch_array($result_show) ) 
			{
				//$arr_data[$row["etpa_parent_id"]."##".$row["etpa_child_id"]] = $row["parent_name"]."##".$row["child_name"];
				if ($row["parent_name"] == $old_val) {
					?>
					<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;<?=$row["child_name"]?></td></tr>
					<?
					$old_val = $row["parent_name"];
				} else {
					?>
					<tr><td><?=$cnt?>.&nbsp;&nbsp;</td><td><?=$row["parent_name"]?></td><td>&nbsp;&nbsp;&nbsp;<?=$row["child_name"]?></td></tr>
					<?
					$old_val = $row["parent_name"];
					$cnt = $cnt + 1;
				}
				
			}
			echo "</table>";
			//show_array($arr_data);
			//==============================================================================================

}

if ($mod_request=='get_parent_child') {

	//$result = mysql_query("truncate table pay_analyst_users");
 
 	//print_r($_POST);
	//show_array($_GET);
?>
				<br />&nbsp;&nbsp;<a class="ilt">Select Employee getting approvals on behalf</a>
        <table>
				<tr> 
					<td>
					<select class="Text" name="selectUser" multiple size="8" style="width: 200" onDblClick="move(selectUser, userSelected)">
					<?
								 if ($selectParent != "") {
									 $qry_result = "select `ID`, `Fullname` from users where `user_isactive` = 1 and `is_login_acct`= 1 
																	and ID not in (select etpa_child_id from etpa_on_behalf where etpa_parent_id = '".$selectParent."')  
																	order by Firstname";
								 } else {
									 $qry_result = "select `ID`, `Fullname` from users where `user_isactive` = 1 and `is_login_acct`= 1 
																	order by Firstname";
								 }
								 $result = mysql_query($qry_result);
								 while ( $row = mysql_fetch_array($result) ) 
									{
								 		echo '<option value="' . $row["ID"] . '">' . $row["Fullname"] . '</option>'."\n";
									}
					 ?>							
					</select>
          								 <? //echo $qry_result; ?>

					</td>
					<td>
					<input class="Submit" onclick="move(selectUser, userSelected)" type="button" value="&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;">
					<br>
					<input class="Submit" onclick="move(userSelected,  selectUser)" type="button" value="&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;">
					</td>
					<td>
					<select class="Text" name="userSelected" multiple size="8" style="width: 200" onDblCLick="move(userSelected,  selectUser)">
					<?
								 $qry_result = "select `ID`, `Fullname` from users where `user_isactive` = 1 and `is_login_acct`= 1 
								 								and ID in (select etpa_child_id from etpa_on_behalf where etpa_parent_id = '".$selectParent."')  
																order by Firstname";
								 $result = mysql_query($qry_result);
								 while ( $row = mysql_fetch_array($result) ) 
									{
								 		echo '<option value="' . $row["ID"] . '">' . $row["Fullname"] . '</option>'."\n";
									}
					 ?>							
					</select>
					</td>
  			</tr>
				<tr>
				<td align="left" class="csys_regtext">&nbsp;</td>
				<td colspan="2" align="left"><input class="Submit" name="Save" type="button" value="Save" onclick="javascript:select_all(userSelected);process_data()"></td>
				</tr>
			</table>

			<br />
      <hr size="1" noshade color="#0099FF" />
      &nbsp;&nbsp;<a class="ilt">Employee and People who can enter preapprovals on behalf of the employee.</a>
      <hr size="1" noshade color="#0099FF" />
      <table class="ilt">
<?
			//==============================================================================================
			$qry_show = "select a.etpa_parent_id, a.etpa_child_id, b.Fullname as parent_name, c.Fullname as child_name from etpa_on_behalf a
									 left join users b on a.etpa_parent_id = b.ID 
									 left join users c on a.etpa_child_id = c.ID
									 order by b.Fullname, c.Fullname"; 
			$result_show = mysql_query($qry_show);
			$cnt = 1;
			$old_val = "xxx";
			
			while ( $row = mysql_fetch_array($result_show) ) 
			{
				//$arr_data[$row["etpa_parent_id"]."##".$row["etpa_child_id"]] = $row["parent_name"]."##".$row["child_name"];
				if ($row["parent_name"] == $old_val) {
					?>
					<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;<?=$row["child_name"]?></td></tr>
					<?
					$old_val = $row["parent_name"];
				} else {
					?>
					<tr><td><?=$cnt?>.&nbsp;&nbsp;</td><td><?=$row["parent_name"]?></td><td>&nbsp;&nbsp;&nbsp;<?=$row["child_name"]?></td></tr>
					<?
					$old_val = $row["parent_name"];
					$cnt = $cnt + 1;
				}
				
			}
			echo "</table>";
			//show_array($arr_data);
			//==============================================================================================
}
?>