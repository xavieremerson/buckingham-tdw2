<?
//Check if there are clients with multiple RR's within the month.
$ec_qry_mult_rr = "SELECT count(DISTINCT(trad_rr)), trad_advisor_code
							FROM `mry_comm_rr_trades` 
							WHERE trad_settle_date between '".$brk_start_settle_date."' AND '".$brk_end_settle_date."'
							AND trad_is_cancelled =0
							GROUP BY trad_advisor_code
							HAVING count(DISTINCT(trad_rr)) >1
							ORDER BY `trad_advisor_code` ASC";
//xdebug("ec_qry_mult_rr",$ec_qry_mult_rr);
$ec_result_mult_rr = mysql_query($ec_qry_mult_rr) or die (tdw_mysql_error($ec_qry_mult_rr));
$err_found = 0;
$str_message = "<img src='images/stop_error_1818.gif' border='0'> <font color='#CC0000' size='2' face='Verdana, Arial, Helvetica, sans-serif'><strong>The following clients have trades in multiple RR's<BR>";
while ( $row_mult_rr = mysql_fetch_array($ec_result_mult_rr) ) 
{
	$err_found = $err_found + 1;
  $clnt_name = db_single_val("select clnt_name as single_val from int_clnt_clients where clnt_code = '".$row_mult_rr["trad_advisor_code"]."' AND clnt_isactive = 1 LIMIT 1");
	$str_message .= "[ ".$row_mult_rr["trad_advisor_code"]." ] ".$clnt_name."<BR></strong></font>";
}
if ($err_found > 0) {
echo $str_message;
}
?>