<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = md5(rand(1000000000,9999999999))."_activity.csv";

$fp = fopen($exportlocation.$output_filename, "w");

//echo $xl;
$arr_vals = split('\^',$xl);
//show_array($arr_vals);
/*
0 = [5fd0b37cd7dbbb00f97ba6ce92bf5add]
1 = [037]
2 = [237]
3 = [2006-03-15]
4 = [2006-03-24]
*/


$string = "\"Trade Date\",\"ADVISOR / CLIENT\",\"RR #\",\"Symbol\",\"B/S\",\"Shares\",\"Price\",\"Commission\",\"Comm./Shr. ($)"."\"".chr(13); 

fputs ($fp, $string);

			$query_trades = "SELECT 
													trad_advisor_code,
													trad_symbol,
													trad_buy_sell,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(trad_quantity),0) as trad_quantity,
													FORMAT(max(trade_price),2) as trade_price,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission,
													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share 
												FROM mry_comm_rr_trades 
												WHERE trad_rr = '".$arr_vals[1]."'  
												AND trad_is_cancelled = 0 
												AND trad_trade_date between '".$arr_vals[3]."' AND '".$arr_vals[4]."'"
												. $arr_vals[5] . $arr_vals[6] .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
												
      $query_shared_rep_trades = "SELECT 
																a.trad_advisor_code,
																a.trad_symbol,
																a.trad_buy_sell,
																DATE_FORMAT(a.trad_trade_date,'%m/%d/%Y') as trad_trade_date,
																max(a.trad_advisor_name) as trad_advisor_name,
																FORMAT(sum(a.trad_quantity),0) as trad_quantity,
																FORMAT(max(a.trade_price),2) as trade_price,
																FORMAT(sum(a.trad_commission),2) as trad_commission,
																sum(a.trad_commission) as for_sum_trad_commission,
																FORMAT(avg(a.trad_cents_per_share),3) as trad_cents_per_share,
																max(a.trad_rr) as trad_rr
															FROM mry_comm_rr_trades a, sls_sales_reps b
															WHERE a.trad_rr = b.srep_rrnum 
															AND b.srep_user_id = '".$arr_vals[2]."'
															AND trad_is_cancelled = 0 
															AND b.srep_isactive = 1 
															AND trad_trade_date between '".$arr_vals[3]."' AND '".$arr_vals[4]."'"
															. $arr_vals[5] . $arr_vals[6] .
															" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
															ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";	
															
		$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
		$result_shared_rep_trades = mysql_query($query_shared_rep_trades) or die(tdw_mysql_error($query_shared_rep_trades));
															

		while ( $row_trades = mysql_fetch_array($result_trades) ) {
	
					if ($row_trades["trad_advisor_name"] == '') {
						$show_trad_advisor_name = $row_trades["trad_advisor_code"];
					} else {
						$show_trad_advisor_name = $row_trades["trad_advisor_name"];
					}
					
					$show_trad_rr = $arr_vals[1]; // $row_trades["trad_rr"]; //
					$show_trad_trade_date = $row_trades["trad_trade_date"];
					$show_trad_symbol = $row_trades["trad_symbol"];
					$show_trad_buy_sell = $row_trades["trad_buy_sell"];
					$show_trad_quantity = $row_trades["trad_quantity"];
					$show_trade_price = $row_trades["trade_price"];
					$show_trad_commission = $row_trades["trad_commission"];
					$show_trad_cents_per_share = $row_trades["trad_cents_per_share"];	
					//$running_trad_commission_total = $running_trad_commission_total + $row_trades["trad_commission"];
	
	
					//$string = "\"".$row_trades["nadd_advisor"]."\",\"".$row_trades["nadd_short_name"]."\",\"".$row_trades["nadd_rr_exec_rep"]."\",\"".$row_trades["nadd_full_account_number"]."\",\"".$row_trades["nadd_address_line_1"]."\",\"".$row_trades["nadd_address_line_2"]."\",\"".$row_trades["nadd_address_line_3"]."\",\"".$row_trades["nadd_address_line_4"]."\",\"".$row_trades["nadd_address_line_5"]."\",\"".$row_trades["nadd_address_line_6"]."\"\n";
					$string = "\"".$show_trad_trade_date."\",\"".$show_trad_advisor_name."\",\"".$show_trad_rr."\",\"".$show_trad_symbol."\",\"".$show_trad_buy_sell."\",\"".$show_trad_quantity."\",\"".$show_trade_price."\",\"".$show_trad_commission."\",\"".$show_trad_cents_per_share."\"".chr(13); 
					//echo $string;
					
			fputs ($fp, $string);
		}

		while ( $row_shared_rep_trades = mysql_fetch_array($result_shared_rep_trades) ) {
	
					if ($row_shared_rep_trades["trad_advisor_name"] == '') {
						$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_code"];
					} else {
						$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_name"];
					}
					
					$show_trad_rr = $row_shared_rep_trades["trad_rr"];
					$show_trad_trade_date = $row_shared_rep_trades["trad_trade_date"];
					$show_trad_symbol = $row_shared_rep_trades["trad_symbol"];
					$show_trad_buy_sell = $row_shared_rep_trades["trad_buy_sell"];
					$show_trad_quantity = $row_shared_rep_trades["trad_quantity"];
					$show_trade_price = $row_shared_rep_trades["trade_price"];
					$show_trad_commission = $row_shared_rep_trades["trad_commission"];
					$show_trad_cents_per_share = $row_shared_rep_trades["trad_cents_per_share"];	
					//$running_trad_commission_total = $running_trad_commission_total + $row_trades["trad_commission"];
	
	
					//$string = "\"".$row_trades["nadd_advisor"]."\",\"".$row_trades["nadd_short_name"]."\",\"".$row_trades["nadd_rr_exec_rep"]."\",\"".$row_trades["nadd_full_account_number"]."\",\"".$row_trades["nadd_address_line_1"]."\",\"".$row_trades["nadd_address_line_2"]."\",\"".$row_trades["nadd_address_line_3"]."\",\"".$row_trades["nadd_address_line_4"]."\",\"".$row_trades["nadd_address_line_5"]."\",\"".$row_trades["nadd_address_line_6"]."\"\n";
					$string = "\"".$show_trad_trade_date."\",\"".$show_trad_advisor_name."\",\"".$show_trad_rr."\",\"".$show_trad_symbol."\",\"".$show_trad_buy_sell."\",\"".$show_trad_quantity."\",\"".$show_trade_price."\",\"".$show_trad_commission."\",\"".$show_trad_cents_per_share."\"".chr(13); 
					//echo $string;
			fputs ($fp, $string);
		}




fclose($fp);


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//This works!
Header("Location: data/exports/".$output_filename);

?>