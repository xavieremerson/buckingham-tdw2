<?php

  include('top.php');
	 
	include('includes/functions.php'); 
	
	//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CREATE MECHANISM TO HANDLE IT.
	//$trade_date_to_process = format_date_ymd_to_mdy(previous_business_day());
	$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2004-02-20';

	//====================================================================	
	function get_emp_name_by_id ($emp_id) {

	$result = mysql_query("SELECT acct_name1 from Employee_accounts where acct_auto_id = $emp_id") or $emp_name = "Undefined!"; //die (mysql_error());

		while ($row = mysql_fetch_array($result)) {
		$emp_name = $row["acct_name1"];		
		}	
		return $emp_name;
	}
	
	//====================================================================
	
	if ($list_type == "m"){
	$list_table = "lmkt_mktmaker_stock_list";
	$page_label = "Market Maker Stock List";
	} elseif ($list_type == "b"){
	$list_table = "lban_banker_stock_list";
	$page_label = "Banker Stock List";
	} elseif ($list_type == "a"){
	$list_table = "lana_analyst_stock_list";
	$page_label = "Analyst Stock List";
	} else {
	echo "Stock List Type not defined. Please contact Technical Support!";
	$page_label = "Not Defined! Please contact Technical Support!";
	exit;
	}
	
	
?>

<?
	if ($action == "add") {

		if ($symbol_list != NULL) {
		 
		 $symbol_list = strtoupper(str_replace(" ","",$symbol_list));
		
			$arr_symbol = explode(",",$symbol_list,30);
			
			for($i = 0; $i < count($arr_symbol); $i++) {
    		//echo $arr_symbol[$i];
				$company_name = get_company_name($arr_symbol[$i]);
				$query_statement = "insert into $list_table(list_symbol, acct_auto_id, list_description,list_added_by,list_date_added) values('$arr_symbol[$i]','$acct_auto_id','$company_name','$user',now())";
				//echo $query_statement;
				$result = mysql_query($query_statement) or $insert_error="There was a data entry error. Please reenter the values and try again.<BR>".mysql_error(); //die (mysql_error());
				//mysql_query("SELECT list_id,list_symbol,list_description,list_date_added FROM lres_restricted_list where list_isactive = 1 ORDER BY list_symbol") or die (mysql_error());
			}
			
			if ($insert_error) {
				$insert_status = $insert_error;
			} else {
				$insert_status = "&nbsp;&nbsp;Symbols added successfully!";
			}
			
?>
     <?  echo sys_message(1, $insert_status); ?>
<?
		} else {
?>
		<?  echo sys_message(3, "No Data Entered!");  ?>
<?
		}
	}
// END ADD SYMBOL
?>

<?
// BEGIN REMOVE SYMBOL
	if ($action == "remove") {

				$query_statement = "update $list_table set list_isactive = 0 where list_id = $ID";
				//echo $query_statement;
				$result = mysql_query($query_statement) or $insert_error="There was a recoverable data error. Please try again.<BR>".mysql_error(); //die (mysql_error());
				//mysql_query("SELECT list_id,list_symbol,list_description,list_date_added FROM lres_restricted_list where list_isactive = 1 ORDER BY list_symbol") or die (mysql_error());

			
			if ($insert_error) {
				$insert_status = $insert_error;
			} else {
				$insert_status = "&nbsp;&nbsp;Symbols removed from active list successfully!";
			}
			
?>
			<?  echo sys_message(2, $insert_status);  ?>
<?
		
	}


// END REMOVE SYMBOL
?>

<tr>
	<td align="right" valign="top">
	
		<table cellpadding="10" width="100%"><tr><td>
	
		<? table_start_percent(100, "Add to ".$page_label); ?><!-- class="tablewithdata" -->
		<table  width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td align="left" valign="middle" class="fieldlabel10">&nbsp;&nbsp;Enter single value or comma separated values.</td>
				<td align="left" valign="middle" class="fieldlabel10">&nbsp;</td>
			</tr>
			<tr><form action="<?=$PHP_SELF?>?list_type=<?=$list_type?>&action=add" id="addsymbol" method="post"> 
				<td align="left" valign="middle">&nbsp;&nbsp;<input class="Text" name="symbol_list" type="text" size="60" maxlength="60">&nbsp;&nbsp;</td>
				<td align="left" valign="bottom" class="links12">
				<select class="Text" name="acct_auto_id" size="1" >
						<option value="">&nbsp;&nbsp;&nbsp;Select Name&nbsp;&nbsp;&nbsp;</option>
						<option value="">==========</option>
						<?
						$query_statement = "SELECT acct_auto_id, concat(UPPER(acct_name1), ' (', acct_number , ')') as 'acct_name1'
																 FROM Employee_accounts 
																 where acct_is_active = 1
																 ORDER BY acct_name1";	

			      $result = mysql_query($query_statement) or die (mysql_error());
						
						while ( $row = mysql_fetch_array($result) ) {

					 ?>
						<option value="<?=$row["acct_auto_id"]?>"><?=$row["acct_name1"]?></option>
						<?
						}						
						?>
						&nbsp;&nbsp;<input class="Submit" name="submit1" type="submit" value="  ADD  ">
				</td>
				</form>
			</tr>
		</table>
	<? table_end_percent();?>
	<br>
		<? table_start_percent(100,$page_label); ?><!-- class="tablewithdata" -->
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td>
<?php
			  include('includes/dbconnect.php');
			  $date = date("Ymd");
			  $lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
				$query_stmt = "SELECT list_id, acct_auto_id, list_symbol,list_description,list_date_added, TO_DAYS(NOW()) - TO_DAYS(list_date_added) + 1 as 'list_days_on_list'  FROM $list_table where list_isactive = 1 ORDER BY list_symbol";
				//echo $query_stmt;
			  $result = mysql_query($query_stmt) or die (mysql_error());
?>
							<!-- Table with thin gray cell border -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td>
								<table class="sortable"  id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
									<tr class="tableheading12"> 
										<td width="25" align="center" valign="middle" >&nbsp;</td>
										<td width="80">Symbol</td>
										<td width="250">Description</td>
										<td width="250">Account Name</td>
										<td width="150">Date Added</td>
										<td >Days on List</td>
									</tr>
					
<?
									while ( $row = mysql_fetch_array($result) ) {
?>

									<tr class="tablerow"> 								
  									<td><a href="<?=$PHP_SELF?>?list_type=<?=$list_type?>&action=remove&Name=<?=$row["list_symbol"]?>:<?=$row["list_description"]?>&ID=<?=$row["list_id"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n<?=$row["list_symbol"]?>\nfrom the list?')"><img src="images/delete.gif" alt="Remove <?=$row["list_symbol"]?> from active list."></a></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["list_symbol"]?>', 500, 200, false);"><?=$row["list_symbol"]?></a></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["list_description"]?></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=get_emp_name_by_id($row["acct_auto_id"])?></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["list_date_added"]?></td>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["list_days_on_list"]?></td>
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
	<? table_end_percent();?>
	</td></tr></table>
	</td>
</tr>


<?php
  include('bottom.php');
?>




