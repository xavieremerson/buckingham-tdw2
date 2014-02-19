<html>
<head>
<!--Server: 192.168.20.63-->
<!--Client: 192.168.20.129-->
<!--Administrator Email: support@centersys.com-->
<!--Page Process Time: Fri, 04/07/2006 01:06 am-->
<link rel="shortcut icon" href="../favicon.ico"></link>
<link rel="bookmark" href="../favicon.ico"></link>
<title>TDW v 1.0b</title>
<script language="JavaScript" src="../includes/highlight_tables/tigra_tables.js"></script>
<script language='JavaScript' src='../includes/js/javascript.js'></script>
<script language='JavaScript' src='../includes/js/timer.js'></script>
<script language='JavaScript' src='../includes/js/ajax.js'></script>
<script language="JavaScript" src="../includes/js/popup.js"></script>
<script language="JavaScript" src="../includes/js/tbl_sort.js"></script>
<script language="JavaScript" src="../includes/js/tblsort.js"></script>

<script language="JavaScript" src="../includes/menu/JSCookMenu.js" type="text/javascript"></script>
<script language="JavaScript" src="../includes/menu/js/ThemeOffice/theme.js" type="text/javascript"></script>

<script language="javascript">
var xmlHttp

function showDetail(str)
{ 
	//alert("here  " + str);
	var url="test_ajax_inc.php"
	xmlHttp=GetXmlHttpObject(setDataInDiv)
	xmlHttp.open("GET", url , true)
	xmlHttp.send(null)
} 

function setDataInDiv() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
			//alert("ready state achieved.");
			document.getElementById("1234567").innerHTML=xmlHttp.responseText 
		} 
} 

function GetXmlHttpObject(handler)
{ 
	var objXmlHttp=null

		if (navigator.userAgent.indexOf("Opera")>=0)
			{
				alert("This example doesn't work in Opera") 
				return 
			}

		if (navigator.userAgent.indexOf("MSIE")>=0)
			{ 
				var strName="Msxml2.XMLHTTP"
				if (navigator.appVersion.indexOf("MSIE 5.5")>=0)
					{
						strName="Microsoft.XMLHTTP"
					} 
				try
					{ 
						objXmlHttp=new ActiveXObject(strName)
						objXmlHttp.onreadystatechange=handler 
						return objXmlHttp
					} 
				catch(e)
					{ 
						alert("Error. Scripting for ActiveX might be disabled") 
						return 
					} 
			} 

		if (navigator.userAgent.indexOf("Mozilla")>=0)
			{
				objXmlHttp=new XMLHttpRequest()
				objXmlHttp.onload=handler
				objXmlHttp.onerror=handler 
				return objXmlHttp
			}
}
</script>

<link rel="stylesheet" href="../includes/menu/css/template_css.css" type="text/css" />
<link rel="stylesheet" href="../includes/menu/css/theme.css" type="text/css" />

<!-- TEMP PPRA -->
<link href="../includes/mainpage.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
<!--
function removeClassName (elem, className) {
	elem.className = elem.className.replace(className, "").trim();
}

function addCSSClass (elem, className) {
	removeClassName (elem, className);
	elem.className = (elem.className + " " + className).trim();
}

String.prototype.trim = function() {
	return this.replace( /^\s+|\s+$/, "" );
}

function stripedTable() {
	if (document.getElementById && document.getElementsByTagName) {  
		var allTables = document.getElementsByTagName('table');
		if (!allTables) { return; }

		for (var i = 0; i < allTables.length; i++) {
			if (allTables[i].className.match(/[\w\s ]*scrollTable[\w\s ]*/)) {
				var trs = allTables[i].getElementsByTagName("tr");
				for (var j = 0; j < trs.length; j++) {
					removeClassName(trs[j], 'alternateRow');
					addCSSClass(trs[j], 'normalRow');
				}
				for (var k = 0; k < trs.length; k += 2) {
					removeClassName(trs[k], 'normalRow');
					addCSSClass(trs[k], 'alternateRow');
				}
			}
		}
	}
}

window.onload = function() { stripedTable(); }
-->
</script>

<!-- TEMP PPRA -->

