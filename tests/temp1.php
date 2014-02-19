<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

	//Previous Business Day should be applied here.
	//$trade_date_to_process = previous_business_day();
	
	$trade_date_to_process = previous_business_day();
	
	echo $trade_date_to_process;
	
	echo date('Y-m-d');