<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');
	
	
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

$trade_date_to_process = previous_business_day();
$previous_year_date = get_date_previous_year($trade_date_to_process);

//print_r($_GET);
//exit;

//[thiscriteria] => filter_tier [valcriteria] => 4
//[thiscriteria] => filter_name [valcriteria] => A
//[thiscriteria] => filter_reps [valcriteria] => AF
//[thiscriteria] => filter_trdr [valcriteria] => TS 
//[thiscriteria] => filter_type [valcriteria] => AP 


if ($_GET && !$proc_user) {

	$process_tier = 0;

//--------------------------------------------------------------
	if ($thiscriteria == 'filter_tier') {
		$process_tier = 1;
	}
//--------------------------------------------------------------
		$str_show_deleted = " clnt_isactive != '0' ";
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_name') {
		$strltr = $valcriteria;	
	} else {
		$strltr = "";	
	}
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_reps') {
		$strrep = " and (clnt_rr1='".$valcriteria."' OR clnt_rr2='".$valcriteria."') ";	
	} else {
		$strrep = " ";	
	}
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_trdr') {
		$strtrdr = " and clnt_trader ='".$valcriteria."' ";	
	} else {
		$strtrdr = " ";	
	}
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_type') {
		if ($valcriteria == 'AP') {
			$strtype = " and clnt_status like 'P%' ";	
		} else if ($valcriteria == 'NP') {
			$strtype = " and clnt_status like 'X%' ";	
		} else {
			$strtype = " and clnt_status ='".$valcriteria."' ";	
		}
	} else {
		$strtype = " ";	
	}
//--------------------------------------------------------------
}


//rep filter criteria
$str_rep_filter_criteria = " AND ( (clnt_rr1 = '".$user_initials."' OR clnt_rr2 = '".$user_initials."') OR (clnt_rr1 = '' AND clnt_rr2 = '' AND clnt_status like 'P%')) ";


//[thiscriteria] => filter_name [valcriteria] => E


if ($req_ajax) {
	$query_clients = "SELECT * from int_clnt_clients where ".$str_show_deleted." and clnt_name like '".$strltr."%' ". $strrep . $strtrdr. $strtype.$str_rep_filter_criteria." order by clnt_name";
	$query_money = "SELECT clnt_code from int_clnt_clients where ".$str_show_deleted." and clnt_name like '".$strltr."%' ". $strrep . $strtrdr. $strtype.$str_rep_filter_criteria. " order by clnt_name";
} else {
	$query_clients = "SELECT * from int_clnt_clients where clnt_isactive != 0 ".$str_rep_filter_criteria." order by clnt_name LIMIT 100";
	$query_money = "SELECT clnt_code from int_clnt_clients where clnt_isactive != 0 ".$str_rep_filter_criteria." order by clnt_name LIMIT 100";
}


	$arr_subset_clients = array();
	$result_subset_clients = mysql_query($query_money) or die (tdw_mysql_error($query_money));
	while ( $row = mysql_fetch_array($result_subset_clients) ) {
	  $arr_subset_clients[] = $row["clnt_code"];
	}
	$str_subset_clients = implode(",",$arr_subset_clients);
	$str_subset_clients = "'".str_replace(",","','",$str_subset_clients)."'";



//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================

//now mtd comm
//get the start day of month for this
$global_qry_date_start_mtd = db_single_val("SELECT brk_start_date as single_val 
																			FROM `brk_brokerage_months` 
																			WHERE `brk_start_date` <= '".$trade_date_to_process."'
																			AND `brk_end_date` >= '".$trade_date_to_process."'");
//xdebug("global_qry_date_start_mtd",$global_qry_date_start_mtd);

$qry_mtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_advisor_code in (".$str_subset_clients.")
								 GROUP BY trad_advisor_code";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{
	$arr_mtd_comm[$row_mtd_comm["trad_advisor_code"]] = $row_mtd_comm["trad_comm"];
}
//show_array($arr_mtd_comm);

//get qtd values
//get quarter start date
$arr_qtr_start = array(1=>'Jan',2=>'Apr',3=>'Jul',4=>'Oct');

$arr_month_in_qtr = array('01'=>1,'02'=>1,'03'=>1,'04'=>2,'05'=>2,'06'=>2,'07'=>3,'08'=>3,'09'=>3,'10'=>4,'11'=>4,'12'=>4);

