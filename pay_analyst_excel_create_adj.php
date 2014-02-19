<?

function gpq ($q, $y) { //get previous quarter

	if ($q == 1) { $ret_q = 4; $ret_y = $y - 1;
	} else if ($q == 2) {		$ret_q = 1; $ret_y = $y;
	} else if ($q == 3) {		$ret_q = 2; $ret_y = $y;
	} else if ($q == 4) {		$ret_q = 3;	$ret_y = $y;
	} else {		$ret_q = 0; $ret_y = 0;
	}
  $arr_return = array($ret_q,$ret_y);
	return $arr_return;
}


















	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//================================================================================================
	//Adjustments data
	$arr_adj = array();
	$arr_adj_clnts = array();
	$arr_adj_clnts_processed = array();
	$arr_adj_string = array();
	$arr_prev_qtr = gpq($sel_qtr,$sel_year);
	$qry_get_adj = "select * from pay_analyst_allocations_adj 
										where pay_qtr = '".$arr_prev_qtr[0]."'
										and pay_year = '".$arr_prev_qtr[1]."'
										and pay_sales_id = '".$user_sales."'
										and pay_isactive = 1
										order by pay_advisor_code, pay_percent";
	//echo $qry_get_adj;
	$result_get_adj = mysql_query($qry_get_adj) or die (tdw_mysql_error($qry_get_adj));
	while ( $row_get_adj = mysql_fetch_array($result_get_adj) ) 
	{
		$arr_adj[$row_get_adj["pay_analyst_id"]][$row_get_adj["pay_advisor_code"]] = $row_get_adj["pay_percent"]; 
		$arr_adj_clnts[$row_get_adj["pay_advisor_code"]] = $row_get_adj["pay_advisor_code"];
		$arr_adj_string[] = $row_get_adj["pay_advisor_code"]."^".$row_get_adj["pay_percent"]."^".$row_get_adj["pay_analyst_id"];
	}
	//show_array($arr_adj);
	//show_array($arr_adj_clnts);
	if (count($arr_adj_clnts) > 0) {
			//================================================================================================================
			//================================================================================================================
			$arr_prev_quarter_brok_dates = get_quarter_dates ($arr_prev_qtr[0], $arr_prev_qtr[1]);
			$arr_prev_quarter_cal_dates = get_quarter_dates ($arr_prev_qtr[0], $arr_prev_qtr[1], "C");

			//get totals for each client
			$str_clients = implode(",",$arr_adj_clnts);
			$str_clients = str_replace(",",'","',$str_clients);
			$str_clients = '"'.$str_clients.'"';
			//xdebug("S",$str_clients);
			
				//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@				
				//COMMISSION
				$arr_adj_comm_for_rr_comm = array();
				$qry_comm_for_rr = "SELECT trad_advisor_code, sum(trad_commission) as commission 
														FROM mry_comm_rr_trades 
														WHERE trad_trade_date between '".$arr_prev_quarter_brok_dates[0]."' AND '".$arr_prev_quarter_brok_dates[1]."'
															and trad_is_cancelled = 0
															and trad_advisor_code in (".$str_clients.")
														GROUP by trad_advisor_code";
				//xdebug("Q",$qry_comm_for_rr);
				$result_comm_for_rr = mysql_query($qry_comm_for_rr) or die (tdw_mysql_error($qry_comm_for_rr));
				while ( $row_comm_for_rr = mysql_fetch_array($result_comm_for_rr) ) 
				{
					$arr_adj_comm_for_rr_comm[$row_comm_for_rr["trad_advisor_code"]] = $row_comm_for_rr["commission"];
				}
				//$arr_comm_for_rr_comm["INTR"] = 1;	
				
				//CHECKS
				$arr_adj_comm_for_rr_chek = array();
				$qry_comm_for_rr = "SELECT chek_advisor, sum(chek_amount) as commission  
														FROM chk_chek_payments_etc 
														WHERE chek_date between '".$arr_prev_quarter_cal_dates[0]."' AND '".$arr_prev_quarter_cal_dates[1]."' 
															AND chek_isactive = 1
															AND chek_advisor in (".$str_clients.")
														GROUP BY chek_advisor";
				$result_comm_for_rr = mysql_query($qry_comm_for_rr) or die (tdw_mysql_error($qry_comm_for_rr));
				while ( $row_comm_for_rr = mysql_fetch_array($result_comm_for_rr) ) 
				{
					$arr_adj_comm_for_rr_chek[$row_comm_for_rr["chek_advisor"]] = $row_comm_for_rr["commission"];
				}
				
				//incorporate checks into comm array
				$arr_adj_composite_primary = array();
				$arr_adj_tmp_processed = array();
				foreach ($arr_adj_comm_for_rr_comm as $code=>$comm) {
					if (array_key_exists($code, $arr_adj_comm_for_rr_chek)) {
						$arr_adj_composite_primary[$code] = $arr_comm_for_rr_chek[$code] + $comm;
						$arr_adj_tmp_processed[] = $code;
					} else {
						$arr_adj_composite_primary[$code] = $comm;
					} 
				}
				
				foreach ($arr_adj_comm_for_rr_chek as $code=>$comm) {
					if (!in_array($code, $arr_adj_tmp_processed)) {
						$arr_adj_composite_primary[$code] = $comm;
					} 
				}
				
				//show_array($arr_adj_composite_primary);
				
				$arr_adj_master_composite = array();
        $arr_adj_master_composite = $arr_adj_composite_primary;
			
			  //show_array($arr_adj_master_composite);
			//================================================================================================================
			//================================================================================================================
	}
	//================================================================================================
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


if (count($arr_adj_clnts) > 0) {

	$row_adj = $gsre+3;

  $wks->setMerge ($row_adj, 0, $row_adj, 8);
	$wks->write($row_adj, 0, "Adjustments to Q".$arr_prev_qtr[0]. " ".$arr_prev_qtr[1], $bold10right);
	$row_adj = $row_adj + 1;
	//show_array($arr_adj_clnts);
	//show_array($arr_adj);
  //show_array($arr_adj_string);
	
		foreach($arr_adj_clnts as $ck=>$cv) {
		 
		 		foreach($arr_adj_string as $dk=>$dv) {
					$arr_vals = array();
					$arr_vals = explode("^", $dv); //[SANO^50.00^210]
					if ($arr_vals[0] == $cv) {
						if ($arr_vals[1] < 0) {
						  $str_val = "($" . number_format($arr_adj_master_composite[$cv]*$arr_vals[1]*(-0.01),2,".",",") . ")";
							$wks->setMerge ($row_adj, 0, $row_adj, 8);
							$wks->write($row_adj, 0, str_pad(trim(look_up_client($cv)).":",24," ") . str_pad(get_user_by_id ($arr_vals[2]).":",24," ") . $str_val, $format_data_courier);
							$row_adj = $row_adj + 1;
						} else {
						  $str_val = "$" . number_format($arr_adj_master_composite[$cv]*$arr_vals[1]*(0.01),2,".",",");
							$wks->setMerge ($row_adj, 0, $row_adj, 8);
							$wks->write($row_adj, 0, str_pad(trim(look_up_client($cv)).":",24," ") . str_pad(get_user_by_id ($arr_vals[2]).":",24," ") . $str_val, $format_data_courier);
							$row_adj = $row_adj + 1;
						}
					}
				}
		}
}

//exit;


?>