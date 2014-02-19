<a class="links10">
<?
////
// User Admin for DEMO

$result = mysql_query("SELECT * FROM Users where user_isactive = 1 ORDER BY acct_number LIMIT 0, 20") or die (mysql_error());

	include('includes/functions.php');
  include('includes/dbconnect.php');
  include('includes/global.php'); 

echo "Previous business day = ".previous_business_day()."<br><br>";

$previous_business_day = previous_business_day();

////
// Set the date added for each stock in each list to 2 days prior to today

$result = mysql_query("UPDATE lres_restricted_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Restricted List updated successfully!<br>";						

$result = mysql_query("UPDATE lwat_watch_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Watch List updated successfully!<br>";						

$result = mysql_query("UPDATE lgry_gray_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Gray List updated successfully!<br>";						

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
						while ($i < 90) {

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
							
							$qry_statement = "delete from Trades_m where trdm_trade_date = '".$max_trade_date."'";
							$exec_query = mysql_query($qry_statement) or die (mysql_error());
							
							$qry_statement = "insert into Trades_m (trdm_order_id,trdm_account_number,trdm_buy_sell,trdm_quantity,trdm_symbol,trdm_sec_description,trdm_price,trdm_trade_date,trdm_settle_date,trdm_trade_time) SELECT trdm_order_id,trdm_account_number,trdm_buy_sell,trdm_quantity,trdm_symbol,trdm_sec_description,trdm_price,trdm_trade_date,trdm_settle_date,trdm_trade_time FROM tradeset0";
							echo "Latest trade date updated with demo trades.<br>";
							$exec_query = mysql_query($qry_statement) or die (mysql_error());
						
//// END TRADES UPDATE


//***************************************************************************************************************************
/*
//update trade table to show trade dates relevant to demo

$result = mysql_query("SELECT max( trdm_trade_date ) as 'maxdate' FROM Trades_m") or die (mysql_error());
while ( $row = mysql_fetch_array($result) ) {
$maxdate = $row["maxdate"];
}					  

echo "Most recent trade date in trade data table was ".$maxdate."<br>";

//echo "The difference between the max date in trade table and the previous business day is : ".datediff($maxdate, previous_business_day());

$datediffval = datediff($maxdate, previous_business_day());
 
//echo "<br>max date + ".$datediffval." days = ".dateplusxdays($datediffval,$maxdate);



$trows = 0;
$result = mysql_query("SELECT trdm_auto_id, trdm_trade_date, trdm_settle_date FROM Trades_m") or die (mysql_error());

while ( $row = mysql_fetch_array($result) ) {
		$trdm_auto_id = $row["trdm_auto_id"];
		$trdm_trade_date = $row["trdm_trade_date"];
		$trdm_settle_date = $row["trdm_settle_date"];

		$new_trade_date = dateplusxdays($datediffval, $trdm_trade_date);
		$new_settle_date = dateplusxdays(3, dateplusxdays($datediffval, $trdm_trade_date));

		$qryval = "UPDATE Trades_m set trdm_trade_date ='".$new_trade_date."', trdm_settle_date = '".$new_settle_date."' where trdm_auto_id =".$trdm_auto_id;
		//echo "<BR>". $qryval;
		$resultupdate = mysql_query($qryval) or die (mysql_error());
		//echo "<BR>updated!<BR>";
		$trows = $trows + 1;
		
}				

echo $trows . " rows updated in the Trades Tables.";
*/
//***************************************************************************************************************************

/*

// THIS IS USED TO GET THE DEMO TRADE DATA UPDATED WITH DATA WITH THE FOLLOWING TICKER SET.

$arr_symbols = array("CA", "IBM","FD","MSFT","YHOO","BGO","XRX","CORV","LVLT","BRCD",
										"INTC","CSCO","SUNW","AMAT","ORCL","BEAS","SIRI","JDSU","MLNM","JNPR",
										"DELL","GILD","ISON","TASR","NXTL", "ATML", "LEXR","ADCT","CIEN","SPLS",
										"FIC","FARM","FNM","FDO","HRB","JHF","JCOM","KSU","KDN","KBH",
										"ALSM","EFII","PCLE","XATA","ADS","ARBA","ARB","ADS","CITI","CKFR");

for ($i=0;$i < 50; $i++) {

$arr_name[$i] = get_company_name($arr_symbols[$i]);
$arr_price[$i] = getpricefromyahoo($arr_symbols[$i]);										

}										

$result = mysql_query("SELECT trdm_auto_id FROM Trades_m") or die (mysql_error());
$trows = 0;
while ( $row = mysql_fetch_array($result) ) {
		$trdm_auto_id = $row["trdm_auto_id"];

		$rand_a = rand(1,50);
		$rand_b = rand(1,500)*100;
			
		$qryval = "UPDATE Trades_m set trdm_symbol = '".$arr_symbols[$rand_a]."', trdm_sec_description ='".$arr_name[$rand_a]."', trdm_price = '".$arr_price[$rand_a]."', trdm_quantity = ".$rand_b." WHERE trdm_auto_id =".$trdm_auto_id;
		echo "<BR>". $qryval;
		$resultupdate = mysql_query($qryval) or die (mysql_error());
		echo "<BR>updated row $trows!<BR>";
		$trows = $trows + 1;
		
}


*/									
?>
</a>
