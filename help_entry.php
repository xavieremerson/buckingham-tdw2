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
params_val = "vtitle=" + document.de_help.vtitle.value + "&";
params_val = params_val + "vdetail=" + document.de_help.vdetail.value.replace(/\n/gi, "<br>");

	if (document.de_help.vtitle.value == '' || document.de_help.vdetail.value == '') {
		alert("Title and Description are required fields. Please enter appropriate values and then proceed!");
		return false;
	} else {
		showDetail(params_val);	
	}
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>help_entry_process.php" + "?" +  str;
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

	setFocus('vtitle');
	document.de_help.vtitle.value = ""
	document.de_help.vdetail.value = ""
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
<body onLoad="setFocus('vtitle'); showDetail('');">
<?
tsp(100, "Help Data Entry");
?>
&nbsp;&nbsp;
<form name="de_help" id="de_help">
<table width="500" height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Title</td>
    <td>
			<input class="Text" name="vtitle" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'vdetail')">		
		</td>
  </tr>
  <tr>
    <td class="ilt">&nbsp;&nbsp;&nbsp;Help Description</td>
    <td>
			<textarea class="Text" name="vdetail" rows="6" cols="80"></textarea>
		</td>
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

<table width="100%">
<!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" src="help_entry_process.php" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
<DIV ID="divvdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</body>

