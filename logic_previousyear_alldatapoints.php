<?
include('includes/dbconnect.php');
include('includes/global.php'); 
include('includes/functions.php'); 

// PROCESS TO CHECK PREVIOUS YEAR NUMBERS
// This process should only kick-off when the current date is 2007-01-18
$date_start = '2006-12-27';
$date_end = '2007-12-26';

$qry_prev_year = "SELECT trad_advisor_code, sum(trad_commission) as trad_commission
									FROM mry_comm_rr_trades 
									WHERE trad_trade_date between '".$date_start."' and '".$date_end."'
									AND trad_is_cancelled = 0
									GROUP BY trad_advisor_code
									ORDER BY trad_advisor_code";
//xdebug('qry_prev_year',$qry_prev_year);
$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
$arr_prev_year = array();
$arr_prev_year_detail = array();
while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
{
$arr_prev_year[$row_prev_year['trad_advisor_code']] = $row_prev_year['trad_commission'];
}
show_array($arr_prev_year);
exit;



// WILL CREATE DATASET FOR PREVIOUS YEAR UNDER THIS CONDITION ONLY
		////
		// Get date in previous year (input and output format: yyyy-mm-dd)
		function get_date_previous_year($dateval) {
		$arr_date = explode("-",$dateval);
		$retval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];
		return $retval;
		}
		
		////
		// Get data in previous year (input and output format: yyyy-mm-dd)
		function get_previous_yr_data($clntval, $dateval, $arr_prev_year, $arr_prev_year_detail) {
		
		//global $arr_prev_year, $arr_prev_year_detail;
		
		 if ($arr_prev_year[$clntval] == "") {
				$arr_out[0] = "";
				$arr_out[1] = "";
				$arr_out[2] = "";
			return $arr_out;
			} else {
				$arr_date = explode("-",$dateval);
				$matchval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];	
		
				$date_old = date('Y-m-d', strtotime($arr_prev_year[$clntval]));
				$date_new = date('Y-m-d', strtotime($matchval));
		
				//xdebug('matchval',$matchval);
				//xdebug('arr_prev_year[$clntval]',$arr_prev_year[$clntval]);
				
				if (samebrokmonth($date_old, $date_new)==1) {
				$arr_out[0] = $arr_prev_year_detail[$clntval][0];
				} else {
				$arr_out[0] = "";
				}
				
				if (samebrokqtr($date_old, $date_new)==1) {
				$arr_out[1] = $arr_prev_year_detail[$clntval][1];
				} else {
				$arr_out[1] = "";
				}
		
				$arr_out[2] = $arr_prev_year_detail[$clntval][2];
			}
			return $arr_out;
		}	
?>