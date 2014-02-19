<?php
/*
  $Id: accounts.php
  Copyright (c) 2004 CenterSys Group, Inc.
  http://www.centersysgroup.com
*/  

  include('top.php');
 
?>

<?
	if ($action == "add") {

		$result = mysql_query("insert into Employee_accounts(acct_rep,acct_number,acct_name1,acct_name2,acct_open_date) values('$acct_rep','$acct_number','$acct_name1','$acct_name2','$acct_open_date')") or $insert_error="There was a data entry error. Please reenter the values and try again."; //die (mysql_error());

		if ($insert_error) {
			$insert_status = $insert_error;
		} else {
			$insert_status = "&nbsp;&nbsp;Account added successfully!";
		}
			
?>

		<tr>
			<td align="left" valign="top" class="fieldlabel10"><BR><?=$insert_status?></td>
		</tr>

<?
	}
?>

<!-- 1 -->
		<tr>
			<td align="left" valign="top">
				<table class="tablewithdata" width="100%"  border="0" cellspacing="0" cellpadding="0">
					<tr><form action="accounts.php?action=add" id="addacct" method="post"> 
						<td width="80" align="left" valign="middle" class="links12">ADD Acct.</td>
						<td width="30" align="right" valign="middle" class="fieldlabel10">Rep.</td> 
						<td width="40" ><input class="Text" name="acct_rep" type="text" size="6" maxlength="6"></td>
						<td width="45" align="right" valign="middle" class="fieldlabel10">Acct. #</td> 
						<td width="40" ><input class="Text" name="acct_number" type="text" size="10" maxlength="10"></td>
						<td width="45" align="right" valign="middle" class="fieldlabel10">Name 1</td> 
						<td width="40" ><input class="Text" name="acct_name1" type="text" size="25" maxlength="30"></td>
						<td width="45" align="right" valign="middle" class="fieldlabel10">Name 2</td> 
						<td width="40" ><input class="Text" name="acct_name2" type="text" size="20" maxlength="30"></td>
						<td width="30" align="right" valign="middle" class="fieldlabel10">Date</td> 
						<td width="40" ><input class="Text" name="acct_open_date" type="text" size="11" maxlength="10" value="yyyy-mm-dd"></td>
						<td align="right"><input class="Submit" name="submit1" type="submit" value="  Save  "></td>
						</form>
					</tr>
				</table>
			</td>
		</tr>


		<tr valign="top">
			<td align="left" valign="top">
				<table width="100%" cellpadding="5" cellspacing="5" border="1">
					<tr valign="top">
						<td valign="top">

<?php
						include('includes/dbconnect.php');
						$date = date("Ymd");
						$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
		
						//$result = mysql_query("SELECT * FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());
						$result = mysql_query("SELECT list_id,list_symbol,list_description,list_date_added FROM lres_restricted_list where list_isactive = 1 ORDER BY list_symbol") or die (mysql_error());
?>
					
							<!-- Table with thin gray cell border -->
							<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
								<tr>
									<td>
										<table class="tablewithdata" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
											<tr class="tableheading12"> 
												<td width="25" height="25" align="center" valign="middle" >&nbsp;</td>
												<td width="25" height="25" align="center" valign="middle" >&nbsp;</td> 
												<td >Symbol</td>
												<td >Description</td>
												<td >Date Added</td>
												<td >Days on List</td>
												<td >Open Date</td>
											</tr>
					
<?
											while ( $row = mysql_fetch_array($result) ) {
											//list_id,list_symbol,list_description,list_date_added
?>

											<tr class="tablerow"> 
												<td><a href="action.php?Action=DeleteSingle&Name=<?=$row["list_symbol"]?>%20<?=$row["list_description"]?>&ID=<?=$row["list_id"]?>"><img src="images/delete.gif" alt="Delete"></a></td>
												<td><a href="action.php?Action=Edit&ID=<?=$row["list_id"]?>"><img src="images/edit.gif" alt="Edit"></a></td>
												<td><?=$row["list_symbol"]?></td>
												<td><?=$row["list_description"]?></td>
												<td><?=$row["list_date_added"]?></td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
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
			</td>
		</tr>

		<tr>
			<td align="center" valign="top">
			<p class="LocOps">

<?php
			$number = mysql_query("SELECT COUNT(*) FROM Employee_accounts") or die(mysql_error());
			$number_of_records = mysql_result($number, 0, 0);
			echo $number_of_records;
?>

			</td>
		</tr>




<?php
  include('bottom.php');
?>


