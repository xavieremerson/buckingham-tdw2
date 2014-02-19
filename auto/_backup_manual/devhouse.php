<?
ini_set('max_execution_time', 3600);
?>

<?
mysql_connect("localhost", "newadmin", "newpassword") or die(mysql_error());  
mysql_select_db("devhouse") or die(mysql_error());

$backup_folder_temp 	= "d:\\tdw\\tdw\\auto\\backup_tdw\\";  //(Backup Location)trailing slash required
$backup_folder_perm 	= "e:\\backup\\";  //(Backup Location)trailing slash required
$db_hostname		= "localhost";
$db_user 				= "newadmin";
$db_pw 					= "newpassword";
$db_database		= "devhouse";
$loc_mysqldump	= "D:\\Program Files\\MySQL\\MySQL Server 5.0\\bin\\"; //trailing slash required
$temp_dump_file	= "file.dump";


	 
	shell_exec("copy ".$backup_folder_perm."file.dump ". $backup_folder_perm."devhouse.dump");
	
	$fq = fopen ($backup_folder_perm."devhouse_final.dump", "w"); 
	
	
	$fp = fopen ($backup_folder_perm."devhouse.dump", "r"); 
	while (!feof ($fp)) { 
		$content = fgets( $fp, 4096 ); 
		$content = str_replace("warehouse","devhouse",$content);
		fwrite ($fq,$content);        
	}
	
		fclose ($fp);   
		fclose ($fq);   
	 

$str_query = "DROP TABLE 
							arc_comm_rr,
							atd_todo_list,
							bkup_users,
							brk_brokerage_months,
							chk_chek_payments_etc,
							ctrl_control_number,
							holidays,
							int_clnt_clients,
							log_emails,
							mgmt_reports_creation,
							mgmt_sup_report_views,
							mry_comm_rr,
							mry_comm_rr_level_0,
							mry_comm_rr_level_a,
							mry_comm_rr_level_b,
							mry_comm_rr_trades,
							mry_dos_commission,
							mry_nfs_nadd,
							nfs_delta_nadd,
							nfs_delta_nadd_2x0,
							nfs_delta_nadd_2x1,
							nfs_delta_nadd_2x2,
							nfs_delta_nadd_2x3,
							nfs_delta_nadd_101,
							nfs_delta_nadd_102,
							nfs_delta_nadd_103,
							nfs_delta_nadd_104,
							nfs_delta_nadd_113,
							nfs_delta_nadd_115,
							nfs_delta_nadd_901,
							nfs_nadd,
							nfs_nadd_2x0,
							nfs_nadd_2x1,
							nfs_nadd_2x2,
							nfs_nadd_2x3,
							nfs_nadd_101,
							nfs_nadd_102,
							nfs_nadd_103,
							nfs_nadd_104,
							nfs_nadd_113,
							nfs_nadd_115,
							nfs_nadd_901,
							nfs_trades,
							nfs_trades_archive,
							nfs_trades_raw,
							reconcile_run_date,
							rep_comm_rr_level_0,
							rep_comm_rr_level_a,
							rep_comm_rr_level_b,
							rep_comm_rr_trades,
							rep_comm_rr_trades_adj,
							rep_reports,
							sls_sales_reps,
							slx_bmap,
							slx_codemap,
							slx_temp,
							tdw_proc_process_status,
							tmp_cust,
							tmp_map,
							tmp_mry_cmpl_temp,
							tmp_mry_cmpl_trades,
							tmp_pdy,
							tmp_sls_sales_reps,
							tmp_subacct,
							tmp_tradeware,
							tmp_users,
							users,
							user_roles,
							var_global_parameters,
							var_lookup_values,
							z_int_clnt_clients";


$result = mysql_query($str_query) or die (mysql_error());

shell_exec("e:\\backup\\restore_db_devhouse.bat");
/*
DROP TABLE `arc_comm_rr`, `atd_todo_list`, `bkup_users`, `brk_brokerage_months`, `carol_test`, `chk_chek_payments_etc`, `ctrl_control_number`, `holidays`, `int_clnt_clients`, `log_emails`, `mgmt_reports_creation`, `mgmt_sup_report_views`, `mry_comm_rr`, `mry_comm_rr_level_0`, `mry_comm_rr_level_a`, `mry_comm_rr_level_b`, `mry_comm_rr_trades`, `mry_dos_commission`, `mry_nfs_nadd`, `nfs_delta_nadd`, `nfs_delta_nadd_2x0`, `nfs_delta_nadd_2x1`, `nfs_delta_nadd_2x2`, `nfs_delta_nadd_2x3`, `nfs_delta_nadd_101`, `nfs_delta_nadd_102`, `nfs_delta_nadd_103`, `nfs_delta_nadd_104`, `nfs_delta_nadd_113`, `nfs_delta_nadd_115`, `nfs_delta_nadd_901`, `nfs_nadd`, `nfs_nadd_2x0`, `nfs_nadd_2x1`, `nfs_nadd_2x2`, `nfs_nadd_2x3`, `nfs_nadd_101`, `nfs_nadd_102`, `nfs_nadd_103`, `nfs_nadd_104`, `nfs_nadd_113`, `nfs_nadd_115`, `nfs_nadd_901`, `nfs_trades`, `nfs_trades_archive`, `nfs_trades_raw`, `reconcile_run_date`, `rep_comm_rr_level_0`, `rep_comm_rr_level_a`, `rep_comm_rr_level_b`, `rep_comm_rr_trades`, `rep_comm_rr_trades_adj`, `rep_reports`, `sls_sales_reps`, `slx_bmap`, `slx_codemap`, `slx_temp`, `tdw_proc_process_status`, `tmp_cust`, `tmp_map`, `tmp_mry_cmpl_temp`, `tmp_mry_cmpl_trades`, `tmp_pdy`, `tmp_sls_sales_reps`, `tmp_subacct`, `tmp_tradeware`, `tmp_users`, `users`, `user_roles`, `var_global_parameters`, `var_lookup_values`, `z_int_clnt_clients`;
*/	 

?>