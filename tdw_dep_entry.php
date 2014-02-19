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
params_val = "vsource=" + document.de_help.vsource.value + "&";
params_val = params_val + "vtype=" + document.de_help.vtype.value + "&";
params_val = params_val + "vdirection=" + document.de_help.vdirection.value + "&";
params_val = params_val + "venteredby=" + document.de_help.venteredby.value + "&";
params_val = params_val + "is_edit=" + document.de_help.is_edit.value + "&";
params_val = params_val + "vremarks=" + document.de_help.vremarks.value;

	if (document.de_help.vsource.value == '' || document.de_help.vtype.value == '') {
		alert("Source and Type are required fields. Please enter appropriate values and then proceed!");
		return false;
	} else {
		showDetail(params_val);	
		resetThis();
	}
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>tdw_dep_entry_process.php" + "?" +  str;
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
		} 
		else { // IE 4 
			alert("IE 4");
		}
	} 

} 

function resetThis() {
	setFocus('vsource');
	document.de_help.vsource.value = ""
	document.de_help.vtype.value = ""
	document.de_help.vdirection.value = ""
	document.de_help.vremarks.value = ""
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
<body onLoad="setFocus('vsource'); showDetail('');">
<?
tsp(100, "TDW Dependencies Data Entry");

if ($rec_edit) {
	$qry = "select * from tdw_server_dependencies where auto_id = '".$rec_edit."'";
	$result = mysql_query($qry) or die(tdw_mysql_error($qry));
	while ( $row = mysql_fetch_array($result) ) {
		$vsource = $row["dep_source"];
		$vtype = $row["dep_type"];
		$vdirection = $row["dep_direction"];
		$vremarks = $row["dep_remarks"];
	} 
} else {
		$vsource = "";
		$vtype = "";
		$vdirection = "";
		$vremarks = "";
}

?>
&nbsp;&nbsp;
<form name="de_help" id="de_help">
<table width="500" height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Server:</td>
    <td>
			<input class="Text" name="vsource" value="<?=$vsource?>" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vtype')">		
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Type</td>
    <td>
			<input class="Text" name="vtype" value="<?=$vtype?>" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vdirection')">		
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Direction</td>
    <td>
			<input class="Text" name="vdirection" value="<?=$vdirection?>" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vremarks')">		
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Comments</td>
    <td>
			<textarea rows="5" cols="100" name="vremarks"><?=$vremarks?></textarea><!-- onKeyPress="return bar(event, 'Submit')"-->
		</td>
  </tr>
  <tr>
    <td></td>
    <td><input name="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;"></td>
  </tr>
</table>
<input type="hidden" name="venteredby" value="<?=$user_id?>">
<input type="hidden" name="is_edit" value="<?=$rec_edit?>">
</form>
<?
tep();
?>

<table width="100%">
<!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" src="tdw_dep_entry_process.php" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
</body>