$qtr_start_val = $arr_qtr_start[$arr_month_in_qtr[substr($trade_date_to_process,5,2)]];
$year_to_process = substr($trade_date_to_process,0,4);
$global_qtr_start_date = db_single_val("SELECT brk_start_date as single_val 
																	FROM `brk_brokerage_months` 
																	WHERE brk_month = '".$qtr_start_val."'
																	  AND brk_year = '".$year_to_process."'");
//xdebug("qtr_start_val",$qtr_start_val);
//xdebug("year_to_process",$year_to_process);
//xdebug("global_qtr_start_date",$global_qtr_start_date);


$qry_qtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_advisor_code in (".$str_subset_clients.")
								 GROUP BY trad_advisor_code";
//xdebug("qry_qtd_comm",$qry_qtd_comm);
$result_qtd_comm = mysql_query($qry_qtd_comm) or die (tdw_mysql_error($qry_qtd_comm));
while ( $row_qtd_comm = mysql_fetch_array($result_qtd_comm) ) 
{
	$arr_qtd_comm[$row_qtd_comm["trad_advisor_code"]] = $row_qtd_comm["trad_comm"];
}
//show_array($arr_qtd_comm);

//GET THE START OF THE YEAR
//xdebug("First Day of the current Brokerage Year",substr($trade_date_to_process,0,4));
$global_year_start_date = db_single_val("SELECT brk_start_date as single_val 
																		FROM `brk_brokerage_months` 
																		WHERE brk_month = 'Jan'
																	  AND brk_year = '".substr($trade_date_to_process,0,4)."'");
//now get ytd
$qry_ytd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_advisor_code in (".$str_subset_clients.")
								 GROUP BY trad_advisor_code";
								 //removed 	WHERE trad_trade_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
								 							 
//xdebug("qry_ytd_comm",$qry_ytd_comm);
$result_ytd_comm = mysql_query($qry_ytd_comm) or die (tdw_mysql_error($qry_ytd_comm));
while ( $row_ytd_comm = mysql_fetch_array($result_ytd_comm) ) 
{
	$arr_ytd_comm[$row_ytd_comm["trad_advisor_code"]] = $row_ytd_comm["trad_comm"];
}
//show_array($arr_ytd_comm);


//now get the check commissions for the clients for the qtd, mtd, ytd

//for the mtd
$qry_mtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,8)."01"."' and '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND a.chek_advisor in  (".$str_subset_clients.") 
								 GROUP BY a.chek_advisor";

$result_mtd_check = mysql_query($qry_mtd_check) or die (tdw_mysql_error($qry_mtd_check));
while ( $row_mtd_check = mysql_fetch_array($result_mtd_check) ) 
{
	$arr_mtd_check[$row_mtd_check["chek_advisor"]] = $row_mtd_check["total_checks"];
}

//show_array($arr_mtd_check);

//for the qtd

function get_quarter_dates ($q, $y, $b="B") { // Brokerage vs Calendar

$arr_qtrs = array(1=>"Jan|Mar",2=>"Apr|Jun",3=>"Jul|Sep",4=>"Oct|Dec"); 
$arr_qtrs_startmon = array(1=>"01",2=>"04",3=>"07",4=>"10"); 
$arr_qtrs_endmon =   array(1=>"03",2=>"06",3=>"09",4=>"12"); 

$arr_start_end_months = explode("|",$arr_qtrs[$q]);

	if ($b=="B") {
		$result_ = mysql_query("SELECT brk_start_date FROM brk_brokerage_months where brk_month = '".$arr_start_end_months[0]."' and brk_year = '".$y."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$begin_tradedate = $row["brk_start_date"];
		}

		$result_ = mysql_query("SELECT brk_end_date FROM brk_brokerage_months where brk_month = '".$arr_start_end_months[1]."' and brk_year = '".$y."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$end_tradedate = $row["brk_end_date"];
		}

		$arr_return_dates = array($begin_tradedate,$end_tradedate);
		return $arr_return_dates;

	} else {
		//to be programmed
		$sdate = $y."-".$arr_qtrs_startmon[$q]."-01";
		$edate = $y."-".$arr_qtrs_endmon[$q]."-".idate('d', mktime(0, 0, 0, ($arr_qtrs_endmon[$q] + 1), 0, $y));
		return array($sdate,$edate);
	}
}

$z_arr_month_qtr = array("01"=>"1","02"=>"1","03"=>"1","04"=>"2","05"=>"2","06"=>"2","07"=>"3","08"=>"3","09"=>"3","10"=>"4","11"=>"4","12"=>"4",);

//xdebug("Q",$z_arr_month_qtr[substr($trade_date_to_process,5,2)]);
//xdebug("Y",substr($trade_date_to_process,0,4));

$z_qtr_dates = get_quarter_dates($z_arr_month_qtr[substr($trade_date_to_process,5,2)],substr($trade_date_to_process,0,4),"C");

$qry_qtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$z_qtr_dates[0]."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_advisor in  (".$str_subset_clients.") 
								 GROUP BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_qtd_check",$qry_qtd_check);
$result_qtd_check = mysql_query($qry_qtd_check) or die (tdw_mysql_error($qry_qtd_check));
while ( $row_qtd_check = mysql_fetch_array($result_qtd_check) ) 
{
	$arr_qtd_check[$row_qtd_check["chek_advisor"]] = $row_qtd_check["total_checks"];
}

//show_array($arr_qtd_check);
//xdebug("qry_qtd_check",$qry_qtd_check);

//for the ytd
$qry_ytd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_advisor in  (".$str_subset_clients.") 
								 GROUP BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_ytd_check",$qry_ytd_check);
$result_ytd_check = mysql_query($qry_ytd_check) or die (tdw_mysql_error($qry_ytd_check));
while ( $row_ytd_check = mysql_fetch_array($result_ytd_check) ) 
{
	$arr_ytd_check[$row_ytd_check["chek_advisor"]] = $row_ytd_check["total_checks"];
}

//show_array($arr_ytd_check);
//xdebug("qry_day_check",$qry_day_check);
//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================







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

//????????????????????????????????????????
$arr_name_for_id = array(); //[$row_comment["clnt_comment_by"]]
$qry_usr = "select ID, Fullname FROM users"; 
$result_usr = mysql_query($qry_usr) or die (tdw_mysql_error($qry_usr));
while ( $row_usr = mysql_fetch_array($result_usr) ) 
{
	$arr_name_for_id[$row_usr["ID"]] = $row_usr["Fullname"];
}

$arr_comment_count = array();;
$qry_comment_count = "select count(clnt_auto_id) as commcount, clnt_auto_id
               FROM int_clnt_clients_comments 
							 WHERE clnt_isactive = 1
							 GROUP BY clnt_auto_id"; 
$result_comment_count = mysql_query($qry_comment_count) or die (tdw_mysql_error($qry_comment_count));
while ( $row_comment_count = mysql_fetch_array($result_comment_count) ) 
{
	$arr_comment_count[$row_comment_count["clnt_auto_id"]] = $row_comment_count["commcount"];
}

//show_array($arr_comment_count);

$arr_clnt_comment = array();
$qry_comment = "select auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive  
               FROM int_clnt_clients_comments 
							 WHERE clnt_isactive = 1
							 ORDER BY clnt_auto_id, clnt_timestamp"; 
$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
while ( $row_comment = mysql_fetch_array($result_comment) ) 
{
  $whowhen = "";
	
	$whowhen = "[".date('m/d/y h:ia',strtotime($row_comment["clnt_timestamp"]))." ".$arr_name_for_id[$row_comment["clnt_comment_by"]]."]\r\n";
	$str_comment_data = str_replace('"','',$row_comment["clnt_comment"]);
	$arr_clnt_comment[$row_comment["clnt_auto_id"]] = "&#9658;".$whowhen.$str_comment_data."\r\n".$arr_clnt_comment[$row_comment["clnt_code"]];
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

if ($proc_user) {
	$val_user_id = $proc_user;
} else {
	$val_user_id = $user_id;
} 

?>


<?
$output_filename = "clients_prospects.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);

	if ($mqy_sel == 'M') {
		$var_lbl_a = 'Curr. MTD';
	} else if ($mqy_sel == 'Q') {
		$var_lbl_a = 'Curr. QTD';
	} else {
		$var_lbl_a = date('Y').' YTD';
	}
							
$str = '<table id="sort_this" width="100%"  border="1" cellspacing="1" cellpadding="1"><!-- class="sortable" preserve_style="cell"-->
						<thead>
							<tr class="headrow">
							<td><strong>Client Name</strong></td>
							<td><strong>Code</strong></td>
							<td><strong>T\'ware</strong></td>
							<td><strong>RR1</strong></td>
							<td><strong>RR2</strong></td>
							<td><strong>Trdr.</strong></td>
							<td><strong>Status</strong></td>
							<td><strong>'.$var_lbl_a.'</strong></td>
							<td><strong>Last Year</strong></td>
							<td><strong>Tier</strong></td>
							<td><strong>Comments</strong></td>
							<td>&nbsp;</td>
							</tr>
            </thead>';
fputs ($fp, $str);

						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
	
								//GET TIER
								if ($row["clnt_status"] == 'A') {
								
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
									$str_tier_show = "<td>&nbsp;".$val_tier. "</td>";
								} else {
									$str_tier_show = "<td></td>";
								}
	
	
	
	
						
						if ($row["clnt_alt_code"]=='INACTIVE') {
						$str_clnt_code = " "; //"<font color=red>".$row["clnt_alt_code"].'</font>';
						} else {
						$str_clnt_code = $row["clnt_alt_code"];
						}

						$short_td_string = "";
						
						if ($mqy_sel == 'M') {
						$short_td_string = '<td align="right">'. round(($arr_mtd_comm[$row["clnt_code"]] + $arr_mtd_check[$row["clnt_code"]]),0) .'</td>';
						} else if ($mqy_sel == 'Q') {
						$short_td_string = '<td align="right">'. round(($arr_qtd_comm[$row["clnt_code"]] + $arr_qtd_check[$row["clnt_code"]]),0) .'</td>';
						} else {
						$short_td_string = '<td align="right">'. round(($arr_ytd_comm[$row["clnt_code"]] + $arr_ytd_check[$row["clnt_code"]]),0) .'</td>';
						}

								if ($process_tier == 1) {
								
									if ($val_tier == $valcriteria) {
													if ($row["clnt_isactive"] != 0) {
														$str = '<tr>
															<td nowrap="nowrap">&nbsp;'.trim($row["clnt_name"]).'</td>
															<td>&nbsp;'.$row["clnt_code"].'</td>
															<td>&nbsp;'.$str_clnt_code.'</td>
															<td>&nbsp;'.$row["clnt_rr1"].'</td>
															<td>&nbsp;'.$row["clnt_rr2"].'</td>
															<td>&nbsp;'.$row["clnt_trader"].'</td>
															<td>&nbsp;'.$row["clnt_status"].'</td>
															'.$short_td_string.'
															<td align="right">'.round(get_previous_yr_data($row["clnt_code"]),0).'</td>
															'.$str_tier_show.'
															<td>'.$arr_clnt_comment[$row["clnt_auto_id"]].'</td>
															<td></td>
															</tr>';
														fputs ($fp, $str);
													} else {
														$str = '<tr>
															<td nowrap="nowrap">&nbsp;'.trim($row["clnt_name"]).'</td>
															<td>&nbsp;'.$row["clnt_code"].'</td>
															<td>&nbsp;'.$str_clnt_code.'</td>
															<td>&nbsp;'.$row["clnt_rr1"].'</td>
															<td>&nbsp;'.$row["clnt_rr2"].'</td>
															<td>&nbsp;'.$row["clnt_trader"].'</td>
															<td>&nbsp;'.$row["clnt_status"].'</td>
															'.$short_td_string.'
															<td align="right">'.round(get_previous_yr_data($row["clnt_code"]),0).'</td>
															'.$str_tier_show.'
															<td>'.$arr_clnt_comment[$row["clnt_auto_id"]].'</td>
															<td></td>
															</tr>';							
														fputs ($fp, $str);
													}
													$count_row = $count_row + 1;
									}
								} else {
													if ($row["clnt_isactive"]  != 0) { // == 1
														$str = '<tr>
															<td nowrap="nowrap">&nbsp;'.trim($row["clnt_name"]).'</td>
															<td>&nbsp;'.$row["clnt_code"].'</td>
															<td>&nbsp;'.$str_clnt_code.'</td>
															<td>&nbsp;'.$row["clnt_rr1"].'</td>
															<td>&nbsp;'.$row["clnt_rr2"].'</td>
															<td>&nbsp;'.$row["clnt_trader"].'</td>
															<td>&nbsp;'.$row["clnt_status"].'</td>
															'.$short_td_string.'
															<td align="right">'.round(get_previous_yr_data($row["clnt_code"]),0).'</td>
															'.$str_tier_show.'
															<td>'.$arr_clnt_comment[$row["clnt_auto_id"]].'</td>
															<td></td>
															</tr>';							
														fputs ($fp, $str);
													} else {
														$str = '<tr>
															<td nowrap="nowrap">&nbsp;'.trim($row["clnt_name"]).'</td>
															<td>&nbsp;'.$row["clnt_code"].'</td>
															<td>&nbsp;'.$str_clnt_code.'</td>
															<td>&nbsp;'.$row["clnt_rr1"].'</td>
															<td>&nbsp;'.$row["clnt_rr2"].'</td>
															<td>&nbsp;'.$row["clnt_trader"].'</td>
															<td>&nbsp;'.$row["clnt_status"].'</td>
															'.$short_td_string.'
															<td align="right">'.round(get_previous_yr_data($row["clnt_code"]),0).'</td>
															'.$str_tier_show.'
															<td>'.$arr_clnt_comment[$row["clnt_auto_id"]].'</td>
															<td></td>
															</tr>';							
														fputs ($fp, $str);
													}
													$count_row = $count_row + 1;
								}
						}

$str = '</table>
	</body>
</html>';
fputs ($fp, $str);

fclose($fp);


Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>