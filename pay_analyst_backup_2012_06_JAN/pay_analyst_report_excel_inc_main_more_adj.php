			<?

			$arr_prev_qtr = gpq($sel_qtr, $sel_year);
			$arr_prev_quarter_brok_dates = get_quarter_dates ($arr_prev_qtr[0], $arr_prev_qtr[1]);
			$arr_prev_quarter_cal_dates = get_quarter_dates ($arr_prev_qtr[0], $arr_prev_qtr[1], "C");

			
			//*********************************************************************************************
			//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^						
			//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
			
			//get totals for each client
			$str_clients = implode(",",$arr_adj_clnts);
			$str_clients = str_replace(",",'","',$str_clients);
			$str_clients = '"'.$str_clients.'"';
			//xdebug("S",$str_clients);
			
				//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
				//PRIMARY REP DATA
				
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

			
			//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
			//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
			//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			?>