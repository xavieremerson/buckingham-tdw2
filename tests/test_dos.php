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
 if (k==13 && itemid
return k!=13;
}
</script>


<body onLoad= "setFocus(<?=$start_textitem_num - 1?>)">
<form name="de_comm" action="" method="post">
<table width="400" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60">input1</td>
    <td width="334"><input name="<?=$start_textitem_num?>" type="text" size="30" maxlength="30" onKeyPress="return bar(event, <?=$start_textitem_num?>)"></td>
  </tr>
  <tr>
    <td>input2</td>
    <td><input name="<?=$start_textitem_num + 1?>" type="text" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td>input3</td>
    <td><input name="<?=$start_textitem_num + 2?>" type="text" size="30" maxlength="30"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit" value="SAVE"></td>
  </tr>
</table>
</form>
</body>