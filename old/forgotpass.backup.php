<?php

  session_start();
  session_unset();
  session_destroy();

  include('includes/dbconnect.php');
  include('includes/global.php');


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
		
		<!-- Include Header Table -->
<? include ('inc_header_table.php'); ?>
<!-- End Include Header Table --> 
		
		<table width="100%"  border="2" cellpadding="0" cellspacing="0" bordercolor="#000000" bgcolor="#21427B">
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>

</td></tr>

<tr><td align="center" valign="top">
        <BR>
        <BR>
        <BR>
        <BR>
<p class="red">Please select your name below and submit the form. <BR>Your username and password will be sent to your email address immediately.</p>

<br>

<form action="sendinfo.php" method="post">

<table cellpadding="3" cellspacing="0" border="0" style="border: 2px solid #CCCCFF;">
	<tr valign="center"> 
 		<td colspan="2" bgcolor="#21427B" align="center"> <p class="QView"><a><sup>Forgot Password</sup></a></p></td>
	</tr>
	<tr>
		<td class="tablecellbkgray12">Email:</td>
		<td  class="tablecellbkgray12">
		<select name="email" size="1">
						<option value="" selected>Please Select</option>
						<?
						$result = mysql_query("SELECT Fullname, Email FROM Users ORDER BY Fullname") or die (mysql_error());
						
						while ( $row = mysql_fetch_array($result) ) {
						?>
						<option value="<?=$row["Email"]?>"><?=$row["Fullname"]?></option>
					  
						<?
						}
						?>	
						</select>
						<input type="submit" value="Go">
		</td>
	</tr>	
</table>

</form>

</td></tr>

<tr><td align="center" valign="bottom"><hr align="center" size="1" noshade color="#FF0000">
				<center><a class="centersys">Powered by</a><br>
				<a class="centersys" href="http://www.centersysgroup.com" target="_blank">CenterSys Group, Inc.</a></center>
			</td>
			</tr>
			
</table>

</body>



</html>