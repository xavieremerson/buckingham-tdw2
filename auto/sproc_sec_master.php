<?

ini_set('max_execution_time', 3600);

include('../includes/dbconnect.php');
include('../includes/global.php');
include('../includes/functions.php');

//create an array of tickers in sec master
$arr_master = array();
$query_symbol = "SELECT symbol
								 FROM sec_master 
								 ORDER BY symbol";
$result_symbol = mysql_query($query_symbol) or die(mysql_error());
while($row_symbol = mysql_fetch_array($result_symbol))
{
$arr_master[] = $row_symbol["symbol"];	
}

//get all symbols from trades
$query_trades_symbol = "SELECT distinct(trad_symbol)
												FROM mry_comm_rr_trades 
												ORDER BY trad_symbol";
$result_trades_symbol = mysql_query($query_trades_symbol) or die(mysql_error());
while($row_trades_symbol = mysql_fetch_array($result_trades_symbol))
{
 if (!in_array($row_trades_symbol["trad_symbol"],$arr_master)) {
			//sleep(1);
	    echo $row_trades_symbol["trad_symbol"]."...<br>";
			ob_flush();
			flush();
	//insert into sec_master
   $result_insert = mysql_query("insert into sec_master(symbol) values('".$row_trades_symbol["trad_symbol"]."')") or die(mysql_error());
	
	//update sec master with sec name from Yahoo Finance get_company_name($symbol)
   $result_update = mysql_query("update sec_master set description = '".str_replace("'","\'",get_company_name($row_trades_symbol["trad_symbol"]))."' where symbol = '".$row_trades_symbol["trad_symbol"]."'") or die(mysql_error());
 
 }
}

ydebug('Process Finish Time', date('m/d/Y H:i:s a'));
?>
