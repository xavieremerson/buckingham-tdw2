<?
include('top.php');
include('includes/functions.php'); 
include('includes/dbconnect.php');
?>

<script language="javascript">
	
	function validate(form)
	{
		if(jpost.title.value == '')
		{
			alert('"Brief Description" is required !');
			return false;	
		}
		if(jpost.description.value == '')
		{
			alert('"Detailed Description" is required !');
			return false;	
		}
	}		
</script>

<?
$trade_date_to_process = previous_business_day();

$query_list_name = "SELECT usli_title_name FROM usli_user_lists WHERE usli_auto_id = '".$user_list_type."' AND usli_isactive = '1'";
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
			$query_statement = "INSERT INTO usll_user_list_lists(usll_user_id, usll_list_id, usll_symbol,usll_description,usll_added_by,usll_date_added) values('".$user_id."', '".$user_list_type."', '$arr_symbol[$i]','$company_name','$user_id',now())";
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
	$query_statement = "UPDATE usll_user_list_lists SET usll_isactive = 0 where usll_auto_id = $ID";
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

<tr valign="top">
	<td align="right" valign="top">
	
	<? echo $table_start; ?>
	<a class="links12"><?=$row_list_name['usli_title_name']?></a>
	<? echo $table_end; ?>
	
	<? echo $table_start; ?><!-- class="tablewithdata" -->
		<table  width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td align="left" valign="middle" class="links12">&nbsp;</td>
				<td align="left" valign="middle" class="fieldlabel10">Enter single value or comma separated values.</td>
			</tr>
			<tr>
				<form action="<?=$PHP_SELF?>?user_list_type=<?=$user_list_type?>&action=add" id="addsymbol" method="post"> 
				<td align="left" valign="middle" class="links12">ADD Equity Symbol</td>
				<td><input class="Text" name="symbol_list" type="text" size="60" maxlength="60">&nbsp;&nbsp;<input class="Submit" name="submit1" type="submit" value="  ADD  "></td>
				</form>
			</tr>
		</table>
		<? echo $table_end; ?>		
		<? echo $table_start; 
		$date = date("Ymd");
		$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
		$result = mysql_query("SELECT usll_auto_id,usll_symbol,usll_description, DATE_FORMAT(usll_date_added, '%m/%d/%y %h:%i %p') as usll_date_added, TO_DAYS(NOW()) - TO_DAYS(usll_date_added) + 1 as 'usll_days_on_list'  FROM usll_user_list_lists WHERE usll_list_id = '".$user_list_type."' AND usll_isactive = 1 ORDER BY usll_symbol") or die (mysql_error());
		
		if(mysql_num_rows($result) > 0)
		{
		?>

		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td>
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
										<td><a href="<?=$PHP_SELF?>?user_list_type=<?=$user_list_type?>&action=remove&Name=<?=$row["usll_symbol"]?>:<?=$row["usll_description"]?>&ID=<?=$row["usll_auto_id"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n<?=$row["usll_symbol"]?>\nfrom the list?')"><img src="images/delete.gif" alt="Delete"></a></td>
											<!-- Get quotes pop_quote.php?param_symbol=IBM -->
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["usll_symbol"]?>', 500, 200, false);"><?=$row["usll_symbol"]?></a></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["usll_description"]?></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["usll_date_added"]?></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["usll_days_on_list"]?></td>
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
		<? 
		}
		else
		{
			echo "<a class='links12'>Empty List!</a>";
		}
		echo $table_end; ?>
	</td>
</tr>
