<?
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//Get the Name/Address information into Memory Table for lookup purposes
$result_nadd_flush = mysql_query("truncate table mry_nfs_nadd") or die (mysql_error());
$result_nadd_populate = mysql_query("insert into mry_nfs_nadd select * from nfs_nadd") or die (mysql_error());

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (mysql_error());
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
}

$email_log .= '<hr align="left" width="600" size="2" noshade color="#0099FF"><strong>ALERT 1: Advisors with Multiple Reps.</strong><hr align="left" width="600" size="2" noshade color="#0099FF">'."Rep. Numbers with fewer occurences within a client have been displayed in <b>BOLD</b>.<br><br>\n";
//Get all Advisors where branch = PDY
//fields are nadd_firm  nadd_branch  nadd_account_number  nadd_full_account_number  nadd_advisor  nadd_short_name  nadd_rr_owning_rep  nadd_rr_exec_rep  nadd_num_address_lines  nadd_address_line_1  nadd_address_line_2  nadd_address_line_3  nadd_address_line_4  nadd_address_line_5  nadd_address_line_6  
$query_adv = "SELECT distinct(nadd_advisor)
									FROM mry_nfs_nadd
									WHERE length(nadd_advisor) = 4
									AND nadd_advisor NOT LIKE '&%'
									AND nadd_advisor NOT LIKE 'XXXX'
								  AND nadd_branch = 'PDY'
									ORDER BY nadd_advisor";
