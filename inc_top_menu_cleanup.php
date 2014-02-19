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
    ['<img src="./includes/menu/js/ThemeOffice/mgmt.png" />','<b>Mgmt : Sales Rep : </b> Business Summary','buss_src_container.php',null,'Client Activity'],
    //['<img src="./includes/menu/js/ThemeOffice/mgmt.png" />','<b>Mgmt : Sales Rep : </b>Commissions','rep_msrc2y_container.php',null,'Sales Rep. Commissions'],
    ['<img src="./includes/menu/js/ThemeOffice/mgmt.png" />','<b>Mgmt : Sales Rep : </b>Commissions','comm_m_src_container.php',null,'Sales Rep. Commissions'],
    ['<img src="./includes/menu/js/ThemeOffice/mgmt.png" />','<b>Mgmt : Sales Rep : </b>Client Activity','rep_all_rep_ca_container.php',null,'Client Activity'],
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Mgmt : Trader : </b>Commissions','trdr_mcomm_container.php',null,'Client Activity'], //rep_mtrdr2y_container.php
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Mgmt : Trader : </b>Client Activity','rep_all_trdr_ca_container.php',null,'Client Activity'],
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Analyst : </b>Commissions','clnt_if2y_src_container.php',null,'Client Commissions'],
    //['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Analyst : </b>Client Activity','clnt_all_rep_ca_container.php',null,'Client Activity'],
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
    ['<img src="./includes/menu/js/ThemeOffice/compliance.png" />','<b>Compliance :</b> Compliance Review Log','rep_viewed_mgmt.php',null,'Compliance Review Log'],
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
    ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
    ['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Reporting','mod_exp_expense.php',null,'Expense Items Data Entry'],
		<? 
		if ($user_id == 85 || $user_id == 79 || $user_id == 93 || $user_id == 209 || $user_id == 230 || $user_id == 268 || $user_id = 245 || $user_id == 274 ) {
		?>
    ['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Approval','mod_exp_mod_approver.php',null,'Approve Pending Items'],
    <?
		}
		?>
		<? 
		if ($user_id == 253 ) {
		?>
		['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Processing','mod_exp_mod_processor.php',null,'Mark items as Processed/Paid'],
    <?
		}
		?>
   ],
   _cmSplit, /*<font color="red">[ &beta; version ]</font>*/
   [null,'&#9658 Reports&nbsp;&nbsp;',null,null,'Reports',
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Payout </strong>',null,null,'Payout Reports',
      /*['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Trade Date</strong> Basis',null,null,'Trade Date Basis',
       ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','<b>Payout Detail</b> Payout Report','#',null,'Payout Report'], //pay_detl_tdate_container.php
       ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','<b>Payout Summary</b> Payout Report','#',null,'Payout Summary Report'], //pay_summ_container.php
      ],*/
      //['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Settle Date</strong> Basis',null,null,'Settle Date Basis',
       //['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Payout Report <b>Detail <font color="red">Being Updated</font></b>','#',null,'Payout Report'],
       //['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Payout Report <b>Summary <font color="red">Being Updated</font></b>','#',null,'Payout Summary Report'], 
			 //pay_nsumm_sdate_container.php
		 		['<img src="./includes/menu/js/ThemeOffice/reports.png" />','Monthly Draw','payout_draw_container.php',null,'Payout Monthly Draw'], 
  		 _cmSplit,
			 	['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','V1: Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
		 		['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','V1: Payout Report <b>Summary</b> ','pay_xnsumm_sdate_container.php',null,'Payout Summary Report'], 
  		 _cmSplit,
			 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Sales & Trading <strong>Monthly Revenue Report</strong>','mrr_container.php',null,'Payout Summary Report'], 
	     ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','V2: Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
			 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','V2: Payout Report <b>Summary</b> ','pays_container.php',null,'Payout Summary Report'], 
    ],
   _cmSplit,
			['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Analyst Allocations</strong>',null,null,'Analyst Allocations',
			['<img src="./includes/menu/js/ThemeOffice/maint.png" />', 'Analyst Payout <b>Configuration</b>', 'pay_analyst_config_mgmt_container.php', null,'Analyst Allocations'],    
			<?
			if (checkpriv($privileges,"apadj") == 1) { //only Lloyd Karp and Pravin Prasad for now
			?>
      ['<img src="./includes/menu/js/ThemeOffice/maint.png" />', 'Analyst Payout: <b>Adjustments</b>', 'pay_analyst_adj_mgmt_container.php', null,'Data Entry: Help Entries'],
			 <?
			 }   
			 ?>
     _cmSplit,
			['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Individual)</b>', 'pay_analyst_mgmt_container.php', null,'Analyst Allocations'],
			['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Summary)</b>', 'pay_analyst_summ_container.php', null,'Analyst Allocations'],
			['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>Print Report</b>', 'pay_analyst_gen_report_container.php', null,'Analyst Allocations : Print Report'],
    ],
   _cmSplit,
      ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />', 'Rolling 12 Months Data [~10 sec.]','xl_rep_rolling_12m.php',"_BLANK",'SEARCH: Accounts'],
      ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />', 'Client Payout % Data','cmgmt_export.php',"_BLANK",'Client Payout % Data'],
   _cmSplit,
    	['<img src="./images/centersys.png" />', '<b>Client Tiering:</b> (&beta;: Beta)', '_.php?mod=client_tiering&mode=m', null,'Client Tiering'],
   ],
   _cmSplit,
   [null,'&#9658 Administration&nbsp;&nbsp;',null,null,'System Administration',
    ['<img src="./includes/menu/js/ThemeOffice/users_manage.png" />','<strong>Users</strong>',null,null,'Manage Users',
     //['<img src="./includes/menu/js/ThemeOffice/users_add_new.png" />','<strong>Add</strong> User','javascript:CreateWnd("umgmt_add.php", 600, 500, false);',null,'Manage Users'],
     ['<img src="./includes/menu/js/ThemeOffice/users_add_new.png" />','<strong>Add</strong> User','javascript:showPopWin("umgmt_add.php", 600, 500, null);',null,'Manage Users'],
		 ['<img src="./includes/menu/js/ThemeOffice/users_manage.png" />','<strong>Edit</strong> Users','umgmt.php?type=manage',null,'Edit Users'],
    ],
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/reps.png" />', '<strong>RR</strong> Maintenance','maint_rr.php',null,'RR Maintenance'],
    ['<img src="./includes/menu/js/ThemeOffice/reps.png" />', '<strong>RR</strong> Create New','javascript:CreateWnd("maint_rr_add.php?user_id=<?=$user_id?>", 420, 170, false);',null,'Add RR'],
    ['<img src="./includes/menu/js/ThemeOffice/print.png" />', '<strong>RR</strong> List (Print)','maint_rr_print.php',"_blank",'Print RR List'],
    _cmSplit,
		    ['<img src="./images/centersys.png" />', '<strong>Watch List </strong>Maintenance', 'stocklist_entry_container.php', null,'Open'],
    _cmSplit,
		['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Employee Trades</strong>',null,null,'Employee Trades',
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>External Accounts</strong>',null,null,'External Accounts',
				['<img src="./includes/menu/js/ThemeOffice/accounts.png" />', '<strong>External</strong> Accounts','ext_accts_entry_container.php',null,'External Accounts'],
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>External</strong> Trades (Data Entry)','ext_trades_entry_container.php',null,'Data Entry: Trades'],
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>External</strong> Trades: Maintenance','mod_ext_trades_container.php',null,'Trades: Maintenance & Reporting'],
      ],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>NFS Accounts</strong>',null,null,'NFS Accounts',
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>Trades</strong>','mod_emp_trades_container.php',null,'Trades: Maintenance & Reporting'],
      ],
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Fidelity Accounts</strong>',null,null,'Fidelity Accounts',
				['<img src="./includes/menu/js/ThemeOffice/trades.png" />', '<strong>Trades</strong>','mod_fid_emp_trades_container.php',null,'Trades: Maintenance & Reporting'],
      ]
    ],
    _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/users_manage.png" />','<strong>Client</strong> Maintenance',null,null,'Manage Clients',
			['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'cmgmt_add.php\', 450, 300, null);" />', '<strong>Add</strong> New Client', 'javascript:showPopWin(\'cmgmt_add.php\', 450, 300, null);', null, 'Add New Client'],
			['<img src="./includes/menu/js/ThemeOffice/maint.png" />', '<strong>View / Edit</strong> Clients','cmgmt.php?type=manage',null,'Client Maintenance'],
    ],
   _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/search.png" />', '<strong>SEARCH</strong>: Accounts','srch_a.php',null,'SEARCH: Accounts'],
   _cmSplit,
    ['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>BCM Trends Analysis</strong>',null,null,'BCM Trends Analysis',

			['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis','bcm_trend_container.php',null,'BCM Trends Analysis'], 

    	['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
			['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Alerts Configuration</strong>','bcm_trend_v2_config.php',null,'BCM Trends Analysis : Alerts Configuration'], 
			['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis v2','bcm_trend_v2_container.php',null,'BCM Trends Analysis'], 
			['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>Data Entry</b>)','events_entry_container.php',null,'News and Events'], 
			['<img src="./includes/menu/js/ThemeOffice/reports.png" />','News and Events (<b>View/Edit/Export</b>)','events_entry_mgmt.php',null,'News and Events'], 
    ],
	 _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>GTA Processes</b>', 'gta_processes_container.php', null,'GTA Processes'],
    ['<img src="./images/centersys.png" />', '<b>Customer vs. Streetside</b>', 'cust_vs_street_container.php', null,'Customer vs. Streetside'],

		<?
		if ($user_id == 79 or $user_id == 93 or $user_id == 230 or $user_id == 252 or $user_id == 253) {
		?>
    _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>CLIENT / PROSPECT MAINTENANCE</b>', 'client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
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
	 //check if satisfied <?=$user_id?>
    <?
   if ($user_id == 93) { //Lloyd Karp
   ?>
   ,
	 // actually satisfied <?=$user_id?>
   _cmSplit,
   [null,'&#9658 <b>Lloyd Karp Menu</b>&nbsp;&nbsp;',null,null,'Lloyd Karp', 
    //['<img src="./images/centersys.png" />', '<b>Analyst Payout &alpha;</b>', 'pay_analyst_container.php', null,'Open'],
    //['<img src="./images/centersys.png" />', '<b>Analyst Payout &beta;</b>', 'pay_analyst_mgmt_container.php', null,'Open'],
    //['<img src="./images/centersys.png" />', '<b>Analyst Payout [Reporting & Printing] &alpha;</b>', 'pay_analyst_summ_container.php', null,'Open'],
    _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>EXPENSE REPORTING</b>', 'mod_exp_expense.php', null,'Expense Reporting'],
    _cmSplit,
    //['<img src="./images/centersys.png" />', '<b>CLIENT / PROSPECT LIST (Bobby Efstathiou)</b>', 'rep_client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
    _cmSplit,
    //['<img src="./images/centersys.png" />', '<b>CLIENT / PROSPECT MAINTENANCE</b>', 'client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
    //_cmSplit,
    //['<img src="./images/centersys.png" />', '<b>GTA Processes</b>', 'gta_processes_container.php', null,'GTA Processes'],
    //['<img src="./images/centersys.png" />', '<b>Customer vs. Streetside</b>', 'cust_vs_street_container.php', null,'Customer vs. Streetside'],
    //_cmSplit,
    //['<img src="./images/centersys.png" />', 'TrackSys', 'http://192.168.20.63/track/index.php?do=auth&user_name=brg&password=password&return_to=/track/', "_BLANK",'Open'],
    //['<img src="./images/centersys.png" />', 'TrackSys Integrated', 'tracksys_container.php', null,'TrackSys'],
    
		
   	['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Fidelity Cost Report','fidemp_container.php',null,'Fidelity Cost Report'], 
    _cmSplit,
   	['<img src="./includes/menu/js/ThemeOffice/reports.png" />','BCM Trends Analysis','bcm_trend_container.php',null,'BCM Trends Analysis'], 
		
		//['<img src="./images/centersys.png" />', 'None Available', '#', null,'None Available'],
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
    //['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'cmgmt_add.php\', 450, 300, null);" />', 'Add New Client', 'javascript:showPopWin(\'cmgmt_add.php\', 450, 300, null);', null, 'Add New Client'],
    ['<img src="./images/centersys.png" />', '<b>EXPENSE REPORT</b>', 'mod_exp_expense.php', null,'CLIENT / PROSPECT MAINTENANCE'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>CLIENT / PROSPECT MAINTENANCE</b>', 'client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>Jessica Perno:</b> Client Tiering', '_.php?mod=client_tiering', null,'Client Tiering'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>GTA Processes</b>', 'gta_processes_container.php', null,'GTA Processes'],
    ['<img src="./images/centersys.png" />', '<b>Customer vs. Streetside</b>', 'cust_vs_street_container.php', null,'Customer vs. Streetside'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>Data Entry:</b> Help Entries', 'help_entry_container.php', null,'Data Entry: Help Entries'],
    ['<img src="./images/centersys.png" />', '<b>Data Entry:</b> Server Dependencies', 'tdw_dep_entry_container.php', null,'Data Entry: Server Dependencies'],
     _cmSplit,
		['<img src="./images/centersys.png" />', 'BCM Employee Position Report 13G', 'bcm_pos_container.php', null,'Open'],
     _cmSplit,
		['<img src="./images/centersys.png" />', 'Comprehensive Trade Activity', 'activity_container.php', null,'Open'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>Data Maintenance:</b> Build/Populate Memory', 'javascript:CreateWnd(\'sproc_populate_memory.php\', 404, 216, false)', null,'Open'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>Fidelity:</b> Emp Trades', 'mod_fid_emp_trades_container.php', null,'Open'],
     _cmSplit,
    ['<img src="./images/centersys.png" />', '<b>Analyst Allocations:</b> Adjustments', 'pay_analyst_adj_mgmt_container.php', null,'Data Entry: Help Entries'],
    _cmSplit,
		['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<strong>Analyst Allocations</strong>',null,null,'Analyst Allocations',
		['<img src="./includes/menu/js/ThemeOffice/maint.png" />', 'Analyst Payout <b>Configuration</b>', 'pay_analyst_config_mgmt_container.php', null,'Analyst Allocations'],    
		['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Individual)</b>', 'pay_analyst_mgmt_container.php', null,'Analyst Allocations'],
		['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>(Summary)</b>', 'pay_analyst_summ_container.php', null,'Analyst Allocations'],
		['<img src="./includes/menu/js/ThemeOffice/dollar.png" />', 'Analyst Allocations <b>Print Report</b>', 'pay_analyst_gen_report_container.php', null,'Analyst Allocations : Print Report'],
    ],
    _cmSplit,
		 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
		 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Payout Report <b>Summary</b> ','pay_xnsumm_sdate_container.php',null,'Payout Summary Report'], 
    _cmSplit,
		 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','New Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
		 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','New Payout Report <b>Summary</b> ','pays_container.php',null,'Payout Summary Report'], 
    _cmSplit,
		 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Sales & Trading <strong>Monthly Revenue Report</strong>','mrr_container.php',null,'Payout Summary Report'], 
    _cmSplit,
		 ['<img src="./includes/menu/js/ThemeOffice/excel_new.png" />','Fidelity Cost Report','fidemp_container.php',null,'Payout Summary Report'], 
    _cmSplit,
    ['<img src="./images/centersys.png" />', 'TrackSys Integrated', 'tracksys_a_container.php', null,'TrackSys'],
    _cmSplit,
    ['<img src="./images/centersys.png" />', 'Test Summary Report (Settle Date)', 'pay_summ_sdate_container.php', null,'Open'],
    ['<img src="./images/centersys.png" />', 'BCM Watch List', 'main_stocklist_container.php', null,'Open'],
    ['<img src="./images/centersys.png" />', 'NEW BCM Watch List', 'stocklist_entry_container.php', null,'Open'],
     ['<img src="./images/centersys.png" />', 'Analyst Export Excel', 'http://192.168.20.63/tdw/pay_analyst_excel_create2.php?xl=289^029^3^2008', null,'Open'],
   ['<img src="./images/centersys.png" />', 'New Client Activity', 'activity_container.php', null,'Open'],
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