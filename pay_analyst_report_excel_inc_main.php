<?

////
// Get previous quarter for getting adjustments.

function gpq ($q, $y) { //get previous quarter

	if ($q == 1) {
		$ret_q = 4;
		$ret_y = $y - 1;
	} else if ($q == 2) {
		$ret_q = 1;
		$ret_y = $y;
	} else if ($q == 3) {
		$ret_q = 2;
		$ret_y = $y;
	} else if ($q == 4) {
		$ret_q = 3;
		$ret_y = $y;
	} else {
		$ret_q = 0;
		$ret_y = 0;
	}

  $arr_return = array($ret_q,$ret_y);
	return $arr_return;
}

function create_arr ($q, $i=1) {
  $arr_created = array();
	$result = mysql_query($q) or die(tdw_mysql_error($q));
	if ($i == 1) {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[] = $row["v"];
		}
	} else {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[$row["k"]] = $row["v"];
		}
	}
	return $arr_created;
}

//Create Lookup Array of Client Code / Client Name

	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = trim($row_clients["clnt_name"]);
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

$arr_quarter_brok_dates = get_quarter_dates ($sel_qtr, $sel_year);
$arr_quarter_cal_dates = get_quarter_dates ($sel_qtr, $sel_year, "C");

//show_array($arr_quarter_brok_dates);
//show_array($arr_quarter_cal_dates);

	$arr_clnt_master_qtr = array();
	$qry_clnt_master_qtr = "SELECT distinct(trad_advisor_code) 
													FROM mry_comm_rr_trades 
													WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
														and trad_is_cancelled = 0
													order by trad_advisor_code";
	//xdebug("qry_clnt_master_qtr",$qry_clnt_master_qtr);
	$result_clnt_master_qtr = mysql_query($qry_clnt_master_qtr) or die (tdw_mysql_error($qry_clnt_master_qtr));
	while ( $row_clnt_master_qtr = mysql_fetch_array($result_clnt_master_qtr) ) 
	{
		$arr_clnt_master_qtr[$row_clnt_master_qtr["trad_advisor_code"]] = $row_clnt_master_qtr["trad_advisor_code"];
	}
	
	$qry_clnt_master_qtr = "SELECT distinct(a.chek_advisor) as chek_advisor
														FROM chk_chek_payments_etc a
														WHERE a.chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
															AND a.chek_isactive = 1
													 ORDER BY a.chek_advisor";
	//xdebug("qry_clnt_for_rr",$qry_clnt_for_rr);
	$result_clnt_master_qtr = mysql_query($qry_clnt_master_qtr) or die (tdw_mysql_error($qry_clnt_master_qtr));
	while ( $row_clnt_master_qtr = mysql_fetch_array($result_clnt_master_qtr) ) 
	{
		$arr_clnt_master_qtr[$row_clnt_master_qtr["chek_advisor"]] = $row_clnt_master_qtr["chek_advisor"];
	}
	
	ksort($arr_clnt_master_qtr);
	//show_array($arr_clnt_master_qtr);

	//Get array of all initials of users against the clients (RR1 ONLY)
	$str_clients = implode(",",$arr_clnt_master_qtr);
	$str_clients = str_replace(",",'","',$str_clients);
	$str_clients = '"'.$str_clients.'"';
	
	//echo $str_clients;

	$arr_clnt_rr_initials = array();
	$qry_clnt_rr_initials = "select clnt_code, clnt_rr1 from int_clnt_clients where clnt_code in (".$str_clients.")";
	$result_clnt_rr_initials = mysql_query($qry_clnt_rr_initials) or die (tdw_mysql_error($qry_clnt_rr_initials));
	while ( $row_clnt_rr_initials = mysql_fetch_array($result_clnt_rr_initials) ) 
	{
		if (trim($row_clnt_rr_initials["clnt_rr1"]) != "") {
			$arr_clnt_rr_initials[trim($row_clnt_rr_initials["clnt_code"])] = trim($row_clnt_rr_initials["clnt_rr1"]);
		}
	}
	
	//show_array($arr_clnt_rr_initials);
	
	//ARRAY OF JUST INITIALS
	$arr_list_sales = array();
	foreach ($arr_clnt_rr_initials as $k=>$v) {
			if ($v != '') {
				$arr_list_sales[$v] = $v;
			}
	} 
	
	//FOR SCOTT BRUNNER
	if ($sel_qtr == "3" && $sel_year == "2011") {
	$arr_list_sales['SB'] = 'SB';
	}
	
	//ARRAY OF INITIALS, ID
	$arr_initials_id_temp = array();
	foreach($arr_list_sales as $k=>$v) {
		$arr_initials_id_temp[$v] = db_single_val("select ID as single_val from users where Initials = '".$v."'");
	}

	$arr_initials_id = array();
	$qry_get_reps = "SELECT
										ID, rr_num, concat(Firstname, ' ', Lastname ) as rep_name, Initials 
										from users
									WHERE user_isactive = 1
									AND is_login_acct  = 1
									ORDER BY Firstname";
	$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
	while($row_get_reps = mysql_fetch_array($result_get_reps))
	{
			if (in_array($row_get_reps["ID"], $arr_initials_id_temp)) {
				$arr_initials_id[$row_get_reps["Initials"]] = $row_get_reps["ID"];
			}
	}
	//show_array($arr_initials_id);
	//exit;
    //Dirty hack for Brandon Heller
    if ($sel_qtr == "4" && $sel_year == "2013") {
        $arr_initials_id['BH'] = 325;
    }




?>