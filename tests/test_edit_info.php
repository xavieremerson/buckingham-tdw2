<?
include('../includes/dbconnect.php');
include('../includes/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Info</title>
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

function refParent() {
	//opener.location.reload(true);
	opener.location.href = opener.location.href 
    //self.close();
}
</script>
</head>
<body onLoad="setFocus('val1')">
<? 
		if ($submit)
		{ 
			$query_upd = ("UPDATE carol_test SET field1='$val1', field2='$val2', field3='$val3' WHERE auto_id = '$auto_id'") or die (mysql_error());
			$result_upd = mysql_query($query_upd) or die(mysql_error());
			echo "Form submitted";
		}
			
?>
<form action="<?=$PHP_SELF?>" method="post" name="edit_comm" id="edit_comm" onSubmit="refParent()">
<table width="100%" border="1" cellspacing="0" cellpadding="0">
<?
$query_test = "SELECT * from carol_test where auto_id = '$auto_id'";
$result_test = mysql_query($query_test) or die(mysql_error());

while($row_test = mysql_fetch_array($result_test))
{
?>
  <tr>
  <td>ID</td>
  <td><?=$row_test["auto_id"]?>
  <input name="auto_id" type="hidden"  value="<?=$row_test["auto_id"]?>"/></td>
  <tr>
    <td>input1</td>
	<td><input name="val1" type="text" onKeyPress="return bar(event, 'val2')" value="<?=$row_test["field1"]?>" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td>input2</td>
    <td><input name="val2" type="text" onKeyPress="return bar(event, 'val3')" value="<?=$row_test["field2"]?>" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td>input3</td>
    <td><input name="val3" type="text" onKeyPress="return bar(event, 'Submit')" value="<?=$row_test["field3"]?>" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td></td>
    <td><input name="submit" type="button" onClick="refParent()" value="SAVE" />&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="cancel" type="button" onClick="javascript:window.close()" value="Cancel" /></td>
  </tr>
 <?
}
?>
</table>
</form>
</body>
</html>