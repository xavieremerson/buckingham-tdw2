<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

include('pay_payout_functions.php');

 
//Create array of special payouts
$qry_sp_payout = "SELECT a.clnt_code, b.clnt_special_payout_rate
								FROM int_clnt_clients a, int_clnt_payout_rate b
								WHERE a.clnt_auto_id = b.clnt_auto_id
								AND b.clnt_default_payout !=1";
$result_sp_payout = mysql_query($qry_sp_payout) or die (tdw_mysql_error($qry_sp_payout));
$arr_clients = array();
while ( $row_sp_payout = mysql_fetch_array($result_sp_payout) ) 
{
	$arr_sp_payout[$row_sp_payout["clnt_code"]] = $row_sp_payout["clnt_special_payout_rate"]; 
}
show_array($arr_sp_payout);

echo sp_payout_rate('AIMA', 238, $arr_sp_payout);


?>