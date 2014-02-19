<?
include('includes/dbconnect.php');
////
// Show variable values and debugging information $show_debug = 1; shows debug information
function xdebug ($varname, $varval) {
$show_debug = 1;
	if ($show_debug == 1 ) {
	echo '<font color="#0000FF"><strong>'.$varname."</strong></font> = [".$varval."]<br>";
	}
}

////
// Echo the contents of a simple or nested array (Helpful with POST/GET variables)
function show_array($array) { 
    foreach ($array as $key => $value) { 
        if (is_array($value)) { 
            echo "[" .$key. "]<br>";
						show_array($value); 
        } else { 
            echo "&nbsp;&nbsp;".$key . " = [" .$value. "]<br>"; 
        } 
    } 
}
////
// same brokerage month? take YYYY-MM-DD as input
function samebrokmonth($old, $new) {
		$bmqy_date_old=explode("-",get_brok_mqy($old));
		$bmqy_date_new=explode("-",get_brok_mqy($new));
    if ($bmqy_date_old[2] == $bmqy_date_new[2] AND $bmqy_date_old[0] == $bmqy_date_new[0]) {
		return 1;
		} else {
		return 0;
		}
}	

////
// Given a date find the brokerage year, qtr and month
// Date input is YYYY-MM-DD
// Return is BM-BQ-BY e.g. 12-4-2006

function get_brok_mqy($date) {

	$arr_date = explode("-",$date);
	$arr_brok_month_dates = get_commission_month_dates(flip_month_display($arr_date[1]), $arr_date[0]);
	
	//print_r($arr_brok_month_dates);
	
	$start_date = $arr_brok_month_dates[0];
	$end_date = $arr_brok_month_dates[1];
	
	$arr_start_date = explode("-",trim($start_date)); 
	$arr_end_date = explode("-",trim($end_date)); 
	
	// mktime(hour, minute, second, month, day, year)
	//check for the condition which will tell us what the brokerage month, qtr, year is.
	if (
					mktime(0,0,0,$arr_date[1],$arr_date[2],$arr_date[0]) >= mktime(0,0,0,$arr_start_date[1],$arr_start_date[2],$arr_start_date[0]) 
			AND mktime(0,0,0,$arr_date[1],$arr_date[2],$arr_date[0]) <= mktime(0,0,0,$arr_end_date[1],$arr_end_date[2],$arr_end_date[0])
		 ) {
		$ret_brok_month = $arr_date[1];
				switch ($arr_date[1]) {
					case "01":
						 $ret_brok_qtr = "1";			 
						 break;
					case "02":
						 $ret_brok_qtr = "1";			 
						 break;
					case "03":
						 $ret_brok_qtr = "1";			 
						 break;
					case "04":
						 $ret_brok_qtr = "2";			 
						 break;
					case "05":
						 $ret_brok_qtr = "2";			 
						 break;
					case "06":
						 $ret_brok_qtr = "2";			 
						 break;
					case "07":
						 $ret_brok_qtr = "3";			 
						 break;
					case "08":
						 $ret_brok_qtr = "3";			 
						 break;
					case "09":
						 $ret_brok_qtr = "3";			 
						 break;
					case "10":
						 $ret_brok_qtr = "4";			 
						 break;
					case "11":
						 $ret_brok_qtr = "4";			 
						 break;
					case "12":
						 $ret_brok_qtr = "4";			 
						 break;
					}	
			 	$ret_brok_year = $arr_date[0];
	} else {
			switch ($arr_date[1]) {
			case "01":
				 $ret_brok_month = "02";
				 $ret_brok_qtr = "1";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "02":
				 $ret_brok_month = "03";
				 $ret_brok_qtr = "1";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "03":
				 $ret_brok_month = "04";
				 $ret_brok_qtr = "2";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "04":
				 $ret_brok_month = "05";
				 $ret_brok_qtr = "2";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "05":
				 $ret_brok_month = "06";
				 $ret_brok_qtr = "2";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "06":
				 $ret_brok_month = "07";
				 $ret_brok_qtr = "3";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "07":
				 $ret_brok_month = "08";
				 $ret_brok_qtr = "3";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "08":
				 $ret_brok_month = "09";
				 $ret_brok_qtr = "3";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "09":
				 $ret_brok_month = "10";
				 $ret_brok_qtr = "4";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "10":
				 $ret_brok_month = "11";
				 $ret_brok_qtr = "4";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "11":
				 $ret_brok_month = "12";
				 $ret_brok_qtr = "4";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "12":
				 $ret_brok_month = "01";
				 $ret_brok_qtr = "1";			 
				 $ret_brok_year = $arr_date[0]+1;
				 break;
			}	
				xdebug("ret_brok_month",$ret_brok_month);
				xdebug("ret_brok_qtr",$ret_brok_qtr);
				xdebug("ret_brok_year",$ret_brok_year);
	}
	
	return $ret_brok_month."-".$ret_brok_qtr."-".$ret_brok_year;
 	
}

