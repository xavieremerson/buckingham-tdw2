<?
//include('includes/dbconnect.php');
//include('includes/functions.php');

$tdate = previous_business_day ();
$arr_emp_accts = array();


/////////////////////////// CHECK TO PREVENT CRON REPEAT INSERTS /////////////////////////
	$flag = 0;
	/*
	$check_type = array();
	$check_list = array();
	
	$query_check = "SELECT mlis_list_type_id, mlis_list_id FROM mlis_main_list WHERE mlis_trade_date = '".$tdate."'";
	$result_check = mysql_query($query_check) or die(mysql_error());
	while($row_check = mysql_fetch_array($result_check))
	{
		$check_type[] = $row_check['mlis_list_type_id'];
		$check_list[] = $row_check['mlis_list_id'];	
	}
	*/
	
	$query_check = "SELECT mlis_flag FROM mlis_main_list WHERE mlis_trade_date = '".$tdate."'";
	$result_check = mysql_query($query_check) or die(mysql_error());
	$row_check = mysql_fetch_array($result_check);
	$flag = $row_check['mlis_flag'];
	
//////////////////////////////////////////////////////////////////////////////////////////


//EMPLOYEE ACCOUNTS
$query_emp_accts = "SELECT acct_number FROM Employee_accounts WHERE acct_is_active = '1'";
$result_emp_accts = mysql_query($query_emp_accts) or die(mysql_error());
while($row_emp_accts = mysql_fetch_array($result_emp_accts))
{
	$arr_emp_accts[] = $row_emp_accts["acct_number"];
}

/***************** PROCESSING SYSTEM LISTS (MARKET MAKER, ANALYST, BANKER, HOLDING PERIOD VIOLATION LISTS) ***************************/
$query_list_types = "SELECT slis_auto_id, slis_list_type_id, slis_title_name FROM slis_system_lists WHERE slis_isactive = '1'";
$result_list_types = mysql_query($query_list_types) or die(mysql_error());
//START WHILE 1
while($row_list_types = mysql_fetch_array($result_list_types))
{
	//MARKETMAKER, ANALYST, BANKER
	if($row_list_types['slis_auto_id'] != 4)
	{
		$query_get_symbol = "SELECT syll_symbol FROM syll_system_list_lists WHERE syll_id = '".$row_list_types['slis_auto_id']."' AND syll_isactive = '1'";
		$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
		
		$arr_symbol = array();
		$count_list = 0;
		while($row_get_symbol = mysql_fetch_array($result_get_symbol))
		{
			$arr_symbol[] = $row_get_symbol['syll_symbol'];
		}
		
		$query_get_trades = "SELECT trdm_account_number, trdm_symbol FROM Trades_m WHERE trdm_trade_date = '".$tdate."'";
		$result_get_trades = mysql_query($query_get_trades) or die(mysql_error());
		while($row_get_trades = mysql_fetch_array($result_get_trades))
		{
			if(in_array($row_get_trades["trdm_symbol"],$arr_symbol) AND in_array($row_get_trades["trdm_account_number"],$arr_emp_accts))
			{
				$count_list++;
			}
		}
	} 
	// HOLDING PERIOND
	else
	{
		$hdate =  business_day_backward(strtotime("now"), (20+1));
		$arr_acctnames = array();
		$count_list = 0;
		
		$query = "trdm_account_number = '3123123123131' ";
		$qry_acctnames = "SELECT acct_number, acct_name1 FROM Employee_accounts";
		$result_acctnames = mysql_query($qry_acctnames) or die(mysql_error());
		while($row= mysql_fetch_array($result_acctnames))
		{
			$arr_acctnames[$row["acct_number"]] = $row["acct_name1"];
			$query = $query . " OR trdm_account_number = '" . $row["acct_number"] . "' ";
		}
		
		//GET ALL THE TRADES FROM LAST BUSINESS DAY THAT WERE SELL 
		$get_sell_trades = "SELECT * FROM Trades_m WHERE trdm_trade_date = '".$tdate."' AND (trdm_buy_sell = 'sl' OR trdm_buy_sell = 'Sell') AND (".$query.")";
		$result_sell_trades = mysql_query($get_sell_trades) or die(mysql_error());
		
		while($row_sell_trades = mysql_fetch_array($result_sell_trades))
		{
			$get_buy_trades = "SELECT * FROM Trades_m WHERE trdm_account_number = '".$row_sell_trades["trdm_account_number"]."' AND trdm_symbol = '".$row_sell_trades["trdm_symbol"]."' AND (trdm_buy_sell = 'by' OR trdm_buy_sell = 'Buy') AND trdm_trade_date >= '".$hdate."'";
			$result_buy_trades = mysql_query($get_buy_trades) or die(mysql_error());
			$count_list = $count_list + mysql_num_rows($result_buy_trades);
		}
	}
	
	/*
	//CHECKING TO PREVENT DUPLICATES
	$proceed = 0;
	for($i = 0; $i < count($check_list); $i++)
	{
		if($row_list_types["slis_list_type_id"] != $check_type[$i])
		{
			if($row_list_types["slis_auto_id"] != $check_list[$i])
			{
				$proceed = 1;
			}
		}
		else
		{
			if($row_list_types["slis_auto_id"] != $check_list[$i])
			{
				$proceed = 1;
			}
		}
		
		 OR ($row_list_types["slis_auto_id"] != $check_list[$i]))
		{
			$proceed = 1 ;	
		}
	}	
	*/
	if($flag == 0)
	{
		$query_insert = "INSERT INTO mlis_main_list(mlis_list_id, mlis_list_type_id, mlis_num_trades, mlis_trade_date)  
						 VALUES('".$row_list_types["slis_auto_id"]."', '".$row_list_types["slis_list_type_id"]."', '".$count_list."',  '".$tdate."')";
		$result_insert = mysql_query($query_insert) or die(mysql_error());
	}
}
/*********************************************************************************************************************/

