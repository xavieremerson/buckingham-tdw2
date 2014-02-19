<?
$rep_to_process = $tmp_rep;
$arr_day_comm_shrd = array();
$arr_mtd_comm_shrd = array();
$arr_qtd_comm_shrd = array();
$arr_ytd_comm_shrd = array();
$arr_day_check_shrd = array();
$arr_mtd_check_shrd = array();
$arr_qtd_check_shrd = array();
$arr_ytd_check_shrd = array();

//echo "entered file <br>";

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//SHARED SECTION
													
	//Get lookup relevant client codes from client master (internal) for verification
	$qry_relevant_shared_clients = "SELECT DISTINCT (a.clnt_code) as relevant_shared_client
																		FROM int_clnt_clients a, Users b
																	WHERE (
																						(
																						a.clnt_rr1 = b.Initials
																						AND a.clnt_rr2 != ''
																						)
																				OR  (
																						a.clnt_rr2 = b.Initials
																						AND a.clnt_rr1 != ''
																						)
																				)
																				AND b.rr_num = '".$rep_to_process."'
																	ORDER BY a.clnt_name";
																	
	$result_relevant_shared_clients = mysql_query($qry_relevant_shared_clients) or die (tdw_mysql_error($qry_relevant_shared_clients));
	
	$arr_relevant_shared_clients = array();
	$arr_rel_shrd_clnts = array();
	while ( $row_relevant_shared_clients = mysql_fetch_array($result_relevant_shared_clients) ) 
	{
		//getting the shared rep number information from the trades file.
		$qry = "select max(trad_rr) as single_val from mry_comm_rr_trades 
													where trad_advisor_code = '".$row_relevant_shared_clients["relevant_shared_client"]."' 
													and trad_rr != '".$rep_to_process."'";
	  //xdebug("qry",$qry);
		$srn = db_single_val($qry);
		$arr_relevant_shared_clients[$row_relevant_shared_clients["relevant_shared_client"]] = $srn;
	  $arr_rel_shrd_clnts[$row_relevant_shared_clients["relevant_shared_client"]] = $row_relevant_shared_clients["relevant_shared_client"];
	}
	$str_shrd_clnts = implode(",",$arr_rel_shrd_clnts);
	$str_shrd_clnts = "'".str_replace(",","','",$str_shrd_clnts)."'";
  //show_array($arr_relevant_shared_clients);
	//xdebug("str_shrd_clnts",$str_shrd_clnts);
	
//now get the trad commissions for the clients for the day, qtd, mtd, ytd
//for the day
$qry_day_comm_shrd = "SELECT trad_rr, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date = '".$trade_date_to_process."'
											 AND trad_is_cancelled = 0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_rr";
//xdebug("qry_day_comm_shrd",$qry_day_comm_shrd);
$result_day_comm_shrd = mysql_query($qry_day_comm_shrd) or die (tdw_mysql_error($qry_day_comm_shrd));
while ( $row_day_comm_shrd = mysql_fetch_array($result_day_comm_shrd) ) 
{
	$arr_day_comm_shrd[$rep_to_process] = $arr_day_comm_shrd[$rep_to_process] + $row_day_comm_shrd["trad_comm"];
}

//show_array($arr_day_comm_shrd);

//now mtd comm
$qry_mtd_comm_shrd =  "SELECT trad_rr, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
											 AND trad_is_cancelled =0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_rr";
$result_mtd_comm_shrd = mysql_query($qry_mtd_comm_shrd) or die (tdw_mysql_error($qry_mtd_comm_shrd));
while ( $row_mtd_comm_shrd = mysql_fetch_array($result_mtd_comm_shrd) ) 
{
	$arr_mtd_comm_shrd[$rep_to_process] = $arr_mtd_comm_shrd[$rep_to_process] + $row_mtd_comm_shrd["trad_comm"];
}
//show_array($arr_mtd_comm_shrd);

//get qtd values
$qry_qtd_comm_shrd =  "SELECT trad_rr, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
											 AND trad_is_cancelled =0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_rr";
$result_qtd_comm_shrd = mysql_query($qry_qtd_comm_shrd) or die (tdw_mysql_error($qry_qtd_comm_shrd));
while ( $row_qtd_comm_shrd = mysql_fetch_array($result_qtd_comm_shrd) ) 
{
	$arr_qtd_comm_shrd[$rep_to_process] = $arr_qtd_comm_shrd[$rep_to_process] + $row_qtd_comm_shrd["trad_comm"];
}
//show_array($arr_qtd_comm_shrd);

