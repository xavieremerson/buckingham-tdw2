<?

//some variables used down below
$arr_commission_clients = array();

if ($datefilterval) {
		//xdebug('datefilterval',$datefilterval);
		$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
		$trdr_user_id = $trader_user_id;
		$show_selected_user = "(".get_user_by_id($trdr_user_id).")";
		
		$str_urr = "select rr_num as single_val from users where ID = '".$trdr_user_id."'";
		//xdebug("str_urr",$str_urr);
		$rep_to_process = db_single_val($str_urr);
		//xdebug("rep_to_process",$rep_to_process);

		//get initials for the user
		$str_ui = "select Initials as single_val from users where ID = '".$trdr_user_id."'";
		$user_initials = db_single_val($str_ui);
		//xdebug("user_initials",$user_initials);
		
		//xdebug('trade_date_to_process',$trade_date_to_process);
} else {
		$trade_date_to_process = previous_business_day();
		$trdr_user_id = "";
		$show_selected_user = "";
		$rep_to_process = "";
		$user_initials = 'NO_INITIALS';
		//xdebug('trade_date_to_process',$trade_date_to_process);
}

//xdebug("user_initials",$user_initials);
//xdebug('trade_date_to_process',$trade_date_to_process);
//xdebug("rep_to_process",$rep_to_process);
//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

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
		$qry_prev_year = "SELECT yrt_advisor_code, sum(yrt_commission) as yrt_commission
											FROM yrt_yearly_total_lookup
											WHERE yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											AND trim(yrt_rr) != '099'
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

//Create Lookup Array of Client Code / Client Name
	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Get lookup relevant client codes from client master (internal) for verification
$qry_relevant_clients = "SELECT DISTINCT (a.clnt_code) as relevant_primary_client
																	FROM int_clnt_clients a, Users b
																	WHERE a.clnt_trader = b.Initials
																	AND b.ID = '".$trdr_user_id."'
																	ORDER BY a.clnt_name";
//xdebug("qry_relevant_clients",$qry_relevant_clients);
$result_relevant_clients = mysql_query($qry_relevant_clients) or die (tdw_mysql_error($qry_relevant_clients));
$arr_relevant_clients = array();
while ( $row_relevant_clients = mysql_fetch_array($result_relevant_clients) ) 
{
	$arr_relevant_clients[$row_relevant_clients["relevant_primary_client"]] = $row_relevant_clients["relevant_primary_client"];
}
$str_show_clnt = implode("','",$arr_relevant_clients);
$str_show_clnt = "'".$str_show_clnt."'";
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//now get distinct Client/RR Combinations from the trades table
$qry_dist_clnt_rr = "SELECT DISTINCT (
											concat( trad_advisor_code, '#', trad_rr ) 
											) as clnt_rr
											FROM mry_comm_rr_trades
											WHERE trad_is_cancelled =0
											AND trad_advisor_code in (".$str_show_clnt.") 
											ORDER BY trad_advisor_code, trad_rr";
//xdebug("qry_dist_clnt_rr",$qry_dist_clnt_rr);
$result_dist_clnt_rr = mysql_query($qry_dist_clnt_rr) or die (tdw_mysql_error($qry_dist_clnt_rr));
$arr_show_clnt_rr = array();
while ( $row_dist_clnt_rr = mysql_fetch_array($result_dist_clnt_rr) ) 
{
	$arr_show_clnt_rr[] = $row_dist_clnt_rr["clnt_rr"];
}
//print_r($arr_show_clnt_rr);

//CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
//CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
//Added Checks Data here also, so ALL Clients show up in the display.
$arr_show_clnt_rr_c = array();
$qry_dist_clnt_rr = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
//xdebug("qry_ytd_check",$qry_ytd_check);
$result_dist_clnt_rr = mysql_query($qry_dist_clnt_rr) or die (tdw_mysql_error($qry_dist_clnt_rr));
while ( $row_dist_clnt_rr = mysql_fetch_array($result_dist_clnt_rr) ) 
{
	$arr_show_clnt_rr_c[] = $row_dist_clnt_rr["chek_advisor"]."#".get_rr_num($row_dist_clnt_rr["rr1"],$row_dist_clnt_rr["rr2"]);
}

//if not already in the previous array, include values.
foreach($arr_show_clnt_rr_c as $k=>$v) {

	if (!in_array($v,$arr_show_clnt_rr)) {
		$arr_show_clnt_rr[] = $v;
	}

}
//CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC
//CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC






$str_show_clnt_rr = implode("','",$arr_show_clnt_rr);
$str_show_clnt_rr = "'".$str_show_clnt_rr."'";
//xdebug("str_show_clnt_rr",$str_show_clnt_rr);

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
											and trad_rr = '".$rep_to_process."' 
										order by trad_advisor_code";

$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["trad_advisor_code"]] = $row_clnt_for_rr["trad_advisor_code"];
}


//show_array($arr_clnt_for_rr);

$qry_clnt_for_rr = "SELECT distinct(a.chek_advisor) as chek_advisor
										FROM chk_chek_payments_etc a
										 left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
										WHERE a.chek_date <= '".$trade_date_to_process."' 
											AND a.chek_isactive = 1
											AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
									  ORDER BY a.chek_advisor";
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}
//show_array($arr_clnt_for_rr);


//now get the trad commissions for the clients for the day, qtd, mtd, ytd
//for the day
$qry_day_comm = "SELECT concat(trad_advisor_code,'#',trad_rr) as trad_clnt_rr, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date = '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND concat(trad_advisor_code,'#',trad_rr) in (".$str_show_clnt_rr.")
								 GROUP BY concat(trad_advisor_code,'#',trad_rr)
								 ORDER BY concat(trad_advisor_code,'#',trad_rr)";
