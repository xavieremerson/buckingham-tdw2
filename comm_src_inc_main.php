<?
function getrepnum_for_client ($clientcode) {

	$qry = "select clnt_rr1, clnt_rr2 from int_clnt_clients where clnt_code = '".$clientcode."'"; 
  $result = mysql_query($qry);
	
	while ( $row = mysql_fetch_array($result) ) {
		$rep1 = trim($row["clnt_rr1"]);
		$rep2 = trim($row["clnt_rr2"]);
	}

	$result_rep = "";
	if ($rep2 != "") { //shared rep
		$rep1_id = db_single_val ("select ID as single_val from users where Initials = '".$rep1."'");	
		$rep2_id = db_single_val ("select ID as single_val from users where Initials = '".$rep2."'");	
		
		$qrya = "select sum(srep_user_id) as sumrep, srep_rrnum from sls_sales_reps 
		         where srep_isactive = 1
						 group by srep_rrnum";
  	$resulta = mysql_query($qrya);
		while ( $rowa = mysql_fetch_array($resulta) ) {
			if ($rowa["sumrep"] == ($rep1_id+$rep2_id)) {
				$result_rep = $rowa["srep_rrnum"];	
			}
		}
		return $result_rep;
		
	} else { //primary rep
			
		$result_rep = db_single_val ("select rr_num as single_val from users where Initials = '".$rep1."'");	
		return $result_rep;
	}
	
}

		////
		// Create percentage with appropriate format
		function mkpercent($a, $b) {
			if ($a == 0) { return 0; }
		  elseif ($b == 0) { return '---'; }
			else { return number_format(($a*100/$b),0,'',','); }		
		}

    ////
		// Get date in previous year (input and output format: yyyy-mm-dd)
		function get_date_previous_year($dateval) {
		$arr_date = explode("-",$dateval);
		$retval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];
		return $retval;
		}
		
		////
		// Get data in previous year (input and output format: yyyy-mm-dd)
		function get_previous_yr_data($clntval) {
		global $arr_prev_year, $arr_prev_year_shared;
			 if ($arr_prev_year[$clntval] == "") {
			 		if ($arr_prev_year_shared[$clntval] != "") {
						$pyc = $arr_prev_year_shared[$clntval];
						return $pyc;
					} else {
						$pyc = "";
						return $pyc;
					}
			 } else {
					$pyc = $arr_prev_year[$clntval];
					return $pyc;
			 }
		 }	
		
		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year = "SELECT yrt_advisor_code, sum(yrt_commission) as yrt_commission
											FROM yrt_yearly_total_lookup
											WHERE (yrt_rr  = '".$rep_to_process."' or yrt_rr  = '')
											AND yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code"; //ORDER BY yrt_advisor_code
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}
		
		//print_r($arr_prev_year);

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

//*********************************************************************************************
//Create Lookup Array of Client Code / Client Name

	$qry_clients = "select * from int_clnt_clients"; // where clnt_status = 'A'
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}
	
	//temporary MUST CHANGE THIS LATER
	function look_up_client($clnt) {
		global $arr_clients;
		if ($arr_clients[$clnt] == '') {
		   return $clnt;
		} else {
		   return $arr_clients[$clnt];
		}
	}
//*********************************************************************************************

//Create Array of all clients to show here
$arr_clnt_for_rr = array();
$qry_clnt_for_rr = "SELECT distinct(trad_advisor_code) 
										FROM mry_comm_rr_trades 
										WHERE trad_trade_date <= '".$trade_date_to_process."' 
											and trad_is_cancelled = 0
											and datediff('".$trade_date_to_process."', trad_trade_date) < 365
											and trad_rr = '".$rep_to_process."' 
										order by trad_advisor_code";
										
										//AND date_format( trad_run_date, '%Y' ) = date_format( now( ) , '%Y' )
										//not sure why this was put there but caused Bobby problems

$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["trad_advisor_code"]] = $row_clnt_for_rr["trad_advisor_code"];
}

