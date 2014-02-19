<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = md5(rand(1000000000,9999999999))."_emptrd_nfs.csv";

$fp = fopen($exportlocation.$output_filename, "w");

echo $xl;
$arr_vals = split('\^',$xl);
//show_array($arr_vals);
/*
0 = [5fd0b37cd7dbbb00f97ba6ce92bf5add]
1 = [037]
2 = [237]
3 = [2006-03-15]
4 = [2006-03-24]
*/


$string = "\"Trade Date\",\"Employee Name\",\"Account #\",\"Symbol\",\"B/S\",\"Shares\",\"Price\",\"Order Time\",\"Exec Time\"".chr(13); 

if ($arr_vals[3] == '_ALL_' or $arr_vals[3] == '') {
	$str_emp = " and trad_account_name like '%' "	;					
} else {
	$str_emp = " and trad_account_name like '%".$arr_vals[3]."%' ";				
}

if ($arr_vals[4] == 'SYMBOL' or trim($arr_vals[4]) == '') {
	$str_symbol = " and trad_symbol like '%' ";
} else {
	$str_symbol = " and trad_symbol like '%".trim($arr_vals[4])."%' ";
}


fputs ($fp, $string);

			$query_trades = "SELECT 
													count(trad_order_reference_number) as tcount,
													trad_order_reference_number,
													max(trad_rr) as trad_rr,
													trad_trade_date,
													TIME_FORMAT(max(trad_order_time), '%l:%i:%s %p' ) as trad_order_time,
													TIME_FORMAT(max(trad_exec_time), '%l:%i:%s %p' ) as trad_exec_time,
													max(trad_advisor_code) as trad_advisor_code,
													max(trad_advisor_name) as trad_advisor_name,
													max(trad_account_name) as trad_account_name,
													max(trad_account_number) as trad_account_number,
													max(trad_symbol) as trad_symbol,
													max(trad_buy_sell) as trad_buy_sell,
													sum(trad_quantity) as trad_quantity,
													avg(trade_price) as trade_price,
													sum(trad_commission) as trad_commission,
													avg(trad_cents_per_share) as trad_cents_per_share 
												FROM emp_employee_trades
												WHERE trad_is_cancelled = 0
												AND trad_trade_date between '".$arr_vals[1]."' and '".$arr_vals[2]."' ".$str_emp.$str_symbol."
												GROUP BY trad_order_reference_number
												ORDER BY trad_auto_id DESC";

						/*
						trad_auto_id  trad_reference_number  trad_rr  trad_trade_date  trad_run_date  
						trad_settle_date  trad_advisor_code  trad_advisor_name  trad_account_name  
						trad_account_number  trad_symbol  trad_buy_sell  trad_quantity  trade_price  
						trad_commission  trad_cents_per_share  trad_is_cancelled  						
						*/
																											
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));

			while ( $row_trades = mysql_fetch_array($result_trades) ) {
		
					$string = "\"".
										format_date_ymd_to_mdy($row_trades["trad_trade_date"])
					          ."\",\"".
										$row_trades["trad_account_name"]
										."\",\"".
										$row_trades["trad_account_number"]
										."\",\"".
										$row_trades["trad_symbol"]
										."\",\"".
										$row_trades["trad_buy_sell"]
										."\",\"".
										number_format($row_trades["trad_quantity"],0,'',',')
										."\",\"".
										number_format($row_trades["trade_price"],2,'.',',')
										."\",\"".
										$row_trades["trad_order_time"]
										."\",\"".
										$row_trades["trad_exec_time"]
										."\"".chr(13); 

					
			fputs ($fp, $string);
		}

fclose($fp);


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";
//This works!
Header("Location: data/exports/".$output_filename);

?>