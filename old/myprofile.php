<?php

//needs fixing, start from template
include('top.php');
include('includes/functions.php'); 
?>
		<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
		<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
		<!-- helper script that uses the calendar -->
		<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
		<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
		<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
<?
	if($action == 'remove')
	{
		$query_remove = "UPDATE arep_adhoc_reports SET arep_isactive = '0' WHERE arep_auto_id = '".$id."'";
		$result_remove = mysql_query($query_remove) or die(mysql_error());
		
		$query_remove = "UPDATE rdat_report_data SET rdat_isactive = '0' WHERE rdat_repo_id = '".$id."' AND rdat_report_type = '".$type."'";
		$result_remove = mysql_query($query_remove) or die(mysql_error());
	}

	if ($submit) 
	{
		$result = mysql_query("Update Users set Username = '$Username', Fullname = '$Fullname', Email = '$Email', Workphone = '$Workphone', Mobilephone = '$Mobilephone', Report_via_email = '$Report_via_email' where ID = '$ID'") or die (mysql_error());
		$arr_reverse_report = array_flip($reportid);
		
		foreach( $arr_reverse_report as $key => $value )
		{			
			$new_time = format_time_to_military($time[$value]);
			$query_update_report = "UPDATE rdat_report_data SET rdat_rfre_id = '".$frequency[$value]."', rdat_rmod_id='".$mode[$value]."', rdat_time='".$new_time."', rdat_get_report='".$rep_select[$value]."' WHERE rdat_auto_id = ".$reportid[$value];	
			$result_update_report = mysql_query($query_update_report) or die(mysql_error());
		}
		
		for($i = 0; $i < count($components); $i++)
		{
		//	echo '<BR> user authorized ' .  $components[$i]; 
		//	echo '<BR> component id  ' .  $comp_id[$i]; 
			$query_update = "UPDATE cdas_customize_dashboard SET cdas_user_authorized = '".$components[$i]."' WHERE cdas_user_id = '$ID' AND cdas_component_id = '".$comp_id[$i]."'";
			$result_update = mysql_query($query_update) or die(mysql_error());
		}
		
		echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;<a class="links10">&nbsp;&nbsp;Profile Saved!</a>';
	}
	
	$resultmember = mysql_query("SELECT ID, Username, Password, Fullname, Email, Workphone, Mobilephone, Report_via_email FROM Users where ID = $user_id") or die (mysql_error());
	while ( $row = mysql_fetch_array($resultmember) ) 
	{
		$ID = $row["ID"];
		$Username = $row["Username"];
		$Password = $row["Password"];
		$Fullname = $row["Fullname"];
		$Email = $row["Email"];
		$Workphone = $row["Workphone"];
		$Mobilephone = $row["Mobilephone"];
		$Report_via_email = $row["Report_via_email"];
	}
	
	//GET REPORTS THAT THIS USER HAS SELECTED
	$get_report_data = "SELECT * FROM rdat_report_data WHERE rdat_user_id = $user_id AND rdat_isactive = '1'";
	$result_report_data = mysql_query($get_report_data) or die(mysql_error());
		
	//CUSTOMIZE DASHBOARD	
	$query_dash = "SELECT cdas_component_id, cdas_user_authorized FROM cdas_customize_dashboard WHERE cdas_user_id = '".$user_id."' AND cdas_isactive = '1' AND cdas_admin_authorized = '1'";
	$result_dash = mysql_query($query_dash) or die(mysql_error());
		
