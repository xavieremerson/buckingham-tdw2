<?php
  session_start();
  session_register('user');
  session_register('pass');
	session_register('userfullname');
	session_register('user_id');
	session_register('user_email');
	session_register('user_isadmin');
	session_register('tval');
	session_register('dval');

	if ($user == '')
	{
	Header("Location: index.php");
	exit;
	}

  include('includes/dbconnect.php');
  include('includes/global.php'); 

function get_demo_company() {
	$query_statement = "select company from demo_account order by auto_id desc limit 1";	
	$result = mysql_query($query_statement) or die (mysql_error());
		while ( $row = mysql_fetch_array($result) ) 
		{
			return($row["company"]);
		}
}
	 
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
<script language='JavaScript' src='includes/timer.js'></script>
<script language='JavaScript' src='includes/ajax.js'></script>
<script language="JavaScript" src="includes/popup.js"></script>
<script language="JavaScript" src="includes/sorttable.js"></script>
<!-- TEMP PPRA -->
<link href="includes/mainpage.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
<!--
function removeClassName (elem, className) {
	elem.className = elem.className.replace(className, "").trim();
}

function addCSSClass (elem, className) {
	removeClassName (elem, className);
	elem.className = (elem.className + " " + className).trim();
}

String.prototype.trim = function() {
	return this.replace( /^\s+|\s+$/, "" );
}

function stripedTable() {
	if (document.getElementById && document.getElementsByTagName) {  
		var allTables = document.getElementsByTagName('table');
		if (!allTables) { return; }

		for (var i = 0; i < allTables.length; i++) {
			if (allTables[i].className.match(/[\w\s ]*scrollTable[\w\s ]*/)) {
				var trs = allTables[i].getElementsByTagName("tr");
				for (var j = 0; j < trs.length; j++) {
					removeClassName(trs[j], 'alternateRow');
					addCSSClass(trs[j], 'normalRow');
				}
				for (var k = 0; k < trs.length; k += 2) {
					removeClassName(trs[k], 'normalRow');
					addCSSClass(trs[k], 'alternateRow');
				}
			}
		}
	}
}

window.onload = function() { stripedTable(); }
-->
</script>

<style type="text/css">
<!--
body {
	background: #FFF;
	color: #000;
	font: normal normal 12px Verdana, Geneva, Arial, Helvetica, sans-serif;
	margin: 10px;
	padding: 0
}

table, td, a {
	color: #000;
	font: normal normal 12px Verdana, Geneva, Arial, Helvetica, sans-serif
}

h1 {
	font: normal normal 18px Verdana, Geneva, Arial, Helvetica, sans-serif;
	margin: 0 0 5px 0
}

h2 {
	font: normal normal 16px Verdana, Geneva, Arial, Helvetica, sans-serif;
	margin: 0 0 5px 0
}

h3 n.c
	font: normal normal 13px Verdana, Geneva, Arial, Helvetica, sans-serif;
	color: #008000;
	margin: 0 0 15px 0
}

-->
</style>
<!-- TEMP PPRA -->

<link rel="stylesheet" type="text/css" href="includes/styles.css">
<link rel="stylesheet" type="text/css" href="status_app/style.css">
</head>
<!-- <body background="images/bk1.jpg" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"> -->
<body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" onLoad="InitializeTimer()">
<!-- TOP LEVEL TABLE -->
<!-- <table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" background="images/bk1.jpg"> -->
<table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" bgcolor="#F4F8FB">
	<tr valign="top">
    <td height="20"> 

<div id="dhtmltooltip"></div>
<script language='JavaScript' src='includes/tooltip.js'></script>
<!-- background="images/table_bkground_grey.jpg" -->
<!-- <table width="100%"  border="0" cellpadding="0" cellspacing="0" background="images/bk2.jpg" > -->
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#2155AD">
  <tr> 
    <!-- <td width="50" height="50"><img src="images/compliancelogo_v1.gif" ></td> -->
	<td width="50" height="50"><img src="images/compliancelogo_bb.gif" ></td>
    <td align="right" valign="top"> <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> <table width="100%"  border="0" cellspacing="1" cellpadding="1">
              <tr> 
                <!-- <td align="left" valign="top"><a class="CompanyName">&nbsp;&nbsp;<? echo $_company_name; ?></a></td> -->
								<td align="left" valign="top"><a class="CompanyName">&nbsp;&nbsp;<? echo get_demo_company() ?></a></td>
                <td align="right" valign="top"><a class="links10top">User: <?=$userfullname?> [Login expires on <?=$dval?> at <?=$tval?>] </a> (<a href="logout.php" class="links10top">Logout</a>) </td>
              </tr>
            </table></td>
        </tr>
        <tr>
					<td>
						<table width="100%">
							<tr>
          			<td align="left" nowrap><a class="AppName">&nbsp;&nbsp;&nbsp;&nbsp;<? echo $_app_name ." v ".$_version; ?></a></td>
								<td align="right" nowrap><a class="links10top"><?=$_global_header_message?></a></td>
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
