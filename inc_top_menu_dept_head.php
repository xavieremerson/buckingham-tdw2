<?php
$menuItems = array(
    'client_revenue' => (in_array($user_id, array(79, 290, 92, 93, 252, 95, 357, 394, 383, 230))) ? true : false,
    'expense_approval' => (in_array($user_id, array(79, 85, 93, 209, 230, 268, 245, 274))) ? true : false,
    'expense_processing' => (in_array($user_id, array(79, 93, 253))) ? true : false,
    'apadj' => (checkpriv($privileges,"apadj") == 1) ? true : false,
    'clients' => (in_array($user_id, array(79, 381, 390, 403))) ? 'edit' : 'view',
    'client_prospect' => (in_array($user_id, array(79, 93, 253, 381, 390, 403))) ? 'maintenance' : 'master',
    'lloyd_karp_menu' => (in_array($user_id, array(93, 403))) ? true : false,
    'centersys_menu' => ($user_id == 79) ? true : false,
);

?>
<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/themes/standard/menubarbkground.jpg" class="menubar">
<tr> 
<td class="menubackgr"> <div id="myMenuID"></div>
<script lang="javascript">
var _emptyMenuItem = ['<img src="./images/menu/transparent.png" />','&nbsp;','#', null,' '];
var myMenu =
[
    [null,'&#9658 Main Menu&nbsp;&nbsp;',null,null,'Main Menu',
        _cmSplit,
		_emptyMenuItem,
		['<img src="./images/menu/centersys.png" />', '<b>CLIENT REVENUE</b>', '_.php?mod=client_revenue__v2&mode=m', null,'CLIENT REVENUE'],    
		['<img src="./images/menu/centersys.png" />', 'REVENUE <b>[Tiered By Sales Rep.]</b>', '_.php?mod=client_revenue_by_rep&mode=m', null,'CLIENT REVENUE'],    
		['<img src="./images/menu/centersys.png" />', 'CLIENT REVENUE <b>[Account Status]</b>', '_.php?mod=client_revenue_by_rep_tier__v2&mode=m', null,'CLIENT REVENUE'],    
		_emptyMenuItem,
		['<img src="./images/menu/centersys.png" />', 'SALES REVENUE SUMMARY', '_.php?mod=sales_rev_summ&mode=m', null,'CLIENT REVENUE'],  
        _cmSplit,
		_emptyMenuItem,
        ['<img src="./images/menu/mgmt.png" />','<b>Mgmt : Sales Rep : </b> Business Summary','buss_src_container.php',null,'Client Activity'],
        ['<img src="./images/menu/mgmt.png" />','<b>Mgmt : Sales Rep : </b>Commissions','comm_m_src_container.php',null,'Sales Rep. Commissions'],
        ['<img src="./images/menu/mgmt.png" />','<b>Mgmt : Sales Rep : </b>Client Activity','rep_all_rep_ca_container.php',null,'Client Activity'],
        _cmSplit,
        _emptyMenuItem,
        ['<img src="./images/menu/reports.png" />','<b>Mgmt : Trader : </b>Commissions','trdr_mcomm_container.php',null,'Client Activity'], //rep_mtrdr2y_container.php
        ['<img src="./images/menu/reports.png" />','<b>Mgmt : Trader : </b>Client Activity','rep_all_trdr_ca_container.php',null,'Client Activity'],
        _cmSplit,
        _emptyMenuItem,
        ['<img src="./images/menu/reports.png" />','<b>Analyst : </b>Commissions','clnt_if2y_src_container.php',null,'Client Commissions'],
        _cmSplit,
        _emptyMenuItem,
        //['<img src="./images/menu/compliance.png" />','<b>Compliance :</b> Compliance Review Log','rep_viewed_mgmt.php',null,'Compliance Review Log'],
        _cmSplit,
        _emptyMenuItem,
        ['<img src="./images/menu/operations.png" />','<b>Operations :</b> Client Activity','rep_all_rep_ca_container.php',null,'Client Activity'],
        ['<img src="./images/menu/operations.png" />','<b>Operations :</b> Adjustments','rep_adj_all_rep_ca_container.php',null,'Client Activity'],
        ['<img src="./images/menu/operations.png" />','<b>Operations :</b> View Adjustments History','rep_adj_report.php',null,' View Adjustments History'],
        ['<img src="./images/menu/operations.png" />','<b>Operations :</b> Checks & Payments',null,null,'Checks & Payments',
            ['<img src="./images/menu/operations.png" />','Data Entry','pde_payment_entry_container.php',null,'Data Entry'],
            ['<img src="./images/menu/operations.png" />','Data Management','check_mgmt.php?type=manage',null,'Data Management']
        ],
        _emptyMenuItem,
        //['<img src="./images/menu/dollar.png" />','<b>Expense :</b> Reporting','mod_exp_expense.php',null,'Expense Items Data Entry'],
        <?php if (in_array($user_id, array(79, 85, 93, 209, 230, 268, 245, 274))): ?>
        //['<img src="./images/menu/dollar.png" />','<b>Expense :</b> Approval','mod_exp_mod_approver.php',null,'Approve Pending Items'],
        <?php endif; ?>
        <?php if(in_array($user_id, array(79, 93, 253))): ?>
        //['<img src="./images/menu/dollar.png" />','<b>Expense :</b> Processing','mod_exp_mod_processor.php',null,'Mark items as Processed/Paid'],
        <?php endif; ?>
        _cmSplit,	
    ],
    _cmSplit,
    [null,'&#9658 Reports&nbsp;&nbsp;',null,null,'Reports',
        _cmSplit,
        ['<img src="./images/menu/reports.png" />','<b>Payout </b>',null,null,'Payout Reports',
            ['<img src="./images/menu/reports.png" />','Monthly Draw','payout_draw_container.php',null,'Payout Monthly Draw'], 
            _cmSplit,
            ['<img src="./images/menu/excel_new.png" />','V1: Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
            ['<img src="./images/menu/excel_new.png" />','V1: Payout Report <b>Summary</b> ','pay_xnsumm_sdate_container.php',null,'Payout Summary Report'], 
            _cmSplit,
            ['<img src="./images/menu/excel_new.png" />','Sales & Trading <b>Monthly Revenue Report</b>','mrr_container.php',null,'Payout Summary Report'], 
            ['<img src="./images/menu/excel_new.png" />','V2: Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
            ['<img src="./images/menu/excel_new.png" />','V2: Payout Report <b>Summary</b> ','pays_container.php',null,'Payout Summary Report']
        ],
        _cmSplit,
        ['<img src="./images/menu/reports.png" />','<b>Analyst Allocations</b>',null,null,'Analyst Allocations',
            ['<img src="./images/menu/maint.png" />', 'Analyst Payout <b>Configuration</b>', 'pay_analyst_config_mgmt_container.php', null,'Analyst Allocations'],
            <?php if (checkpriv($privileges,"apadj") == 1): //only Lloyd Karp and Pravin Prasad for now ?>
            ['<img src="./images/menu/maint.png" />', 'Analyst Payout: <b>Adjustments</b>', 'pay_analyst_adj_mgmt_container.php', null,'Data Entry: Help Entries'],
            <?php endif; ?>
            _cmSplit,
            ['<img src="./images/menu/dollar.png" />', 'Analyst Allocations <b>(Individual)</b>', 'pay_analyst_mgmt_container.php', null,'Analyst Allocations'],
            ['<img src="./images/menu/dollar.png" />', 'Analyst Allocations <b>(Summary)</b>', 'pay_analyst_summ_container.php', null,'Analyst Allocations'],
            ['<img src="./images/menu/dollar.png" />', 'Analyst Allocations <b>Print Report</b>', 'pay_analyst_gen_report_container.php', null,'Analyst Allocations : Print Report']
        ],
        _cmSplit,
        ['<img src="./images/menu/excel_new.png" />', 'Rolling 12 Months Data [~10 sec.]','xl_rep_rolling_12m.php',"_BLANK",'SEARCH: Accounts'],
        ['<img src="./images/menu/excel_new.png" />', 'Client Payout % Data','cmgmt_export.php',"_BLANK",'Client Payout % Data'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>Client Tiering</b>', '_.php?mod=client_tiering&mode=m', null,'Client Tiering']
    ],
    _cmSplit,
    [null,'&#9658 Data Administration&nbsp;&nbsp;',null,null,'Data Administration',
        ['<img src="./images/menu/users_manage.png" />','<b>Users</b>',null,null,'Manage Users',
            ['<img src="./images/menu/users_add_new.png" />','<b>Add</b> User','javascript:CreateWnd("umgmt_add.php", 600, 500, false);',null,'Manage Users'],
            ['<img src="./images/menu/users_manage.png" />','<b>Edit</b> Users','umgmt.php?type=manage',null,'Edit Users']
        ],
        _cmSplit,
        ['<img src="./images/menu/reps.png" />', '<b>RR</b> Maintenance','maint_rr.php',null,'RR Maintenance'],
        ['<img src="./images/menu/reps.png" />', '<b>RR</b> Create New','javascript:CreateWnd("maint_rr_add.php?user_id=<?=$user_id?>", 420, 170, false);',null,'Add RR'],
        ['<img src="./images/menu/print.png" />', '<b>RR</b> List (Print)','maint_rr_print.php',"_blank",'Print RR List'],
        _cmSplit,
        ['<img src="./images/menu/users_manage.png" />','<b>Client</b> Maintenance',null,null,'Manage Clients',
            ['<img src="./images/menu/about.png" />', '<b>Add</b> New Client', 'javascript:showPopWin(\'cmgmt_add.php\', 450, 300, null);', null, 'Add New Client'],
            <?php if(in_array($user_id, array(79, 381, 390, 403))): ?>
            ['<img src="./images/menu/maint.png" />', '<b>View / Edit</b> Clients','cmgmt.php?type=manage',null,'Client Maintenance'],
            <?php else: ?>
            ['<img src="./images/menu/maint.png" />', '<b>View</b> Clients','cmgmt_ro.php?type=manage',null,'Client Maintenance'],		
            <?php endif; ?>
        ],
        _cmSplit,
        ['<img src="./images/menu/search.png" />', '<b>SEARCH</b>: Accounts','srch_a.php',null,'SEARCH: Accounts'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>GTA Processes</b>', 'gta_processes_container.php', null,'GTA Processes'],
        ['<img src="./images/menu/centersys.png" />', '<b>Customer vs. Streetside</b>', 'cust_vs_street_container.php', null,'Customer vs. Streetside'],
        <?php if(in_array($user_id, array(79, 93, 253, 381, 390, 403))): ?>
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>CLIENT / PROSPECT MAINTENANCE</b>', 'client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
        <?php else:  ?>
        ['<img src="./images/menu/centersys.png" />', '<b>CLIENT / PROSPECT MASTER</b>', 'client_master_ro.php', null,'CLIENT / PROSPECT MAINTENANCE'],
        <?php endif; ?>
    ],
    _cmSplit,
    [null,'&#9658 My Prefs&nbsp;&nbsp;',null,null,'System Administration',
        ['<img src="./images/menu/profile.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
        ['<img src="./images/menu/about.png" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password']
    ],
    _cmSplit,
    [null,'&#9658 Reference&nbsp;&nbsp;',null,null,'Reference',
        ['<img src="./images/menu/search.png" />','Registered Rep. List','ref_rr.php',null,'Registered Rep. List']
    ],
    _cmSplit,
    [null,'&#9658 Help&nbsp;&nbsp;',null,null,'Help',
        ['<img src="./images/menu/messaging_email.png" />', 'Email Technical Support', '<?=$_email_tech_support?>', null,'Email Technical Support'],
        ['<img src="./images/menu/about.png" />', 'TDW Server Dependencies','tdw_dep_print.php?u=<?=$userfullname?>',"_BLANK",'Client Maintenance'],
        ['<img src="./images/menu/about.png" />', 'TDW Server Status','winsysinfo_container.php',null,'TDW Server Health'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', 'TrackSys (Tracking System)', 'tracksys_a_container.php', null,'TrackSys'],
        _cmSplit,
        ['<img src="./images/menu/about.png" />', 'About <?=$_app_name?>', 'javascript:showPopWin(\'about.php\', 400, 200, null);', null, 'About <?=$_app_name?>']
    ],
    <?php if (in_array($user_id, array(93, 403))): //Lloyd Karp or Steve Soto ?>
    _cmSplit,
    [null,'&#9658 <b>Lloyd Karp Menu</b>&nbsp;&nbsp;',null,null,'Lloyd Karp', 
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>ANALYST: VIEW CLIENT ACTIVITY</b>', 'anly_all_rep_ca_container.php', null,'ANALYST: CLIENT ACTIVITY'],
        ['<img src="./images/menu/centersys.png" />', '<b>PAYOUT RECONCILIATION REPORT</b>', 'payout_reconciliation_container_v2.php', null,'PAYOUT RECONCILIATION'],
        ['<img src="./images/menu/centersys.png" />', 'MOVED TO MAIN MENU (SALES REVENUE SUMMARY) <b>[<i><font color="red">&beta;</font></i>]</b>', '#', null,'CLIENT REVENUE'],    
        _cmSplit
    ],
    <?php endif; ?>
    <?php if ($user_id == 79): //Pravin Prasad?>
    _cmSplit,
    [null,'&#9658 CenterSys&nbsp;&nbsp;',null,null,'CenterSys Menu',
		['<img src="./images/menu/centersys.png" />', 'SALES REVENUE SUMMARY', 'sales_rev_rep_summ.php', null,'SALES REVENUE SUMMARY'],    
        _cmSplit,
		['<img src="./images/menu/centersys.png" />', '<b>Client REVENUE</b>', '_.php?mod=client_revenue&mode=m', null,'CLIENT REVENUE'],    
		['<img src="./images/menu/centersys.png" />', '<b>Client REVENUE (By Rep.)</b>', '_.php?mod=client_revenue_by_rep&mode=m', null,'CLIENT REVENUE'],    
		['<img src="./images/menu/centersys.png" />', '<b>CLIENT REVENUE (By Rep./Tier) (<font color=red>&beta;eta</font>)</b>', '_.php?mod=client_revenue_by_rep_tier&mode=m', null,'CLIENT REVENUE'],    
        ['<img src="./images/menu/centersys.png" />', '<b>BCM Price/Volume SERGEI</b>', 'bcm_trend_v4_config.php', null,'BCM Price/Volume SERGEI'],
        ['<img src="./images/menu/centersys.png" />', '<b>30 DAY HOLDING PERIOD REVIEW.</b>', '_30_day_violation_test.php', null,'30 DAY HOLDING PERIOD REVIEW'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>CLIENT / PROSPECT MAINTENANCE</b>', 'client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>Jessica Perno:</b> Client Tiering', '_.php?mod=client_tiering', null,'Client Tiering'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>GTA Processes</b>', 'gta_processes_container.php', null,'GTA Processes'],
        ['<img src="./images/menu/centersys.png" />', '<b>Customer vs. Streetside</b>', 'cust_vs_street_container.php', null,'Customer vs. Streetside'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>Data Entry:</b> Help Entries', 'help_entry_container.php', null,'Data Entry: Help Entries'],
        ['<img src="./images/menu/centersys.png" />', '<b>Data Entry:</b> Server Dependencies', 'tdw_dep_entry_container.php', null,'Data Entry: Server Dependencies'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', 'BCM Employee Position Report 13G', 'bcm_pos_container.php', null,'Open'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', 'Comprehensive Trade Activity', 'activity_container.php', null,'Open'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>Data Maintenance:</b> Build/Populate Memory', 'javascript:CreateWnd(\'sproc_populate_memory.php\', 404, 216, false)', null,'Open'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>Fidelity:</b> Emp Trades', 'mod_fid_emp_trades_container.php', null,'Open'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', '<b>Analyst Allocations:</b> Adjustments', 'pay_analyst_adj_mgmt_container.php', null,'Data Entry: Help Entries'],
        _cmSplit,
        ['<img src="./images/menu/reports.png" />','<b>Analyst Allocations</b>',null,null,'Analyst Allocations',
            ['<img src="./images/menu/maint.png" />', 'Analyst Payout <b>Configuration</b>', 'pay_analyst_config_mgmt_container.php', null,'Analyst Allocations'],
            ['<img src="./images/menu/dollar.png" />', 'Analyst Allocations <b>(Individual)</b>', 'pay_analyst_mgmt_container.php', null,'Analyst Allocations'],
            ['<img src="./images/menu/dollar.png" />', 'Analyst Allocations <b>(Summary)</b>', 'pay_analyst_summ_container.php', null,'Analyst Allocations'],
            ['<img src="./images/menu/dollar.png" />', 'Analyst Allocations <b>Print Report</b>', 'pay_analyst_gen_report_container.php', null,'Analyst Allocations : Print Report']
        ],
        _cmSplit,
        ['<img src="./images/menu/excel_new.png" />','Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
        ['<img src="./images/menu/excel_new.png" />','Payout Report <b>Summary</b> ','pay_xnsumm_sdate_container.php',null,'Payout Summary Report'], 
        _cmSplit,
        ['<img src="./images/menu/excel_new.png" />','New Payout Report <b>Detail</b>','pay_ndetl_sdate_container.php',null,'Payout Report'],
        ['<img src="./images/menu/excel_new.png" />','New Payout Report <b>Summary</b> ','pays_container.php',null,'Payout Summary Report'], 
        _cmSplit,
        ['<img src="./images/menu/excel_new.png" />','Sales & Trading <b>Monthly Revenue Report</b>','mrr_container.php',null,'Payout Summary Report'], 
        _cmSplit,
        ['<img src="./images/menu/excel_new.png" />','Fidelity Cost Report','fidemp_container.php',null,'Payout Summary Report'], 
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', 'TrackSys Integrated', 'tracksys_a_container.php', null,'TrackSys'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', 'Test Summary Report (Settle Date)', 'pay_summ_sdate_container.php', null,'Open'],
        ['<img src="./images/menu/centersys.png" />', 'BCM Watch List', 'main_stocklist_container.php', null,'Open'],
        ['<img src="./images/menu/centersys.png" />', 'NEW BCM Watch List', 'stocklist_entry_container.php', null,'Open'],
        ['<img src="./images/menu/centersys.png" />', 'Analyst Export Excel', 'http://192.168.20.58/tdw/pay_analyst_excel_create2.php?xl=289^029^3^2008', null,'Open'],
        ['<img src="./images/menu/centersys.png" />', 'New Client Activity', 'activity_container.php', null,'Open'],
        _cmSplit,
        ['<img src="./images/menu/centersys.png" />', 'About CenterSys Menu', 'javascript:CreateWnd(\'help.php?item=24\', 404, 216, false)', null, 'About CenterSys Menu'],
    ]
    <?php endif; ?> 
]; 
cmDraw('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
</script></td>
</tr>
</table>