<link rel="stylesheet" type="text/css" href="../includes/styles.css">
<link rel="stylesheet" type="text/css" href="../status_app/style.css">
</head>
<!-- <body background="images/bk1.jpg" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"> -->
<!-- <body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" onLoad="InitializeTimer()"> -->
<body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">

<!-- TOP LEVEL TABLE -->
<!-- <table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" background="images/bk1.jpg"> -->
<table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" bgcolor="#F4F8FB">
	<tr valign="top">
    <td height="20"> 

<div id="dhtmltooltip"></div>
<script language='JavaScript' src='../includes/tooltip.js'></script>
<!-- background="images/table_bkground_grey.jpg" -->
<!-- <table width="100%"  border="0" cellpadding="0" cellspacing="0" background="images/bk2.jpg" > -->
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="FFFFFF">
  <tr> 
    <!-- <td width="50" height="50"><img src="images/compliancelogo_v1.gif" ></td> -->
	<td width="91"><img src="../images/logo.gif" ></td>
    <td align="right" valign="top"> 
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> 
						<table width="100%"  border="0" cellspacing="1" cellpadding="1">
              <tr> 
								<td align="left" valign="top"><img src="../images/client_app.jpg" border="0"></td>
              </tr>
            </table>
					</td>
        </tr>
			</table>
		</td>
		<td valign="top">
			<table width="100%" height="57">
				<tr>
					<td align="right" valign="top"><a class="links10top">User: zPravin Prasad <!--[Login expires on Jul 28th, 2006 at 5:00 PM]--> </a> (<a href="../logout.php?logoutval=zPravin Prasad" class="links10top">Logout</a>) </td>
				</tr>
				<tr>
					<td align="right" nowrap><a class="ghm">This application is still in beta and as such please alert IT with  any problems encountered.</a></td>
				</tr>
      </table>
		</td>
  </tr>
</table>
<!-- Menu bar. -->
<div id="wrapper">
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="../images/themes/standard/menubarbkground.jpg" class="menubar">
  <tr> 
    <td class="menubackgr"> <div id="myMenuID"></div>
      <script language="JavaScript" type="text/javascript">
		var myMenu =
		[
			[null,'Home','main.php',null,'Main Page'],
			_cmSplit,
			[null,'Trades',null,null,'Site Management',
				['<img src="./includes/menu/js/ThemeOffice/preview.png" />','View Trades','vtrades.php',null,'View Trades'],
			],
			_cmSplit,
			[null,'Accounts',null,null,'Accounts Menu',
				['<img src="./includes/menu/js/ThemeOffice/menus.png" />','View Accounts','acctview.php',null,'View Accounts'],
				['<img src="./includes/menu/js/ThemeOffice/excel.png" />','Export Accounts (Excel)','acctexpcsv.php',null,'Export Accounts (Excel)'],
			],
			_cmSplit,
			[null,'Reports',null,null,'Reports',
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Mgmt :</b> Business Summary','wip.php',null,'Client Activity'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Mgmt : Sales Rep : </b>Commissions','rep_msrc_container.php',null,'Client Activity'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Mgmt :</b> Client Activity','rep_all_rep_ca_container.php',null,'Client Activity'],
				_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Sales Rep :</b> Commissions','rep_src_container.php',null,'Sales Rep. Commissions'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Sales Rep :</b> Client Activity','rep_ca_container.php',null,'Sales Rep. Client Activity'],
				_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Operations :</b> Reconcile Commissions','reconcile_comm_container.php',null,'Reconcile Commissions'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Operations :</b> Client Activity','rep_all_rep_ca_container.php',null,'Client Activity'],
				['<img src="./includes/menu/js/ThemeOffice/reports.png" />','<b>Operations :</b> Adjustments','rep_adj_all_rep_ca_container.php',null,'Client Activity'],
				_cmSplit,
			],
			_cmSplit,
			[null,'Administration',null,null,'System Administration',
				['<img src="./includes/menu/js/ThemeOffice/users.png" />','My Profile','myprofile.php',null,'View/Update My Profile'],
				['<img src="./includes/menu/js/ThemeOffice/users.png" />','Users',null,null,'Manage Users',
					['<img src="./includes/menu/js/ThemeOffice/users.png" />','Manage Users','umgmt.php?type=manage',null,'Manage Users'],
					['<img src="./includes/menu/js/ThemeOffice/users.png" />','Add User','umgmt.php?type=create',null,'Manage Users'],
				],
				['<img src="./includes/menu/js/ThemeOffice/install.png" />','Change my password','javascript:CreateWnd(\'passwdchange.php?ID=79\', 350, 250, false);',null,'Change my password'],
				_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/install.png" />', 'RR Maintenance','maint_srep.php',null,'RR Maintenance'],
			],
			_cmSplit,
			_cmSplit,
			  [null,'Help',null,null,'Help',
				['<img src="./includes/menu/js/ThemeOffice/messaging_email.png" />', 'Email Technical Support', 'mailto:support@centersysgroup.com?Subject=Technical Support Request (The Buckingham Research Group, Inc. : TDW v 1.0b)&Body=Problem Description:%0D--------------------%0D%0D%0D%0DSeverity:%0D---------%0D%0D%0D%0DMy Contact Information:%0D-----------------------', null,'Email Technical Support'],
				_cmSplit,
				['<img src="./includes/menu/js/ThemeOffice/about.png" />', 'About TDW', 'about_ly.php', null, 'About TDW'],
			]
		];
		cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
		</script> </td>
  </tr>
