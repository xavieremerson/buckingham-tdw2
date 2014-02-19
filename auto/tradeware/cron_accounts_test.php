<?
ini_set('max_execution_time', 3600);
?>

<?
//This is a once a day process in the morning.
include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 

echo "<pre>";

//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."\n";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!"."\n";
		echo "Not exiting!"."\n";
		//exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!"."\n";
	}
  echo "<br>Proceeding after holiday/weekend check....";
//====================================================================================================

echo "\n";
echo "**********************************************************************\n";
echo "*                        DAILY PROCESS                               *\n";
echo "**********************************************************************\n";
echo "Current Date/Time: [".date("l, m-d-Y h:i a")."]\n\n";
echo "\n";


//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CAN USE THIS AS A POST FILE UPLOAD REDIRECT

$trade_date_to_process = previous_business_day();
ydebug('trade_date_to_process',$trade_date_to_process);

if (file_exists($download_location.$trade_date_to_process."\\NAMED.DAT")) {
echo "Processing begins.... file NAMED.DAT found.\n<br>";


							//====================================================================================
							//====================================================================================  
							//Synch up the clients list with DOS
							//include('proc_synch_clients_inc.php');
							//#include('proc_synch_clients_delta_inc.php');



							//#include('nameadd_u_delta.php');
							//#$str_sql_insert = "INSERT INTO tdw_proc_process_status(proc_process, proc_date, proc_status,proc_timestamp) values('Accounts Delta','".$trade_date_to_process."',1, now())";
							//#$result_insert = mysql_query($str_sql_insert) or die (mysql_error());
							
							//Create an array of client codes for lookup
							$qry_adv_code = "select clnt_name, clnt_code, clnt_alt_code from int_clnt_clients";
							$result_adv_code = mysql_query($qry_adv_code) or die (tdw_mysql_error($qry_adv_code));
							$arr_adv_code = array();
							$arr_adv_name = array();
							while ( $row_adv_code = mysql_fetch_array($result_adv_code) ) 
							{
								$arr_adv_code[strtoupper(trim($row_adv_code["clnt_code"]))] = strtoupper(trim($row_adv_code["clnt_alt_code"]));
								$arr_adv_name[strtoupper(trim($row_adv_code["clnt_code"]))] = strtoupper(trim($row_adv_code["clnt_name"]));
							}
							//show_array($arr_adv_code);
							

							$arr_alert_adv_code = array();
							$count_alert_adv_code = 0;
							$count_accounts_pending = 0;
							$count_accounts_new_transmitted = 0;
							
							
							//INITIATING THE CREATION OF ADD FILE.
							$output_filename = "XADD_FILE_".date('Ymd').".txt";
							$fp = fopen($output_filename, "w");
							
							//Header
							$string = "BUCKCAP   ".date("mdY")."  ". date("His").str_pad("",22," ",1)."NAME AND ADDRESS ADD FILE"."\n"; //."<br>"
							fputs ($fp, $string);

							//============================================================================================================
							// THESE ARE ACCOUNTS WHICH WERE NOT PROCESSED DUE TO MISSING CODES
							//============================================================================================================
							//Create ADD FILE for accounts which were created but not processed because of missing Tradeware Code.
							 $qry_acct_not_processed = "SELECT a.*
																					FROM nfs_nadd a
																					LEFT JOIN nfs_nadd_processed b ON a.nadd_auto_id = b.nadd_auto_id
																					WHERE b.nadd_auto_id IS NULL 
																					AND a.nadd_branch = 'PDY'
																					AND a.nadd_advisor NOT LIKE '&%'
																					ORDER BY a.nadd_full_account_number";
							
								$result_acct_not_processed = mysql_query($qry_acct_not_processed) or die(tdw_mysql_error($qry_acct_not_processed));
								while($row_acct_not_processed = mysql_fetch_array($result_acct_not_processed)) {
										
										 //show_array($row_acct_not_processed);
										 //echo trim($row_acct_not_processed["nadd_advisor"]) . "|" . $arr_adv_name[trim($row_acct_not_processed["nadd_advisor"])] . "<br>";
										 
										 if ($arr_adv_code[strtoupper(trim($row_acct_not_processed["nadd_advisor"]))] != "" AND $arr_adv_code[strtoupper(trim($row_acct_not_processed["nadd_advisor"]))] != "INACTIVE" ) {
										 
										 		//insert into processed area
												//#$sql_insert_processed = "INSERT INTO nfs_nadd_processed select * from nfs_nadd where nadd_full_account_number = '".$row_acct_not_processed["nadd_full_account_number"]."'";
												//echo $sql_insert_processed;
												//#$result_insert_processed = mysql_query($sql_insert_processed) or die (tdw_mysql_error($sql_insert_processed));												
												
												//delete from delta area so the rest are identified as change
												//#$sql_delete_delta = "DELETE FROM nfs_delta_nadd where nadd_full_account_number = '".$row_acct_not_processed["nadd_full_account_number"]."'";
												//echo $sql_delete_delta."<br><br>";
												//#$result_delete_delta = mysql_query($sql_delete_delta) or die (tdw_mysql_error($sql_delete_delta));												
												
												$count_accounts_new_transmitted = $count_accounts_new_transmitted + 1;
												
												$string =  str_pad($row_acct_not_processed["nadd_full_account_number"],9," ",1).
																	 str_pad("",3," ",1).
																	 "A".
																	 str_pad("",26," ",1).
																	 "2". //Put 1 for Principal and 2 for Agency
																	 str_pad($arr_adv_code[strtoupper(trim($row_acct_not_processed["nadd_advisor"]))],10," ",1).
																	 str_pad("",2," ",1).
																	 strtoupper(trim($row_acct_not_processed["nadd_rr_owning_rep"]))."\n"; // Delete Account should be added later
												fputs ($fp, $string);
												$string =  str_pad($row_acct_not_processed["nadd_full_account_number"],9," ",1).
																	 str_pad("",3," ",1).
																	 "D".
																	 str_pad(substr(trim($row_acct_not_processed["nadd_short_name"]),0,32),32," ",1).
																	 str_pad(substr(trim($row_acct_not_processed["nadd_address_line_2"]),0,32),32," ",1).
																	 str_pad(substr(trim($row_acct_not_processed["nadd_address_line_3"]),0,32),32," ",1)."\n";  //."<br>"
												fputs ($fp, $string);

										 } else {
													if ($arr_adv_name[strtoupper(trim($row_acct_not_processed["nadd_advisor"]))] != "") {
													$count_accounts_pending = $count_accounts_pending + 1;
														if (!in_array(trim($row_acct_not_processed["nadd_advisor"]),$arr_alert_adv_code)) {
															$arr_alert_adv_code[$arr_adv_name[trim($row_acct_not_processed["nadd_advisor"])]] = trim($row_acct_not_processed["nadd_advisor"]);
															$count_alert_adv_code = $count_alert_adv_code + 1;	
															//echo trim($row_acct_not_processed["nadd_advisor"]) . "|" . $arr_adv_name[trim($row_acct_not_processed["nadd_advisor"])] ."|". $row_acct_not_processed["nadd_full_account_number"]. "<br>";
														}									 
										 			}
										 //This is where the back-office needs to be alerted for missing Tradeware Client Code
										 }	

								}
								
							  echo "Accounts transmitted to Tradeware = [".$count_accounts_new_transmitted."]<br>";
								echo "Accounts pending transmission to Tradeware due to missing Client Code = [".$count_accounts_pending."]<br>";

							$string =  "***EOF***"; //.chr(13);
							fputs ($fp, $string);
							
							fclose($fp);
							
							//============================================================================================================
							//============================================================================================================
							
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//With the data in nfs_delta_add, figure out creation of change and add files for Tradeware
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//Create CHANGE FILE
																			
							$output_filename = "XCHANGE_FILE_".date('Ymd').".txt";
							$fp = fopen($output_filename, "w");
							
							//Header
							$string = "BUCKCAP   ".date("mdY")."  ". date("His").str_pad("",22," ",1)."NAME AND ADDRESS CHANGE FILE"."\n"; //."<br>"
							fputs ($fp, $string);
							
							$query_acct = "SELECT a. * 
															FROM nfs_delta_nadd a
															LEFT JOIN nfs_nadd_processed b ON a.nadd_auto_id = b.nadd_auto_id
															WHERE b.nadd_auto_id IS NOT NULL 
															AND a.nadd_branch = 'PDY'
															AND a.nadd_advisor NOT LIKE '&%'
															ORDER BY a.nadd_full_account_number";
														
							$result_acct = mysql_query($query_acct) or die(tdw_mysql_error($query_acct));
							
							while($row_acct = mysql_fetch_array($result_acct)) {
								 
										 //check if a mapped code for tradeware exists
										 if ($arr_adv_code[strtoupper(trim($row_acct["nadd_advisor"]))] != "" AND $arr_adv_code[strtoupper(trim($row_acct["nadd_advisor"]))] != "INACTIVE") {		
												$string =  str_pad($row_acct["nadd_full_account_number"],9," ",1).
																	 str_pad("",3," ",1).
																	 "A".
																	 str_pad("",26," ",1).
																	 "2". //Put 1 for Principal and 2 for Agency
																	 str_pad($arr_adv_code[strtoupper(trim($row_acct["nadd_advisor"]))],10," ",1).
																	 str_pad("",2," ",1).
																	 strtoupper(trim($row_acct["nadd_rr_owning_rep"]))."\n"; // Delete Account should be added later
												fputs ($fp, $string);
												$string =  str_pad($row_acct["nadd_full_account_number"],9," ",1).
																	 str_pad("",3," ",1).
																	 "D".
																	 str_pad(substr(trim($row_acct["nadd_short_name"]),0,32),32," ",1).
																	 str_pad(substr(trim($row_acct["nadd_address_line_2"]),0,32),32," ",1).
																	 str_pad(substr(trim($row_acct["nadd_address_line_3"]),0,32),32," ",1)."\n";  //."<br>"
												fputs ($fp, $string);
											}
								}
								
							
							$string =  "***EOF***"; //.chr(13);
							fputs ($fp, $string);
							
							
							fclose($fp);
							
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							
							//*************************************************************************************************************
							//Create config script to be used by batch file.
							//*************************************************************************************************************
							
							/*
							$output_script_filename = "tradeware_ftp_script.txt";
							$fp = fopen($output_script_filename, "w");
							//Put commands in the config file
							fputs ($fp, "cd incoming"."\r\n");
							fputs ($fp, "put ".$tdw_local_location."auto\\tradeware\\"."CHANGE_FILE_".date('Ymd').".txt"."\r\n");
							fputs ($fp, "put ".$tdw_local_location."auto\\tradeware\\"."ADD_FILE_".date('Ymd').".txt"."\r\n");
							fputs ($fp, "pwd"."\r\n");
							fputs ($fp, "ls"."\r\n");
							fclose($fp);
							
							shell_exec("tradeware_put.bat");
							*/
							
							$email_log = '<style type="text/css">
														<!--
														.datatable {
															font-family: Verdana, Arial, Helvetica, sans-serif;
															font-size: 11px;
															color: #000000;
															font-style: normal;
															border-top-color: #000000;
															border-right-color: #0000FF;
															border-bottom-color: #000000;
															border-left-color: #0000FF;
															border-style: solid;
															border-width: 1px;
															border-collapse: collapse;
														}
														.notetext {
															font-family: Verdana, Arial, Helvetica, sans-serif;
															font-size: 12px;
															color: #0000ff;
															font-style: normal;
														}

														-->
														</style>';
							
							$email_log .= '<table width="600"><tr><td><p class="notetext" align="justify">The following table shows the list of clients, the accounts for which have not been uploaded to Tradeware because of missing Tradeware Client Code. <br />Please update the Client Master in TDW with appropriate Tradeware Codes and the accounts will be uploaded to Tradeware during the next upload which is scheduled for 7:00AM on the next business day.</p></td></tr></table>';
							
							  $email_log .=  "Accounts transmitted to Tradeware = [".$count_accounts_new_transmitted."]<br>";
								$email_log .=  "Accounts pending transmission to Tradeware due to missing Client Code = [".$count_accounts_pending."]<br>";

							$email_log .= "<table border='1' width='600' class='datatable'>";
							$email_log .= "<tr bgcolor='#CCCCCC'><td width='85'><strong>Client Code</strong></td><td width='85'><strong>Client Name</strong></td></tr>";
							
							//Sort the Codes in the Array
							ksort($arr_alert_adv_code);
							
							$count_row_table = 0;
							
							foreach($arr_alert_adv_code as $clntname => $clntcode) {
								
								if ( $count_row_table % 2 == 0 ) {
								$bkclr = " bgcolor = '#E4E4E4'";
								} else {
								$bkclr = "";
								}
								
								$email_log .= "<tr".$bkclr."><td>".$clntcode."</td><td>".$clntname."</td></tr>";
								$count_row_table = $count_row_table + 1;
							}
							$email_log .= "</table>";
							
							//EMAIL ROUTINE FOR SENDING THIS ALERT
							$alert_email = 1;
							if ($alert_email == 1) {
								
								echo $email_log;
								//create mail to send
								$html_body .= zSysMailHeader("");
								$html_body .= $email_log;
								$html_body .= zSysMailFooter ();
								
								$subject = "TDW Alert: TRADEWARE Account Upload (".date('m-d-Y').")";
								$text_body = $subject;
								
															zSysMailer("pprasad@centersys.com",    "", $subject, $html_body, $text_body, "") ;
															//zSysMailer("backoffice@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
															//zSysMailer("tsutera@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
							}


} else {
echo "FILE NOT FOUND! NAMED.DAT MISSING. \n<br>";
exit;
}
echo "</pre>";
?>