<?
//include('includes/dbconnect.php');
//include('includes/functions.php');

$tdate1 = business_day_backward(strtotime("now()"), 1);
$sdate1 = business_day_forward(strtotime("now()"), 2);

echo $tdate1. "<br><br>";
$query_update = "UPDATE Trades_m SET trdm_trade_date = '".$tdate1."', trdm_settle_date = '".$sdate1."' WHERE trdm_auto_id = '250' OR trdm_auto_id = '270' OR trdm_auto_id = '375' OR trdm_auto_id = '784'";

echo $query_update. "<br><br>";																																				
$result_update = mysql_query($query_update) or die(mysql_error());

$tdate2 = business_day_backward(strtotime("now()"), 4);
$tdate3 = business_day_backward(strtotime("now()"), 7); 
$tdate4 = business_day_backward(strtotime("now()"), 8);
$tdate5 = business_day_backward(strtotime("now()"), 12); 

$sdate2 = business_day_backward(strtotime("now()"), 1);
$sdate3 = business_day_backward(strtotime("now()"), 4); 
$sdate4 = business_day_backward(strtotime("now()"), 5);
$sdate5 = business_day_backward(strtotime("now()"), 9); 


$query_update2 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate2."', trdm_settle_date = '".$sdate2."'  WHERE trdm_auto_id = '3335'";
echo $query_update2. "<br><br>";																																				
$result_update2 = mysql_query($query_update2) or die(mysql_error());

$query_update3 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate3."', trdm_settle_date = '".$sdate3."'  WHERE trdm_auto_id = '3375'";
echo $query_update3. "<br><br>";																																				
$result_update3 = mysql_query($query_update3) or die(mysql_error());

$query_update4 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate4."', trdm_settle_date = '".$sdate4."'  WHERE trdm_auto_id = '3386'";
echo $query_update4. "<br><br>";																																				
$result_update4 = mysql_query($query_update4) or die(mysql_error());

$query_update5 = "UPDATE Trades_m SET trdm_trade_date = '".$tdate5."', trdm_settle_date = '".$sdate5."'  WHERE trdm_auto_id = '28630'";
echo $query_update5. "<br><br>";																																				
$result_update5 = mysql_query($query_update5) or die(mysql_error());

/*

echo $tdate1 . "<BR>";
echo $tdate2 . "<BR>";
echo $tdate3 . "<BR>";
echo $tdate4 . "<BR>";
echo $tdate5. "<BR>";

78970 80610729 MSFT   MICROSOFT CP   "Previous day"
78296 40500045 INTC   INTEL CORP     "Previous day"
79651 30580265 FD     FED DEPT STRS  "Previous day"
79645 30586523 ORCL   ORACLE CORP    "Previous day"
 
New Items!!!
30092 80610729 MSFT   MICROSOFT CP   "Previous day"
30069 40500045 INTC   INTEL CORP     "Previous day"
30034 30580265 FD     FED DEPT STRS  "Previous day"
14187 30586523 ORCL   ORACLE CORP    "Previous day"

77113 80610729 MSFT   MICROSOFT CP   4 days prior
77043 40500045 INTC   INTEL CORP     7 days prior
72066 30580265 FD     FED DEPT STRS  8 days prior
72065 30586523 ORCL   ORACLE CORP    12 days prior

*/
?> 