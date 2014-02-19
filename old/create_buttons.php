
<?
function makebutton ($label) {

echo '	  <table cellpadding="0" cellspacing="0" height="24" onClick="form.submit">
		<tr>
			<td width="20" background="images/button/left.jpg"></td>
			<td background="images/button/center.jpg"><font color="#333333" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>'.$label.'</b></font></td>
			<td width="20" background="images/button/right.jpg"></td>
		</tr>
	  </table>';

}

makebutton("Apply Filter");
echo "<br>";
makebutton("Save & Close");
echo "<br>";
makebutton("Add");
echo "<br>";
makebutton("cellpadding");
echo "<br>";
?>

<table height="24" cellpadding="0" cellspacing="0" onClick="form.submit">
		<tr>
			<td width="20" background="images/button/left.jpg"></td>
			<td background="images/button/center.jpg"><font color="#333333" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Test Button</b></font></td>
			<td width="20" background="images/button/right.jpg"></td>
		</tr>
	  </table>
