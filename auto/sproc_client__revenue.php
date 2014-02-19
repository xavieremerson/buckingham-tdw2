<?
//Task to create static data for Client Revenue.
//Performance Issue when runnung on demand.

//error_reporting(E_ALL);
ini_set("memory_limit","512M");

include('../includes/dbconnect.php');
include('../includes/global.php');
include('../includes/functions.php');

//Trade Date To Process
$trade_date_to_process = previous_business_day();

//now mtd comm
//get the start day of month for this
$global_qry_date_start_mtd = db_single_val("SELECT brk_start_date as single_val 
																			FROM `brk_brokerage_months` 
																			WHERE `brk_start_date` <= '".$trade_date_to_process."'
																			AND `brk_end_date` >= '".$trade_date_to_process."'");
//ydebug("global_qry_date_start_mtd",$global_qry_date_start_mtd);
$arr_mtd_comm = array();
$qry_mtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 GROUP BY trad_advisor_code
								 ORDER BY trad_advisor_code";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{ 
	$arr_mtd_comm[$row_mtd_comm["trad_advisor_code"]] = $row_mtd_comm["trad_comm"];
}

//print_r($arr_mtd_comm);
$arr_mtd_chek = array();
$qry_mtd_chek = "SELECT chek_advisor, sum( chek_amount ) as chek_comm 
								 FROM chk_chek_payments_etc 
								 WHERE chek_date between '".date("Y-m-",strtotime($trade_date_to_process))."01"."' and '".$trade_date_to_process."'
								 AND chek_isactive = 1
								 GROUP BY chek_advisor
								 ORDER BY chek_advisor";
$result_mtd_chek = mysql_query($qry_mtd_chek) or die (tdw_mysql_error($qry_mtd_chek));
while ( $row_mtd_chek = mysql_fetch_array($result_mtd_chek) ) 
{
	$arr_mtd_chek[$row_mtd_chek["chek_advisor"]] = $row_mtd_chek["chek_comm"];
}
//print_r($arr_mtd_chek);

$sum_curr_mtd = array();
foreach ($arr_mtd_comm as $k=>$v) {
	$sum_curr_mtd[$k] = $v + $arr_mtd_chek[$k];
	
}
foreach ($arr_mtd_chek as $k=>$v) {
	if (!array_key_exists($k,$arr_mtd_comm)) {
		$sum_curr_mtd[$k] = $v;
	}
}

print_r($sum_curr_mtd);
								 
?>