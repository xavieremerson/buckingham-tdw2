<script language="JavaScript" src="includes/js/popup.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--
function get_companyname_from_symbol(symbol) {
	AjaxRequest.get(
		{
			'url':'get_companyname.php?symbol='+ symbol
			,'onSuccess':function(req){ document.getElementById('compname').innerHTML=req.responseText; }
			,'onError':function(req){ document.getElementById('compname').innerHTML='Retrieve error from Yahoo Finance';}
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
params_val = "vdate=" + document.ext_trades.vdate.value + "&";
params_val = params_val + "vaccount=" + document.ext_trades.vaccount.value + "&";
params_val = params_val + "vbuysell=" + document.ext_trades.vbuysell.value + "&";
params_val = params_val + "vsymbol=" + document.ext_trades.vsymbol.value + "&";
params_val = params_val + "vquantity=" + document.ext_trades.vquantity.value + "&";
params_val = params_val + "vprice=" + document.ext_trades.vprice.value + "&";
params_val = params_val + "venteredby=" + document.ext_trades.venteredby.value;
showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>ext_trades_entry_process.php" + "?" +  str;
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

	setFocus('vaccount');
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
<body onLoad="setFocus('vaccount'); showDetail('');">
<?
tsp(100, "External Employee Trades Entry");
?>
&nbsp;&nbsp;
<form name="ext_trades" id="ext_trades">
<table width="800" height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Employee Account</td>
    <td>
				<select class="Text" name="vaccount" size="1" onKeyPress="return bar(event, 'vbuysell')">
				<option value="">Select Account</option>
				<?
				$qry_accts = "select a.*, b.Fullname 
											 from oac_emp_accounts a, users b
											 WHERE a.oac_emp_userid = b.ID
											 ORDER BY b.Fullname";
				$result_accts = mysql_query($qry_accts) or die (tdw_mysql_error($qry_accts));
				while ( $row_accts = mysql_fetch_array($result_accts) ) 
				{
				?>
				<option value="<?=$row_accts["auto_id"]?>"><?=$row_accts["Fullname"]." : ".$row_accts["oac_account_number"]. " (".trim($row_accts["oac_custodian"]).")"?></option>
				<?
				}
				?>
				</select> 
				&nbsp;&nbsp;&nbsp;(<a href="ext_accts_entry_container.php" onFocus="return bar(event, 'vbuysell')">Add New Account</a>)
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Buy/Sell</td>
    <td>				
				<select class="Text" name="vbuysell" size="1" onKeyPress="return bar(event, 'vsymbol')">
				<option value="B">Buy</option>
				<option value="CS">Cover</option>
				<option value="S">Sell</option>
				<option value="SS">Short</option>
				</select>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Symbol</td>
    <td><input class="Text" name="vsymbol" type="text" size="12" maxlength="12" onKeyPress="return bar(event, 'vquantity')" onChange="get_companyname_from_symbol(document.ext_trades.vsymbol.value);"><div id="compname" class="ilt"></div></td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Quantity</td>
    <td><input class="Text" name="vquantity" type="text" size="12" maxlength="12" onKeyPress="return bar(event, 'vprice')"></td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Price</td>
    <td><input class="Text" name="vprice" type="text" size="12" maxlength="12" onKeyPress="return bar(event, 'vdate')"></td>
  </tr>
  <tr>
    <td class="ilt" width="150">&nbsp;&nbsp;&nbsp;Trade Date</td>
    <td width="650">
		
		<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
		<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
		<SCRIPT LANGUAGE="JavaScript">
		var calvdate = new CalendarPopup("divvdate");
		</SCRIPT>	
		<input type="text" id="vdate" class="Text1" name="vdate" size="12" maxlength="12" value="<?=date('m/d/Y')?>"  onKeyPress="return bar(event, 'Submit')">															
		<A HREF="#" onClick="calvdate.select(document.forms['ext_trades'].vdate,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A><font color="#FF0000">*</font>
  	</TD>
	</tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Entered By:</td>
    <td class="ilt"><?=$userfullname?></td>
  </tr>
  <tr>
    <td></td>
    <td><input name="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;<input type="reset" value="CLEAR FORM"></td>
  </tr>
</table>
<input type="hidden" name="venteredby" value="<?=$user_id?>">
</form>
<?
tep();
?>
<DIV ID="divvdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" src="ext_trades_entry_process.php" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
</body>

