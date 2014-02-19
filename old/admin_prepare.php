<a class="links10">
<?
////
// Prepare Data for Demo

//include('includes/functions.php');
//include('includes/dbconnect.php');
//include('includes/global.php'); 

echo "Previous business day = ".previous_business_day()."<br><br>";

$previous_business_day = previous_business_day();

////
// Set the date added for each stock in each list to 2 days prior to today 

$result = mysql_query("UPDATE adll_admin_list_lists SET adll_date_added = '".$previous_business_day."' WHERE adll_isactive = 1") or die (mysql_error());
echo "Watch, Gray, Restricted Lists updated successfully!<br>";						

$result = mysql_query("UPDATE lmkt_mktmaker_stock_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Market Maker List List updated successfully!<br>";						

$result = mysql_query("UPDATE lana_analyst_stock_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Analyst List List updated successfully!<br>";						

$result = mysql_query("UPDATE lban_banker_stock_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Banker List List updated successfully!<br>";						

$result = mysql_query("UPDATE lban_banker_stock_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Banker List List updated successfully!<br>";						

//// TRADES UPDATE
			
						echo "Inserting Trades for the last three months<br>";
						
						$i = 1;
						while ($i < 150) {

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
					 
					 	<?
						$trade_date_val = date("Y-m-d", time() - (60*60*24*($i-1)));
						$settle_date_val = dateplusxdays(3, $trade_date_val);
						?>
						
						Trade Date = <?=$trade_date_val?> Settle Date = <?=$settle_date_val?>
						<!-- <?=date("Y-m-d", time() - (60*60*24*($i-1)))?> => <?=date("m-d-Y", time() - (60*60*24*($i-1)))?> <br> -->
							
							
						<? 	$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$trade_date_val."'") or die (mysql_error());
								while ( $row = mysql_fetch_array($result_num_trades) ) {
								$numtrades_val = $row["numtrades"];
								}
								if ($numtrades_val == 0) {
								$randval = rand(0,9);
								
								$qry_statement = "update tradeset".$randval." set trdm_trade_date = '".$trade_date_val."', trdm_settle_date = '".$settle_date_val."'";
								//echo $qry_statement."<br>";
								$exec_query = mysql_query($qry_statement) or die (mysql_error());
								
								$qry_statement = "insert into Trades_m (trdm_order_id,trdm_account_number,trdm_buy_sell,trdm_quantity,trdm_symbol,trdm_sec_description,trdm_price,trdm_trade_date,trdm_settle_date,trdm_trade_time) SELECT trdm_order_id,trdm_account_number,trdm_buy_sell,trdm_quantity,trdm_symbol,trdm_sec_description,trdm_price,trdm_trade_date,trdm_settle_date,trdm_trade_time FROM tradeset".$randval;
								//echo $qry_statement."<br>";
								$exec_query = mysql_query($qry_statement) or die (mysql_error());
								echo "Trades entered for date ". $trade_date_val."<br>";
								} else {
								echo "Trades exist for ".$trade_date_val."<br>";
								}
						?>
						
						<?
						}						
						?>
						
						<?	
							
							$result_max_trade_date = mysql_query("SELECT max(trdm_trade_date) as 'trdm_trade_date' FROM Trades_m") or die (mysql_error());
							while ( $row = mysql_fetch_array($result_max_trade_date) ) {
							$max_trade_date = $row["trdm_trade_date"];
								}
							//echo $max_trade_date."<br>";
							$max_settle_date = dateplusxdays(3, $max_trade_date);
							
							$qry_statement = "update tradeset0 set trdm_trade_date = '".$max_trade_date."', trdm_settle_date = '".$max_settle_date."'"; 
							$exec_query = mysql_query($qry_statement) or die (mysql_error());
							
							$qry_statement = "delete from Trades_m where trdm_trade_date = '".$max_trade_date."' and trdm_auto_id != '250' and trdm_auto_id != '270' and trdm_auto_id != '375' and trdm_auto_id != '784'";
							$exec_query = mysql_query($qry_statement) or die (mysql_error());
							
							$qry_statement = "insert into Trades_m (trdm_order_id,trdm_account_number,trdm_buy_sell,trdm_quantity,trdm_symbol,trdm_sec_description,trdm_price,trdm_trade_date,trdm_settle_date,trdm_trade_time) SELECT trdm_order_id,trdm_account_number,trdm_buy_sell,trdm_quantity,trdm_symbol,trdm_sec_description,trdm_price,trdm_trade_date,trdm_settle_date,trdm_trade_time FROM tradeset0";
							echo "Latest trade date updated with demo trades.<br>";
							$exec_query = mysql_query($qry_statement) or die (mysql_error());
						
//// END TRADES UPDATE

?>
</a>
