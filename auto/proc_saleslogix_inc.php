<?
/*

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 
//Now this information flows from the cron.php file
$trade_date_to_process = previous_business_day();
*/


//show all trades for all days
//fields are trad_reference_number  trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share  trad_is_cancelled 
$trade_str = "TRADEREFNUM,TRADEDATE,ADVISOR,BUYSELL,SYMBOL,PRICE,COMMISSION,COMMPERSHARE,BADTRADE".chr(13) . chr(10);
			
$query_trades = "SELECT * from mry_comm_rr_trades
							   WHERE trad_trade_date = '".$trade_date_to_process."'";
							
$result_trades= mysql_query($query_trades) or die(mysql_error());
//echo mysql_num_rows($result_trades);
while($row_trades = mysql_fetch_array($result_trades))
{
$trade_str .= $row_trades["trad_reference_number"].",".format_date_ymd_to_mdy($row_trades["trad_trade_date"]).",".$row_trades["trad_advisor_code"].",".$row_trades["trad_buy_sell"].",".$row_trades["trad_symbol"].",".round($row_trades["trade_price"],2).",".$row_trades["trad_commission"].",".$row_trades["trad_cents_per_share"].",".$row_trades["trad_is_cancelled"].chr(13) . chr(10);
}

define("FILE_PATH","C:\\");
$filename = "TRADES.DAT";

$tradeFile = fopen(FILE_PATH.$filename, "w" ) ;

  if ( $tradeFile )
  {
	 fwrite($tradeFile, $trade_str);
   fclose($tradeFile);
	 echo "File ".FILE_PATH.$filename . " written successfully<br>";
  }
  else
  {
   die( "File could not be opened for writing" ) ;
  }

$cmd_string = "copy c:\TRADES.DAT \\\\192.168.20.49\\nfs$\\TRADES.DAT";
shell_exec($cmd_string);









