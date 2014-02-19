<?

//function get shared rr_num from client
//function corrected, was giving wrong output
function get_rr_num ($initial_a, $initial_b) {
	$userid_a = get_userid_for_initials($initial_a);
	$userid_b = get_userid_for_initials($initial_b);
	//xdebug("initials/userid_a",$initial_a."/".$userid_a);
	//xdebug("initials/userid_b",$initial_b."/".$userid_b);
	if ($userid_a == '' or $userid_a == NULL) {
		return '';
	} elseif ($userid_a != '' AND ($userid_b == '' or $userid_b == NULL)) {
		$prim_rr = db_single_val("select rr_num as single_val from users where ID = ".$userid_a);	
		return $prim_rr;
	} else {
		$qry_shared_rr_num = "SELECT trim(srep_rrnum) as srep_rrnum 
													FROM sls_sales_reps
													WHERE srep_user_id ='".$userid_a."'
													AND	srep_isactive = 1 
													AND srep_rrnum
													IN (
													SELECT trim(srep_rrnum)  
													FROM sls_sales_reps
													WHERE 
														srep_isactive = 1 
														AND srep_user_id ='".$userid_b."')";   
		//xdebug("qry_shared_rr_num",$qry_shared_rr_num);
		$result_shared_rr_num = mysql_query($qry_shared_rr_num) or die(tdw_mysql_error($qry_shared_rr_num));
		while($row_shared_rr_num = mysql_fetch_array($result_shared_rr_num)) {
			$shared_rr_num = $row_shared_rr_num["srep_rrnum"];
		}
		return $shared_rr_num;
	}
}

//Get user lookup
$arr_id_for_initials = array();
$arr_id_for_rr_num = array();
$arr_rr_for_id = array();
$arr_name_for_id = array();
$arr_name_for_initials = array();
$arr_rr_for_initials = array();
$q_users = "select ID, rr_num, Initials, Fullname from users;";
$r_users = mysql_query($q_users) or die (tdw_mysql_error($q_users));
while ( $row_users = mysql_fetch_array($r_users) ) {
	$arr_id_for_initials[$row_users["Initials"]] = $row_users["ID"];
	$arr_id_for_rr_num[$row_users["rr_num"]] = $row_users["ID"];
	$arr_rr_for_id[$row_users["ID"]] = $row_users["rr_num"];
	$arr_name_for_id[$row_users["ID"]] = $row_users["Fullname"];
	$arr_name_for_initials[$row_users["Initials"]] = $row_users["Fullname"];
	$arr_rr_for_initials[$row_users["Initials"]] = $row_users["rr_num"];
}

//Create Lookup Array of Client Code / Client Name
// also get a list of reps to use in filter
$qry_clients = "select clnt_code,
                       clnt_name,
											 trim(clnt_rr1) as clnt_rr1,
											 trim(clnt_rr2) as clnt_rr2
								from int_clnt_clients where clnt_isactive = 1
								and clnt_trader = '".$user_initials."'";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
$arr_clients_show = array();
$arr_client_rrs = array();
$arr_reps = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	$arr_clients_show[] = $row_clients["clnt_code"];
	$arr_client_rrs[$row_clients["clnt_code"]] = $row_clients["clnt_rr1"]."##".$row_clients["clnt_rr2"];
	if ($row_clients["clnt_rr1"] != "") { $arr_reps[$row_clients["clnt_rr1"]] = $arr_name_for_initials[$row_clients["clnt_rr1"]]; }
	if ($row_clients["clnt_rr2"] != "") { $arr_reps[$row_clients["clnt_rr2"]] = $arr_name_for_initials[$row_clients["clnt_rr2"]]; }
}

$final_display_client = array();
foreach ($arr_clients as $k=>$v) {
	//echo $k."<br>	";
	$arr_reps = explode("##",$arr_client_rrs[$k]);
	if ($arr_reps[1]=="") { //Primary
		$final_display_client[$k] = $arr_rr_for_initials[$arr_reps[0]];
	} else { //Shared
		$final_display_client[$k] = get_rr_num ($arr_reps[0], $arr_reps[1]);
	}
}

ksort($final_display_client);

//show_array($final_display_client);

$str_final_clients = " ('".implode("','",$arr_clients_show)."') ";
//echo $str_final_clients;
///exit;

//function get user_id from Initials
function get_userid_for_initials ($Initials) {
  //dupe initials caused problems
		if (trim($Initials) != '') {
		$qry = "SELECT ID as single_val FROM users WHERE Initials = '".$Initials."' and Role < 5";
		$user_id = db_single_val($qry);   
		//xdebug("qry",$qry);
		//xdebug("user_id",$user_id);
	return $user_id;
	} else {
		return '';
	}
}


//some variables used down below
$arr_commission_clients = array();

if ($datefilterval) {
	$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
} else {
	$trade_date_to_process = previous_business_day();
}
$rep_to_process = $rr_num;

//Create Lookup Array of Client Code / Client Name
	$qry_clients = "select * from int_clnt_clients";
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
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

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
											WHERE yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//John Hickey (Trader with no Client Trades in Golden Tree, but want's to see the trades in his run (not trades but checks)


