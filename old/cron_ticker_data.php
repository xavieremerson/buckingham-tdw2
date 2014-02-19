<?
//include('includes/functions.php');
//include('includes/dbconnect.php');

$tdate = previous_business_day ();
/////////////////////////// CHECK TO PREVENT CRON REPEAT INSERTS /////////////////////////
	$flag = 0;
	
	$query_check = "SELECT tdat_flag FROM tdat_ticker_data WHERE tdat_trade_date = '".$tdate."'";
	$result_check = mysql_query($query_check) or die(mysql_error());
	$row_check = mysql_fetch_array($result_check);
	$flag = $row_check['tdat_flag'];
//////////////////////////////////////////////////////////////////////////////////////////


$arr_emp_accts = array();
$arr_ticker_trades = array();

$query_emp_accts = "SELECT acct_number FROM Employee_accounts WHERE acct_is_active = '1'";
$result_emp_accts = mysql_query($query_emp_accts) or die(mysql_error());
while($row_emp_accts = mysql_fetch_array($result_emp_accts))
{
	$arr_emp_accts[] = $row_emp_accts["acct_number"];		
}

$query_tickers = "SELECT DISTINCT(trdm_symbol) FROM Trades_m WHERE trdm_trade_date = '".$tdate."' ORDER BY trdm_symbol ASC";
$result_tickers = mysql_query($query_tickers) or die(mysql_error());
while($row_tickers = mysql_fetch_array($result_tickers))
{	
	$cust_count = 0;
	$emp_count = 0;
	$spaces1 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$spaces2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	
	if(strlen($row_tickers["trdm_symbol"]) == 2)
	{
		$spaces1 = $spaces1 . "&nbsp;&nbsp;";
	}

	if(strlen($row_tickers["trdm_symbol"]) == 3)
	{
		$spaces1 = $spaces1 . "&nbsp;";
	}

	$query_trades = "SELECT trdm_symbol, trdm_account_number FROM Trades_m WHERE trdm_trade_date = '".$tdate."' AND trdm_symbol = '".$row_tickers["trdm_symbol"]."'";
	$result_trades = mysql_query($query_trades) or die(mysql_error());
	
	while($row_trades = mysql_fetch_array($result_trades))
	{
		if(in_array ($row_trades["trdm_account_number"], $arr_emp_accts))
		{
			$emp_count++;	
		}
		else
		{
			$cust_count++;
		}
	}
	
	//CHECKING TO PREVENT DUPLICATES
	//if($row_tickers["trdm_symbol"] != $row_check['tdat_ticker'])
	if($flag == 0)
	{	
		$query_insert = "INSERT INTO tdat_ticker_data(tdat_ticker, tdat_cust, tdat_emp, tdat_trade_date) VALUES('".$row_tickers["trdm_symbol"]."','".$cust_count."','".$emp_count."','".$tdate."')";
		$result_insert = mysql_query($query_insert) or die(mysql_error());	
	}
}

$query_update = "UPDATE tdat_ticker_data SET tdat_flag = '1' WHERE tdat_trade_date = '".$tdate."'";
$result_update = mysql_query($query_update) or die(mysql_error());

echo '<br>CRON TICKER DATA SUCCESSFUL!<BR>';

?>