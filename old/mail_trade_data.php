<?
include ('includes/dbconnect.php');
include ('includes/global.php');
include('includes/functions.php');

if ($submit) 
{
	$mailsubject = "Trade Details and Comments";
	$mailbody = $comments;
	$emailheading = '';
	$fileattach = '';
	html_emails_dynamic ($to, $from, $mailsubject, $mailbody, $emailheading, $fileattach, gen_control_number ());
}
?>

<title>Email Trade Info.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body onunload="window.opener.location.reload()">

<?
if ($trade_id != '') 
{
	$str_query = "SELECT * FROM acti_action_item_flag WHERE acti_trade_id = ".$trade_id." and acti_user_id = ".$user_id." and acti_is_active = 1";
	$result = mysql_query($str_query) or die (mysql_error());
	
	$query_user_data = "SELECT Email FROM Users WHERE ID = ".$user_id;
	$result_user_data = mysql_query($query_user_data) or die (mysql_error());
	$row_user_data = mysql_fetch_array($result_user_data);
	
	$query_trade_data = "SELECT trdm_auto_id, trdm_account_number, trdm_buy_sell, trdm_quantity, trdm_symbol, trdm_price, trdm_trade_date, trdm_trade_time 
						 FROM Trades_m WHERE trdm_auto_id = ".$trade_id;
	$result_trade_data =  mysql_query($query_trade_data) or die (mysql_error());
	$row_trade_data = mysql_fetch_array($result_trade_data);	

	$text_area_data = "\n\nTrade Info -- \n";
	$text_area_data = $text_area_data . "Acct #: ".$row_trade_data["trdm_account_number"]."\n";
	$text_area_data = $text_area_data . "Buy/Sell: ".$row_trade_data["trdm_buy_sell"]."\n";
	$text_area_data = $text_area_data . "Quantity: ".$row_trade_data["trdm_quantity"]."\n";
	$text_area_data = $text_area_data . "Symbol: ".$row_trade_data["trdm_symbol"]."\n";
	$text_area_data = $text_area_data . "Price: ".$row_trade_data["trdm_price"]."\n";
	$text_area_data = $text_area_data . "Trade Date: ".$row_trade_data["trdm_trade_date"]."\n";
	$text_area_data = $text_area_data . "Trade Time: ".$row_trade_data["trdm_trade_time"]."\n\n\n";
	$text_area_data = $text_area_data . "Comments -- \n";
}
	?>

	<form action="mail_trade_data.php" method="post" enctype="multipart/form-data" name="flagdata" target="_self">
<? table_start_percent(100, "Send Email with Trade Information"); ?>
		<table>
			<tr>
				<td><p class="Contact">To:</p></td>
				<td><input name="to" type="text" class="Text" value="" size="25" maxlength="40"></td>
			</tr>
			<tr>
				<td><p class="Contact">From:</p></td>
				<td><input name="from" type="text" class="Text" value="<?=$row_user_data["Email"]?>" size="25" maxlength="40"></td>
			</tr>
			<tr>
				<td><p class="Contact">Message:</p></td>
				<td><textarea name="comments" class="Textarea" cols="50" rows="25"><?=$text_area_data?></textarea></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><center>
					<!-- <input name="submit" class="Submit" type="submit" value=" Send "  onClick="javascript:window.close()"> -->
					<INPUT type=image name="submit" src="images/submit_buttons/send_msg.jpg" border="0" onClick="javascript:window.close()">
					</center></td>
			</tr>
		</table>
	
	<input name="acti_id" type="hidden" value="<?=$acti_id?>">
	<input name="user_id" type="hidden" value="<?=$user_id?>">
	<input name="trade_id" type="hidden" value="<?=$trade_id?>">
	<? table_end_percent(); ?>
	</form>
</body>