//show_array($arr_clnt_for_rr);
//get initials for the user
$user_initials = db_single_val("select Initials as single_val from users where rr_num = '".$rep_to_process."'");

$qry_clnt_for_rr = "SELECT distinct(a.chek_advisor) as chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date <= '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
//xdebug("qry_clnt_for_rr",$qry_clnt_for_rr);
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}

//show_array($arr_clnt_for_rr);

//also check client history table for information
$qry_clnt_for_rr = "SELECT distinct(a.chek_advisor) as chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients_history b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date <= '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
//xdebug("qry_clnt_for_rr",$qry_clnt_for_rr);
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}

//show_array($arr_clnt_for_rr);

//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
//get the client names sorted
$arr_sorted_clients = array();
/*

*/

foreach($arr_clnt_for_rr as $code=>$codeval) {
	$arr_sorted_clients[$code] = look_up_client($code);
}

$arr_clnt_for_rr = $arr_sorted_clients;
asort($arr_clnt_for_rr);

//show_array($arr_clnt_for_rr);
//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+


//ksort($arr_clnt_for_rr);






//now get the trad commissions for the clients for the day, qtd, mtd, ytd
//for the day
$qry_day_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date = '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_advisor_code
								 ORDER BY trad_advisor_code";
//xdebug("qry_day_comm",$qry_day_comm);
$result_day_comm = mysql_query($qry_day_comm) or die (tdw_mysql_error($qry_day_comm));
while ( $row_day_comm = mysql_fetch_array($result_day_comm) ) 
{
	$arr_day_comm[$row_day_comm["trad_advisor_code"]] = $row_day_comm["trad_comm"];
}

//show_array($arr_day_comm);

//now mtd comm
//get the start day of month for this
$global_qry_date_start_mtd = db_single_val("SELECT brk_start_date as single_val 
																			FROM `brk_brokerage_months` 
																			WHERE `brk_start_date` <= '".$trade_date_to_process."'
																			AND `brk_end_date` >= '".$trade_date_to_process."'");
//xdebug("global_qry_date_start_mtd",$global_qry_date_start_mtd);

$qry_mtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_advisor_code
								 ORDER BY trad_advisor_code";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{
	$arr_mtd_comm[$row_mtd_comm["trad_advisor_code"]] = $row_mtd_comm["trad_comm"];
}
//show_array($arr_mtd_comm);

//get qtd values
//get quarter start date
$arr_qtr_start = array();
$arr_qtr_start[1] = 'Jan';
$arr_qtr_start[2] = 'Apr';
$arr_qtr_start[3] = 'Jul';
$arr_qtr_start[4] = 'Oct';

$arr_month_in_qtr = array();
$arr_month_in_qtr['01'] = 1;
$arr_month_in_qtr['02'] = 1;
$arr_month_in_qtr['03'] = 1;
$arr_month_in_qtr['04'] = 2;
$arr_month_in_qtr['05'] = 2;
$arr_month_in_qtr['06'] = 2;
$arr_month_in_qtr['07'] = 3;
$arr_month_in_qtr['08'] = 3;
$arr_month_in_qtr['09'] = 3;
$arr_month_in_qtr['10'] = 4;
$arr_month_in_qtr['11'] = 4;
$arr_month_in_qtr['12'] = 4;

