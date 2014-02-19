<?
include ('includes/dbconnect.php');
include ('includes/global.php');

if ($submit and $nadd_notes != 'Add Note here ...') {
$str_query = "insert into nadd_add_notes values ('',".$user_id.",".$trade_id.",'".$nadd_notes."', now(),1)";
$result = mysql_query($str_query) or die (mysql_error());
}
?>
<?
if ($dodelete == 1) {
$str_query = "update nadd_add_notes set nadd_is_active = 0 where nadd_id = ".$nadd_id;
$result = mysql_query($str_query) or die (mysql_error());
}
?>



<title>Add Note</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body onunload="window.opener.location.reload()">
<?
if ($trade_id != '') {

	$str_query = "select * from nadd_add_notes where nadd_trade_id = ".$trade_id." and nadd_user_id = ".$user_id." and nadd_is_active = 1";
	$result = mysql_query($str_query) or die (mysql_error());
						
		while ( $row = mysql_fetch_array($result) ) {
	 ?>
		<? echo $table_start; ?>
		<form action="av_note.php" method="post" enctype="multipart/form-data" name="delnote" target="_self">
		<input name="nadd_id" type="hidden" value="<?=$row["nadd_id"]?>">
		<input name="user_id" type="hidden" value="<?=$user_id?>">
		<input name="trade_id" type="hidden" value="<?=$trade_id?>">
		<input name="dodelete" type="hidden" value="1">
		<INPUT TYPE="IMAGE" src="images/delete.gif" name="deletenote" ALT="Delete Note" BORDER="0">
		<a class="links10"><?=$row["nadd_datetime"]?></a>
		<br>
		<a class="links10"><?=$row["nadd_notes"]?></a></form>
		<? echo $table_end; ?>
		<?
		}
}
?>

		<form action="av_note.php" method="post" enctype="multipart/form-data" name="notedata" target="_self">
		<? echo $table_start; ?>
		<textarea name="nadd_notes" class="Textarea" cols="50" rows="3">Add Note here ...</textarea>
		<input name="user_id" type="hidden" value="<?=$user_id?>">
		<input name="trade_id" type="hidden" value="<?=$trade_id?>">
		<? echo $table_end; ?>
		<center><input name="submit" class="Submit" type="submit" value=" Save "></center>
		</form>
</body>


