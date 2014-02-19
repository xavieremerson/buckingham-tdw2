<script language="JavaScript">
function getFormValues(rr_num, user_id) {
//alert("button clicked");
var params_val;
params_val = "sel_client=" + document.clnt_activity.sel_client.options[document.clnt_activity.sel_client.options.selectedIndex].value + "&";
params_val = params_val + "sel_symbol=" + document.clnt_activity.sel_symbol.options[document.clnt_activity.sel_symbol.options.selectedIndex].value + "&";
params_val = params_val + "sel_month=" + document.clnt_activity.sel_month.options[document.clnt_activity.sel_month.options.selectedIndex].value + "&";
params_val = params_val + "datefrom=" + document.clnt_activity.datefrom.value + "&";
params_val = params_val + "dateto=" + document.clnt_activity.dateto.value + "&";
params_val = params_val + "user_id=" + user_id + "&";
params_val = params_val + "rep_to_process=" + rr_num;
//alert(params_val);
showDetail(params_val);
}
</script>

<script language="javascript">
var xmlHttp

function showDetail(str)
{ 
	document.getElementById('ca_trades').src="rep_ca_aj_inc_trade.php" + "?" +  str 
	/*
	//alert("here  " + str);
	var url="rep_ca_aj_inc_trade.php" + "?" +  str
	xmlHttp=GetXmlHttpObject(setDataInDiv)
	xmlHttp.open("GET", url , true)
	xmlHttp.send(null)
	*/
} 

function setDataInDiv(str) 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
			//alert("ready state achieved.");
			//document.getElementById("ca_trades").innerHTML=xmlHttp.responseText 
			//document.getElementById("ca_trades").innerHTML=xmlHttp.responseText 
			document.getElementById('ca_trades').src="rep_ca_aj_inc_trade.php" + "?" +  str 
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



<?
if ($datefilterval) {
//xdebug('datefilterval',$datefilterval);
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
//xdebug('trade_date_to_process',$trade_date_to_process);
} else {
$trade_date_to_process = previous_business_day();
//xdebug('trade_date_to_process',$trade_date_to_process);
}
//$rep_to_process = '028';
$rep_to_process = $rr_num;
?>
<table width="100%" cellpadding="1" cellspacing="1">
		<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr> 
										<td height="20" valign="middle" background="images/tables3/header_bk.jpg">
										&nbsp;&nbsp;<a class="table_heading_text">Sales Rep : CLIENT ACTIVITY</a>
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
														<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="#" method="post">
															<tr>
																<td width="10">&nbsp;</td>
																<td width="100">
																<select class="Text1" name="sel_client" size="1" >
																<option value="^ALL^">&nbsp;CLIENTS&nbsp;(ALL)</option>
																<option value="^ALL^">____________</option>
																<?
																$query_sel_client = "SELECT comm_advisor_code, max( comm_advisor_name ) as comm_advisor_name 
																											FROM rep_comm_rr_level_a
																											WHERE comm_rr = '".$rep_to_process."'
																											GROUP BY comm_advisor_code
																											ORDER BY comm_advisor_name";
																$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
																while($row_sel_client = mysql_fetch_array($result_sel_client))
																{
																	if ($row_sel_client["comm_advisor_name"] == '') {
																	$display_val_client = $row_sel_client["comm_advisor_code"];
																	} else {
																	$display_val_client = $row_sel_client["comm_advisor_name"];
																	}
																?>
																
																<option value="<?=$row_sel_client["comm_advisor_code"]?>"><?=$display_val_client?>&nbsp;&nbsp;</option>
																<?
																}
																?>
																</select>
																</td>
																<td width="10">&nbsp;</td>
																<td width="100">																
																<select class="Text1" name="sel_symbol" size="1" >
																<option value="^ALL^">&nbsp;SYMBOLS&nbsp;(ALL)&nbsp;&nbsp;</option>
																<option value="^ALL^">_____________</option>
																<?
																$query_sel_symbol = "SELECT DISTINCT(trad_symbol)
																											FROM rep_comm_rr_trades 
																											WHERE trad_rr = '".$rep_to_process."'
																											ORDER BY trad_symbol";
																$result_sel_symbol = mysql_query($query_sel_symbol) or die(mysql_error());
																while($row_sel_symbol = mysql_fetch_array($result_sel_symbol))
																{
																?>
																<option value="<?=$row_sel_symbol["trad_symbol"]?>"><?=$row_sel_symbol["trad_symbol"]?></option>
																<?
																}
																?>
																</select>
																</td>
																<td width="5">&nbsp;</td>
																<td width="100">																
																<select class="Text1" name="sel_month" size="1" >
																<option value="">&nbsp;BROKERAGE MONTH&nbsp;&nbsp;</option>
																<option value="">_______________</option>
																<?
																echo create_commission_month();
																?>
																</select>
																</td>
																<td width="10">&nbsp;</td>
																
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	
																	</SCRIPT>																
																<td width="10">From:</td>
																<td width="10">&nbsp;</td>
																
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy(previous_business_day())?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10">&nbsp;</td>
																<td width="10">To:</td>
																<td width="10">&nbsp;</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy(previous_business_day())?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10">&nbsp;</td>
																<td width="10"><a href="javascript:getFormValues('<?=$rr_num?>','<?=$user_id?>');"><img src="images/lf_v1/form_submit.png"></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="50" align="center">&nbsp;</td>
																<td width="100"><a href="rep_src_container.php"><img src="images/lf_v1/clnt_commissions.png" border="0"></a></td>
																<td>&nbsp;</td>
														</tr>
														</form>															
														</table>
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
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td height="100%">
					<!--include('rep_ca_aj_inc_trade.php');-->
					<!--<div id="ca_trades"></div>-->
					<iframe src="" name="ca_trades" width="100%" marginwidth="0" marginheight="0" scrolling="no" frameborder="0"></iframe>
				</td>
			</tr>

		</table>
		</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
				