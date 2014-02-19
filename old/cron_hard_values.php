<?

//GRAPH VALUES
$tdate = business_day_backward(strtotime("now()"), 1);
//for($i = 1; $i <= 66; $i++)
//{
	//$tdate = business_day_backward(strtotime("now()"), (67-$i));
	$rand1 = rand(150,200);
	$rand2 = rand(25,50);
	$rand3 = rand(0,30);

 	$query_insert = "INSERT INTO gdat_graph_data(gdat_cust_trades, gdat_emp_trades, gdat_exceptions, gdat_trade_date) VALUES('".$rand1."', '".$rand2."', '".$rand3."', '".$tdate."')";
	$result_insert = mysql_query($query_insert) or die(mysql_error());
	
//}

//LIST VALUES
$query_symb = "SELECT list_symbol FROM ldat_list_data WHERE list_trade_date = '2005-04-18'";
$result_symb = mysql_query($query_symb) or die(mysql_error());

while($row_symb = mysql_fetch_array($result_symb))
{
	$query_insert = "INSERT INTO ldat_list_data(list_watch, list_gray, list_restricted, list_marketmaker, list_analyst, list_banker, list_symbol, list_trade_date) 
					 VALUES('".rand(0,1)."', '".rand(0,1)."', '".rand(0,1)."', '".rand(0,1)."', '".rand(0,1)."', '".rand(0,1)."', '".$row_symb['list_symbol']."', '".$tdate."')";

	$result_insert = mysql_query($query_insert) or die(mysql_error());
}

//HOLDING LIST
$query_holding = "INSERT INTO lhol_holding_list(lhol_holding, lhol_trade_date) VALUES('".rand(3,5)."', '".$tdate."')";
$result_holding = mysql_query($query_holding) or die(mysql_error());

//TICKER DATA
$query_ticker = "SELECT * FROM tdat_ticker_data WHERE tdat_trade_date = '2005-04-20'";
$result_ticker = mysql_query($query_ticker) or die(mysql_error());

while($row_ticker = mysql_fetch_array($result_ticker))
{
	$query_insert_data = "INSERT INTO tdat_ticker_data(tdat_ticker, tdat_cust, tdat_emp, tdat_trade_date, tdat_isactive) 
						  VALUES('".$row_ticker["tdat_ticker"]."', '".$row_ticker["tdat_cust"]."', '".$row_ticker["tdat_emp"]."', '".$tdate."', '".$row_ticker["tdat_isactive"]."')";
	$result_insert_data = mysql_query($query_insert_data) or die(mysql_error()); 
}




?>