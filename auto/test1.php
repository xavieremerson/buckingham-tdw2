<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');


	//Previous Business Day should be applied here.
	//$trade_date_to_process = previous_business_day();
	$trade_date_to_process = '2007-04-12';
	
		$dval = explode("-", $trade_date_to_process); 
		$y1 = $dval[0];
		$m1 = $dval[1];
		$d1 = $dval[2];
		
		$timeval = mktime(0,0,0, $m1, $d1, $y1);
		
		$newtime = $timeval + (60*60*24);	
		$nextday = date("Y-m-d", $newtime);
		
		echo $nextday;
	
?>