<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="./includes/styles.css" rel="stylesheet" type="text/css" />   
<script language="JavaScript" src="./includes/highlight_tables/tigra_tables.js"></script>
<script language='JavaScript' src='../includes/js/javascript.js'></script>
<!--
<script language='JavaScript' src='includes/js/timer.js'></script>
<script language='JavaScript' src='includes/js/ajax.js'></script>
-->
<script language="JavaScript" src="./includes/js/popup.js"></script>
<script language="JavaScript" src="./includes/js/tbl_sort.js"></script>
<script language="JavaScript" src="./includes/js/tblsort.js"></script>


<script type="text/javascript" src="./includes/submodal/subModal.js"></script>
<script type="text/javascript" src="./includes/submodal/common.js"></script>
<link rel="stylesheet" type="text/css" href="./includes/submodal/subModal.css" />

<script language="JavaScript" src="./includes/menu/JSCookMenu.js" type="text/javascript"></script>
<script language="JavaScript" src="./includes/menu/js/ThemeOffice/theme.js" type="text/javascript"></script>

<link rel="stylesheet" href="./includes/menu/css/template_css.css" type="text/css" />
<link rel="stylesheet" href="./includes/menu/css/theme.css" type="text/css" />
</head>
<body bgcolor="#dddddd" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
		<!-- Menu bar. -->
		<div id="wrapper">
		</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" background="./images/themes/standard/menubarbkground.jpg" class="menubar">
			<tr> 
				<td class="menubackgr" valign="top"> <div id="myMenuID"></div>
					<script language="JavaScript" type="text/javascript">
						var myMenu =
						[
						 [null,'&#9658 Modules&nbsp;&nbsp;',null,null,'Modules',
							_cmSplit,
							['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
							['<img src="./includes/menu/js/ThemeOffice/operations.png" />','<b>Operations :</b> Reconcile Commissions','reconcile_comm_container.php',null,'Reconcile Commissions'],
							['<img src="./includes/menu/js/ThemeOffice/operations.png" />','<b>Operations :</b> Client Activity','rep_all_rep_ca_container.php',null,'Client Activity'],
							['<img src="./includes/menu/js/ThemeOffice/operations.png" />','<b>Operations :</b> Adjustments','rep_adj_all_rep_ca_container.php',null,'Client Activity'],
							['<img src="./includes/menu/js/ThemeOffice/operations.png" />','<b>Operations :</b> View Adjustments History','rep_adj_report.php',null,' View Adjustments History'],
							['<img src="./includes/menu/js/ThemeOffice/operations.png" />','<b>Operations :</b> Checks & Payments',null,null,'Checks & Payments',
							 ['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Data Entry','pde_payment_entry_container.php',null,'Data Entry'],
							 ['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Data Management','check_mgmt.php?type=manage',null,'Data Management'],
							],
						 _cmSplit,
						 ],
						 _cmSplit,
						 [null,'&#9658 Reports&nbsp;&nbsp;',null,null,'Reports',
							_cmSplit,
							['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Payout <font color="red">[ &beta; version ]</font></strong>',null,null,'Payout Reports',
								/*['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Trade Date</strong> Basis',null,null,'Trade Date Basis',
								 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','<b>Payout Detail</b> Payout Report','#',null,'Payout Report'], //pay_detl_tdate_container.php
								 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','<b>Payout Summary</b> Payout Report','#',null,'Payout Summary Report'], //pay_summ_container.php
								],*/
								//['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Settle Date</strong> Basis',null,null,'Settle Date Basis',
								 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','<b>Payout Detail</b> Payout Report','pay_ndetl_sdate_container.php',null,'Payout Report'],
								 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','<b>Payout Summary</b> Payout Report','pay_xnsumm_sdate_container.php',null,'Payout Summary Report'], //pay_nsumm_sdate_container.php
							],
						 _cmSplit,
								['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Analyst Allocations</strong>',null,null,'Analyst Allocations',
								['<img src="./includes/menu/js/ThemeOffice/maint.png" />', 'Analyst Payout <b>Configuration</b>', 'pay_analyst_config_mgmt_container.php', null,'Analyst Allocations'],    
								['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Individual)</b>', 'pay_analyst_mgmt_container.php', null,'Analyst Allocations'],
								['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Summary)</b>', 'pay_analyst_summ_container.php', null,'Analyst Allocations'],
								['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>Print Report</b>', 'pay_analyst_gen_report_container.php', null,'Analyst Allocations : Print Report'],
							],
						 _cmSplit,
								['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />', 'Rolling 12 Months Data [~10 sec.]','xl_rep_rolling_12m.php',"_BLANK",'SEARCH: Accounts'],
								['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />', 'Client Payout % Data','cmgmt_export.php',"_BLANK",'Client Payout % Data'],
						 ],
						 _cmSplit,
						 [null,'&#9658 Administration&nbsp;&nbsp;',null,null,'System Administration',
							['<img src="./includes/menu/js/ThemeOffice/users_manage.png" />','<strong>Users</strong>',null,null,'Manage Users',
							 ['<img src="./includes/menu/js/ThemeOffice/users_add_new.png" />','<strong>Add</strong> User','javascript:CreateWnd("umgmt_add.php", 600, 500, false);',null,'Manage Users'],
							 ['<img src="./includes/menu/js/ThemeOffice/users_manage.png" />','<strong>Edit</strong> Users','umgmt.php?type=manage',null,'Edit Users'],
							],
							_cmSplit,
							['<img src="./includes/menu/js/ThemeOffice/reps.png" />', '<strong>RR</strong> Maintenance','maint_rr.php',null,'RR Maintenance'],
							['<img src="./includes/menu/js/ThemeOffice/reps.png" />', '<strong>RR</strong> Create New','javascript:CreateWnd("maint_rr_add.php?user_id=<?=$user_id?>", 420, 170, false);',null,'Add RR'],
							['<img src="./includes/menu/js/ThemeOffice/print.png" />', '<strong>RR</strong> List (Print)','maint_rr_print.php',"_blank",'Print RR List'],
							_cmSplit,
							['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Employee Trades</strong>',null,null,'Employee Trades',
								['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>External Accounts</strong>',null,null,'External Accounts',
									['<img src="./includes/menu/js/ThemeOffice/accounts.png" />', '<strong>External</strong> Accounts','ext_accts_entry_container.php',null,'External Accounts'],
									['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>External</strong> Trades (Data Entry)','ext_trades_entry_container.php',null,'Data Entry: Trades'],
									['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>External</strong> Trades: Maintenance','mod_ext_trades_container.php',null,'Trades: Maintenance & Reporting'],
								],
								['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>NFS Accounts</strong>',null,null,'NFS Accounts',
									['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>Trades</strong>','mod_emp_trades_container.php',null,'Trades: Maintenance & Reporting'],
								]
							],
							_cmSplit,
							['<img src="./includes/menu/js/ThemeOffice/maint.png" />', '<strong>Client</strong> Maintenance','cmgmt.php?type=manage',null,'Client Maintenance'],
						 _cmSplit,
							['<img src="./includes/menu/js/ThemeOffice/search.png" />', '<strong>SEARCH</strong>: Accounts','srch_a.php',null,'SEARCH: Accounts'],
						 ],
						 _cmSplit,
						 [null,'&#9658 My Prefs&nbsp;&nbsp;',null,null,'System Administration',
							['<img src="./includes/menu/js/ThemeOffice/profile.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
							//['<img src="./includes/menu/js/ThemeOffice/password.png" />','Change Password','javascript:CreateWnd(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, false);',null,'Change my password'],
							['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password'],
						 ],
						 _cmSplit,
						 [null,'&#9658 Help&nbsp;&nbsp;',null,null,'Help',
						 //'<img src="./includes/menu/js/ThemeOffice/help_new.png" />'
							['<img src="./includes/menu/js/ThemeOffice/messaging_email.png" />', 'Email Technical Support', '<?=$_email_tech_support?>', null,'Email Technical Support'],
							['<img src="./includes/menu/js/ThemeOffice/about.png" />', 'TDW Server Dependencies','tdw_dep_print.php?u=<?=$userfullname?>',"_BLANK",'Client Maintenance'],
							['<img src="./includes/menu/js/ThemeOffice/about.png" />', 'TDW Server Status','winsysinfo_container.php',null,'TDW Server Health'],
							_cmSplit,
							['<img src="./images/centersys.png" />', 'TrackSys (Tracking System)', 'tracksys_a_container.php', null,'TrackSys'],
							_cmSplit,
							['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'about.php\', 400, 200, null);" />', 'About <?=$_app_name?>', 'javascript:showPopWin(\'about.php\', 400, 200, null);', null, 'About <?=$_app_name?>'],
						 ]
						];
						cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
						</script> </td>
				</tr>
			</table>




<table width="478" height="365" border="0">
	<tr>
		<td class="changepasswd">
			<?
			echo md5(rand(1,1000000000));
			?>
		</td>
	</tr>
</table>
</body>
</html>