//*********************************************************************************************

//now get the trad commissions for the clients for the day, qtd, mtd, ytd
//for the day
$qry_day_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date = '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_advisor_code in ".$str_final_clients."
								 GROUP BY trad_advisor_code";
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
								 AND trad_advisor_code in ".$str_final_clients."
								 GROUP BY trad_advisor_code";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{
	$arr_mtd_comm[$row_mtd_comm["trad_advisor_code"]] = $row_mtd_comm["trad_comm"];
}
//show_array($arr_mtd_comm);

//get qtd values
//get quarter start date
$arr_qtr_start = array(1 => "Jan", 2 => "Apr", 3 => "Jul", 4 => "Oct");

$arr_month_in_qtr = array('01'=>1,'02'=>1,'03'=>1,'04'=>2,'05'=>2,'06'=>2,'07'=>3,'08'=>3,'09'=>3,'10'=>4,'11'=>4,'12'=>4);

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
								 AND trad_advisor_code in ".$str_final_clients."
								 GROUP BY trad_advisor_code";
$result_qtd_comm = mysql_query($qry_qtd_comm) or die (tdw_mysql_error($qry_qtd_comm));
while ( $row_qtd_comm = mysql_fetch_array($result_qtd_comm) ) 
{
	$arr_qtd_comm[$row_qtd_comm["trad_advisor_code"]] = $row_qtd_comm["trad_comm"];
}
//show_array($arr_qtd_comm);

//now get ytd
$global_year_start_date = db_single_val("SELECT brk_start_date as single_val 
																		FROM `brk_brokerage_months` 
																		WHERE brk_month = 'Jan'
																	  AND brk_year = '".substr($trade_date_to_process,0,4)."'");

$qry_ytd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_advisor_code in ".$str_final_clients."
								 GROUP BY trad_advisor_code";
								 //WHERE trad_trade_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
								 								 
$result_ytd_comm = mysql_query($qry_ytd_comm) or die (tdw_mysql_error($qry_ytd_comm));
while ( $row_ytd_comm = mysql_fetch_array($result_ytd_comm) ) 
{
	$arr_ytd_comm[$row_ytd_comm["trad_advisor_code"]] = $row_ytd_comm["trad_comm"];
}
//show_array($arr_ytd_comm);



//now get the check commissions for the clients for the day, qtd, mtd, ytd
//for the day

$qry_day_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date = '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";

//xdebug("qry_day_check",$qry_day_check);

$result_day_check = mysql_query($qry_day_check) or die (tdw_mysql_error($qry_day_check));
while ( $row_day_check = mysql_fetch_array($result_day_check) ) 
{
	$arr_day_check[$row_day_check["chek_advisor"]."#".get_rr_num($row_day_check["rr1"],$row_day_check["rr2"])] = $row_day_check["total_checks"];
	$arr_day_check_new[$row_day_check["chek_advisor"]] = $row_day_check["total_checks"];
}

//show_array($arr_day_check_new);

//for the mtd
$qry_mtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";

echo "<!--".$qry_mtd_check."-->";								
$result_mtd_check = mysql_query($qry_mtd_check) or die (tdw_mysql_error($qry_mtd_check));
while ( $row_mtd_check = mysql_fetch_array($result_mtd_check) ) 
{
	$arr_mtd_check[$row_mtd_check["chek_advisor"]."#".get_rr_num($row_mtd_check["rr1"],$row_mtd_check["rr2"])] = $row_mtd_check["total_checks"];
	$arr_mtd_check_new[$row_mtd_check["chek_advisor"]] = $row_mtd_check["total_checks"];
}

//show_array($arr_mtd_check_new);

//for the qtd
$qry_qtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
//xdebug("qry_qtd_check",$qry_qtd_check);
$result_qtd_check = mysql_query($qry_qtd_check) or die (tdw_mysql_error($qry_qtd_check));
while ( $row_qtd_check = mysql_fetch_array($result_qtd_check) ) 
{
	$arr_qtd_check[$row_qtd_check["chek_advisor"]."#".get_rr_num($row_qtd_check["rr1"],$row_qtd_check["rr2"])] = $row_qtd_check["total_checks"];
	$arr_qtd_check_new[$row_qtd_check["chek_advisor"]] = $row_qtd_check["total_checks"];
}

//show_array($arr_qtd_check_new);
//xdebug("qry_day_check",$qry_day_check);

//for the ytd
$qry_ytd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
echo "<!--".$qry_ytd_check."-->";
$result_ytd_check = mysql_query($qry_ytd_check) or die (tdw_mysql_error($qry_ytd_check));
while ( $row_ytd_check = mysql_fetch_array($result_ytd_check) ) 
{
	$arr_ytd_check[$row_ytd_check["chek_advisor"]."#".get_rr_num($row_ytd_check["rr1"],$row_ytd_check["rr2"])] = $row_ytd_check["total_checks"];
	$arr_ytd_check_new[$row_ytd_check["chek_advisor"]] = $row_ytd_check["total_checks"];
}

//show_array($arr_ytd_check_new);
//echo "<!--".print_r($arr_ytd_check)."-->";
//xdebug("qry_day_check",$qry_day_check);
//exit;
?>