<?
//Function to determine Buy, Sell, Cover, Short
function compute_buy_sell ($val) {
	if (stripos($val,"P ") !== false) {
		return 'SS';
	} elseif (stripos($val,"R ") !== false) {
		return 'C';
	} else { 
	 return 'X';
	}
}

//Move trade data from staging to production
$query_staging = "SELECT * from fidelity_emp_trades_raw";
$result_staging = mysql_query($query_staging) or die (mysql_error());

$row_inserted = 1;
while ( $row_staging = mysql_fetch_array($result_staging) ) {

		$computed_buy_sell = '';
		$get_bscss_val = compute_buy_sell ($row_staging['trad_confirm_legend_code']);
		
		if ($get_bscss_val == 'X') {
		$computed_buy_sell = $row_staging['trad_buy_sell'];
		} elseif ($get_bscss_val == 'SS' and ($row_staging['trad_sec_type']=='1' or $row_staging['trad_sec_type']=='2')) {
		$computed_buy_sell = $get_bscss_val;
		} elseif ($get_bscss_val == 'C' and ($row_staging['trad_sec_type']=='1' or $row_staging['trad_sec_type']=='2')) {
		$computed_buy_sell = $get_bscss_val;
		} else {
		$computed_buy_sell = $row_staging['trad_buy_sell'];
		}

$query_main = "INSERT INTO fidelity_emp_trades(
								auto_id,
								processed_on,
								symbol,
								cusip,
								sec_type,
								sec_desc_1,
								sec_desc_2,
								sec_desc_3,
								sec_desc_4,
								sec_desc_5,
								sec_desc_6,
								buy_sell,
								acct_type,
								acct_num,
								broker,
								price,
								quantity,
								commission,
								principal,
								trade_date,
								settle_date,
								accrued_interest,
								net,
								cancel,
								correct,
								ssno_1,
								first_name,
								middle_name,
								last_name,
								ssno_2,
								custom_1)
								values
								(".
									"NULL,".
									"now(),".
									"'".$row_staging['symbol']."',".
									"'".$row_staging['cusip']."',".
									"'".$row_staging['sec_type']."',".
									"'".$row_staging['sec_desc_1']."',".
									"'".$row_staging['sec_desc_2']."',".
									"'".$row_staging['sec_desc_3']."',".
									"'".$row_staging['sec_desc_4']."',".
									"'".$row_staging['sec_desc_5']."',".
									"'".$row_staging['sec_desc_6']."',".
									"'".$row_staging['buy_sell']."',".
									"'".$row_staging['acct_type']."',".
									"'".$row_staging['acct_num']."',".
									"'".$row_staging['broker']."',".

									"'".substr($row_staging['price'],0,9).".".substr($row_staging['price'],9,9)."',".
									"'".substr($row_staging['quantity'],0,11).".".substr($row_staging['quantity'],11,5)."',".
									"'".substr($row_staging["commission"],0,6).".".substr($row_staging["commission"],6,2)."',".
									"'".substr($row_staging['principal'],0,8).".".substr($row_staging['principal'],8,2)."',".

									"'".$row_staging['trade_date']."',".
									"'".$row_staging['settle_date']."',".

									"'".substr($row_staging['accrued_interest'],0,6).".".substr($row_staging['accrued_interest'],6,2)."',".
									"'".substr($row_staging['net'],0,8).".".substr($row_staging['net'],8,2)."',".

									"'".$row_staging['cancel']."',".
									"'".$row_staging['correct']."',".
									"'".$row_staging['ssno_1']."',".
									"'".trim(str_replace("'","\'",$row_staging['first_name']))."',".
									"'".trim(str_replace("'","\'",$row_staging['middle_name']))."',".
									"'".trim(str_replace("'","\'",$row_staging['last_name']))."',".
									"'".$row_staging['ssno_2']."',".
									"'".$row_staging['custom_1']."')";


  xdebug("query_main",$query_main);

  $result_main = mysql_query($query_main) or die("<b>A fatal Database (MySQL) error occured</b>.\n<br />Query: " . $query_main . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	echo "Trade Rows inserted: ".$row_inserted."\n";
  $row_inserted = $row_inserted + 1;

 
}	 
?>