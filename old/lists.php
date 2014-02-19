<?php
include('top.php');
include('includes/functions.php'); 
include('includes/dbconnect.php');

?>
<script language=JavaScript>
<!-- Script courtesy of CenterSys
function clear_textbox()
{
if (document.enterval.symbol_list.value == "Enter single value or comma separated values.")
document.enterval.symbol_list.value = "";
}
-->
</script> 

<?




$trade_date_to_process = previous_business_day();

	$query_list_name = "SELECT alis_title_name FROM alis_admin_lists WHERE alis_auto_id = '".$list_type."' AND alis_isactive = '1'";
	$result_list_name = mysql_query($query_list_name) or die(mysql_error());
	$row_list_name = mysql_fetch_array($result_list_name);
	
	if ($action == "add") 
	{
	
		if ($symbol_list != NULL) 
		{
			$symbol_list = strtoupper(str_replace(" ","",$symbol_list));
			$arr_symbol = explode(",",$symbol_list,30);
	
			for($i = 0; $i < count($arr_symbol); $i++) 
			{
				//echo $arr_symbol[$i];
				$company_name = get_company_name($arr_symbol[$i]);
				$query_statement = "insert into adll_admin_list_lists(adll_id, adll_symbol, adll_description, adll_added_by, adll_date_added) values('".$list_type."', '$arr_symbol[$i]','$company_name','$user_id',now())";
				//echo $query_statement;
				$result = mysql_query($query_statement) or $insert_error="There was a data entry error. Please reenter the values and try again.<BR>".mysql_error(); //die (mysql_error());
			}
	
			if ($insert_error) 
			{
				$insert_status = $insert_error;
			} 
			else 
			{
				$insert_status = "&nbsp;&nbsp;Symbols added successfully!";
			}
				
			echo sys_message(1, $insert_status); 
		} 
		else 
		{
			echo sys_message(3, "No Data Entered!");  
		}
	}
	// END ADD SYMBOL
	
	// BEGIN REMOVE SYMBOL
	if ($action == "remove") 
	{
		$query_statement = "UPDATE adll_admin_list_lists SET adll_isactive = 0 where adll_auto_id = $ID";
		$result = mysql_query($query_statement) or $insert_error="There was a recoverable data error. Please try again.<BR>".mysql_error(); //die (mysql_error());
	
		if ($insert_error)
		{
			$insert_status = $insert_error;
		} 
		else 
		{
			$insert_status = "&nbsp;&nbsp;Symbols removed from active list successfully!";
		}
	
		echo sys_message(2, $insert_status); 
	}
	// END REMOVE SYMBOL
	?>
	
	<tr>
		<td align="right" valign="top">
		
		<table width="100%" cellpadding="4"><tr><td>
	
			<?
			table_start_percent(100, "Add to ".$row_list_name['alis_title_name']);
			?>
		
			<table  width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<form name="enterval" action="<?=$PHP_SELF?>?list_type=<?=$list_type?>&action=add" id="addsymbol" method="post"> 
					<td align="left">&nbsp;&nbsp;<input class="Text" name="symbol_list" type="text" size="60" maxlength="60" onFocus=clear_textbox() value="Enter single value or comma separated values.">&nbsp;&nbsp;<INPUT type=image name="submit1" src="images/submit_buttons/add.jpg" border="0"></td>
					</form>
					<td><a href="javascript:CreateWnd('pop_sendlist.php?list_type=<?=$list_type?>&action=email', 500, 200, false);">Email List</a></td>
				</tr>
			</table>
			<? table_end_percent(); ?> 
			<br>
			<?
			table_start_percent(100, $row_list_name['alis_title_name']);
			?>
		
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr valign="top">
					<td>
						<?php			
						$date = date("Ymd");
						$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
						$result = mysql_query("SELECT adll_auto_id,adll_symbol,adll_description, DATE_FORMAT(adll_date_added, '%m/%d/%y %h:%i %p') as adll_date_added, TO_DAYS(NOW()) - TO_DAYS(adll_date_added) + 1 as 'adll_days_on_list'  FROM adll_admin_list_lists WHERE adll_id = '".$list_type."' AND adll_isactive = 1 ORDER BY adll_symbol") or die (mysql_error());
						?>
						<!-- Table with thin gray cell border -->
						<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
							<tr>
								<td>
									<table class="sortable" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
										<tr class="tableheading12"> 
											<td width="25" align="center" valign="middle" >&nbsp;</td>
											<td width="80">Symbol</td>
											<td width="250">Description</td>
											<td width="150">Date Added</td>
											<td align="left">Days on List</td>
										</tr>
						
										<?
										while ( $row = mysql_fetch_array($result) ) 
										{
										?>
				
										<tr class="tablerow"> 								
											<td><a href="<?=$PHP_SELF?>?list_type=<?=$list_type?>&action=remove&Name=<?=$row["adll_symbol"]?>:<?=$row["adll_description"]?>&ID=<?=$row["adll_auto_id"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n<?=$row["adll_symbol"]?>\nfrom the list?')"><img src="images/delete.gif" alt="Delete"></a></td>
												<!-- Get quotes pop_quote.php?param_symbol=IBM -->
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["adll_symbol"]?>', 500, 200, false);"><?=$row["adll_symbol"]?></a></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["adll_description"]?></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["adll_date_added"]?></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["adll_days_on_list"]?></td>
										</tr>
										<?php
										}
										?>
									</table>
			
									<script language="JavaScript">
									<!--
									//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
									tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
									// -->
									</script>
								</td>
							</tr>
						</table>
						<!--Table with thin cell border ends-->
					</td>
				</tr>
			</table>
			<? 	table_end_percent(); ?>
			</td></tr></table>
			
		</td>
	</tr>

<?
include('bottom.php');
?>




