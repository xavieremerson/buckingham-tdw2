<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/themes/standard/menubarbkground.jpg" class="menubar">
  <tr> 
    <td class="menubackgr"> <div id="myMenuID"></div>
      <script language="JavaScript" type="text/javascript">
		var myMenu =
		[
			/*[null,'Home','main.php',null,'Main Page'],
			_cmSplit,
			[null,'Trades',null,null,'Site Management',
				['<img src="./includes/menu/js/ThemeOffice/preview.png" />','View Trades','vtrades.php',null,'View Trades'],
			],
			_cmSplit,
			[null,'Accounts',null,null,'Accounts Menu',
				['<img src="./includes/menu/js/ThemeOffice/menus.png" />','View Accounts','acctview.php',null,'View Accounts'],
				['<img src="./includes/menu/js/ThemeOffice/excel.png" />','Export Accounts (Excel)','acctexpcsv.php',null,'Export Accounts (Excel)'],
			],
			_cmSplit,*/
			[null,'&#9658 Modules&nbsp;&nbsp;',null,null,'Modules',
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','Client Commissions','clnt_if2y_src_container.php',null,'Client Commissions'],
				//['<img src="./includes/menu/js/ThemeOffice/reports.png" />','Client Activity','clnt_all_rep_ca_container.php',null,'Client Activity'],
    		['<img src="./includes/menu/js/ThemeOffice/reports.png" />', '<b>CLIENT ACTIVITY</b>', 'anly_all_rep_ca_container.php', null,'ANALYST: CLIENT ACTIVITY'],
    _cmSplit,
		  ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Reporting','mod_exp_expense.php',null,'Expense Items Data Entry'],
			],
			_cmSplit,
			[null,'&#9658 My Prefs&nbsp;&nbsp;',null,null,'System Administration',
				['<img src="./includes/menu/js/ThemeOffice/users.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
				//['<img src="./includes/menu/js/ThemeOffice/password.png" />','Change Password','javascript:CreateWnd(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, false);',null,'Change my password'],
				['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password'],
				//['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password'],
			],
			 _cmSplit,
			 [null,'&#9658 Reference&nbsp;&nbsp;',null,null,'Reference',
				['<img src="./includes/menu/js/ThemeOffice/search.png" />','Registered Rep. List','ref_rr.php',null,'Registered Rep. List'],
			 ],
			_cmSplit,
			[null,'&#9658 Help&nbsp;&nbsp;',null,null,'Help',
				['<img src="./includes/menu/js/ThemeOffice/messaging_email.png" />', 'Email Technical Support', '<?=$_email_tech_support?>', null,'Email Technical Support'],
				_cmSplit,
   			['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'about.php\', 400, 200, null);" />', 'About <?=$_app_name?>', 'javascript:showPopWin(\'about.php\', 400, 200, null);', null, 'About <?=$_app_name?>'],
			]
		];
		cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script> </td>
  </tr>
</table>
