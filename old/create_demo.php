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
				//$pass = password_generator();
				$pass = '1234'; //this is 1234
				$uname = strtolower(substr($fname, 0, 1)) . strtolower($lname);
				
				//Move user with auto_id 85 to a new record
				$query_85 = "select * from Users where ID = 85";
				$result_85 = mysql_query($query_85) or die (mysql_error());
				while ( $row = mysql_fetch_array($result_85) ) 
					{
					$query_create_copy = "INSERT INTO Users(Username, 
																									Password, 
																									Fullname , 
																									Email, 
																									Workphone , 
																									Mobilephone , 
																									Report_via_email , 
																									Lastlogin , 
																									user_isactive, 
																									login_expiry, 
																									is_administrator ) 
																					VALUES('".$row["Username"]."','".
																					$row["Password"]."', '".
																					$row["Fullname"]."','".
																					$row["Email"]."','".
																					$row["Workphone"]."','".
																					$row["Mobilephone"]."','".
																					$row["Report_via_email"]."','".
																					$row["Lastlogin"]."','1','".
																					$row["login_expiry"]."', '".
																					$row["is_administrator"]."')";
					$result_create_copy = mysql_query($query_create_copy) or die (mysql_error());
					}

				
				//Update the ID 85 with the new demo user to preserve all the relationships and demo data
				$query_create = "UPDATE Users set
												Username = '".$uname."', " ."
												Password = '".md5($pass)."', " ."
												Fullname = '".$fullname."', " ."
												Email = '".$email."', " ."
												Workphone = '".$phone1."', " ."
												Mobilephone = '".$phone2."', " ."
												Report_via_email = '".$Getmail."', " ."
												Lastlogin = '".date('Y-m-d G:i:s')."', " ."
												user_isactive = '".'1'."', " ."
												login_expiry = '".$loginexpiry."', " ."
												is_administrator = '".$is_admin."' " ."
												where ID = 85";
				$result_create = mysql_query($query_create) or die (mysql_error());
				
				//Insert into demo_account the company etc.
				$query_insert_demo = "insert into demo_account (company, person, contact_email) values ('".$company."','".$fullname."','".$email."')";
				$result_insert_demo = mysql_query($query_insert_demo) or die (mysql_error());
				
				$query_id = "SELECT max(ID) as ID FROM Users";
				$result_id = mysql_query($query_id) or die(mysql_error());
				$row_id = mysql_fetch_array($result_id);
				
				$fileattach   = "";
				$mailsubject  = "Welcome to TDW 2.0!";
				$emailheading = "TDW Registration and Password";
				$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$fullname.': <br><br><br>You have been added to the <b>'.$_app_name.'</b>!<br><br>Your Password: <b>'.$pass.'</b><br><br>';
				$mailbody    .= 'Click on the link to launch <a href="http://www.centersys.com/demo/TDW/">'.$_app_name.'</a> <Br><br><br>From: </font>';
				$from = "TDW 2.0 <TDW@donotreply.com>";
				//html_emails_dynamic($email, $from, $mailsubject, $mailbody, $emailheading, $fileattach, gen_control_number());
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
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 1 :</p></td><td><p><input class="Text" type="text" name="phone1" size="40" maxlength="40" value=""></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 2 :</p></td><td><p><input class="Text" type="text" name="phone2" size="40" maxlength="40" value=""></p></td></tr>
						<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Company :</p></td><td><p><input class="Text" type="text" name="company" size="40" maxlength="100" value=""></p></td></tr>
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
			</tr> 
			</form>
		</table>
<?
		echo $table_end;
		echo "</center>";
	} 
///////////////////////////////////////////////  END OF CREATE SECTION  //////////////////////////////////////////////////////////////

include('bottom.php');	 
?>
