<?
// pop_acct_delete
// Calling string
/* <a href="javascript:CreateWnd('pop_acctdel.php?Name=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"> */

include ('includes/dbconnect.php');
include ('includes/global.php');
?>

<title>Delete Account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body onunload="window.opener.location.reload()">

<?
  if ($Action == "DeleteY") { 

    $result = mysql_query("update Employee_accounts set acct_is_active = 0 where acct_auto_id = '$ID'") or die(mysql_error());

?>
<? echo $table_start; ?>
<a class="links10">Acoount: (<?=$Name?>) deleted successfully.</a><br><center><a href="JavaScript:window.close();" class="links12">Close</a></center>
<? echo $table_end; ?>

<?
  }
?>





<?
if ($Action == "Delete") { 
?>


<? echo $table_start; ?>
<a class="links10">Are you sure you want to delete account: (<?=$Name?>) ?</a><br><center><a href="pop_acctdel?Action=DeleteY&Name=<?=$Name?>&ID=<?=$ID?>" class="links12">Yes</a> &nbsp;&nbsp;:&nbsp;&nbsp; <a href="javascript:window.close();" class="links12">No</a></center> 
<? echo $table_end; ?>

<?
}
?>
</body>








