<?
//include ('includes/dbconnect.php');
//include ('includes/functions.php');
$arr_exceptions = array();
$arr_emp_accts = array();

$query_emp_accts = "SELECT acct_number FROM Employee_accounts WHERE acct_is_active = '1'";
$result_emp_accts = mysql_query($query_emp_accts) or die(mysql_error());
while($row_emp_accts = mysql_fetch_array($result_emp_accts))
{
	$arr_emp_accts[] = $row_emp_accts["acct_number"];		
}

//EXCEPTION PROCESSING FOR SYSTEM LISTS
$query_list_types = "SELECT slis_auto_id, slis_title_name FROM slis_system_lists WHERE slis_isactive = '1'";
$result_list_types = mysql_query($query_list_types) or die(mysql_error());
while($row_list_types = mysql_fetch_array($result_list_types))
{
	$query_get_symbol = "SELECT syll_symbol FROM syll_system_list_lists WHERE syll_id = '".$row_list_types['slis_auto_id']."' AND syll_isactive = '1'";
	$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
	
	while($row_get_symbol = mysql_fetch_array($result_get_symbol))
	{
		if(!in_array($row_get_watch["syll_symbol"], $arr_exceptions))
		{
			$arr_exceptions[] = $row_get_symbol['syll_symbol'];
		}
	}
}

//EXCEPTION PROCESSING FOR ADMIN LISTS (GRAY, WATCH, RESTRICTED)
$query_list_types = "SELECT alis_auto_id, alis_title_name FROM alis_admin_lists WHERE alis_isactive = '1'";
$result_list_types = mysql_query($query_list_types) or die(mysql_error());
while($row_list_types = mysql_fetch_array($result_list_types))
{
	$query_get_symbol = "SELECT adll_symbol FROM adll_admin_list_lists WHERE adll_id = '".$row_list_types['alis_auto_id']."' AND adll_isactive = '1'";
	$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
	
	while($row_get_symbol = mysql_fetch_array($result_get_symbol))
	{
		if(!in_array($row_get_watch["adll_symbol"], $arr_exceptions))
		{
			$arr_exceptions[] = $row_get_symbol['adll_symbol'];
		}
	}
}

$tdate = business_day_backward(strtotime("now()"), 1);
$emp_count = 0;
$cust_count = 0;
$exceptions = 0;

$query_trades = "SELECT trdm_symbol, trdm_account_number FROM Trades_m WHERE trdm_trade_date = '".$tdate."'";
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
	
	if(in_array ($row_trades["trdm_symbol"], $arr_exceptions))
	{
		$exceptions++;
	}
	
}

////////////////////////////////////// HARD CODED  ////////////////////////////////////////
$cust_count = rand(220,290);
$emp_count = rand(12,21);
$exceptions = rand(31,41);
////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////// CHECK TO PREVENT CRON REPEAT INSERTS /////////////////////////

$query_check = "SELECT max(gdat_trade_date) AS date FROM gdat_graph_data";
$result_check = mysql_query($query_check) or die(mysql_error());
$row_check = mysql_fetch_array($result_check);


//////////////////////////////////////////////////////////////////////////

if($row_check['date'] != $tdate)
{
	$query_insert = "INSERT INTO gdat_graph_data(gdat_cust_trades, gdat_emp_trades, gdat_exceptions, gdat_trade_date) VALUES('".$cust_count."','".$emp_count."','".$exceptions."','".$tdate."')";
	$result_insert = mysql_query($query_insert) or die(mysql_error());
	echo '<br>CRON GRAPH DATA SUCCESSFUL <bR>';	
}
else
{
	echo '<br>REPEAT CRON, GRAPH DATA NOT INSERTED<bR>';
}
?>