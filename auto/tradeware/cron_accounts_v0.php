<?
ini_set('max_execution_time', 3600);
?>

<?
//This is a once a day process in the morning.
include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 


//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!";
		exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!";
	}
  echo "<br>Proceeding after holiday/weekend check....";
//====================================================================================================





echo "<pre>";
echo "**********************************************************************\n";
echo "*                        DAILY PROCESS                               *\n";
echo "**********************************************************************\n";
echo "Current Date/Time: [".date("l, m-d-Y h:i a")."]\n\n";
echo "<pre>";

//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CAN USE THIS AS A POST FILE UPLOAD REDIRECT
$trade_date_to_process = previous_business_day();
ydebug('trade_date_to_process',$trade_date_to_process);

if (file_exists($download_location.$trade_date_to_process."\\NAMED.DAT")) {
echo "Processing begins.... file NAMED.DAT found.\n<br>";

							//====================================================================================
							//====================================================================================  
							//Synch up the clients list with DOS
							//include('proc_synch_clients_inc.php');
							include('proc_synch_clients_delta_inc.php');



							include('nameadd_u_delta.php');
							$str_sql_insert = "INSERT INTO tdw_proc_process_status(proc_process, proc_date, proc_status,proc_timestamp) values('Accounts Delta','".$trade_date_to_process."',1, now())";
							$result_insert = mysql_query($str_sql_insert) or die (mysql_error());
							
							//Create an array of client codes for lookup
							$qry_adv_code = "select clnt_code, clnt_alt_code from int_clnt_clients";
							$result_adv_code = mysql_query($qry_adv_code) or die (tdw_mysql_error($qry_adv_code));
							$arr_adv_code = array();
							while ( $row_adv_code = mysql_fetch_array($result_adv_code) ) 
							{
								$arr_adv_code[strtoupper(trim($row_adv_code["clnt_code"]))] = strtoupper(trim($row_adv_code["clnt_alt_code"]));
							}
							
							
							//============================================================================================================
							//With the data in nfs_delta_add, figure out creation of change and add files for Tradeware
							//============================================================================================================
							//Create ADD FILE
																			
							$output_filename = "ADD_FILE_".date('Ymd').".txt";
							$fp = fopen($output_filename, "w");
							
							//Header
							$string = "BUCKCAP   ".date("mdY")."  ". date("His").str_pad("",22," ",1)."NAME AND ADDRESS ADD FILE"."\n"; //."<br>"
							fputs ($fp, $string);
							
							//Clients List
							/*
							$query_acct = "SELECT * 
														FROM mry_nfs_nadd 
														where nadd_full_account_number like 'PDS%' or nadd_full_account_number like 'PDZ%'
														order by nadd_full_account_number";
							*/
							$query_acct = "SELECT * 
														FROM nfs_delta_nadd 
														order by nadd_full_account_number";
														//where nadd_full_account_number like 'PDY%'
														
							$result_acct = mysql_query($query_acct) or die(tdw_mysql_error($query_acct));
							
							while($row_acct = mysql_fetch_array($result_acct)) {
								 
								 //first, check if this is a new account, if it is, it will not exist in the nfs_nadd table
									$query_acct_check = "SELECT * 
																			FROM nfs_nadd
																			where nadd_full_account_number = '".$row_acct["nadd_full_account_number"]."'";
									//xdebug("query_acct_check",$query_acct_check);							
									$result_acct_check = mysql_query($query_acct_check) or die(tdw_mysql_error($query_acct_check));	 
									$result_acct_count = mysql_num_rows($result_acct_check) or die(tdw_mysql_error($query_acct_check));	 
								 
									if ($result_acct_count == 0) {
										 //check if a mapped code for tradeware exists
										 if ($arr_adv_code[strtoupper(trim($row_acct["nadd_advisor"]))] != "") {		
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
							}
							
										$string =  "***EOF***"; //.chr(13);
										fputs ($fp, $string);
							
							
							fclose($fp);
							//============================================================================================================
							//============================================================================================================
							
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//With the data in nfs_delta_add, figure out creation of change and add files for Tradeware
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//Create CHANGE FILE
																			
							$output_filename = "CHANGE_FILE_".date('Ymd').".txt";
							$fp = fopen($output_filename, "w");
							
							//Header
							$string = "BUCKCAP   ".date("mdY")."  ". date("His").str_pad("",22," ",1)."NAME AND ADDRESS CHANGE FILE"."\n"; //."<br>"
							fputs ($fp, $string);
							
							//Clients List
							/*
							$query_acct = "SELECT * 
														FROM mry_nfs_nadd 
														where nadd_full_account_number like 'PDS%' or nadd_full_account_number like 'PDZ%'
														order by nadd_full_account_number";
							*/
							$query_acct = "SELECT * 
														FROM nfs_delta_nadd 
														order by nadd_full_account_number";
														//where nadd_full_account_number like 'PDY%'
														
							$result_acct = mysql_query($query_acct) or die(tdw_mysql_error($query_acct));
							
							while($row_acct = mysql_fetch_array($result_acct)) {
								 
								 //first, check if this is a new account, if it is, it will not exist in the nfs_nadd table
									$query_acct_check = "SELECT * 
																			FROM nfs_nadd
																			where nadd_full_account_number = '".$row_acct["nadd_full_account_number"]."'";
									//xdebug("query_acct_check",$query_acct_check);							
									$result_acct_check = mysql_query($query_acct_check) or die(tdw_mysql_error($query_acct_check));	 
									$result_acct_count = mysql_num_rows($result_acct_check) or die(tdw_mysql_error($query_acct_check));	 
								 
									if ($result_acct_count > 0) {
										 //check if a mapped code for tradeware exists
										 if ($arr_adv_code[strtoupper(trim($row_acct["nadd_advisor"]))] != "") {		
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
							}
							
										$string =  "***EOF***"; //.chr(13);
										fputs ($fp, $string);
							
							
							fclose($fp);
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							
							//*************************************************************************************************************
							//Create config script to be used by batch file.
							//*************************************************************************************************************
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

} else {
echo "FILE NOT FOUND! NAMED.DAT MISSING. \n<br>";
exit;
}
?>