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
	
	//Tocqueville Company Logo color #21427B
	 
?>

<?
////
//Get user information for use within the application





?>

<html>
<head>
<script language="JavaScript" src="includes/highlight_tables/tigra_tables.js"></script>

<style type="text/css">

div.menuBar,
div.menuBar a.menuButton,
div.menu,
div.menu a.menuItem {
  font-family: "MS Sans Serif", Arial, sans-serif;
  font-size: 8pt;
  font-style: normal;
  font-weight: normal;
  color: #000000;
}

div.menuBar {
  background-color: #d0d0d0;
  border: 2px solid;
  border-color: #f0f0f0 #909090 #909090 #f0f0f0;
  padding: 4px 2px 4px 2px;
  text-align: left;
}

div.menuBar a.menuButton {
  background-color: transparent;
  border: 1px solid #d0d0d0;
  color: #000000;
  cursor: default;
  left: 0px;
  margin: 1px;
  padding: 2px 6px 2px 6px;
  position: relative;
  text-decoration: none;
  top: 0px;
  z-index: 100;
}

div.menuBar a.menuButton:hover {
  background-color: transparent;
  border-color: #f0f0f0 #909090 #909090 #f0f0f0;
  color: #000000;
}

div.menuBar a.menuButtonActive,
div.menuBar a.menuButtonActive:hover {
  background-color: #a0a0a0;
  border-color: #909090 #f0f0f0 #f0f0f0 #909090;
  color: #ffffff;
  left: 1px;
  top: 1px;
}

div.menu {
  background-color: #d0d0d0;
  border: 2px solid;
  border-color: #f0f0f0 #909090 #909090 #f0f0f0;
  left: 0px;
  padding: 0px 1px 1px 0px;
  position: absolute;
  top: 0px;
  visibility: hidden;
  z-index: 101;
}

div.menu a.menuItem {
  color: #000000;
  cursor: default;
  display: block;
  padding: 3px 1em;
  text-decoration: none;
  white-space: nowrap;
}

div.menu a.menuItem:hover, div.menu a.menuItemHighlight {
  background-color: #000080;
  color: #ffffff;
}

div.menu a.menuItem span.menuItemText {}

div.menu a.menuItem span.menuItemArrow {
  margin-right: -.75em;
}

div.menu div.menuItemSep {
  border-top: 1px solid #909090;
  border-bottom: 1px solid #f0f0f0;
  margin: 4px 2px;
}

</style>
<script type="text/javascript">//<![CDATA[


//----------------------------------------------------------------------------
// Code to determine the browser and version.
//----------------------------------------------------------------------------

function Browser() {

  var ua, s, i;

  this.isIE    = false;  // Internet Explorer
  this.isOP    = false;  // Opera
  this.isNS    = false;  // Netscape
  this.version = null;

  ua = navigator.userAgent;

  s = "Opera";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isOP = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  s = "Netscape6/";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  // Treat any other "Gecko" browser as Netscape 6.1.

  s = "Gecko";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = 6.1;
    return;
  }

  s = "MSIE";
  if ((i = ua.indexOf(s))) {
    this.isIE = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }
}

var browser = new Browser();

//----------------------------------------------------------------------------
// Code for handling the menu bar and active button.
//----------------------------------------------------------------------------

var activeButton = null;

// Capture mouse clicks on the page so any active button can be
// deactivated.

if (browser.isIE)
  document.onmousedown = pageMousedown;
else
  document.addEventListener("mousedown", pageMousedown, true);

function pageMousedown(event) {

  var el;

  // If there is no active button, exit.

  if (activeButton == null)
    return;

  // Find the element that was clicked on.

  if (browser.isIE)
    el = window.event.srcElement;
  else
    el = (event.target.tagName ? event.target : event.target.parentNode);

  // If the active button was clicked on, exit.

  if (el == activeButton)
    return;

  // If the element is not part of a menu, reset and clear the active
  // button.

  if (getContainerWith(el, "DIV", "menu") == null) {
    resetButton(activeButton);
    activeButton = null;
  }
}

function buttonClick(event, menuId) {

  var button;

  // Get the target button element.

  if (browser.isIE)
    button = window.event.srcElement;
  else
    button = event.currentTarget;

  // Blur focus from the link to remove that annoying outline.

  button.blur();

  // Associate the named menu to this button if not already done.
  // Additionally, initialize menu display.

  if (button.menu == null) {
    button.menu = document.getElementById(menuId);
    if (button.menu.isInitialized == null)
      menuInit(button.menu);
  }

  // Reset the currently active button, if any.

  if (activeButton != null)
    resetButton(activeButton);

  // Activate this button, unless it was the currently active one.

  if (button != activeButton) {
    depressButton(button);
    activeButton = button;
  }
  else
    activeButton = null;

  return false;
}

