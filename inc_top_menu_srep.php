<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/themes/standard/menubarbkground.jpg" class="menubar">
  <tr> 
    <td class="menubackgr"> <div id="myMenuID"></div>
      <script language="JavaScript" type="text/javascript">
		var myMenu =
		[
			[null,'&nbsp;&nbsp;&nbsp;&nbsp; &#9658 <b>Main Menu</b>',null,null,' Reports',
				//comm_src_container.php
				//['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Sales Rep :</b> Commissions','rep_if2y_src_container.php',null,'Sales Rep. Commissions'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Sales Rep :</b> Commissions','comm_src_container.php',null,'Sales Rep. Commissions'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Sales Rep :</b> Client Activity','rep_ca_container.php',null,'Sales Rep. Client Activity'],
   			   <?
					 $db_allocation = db_single_val("select count(*) as single_val from pay_analyst_users where user_id = '".$user_id."'");
					 if ($db_allocation > 0) {
					  echo "['<img src=\"./images/centersys.png\" />', '<b>Analyst Allocations</b>', 'pay_analyst_container.php', null,'Open'],";
					 }
					 ?>					 
   _cmSplit,
		['<img src="./images/centersys.png" />', '<b>CLIENT REVENUE</b>', 'client_revenue__v2_srep.php', null,'CLIENT REVENUE'],    
		['<img src="./images/centersys.png" />', 'SALES REVENUE SUMMARY', 'sales_rev_rep_summ.php', null,'SALES REVENUE SUMMARY'],    
   _cmSplit,
    	['<img src="./includes/menu/js/ThemeOffice/reports.png" />', '<b>Client Tiering:</b>', '_.php?mod=client_tiering&mode=r', null,'Client Tiering'],
    _cmSplit,
      ['<img src="./includes/menu/js/ThemeOffice/reports.png" />', '<b>CLIENT / PROSPECT LIST</b>', 'rep_client_master.php', null,'CLIENT / PROSPECT MAINTENANCE'],
    _cmSplit,
		  ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Reporting','mod_exp_expense.php',null,'Expense Items Data Entry'],
	],
			_cmSplit,
			[null,'&nbsp;&nbsp;&nbsp;&nbsp; &#9658 Administration',null,null,'System Administration',
				['<img src="./includes/menu/js/ThemeOffice/users.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
				//['<img src="./includes/menu/js/ThemeOffice/password.png" />','Change Password','javascript:CreateWnd(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, false);',null,'Change my password'],
				['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password'],
			],
			 _cmSplit,
			 [null,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#9658 Reference&nbsp;&nbsp;',null,null,'Reference',
				['<img src="./includes/menu/js/ThemeOffice/search.png" />','Registered Rep. List','ref_rr.php',null,'Registered Rep. List'],
			 ],
			_cmSplit,
			  [null,'&nbsp;&nbsp;&nbsp;&nbsp; &#9658 Help',null,null,'Help',
				['<img src="./includes/menu/js/ThemeOffice/messaging_email.png" />', 'Email Technical Support', '<?=$_email_tech_support?>', null,'Email Technical Support'],
   			['<img src="./images/centersys.png" />', 'TrackSys (Tracking System)', 'tracksys_a_container.php', null,'TrackSys'],
				_cmSplit,
   			['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'about.php\', 400, 200, null);" />', 'About <?=$_app_name?>', 'javascript:showPopWin(\'about.php\', 400, 200, null);', null, 'About <?=$_app_name?>'],
			]
		];
		cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script> </td>
  </tr>
</table>
