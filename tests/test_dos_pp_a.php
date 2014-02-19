<?
include('../includes/global.php');
include('../includes/dbconnect.php');
include('../includes/functions.php');
?>

<script language="JavaScript" src="../includes/js/popup.js"></script>
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
params_val = "val1=" + document.de_comm.val1.value + "&";
params_val = params_val + "val2=" + document.de_comm.val2.value + "&";
params_val = params_val + "val3=" + document.de_comm.val3.value;
showDetail(params_val);
}

function showDetail(str)
{ 
	var url="test_dos_pp_b.php" + "?" +  str;
	xmlHttp=GetXmlHttpObject(setDataInDiv);
	xmlHttp.open("GET", url , true);
	xmlHttp.send(null);
	setFocus('val1');
	document.de_comm.val1.value = ""
	document.de_comm.val2.value = ""
	document.de_comm.val3.value = ""
} 

function setDataInDiv() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState == "complete")
		{ 
			//alert("ready state achieved.");
			//alert(xmlHttp.responseText);
			document.getElementById("div_status").innerHTML=xmlHttp.responseText 
		} 
	//else 
		//{
         //window.location.reload(false)
		//}
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


<style type="text/css">
<!--
.txt_status {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #0000FF;
}
-->
</style>
<body onLoad="setFocus('val1'); showDetail('');">
<form name="de_comm">
<table width="400" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60">input1</td>
    <td width="334"><input name="val1" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'val2')"></td>
  </tr>
  <tr>
    <td>input2</td>
    <td><input name="val2" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'val3')"></td>
  </tr>
  <tr>
    <td>input3</td>
    <td><input name="val3" type="text" size="30" maxlength="30" onKeyPress="return bar(event, 'Submit')"></td>
  </tr>
  <tr>
    <td></td>
    <td><input name="Submit" type="button" onClick="getFormValues()" value="SAVE"></td>
  </tr>
</table>
</form>
<div id="div_status" class="txt_status">Status from the other page will appear here

</div>

<br>



</body>