<?
        //** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ 
        //** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ 
        //** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ 
				//INCLUDE FILE FOR CODE

				// Create Array of Tickers to show in table
				//THIS HAS BEEN CREATED/POPULATED ABOVE
				$arr_lookback = $arr_show_lookback_symbols;
				
				$arr_lookback_new = array_merge($arr_lookback,$arr_ioc); 
				
				asort($arr_lookback_new);
				show_array($arr_lookback_new);
				
				$arr_lookback = $arr_lookback_new;			
																
												
												//## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ##
												//## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ##
												if (count($arr_lookback) == 0) {
												
													$data_to_html_file  .= ' ';
												
												} else {

													$lkbk_t_0 = format_date_ymd_to_mdy($trade_date_to_process);
													$lkbk_t_1 = format_date_ymd_to_mdy(business_day_backward(strtotime($trade_date_to_process), 1));
													$lkbk_t_2 = format_date_ymd_to_mdy(business_day_backward(strtotime($trade_date_to_process), 2));
													$lkbk_t_3 = format_date_ymd_to_mdy(business_day_backward(strtotime($trade_date_to_process), 3));
													$lkbk_t_4 = format_date_ymd_to_mdy(business_day_backward(strtotime($trade_date_to_process), 4));
													
													$data_to_html_file .=	'
															<tr>
																<td>'.$trade_date_to_process.'</td>
																<td>&nbsp;</td>
																<td align="center">'.$lkbk_t_0.'</td>
																<td align="center">'.$lkbk_t_1.'</td>
																<td align="center">'.$lkbk_t_2.'</td>
																<td align="center">'.$lkbk_t_3.'</td>
																<td align="center">'.$lkbk_t_4.'</td>		
															</tr>';
											
											foreach($arr_lookback as $k=>$lkbk_symbol) {

													//Get values for each cell
													$lkbk_name = db_single_val("select description as single_val from sec_master where symbol = '".$lkbk_symbol."' limit 1");
													if (strlen(trim($lkbk_name)) == 0) {
														//get company name
														$lkbk_name = get_company_name($lkbk_symbol);  //FROM YAHOO FINANCE
														if (strlen(trim($lkbk_name)) == 0) {
															$lkbk_name = "";
														} else {
													  	$lkbk_name = "[".$lkbk_name."]";
														}
													} else {
													  $lkbk_name = "[".$lkbk_name."]";
													}
													
													
													//Initiation of coverage show in red [ioc]
													
													if (in_array($lkbk_symbol,$arr_ioc)) {
													 
													 $lkbk_name = $lkbk_name . " <font color=red>[ioc]</font>";
													
													} 
													
													
													
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													$show_0 = 0;													
													$qry_0 = "select distinct(trad_buy_sell) as lkbk_bs 
																		from mry_comm_rr_trades 
																		where trad_advisor_code = 'BUCK' 
																		and trad_symbol = '".$lkbk_symbol."'
																		and trad_trade_date = '".$trade_date_to_process."'
																		and trad_is_cancelled = 0";
													//xdebug("qry_0",$qry_0);
													$result_0 = mysql_query($qry_0) or die (tdw_mysql_error($qry_0));
													$count_0 = mysql_num_rows($result_0);
													$str_0 = "";
													if ($count_0 > 0) {
													  $show_0 = 1;													
														$str_0 = "BUCK (";
														while ( $row_0 = mysql_fetch_array($result_0) ) 
														{
															$str_0 .= $row_0["lkbk_bs"].", ";
														}
														$str_0 = substr($str_0,0,strlen($str_0)-2).")<br>";
													}
		
													$qry_0a = "select distinct(oth_buysell) as lkbk_bs_a 
																		from oth_other_trades 
																		where oth_symbol = '".$lkbk_symbol."'
																		and oth_trade_date = '".$trade_date_to_process."'
																		and trim(oth_broker) != 'BUCK'
																		and oth_isactive = 1";
													//xdebug("qry_0a",$qry_0a);
													$result_0a = mysql_query($qry_0a) or die (tdw_mysql_error($qry_0a));
													$count_0a = mysql_num_rows($result_0a);
													if ($count_0a > 0) {
													  $show_0 = 1;													
														$str_0 .= "BCM (";
														while ( $row_0a = mysql_fetch_array($result_0a) ) 
														{
															$str_0 .= $row_0a["lkbk_bs_a"].", ";
														}
														$str_0 = substr($str_0,0,strlen($str_0)-2).")";
													}

													if ($show_0 == 0) { $show_val_0 = "--"; } else { $show_val_0 = $str_0; }
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													$show_1 = 0;													
													$qry_1 = "select distinct(trad_buy_sell) as lkbk_bs 
																		from mry_comm_rr_trades 
																		where trad_advisor_code = 'BUCK' 
																		and trad_symbol = '".$lkbk_symbol."'
																		and trad_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 1)."'
																		and trad_is_cancelled = 0";
													//xdebug("qry_1",$qry_1);
													$result_1 = mysql_query($qry_1) or die (tdw_mysql_error($qry_1));
													$count_1 = mysql_num_rows($result_1);
													$str_1 = "";
													if ($count_1 > 0) {
													  $show_1 = 1;													
														$str_1 = "BUCK (";
														while ( $row_1 = mysql_fetch_array($result_1) ) 
														{
															$str_1 .= $row_1["lkbk_bs"].", ";
														}
														$str_1 = substr($str_1,0,strlen($str_1)-2).")<br>";
													}
		
													$qry_1a = "select distinct(oth_buysell) as lkbk_bs_a 
																		from oth_other_trades 
																		where oth_symbol = '".$lkbk_symbol."'
																		and oth_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 1)."'
																		and trim(oth_broker) != 'BUCK'
																		and oth_isactive = 1";
													//xdebug("qry_1a",$qry_1a);
													$result_1a = mysql_query($qry_1a) or die (tdw_mysql_error($qry_1a));
													$count_1a = mysql_num_rows($result_1a);
													if ($count_1a > 0) {
													  $show_1 = 1;													
														$str_1 .= "BCM (";
														while ( $row_1a = mysql_fetch_array($result_1a) ) 
														{
															$str_1 .= $row_1a["lkbk_bs_a"].", ";
														}
														$str_1 = substr($str_1,0,strlen($str_1)-2).")";
													}

													if ($show_1 == 0) { $show_val_1 = "--"; } else { $show_val_1 = $str_1; }
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													$show_2 = 0;													
													$qry_2 = "select distinct(trad_buy_sell) as lkbk_bs 
																		from mry_comm_rr_trades 
																		where trad_advisor_code = 'BUCK' 
																		and trad_symbol = '".$lkbk_symbol."'
																		and trad_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 2)."'
																		and trad_is_cancelled = 0";
													//xdebug("qry_2",$qry_2);
													$result_2 = mysql_query($qry_2) or die (tdw_mysql_error($qry_2));
													$count_2 = mysql_num_rows($result_2);
													$str_2 = "";
													if ($count_2 > 0) {
													  $show_2 = 1;													
														$str_2 = "BUCK (";
														while ( $row_2 = mysql_fetch_array($result_2) ) 
														{
															$str_2 .= $row_2["lkbk_bs"].", ";
														}
														$str_2 = substr($str_2,0,strlen($str_2)-2).")<br>";
													}
		
													$qry_2a = "select distinct(oth_buysell) as lkbk_bs_a 
																		from oth_other_trades 
																		where oth_symbol = '".$lkbk_symbol."'
																		and oth_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 2)."'
																		and trim(oth_broker) != 'BUCK'
																		and oth_isactive = 1";
													//xdebug("qry_2a",$qry_2a);
													$result_2a = mysql_query($qry_2a) or die (tdw_mysql_error($qry_2a));
													$count_2a = mysql_num_rows($result_2a);
													if ($count_2a > 0) {
													  $show_2 = 1;													
														$str_2 .= "BCM (";
														while ( $row_2a = mysql_fetch_array($result_2a) ) 
														{
															$str_2 .= $row_2a["lkbk_bs_a"].", ";
														}
														$str_2 = substr($str_2,0,strlen($str_2)-2).")";
													}

													if ($show_2 == 0) { $show_val_2 = "--"; } else { $show_val_2 = $str_2; }
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													$show_3 = 0;													
													$qry_3 = "select distinct(trad_buy_sell) as lkbk_bs 
																		from mry_comm_rr_trades 
																		where trad_advisor_code = 'BUCK' 
																		and trad_symbol = '".$lkbk_symbol."'
																		and trad_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 3)."'
																		and trad_is_cancelled = 0";
													//xdebug("qry_3",$qry_3);
													$result_3 = mysql_query($qry_3) or die (tdw_mysql_error($qry_3));
													$count_3 = mysql_num_rows($result_3);
													$str_3 = "";
													if ($count_3 > 0) {
													  $show_3 = 1;													
														$str_3 = "BUCK (";
														while ( $row_3 = mysql_fetch_array($result_3) ) 
														{
															$str_3 .= $row_3["lkbk_bs"].", ";
														}
														$str_3 = substr($str_3,0,strlen($str_3)-2).")<br>";
													}
		
													$qry_3a = "select distinct(oth_buysell) as lkbk_bs_a 
																		from oth_other_trades 
																		where oth_symbol = '".$lkbk_symbol."'
																		and oth_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 3)."'
																		and trim(oth_broker) != 'BUCK'
																		and oth_isactive = 1";
													//xdebug("qry_3a",$qry_3a);
													$result_3a = mysql_query($qry_3a) or die (tdw_mysql_error($qry_3a));
													$count_3a = mysql_num_rows($result_3a);
													if ($count_3a > 0) {
													  $show_3 = 1;													
														$str_3 .= "BCM (";
														while ( $row_3a = mysql_fetch_array($result_3a) ) 
														{
															$str_3 .= $row_3a["lkbk_bs_a"].", ";
														}
														$str_3 = substr($str_3,0,strlen($str_3)-2).")";
													}

													if ($show_3 == 0) { $show_val_3 = "--"; } else { $show_val_3 = $str_3; }
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													$show_4 = 0;													
													$qry_4 = "select distinct(trad_buy_sell) as lkbk_bs 
																		from mry_comm_rr_trades 
																		where trad_advisor_code = 'BUCK' 
																		and trad_symbol = '".$lkbk_symbol."'
																		and trad_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 4)."'
																		and trad_is_cancelled = 0";
													//xdebug("qry_4",$qry_4);
													$result_4 = mysql_query($qry_4) or die (tdw_mysql_error($qry_4));
													$count_4 = mysql_num_rows($result_4);
													$str_4 = "";
													if ($count_4 > 0) {
													  $show_4 = 1;													
														$str_4 = "BUCK (";
														while ( $row_4 = mysql_fetch_array($result_4) ) 
														{
															$str_4 .= $row_4["lkbk_bs"].", ";
														}
														$str_4 = substr($str_4,0,strlen($str_4)-2).")<br>";
													}
		
													$qry_4a = "select distinct(oth_buysell) as lkbk_bs_a 
																		from oth_other_trades 
																		where oth_symbol = '".$lkbk_symbol."'
																		and oth_trade_date = '".business_day_backward(strtotime($trade_date_to_process), 4)."'
																		and trim(oth_broker) != 'BUCK'
																		and oth_isactive = 1";
													//xdebug("qry_4a",$qry_4a);
													$result_4a = mysql_query($qry_4a) or die (tdw_mysql_error($qry_4a));
													$count_4a = mysql_num_rows($result_4a);
													if ($count_4a > 0) {
													  $show_4 = 1;													
														$str_4 .= "BCM (";
														while ( $row_4a = mysql_fetch_array($result_4a) ) 
														{
															$str_4 .= $row_4a["lkbk_bs_a"].", ";
														}
														$str_4 = substr($str_4,0,strlen($str_4)-2).")";
													}

													if ($show_4 == 0) { $show_val_4 = "--"; } else { $show_val_4 = $str_4; }
													//^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*^*
													$data_to_html_file .=	'
																						<tr>
																							<td>'.$trade_date_to_process.'</td>
																							<td>'.$lkbk_symbol.' '.$lkbk_name.'</td>
																							<td>'.format_buysellcovershort($show_val_0).'&nbsp;</td>
																							<td>'.format_buysellcovershort($show_val_1).'&nbsp;</td>
																							<td>'.format_buysellcovershort($show_val_2).'&nbsp;</td>
																							<td>'.format_buysellcovershort($show_val_3).'&nbsp;</td>
																							<td>'.format_buysellcovershort($show_val_4).'&nbsp;</td>		
																						</tr>';	
											}
					
							}
							//## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ##
							//## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ## -- ##

																			
											
        //** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ 
        //** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ 
        //** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ ** ++ 
				
			
?>
