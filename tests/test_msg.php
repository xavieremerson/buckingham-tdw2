
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<?
include('includes/dbconnect.php');

////
// Create message with image depending on severity
// 1 = green, 2 = orange, 3 = red
   function sys_message($severity, $msgtext) {
	 
	 
	 //Rounded corner tables used across the application
		$table_start = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td width="7" height="7" background="images/tables/lt.jpg"></td>
				<td background="images/tables/ts.jpg"></td>
				<td width="7" height="7" background="images/tables/rt.jpg"></td>
			</tr>
			<tr> 
				<td width="7" background="images/tables/ls.jpg"></td>
				<td>';
		$table_end = '</td>
				<td width="7" background="images/tables/rs.jpg"></td>
			</tr>
			<tr> 
				<td width="7" height="7" background="images/tables/lb.jpg"></td>
				<td background="images/tables/bs.jpg"></td>
				<td width="7" height="7" background="images/tables/rb.jpg"></td>
			</tr>
		</table>';

   if ($severity == 1){
	 $imagefile = 'msg_success.gif';
 	 $varcssstyle = 'links10';
	 }
	 elseif ($severity == 2){
	 $imagefile = 'msg_warning.gif';
 	 $varcssstyle = 'links10';
	 }
	 elseif ($severity == 3){
	 $imagefile = 'msg_error.gif';
 	 $varcssstyle = 'links10';
	 }
	 else {
	 $imagefile = 'msg_warning.gif';
 	 $varcssstyle = 'msg_warning';
	 }
	 
	 
   echo '<tr><td valign="top">'.$table_start.'<img src="images/' . $imagefile . '"><a class="' . $varcssstyle . '">' . $msgtext . '</a>'.$table_end.'</td></tr>';
	 						 
	 }	 

sys_message(1,"This is a test");
sys_message(2,"This is a test");
sys_message(3,"This is a test");

?>
<style type="text/css">
<!--
input.submit, a.button, input.button {
	border: 3px double #0179a5;
	/* #0092C9 */
	border-left-color: #54C9F5;
	border-top-color: #54C9F5;
	margin: 5px 5px 5px 0;
	color: white;
	height: auto;
	text-decoration: underline;
	font-family: Helvetica, Arial, sans-serif;
	font-weight: bold;
	font-size: 12;
	padding: 0.1ex 0;
	cursor: pointer;
	background: #00AFF0 url(images/button_bg.gif) top left repeat-x;
	text-decoration: none;
}

div.buttons {
	clear: both;
	margin-bottom: 10px;
}
div.buttons span {
	margin: 0 1em 0 1ex;
	position: relative;
	top: -1em;
}
-->
</style>
<form>
<input class="submit" type="submit" name="test" value="test button" />
</form>