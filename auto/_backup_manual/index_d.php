<?
ini_set('max_execution_time', 3600);
?>
<?
include('../../includes/functions.php');
?>


<?
$log_data = "";
//Backup the entire database (warehouse to the filesystem for restoration if required.
//Done on a daily basis at 3:00AM in the morning (all seven days), files overwritten

//ALL VARIABLES USED HERE.
$backup_folder_temp 	= "d:\\tdw\\tdw\\auto\\backup_tdw\\";  //(Backup Location)trailing slash required
$backup_folder_perm 	= "e:\\backup\\";  //(Backup Location)trailing slash required
$archive_location_perm = "\\\\bucksnapNY\\SHARE1\\TDWbackup\\";
$db_hostname		= "localhost";
$db_user 				= "newadmin";
$db_pw 					= "newpassword";
$db_database		= "warehouse";
$loc_mysqldump	= "D:\\Program Files\\MySQL\\MySQL Server 5.0\\bin\\"; //trailing slash required
$temp_dump_file	= "d_file.dump";
$list_of_tables = "acv_analyst_coverage arc_comm_rr atd_todo_list brk_brokerage_months chk_chek_payments_etc chk_totals_level_a ctrl_control_number cvr_coverage_universe help_data holidays int_clnt_clients int_clnt_payout_rate lkup_clients lkup_rrep lkup_symbols log_emails mgmt_reports_creation mgmt_reports_notes mgmt_sup_report_views mry_comm_rr mry_comm_rr_level_0 mry_comm_rr_level_a mry_comm_rr_level_b mry_comm_rr_trades mry_dos_commission mry_nfs_nadd mry_tmp_process nfs_delta_nadd nfs_nadd nfs_nadd_processed nfs_nadd_tmp_tradeware oac_emp_accounts ofac_add_list ofac_alt_list ofac_sdn_list otd_emp_trades_external oth_other_trades reconcile_run_date rep_comm_rr_level_0 rep_comm_rr_level_a rep_comm_rr_level_b rep_comm_rr_trades rep_comm_rr_trades_adj rep_comm_rr_trades_test rep_reports sls_sales_reps sls_sales_reps_bkup_20061107 slx_bmap slx_codemap slx_temp tdw_proc_process_status tmp_cust tmp_mry_cmpl_temp tmp_mry_cmpl_trades tmp_subacct tmp_tradeware user_roles var_global_parameters var_lookup_values yrt_yearly_total_lookup";

$command = "\"".$loc_mysqldump."mysqldump"."\""." -v -c -h ".$db_hostname." -u".$db_user." -p".$db_pw." -r ".$backup_folder_perm.$temp_dump_file." --databases ".$db_database." --tables ".$list_of_tables;

//echo $command;
//exit;

//$log_data .= $command."<br>";
$log_data .= date('m-d-Y h:i:s a')."<br>";
$time_start=getmicrotime(); 
$log_data .= "Dumping data to file...<br>";
shell_exec($command);
$log_data .= "<b>Time taken to process = </b>". sprintf("%01.7f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						


//Write log data to file
$log_data .= date('m-d-Y h:i:s a')."<br>";
$time_start=getmicrotime(); 
$log_data .= "Writing log file...<br>";
write_to_file($backup_folder_perm,"log_d_".date('l').".html",$log_data);
$log_data .= "<b>Time taken to process = </b>". sprintf("%01.7f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						

//show the log data as well
echo $log_data;


?>