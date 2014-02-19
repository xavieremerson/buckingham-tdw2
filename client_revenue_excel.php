<?

// Turn off all error reporting
error_reporting(0);

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = "client_revenue.xls";
$fp = fopen($exportlocation.$output_filename, "w");


$trade_date_to_process = previous_business_day();
$previous_year_date = get_date_previous_year($trade_date_to_process);

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 

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
?>

<!--

<? // print_r($arr_reps); ?>

-->

<?

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

//get rr_num and initials for client
//function corrected, was giving wrong output
function get_rep_for_client ($arr_client_rrs, $client_code) {
  //$initial_a, $initial_b
	$arr_initials = explode('##',	$arr_client_rrs[$client_code]);
	$initial_a = $arr_initials[0];
	$initial_b = $arr_initials[1];
	
	if (strlen($initial_b) > 1 and strlen($initial_a) > 1) { //we are talking about shared reps.
	    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$userid_a = get_userid_for_initials($initial_a);
			$userid_b = get_userid_for_initials($initial_b);
			//xdebug("initials/userid_a",$initial_a."/".$userid_a);
			//xdebug("initials/userid_b",$initial_b."/".$userid_b);
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
	    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	} elseif (strlen($initial_b) == 0 and strlen($initial_a) > 1) {
	    //===============================================================================================
			$prim_rr_num = get_rr_num (get_userid_for_initials ($initial_a));
			return $prim_rr_num;
	    //===============================================================================================
	} else {
	    return "BRG"; 
	}
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
$arr_clnt_budget_count = array();
$arr_clnt_budget = array();
$arr_clnt_budget_history = "";
$qry_budget = "select auto_id, clnt_code, clnt_budget, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_attribute = 'B'
							 AND clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_isactive = 1
							 ORDER BY clnt_code, clnt_timestamp"; 
$result_budget = mysql_query($qry_budget) or die (tdw_mysql_error($qry_budget));
while ( $row_budget = mysql_fetch_array($result_budget) ) 
{
	$arr_clnt_budget_count[$row_budget["clnt_code"]] = $arr_clnt_budget_count[$row_budget["clnt_code"]] + 1;
	$arr_clnt_budget[$row_budget["clnt_code"]] = $row_budget["clnt_budget"];
}
//show_array($arr_clnt_budget);
//xdebug("qry_budget",$qry_budget);
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
//????????????????????????????????????????
$arr_clnt_comment = array();
if ($mode == 'm') {
$qry_comment = "select auto_id, clnt_code, clnt_attribute, clnt_comment, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_isactive = 1
							 AND clnt_code != '----'
							 ORDER BY clnt_code, clnt_timestamp desc"; 
} else {
$qry_comment = "select auto_id, clnt_code, clnt_attribute, clnt_comment, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_override_id = '".$user_id."' 
							 AND clnt_isactive = 1
							 AND clnt_code != '----'
							 ORDER BY clnt_code, clnt_timestamp desc"; 
}
$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
while ( $row_comment = mysql_fetch_array($result_comment) ) 
{
  $whowhen = "";
	
	if ($row_comment["clnt_override_id"] != "") { $whowhen = "[".date('m/d/y h:ia',strtotime($row_comment["clnt_timestamp"]))." ".$arr_name_for_id[$row_comment["clnt_override_id"]]."]<br>";}
	if (($row_comment["clnt_attribute"] == 'T' || $row_comment["clnt_attribute"] == 'B') && strlen($row_comment["clnt_override_comment"]) > 1) { //
		$str_comment_data = str_replace('"','',$row_comment["clnt_override_comment"]);
		//$str_comment_data = str_replace('"','\\"',$row_comment["clnt_override_comment"]);
		$arr_clnt_comment[$row_comment["clnt_code"]] = "&#9658;".$whowhen.$str_comment_data."<br>".$arr_clnt_comment[$row_comment["clnt_code"]]; 
	} elseif ($row_comment["clnt_attribute"] == 'C') {
		$str_comment_data = str_replace('"','',$row_comment["clnt_comment"]);
		//$str_comment_data = str_replace('"','\\"',$row_comment["clnt_override_comment"]);
		$arr_clnt_comment[$row_comment["clnt_code"]] = "&#9658;".$whowhen.$str_comment_data."<br>".$arr_clnt_comment[$row_comment["clnt_code"]];
	} else {
	 $dummy = 1;
	}
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&




///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////

					$str .= '<table>
						<tr>
              <th width="120">City/State</td>".
							<th width="40">RR2</th>
							<th width="40">Trdr.</th>
              <th width="40">Tier</th>
              <th width="40">Yrs.</th>
              <th width="30">u/d</th>
              <th width="60">Region</th>
							<th width="40">RR1</th>
							<th width="220">Client</th>
							<th width="65">YTD($K)</th>
							<th width="50">Ann.</th>
							<th width="50">'.(date('Y')-1).'</th>
							<th width="50">'.(date('Y')-2).'</th>
							<th width="50">'.(date('Y')-3).'</th>
							<th width="60">CY Ann.</th>
							<th width="90">CY vs PY %</th>
							<!--<th width="80">Tier</th>-->
							<th>&nbsp;</th>
						</tr>';

						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						if ($mode == 'm') {
							 if ($filter_rep && $filter_rep != "" && $filter_rep != 'BRG') {
							    $augment_filter = " (clnt_rr1 = '".$filter_rep."' OR clnt_rr2 = '".$filter_rep."' ) ";
							 } elseif ($filter_rep == 'BRG') {
							    $augment_filter = " (trim(clnt_rr1) = '' AND trim(clnt_rr2) = '' ) ";
							 } else {
							 		$augment_filter = " 1 ";
							 }
						} elseif ($mode == 'r') {
							 $augment_filter = " (clnt_rr1 = '".$user_initials."' OR clnt_rr2 = '".$user_initials."' ) ";
						} else {
							echo "<tr><td colspan='15'>Invalid Module Access, please contact Technical Support.</td></tr>";
							exit;
						}
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						
						$query_clients = "SELECT * from int_clnt_clients 
															where ". $augment_filter . " AND clnt_status ='A' order by clnt_name";
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
								$str .= "<tr " . $rowclass . ">".
													"<td>".$clnt_address[$row["clnt_code"]]."</td>".
													"<td>&nbsp;".$row["clnt_rr2"]."</td>". 
													"<td>&nbsp;".$row["clnt_trader"]."</td>".
													"<td>".$arr_get_tier[0]."</td>".
													"<td>".$arr_get_tier[1]."</td>".
													"<td>".$arr_get_tier[2]."</td>".
              						"<td>".$row["clnt_address_state"] ."</td>".
													"<td>&nbsp;".$row["clnt_rr1"]."</td>". 
													"<td>&nbsp;".substr(trim($row["clnt_name"]),0,24)."</td>". 
													"<td>".($arr_cur_yearly_total_actual[$row["clnt_code"]] + $arr_cur_yearly_chk_total_actual[$row["clnt_code"]])."</td>".
													"<td>".round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0)."</td>".
													"<td>".get_clnt_yearly_total($row["clnt_code"], (date('Y')-1), $arr_clnt_yearly_total)."</td>".
													"<td>".get_clnt_yearly_total($row["clnt_code"], (date('Y')-2), $arr_clnt_yearly_total)."</td>".
													"<td>".get_clnt_yearly_total($row["clnt_code"], (date('Y')-3), $arr_clnt_yearly_total)."</td>".
													"<td>".round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0)."</td>".
													"<td align='right'>".$pct_chng."%</td>".
													"<td>&nbsp;</td>".
												"</tr>";
												//"<td>&nbsp;".get_rep_for_client ($arr_client_rrs, $row["clnt_code"])."</td>".
												//"<td align='right'>".number_format($ann_val,0,"",",")."</td>".
												//"<td style=\"cursor:pointer;cursor:hand;\" align='right' onclick=\"CreateWnd('client_tiering_submodal.php?user_id=" . $user_id . "&element_id=T_" .$row["clnt_code"]. "&elementval=".$val_tier. "&cname=".$proper_cname."', 500, 280, false);\" id='T_" .$row["clnt_code"]. "'>".$val_tier." <img src='images/tier/".$val_tier.".png' height='13' border='0'>&nbsp;&nbsp;&nbsp;</td>".
													
									$count_row = $count_row + 1;
									}
							} else {
								$str .= "<tr " . $rowclass . ">".
													"<td>".$clnt_address[$row["clnt_code"]]."</td>".
													"<td>&nbsp;".$row["clnt_rr2"]."</td>". 
													"<td>&nbsp;".$row["clnt_trader"]."</td>".
													"<td>".$arr_get_tier[0]."</td>".
													"<td>".$arr_get_tier[1]."</td>".
													"<td>".$arr_get_tier[2]."</td>".
              						"<td>".$row["clnt_address_state"] ."</td>".
													"<td>&nbsp;".$row["clnt_rr1"]."</td>". 
													"<td>&nbsp;".substr(trim($row["clnt_name"]),0,24)."</td>". 
													"<td>".($arr_cur_yearly_total_actual[$row["clnt_code"]] + $arr_cur_yearly_chk_total_actual[$row["clnt_code"]])."</td>".
													"<td>".round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0)."</td>".
													"<td>".get_clnt_yearly_total($row["clnt_code"], (date('Y')-1), $arr_clnt_yearly_total)."</td>".
													"<td>".get_clnt_yearly_total($row["clnt_code"], (date('Y')-2), $arr_clnt_yearly_total)."</td>".
													"<td>".get_clnt_yearly_total($row["clnt_code"], (date('Y')-3), $arr_clnt_yearly_total)."</td>".
													"<td>".round((($arr_cur_yearly_total[$row["clnt_code"]] + $arr_cur_yearly_chk_total[$row["clnt_code"]]))/1000,0)."</td>".
													"<td align='right'>".$pct_chng."%</td>".
													"<td>&nbsp;</td>".
												"</tr>";
							$count_row = $count_row + 1;
							}																								
										
						}
							// onmouseout=\"document.getElementById('C_" + rowclients_array[3] + "').className='m_off';\" onmouseover=\"document.getElementById('C_" + rowclients_array[3] + "').className='m_on';\"


$str .= '</table>
	</body>
</html>';

fputs ($fp, $str);
fclose($fp);

//echo $str;
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>