$qtr_start_val = $arr_qtr_start[$arr_month_in_qtr[substr($trade_date_to_process,5,2)]];
$year_to_process = substr($trade_date_to_process,0,4);
$global_qtr_start_date = db_single_val("SELECT brk_start_date as single_val 
																	FROM `brk_brokerage_months` 
																	WHERE brk_month = '".$qtr_start_val."'
																	  AND brk_year = '".$year_to_process."'");
//xdebug("qtr_start_val",$qtr_start_val);
//xdebug("year_to_process",$year_to_process);
//xdebug("global_qtr_start_date",$global_qtr_start_date);


$qry_qtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_advisor_code
								 ORDER BY trad_advisor_code";
//xdebug("qry_qtd_comm",$qry_qtd_comm);
$result_qtd_comm = mysql_query($qry_qtd_comm) or die (tdw_mysql_error($qry_qtd_comm));
while ( $row_qtd_comm = mysql_fetch_array($result_qtd_comm) ) 
{
	$arr_qtd_comm[$row_qtd_comm["trad_advisor_code"]] = $row_qtd_comm["trad_comm"];
}
//show_array($arr_qtd_comm);

//GET THE START OF THE YEAR
//xdebug("First Day of the current Brokerage Year",substr($trade_date_to_process,0,4));
$global_year_start_date = db_single_val("SELECT brk_start_date as single_val 
																		FROM `brk_brokerage_months` 
																		WHERE brk_month = 'Jan'
																	  AND brk_year = '".substr($trade_date_to_process,0,4)."'");
//now get ytd
$qry_ytd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_advisor_code
								 ORDER BY trad_advisor_code";
								 //removed 	WHERE trad_trade_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
								 							 
//xdebug("qry_ytd_comm",$qry_ytd_comm);
$result_ytd_comm = mysql_query($qry_ytd_comm) or die (tdw_mysql_error($qry_ytd_comm));
while ( $row_ytd_comm = mysql_fetch_array($result_ytd_comm) ) 
{
	$arr_ytd_comm[$row_ytd_comm["trad_advisor_code"]] = $row_ytd_comm["trad_comm"];
}
//show_array($arr_ytd_comm);


//now get the check commissions for the clients for the day, qtd, mtd, ytd
//for the day

$qry_day_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date = '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '%".$user_initials."%'
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
$result_day_check = mysql_query($qry_day_check) or die (tdw_mysql_error($qry_day_check));
while ( $row_day_check = mysql_fetch_array($result_day_check) ) 
{
	$arr_day_check[$row_day_check["chek_advisor"]] = $row_day_check["total_checks"];
}

$qry_day_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients_history b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date = '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '%".$user_initials."%'
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
$result_day_check = mysql_query($qry_day_check) or die (tdw_mysql_error($qry_day_check));
while ( $row_day_check = mysql_fetch_array($result_day_check) ) 
{
	$arr_day_check[$row_day_check["chek_advisor"]] = $row_day_check["total_checks"];
}

//show_array($arr_day_check);
//xdebug("qry_day_check",$qry_day_check);






//for the mtd
$qry_mtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,8)."01"."' and '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '%".$user_initials."%'
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 
								 //substr($trade_date_to_process,0,8)."01")
								 //$global_qry_date_start_mtd
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
$result_mtd_check = mysql_query($qry_mtd_check) or die (tdw_mysql_error($qry_mtd_check));
while ( $row_mtd_check = mysql_fetch_array($result_mtd_check) ) 
{
	$arr_mtd_check[$row_mtd_check["chek_advisor"]] = $row_mtd_check["total_checks"];
}

//show_array($arr_mtd_check);

//for the qtd

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

$z_arr_month_qtr = array("01"=>"1","02"=>"1","03"=>"1","04"=>"2","05"=>"2","06"=>"2","07"=>"3","08"=>"3","09"=>"3","10"=>"4","11"=>"4","12"=>"4",);

//xdebug("Q",$z_arr_month_qtr[substr($trade_date_to_process,5,2)]);
//xdebug("Y",substr($trade_date_to_process,0,4));

$z_qtr_dates = get_quarter_dates($z_arr_month_qtr[substr($trade_date_to_process,5,2)],substr($trade_date_to_process,0,4),"C");

$qry_qtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$z_qtr_dates[0]."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '%".$user_initials."%'
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_qtd_check",$qry_qtd_check);
$result_qtd_check = mysql_query($qry_qtd_check) or die (tdw_mysql_error($qry_qtd_check));
while ( $row_qtd_check = mysql_fetch_array($result_qtd_check) ) 
{
	$arr_qtd_check[$row_qtd_check["chek_advisor"]] = $row_qtd_check["total_checks"];
}

//show_array($arr_qtd_check);
//xdebug("qry_qtd_check",$qry_qtd_check);

//for the ytd
$qry_ytd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '%".$user_initials."%'
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_ytd_check",$qry_ytd_check);
$result_ytd_check = mysql_query($qry_ytd_check) or die (tdw_mysql_error($qry_ytd_check));
while ( $row_ytd_check = mysql_fetch_array($result_ytd_check) ) 
{
	$arr_ytd_check[$row_ytd_check["chek_advisor"]] = $row_ytd_check["total_checks"];
}

//show_array($arr_ytd_check);
//xdebug("qry_day_check",$qry_day_check);

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//SHARED SECTION
													
	//Get lookup relevant client codes from client master (internal) for verification
	$qry_relevant_shared_clients = "SELECT DISTINCT (a.clnt_code) as relevant_shared_client
																		FROM int_clnt_clients a, Users b
																	WHERE (
																						(
																						a.clnt_rr1 = b.Initials
																						AND a.clnt_rr2 != ''
																						)
																				OR  (
																						a.clnt_rr2 = b.Initials
																						AND a.clnt_rr1 != ''
																						)
																				)
																				AND b.rr_num = '".$rep_to_process."'
																				AND a.clnt_status = 'A'
																				AND b.user_isactive =1
																	ORDER BY a.clnt_name";
	//xdebug("qry_relevant_shared_clients",$qry_relevant_shared_clients);

	$result_relevant_shared_clients = mysql_query($qry_relevant_shared_clients) or die (tdw_mysql_error($qry_relevant_shared_clients));
	$arr_relevant_shared_clients = array();
	$arr_rel_shrd_clnts = array();
	while ( $row_relevant_shared_clients = mysql_fetch_array($result_relevant_shared_clients) ) 
	{
		//getting the shared rep number information from the trades file.
		$qry = "select max(trad_rr) as single_val from mry_comm_rr_trades 
													where trad_advisor_code = '".$row_relevant_shared_clients["relevant_shared_client"]."' 
													and trad_rr != '".$rep_to_process."' and trad_rr != ''";
	  //xdebug("qry",$qry);
		$srn = db_single_val($qry);
		$arr_relevant_shared_clients[$row_relevant_shared_clients["relevant_shared_client"]] = $srn;
	  $arr_rel_shrd_clnts[$row_relevant_shared_clients["relevant_shared_client"]] = $row_relevant_shared_clients["relevant_shared_client"];
	}
	
  //show_array($arr_rel_shrd_clnts);
	//xdebug("user_id",$user_id);
	//*******************************************************************************************
	//20090901
	//Ann Freeman's case where there were shared clients which became primary clients later.
	$new_shared_rep_num = array();
	$z_qry = "SELECT srep_rrnum FROM sls_sales_reps WHERE srep_user_id = '".$user_id."' and srep_isactive = 1";
	
	$rep_shared_initials = db_single_val("select Initials as single_val from users where ID = '".$user_id."'");
	//xdebug("z_qry",$z_qry);
	$z_result = mysql_query($z_qry) or die (tdw_mysql_error($z_qry));
	while ( $z_row = mysql_fetch_array($z_result) ) {
	  $new_shared_rep_num[] = $z_row["srep_rrnum"];
	}
  //show_array($new_shared_rep_num);
	
	$str_new_shared_rep_num = implode(",",$new_shared_rep_num);
	$str_new_shared_rep_num = "'".str_replace(",","','",$str_new_shared_rep_num)."'";
	$z_qry_clients = "select distinct(trad_advisor_code) as z_code from mry_comm_rr_trades
	                  where trad_rr in (".$str_new_shared_rep_num.")
										AND date_format( trad_run_date, '%Y' ) = date_format( now( ) , '%Y' )
										AND trad_is_cancelled = 0";
	//xdebug("z_qry_clients",$z_qry_clients);
	$z_result_client_code = mysql_query($z_qry_clients) or die (tdw_mysql_error($z_qry_clients));
	while ( $z_row_client_code = mysql_fetch_array($z_result_client_code) ) {

		//getting the shared rep number information from the trades file.
		$qry = "select max(trad_rr) as single_val from mry_comm_rr_trades 
													where trad_advisor_code = '".$z_row_client_code["z_code"]."' 
													and trad_rr != '".$rep_to_process."' and trad_rr != ''";
	  //xdebug("qry",$qry);
		$srn = db_single_val($qry);

    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		$test_qry = "SELECT count( * ) AS single_val
																FROM int_clnt_clients
																WHERE (
																	(
																	clnt_rr1 = '".$rep_shared_initials."'
																	AND trim( clnt_rr2 ) != '')
																	OR (
																	clnt_rr2 = '".$rep_shared_initials."'
																	AND trim( clnt_rr1 ) != '')
																	)
																AND
																	clnt_code = '".$z_row_client_code["z_code"]."'";
		$test_count = db_single_val($test_qry);
		//xdebug("test_qry",$test_qry);
		if ($test_count > 0) {
			$arr_rel_shrd_clnts[$z_row_client_code["z_code"]] = $z_row_client_code["z_code"];
			$arr_relevant_shared_clients[$z_row_client_code["z_code"]] = $srn;
		}
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	}


  //show_array($arr_rel_shrd_clnts);
	
	
	
	
	//*******************************************************************************************

	$str_shrd_clnts = implode(",",$arr_rel_shrd_clnts);
	$str_shrd_clnts = "'".str_replace(",","','",$str_shrd_clnts)."'";
	
  //Adding to the array of previous year totals
	
		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year_shared = "SELECT yrt_advisor_code, yrt_commission 
											FROM yrt_yearly_total_lookup
											WHERE yrt_rr not like '0%'
											AND yrt_advisor_code in (".$str_shrd_clnts.") 
											AND yrt_year = '". substr($previous_year_date,0,4)."' 
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
		//xdebug('qry_prev_year_shared',$qry_prev_year_shared);
		$result_prev_year_shared = mysql_query($qry_prev_year_shared) or die (tdw_mysql_error($qry_prev_year_shared));
		$arr_prev_year_shared = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year_shared) ) 
		{
			$arr_prev_year_shared[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}
	
	  //show_array($arr_prev_year_shared);
	
  //show_array($arr_relevant_shared_clients);
	//xdebug("str_shrd_clnts",$str_shrd_clnts);
	
//now get the trad commissions for the clients for the day, qtd, mtd, ytd
//for the day
$qry_day_comm_shrd = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date = '".$trade_date_to_process."'
											 AND trad_is_cancelled = 0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_advisor_code
											 ORDER BY trad_advisor_code";
//xdebug("qry_day_comm_shrd",$qry_day_comm_shrd);
$result_day_comm_shrd = mysql_query($qry_day_comm_shrd) or die (tdw_mysql_error($qry_day_comm_shrd));
while ( $row_day_comm_shrd = mysql_fetch_array($result_day_comm_shrd) ) 
{
	$arr_day_comm_shrd[$row_day_comm_shrd["trad_advisor_code"]] = $row_day_comm_shrd["trad_comm"];
}

//show_array($arr_day_comm_shrd);

//now mtd comm
$qry_mtd_comm_shrd =  "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
											 AND trad_is_cancelled =0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_advisor_code
											 ORDER BY trad_advisor_code";
$result_mtd_comm_shrd = mysql_query($qry_mtd_comm_shrd) or die (tdw_mysql_error($qry_mtd_comm_shrd));
while ( $row_mtd_comm_shrd = mysql_fetch_array($result_mtd_comm_shrd) ) 
{
	$arr_mtd_comm_shrd[$row_mtd_comm_shrd["trad_advisor_code"]] = $row_mtd_comm_shrd["trad_comm"];
}
//show_array($arr_mtd_comm_shrd);

//get qtd values
$qry_qtd_comm_shrd =  "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
											 AND trad_is_cancelled =0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_advisor_code
											 ORDER BY trad_advisor_code";
//xdebug("qry_qtd_comm_shrd",$qry_qtd_comm_shrd);
$result_qtd_comm_shrd = mysql_query($qry_qtd_comm_shrd) or die (tdw_mysql_error($qry_qtd_comm_shrd));
while ( $row_qtd_comm_shrd = mysql_fetch_array($result_qtd_comm_shrd) ) 
{
	$arr_qtd_comm_shrd[$row_qtd_comm_shrd["trad_advisor_code"]] = $row_qtd_comm_shrd["trad_comm"];
}
//show_array($arr_qtd_comm_shrd);

//now get ytd
//Get the Start Date for Year.




$qry_ytd_comm_shrd =  "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
											 AND trad_is_cancelled = 0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_advisor_code
											 ORDER BY trad_advisor_code";
											 //WHERE trad_trade_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'


//xdebug("qry_ytd_comm_shrd",$qry_ytd_comm_shrd);

$result_ytd_comm_shrd = mysql_query($qry_ytd_comm_shrd) or die (tdw_mysql_error($qry_ytd_comm_shrd));
while ( $row_ytd_comm_shrd = mysql_fetch_array($result_ytd_comm_shrd) ) 
{
	$arr_ytd_comm_shrd[$row_ytd_comm_shrd["trad_advisor_code"]] = $row_ytd_comm_shrd["trad_comm"];
}

//show_array($arr_ytd_comm_shrd);


//now get the check commissions for the clients for the day, qtd, mtd, ytd
//for the day

$qry_day_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date = '".$trade_date_to_process."' 
													AND a.chek_isactive = 1 
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor
											 ORDER BY a.chek_advisor";
$result_day_check_shrd = mysql_query($qry_day_check_shrd) or die (tdw_mysql_error($qry_day_check_shrd));
while ( $row_day_check_shrd = mysql_fetch_array($result_day_check_shrd) ) 
{
	$arr_day_check_shrd[$row_day_check_shrd["chek_advisor"]] = $row_day_check_shrd["total_checks"];
}

//show_array($arr_day_check);
//xdebug("qry_day_check",$qry_day_check);

//for the mtd
$qry_mtd_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
													AND a.chek_isactive = 1
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor
											 ORDER BY a.chek_advisor";
$result_mtd_check_shrd = mysql_query($qry_mtd_check_shrd) or die (tdw_mysql_error($qry_mtd_check_shrd));
while ( $row_mtd_check_shrd = mysql_fetch_array($result_mtd_check_shrd) ) 
{
	$arr_mtd_check_shrd[$row_mtd_check_shrd["chek_advisor"]] = $row_mtd_check_shrd["total_checks"];
}

//show_array($arr_mtd_check_shrd);

//for the qtd
$qry_qtd_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
													AND a.chek_isactive = 1
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor
											 ORDER BY a.chek_advisor";
//xdebug("qry_qtd_check_shrd",$qry_qtd_check_shrd);
$result_qtd_check_shrd = mysql_query($qry_qtd_check_shrd) or die (tdw_mysql_error($qry_qtd_check_shrd));
while ( $row_qtd_check_shrd = mysql_fetch_array($result_qtd_check_shrd) ) 
{
	$arr_qtd_check_shrd[$row_qtd_check_shrd["chek_advisor"]] = $row_qtd_check_shrd["total_checks"];
}

//show_array($arr_qtd_check_shrd);
//xdebug("qry_day_check_shrd",$qry_day_check_shrd);

//for the ytd
$qry_ytd_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
													AND a.chek_isactive = 1
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor
											 ORDER BY a.chek_advisor";
//xdebug("qry_ytd_check_shrd",$qry_ytd_check_shrd);
$result_ytd_check_shrd = mysql_query($qry_ytd_check_shrd) or die (tdw_mysql_error($qry_ytd_check_shrd));
while ( $row_ytd_check_shrd = mysql_fetch_array($result_ytd_check_shrd) ) 
{
	$arr_ytd_check_shrd[$row_ytd_check_shrd["chek_advisor"]] = $row_ytd_check_shrd["total_checks"];
}

//show_array($arr_ytd_check_shrd);

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

?>