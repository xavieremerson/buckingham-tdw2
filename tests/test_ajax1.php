<html>
<head>
<!--Server: 192.168.20.63-->
<!--Client: 192.168.20.129-->
<!--Administrator Email: support@centersys.com-->
<!--Page Process Time: Fri, 04/07/2006 01:06 am-->
<link rel="shortcut icon" href="../favicon.ico"></link>
<link rel="bookmark" href="../favicon.ico"></link>
<title>TDW v 1.0b</title>
<script language="JavaScript" src="../includes/menu/JSCookMenu.js" type="text/javascript"></script>
<script language="JavaScript" src="../includes/menu/js/ThemeOffice/theme.js" type="text/javascript"></script>

<script language="JavaScript">
function getFormValues() {
//alert("button clicked");
var params_val;
params_val = "sel_rep=" + document.clnt_activity.sel_rep.options[document.clnt_activity.sel_rep.options.selectedIndex].value + "&";
params_val = params_val + "sel_client=" + document.clnt_activity.sel_client.options[document.clnt_activity.sel_client.options.selectedIndex].value;
showDetail(params_val);
}
</script>

<script language="javascript">
var xmlHttp

function showDetail(str)
{ 
	//alert("here  " + str);
	var url="test_ajax_inc1.php" + "?" +  str
	xmlHttp=GetXmlHttpObject(setDataInDiv)
	xmlHttp.open("POST", url , true)
	xmlHttp.send(null)
} 

function setDataInDiv() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
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


<link rel="stylesheet" type="text/css" href="../includes/styles.css">
<link rel="stylesheet" type="text/css" href="../status_app/style.css">
</head>
<!-- <body background="images/bk1.jpg" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"> -->
<!-- <body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" onLoad="InitializeTimer()"> -->
<body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">

<!-- TOP LEVEL TABLE -->
<table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" bgcolor="#F4F8FB">
	<tr valign="top">
    <td height="20"> 

<div id="dhtmltooltip"></div>
<script language='JavaScript' src='../includes/tooltip.js'></script>
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="#" method="post">
															<tr>
																<td width="100">
																<select class="Text1" name="sel_rep" size="1" >
																		<option value="^ALL^">&nbsp;REGISTERED REPS.&nbsp;(ALL)</option>
																		<option value="^ALL^">____________</option>
																		<option value="005^197">Backus, Lee&nbsp; &nbsp; (005)</option>
																		<option value="045^217">Baker, Aaron&nbsp; &nbsp; (045)</option>
																		<option value="012^218">Brunner, Scott&nbsp; &nbsp; (012)</option>
																</select>
																</td>
																<td width="5">&nbsp;</td>
																<td width="100">
																<select class="Text1" name="sel_client" size="1" >
																		<option value="^ALL^">&nbsp;CLIENTS&nbsp;(ALL)</option>
																		<option value="^ALL^">____________</option>
																		<option value="BESS">BESSIMER TRUST          </option>
																		<option value="BLRS">BLACKROCK/BOSTON        </option>
																		<option value="BONZ">BONANZA PARTNERS        </option>
																		<option value="BRAH">BRAHMAN CAPITAL         </option>
																		<option value="BRAM">BRAMWELL CAP            </option>
																	</select>
																</td>
																<td width="5">&nbsp;</td>
																<td width="100">																
																<select class="Text1" name="sel_symbol" size="1" >
																		<option value="^ALL^">&nbsp;SYMBOLS&nbsp;(ALL)&nbsp;&nbsp;</option>
																		<option value="^ALL^">_____________</option><option value=""></option>
																		<option value="YUM">YUM</option>
																		<option value="ZGEN">ZGEN</option>
																		<option value="ZION">ZION</option>
																		<option value="ZLC">ZLC</option>
																		<option value="ZMH">ZMH</option>
																		<option value="ZQK">ZQK</option>
																		<option value="ZRAN">ZRAN</option>
																		<option value="ZUMZ">ZUMZ</option>
																</select>
																</td>
																<td width="5">&nbsp;</td>
																<td width="100">																
																<select class="Text1" name="sel_month" size="1" >
																<option value="">&nbsp;BROKERAGE MONTH&nbsp;&nbsp;</option>
																<option value="">_______________</option>
																<option value="Apr^2006">Apr 2006</option><option value="Mar^2006">Mar 2006</option><option value="Feb^2006">Feb 2006</option><option value="Jan^2006">Jan 2006</option>																</select>
																</td>

																<SCRIPT LANGUAGE="JavaScript" SRC="../includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("04/11/2006",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("04/11/2006",null);
																	
																	</SCRIPT>																
																<td width="5">&nbsp;</td>
																<td width="10">From:</td>
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" readonly size="12" maxlength="12" value="04/10/2006"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="../images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10">To:</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" readonly size="12" maxlength="12" value="04/10/2006"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="../images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10"><a href="javascript:getFormValues();"><img src="../images/lf_v1/form_submit.png"></a></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="10" align="center">&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
														</form>			
														</table>
														</td> 
													</tr>
												</table>
																	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
																	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
												<!-- END TABLE 4 -->
              </td>
						</tr>
			</table>
						<!-- END TABLE 3 -->


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
														<!--showDetail("1234567")-->
															<div id="1234567"></div>
														</td> 
													</tr>
												</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
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
