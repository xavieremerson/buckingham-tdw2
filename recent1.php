<?

	 foreach ($arr_recent_mri_symbols as $k=>$v) {

			//Get the subset
			$arr_subset = return_arr_mri ($v, $arr_mri);
			
			if ($v == 'TINX') {
				show_array($arr_subset);
			}
		 
		  foreach ($arr_subset as $kk=>$vv) {

				 $arr_data = explode("<###>",$vv);
				 //show_array($arr_data);
				 $str_x_symbol = $arr_data[0];
				 $str_x_date = $arr_data[1];
				 $str_x_rating = $arr_data[2];
				 $str_x_rating_previous = $arr_data[6];
				 $str_x_target = $arr_data[5];
				 
				 if (!in_array($arr_data[0], $ignore_more_mri)) {
				 
				 		//Check 1 (Rating and Previous Rating)
						if ($str_x_rating != $str_x_rating_previous && trim($str_x_rating_previous) != "" && date('Y-m-d', strtotime($str_x_date)) == $trade_date_to_process) {
							$arr_recent_mri[$str_x_symbol] = $str_x_date;
							$ignore_more_mri[$str_x_symbol] = $str_x_symbol;
						}
						
				 }
			}

			//Check 2 (Target)
			if (!in_array($v, $ignore_more_mri)) {
				$arr_targets = array();
				$hold_current_target = "";
				foreach ($arr_subset as $nk=>$nv) {
				  $arr_data_n = explode("<###>",$nv);
					$str_x_target = $arr_data_n[5];
					if ($arr_data_n[5] != 'N/A' && $arr_data_n[5] != 'NA' && $arr_data_n[5] != 'NM' && trim($arr_data_n[5]) != '') {
						$arr_targets[$arr_data_n[1]] = $str_x_target;
					}
					if (date('Y-m-d', strtotime($arr_data_n[1])) == $trade_date_to_process) {
						if ($arr_data_n[5] != 'N/A' && $arr_data_n[5] != 'NA' && $arr_data_n[5] != 'NM' && trim($arr_data_n[5]) != '') {
							$hold_current_target = $arr_data_n[5];
							xdebug("hold_current_target",$hold_current_target);
						}
					}
				}
			}
			
			for ($i=0;$i<count($arr_targets);$i++) {

					//if ((int)str_replace("$",'',$hold_current_target) != (int)str_replace("$",'',$arr_targets[$i]) && trim($hold_current_target) != "") {
//						$arr_recent_mri[$str_x_symbol] = $str_x_date;
//					}
			
			}

			show_array($arr_targets);

		 
		 
		 
	 }
 


?>