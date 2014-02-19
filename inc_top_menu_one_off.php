<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/themes/standard/menubarbkground.jpg" class="menubar">
  <tr> 
    <td class="menubackgr"> <div id="myMenuID"></div>
      <script language="JavaScript" type="text/javascript">
  var myMenu =
  [
   [null,'&#9658 Administration&nbsp;&nbsp;',null,null,'System Administration',
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
    _cmSplit,
		['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis v2','bcm_trend_v2_container.php',null,'BCM Trends Analysis'], 
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
    ['<img src="./includes/menu/js/ThemeOffice/compliance.png" />','<b>Compliance :</b> Compliance Review Log','rep_viewed_mgmt.php',null,'Compliance Review Log'],
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
   _cmSplit,
	 
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>BCM Trends Analysis</strong>',null,null,'BCM Trends Analysis',
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis','bcm_trend_container.php',null,'BCM Trends Analysis'], 
      ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Alerts Configuration</strong>','bcm_trend_v2_config.php',null,'BCM Trends Analysis : Alerts Configuration'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis v2','bcm_trend_v2_container.php',null,'BCM Trends Analysis'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>Data Entry</b>)','events_entry_container.php',null,'News and Events'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>View/Edit/Export</b>)','events_entry_mgmt.php',null,'News and Events'], 
      ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<font color="blue"><strong>CITTA List Maintenance &beta;</strong></font>','citta_entry_container.php',null,'CITTA List Maintenance'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<font color="blue"><strong>BCM Trends Analysis w/ CITTA &beta;</strong></font>','bcm_trend_v3_container.php',null,'BCM Trends Analysis'], 
    ],
	 _cmSplit,

	 
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
		['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Employee Trades</strong>',null,null,'Employee Trades',
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>External Accounts</strong>',null,null,'External Accounts',
				['<img src="./includes/menu/js/ThemeOffice/accounts.png" />', '<strong>External</strong> Accounts','ext_accts_entry_container.php',null,'External Accounts'],
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>External</strong> Trades (Data Entry)','ext_trades_entry_container.php',null,'Data Entry: Trades'],
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>External</strong> Trades: Maintenance','mod_ext_trades_container.php',null,'Trades: Maintenance & Reporting'],
      ],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>NFS ACCOUNTS</strong>',null,null,'NFS Accounts',
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>Trades</strong>','mod_emp_trades_container.php',null,'Trades: Maintenance & Reporting'],
      ],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Fidelity Accounts</strong>',null,null,'Fidelity Accounts',
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>Trades</strong>','mod_fid_emp_trades_container.php',null,'Trades: Maintenance & Reporting'],
      ]
    ],
    _cmSplit,
		  ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Reporting','mod_exp_expense.php',null,'Expense Items Data Entry'],
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