<?

			//put these values in temp db table
			$qry = "truncate table mry_tmp_process";
			$result_truncate = mysql_query($qry) or die (tdw_mysql_error($qry));
			
			foreach($arr_complete_sole as $k=>$v) {
				$qry = "insert into mry_tmp_process(val) values('".$v."')";
				$result_insert = mysql_query($qry) or die (tdw_mysql_error($qry));
			}

			$final_user_processed = array();
  		//CAPTURE SUBTOTAL ROW IN ARRAY TO GET GRAND TOTAL
			$arr_subtotal_row = array();

			//SECTION 1 START

			$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
										FROM Users a, mry_tmp_process b
										WHERE a.ID = b.val
										AND (a.Role = 3 or a.Role = 5)
										AND (a.ID != 288)
										ORDER BY Lastname, Role";
			$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));

			$count_row_xls = 3;

			while ($row_users = mysql_fetch_array($result_users) )  {
					//THIS SECTION TAKES THE MASTER ARRAY AND WRITES TO EXCEL
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if ($data[1] == $row_users["ID"]) {
							
									$final_user_processed[] = $row_users["ID"];
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, $row_users["lastname"]);

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			
			}


									//SECTION 1 SUBSECTION START

									$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
																FROM Users a, mry_tmp_process b
																WHERE a.ID = b.val
																AND (a.Role != 3 AND a.Role != 5 AND a.Role != 4)
																AND (a.ID != 288)
																ORDER BY Lastname, Role";
									$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));
												
									while ($row_users = mysql_fetch_array($result_users) )  {
											//THIS SECTION TAKES THE MASTER ARRAY AND WRITE TO EXCEL
											foreach($arr_master_new as $k=>$v) {
												$data = explode("^",$v);
													if ($data[1] == $row_users["ID"]) {
													
															$final_user_processed[] = $row_users["ID"];
															//show_array($data);
															$wks->writeString($count_row_xls, 2, $data[0]);
															$wks->write($count_row_xls, 3, $data[3],$format_data_3);
															$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
															
															if ($data[5] != 0) {
																$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
															}
															
															$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
															$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
															if ($data[8] > 0){
																$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
															}
															
															if ($data[9]){
																$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
															}
															
															$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																																		 "+".$arr_xl_cols[11].($count_row_xls+1).
																																		 "+".$arr_xl_cols[13].($count_row_xls+1).
																																		 "+".$arr_xl_cols[14].($count_row_xls+1).
																																		 "+".$arr_xl_cols[15].($count_row_xls+1).
																																		 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
															if ($data[11] != 0) {
																$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
															}
															$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
															$wks->writeString($count_row_xls, 26, $row_users["lastname"]);
						
															$count_row_xls = $count_row_xls + 1;
													}
								
											}			
									}

									
									//SECTION 1 SUBSECTION END

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5]."4".":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6]."4".":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8]."4".":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10]."4".":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11]."4".":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13]."4".":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19]."4".":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20]."4".":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21]."4".":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls = $count_row_xls;


			//SECTION 1 END
			
									
			//SECTION 2 START
			$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
										FROM Users a, mry_tmp_process b
										WHERE a.ID = b.val
										AND (a.Role = 4)
										AND (a.ID != 288)
										ORDER BY Lastname, Role";
			$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));

			while ($row_users = mysql_fetch_array($result_users) )  {
					//THIS SECTION TAKES THE MASTER ARRAY AND WRITE TO EXCEL
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if ($data[1] == $row_users["ID"]) {
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, $row_users["lastname"]);

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			
			}

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5].$hold_count_row_xls.":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6].$hold_count_row_xls.":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8].$hold_count_row_xls.":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10].$hold_count_row_xls.":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11].$hold_count_row_xls.":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13].$hold_count_row_xls.":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19].$hold_count_row_xls.":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20].$hold_count_row_xls.":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21].$hold_count_row_xls.":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls_new = $count_row_xls;

			//SECTION 2 END
			
			//SECTION 3 BEGIN
			$qry_users = "SELECT a.ID, a.lastname, a.Role, a.Fullname
										FROM Users a, mry_tmp_process b
										WHERE a.ID = b.val
										AND (a.ID = 288)
										ORDER BY Lastname, Role";
			$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));

			while ($row_users = mysql_fetch_array($result_users) )  {
					//THIS SECTION TAKES THE MASTER ARRAY AND WRITE TO EXCEL
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if ($data[1] == $row_users["ID"]) {
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, $row_users["lastname"]);

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			
			}

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5].$hold_count_row_xls_new.":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6].$hold_count_row_xls_new.":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8].$hold_count_row_xls_new.":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10].$hold_count_row_xls_new.":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11].$hold_count_row_xls_new.":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13].$hold_count_row_xls_new.":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19].$hold_count_row_xls_new.":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20].$hold_count_row_xls_new.":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21].$hold_count_row_xls_new.":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls_new_2 = $count_row_xls;
			
			//SECTION 3 END
			
			//SECTION 4 BEGIN
					foreach($arr_master_new as $k=>$v) {
						$data = explode("^",$v);
							if (substr($data[0],0,1) != '0') {
									//show_array($data);
									$wks->writeString($count_row_xls, 2, $data[0]);
									$wks->write($count_row_xls, 3, $data[3],$format_data_3);
									$wks->writeNumber($count_row_xls, 5, $data[4],$format_currency_1);
									
									if ($data[5] != 0) {
										$wks->writeNumber($count_row_xls, 6, $data[5], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 8, '='.$arr_xl_cols[5].($count_row_xls+1)."+".$arr_xl_cols[6].($count_row_xls+1) ,$format_currency_1);
									$wks->writeNumber($count_row_xls, 10, $data[6], $format_currency_1);
									if ($data[8] > 0){
										$wks->writeNumber($count_row_xls, 11, $data[8], $format_currency_1);
									}
									
									if ($data[9]){
										$wks->writeNumber($count_row_xls, 13, $data[9], $format_currency_1);
									}
									
									$wks->writeFormula($count_row_xls, 19, "=".$arr_xl_cols[10].($count_row_xls+1).
																												 "+".$arr_xl_cols[11].($count_row_xls+1).
																												 "+".$arr_xl_cols[13].($count_row_xls+1).
																												 "+".$arr_xl_cols[14].($count_row_xls+1).
																												 "+".$arr_xl_cols[15].($count_row_xls+1).
																												 "+".$arr_xl_cols[16].($count_row_xls+1), $format_currency_1);
									if ($data[11] != 0) {
										$wks->writeNumber($count_row_xls, 20, $data[11], $format_currency_1);
									}
									//$wks->writeFormula($count_row_xls, 21, "=".$arr_xl_cols[19].($count_row_xls+1)."+".$arr_xl_cols[20].($count_row_xls+1), $format_currency_1);
									$wks->writeString($count_row_xls, 26, "");

									$count_row_xls = $count_row_xls + 1;
							}
		
					}			

									//PUT SUBTOTAL
									$wks->writeFormula($count_row_xls, 5, "=sum(".$arr_xl_cols[5].$hold_count_row_xls_new_2.":".$arr_xl_cols[5].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 6, "=sum(".$arr_xl_cols[6].$hold_count_row_xls_new_2.":".$arr_xl_cols[6].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 8, "=sum(".$arr_xl_cols[8].$hold_count_row_xls_new_2.":".$arr_xl_cols[8].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 10, "=sum(".$arr_xl_cols[10].$hold_count_row_xls_new_2.":".$arr_xl_cols[10].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 11, "=sum(".$arr_xl_cols[11].$hold_count_row_xls_new_2.":".$arr_xl_cols[11].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 13, "=sum(".$arr_xl_cols[13].$hold_count_row_xls_new_2.":".$arr_xl_cols[13].($count_row_xls).")", $format_currency_2);
									$wks->writeFormula($count_row_xls, 19, "=sum(".$arr_xl_cols[19].$hold_count_row_xls_new_2.":".$arr_xl_cols[19].($count_row_xls).")", $format_currency_2);
									//$wks->writeFormula($count_row_xls, 20, "=sum(".$arr_xl_cols[20].$hold_count_row_xls_new_2.":".$arr_xl_cols[20].($count_row_xls).")", $format_currency_2);
									//$wks->writeFormula($count_row_xls, 21, "=sum(".$arr_xl_cols[21].$hold_count_row_xls_new_2.":".$arr_xl_cols[21].($count_row_xls).")", $format_currency_2);
									//CAPTURE ROW IN ARRAY
									$arr_subtotal_row[] = $count_row_xls;
									
									
									$count_row_xls = $count_row_xls + 2;
									$hold_count_row_xls_new_3 = $count_row_xls;
			
			//SECTION 4 END
			
			
			//GRAND TOTAL LINE
			
			$wks->write($hold_count_row_xls_new_3, 3, "Grand Total", $format_data_3);
			$wks->writeFormula($hold_count_row_xls_new_3, 5,  "=".$arr_xl_cols[5].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[5].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[5].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[5].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 6,  "=".$arr_xl_cols[6].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[6].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[6].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[6].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 8,  "=".$arr_xl_cols[8].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[8].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[8].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[8].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 10,  "=".$arr_xl_cols[10].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[10].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[10].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[10].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 11,  "=".$arr_xl_cols[11].($arr_subtotal_row[0]+1).

																												"+".$arr_xl_cols[11].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[11].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[11].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 13,  "=".$arr_xl_cols[13].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[13].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[13].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[13].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 19,  "=".$arr_xl_cols[19].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[19].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[19].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[19].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 20,  "=".$arr_xl_cols[20].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[20].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[20].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[20].($arr_subtotal_row[3]+1), $format_currency_2);
			$wks->writeFormula($hold_count_row_xls_new_3, 21,  "=".$arr_xl_cols[21].($arr_subtotal_row[0]+1).
																												"+".$arr_xl_cols[21].($arr_subtotal_row[1]+1).
																												"+".$arr_xl_cols[21].($arr_subtotal_row[2]+1).
																												"+".$arr_xl_cols[21].($arr_subtotal_row[3]+1), $format_currency_2);
																												
			
			////~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			// Put total shares as requested by Lloyd Karp 2008-12-FEB
			$qty_value = db_single_val("SELECT sum(trad_quantity) as single_val 
																	FROM mry_comm_rr_trades 
																	WHERE trad_is_cancelled = 0 
																	AND trad_settle_date between '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'");
			//go two rows dowm
			$hold_count_row_xls_new_3 = $hold_count_row_xls_new_3 + 2;
			$wks->write($hold_count_row_xls_new_3, 3, "Total Shares", $format_data_3);
			$wks->write($hold_count_row_xls_new_3, 5, number_format($qty_value,0,'',","), $format_data_3);
			//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

			$wks->printArea(0,0,$hold_count_row_xls_new_3+2,26);
			$wks->fitToPages(1,1);


//Adding a new worksheet for showing additional information:
			$wks_addinfo=& $wkb->addWorksheet("More Info");
			$wks_addinfo->setLandscape ();
			$wks_addinfo->setMarginLeft(0.4);
			$wks_addinfo->setMarginRight(0.4);
			$wks_addinfo->setMarginTop(0.5);
			$wks_addinfo->setMarginBottom(0.4);
			$wks_addinfo->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);
			
			$wks_addinfo->setPaper(5);
			
			$wks_addinfo->writeString(1, 2, "test");
			

// We still need to explicitly close the workbook
$wkb->close();
//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
	echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
<p class="ilt">Following is the preformatting for printing the Summary Report<br />
- LEGAL<br />
- Landscape<br />
- 1 Page Wide by 1 Page Tall 
<br />
Should you want to print in a format other than this, please use Page Setup in Excel to get the desired print output.<br /></p>
<a href="http://192.168.20.63/tdw/data/xls/<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br /><br />
<?
xdebug("Process ". $rnd_process_id . " completed at ",date('m/d/Y H:i:s a'));
//echo "RR^NAME^TOTALCOMM^TOTALCHECKS^STANDARDPAY^RATE^SPECIALPAY^ROLLING12MON"."<br>";
//show_array($arr_master);
//show_array($arr_sp_payout);
//show_array(sp_payout_rate_alt('AIMA', '', $arr_sp_payout));

?>