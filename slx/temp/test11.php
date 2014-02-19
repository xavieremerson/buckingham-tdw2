

<?php

function zzz() {

	include('includes/functions.php');
	include('includes/dbconnect.php');
		
	//$fp = fopen('/var/www/html/dev_demo_compliance/includes/zzztest.js', "w"); 
	//fputs ($fp, $pre_content);
	
	fputs ($fp, "['Employee Trades',0,{'tt':'Methods of the window object','sb':'Methods of the window object'},");
		//	['open'],


	
	 
	//Date in YYYY-MM-DD Format
	$trade_date_to_process = previous_business_day();

////
// Check if trades exist and only then begin processing
// If trades do not exist, there has been one or more errors in the import/upload of trades

	$result_num_trades = mysql_query("SELECT count(*) as 'numtrades' FROM Trades_m where trdm_trade_date = '".$trade_date_to_process."'") or die (mysql_error());
	while ( $row = mysql_fetch_array($result_num_trades) ) {
		$numtrades_val = $row["numtrades"];
	}







	if ($numtrades_val > 0) {
	
		//// For each list type process the data.
		$arr_list_types = array('watch', 'gray', 'restricted');

		$arr_list_types_tables = array('watch' => 'lwat_watch_list', 'gray' => 'lgry_gray_list', 'restricted' => 'lres_restricted_list');
		$arr_list_names_label = array('watch' => 'WATCH LIST', 'gray' => 'GRAY LIST', 'restricted' =>'RESTRICTED LIST');						

		for ($i_list =0; $i_list < count($arr_list_types); $i_list++) {
			xdebug('arr_list_types',$arr_list_types[$i_list]);
	
			//******************************************************************************	
			//Get tickers on list
			$query_symbols_on_list = "SELECT list_symbol from ".$arr_list_types_tables[$arr_list_types[$i_list]]." where list_isactive = 1";
			xdebug("query_symbols_on_list", $query_symbols_on_list );
			$result_num_trades = mysql_query($query_symbols_on_list) or die (mysql_error());
			$i = 0;
			$symbol_string = '';
			while ( $row = mysql_fetch_array($result_num_trades) ) {
				$symbols_on_list[$i] = $row["list_symbol"];
				if ($symbol_string=='') {
					$symbol_string = "'".$row["list_symbol"]."'";
				} else {
				$symbol_string = $symbol_string.",'".$row["list_symbol"]."'";
				}
				$i = $i + 1;
			}





			//******************************************************************************	
			//Find if there are trades in these tickers
			$query_trades = "SELECT trdm_auto_id, trdm_account_number, trdm_symbol from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.")";
			$result_query_trades = mysql_query($query_trades) or die (mysql_error());
			$i = 0;
			while ( $row = mysql_fetch_array($result_query_trades) ) {
				$arr_accounts[$i] = $row["trdm_account_number"];
				if ($str_accounts =='') {
				$str_accounts = "'".$row["trdm_account_number"]."'";
				} else {
				$str_accounts = $str_accounts.",'".$row["trdm_account_number"]."'";
				}
				$i = $i + 1;
			}
			xdebug("str_accounts",$str_accounts);
			
			//Check this condition thoroughly later
			xdebug("i",$i);
			$proceed = 0;
			if ($i > 0) {
			$proceed = 1;
			} 
			xdebug("proceed",$proceed); 
			
						
			//******************************************************************************	
			//Find if there are employee trades in these tickers, given that there are trades
			//the tickers on the stock list.

			if ($proceed == 1) {
			
				$query_accounts = "SELECT acct_rep, acct_number, acct_name1,acct_name2 from Employee_accounts where acct_is_active = 1 and acct_number in (".$str_accounts.")";
				//xdebug("query_accounts",$query_accounts);
				$result_query_accounts = mysql_query($query_accounts) or die (mysql_error());
				$i = 0;
				while ( $row = mysql_fetch_array($result_query_accounts) ) {
					$arr_accounts_match[$i] = $row["acct_number"];
					
					$arr_get_account_detail[$row["acct_number"]] = $row["acct_name1"]." (".$row["acct_rep"].")";
					
					if ($str_accounts_match =='') {
					$str_accounts_match = "'".$row["acct_number"]."'";
					
					} else {
					$str_accounts_match = $str_accounts_match.",'".$row["acct_number"]."'";
					}
					$i = $i + 1;
				}
					xdebug("str_accounts_match",$str_accounts_match);
				
					xdebug("i",$i);
					$proceed_final = 0;
					if ($i > 0) {
					$proceed_final = 1;
					} 
					xdebug("proceed_final",$proceed_final);
				
			} else {
			 		$proceed_final = 0;
			}
			
			if ($proceed_final == 1) {
			//Add to content $rep_content_emp_trades
			
			$query_trades_final = "SELECT * from Trades_m where trdm_trade_date ='".$trade_date_to_process."' and trdm_symbol in (".$symbol_string.") and  trdm_account_number in (".$str_accounts_match.")";
			$result_query_trades_final = mysql_query($query_trades_final) or die (mysql_error());
			
				while ( $row = mysql_fetch_array($result_query_trades_final) ) {
					//$arr_accounts[$i] = $row["trdm_account_number"];
					
						fputs ($fp, "['".$arr_list_names_label[$arr_list_types[$i_list]]."'],");
	
						//fputs ($fp, "['Employee Trades',0,{'tt':'Methods of the window object','sb':'Methods of the window object'},");
						//	['open'],
		
								$htmlfilebodydata .= '<tr> 
															<td colspan="8">&nbsp;</td>
														</tr>
														<tr> 
															<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$arr_list_names_label[$arr_list_types[$i_list]].'</b></font></td>
														</tr>
														<tr> 
															<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Acct.</u></font></td>
															<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
															<td ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2">&nbsp;&nbsp;<u>B/S</u></font></td>
															<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Qty.</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
															<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Price</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
															<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Total</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
															<td align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Time</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
															<td align="center" valign="middle" ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>NAME</u></font></td>
														</tr>';

				
					$htmlfilebodydata .='<tr> 
																<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_account_number"].'</font></td>
																<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_symbol"].'&nbsp;&nbsp;</font></td>
																<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;'.convert_buy_sell($row["trdm_buy_sell"]).'&nbsp;&nbsp;&nbsp;</font></td>
																<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_quantity"].'&nbsp;&nbsp;</font></td>
																<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row["trdm_price"].'&nbsp;&nbsp;</font></td>
																<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">'.format_no_decimal_comma($row["trdm_quantity"]*$row["trdm_price"]).'&nbsp;&nbsp;</font></td>
																<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="2">1'.$row["trdm_trade_time"].'&nbsp;&nbsp;</font></td>
																<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$arr_get_account_detail[$row["trdm_account_number"]].'</font></TD>
															</tr>';
	
				}														
			} else {
			//Add to content $rep_content_emp_trades (no trades)
			$htmlfilebodydata .= '<tr> 
															<td colspan="8">&nbsp;</td>
														</tr>
														<tr> 
															<td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>'.$arr_list_names_label[$arr_list_types[$i_list]].'</b> (No Trades)</font></td>
														</tr>';
			}
						
		} //End for loop for processing all lists.


}
										
										



fputs ($fp, "],");
//fclose($fp);

}
?>