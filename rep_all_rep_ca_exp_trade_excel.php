<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = date('mdY_h-ia')."_actv.xls";
$fp = fopen($exportlocation.$output_filename, "w");

//echo $xl;
$arr_vals = split('\^',$xl);
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
								trad_advisor_code,
								trad_symbol,
								trad_buy_sell,
								DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
								max(trad_advisor_name) as trad_advisor_name,
								FORMAT(sum(trad_quantity),0) as trad_quantity,
								FORMAT(max(trade_price),2) as trade_price,
								FORMAT(sum(trad_commission),2) as trad_commission,
								sum(trad_commission) as for_sum_trad_commission,
								FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
								max(trad_rr) as trad_rr 
							FROM mry_comm_rr_trades 
							WHERE trad_is_cancelled = 0 
							AND trad_trade_date between '".$arr_vals[2]."' AND '".$arr_vals[3]."'"
							. $arr_vals[4] . $arr_vals[5] .$arr_vals[6] .
							" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
							ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
			
			//xdebug("query_trades",$query_trades);
			//$passtoexcel = $query_trades;
			
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
												AND b.srep_user_id = '".$arr_vals[1]."'
												AND trad_is_cancelled = 0 
												AND b.srep_isactive = 1 
												AND trad_trade_date between '".$arr_vals[2]."' AND '".$arr_vals[3]."'"
												. $arr_vals[4] . $arr_vals[5] .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";	


			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			$result_shared_rep_trades = mysql_query($query_shared_rep_trades) or die(tdw_mysql_error($query_shared_rep_trades));



$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);


$str = '<table width="800" border="1" cellspacing="0" cellpadding="0">
					<tr>
						<td width="80"><b>Trade Date</b></td>
						<td width="250"><b>ADVISOR / CLIENT</b></td>
						<td width="60"><b>RR #</b></td>
						<td width="80"><b>Symbol</b></td>
						<td width="80"><b>B/S</b></td>
						<td width="80"><b>Shares</b></td>
						<td width="80"><b>Price</b></td>
						<td width="100"><b>Commission</b></td>
						<td width="110"><b>Comm./Shr. ($)</b></td>
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
								<td>'.$show_trad_trade_date.'</td>
								<td>'.$show_trad_advisor_name.'</td>
								<td>'.$show_trad_rr.'</td>
								<td>'.$show_trad_symbol.'</td>
								<td>'.$show_trad_buy_sell.'</td>
								<td>'.$show_trad_quantity.'</td>
								<td>'.$show_trade_price.'</td>
								<td>'.$show_trad_commission.'</td>
								<td>'.$show_trad_cents_per_share.'</td>
							</tr>';

					
					fputs ($fp, $str);
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


					$str = '<tr>
								<td>'.$show_trad_trade_date.'</td>
								<td>'.$show_trad_advisor_name.'</td>
								<td>'.$show_trad_rr.'</td>
								<td>'.$show_trad_symbol.'</td>
								<td>'.$show_trad_buy_sell.'</td>
								<td>'.$show_trad_quantity.'</td>
								<td>'.$show_trade_price.'</td>
								<td>'.$show_trad_commission.'</td>
								<td>'.$show_trad_cents_per_share.'</td>
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