<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = "bcm.xls";
$fp = fopen($exportlocation.$output_filename, "w");

//echo $xl;
$arr_vals = split("\^",$xl);
//show_array($arr_vals);
/*
0 = [c52f1bd66cc19d05628bd8bf27af3ad6]
1 = [235]
2 = [2006-03-08]
3 = [2006-03-27]
4 = [ AND trad_symbol = 'KNL' ]
5 = [ AND trad_advisor_code = 'MAZA' ]
6 = [ AND trad_rr = '040']
*/

			$query_trades = "SELECT 
													* 
												FROM oth_other_trades  
												WHERE oth_trade_date between '".$arr_vals[1]."' AND '".$arr_vals[2]."'"
												. $arr_vals[3] .
												" ORDER BY oth_trade_time desc";

			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);


$str = '<table border="1" cellspacing="0" cellpadding="0">
				<tr bgcolor="#cccccc">
					<td width="90">Trade Date</td>
				  <td width="90">Process Dt.</td>
				  <td width="100">Parent CXL</td>
					<td width="56">Broker</td>
					<td width="80">Symbol</td>
					<td width="80">B/S</td>
					<td ts_type="money" width="80">Quantity</td>
					<td ts_type="money" width="80">Price</td>
					<td ts_type="money" width="80">Commission</td>
					<td ts_type="money" width="100">Net Money</td>
					<td width="80">Trd Time</td>
					<td width="40">PM</td>
					<td width="80">Emp./Client</td>
					<td width="80">EmpAlloc</td>
					<td width="100">Trade ID</td>
					<td width="100">Trade TS</td>
					<td width="100">First Exec</td>
					<td width="100">Last Exec</td>
					<td>&nbsp;</td>
				</tr>';
fputs ($fp, $str);

	while ( $row_trades = mysql_fetch_array($result_trades) ) {

				if ($row_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
				
				$show_trad_rr = $row_trades["trad_rr"];
				$show_trad_trade_date = $row_trades["trad_trade_date"];
				$show_trad_symbol = $row_trades["trad_symbol"];
				$show_trad_buy_sell = $row_trades["trad_buy_sell"];
				$show_trad_quantity = $row_trades["trad_quantity"];
				$show_trade_price = $row_trades["trade_price"];
				$show_trad_commission = $row_trades["trad_commission"];
				$show_trad_cents_per_share = $row_trades["trad_cents_per_share"];	
				//$running_trad_commission_total = $running_trad_commission_total + $row_trades["trad_commission"];

					$str = '<tr>
											<td>'.format_date_ymd_to_mdy($row_trades["oth_trade_date"]).'</td>
											<td>'.format_date_ymd_to_mdy($row_trades["oth_process_date"]).'</td>
											<td>'.$row_trades["oth_original_trade_id "].'</td>
											<td>'.$row_trades["oth_broker"].'</td>
											<td>'.$row_trades["oth_symbol"].'</td>
											<td>'.$row_trades["oth_buysell"].'</td>
											<td>'.number_format($row_trades["oth_quantity"],0,"",",").'</td>
											<td>'.number_format($row_trades["oth_price"],2,".",",").'</td>
											<td>'.number_format($row_trades["oth_commission"],2,".",",").'</td>
											<td>'.number_format($row_trades["oth_net_money"],2,".",",").'</td>
											<td>'.date('h:i:sa',strtotime($row_trades["oth_trade_time"])).'</td>
											<td>'.$row_trades["oth_pm_code"].'</td>
											<td>'.$row_trades["oth_emp_client"].'</td>
											<td>'.$row_trades["oth_emp_alloc"].'</td>
											<td>'.$row_trades["oth_trade_id"].'</td>
											<td>'.date('h:i:sa',strtotime($row_trades["oth_trade_ts"])).'</td>
											<td>'.date('h:i:sa',strtotime($row_trades["oth_first_exec"])).'</td>
											<td>'.date('h:i:sa',strtotime($row_trades["oth_last_exec"])).'</td>
											<td>&nbsp;</td>
									</tr>';
					
					fputs ($fp, $str);
	}


$str = '</table>
	</body>
</html>';
fputs ($fp, $str);


fclose($fp);


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//*******************************************************************************************
/*
//This works!
//header("Location: data/exports/".$output_filename);

$export_file = $output_filename; //"my_name.xls";
$myFile = "data/exports/".$output_filename;

header('Pragma: public');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                  // Date in the past    
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1 
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header ("Pragma: no-cache");
header("Expires: 0");
header('Content-Transfer-Encoding: none');
header('Content-Type: application/vnd.ms-excel;');  // This should work for IE & Opera
header("Content-type: application/x-msexcel");      // This should work for the rest
header('Content-Disposition: attachment; filename="'.basename($output_filename).'"');
readfile($myFile);
*/
//**********************************************************************************************
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>