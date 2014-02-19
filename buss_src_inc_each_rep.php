<?
$rep_to_process = $tmp_rep;

$arr_day_comm = array();
$arr_mtd_comm = array();
$arr_qtd_comm = array();
$arr_ytd_comm = array();
$arr_day_check = array();
$arr_mtd_check = array();
$arr_qtd_check = array();
$arr_ytd_check = array();

//*********************************************************************************************

//Create Array of all clients to show here
$arr_clnt_for_rr = array();
$qry_clnt_for_rr = "SELECT distinct(trad_advisor_code) 
										FROM mry_comm_rr_trades 
										WHERE trad_trade_date <= '".$trade_date_to_process."' 
										  and trad_trade_date >= '".substr($trade_date_to_process,0,4)."-01-01' 
											and trad_is_cancelled = 0
											and trad_rr = '".$rep_to_process."' 
										order by trad_advisor_code";

$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["trad_advisor_code"]] = $row_clnt_for_rr["trad_advisor_code"];
}

//show_array($arr_clnt_for_rr);
//get initials for the user
$user_initials = db_single_val("select Initials as single_val from users where rr_num = '".$rep_to_process."'");
if ($user_initials == "") {
$user_initials = "ZZZ";
}

$qry_clnt_for_rr = "SELECT distinct(a.chek_advisor) as chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date <= '".$trade_date_to_process."'
										  and a.chek_date >= '".substr($trade_date_to_process,0,4)."-01-01' 
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}
//show_array($arr_clnt_for_rr);

//also check client history table for information
$qry_clnt_for_rr = "SELECT distinct(a.chek_advisor) as chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients_history b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date <= '".$trade_date_to_process."' 
										  and a.chek_date >= '".substr($trade_date_to_process,0,4)."-01-01' 
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}
//show_array($arr_clnt_for_rr);

//now get the trad commissions for the clients for the day, qtd, mtd, ytd
//for the day
$qry_day_comm = "SELECT trad_rr, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date = '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_rr";
								 
//xdebug("qry_day_comm",$qry_day_comm);
//exit;
$result_day_comm = mysql_query($qry_day_comm) or die (tdw_mysql_error($qry_day_comm));
while ( $row_day_comm = mysql_fetch_array($result_day_comm) ) 
{
	$arr_day_comm[$row_day_comm["trad_rr"]] = $row_day_comm["trad_comm"];
}

//show_array($arr_day_comm);