//xdebug('query_adv',$query_adv);
$result_adv = mysql_query($query_adv) or die(mysql_error());
$countval = 0;
while($row_adv = mysql_fetch_array($result_adv))
{
	//xdebug('row_adv["nadd_advisor"]',$row_adv["nadd_advisor"]);
	//for each advisor get the min, max of the rr vals
		$query_adv_minmax = "SELECT nadd_advisor, min(nadd_rr_owning_rep) as rrmin, max(nadd_rr_owning_rep) as rrmax
													FROM mry_nfs_nadd
													WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
													AND nadd_branch = '"."PDY"."'
													GROUP BY nadd_advisor";
													
		//xdebug("query_adv_minmax",$query_adv_minmax);
		$result_adv_minmax = mysql_query($query_adv_minmax) or die(mysql_error());
		while($row_adv_minmax = mysql_fetch_array($result_adv_minmax))
		{
			$advisor_code = $row_adv_minmax["nadd_advisor"];
			$rrmin = $row_adv_minmax["rrmin"];
			$rrmax = $row_adv_minmax["rrmax"]; 
			if ($rrmin == $rrmax) {
			//do nothing
				//xdebug("Advisor OK",$advisor_code);
			} else {
				$countval = $countval + 1;
				echo $advisor_code. " needs processing<br>";
				//get count of all RRs for this Advisor
					$query_adv_rrcount = "SELECT count(distinct(nadd_rr_owning_rep)) as numreps
																FROM mry_nfs_nadd
																WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																AND nadd_branch = '"."PDY"."'";
					//xdebug("query_adv_minmax",$query_adv_minmax);
					$result_adv_rrcount = mysql_query($query_adv_rrcount) or die(mysql_error());
					while($row_adv_rrcount = mysql_fetch_array($result_adv_rrcount))
					{
					xdebug("numreps",$row_adv_rrcount["numreps"]);
					$numreps = $row_adv_rrcount["numreps"];
						if ($numreps > 2) {
								$email_log .= $row_adv["nadd_advisor"]. "[".$arr_clients[$row_adv["nadd_advisor"]]."] has more than 2 reps. Please investigate and correct.<br>\n<hr>";
								//show the details here
								$query_display_more2 = "SELECT nadd_advisor, nadd_short_name, nadd_full_account_number, nadd_rr_owning_rep
																				FROM mry_nfs_nadd
																				WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																				AND nadd_branch = '"."PDY"."' ORDER BY nadd_rr_owning_rep, nadd_full_account_number";
								$result_display_more2 = mysql_query($query_display_more2) or die(mysql_error());
								$email_log .= "<table width='400'>";
								$email_log .= "<tr><td><u>Client Code</u></td><td><u>Account Short Name</u></td><td><u>Account Number</u></td><td><u>Rep #</u></td></tr>\n";
								while($row_display_more2 = mysql_fetch_array($result_display_more2)){
									//$count_rrmin = $row_count_rrmin["count_rrmin"];
									//xdebug("count_rrmin",$count_rrmin);
									$email_log .= "<tr><td>".$row_display_more2["nadd_advisor"]."</td><td> [".$row_display_more2["nadd_short_name"]."]</td><td>".$row_display_more2["nadd_full_account_number"]."</td><td><b>".$row_display_more2["nadd_rr_owning_rep"]."</b></td></tr>\n";
								}
								$email_log .= "</table><hr>";
								
						} else {
						//Figure out what to output and send via email
								$query_count_rrmin = "SELECT count(nadd_rr_owning_rep) as count_rrmin
																			FROM mry_nfs_nadd
																			WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																			AND nadd_rr_owning_rep = '".$rrmin."'
																			AND nadd_branch = '"."PDY"."'";
								$result_count_rrmin = mysql_query($query_count_rrmin) or die(mysql_error());
								while($row_count_rrmin = mysql_fetch_array($result_count_rrmin)){
								$count_rrmin = $row_count_rrmin["count_rrmin"];
								xdebug("count_rrmin",$count_rrmin);
								}
								$query_count_rrmax = "SELECT count(nadd_rr_owning_rep) as count_rrmax
																			FROM mry_nfs_nadd
																			WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																			AND nadd_rr_owning_rep = '".$rrmax."'
																			AND nadd_branch = '"."PDY"."'";
								$result_count_rrmax = mysql_query($query_count_rrmax) or die(mysql_error());
								while($row_count_rrmax = mysql_fetch_array($result_count_rrmax)){
								$count_rrmax = $row_count_rrmax["count_rrmax"];
								xdebug("count_rrmax",$count_rrmax);
								}
								
								//Get the data to display in email
								//fields are nadd_firm  nadd_branch  nadd_account_number  nadd_full_account_number  nadd_advisor  nadd_short_name  nadd_rr_owning_rep  nadd_rr_exec_rep  nadd_num_address_lines  nadd_address_line_1  nadd_address_line_2  nadd_address_line_3  nadd_address_line_4  nadd_address_line_5  nadd_address_line_6  
												
								$email_log .= "Reps. found for Client Code (".$row_adv["nadd_advisor"].") : <u>".$rrmin."</u> and <u>".$rrmax."</u><br>\n";
								
								if ($row_adv["nadd_advisor"] == 'GART') {
										$email_log .= "Results temporarily hidden for ". $row_adv["nadd_advisor"]."<br>\n";
								} else {
										$email_log .= "<table width='400'>";
										$email_log .= "<tr><td><u>Client Code</u></td><td><u>Account Short Name</u></td><td><u>Account Number</u></td><td><u>Rep #</u></td></tr>\n";
										if ($count_rrmin <= $count_rrmax) {
														$query_display_rrmin = "SELECT nadd_advisor, nadd_short_name, nadd_full_account_number, nadd_rr_owning_rep
																										FROM mry_nfs_nadd
																										WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																										AND nadd_rr_owning_rep = '".$rrmin."'
																										AND nadd_branch = '"."PDY"."' ORDER BY nadd_full_account_number";
														$result_display_rrmin = mysql_query($query_display_rrmin) or die(mysql_error());
														while($row_display_rrmin = mysql_fetch_array($result_display_rrmin)){
															//$count_rrmin = $row_count_rrmin["count_rrmin"];
															//xdebug("count_rrmin",$count_rrmin);
															$email_log .= "<tr><td>".$row_display_rrmin["nadd_advisor"]."</td><td> [".$row_display_rrmin["nadd_short_name"]."]</td><td>".$row_display_rrmin["nadd_full_account_number"]."</td><td><b>".$row_display_rrmin["nadd_rr_owning_rep"]."</b></td></tr>\n";
														}
														$query_display_rrmax = "SELECT nadd_advisor, nadd_short_name, nadd_full_account_number, nadd_rr_owning_rep
																										FROM mry_nfs_nadd
																										WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																										AND nadd_rr_owning_rep = '".$rrmax."'
																										AND nadd_branch = '"."PDY"."' ORDER BY nadd_full_account_number";
														$result_display_rrmax = mysql_query($query_display_rrmax) or die(mysql_error());
														while($row_display_rrmax = mysql_fetch_array($result_display_rrmax)){
															//$count_rrmax = $row_count_rrmax["count_rrmax"];
															//xdebug("count_rrmax",$count_rrmax);
															$email_log .= "<tr><td>".$row_display_rrmax["nadd_advisor"]."</td><td> [".$row_display_rrmax["nadd_short_name"]."]</td><td>".$row_display_rrmax["nadd_full_account_number"]."</td><td>".$row_display_rrmax["nadd_rr_owning_rep"]."</td></tr>\n";
														}
										} else {
														$query_display_rrmax = "SELECT nadd_advisor, nadd_short_name, nadd_full_account_number, nadd_rr_owning_rep
																										FROM mry_nfs_nadd
																										WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																										AND nadd_rr_owning_rep = '".$rrmax."'
																										AND nadd_branch = '"."PDY"."' ORDER BY nadd_full_account_number";
														$result_display_rrmax = mysql_query($query_display_rrmax) or die(mysql_error());
														while($row_display_rrmax = mysql_fetch_array($result_display_rrmax)){
															//$count_rrmax = $row_count_rrmax["count_rrmax"];
															//xdebug("count_rrmax",$count_rrmax);
															$email_log .= "<tr><td>".$row_display_rrmax["nadd_advisor"]."</td><td> [".$row_display_rrmax["nadd_short_name"]."]</td><td>".$row_display_rrmax["nadd_full_account_number"]."</td><td><b>".$row_display_rrmax["nadd_rr_owning_rep"]."</b></td></tr>\n";
														}
														$query_display_rrmin = "SELECT nadd_advisor, nadd_short_name, nadd_full_account_number, nadd_rr_owning_rep
																										FROM mry_nfs_nadd
																										WHERE nadd_advisor = '".$row_adv["nadd_advisor"]."'
																										AND nadd_rr_owning_rep = '".$rrmin."'
																										AND nadd_branch = '"."PDY"."' ORDER BY nadd_full_account_number";
														$result_display_rrmin = mysql_query($query_display_rrmin) or die(mysql_error());
														while($row_display_rrmin = mysql_fetch_array($result_display_rrmin)){
															//$count_rrmin = $row_count_rrmin["count_rrmin"];
															//xdebug("count_rrmin",$count_rrmin);
															$email_log .= "<tr><td>".$row_display_rrmin["nadd_advisor"]."</td><td> [".$row_display_rrmin["nadd_short_name"]."]</td><td>".$row_display_rrmin["nadd_full_account_number"]."</td><td>".$row_display_rrmin["nadd_rr_owning_rep"]."</td></tr>\n";
														}
										}
										
										$email_log .= "</table><hr>";
								}
						}
						
					}

			
			}
	
		}


}


