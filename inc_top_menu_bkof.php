<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/themes/standard/menubarbkground.jpg" class="menubar">
  <tr> 
    <td class="menubackgr"> <div id="myMenuID"></div>
      <script language="JavaScript" type="text/javascript">
		var myMenu =
		[
			[null,'&nbsp;&nbsp;&nbsp;&nbsp; &#9658 Main Menu',null,null,' Main Menu',
				['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Operations : <b>Reconcile Commissions</b>','reconcile_comm_container.php',null,'Reconcile Commissions'],
				['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Operations : <b>Client Activity</b>','rep_all_rep_ca_container.php',null,'Client Activity'],
				['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Operations : <b>Adjustments</b>','rep_adj_all_rep_ca_container.php',null,'Adjustments'],
			_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Operations : <b>View Adjustments History</b>','rep_adj_report.php',null,' View Adjustments History'],
			_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Operations : <b>Checks & Payments</b>',null,null,'Checks & Payments',
					['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Data Entry','pde_payment_entry_container.php',null,'Data Entry'],
					['<img src="./includes/menu/js/ThemeOffice/operations.png" />','Data Management','check_mgmt.php?type=manage',null,'Data Management'],
				],
			_cmSplit,
		<? if ($user_id == 93 or $user_id == 381 or $user_id == 390 or $user_id == 79 or  $user_id == 403 or 1==1) { ?>
    _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>CLIENT / PROSPECT MAINTENANCE</b>', 'client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
    <? } else { ?>
    ['<img src="./images/centersys.png" />', '<b>CLIENT / PROSPECT MASTER</b>', 'client_master_ro.php', null,'CLIENT / PROSPECT MAINTENANCE'],
		<? } ?>
				/*['<img src="./includes/menu/js/ThemeOffice/users_manage.png" />','<strong>Client</strong> Maintenance',null,null,'Manage Clients',
				['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'cmgmt_add.php\', 450, 300, null);" />', '<strong>Add</strong> New Client', 'javascript:showPopWin(\'cmgmt_add.php\', 450, 300, null);', null, 'Add New Client'],
				['<img src="./includes/menu/js/ThemeOffice/maint.png" />', '<strong>View / Edit</strong> Clients','cmgmt.php?type=manage',null,'Client Maintenance'],
			],*/
			<? if ($user_id == 383)  { ?>
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>BCM Trends Analysis</strong>',null,null,'BCM Trends Analysis',
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>Data Entry</b>)','events_entry_container.php',null,'News and Events'], 
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>View/Edit/Export</b>)','events_entry_mgmt.php',null,'News and Events'], 
    ],
			<? }  ?>
			_cmSplit,
   		 ['<img src="./images/centersys.png" />', '<b>Customer vs. Streetside</b>', 'cust_vs_street_container.php', null,'Customer vs. Streetside'],
			_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/search.png" />', '<strong>SEARCH</strong>: Accounts','srch_a.php',null,'SEARCH: Accounts'],
    _cmSplit,
		  ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Reporting','mod_exp_expense.php',null,'Expense Items Data Entry'],
			],
			_cmSplit,
			[null,'&nbsp;&nbsp;&nbsp;&nbsp; &#9658 Administration&nbsp;&nbsp;&nbsp;',null,null,'System Administration',
				['<img src="./includes/menu/js/ThemeOffice/profile.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
				//['<img src="./includes/menu/js/ThemeOffice/password.png" />','Change Password','javascript:CreateWnd(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, false);',null,'Change my password'],
				['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password'],
			_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/reps.png" />', 'RR Maintenance','maint_rr.php',null,'RR Maintenance'],
				['<img src="./includes/menu/js/ThemeOffice/reps.png" />', 'RR Create New','javascript:CreateWnd("maint_rr_add.php?user_id=<?=$user_id?>", 420, 170, false);',null,'Add RR'],
				['<img src="./includes/menu/js/ThemeOffice/print.png" />', 'Print RR List','maint_rr_print.php',"_blank",'Print RR List'],
			],
			 _cmSplit,
			 [null,'&#9658 Reference&nbsp;&nbsp;',null,null,'Reference',
				['<img src="./includes/menu/js/ThemeOffice/search.png" />','Registered Rep. List','ref_rr.php',null,'Registered Rep. List'],
			 ],
			_cmSplit,
				[null,'&nbsp;&nbsp;&nbsp;&nbsp; &#9658 Help',null,null,'Help',
				['<img src="./includes/menu/js/ThemeOffice/messaging_email.png" />', 'Email Technical Support', '<?=$_email_tech_support?>', null,'Email Technical Support'],
				_cmSplit,
        ['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'about.php\', 400, 200, null);" />', 'About <?=$_app_name?>', 'javascript:showPopWin(\'about.php\', 400, 200, null);', null, 'About <?=$_app_name?>'],
			]
		];
		cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script> </td>
  </tr>
</table>
