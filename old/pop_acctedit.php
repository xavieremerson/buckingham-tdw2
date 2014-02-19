<?
// pop_acct_delete
// Calling string
/* <a href="javascript:CreateWnd('pop_acctdel.php?Name=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"> */

include ('includes/dbconnect.php');
include ('includes/global.php');
?>

<title>Edit Account Information</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body onunload="window.opener.location.reload()">

<?
  if ($Action == "Edit") {

    $result = mysql_query("SELECT * FROM Employee_accounts WHERE acct_auto_id = '$ID'") or die(mysql_error());

    while ($row = mysql_fetch_array($result)) {

?>

<? echo $table_start; ?>
<form action="pop_acctedit.php?Action=Save&ID=<?=$ID?>" method="post">


<p class="Contact">Update Account</p> 
<table cellspacing="0" cellpadding="2" border="0">


	<tr valign="top"><td><p class="Contact">Acct. Rep. </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_rep" value="<?=$row["acct_rep"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Acct. # </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_number" value="<?=$row["acct_number"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Acct. Name 1 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="30" name="acct_name1" value="<?=$row["acct_name1"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Acct. Name 2 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="30" name="acct_name2" value="<?=$row["acct_name2"]?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Open Date</p></td><td><p class="Contact">:</p></td><td><p><input readonly class="Text" size="12" type="text" name="acct_open_date" value="<?=$row["acct_open_date"]?>" /></p></td></tr>

	<tr valign="top"><td colspan="3" align="center"><p><input class="Submit" type="submit" value="  Save  " /></p></td></tr>

</table>

</form>
<? echo $table_end; ?>
<?php

    }

  }
?>

<?

  if ($Action == "Save") {

    $result = mysql_query("UPDATE Employee_accounts SET acct_rep = '$acct_rep',  acct_number = '$acct_number', acct_name1 = '$acct_name1', acct_name2 = '$acct_name2', acct_open_date = '$acct_open_date' WHERE acct_auto_id = '$ID'") or die(mysql_error());

?>
<? echo $table_start; ?>
<p class="Contact">You updated the following:</p>
<table cellpadding="2" cellspacing="0" border="0">
	<tr valign="top"><td><p class="Contact">Acct. Rep. </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_rep" value="<?=$acct_rep?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Acct. Number </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_number" value="<?=$acct_number?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Acct. Name 1 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="30" name="acct_name1" value="<?=$acct_name1?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Acct. Name 2 </p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="30" name="acct_name2" value="<?=$acct_name2?>" /></p></td></tr>

	<tr valign="top"><td><p class="Contact">Open Date</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" size="12" name="acct_open_date" value="<?=$acct_open_date?>" /></p></td></tr>

	<tr valign="top"><td></td><td></td><td><BR><a href="JavaScript:window.close();" class="links12">Close</a></td></tr>


</table>
<? echo $table_end; ?>
<?php

  }
?>
</body>