//============================================================================================================
//ALERT 3: Trades processed with a different RR as the one in the accounts masters
//
$proceed_alert_3 = 0;

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
											$trade_date_to_process = '2006-06-16'; // ENTER DPECIFIC DATE TO PROCESS
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
														
														//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
														// trad_trade_date, trad_account_number, trad_symbol, trad_buy_sell, trad_quantity
														$query_trades_detail = "SELECT * 
																									 FROM mry_comm_rr_trades
																									 WHERE trad_advisor_code = '".$row_get_clients["nadd_advisor"]."'
																									 AND trad_rr != '".$row_get_rr["nadd_rr_owning_rep"]."'
																									 AND trad_is_cancelled != '1'
																									 AND trad_run_date = '".$trade_date_to_process."'";
														
														$result_trades_detail = mysql_query($query_trades_detail) or die(tdw_mysql_error($query_trades_detail));
														while($row_trades_detail = mysql_fetch_array($result_trades_detail)) 
														{
															$str_diff_reps_out .= 
																'<tr>
																	<td colspan="5">Trade Detail => '
																	.format_date_ymd_to_mdy($row_trades_detail["trad_trade_date"])."&nbsp;&nbsp;&nbsp;&nbsp;".$row_trades_detail["trad_account_number"]."&nbsp;&nbsp;&nbsp;&nbsp;".$row_trades_detail["trad_symbol"]."&nbsp;&nbsp;&nbsp;&nbsp;".$row_trades_detail["trad_buy_sell"]."&nbsp;&nbsp;&nbsp;&nbsp;".number_format($row_trades_detail["trad_quantity"],0)." Shrs.".
																	'</td>
																</tr>';
														}
														//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
														
													$str_all_diff_reps = $str_all_diff_reps . $str_diff_reps_out;
													//xdebug ("str_all_diff_reps",$str_all_diff_reps);
													}
											}
									}
				}

if ($str_all_diff_reps != "") {

$final_out_diff_rep = 
			'<hr align="left" width="600" size="2" noshade color="#0099FF">
				<strong>Alert 2: Trades Processed with Different RR (Run Date: '.format_date_ymd_to_mdy($trade_date_to_process).')</strong>			 
			<hr align="left" width="600" size="2" noshade color="#0099FF">
			 <table width="600" border="1" cellpadding="4" cellspacing="0" bordercolor="#333333">
				<tr bgcolor="#F7F7F7">
					<td><b>Client Code</b></td>
					<td><b>Client Name</b></td>
					<td><b>RR1</b></td>
					<td><b>RR2</b></td>
					<td><b>Trades processed with.</b></td>
				</tr>' . $str_all_diff_reps . '</table>';

$email_log .= "<br>". $final_out_diff_rep;
$proceed_alert_3 = 1;
}
//============================================================================================================

//EMAIL ROUTINE FOR SENDING THIS ALERT
	if ($countval > 0 OR $proceed_alert_3 == 1) {
		echo $email_log;
		//create mail to send
		$html_body .= zSysMailHeader("");
		$html_body .= $email_log;
		$html_body .= zSysMailFooter ();
		
		$subject = "TDW Alert: (".date('m-d-Y').")";
		$text_body = $subject;
		
									zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
									//zSysMailer("lkarp@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
									//zSysMailer("backoffice@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
	}

								
?>