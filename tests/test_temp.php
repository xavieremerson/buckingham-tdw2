<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

	// get client details from table and ppopulate other table
	$query_sel_client =  "SELECT clnt_auto_id  clnt_code,
													clnt_alt_code,
													clnt_name,
													clnt_rr1,
													clnt_rr2,
													clnt_reps_and_or,
													clnt_reps_or,
													clnt_reps_special,
													clnt_trader,
													clnt_timestamp,
													clnt_isactive 
												FROM int_clnt_clients
												WHERE length(comm_advisor_name) > 4
												AND comm_advisor_code != 'BUCK' and comm_advisor_code != 'FRIS'
												ORDER BY comm_advisor_name, comm_advisor_code";
	$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
	while($row_sel_client = mysql_fetch_array($result_sel_client)) {
	$arr_final_list_clients[$row_sel_client["comm_advisor_code"]] = $row_sel_client["comm_advisor_name"];
	}

		
	















exit;

$arr_final_list_clients = array();
	$query_sel_client = "SELECT comm_advisor_code, comm_advisor_name 
												FROM lkup_clients
												WHERE length(comm_advisor_name) > 4
												AND comm_advisor_code != 'BUCK' and comm_advisor_code != 'FRIS'
												ORDER BY comm_advisor_name, comm_advisor_code";
	$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
	while($row_sel_client = mysql_fetch_array($result_sel_client)) {
	$arr_final_list_clients[$row_sel_client["comm_advisor_code"]] = $row_sel_client["comm_advisor_name"];
	}
	
	$qry_checks = "SELECT DISTINCT (a.chek_advisor), b.clnt_name
									FROM chk_chek_payments_etc a, int_clnt_clients b
									WHERE a.chek_advisor = b.clnt_code
									AND length( trim( clnt_rr1 ) ) >0
									AND a.chek_advisor NOT IN ('TRA2', 'MISC')";
								 
	$result_checks = mysql_query($qry_checks) or die(mysql_error());
	while($row_checks = mysql_fetch_array($result_checks)) {
	$arr_final_list_clients[$row_checks["chek_advisor"]] = $row_checks["clnt_name"];
	}
	
	ksort($arr_final_list_clients);

	$count_row_client = 0;
foreach ($arr_final_list_clients as $val_code=>$val_name) {
    echo 'dc ['.$count_row_client.'] = "'.$val_code.'^'.trim($val_name).'"'.";\n";
		$count_row_client = $count_row_client + 1;
	}

	//show_array($arr_final_list_clients);

?>