function buttonMouseover(event, menuId) {

  var button;

  // Find the target button element.

  if (browser.isIE)
    button = window.event.srcElement;
  else
    button = event.currentTarget;

  // If any other button menu is active, make this one active instead.

  if (activeButton != null && activeButton != button)
    buttonClick(event, menuId);
}

function depressButton(button) {

  var x, y;

  // Update the button's style class to make it look like it's
  // depressed.

  button.className += " menuButtonActive";

  // Position the associated drop down menu under the button and
  // show it.

  x = getPageOffsetLeft(button);
  y = getPageOffsetTop(button) + button.offsetHeight;

  // For IE, adjust position.

  if (browser.isIE) {
    x += button.offsetParent.clientLeft;
    y += button.offsetParent.clientTop;
  }

  button.menu.style.left = x + "px";
  button.menu.style.top  = y + "px";
  button.menu.style.visibility = "visible";
}

function resetButton(button) {

  // Restore the button's style class.

  removeClassName(button, "menuButtonActive");

  // Hide the button's menu, first closing any sub menus.

  if (button.menu != null) {
    closeSubMenu(button.menu);
    button.menu.style.visibility = "hidden";
  }
}

//----------------------------------------------------------------------------
// Code to handle the menus and sub menus.
//----------------------------------------------------------------------------

function menuMouseover(event) {

  var menu;

  // Find the target menu element.

  if (browser.isIE)
    menu = getContainerWith(window.event.srcElement, "DIV", "menu");
  else
    menu = event.currentTarget;

  // Close any active sub menu.

  if (menu.activeItem != null)
    closeSubMenu(menu);
}

function menuItemMouseover(event, menuId) {

  var item, menu, x, y;

  // Find the target item element and its parent menu element.

  if (browser.isIE)
    item = getContainerWith(window.event.srcElement, "A", "menuItem");
  else
    item = event.currentTarget;
  menu = getContainerWith(item, "DIV", "menu");

  // Close any active sub menu and mark this one as active.

  if (menu.activeItem != null)
    closeSubMenu(menu);
  menu.activeItem = item;

  // Highlight the item element.

  item.className += " menuItemHighlight";

  // Initialize the sub menu, if not already done.

  if (item.subMenu == null) {
    item.subMenu = document.getElementById(menuId);
    if (item.subMenu.isInitialized == null)
      menuInit(item.subMenu);
  }

  // Get position for submenu based on the menu item.

  x = getPageOffsetLeft(item) + item.offsetWidth;
  y = getPageOffsetTop(item);

  // Adjust position to fit in view.

  var maxX, maxY;

  if (browser.isIE) {
    maxX = Math.max(document.documentElement.scrollLeft, document.body.scrollLeft) +
      (document.documentElement.clientWidth != 0 ? document.documentElement.clientWidth : document.body.clientWidth);
    maxY = Math.max(document.documentElement.scrollTop, document.body.scrollTop) +
      (document.documentElement.clientHeight != 0 ? document.documentElement.clientHeight : document.body.clientHeight);
  }
  if (browser.isOP) {
    maxX = document.documentElement.scrollLeft + window.innerWidth;
    maxY = document.documentElement.scrollTop  + window.innerHeight;
  }
  if (browser.isNS) {
    maxX = window.scrollX + window.innerWidth;
    maxY = window.scrollY + window.innerHeight;
  }
  maxX -= item.subMenu.offsetWidth;
  maxY -= item.subMenu.offsetHeight;

  if (x > maxX)
    x = Math.max(0, x - item.offsetWidth - item.subMenu.offsetWidth
      + (menu.offsetWidth - item.offsetWidth));
  y = Math.max(0, Math.min(y, maxY));

  // Position and show it.

  item.subMenu.style.left = x + "px";
  item.subMenu.style.top  = y + "px";
  item.subMenu.style.visibility = "visible";

  // Stop the event from bubbling.

  if (browser.isIE)
    window.event.cancelBubble = true;
  else
    event.stopPropagation();
}

function closeSubMenu(menu) {

  if (menu == null || menu.activeItem == null)
    return;

  // Recursively close any sub menus.

  if (menu.activeItem.subMenu != null) {
    closeSubMenu(menu.activeItem.subMenu);
    menu.activeItem.subMenu.style.visibility = "hidden";
    menu.activeItem.subMenu = null;
  }
  removeClassName(menu.activeItem, "menuItemHighlight");
  menu.activeItem = null;
}

//----------------------------------------------------------------------------
// Code to initialize menus.
//----------------------------------------------------------------------------

