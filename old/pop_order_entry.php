<?
include ('includes/dbconnect.php');
include ('includes/global.php');
include ('includes/functions.php');
include ('includes/functions_email.php');

?>
<title>Order Entry</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body>
<?
if($submit_x)
{
	//create tracker_id
	$tracker_id = md5(gen_control_number());
	$query_insert = "INSERT INTO opre_order_preapproval (
									opre_tracker_id,
									opre_user_id, 
									opre_request_datetime, 
									opre_requestor_comments, 
									opre_buy_sell, 
									opre_quantity, 
									opre_symbol, 
									opre_type, 
									opre_price, 
									opre_expiration, 
									opre_instructions)
					 		VALUES('".
									$tracker_id."',".
									$user_id.", 
									now(), '".
									$requestor_comments."','".
									$buy_sell."', '".
									$quantity."', '".
									$symbol."', '".
									$type."', '".
									$price."', '".
									$expiration."', '".
									$instructions."')";
	
	$result_insert = mysql_query($query_insert) or die(mysql_error());


	//user_info
		$getuserdata = mysql_query("SELECT ID, Fullname, Email, is_administrator, (now() < login_expiry) as login_active, DATE_FORMAT(login_expiry,'%b %D, %Y') as login_expiry, DATE_FORMAT(login_expiry,'%l:%i %p') as tval FROM Users WHERE ID = ".$user_id) or die (mysql_error());
		while ( $row = mysql_fetch_array($getuserdata) ) {
	
		$user_fullname = $row["Fullname"];
		$user_email = $row["Email"];
		$sender_email = $user_fullname ."<".$user_email.">";
		}

	//send email to approver(s) with information
	
	//get addressee information
		$getapproverdata = mysql_query("SELECT ID, Fullname, Email, is_administrator FROM Users WHERE is_trade_approver = 1") or die (mysql_error());
		while ( $rowapprover = mysql_fetch_array($getapproverdata) ) {
	
			$approver_fullname = $rowapprover["Fullname"];
			$approver_email = $rowapprover["Email"];
			$approver_id = $rowapprover["ID"];
	
				$email_data = "";
				$email_data .= "From : ".$user_fullname . "<br><br>";
				$email_data .= "Details:<br>
												Buy/Sell: ".$buy_sell."<br>".
												"Symbol: ".$symbol."<br>".
												"Quantity: ".$quantity."<br>".
												"Order Type: ".lookup_order_type ($type)."<br>".
												"Price: ".$price."<br>".
												"Expiration: ".$expiration."<br>".
												"Instructions: ".$instructions."<br>".
												"Comments: ".$requestor_comments."<br>";
												
				//get supporting information
				$email_data .= "<hr>";
				$email_data .= "Supporting Information";								
				$email_data .= "<hr>";
				$email_data .= '<a href="'.$_site_url.'pop_order_authorize.php?requestid='.$tracker_id.'&flag=1&authid='.rand(11111,22222).$approver_id.'">Go to Authorization Module</a><br><br>';
		
				$mailsubject = "Request to ".$buy_sell." ".$quantity. " shares of ".$symbol." (".date("D, m/d/Y h:i a"). ")";
				$email_heading = "Request for approval on ".date("D, m/d/Y h:i a");
				$fileattach = "";
				$control_id = gen_control_number();
				$mailbodysubinfo = $email_data;
				//html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
				html_email_person($approver_email, $sender_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
				//html_email_system($approver_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
				echo "Mail sent to :". $approver_email."<br><br>";
		}
}

$query_type = "SELECT otyp_value AS d_value, otyp_option AS d_option FROM otyp_order_type WHERE otyp_isactive = '1'";
$result_type = mysql_query($query_type) or die(mysql_error());

$query_expiration = "SELECT oexp_value AS d_value, oexp_option AS d_option FROM oexp_order_expiration WHERE oexp_isactive = '1'";
$result_expiration = mysql_query($query_expiration) or die(mysql_error());

$query_instruction = "SELECT oins_value AS d_value, oins_option AS d_option FROM oins_order_instruction WHERE oins_isactive = '1'";
$result_instruction = mysql_query($query_instruction) or die(mysql_error());

?>

<? table_start_percent(100, "Order Entry"); ?>


<form action="<?$php_self?>" name="frm_order" method="post" target="_self">
<table class="links12_orderentry" border="0" cellspacing="2" cellpadding="5" width="100%" height="100%">
	<tr> 
		<td nowrap>
			<input type="radio" name="buy_sell" value="Buy">Buy <br>
			<input type="radio" name="buy_sell" value="Sell">Sell
		</td>
		<td nowrap>Quantity&nbsp;&nbsp;<input type="text" class="Text" name="quantity">&nbsp;&nbsp;of Symbol &nbsp;&nbsp;<input type="text" class="Text" name="symbol"></td>
	</tr>
	
	<tr>
		<td colspan="2" nowrap><hr size="1" color="#999999"></td>
	</tr>

	
	<tr> 
		<td nowrap>Order Type: 
			<select name="type" class="Text">
			<? createdropdown3($result_type, 1); ?>
			</select>
		</td>
		
		<td nowrap>Price:&nbsp;&nbsp;
			<input type="text" class="Text" name="price">
		</td>
	</tr>
	
	<tr>
		<td colspan="2" nowrap><hr size="1" color="#999999"></td>
	</tr>

	<tr> 
		<td nowrap>Expiration: 
			<select name="expiration" class="Text">
			<? createdropdown3($result_expiration, 2); ?>
			</select>
		</td>
		
		<td nowrap>Special Instructions:
			<select name="instructions" class="Text">
			<? createdropdown3($result_instruction, 3); ?>
			</select>
		</td>
	</tr>

	<tr>
		<td colspan="2" nowrap><hr size="1" color="#999999"></td>
	</tr>
	<tr>
		<td colspan="2" nowrap><textarea name="requestor_comments" cols="30" rows="3">Comments:</textarea></td>
	</tr>
	<tr valign="bottom">
		<td colspan="2" nowrap valign="bottom">
<!-- 			<input type="submit" class="Submit" name="submit" value="Save and Close" onClick="javascript:window.close()">
 -->			<INPUT type=image name="submit" src="images/submit_buttons/save_close.jpg" border="0" >			
			&nbsp;&nbsp;
		</td>
	</tr>

</table>
</form>
<? table_end_percent();?>

</body>








