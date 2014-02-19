<title>Add TDW User</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
?>

			<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
			<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
			<!-- helper script that uses the calendar -->
			<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
			<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
			<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
			
<?
///////////////////////////////////////////START OF CREATE SECTION//////////////////////////////////
		echo "<center>";
		echo tsp(100,"Add New User");  
			
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
				$test_name[8] = "Full Name is required. Please enter and re-submit.";
				$test_name[9] = "TDW requires unique Initials and you have entered Initials which already exist. Please correct and re-submit.";
				if($Firstname == "") 
				{
					$array[1] = "0";
					$Firstname_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$Firstname_blank = "1";
				}
				if((ord($Firstname) > 64 AND ord($Firstname) < 91) OR (ord($Firstname) > 96 AND ord($Firstname) < 123)) 
				{
					$array[2] = "1";
					$Firstname_first = "1";
				} 
				else  
				{
					$array[2] = "0";
					$Firstname_first = "0";
				}
				if($Lastname == "") 
				{
					$array[3] = "0";
					$Lastname_blank = "0";
				} 
				else 
				{
					$array[3] = "1";
					$Lastname_blank = "1";
				}
				if((ord($Lastname) > 64 AND ord($Lastname) < 91) OR (ord($Lastname) > 96 AND ord($Lastname) < 123)) 
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
				if($Fullname == "") 
				{
					$array[8] = "0";
					$Fullname_blank = "0";
				}  
				else 
				{
					$array[8] = "1";
					$Fullname_blank = "1";
				}
				
				$uniqueInitials = mysql_query("SELECT Initials FROM Users WHERE Initials <> '' AND Initials = '$initials' AND ID != '$ID'") or die(mysql_error());
				if(mysql_num_rows($uniqueInitials) > 0) 
				{
					$array[9] = "0";
					$initials_unique = "0";
				} 
				else 
				{
					$array[9] = "1";
					$initials_unique = "1";
				}

       // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0" OR $array[5] == "0" OR $array[6] == "0" OR $array[7] == "0" OR $array[8] == "0" OR $array[9] == "0") 
			{
					$create_err_msg = "There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.";
					$show_err = 0;
						for($x = 1; $x <= count($array); $x++)
						{
							if($array[$x] == "0") 
							{
								$create_err_msg = $create_err_msg . "<br>" . $test_name[$x];
								$show_err = 1;
							} 
						}
					
					if ($show_err == 1) {
					showmsg(3, $create_err_msg);
					}
			}
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
				$pass = password_generator();
				$uname = strtolower(substr($Firstname, 0, 1)) . strtolower($Lastname). rand(1,9);
				
				//INSERT USER DATA
				$query_create = "INSERT INTO
												 Users(
													 Username, 
													 Password,
													 Firstname,
													 Lastname,
													 Fullname,
													 Initials, 
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
												 .$Firstname."','"
												 .$Lastname."','"
												 .$Fullname."','"
												 .$Initials."','"
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
				
				$mailsubject  = "User Account (".$Fullname.") created in Buckingham TDW";
				$emailheading = "TDW User/Password";
				$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$Fullname.': <br><br><br>';
				$mailbody    .= 'You user account has been created in <b>'.$_app_name.'</b>.<br><br>Your Password: <b>'.$pass.'</b><br><br>';
				$mailbody    .= 'Click on the link to launch <a href="'.$_site_url.'">'.$_app_name.'</a></font>';
				
				$html_body .= zSysMailHeader("");
				$html_body .= $mailbody;
				$html_body .= zSysMailFooter ();
				$subject = $mailsubject;
				$text_body = $subject;
				
				if ($welcome_email) {
					zSysMailer($email, $fullname, $subject, $html_body, $text_body, "") ;
					zSysMailer("pprasad@centersys.com", "Pravin Prasad", $subject, $html_body, $text_body, "") ;
				}
				
				//Message Success
        showmsg(1, "User: [".$Fullname."] created successfully.");

	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1

?>
		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="100%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
						<tr valign="top">
							<td class="ilt" nowrap="nowrap">Send WELCOME Email :</td>
							<td><input type="checkbox" name="welcome_email" checked="checked" /> &nbsp;&nbsp;<?=showhelp(26)?></td>
						</tr>
						<tr valign="top">
							<td class="ilt">First Name :</td>
							<td><input class="Text" name="Firstname" type="text" value="<?=$Firstname?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Last Name :</td>
							<td><input class="Text" name="Lastname" type="text" value="<?=$Lastname?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>				
						<tr valign="top">
							<td class="ilt">Full Name :</td>
							<td><input class="Text" name="Fullname" type="text" value="<?=$Fullname?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>				
						<tr valign="top">
							<td class="ilt">Initials :</td>
							<td><input class="Text" name="Initials" type="text" value="<?=$Initials?>" size="30" maxlength="40"></td>
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
							$today = business_day_forward(strtotime("now"), 200) . " 17:00:00";
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
		tep();
		echo "</center>";
?>
