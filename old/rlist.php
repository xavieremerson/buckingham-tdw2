<?php

  include('top.php');
	 
	include('includes/functions.php'); 
	
	//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CREATE MECHANISM TO HANDLE IT.
	//$trade_date_to_process = format_date_ymd_to_mdy(previous_business_day());
	$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2004-02-20';
	
?>

<?
	if ($action == "add") {

		if ($symbol_list != NULL) {
		 
		 $symbol_list = strtoupper(str_replace(" ","",$symbol_list));
		
			$arr_symbol = explode(",",$symbol_list,30);
			
			for($i = 0; $i < count($arr_symbol); $i++) {
    		//echo $arr_symbol[$i];
				$company_name = get_company_name($arr_symbol[$i]);
				$query_statement = "insert into lres_restricted_list(list_symbol,list_description,list_added_by,list_date_added) values('$arr_symbol[$i]','$company_name','$user',now())";
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
		<tr>
			<td align="left" valign="top" class="fieldlabel10"><BR><?=$insert_status?></td>
		</tr>
<?
		} else {
?>
		<tr>
			<td align="left" valign="top" class="fieldlabel10"><BR>No data entered!</td>
		</tr>
<?
		}
	}
// END ADD SYMBOL
?>

<?
// BEGIN REMOVE SYMBOL
	if ($action == "remove") {

				$query_statement = "update lres_restricted_list set list_isactive = 0 where list_id = $ID";
				//echo $query_statement;
				$result = mysql_query($query_statement) or $insert_error="There was a recoverable data error. Please try again.<BR>".mysql_error(); //die (mysql_error());
				//mysql_query("SELECT list_id,list_symbol,list_description,list_date_added FROM lres_restricted_list where list_isactive = 1 ORDER BY list_symbol") or die (mysql_error());

			
			if ($insert_error) {
				$insert_status = $insert_error;
			} else {
				$insert_status = "&nbsp;&nbsp;Symbols removed from active list successfully!";
			}
			
?>
		<tr>
			<td align="left" valign="top" class="fieldlabel10"><BR><?=$insert_status?></td>
		</tr>
<?
		
	}


// END REMOVE SYMBOL
?>




<tr>
	<td align="right" valign="top">

		<? echo $table_start; ?><!-- class="tablewithdata" -->
		<table  width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td align="left" valign="middle" class="links12">&nbsp;</td>
				<td align="left" valign="middle" class="fieldlabel10">Enter single value or comma separated values.</td>
			</tr>
			<tr><form action="<?=$PHP_SELF?>?action=add" id="addsymbol" method="post"> 
				<td align="left" valign="middle" class="links12">ADD Equity Symbol</td>
				<td><input class="Text" name="symbol_list" type="text" size="60" maxlength="60">&nbsp;&nbsp;<input class="Submit" name="submit1" type="submit" value="  ADD  "></td>
				</form>
			</tr>
		</table>
		<? echo $table_end; ?>		
		<? echo $table_start; ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td>
<?php
			  include('includes/dbconnect.php');
			  $date = date("Ymd");
			  $lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());

			  $result = mysql_query("SELECT list_id,list_symbol,list_description,list_date_added, TO_DAYS(NOW()) - TO_DAYS(list_date_added) + 1 as 'list_days_on_list'  FROM lres_restricted_list where list_isactive = 1 ORDER BY list_symbol") or die (mysql_error());
?>
							<!-- Table with thin gray cell border -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td>
								<table class="tablewithdata" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
									<tr class="tableheading12"> 
										<td width="25" height="25" align="center" valign="middle" >&nbsp;</td>
										<td >Symbol</td>
										<td >Description</td>
										<td >Date Added</td>
										<td >Days on List</td>
									</tr>
					
<?
									while ( $row = mysql_fetch_array($result) ) {
?>

									<tr class="tablerow"> 								
  									<td><a href="<?=$PHP_SELF?>?action=remove&Name=<?=$row["list_symbol"]?>%20<?=$row["list_description"]?>&ID=<?=$row["list_id"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n<?=$row["list_symbol"]?>\nfrom the list?')"><img src="images/delete.gif" alt="Delete"></a></td>
										<td><?=$row["list_symbol"]?></td>
										<td><?=$row["list_description"]?></td>
										<td><?=$row["list_date_added"]?></td>
										<td><?=$row["list_days_on_list"]?></td>
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
		<? echo $table_end; ?>
	</td>
</tr>


<?php
  include('bottom.php');
?>