/***************************************** PROCESSING ADMIN LISTS *********************************************/
$query_list_types = "SELECT alis_auto_id, alis_list_type_id, alis_title_name FROM alis_admin_lists WHERE alis_isactive = '1'";
$result_list_types = mysql_query($query_list_types) or die(mysql_error());
//START WHILE 1
while($row_list_types = mysql_fetch_array($result_list_types))
{
	$query_get_symbol = "SELECT adll_symbol FROM adll_admin_list_lists WHERE adll_id = '".$row_list_types['alis_auto_id']."' AND adll_isactive = '1'";
	$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
	
	$arr_symbol = array();
	$count_list = 0;
	while($row_get_symbol = mysql_fetch_array($result_get_symbol))
	{
		$arr_symbol[] = $row_get_symbol['adll_symbol'];
	}
	
	$query_get_trades = "SELECT trdm_account_number, trdm_symbol FROM Trades_m WHERE trdm_trade_date = '".$tdate."'";
	$result_get_trades = mysql_query($query_get_trades) or die(mysql_error());
	while($row_get_trades = mysql_fetch_array($result_get_trades))
	{
		if(in_array($row_get_trades["trdm_symbol"],$arr_symbol) AND in_array($row_get_trades["trdm_account_number"],$arr_emp_accts))
		{
			$count_list++;
		}
	}
	
	if($flag == 0)
	{
		$query_insert = "INSERT INTO mlis_main_list(mlis_list_id, mlis_list_type_id, mlis_num_trades, mlis_trade_date)  
						 VALUES('".$row_list_types["alis_auto_id"]."', '".$row_list_types["alis_list_type_id"]."', '".$count_list."',  '".$tdate."')";
		$result_insert = mysql_query($query_insert) or die(mysql_error());
	}
}
/*************************************************************************************************************/

/***************************************** PROCESSING USER LISTS *********************************************/
$query_list_types = "SELECT usli_auto_id, usli_list_type_id, usli_title_name FROM usli_user_lists WHERE usli_isactive = '1'";
$result_list_types = mysql_query($query_list_types) or die(mysql_error());
//START WHILE 1
while($row_list_types = mysql_fetch_array($result_list_types))
{
	$query_get_symbol = "SELECT usll_symbol FROM usll_user_list_lists WHERE usll_list_id = '".$row_list_types['usli_auto_id']."' AND usll_isactive = '1'";
	$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
	
	$arr_symbol = array();
	$count_list = 0;
	while($row_get_symbol = mysql_fetch_array($result_get_symbol))
	{
		$arr_symbol[] = $row_get_symbol['usll_symbol'];
	}
	
	$query_get_trades = "SELECT trdm_account_number, trdm_symbol FROM Trades_m WHERE trdm_trade_date = '".$tdate."'";
	$result_get_trades = mysql_query($query_get_trades) or die(mysql_error());
	while($row_get_trades = mysql_fetch_array($result_get_trades))
	{
		if(in_array($row_get_trades["trdm_symbol"],$arr_symbol) AND in_array($row_get_trades["trdm_account_number"],$arr_emp_accts))
		{
			$count_list++;
		}
	}


	//CHECKING TO PREVENT DUPLICATES
	if($flag == 0)
	{
		$query_insert = "INSERT INTO mlis_main_list(mlis_list_id, mlis_list_type_id, mlis_num_trades, mlis_trade_date)  
						 VALUES('".$row_list_types["usli_auto_id"]."', '".$row_list_types["usli_list_type_id"]."', '".$count_list."',  '".$tdate."')";
		$result_insert = mysql_query($query_insert) or die(mysql_error());
	}
}
/*************************************************************************************************************/

$query_update = "UPDATE mlis_main_list SET mlis_flag = '1' WHERE mlis_trade_date = '".$tdate."'";
$result_update = mysql_query($query_update) or die(mysql_error());

echo '<br>CRON LIST DATA SUCCESSFUL!<BR>';
?>