//xdebug("qry_day_comm",$qry_day_comm);
$result_day_comm = mysql_query($qry_day_comm) or die (tdw_mysql_error($qry_day_comm));
while ( $row_day_comm = mysql_fetch_array($result_day_comm) ) 
{
	$arr_day_comm[$row_day_comm["trad_clnt_rr"]] = $row_day_comm["trad_comm"];
}

//show_array($arr_day_comm);


//now mtd comm
//get the start day of month for this
$global_qry_date_start_mtd = db_single_val("SELECT brk_start_date as single_val 
																			FROM `brk_brokerage_months` 
																			WHERE `brk_start_date` <= '".$trade_date_to_process."'
																			AND `brk_end_date` >= '".$trade_date_to_process."'");
//xdebug("global_qry_date_start_mtd",$global_qry_date_start_mtd);

$qry_mtd_comm = "SELECT concat(trad_advisor_code,'#',trad_rr) as trad_clnt_rr, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND concat(trad_advisor_code,'#',trad_rr) in (".$str_show_clnt_rr.")
								 GROUP BY concat(trad_advisor_code,'#',trad_rr)
								 ORDER BY concat(trad_advisor_code,'#',trad_rr)";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{
	$arr_mtd_comm[$row_mtd_comm["trad_clnt_rr"]] = $row_mtd_comm["trad_comm"];
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


$qry_qtd_comm = "SELECT concat(trad_advisor_code,'#',trad_rr) as trad_clnt_rr, sum( trad_commission ) as trad_comm
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND concat(trad_advisor_code,'#',trad_rr) in (".$str_show_clnt_rr.")
								 GROUP BY concat(trad_advisor_code,'#',trad_rr)
								 ORDER BY concat(trad_advisor_code,'#',trad_rr)";
$result_qtd_comm = mysql_query($qry_qtd_comm) or die (tdw_mysql_error($qry_qtd_comm));
while ( $row_qtd_comm = mysql_fetch_array($result_qtd_comm) ) 
{
	$arr_qtd_comm[$row_qtd_comm["trad_clnt_rr"]] = $row_qtd_comm["trad_comm"];
}
//show_array($arr_qtd_comm);

//now get ytd
$global_year_start_date = db_single_val("SELECT brk_start_date as single_val 
																		FROM `brk_brokerage_months` 
																		WHERE brk_month = 'Jan'
																	  AND brk_year = '".substr($trade_date_to_process,0,4)."'");

$qry_ytd_comm = "SELECT concat(trad_advisor_code,'#',trad_rr) as trad_clnt_rr, sum( trad_commission ) as trad_comm
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND concat(trad_advisor_code,'#',trad_rr) in (".$str_show_clnt_rr.")
								 GROUP BY concat(trad_advisor_code,'#',trad_rr)
								 ORDER BY concat(trad_advisor_code,'#',trad_rr)";
								 //WHERE trad_trade_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
								 								 
$result_ytd_comm = mysql_query($qry_ytd_comm) or die (tdw_mysql_error($qry_ytd_comm));
while ( $row_ytd_comm = mysql_fetch_array($result_ytd_comm) ) 
{
	$arr_ytd_comm[$row_ytd_comm["trad_clnt_rr"]] = $row_ytd_comm["trad_comm"];
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
}

//show_array($arr_day_check);

//for the mtd
$qry_mtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, 
										trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
$result_mtd_check = mysql_query($qry_mtd_check) or die (tdw_mysql_error($qry_mtd_check));
while ( $row_mtd_check = mysql_fetch_array($result_mtd_check) ) 
{
	$arr_mtd_check[$row_mtd_check["chek_advisor"]."#".get_rr_num($row_mtd_check["rr1"],$row_mtd_check["rr2"])] = $row_mtd_check["total_checks"];
}

//show_array($arr_mtd_check);

//for the qtd
$qry_qtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
$result_qtd_check = mysql_query($qry_qtd_check) or die (tdw_mysql_error($qry_qtd_check));
while ( $row_qtd_check = mysql_fetch_array($result_qtd_check) ) 
{
	$arr_qtd_check[$row_qtd_check["chek_advisor"]."#".get_rr_num($row_qtd_check["rr1"],$row_qtd_check["rr2"])] = $row_qtd_check["total_checks"];
}

//show_array($arr_qtd_check);
//xdebug("qry_qtd_check",$qry_qtd_check);

//for the ytd
$arr_ytd_check = array();
$arr_ytd_check_cc = array();

$qry_ytd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor, trim(b.clnt_rr1) as rr1, trim(b.clnt_rr2) as rr2
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND (b.clnt_trader = '".$user_initials."')
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
//xdebug("qry_ytd_check",$qry_ytd_check);
$result_ytd_check = mysql_query($qry_ytd_check) or die (tdw_mysql_error($qry_ytd_check));
while ( $row_ytd_check = mysql_fetch_array($result_ytd_check) ) 
{
	$arr_ytd_check[$row_ytd_check["chek_advisor"]."#".get_rr_num($row_ytd_check["rr1"],$row_ytd_check["rr2"])] = $row_ytd_check["total_checks"];
	$arr_ytd_check_cc[$row_ytd_check["chek_advisor"]] = $row_ytd_check["total_checks"];
}

//show_array($arr_ytd_check);
//show_array($arr_ytd_check_cc);
//xdebug("qry_day_check",$qry_day_check);
//exit;
//show_array($arr_show_clnt_rr);
?>