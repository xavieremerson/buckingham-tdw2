<?
include('../includes/functions.php');
include('../includes/global.php');
include('../includes/dbconnect.php');

/*

		$arr_monthname = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$start_year = 2005;

		for ($year=2005; $year<2016; $year++) {
			
			echo "<hr>".$year . "<br>";
			
			foreach($arr_monthname as $monthname) {
				$arr_dates = get_commission_month_dates($monthname, $year);
				echo "Brokerage Month ".$monthname. "-".$year. " : ".format_date_ymd_to_mdy($arr_dates[0]). " to ".format_date_ymd_to_mdy($arr_dates[1])."<br>";
			}
			
		}
*/

$arr_comm_month = xget_commission_month_dates("May","2006");
show_array($arr_comm_month);
		//get possible dates for this module and have it verified
		
////
// GET COMMISSION MONTH BEGIN AND END TRADE DATES BASED ON LOGIC PROVIDED BY BRG
// ESSENTIALLY TRADE DATE CORRESPONDING TO LAST SETTLEMENT FRIDAY IS TAKEN
// FOR ALL MONTHS EXCEPT DECEMBER WHERE THE LAST SETTLEMENT DATE IS CONSIDERED
//
// USAGE: get_commission_month_dates("Feb","2006")
function xget_commission_month_dates($month, $year) {

		$result_ = mysql_query("SELECT brk_start_date, brk_end_date FROM brk_brokerage_months where brk_month = '".$month."' and brk_year = '".$year."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$begin_tradedate = $row["brk_start_date"];
			$end_tradedate = $row["brk_end_date"];
		}

$arr_return_dates = array($begin_tradedate,$end_tradedate);
return $arr_return_dates;
		
}

?>