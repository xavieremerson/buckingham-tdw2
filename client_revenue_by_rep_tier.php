<?

$trade_date_to_process = previous_business_day();
$previous_year_date = get_date_previous_year($trade_date_to_process);

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

//33333333333333333333333333333333333333333333333333333333333333333333333333333333333333333333
//get all Sales Reps based on any transaction withing the last year.
$arr_rep_nums = array();
$qry_rep_nums = "SELECT distinct(trad_rr) as trad_rr from mry_comm_rr_trades 
								 WHERE trad_run_date > '".(date(Y)-1).date("-m-d")."' 
								 and trad_is_cancelled = 0
								 order by trad_rr";
$result = mysql_query($qry_rep_nums) or die(tdw_mysql_error($qry_rep_nums));
while ( $row = mysql_fetch_array($result) ) 
{
		$arr_rep_nums[] = $row["trad_rr"];
}

//get initials and names of reps.
$arr_initials_names = array();
foreach($arr_rep_nums as $k=>$v) {
	if (substr($v,0,1) == "0") {
		$str_qry = "select Initials, Fullname from users where rr_num = '".$v."'";
		$result = mysql_query($str_qry) or die(tdw_mysql_error($str_qry));
		while ( $row = mysql_fetch_array($result) ) {
			$arr_initials_names[$row["Initials"]] = $row["Fullname"];
		} 
	}
}

ksort($arr_initials_names);
//show_array($arr_initials_names);

//GET PROSPECTS FOR REPS.
$qry_prospects = "SELECT clnt_name, clnt_rr1, clnt_rr2
									FROM int_clnt_clients 
									WHERE clnt_status LIKE 'P%'
									AND clnt_rr1 != ''";
$result = mysql_query($qry_prospects) or die(tdw_mysql_error($qry_prospects));
while ( $row = mysql_fetch_array($result) ){
	$prospect_rep_list[$row["clnt_name"]] = $row["clnt_rr1"]."^".$row["clnt_rr2"];	
}

//show_array($prospect_rep_list);
//exit;


$rep_prospect_count = array();
foreach($arr_initials_names as $initial=>$name) {
  //echo "Processing >>".$name." >> ".$initial."<br>";
	foreach ($prospect_rep_list as $clnt_name=>$reps) {
		$arr_rep_vals = explode("^",$reps);
			if ($arr_rep_vals[0] == $initial && $arr_rep_vals[1] == "") {
				$rep_prospect_count[$initial]["Sole"] = $rep_prospect_count[$initial]["Sole"] + 1;
			} else if ($arr_rep_vals[0] == $initial && $arr_rep_vals[1] != "") {
				$rep_prospect_count[$initial]["JP"] = $rep_prospect_count[$initial]["JP"] + 1;
			} else if ($arr_rep_vals[1] == $initial) {
				$rep_prospect_count[$initial]["JS"] = $rep_prospect_count[$initial]["JS"] + 1;
			}
	}
}
//show_array($rep_prospect_count);
//exit;


$clnt_rep_list = array();
$qry_clnt_rep_list = "SELECT clnt_code, clnt_rr1, clnt_rr2 from int_clnt_clients  
											where clnt_status ='A'
											and clnt_isactive = 1";
$result = mysql_query($qry_clnt_rep_list) or die(tdw_mysql_error($qry_clnt_rep_list));
while ( $row = mysql_fetch_array($result) ) 
{
	if (trim($row["clnt_rr1"]) != "" 
			&& trim($row["clnt_rr1"])!= "**" 
			&& trim($row["clnt_code"]) != "----"
			&& substr(trim($row["clnt_code"]),0,4) != "ADJ ") {
		$clnt_rep_list[$row["clnt_code"]] = $row["clnt_rr1"]."^".$row["clnt_rr2"];
	}
}


