<?php

  include('top.php');
	 
?>
<tr>
<td valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
		<!-- ADMIN CONTENT BEGIN -->
		
		
		<form name="jumpy">
<select name="example" size="1" onChange="gone()">
<!-- CHANGE THE BELOW URLS TO YOUR OWN-->
<option value="http://www.yahoo.com" selected>Yahoo.com</option>
<option value="./data/exports/Trades_Report_NEW2004-05-18.pdf">Trades Report</option>
<option value="http://www.lycos.com">Lycos</option>
<option value="http://www.AltaVista.com">AltaVista</option>
</select>

<input type="button" name="test" value="Go!" onClick="gone()">
</form>

<script language="javascript">
<!--

//Drop-down Document Viewer- © Dynamic Drive (www.dynamicdrive.com)
//For full source code, 100's more DHTML scripts, and TOS,
//visit http://www.dynamicdrive.com

//Specify display mode (0 or 1)
//0 causes document to be displayed in an inline frame, while 1 in a new browser window
var displaymode=0
//if displaymode=0, configure inline frame attributes (ie: dimensions, intial document shown
var iframecode='<iframe id="external" style="width:100%;height:400px" src="http://www.yahoo.com"></iframe>'

/////NO NEED TO EDIT BELOW HERE////////////

if (displaymode==0)
document.write(iframecode)

function gone(){
var selectedurl=document.jumpy.example.options[document.jumpy.example.selectedIndex].value
if (document.getElementById&&displaymode==0)
document.getElementById("external").src=selectedurl
else if (document.all&&displaymode==0)
document.all.external.src=selectedurl
else{
if (!window.win2||win2.closed)
win2=window.open(selectedurl)
//else if win2 already exists
else{
win2.location=selectedurl
win2.focus()
}
}
}
//-->
</script>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		<!-- ADMIN CONTENT END -->
		</td>
  </tr>
</table>
</td>
</tr>

<?php

  include('bottom.php');
	 
?>

