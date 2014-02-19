<?

/*
include('../includes/dbconnect.php');
include('../includes/global.php');
include('../includes/functions.php');
*/
	
	//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CREATE MECHANISM TO HANDLE IT.
	//$trade_date_to_process = format_date_ymd_to_mdy(previous_business_day());
	//$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2005-09-12';


//Get IPO Tickers in a array from the past 5 days
$ipo_tickers = mysql_query("SELECT ipo_symbol FROM IPO_info where ipo_isactive = 1 and ipo_date > DATE_ADD(now(), INTERVAL -7 DAY) ORDER BY ipo_symbol") or die (mysql_error());
$i = 0;
$arr_ipo_tickers = array();
	while ( $row = mysql_fetch_array($ipo_tickers) ) 
	{
		$arr_ipo_tickers[$i] = $row["ipo_symbol"];
		$i = $i+1;
	}
//print_r($arr_ipo_tickers);

//Format sql clauses
	$str_trdm_trade_date = " where trdm_trade_date = '". $trade_date_to_process ."'";
		  
	if ($trdm_symbol != '') { 
		$str_trdm_symbol = " and trdm_symbol = '".$trdm_symbol."'";
	} else {
		$str_trdm_symbol = " and trdm_symbol != '' and LENGTH(trdm_symbol) < 8";
	}			  
	
	if ($trdm_account_number != '') { 
		$str_trdm_account_number = " and trdm_account_number = '".$trdm_account_number."'";
	} else {
		$str_trdm_account_number = " and trdm_account_number not like '0000%'";
	}			  
	
	$query_statement = "SELECT 	trdm_auto_id, 
															trdm_account_number, 
															trdm_trade_date, 
															trdm_settle_date, 
															abs(round(trdm_quantity,0)) as 'trdm_quantity',
															round(trdm_price,2) as 'trdm_price',
															trdm_buy_sell,
															UPPER(trdm_symbol) as 'trdm_symbol',
															trdm_sec_description,
															trdm_trade_time
											 FROM Trades_m " . $str_trdm_trade_date . 
											 $str_trdm_symbol .
											 $str_trdm_account_number .
											 " ORDER BY trdm_symbol, trdm_trade_time";	
			
		echo $query_statement;
		//exit;
		$result_trades = mysql_query($query_statement) or die (mysql_error());

		$count_ipo_trades = 0;
		while ( $row_trades = mysql_fetch_array($result_trades) ) {
	 				
					if (in_array(strtoupper($row_trades["trdm_symbol"]), $arr_ipo_tickers)) {
					$count_ipo_trades = $count_ipo_trades + 1;
					} 
		}
		
		if ($count_ipo_trades > 0) {
		echo "There are ".$count_ipo_trades. " IPO trades.<br>";
		} else {
		echo "There are no IPO trades.<br>";
		}
		
		if ($count_ipo_trades > 0) {
		//table header here
		$reportmailbody .= '
			<tr><td colspan="9">&nbsp;</td></tr>
			<tr class="tableheading">
				<td colspan="9">IPO Trades</td>
			</tr>
			<tr class="tableheading"> 
				<td >Acct.</td>
				<td align="right">Symbol&nbsp;&nbsp;</td>
				<td >Description</td>
				<td >B/S</td>
				<td align="right">Qty.&nbsp;&nbsp;</td>
				<td align="right">Price&nbsp;&nbsp;</td>
				<td align="right">Total&nbsp;&nbsp;</td>
				<td align="right">Time&nbsp;&nbsp;</td>
				<td align="center" valign="middle" >NAME</td>
			</tr>';
				$result_trades = mysql_query($query_statement) or die (mysql_error());
				while ( $row_trades = mysql_fetch_array($result_trades) ) {
							if (in_array(strtoupper($row_trades["trdm_symbol"]), $arr_ipo_tickers)) {
							//table data here
							$reportmailbody .= '<tr class="tablerow">';
							$reportmailbody .= '<td nowrap>'.$row_trades["trdm_account_number"].'</td>
																	<td nowrap align="right">'.strtoupper($row_trades["trdm_symbol"]).'&nbsp;&nbsp;</td>
																	<td nowrap>'.$row_trades["trdm_sec_description"].'</td>
																	<td nowrap align="right">'.offset_buy_sell(convert_buy_sell($row_trades["trdm_buy_sell"])).'&nbsp;&nbsp;&nbsp;</td>
																	<td nowrap align="right">'.$row_trades["trdm_quantity"].'&nbsp;&nbsp;</td>
																	<td nowrap align="right">'.$row_trades["trdm_price"].'&nbsp;&nbsp;</td>
																	<td nowrap align="right">'.format_no_decimal_comma($row_trades["trdm_quantity"]*$row_trades["trdm_price"]).'&nbsp;&nbsp;</td>
																	<td nowrap align="right">'.$row_trades["trdm_trade_time"].'&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>';							
							} 
				}
		$reportmailbody .= '</table>';
		} else {
		$reportmailbody .= '<tr><td colspan="9">&nbsp;</td></tr>
			<tr><td colspan="9">&nbsp;</td></tr>
			<tr class="tableheading">
				<td colspan="9">No IPO Trades found.</td>
			</tr></table>';
		}
		
?>