//Count of Sole, Joint Primary and Joint Secondary Clients for Reps.
function total_client_types($rr_initials, $clnt_rep_list) {
	$arr_rep_client_type_nums = array();
	foreach ($clnt_rep_list as $k=>$v) {
		$arr_rep_vals = explode("^",$v);
		if ($arr_rep_vals[0] == $rr_initials && $arr_rep_vals[1] == "") {
			$arr_rep_client_type_nums["Sole"] = $arr_rep_client_type_nums["Sole"] + 1;
		} else if ($arr_rep_vals[0] == $rr_initials && $arr_rep_vals[1] != "") {
			$arr_rep_client_type_nums["JP"] = $arr_rep_client_type_nums["JP"] + 1;
		} else if ($arr_rep_vals[1] == $rr_initials) {
			$arr_rep_client_type_nums["JS"] = $arr_rep_client_type_nums["JS"] + 1;
		}
	}	
	
	$arr_sorted_counts = array();
	$arr_sorted_counts["Sole"] = $arr_rep_client_type_nums["Sole"];
	$arr_sorted_counts["JP"] = $arr_rep_client_type_nums["JP"];
	$arr_sorted_counts["JS"] = $arr_rep_client_type_nums["JS"];
	
	
	return $arr_sorted_counts;
}

//show_array(total_client_types("CC", $clnt_rep_list));

