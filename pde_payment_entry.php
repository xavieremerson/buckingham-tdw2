<script language="JavaScript" src="includes/js/popup.js"></script>
<script language ="Javascript">
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
params_val = "vdate=" + document.de_comm.vdate.value + "&";
params_val = params_val + "vamount=" + document.de_comm.vamount.value + "&";
params_val = params_val + "vpaymenttype=" + document.de_comm.vpaymenttype.value + "&";
params_val = params_val + "vclient=" + document.de_comm.vclient.value + "&";
params_val = params_val + "vcomment=" + document.de_comm.vcomment.value + "&";
params_val = params_val + "venteredby=" + document.de_comm.venteredby.value;

	if (document.de_comm.vamount.value == '' || document.de_comm.vclient.value == '') {
		alert("Amount and Client are required fields. Please select/enter appropriate values and then proceed!");
		return false;
	} else {
		showDetail(params_val);	
	}
//showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>pde_payment_entry_process.php" + "?" +  str;
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

	setFocus('vpaymenttype');
	//document.de_comm.vdate.value = ""
	document.de_comm.vamount.value = ""
	document.de_comm.vclient.value = ""
	document.de_comm.vcomment.value = ""
	document.de_comm.vpaymenttype.value = "0"
} 
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
<body onLoad="setFocus('vpaymenttype'); showDetail('');">
<?
tsp(100, "Payments Data Entry");
?>
&nbsp;&nbsp;
<form name="de_comm" id="de_comm">
<table width="500" height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Type</td>
    <td><select name="vpaymenttype" size="1" onKeyPress="return bar(event, 'vclient')">
				  <option value="0">Select Payment Type</option>
				  <option value="1">Research - Research</option>
					<option value="2">Research - Independent</option>
					<option value="3">Research - Geneva</option>
					<option value="4">Broker-to-Broker</option>
					<option value="5">Trading 2</option>
					<option value="6">Other</option>
				</select>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Advisor/Client</td>
    <td>
				<select class="Text" name="vclient" size="1" onKeyPress="return bar(event, 'vdate')">
				<option value="">Select Client</option>
				<?
				$qry_accts = "select trim(clnt_code) as clnt_code, trim(clnt_name) as clnt_name, concat(clnt_rr1,' ', clnt_rr2) as reps
								 from int_clnt_clients
								 WHERE clnt_isactive = 1
								 ORDER BY clnt_name";
				//xdebug("qry_accts",$qry_accts);
				$result_accts = mysql_query($qry_accts) or die (tdw_mysql_error($qry_accts));
				while ( $row_accts = mysql_fetch_array($result_accts) ) 
				{
				if (strlen($row_accts["reps"]) == 4) {
					$str_rep = " (" . substr($row_accts["reps"],0,2).")";
				} elseif (strlen($row_accts["reps"]) == 1) {
					$str_rep = "";
				} else {
					$str_rep = " (" . $row_accts["reps"].")";				
				}
				?>
				<option value="<?=$row_accts["clnt_code"]?>"><?=$row_accts["clnt_name"]. $str_rep?></option>
				<?
				}
				?>
				</select> 
		<!--<input class="Text" name="vclient" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vcomment')"> -->
		</td>
  </tr>
  <tr>
    <td class="ilt" width="100">&nbsp;&nbsp;&nbsp;Date</td>
    <td width="400">
		
		<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
		<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
		<SCRIPT LANGUAGE="JavaScript">
		var calvdate = new CalendarPopup("divvdate");
		</SCRIPT>	
		<input type="text" id="vdate" class="Text1" name="vdate" size="14" readonly maxlength="12" value="<?=date('m/d/Y')?>" onKeyPress="return bar(event, 'vamount')">															
		<A HREF="#" onClick="calvdate.select(document.forms['de_comm'].vdate,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
		&nbsp;&nbsp;<?=showhelp(3)?><!--<input class="Text" name="vdate" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vamount')"> mm/dd/yyyy -->
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Amount</td>
    <td><input class="Text" name="vamount" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vcomment')"></td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Comment</td>
    <td><input class="Text" name="vcomment" type="text" size="30" maxlength="100" onKeyPress="return bar(event, 'Submit')"></td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Entered By:</td>
    <td class="ilt"><?=$userfullname?></td>
  </tr>
  <tr>
    <td></td>
    <td><input name="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;"></td>
  </tr>
</table>
<input type="hidden" name="venteredby" value="<?=$user_id?>">
</form>
<?
tep();
?>

<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" src="pde_payment_entry_process.php" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
<DIV ID="divvdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</body>

