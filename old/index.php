<?php
//BRG

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
</head>
<body>
<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="0">
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
						<td><form action="login.php" method="post">
						<table width="300" border="0" cellpadding="1" cellspacing="0" bgcolor="#0099FF"><tr><td>
						<table background="images/login/login_boxbkground.jpg" width="298"border="0" cellpadding="3" cellspacing="0" bgcolor="#FFFFFF">
							<tr><td colspan=2 height="10"></td></tr>
							<tr><td colspan=2 height="10">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?=$_client_name?></strong></td></tr>
							<tr><td colspan=2 height="10"></td></tr>
							<tr> 
								<td class="login_form_txt">Username:</td>
								<td class="login_form_td">
								<select class="Text" name="user" size="1">
								<option value="" selected>Please Select</option>
								<?
								$result = mysql_query("SELECT * FROM users WHERE user_isactive = '1' ORDER BY Fullname") or die (mysql_error());
								
								while ( $row = mysql_fetch_array($result) ) {
									if ($row["ID"]==85) {
								?>
								
									<option value="<?=$row["Username"]?>"> => <?=$row["Fullname"]?></option>
								
								<?
									} else {
								?>
									<option value="<?=$row["Username"]?>">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["Fullname"]?></option>
								<?
									}
								}
								?>	
								</select>
								</td>
							</tr>
						 	<tr> 
								<td class="login_form_txt">Password:</td>
								<td class="login_form_td"><input class="Text" type="password" name="pass"> 
								</td>
							</tr>
						 	<tr> 
								<td class="login_form_txt">Remember:</td>
								<td class="login_form_td"><input type="checkbox" name="remember">&nbsp;
								<select class="Text" name="rememberdays">
								<option selected>Select Duration</option>
								<option value="1">1 day</option>
								<option value="5">5 days</option>
								<option value="30">30 days</option>
								<option value="9999">Always</option>
								</select>
								</td>
							</tr>
							<tr>
								<td></td> 
								<td align="left"><input class="Submit" type="submit" value="Login"></td>
							</tr>
							<tr><td colspan=2 height="10"></td></tr>
							<tr><td colspan=2 height="10"></td></tr>
							<tr><td colspan=2 height="10"><a class="links10gray">Forgot Password?</a> <a class="links10gray" href="forgotpass.php">Click Here.</a></td></tr>
						</table>
						</td></tr></table>
					</form>
					</td>
					</tr>
					<!-- Message -->
					<tr>
						<td colspan=2 valign="top">
						   <?php
								if ($logout == "y") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;You have been logged out successfully!</p>");
								} elseif ($login == "n") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;Your Username / Password combination is incorrect. Please try again!<br>If you have forgotten your password click <a href=\"forgotpass.php\">here</a>.</p>");
								} elseif ($login == "ae") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;Your account expired on ".$dval." at ".$tval.". <br>&nbsp;&nbsp;&nbsp;&nbsp;Please contact your Sales Representative to reactivate.</p>");
								} elseif ($forpass == "y") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;You password has been sent to your email address.</p>");
								} elseif ($forpass == "n") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;The email address you submitted is not in our system. No username/password has been sent.</p>");
								} elseif ($signup == "y") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;THANK YOU FOR SIGNING UP! YOU CAN LOGIN USING THE FORM ABOVE.</p>");
								} elseif ($signup == "n") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;SORRY! YOUR USERNAME IS TAKEN. PLEASE TRY ANOTHER!</p>");
								} elseif ($signup == "f") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;AN ACCOUNT HAS ALREADY BEEN SET UP USING THIS E-MAIL ADDRESS. PLEASE USE ANOTHER.</p>");
								} elseif ($user == "n") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;YOUR USERNAME MUST BE AT LEAST 4 CHAREACTERS LONG!</p>");
								} elseif ($pass == "n") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;YOUR PASSWORD MUST BE AT LEAST 4 CHAREACTERS LONG!</p>");
								} elseif ($email == "n") {
									echo("<p class=\"red\"><img src='images/blinkbox.gif' border='0'>&nbsp;YOUR E-MAIL ADDRESS MUST BE AT LEAST 7 CHAREACTERS LONG!</p>");
								}
							?>
						</td>
					</tr>
					<!-- Message END -->
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
