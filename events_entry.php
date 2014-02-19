
<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language="JavaScript" src="includes/js/popup.js"></script>
<script language ="Javascript">

//temporary arrangement
//Evelyn logs in as two users and then user_id gets reset to 1.

function get_company_name() {

	if ($("vsymbol").value.length == 0) {
		//alert("no symbol");
		return false;
	}
	var url = 'http://192.168.20.63/tdw/events_entry_ajax.php';
	var pars = 'mod_request=cname';
  pars = pars + '&symbol='+ $("vsymbol").value;

  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;       
          $("get_name").innerHTML = response;
				},     
			onFailure: 
			function(){ $("get_name").innerHTML = "Error accessing Company Data using Symbol"; }
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
params_val = "vdate=" + document.de_news.vdate.value + "&";
params_val = params_val + "vtype=" + document.de_news.vtype.value + "&";
params_val = params_val + "vsymbol=" + document.de_news.vsymbol.value + "&";
params_val = params_val + "vnote=" + document.de_news.vnote.value.replace(/&/gi, "%26") + "&";
params_val = params_val + "venteredby=" + document.de_news.venteredby.value;

	if (document.de_news.vsymbol.value == '' ) { /*|| document.de_news.vclient.value == ''*/
		alert("Symbol is a required fields. Please enter appropriate value and then proceed!");
		return false;
	} else {
		if (document.de_news.venteredby.value == 1) {
			alert("ERROR: TWO DIFFERENT USERS CONCURRENTLY LOGGED IN TO TDW/ETPA ON THIS M/C.\n\nPlease logout and login again to continue entering news items.");
			return false;
	  }
		
		showDetail(params_val);	
	}
//showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>events_entry_process.php" + "?" +  str;
	//alert(url);
  var trid;
	trid = 'if_status'; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById(trid).src=url;
			$("get_name").innerHTML = "";
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

	setFocus('vtype');
	//document.de_news.vdate.value = ""
	document.de_news.vsymbol.value = ""
	document.de_news.vnote.value = ""
} 
</script>


<style type="text/css">
<!--
.txt_status {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #0000FF;
}
.textarea_note {
	font-family: Verdana;
	font-size: 12px;
	color: #0000FF;
}
-->
</style>
<body onLoad="setFocus('vtype'); showDetail('');">
<?
tsp(100, "News/Events Data Entry");
?>
&nbsp;&nbsp;
<form name="de_news" id="de_news">
<table width="500" height="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Type</td>
    <td><select name="vtype" size="1" onKeyPress="return bar(event, 'vsymbol')">
				  <option value="News">News</option>
				  <option value="Earnings">Earnings</option>
					<option value="Meeting">Meeting</option>
					<option value="Other">Other</option>
				</select>
     </td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Symbol</td>
    <td><input class="Text" name="vsymbol" id="vsymbol" type="text" size="14" maxlength="30" onKeyPress="return bar(event, 'vdate')"  onchange="get_company_name();">&nbsp; &nbsp; &nbsp;<a id="get_name"></a>
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
		<input type="text" id="vdate" class="Text1" name="vdate" size="14" maxlength="12" value="<?=date('m/d/Y')?>" onKeyPress="return bar(event, 'vnote')">															
		<A HREF="#" onClick="calvdate.select(document.forms['de_news'].vdate,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Note</td>
    <td><textarea class="textarea_note" rows="6" cols="100" name="vnote"></textarea></td>
  </tr>
  <tr>
    <td class="ilt" nowrap>&nbsp;&nbsp;&nbsp;Entered By:</td>
<? if ($user_id != 1) {
?>
    <td class="ilt"><?=$userfullname?></td>
<? } else {
?>
    <td class="ilt"><font color="red">ERROR: TWO DIFFERENT USERS CONCURRENTLY LOGGED IN TO TDW/ETPA ON THIS M/C.<br>
    <u>Please logout and login again to continue entering news items.</u></font></td>
<?
}
?>
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
	<iframe name="if_status" src="events_entry_process.php" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
<DIV ID="divvdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</body>