//now get ytd
//THIS PROBLEM RESOLVED: YEAR START DATE replacing substr($trade_date_to_process,0,4)."-01-01"

$qry_ytd_comm_shrd =  "SELECT trad_rr, sum( trad_commission ) as trad_comm 
											 FROM mry_comm_rr_trades 
											 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
											 AND trad_is_cancelled = 0
											 AND trad_rr != '".$rep_to_process."'
											 AND trad_advisor_code in (".$str_shrd_clnts.")
											 GROUP BY trad_rr";
$result_ytd_comm_shrd = mysql_query($qry_ytd_comm_shrd) or die (tdw_mysql_error($qry_ytd_comm_shrd));
while ( $row_ytd_comm_shrd = mysql_fetch_array($result_ytd_comm_shrd) ) 
{
	$arr_ytd_comm_shrd[$rep_to_process] = $arr_ytd_comm_shrd[$rep_to_process] + $row_ytd_comm_shrd["trad_comm"];
}

//show_array($arr_ytd_comm_shrd);


//now get the check commissions for the clients for the day, qtd, mtd, ytd
//for the day

$qry_day_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date = '".$trade_date_to_process."' 
													AND a.chek_isactive = 1
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor";
$result_day_check_shrd = mysql_query($qry_day_check_shrd) or die (tdw_mysql_error($qry_day_check_shrd));
while ( $row_day_check_shrd = mysql_fetch_array($result_day_check_shrd) ) 
{
	$arr_day_check_shrd[$rep_to_process] = $arr_day_check_shrd[$rep_to_process] + $row_day_check_shrd["total_checks"];
}

//show_array($arr_day_check);
//xdebug("qry_day_check",$qry_day_check);

//for the mtd
$qry_mtd_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date between '".$global_chk_qry_date_start_mtd."' and '".$trade_date_to_process."'
													AND a.chek_isactive = 1
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor";
$result_mtd_check_shrd = mysql_query($qry_mtd_check_shrd) or die (tdw_mysql_error($qry_mtd_check_shrd));
while ( $row_mtd_check_shrd = mysql_fetch_array($result_mtd_check_shrd) ) 
{
	$arr_mtd_check_shrd[$rep_to_process] = $arr_mtd_check_shrd[$rep_to_process] + $row_mtd_check_shrd["total_checks"];
}

//show_array($arr_mtd_check_shrd);

//for the qtd
$qry_qtd_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date between '".$global_chk_qry_date_start_qtd."' and '".$trade_date_to_process."'
													AND a.chek_isactive = 1
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor";
//xdebug("qry_qtd_check_shrd",$qry_qtd_check_shrd);
$result_qtd_check_shrd = mysql_query($qry_qtd_check_shrd) or die (tdw_mysql_error($qry_qtd_check_shrd));
while ( $row_qtd_check_shrd = mysql_fetch_array($result_qtd_check_shrd) ) 
{
	$arr_qtd_check_shrd[$rep_to_process] = $arr_qtd_check_shrd[$rep_to_process] + $row_qtd_check_shrd["total_checks"];
}

//show_array($arr_qtd_check_shrd);
//xdebug("qry_day_check_shrd",$qry_day_check_shrd);

//for the ytd
$qry_ytd_check_shrd = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
												FROM chk_chek_payments_etc a
												WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
													AND a.chek_isactive = 1
													AND a.chek_reps_and like '%".$user_initials."%'
													AND a.chek_advisor in (".$str_shrd_clnts.")
											 GROUP BY a.chek_advisor";
//xdebug("qry_ytd_check_shrd",$qry_ytd_check_shrd);
$result_ytd_check_shrd = mysql_query($qry_ytd_check_shrd) or die (tdw_mysql_error($qry_ytd_check_shrd));
while ( $row_ytd_check_shrd = mysql_fetch_array($result_ytd_check_shrd) ) 
{
	$arr_ytd_check_shrd[$rep_to_process] = $arr_ytd_check_shrd[$rep_to_process] + $row_ytd_check_shrd["total_checks"];
}

//show_array($arr_ytd_check_shrd);

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


?>