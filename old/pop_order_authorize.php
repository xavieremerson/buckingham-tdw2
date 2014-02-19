<?
include ('includes/dbconnect.php');
include ('includes/global.php');
include ('includes/functions.php');

?>


<title>Order Entry</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
		window.opener = top;
</SCRIPT>

<body>


<?
if($accept)
{
	$query_update = "UPDATE opre_order_preapproval 
									 SET 
										opre_isauthorized = '1', 
										opre_authorizedby = '".substr($authid,5,10)."', 
										opre_approver_comments = '".$approver_comments."',
										opre_action_datetime = now() 
									 WHERE opre_tracker_id = '".$requestid."'"; 

	echo $query_update;
               
 	$result_update = mysql_query($query_update) or die(mysql_error());
}

if($decline)
{
	$query_update = "UPDATE opre_order_preapproval 
									 SET 
										opre_isauthorized = '-1', 
										opre_authorizedby = '".substr($authid,5,10)."', 
										opre_approver_comments = '".$approver_comments."',
										opre_action_datetime = now() 
									 WHERE opre_tracker_id = '".$requestid."'"; 
	$result_update = mysql_query($query_update) or die(mysql_error());
}


//GET ORDER
$query_order = "SELECT * FROM opre_order_preapproval WHERE opre_tracker_id = '".$requestid."'";
$result_order = mysql_query($query_order) or die(mysql_error());
$row_order = mysql_fetch_array($result_order);

//already authorized order
if ($row_order["opre_authorizedby"] > 0) {
$prior_action = 1;
}

//GET TYPE
$query_type = "SELECT otyp_option FROM otyp_order_type WHERE otyp_value = '".$row_order['opre_type']."'";
$result_type = mysql_query($query_type) or die(mysql_error());
$row_type = mysql_fetch_array($result_type);

//GET EXPIRATION
$query_expiration = "SELECT oexp_option FROM oexp_order_expiration WHERE oexp_value = '".$row_order['opre_expiration']."'";
$result_expiration = mysql_query($query_expiration) or die(mysql_error());
$row_expiration = mysql_fetch_array($result_expiration);

//GET INSTRUCTION
$query_instruction = "SELECT oins_option FROM oins_order_instruction WHERE oins_value = '".$row_order['opre_instructions']."'";
$result_instruction = mysql_query($query_instruction) or die(mysql_error());
$row_instruction = mysql_fetch_array($result_instruction);

?>
<?
	//user_info
		$getuserdata = mysql_query("SELECT ID, Fullname, Email FROM Users WHERE ID = ".$row_order["opre_user_id"]) or die (mysql_error());
		$rowuser = mysql_fetch_array($getuserdata);
		$user_fullname = $rowuser["Fullname"];
		$user_email = $rowuser["Email"];

//get approver info
		$getapproverdata = mysql_query("SELECT ID, Fullname, Email FROM Users WHERE ID = ".substr($authid,5,10)) or die (mysql_error());
		$rowapprover = mysql_fetch_array($getapproverdata);
		$approver_fullname = $rowapprover["Fullname"];
		$approver_email = $rowapprover["Email"];
		$approver_id = $rowapprover["ID"];
?>

<center>

<? table_start_percent(50, "Order Preauthorization by ".$approver_fullname); ?>

