<script language="JavaScript" src="includes/js/popup.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language="javascript" src="includes/prototype/prototype.js"></script>
<script language ="Javascript">
<!--

function get_company_name() {

	if ($("citta_company_symbol").value.length == 0) {
		//alert("no symbol");
		return false;
	}
	var url = 'http://192.168.20.63/tdw/get_companyname.php';
	var pars = 'symbol='+ $("citta_company_symbol").value;

  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;  
          $("citta_company_name").value = response.toUpperCase();
          $("citta_company_symbol").value = $("citta_company_symbol").value.toUpperCase();
					toUpperCase()
				},     
			onFailure: 
			function(){ $("citta_company_name").value = "PROBABLE ERROR"; }
		}
	);
}

function setFocus(nextid) {
  document.getElementById(nextid).focus();
}

function bar(evt, nextid){
var k=evt.keyCode||evt.which;
 if (k==13 && nextid != "") {
   setFocus(nextid);
 }
return k!=13;
}

function getFormValues(){
	allitems = Form.serialize("cittalist");
	var params_val = allitems;
	//alert(params_val);
	//return false;
	showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>citta_entry_process.php" + "?" +  str;
	//alert(url);
  var trid;
	trid = 'if_status'; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById(trid).src=url;
			//alert(document.getElementById(trid).src)
	} 
	else { 
		if (document.layers) { // Netscape 4 
			alert("Netscape 4");
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 

	document.forms["cittalist"].reset();
	setFocus('citta_fund_name');
	//document.ext_trades.vdate.value = ""
	//document.ext_trades.vaccount.value = ""
	//document.ext_trades.vbuysell.value = "B"
	//document.ext_trades.vsymbol.value = ""
	//document.ext_trades.vquantity.value = ""
	//document.ext_trades.vprice.value = ""
	//document.getElementById('compname').innerHTML=""
		
} 
-->
</script>


<style type="text/css">
<!--
.txt_status {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #0000FF;
}
-->
</style>
<body onLoad="setFocus('citta_fund_name');"><!-- showDetail('');-->
<?
tsp(100, "CITTA List (Data Entry)");
?>
				 <form id="cittalist" name="cittalist">
					<table border="0">
						<tr>
							<td colspan="3"><?=$str_status?>
							</td>
						</tr>
          </table>
          
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																</SCRIPT>						

					<?
					if ($edcit) { //get the record to edit and populate the form.
						//process
					}
					?>



          
          
          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="100">Fund</td><td width="200" align="left">
            <select class="text" name="citta_fund_name" id="citta_fund_name" size="1" style="width:75px">
          		<option value="">Select</option>
						<? 
							$var_fundnames = array("BIP", "BP","BPII","BRIP","RAF","RAFII", "OTHER"); 
							foreach ($var_fundnames as $k=>$v) {
								echo '<option value="'.$v.'">'.$v.'</option>';
							}
						?>
            </select>
          </td>
          <td align="left" width="115">Client Code</td><td width="201" align="left"><input class="text" name="citta_client_code" id="citta_client_code" type="text" value="" size="20"/></td>
          <td align="left" width="102">Account Name</td><td width="202" align="left"><input class="text" name="citta_account_name" id="citta_account_name" type="text" value="" size="30"/></td>
          <td align="left" width="116">Date Received</td>
          <td width="10"><input type="text" id="citta_date_received" class="Text1" name="citta_date_received" size="12" maxlength="12" value="<?=date('m/d/Y')?>"></td>
          <td width="12" align="left"><A HREF="#" onClick="calfrom.select(document.forms['cittalist'].citta_date_received,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
					<td>&nbsp;</td>
          </tr></table>
          
          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="100">Info. Source</td><td width="200" align="left"><input class="text" name="citta_information_source" id="citta_information_source" type="text" value="" size="12" maxlength="12"/></td>
          <td align="left" width="115">Update Info.</td><td width="201" align="left"><select name="citta_update_information" id="citta_update_information" size="1"><option value="Y" selected="selected">Yes</option><option value="N">No</option></select></td>
          <td align="left" width="102">Is Corp. Insider</td><td width="202" align="left"><select name="citta_is_corporate_insider" id="citta_is_corporate_insider" size="1"><option value="Y" selected="selected">Yes</option><option value="N">No</option></select></td>
          </tr></table>
          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="100">Symbol</td><td width="200" align="left"><input class="text" name="citta_company_symbol" id="citta_company_symbol" type="text" value="" size="12" onBlur="get_company_name()"/></td>
          <td align="left" width="115">Company Name</td><td width="201" align="left"><input class="text" name="citta_company_name" id="citta_company_name" type="text" value="" size="30"/></td>
          <td align="left" width="102">Person</td><td width="202" align="left"><input class="text" name="citta_company_person" id="citta_company_person" type="text" value="" size="30"/></td>
          <td align="left" width="116">Title</td><td align="left"><input class="text" name="citta_company_person_title" id="citta_company_person_title" type="text" value="" size="30"/></td>
          </tr></table>
          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="100">B/D Affiliate</td><td width="200" align="left"><select name="citta_broker_dealer_affiliate" id="citta_broker_dealer_affiliate" size="1"><option value="Y" selected="selected">Yes</option><option value="N">No</option></select></td>
          <td align="left" width="115">Affiliate Name</td><td width="201" align="left"><input class="text" name="citta_affiliate_name" id="citta_affiliate_name" type="text" value="" size="30"/></td>
          <td align="left" width="102">Insider Name</td><td width="202" align="left"><input class="text" name="citta_insider_name" id="citta_insider_name" type="text" value="" size="30"/></td>
          <td align="left" width="116">Insider Title</td><td align="left"><input class="text" name="citta_insider_title" id="citta_insider_title" type="text" value="" size="30"/></td>
          </tr></table>
          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="100">Is Fin. Service</td><td width="200" align="left"><select name="citta_is_financial_services" id="citta_is_financial_services" size="1"><option value="Y" selected="selected">Yes</option><option value="N">No</option></select></td>
          <td align="left" width="115">Company Name</td><td width="201" align="left"><input class="text" name="citta_finserv_company_name" id="citta_finserv_company_name" type="text" value="" size="30"/></td>
          <td align="left" width="102">Person</td><td width="202" align="left"><input class="text" name="citta_finserv_company_person" id="citta_finserv_company_person" type="text" value="" size="30"/></td>
          <td align="left" width="116">Fin. Service Type</td><td align="left"><input class="text" name="citta_finserv_type" id="citta_finserv_type" type="text" value="" size="30"/></td>
          </tr></table>
          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="100">Entity Type</td><td width="200" align="left"><input class="text" name="citta_finserv_entity_type" id="citta_finserv_entity_type" type="text" value="" size="30"/></td>
          <td align="left" width="115">Investment Type</td><td width="201" align="left"><input class="text" name="citta_finserv_investment_type" id="citta_finserv_investment_type" type="text" value="" size="30"/></td>
          <td align="left" width="102">Effective Date</td>
          <td width="10"><input type="text" id="citta_active_since" class="Text1" name="citta_active_since" size="12" maxlength="12" value="<?=date('m/d/Y')?>"></td>
					<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['cittalist'].citta_active_since,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
					<td>&nbsp;</td>
          </tr></table>
          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="100">Comments</td><td align="left"><input class="text" name="citta_comments" id="citta_comments" type="text" value="" size="160"/></td>
          </tr></table>
					<table width="100%">
            <tr>
              <td class="ilt" colspan="3" align="left">Entered By: <?=$userfullname?></td>
            </tr>
						<tr>
							<td colspan="2">
              <input name="Submit" id="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;<input type="reset" value="CLEAR FORM">
							</td>
              <td align="right" valign="bottom"><img src="images/lf_v1/exp2excel.png" border="0">&nbsp;&nbsp;&nbsp;<a href="citta_entry_export_excel.php?uid=<?=$user_id?>&mode=all" target="_blank" class="ilt">[ALL]</a>&nbsp;&nbsp;&nbsp;<a href="citta_entry_export_excel.php?uid=<?=$user_id?>&mode=active" target="_blank" class="ilt">[ACTIVE]</a></td>
						 </tr>  
					</table>  
					<input type="hidden" name="venteredby" value="<?=$user_id?>">
					<input type="hidden" name="uid" value="<?=$user_id?>">
					</form>
<?
tep();
?>
<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" id="if_status" src="citta_entry_process.php?uid=<?=$user_id?>" height="400" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
</body>