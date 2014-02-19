<?
 
ini_set('max_execution_time', 7200);
ini_set('memory_limit','256M');
ini_set("display_errors", 0); 
 
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

	include('sproc_alert_bcm_vol_pct_func.php');  
	
	function lastdayofmonth($month = '', $year = '') {
   if (empty($month)) {
      $month = date('m');
   }
   if (empty($year)) {
      $year = date('Y');
   }
   $result = strtotime("{$year}-{$month}-01");
   $result = strtotime('-1 second', strtotime('+1 month', $result));
   return date('Y-m-d', $result);
	}

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	//GET DATES : If passed as GET then parse/create, else get previous month.
	if ($sel_month) {
		$arr_dt = explode("-",$sel_month);
		$startdate = date('Y-m-d', strtotime("01-".$sel_month)); //'2011-09-08';
		$enddate = lastdayofmonth($arr_dt[0],$arr_dt[1]); //'2011-09-30'; 
		$str_report_header = date('F \'Y', strtotime("01-".$sel_month));
	} else {
		//Get Dates of the previous month.
		$startdate = date("Y-m-01", strtotime("-1 month") ) ;
		$enddate = date("Y-m-t", strtotime("-1 month") ) ;
		$str_report_header = date('F \'Y', strtotime("-1 month") ) ;
	}

	//Get all symbols for the day.
	$query = "SELECT distinct(oth_symbol) as symbols FROM `oth_other_trades` 
						where oth_trade_date between '".$startdate."' and '".$enddate."' 
						order by oth_symbol";

	$result = mysql_query($query) or die(tdw_mysql_error($query));
	$val_rows = mysql_num_rows($result);
	if ($val_rows > 0) {
		$arr_symbols = array();
		while($row = mysql_fetch_array($result)) {
			if (strlen(trim($row["symbols"])) < 7 && !strpos(trim($row["symbols"])," ")) { 
				$arr_symbols[] = strtoupper(trim($row["symbols"])); 
			}
		}
	}

	//process each symbol perform lookup and processing
	$count_result = 0;
	$arr_master = array();
	foreach ($arr_symbols as $k=>$valsymbol) {
		
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		//create percentages for price
		$arr_prices = hist_prices($valsymbol, $startdate, $enddate);
		$arr_dates = array();
		$arr_vals = array();
		foreach($arr_prices as $k=>$v) {
			$arr_dates[] = $k;
			$arr_vals[] = $v;
		}

		$arr_price_percent = array();
		for($i=0; $i<count($arr_prices)-1; $i++) {
			$arr_price_percent[$arr_dates[$i]] = round( (($arr_vals[$i] - $arr_vals[$i+1])/$arr_vals[$i])*100 , 2); //abs ();
		}
	
		$price_criteria = db_single_val("select bct_value as single_val from bcm_trend_config where bct_type = 'price' and bct_isactive = 1");

		$arr_price_percent_filtered = array();
		$arr_price_percent_filtered_dates = array();
		foreach($arr_price_percent as $k=>$v) {
			 if ( abs($v) > abs($price_criteria) ) {
					$arr_price_percent_filtered[$k] = $v;
					$arr_price_percent_filtered_dates[] = $k;
			 }
		}

			$str_dates = " ('".implode("','",$arr_price_percent_filtered_dates)."') ";

			$query = "SELECT oth_symbol, oth_trade_date, sum(oth_quantity) as oth_quantity,
								oth_buysell, avg(oth_price) as oth_price 
								FROM `oth_other_trades` 
								where oth_symbol = '".$valsymbol."' 
								and oth_trade_date in ".$str_dates."  
								group by  oth_trade_date, oth_buysell
								order by oth_trade_date desc";

			$result = mysql_query($query) or die(tdw_mysql_error($query));
	
			$val_rows = mysql_num_rows($result);
			
			if ($val_rows > 0) { //<a class="ilt">SYMBOL: </a>$valsymbol<br>

					while($row = mysql_fetch_array($result)) {

					$count_result++;
          $arr_master[$count_result."^".$valsymbol."^"."P"] = format_date_ymd_to_mdy($row["oth_trade_date"])."^".
																								              $valsymbol."^".
																															number_format($row["oth_quantity"],0,"",",")."^".
																															$row["oth_buysell"]."^".
																															$row["oth_price"]."^".
																															$arr_price_percent_filtered[$row["oth_trade_date"]]."^".
																															$price_criteria;
					}

			} else {
				$var_dummy = 1;
				//<!--<a class="ilt">CRITERIA: PRICE CHANGE % ABOVE MONITORING THRESHOLD <font color="red">[No trades meet the criteria]</font></a><br>-->
			}

			$volume_criteria = db_single_val("select bct_value as single_val from bcm_trend_config where bct_type = 'volume' and bct_isactive = 1");
			//xdebug("volume_criteria",$volume_criteria);
		
			//create volumes
			$arr_volumes = hist_volume($valsymbol, $startdate, $enddate);
			$arr_dates = array();
			$arr_vals = array();
			foreach($arr_volumes as $k=>$v) {
				$arr_dates[] = $k;
				$arr_vals[] = $v;
			}			

				$query = "SELECT oth_symbol, oth_trade_date, sum(oth_quantity) as oth_quantity,
												oth_buysell, avg(oth_price) as oth_price 
												FROM `oth_other_trades` 
												where oth_symbol = '".$valsymbol."' 
												and oth_trade_date between '".$startdate."' and '".$enddate."'  
												group by  oth_trade_date, oth_buysell
												order by oth_trade_date desc";				
				
				$result = mysql_query($query) or die(tdw_mysql_error($query));

				$val_rows = mysql_num_rows($result);
				if ($val_rows > 0) {
							$arr_string_data = array();
							while($row = mysql_fetch_array($result)) {
								if (round(( $row["oth_quantity"]/$arr_volumes[$row["oth_trade_date"]] )*100,2) > $volume_criteria) {
									$arr_string_data[] = 	format_date_ymd_to_mdy($row["oth_trade_date"]) ."^". 
									                      $valsymbol ."^". 
																				number_format($row["oth_quantity"],0,"",",") ."^". 
																				$row["oth_buysell"] ."^". 
																				$row["oth_price"] ."^". 
																				round(( $row["oth_quantity"]/$arr_volumes[$row["oth_trade_date"]] )*100,2) ."^".
																				$volume_criteria ."^";
								}
							}
				
							if (count($arr_string_data)>0) {
							
              		foreach($arr_string_data as $k=>$v) {
										$arrout = explode("^",$v);
										$count_result++;
                    $arr_master[$count_result."^".$valsymbol."^"."V"] = $arrout[0]."^".$arrout[1]."^".$arrout[2]."^".$arrout[3]."^".$arrout[4]."^".$arrout[5]."^".$arrout[6];
									}
							}
				} else {
					$var_dummy = 1;
					//<!--<a class="ilt">CRITERIA: % OF TOTAL VOLUME ABOVE MONITORING THRESHOLD <font color="red">[No trades meet the criteria]</font></a><br>-->
				}
		
				//<!-- NEWS SECTION -->
				
			$qry_news = "select * from news_events 
								 where news_date between '".$startdate."' and '".$enddate."'
								 and news_symbol = '".$valsymbol."'
								 order by auto_id";
			$result_news = mysql_query($qry_news) or die(tdw_mysql_error($qry_news));
			$arr_ndates = array();
			$arr_news = array();
			//with the arrays above it will hold only one piece of news for the date.
			while($row = mysql_fetch_array($result_news)) {
				$arr_ndates[] = $row["news_date"];
				$arr_nnews[$row["news_date"]] = $row["news_notes"];
			}
			
			$valid_dates = " ('" . implode("','", $arr_ndates) . "') ";

			$query = "SELECT oth_symbol, oth_trade_date, sum(oth_quantity) as oth_quantity,
								oth_buysell, avg(oth_price) as oth_price 
								FROM `oth_other_trades` 
								where oth_symbol = '".$valsymbol."' 
								and oth_trade_date in " . $valid_dates . "  
								group by  oth_trade_date, oth_buysell
								order by oth_trade_date desc";
											 
				$result = mysql_query($query) or die(tdw_mysql_error($query));
		
				$val_rows = mysql_num_rows($result);
				if ($val_rows > 0) {

					while($row = mysql_fetch_array($result)) {

						$count_result++;
						$arr_master[$count_result."^".$valsymbol."^"."N"] =  format_date_ymd_to_mdy($row["oth_trade_date"])."^".$valsymbol."^".
															                                   number_format($row["oth_quantity"],0,"",",")."^".$row["oth_buysell"]."^".$row["oth_price"]."^".nl2br($arr_nnews[$row["oth_trade_date"]]);
					}

				} else {
					$var_dummy = 1;
					//<a class="ilt">NEWS / EVENTS IN THE SELECTED TIME FRAME <font color="red">[NONE]</font></a><br>
				}
			
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	}


	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

  //print_r($arr_master);
	
