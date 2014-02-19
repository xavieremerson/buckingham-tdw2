<?php
include('top.php');
include('includes/functions.php'); 
	
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
			
				$uniqueEmail = mysql_query("SELECT Email FROM Users WHERE Email <> '' AND Email = '$email'") or die(mysql_error());
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
				$pass = password_generator();
				$uname = strtolower(substr($fname, 0, 1)) . strtolower($lname);
				$fullname = $fname . " " . $lname;
				$query_create = "INSERT INTO Users(Username, Password, Fullname , Email, Workphone , Mobilephone , Report_via_email , Lastlogin , user_isactive, login_expiry) VALUES('".$uname."','".md5($pass)."', '".$fullname."','".$email."','".$phone1."','".$phone2."','".$Getmail."','".date('Y-m-d G:i:s')."','1','".$loginexpiry."')";
				$result_create = mysql_query($query_create) or die (mysql_error());
				
				$fileattach   = "";
				$mailsubject  = "Welcome to TDW!";
				$emailheading = "TDW Password";
				$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$fullname.': <br><br><br>You have been added to the <b>'.$_app_name.'</b>!<br><br>Your Password: <b>'.$pass.'</b><br><br>';
				$mailbody    .= 'Click on the link to launch <a href="'.$_site_url.'">'.$_app_name.'</a> <Br><br><br>From: </font>';
				$from = "TDW@donotreply.com";
				html_emails_dynamic($email, $from, $mailsubject, $mailbody, $emailheading, $fileattach, gen_control_number());

	?>
				<!-- CREATE ACCOMPLISHED TABLE -->
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td colspan="3"><p class="Contact"><b>You added the following:</b></p></td>
					</tr>
					<tr valign="top">
						<td><p class="Contact">Firstname</p></td>
						<td width="10"><p class="Contact">:</p></td>
						<td><p class="Contact"><b><?=$fname?></b></p></td>
					</tr>
					<tr valign="top">
						<td><p class="Contact">Lastname</p></td>
						<td width="10"><p class="Contact">:</p></td>
						<td><p class="Contact"><b><?=$lname?></b></p></td>
					</tr>
				</table>
				
	<?		
				echo "<Br><br>";			
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1
?>
		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="250" width="325">  
			<form action="<?=$php_self?>" method="post"> 
				<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">First Name:</p></td><td><p><input class="Text" name="fname" type="text" value="" size="25" maxlength="40"></p></td></tr>
				<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">Last Name:</p></td><td><p><input class="Text" name="lname" type="text" value="" size="25" maxlength="40"></p></td></tr>				
				<tr valign="top"><td><font color="#FF0000">*</font></td><td><p class="Contact">Primary Email :</p></td><td><p><input class="Text" type="text" name="email" size="25" maxlength="40" value=""></p></td></tr>
				<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 1 :</p></td><td><p><input class="Text" type="text" name="phone1" size="25" maxlength="40" value=""></p></td></tr>
				<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Phone 2 :</p></td><td><p><input class="Text" type="text" name="phone2" size="25" maxlength="40" value=""></p></td></tr>
				<tr valign="top"><td>&nbsp;</td><td><p class="Contact">Login Expires :</p></td><td><p><input class="Text" type="text" name="loginexpiry" size="25" maxlength="40" value=""></p></td></tr>
				<tr valign="top"><td>&nbsp;</td><td><p class="Contact"></p></td><td><p  class="Contact">Format: YYYY-MM-DD HH:MI:SS    <br>HH in 24hr Format</p></td></tr>
				<tr valign="top">
					<td>&nbsp;</td>
					<td><p class="Contact">Receive Mail:</p></td>
					<td> 
						<p>
							<select class="Text" name="Getmail" size='1'>
								<option value="1" <? if ($user_receive_emails == "1") {echo "selected";} ?>>&nbsp;Yes&nbsp;</option>
								<option value="0" <? if ($user_receive_emails == "0") {echo "selected";} ?>>&nbsp;No&nbsp;</option>
							</select> 
						</p>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr valign="top"><td colspan="3" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
				<tr valign="top"><td colspan="3" align="center"><p>
				<input class="Submit" type="submit" name="createUser" value="Create User"></p></td></tr>  
			</form>
		</table>
<?
		echo $table_end;
		echo "</center>";
	} 
/////////////////////////////////////////////////END OF CREATE SECTION/////////////////////////////////////////////////

/////////////////////////////////////////////////START OF DELETE SECTION///////////////////////////////////////////////
	if($type == "delete")
    {
		echo "<center>";
		echo $table_start;
		
		if($action == "remove")
		{
			$query_delete = "DELETE FROM Users WHERE ID = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}
?>
		
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<table class="sortable"  id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
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

include('bottom.php');	 
?>
