						 //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
						 $arr_data = explode("<###>",$value);
						 						 
						 //show_array($arr_data);
						 $str_x_symbol = $arr_data[0];
						 $str_x_date = $arr_data[1];
						 $str_x_rating = $arr_data[2];
						 $str_x_rating_previous = $arr_data[6];
						 $str_x_target = $arr_data[5];
						 
						 if (!in_array($arr_data[0], $ignore_more_mri)) {
						 
						 
									 if ($str_x_symbol == 'TIN') {
										echo "Processing ".$str_x_symbol."<br>";
									 }
									 
									 if (date('Y-m-d', strtotime($arr_data[1])) == $trade_date_to_process && $str_x_target !='N/A' && $str_x_target != 'NA') {
										 //get the next in line data if applicable
										 $arr_data_next = explode("<###>",$arr_mri[$key + 1]);
										 if ($str_x_symbol == $arr_data_next[0]) {
											 $str_x_symbol_next = $arr_data_next[0];
											 $str_x_date_next = $arr_data_next[1];
											 $str_x_rating_next = $arr_data_next[2];
											 $str_x_rating_previous_next = $arr_data_next[6];
											 $str_x_target_next = $arr_data_next[5];
										 } else {
											 $str_x_symbol_next = "";
											 $str_x_date_next = "";
											 $str_x_rating_next = "";
											 $str_x_rating_previous_next = "";
											 $str_x_target_next = "";
										 }
										 
										 //Condition 1: Rating Current and Previous are different
										 if ($str_x_rating != $str_x_rating_previous && trim($str_x_rating_previous) != "") {
												$arr_recent_mri[$str_x_symbol] = $str_x_date;
										 }
										 
										 //Condition 2: Target Current and Previous are different
										 if ((int)str_replace("$",'',$str_x_target) != (int)str_replace("$",'',$str_x_target_next) && trim($str_x_target_next) != "") {
												$arr_recent_mri[$str_x_symbol] = $str_x_date;
										 }
							 
							 }
							 //xdebug("Variables :",$str_x_symbol."//".$str_x_date."//".$str_x_rating."//".$str_x_rating_previous."//".$str_x_target);
							 //show_array($arr_data_next);
						 }
						 //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
