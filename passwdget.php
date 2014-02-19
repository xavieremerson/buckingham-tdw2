<?php
//BRG last change 05/03/2008 PPRA

  session_start();
  session_unset();
  session_destroy();

  include('includes/dbconnect.php');
  include('includes/global.php'); 

?>
<html>
<head>
<title>LOGIN : <? echo $_app_title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<link rel="shortcut icon" href="favicon.ico"></link>
<link rel="bookmark" href="favicon.ico"></link>
</head>
<body>
<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td valign="middle" align="center"> 
		
		<table border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td><img src="images/login/login_lt.gif"></td>
				<td background="images/login/login_ts.gif"></td>
				<td><img src="images/login/login_rt.gif"></td>
			</tr>
			<tr> 
				  <td background="images/login/login_ls.gif"></td> 
				<td>
				<!--  -->
				<table width="400" border="0" cellspacing="0" cellpadding="0">
					<tr> 
							<td><img src="images/logo.gif" alt="" border="0"></td>
						<td>&nbsp;</td>
					</tr>
					<tr> 
						<td valign="top" class="links10">&nbsp;&nbsp;Version <?=$_version?></td> 
						<td><form action="passwdsend.php" method="post">
						<table width="300" border="0" cellpadding="1" cellspacing="0" bgcolor="#0099FF"><tr><td>
						<table background="images/login/login_boxbkground.jpg" width="298"border="0" cellpadding="3" cellspacing="0" bgcolor="#FFFFFF">
							<tr><td colspan=2 height="10"></td></tr>
							<tr><td colspan=2 height="10">&nbsp;&nbsp;&nbsp;<strong><?=$_client_name?></strong></td></tr>
							<tr><td colspan=2 height="10"></td></tr>
							<tr><td colspan=2 height="10" class="login_form_txt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please select your name:</td></tr>
							<tr> 
								<td class="login_form_txt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
							<tr><td colspan=2 height="20"><br><br><br><br><br></td></tr>
						</table>
						</td></tr></table>
					</form>
					</td>
					</tr>
					<tr>
						<td colspan=2 valign="top">
							<p class="red"><img src="images/login/blinkbox.gif" border="0">&nbsp;Please select your name and submit the form. <BR>Your username and password will be sent to your email address immediately.</p>
						</td>
					</tr>
				</table>
				<!--  -->
				</td>
				  <td background="images/login/login_rs.gif"></td>
			</tr>
			<tr> 
				  <td><img src="images/login/login_bl.gif" width="21" height="21"></td>
				<td background="images/login/login_bs.gif"></td>
				<td><img src="images/login/login_br.gif"></td>
			</tr>
			<tr> 
				<td colspan=3 align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#CC0000"><b>CenterSys Group, Inc.</b></font></td>
			</tr>
			
		</table>


		
		</td>
  </tr>
</table>

</body>
</html>