<br>
<?
// if already approved, show information
if ($prior_action == 1) {
	//get prior authorizer info
		$getpriorapproverdata = mysql_query("SELECT ID, Fullname, Email FROM Users WHERE ID = ".$row_order["opre_authorizedby"]) or die (mysql_error());
		$rowpriorapprover = mysql_fetch_array($getpriorapproverdata);
		$priorapprover_fullname = $rowpriorapprover["Fullname"];
		$priorapprover_email = $rowpriorapprover["Email"];
		$priorapprover_id = $rowpriorapprover["ID"];

	//get information on action taken
	?>
	<table width="100%" border="1" cellpadding="10" bordercolor="#999999" bordercolorlight="#FFFFFF" bordercolordark="#FFFFFF"><tr><td>
	<table width="100%" border="1" cellspacing="0" cellpadding="3">
  <tr> 
    <td>Action already taken on this request.</td>
  </tr>
  <tr> 
    <td>By: <?=$priorapprover_fullname?> </td>
  </tr>
  <tr> 
    <td>On: <?=$row_order["opre_action_datetime"]?></td>
  </tr>
  <tr> 
    <td>Action: <?=$row_order["opre_isauthorized"]?></td>
  </tr>
  <tr>
    <td>Comments: <?=$row_order["opre_approver_comments"]?></td>
  </tr>
</table>
</td></tr></table>

<?
}
?>
<form action="<?$php_self?>" name="frm_order" method="post" target="_self">
<table class="links12_orderentry" border="0" cellspacing="2" cellpadding="5" width="100%" height="100%">
	<tr> 
		<td nowrap>
			<input type="radio" name="buy_sell" value="Buy" disabled <? if ($row_order['opre_buy_sell'] == 'Buy') echo ' checked'; ?> >Buy <br>
			<input type="radio" name="buy_sell" value="Sell" disabled <? if ($row_order['opre_buy_sell'] == 'Sell') echo ' checked'; ?> >Sell
		</td>
		<td nowrap>Quantity&nbsp;&nbsp;<input type="text" class="Text" name="quantity" value="<?=$row_order['opre_quantity']?>" readonly>&nbsp;&nbsp;of Symbol &nbsp;&nbsp;<input type="text" class="Text" name="symbol" value="<?=$row_order['opre_symbol']?>" readonly></td>
	</tr>
	
	<tr>
		<td colspan="2" nowrap><hr size="1" color="#999999"></td>
	</tr>

	
	<tr> 
		<td nowrap>Order Type: 
			<select name="type" class="Text" disabled>
			<option value=""><?=$row_type['otyp_option']?></option>
			</select>
		</td>
		
		<td nowrap>Price:&nbsp;&nbsp;
			<input type="text" class="Text" name="price" value="<?=$row_order['opre_price']?>" readonly>
		</td>
	</tr>
	
	<tr>
		<td colspan="2" nowrap><hr size="1" color="#999999"></td>
	</tr>

	<tr> 
		<td nowrap>Expiration: 
			<select name="expiration" class="Text" disabled>
			<option value=""><?=$row_expiration['oexp_option']?></option>
			</select>
		</td>
		
		<td nowrap>Special Instructions:
			<select name="instructions" class="Text" disabled>
			<option value=""><?=$row_instruction['oins_option']?></option>
			</select>
		</td>
	</tr>

	<tr>
		<td colspan="2" nowrap><hr size="1" color="#999999"></td>
	</tr>
 
	<tr> 
		<td>Comments:<br>(By <?=$user_fullname?>)</td>
		<td>
			<textarea name="comments" class="Textarea" cols="50" rows="3" <? if($flag == 1) echo ' readonly '; ?>><?=$row_order['opre_requestor_comments']?></textarea>
		</td>
	</tr>
	<tr> 
		<td>Comments:<br>(By <?=$approver_fullname?>)</td>
		<td>
			<textarea name="approver_comments" class="Textarea" cols="50" rows="3"></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" nowrap><hr size="1" color="#999999"></td>
	</tr>

	<tr valign="bottom">
		<td colspan="2" nowrap valign="bottom">
			<? 
			if($flag == 1)
			{
			?>
			<input type="hidden" name="authid" value="<?=$authid?>">
			<input type="submit" class="Submit" name="accept" value=" Accept " >&nbsp;&nbsp;
			<input type="submit" class="Submit" name="decline" value=" Decline ">&nbsp;&nbsp;
			
			<?
			}
			?>
			
			<input type="submit" class="Submit" name="close" value=" Close "  onClick="javascript:self.close()">
		</td>
	</tr>

</table>
</form>
<? table_end_percent(); ?>
</center>

</body>








