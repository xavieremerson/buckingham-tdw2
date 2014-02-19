<?
//Task to create static data for Client Revenue.
//Performance Issue when runnung on demand.

//error_reporting(E_ALL);
ini_set("memory_limit","512M");

include('../includes/dbconnect.php');
include('../includes/global.php');
include('../includes/functions.php');

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//Trade Date To Process
$trade_date_to_process = previous_business_day();

//now mtd comm
//get the start day of month for this
$global_qry_date_start_mtd = db_single_val("SELECT brk_start_date as single_val 
																			FROM `brk_brokerage_months` 
																			WHERE `brk_start_date` <= '".$trade_date_to_process."'
																			AND `brk_end_date` >= '".$trade_date_to_process."'");
//ydebug("global_qry_date_start_mtd",$global_qry_date_start_mtd);
$arr_mtd_comm = array();
$qry_mtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 GROUP BY trad_advisor_code
								 ORDER BY trad_advisor_code";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{ 
	$arr_mtd_comm[$row_mtd_comm["trad_advisor_code"]] = $row_mtd_comm["trad_comm"];
}

//print_r($arr_mtd_comm);
$arr_mtd_chek = array();
$qry_mtd_chek = "SELECT chek_advisor, sum( chek_amount ) as chek_comm 
								 FROM chk_chek_payments_etc 
								 WHERE chek_date between '".date("Y-m-",strtotime($trade_date_to_process))."01"."' and '".$trade_date_to_process."'
								 AND chek_isactive = 1
								 GROUP BY chek_advisor
								 ORDER BY chek_advisor";
$result_mtd_chek = mysql_query($qry_mtd_chek) or die (tdw_mysql_error($qry_mtd_chek));
while ( $row_mtd_chek = mysql_fetch_array($result_mtd_chek) ) 
{
	$arr_mtd_chek[$row_mtd_chek["chek_advisor"]] = $row_mtd_chek["chek_comm"];
}
//print_r($arr_mtd_chek);

$sum_curr_mtd = array(); //THIS IS THE VALUE USED FOR OUTPUT
foreach ($arr_mtd_comm as $k=>$v) {
	$sum_curr_mtd[$k] = $v + $arr_mtd_chek[$k];
	
}
foreach ($arr_mtd_chek as $k=>$v) {
	if (!array_key_exists($k,$arr_mtd_comm)) {
		$sum_curr_mtd[$k] = $v;
	}
}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

//empty revenue table
$rez = mysql_query("truncate table _client_revenue");


//11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//Get Client Address (Complete array to be looked up when needed)
$clnt_address = array();
$qry_address = "SELECT clnt_code, clnt_address_city, clnt_address_state from int_clnt_clients 
								where clnt_status ='A'";
$result = mysql_query($qry_address) or die(tdw_mysql_error($qry_address));
while ( $row = mysql_fetch_array($result) ) 
{
	if (trim($row["clnt_address_city"]) == "") {
		$clnt_address[$row["clnt_code"]] = "---";
	} else {
		$clnt_address[$row["clnt_code"]] = $row["clnt_address_city"].", ".$row["clnt_address_state"];
	}
}
//show_array($clnt_address);

