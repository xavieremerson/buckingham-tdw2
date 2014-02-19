<?
/* This file includes all source files relating to alerts run on a daily basis */
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//Types of alerts

// 1: Advisors with multiple RRs
//  This is in validate_nadd_inc currently in production


//2. RR1 not equal to RR2 in the accounts master


////3. Trades processed with a different RR as the one in the accounts masters
//
//Create Lookup Array of Client Code / Client Name
	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}

//Get the Name/Address information into Memory Table for lookup purposes
$result_nadd_flush = mysql_query("truncate table mry_nfs_nadd") or die (mysql_error());
$result_nadd_populate = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (mysql_error());

//Create an array of account names and advisor code for lookup.
	$qry_acct_adv = "select nadd_full_account_number, nadd_advisor from mry_nfs_nadd";
	$result_acct_adv = mysql_query($qry_acct_adv) or die (tdw_mysql_error($qry_acct_adv));
	$arr_acct_adv = array();
	while ( $row_acct_adv = mysql_fetch_array($result_acct_adv) ) 
	{
		$arr_acct_adv[strtoupper(trim($row_acct_adv["nadd_full_account_number"]))] = $row_acct_adv["nadd_advisor"];
	}

//select all clients from nfs_nadd
$qry_get_clients = "SELECT
			distinct(nadd_advisor) from mry_nfs_nadd 
			WHERE nadd_branch = 'PDY'
			AND nadd_advisor not like '&%'
			ORDER BY nadd_advisor";


//xdebug("qry_get_clients",$qry_get_clients);

$result_get_clients = mysql_query($qry_get_clients) or die (tdw_mysql_error($qry_get_clients));
$str_all_diff_reps = "";
while ( $row_get_clients = mysql_fetch_array($result_get_clients) )
				{
					
					//echo $row_get_clients["nadd_advisor"]."<br>";
					//get the rr values for the client
					$qry_get_rr = "SELECT max(nadd_rr_owning_rep) as nadd_rr_owning_rep, max(nadd_rr_exec_rep) as nadd_rr_exec_rep
															from mry_nfs_nadd 
															WHERE nadd_branch = 'PDY'
															AND nadd_advisor = '".$row_get_clients["nadd_advisor"]."'  
															ORDER BY nadd_advisor";

					//xdebug("qry_get_rr",$qry_get_rr);
					
					$result_get_rr = mysql_query($qry_get_rr) or die (tdw_mysql_error($qry_get_rr));
					while ( $row_get_rr = mysql_fetch_array($result_get_rr) )
									{
									//echo $row_get_rr["nadd_rr_owning_rep"]."<br>";
									//echo $row_get_rr["nadd_rr_exec_rep"]."<br>";
											//find trades that do not have these rr values and get the rr value
											
											$str_diff_reps = "";
											$trade_date_to_process = previous_business_day();
											$query_trades = "SELECT trad_rr 
																			 FROM mry_comm_rr_trades
																			 WHERE trad_advisor_code = '".$row_get_clients["nadd_advisor"]."'
																			 AND trad_rr != '".$row_get_rr["nadd_rr_owning_rep"]."'
																			 AND trad_is_cancelled != '1'
																			 AND trad_run_date = '".$trade_date_to_process."' GROUP BY trad_rr";
											//xdebug ("query_trades",$query_trades);
											$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
											$count_found = mysql_num_rows($result_trades);
											//xdebug ("count_found",$count_found);
											if ($count_found > 0) {
													//$countval = 1;
													while($row_trades = mysql_fetch_array($result_trades))
													{
														//do something
														$str_diff_reps = $row_trades["trad_rr"].",".$str_diff_reps;
														//$countval = $countval + 1;
													}
													
													if ($str_diff_reps != '') {
													//xdebug ("str_diff_reps",$str_diff_reps);
													$str_diff_reps_out = 
														'<tr>
															<td>'.$row_get_clients["nadd_advisor"].'</td>
															<td>'.$arr_clients[$row_get_clients["nadd_advisor"]].'</td>
															<td>'.$row_get_rr["nadd_rr_owning_rep"].'</td>
															<td>'.$row_get_rr["nadd_rr_exec_rep"].'</td>
															<td>'.$str_diff_reps.'</td>
														</tr>';
													$str_all_diff_reps = $str_all_diff_reps . $str_diff_reps_out;
													//xdebug ("str_all_diff_reps",$str_all_diff_reps);
													}
											}
									}
				}

if ($str_all_diff_reps != "") {

$final_out_diff_rep = 
			'<table width="600" border="1" cellpadding="4" cellspacing="0" bordercolor="#333333">
				<caption align="left">
				<strong>Trades Processed with Different RR (Run Date: '.format_date_ymd_to_mdy($trade_date_to_process).')</strong>
				</caption>
				<tr bgcolor="#F7F7F7">
					<td><b>Client Code</b></td>
					<td><b>Client Name</b></td>
					<td><b>RR1</b></td>
					<td><b>RR2</b></td>
					<td><b>Trades processed with.</b></td>
				</tr>' . $str_all_diff_reps . '</table>';

//xdebug ("str_all_diff_reps",$str_all_diff_reps);

}

echo $final_out_diff_rep;
?>



<?

//4. Trades in accounts which show as closed in the NFS Accounts Master



//Get the Name/Address information into Memory Table for lookup purposes


								if ($countval > 0) {
									echo $email_log;
									//create mail to send
									$html_body .= zSysMailHeader("");
									$html_body .= $email_log;
									$html_body .= zSysMailFooter ();
									
									$subject = "TDW Alert: (".date('m-d-Y').") : ".$countval. " Clients with possible RR mismatch.";
									$text_body = $subject;
									
									//zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) 
									//zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
									//zSysMailer("lkarp@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
								}

								
?>