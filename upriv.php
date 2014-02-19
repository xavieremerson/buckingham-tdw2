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
///////////////////////////////////////////START OF CREATE SECTION//////////////////////////////////
 if($type == "create")
    {
		echo "<center>";
		echo table_start_percent(100,"Add New User");  
		echo "<br>";
			
		//START OF IF 1
		if($createUser)
		{
					//FIRST NAME, LAST NAME AND EMAIL ERROR CHECKING
				$atSign   = strstr($email, "@"); 
				$fullStop = strstr($email, ".");
				$array    = array();
				$test_name = array();
				$test_name[1] = "First Name cannot be blank.";
				$test_name[2] = "The First Name entered is invalid.";
				$test_name[3] = "Last Name cannot be blank";
				$test_name[4] = "The Last Name entered is invalid.";
				$test_name[5] = "The Email Address entered is invalid.";
				$test_name[6] = "The Email Address already exists in the system and may not be reused.";
				$test_name[7] = "Role is required. Please enter and re-submit.";
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
				if($sel_role == "") 
				{
					$array[7] = "0";
					$sel_role_blank = "0";
				}  
				else 
				{
					$array[7] = "1";
					$sel_role_blank = "1";
				}

?> 


				<!-- ERROR DISPLAY TABLE -->
				<table cellspacing="0" cellpadding="5">
					<tr> 
						<td class="errnote"><?php
									for($x = 1; $x < 8; $x++)
									{
										if($array[$x] == "0") 
										{
											echo "&nbsp;&nbsp;".$test_name[$x]."<br>";
										} 
									}
								?></td>
					</tr>
				</table>
<?
            // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0" OR $array[5] == "0" OR $array[6] == "0" OR $array[7] == "0") 
			{
			?>
				<a class="red9">&nbsp;&nbsp;There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.</a>
			<?   
			}
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
				$fullname = $fname . " " . $lname;
				$pass = password_generator();
				$uname = rand(100,999)."_".strtolower(substr($fname, 0, 1)) . strtolower($lname);
				
				//INSERT USER DATA
				$query_create = "INSERT INTO
												 Users(
													 Username, 
													 Password,
													 Firstname,
													 Lastname,
													 Fullname, 
													 Email, 
													 Workphone, 
													 Mobilephone, 
													 Lastlogin, 
													 user_isactive, 
													 login_expiry, 
													 Role) 
												 VALUES('"
												 .$uname."','"
												 .md5($pass)."', '"
												 .$fname."','"
												 .$lname."','"
												 .$fullname."','"
												 .$email."','"
												 .$phone1."','"
												 .$phone2."','"
												 .date('Y-m-d G:i:s')."','1','"												 
												 .$loginexpiry."','"
												 .$sel_role."')";
				//xdebug("query_create",$query_create);
				$result_create = mysql_query($query_create) or die (mysql_error());
				
				$query_id = "SELECT max(ID) as ID FROM Users";
				$result_id = mysql_query($query_id) or die(mysql_error());
				$row_id = mysql_fetch_array($result_id);
				
				$mailsubject  = "User Account (".$fullname.") created in Buckingham TDW";
				$emailheading = "TDW User/Password";
				$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$fullname.': <br><br><br>';
				$mailbody    .= 'You user account has been created in <b>'.$_app_name.'</b>.<br><br>Your Password: <b>'.$pass.'</b><br><br>';
				$mailbody    .= 'Click on the link to launch <a href="'.$_site_url.'">'.$_app_name.'</a></font>';
				
				$html_body .= zSysMailHeader("");
				$html_body .= $mailbody;
				$html_body .= zSysMailFooter ();
				$subject = $mailsubject;
				$text_body = $subject;
				zSysMailer($email, $fullname, $subject, $html_body, $text_body, "") ;
				
				//html_emails_dynamic($email, $from, $mailsubject, $mailbody, $emailheading, $fileattach, gen_control_number());
	?>
				<!-- CREATE ACCOMPLISHED TABLE -->
				<table width="400" cellpadding="2" cellspacing="0" border="0">
					<tr valign="top">
						<td class="green11">&nbsp; User: <?=$fname?> <?=$lname?> added successfully.</td>
					</tr>
				</table>
				
	<?		
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1

?>

		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
					
											<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">First Name :</td>
							<td><input class="Text" name="fname" type="text" value="<?=$fname?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Last Name :</td>
							<td><input class="Text" name="lname" type="text" value="<?=$lname?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>				
						<tr valign="top">
							<td class="ilt">Primary Email :</td>
							<td><input class="Text" type="text" name="email" size="30" maxlength="40" value="<?=$email?>"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Work Phone :</td>
							<td><p><input class="Text" type="text" name="phone1" size="30" maxlength="40" value="<?=$phone1?>"></p></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Mobile Phone :</td>
							<td><input class="Text" type="text" name="phone2" size="30" maxlength="40" value="<?=$phone2?>"></td>
						</tr>
						<?
							$today = business_day_forward(strtotime("now"), 1) . " 17:00:00";
						?>
						<tr valign="top">
							<td class="ilt">Login Expires :</td>
							<td>
							<input type="text" id="logexp" class="Text" name="loginexpiry" readonly size="25" maxlength="40" value="<?=$today?>"><input type="reset" class="submit" value=" ... " onclick="return showCalendar('logexp', '%Y-%m-%d %H:%M:00', '24');">
							</td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">ROLE :</td>
							<td>
							<select name="sel_role">
							<option value="">Select Role</option>
							<?
							$query_role = "SELECT role_auto_id, role_name from user_roles";
							$result_role = mysql_query($query_role) or die(mysql_error());
							
							while($row_role = mysql_fetch_array($result_role))
							{
								if ($row_role["role_auto_id"] == $sel_role) {
									$selstr = "selected";
								} else {
									$selstr = "";
								}
							echo '<option value="'.$row_role["role_auto_id"].'" '.$selstr.'>'.$row_role["role_name"].'</option>';
							}

							?>
							</select><font color="#FF0000">*</font>
							</td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Primary RR# :</td>
							<td><input class="Text" type="text" name="enter_rr_num" size="30" maxlength="40" value="<?=$enter_rr_num?>"></td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center"><input class="Submit" type="submit" name="createUser" value="Create User">
							</td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
<?
		echo $table_end;
		echo "</center>";
	} 
///////////////////////////////////////////////  END OF CREATE SECTION  //////////////////////////////////////////////////////////////

///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////
	if($type == "manage")
    {
		echo "<center>";
	?>
	<? table_start_percent(100, "User Management"); ?>
	<?	
		if($action == "remove")
		{
			$query_delete = "UPDATE Users SET user_isactive = '0' WHERE ID = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}
?>
				<style type="text/css">
					<!--
					.verticaltext {
					writing-mode: tb-rl;
					filter: flipv fliph;
					}
					-->
        </style>
		
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td valign="bottom" width="180">Name</td>
							<td valign="bottom" width="200">Role</td>
							<td class="verticaltext" width="10">Compliance Report v1</td>
							<td class="verticaltext" width="10">Compliance Report v2</td>
							<td class="verticaltext" width="10">Edit User</td>
							<td class="verticaltext" width="10">x</td>
							<td class="verticaltext" width="10">x</td>
							<td class="verticaltext" width="10">x</td>
							<td class="verticaltext" width="10">x</td>
							<td class="verticaltext" width="10">x</td>
							<td class="verticaltext" width="10">x</td>
							<td class="verticaltext" width="10">x</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$query_users = "SELECT a.ID, a.Fullname, a.privileges, b.role_name 
														FROM Users a, user_roles b
														WHERE a.Role = b.role_auto_id  
														AND a.user_isactive = '1' order by a.Fullname";
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
							<td><?=$row["Fullname"]?></td>
							<td><?=$row["role_name"]?></td>
							<td width="30" align="center">
							<input name="0^<?=substr($row["privileges"],0,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],0,1)?>" <? if (substr($row["privileges"],0,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="1^<?=substr($row["privileges"],1,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],1,1)?>" <? if (substr($row["privileges"],1,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="2^<?=substr($row["privileges"],2,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],2,1)?>" <? if (substr($row["privileges"],2,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="3^<?=substr($row["privileges"],3,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],3,1)?>" <? if (substr($row["privileges"],3,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="4^<?=substr($row["privileges"],4,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],4,1)?>" <? if (substr($row["privileges"],4,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="5^<?=substr($row["privileges"],5,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],5,1)?>" <? if (substr($row["privileges"],5,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="6^<?=substr($row["privileges"],6,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],6,1)?>" <? if (substr($row["privileges"],6,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="7^<?=substr($row["privileges"],7,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],7,1)?>" <? if (substr($row["privileges"],7,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="8^<?=substr($row["privileges"],8,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],8,1)?>" <? if (substr($row["privileges"],8,1)==1) { echo "checked"; }?> />
							</td>
							<td width="30" align="center">
							<input name="9^<?=substr($row["privileges"],9,1)?>^<?=$row["ID"]?>" type="checkbox" value="<?=substr($row["privileges"],9,1)?>" <? if (substr($row["privileges"],9,1)==1) { echo "checked"; }?> />
							</td>
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
	
		<? table_end_percent(); ?>
		<?
		echo "</center>";
	}  
/////////////////////////////////////////////////END OF DELETE SECTION/////////////////////////////////////////////////

/////////////////////////////////////////////////START OF EDIT SECTION////////////////////////////////////////////////
 if($type == "edit")
  {

			echo "<center>";
			table_start_percent(100, "Edit User");
			echo "<br>";
			
			//START OF IF 1
			if($editUser)
			{
					//FIRST NAME, LAST NAME AND EMAIL ERROR CHECKING
				$atSign   = strstr($email, "@"); 
				$fullStop = strstr($email, ".");
				$array    = array();
				$test_name = array();
				$test_name[1] = "First Name cannot be blank.";
				$test_name[2] = "The First Name entered is invalid.";
				$test_name[3] = "Last Name cannot be blank";
				$test_name[4] = "The Last Name entered is invalid.";
				$test_name[5] = "The Email Address entered is invalid.";
				$test_name[6] = "The Email Address already exists in the system and may not be reused.";
				$test_name[7] = "Role is required. Please enter and re-submit.";
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
				if($sel_role == "") 
				{
					$array[7] = "0";
					$sel_role_blank = "0";
				}  
				else 
				{
					$array[7] = "1";
					$sel_role_blank = "1";
				}

?> 
				<!-- ERROR DISPLAY TABLE -->
				<table cellspacing="0" cellpadding="5">
					<tr> 
						<td class="errnote"><?php
									for($x = 1; $x < 8; $x++)
									{
										if($array[$x] == "0") 
										{
											echo "&nbsp;&nbsp;".$test_name[$x]."<br>";
										} 
									}
								?></td>
					</tr>
				</table>
<?
            // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0" OR $array[5] == "0" OR $array[6] == "0" OR $array[7] == "0") 
			{
			?>
				<a class="red9">&nbsp;&nbsp;There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.</a>
			<?   
			}
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
				$fullname = $fname . " " . $lname;
				
				$query_edit = "UPDATE Users 
											SET Fullname='".str_replace("'","\\'",$fullname)."',
													Role = '".$sel_role."',
													Username = '".$username."',   
													Firstname = '".str_replace("'","\\'",$fname)."',
													Lastname = '".str_replace("'","\\'",$lname)."', 
													Initials = '".$initials."', 
													Email='".$email."', 
													Workphone='".$phone1."' , 
													Mobilephone='".$phone2."', 
													rr_num = '".$enter_rr_num."',
													login_expiry = '".$loginexpiry."' 
											WHERE ID='$ID'";
				//xdebug("query_edit",$query_edit);
				$result_edit = mysql_query($query_edit) or die (tdw_mysql_error($query_edit));
	?>
				<!-- CREATE ACCOMPLISHED TABLE -->
				<table width="400" cellpadding="2" cellspacing="0" border="0">
					<tr valign="top">
						<td class="green11">&nbsp; User: <?=$fname?> <?=$lname?> updated successfully.</td>
					</tr>
				</table>
	<?		
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1


    //show_array($_POST);
		$result_user = mysql_query("SELECT Role, Username, Fullname,  Email, Initials, Workphone, Mobilephone, rr_num, Report_via_email, login_expiry, is_administrator FROM Users WHERE ID = '$ID'") or die (mysql_error());
		while ( $row_user = mysql_fetch_array($result_user) ) 
		{
			$Fullname = $row_user["Fullname"];  //str_replace("'","\'",$row_user["Fullname"]);
			list($first, $last) = explode(" ", $Fullname);
			$Role = $row_user["Role"];
			$Username = $row_user["Username"];
			$Initials = $row_user["Initials"];
			$Email = $row_user["Email"];
			$Workphone = $row_user["Workphone"];
			$Mobilephone = $row_user["Mobilephone"];
			$rr_num = $row_user["rr_num"];
			$Report_via_email = $row_user["Report_via_email"];
			$login_expiry = $row_user["login_expiry"];
			$isadmin = $row_user["is_administrator"];
		}
		
?>


		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">First Name :</td>
							<td><input class="Text" name="fname" type="text" value="<?=$first?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Last Name :</td>
							<td><input class="Text" name="lname" type="text" value="<?=$last?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>				
						<tr valign="top">
							<td class="ilt">Username :</td>
							<td><input class="Text" type="text" name="username" size="30" maxlength="40" value="<?=$Username?>"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Initials :</td>
							<td><input class="Text" type="text" name="initials" size="30" maxlength="40" value="<?=$Initials?>"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Primary Email :</td>
							<td><input class="Text" type="text" name="email" size="30" maxlength="40" value="<?=$Email?>"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Work Phone :</td>
							<td><p><input class="Text" type="text" name="phone1" size="30" maxlength="40" value="<?=$Workphone?>"></p></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Mobile Phone :</td>
							<td><input class="Text" type="text" name="phone2" size="30" maxlength="40" value="<?=$Mobilephone?>"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Login Expires :</td>
							<td><p>
							<input type="text" id="logexp" class="Text" name="loginexpiry" readonly size="30" maxlength="40" value="<?=$login_expiry?>"><input type="reset" class="submit" value=" ... " onclick="return showCalendar('logexp', '%Y-%m-%d %H:%M:00', '24');"></p>
							</td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">ROLE :</td>
							<td>
							<select name="sel_role">
							<option value="">Select Role</option>
							<?
							$query_role = "SELECT role_auto_id, role_name from user_roles";
							$result_role = mysql_query($query_role) or die(mysql_error());
							
							while($row_role = mysql_fetch_array($result_role))
							{
								if ($Role == $row_role["role_auto_id"]) {
									$selstr = "selected";
								} else {
									$selstr = "";
								}
							echo '<option value="'.$row_role["role_auto_id"].'" '.$selstr.'>'.$row_role["role_name"].'</option>';
							}

							?>
							</select><font color="#FF0000">*</font>
							</td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Primary RR# :</td>
							<td><input class="Text" type="text" name="enter_rr_num" size="30" maxlength="40" value="<?=$rr_num?>"></td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center"><p>
								<input class="Submit" type="submit" name="editUser" value="Update"></p>
							</td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
<?
		table_end_percent();
	} 
/////////////////////////////////////////////////END OF EDIT SECTION/////////////////////////////////////////////////

  include('inc_footer.php');
?>