/*	105^SCS^P = [09/22/2011^SCS^398,800^Sell^5.530000^-24.95^6]
  106^SCS^V = [09/22/2011^SCS^398,800^Sell^5.530000^7.29^5]
  107^SCS^V = [09/20/2011^SCS^40,000^Sell^7.260000^5.11^5]
  108^SCS^N = [09/22/2011^SCS^398,800^Sell^5.530000^Steelcase Inc.'s (NYSE:SCS) shares fell 18% a day after the office-furniture maker reported a second-quarter profit below analysts' consensus projection. ]*/

	//first get unique symbols from array
	$arr_u_symbol = array();
	foreach ($arr_master as $k=>$v) {
		$arr_symbol = explode("^",$k);
		$arr_u_symbol[$arr_symbol[1]] = 1;
	}
	
	//show_array($arr_u_symbol);
	
	$arr_u_symbol_P = array();
	$arr_u_symbol_V = array();
	$arr_u_symbol_N = array();
	
	$arr_new_u_symbol = array();
	
	foreach ($arr_u_symbol as $k=>$v) {
		foreach ($arr_master as $str_k=>$str_v) {
			$arr_index_k = explode("^",$str_k);
			$arr_index_v = explode("^",$str_v);
			
			if ($arr_index_k[1] == $k) {
					if ($arr_index_k[2] == 'P') {
						if ( ( ($arr_index_v[3] == 'Sell' || $arr_index_v[3] == 'Short') && $arr_index_v[5] < 0) || ( ($arr_index_v[3] == 'Buy' || $arr_index_v[3] == 'Cover') && $arr_index_v[5] > 0)) {
							$arr_u_symbol_P[$k] = $arr_u_symbol_P[$k] + 1;
							$arr_new_u_symbol[$k] = $k;
						}
					}
									
					if ($arr_index_k[2] == 'V') {
							$arr_u_symbol_V[$k] = $arr_u_symbol_V[$k] + 1;
							$arr_new_u_symbol[$k] = $k;
					}
									
					if ($arr_index_k[2] == 'N') {
							$arr_u_symbol_N[$k] = $arr_u_symbol_N[$k] + 1;
							$arr_new_u_symbol[$k] = $k;
					}
			}
		}
	}


