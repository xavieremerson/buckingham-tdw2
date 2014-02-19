<?php

//needs fixing, start from template
include('inc_header.php');

?>
		<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
		<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
		<!-- helper script that uses the calendar -->
		<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
		<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
		<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
<?

	if ($submit) 
	{
		$result = mysql_query("Update Users set Username = '$Username', Fullname = '$Fullname', Email = '$Email', Workphone = '$Workphone', Mobilephone = '$Mobilephone', Report_via_email = '$Report_via_email' where ID = '$ID'") or die (mysql_error());
	
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
	
?>

	<form action="<?=$php_self?>" method="post">
			<br />
			<? tsp(100, "Personal Information"); ?>
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
			<table cellpadding="0" cellspacing="4" border="0">
				<tr valign="top">
					 <td valign="top"  colspan="3">
					 <br>
					 <table cellpadding="2"><tr><td></td></tr></table>
					</td>
				</tr>
				<!-- <br><BR> -->
				<tr>
					<td colspan="3">
						<table cellpadding="2" cellspacing="0" border="0">
							<input type="hidden" name="ID" value="<?=$ID?>">
							<tr valign="top"><td colspan="3" align="center"><p><input name="submit" class="Submit" type="submit" value="Update Profile"></p></td></tr> 
						</table>
					</td>
				</tr>
			</form>
			</table>			
			<? tep(); ?>

<?php
	include('inc_footer.php');
?>