//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
// PROCESS TO CHECK PREVIOUS YEAR NUMBERS
// This process should only kick-off when the current date is 2007-01-18
$date_considered = previous_business_day(); //'2007-01-10';
if (strtotime($date_considered) > strtotime('2007-01-09') ) {

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
											WHERE comm_trade_date <= '".$previous_year_date."'
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
																AND comm_trade_date = '".$row_prev_year["comm_trade_date"]."'";
																	
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
//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


$advisor_str = "TRADEDATE,ADVISOR,CURDAYCOMM,CURMTD,CURYTD,PREMTD,PREYTD".chr(13) . chr(10);
$qry_distinct_adv = "SELECT distinct(comm_advisor_code) as comm_advisor_code 
										 FROM mry_comm_rr_level_a 
										 WHERE comm_advisor_code not like '&%' 
										 order by comm_advisor_code";
//xdebug("qry_distinct_adv",$qry_distinct_adv);										 
$result_distinct_adv = mysql_query($qry_distinct_adv) or die(mysql_error());
while($row_distinct_adv = mysql_fetch_array($result_distinct_adv))
{
		$query_mqydate_adv = "SELECT max(comm_trade_date)  AS comm_trade_date
													FROM mry_comm_rr_level_a
													WHERE comm_advisor_code = '".$row_distinct_adv["comm_advisor_code"]."'";
		$result_mqydate_adv= mysql_query($query_mqydate_adv) or die(mysql_error());
		while($row_mqydate_adv = mysql_fetch_array($result_mqydate_adv))
		{
			$query_data = "SELECT * from mry_comm_rr_level_a
														WHERE comm_advisor_code = '".$row_distinct_adv["comm_advisor_code"]."'
														AND comm_trade_date = '".$row_mqydate_adv["comm_trade_date"]."'";
			$result_data= mysql_query($query_data) or die(mysql_error());
			while($row_data = mysql_fetch_array($result_data))
			{
				//This gets the previous year data
				$arr_prev_yr = get_previous_yr_data($row_distinct_adv["comm_advisor_code"], $trade_date_to_process, $arr_prev_year, $arr_prev_year_detail);
				//xdebug("row_distinct_adv['comm_advisor_code']",$row_distinct_adv["comm_advisor_code"]);
				//xdebug("trade_date_to_process",$trade_date_to_process);
				if ($row_mqydate_adv["comm_trade_date"] == $trade_date_to_process) {
					$advisor_str .= format_date_ymd_to_mdy($trade_date_to_process).",".$row_distinct_adv["comm_advisor_code"].",".$row_data["comm_total"].",".$row_data["comm_mtd"].",".$row_data["comm_ytd"] .",".$arr_prev_yr[0] .",".$arr_prev_yr[2].chr(13) . chr(10);
					//echo format_date_ymd_to_mdy($trade_date_to_process).",".$row_distinct_adv["comm_advisor_code"].",".$row_data["comm_total"].",".$row_data["comm_mtd"].",".$row_data["comm_ytd"] .",".$arr_prev_yr[0] .",".$arr_prev_yr[2].chr(13) . chr(10)."<br>";
				} else {
					$advisor_str .= format_date_ymd_to_mdy($trade_date_to_process).",".$row_distinct_adv["comm_advisor_code"].","."0".",".$row_data["comm_mtd"].",".$row_data["comm_ytd"] .",".$arr_prev_yr[0] .",".$arr_prev_yr[2].chr(13) . chr(10);
					//echo format_date_ymd_to_mdy($trade_date_to_process).",".$row_distinct_adv["comm_advisor_code"].","."0".",".$row_data["comm_mtd"].",".$row_data["comm_ytd"] .",".$arr_prev_yr[0] .",".$arr_prev_yr[2].chr(13) . chr(10)."<br>";
				}
				
			}
			
		}

}

$filename = "COMM.DAT";

$commFile = fopen(FILE_PATH.$filename, "w" ) ;

  if ( $commFile )
  {
	 fwrite($commFile, $advisor_str);
   fclose($commFile);
	 echo "File ".FILE_PATH.$filename . " written successfully<br>";
  }
  else
  {
   die( "File could not be opened for writing" ) ;
  }

$cmd_string = "copy c:\COMM.DAT \\\\192.168.20.49\\nfs$\\COMM.DAT";
shell_exec($cmd_string);

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//function get user email from Initials
function get_email_for_initials ($Initials) {
	$qry_useremail = "SELECT 
									Email 
									FROM users 
								WHERE Initials = '".$Initials."'";   
	$result_useremail = mysql_query($qry_useremail) or die(tdw_mysql_error($qry_useremail));
	while($row_useremail = mysql_fetch_array($result_useremail)) {
		$useremail = $row_useremail["Email"];
	}
	return $useremail;
}

$acct_str = "EMAIL,COUNTofACCOUNTS".chr(13) . chr(10);
			
$query_rr1 = "SELECT count( clnt_code ) as xcount , trim( clnt_rr1 ) as clnt_rr1 
							FROM int_clnt_clients
							WHERE trim( clnt_rr1 ) != ''
							AND trim( clnt_rr1 ) != '**'
							GROUP BY trim( clnt_rr1 ) ";
$result_rr1= mysql_query($query_rr1) or die(mysql_error());
$arr_one = array();
while($row_rr1 = mysql_fetch_array($result_rr1)) {
	$arr_one[$row_rr1["clnt_rr1"]] = $row_rr1["xcount"];
}

//show_array($arr_one);
//echo "<br><br><br>";

$query_rr2 = "SELECT count( clnt_code ) as xcount , trim( clnt_rr2 ) as clnt_rr2 
							FROM int_clnt_clients
							WHERE trim( clnt_rr2 ) != ''
							AND trim( clnt_rr2 ) != '**'
							GROUP BY trim( clnt_rr2 ) ";
$result_rr2= mysql_query($query_rr2) or die(mysql_error());
$arr_two = array();
while($row_rr2 = mysql_fetch_array($result_rr2)) {
	$arr_two[$row_rr2["clnt_rr2"]] = $row_rr2["xcount"];
}
//show_array($arr_two);
//echo "<br><br><br>";

foreach ($arr_one as $rr_a=>$xcount_a) {
	foreach ($arr_two as $rr_b=>$xcount_b) {
			if ($rr_a == $rr_b) {
			 //echo $client_a . " >> " . ($xcount_a + $xcount_b). "<br>";
			 $xcount_a = $xcount_a + $xcount_b;
			} 
	}
	//echo $rr_a . " >> " .$xcount_a . "<br>";
  $acct_str .= get_email_for_initials($rr_a) . "," . $xcount_a . chr(13) . chr(10);	
}

//echo $acct_str;

define("FILE_PATH","C:\\");
$filename = "COUNT_ACCTS.DAT";

$acctFile = fopen(FILE_PATH.$filename, "w" ) ;

  if ( $acctFile )
  {
	 fwrite($acctFile, $acct_str);
   fclose($acctFile);
	 echo "File ".FILE_PATH.$filename . " written successfully<br>";
  }
  else
  {
   die( "File could not be opened for writing" ) ;
  }

$cmd_string = "copy c:\\".$filename." \\\\192.168.20.49\\nfs$\\".$filename;
shell_exec($cmd_string);
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
?>