<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="images/themes/standard/menubarbkground.jpg" class="menubar">
  <tr> 
    <td class="menubackgr"> <div id="myMenuID"></div>
      <script language="JavaScript" type="text/javascript">
		var myMenu =
		[
			[null,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="red">&#9658</font> Reports',null,null,' Reports',
				//['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Trader :</b> Commissions','rep_trdr2y_container.php',null,'Trader: Commissions'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Trader :</b> Commissions','trdrs_comm_container.php',null,'Trader: Commissions'],
				_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Trader :</b> My Client Activity','rep_trdr_ca_container.php',null,'Trader: My Client Activity'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Trader :</b> All Client Activity','rep_all_trdr_team_ca_container.php',null,'Trader: All Client Activity'],
				_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Client/Prospect Master</b>','cmgmt_readonly.php',null,'Client Master'],
				_cmSplit,
				 <?
				 $db_allocation = db_single_val("select count(*) as single_val from pay_analyst_users where user_id = '".$user_id."'");
				 if ($db_allocation > 0) {
					echo "['<img src=\"./images/centersys.png\" />', '<b>Analyst Allocations</b>', 'pay_analyst_container.php', null,'Open'],";
				 }
				 ?>					 
    _cmSplit,
		  ['<img src="./includes/menu/js/ThemeOffice/transparent.png" />',' ','#',null,' '],
      ['<img src="./includes/menu/js/ThemeOffice/dollar.png" />','<b>Expense :</b> Reporting','mod_exp_expense.php',null,'Expense Items Data Entry'],
			],
			_cmSplit,
			[null,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="red">&#9658</font> Administration',null,null,'System Administration',
				['<img src="./includes/menu/js/ThemeOffice/users.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
				//['<img src="./includes/menu/js/ThemeOffice/password.png" />','Change Password','javascript:CreateWnd(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, false);',null,'Change my password'],
				['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);" />', 'Change my password', 'javascript:showPopWin(\'passwdchange.php?ID=<?=$user_id?>\', 350, 250, null);', null, 'Change my password'],
			],
			 _cmSplit,
			 [null,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="red">&#9658</font> Reference&nbsp;&nbsp;',null,null,'Reference',
				['<img src="./includes/menu/js/ThemeOffice/search.png" />','Registered Rep. List','ref_rr.php',null,'Registered Rep. List'],
			 ],
			_cmSplit,
			  [null,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="red">&#9658</font> Help',null,null,'Help',
				['<img src="./includes/menu/js/ThemeOffice/messaging_email.png" />', 'Email Technical Support', '<?=$_email_tech_support?>', null,'Email Technical Support'],
				_cmSplit,
   			['<img src="./includes/menu/js/ThemeOffice/about.png" onclick="showPopWin(\'about.php\', 400, 200, null);" />', 'About <?=$_app_name?>', 'javascript:showPopWin(\'about.php\', 400, 200, null);', null, 'About <?=$_app_name?>'],
			]
		];
		cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script> </td>
  </tr>
</table>
