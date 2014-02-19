<?
//include ('includes/dbconnect.php');
//include ('includes/functions.php');
$tdate = business_day_backward(strtotime("now()"), 1);

$query_update = "UPDATE Trades_m SET trdm_trade_date = '".$tdate."' WHERE trdm_auto_id = '28622' OR trdm_auto_id = '28617' OR trdm_auto_id = '28614'";
$result_update = mysql_query($query_update) or die(mysql_error());

echo '<br>CRON LAUNDERING DATA SUCCESSFUL!<br>';
?> 