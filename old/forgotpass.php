<?php

  session_start();
  session_unset();
  session_destroy();

  include('includes/dbconnect.php');
  include('includes/global.php');


?><html>
<head>
<title>LOGIN : CompSys v 2.0</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<body>
<!-- #DE0029 -->
<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td valign="middle" align="center">
		
		<table border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td><img src="images/login_lt.gif"></td>
				<td background="images/login_ts.gif"></td>
				<td><img src="images/login_rt.gif"></td>
			</tr>
			<tr> 
				  <td background="images/login_ls.gif"></td>
				<td>
				<!--  -->
				<table width="400" border="0" cellspacing="0" cellpadding="0">
					<tr> 
							<td><img src="images/compliancelogo.gif" alt="CompSys v 2.0" width="130" height="55"></td>
						<td>&nbsp;</td>
					</tr>
					<tr> 
						<td valign="top" class="links10">&nbsp;&nbsp;Version 2.1.4 (Demo)</td>
						<td><form action="sendinfo.php" method="post">
						<table width="300" border="0" cellpadding="1" cellspacing="0" bgcolor="#DE0029"><tr><td>
						<table background="images/login_boxbkground.jpg" width="298"border="0" cellpadding="3" cellspacing="0" bgcolor="#FFFFFF">
							<tr><td colspan=2 height="10"></td></tr>
							<tr> 
								<td class="login_form_txt">Email:</td>
								<td class="login_form_td">
										<select  class="Text" name="email" size="1">
						<option value="" selected>Please Select</option>
						<?
						$result = mysql_query("SELECT Fullname, Email FROM Users WHERE user_isactive = '1' ORDER BY Fullname") or die (mysql_error());
						
						while ( $row = mysql_fetch_array($result) ) {
						?>
						<option value="<?=$row["Email"]?>"><?=$row["Fullname"]?></option>
					  
						<?
						}
						?>	
						</select>
						<input class="Submit" type="submit" value="Go">
								</td>
							</tr>
							<tr><td colspan=2 height="20"></td></tr>
							<tr><td colspan=2 height="20"></td></tr>
							<tr><td colspan=2 height="20"></td></tr>
							<tr><td colspan=2 height="20"></td></tr>
							<tr><td colspan=2 height="20"></td></tr>
							<!-- <tr><td colspan=2 height="10"><a class="links10gray">Forgot Password?</a> <a class="links10gray" href="forgotpass.php">Click Here.</a></td></tr> -->
						</table>
						</td></tr></table>
					</form>
					</td>
					</tr>
					<tr>
						<td colspan=2 valign="top">
							<p class="red"><img src="images/blinkbox.gif" border="0">&nbsp;Please select your name and submit the form. <BR>Your username and password will be sent to your email address immediately.</p>
						</td>
					</tr>
				</table>
				<!--  -->
				</td>
				  <td background="images/login_rs.gif"></td>
			</tr>
			<tr> 
				  <td><img src="images/login_bl.gif" width="21" height="21"></td>
				<td background="images/login_bs.gif"></td>
				<td><img src="images/login_br.gif"></td>
			</tr>
			<tr> 
				<td height="10" colspan=3 align="center">&nbsp;</td>
			</tr>
			<tr> 
				<td height="10" colspan=3 align="center"><!-- <hr size="1" color="#CC0000"> --></td>
			</tr>
			<tr> 
				<td height="10" colspan=3 align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#AAAAAA">CompSys v 2.0 (DEMO) has been optimized for Internet Explorer v 5.0+<br>
				The Production Release is configurable to client's choice of browser.</font></td>
			</tr>
			<tr> 
				<td height="10" colspan=3 align="center"><!-- <hr size="1" color="#CC0000"> --></td>
			</tr>
			<tr> 
				<td colspan=3 align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#CC0000"><b>Copyright © 2001-2004 CenterSys Group, Inc. All Rights Reserved.</b></font></td>
			</tr>
			
		</table>


		
		</td>
  </tr>
</table>

</body>
</html>
