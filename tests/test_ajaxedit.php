
<HTML>
<HEAD><TITLE>test</TITLE>
</HEAD>
<BODY>
<?
include('includes/dbconnect.php');
include('includes/functions.php');
?>

<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>

<input type="text" id="1234" name="varval" size="20" />
<a href="javascript:xedit(document.getElementById('1234').value)">Save</a>&nbsp;&nbsp;<a href="javascript:changecontent('zzzzzzzzzzzzzzzzzzzz')">test</a>
<SCRIPT>
// CREDITS:
// Messagebox with fadeing Background By Peter Gehrig http://www.24fun.com

// set your messages. Add as many as you like (you may add additional HTML-tags)
//removed, message will passed as argument

var textfont="Arial"
var textfontcolor="#000000"
var textfontcolorrollover="#ffffff"
var textfontsize=9
var textfontsizeHTML=2
var textbgcolor="#DAFDD9"
var textweight="nomal"
var textitalic="normal"
var textwidth=240
var textheight=40
var textpause=2
var textborder=1
var textbordercolor="#ffffff"
var textalign="left"
var textvalign="top"

// do not edit below this line
var textdecoration="none"
var textweightA="<b>"
var textweightB="</b>"
var textitalicA=""
var textitalicB=""
var transparency=100
var transparencystep=2

var x_pos=0
var y_pos=0
var i_text=0
var textsplit=""
var i_textsplit=0
var i_mark=0
var tickercontent
var pausefade=0
textpause*=500

var oneloopfinished=true

var browserinfos=navigator.userAgent 
var ie=document.all&&!browserinfos.match(/Opera/)
var ns4=document.layers
var ns6=document.getElementById&&!document.all&&!browserinfos.match(/Opera/)
var opera=browserinfos.match(/Opera/)  
var browserok=ie||ns4||ns6||opera

function changecontent(tdw_msg) {
	getcontent(tdw_msg)
	alert("here1");

	if (ie) {
		ticker.innerHTML=content
		fadeout(tdw_msg)
	}
	if (opera || ns6) {
		document.getElementById('ticker').innerHTML=content
		var texttimer=setTimeout("changecontent(tdw_msg)",2*textpause)
	}
	if (ns4) {
		document.roof.document.ticker.document.write(content)
		document.roof.document.ticker.document.close()
		var texttimer=setTimeout("changecontent(tdw_msg)",textpause)
	}	
}

function fadein(tdw_msg) {
	if (transparency<100){
		transparency+=transparencystep
		if (ie) {
			document.all.tickerbg.filters.alpha.opacity=transparency
		}
		var fadetimer=setTimeout("fadein(tdw_msg)",pausefade)
	}
	else {
		clearTimeout(fadetimer)
		setTimeout("changecontent(tdw_msg)",1000)
	}
}

function fadeout(tdw_msg) {
	if (transparency>0){
		transparency-=transparencystep
		if (ie) {
			document.all.tickerbg.filters.alpha.opacity=transparency
		}
		if (ns6) {
			document.getElementById('tickerbg').style.MozOpacity=transparency/100
		}
		var fadetimer=setTimeout("fadeout(tdw_msg)",pausefade)
	}
	else {
		clearTimeout(fadetimer)
		setTimeout("fadein(tdw_msg)",textpause)
	}
}

