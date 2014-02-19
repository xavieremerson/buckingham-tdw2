<?php

  include('top.php');
	 
?>

<tr><td>
<table width="100%" border="1" align="left" cellpadding="2" cellspacing="1" bordercolor="#0000FF" bordercolorlight="#6699FF" bordercolordark="#FF0066" bgcolor="#999999"><tr><td>
<table width="100%" border="0" cellpadding="0" cellspacing="1" >
  <tr> 
    <td>Filter</td>
    <td>Account Number:</td>
    <td><select name="filter_acct" size="1">&nbsp;</select></td>
    <td>Symbol:</td>
    <td><select name="filter_symbol" size="1">
		
		<? 
		$result = mysql_query("SELECT distinct trda_symbol FROM Trades_a ORDER BY trda_symbol") or die (mysql_error());
		
		 while ( $row = mysql_fetch_array($result) ) {

		?>
		<option value="<?=$row["trda_symbol"]?>"><?=$row["trda_symbol"]?></option>		
		<?
		}
		?>

		</select>
		
		</td>
		<td>Trade Date:</td>
    <td><select name="filter_trade_date" size="1">
		
		<? 
		$result = mysql_query("SELECT distinct trda_trade_date FROM Trades_a ORDER BY trda_trade_date desc") or die (mysql_error());
		
		 while ( $row = mysql_fetch_array($result) ) {

		?>
		<option value="<?=$row["trda_trade_date"]?>"><?=$row["trda_trade_date"]?></option>		
		<?
		}
		?>

		</select>
		
		</td>
    <td>Employee Accts:</td>
    <td><input name="filter_emp_accts" type="checkbox" value="0"></td>
  </tr>
</table>
</td></tr></table>


</td></tr>



<tr><td align="left" valign="top">

<table width="100%" cellpadding="5" cellspacing="5" border="1">

	<tr valign="top">

		<td>

			<?php

			  include('includes/dbconnect.php');

			  $date = date("Ymd");

			  $lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());



			  if (!$SortBy) {

			    if (!$x) {

			      $result = mysql_query("SELECT * FROM Trades_a ORDER BY trda_trade_date LIMIT 0, 20") or die (mysql_error());

			    } else {

			      $result = mysql_query("SELECT * FROM Trades_a ORDER BY trda_trade_date LIMIT $x, 20") or die (mysql_error());

			    }
					
					?>
					
					<table class="tablewithdata" id="accounts_table"  width="100%"  border="1" cellspacing="0" cellpadding="1">

					<tr class="tableheading12"> 
						<td width="25" height="25" align="center" valign="middle" >&nbsp;</td>
						<td >Acct.</td> 
						<td >Symbol</td>
						<td >Description</td>
						<td >B/S</td>
						<td >Qty.</td>
						<td >Price</td>
						<td >Trade Date</td>
						<td >Settle Date</td>
					</tr>
					
					<?
			    while ( $row = mysql_fetch_array($result) ) {

			?>

			  <tr class="tablerow"> 
					<td><a href="action.php?Action=DeleteSingle&Name=<?=$row["acct_name1"]?>%20<?=$row["acct_name2"]?>&ID=<?=$row["acct_auto_id"]?>"><img src="images/delete.gif" alt="Delete"></a></td>
					<td><?=$row["trda_acct_number"]?></td>
					<td><?=$row["trda_symbol"]?></td>
					<td><?=$row["trda_sec_desc_1"]?></td>
					<td><?=$row["trda_buy_sell"]?></td>
					<td><?=$row["trda_quantity"]?></td>
					<td><?=$row["trda_price"]?></td>
					<td><?=$row["trda_trade_date"]?></td>
					<td><?=$row["trda_settle_date"]?></td>
				</tr>

			<!--<? if ($row["acct_name2"] != ''){echo $row["acct_name2"];} else{ echo "&nbsp;";} ?>-->

			<?php
			   
			    }
					
					
					
					
					//Sorting by scenario

			  } else {

			    if (!$x) {

			      $result = mysql_query("SELECT * FROM tbl_$user ORDER BY '$SortBy' LIMIT 0, 20") or die (mysql_error());

			    } else {

			      $result = mysql_query("SELECT * FROM tbl_$user ORDER BY '$SortBy' LIMIT $x, 20") or die (mysql_error());

			    }

					?>
					
					<table width="100%"  border="1" cellspacing="0" cellpadding="2">

					<tr class="tableheading12"> 
						<td width="25" height="25" align="center" valign="middle" ></td>
						<td width="25" height="25" align="center" valign="middle" ></td>
						<td >Acct. Rep.</td>
						<td >Acct. Number</td>
						<td >Name 1</td>
						<td >Name 2</td>
						<td >Open Date</td>
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
				//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
				tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
			// -->
			</script>

  </td>

	</tr>

</table>

</td></tr>

<tr><td align="center" valign="top">

<p class="LocOps">

<?php

  $number = mysql_query("SELECT COUNT(*) FROM Trades_a") or die(mysql_error());



  if (!$SortBy) {

    if ($x < 20) {

      echo("<< Previous 20");

    } else {

      echo("<a class=\"LocOps\" href=\"$PHP_SELF?x=" . ($x - 20) . "\"><< Previous 20</a>");

    }

    if (($x + 20) >= mysql_result($number, 0, 0)) {

      echo("&nbsp;: Next 20 >></p>");

    } else {

      echo("&nbsp;: <a class=\"LocOps\" href=\"$PHP_SELF?x=" . ($x + 20) . "\">Next 20 >></a></p>");

    }

  } elseif ($SortBy == "Firstname") {

    if ($x < 20) {

      echo("<< Previous 20");

    } else {

      echo("<a class=\"LocOps\" href=\"$PHP_SELF?SortBy=Firstname&x=" . ($x - 20) . "\"><< Previous 20</a>");

    }

    if (($x + 20) >= mysql_result($number, 0, 0)) {

      echo("&nbsp;: Next 20 >></p>");

    } else {

      echo("&nbsp;: <a class=\"LocOps\" href=\"$PHP_SELF?SortBy=Firstname&x=" . ($x + 20) . "\">Next 20 >></a></p>");

    }

  }

?>

</td></tr>

<!--<p class="LocOps"><a class="LocOps" href="addaccount.php">Add Account</a> : <?php if ($SortBy == "Name1") { ?><b>Sorted By Name1</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>?SortBy=Name1">Sort By Name1</a><?php } ?> : <?php if (!$SortBy) { ?><b>Sorted By Name2</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>">Sort By Name2</a><?php } ?></p>-->


<?php

  include('bottom.php');
	 
?>


