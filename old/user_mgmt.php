<?php
include('top.php');
include('includes/functions.php'); 
	
function datetime_convert($inputval) {



}

///////////////////////////////////////////START OF CREATE SECTION//////////////////////////////////
 if($type == "create")
    {
		echo "<center>";
		echo $table_start;
		echo "<br>";
			
		//START OF IF 1
		if($createUser)
		{
				//FIRST NAME, LAST NAME AND EMAIL ERROR CHECKING
			$atSign   = strstr($email, "@"); 
			$fullStop = strstr($email, ".");
			$array    = array();
			$test_name = array();
			$test_name[1] = "Blank Firstname";
			$test_name[2] = "Invalid Firstname";
			$test_name[3] = "Blank Lastname";
			$test_name[4] = "Invalid Lastname";
			$test_name[5] = "Invalid Email Address";
			$test_name[6] = "Unique Email Address";
			if($fname == "") 
			{
				$array[1] = "0";
				$Firstname_blank = "0";
			}  
			else 
			{
				$array[1] = "1";
				$Firstname_blank = "1";
			}
			if((ord($fname) > 64 AND ord($fname) < 91) OR (ord($fname) > 96 AND ord($fname) < 123)) 
			{
				$array[2] = "1";
				$Firstname_first = "1";
			} 
			else  
			{
				$array[2] = "0";
				$Firstname_first = "0";
			}
			if($lname == "") 
			{
				$array[3] = "0";
				$Lastname_blank = "0";
			} 
			else 
			{
				$array[3] = "1";
				$Lastname_blank = "1";
			}
			if((ord($lname) > 64 AND ord($lname) < 91) OR (ord($lname) > 96 AND ord($lname) < 123)) 
			{
				$array[4] = "1";
				$Lastname_first = "1";
			} 
			else 
			{
				$array[4] = "0";
				$Lastname_first = "0";
			}
			if($email != "" AND (!$atSign OR !$fullStop)) 
			{
				$array[5] = "0";
				$email_check = "0";
			} 
			else 
			{
				$array[5] = "1";
				$email_check = "1";
			}
		
			$uniqueEmail = mysql_query("SELECT Email FROM Users WHERE Email <> '' AND Email = '$email' AND user_isactive = '1'") or die(mysql_error());
			if(mysql_num_rows($uniqueEmail) > 0) 
			{
				$array[6] = "0";
				$email_unique = "0";
			} 
			else 
			{
				$array[6] = "1";
				$email_unique = "1";
			}
?> 
			<center>
				<!-- ERROR DISPLAY TABLE -->
				<table border="1" bordercolor="#ffffff" cellspacing="0" cellpadding="5" bgcolor="#ffffff">
					<tr> 
						<td> 
							<p class="LocOps"> 
								<?php
									for($x = 1; $x < 7; $x++)
									{
										if($array[$x] == "0") 
										{
											echo("<font color=\"#FF0000\">$test_name[$x] !</font><br>");
										} 
									}
								?>
							</p>
						</td>
					</tr>
				</table>
			</center>
			<br/> 
<?
            // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0" OR $array[5] == "0" OR $array[6] == "0") 
			{
			?>
				<center>
					<p class="red">There are one or more invalid or incomplete fields. <br> Please resolve this problem and re-submit the data.</p>
				</center>
			<?   
			}
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
				$fullname = $fname . " " . $lname;
				$pass = password_generator();
				$uname = strtolower(substr($fname, 0, 1)) . strtolower($lname);
				
				//INSERT USER DATA
				$query_create = "INSERT INTO Users(Username, Password, Fullname , Email, Workphone , Mobilephone , Report_via_email , Lastlogin , user_isactive, login_expiry, is_administrator ) VALUES('".$uname."','".md5($pass)."', '".$fullname."','".$email."','".$phone1."','".$phone2."','".$Getmail."','".date('Y-m-d G:i:s')."','1','".$loginexpiry."', '".$is_admin."')";
				$result_create = mysql_query($query_create) or die (mysql_error());
				
				$query_id = "SELECT max(ID) as ID FROM Users";
				$result_id = mysql_query($query_id) or die(mysql_error());
				$row_id = mysql_fetch_array($result_id);
				
				//INSERT REPORT DATA
				$query_reports = "SELECT repo_auto_id, repo_type FROM repo_reports WHERE repo_isactive = '1'";
				$result_reports = mysql_query($query_reports) or die(mysql_error());
				
				while($row_reports = mysql_fetch_array($result_reports))
				{
					$query_insert_reports = "INSERT INTO rdat_report_data(rdat_user_id, rdat_repo_id, rdat_report_type, rdat_rfre_id, rdat_rmod_id, rdat_time, rdat_get_report, report_sent, rdat_isactive) 
											 VALUES('".$row_id["ID"]."', '".$row_reports["repo_auto_id"]."', '".$row_reports["repo_type"]."', '1', '1', '0000-00-00 07:30:00', '0', '0','1')";
					$result_insert_reports = mysql_query($query_insert_reports) or die(mysql_error());
				}
				
				//INSERT DASHBOARD CUSTOMIZATION DATA
				$query_components = "SELECT comp_auto_id FROM comp_components WHERE comp_isactive = '1'";
				$result_components = mysql_query($query_components) or die(mysql_error());
				 
				$i = 0;
				while($row_components = mysql_fetch_array($result_components))
				{
					$query_dash = "INSERT INTO cdas_customize_dashboard(cdas_user_id, cdas_component_id, cdas_admin_authorized) 
								   VALUES('".$row_id["ID"]."', '".$row_components['comp_auto_id']."', '".$components[$i]."')";
								   
					$result_dash = mysql_query($query_dash) or die(mysql_error());
					$i++;
				}
				
				
				$fileattach   = "";
				$mailsubject  = "Welcome to CompSys 2.0!";
				$emailheading = "CompSys Password";
				$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$fullname.': <br><br><br>You have been added to the <b>'.$_app_name.'</b>!<br><br>Your Password: <b>'.$pass.'</b><br><br>';
				$mailbody    .= 'Click on the link to launch <a href="'.$_site_url.'">'.$_app_name.'</a> <Br><br><br>From: </font>';
				$from = "CompSys 2.0 <compliance_admin@donotreply.com>";
				html_emails_dynamic($email, $from, $mailsubject, $mailbody, $emailheading, $fileattach, gen_control_number());
	?>
				<!-- CREATE ACCOMPLISHED TABLE -->
				<table width="400" cellpadding="2" cellspacing="0" border="0">
					<tr>
						<?
						if($createUser)
						{
						?>
						<td colspan="3"><p class="Contact">You added the following:</p></td>
						<?
						}
						else if($editUser)
						{
						?>
						<td colspan="3"><p class="Contact">You updated the following:</p></td>
						<?
						}
						?>
					</tr>
					<tr valign="top">
						<td colspan="3"><b><p class="Contact"><?=$fname?> <?=$lname?></p></b></td>
					</tr>
				</table>
				
	<?		
				echo "<Br><br>";			
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1
?>

		<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
		<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
		<!-- helper script that uses the calendar -->
		<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
		<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
		<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>

<?

$query_components = "SELECT comp_name FROM comp_components WHERE comp_isactive = '1'";
$result_components = mysql_query($query_components) or die(mysql_error());
?>

		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
						<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">First Name:</p></td><td><p><input class="Text" name="fname" type="text" value="" size="25" maxlength="40"></p></td></tr>
						<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">Last Name:</p></td><td><p><input class="Text" name="lname" type="text" value="" size="25" maxlength="40"></p></td></tr>				
						<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">Primary Email :</p></td><td><p><input class="Text" type="text" name="email" size="25" maxlength="40" value=""></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 1 :</p></td><td><p><input class="Text" type="text" name="phone1" size="25" maxlength="40" value=""></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 2 :</p></td><td><p><input class="Text" type="text" name="phone2" size="25" maxlength="40" value=""></p></td></tr>
						
						<?
							$today = business_day_forward(strtotime("now"), 1) . " 17:00:00";
						?>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Login Expires :</p></td><td><p><input type="text" id="logexp" class="Text" name="loginexpiry" readonly size="25" maxlength="40" value="<?=$today?>"><input type="reset" class="submit" value=" ... " onclick="return showCalendar('logexp', '%Y-%m-%d %H:%M:00', '24');"></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact"></p></td><td><p  class="Contact">&nbsp;</p></td></tr>
						
						<tr valign="top">
							<td>&nbsp;</td>
							<td><p class="Contact">Admin Priviliges:</p></td>
							<td> 
								<p>
									<select class="Text" name="is_admin" size='1'>
										<option value="1">&nbsp;Yes&nbsp;</option>
										<option value="0" selected>&nbsp;No&nbsp;</option>
									</select> 
								</p>
							</td>
						</tr>
		
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact"></p></td><td><p  class="Contact">&nbsp;</p></td></tr>
		
						<tr valign="top">
							<td>&nbsp;</td>
							<td><p class="Contact">Receive Mail:</p></td>
							<td> 
								<p>
									<select class="Text" name="Getmail" size='1'>
										<option value="1" selected>&nbsp;Yes&nbsp;</option>
										<option value="0">&nbsp;No&nbsp;</option>
									</select> 
								</p>
							</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr valign="top"><td colspan="3" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="3" align="center"><p>
								<!-- <input type="hidden" name="type" value="create"> -->
								<input class="Submit" type="submit" name="createUser" value="Create User">
								</p>
							</td>
						</tr>
					</table>
				</td>
				
				<td valign="top" align="left">
					<fieldset style="BORDER-RIGHT: #9999ff 1px solid; 
						BORDER-TOP: #9999ff 1px solid; 
						BORDER-LEFT: #9999ff 1px solid;
						PADDING: 2px;
						WIDTH: 270px; 
						HEIGHT: 250px;
						
						BORDER-BOTTOM: #9999ff 1px solid"> 
				
						<table width="100%" height="100%" class="links12">
							<tr>
								<td align="center" colspan="2">Dashboard Customization</td>
							</tr>

							<tr>
								<td colspan="2" nowrap><hr size="1" color="#999999"></td>
							</tr>


							<?
							while($row_components = mysql_fetch_array($result_components))
							{
							?>
							<tr>
								<td><b><?=$row_components['comp_name']?></b></td>
								<td>
									<select name="components[]" class="Text" size="1">
										<option value="1">&nbsp;Yes&nbsp;</option>
										<option value="0" selected>&nbsp;No&nbsp;</option>
									</select>
								</td>
							</tr>
							<?
							} 
							?>
			

						</table>
					</fieldset>
				</td>
			</tr> 
			</form>
		</table>
<?
		echo $table_end;
		echo "</center>";
	} 
///////////////////////////////////////////////  END OF CREATE SECTION  //////////////////////////////////////////////////////////////

///////////////////////////////////////////////  START OF DELETE SECTION  ////////////////////////////////////////////////////////////
	if($type == "manage")
    {
		echo "<center>";
		echo $table_start;
		
		if($action == "remove")
		{
			$query_delete = "UPDATE Users SET user_isactive = '0' WHERE ID = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
			
			$query_delete_reports = "UPDATE rdat_report_data SET rdat_isactive = '0' WHERE rdat_user_id = '$ID'";
			$result_delete_reports = mysql_query($query_delete_reports) or die(mysql_error());
		}
		
		
		
?>
		
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<table class="sortable"  id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						    <td></td>
							<td></td>
							<td>Name</td>
							<td>Primary Email</td>
							<td>Primary Phone</td>
							<td>Secondary Phone</td>
						</tr>
						<?
						$query_users = "SELECT ID, Fullname, Email, Workphone, Mobilephone FROM Users WHERE user_isactive = '1'";
						//echo $query_trades;
						$result = mysql_query($query_users) or die(mysql_error());
						while ( $row = mysql_fetch_array($result) ) 
						{
						?>
						<tr class="tablerow"> 
 							<td><a href="<?=$PHP_SELF?>?type=<?=$type?>&action=remove&ID=<?=$row["ID"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n\n<?=$row["Fullname"]?>\n\nfrom the list?')"><img src="images/delete.gif" alt="Delete"></a></td>
							<td><a href="<?=$PHP_SELF?>?type=edit&ID=<?=$row["ID"]?>"><img src="images/edit.gif" alt="Edit"></a></td>
							<td><?=$row["Fullname"]?></td>
							<td><?=$row["Email"]?></td>
							<td><?=$row["Workphone"]?></td>
							<td><?=$row["Mobilephone"]?></td>
						</tr>
						<?php
						}
						?>
					</table>
				<!-- TABLE 2 END -->
				<script language="JavaScript">
				<!--
					///////////////////////tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
					tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
				// -->
				</script>
				</td>
			</tr>
		</table>
<?	
		echo $table_end;
		echo "</center>";
	}  
/////////////////////////////////////////////////END OF DELETE SECTION/////////////////////////////////////////////////

/////////////////////////////////////////////////START OF EDIT SECTION////////////////////////////////////////////////
 if($type == "edit")
  {
			echo "<center>";
			echo $table_start;
			echo "<br>";

			//START OF IF 1
			if($editUser)
			{
					//FIRST NAME, LAST NAME AND EMAIL ERROR CHECKING
				$atSign   = strstr($email, "@"); 
				$fullStop = strstr($email, ".");
				$array    = array();
				$test_name = array();
				$test_name[1] = "Blank Firstname";
				$test_name[2] = "Invalid Firstname";
				$test_name[3] = "Blank Lastname";
				$test_name[4] = "Invalid Lastname";
				$test_name[5] = "Invalid Email Address";
				$test_name[6] = "Unique Email Address";
				if($fname == "") 
				{
					$array[1] = "0";
					$Firstname_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$Firstname_blank = "1";
				}
				if((ord($fname) > 64 AND ord($fname) < 91) OR (ord($fname) > 96 AND ord($fname) < 123)) 
				{
					$array[2] = "1";
					$Firstname_first = "1";
				} 
				else  
				{
					$array[2] = "0";
					$Firstname_first = "0";
				}
				if($lname == "") 
				{
					$array[3] = "0";
					$Lastname_blank = "0";
				} 
				else 
				{
					$array[3] = "1";
					$Lastname_blank = "1";
				}
				if((ord($lname) > 64 AND ord($lname) < 91) OR (ord($lname) > 96 AND ord($lname) < 123)) 
				{
					$array[4] = "1";
					$Lastname_first = "1";
				} 
				else 
				{
					$array[4] = "0";
					$Lastname_first = "0";
				}
				if($email != "" AND (!$atSign OR !$fullStop)) 
				{
					$array[5] = "0";
					$email_check = "0";
				} 
				else 
				{
					$array[5] = "1";
					$email_check = "1";
				}
			
				$uniqueEmail = mysql_query("SELECT Email FROM Users WHERE Email <> '' AND Email = '$email' AND ID != '$ID'") or die(mysql_error());
				if(mysql_num_rows($uniqueEmail) > 0) 
				{
					$array[6] = "0";
					$email_unique = "0";
				} 
				else 
				{
					$array[6] = "1";
					$email_unique = "1";
				}
?> 
			<center>
				<!-- ERROR DISPLAY TABLE -->
				<table border="1" bordercolor="#ffffff" cellspacing="0" cellpadding="5" bgcolor="#ffffff">
					<tr> 
						<td> 
							<p class="LocOps"> 
								<?php
									for($x = 1; $x < 7; $x++)
									{
										if($array[$x] == "0") 
										{
											echo("<font color=\"#FF0000\">$test_name[$x] !</font><br>");
										} 
									}
								?>
							</p>
						</td>
					</tr>
				</table>
			</center>
			<br/> 
<?
            // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0" OR $array[5] == "0" OR $array[6] == "0") 
			{
			?>
				<center>
					<p class="red">There are one or more invalid or incomplete fields. <br> Please resolve this problem and re-submit the data.</p>
				</center>
			<?   
			}
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
				$fullname = $fname . " " . $lname;
				$query_edit = "UPDATE Users SET Fullname='".$fullname."', Email='".$email."', Workphone='".$phone1."' , Mobilephone='".$phone2."', Report_via_email='".$Getmail."', login_expiry = '".$loginexpiry."', is_administrator = '".$is_admin."' WHERE ID='$ID'";
				//echo $query_edit;
				$result_edit = mysql_query($query_edit) or die (mysql_error());
				
				for($i = 0; $i < count($components); $i++)
				{
					$query_update = "UPDATE cdas_customize_dashboard SET cdas_admin_authorized = '".$components[$i]."' WHERE cdas_user_id = '$ID' AND cdas_component_id = '".$comp_id[$i]."'";
					$result_update = mysql_query($query_update) or die(mysql_error());
				}
				
	?>
				<!-- CREATE ACCOMPLISHED TABLE -->
				<table width="400" cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td colspan="3"><p class="Contact">You updated the following:</p></td>
					</tr>
					<tr valign="top">
						<td colspan="3"><b><p class="Contact"><?=$fname?> <?=$lname?></p></b></td>
					</tr>
				</table>
				
	<?		
				echo "<Br><br>";			
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1
		
		$result_user = mysql_query("SELECT Fullname,  Email, Workphone, Mobilephone, Report_via_email, login_expiry, is_administrator FROM Users WHERE ID = '$ID'") or die (mysql_error());
		while ( $row_user = mysql_fetch_array($result_user) ) 
		{
			$Fullname = $row_user["Fullname"];
			list($first, $last) = explode(" ", $Fullname);
			$Email = $row_user["Email"];
			$Workphone = $row_user["Workphone"];
			$Mobilephone = $row_user["Mobilephone"];
			$Report_via_email = $row_user["Report_via_email"];
			$login_expiry = $row_user["login_expiry"];
			$isadmin = $row_user["is_administrator"];
		}
		
		//DASHBOARD SECTION
		$query_components = "SELECT comp_name FROM comp_components WHERE comp_isactive = '1' ORDER BY comp_auto_id";
		$result_components = mysql_query($query_components) or die(mysql_error());

		
		$arr_components = array();
		$query_dash = "SELECT cdas_component_id, cdas_admin_authorized FROM cdas_customize_dashboard WHERE cdas_user_id = '".$ID."' AND cdas_isactive = '1'";
		$result_dash = mysql_query($query_dash) or die(mysql_error());
		while($row_dash = mysql_fetch_array($result_dash) )
		{
			$arr_components[$row_dash['cdas_component_id']] = $row_dash['cdas_admin_authorized'];
		}
		ksort ($arr_components);
?>


		<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
		<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
		<!-- helper script that uses the calendar -->
		<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
		<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
		<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
			
						<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">First Name:</p></td><td><p><input class="Text" name="fname" type="text" value="<?=$first?>" size="25" maxlength="40"></p></td></tr>
						<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">Last Name:</p></td><td><p><input class="Text" name="lname" type="text" value="<?=$last?>" size="25" maxlength="40"></p></td></tr>				
						<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">Primary Email :</p></td><td><p><input class="Text" type="text" name="email" size="25" maxlength="40" value="<?=$Email?>"></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 1 :</p></td><td><p><input class="Text" type="text" name="phone1" size="25" maxlength="40" value="<?=$Workphone?>"></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 2 :</p></td><td><p><input class="Text" type="text" name="phone2" size="25" maxlength="40" value="<?=$Mobilephone?>"></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Login Expires :</p></td><td><p><input type="text" id="logexp" class="Text" name="loginexpiry" readonly size="25" maxlength="40" value="<?=$login_expiry?>"><input type="reset" class="submit" value=" ... " onclick="return showCalendar('logexp', '%Y-%m-%d %H:%M:00', '24');"></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact"></p></td><td><p  class="Contact">&nbsp;</p></td></tr>
		
						<tr valign="top">
							<td>&nbsp;</td>
							<td><p class="Contact">Admin Priviliges:</p></td>
							<td> 
								<p>
									<select class="Text" name="is_admin" size='1'>
										<option value="1" <? if ($isadmin == "1") {echo "selected";} ?>>&nbsp;Yes&nbsp;</option>
										<option value="0" <? if ($isadmin == "0") {echo "selected";} ?>>&nbsp;No&nbsp;</option>
									</select> 
								</p>
							</td>
						</tr>
				
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact"></p></td><td><p  class="Contact">&nbsp;</p></td></tr>
			
						<tr valign="top">
							<td>&nbsp;</td>
							<td><p class="Contact">Receive Mail:</p></td>
							<td> 
								<p>
									<select class="Text" name="Getmail" size='1'>
										<option value="1" <? if ($Report_via_email == "1") {echo "selected";} ?>>&nbsp;Yes&nbsp;</option>
										<option value="0" <? if ($Report_via_email == "0") {echo "selected";} ?>>&nbsp;No&nbsp;</option>
									</select> 
								</p>
							</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr valign="top"><td colspan="3" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="3" align="center"><p>
								<input class="Submit" type="submit" name="editUser" value="Update"></p>
							</td>
						</tr>  
						
					</table>
				</td>
				
				<td valign="top" align="left">
					<fieldset style="BORDER-RIGHT: #9999ff 1px solid; 
						BORDER-TOP: #9999ff 1px solid; 
						BORDER-LEFT: #9999ff 1px solid;
						PADDING: 2px;
						WIDTH: 270px; 
						HEIGHT: 250px;
						
						BORDER-BOTTOM: #9999ff 1px solid"> 
				
						<table width="100%" height="100%" class="links12">
							<tr>
								<td align="center" colspan="2">Dashboard Customization</td>
							</tr>

							<tr>
								<td colspan="2" nowrap><hr size="1" color="#999999"></td>
							</tr>


							<?
							$i = 1;
							while($row_components = mysql_fetch_array($result_components))
							{
							?>
							<tr>
								<td><b><?=$row_components['comp_name']?></b></td>
								<td>
									<select name="components[]" class="Text" size="1">
										<option value="1" <? if ($arr_components[$i] == "1") {echo "selected";} ?>>&nbsp;Yes&nbsp;</option>
										<option value="0" <? if ($arr_components[$i] == "0") {echo "selected";} ?>>&nbsp;No&nbsp;</option>
									</select>
									<input type="hidden" name="comp_id[]" value="<?=$i?>">
								</td>
							</tr>
							<?
							$i++;
							}
							?>
						</table>
					</fieldset>
				</td>
			</tr> 
			</form>
		</table>
<?
		echo $table_end;
		echo "</center>";
	} 
/////////////////////////////////////////////////END OF EDIT SECTION/////////////////////////////////////////////////

include('bottom.php');	 
?>
