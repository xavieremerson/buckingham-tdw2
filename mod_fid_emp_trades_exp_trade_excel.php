<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = substr(md5(rand(1,10)),0,2)."_fid_emptrd_nfs.xls";

$fp = fopen($exportlocation.$output_filename, "w");


$arr_vals = explode("^",$xl);
//show_array($arr_vals);
/*
0 = [5fd0b37cd7dbbb00f97ba6ce92bf5add]
1 = [037]
2 = [237]
3 = [2006-03-15]
4 = [2006-03-24]
*/

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);


$str = '<table width="800" border="1" cellspacing="0" cellpadding="0">
					<tr>
							<td width="85">Trade Date</td>
							<td width="200">Name</td>
							<td width="100">Account</td> 
							<td width="60">Symbol</td>
							<td width="100">Cusip</td> 
							<td width="320">Sec. Description</td>
							<td width="40">Buy/Sell</td>
							<td width="70">Quantity</td>
							<td width="100">Price</td>
							<td width="80">Ord Entry</td>
							<td width="75">Ord Exec</td>
					</tr>';
fputs ($fp, $str);


if ($arr_vals[4] == 'SYMBOL' or trim($arr_vals[4]) == '') {
	$str_symbol = " and symbol like '%' ";
} else {
	$str_symbol = " and symbol like '%".strtoupper(trim($arr_vals[4]))."%' ";
}

if ($arr_vals[3] == '_ALL_' or trim($arr_vals[3]) == '') {
	$str_name = " and first_name like '%' ";
} else {
  //xdebug("namex",$arr_vals[3]);
	$znameval = split("ZZZ",$arr_vals[3]);
	$str_name = " and first_name = '".str_replace("'","\\'",$znameval[0])."' ";
	$str_name .= " and middle_name = '".str_replace("'","\\'",$znameval[1])."' ";
	$str_name .= " and last_name = '".str_replace("'","\\'",$znameval[2])."' ";
}

			$query_trades = "SELECT 
												auto_id,
												acct_num,
												trade_date,
												buy_sell,
												symbol,
												cusip,
												date_format(order_entry_time, '%l:%i%p') as order_entry_time,
												date_format(order_exec_time, '%l:%i%p') as order_exec_time,
												concat(sec_desc_1, ' ', sec_desc_2) as sec_desc,  
												round(sum(quantity),0) as quantity, 
												round(avg(price),2) as price,
												is_active, 
												substring(concat(trim(first_name),' ',trim(middle_name),' ',trim(last_name)),1,20) as Fullname
											FROM fidelity_emp_trades
											WHERE trade_date between '".$arr_vals[1]."' and '".$arr_vals[2]."' ".$str_symbol." ".$str_name ." 
											AND is_active  = 1
											GROUP BY acct_num, symbol, trade_date, buy_sell";

						/*
						trad_auto_id  trad_reference_number  trad_rr  trad_trade_date  trad_run_date  
						trad_settle_date  trad_advisor_code  trad_advisor_name  trad_account_name  
						trad_account_number  trad_symbol  trad_buy_sell  trad_quantity  trade_price  
						trad_commission  trad_cents_per_share  trad_is_cancelled  						
						*/
			
			//xdebug("query_trades",$query_trades);			
			//exit;
																											
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));

			while ( $row_trades = mysql_fetch_array($result_trades) ) {
		
					$str = '<tr>
										<td>'.format_date_ymd_to_mdy($row_trades["trade_date"]).'</td>
										<td>'.$row_trades["Fullname"].'</td>
										<td>'.$row_trades["acct_num"].'</td>
										<td>'.$row_trades["symbol"].'</td>
										<td>'.$row_trades["cusip"].'</td>
										<td>'.$row_trades["sec_desc"].'</td>
										<td>'.$row_trades["buy_sell"].'</td>
										<td>'.number_format($row_trades["quantity"],0,'',',').'</td>
										<td>'.number_format($row_trades["price"],2,'.',',').'</td>
										<td>'.$row_trades["order_entry_time"].'</td>
										<td>'.$row_trades["order_exec_time"].'</td>
									</tr>';
					fputs ($fp, $str);					
		}

$str = '</table>';
fputs ($fp, $str);

$str = '</body></html>';
fputs ($fp, $str);

fclose($fp);


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";
//This works!

//Header("Location: data/exports/".$output_filename);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>