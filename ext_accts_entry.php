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
params_val = "vemployee=" + document.ext_accts.vemployee.value + "&";
params_val = params_val + "vaccount=" + document.ext_accts.vaccount.value + "&";
params_val = params_val + "vcustodian=" + document.ext_accts.vcustodian.value + "&";
params_val = params_val + "user_id=" + "<?=$user_id?>" + "&";
params_val = params_val + "venteredby=" + document.ext_accts.venteredby.value;
showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>ext_accts_entry_process.php" + "?" +  str;
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

	setFocus('vemployee');
	document.ext_accts.vemployee.value = ""
	document.ext_accts.vaccount.value = ""
	document.ext_accts.vcustodian.value = ""		
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
<body onLoad="setFocus('vemployee'); showDetail('user_id=<?=$user_id?>');">
<?
tsp(100, "External Employee Accounts Entry");
?>
<form name="ext_accts">
<table border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="ilt">&nbsp;Employee</td>
    <td class="ilt">&nbsp;Account Number</td>
    <td class="ilt">&nbsp;Custodian</td>
    <td class="ilt">Entered By:</td>
    <td class="ilt"></td>
    <td class="ilt"></td>
  </tr>
  <tr>
    <td>
				<select name="vemployee" size="1" onKeyPress="return bar(event, 'vaccount')">
				<option value="">Select Employee</option>
				<?
				$qry_users = "select ID, Fullname 
											 from users
											 WHERE user_isactive = 1
											 ORDER BY Fullname";
				$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));
				while ( $row_users = mysql_fetch_array($result_users) ) 
				{
				?>
				<option value="<?=$row_users["ID"]?>"><?=$row_users["Fullname"]?></option>
				<?
				}
				?>
				</select>
		</td>
    <td><input class="Text" name="vaccount" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vcustodian')"></td>
    <td><input class="Text" name="vcustodian" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'Submit')"></td>
    <td class="ilt"><?=$userfullname?></td>
    <td><input name="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;"></td>
    <td width="15">&nbsp;</td>
    <td><a class="links_temp" href="ext_accts_entry_excel.php" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a></td>
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
	<iframe name="if_status" src="" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
</body>