<?php

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
                <td align="right" valign="top"><a href="logout.php" class="links12">Help</a> 
                  | <a href="about.php" class="links12">About</a></td>
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
        </center>
      <BR>
			</td>
  </tr>
  <tr> 
    <td align="center"> 
        <table cellpadding="3" cellspacing="0" border="0" style="border: 2px solid #CCCCFF;">
          <tr valign="center"> 
            <td colspan="2" bgcolor="#21427B" align="center"> <p class="QView"><a><sup>About</sup></a></p></td>
          </tr>
          <tr class="tablecellbkgray12"> 
            <td>Username:</td>
            <td>
						test
					</td>	
          </tr>
         <tr class="tablecellbkgray12"> 
            <td>Password:</td>
            <td>test
            </td>
          </tr>
        </table>
      </td>
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