?>

	<form action="<?=$php_self?>" method="post">
	<tr valign="top">
		<td>
		<table>
		<td  width="520">
			<? table_start(520, "Personal Information"); ?>
			<table cellpadding="0" cellspacing="8" border="0">
				<tr>
					<td>
						<table cellspacing="11" >
							<tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Username</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="Username" value="<?=$Username?>" size="20" maxlength="20"></p></td></tr>
							<tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Full Name</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="Fullname" size="40" maxlength="100" value="<?=$Fullname?>"></p></td></tr>
							<tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Email</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="Email" size="40" maxlength="40" value="<?=$Email?>"></p></td><td></tr>
							<tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Work Phone</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="Workphone" size="40" maxlength="20" value="<?=$Workphone?>"></p></td></tr>
							<tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Cell Phone</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="Mobilephone" size="40" maxlength="50" value="<?=$Mobilephone?>"></p></td></tr>
							<tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Get Reports (Email)</p></td><td><p class="Contact">:</p></td><td><p><select class="Text" name="Report_via_email" size="1"><option value="1" <? if ($Report_via_email=='1') {echo 'selected';}?>>Yes</option><option value="0" <? if ($Report_via_email=='0') {echo 'selected';}?>>No</option></select></p></td></tr>
						</table>
					</td>
				</tr>
			</table>
			<? table_end(); ?>
			</td>
			<td width="20" align="left">&nbsp;</td>
			<td width="400" align="left" valign="top">
			<? table_start(400, "Dashboard Customization"); ?>
			<table cellpadding="2" cellspacing="0" border="0" width="70%">
				<tr>
				<td valign="top" align="left">
						<fieldset style="BORDER-RIGHT: #9999ff 0px solid; 
							BORDER-TOP: #9999ff 0px solid; 
							BORDER-LEFT: #9999ff 0px solid;
							PADDING: 2px;
							WIDTH: 270px; 
							HEIGHT: 225px;
							
							BORDER-BOTTOM: #9999ff 0px solid"> 
					
							<table width="100%" height="100%" class="dashboard">
	
								<?
								if(mysql_num_rows($result_dash) < 1)
								{
									echo '<tr>
											<td><a class= "links12" >You do not have component priviliges for your dashboard.<BR><BR> Contact your System Administrator.</a></td>
										  </tr>';
								}
								else
								{
									while($row_dash = mysql_fetch_array($result_dash))
									{
										$query_components = "SELECT comp_name FROM comp_components WHERE comp_auto_id = '".$row_dash['cdas_component_id']."' AND comp_isactive = '1'";
										$result_components = mysql_query($query_components) or die(mysql_error());
										$row_components = mysql_fetch_array($result_components);
									?>
									<tr>
										<td><p class="Contact"><?=$row_components['comp_name']?></p></td>
										<td>
											<select name="components[]" class="Text" size="1">      
												<option value="1" <? if ($row_dash['cdas_user_authorized'] == "1") {echo "selected";} ?> >&nbsp;Yes&nbsp;</option>
												<option value="0" <? if ($row_dash['cdas_user_authorized'] == "0") {echo "selected";} ?> >&nbsp;No&nbsp;</option>
											</select>
											<input type="hidden" name="comp_id[]" value="<?=$row_dash['cdas_component_id']?>">
										</td>
									</tr>
									<?
									}
								}
								?>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<? table_end(); ?>
		</td>
		</table>
		</td>
	</tr>

	<tr valign="top">
	   <td valign="top"  colspan="3">
		 <br>
		 <table cellpadding="2"><tr><td>
			<? table_start(940, "Reporting Preferences"); ?>
			<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
				<tr>
					<td>
						<table id="accounts_table" class="sortablelarge" width="100%" border="0" cellspacing="1" cellpadding="0">			
							<tr valign="top"  bgcolor="#CCCCCC">
								<td>&nbsp;&nbsp;Report Name</td>
								<td>&nbsp;&nbsp;Get Report</td>
								<td>&nbsp;&nbsp;Frequency</td>
								<td>&nbsp;&nbsp;Time</td>
								<td>&nbsp;&nbsp;Mode</td>
							</tr>
							
							<?
							while($row_report_data = mysql_fetch_array($result_report_data))
							{								
							?>
							<tr valign="top" class="tablerowlarge">
								<td valign="top">&nbsp;
								<?
									if($row_report_data["rdat_report_type"] == '1')
									{
										$query_name = "SELECT repo_auto_id, repo_name FROM repo_reports WHERE repo_auto_id = '".$row_report_data["rdat_repo_id"]."'";
										$result_name = mysql_query($query_name) or die(mysql_error());
										$row_name = mysql_fetch_array($result_name);
										echo $row_name["repo_name"];
									}
									else
									if($row_report_data["rdat_report_type"] == '2')
									{
										$query_name = "SELECT arep_auto_id, arep_name FROM arep_adhoc_reports WHERE arep_auto_id = '".$row_report_data["rdat_repo_id"]."' AND arep_isactive = '1'";
										$result_name = mysql_query($query_name) or die(mysql_error());
										$row_name = mysql_fetch_array($result_name);
										echo $row_name["arep_name"];
										
										?>
										<a href="<?=$PHP_SELF?>?action=remove&id=<?=$row_name['arep_auto_id']?>&type=2" onclick="javascript:return confirm('Are you sure you want to remove \n\n<?=$row_name['arep_name']?>\n\nfrom the list?')"><img src="images/delete.gif" alt="Delete" align="right"></a>
										
									<?
									}
									?>
									<input type="hidden" name="reportid[]" value="<?=$row_report_data['rdat_auto_id']?>">
									
								</td>
								<td height="30" >&nbsp;&nbsp;
								<select name="rep_select[]" class="Text" size="1">
								<option value="1" <? if($row_report_data["rdat_get_report"] == '1') {echo "Selected";}?>>Yes</option>
								<option value="0" <? if($row_report_data["rdat_get_report"] == '0') {echo "Selected";}?>>No</option>
								</select>
								
								<td>&nbsp;&nbsp;
								<?
									$query_frequency = "SELECT rfre_frequency, rfre_id  FROM rfre_report_frequency WHERE  rfre_id = '".$row_report_data["rdat_rfre_id"]."'";
									$result_frequency = mysql_query($query_frequency) or die(mysql_error());
									$row_frequency = mysql_fetch_array($result_frequency);
								?>
									<select class="Text" name="frequency[]" size="1">
										<option value="1" <? if($row_frequency["rfre_id"] == '1') echo 'selected'; ?>>Daily</option>
									 	<option value="7" <? if($row_frequency["rfre_id"] == '7') echo 'selected'; ?>>Weekly</option>
									</select>
								</td>
								<td>&nbsp;&nbsp;
								<?
									$rep_time = "rep_time".rand(0,445454554);
									$time_string = military_to_ampm(substr(trim($row_report_data['rdat_time']),11,8));
								?>                                                                                                                      
								<input class="Text" type="text" id="<?=$rep_time?>" name="time[]" size="40" maxlength="20" value="<?=$time_string?>"><input type="reset" class="submit" value=" ... " onclick="return showCalendar('<?=$rep_time?>', '%H:%M %p', '12');"> <!-- showCalendar('<?=$rep_time?>', '%Y-%m-%d %H:%M:00', '24') -->
								</td>
								<td>&nbsp;&nbsp;
								<?
									$query_mode = "SELECT rmod_mode, rmod_auto_id FROM rmod_report_mode WHERE  rmod_auto_id = '".$row_report_data["rdat_rmod_id"]."'";
									$result_mode = mysql_query($query_mode) or die(mysql_error());
									$row_mode = mysql_fetch_array($result_mode);
								?>
	
									<select class="Text" name="mode[]" size="1">
										<option value="1" <? if($row_mode["rmod_auto_id"] == '1') echo 'selected'; ?>>Link</option>
									 	<option value="2" <? if($row_mode["rmod_auto_id"] == '2') echo 'selected'; ?>>PDF</option>
										<option value="3" <? if($row_mode["rmod_auto_id"] == '3') echo 'selected'; ?>>HTML</option>
									</select>
								</td>
							</tr>
							<?
							}
							?>
						</table>
					</td>
				</tr>
			</table>
			<? table_end(); ?>
			</td></tr></table>
		</td>
	</tr>
	<!-- <br><BR> -->
	<tr>
		<td colspan="3">
			<table cellpadding="2" cellspacing="0" border="0">
				<input type="hidden" name="ID" value="<?=$ID?>">
				<tr valign="top"><td colspan="3" align="center"><p><input name="submit" class="Submit" type="submit" value="Update Profile"></p></td></tr> 
			</table>
			<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>
		</td>
	</tr>
	
	
	<script language="JavaScript">
	<!--
		//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
		tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
	// -->
	</script>
</form>
<?php
	include('bottom.php');
?>


