<div class="menuBar" style="width:100%;"> 
<!-- onclick="return buttonClick(event, 'homeMenu');"  -->
<!--onclick="return buttonClick(event, 'tradesMenu');" -->
<a class="menuButton"
    href="main.php"
    onmouseover="buttonMouseover(event, 'homeMenu');"> <img src="images/buttons/home.gif" border="0"> </a> 

<a class="menuButton"
    href="actionitems.php"> <img src="images/buttons/tasks.gif" border="0"> </a>

<a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'tradesMenu');"
    onmouseover="buttonMouseover(event, 'tradesMenu');"> <img src="images/buttons/trades.gif" border="0"> </a> 
	
<a class="menuButton"
    href="view_trades_m.php"
	onclick="return buttonClick(event, 'accountsMenu');" 
    onmouseover="buttonMouseover(event, 'accountsMenu');"> <img src="images/buttons/accts.gif" border="0"> </a> 

<a class="menuButton"
    href="" 
    onclick="return buttonClick(event, 'stocklistMenu');"
    onmouseover="buttonMouseover(event, 'stocklistMenu');"> <img src="images/buttons/lists.gif" border="0"> </a> 

<a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'reportsMenu');"
    onmouseover="buttonMouseover(event, 'reportsMenu');"> <img src="images/buttons/reports.gif" border="0"> </a> 

<a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'documentsMenu');"
    onmouseover="buttonMouseover(event, 'documentsMenu');"> <img src="images/buttons/documents.gif" border="0"> </a> 

<a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'administrationMenu');"
    onmouseover="buttonMouseover(event, 'administrationMenu');"> <img src="images/buttons/admin.gif" border="0"> </a> 

<a class="menuButton"
    href=""
    onclick="return buttonClick(event, 'helpMenu');"
    onmouseover="buttonMouseover(event, 'helpMenu');"> <img src="images/buttons/help.gif" border="0"> </a> </div>

<!-- Main menus. -->
<!-- <div id="homeMenu" class="menu" onmouseover="menuMouseover(event)">
<a class="menuItem" href="welc.php">Main Page</a>
</div>
 -->

<!-- <div id="tradesMenu" class="menu" onmouseover="menuMouseover(event)">
<a class="menuItem" href="view_trades_m.php">View Trades</a>
<a class="menuItem" href="view_trades.php" onMouseover="ddrivetip('This module is to be deactivated/removed. <BR> It contains trades downloaded from RBC Dain. Please use the <b><i>View Trades</i></b> menu instead.','yellow', 300)"; onMouseout="hideddrivetip()">View Trades (From RBC Dain)</a>
</div>
 -->
 
<div id="tradesMenu" class="menu">
<a class="menuItem" href="view_trades_mult.php">View Trades</a> 

<?
if($user_id != $_comp_off_id)
{
?>
<a class="menuItem" href="main.php" onClick="javascript:CreateWnd('pop_order_entry.php?user_id=<?=$user_id?>', 680, 340, false);">Order Entry</a> 
<?
}
?>


<a class="menuItem" href="piggyback.php">Front-running/Piggy-backing</a>
</div> 
 
<div id="accountsMenu" class="menu">
<a class="menuItem" href="accounts.php">View Accounts</a> 
<a class="menuItem" href="expempaccts_csv.php">Export Accounts <B>(Excel/CSV)</B></a>
</div>

<div id="stocklistMenu" class="menu">

<?
//SYSTEM DEFINED LISTS
$query_get = "SELECT alis_auto_id, alis_title_name FROM alis_admin_lists WHERE alis_isactive = '1'";
$result_get = mysql_query($query_get) or die(mysql_error());

while($row_get = mysql_fetch_array($result_get))
{
?>
<a class="menuItem" href="lists.php?list_type=<?=$row_get['alis_auto_id']?>"><?=$row_get['alis_title_name']?></a> 
<?
}
?>

<?
//USER DEFINED LISTS
$query_user_list = "SELECT usli_auto_id, usli_title_name FROM usli_user_lists WHERE usli_user_id = '".$user_id."' AND usli_isactive = '1'";
$result_user_list = mysql_query($query_user_list) or die(mysql_error());

if(mysql_num_rows($result_user_list) > 0)
{
	echo '<div class="menuItemSep"></div>';
}

while($row_user_list = mysql_fetch_array($result_user_list))
{
?>
<a class="menuItem" href="user_lists_tickers.php?user_list_type=<?=$row_user_list['usli_auto_id']?>"><?=$row_user_list['usli_title_name']?></a> 
<?
}
?>



<!-- <a class="menuItem" href="lists.php?list_type=watch">Watch List</a>
<a class="menuItem" href="lists.php?list_type=gray">Gray List</a>
 --><div class="menuItemSep"></div>
<a class="menuItem" href="lists_es.php?list_type=m">Marketmaker Stock List</a> 
<a class="menuItem" href="lists_es.php?list_type=b">Banker Stock List</a>
<a class="menuItem" href="lists_es.php?list_type=a">Analyst Stock List</a>
</div>

