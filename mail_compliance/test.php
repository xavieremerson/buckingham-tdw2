<?
require_once("../includes/functions.php");
require_once("../includes/dbconnect.php");
require_once("../includes/global.php");

$start_date_seed = '2012-11-28';
echo "Seed ".$start_date_seed."<br>";

for ($bizdays=1; $bizdays < 300; $bizdays++) {
	if (strtotime(business_day_backward(strtotime($start_date_seed),$bizdays+1)) < strtotime("2012-04-13")) {
		echo business_day_backward(strtotime($start_date_seed),$bizdays)."<br>";
		echo "Exit condition met... Program exiting normally<br>";
		ob_flush();
		flush();
		exit;
	} else {
		$trade_date_to_process = business_day_backward(strtotime($start_date_seed),$bizdays);
		echo "Processing Trade Date: ".$trade_date_to_process."<br>";
	}
}

?> 