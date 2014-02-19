<?
include('includes/dbconnect.php');
include('includes/functions.php');

$tdate1 = business_day(strtotime("now()"), 1);

//echo $tdate1. "<br><br>";
$query_update = "UPDATE Trades_m SET trdm_trade_date = '".$tdate1."' WHERE trdm_auto_id = '77705' OR trdm_auto_id = '77697' OR trdm_auto_id = '77688' OR trdm_auto_id = '77678'";

echo $query_update;
/*$result_update = mysql_query($query_update) or die(mysql_error());



$tdate2 = business_day(strtotime("now()"), 4);
$tdate3 = business_day(strtotime("now()"), 7);
$tdate4 = business_day(strtotime("now()"), 8);
$tdate5 = business_day(strtotime("now()"), 12);


$query_update2 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate2."' WHERE trdm_auto_id = '72068'";
$result_update2 = mysql_query($query_update2) or die(mysql_error());

$query_update3 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate3."' WHERE trdm_auto_id = '72067'";
$result_update3 = mysql_query($query_update3) or die(mysql_error());

$query_update4 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate4."' WHERE trdm_auto_id = '72066'";
$result_update4 = mysql_query($query_update4) or die(mysql_error());

$query_update5 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate5."' WHERE trdm_auto_id = '72065'";
$result_update5 = mysql_query($query_update5) or die(mysql_error());
*/
/*
echo $tdate1 . "<BR>";
echo $tdate2 . "<BR>";
echo $tdate3 . "<BR>";
echo $tdate4 . "<BR>";
echo $tdate5. "<BR>";
*/
?> 