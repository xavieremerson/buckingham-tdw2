<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = md5(rand(1000000000,9999999999))."_emptrd_ext.csv";

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


$string = "\"Trade Date\",\"Employee Name\",\"Account #\",\"Symbol\",\"B/S\",\"Shares\",\"Price\",\"Date Entered\"".chr(13); 

fputs ($fp, $string);

if ($arr_vals[3] == '_ALL_' or $arr_vals[3] == '') {
	$str_emp = " and c.oac_emp_userid like '%' "	;					
} else {
	$str_emp = " and c.oac_emp_userid like '%".$arr_vals[3]."%' ";				
}

if ($arr_vals[4] == 'SYMBOL' or trim($arr_vals[4]) == '') {
	$str_symbol = " and a.otd_symbol like '%' ";
} else {
	$str_symbol = " and a.otd_symbol like '%".trim($arr_vals[4])."%' ";
}

			$query_trades = "SELECT a. * , date_format(a.otd_last_edited_on, '%c/%e/%Y %h:%i%p' ) as date_added, b.Fullname, c.oac_account_number, c.oac_custodian
																FROM otd_emp_trades_external a, users b, oac_emp_accounts c
																WHERE a.otd_account_id = c.auto_id
																AND c.oac_emp_userid = b.ID
																AND a.otd_isactive = 1
																AND otd_trade_date between '".$arr_vals[1]."' and '".$arr_vals[2]."' ".$str_emp.$str_symbol."
																ORDER BY a.auto_id DESC";
																											
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));

			while ( $row_trades = mysql_fetch_array($result_trades) ) {
		
					$string = "\"".
										format_date_ymd_to_mdy($row_trades["otd_trade_date"])
					          ."\",\"".
										$row_trades["Fullname"]
										."\",\"".
										$row_trades["oac_account_number"] . "  (".trim($row_trades["oac_custodian"]).")"
										."\",\"".
										$row_trades["otd_symbol"]
										."\",\"".
										$row_trades["otd_buysell"]
										."\",\"".
										$row_trades["otd_quantity"]
										."\",\"".
										$row_trades["otd_price"]
										."\",\"".
										$row_trades["date_added"]
										."\"".chr(13); 

					
			fputs ($fp, $string);
		}

fclose($fp);


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";
//This works!
Header("Location: data/exports/".$output_filename);

?>