function menuInit(menu) {

  var itemList, spanList;
  var textEl, arrowEl;
  var itemWidth;
  var w, dw;
  var i, j;

  // For IE, replace arrow characters.

  if (browser.isIE) {
    menu.style.lineHeight = "2.5ex";
    spanList = menu.getElementsByTagName("SPAN");
    for (i = 0; i < spanList.length; i++)
      if (hasClassName(spanList[i], "menuItemArrow")) {
        spanList[i].style.fontFamily = "Webdings";
        spanList[i].firstChild.nodeValue = "4";
      }
  }

  // Find the width of a menu item.

  itemList = menu.getElementsByTagName("A");
  if (itemList.length > 0)
    itemWidth = itemList[0].offsetWidth;
  else
    return;

  // For items with arrows, add padding to item text to make the
  // arrows flush right.

  for (i = 0; i < itemList.length; i++) {
    spanList = itemList[i].getElementsByTagName("SPAN");
    textEl  = null;
    arrowEl = null;
    for (j = 0; j < spanList.length; j++) {
      if (hasClassName(spanList[j], "menuItemText"))
        textEl = spanList[j];
      if (hasClassName(spanList[j], "menuItemArrow")) {
        arrowEl = spanList[j];
      }
    }
    if (textEl != null && arrowEl != null) {
      textEl.style.paddingRight = (itemWidth 
        - (textEl.offsetWidth + arrowEl.offsetWidth)) + "px";
      // For Opera, remove the negative right margin to fix a display bug.
      if (browser.isOP)
        arrowEl.style.marginRight = "0px";
    }
  }

  // Fix IE hover problem by setting an explicit width on first item of
  // the menu.

  if (browser.isIE) {
    w = itemList[0].offsetWidth;
    itemList[0].style.width = w + "px";
    dw = itemList[0].offsetWidth - w;
    w -= dw;
    itemList[0].style.width = w + "px";
  }

  // Mark menu as initialized.

  menu.isInitialized = true;
}

//----------------------------------------------------------------------------
// General utility functions.
//----------------------------------------------------------------------------

function getContainerWith(node, tagName, className) {

  // Starting with the given node, find the nearest containing element
  // with the specified tag name and style class.

  while (node != null) {
    if (node.tagName != null && node.tagName == tagName &&
        hasClassName(node, className))
      return node;
    node = node.parentNode;
  }

  return node;
}

function hasClassName(el, name) {

  var i, list;

  // Return true if the given element currently has the given class
  // name.

  list = el.className.split(" ");
  for (i = 0; i < list.length; i++)
    if (list[i] == name)
      return true;

  return false;
}

function removeClassName(el, name) {

  var i, curList, newList;

  if (el.className == null)
    return;

  // Remove the given class name from the element's className property.

  newList = new Array();
  curList = el.className.split(" ");
  for (i = 0; i < curList.length; i++)
    if (curList[i] != name)
      newList.push(curList[i]);
  el.className = newList.join(" ");
}

function getPageOffsetLeft(el) {

  var x;

  // Return the x coordinate of an element relative to the page.

  x = el.offsetLeft;
  if (el.offsetParent != null)
    x += getPageOffsetLeft(el.offsetParent);

  return x;
}

function getPageOffsetTop(el) {

  var y;

  // Return the x coordinate of an element relative to the page.

  y = el.offsetTop;
  if (el.offsetParent != null)
    y += getPageOffsetTop(el.offsetParent);

  return y;
}

//]]></script>


<title>Tocqueville Asset Management LP : Trade Compliance</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<body text="#330099" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
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
<!-- Menu bar. -->

<div class="menuBar" style="width:100%;"> <a class="menuButton"
    href="welcome.php"
    onclick="return buttonClick(event, 'homeMenu');"
    onmouseover="buttonMouseover(event, 'homeMenu');"
>Home</a> <a class="menuButton"
    href="welcome.php"
    onclick="return buttonClick(event, 'tradesMenu');"
    onmouseover="buttonMouseover(event, 'tradesMenu');"
>Trades</a> <a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'accountsMenu');"
    onmouseover="buttonMouseover(event, 'accountsMenu');"
>Accounts</a> <a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'reportsMenu');"
    onmouseover="buttonMouseover(event, 'reportsMenu');"
>Reports</a> <a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'administrationMenu');"
    onmouseover="buttonMouseover(event, 'administrationMenu');"
>Administration</a> <a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'helpMenu');"
    onmouseover="buttonMouseover(event, 'helpMenu');"
>Help</a> </div>

<!-- Main menus. -->
<div id="homeMenu" class="menu" onmouseover="menuMouseover(event)">
<a class="menuItem" href="blank.html">File Menu Item 1</a>
<a class="menuItem" href=""
   onclick="return false;"
   onmouseover="menuItemMouseover(event, 'fileMenu2');">
   <span class="menuItemText">File Menu Item 2</span>
   <span class="menuItemArrow">&#9654;</span>