</table>


<!-- End Menu bar -->
<!--<br><br>-->

	</td>
</tr>
<tr valign="top">
	<td valign="top">
	
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>	
	<td valign="top">
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">
				<table width="100%" cellpadding="1" cellspacing="1">
		<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr> 
										<td height="20" valign="middle" background="../images/tables3/header_bk.jpg">
										&nbsp;&nbsp;<a class="table_heading_text">Sales Rep : COMMISSIONS : As of 04/05/2006</a>
										</td>
									</tr>
									<tr> 
										<td valign="middle">			
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														
														</td> 
													</tr>
												</table>
								                    
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr bgcolor="#333333"> 
                            <td width="280"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
                            <td width="60"><a class="tblhead_a">RR #</a> &nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">04/05/2006 ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">MTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY MTD ($)</a></td>
                            <td width="100" align="right"><a class="tblhead_a">QTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY QTD ($)</a></td>
                            <td width="100" align="right"><a class="tblhead_a">YTD ($)</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right"><a class="tblhead_a">LY YTD ($)</a></td>
                            <td>&nbsp;</td>
                          </tr>
													<tr class="trdark" onDblClick="javascript:showhidedetail(1)"> 
														<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
														<a href='javascript:showDetail("1234567")'><img id="img1" src="../images/lf_v1/expand.png" border="0"></a> 
														AMARANTH                </td>
														<td>&nbsp;&nbsp;&nbsp;&nbsp;040</td>
														<td align="right"><a class="display_zero">0.00</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
														<td align="right"><a class="display_zero">0.00</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
														<td>&nbsp;</td>
														<td align="right"><a class="display_zero">0.00</a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
														<td>&nbsp;</td>
														<td align="right">37,585.00&nbsp;&nbsp;&nbsp;&nbsp;</td>
														<td>&nbsp;</td>
														<td align="right">&nbsp;</td>
													</tr>
													<tr><td colspan="10">
															<div id="1234567"></div>
													</td></tr>
												</table>
												<table>
												   <tr class="display_totals"> 
                            <td width="280"><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
                            <td width="60"><div align="left">&nbsp;&nbsp;</div></td>
                            <td width="100" align="right">21,129.40&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100" align="right">49,959.90&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right">49,959.90&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100" align="right">759,394.64&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>

												</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
				</td>
			</tr>
			<tr id="shrd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
					</td>
			</tr>
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<br>
							
									</td>
			</tr>
		</table>
		</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
	</td>
</tr>
<tr valign="bottom">
<td>
	<table width="100%" height="20">
		<tr valign="top">
		<td align="center" valign="bottom">
			<table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">
				<tr valign="top"> 
				<td align="center" valign="bottom">
						<hr align="center" size="1" color="#CCCCCC" noshade>
						<center><a class="centersys" href="http://www.centersysgroup.com" target="_blank">CenterSys Group, Inc.</a>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="centersys">
						 2.9997954 s.						</center></a>

				</td>
				</tr>
			</table>
		</td>
		</tr>
	</table>
</td>
</tr>
</table>
</body>
</html>
