<?
include('includes/dbconnect.php');
include('includes/functions.php');


$tdate = previous_business_day ();

$arr_ticker = array("MSFT", "ORCL", "IBM", "GOOG", "INTC", "FD", "DELL", "XATA", "MLNB", "ARB");

for($i = 0; $i < count($arr_ticker); $i++)
{
	$buy_volume = 0;
	$sell_volume = 0;
	
	$query_buy = "SELECT trdm_quantity*trdm_price AS bvolume FROM Trades_m WHERE trdm_trade_date = '".$tdate."' AND trdm_symbol = '".$arr_ticker[$i]."' AND (trdm_buy_sell = 'buy' OR trdm_buy_sell = 'by' OR trdm_buy_sell = 'Buy' OR trdm_buy_sell = 'BUY')";
	echo $query_buy . "<BR><bR>";
	
	
	$result_buy = mysql_query($query_buy) or die(mysql_error());
	while($row_buy = mysql_fetch_array($result_buy))
	{
		$buy_volume = $buy_volume + $row_buy['bvolume'];
	}
	
	$query_sell = "SELECT trdm_quantity*trdm_price AS svolume FROM Trades_m WHERE trdm_trade_date = '".$tdate."' AND trdm_symbol = '".$arr_ticker[$i]."' AND (trdm_buy_sell = 'sell' OR trdm_buy_sell = 'sl' OR trdm_buy_sell = 'Sell' OR trdm_buy_sell = 'SELL')";
	
	echo $query_sell . "<BR><bR>";
	$result_sell = mysql_query($query_sell) or die(mysql_error());
	while($row_sell = mysql_fetch_array($result_sell))
	{
		$sell_volume = $sell_volume + $row_sell['svolume'];
	}
	
	$query_insert = "INSERT INTO tvol_ticker_volume(tvol_ticker, tvol_trade_date, tvol_buy_volume, tvol_sell_volume, tvol_isactive) VALUES('".$arr_ticker[$i]."', '".$tdate."', '".$buy_volume."', '".$sell_volume."', '1')";
	
	echo $query_insert;
	$result_insert = mysql_query($query_insert) or die(mysql_error());		
}


?>