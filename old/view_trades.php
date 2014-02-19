<?php

  include('top.php');
	 
	include('includes/functions.php'); 
?>

<?
//**********************************************************************
//Get Employee Accounts data in a local variable
$result = mysql_query("SELECT acct_number FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());

$i = 0;
$arr_accounts = array();

	while ( $row = mysql_fetch_array($result) ) {
 
 				$arr_accounts[$i] = $row["acct_number"];
				$i = $i+1;
	}
//print_r($arr_accounts);

//Get Employee Names on account
$result1 = mysql_query("SELECT acct_number, concat( acct_name1, ', ', acct_name2, ' <BR>(Rep: ', acct_rep, ')' ) as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());

$i = 0;
$arr_accountnames = array();

	while ( $row = mysql_fetch_array($result1) ) {
 
				$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
				$i = $i+1;
	}
//print_r($arr_accounts);

//***********************************************************************

?>

<tr><td align="left" valign="top">
<?
/*
if ($trda_trade_date != '') { echo $trda_trade_date; }
if ($trda_symbol != '') { echo $trda_symbol; }
if ($trda_acct_number != '') { echo $trda_acct_number; }
if ($emp_trades) { echo "Value of chkbox =". $emp_trades; }
*/
?>
</td></tr>



<tr><td align="right" valign="top">

				<table class="tablewithdata" width="100%"  border="0" cellspacing="0" cellpadding="0">
					<tr><form action="view_trades.php?action=filter" id="filtertrade" method="post"> 
						<td width="80" align="left" valign="middle" class="links12">Filter</td>
						<td width="500" align="right">
						<select class="Text" name="trda_trade_date" size="1" >
						<option value="">&nbsp;&nbsp;&nbsp;TRADE DATE&nbsp;&nbsp;&nbsp;</option>
						<option value="">==========</option>
						<?
						
						$i = 1;
						while ($i < 30) {

						$previoustime = time() - (60*60*24*$i);
						$previousday = date("Y-m-d", $previoustime);
 
 						if (date("l", $previoustime) == "Sunday") {
						$previoustime = time() - (60*60*24*($i+2));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+2 + 1;	
 							if ( check_holiday($previousday) == 1) {						
							$previoustime = time() - (60*60*24*($i));
							$previousday = date("Y-m-d", $previoustime);
							$i = $i+1;	
							}
						} elseif (date("l", $previoustime) == "Monday" and check_holiday($previousday) == 1) {
						$previoustime = time() - (60*60*24*($i+3));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+3 + 1;	
						} else {
						$previousday = "ERROR!";
						$i = $i+1;						
						}
  						
					 ?>
						<option value="<?=date("Y-m-d", time() - (60*60*24*($i-1)))?>"><?=date("m-d-Y", time() - (60*60*24*($i-1)))?></option>
						
						<?
						}						
						?>
						
						</select>
						
						</td>
						<td width="40" >
						
						<select class="Text" name="trda_symbol" size="1" >
						<option value="">&nbsp;&nbsp;&nbsp;SYMBOL&nbsp;&nbsp;&nbsp;</option>
						<option value="">==========</option>
						<?
						$query_statement = "SELECT distinct (trda_symbol)
																 FROM Trades_a 
																 where trda_trade_date > '". date("Y-m-d", time() - (60*60*24*30)) ."' 
																 and trda_symbol != ''
																 and trda_acct_number not like '0000%' 
																 ORDER BY trda_symbol";	

			      $result = mysql_query($query_statement) or die (mysql_error());
						
						while ( $row = mysql_fetch_array($result) ) {

					 ?>
						<option value="<?=$row["trda_symbol"]?>"><?=$row["trda_symbol"]?></option>
						<?
						}						
						?>
						
						</select>
						</td>
												<td width="40" >
						
						<select class="Text" name="trda_acct_number" size="1" >
						<option value="">&nbsp;&nbsp;&nbsp;ACCOUNT NUMBER&nbsp;&nbsp;&nbsp;</option>
						<option value="">============</option>
						<?
						$query_statement = "SELECT distinct (trda_acct_number)
																 FROM Trades_a 
																 where trda_trade_date > '". date("Y-m-d", time() - (60*60*24*30)) ."' 
																 and trda_symbol != ''
																 and trda_acct_number not like '0000%' 
																 ORDER BY trda_acct_number";	

			      $result = mysql_query($query_statement) or die (mysql_error());
						
						while ( $row = mysql_fetch_array($result) ) {

					 ?>
						<option value="<?=$row["trda_acct_number"]?>"><?=$row["trda_acct_number"]?></option>
						<?
						}						
						?>
						
						</select>
						</td>
						<td nowrap align="right"><input name="emp_trades" type="checkbox" value="1"><a class="fieldlabel10">Employee</a></td>
						<td align="right"><input class="fieldlabel10" name="submit1" type="submit" value="  Apply  "></td>
					</form></tr>
				</table>
				
</td></tr>

<tr><td align="left" valign="top">

<table width="100%" cellpadding="5" cellspacing="5" border="1">

	<tr valign="top">

		<td>

			<?php

			  include('includes/dbconnect.php');

			  $date = date("Ymd");

			  $lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());


				if ($trda_trade_date != '') { 
				$str_trda_trade_date = " where trda_trade_date = '". $trda_trade_date ."'";
				} else {
				$str_trda_trade_date = " where trda_trade_date = '". previous_business_day() ."'";
				}			  

				if ($trda_symbol != '') { 
				$str_trda_symbol = " and trda_symbol = '".$trda_symbol."'";
				} else {
				$str_trda_symbol = " and trda_symbol != ''";
				}			  

				if ($trda_acct_number != '') { 
				$str_trda_acct_number = " and trda_acct_number = '".$trda_acct_number."'";
				} else {
				$str_trda_acct_number = " and trda_acct_number not like '0000%'";
				}			  

/*			  if (!$SortBy) {

			    if (!$x) {

			      $result = mysql_query("SELECT * FROM Trades_a ORDER BY trda_trade_date LIMIT 0, 20") or die (mysql_error());

			    } else {  */
	
						$query_statement = "SELECT 	trda_auto_id, 
																				trda_acct_number, 
																				trda_trade_date, 
																				trda_settle_date, 
																				trda_as_of_date,
																				abs(round(trda_quantity,0)) as 'trda_quantity',
																				round(trda_price,2) as 'trda_price',
																				trda_buy_sell,
																				trda_cancel_rebill,
																				trda_net_amt,
																				trda_symbol,
																				trda_sec_desc_1,
																				trda_time_order_entry
																 FROM Trades_a " . $str_trda_trade_date . 
																 $str_trda_symbol .
																 $str_trda_acct_number .
																 "ORDER BY trda_symbol, trda_time_order_entry";	
						
						//where trda_trade_date = '". previous_business_day() ."' 
						//"and trda_symbol != ''
						//and trda_acct_number not like '0000%' 
						//echo $query_statement;
						//exit;
			      $result = mysql_query($query_statement) or die (mysql_error());

			   //}
					
					?>
					<!--Table with thin cell border-->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC"><tr><td>
					<table class="tablewithdata" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="1">

					<tr class="tableheading12"> 
						<td width="25" height="25" align="center" valign="middle" >&nbsp;</td>
						<td >Acct.</td>  
						<td >Symbol</td>
						<td >Description</td>
						<td >B/S</td>
						<td >Qty.</td>
						<td >Price</td>
						<td >Trade Date</td>
						<td >Time</td>
					</tr>
					
					<?
			    
					while ( $row = mysql_fetch_array($result) ) {

					?>

					<?
					if ($emp_trades != 1) {
							if (in_array($row["trda_acct_number"], $arr_accounts)) {
							echo '<tr class="tablerowhighlight">';
							} else {
							echo '<tr class="tablerow">';					
							}
							?>
							
							<td><?
							if (in_array($row["trda_acct_number"], $arr_accounts)) {
							echo '<img src="images/arrow_anim.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trda_acct_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
							} else {
							echo '&nbsp;';					
							}
							?>
							</td>
							
							<?
							if (in_array($row["trda_acct_number"], $arr_accounts)) {
							?>							
							<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trda_acct_number"]]?>','yellow', 300)"; onMouseout="hideddrivetip()"><?=$row["trda_acct_number"]?></td>
							<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trda_acct_number"]]?>','yellow', 300)"; onMouseout="hideddrivetip()"><?=$row["trda_symbol"]?></td>
							<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trda_acct_number"]]?>','yellow', 300)"; onMouseout="hideddrivetip()"><?=$row["trda_sec_desc_1"]?></td>
							<?
							} else {
							?>
							<td><?=$row["trda_acct_number"]?></td>
							<td><?=$row["trda_symbol"]?></td>
							<td><?=$row["trda_sec_desc_1"]?></td>
							<?
							}
							?>
							<td align="right"><?=$row["trda_buy_sell"]?>&nbsp;&nbsp;&nbsp;</td>
							<td align="right"><?=$row["trda_quantity"]?>&nbsp;&nbsp;</td>
							<td align="right"><?=$row["trda_price"]?>&nbsp;&nbsp;</td>
							<td align="right"><?=format_date_ymd_to_mdy($row["trda_trade_date"])?>&nbsp;&nbsp;</td>
							<td align="right"><?=format_time($row["trda_time_order_entry"])?>&nbsp;&nbsp;</td>
						</tr>
						
						<?

					} else {

							if (in_array($row["trda_acct_number"], $arr_accounts)) {
							echo '<tr class="tablerowhighlight">';
							?><td><?
							if (in_array($row["trda_acct_number"], $arr_accounts)) {
							echo '<img src="images/arrow_anim.gif" alt="Employee Trade">';
							} else {
							echo '&nbsp;';					
							}
							?>
							</td>
							
							<td><?=$row["trda_acct_number"]?></td>
							<td><?=$row["trda_symbol"]?></td>
							<td><?=$row["trda_sec_desc_1"]?></td>
							<td align="right"><?=$row["trda_buy_sell"]?>&nbsp;&nbsp;&nbsp;</td>
							<td align="right"><?=$row["trda_quantity"]?>&nbsp;&nbsp;</td>
							<td align="right"><?=$row["trda_price"]?>&nbsp;&nbsp;</td>
							<td align="right"><?=format_date_ymd_to_mdy($row["trda_trade_date"])?>&nbsp;&nbsp;</td>
							<td align="right"><?=format_time($row["trda_time_order_entry"])?>&nbsp;&nbsp;</td>
						</tr>
							<?
							} 
							?>
							
							
          <?
					
					}

					?>					

			<!--<? if ($row["acct_name2"] != ''){echo $row["acct_name2"];} else{ echo "&nbsp;";} ?>-->

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
  	</td></tr></table>
		<!--Table with thin cell border ends-->

  </td>

	</tr>

</table>

</td></tr>

<tr><td align="center" valign="top">

<p class="LocOps">

<?php

  $number = mysql_query("SELECT COUNT(*) FROM Trades_a") or die(mysql_error());


/*
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

  }*/

?>

</td></tr>

<!--<p class="LocOps"><a class="LocOps" href="addaccount.php">Add Account</a> : <?php if ($SortBy == "Name1") { ?><b>Sorted By Name1</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>?SortBy=Name1">Sort By Name1</a><?php } ?> : <?php if (!$SortBy) { ?><b>Sorted By Name2</b> <?php } else { ?><a class="LocOps" href="<?=$PHP_SELF?>">Sort By Name2</a><?php } ?></p>-->


<?php

  include('bottom.php');
	 
?>


