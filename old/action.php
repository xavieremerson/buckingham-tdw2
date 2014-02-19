<?php
  include('top.php');
?>
<tr><td valign="top" align="center">
<?php
  if ($Action == "DeleteSingle") {
?>
<BR><BR><BR><BR><table border="1" bordercolor="#000099" cellpadding="4"><tr><td><BR><BR>
Are you sure you want to delete account: (<?=$Name?>)?<br><br><center><a href="action.php?Action=DeleteSingleY&Name=<?=$Name?>&ID=<?=$ID?>" class="links12">Yes</a> &nbsp;&nbsp;:&nbsp;&nbsp; <a href="JavaScript:history.back()" class="links12">No</a></center><BR><BR>
</td></tr></table>
<?php

  }

  if ($Action == "DeleteSingleY") { 

    $result = mysql_query("update Employee_accounts set acct_is_active = 0 where acct_auto_id = '$ID'") or die(mysql_error());

?>

<?
//this is a blank line
?>

<BR><BR><BR><BR><table border="1" bordercolor="#000099" cellpadding="4"><tr><td><BR><BR><BR><BR>
Acoount: (<?=$Name?>) deleted successfully.<br><br><center><a href="JavaScript:history.go(-2)" class="links12">Back</a></center><BR><BR>
</td></tr></table>

<?
  }



  if ($Action == "Edit") {

    $result = mysql_query("SELECT * FROM Employee_accounts WHERE acct_auto_id = '$ID'") or die(mysql_error());

    while ($row = mysql_fetch_array($result)) {

?>

<form action="action.php?Action=Save&ID=<?=$ID?>" method="post">

<BR><BR><BR><BR><table border="1" bordercolor="#000099" cellpadding="4"><tr><td><BR>Update Account<BR><br>
<table cellspacing="0" cellpadding="2" border="0">


	<tr valign="top"><td><p class="Contact">Account Rep. </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_rep" value="<?=$row["acct_rep"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Account Number </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_number" value="<?=$row["acct_number"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Account Name 1 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="50" name="acct_name1" value="<?=$row["acct_name1"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Account Name 2 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="50" name="acct_name2" value="<?=$row["acct_name2"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Open Date (yyyy-mm-dd) </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" size="12" type="text" name="acct_open_date" value="<?=$row["acct_open_date"]?>" /></p></td></tr>

	<tr valign="top"><td colspan="3" align="center"><p><input type="submit" value="  Save  " /></p></td></tr>

</table>
</td></tr></table>
</form>

<?php

    }

  }



  if ($Action == "Save") {

    $result = mysql_query("UPDATE Employee_accounts SET acct_rep = '$acct_rep',  acct_number = '$acct_number', acct_name1 = '$acct_name1', acct_name2 = '$acct_name2', acct_open_date = '$acct_open_date' WHERE acct_auto_id = '$ID'") or die(mysql_error());

?>
<BR><BR><BR><BR><table border="1" bordercolor="#000099" cellpadding="4"><tr><td>
<table cellpadding="2" cellspacing="0" border="0"><tr><td colspan="2"><p class="Contact">You updated the following:<br><img height="8" width="0" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Account Rep. </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_rep" value="<?=$acct_rep?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Account Number </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_number" value="<?=$acct_number?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Account Name 1 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="50" name="acct_name1" value="<?=$acct_name1?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Account Name 2 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="50" name="acct_name2" value="<?=$acct_name2?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Open Date (yyyy-mm-dd) </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_open_date" value="<?=$acct_open_date?>" /></p></td></tr>

	<tr valign="top"><td></td><td></td><td><BR><a href="JavaScript:history.go(-2)" class="links12">Go Back</a></td></tr>


</table>
</td></tr></table>
<?php

  }

?>
</td></tr>

<?php

  include('bottom.php');
	 
?>
