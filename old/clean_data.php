<?
include('includes/dbconnect.php'); 
include('includes/functions.php'); 

function create_time()
{
$arr_hour = array();
$arr_hour[0] = "09";
$arr_hour[1] = "10";
$arr_hour[2] = "11";
$arr_hour[3] = "12";
$arr_hour[4] = "01";
$arr_hour[5] = "02";
$arr_hour[6] = "03";
$arr_hour[7] = "04";

$hour = $arr_hour[rand(0,7)];

$min = rand(0,59);
if($min  <= 9)
$min = '0' . $min;

$sec = rand(0,59);
if($sec  <= 9)
$sec = '0' . $sec;

$ampm = '';
if($hour == '09' OR $hour == '10' OR $hour == '11')
$ampm = 'AM';
else
$ampm = 'PM';

if($hour == '04')
{
	$min = '00';
	$sec = '00';
}

$time = $hour.":" . $min . ":" . $sec . " " . $ampm;

return $time;
}



$query_get = "SELECT trdm_auto_id FROM tradeset9";
$result_get = mysql_query($query_get) or die(mysql_error());

while($row_get = mysql_fetch_array($result_get))
{

	$query = "UPDATE tradeset9 SET trdm_trade_time = '".create_time()."' WHERE trdm_auto_id = ".$row_get["trdm_auto_id"];
	//echo $query ."<BR>";
	
	$result = mysql_query($query) or die(mysql_error());
	
}
echo "<BR> PROCESS COMPLETE";
?>