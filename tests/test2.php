<?
include('includes/dbconnect.php');
include('includes/functions.php');


//============================================================================================
//some variables used down below
$arr_commission_clients = array();


$trade_date_to_process = '2007-01-19';

//$rep_to_process = '035'; //'028';
$rep_to_process = '044';

//*********************************************************************************************
// PROCESS TO CHECK PREVIOUS YEAR NUMBERS
// This process should only kick-off when the current date is 2007-01-18
$date_considered = '2007-01-18';
if (strtotime($date_considered) > strtotime('2007-01-17') ) {

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
		
		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year = "SELECT comm_advisor_code, max( comm_trade_date ) as comm_trade_date 
											FROM mry_comm_rr_level_a
											WHERE comm_rr = '".$rep_to_process."'
											AND comm_trade_date <= '".$previous_year_date."'
											AND EXTRACT(YEAR FROM comm_trade_date) = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY comm_advisor_code
											ORDER BY comm_advisor_code";
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		$arr_prev_year_detail = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["comm_advisor_code"]] = $row_prev_year["comm_trade_date"];
		
			$qry_prev_year_detail = "SELECT * 
																 FROM mry_comm_rr_level_a
																WHERE comm_advisor_code = '".$row_prev_year["comm_advisor_code"]."'
																AND comm_trade_date = '".$row_prev_year["comm_trade_date"]."'
																AND comm_rr = '".$rep_to_process."'";
																	
			//xdebug('qry_prev_year_detail',$qry_prev_year_detail);
			$result_prev_year_detail = mysql_query($qry_prev_year_detail) or die (tdw_mysql_error($qry_prev_year_detail));
			$arr_prev_year_detail_data = array();
			while ( $row_prev_year_detail = mysql_fetch_array($result_prev_year_detail) ) 
			{
			 $arr_prev_year_detail_data[0] = $row_prev_year_detail["comm_mtd"]; 
			 $arr_prev_year_detail_data[1] = $row_prev_year_detail["comm_qtd"]; 
			 $arr_prev_year_detail_data[2] = $row_prev_year_detail["comm_ytd"]; 
			}
			
			$arr_prev_year_detail[$row_prev_year["comm_advisor_code"]] = $arr_prev_year_detail_data;
		}

} 

$time=getmicrotime(); 
		$arr_prev_yr = get_previous_yr_data('BALY', '2007-01-19', $arr_prev_year, $arr_prev_year_detail);
		show_array($arr_prev_yr);
echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 						

?>