<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = date('mdY_h-ia')."_BCM_Positions.xls";
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

			$query_pos = "select
											pos_cusip, 
											min(pos_symbol) as pos_symbol, 
											long_short, 
											transaction_number, 
											sum(quantity) as quantity, 
											avg(price_base) as price_base,
											sum(market_value_net_issue) as market_value_net_issue, 
											min(security_description) as security_description, 
											reporting_date from pos_bcm_positions
											where reporting_date between '".$arr_vals[1]."' AND '".$arr_vals[2]."' ". 
											" group by reporting_date, pos_cusip, long_short order by pos_symbol";

			$result_pos = mysql_query($query_pos) or die(tdw_mysql_error($query_pos));

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);


$str = '<table width="800" border="1" cellspacing="0" cellpadding="0">
					<tr>
					<td width="120">Reporting Date</td>
				  <td width="200">Symbol</td>
					<td width="350">Security Desc.</td>
					<td width="100">Long/Short</td>
					<td width="120">Quantity</td>
					<td width="120">Price</td>
					<td width="120">Market Value</td>
					<td align="right">&nbsp;</td>
					</tr>';
fputs ($fp, $str);

	while ( $row = mysql_fetch_array($result_pos) ) {
				
				if ($row["long_short"] == 'S') {
					$str_ls = '&nbsp;&nbsp;S';
				} else {
					$str_ls = $row["long_short"];
				}

					$str = '<tr>
										<td>'.format_date_ymd_to_mdy($row["reporting_date"]).'</td>
										<td>'.$row["pos_symbol"].'</td>
										<td>'.$row["security_description"].'</td>
										<td>'.$str_ls.'</td>
										<td>'.number_format($row["quantity"],0,"",",").'</td>
										<td>'.number_format($row["price_base"],2,".",",").'</td>
										<td>'.number_format($row["market_value_net_issue"],2,".",",").'</td>
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