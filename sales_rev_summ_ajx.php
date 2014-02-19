<?

  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

//Array ( [clnts] => BAIR,INVS,SCHT,WBLA,MIFD,MICH,OHIO,EVRG,EVRA [tdate] => 2013-04-04 [xrand] => 3.740189005596728 ) 
$trade_date_to_process = $tdate;
$arr_active_clients = explode(",",$clnts);
asort($arr_active_clients);
$str_active_clients = implode("','",$arr_active_clients);
$str_active_clients = " ('".$str_active_clients."') ";
//exit;


//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
?>
    <table cellpadding="3" cellspacing="1" bgcolor="#0000FF" border="0"> 
<?
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
												
//Active Clients.
																								
//Client current tier and how many years in that tier and if it went up or down.
	for ($k=1;$k<3;$k++) {
		$arr_years[] = date('Y',strtotime($trade_date_to_process)) - $k;
	}
	$arr_years = array_reverse($arr_years);
	$str_years = implode("','", $arr_years);
	$str_years = "('".$str_years."')";
	//echo $str_years;
	//show_array($arr_years);

	//Yearly Totals for Y-1 and Y-2
	$arr_clnt_yearly_total = array();
	$qry_yearly_total = "select yrt_advisor_code, yrt_year, round(sum(yrt_commission),0) as clnt_revenue
											 from yrt_yearly_total_lookup
											 where yrt_advisor_code in ".$str_active_clients." 
											 and yrt_year in ".$str_years."
											 group by yrt_advisor_code, yrt_year
											 order by yrt_advisor_code, yrt_year";
	$result_yearly_total = mysql_query($qry_yearly_total) or die (tdw_mysql_error($qry_yearly_total));
	while ( $row = mysql_fetch_array($result_yearly_total) ) {
		$arr_clnt_yearly_total[] = $row["yrt_advisor_code"]."^".$row["yrt_year"]."^".$row["clnt_revenue"];
		$arr_clnt_yearly_total_process[$row["yrt_advisor_code"]][$row["yrt_year"]]= $row["clnt_revenue"];
	}
	//show_array($arr_clnt_yearly_total);
		
	//Annualized Current Year
	$qry_cur_yearly_total = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y')."-01-01' and '".date('Y')."-12-31' 
													and trad_is_cancelled = 0
													and trad_advisor_code in ".$str_active_clients."
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
													where chek_date between '".date('Y',strtotime($trade_date_to_process))."-01-01' and '".date('Y',strtotime($trade_date_to_process))."-12-31' 
													and chek_isactive = 1
													and chek_advisor in ".$str_active_clients."
													group by chek_advisor
													order by chek_advisor";
																	
	$result_cur_yearly_chk_total = mysql_query($qry_cur_yearly_chk_total) or die (tdw_mysql_error($qry_cur_yearly_chk_total));
	$arr_cur_yearly_chk_total = array();
	$arr_cur_yearly_chk_total_actual = array();
	while ( $row = mysql_fetch_array($result_cur_yearly_chk_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z',strtotime($trade_date_to_process)))*365,0);
		$arr_cur_yearly_chk_total_actual[$row["chek_advisor"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_chk_total[$row["chek_advisor"]] = $annualized_cur_year;
	}
	//show_array($arr_cur_yearly_chk_total);
	
	//Merge Commission and Checks for Current Year.
	$arr_merge_current_year_actual = array();
	$arr_merge_current_year_annualized = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_current_year_actual[$ccode] = $arr_cur_yearly_total_actual[$ccode] + $arr_cur_yearly_chk_total_actual[$ccode];
		$arr_merge_current_year_annualized[$ccode] = $arr_cur_yearly_total[$ccode] + $arr_cur_yearly_chk_total[$ccode];
	}
	//show_array($arr_merge_current_year_actual);
	//($arr_merge_current_year_annualized);


	//Client Rep. List. Containing Rep. Initials.
	$clnt_rep_list = array();
	$clnt_names = array();
	$qry_clnt_rep_list = "SELECT clnt_code, clnt_rr1, clnt_rr2, clnt_name from int_clnt_clients  
												where clnt_code in ".$str_active_clients;
	$result = mysql_query($qry_clnt_rep_list) or die(tdw_mysql_error($qry_clnt_rep_list));
	while ( $row = mysql_fetch_array($result) ) 
	{
		$clnt_names[$row["clnt_code"]] = $row["clnt_name"];
		if (trim($row["clnt_rr1"]) != "" 
				&& trim($row["clnt_rr1"])!= "**" 
				&& trim($row["clnt_code"]) != "----"
				&& substr(trim($row["clnt_code"]),0,4) != "ADJ ") {
			$clnt_rep_list[$row["clnt_code"]] = trim($row["clnt_rr1"])."^".trim($row["clnt_rr2"]);
		}
	}
	//show_array($clnt_rep_list);
	//exit;

	//Get userid, rep name, initial from db
	$qry_get_reps = "SELECT a.ID, a.Initials, a.rr_num, trim(concat(a.Firstname,' ', a.Lastname)) AS rep_name, b.trad_rr
										FROM users a, mry_comm_rr_trades b
										WHERE a.rr_num = b.trad_rr
										AND a.user_isactive = 1
										AND b.trad_rr like '0%'
										AND trim(a.Initials) != ''
										GROUP BY b.trad_rr
										ORDER BY a.Firstname";
	
	$arr_reps_list = array();
	$arr_reps_list_array = array();												
	$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
	while ( $row = mysql_fetch_array($result_get_reps) ) {
		$arr_reps_list[$row["ID"]] = $row["Initials"]."^".$row["rr_num"]."^".$row["rep_name"];
		$arr_reps_list_array [$row["ID"]] = array($row["Initials"],$row["rr_num"],$row["rep_name"]); 
	}
	//show_array($arr_reps_list_array);
	//exit;
	
	//Array of Client Revenue, Tier, Type and Reps.
	$clnt_rep_revenue_tier = array();
  foreach ($arr_active_clients as $k=>$v) {
		//$str_revenue = $arr_cur_yearly_total[$v] + $arr_cur_yearly_chk_total[$v];
		$str_revenue = $arr_merge_current_year_annualized[$v];
		$str_tier = get_tier($str_revenue);
		$arr_rep_codes = explode("^",$clnt_rep_list[$v]);
		$str_rep1 = trim($arr_rep_codes[0]);
		$str_rep2 = trim($arr_rep_codes[1]);
		$clnt_rep_revenue_tier[$v] = array($str_revenue,$str_tier,$str_rep1,$str_rep2);
		//echo $v. "#" . $str_revenue. "#" . $str_tier ."#" . $str_rep1."#" . $str_rep2."<br>"; 
	}
	//show_array($clnt_rep_revenue_tier);
	
	//$arr_reps_list_array [$row["ID"]] = array($row["Initials"],$row["rr_num"],$row["rep_name"]);
	//$clnt_rep_revenue_tier[$v] = array($str_revenue,$str_tier,$str_rep1,$str_rep2);
	//$clnt_rep_list[$row["clnt_code"]] = trim($row["clnt_rr1"])."^".trim($row["clnt_rr2"]);

	$arr_rep_client_details = array(); // array holds details of client, tier, etc.
	foreach ($clnt_rep_revenue_tier as $ccode=>$aval) {
		if ($aval[2] != "" && $aval[3] == "" ) { //SOLE CLIENT
			foreach ($arr_reps_list_array as $ak=>$av) {
				if ($aval[2]==$av[0]) {
					$arr_rep_client_details[] = array($ak,$ccode,$aval[0],$aval[1],"Sole");
				}
			}
		} else if ($aval[2] != "" && $aval[3] != "" ) { //JOINT PRIMARY AND SECONDARY
			foreach ($arr_reps_list_array as $ak=>$av) {
				if ($aval[2]==$av[0]) {
					$arr_rep_client_details[] = array($ak,$ccode,$aval[0],$aval[1],"JP"); //PRIMARY
				}
				if ($aval[3]==$av[0]) {
					$arr_rep_client_details[] = array($ak,$ccode,$aval[0],$aval[1],"JS"); //SECONDARY
				}
			}
		} else if ($aval[2] == "" && $aval[3] == "" ) {
			$var_dummy = 1;
			//echo "[".$av[0]."]"."[".$aval[2]."]"."[".$aval[3]."]"."[".$ccode."]"."[".$aval[0]."]"."<br>";
		} else {
			//do nothing for now.
			//echo "[".$av[0]."]"."[".$aval[2]."]"."[".$aval[3]."]"."<br>";
			echo "ERROR: UNKNOWN EXCEPTION.<br>"; //
			$var_dummy = 1;
		}
	}
	//show_array($arr_rep_client_details);
	//exit;
	
	/*if ( $arr_reps_list_array [$ck][2] == 'Andrew Sinclair') {
		echo "Processing Andrew Sinclair<br>";
		xdebug("i",$i);
	}*/
	//exit;
	
	//Get Count with Tier for each user.
	$arr_tier_count = array();
	foreach($arr_rep_client_details as $bk=>$bv) {
		//show_array($bv);
		//$arr_tier_count[$bv[0]][$bv[4]][$aval[1]] = array($arr_tier_count[$bv[0]][$bv[4]][$aval[1]][0]+1, $arr_tier_count[$bv[0]][$bv[4]][$aval[1]][1]."','".$bv[1]);
		$arr_tier_count[$bv[0]][$bv[4]][$bv[3]] = array($arr_tier_count[$bv[0]][$bv[4]][$bv[3]][0]+1, $arr_tier_count[$bv[0]][$bv[4]][$bv[3]][1]."','".$bv[1]);
	}
	//show_array($arr_tier_count);	
	//exit;
	//echo ">>>>>>>>>>>>>>>>> [346]['Sole'][4]"."<br>";
	//show_array($arr_tier_count[346]['Sole'][4]);
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	
	//Get CY MTD Commission for Each Client, to be consolidated in a function.
	//xdebug('trade_date_to_process',$trade_date_to_process);
	$cur_month_start_date = db_single_val("SELECT `brk_start_date` as single_val from brk_brokerage_months where brk_start_date <= '".$trade_date_to_process."' and brk_end_date >= '".$trade_date_to_process."'");
	$qry_cymtd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".$cur_month_start_date."' and '".$trade_date_to_process."' 
													and trad_is_cancelled = 0
													and trad_advisor_code in ".$str_active_clients."
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_cymtd = mysql_query($qry_cymtd) or die (tdw_mysql_error($qry_cymtd));
	$arr_cymtd = array();
	while ( $row = mysql_fetch_array($result_cymtd) ) {
		$arr_cymtd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cymtd);
	//exit;
	
	// if Brokerage Month is selected then use end of calendar month.
	//check if trade_date_to_process exists in brokerage months data
	$is_brok_month_end = db_single_val("SELECT count(*) as single_val FROM `brk_brokerage_months` where `brk_end_date` = '".$trade_date_to_process."'");
	if ($is_brok_month_end > 0) {
		$end_date = date("Y-m-t", strtotime($trade_date_to_process));
	} else {
		$end_date = $trade_date_to_process;
	}

	
	//Get CY MTD Check for Each Client, to be consolidated in a function.
	$qry_cymtd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($trade_date_to_process))."-".date('m',strtotime($trade_date_to_process))."-01' and '".$end_date."' 
													and chek_isactive = 1
													and chek_advisor in ".$str_active_clients."
													group by chek_advisor
													order by chek_advisor";
	//xdebug("qry_cymtd_chk",$qry_cymtd_chk);
	$result_cymtd_chk = mysql_query($qry_cymtd_chk) or die (tdw_mysql_error($qry_cymtd_chk));
	$arr_cymtd_chk = array();
	while ( $row = mysql_fetch_array($result_cymtd_chk) ) {
		$arr_cymtd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cymtd_chk); 
	
	//Merge Commission and Checks for CY MTD.
	$arr_merge_cymtd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_cymtd[$ccode] = $arr_cymtd[$ccode] + $arr_cymtd_chk[$ccode];
	}
	//show_array($arr_merge_cymtd);	

	//Get CY YTD for Each Client, to be consolidated in a function.
	$qry_cyytd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y',strtotime($trade_date_to_process))."-01-01' and '".$trade_date_to_process."' 
													and trad_advisor_code in ".$str_active_clients."
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_cyytd = mysql_query($qry_cyytd) or die (tdw_mysql_error($qry_cyytd));
	$arr_cyytd = array();
	while ( $row = mysql_fetch_array($result_cyytd) ) {
		$arr_cyytd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cyytd);

	//Get CY YTD Check for Each Client, to be consolidated in a function.
	$qry_cyytd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($trade_date_to_process))."-01-01' and '".$trade_date_to_process."' 
													and chek_advisor in ".$str_active_clients."
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	$result_cyytd_chk = mysql_query($qry_cyytd_chk) or die (tdw_mysql_error($qry_cyytd_chk));
	$arr_cyytd_chk = array();
	while ( $row = mysql_fetch_array($result_cyytd_chk) ) {
		$arr_cyytd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cyytd_chk);

	//Merge Commission and Checks for CY YTD.
	$arr_merge_cyytd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_cyytd[$ccode] = $arr_cyytd[$ccode] + $arr_cyytd_chk[$ccode];
	}
	//show_array($arr_merge_cyytd);	
	//exit;
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	
	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	
	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	
	
	$py_trade_date_to_process = date('Y-m-d', strtotime("last year", strtotime($trade_date_to_process)));
	//Get PY MTD Commission for Each Client, to be consolidated in a function.
	//xdebug('trade_date_to_process',$trade_date_to_process);
	$py_cur_month_start_date = db_single_val("SELECT `brk_start_date` as single_val from brk_brokerage_months where brk_start_date <= '".$py_trade_date_to_process."' and brk_end_date >= '".$py_trade_date_to_process."'");
	
	$qry_pymtd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".$py_cur_month_start_date."' and '".$py_trade_date_to_process."' 
													and trad_advisor_code in ".$str_active_clients."
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_pymtd = mysql_query($qry_pymtd) or die (tdw_mysql_error($qry_pymtd));
	$arr_pymtd = array();
	while ( $row = mysql_fetch_array($result_pymtd) ) {
		$arr_pymtd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pymtd);
	//exit;
	
	//Get CY MTD Check for Each Client, to be consolidated in a function.
	$qry_pymtd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($py_trade_date_to_process))."-".date('m',strtotime($py_trade_date_to_process))."-01' and '".$py_trade_date_to_process."' 
													and chek_advisor in ".$str_active_clients."
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	$result_pymtd_chk = mysql_query($qry_pymtd_chk) or die (tdw_mysql_error($qry_pymtd_chk));
	$arr_pymtd_chk = array();
	while ( $row = mysql_fetch_array($result_pymtd_chk) ) {
		$arr_pymtd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pymtd_chk);
	
	//Merge Commission and Checks for CY MTD.
	$arr_merge_pymtd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_pymtd[$ccode] = $arr_pymtd[$ccode] + $arr_pymtd_chk[$ccode];
	}
	//show_array($arr_merge_pymtd);	

	//Get CY YTD for Each Client, to be consolidated in a function.
	$qry_pyytd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y',strtotime($py_trade_date_to_process))."-01-01' and '".$py_trade_date_to_process."' 
													and trad_advisor_code in ".$str_active_clients."
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_pyytd = mysql_query($qry_pyytd) or die (tdw_mysql_error($qry_pyytd));
	$arr_pyytd = array();
	while ( $row = mysql_fetch_array($result_pyytd) ) {
		$arr_pyytd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pyytd);

	//Get CY YTD Check for Each Client, to be consolidated in a function.
	$qry_pyytd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($py_trade_date_to_process))."-01-01' and '".$py_trade_date_to_process."' 
													and chek_advisor in ".$str_active_clients."
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	$result_pyytd_chk = mysql_query($qry_pyytd_chk) or die (tdw_mysql_error($qry_pyytd_chk));
	$arr_pyytd_chk = array();
	while ( $row = mysql_fetch_array($result_pyytd_chk) ) {
		$arr_pyytd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pyytd_chk);

	//Merge Commission and Checks for CY YTD.
	$arr_merge_pyytd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_pyytd[$ccode] = $arr_pyytd[$ccode] + $arr_pyytd_chk[$ccode];
	}
	//show_array($arr_merge_pyytd);	
	//exit;

	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	
	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	
	
	$arr_budget = array();
	$qry_budget = "select a.bdgt_amount, a.bdgt_year, b.clnt_code from
								int_clnt_clients_budget a
								left join int_clnt_clients b on a.clnt_id = b.clnt_auto_id 
								where a.bdgt_year = '".date('Y',strtotime($trade_date_to_process))."'";
	$result_budget = mysql_query($qry_budget) or die (tdw_mysql_error($qry_budget));
	while ( $row = mysql_fetch_array($result_budget) ) {
		$arr_budget[$row["clnt_code"]] = $row["bdgt_amount"]; 
	}
	//echo $qry_budget;
	//show_array($arr_budget);	 

	//Function to provide budget for a group of clients.
	//Inputs are Budget Array and Client Group String
	function budget_group_clients ($arr_budget, $str_clients) {
		$arr_clnts = array($str_clients);
		$return_rev = 0;
		foreach($arr_clnts as $k=>$v) {
			if (array_key_exists($v,$arr_budget)) {
				$return_rev = $return_rev + $arr_budget[$v];
			}
		}
		return $return_rev;
	}

		
	//Function to provide revenue for a group of clients.
	//Inputs are Revenue Array and Client Group String
	function rev_group_clients ($arr_rev, $str_clients) {
		$arr_clnts = array($str_clients);
		$return_rev = 0;
		foreach($arr_clnts as $k=>$v) {
			if (array_key_exists($v,$arr_rev)) {
				$return_rev = $return_rev + $arr_rev[$v];
			}
		}
		return $return_rev;
	}

	//Function to provide past years revenue for a group of clients.
	//Inputs are Revenue Array and Client Group String
	//$arr_clnt_yearly_total_process[$row["yrt_advisor_code"]][$row["yrt_year"]]= $row["clnt_revenue"];
	function rev_group_clients_past_years ($arr_clnt_yearly_total_process, $year, $str_clients) {
		//$str = "','KEYA','WEIS','GUGG";
		$arr_clnts = array($str_clients);
		$return_rev = 0;
		foreach($arr_clnts as $k=>$v) {
			if ($arr_clnt_yearly_total_process[$v][$year]) {
				$return_rev = $return_rev + $arr_clnt_yearly_total_process[$v][$year];
			}
		}
		return $return_rev;
	}
		
	//echo "THIS IS ". rev_group_clients_past_years ($arr_clnt_yearly_total_process, '2012', "','ASCE");
	//exit;	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111

	//$arr_reps_list_array [$row["ID"]] = array($row["Initials"],$row["rr_num"],$row["rep_name"]); 
	$level_a_count = 0;
	//$arr_sole_joint = array("'Sole'","'JP'","'JS'");
	$arr_sole_joint["Sole"] = "Sole";
	$arr_sole_joint["JP"] = "JP";
	$arr_sole_joint["JS"] = "JS";
	
	$arr_tier_val = array('1','2','3','4');
	//show_array($arr_tier_val);

	//echo ">>>>>>>>>>>>>>>>> [346][Sole][4]"."<br>";
	//show_array($arr_tier_count[346][Sole]['4']);

	
			foreach($arr_active_clients as $cindex=>$ccode) {
					//echo "Processing ".$i."<br>";
					//echo $ck." ".$dv." ".$ev."<br>"; 
					//for($i=1;$i<5;$i++) {
					//show_array($arr_tier_count[$ck][$dv][$i]);
					
					//if ($arr_tier_count[$ck][$dv][$i]) {
					//	echo $ck." ".$dv." ".$i."<br>";
					//}
														
							if ($level_a_count % 2) { 
									$class_row = "trdark2";
							} else { 
									$class_row = "trlight2"; 
							} 
							$cymtd = rev_group_clients($arr_merge_cymtd, $ccode);
							$pymtd = rev_group_clients($arr_merge_pymtd, $ccode);
							if ($pymtd > 0) { 
								$mtd_chng = (($cymtd-$pymtd)/$pymtd)*100; 
							} else { 
								$mtd_chng = '-NA-';
							} 
							if ($mtd_chng < 0) { 
								$str_mtd_chng = "<font color='red'>".number_format($mtd_chng,0,".",",")."%</font>"; 
							} else { 
								$str_mtd_chng = number_format($mtd_chng,0,".",",").'%';
							} 
							$cyytd = rev_group_clients($arr_merge_cyytd, $ccode);
							$pyytd = rev_group_clients($arr_merge_pyytd, $ccode);
							if ($pyytd > 0) { 
								$ytd_chng = (($cyytd-$pyytd)/$pyytd)*100; 
							} else { 
								$ytd_chng = '-NA-';
							} 
							if ($ytd_chng < 0) { 
								$str_ytd_chng = "<font color='red'>".number_format($ytd_chng,0,".",",")."%</font>"; 
							} else { 
								$str_ytd_chng = number_format($ytd_chng,0,".",",").'%';
							} 
							$py_full = rev_group_clients_past_years ($arr_clnt_yearly_total_process, date('Y',strtotime($trade_date_to_process))-1, $ccode);
							$ppy_full = rev_group_clients_past_years ($arr_clnt_yearly_total_process, date('Y',strtotime($trade_date_to_process))-2, $ccode);
							
							$bdgt_cy = budget_group_clients($arr_budget, $ccode);
							if ($bdgt_cy > 0) {
								$str_perf_bdgt = round((round(($cyytd/date('z',strtotime($py_trade_date_to_process)))*365,0) / $bdgt_cy)*100,0)."%";
							} else {
								$str_perf_bdgt = '-NA-';
							}
				
							if ($py_full > 0) {
								$str_perf_pyf = round((round(($cyytd/date('z',strtotime($py_trade_date_to_process)))*365,0) / $py_full)*100,0)."%";
							} else {
								$str_perf_pyf = '-NA-';
							}


																					
							?>
              <tr class="<?=$class_row?>"> 
              	<td width="80">&nbsp;</td>
                <td width="308"><?=$clnt_names[$ccode]?></td>
                <td width="70" align="right"><?=number_format($cymtd,0,".",",")?></td>
                <td width="70" align="right"><?=number_format($pymtd,0,".",",")?></td>
                <td width="80" align="right"><?=$str_mtd_chng?></td>
                <td width="70" align="right"><?=number_format($cyytd,0,".",",")?></td>
                <td width="70" align="right"><?=number_format($pyytd,0,".",",")?></td>
                <td width="70" align="right"><?=number_format(($cyytd-$pyytd),0,".",",")?></td>
                <td width="70" align="right"><?=$str_ytd_chng?></td>
                <td width="70" align="right"><?=number_format(round(($cyytd/date('z',strtotime($py_trade_date_to_process)))*365,0),0,".",",")?></td>
                <td width="70" align="center"><?=number_format($bdgt_cy,0,".",",")?></td>
                <td width="70" align="center"><?=$str_perf_bdgt?></td>
                <td width="80" align="right"><?=number_format($py_full,0,".",",")?></td>
                <td width="80" align="center"><?=$str_perf_pyf?></td>
                <td width="80" align="right"><?=number_format($ppy_full,0,".",",")?></td>
              </tr>
							<?
              $level_a_count++;
			}

	?>
	</table></td></table>

<?
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
?>