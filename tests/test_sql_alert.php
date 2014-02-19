<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

	$query_sel_symbol = "select distinct(trad_reference_number) from mry_comm_rr_trades";
	$result_sel_symbol = mysql_query($query_sel_symbol) or tdw_mysql_error_email($query_sel_symbol);

?>
