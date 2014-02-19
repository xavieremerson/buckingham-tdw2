<?
//OLD FILE 
//DO NOT USE
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

						/*
						//Flush the memory tables used by production pages/app
						$result_mry_comm_rr_level_a_flush = mysql_query("truncate table mry_comm_rr_level_0") or die (mysql_error());
						$result_mry_comm_rr_level_a_flush = mysql_query("truncate table mry_comm_rr_level_a") or die (mysql_error());
						$result_mry_comm_rr_level_b_flush = mysql_query("truncate table mry_comm_rr_level_b") or die (mysql_error());
						$result_mry_comm_rr_trades_flush =  mysql_query("truncate table mry_comm_rr_trades") or die (mysql_error());
						
						//Populate tables
						$result_mry_comm_rr_level_a_populate = mysql_query("insert into mry_comm_rr_level_0 select * from rep_comm_rr_level_0") or die (mysql_error());
						$result_mry_comm_rr_level_a_populate = mysql_query("insert into mry_comm_rr_level_a select * from rep_comm_rr_level_a") or die (mysql_error());
						$result_mry_comm_rr_level_b_populate = mysql_query("insert into mry_comm_rr_level_b select * from rep_comm_rr_level_b") or die (mysql_error());
						$result_mry_comm_rr_trades_populate = mysql_query("insert into mry_comm_rr_trades select * from rep_comm_rr_trades") or die (mysql_error());
						*/
						
						//Flush the memory tables used by production pages/app
						$result_nadd_flush = mysql_query("truncate table mry_nfs_nadd ") or die (mysql_error());
						$result_nadd_populate = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (mysql_error());


?>