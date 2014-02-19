<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$fp = fopen($exportlocation."activity.csv", "w");

if ($qry_string == '') {
$qry_string = "";
} else {
$qry_string = $qry_string;
}

$result_trades = mysql_query($qry_string) or die(tdw_mysql_error($qry_string));

$string = "\"Trade Date\",\"ADVISOR / CLIENT\",\"RR #\",\"Symbol\",\"B/S\",\"Shares\",\"Price\",\"Commission\",\"Comm./Shr. ($)"."\"".chr(13); 
fputs ($fp, $string);

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


				//$string = "\"".$row_trades["nadd_advisor"]."\",\"".$row_trades["nadd_short_name"]."\",\"".$row_trades["nadd_rr_exec_rep"]."\",\"".$row_trades["nadd_full_account_number"]."\",\"".$row_trades["nadd_address_line_1"]."\",\"".$row_trades["nadd_address_line_2"]."\",\"".$row_trades["nadd_address_line_3"]."\",\"".$row_trades["nadd_address_line_4"]."\",\"".$row_trades["nadd_address_line_5"]."\",\"".$row_trades["nadd_address_line_6"]."\"\n";
				$string = "\"".$show_trad_trade_date."\",\"".$show_trad_advisor_name."\",\"".$show_trad_rr."\",\"".$show_trad_symbol."\",\"".$show_trad_buy_sell."\",\"".$show_trad_quantity."\",\"".$show_trade_price."\",\"".$show_trad_commission."\",\"".$show_trad_cents_per_share."\"".chr(13); 
		
		fputs ($fp, $string);
	}

fclose($fp);

//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//This works!
Header("Location: data/exports/activity.csv");

?>