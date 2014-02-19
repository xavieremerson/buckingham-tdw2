<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/themes/standard/menubarbkground.jpg" class="menubar">
  <tr> 
    <td class="menubackgr"> <div id="myMenuID"></div>
      <script language="JavaScript" type="text/javascript">
  var myMenu =
  [
   [null,'&#9658 Modules&nbsp;&nbsp;',null,null,'Modules',
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
    //['<img src="./includes/menu/js/ThemeOffice/mgmt.png" />','<b>Mgmt : Sales Rep : </b> Business Summary <font color=blue>(v Beta)</font>','rep_bssrc_container.php',null,'Client Activity'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', 'BCM Watch List', 'stocklist_entry_container.php', null,'Open'],
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>BCM Trends Analysis</strong>',null,null,'BCM Trends Analysis',
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis','bcm_trend_container.php',null,'BCM Trends Analysis'], 
      ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Alerts Configuration</strong>','bcm_trend_v2_config.php',null,'BCM Trends Analysis : Alerts Configuration'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis v2','bcm_trend_v2_container.php',null,'BCM Trends Analysis'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>Data Entry</b>)','events_entry_container.php',null,'News and Events'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>View/Edit/Export</b>)','events_entry_mgmt.php',null,'News and Events'], 
      ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<font color="blue"><strong>CITTA List Maintenance</strong></font>','citta_entry_container.php',null,'CITTA List Maintenance'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<font color="blue"><strong>BCM Trends Analysis w/ CITTA</strong></font>','bcm_trend_v3_container.php',null,'BCM Trends Analysis'], 
    ],
     _cmSplit,
		['<img src="./images/centersys.png" />', 'BCM Employee <b>Position Report 13G</b>', 'bcm_pos_container.php', null,'Open'],
     _cmSplit,
		['<img src="./images/centersys.png" />', '<strong>Expense Reporting</strong>', 'mod_exp_expense.php', null,'Open'],
		<?
		if ($user_id == 268 || $user_id == 274) {
		?>
		['<img src="./images/centersys.png" />', '<strong>Expense Approval</strong>', 'mod_exp_mod_approver.php', null,'Open'],
		<?
		}
		?>
   ],
   _cmSplit,
   [null,'&#9658 My Prefs&nbsp;&nbsp;',null,null,'System Administration',
    ['<img src="./includes/menu/js/ThemeOffice/profile.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
    //['<img src="./includes/menu/js/ThemeOffice/password.png" />','Change Password','javascript:CreateWnd(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, false);',null,'Change my password'],
    ['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password'],
   ],
   _cmSplit,
   [null,'&#9658 Reference&nbsp;&nbsp;',null,null,'Reference',
    ['<img src="./includes/menu/js/ThemeOffice/search.png" />','Registered Rep. List','ref_rr.php',null,'Registered Rep. List'],
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
    <?
   if ($user_id == 93) { //Lloyd Karp
   ?>
   ,
   _cmSplit,
   [null,'&#9658 <b>Lloyd Karp Temporary Menu</b>&nbsp;&nbsp;',null,null,'Lloyd Karp Temporary Menu', 
    //['<img src="./images/centersys.png" />', '<b>Analyst Payout &alpha;</b>', 'pay_analyst_container.php', null,'Open'],
    //['<img src="./images/centersys.png" />', '<b>Analyst Payout &beta;</b>', 'pay_analyst_mgmt_container.php', null,'Open'],
    //['<img src="./images/centersys.png" />', '<b>Analyst Payout [Reporting & Printing] &alpha;</b>', 'pay_analyst_summ_container.php', null,'Open'],
    _cmSplit,
    //['<img src="./images/centersys.png" />', 'TrackSys', 'http://192.168.20.63/track/index.php?do=auth&user_name=brg&password=password&return_to=/track/', "_BLANK",'Open'],
    //['<img src="./images/centersys.png" />', 'TrackSys Integrated', 'tracksys_container.php', null,'TrackSys'],
    ['<img src="./images/centersys.png" />', 'None Available', '#', null,'None Available'],
    _cmSplit,
   ]
   <?
   }   
   ?>
  <?
   if ($user_id == 79) {
   ?>
   ,
   _cmSplit,
   [null,'&#9658 CenterSys&nbsp;&nbsp;',null,null,'CenterSys Menu', 
    ['<img src="./images/centersys.png" />', 'Help Entries', 'help_entry_container.php', null,'Help Entries'],
    ['<img src="./images/centersys.png" />', 'Build/Populate Memory', 'javascript:CreateWnd(\'sproc_populate_memory.php\', 404, 216, false)', null,'Open'],
    _cmSplit,
			['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Analyst Allocations</strong>',null,null,'Analyst Allocations',
			['<img src="./includes/menu/js/ThemeOffice/maint.png" />', 'Analyst Payout <b>Configuration</b>', 'pay_analyst_config_mgmt_container.php', null,'Analyst Allocations'],    
			['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Individual)</b>', 'pay_analyst_mgmt_container.php', null,'Analyst Allocations'],
			['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Summary)</b>', 'pay_analyst_summ_container.php', null,'Analyst Allocations'],
			['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>Print Report</b>', 'pay_analyst_gen_report_container.php', null,'Analyst Allocations : Print Report'],
    ],
    _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>Analyst Payout Configuration</b>', 'pay_analyst_config_mgmt_container.php', null,'Open'],    
    _cmSplit,
    //['<img src="./images/centersys.png" />', 'TrackSys', 'http://192.168.20.63/track/index.php?do=auth&user_name=brg&password=password&return_to=/track/', "_BLANK",'Open'],
    //['<img src="./images/centersys.png" />', 'TrackSys Integrated', 'tracksys_container.php', null,'TrackSys'],
    //['<img src="./images/centersys.png" />', 'TrackSys Integrated', 'tracksys_a_container.php', null,'TrackSys'],
    ['<img src="./images/centersys.png" />', 'TrackSys Integrated', 'tracksys_a_container.php', null,'TrackSys'],
    _cmSplit,
    ['<img src="./images/centersys.png" />', 'Test Summary Report (Settle Date)', 'pay_summ_sdate_container.php', null,'Open'],
    ['<img src="./images/centersys.png" />', 'BCM Watch List', 'main_stocklist_container.php', null,'Open'],
    ['<img src="./images/centersys.png" />', 'NEW BCM Watch List', 'stocklist_entry_container.php', null,'Open'],
    ['<img src="./images/centersys.png" />', 'Analyst Export Excel', 'http://192.168.20.63/tdw/pay_analyst_excel_create2.php?xl=289^029^3^2008', null,'Open'],
    _cmSplit,
    ['<img src="./images/centersys.png" onclick="CreateWnd(\'help.php?item=24\', 404, 216, false)" />', 'About CenterSys Menu', 'javascript:CreateWnd(\'help.php?item=24\', 404, 216, false)', null, 'About CenterSys Menu'],
   ]
   <?
   }   
   ?>
  ];
  cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
  </script> </td>
  </tr>
</table>