//11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//22222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222
//Client current tier and how many years in that tier and if it went up or down.
	for ($i=1;$i<7;$i++) {
		$arr_years[] = date('Y') - $i;
	}
	$arr_years = array_reverse($arr_years);
	
	$arr_active_clients = array();
	$qry_active_clients = "select distinct(trim(clnt_code)) as active_clients 
												 from int_clnt_clients 
												 where clnt_isactive = 1 and clnt_status = 'A'";
	$result_active_clients = mysql_query($qry_active_clients) or die (tdw_mysql_error($qry_active_clients));
	while ( $row = mysql_fetch_array($result_active_clients) ) {
		$arr_active_clients[] = $row["active_clients"];
	}
	$str_active_clients = implode("','", $arr_active_clients);
	$str_active_clients = "('".$str_active_clients."')";
	//echo $str_active_clients;
	
	$arr_clnt_yearly_total = array();
	$qry_yearly_total = "select yrt_advisor_code, yrt_year, round(sum(yrt_commission),0) as clnt_revenue
											 from yrt_yearly_total_lookup
											 where yrt_advisor_code in ".$str_active_clients." 
											 group by yrt_advisor_code, yrt_year
											 order by yrt_advisor_code, yrt_year";
	$result_yearly_total = mysql_query($qry_yearly_total) or die (tdw_mysql_error($qry_yearly_total));
	while ( $row = mysql_fetch_array($result_yearly_total) ) {
		$arr_clnt_yearly_total[] = $row["yrt_advisor_code"]."^".$row["yrt_year"]."^".$row["clnt_revenue"];
	}
	$str_perf = sprintf("%01.2f",((getmicrotime()-$time)/1000));

	//Get Client Total (in 1000s) by year
	function get_clnt_yearly_total($clnt_code, $year, $arr_clnt_yearly_total) {
		$ret_val = "";
		foreach ($arr_clnt_yearly_total as $k=>$v) {
			$arr_val_pieces = explode("^",$v);
			if ($arr_val_pieces[0] == $clnt_code && $arr_val_pieces[1] == $year) {
				$ret_val = round(($arr_val_pieces[2]/1000),0);;
			}
		}
		
		if ($ret_val == "") { $ret_val = 0;}
		
		return $ret_val;
	}
	
	//Annualized Current Year
	$qry_cur_yearly_total = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y')."-01-01' and '".date('Y')."-12-31' 
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_cur_yearly_total = mysql_query($qry_cur_yearly_total) or die (tdw_mysql_error($qry_cur_yearly_total));
	$arr_cur_yearly_total = array();
	$arr_cur_yearly_total_actual = array();
	while ( $row = mysql_fetch_array($result_cur_yearly_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z'))*365,0);
		$arr_cur_yearly_total_actual[$row["trad_advisor_code"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_total[$row["trad_advisor_code"]] = $annualized_cur_year; 
	}
	
	//Annualized Current Year Checks
	$qry_cur_yearly_chk_total = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y')."-01-01' and '".date('Y')."-12-31' 
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
																	
	$result_cur_yearly_chk_total = mysql_query($qry_cur_yearly_chk_total) or die (tdw_mysql_error($qry_cur_yearly_chk_total));
	$arr_cur_yearly_chk_total = array();
	$arr_cur_yearly_chk_total_actual = array();
	while ( $row = mysql_fetch_array($result_cur_yearly_chk_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z'))*365,0);
		$arr_cur_yearly_chk_total_actual[$row["chek_advisor"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_chk_total[$row["chek_advisor"]] = $annualized_cur_year;
	}
	//show_array($arr_cur_yearly_chk_total);
	
	function tier_duration ($clnt_code, $arr_clnt_yearly_total, $arr_cur_yearly_total, $arr_cur_yearly_chk_total) {
		$arr_revenue = array();
		foreach ($arr_clnt_yearly_total as $k=>$v){
			$arr_val_pieces = explode("^",$v);
			if ($arr_val_pieces[0] == $clnt_code) {
				$arr_revenue[$arr_val_pieces[1]] = $arr_val_pieces[2];
			}
		}
		
		$arr_revenue[date('Y')] = $arr_cur_yearly_total[$clnt_code] + $arr_cur_yearly_chk_total[$clnt_code];
		
		$cur_tier = "";
		$cur_tier_years = "";
		$cur_tier_up_down = "--";
		
		$cur_year_tier = get_tier($arr_revenue[date('Y')]);
		$cur_tier = $cur_year_tier;
		$cur_tier_years = 1;
		
		
		$arr_prior_tiers = array();
		for ($i=1;$i<7;$i++) {
			$arr_prior_tiers[date('Y') - $i] = get_tier($arr_revenue[date('Y')-$i]);
		}
		
		$hold_tier = $cur_tier;
		foreach ($arr_prior_tiers as $year=>$tier) {
			if ($tier == $hold_tier) {
				$cur_tier_years = $cur_tier_years + 1;
				$hold_tier = $tier;
			} else if ($tier > $hold_tier) {
				$cur_tier_up_down = "&uarr;";
				break;
			} else if ($tier < $hold_tier) {
				$cur_tier_up_down = "&darr;";
				break;
			} else {
				$var_dummy = 1;
			}
		}

		$arr_return = array($cur_tier, $cur_tier_years, $cur_tier_up_down);
		return $arr_return;
	}

//show_array(tier_duration ("CAPG", $arr_clnt_yearly_total,$arr_cur_yearly_total,$arr_cur_yearly_chk_total));
//22222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222

//Get user lookup
$arr_id_for_initials = array();
$arr_id_for_rr_num = array();
$arr_rr_for_id = array();
$arr_name_for_id = array();
$arr_name_for_initials = array();
$q_users = "select ID, rr_num, Initials, Fullname from users;";
$r_users = mysql_query($q_users) or die (tdw_mysql_error($q_users));
while ( $row_users = mysql_fetch_array($r_users) ) {
	$arr_id_for_initials[$row_users["Initials"]] = $row_users["ID"];
	$arr_id_for_rr_num[$row_users["rr_num"]] = $row_users["ID"];
	$arr_rr_for_id[$row_users["ID"]] = $row_users["rr_num"];
	$arr_name_for_id[$row_users["ID"]] = $row_users["Fullname"];
	$arr_name_for_initials[$row_users["Initials"]] = $row_users["Fullname"];
}

//Create Lookup Array of Client Code / Client Name
// also get a list of reps to use in filter
$qry_clients = "select clnt_code,
                       clnt_name,
											 trim(clnt_rr1) as clnt_rr1,
											 trim(clnt_rr2) as clnt_rr2
								from int_clnt_clients where clnt_isactive = 1";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
$arr_client_rrs = array();
$arr_reps = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	$arr_client_rrs[$row_clients["clnt_code"]] = $row_clients["clnt_rr1"]."##".$row_clients["clnt_rr2"];
	if ($row_clients["clnt_rr1"] != "") { $arr_reps[$row_clients["clnt_rr1"]] = $arr_name_for_initials[$row_clients["clnt_rr1"]]; }
	if ($row_clients["clnt_rr2"] != "") { $arr_reps[$row_clients["clnt_rr2"]] = $arr_name_for_initials[$row_clients["clnt_rr2"]]; }
}

asort($arr_reps);


////
//function get user_id from rr_num
function get_userid_for_rr ($rr_num) {
  global $arr_id_for_rr_num;
	$user_id = $arr_id_for_rr_num[$rr_num];
	//$user_id = db_single_val("SELECT ID as single_val FROM users WHERE rr_num = '".$rr_num."'");   
	return $user_id;
}

//function get user_id from Initials
function get_userid_for_initials ($Initials) {
	global $arr_id_for_initials;
	$user_id = $arr_id_for_initials[$Initials];
	//$user_id = db_single_val("SELECT ID as single_val FROM users WHERE Initials = '".$Initials."'");   
	return $user_id;
}

//function get sole rr_num from ID
function get_rr_num ($ID) {
	global $arr_rr_for_id;
	$rr_num = $arr_rr_for_id[$ID];
	//$rr_num = db_single_val("SELECT rr_num as single_val FROM users WHERE ID = '".$ID."'");   
	return $rr_num;
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    ////
		// Get date in previous year (input and output format: yyyy-mm-dd)
		function get_tier($amt) {
			if ($amt <= 50000) {
				return 4;
			} elseif ($amt > 50000 && $amt <= 100000) {
				return 3;
			} elseif ($amt > 100000 && $amt <= 200000) {
				return 2;
			} elseif ($amt > 200000) {
				return 1;
			} else {
				return "?";
			}
		}

		//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++=
		$qry = "SELECT min( trad_trade_date ) as trad_trade_date, `trad_advisor_code` 
						FROM `mry_comm_rr_trades` 
						WHERE trad_advisor_code NOT LIKE '&%'
						GROUP BY trad_advisor_code";
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		$arr_client_last_year = array();
		while ( $row = mysql_fetch_array($result) ) 
		{
			if (substr($row["trad_trade_date"],0,4) == substr($previous_year_date,0,4)) {
				$arr_client_last_year[$row["trad_advisor_code"]] = $row["trad_trade_date"];
			}
		}
		
		//show_array($arr_client_last_year);

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
		
		//Get all data from table into an array
		$qry_prev_year = "SELECT yrt_advisor_code, yrt_commission 
											FROM yrt_yearly_total_lookup
											WHERE yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// auto_id  clnt_code  clnt_year  clnt_attribute Values are B and T for Budget and Tier clnt_budget (USD) 
// clnt_tier Tier Data (1-4) clnt_override_id  clnt_override_comment  clnt_timestamp  clnt_isactive  
//int_clnt_clients_tiering

$arr_clnt_tier_count = array();
$arr_clnt_tier = array();
$arr_clnt_tier_history = "";
$qry_tier = "select auto_id, clnt_code, clnt_tier, clnt_budget, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_attribute = 'T'
							 AND clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_isactive = 1
							 ORDER BY clnt_code, clnt_timestamp"; 
$result_tier = mysql_query($qry_tier) or die (tdw_mysql_error($qry_tier));
while ( $row_tier = mysql_fetch_array($result_tier) ) 
{
	$arr_clnt_tier_count[$row_tier["clnt_code"]] = $arr_clnt_tier_count[$row_tier["clnt_code"]] + 1;
	$arr_clnt_tier[$row_tier["clnt_code"]] = $row_tier["clnt_tier"];
}

//show_array($arr_clnt_tier);
//TABLE DATA INSERT TABLE DATA INSERT TABLE DATA INSERT TABLE DATA INSERT TABLE DATA INSERT TABLE DATA INSERT 

					$arr_output = array();
					
					$arr_output[] = array("City/State","RR2","Trdr.","Tier","Yrs.","u/d","Region","RR1","HIST","Client","MTD($)","YTD($K)","Ann.",(date('Y')-1),(date('Y')-2),(date('Y')-3),"CY Ann.","CY vs PY %");
						
						$query_clients = "SELECT * from int_clnt_clients 
															where clnt_status ='A' order by clnt_name";
						//echo $query_clients;
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{

						//GET TIER
						$clnt_tieroverride = 0;
						if ($arr_clnt_tier_count[$row["clnt_code"]] > 0) {
						//$arr_clnt_tier_count[$row_tier["clnt_code"]] = $arr_clnt_tier_count[$row_tier["clnt_code"]] + 1;
						//$arr_clnt_tier[$row_tier["clnt_code"]] = $row_tier["clnt_tier"];
								$month_val = 1;
								$ann_val = get_previous_yr_data($row["clnt_code"]);
								$ann_string = "&#9658;"."Tier Modified."."<br>";
								$val_tier = $arr_clnt_tier[$row["clnt_code"]];
								$clnt_tieroverride = 1;
						} else {
							if (array_key_exists($row["clnt_code"],$arr_client_last_year)) {
								$month_val = (int)substr($arr_client_last_year[$row["clnt_code"]],5,2);
								if ($month_val != 1 && get_previous_yr_data($row["clnt_code"]) != 0) {
									$ann_val = (get_previous_yr_data($row["clnt_code"])*12)/(13-$month_val);
									$ann_string = "Annualized $". get_previous_yr_data($row["clnt_code"])." with a start date of ". format_date_ymd_to_mdy($arr_client_last_year[$row["clnt_code"]])."<br>";
									$val_tier = get_tier($ann_val);
								} else {
									$ann_val = (get_previous_yr_data($row["clnt_code"])*12)/(13-$month_val);
									$ann_string = "";
									$val_tier = get_tier($ann_val);
								}
							} else {
								$month_val = 1;
								$ann_val = get_previous_yr_data($row["clnt_code"]);
								$ann_string = "";
								$val_tier = get_tier($ann_val);
							}
						}
						
						//GET BUDGET
						$clnt_budget = "";
						$clnt_budgetoverride = 0;
						if ($arr_clnt_budget_count[$row["clnt_code"]] >= 1) {
							$clnt_budget = $arr_clnt_budget[$row["clnt_code"]];						
						} 
						
						if ($arr_clnt_budget_count[$row["clnt_code"]] > 1) {
							$clnt_budgetoverride = 1;						
						} 

						$proper_cname = trim(str_replace("'","",$row["clnt_name"]));
						$proper_cname = trim(str_replace("&","and",$proper_cname));
																
						if ($count_row%2 == 0) {
							$rowclass = ""; //' class="trdark"';
						} else {
							$rowclass = ""; //' class="trlight"';
						}

						$b_override = "";
						if ($clnt_budgetoverride == "1") { 
							$b_override = "&Ocirc;";
						}
						
						$t_override = "";
						if ($clnt_tieroverride == "1") { 
							$t_override = "&Ocirc;";
						}

						$arr_get_tier = tier_duration ($row["clnt_code"], $arr_clnt_yearly_total,$arr_cur_yearly_total,$arr_cur_yearly_chk_total);
						
						$cya = round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0);
						$py = get_clnt_yearly_total($row["clnt_code"], (date('Y')-1), $arr_clnt_yearly_total);
						if ($py == 0) {
						$pct_chng = "--";
						} else {
						$pct_chng = round( (((($cya)-($py))/($py))*100) , 0);
						}
            if ($filter_tier && $filter_tier != "") {
								if ($val_tier == $filter_tier) {
								$arr_output[] = array(
																			$clnt_address[$row["clnt_code"]],
																			$row["clnt_rr2"],
																			$row["clnt_trader"],
																			$arr_get_tier[0],
																			$arr_get_tier[1],
																			$arr_get_tier[2],
																			$row["clnt_address_state"],
																			$row["clnt_rr1"],
																			$row["clnt_code"],
																			substr(trim($row["clnt_name"]),0,24),
																			$sum_curr_mtd[$row["clnt_code"]],
																			($arr_cur_yearly_total_actual[$row["clnt_code"]] + $arr_cur_yearly_chk_total_actual[$row["clnt_code"]]),
																			round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-1), $arr_clnt_yearly_total),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-2), $arr_clnt_yearly_total),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-3), $arr_clnt_yearly_total),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-4), $arr_clnt_yearly_total),																			
																			round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0),
																			$pct_chng
																			);
									$count_row = $count_row + 1;
									}
							} else {
								$arr_output[] = array(
																			$clnt_address[$row["clnt_code"]],
																			$row["clnt_rr2"],
																			$row["clnt_trader"],
																			$arr_get_tier[0],
																			$arr_get_tier[1],
																			$arr_get_tier[2],
																			$row["clnt_address_state"],
																			$row["clnt_rr1"],
																			$row["clnt_code"],
																			substr(trim($row["clnt_name"]),0,24),
																			$sum_curr_mtd[$row["clnt_code"]],
																			($arr_cur_yearly_total_actual[$row["clnt_code"]] + $arr_cur_yearly_chk_total_actual[$row["clnt_code"]]),
																			round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-1), $arr_clnt_yearly_total),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-2), $arr_clnt_yearly_total),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-3), $arr_clnt_yearly_total),
																			get_clnt_yearly_total($row["clnt_code"], (date('Y')-4), $arr_clnt_yearly_total),																			
																			round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0),
																			$pct_chng
																			);
							$count_row = $count_row + 1;
							}																								
										
						}
							// onmouseout=\"document.getElementById('C_" + rowclients_array[3] + "').className='m_off';\" onmouseover=\"document.getElementById('C_" + rowclients_array[3] + "').className='m_on';\"
//print_r($arr_output);

//insert data into _client_revenue table
foreach($arr_output as $k=>$v) {
	if ($k != 0) {
	$qry = "insert into _client_revenue values(NULL,". 
	        "'".$v[0]."',".
	        "'".$v[1]."',".
	        "'".$v[2]."',".
	        "'".$v[3]."',".
	        "'".$v[4]."',".
	        "'".$v[5]."',".
	        "'".$v[6]."',".
	        "'".$v[7]."',".
	        "'".$v[8]."',".
	        "'".str_replace("'","''",$v[9])."',".
	        "'".$v[10]."',".
	        "'".$v[11]."',".
	        "'".$v[12]."',".
	        "'".$v[13]."',".
	        "'".$v[14]."',".
	        "'".$v[15]."',".
	        "'".$v[16]."',".
	        "'".$v[17]."',".
	        "'".$v[18]."',now()".")";
	//echo $qry;
	$result = mysql_query($qry) or die (tdw_mysql_error($qry));
	}
}

echo date('m/d/Y h:i:sa').": CLIENT REVENUE DATA INSERTED INTO TABLE.\n\n";
?>