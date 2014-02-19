<?
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 


//Populate tables

$result_mry_comm_rr_level_0_populate = mysql_query("insert into mry_comm_rr_level_0 select * from rep_comm_rr_level_0") or die (mysql_error());
$result_mry_comm_rr_level_a_populate = mysql_query("insert into mry_comm_rr_level_a select * from rep_comm_rr_level_a") or die (mysql_error());
$result_mry_comm_rr_level_b_populate = mysql_query("insert into mry_comm_rr_level_b select * from rep_comm_rr_level_b") or die (mysql_error());
$result_mry_comm_rr_trades_populate = mysql_query("insert into mry_comm_rr_trades select * from rep_comm_rr_trades") or die (mysql_error());


?>