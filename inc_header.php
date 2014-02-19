<?php
//BRG
ob_start();

  session_start();
  session_register('user');
  session_register('pass');
  session_register('userfullname');
  session_register('user_id');
	session_register('user_initials');
	session_register('role');
  session_register('user_email');
  session_register('user_isadmin');
  session_register('tval');
  session_register('dval');
  session_register('rr_num');
  session_register('menufile');
	
	session_register('privileges');
  
  if ($user == '')
  {
  //Removed /tdw/ from the URI string, getting 404 errors.
  Header("Location: index.php?mod_requested=".str_replace('/tdw/','',$_SERVER["REQUEST_URI"]));
  exit;
  }

	//20110912 variable lost from session requiring login again.
  if (!$menufile)
  {
  //Removed /tdw/ from the URI string, getting 404 errors.
  Header("Location: index.php?mod_requested=".str_replace('/tdw/','',$_SERVER["REQUEST_URI"]));
  exit;
  }


  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

////
//Get user information for use within the application
//
// Currently implemented in login.php and registered as session variable.
// Have to include user privilege field later on and register that too.

?>
<html>
<head>
<!--<meta http-equiv="X-UA-Compatible" content="IE=8" />-->
<?
echo "<!--"."Server: ".$_SERVER["SERVER_ADDR"]."-->\n";
echo "<!--"."Client: ".$_SERVER["REMOTE_ADDR"]."-->\n";
echo "<!--"."Administrator Email: ".$_SERVER["SERVER_ADMIN"]."-->\n";
echo "<!--"."Page Process Time: ".date("D, m/d/Y h:i a")."-->\n";
echo "<!--"."User ID: ".$user_id."-->\n";
echo "<!--"."User Fullname: ".$userfullname."-->\n";
echo "<!--"."pstr: ".$privileges."-->\n";
echo "<!--"."dcar: ".checkpriv($privileges,"dcar")."-->\n";
?>
<link rel="shortcut icon" href="favicon.ico"></link>
<link rel="bookmark" href="favicon.ico"></link>
<title><?=$_app_title?> @ BRG</title>

<script>
//Mute status bar texts
function hidestatus(){
  window.status=''
  return true
}
if (document.layers)
document.captureEvents(Event.MOUSEOVER | Event.MOUSEOUT)
document.onmouseover=hidestatus
document.onmouseout=hidestatus
</script>

<script language="JavaScript" src="includes/highlight_tables/tigra_tables.js"></script>
<script language='JavaScript' src='includes/js/javascript.js'></script>
<!--
<script language='JavaScript' src='includes/js/timer.js'></script>
<script language='JavaScript' src='includes/js/ajax.js'></script>
-->
<script language="JavaScript" src="includes/js/popup.js"></script>
<script language="JavaScript" src="includes/js/tbl_sort.js"></script>
<script language="JavaScript" src="includes/js/tblsort.js"></script>


<script type="text/javascript" src="includes/submodal/subModal.js"></script>
<script type="text/javascript" src="includes/submodal/common.js"></script>
<link rel="stylesheet" type="text/css" href="includes/submodal/subModal.css" />

<script language="JavaScript" src="includes/menu/JSCookMenu.js" type="text/javascript"></script>
<script language="JavaScript" src="includes/menu/js/ThemeOffice/theme.js" type="text/javascript"></script>

<link rel="stylesheet" href="includes/menu/css/template_css.css" type="text/css" />
<link rel="stylesheet" href="includes/menu/css/theme.css" type="text/css" />

<!-- TEMP PPRA -->
<!--<link href="includes/mainpage.css" rel="stylesheet" type="text/css">-->
<!-- TEMP PPRA -->

<link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<!-- <body background="images/bk1.jpg" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"> -->
<!-- <body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" onLoad="InitializeTimer()"> -->
<body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">

<!--<?=show_array($_SESSION)?>-->

<!-- TOP LEVEL TABLE -->
<!-- <table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" background="images/bk1.jpg"> -->
<table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" bgcolor="#F4F8FB">
  <tr valign="top">
    <td height="20"> 
<!-- This section not required
<div id="dhtmltooltip"></div>
<script language='JavaScript' src='includes/tooltip.js'></script> -->
<!-- background="images/table_bkground_grey.jpg" -->
<!-- <table width="100%"  border="0" cellpadding="0" cellspacing="0" background="images/bk2.jpg" > -->
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="FFFFFF">
  <tr> 
    <!-- <td width="50" height="50"><img src="images/compliancelogo_v1.gif" ></td> -->
  <td width="80"><img src="images/logow64h47.gif" ></td>
    <td align="right" valign="top"> 
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> 
            <table width="100%"  border="0" cellspacing="1" cellpadding="1">
              <tr> 
                <td align="left" valign="top"><img src="images/client_appw290h47.gif" border="0"></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td valign="top">
<?
if (strpos($_SERVER["HTTP_USER_AGENT"],'MSIE') > 0 ) {
	$str_show_header = "";
} else {
	//$str_show_header = "<font color='red'>TDW is NOT approved for this browser.</font>";
}
?>
      <table width="100%" height="47">
        <tr>
          <td align="right" valign="top"><a class="links10top">User: <?=$userfullname?><!--<br><?=$privileges?>--><!--[Login expires on <?=$dval?> at <?=$tval?>]--> </a> [<a href="logout.php?logoutval=<?=$userfullname?>" class="links10top">Logout</a>] </td>
        </tr>
        <tr>
          <td align="right" nowrap><a class="ghm"><?=$str_show_header?></a></td><!-- _global_header_message-->
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- Menu bar. -->
<?
//include('inc_top_menu.php');
?>
<?
include($menufile);
//initiate page load time routine
$time=getmicrotime(); 
?>
<!-- End Menu bar -->
  </td>
</tr>
<tr valign="top">
  <td valign="top">
    <table width="100%" height="100%" border="0" cellpadding="3" cellspacing="0">
      <tr>  
        <td valign="top">