<div id="reportsMenu" class="menu">
<a class="menuItem" href="rep_srv.php?repfile=rep_cr.php">Current Compliance Report</a>
<a class="menuItem" href="rep_srv.php?repfile=rep_tr.php">Current Trades Report</a>

<?
//AD HOC REPORTS
$query_rep = "SELECT * FROM arep_adhoc_reports WHERE arep_user_id = '".$user_id."' AND arep_isactive = '1'";
$result_rep = mysql_query($query_rep) or die(mysql_error());

while($row_rep = mysql_fetch_array($result_rep))
{
?>
	<a class="menuItem" href="rep_ad_hoc.php?id=<?=$row_rep['arep_auto_id']?>&flag=2"><?=$row_rep['arep_name']?> Report</a>
<?
}
?>

<div class="menuItemSep"></div>

<a class="menuItem" href="rep_srv.php?repfile=rep_hs.php">Historical Reports</a>
<div class="menuItemSep"></div>

 
<a class="menuItem" href="rep_srv.php?repfile=rep_sl.php">Stock Lists</a>
<a class="menuItem" href="rep_srv.php?repfile=rep_ea.php">Employee Accounts</a>


<!-- <div class="menuItemSep"></div>
<a class="menuItem" href="sysmsgmain.php?msgval=_tm_futurerelease&headingval=<?=$_headinginfo?>"><font color="#FF0000"><B>&Oslash; Report (Open)</B></font></a> 

<a class="menuItem" href="trades_report_m_hist.php"><input class="Text" name="symbol_list" type="text" size="20" maxlength="60"></a>  -->
</div>

<div id="documentsMenu" class="menu">
<a class="menuItem" href="docs.php">Manage Documents & Forms</a> 
<a class="menuItem" href="expempaccts_csv.php">Export Accounts <B>(Excel/CSV)</B></a>
</div>




<div id="administrationMenu" class="menu">
<!-- <a class="menuItem" href="trade_upload.php">Upload Trade File (.csv)</a>
<div class="menuItemSep"></div>
 -->

<a class="menuItem" href="myprofile.php">My Profile / Preferences</a>
<a class="menuItem" href="javascript:CreateWnd('changepassword.php?ID=<?=$user_id?>', 350, 250, false);">Change Password</a>

<div class="menuItemSep"></div>
<a class="menuItem" href="ad_hoc_rep.php">Custom Reports</a>

<?
if ($user_isadmin == 1) 
{
?>

	<div class="menuItemSep"></div>
	<!-- <a class="menuItem" href="sysmsgmain.php?msgval=_tm_underconstruction&headingval=<?=$_headinginfo?>"><font color="#FF0000"><B>&Oslash; System Defaults</B></font></a>-->
	<!-- <a class="menuItem" href="sysmsgmain.php?msgval=_tm_underconstruction&headingval=<?=$_headinginfo?>"><font color="#FF0000"><B>&Oslash; Look & Feel</B></font></a> -->
	<a class="menuItem" href="user_mgmt.php?type=create&headingval=<?=$_headinginfo?>">Create User</a>
	<a class="menuItem" href="user_mgmt.php?type=manage&headingval=<?=$_headinginfo?>">Manage User</a>
	
	<div class="menuItemSep"></div>
	<?
	$query_list = "SELECT alis_auto_id FROM alis_admin_lists WHERE alis_isactive = '1'";
	$result_list = mysql_query($query_list) or die(mysql_error());
	
	if ($user_isadmin == 1) 
	{
	?>
		<a class="menuItem" href="dyn_lists.php?type=create">Create List</a> 
		
		<?
		if(mysql_num_rows($result_list) > 0)
		{
		?>	
		<a class="menuItem" href="dyn_lists.php?type=manage">Manage List</a>
		<?
		}
		?>
		<div class="menuItemSep"></div>
	<?
	}
	?>
	
	<a class="menuItem" href="admin.php">Demo Administration</a>
	<a class="menuItem" href="mysql.php">Database Administration</a>
	<a class="menuItem" href="create_demo.php?type=create">Create Demo Account</a> 
<?
}
?>
<div class="menuItemSep"></div>
<a class="menuItem" href="user_lists.php?type=create">Create <B>My List</B></a>
<?
if(mysql_num_rows($result_user_list) > 0)
{
?>
	<a class="menuItem" href="user_lists.php?type=manage">Manage <B>My List</B></a>
<?
}
?>

</div>

<div id="helpMenu" class="menu">
<a class="menuItem" href="trak/?do=newtask&project=1">Technical Support</a>
<a class="menuItem" href="<?=$_email_tech_support?>">Email Technical Support</a>
<div class="menuItemSep"></div>
<a class="menuItem" href="trak/"><font color="#0000FF"><B>TrakSYS</B></font></a>
<div class="menuItemSep"></div>
<a class="menuItem" href="http://nasd.complinet.com/nasd/display/index.html" target="_blank"><B>NASD Manual Online</B></a>
<a class="menuItem" href="http://www.sec.gov/rules/final.shtml" target="_blank"><B>SEC Rules</B></a>
<div class="menuItemSep"></div>
<a class="menuItem" href="about_ly.php">About <?=$_app_name?></a>
</div>