//now mtd comm
//get the start day of month for this
$global_qry_date_start_mtd = db_single_val("SELECT brk_start_date as single_val 
																			FROM `brk_brokerage_months` 
																			WHERE `brk_start_date` <= '".$trade_date_to_process."'
																			AND `brk_end_date` >= '".$trade_date_to_process."'");
//xdebug("global_qry_date_start_mtd",$global_qry_date_start_mtd);

$qry_mtd_comm = "SELECT trad_rr, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_rr";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{
	$arr_mtd_comm[$row_mtd_comm["trad_rr"]] = $row_mtd_comm["trad_comm"];
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


$qry_qtd_comm = "SELECT trad_rr, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_rr";
//xdebug("qry_qtd_comm",$qry_qtd_comm);
$result_qtd_comm = mysql_query($qry_qtd_comm) or die (tdw_mysql_error($qry_qtd_comm));
while ( $row_qtd_comm = mysql_fetch_array($result_qtd_comm) ) 
{
	$arr_qtd_comm[$row_qtd_comm["trad_rr"]] = $row_qtd_comm["trad_comm"];
}
//show_array($arr_qtd_comm);

//now get ytd
//get year start date
$year_to_process = substr($trade_date_to_process,0,4);
$global_year_start_date = db_single_val("SELECT brk_start_date as single_val 
																	FROM `brk_brokerage_months` 
																	WHERE brk_month = 'Jan'
																	  AND brk_year = '".$year_to_process."'");
//xdebug("global_year_start_date",$global_year_start_date);


//THIS PROBLEM RESOLVED: YEAR START DATE substr($trade_date_to_process,0,4)."-01-01"
$qry_ytd_comm = "SELECT trad_rr, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_rr = '".$rep_to_process."'
								 GROUP BY trad_rr";
//xdebug("qry_ytd_comm",$qry_ytd_comm);
$result_ytd_comm = mysql_query($qry_ytd_comm) or die (tdw_mysql_error($qry_ytd_comm));
while ( $row_ytd_comm = mysql_fetch_array($result_ytd_comm) ) 
{
	$arr_ytd_comm[$row_ytd_comm["trad_rr"]] = $row_ytd_comm["trad_comm"];
}
//show_array($arr_ytd_comm);


//now get the check commissions for the clients for the day, qtd, mtd, ytd
//for the day

$qry_day_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date = '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '".$user_initials."%'
										AND length (trim(chek_reps_and)) = 3
								 GROUP BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
$result_day_check = mysql_query($qry_day_check) or die (tdw_mysql_error($qry_day_check));
while ( $row_day_check = mysql_fetch_array($result_day_check) ) 
{
	$arr_day_check[$rep_to_process] = $arr_day_check[$rep_to_process] + $row_day_check["total_checks"];
}

//show_array($arr_day_check);
//xdebug("qry_day_check",$qry_day_check);

//for the mtd
//month for checks is the first day of calenday month, not the brokerage month
//using global_chk_qry_date_start_mtd instead of global_qry_date_start_mtd
//Correction made to the logic below.
$global_chk_qry_date_start_mtd = substr($trade_date_to_process,0,7)."-01";

$qry_mtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$global_chk_qry_date_start_mtd."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '".$user_initials."%'
										AND length (trim(chek_reps_and)) = 3
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_mtd_check",$qry_mtd_check);
$result_mtd_check = mysql_query($qry_mtd_check) or die (tdw_mysql_error($qry_mtd_check));
while ( $row_mtd_check = mysql_fetch_array($result_mtd_check) ) 
{
	$arr_mtd_check[$rep_to_process] = $arr_mtd_check[$rep_to_process] + $row_mtd_check["total_checks"];
}

//show_array($arr_mtd_check);

//for the qtd
//qtr for checks is the first day of calenday quarter, not the brokerage quarter
//using global_chk_qry_date_start_qtd instead of global_qtr_start_date
//Correction made to the logic below.
$arr_qtr_start_month_num = array(1=>'01',2=>'04',3=>'07',4=>'10');
$arr_month_in_qtr = array('01'=>1,'02'=>1,'03'=>1,'04'=>2,'05'=>2,'06'=>2,'07'=>3,'08'=>3,'09'=>3,'10'=>4,'11'=>4,'12'=>4);
$year_to_process = substr($trade_date_to_process,0,4);
$global_chk_qry_date_start_qtd = $year_to_process."-".$arr_qtr_start_month_num[$arr_month_in_qtr[substr($trade_date_to_process,5,2)]]."-01";
//xdebug("global_chk_qry_date_start_qtd",$global_chk_qry_date_start_qtd);

$qry_qtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$global_chk_qry_date_start_qtd."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '".$user_initials."%'
										AND length (trim(chek_reps_and)) = 3
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_qtd_check",$qry_qtd_check);
$result_qtd_check = mysql_query($qry_qtd_check) or die (tdw_mysql_error($qry_qtd_check));
while ( $row_qtd_check = mysql_fetch_array($result_qtd_check) ) 
{
	$arr_qtd_check[$rep_to_process] = $arr_qtd_check[$rep_to_process] + $row_qtd_check["total_checks"];
}

//show_array($arr_qtd_check);
//xdebug("qry_day_check",$qry_day_check);

//for the ytd
$qry_ytd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_reps_and like '".$user_initials."%'
										AND length (trim(chek_reps_and)) = 3
								 GROUP BY a.chek_advisor
								 ORDER BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_ytd_check",$qry_ytd_check);
$result_ytd_check = mysql_query($qry_ytd_check) or die (tdw_mysql_error($qry_ytd_check));
while ( $row_ytd_check = mysql_fetch_array($result_ytd_check) ) 
{
	$arr_ytd_check[$rep_to_process] = $arr_ytd_check[$rep_to_process] + $row_ytd_check["total_checks"];
}

//show_array($arr_ytd_check);
//xdebug("qry_day_check",$qry_day_check);

?>