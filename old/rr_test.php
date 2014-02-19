		
<?php
include('includes/dbconnect.php');
include('includes/functions.php');

$data_emp = array();	

//CUSTOMER TRADES
//$data1 = array(50, 55, 47, 34, 42, 49, 63, 62, 73, 59, 56, 50, 64, 60, 67, 67,
  //  58, 59, 73, 77, 84, 82, 80, 84, 98);
$data_cust = array();
	
//EXCEPTIONS
//$data2 = array(36, 28, 25, 33, 38, 20, 22, 30, 25, 33, 30, 24, 28, 15, 21, 26,
    //46, 42, 48, 45, 43, 52, 64, 60, 70);
$data_exceptions = array();
$data_dates = array();

$tdate = business_day_backward(strtotime("now()"), 66);
$query_data = "SELECT * FROM gdat_graph_data WHERE gdat_trade_date >= '".$tdate."' ORDER BY gdat_trade_date ASC";
$result_data = mysql_query($query_data) or die(mysql_error());



echo "<BR>" . $query_data. "<BR>";
$i = 0;
while($row_data = mysql_fetch_array($result_data))
{
    
	$data_emp[] = $row_data["gdat_emp_trades"];
	$data_cust[] = $row_data["gdat_cust_trades"];
	$data_exceptions[] = $row_data["gdat_exceptions"];
	
	$data_dates[] = str_replace('-','/',substr($row_data["gdat_trade_date"],5,5));
	
	echo "<BR>" . str_replace('-','/',substr($row_data["gdat_trade_date"],5,5));
}


echo '<Br> data_emp ' . count($data_emp);
echo '<Br>data_cust ' . count($data_cust);
echo '<Br>data_exceptions ' . count($data_exceptions);
echo '<Br> data_dates ' . count($data_dates);


?>

