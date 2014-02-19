<?php
//BRG

  session_start();
  session_unset();
  session_destroy();

  include('includes/dbconnect.php');
  include('includes/global.php'); 

// if !$logout and remember login cookie "rlogin" is found, proceed to main section
if ($logout) {
  if ($logout == 'y') {
	?>
	<a href="javascript:window.close()">Close</a><br>
	<a href="<?=$_site_url?>">Login</a>
	<?
	exit;
	}
} else {
	if ($_COOKIE["rlogin"]) {
		Header("Location: login.php?rlogin=".$_COOKIE["rlogin"]);
	}
}
?>
<html>
<head>
<title>LOGIN : <? echo $_app_title; ?></title>
<?
echo "<!--"."Server: ".$_SERVER["SERVER_ADDR"]."-->\n";
echo "<!--"."Client: ".$_SERVER["REMOTE_ADDR"]."-->\n";
echo "<!--"."Administrator Email: ".$_SERVER["SERVER_ADMIN"]."-->\n";
echo "<!--"."Page Process Time: ".date("D, m/d/Y h:i a")."-->\n";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<link rel="shortcut icon" href="./favicon.ico"></link>
<link rel="bookmark" href="./favicon.ico"></link>
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
							<tr>
							  <td colspan=2 height="10"><span class="login_form_txt">TDW Server is undergoing data repair/maintenance. 
							It will be back in service at 12:00&nbsp; Noon.. Sorry for the inconvenience. Thank you.</span></td>
							</tr>
							<tr> 
								<td class="login_form_txt">&nbsp;</td>
								<td class="login_form_td">&nbsp;</td>
							</tr>
							<tr><td colspan=2 height="10"></td></tr>
							<tr><td colspan=2 height="10"></td></tr>
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
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;You have been logged out successfully!</p>");
								} elseif ($login == "n") {
									echo("<a class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;Your Username / Password combination is incorrect. Please try again<img src='images/login/exclaim.gif' border='0'> If you have forgotten your password click <a class='red' href=\"passwdget.php\">>>here<<</a>.</a>");
								} elseif ($login == "ae") {
									echo("<a class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;Your account expired on ".$dval." at ".$tval.". <br>&nbsp;&nbsp;&nbsp;&nbsp;Please contact Technical Support.</a>");
								} elseif ($forpass == "y") {
									echo("<a class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;You password has been sent to your email address on file<img src='images/login/exclaim.gif' border='0'></a>");
								} elseif ($forpass == "n") {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;The email address you submitted is not in our system. No username/password has been sent.</p>");
								} elseif ($signup == "y") {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;THANK YOU FOR SIGNING UP! YOU CAN LOGIN USING THE FORM ABOVE.</p>");
								} elseif ($signup == "n") {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;SORRY! YOUR USERNAME IS TAKEN. PLEASE TRY ANOTHER!</p>");
								} elseif ($signup == "f") {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;AN ACCOUNT HAS ALREADY BEEN SET UP USING THIS E-MAIL ADDRESS. PLEASE USE ANOTHER.</p>");
								} elseif ($user == "n") {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;YOUR USERNAME MUST BE AT LEAST 4 CHAREACTERS LONG!</p>");
								} elseif ($pass == "n") {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;YOUR PASSWORD MUST BE AT LEAST 4 CHAREACTERS LONG!</p>");
								} elseif ($email == "n") {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;YOUR E-MAIL ADDRESS MUST BE AT LEAST 7 CHAREACTERS LONG!</p>");
								}
							?>
							<br>
						  <?php
								if ($mod_requested) {
									echo("<p class=\"red\"><img src='images/login/blinkbox.gif' border='0'>&nbsp;You are trying to access a secure area of TDW which requires you to be logged in. Please login to proceed<img src='images/login/exclaim.gif' border='0'></p>");
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