//echo $exportlocation;
//exit;

			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$output_filename = "monthly_price_volume_summary.xls";
			$fp = fopen($exportlocation.$output_filename, "w");
			$str_xl = '';
			$str_xl .= '<table style="border-bottom-style:inset; border-bottom-width:thin" border="1" cellspacing="0" cellpadding="2">
					<tr>
							<td colspan="5"><strong>Monthly Price & Volume Review ' . $str_report_header . '</strong></td>
					</tr>
					<tr style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#000099">
							<td><strong>Symbol</strong></td>
							<td><strong>Company Name</strong></td>
							<td><strong>Price Event</strong></td>
							<td><strong>Volume Event</strong></td>
							<td><strong>News Event</strong></td>
					</tr>';
				
			foreach ($arr_new_u_symbol as $k=>$v) {
					$str_xl .= '<tr class="ilt">
																<td>'.$k.'</td>
																<td>'.get_company_name($k).'</td>
																<td align="center">'.$arr_u_symbol_P[$k].'&nbsp;</td>
																<td align="center">'.$arr_u_symbol_V[$k].'&nbsp;</td>
																<td align="center">'.$arr_u_symbol_N[$k].'&nbsp;</td>
														</tr>';
			}	
		
					$str_xl .= '</table>
						</body>
					</html>';
			fputs ($fp, $str_xl);
			fclose($fp);
			
			//Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$output_filename = "monthly_price_volume_detail.xls";
			$fp = fopen($exportlocation.$output_filename, "w");
			$str_xl = '<html xmlns="http://www.w3.org/1999/xhtml">
									<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
									<body>';
			$str_xl .= '<table style="border-bottom-style:inset; border-bottom-width:thin" border="1" cellspacing="0" cellpadding="2">
					<tr>
							<td colspan="5"><strong>Monthly Price & Volume Review ' . $str_report_header . '</strong></td>
					</tr>
					<tr style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#000099">
							<td><strong>Symbol</strong></td>
							<td><strong>Criteria</strong></td>
							<td><strong>Date</strong></td>
							<td><strong>Company Name</strong></td>
							<td><strong>Quantity</strong></td>
							<td><strong>Buy/Sell</strong></td>
							<td><strong>Price</strong></td>
							<td><strong>% change</strong></td>
							<td><strong>% change Criteria</strong></td>
							<td><strong>News/Event</strong></td>
					</tr>';

			foreach ($arr_master as $str_k=>$str_v) {
				$arr_index_k = explode("^",$str_k);
				$arr_index_v = explode("^",$str_v);
				
			/*	105^SCS^P = [09/22/2011^SCS^398,800^Sell^5.530000^-24.95^6]
				106^SCS^V = [09/22/2011^SCS^398,800^Sell^5.530000^7.29^5]
				107^SCS^V = [09/20/2011^SCS^40,000^Sell^7.260000^5.11^5]
				108^SCS^N = [09/22/2011^SCS^398,800^Sell^5.530000^Steelcase Inc.'s (NYSE:SCS) shares fell 18% a day after the office-furniture maker reported a second-quarter profit below analysts' consensus projection. ]*/

						if ($arr_index_k[2] == 'P') {
							if ( ( ($arr_index_v[3] == 'Sell' || $arr_index_v[3] == 'Short') && $arr_index_v[5] < 0) || ( ($arr_index_v[3] == 'Buy' || $arr_index_v[3] == 'Cover') && $arr_index_v[5] > 0)) {
								$str_xl .= '<tr class="ilt">
																			<td>'.$arr_index_k[1].'</td>
																			<td>Price</td>
																			<td>'.$arr_index_v[0].'</td>
																			<td>'.get_company_name($arr_index_k[1]).'</td>
																			<td>'.$arr_index_v[2].'</td>
																			<td>'.$arr_index_v[3].'</td>
																			<td>'.$arr_index_v[4].'</td>
																			<td>'.$arr_index_v[5].'%</td>
																			<td>'.$arr_index_v[6].'%</td>
																			<td>&nbsp;</td>
																	</tr>';
								//$arr_u_symbol_P[$k] = $arr_u_symbol_P[$k] + 1;
								//$arr_new_u_symbol[$k] = $k;
							}
						}
										
						if ($arr_index_k[2] == 'V') {
								$str_xl .= '<tr class="ilt">
																			<td>'.$arr_index_k[1].'</td>
																			<td>Volume</td>
																			<td>'.$arr_index_v[0].'</td>
																			<td>'.get_company_name($arr_index_k[1]).'</td>
																			<td>'.$arr_index_v[2].'</td>
																			<td>'.$arr_index_v[3].'</td>
																			<td>'.$arr_index_v[4].'</td>
																			<td>'.$arr_index_v[5].'%</td>
																			<td>'.$arr_index_v[6].'%</td>
																			<td>&nbsp;</td>
																	</tr>';
								//$arr_u_symbol_V[$k] = $arr_u_symbol_V[$k] + 1;
								//$arr_new_u_symbol[$k] = $k;
						}
										
						if ($arr_index_k[2] == 'N') {
								$str_xl .= '<tr class="ilt">
																			<td>'.$arr_index_k[1].'</td>
																			<td>News/Event</td>
																			<td>'.$arr_index_v[0].'</td>
																			<td>'.get_company_name($arr_index_k[1]).'</td>
																			<td>'.$arr_index_v[2].'</td>
																			<td>'.$arr_index_v[3].'</td>
																			<td>'.$arr_index_v[4].'</td>
																			<td>&nbsp;</td>
																			<td>&nbsp;</td>
																			<td>'.$arr_index_v[5].'</td>
																	</tr>';
								//$arr_u_symbol_N[$k] = $arr_u_symbol_N[$k] + 1;
								//$arr_new_u_symbol[$k] = $k;
						}

			}
					$str_xl .= '</table>
						</body>
					</html>';
			fputs ($fp, $str_xl);
			fclose($fp);
			
			//Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);

			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Finally format and outout the data
			$str_out_html = "";
			$str_out_html .= '<table style="border-bottom-style:inset; border-bottom-width:thin" border="1" cellspacing="0" cellpadding="2">
					<tr>
							<td colspan="5"><strong>Monthly Price & Volume Review ' . $str_report_header . '</strong></td>
					</tr>
					<tr style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#000099">
							<td><strong>Symbol</strong></td>
							<td><strong>Company Name</strong></td>
							<td><strong>Price Event</strong></td>
							<td><strong>Volume Event</strong></td>
							<td><strong>News Event</strong></td>
					</tr>';
				
			foreach ($arr_new_u_symbol as $k=>$v) {
					$str_out_html .= '<tr style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#000000">
																<td>'.$k.'</td>
																<td>'.get_company_name($k).'</td>
																<td align="center">'.$arr_u_symbol_P[$k].'&nbsp;</td>
																<td align="center">'.$arr_u_symbol_V[$k].'&nbsp;</td>
																<td align="center">'.$arr_u_symbol_N[$k].'&nbsp;</td>
														</tr>';
			}	
		
					$str_out_html .= '</table>';
			
			echo $str_out_html;
			//exit; 
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	
	//exit;

		//444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444
		$arr_recipient = array();
		
		$arr_recipient[] = 'pprasad@centersys.com';
		
		$arr_recipient[] = 'compliance@buckresearch.com';
		
		//$arr_recipient[] = 'lkarp@buckresearch.com';
		//$arr_recipient[] = 'jperno@buckresearch.com';
		//$arr_recipient[] = 'rdaniels@buckresearch.com';
		//$arr_recipient[] = 'ehogenboom@buckresearch.com';
		
		$email_log = '
							<table width="100%" border="0" cellspacing="0" cellpadding="10">
								<tr> 
									<td valign="top">
										<p>'.$str_out_html.'</p>			
										<p>&nbsp;</p>
										<p>&nbsp;</p>
										<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p>
									</td>
								</tr>
							</table>
								';
		//create mail to send
		$html_body = "";
		$html_body .= zSysMailHeader("");
		$html_body .= $email_log;
		$html_body .= zSysMailFooter ();
		
		$subject = "Monthly BCM Price/Volume Alert : (".$str_report_header.")";
		$text_body = $subject;
		
		$arr_attachment["monthly_price_volume_summary.xls"] = $exportlocation."monthly_price_volume_summary.xls";
		$arr_attachment["monthly_price_volume_detail.xls"] = $exportlocation."monthly_price_volume_detail.xls";
		
		foreach ($arr_recipient as $key => $emailval) {
						
						zSysMailer($emailval, "", $subject, $html_body, $text_body, $arr_attachment) ;  
						echo "Mail sent to ". $emailval . "<br>";
		}
		//444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444444
	
?>
