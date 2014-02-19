<?php
/*
  $Id: accounts.php
  Copyright (c) 2004 CenterSys Group, Inc.
  http://www.centersysgroup.com
*/  

  include('top.php');
	include('includes/functions.php');
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


<tr>
	<td align="left" valign="top">
		<table cellpadding="10" width="100%"><tr><td>
		<? table_start_percent(100, "Add Account"); ?>
				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
					<tr><form action="accounts.php?action=add" id="addacct" method="post"> 
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
						<!-- <td align="right"><input class="Submit" name="submit1" type="submit" value="  Save  "></td> -->
						<td align="right"><INPUT type=image name="submit1" src="images/submit_buttons/save_acct.jpg" border="0"></td>
					</form></tr>
				</table>
		<? table_end_percent(); ?>

		<br>

				<?php
					include('includes/dbconnect.php');
					$date = date("Ymd");
					$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
	
					if (!$SortBy) {
						if (!$x) {
							$result = mysql_query("SELECT * FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number LIMIT 0, 30") or die (mysql_error());
						} else {
							$result = mysql_query("SELECT * FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number LIMIT $x, 30") or die (mysql_error());
						}
				?>
					
					<!-- Table with thin gray cell border -->
					<? table_start_percent(100, "Accounts");?>
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td>
							<!-- table class commented out class="tablewithdata" -->
								<table class="sortable"  id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">
								<!--  -->
									<tr> 
										<td width="25" align="center" valign="middle"></td>
										<td width="25" align="center" valign="middle"></td> 
										<td width="100" align="left">Acct. Rep.</td>
										<td width="100" align="left">Acct. Number</td>
										<td width="200" align="left">Name 1</td>
										<td width="200" align="left">Name 2</td>
										<td align="left">Open Date</td>
									</tr>
									<?
			    				while ( $row = mysql_fetch_array($result) ) {
									?>
									<tr class="tablerow"> 
										<!-- <td><a href="action.php?Action=DeleteSingle&Name=<?=$row["acct_name1"]?>%20<?=$row["acct_name2"]?>&ID=<?=$row["acct_auto_id"]?>"><img src="images/delete.gif" alt="Delete"></a></td> -->
										<!-- <a href="javascript:CreateWnd('pop_acctdel.php?Name=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"> -->
										<td><a href="javascript:CreateWnd('pop_acctdel.php?Action=Delete&Name=<?=$row["acct_name1"]?>%20:%20<?=$row["acct_name2"]?>&ID=<?=$row["acct_auto_id"]?>', 360, 80, false);"><img src="images/delete.gif" alt="Delete"></a></td>
										
										<!-- <td><a href="action.php?Action=Edit&ID=<?=$row["acct_auto_id"]?>"><img src="images/edit.gif" alt="Edit"></a></td> -->
										<td><a href="javascript:CreateWnd('pop_acctedit.php?Action=Edit&ID=<?=$row["acct_auto_id"]?>', 360, 235, false);"><img src="images/edit.gif" alt="Edit"></a></td>
										<td><?=$row["acct_rep"]?></td>
										<td><?=$row["acct_number"]?></td>
										<td><?=$row["acct_name1"]?></td>
										<td><? if ($row["acct_name2"] != ''){echo $row["acct_name2"];} else{ echo "&nbsp;";} ?></td>
										<td><?=$row["acct_open_date"]?></td>
									</tr>
								<?php
						    }
								//Sorting by scenario
							  } else {
		
							    if (!$x) {
						      $result = mysql_query("SELECT * FROM tbl_$user ORDER BY '$SortBy' LIMIT 0, 30") or die (mysql_error());
							    } else {
						      $result = mysql_query("SELECT * FROM tbl_$user ORDER BY '$SortBy' LIMIT $x, 30") or die (mysql_error());
							    }

								?>
					
								<table width="100%"  border="1" cellspacing="0" cellpadding="2">
									<tr class="tableheading12"> 
										<td width="25" align="center" valign="middle" ></td>
										<td width="25" align="center" valign="middle" ></td>
										<td width="100" align="left">Acct. Rep.</td>
										<td width="100" align="left">Acct. Number</td>
										<td width="200" align="left">Name 1</td>
										<td width="200" align="left">Name 2</td>
										<td align="left">Open Date</td>
									</tr>
					
								<?
			    			while ( $row = mysql_fetch_array($result) ) {
								?>

								<tr> 
									<td><a href="action.php?Action=DeleteSingle&Name=<?=$row["acct_name1"]?>%20<?=$row["acct_name2"]?>&ID=<?=$row["acct_auto_id"]?>"><img src="images/delete.gif" alt="Delete"></a></td>
									<td><a href="action.php?Action=Edit&ID=<?=$row["acct_auto_id"]?>"><img src="images/edit.gif" alt="Edit"></a></td> 
									<td>test<?=$row["acct_rep"]?></td>
									<td><?=$row["acct_number"]?></td>
									<td><?=$row["acct_name1"]?></td>
									<td><? if ($row["acct_name2"] != ''){echo $row["acct_name2"];} else{ echo "&nbsp;";} ?></td>
									<td><?=$row["acct_open_date"]?></td>
								</tr>
				
								<?php
							    }
							  }
								?>

							</table>

							<script language="JavaScript">
							<!--
								///////////////////////tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
								tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
							// -->
							</script>
			
			  		</td>
					</tr>
				</table>
				<? table_end_percent();?>
				<!--Table with thin cell border ends-->
				</td></tr></table>

	</td>
</tr>

<tr>
	<td align="center" valign="top">
		<p class="LocOps">
		<?php
	  $number = mysql_query("SELECT COUNT(*) FROM Employee_accounts") or die(mysql_error());
	  if (!$SortBy) {

    if ($x < 30) {

      echo("<< Previous 30");

    } else {

      echo("<a class=\"LocOps\" href=\"$PHP_SELF?x=" . ($x - 30) . "\"><< Previous 30</a>");

    }

    if (($x + 30) >= mysql_result($number, 0, 0)) {

      echo("&nbsp;: Next 30 >></p>");

    } else {

      echo("&nbsp;: <a class=\"LocOps\" href=\"$PHP_SELF?x=" . ($x + 30) . "\">Next 30 >></a></p>");

    }

  } elseif ($SortBy == "Firstname") {

    if ($x < 30) {

      echo("<< Previous 30");

    } else {

      echo("<a class=\"LocOps\" href=\"$PHP_SELF?SortBy=Firstname&x=" . ($x - 30) . "\"><< Previous 30</a>");

    }

    if (($x + 30) >= mysql_result($number, 0, 0)) {

      echo("&nbsp;: Next 30 >></p>");

    } else {

      echo("&nbsp;: <a class=\"LocOps\" href=\"$PHP_SELF?SortBy=Firstname&x=" . ($x + 30) . "\">Next 30 >></a></p>");

    }

  }

?>

</td></tr>

<!--<p class="LocOps"><a class="LocOps" href="addaccount.php">Add Account</a> : <?php if ($SortBy == "Name1") { ?><b>Sorted By Name1</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>?SortBy=Name1">Sort By Name1</a><?php } ?> : <?php if (!$SortBy) { ?><b>Sorted By Name2</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>">Sort By Name2</a><?php } ?></p>-->


<?php

  include('bottom.php');
	 
?>


