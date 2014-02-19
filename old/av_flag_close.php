<?
include ('includes/dbconnect.php');
include ('includes/global.php');


if ($submit) 
{
	//$str_query = "insert into acti_action_item_flag(acti_user_id, acti_trade_id, acti_datetime_closed, acti_comments) values(".$user_id.",".$trade_id.",now(),'".$acti_comments."')";
	$str_query = "UPDATE acti_action_item_flag SET acti_datetime_closed = now(), acti_closing_comments = '".$acti_closing_comments."' WHERE acti_id = ".$acti_id; 
	$result = mysql_query($str_query) or die (mysql_error());
}


if ($dodelete == 1)
{
	$str_query = "update acti_action_item_flag set acti_is_active = 0 where acti_id = ".$acti_id;
	$result = mysql_query($str_query) or die (mysql_error());
}
?>


<title>Close Action Item</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body onunload="window.opener.location.reload()">

<?
if ($trade_id != '') 
{
	$str_query = "SELECT * FROM acti_action_item_flag WHERE acti_trade_id = ".$trade_id." and acti_user_id = ".$user_id." and acti_is_active = 1";
	$result = mysql_query($str_query) or die (mysql_error());
						
/*	while ( $row = mysql_fetch_array($result) ) 
	{	 
		if($row["acti_closing_comments"] != null)
		{
		 echo $table_start; 
?>
			<form action="av_flag_close.php" method="post" enctype="multipart/form-data" name="deleteacti" target="_self">
			<input name="acti_id" type="hidden" value="<?=$row["acti_id"]?>">
			<input name="user_id" type="hidden" value="<?=$user_id?>">
			<input name="trade_id" type="hidden" value="<?=$trade_id?>">
			<input name="dodelete" type="hidden" value="1">
			<INPUT TYPE="IMAGE" src="images/delete.gif" name="deleteacti" ALT="Delete Action Item" BORDER="0">
			
			<a class="links10"><?=$row["acti_datetime_closed"]?></a>
			<br>
			<a class="links10"><?=$row["acti_closing_comments"]?></a>	
			</form>
		<? 
		echo $table_end; 
		}
	}*/
}
	?>

	<form action="av_flag_close.php" method="post" enctype="multipart/form-data" name="flagdata" target="_self">
	<? echo $table_start; ?>
	<textarea name="acti_closing_comments" class="Textarea" cols="50" rows="3"><?=$row["acti_closing_comments"]?></textarea>
	<input name="acti_id" type="hidden" value="<?=$acti_id?>">
	<input name="user_id" type="hidden" value="<?=$user_id?>">
	<input name="trade_id" type="hidden" value="<?=$trade_id?>">
	<? echo $table_end; ?>
	<center>
	<input name="submit" class="Submit" type="submit" value=" Save & Close "  onClick="javascript:window.close()">
	
	
	</center>
	</form>
</body>