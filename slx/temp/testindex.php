<?php

  session_start();
  session_unset();
  session_destroy();

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	
	//company logo color #21427B
	 
?>
<html>
<head>
<title><? echo $_app_title; ?></title>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<body text="#330099" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" >

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="left" valign="top">


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="62" height="55"><img src="images/companylogosmall.gif" width="62" height="55"></td>
    <td align="right" valign="top"> 
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> 
						<table width="100%"  border="0" cellspacing="3" cellpadding="3">
              <tr> 
                <td align="left" valign="top"><a class="CompanyName">&nbsp;&nbsp;<? echo $_company_name; ?></a></td>
                <td align="right" valign="top"><a href="about_ln.php" class="links12">About</a></td> 
              </tr>
            </table>
					</td>
        </tr>
        <tr> 
          <td align="left"><a class="AppName">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $_app_name ." v ".$_version; ?></a></td>
        </tr>
      </table>
		</td>
  </tr>
</table>
<table width="100%"  border="2" cellpadding="0" cellspacing="0" bordercolor="#000000" bgcolor="#21427B">
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0">
  <tr valign="top"> 
    <td> <center>
        <BR>
        <BR>
        <BR>
        <BR>
        <BR>
        <BR>
        <BR>
        <?php

			  if ($logout == "y") {

			    echo("<p class=\"red\">You have been logged out successfully!</p>");

			  } elseif ($login == "n") {

			    echo("<p class=\"red\">Your Username / Password combination is incorrect. Please try again!<br>If you have forgotten your password click <a href=\"forgotpass.php\">here</a>.</p>");

			  } elseif ($forpass == "y") {

			    echo("<p class=\"red\">You password has been sent to your email address.</p>");

			  } elseif ($forpass == "n") {

			    echo("<p class=\"red\">The email address you submitted is not in our system. No username/password has been sent.</p>");

			  } elseif ($signup == "y") {

			    echo("<p class=\"red\">THANK YOU FOR SIGNING UP! YOU CAN LOGIN USING THE FORM ABOVE.</p>");

			  } elseif ($signup == "n") {

			    echo("<p class=\"red\">SORRY! YOUR USERNAME IS TAKEN. PLEASE TRY ANOTHER!</p>");

			  } elseif ($signup == "f") {

			    echo("<p class=\"red\">AN ACCOUNT HAS ALREADY BEEN SET UP USING THIS E-MAIL ADDRESS. PLEASE USE ANOTHER.</p>");

			  } elseif ($user == "n") {

			    echo("<p class=\"red\">YOUR USERNAME MUST BE AT LEAST 4 CHAREACTERS LONG!</p>");

			  } elseif ($pass == "n") {

			    echo("<p class=\"red\">YOUR PASSWORD MUST BE AT LEAST 4 CHAREACTERS LONG!</p>");

			  } elseif ($email == "n") {

			    echo("<p class=\"red\">YOUR E-MAIL ADDRESS MUST BE AT LEAST 7 CHAREACTERS LONG!</p>");

			  }

			?>
      </center>
      <BR>
			</td>
  </tr>
  <tr> 
    <td align="center"> <form action="testlogin.php" method="post">
    <input type="hidden" name="frompage" value="<?=$frompage?>">
        <table cellpadding="3" cellspacing="0" border="0" style="border: 2px solid #CCCCFF;">
          <tr valign="center"> 
            <td colspan="2" bgcolor="#21427B" align="center"> <p class="QView"><a><sup>LOGIN</sup></a></p></td>
          </tr>
          <tr class="tablecellbkgray12"> 
            <td>Username:</td>
            <td>
						<select name="user" size="1">
						<option value="" selected>Please Select</option>
						<?
						$result = mysql_query("SELECT Username, Fullname FROM Users ORDER BY Fullname") or die (mysql_error());
						
						while ( $row = mysql_fetch_array($result) ) {
						?>
						<option value="<?=$row["Username"]?>"><?=$row["Fullname"]?></option>
					  
						<?
						}
						?>	
						</select>
						</td>
          </tr>
         <tr class="tablecellbkgray12"> 
            <td>Password:</td>
            <td><input type="password" name="pass" value="password"> <input type="submit" value="Go"> 
            </td>
          </tr>
        </table>
      </form></td>
  </tr>
  <tr> 
    <td align="center"> <h6>Forgot your password? <a href="forgotpass.php">Click 
        here</a>.</h6></td>
  </tr>
  <tr> 
    <td align="center"></td>
  </tr>
</table>
</td></tr> 
<tr> 
  <td align="center"></td>
</tr>

<tr><td align="center" valign="bottom"><hr align="center" size="1" noshade color="#FF0000">
				<center><a class="centersys">Powered by</a><br>
				<a class="centersys" href="http://www.centersysgroup.com" target="_blank">CenterSys Group, Inc.</a></center>
			</td>
			</tr>
			
</table>


</body>
</html>