</a>
<a class="menuItem" href="blank.html">File Menu Item 3</a>
<a class="menuItem" href="blank.html">File Menu Item 4</a>
<a class="menuItem" href="blank.html">File Menu Item 5</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="blank.html">File Menu Item 6</a>
</div>


<div id="tradesMenu" class="menu"
     onmouseover="menuMouseover(event)">
<a class="menuItem" href="view_trades.php">View Trades</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="blank.html">Edit Menu Item 2</a>
<a class="menuItem" href=""
   onclick="return false;"
   onmouseover="menuItemMouseover(event, 'editMenu3');"
><span class="menuItemText">Edit Menu Item 3</span><span class="menuItemArrow">&#9654;</span></a>
<a class="menuItem" href="blank.html">Edit Menu Item 4</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="blank.html">Edit Menu Item 5</a>
</div>

<div id="accountsMenu" class="menu">
<a class="menuItem" href="accounts.php">View Accounts</a> 
<a class="menuItem" href="addaccounts.php">Add Accounts</a>
<a class="menuItem" href="expempaccts_csv.php">Export Accounts (Excel/CSV)</a>
</div>

<div id="reportsMenu" class="menu">
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_futurerelease?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; Report 1</font></a> 
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_futurerelease?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; Report 2</font></a>
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_futurerelease?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; Report 3</font></a> 
</div>

<div id="administrationMenu" class="menu">
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_underconstruction?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; System Defaults</font></a>
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_underconstruction?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; Look & Feel</font></a>
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_underconstruction?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; Email Options</font></a>
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_underconstruction?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; Report Options</font></a>
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_tm_underconstruction?>&headingval=<?=$_headinginfo?>"><font color="#FF0000">&Oslash; User Management</font></a>
</div>

<div id="helpMenu" class="menu">
<a class="menuItem" href="sysmsgmain.php?msgval=<?=$_primarycontact?>&headingval=<?=$_primarycontactheading?>">Technical Support</a>
<a class="menuItem" href="<?=$_email_tech_support?>">Email Technical Support</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="about_ly.php">About <?=$_app_name?></a>
</div>

<!-- File sub menus. -->

<div id="fileMenu2" class="menu">
<a class="menuItem" href="blank.html">File Menu 2 Item 1</a>
<a class="menuItem" href="blank.html">File Menu 2 Item 2</a>
</div>

<!-- Edit sub menus. -->

<div id="editMenu3" class="menu"
     onmouseover="menuMouseover(event)">
<a class="menuItem" href="blank.html">Edit Menu 3 Item 1</a>
<a class="menuItem" href="blank.html">Edit Menu 3 Item 2</a>
<div class="menuItemSep"></div>
<a class="menuItem" href=""
   onclick="return false;"
   onmouseover="menuItemMouseover(event, 'editMenu3_3');"
><span class="menuItemText">Edit Menu 3 Item 3</span><span class="menuItemArrow">&#9654;</span></a>
<a class="menuItem" href="blank.html">Edit Menu 3 Item 4</a>
</div>

<div id="editMenu3_3" class="menu">
<a class="menuItem" href="blank.html">Edit Menu 3-3 Item 1</a>
<a class="menuItem" href="blank.html">Edit Menu 3-3 Item 2</a>
<a class="menuItem" href="blank.html">Edit Menu 3-3 Item 3</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="blank.html">Edit Menu 3-3 Item 4</a>
</div>

<!-- Tools sub menus. -->

<div id="toolsMenu1" class="menu">
<a class="menuItem" href="blank.html">Tools Menu 1 Item 1</a>
<a class="menuItem" href="blank.html">Tools Menu 1 Item 2</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="blank.html">Tools Menu 1 Item 3</a>
<a class="menuItem" href="blank.html">Tools Menu 1 Item 4</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="blank.html">Tools Menu 1 Item 5</a>
</div>

<div id="toolsMenu4" class="menu"
     onmouseover="menuMouseover(event)">
<a class="menuItem" href="blank.html">Tools Menu 4 Item 1</a>
<a class="menuItem" href="blank.html">Tools Menu 4 Item 2</a>
<a class="menuItem" href="blank.html"
   onclick="return false;"
   onmouseover="menuItemMouseover(event, 'toolsMenu4_3');"
><span class="menuItemText">Tools Menu 4 Item 3</span><span class="menuItemArrow">&#9654;</span></a>
</div>

<div id="toolsMenu4_3" class="menu">
<a class="menuItem" href="blank.html">Tools Menu 4-3 Item 1</a>
<a class="menuItem" href="blank.html">Tools Menu 4-3 Item 2</a>
<a class="menuItem" href="blank.html">Tools Menu 4-3 Item 3</a>
<a class="menuItem" href="blank.html">Tools Menu 4-3 Item 4</a>
</div>

<!--<br><br>-->
<table width="100%" height="90%" border="0" cellpadding="0" cellspacing="0">