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
    <td width="50" height="50"><img src="images/companylogosmall.gif" width="50" height="50"></td>
    <td align="right" valign="top"> 
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> 
						<table width="100%"  border="0" cellspacing="1" cellpadding="1">
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
 				<BR><BR><? include ('about.html');?>
       </center>
      
			</td>
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
