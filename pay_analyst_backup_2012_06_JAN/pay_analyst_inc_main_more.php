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
//xdebug("Program is being debugged",$qry_clnt_for_rr);
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
										AND (b.clnt_rr1 = '".$user_initials."' AND (trim(b.clnt_rr2) = '' or b.clnt_rr2 is NULL))
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
									  AND b.clnt_change_begin_date <  '".$arr_quarter_cal_dates[0]."'
										AND b.clnt_change_end_date > '".$arr_quarter_cal_dates[1]."'
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
//xdebug("Lloyd: Don't worry about this temporary debug information.",$qry_clnt_for_rr);
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
if (count($arr_clnt_for_rr)>0) {
asort($arr_clnt_for_rr);
}
//show_array($arr_clnt_for_rr);
//show_array($arr_composite_primary);

//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+


//ksort($arr_clnt_for_rr);


//show_array($arr_clnt_for_rr);



//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//SHARED SECTION
													
	//Get lookup relevant client codes from client master (internal) for verification
	$qry_relevant_shared_clients = "SELECT DISTINCT (a.clnt_code) as relevant_shared_client
																		FROM int_clnt_clients a, Users b
																	WHERE (
																						(
																						trim(a.clnt_rr1) = trim(b.Initials)
																						AND trim(a.clnt_rr2) != ''
																						)
																				)
																				AND trim(b.rr_num) = '".$rep_to_process."'
																	ORDER BY a.clnt_name";

	/*
																					OR  (
																						trim(a.clnt_rr2) = trim(b.Initials)
																						AND trim(a.clnt_rr1) != ''
																						)

	*/
	//xdebug("qry_relevant_shared_clients",$qry_relevant_shared_clients);
	$result_relevant_shared_clients = mysql_query($qry_relevant_shared_clients) or die (tdw_mysql_error($qry_relevant_shared_clients));
	$arr_rel_shrd_clnts = array();
	while ( $row_relevant_shared_clients = mysql_fetch_array($result_relevant_shared_clients) ) 
	{
	  $arr_rel_shrd_clnts[$row_relevant_shared_clients["relevant_shared_client"]] = $row_relevant_shared_clients["relevant_shared_client"];
	}

	$str_shrd_clnts = implode(",",$arr_rel_shrd_clnts);
	$str_shrd_clnts = "'".str_replace(",","','",$str_shrd_clnts)."'";
  //show_array($arr_rel_shrd_clnts);
	//xdebug("str_shrd_clnts",$str_shrd_clnts);
	
	//00000000000000000000000000000000000000000000000000000000000000000000000000000000
	//Now get the trad commissions for the clients for the selected period
	$arr_comm_shrd = array();
	$qry_comm_shrd = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
												 FROM mry_comm_rr_trades 
											   WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
												 AND trad_is_cancelled = 0
												 AND trad_advisor_code in (".$str_shrd_clnts.")
												 GROUP BY trad_advisor_code
												 ORDER BY trad_advisor_code";
	//xdebug("qry_day_comm_shrd",$qry_day_comm_shrd);
	$result_comm_shrd = mysql_query($qry_comm_shrd) or die (tdw_mysql_error($qry_comm_shrd));
	while ( $row_comm_shrd = mysql_fetch_array($result_comm_shrd) ) 
	{
		$arr_comm_shrd[$row_comm_shrd["trad_advisor_code"]] = $row_comm_shrd["trad_comm"];
	}
	//show_array($arr_comm_shrd);

	//00000000000000000000000000000000000000000000000000000000000000000000000000000000
	//Now get the check commissions for the clients for the selected period
	$arr_check_shrd = array();
	$qry_check_shrd =     "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												 FROM chk_chek_payments_etc a
											   WHERE chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
														AND a.chek_isactive = 1 
														AND a.chek_advisor in (".$str_shrd_clnts.")
												 GROUP BY a.chek_advisor
												 ORDER BY a.chek_advisor";
	//xdebug("qry_check_shrd",$qry_check_shrd);
	$result_check_shrd = mysql_query($qry_check_shrd) or die (tdw_mysql_error($qry_check_shrd));
	while ( $row_check_shrd = mysql_fetch_array($result_check_shrd) ) 
	{
		$arr_check_shrd[$row_check_shrd["chek_advisor"]] = $row_check_shrd["total_checks"];
	}
	//$arr_check_shrd['ZZZZ'] = 99999;
	//show_array($arr_check_shrd);

	//incorporate checks & comm
	$arr_composite_shared = array();
	$arr_tmp_processed = array();
	foreach ($arr_comm_shrd as $code=>$comm) {
		if (array_key_exists($code, $arr_check_shrd)) {
			$arr_composite_shared[$code] = $arr_check_shrd[$code] + $comm;
			$arr_tmp_processed[] = $code;
		} else {
			$arr_composite_shared[$code] = $comm;
		} 
	}
	
	foreach ($arr_check_shrd as $code=>$comm) {
		if (!in_array($code, $arr_tmp_processed)) {
			$arr_composite_shared[$code] = $comm;
		} 
	}
	
	//show_array($arr_composite_shared);

	$sole_count = count($arr_composite_primary);
	$shrd_count = count($arr_composite_shared);

	$master_count = $sole_count + $shrd_count;	
	
	$sum_sole = 0;
	$sum_shrd = 0;
	
	foreach($arr_composite_primary as $k=>$v) {
		$sum_sole = $sum_sole + $v;
	}
	foreach($arr_composite_shared as $k=>$v) {
		$sum_shrd = $sum_shrd + $v;
	}
	
	$sum_sole_and_shrd = $sum_sole + $sum_shrd; 
	//echo $master_count;

/*
	foreach($arr_composite_shared as $code=>$codeval) {
		$arr_sorted_clients[$code] = look_up_client($code);
	}
	
	$arr_clnt_for_rr = $arr_sorted_clients;
	asort($arr_clnt_for_rr);
*/

	//MERGE SOLE AND SHARED
  $arr_master_composite = array();
	$arr_master_clnt_rr = array();

	foreach($arr_composite_primary as $k=>$v) {
		$arr_master_composite[$k] = $v;
	}
	foreach($arr_composite_shared as $k=>$v) {
		$arr_master_composite[$k] = $v;
	}

	if (count($arr_clnt_for_rr) > 0) {
		foreach($arr_clnt_for_rr as $k=>$v) {
			$arr_master_clnt_rr[$k] = $v;
		}
	}
	
	foreach($arr_composite_shared as $k=>$v) {
		$arr_master_clnt_rr[$k] = look_up_client($k);
	}

//show_array($arr_master_composite);
//show_array($arr_master_clnt_rr);

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
?>