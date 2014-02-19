<?
include('../includes/global.php');
include('../includes/dbconnect.php');
include('../includes/functions.php');

$start_textitem_num = rand(1000000000,9999999999);

?>
<script language ="Javascript">
function setFocus(currid) {
  var nextid
  
  nextid = currid + 1;
  document.getElementById(nextid).focus();

}

function bar(evt, itemid){
var k=evt.keyCode||evt.which;
 if (k==13 && itemid != "") {
   setFocus(itemid);
 }
  /* var maxTab = 4
 tabNum++
  if (tabNum == maxTab) {    
  	tabNum = 0;    
  }
 
 else {*/

return k!=13;
}

function getFormValues() {

alert("button clicked");
alert(document.de_comm.<?=$start_textitem_num?>.value());

/*
params_val = "val1=" + document.de_comm.<?=$start_textitem_num?>.value + "&";
params_val = params_val + "val2=" + document.de_comm.<?=$start_textitem_num+1?>.value;
params_val = params_val + "val3=" + document.de_comm.<?=$start_textitem_num+2?>.value;
alert(params_val);
showDetail(params_val);
*/
}

function showDetail(str)
{ 
	//alert("here  " + str);
	var url="test_dos_carol1.php" + "?" +  str;
	xmlHttp=GetXmlHttpObject(setDataInDiv);
	xmlHttp.open("GET", url , true);
	xmlHttp.send(null);
	setDataInDiv();
} 

function setDataInDiv() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
			//alert("ready state achieved.");
			document.getElementById("zone").innerHTML=xmlHttp.responseText 
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


<body>
<form name="de_comm" action="#" method="GET">
<table width="400" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60">input1</td>
    <td width="334"><input name="<?=$start_textitem_num?>" type="text" size="30" maxlength="30" onKeyPress="return bar(event, <?=$start_textitem_num?>)"></td>
  </tr>
  <tr>
    <td>input2</td>
    <td><input name="<?=$start_textitem_num + 1?>" type="text" size="30" maxlength="30" onKeyPress="return bar(event, <?=$start_textitem_num + 1?>)"></td>
  </tr>
  <tr>
    <td>input3</td>
    <td><input name="<?=$start_textitem_num + 2?>" type="text" size="30" maxlength="30" onKeyPress="return bar(event, <?=$start_textitem_num + 2?>)"></td>
  </tr>
  <tr>
    <td><a href="javascript:getFormValues()">test</a></td>
    <td><input name="Submit" type="submit" onClick="getFormValues()" value="SAVE"></td>
  </tr>
</table>
</form>
<div id="zone"></div>
</body>