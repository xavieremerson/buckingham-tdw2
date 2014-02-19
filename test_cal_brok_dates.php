<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

function get_quarter_dates ($q, $y, $b="B") { // Brokerage vs Calendar

$arr_qtrs = array(1=>"Jan|Mar",2=>"Apr|Jun",3=>"Jul|Sep",4=>"Oct|Dec"); 
$arr_qtrs_startmon = array(1=>"01",2=>"04",3=>"07",4=>"10"); 
$arr_qtrs_endmon =   array(1=>"03",2=>"06",3=>"09",4=>"12"); 

$arr_start_end_months = explode("|",$arr_qtrs[$q]);

	if ($b=="B") {
		$result_ = mysql_query("SELECT brk_start_date FROM brk_brokerage_months where brk_month = '".$arr_start_end_months[0]."' and brk_year = '".$y."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$begin_tradedate = $row["brk_start_date"];
		}

		$result_ = mysql_query("SELECT brk_end_date FROM brk_brokerage_months where brk_month = '".$arr_start_end_months[1]."' and brk_year = '".$y."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$end_tradedate = $row["brk_end_date"];
		}

		$arr_return_dates = array($begin_tradedate,$end_tradedate);
		return $arr_return_dates;

	} else {
		//to be programmed
		$sdate = $y."-".$arr_qtrs_startmon[$q]."-01";
		$edate = $y."-".$arr_qtrs_endmon[$q]."-".idate('d', mktime(0, 0, 0, ($arr_qtrs_endmon[$q] + 1), 0, $y));
		return array($sdate,$edate);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
test
<?
$trade_date_to_process = '2009-02-23';
xdebug("trade_date_to_process",$trade_date_to_process);

//mtd
xdebug("mtd", substr($trade_date_to_process,0,8)."01");




//qtd
$arr_month_qtr = array("01"=>"1","02"=>"1","03"=>"1","04"=>"2","05"=>"2","06"=>"2","07"=>"3","08"=>"3","09"=>"3","10"=>"4","11"=>"4","12"=>"4",);
show_array(get_quarter_dates($arr_month_qtr[substr($trade_date_to_process,5,2)],substr($trade_date_to_process,0,4),"C"));


//ytd



?>




</body>
</html>
