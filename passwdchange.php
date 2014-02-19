<title>Change Password</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
echo "<center>";
//table_start_percent(100, "Change Password");

 ?>
	<form action="<?=$php_self?>" method="post" enctype="multipart/form-data" name="passreset" target="_self">
		<table width="300" height="180" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr> 
			<td colspan="2" align="center">&nbsp;</td>
			</tr>
		<? 
		if ($submit)
		{
			$check = mysql_query("SELECT * FROM Users WHERE ID = '$ID' AND Password  = '".md5($curpassword)."'") or die (mysql_error());
			if (mysql_num_rows($check) >= 1) 
			{
				if ($newpassword == $newconfirmpassword) 
				{
					$checkupdate = mysql_query("UPDATE Users SET Password = '".md5($newpassword)."' WHERE ID = '$ID'") or die (mysql_error());
					?>
					<tr> 
					<td colspan="2" align="center"> <br> <font size="-1" color="#009900" face="Verdana, Arial, Helvetica, sans-serif"><b>Password changed!!</b></font></td>
					</tr>
					<?
				} 
				else 
				{
					?>
					<tr> 
					<td colspan="2" align="center"> <br> <font size="-1" color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif"><b>New Passwords do not match!</b></font></td>
					</tr>
					<?
				}
			} 
			else
			{
				?>
				<tr> 
				<td colspan="2" align="center"> <br> <font size="-1" color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif"><b>Current Password does not match!</b></font></td>
				</tr>
				<?
			}
		} 
		else 
		{
		?>
		<tr> 
		<td colspan="2" align="center">&nbsp;</td>
		</tr>
		<?
		}
		?>
		
			<tr> 
				<td colspan="2" class="names" align="center">&nbsp;</td>  
			</tr>
			<tr> 
				<td><p class="changepasswd">Current Password:</p></td>
				<td><input type="password" class="Text" name="curpassword" size="20" maxlength="20"></td> 
			</tr>
			<tr> 
				<td><p class="changepasswd">New Password:</p></td>
				<td><input class="Text" name="newpassword" type="password"  size="20" maxlength="20"></td>
			</tr>
			<tr> 
				<td><p class="changepasswd">Confirm New Password:</p></td>
				<td><input class="Text" name="newconfirmpassword" type="password"  size="20" maxlength="20"></td>
			</tr>
			<tr> 
				<td colspan="2" class="names" align="center">&nbsp;</td>
			</tr>
			<tr> 
				<td colspan="2" align="center">
        <input class="Submit" name="submit" type="submit" value="Change Password">&nbsp;&nbsp;
        <!--<input class="Submit" name="submit" type="Button" value="Close" onclick="hidePopWin(false)">
				<input class="Submit" name="close" type="button" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" onclick="window.top.hidePopWin()"></td>--><!--javascript:window.close();-->
			</tr>
		</table>
	</form>
<? 	
//table_end_percent();
	echo "</center>";
?>
