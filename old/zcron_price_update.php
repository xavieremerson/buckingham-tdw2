<?

//Populates the trade data for the previous business day with prices from yahoo offset by a small random amount

function get_quotes($param_symbol) {

	$quotes = new Quotes(); 
  $quotes->mSetSymbol(strtoupper($param_symbol)) ; 
	$quotes->mLoadYahoo() ;

	$outputquote = array($quotes->_strCompany,$quotes->_strLastPrice,$quotes->_strTradeDate,$quotes->_strTradeTime,$quotes->_strChange,$quotes->_strChangePercent,$quotes->_strVolume);

return $outputquote;	
}

$trade_date_to_process = previous_business_day();

$query_statement = "SELECT * FROM Trades_m where trdm_trade_date = '".$trade_date_to_process."'";
$result = mysql_query($query_statement) or die (mysql_error());

while ( $row = mysql_fetch_array($result) ) 
{
	$val_symbol = $row["trdm_symbol"];
	$quoteval = get_quotes($val_symbol);

		if ($quoteval[1] == "0.00") {
					echo "<br>Delete the record!";
					$arr_symbol = $arr_symbol . "|" . $row["trdm_symbol"];
					echo $arr_symbol . "<br>";
					//$result = mysql_query($queryx) or die (mysql_error());	
		} else {
					echo "<br>Update the record!";
		
					if (rand(0,1) == 0) {
					echo "<br>0 add";
					$val_add = "0.".rand(1, 50);
					$new_price = $quoteval[1] + $val_add;
						$queryx = "update Trades_m set trdm_price = '".$new_price."' where trdm_auto_id =".$row["trdm_auto_id"];
						echo $queryx . "<br>";
						$resultx = mysql_query($queryx) or die (mysql_error());	
					} else {
					echo "<br>1 subtract";
					$val_add = "0.".rand(1, 50);
					$new_price = $quoteval[1] - $val_add;
						$queryy = "update Trades_m set trdm_price = '".$new_price."' where trdm_auto_id =".$row["trdm_auto_id"];
						echo "<br>" .$queryy . "<br>";
						$resulty = mysql_query($queryy) or die (mysql_error());	
					}
				echo $val_symbol . " ==> " . $quoteval[1] . "( " . $new_price . " )";
		}
}

$arr_symbol = explode("|", $arr_symbol);

for ($i=0; $i < count($arr_symbol); $i++) {

	if ($arr_symbol[$i] == '') {
	echo "Symbol is null!<br>";
	} else {
	echo "Symbol is ".$arr_symbol[$i]."!<br>";
	$result = mysql_query("delete from Trades_m where trdm_symbol = '".$arr_symbol[$i]."'") or die (mysql_error());
	}

}
?>


