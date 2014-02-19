<?
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
		global $arr_prev_year;
			 if ($arr_prev_year[$clntval] == "") {
					$pyc = "";
					return $pyc;
			 } else {
					$pyc = $arr_prev_year[$clntval];
					return $pyc;
			 }
		 }	
		
		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year = "SELECT yrt_advisor_code, yrt_commission 
											FROM yrt_yearly_total_lookup
											WHERE yrt_rr  = '".$rep_to_process."'
											AND yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
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

	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}
//*********************************************************************************************

//xdebug("trade_date_to_process",$trade_date_to_process);
//xdebug("rep_to_process",$rep_to_process);

//get the start and end dates for the selected quarter and year

////
// function get start and end dates for the selected quarter and year
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

//Create Array of all clients to show here
$arr_quarter_brok_dates = get_quarter_dates ($sel_qtr, $sel_year);
$arr_quarter_cal_dates = get_quarter_dates ($sel_qtr, $sel_year, "C");

//show_array($arr_quarter_brok_dates);
//show_array($arr_quarter_cal_dates);

$arr_clnt_for_rr = array();
$qry_clnt_for_rr = "SELECT distinct(trad_advisor_code) 
										FROM mry_comm_rr_trades 
										WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
											and trad_is_cancelled = 0
											and trad_rr = '".$rep_to_process."' 
										order by trad_advisor_code";
//xdebug("Q",$qry_clnt_for_rr);
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
								  WHERE a.chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
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
								  WHERE a.chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}

//show_array($arr_clnt_for_rr);

//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+

//get totals for each client
$str_clients = implode(",",$arr_clnt_for_rr);
$str_clients = str_replace(",",'","',$str_clients);
$str_clients = '"'.$str_clients.'"';
//xdebug("S",$str_clients);

	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//PRIMARY REP DATA
	
	//COMMISSION
	$arr_comm_for_rr_comm = array();
	$qry_comm_for_rr = "SELECT trad_advisor_code, sum(trad_commission) as commission 
											FROM mry_comm_rr_trades 
											WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
												and trad_is_cancelled = 0
												and trad_advisor_code in (".$str_clients.")
											GROUP by trad_advisor_code";
	//xdebug("Q",$qry_comm_for_rr);
	$result_comm_for_rr = mysql_query($qry_comm_for_rr) or die (tdw_mysql_error($qry_comm_for_rr));
	while ( $row_comm_for_rr = mysql_fetch_array($result_comm_for_rr) ) 
	{
		$arr_comm_for_rr_comm[$row_comm_for_rr["trad_advisor_code"]] = $row_comm_for_rr["commission"];
	}
  //$arr_comm_for_rr_comm["INTR"] = 1;	
	
	//CHECKS
	$arr_comm_for_rr_chek = array();
	$qry_comm_for_rr = "SELECT chek_advisor, sum(chek_amount) as commission  
											FROM chk_chek_payments_etc 
											WHERE chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
												AND chek_isactive = 1
												AND chek_advisor in (".$str_clients.")
											GROUP BY chek_advisor";
	$result_comm_for_rr = mysql_query($qry_comm_for_rr) or die (tdw_mysql_error($qry_comm_for_rr));
	while ( $row_comm_for_rr = mysql_fetch_array($result_comm_for_rr) ) 
	{
		$arr_comm_for_rr_chek[$row_comm_for_rr["chek_advisor"]] = $row_comm_for_rr["commission"];
	}
	
	//incorporate checks into comm array
	$arr_composite_primary = array();
	$arr_tmp_processed = array();
	foreach ($arr_comm_for_rr_comm as $code=>$comm) {
		if (array_key_exists($code, $arr_comm_for_rr_chek)) {
			$arr_composite_primary[$code] = $arr_comm_for_rr_chek[$code] + $comm;
			$arr_tmp_processed[] = $code;
		} else {
			$arr_composite_primary[$code] = $comm;
		} 
	}
	
	foreach ($arr_comm_for_rr_chek as $code=>$comm) {
		if (!in_array($code, $arr_tmp_processed)) {
			$arr_composite_primary[$code] = $comm;
		} 
	}
	
	
	//show_array($arr_composite_primary);
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

//show_array($arr_comm_for_rr_comm);
//show_array($arr_comm_for_rr_chek);

//get the client names sorted
//$arr_sorted_clients = array();

foreach($arr_clnt_for_rr as $code=>$codeval) {
	$arr_sorted_clients[$code] = look_up_client($code);
}

$arr_clnt_for_rr = $arr_sorted_clients;
asort($arr_clnt_for_rr);

//show_array($arr_clnt_for_rr);
//show_array($arr_composite_primary);

//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+


//ksort($arr_clnt_for_rr);


//show_array($arr_clnt_for_rr);




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
													and trad_rr != '".$rep_to_process."'";
	  //xdebug("qry",$qry);
		$srn = db_single_val($qry);
		$arr_relevant_shared_clients[$row_relevant_shared_clients["relevant_shared_client"]] = $srn;
	  $arr_rel_shrd_clnts[$row_relevant_shared_clients["relevant_shared_client"]] = $row_relevant_shared_clients["relevant_shared_client"];
	}
	$str_shrd_clnts = implode(",",$arr_rel_shrd_clnts);
	$str_shrd_clnts = "'".str_replace(",","','",$str_shrd_clnts)."'";
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
$result_qtd_comm_shrd = mysql_query($qry_qtd_comm_shrd) or die (tdw_mysql_error($qry_qtd_comm_shrd));
while ( $row_qtd_comm_shrd = mysql_fetch_array($result_qtd_comm_shrd) ) 
{
	$arr_qtd_comm_shrd[$row_qtd_comm_shrd["trad_advisor_code"]] = $row_qtd_comm_shrd["trad_comm"];
}
//show_array($arr_qtd_comm_shrd);

//now get ytd
$qry_ytd_comm_shrd =  "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
											 AND trad_is_cancelled = 0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_advisor_code
											 ORDER BY trad_advisor_code";
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