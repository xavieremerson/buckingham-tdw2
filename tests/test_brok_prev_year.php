<?
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');

////
// Get date in previous year (input and output format: yyyy-mm-dd)
function get_date_previous_year($dateval) {
$arr_date = explode("-",$dateval);
$retval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];
return $retval;
}

xdebug ("get_date_previous_year(previous_business_day())",get_date_previous_year(previous_business_day()));

SELECT comm_advisor_code, max( comm_trade_date ) 
FROM mry_comm_rr_level_a
WHERE comm_rr = '044'
AND `comm_advisor_code` = 'BALY'
AND comm_trade_date <= '2006-03-22'
GROUP BY comm_advisor_code
ORDER BY `comm_advisor_code` , `comm_trade_date` 

?>

