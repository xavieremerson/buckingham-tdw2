<?php

  session_start();
  session_register('user');
  session_register('pass');
	
	if ($user == '')
	{
	Header("Location: index.php");
	exit;
	}

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	
	//Tocqueville COmpany Logo color #21427B
	 
?>
<html>
<head>
<script language="JavaScript" src="includes/javascript/navcond.js"></script>
<script language="JavaScript" src="includes/highlight_tables/tigra_tables.js"></script>
<script language="JavaScript">

var myNavBar1 = new NavBar(0); 
var dhtmlMenu;

//define menu items (first parameter of NavBarMenu specifies main category width, second specifies sub category width in pixels)
//add more menus simply by adding more "blocks" of same code below

dhtmlMenu = new NavBarMenu(100, 0);
dhtmlMenu.addItem(new NavBarMenuItem("Home", "welcome.php"));
myNavBar1.addMenu(dhtmlMenu);

dhtmlMenu = new NavBarMenu(100, 120);
dhtmlMenu.addItem(new NavBarMenuItem("Accounts", ""));
dhtmlMenu.addItem(new NavBarMenuItem("View Accounts", "accounts.php"));
dhtmlMenu.addItem(new NavBarMenuItem("Add Accounts", "addaccounts.php"));
dhtmlMenu.addItem(new NavBarMenuItem("Export (csv)", "expempaccts_csv.php"));
//dhtmlMenu.addItem(new NavBarMenuItem("MSNBC", "test2.php"));

myNavBar1.addMenu(dhtmlMenu);

dhtmlMenu = new NavBarMenu(100, 120);
dhtmlMenu.addItem(new NavBarMenuItem("Trades", ""));
dhtmlMenu.addItem(new NavBarMenuItem("View Trades", "view_trades.php"));
//dhtmlMenu.addItem(new NavBarMenuItem("Techweb", "test2.php"));
myNavBar1.addMenu(dhtmlMenu);

dhtmlMenu = new NavBarMenu(150, 150);
dhtmlMenu.addItem(new NavBarMenuItem("Administration", ""));
dhtmlMenu.addItem(new NavBarMenuItem("System Defaults", "administration.php?option=sdef"));
dhtmlMenu.addItem(new NavBarMenuItem("Look & Feel", "administration.php?option=lf"));
dhtmlMenu.addItem(new NavBarMenuItem("Email Options", "administration.php?option=eml"));
dhtmlMenu.addItem(new NavBarMenuItem("Report Options", "administration.php?option=rpt"));
dhtmlMenu.addItem(new NavBarMenuItem("User Management", "administration.php?option=usermgmt"));
myNavBar1.addMenu(dhtmlMenu);

dhtmlMenu = new NavBarMenu(100, 150);
dhtmlMenu.addItem(new NavBarMenuItem("Help", ""));
dhtmlMenu.addItem(new NavBarMenuItem("Email Technical Support", "<?=$_email_tech_support?>"));
dhtmlMenu.addItem(new NavBarMenuItem("Support Options", "supportoptions.php"));
myNavBar1.addMenu(dhtmlMenu);

//set menu colors
//setColors(bdColor, hdrFgColor, hdrBgColor, hdrHiFgColor, hdrHiBgColor, itmFgColor, itmBgColor, itmHiFgColor, itmHiBgColor)

myNavBar1.setColors("#000000", "#FFFFFF", "#21427B", "#ffffff", "#C0C0C0", "#000000", "#cccccc", "#ffffff", "#000080")

//uncomment below line to center the menu (valid values are "left", "center", and "right"
//myNavBar1.setAlign("center")

var fullWidth;

function init() {

  // Get width of window, need to account for scrollbar width in Netscape.

  fullWidth = getWindowWidth() 
    - (isMinNS4 && getWindowHeight() < getPageHeight() ? 16 : 0);

  myNavBar1.resize(fullWidth);
  myNavBar1.create();
  myNavBar1.setzIndex(2);
  //UNCOMMENT BELOW LINE TO MOVE MENU DOWN 50 pixels
  myNavBar1.moveTo(0, 55);
}
</script>
<title>Tocqueville Asset Management LP : Trade Compliance</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<body text="#330099" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" onload="init()">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="62" height="55"><img src="images/companylogosmall.gif" width="62" height="55"></td>
    <td align="right" valign="top"> <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> <table width="100%"  border="0" cellspacing="3" cellpadding="3">
              <tr> 
                <td align="left" valign="top"><a class="CompanyName">&nbsp;&nbsp;<? echo $_company_name; ?></a></td>
                <td align="right" valign="top"><a href="logout.php" class="links12">Overview</a> | <a href="logout.php" class="links12">Help</a> | <a href="about_ly.php" class="links12">About</a> | <a href="logout.php" class="links12">Logout</a></td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          <td align="left"><a class="AppName">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $_app_name ." v ".$_version; ?></a></td>
        </tr>
      </table></td>
  </tr>
</table>
<br><br>
<table width="100%" height="90%" border="0" cellpadding="0" cellspacing="0">