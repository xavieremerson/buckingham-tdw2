<?php

  session_start();
  session_register('user');
  session_register('pass');
	session_register('userfullname');
	session_register('user_id');
	session_register('user_email');
	session_register('user_isadmin');
	
	if ($user == '')
	{
	Header("Location: index.php");
	exit;
	}

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	
	//Tocqueville Company Logo color #21427B
	 
?>

<?
////
//Get user information for use within the application
//
// Currently implemented in login.php and registered as session variable.
// Have to include user privilege field later on and register that too.

?>

<html>
<head>
<link REL="SHORTCUT ICON" HREF="favicon.ico"> 
<title><?=$_app_title?></title>
<script language="JavaScript" src="includes/highlight_tables/tigra_tables.js"></script>
<script language='JavaScript' src='includes/javascript.js'></script>
<script language="JavaScript" src="includes/popup.js"></script>
<script language="JavaScript" src="includes/sorttable.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
<!-- TOP LEVEL TABLE -->
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="3" bordercolor="#333333" bordercolordark="#000000" bordercolorlight="#999999">
	<tr valign="top">
    <td height="20"> 

<div id="dhtmltooltip"></div>
<script language='JavaScript' src='includes/tooltip.js'></script>
<!-- background="images/table_bkground_grey.jpg" -->
<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
  <tr> 
    <td width="50" height="50"><img src="images/compliancelogo.gif" ></td>
    <td align="right" valign="top"> <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> <table width="100%"  border="0" cellspacing="1" cellpadding="1">
              <tr> 
                <td align="left" valign="top"><a class="CompanyName">&nbsp;&nbsp;<? echo $_company_name; ?></a></td>
                <td align="right" valign="top"><a class="links10">User: <?=$userfullname?></a> (<a href="logout.php" class="links10">Logout</a>) </td>
              </tr>
            </table></td>
        </tr>
        <tr>
					<td>
						<table width="100%">
							<tr>
          			<td align="left" nowrap><a class="AppName">&nbsp;&nbsp;&nbsp;&nbsp;<? echo $_app_name ." v ".$_version; ?></a></td>
								<td align="right" nowrap><a class="links10"><?=$_global_header_message?></a></td>
							</tr>
						</table>
					</td>
        </tr>
      </table></td>
  </tr>
</table>
<!-- Menu bar. -->
<?
include('inc_top_menu.php');
?>
<!-- End Menu bar -->
<!--<br><br>-->

	</td>
</tr>
<tr valign="top">
	<td valign="top">
	
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