// USAGE: get_commission_month_dates("Feb","2006")
function get_commission_month_dates($month, $year) {

		$result_ = mysql_query("SELECT brk_start_date, brk_end_date FROM brk_brokerage_months where brk_month = '".$month."' and brk_year = '".$year."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$begin_tradedate = $row["brk_start_date"];
			$end_tradedate = $row["brk_end_date"];
		}

		$arr_return_dates = array($begin_tradedate,$end_tradedate);
		return $arr_return_dates;

}

////
// Either the input is Feb or 02, the return will be flipped
// Used to create function arguments
function flip_month_display($month) {

		$arr_monthname     = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$arr_months    		 = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

		if (in_array($month, $arr_monthname)) {
			$arr_flip_monthname = array_flip($arr_monthname);
			return $arr_months[$arr_flip_monthname[$month]];

		} elseif (in_array($month, $arr_months)) {
			$arr_flip_months = array_flip($arr_months);
			return $arr_monthname[$arr_flip_months[$month]];
		
		} else {
		  return "Invalid input to function";
		}
}

////
// same brokerage quarter? take YYYY-MM-DD as input
function samebrokqtr($old, $new) {
		$bmqy_date_old=explode("-",get_brok_mqy($old));
		$bmqy_date_new=explode("-",get_brok_mqy($new));
    if ($bmqy_date_old[2] == $bmqy_date_new[2] AND $bmqy_date_old[1] == $bmqy_date_new[1]) {
		return 1;
		} else {
		return 0;
		}
}	

////
// Used to calculate page load time
function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec*1000); 
} 


function previous_business_day($dateval=NULL) {

	if ($dateval==NULL) {
		$working_dateval = date('Y-m-d');
	} else {
		$working_dateval = $dateval;
	}
	
	$i = 1;
	while ($i < 7) {
		 if (date("w",strtotime($working_dateval)-(60*60*24*$i)) > 0 AND
				 date("w",strtotime($working_dateval)-(60*60*24*$i)) < 6 AND
				 check_holiday(date("Y-m-d", strtotime($working_dateval)-(60*60*24*$i))) == 0 ) {
				$val_pbd = date("Y-m-d",strtotime($working_dateval)-(60*60*24*$i));
			 return $val_pbd;
		 } else {
				$i = $i + 1;
		 }
	}
}


////
// Check if a given date is a holiday based on holiday entry in table
function check_holiday ($checkdate) {
	$check = mysql_query("SELECT holi_date from holidays where holi_date = '$checkdate'") or die (mysql_error());
  if (mysql_num_rows($check) >= 1) {
	return 1;
	} else {
	return 0;
	}		
}


$time=getmicrotime(); 

//======================================================================================================
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
echo "can proceed <br>";
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
		
		//echo ">>>>>>> X<br>";
		//show_array($arr_prev_year);									
		//show_array($arr_prev_year_detail);									
		
		 
		$arr_prev_yr = get_previous_yr_data('BALY', '2007-01-19', $arr_prev_year, $arr_prev_year_detail);
		show_array($arr_prev_yr);
		
		
		echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 						

} else {
		echo "must exit<br>";
}
?>