//Client current tier and how many years in that tier and if it went up or down.
	for ($i=1;$i<3;$i++) {
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

	$arr_active_clients = array();
	$qry_active_clients = "select distinct(trim(clnt_code)) as active_clients 
												 from int_clnt_clients 
												 where clnt_isactive = 1 and clnt_status = 'A'
												 and clnt_code!= '----' and clnt_code not like 'ADJ %'";
	$result_active_clients = mysql_query($qry_active_clients) or die (tdw_mysql_error($qry_active_clients));
	while ( $row = mysql_fetch_array($result_active_clients) ) {
		$arr_active_clients[] = $row["active_clients"];
	}
	$str_active_clients = implode("','", $arr_active_clients);
	$str_active_clients = "('".$str_active_clients."')";
	//echo $str_active_clients;
		
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
	while ( $row =
	 mysql_fetch_array($result_cur_yearly_total) ) {
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

//get all active clients and their reps (sole / shared)


//get sole accounts

//get joint primary 

//get joint secondary

//get total revenue for joint primary clients annualized

//get total revenue for sole clients annualized

//get total revenue for joint secondary annualized

//33333333333333333333333333333333333333333333333333333333333333333333333333333333333333333333

//22222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222
//Client current tier and how many years in that tier and if it went up or down.
	
	//Array of Client Revenue, Tier, Type and Reps.
	$clnt_rep_revenue_tier = array();
  foreach ($arr_active_clients as $k=>$v) {
		$str_revenue = $arr_cur_yearly_total[$v] + $arr_cur_yearly_chk_total[$v];
		$str_tier = get_tier($str_revenue);
		$arr_rep_codes = explode("^",$clnt_rep_list[$v]);
		$str_rep1 = $arr_rep_codes[0];
		$str_rep2 = $arr_rep_codes[1];
		$clnt_rep_revenue_tier[$v] = array($str_revenue,$str_tier,$str_rep1,$str_rep2);
		//echo $v. " ==> " . $str_revenue. " ==> " . $str_tier ." ==> " . $str_rep1." ==> " . $str_rep2."<br>";
	}
	//show_array($clnt_rep_revenue_tier);
	//exit;
	
	function total_revenue_by_rep_type ($rr_initials, $type, $clnt_rep_revenue_tier) {
		$total_revenue = 0;
		$average_revenue = 0;
		foreach($clnt_rep_revenue_tier as $k=>$v) {
			if ($type == 'Sole') {
				if ($v[2]==$rr_initials && $v[3] == "") { //Sole
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JP') {
				if ($v[2]==$rr_initials && $v[3] != "") { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JS') {
				if ($v[3]==$rr_initials) { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else {
				$var_dummy = 1;
			}
		}
		
		if ($count_clients > 0) {
			$average_revenue = $total_revenue / $count_clients;
		} else {
			$average_revenue = 0;
		}
		
		return array($count_clients,number_format(round(($total_revenue/1000),0),0,"",","),round(($average_revenue/1000),0));
		
	}
	
	function total_revenue_by_rep_tier($rr_initials, $tier, $type, $clnt_rep_revenue_tier) {
		$count_clients = 0;
		$total_revenue = 0;
		$average_revenue = 0;
		foreach($clnt_rep_revenue_tier as $k=>$v) {
			if ($type == 'Sole') {
				if ($v[2]==$rr_initials && $v[3] == "" && $v[1] == $tier ) { //Sole
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JP') {
				if ($v[2]==$rr_initials && $v[3] != "" && $v[1] == $tier ) { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JS') {
				if ($v[3]==$rr_initials && $v[1] == $tier ) { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else {
				$var_dummy = 1;
			}
		}
		
		if ($count_clients > 0) {
			$average_revenue = $total_revenue / $count_clients;
		} else {
			$average_revenue = 0;
		}
		
		return array($count_clients,number_format(round(($total_revenue/1000),0),0,"",","),round(($average_revenue/1000),0));
		
	}
	
	function client_tier_duration ($clnt_code, $arr_clnt_yearly_total, $arr_cur_yearly_total, $arr_cur_yearly_chk_total) {
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
//44444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444
	//show_array($arr_clnt_yearly_total);
	
	function rep_client_type_tier_count ($clnt_code, $arr_clnt_yearly_total, $arr_cur_yearly_total, $arr_cur_yearly_chk_total) {
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
		for ($i=1;$i<3;$i++) {
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

		$arr_return = array($clnt_code, $cur_tier, $cur_tier_years, $cur_tier_up_down);
		return $arr_return;
	}

	
	foreach ($arr_active_clients as $k=>$clnt_code) {
		//show_array(rep_client_type_tier_count ($clnt_code, $arr_clnt_yearly_total,$arr_cur_yearly_total,$arr_cur_yearly_chk_total));
	}

	
//function rep_clnt_tier_status($k, "Sole", $clnt_rep_revenue_tier);
	//Array of rep_type_tier_add_drop_net
	$rep_type_tier_add_drop_net = array();
  
	foreach($arr_initials_names as $rep_initial=>$rep_name) {
			
			foreach ($arr_active_clients as $k=>$clnt_code) {
			
				$arr_rep_codes = explode("^",$clnt_rep_list[$clnt_code]);
				$str_rep1 = $arr_rep_codes[0];
				$str_rep2 = $arr_rep_codes[1];
				//$str_revenue = $arr_cur_yearly_total[$clnt_code] + $arr_cur_yearly_chk_total[$clnt_code];
				//$str_tier = get_tier($str_revenue);
				// array($clnt_code, $cur_tier, $cur_tier_years, $cur_tier_up_down);
				$arr_clnt_tier_movement = rep_client_type_tier_count ($clnt_code, $arr_clnt_yearly_total,$arr_cur_yearly_total,$arr_cur_yearly_chk_total);

				//Sole
				if ($str_rep1 == $rep_initial && $str_rep2 == "") {
					if ($arr_clnt_tier_movement[3] == '&uarr;') {
						$rep_type_tier_add_drop_net[$rep_initial]['Sole'][$arr_clnt_tier_movement[1]]["ADD"] = $rep_type_tier_add_drop_net[$rep_initial]['Sole'][$arr_clnt_tier_movement[1]]["ADD"] + 1;
					} else if ($arr_clnt_tier_movement[3] == '&darr;') {
						$rep_type_tier_add_drop_net[$rep_initial]['Sole'][$arr_clnt_tier_movement[1]]["DROP"] = $rep_type_tier_add_drop_net[$rep_initial]['Sole'][$arr_clnt_tier_movement[1]]["DROP"] + 1;
					} else {
						$rep_type_tier_add_drop_net[$rep_initial]['Sole'][$arr_clnt_tier_movement[1]]["STATQ"] = $rep_type_tier_add_drop_net[$rep_initial]['Sole'][$arr_clnt_tier_movement[1]]["STATQ"] + 1;
					}
				}
				//JP
				if ($str_rep1 == $rep_initial && $str_rep2 !="") {
					if ($arr_clnt_tier_movement[3] == '&uarr;') {
						$rep_type_tier_add_drop_net[$rep_initial]['JP'][$arr_clnt_tier_movement[1]]["ADD"] = $rep_type_tier_add_drop_net[$rep_initial]['JP'][$arr_clnt_tier_movement[1]]["ADD"] + 1;
					} else if ($arr_clnt_tier_movement[3] == '&darr;') {
						$rep_type_tier_add_drop_net[$rep_initial]['JP'][$arr_clnt_tier_movement[1]]["DROP"] = $rep_type_tier_add_drop_net[$rep_initial]['JP'][$arr_clnt_tier_movement[1]]["DROP"] + 1;
					} else {
						$rep_type_tier_add_drop_net[$rep_initial]['JP'][$arr_clnt_tier_movement[1]]["STATQ"] = $rep_type_tier_add_drop_net[$rep_initial]['JP'][$arr_clnt_tier_movement[1]]["STATQ"] + 1;
					}
				}
				//JS
				if ($str_rep1 != $rep_initial && $str_rep2 == $rep_initial) {
					if ($arr_clnt_tier_movement[3] == '&uarr;') {
						$rep_type_tier_add_drop_net[$rep_initial]['JS'][$arr_clnt_tier_movement[1]]["ADD"] = $rep_type_tier_add_drop_net[$rep_initial]['JS'][$arr_clnt_tier_movement[1]]["ADD"] + 1;
					} else if ($arr_clnt_tier_movement[3] == '&darr;') {
						$rep_type_tier_add_drop_net[$rep_initial]['JS'][$arr_clnt_tier_movement[1]]["DROP"] = $rep_type_tier_add_drop_net[$rep_initial]['JS'][$arr_clnt_tier_movement[1]]["DROP"] + 1;
					} else {
						$rep_type_tier_add_drop_net[$rep_initial]['JS'][$arr_clnt_tier_movement[1]]["STATQ"] = $rep_type_tier_add_drop_net[$rep_initial]['JS'][$arr_clnt_tier_movement[1]]["STATQ"] + 1;
					}
				}

				//$clnt_rep_revenue_tier[$v] = array($str_revenue,$str_tier,$str_rep1,$str_rep2);
				//echo $v. " ==> " . $str_revenue. " ==> " . $str_tier ." ==> " . $str_rep1." ==> " . $str_rep2."<br>";
			}
			
	}
	
	//print_r(show_array($rep_type_tier_add_drop_net);
	//exit;

//44444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444

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
		
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

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
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&




///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////

	echo "<center>";
	?>
	<? tsp(100, "Client Revenue BY Sales Rep."); ?>

		<table width="100%" border="0" cellpadding="1", cellspacing="0">
    <tr> 
		<?
    if ($mode == 'm') {
    ?>
    <td width="200">&#9658;<a class="ilt" href="client_revenue_by_rep_tier_excel.php?mode=m&filter_rep=<?=$filter_rep?>&filter_tier=<?=$filter_tier?>" target="_blank">Export to Excel</a></td>
		<?
    } else {
		?>
    <td width="200">&#9658;<a class="ilt" href="client_revenue_by_rep_tier_excel.php?mode=r&user_initials=<?=$user_initials?>" target="_blank">Export to Excel</a></td>
    <?
		}
		if ($mode == 'm') {
		?>
    <td width="10">&nbsp;</td>
    <!--<td width="150">
    <form action="<?=$PHP_SELF?>" method="get">
    <input type="hidden" name="mod" value="client_revenue" />
    <input type="hidden" name="mode" value="m" />
    <select name="filter_rep" id="filter_rep" size="1">
			<option value="" selected="selected"> All Reps. </option>
			<option value="BRG"> BRG </option>
    	<?
				foreach ($arr_reps as $k=>$v) {
					if ($v != "") {
						echo '<option value="'.$k.'"> '.$v.' </option>';
					} 
				}
			?>
    </select>
    </td>-->
    <td width="10">&nbsp;</td>
    <!--<td width="60">
    <select name="filter_tier" id="filter_tier" size="1">
			<option value="" selected="selected"> All Tiers. </option>
			<option value="1"> Tier 1 </option>
			<option value="2"> Tier 2 </option>
			<option value="3"> Tier 3 </option>
			<option value="4"> Tier 4 </option>
    </select>
    </td>
    <td width="10">&nbsp;</td>
    <td width="50"><input type="submit" name="Filter" value="   SHOW   "/></td>-->
		</form>	
		<?
    }
    ?>    
    <td align="right"><a class="ilt"><font color="red">NOTE:</font> To apply multiple column sort, please use Shift + Click.</a></td>
		</tr></table>
          <!--TABLE 2 START-->
					<link rel="stylesheet" href="includes/jquery/__jquery.tablesorter/themes/blue/style.css" type="text/css" media="print, projection, screen" />
					<script type="text/javascript" src="includes/jquery/jquery-1.8.2.min.js"></script>
					<script type="text/javascript" src="includes/jquery/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
					<script type="text/javascript">

					$(document).ready(function() {     
						// call the tablesorter plugin and assign widgets with id "zebra" (Default widget in the core) and the newly created "reindextbl"
						$("#myTable").tablesorter(
							{widgets: ['zebra']},
							{headers:  {2: {sorter:false} } }
						);
					}); 
					</script>	
 					<table id="myTable" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
						<thead>
            <tr>
              <td width="1400" colspan="19" bgcolor="#f7f7f7"><center><strong>Change in Account Status</strong></center></td>
							<td bgcolor="#ffffff">&nbsp;</td>
						</tr>
            <tr>
              <td colspan="4" bgcolor="#f7f7f7">&nbsp;</td>
							<td colspan="3" bgcolor="#009933"><center><strong><font color="#FFFFFF">TIER 1</font></strong></center></td>
              <td colspan="3" bgcolor="#00CC66"><center><strong>TIER 2</strong></center></td>
              <td colspan="3" bgcolor="#FFCC33"><center><strong>TIER 3</strong></center></td>
              <td colspan="3" bgcolor="#FF0000"><center><strong><font color="#FFFFFF">TIER 4</font></strong></center></td>
              <td colspan="3" bgcolor="#000000"><center><strong><font color="#FFFFFF">Prospects & Inactive Accounts</font></strong></center></td>
							<td bgcolor="#f7f7f7">&nbsp;</td>
						</tr>
            <tr>
              <td width="20">&nbsp;</td>
              <th width="150">Sales Rep.</th>
              <th width="80">Type</th>
              <th width="40"># Accts.</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<th width="60">Status<br />Quo</th>
							<th width="45">Adds</th>
							<th width="45">Drops</th>
							<td bgcolor="#ffffff">&nbsp;</td>
						</tr>
					  </thead>
					  <tbody>	
						<? 

						foreach($arr_initials_names as $k=>$v) {
								
								foreach(total_client_types($k, $clnt_rep_list) as $type=>$cnt_type) {
									if ($type == 'Sole' && $cnt_type > 0) {
									
									//$rep_type_tier_add_drop_net[$rep_initial]['Sole'][$arr_clnt_tier_movement[1]]["DROP"];									
									echo	"<tr>".
                          "<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_mult_years_by_rep.php?rep=".$k."\", 620, 330, false);'></a></td>".
													"<td>".$v."</td>".
													"<td>Sole</td>". 
													"<td>".$cnt_type."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][1]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][1]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][1]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][2]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][2]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][2]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][3]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][3]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][3]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][4]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][4]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['Sole'][4]["DROP"]."</td>". 
													"<td align='right'>".$rep_prospect_count[$k]["Sole"]."</td>". 
													"<td align='right'>&nbsp;</td>". 
													"<td align='right'>&nbsp;</td>". 
													"<td>&nbsp;</td>". 
												"</tr>";
									} else if ($type == 'JP' && $cnt_type > 0){

									echo	"<tr>".
                          "<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_mult_years_by_rep.php?rep=".$k."\", 620, 330, false);'></a></td>".
													"<td>".$v."</td>".
													"<td>Joint Primary</td>". 
													"<td>".$cnt_type."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][1]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][1]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][1]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][2]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][2]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][2]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][3]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][3]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][3]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][4]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][4]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JP'][4]["DROP"]."</td>". 
													"<td align='right'>".$rep_prospect_count[$k]["JP"]."</td>". 
													"<td align='right'>&nbsp;</td>". 
													"<td align='right'>&nbsp;</td>". 
													"<td>&nbsp;</td>". 
												"</tr>";
									} else if ($type == 'JS' && $cnt_type > 0) {
									
									echo	"<tr>".
                          "<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_mult_years_by_rep.php?rep=".$k."\", 620, 330, false);'></a></td>".
													"<td>".$v."</td>".
													"<td>Joint Secondary</td>". 
													"<td>".$cnt_type."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][1]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][1]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][1]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][2]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][2]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][2]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][3]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][3]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][3]["DROP"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][4]["STATQ"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][4]["ADD"]."</td>". 
													"<td align='right'>".$rep_type_tier_add_drop_net[$k]['JS'][4]["DROP"]."</td>". 
													"<td align='right'>".$rep_prospect_count[$k]["JS"]."</td>". 
													"<td align='right'>&nbsp;</td>". 
													"<td align='right'>&nbsp;</td>". 
													"<td>&nbsp;</td>". 
												"</tr>";
									} else {
											$var_dummmy = 0;
									}
								}
						}
						?> 
					 </tbody>
 					</table>
	
		<? tep();
		
		echo "</center>";
/////////////////////////////////////////////////END OF MANAGE SECTION/////////////////////////////////////////////////
?>