//getcontent()
function getcontent(tdw_msg) {
	if (ie || opera) {
		var tablewidth=textwidth-2*textborder
		var tableheight=textheight-2*textborder
	}
	else {
		var tablewidth=textwidth
		var tableheight=textheight
	}
	if (ie || ns6) {	
		var padding=parseInt(textborder)+3
		content="<table width="+tablewidth+" height="+tableheight+" cellpadding="+padding+" cellspacing=0 border=0><tr valign="+textvalign+"><td align="+textalign+">"
		content+="<a style=\"position:relative;font-family:\'"+textfont+"\';font-size:"+textfontsize+"pt;font-weight:"+textweight+";text-decoration:"+textdecoration+";color:"+textfontcolor+";font-style:"+textitalic+";\" onMouseOver=\"this.style.color=\'"+textfontcolorrollover+"\'\" onMouseOut=\"this.style.color=\'"+textfontcolor+"\'\">"
		content+=tdw_msg
		content+="</a></td></tr></table>"
	}
	else {	
		content="<table width="+tablewidth+" height="+tableheight+" cellpadding="+textborder+" cellspacing=0><tr valign="+textvalign+"><td align="+textalign+">"
		content+="<a style=\"position:relative;font-family:\'"+textfont+"\';font-size:"+textfontsize+"pt;font-weight:"+textweight+";text-decoration:"+textdecoration+";color:"+textfontcolor+";font-style:"+textitalic+";\">"
		content+=tdw_msg
		content+="</a></td></tr></table>"

		framecontent="<table width="+tablewidth+" height="+tableheight+" cellpadding=0 cellspacing=0 border="+textborder+"><tr><td>"
		framecontent+="<font color=\""+textbgcolor+"\">"
		framecontent+="."
		framecontent+="</font>"
		framecontent+="</td></tr></table>"
	}
}

if (ie || ns6 || opera) {
	if (ns6) {
		textwidth-=2*textborder
		textheight-=2*textborder
	}
	document.write("<div id=\"roof\" style=\"position:relative;width:"+textwidth+"px;height:"+textheight+"px;font-family:\'"+textfont+"\';border-style:solid;border-color:"+textbordercolor+";border-width:"+textborder+"px;background-color:"+textbgcolor+";\">")
	
	if (!opera && !ns6 ) {
		document.write("<div id=\"tickerbg\" style=\"position:absolute;top:"+-textborder+"px;left:"+-textborder+"px;width:"+textwidth+"px;height:"+textheight+"px;font-family:\'"+textfont+"\';font-size:"+textfontsize+"pt;font-weight:"+textweight+";font-style:"+textitalic+";border-style:solid;border-color:"+textbordercolor+";border-width:"+textborder+"px;background-color:"+textfontcolor+";overflow:hidden\;filter:alpha(opacity=100)\">")
		document.write("</div>")
	}
	
	document.write("<div id=\"ticker\" style=\"position:absolute;top:"+-textborder+"px;left:"+-textborder+"px;width:"+textwidth+"px;height:"+textheight+"px;font-family:\'"+textfont+"\';font-size:"+textfontsize+"pt;font-weight:"+textweight+";font-style:"+textitalic+";border-style:solid;border-color:"+textbordercolor+";border-width:"+textborder+"px;overflow:hidden\;\">")
	document.write("</div></div>")

	window.onload=changecontent('this is a test');
}

else if (ns4) {
	document.write("<ilayer name=\"roof\" width="+textwidth+" height="+textheight+">")
		document.write("<layer name=\"tickerframe\" width="+textwidth+" height="+textheight+" top=0 left=0 bgcolor="+textbgcolor+">")
		document.write(framecontent)
		document.write("</layer>")
		document.write("<layer name=\"ticker\" width="+textwidth+" height="+textheight+" top=0 left=0>")
		document.write()
		document.write("</layer>")
	document.write("</ilayer>")
	window.onload=changecontent('this is a test');
}
</script>
</BODY>
<script language ="Javascript">
<!--
function xedit(varval) {
	AjaxRequest.get(
		{
			'url':'test_ajaxedit1.php?var='+ varval
			,'onSuccess':function(req){ 
																	document.getElementById('tickerbg').innerHTML='';
																	document.getElementById('tickerbg').innerHTML=req.responseText; 
																	//alert('sdfsdfsdfsdfsdfsdf');
																	changecontent(req.responseText);
																}
			,'onError':function(req)  { document.getElementById('tickerbg').innerHTML='Error from processing page here.'; }
		}
	);
	//alert(req.responseText);
	//alert('sdfsdfsdfsdfsdfsdf');
	//changecontent('sdfsdfsdfsdfsdfsdf');
}
-->
</script>

</HTML>

