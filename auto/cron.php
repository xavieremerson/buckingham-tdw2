<?
ini_set('max_execution_time', 3600);
//This is a once a day process in the morning.
include('../includes/dbconnect.php');
include('../includes/functions.php'); 
include('../includes/global.php'); 


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



//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CAN USE THIS AS A POST FILE UPLOAD REDIRECT
$trade_date_to_process = previous_business_day();
//$trade_date_to_process = '2009-12-31';

xdebug('trade_date_to_process',$trade_date_to_process);


//===================================================================================
//CHECK FOR FILES EXISTING BEFORE ATTEMPTING TO RUN THIS MASSIVE SEQUENCE.
//===================================================================================
if (file_exists($download_location.$trade_date_to_process."\\TRDREV_TD.DAT")) {
					echo "Trade file exists. Processing ....\n<br>";
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//Synch up the clients list with DOS
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//include('proc_synch_clients_inc.php');
					
					
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//REMOVED AFTER DOS DISCONTINUED					
					//include('proc_synch_clients_delta_inc.php');
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					
					
					
					
					
					
					echo "**********************************************************************\n";
					echo "*                        DAILY PROCESS                               *\n";
					echo "**********************************************************************\n";
					echo "Current Date/Time: [".date("l, m-d-Y h:i a")."]\n\n";
					//
					//Check if trades have been processed for the trade_date_to_process
					//
					$trades_process_status = check_process_status ("Trades", $trade_date_to_process);
					if ($trades_process_status == 1 ) { //always process, temporary procedure.
						echo "Trades already processed for ".$trade_date_to_process."\n\n";
						//do nothing
					} else {
						echo "Trades not processed for ".$trade_date_to_process."\n\n";
						echo "Starting process for Trades.\n\n";
					
					//This has been activated on 2/22/2006 after a bulk upload of all trades to date
					
					include('trades_u.php');
					include('trades_mtm.php');
					
						$success = 1;
						
						if ($success == 1) {
						$str_sql_insert = "INSERT INTO tdw_proc_process_status(proc_process, proc_date, proc_status,proc_timestamp) values('Trades','".$trade_date_to_process."',1, now())";
							$result_insert = mysql_query($str_sql_insert) or die (mysql_error());
						} else {
						$str_sql_insert = "INSERT INTO tdw_proc_process_status(proc_process, proc_date, proc_status,proc_timestamp) values('Trades','".$trade_date_to_process."',0, now())";
							$result_insert = mysql_query($str_sql_insert) or die (mysql_error());
						}
					}	
					
					include('nameadd_u.php');
					
					$str_sql_insert = "INSERT INTO tdw_proc_process_status(proc_process, proc_date, proc_status,proc_timestamp) values('Accounts','".$trade_date_to_process."',1, now())";
					$result_insert = mysql_query($str_sql_insert) or die (mysql_error());
					
					//Process for SRep Commissions NOW WITH BROKERAGE MONTH
					//include('proc_src_daily_inc.php');
					
					include('proc_src_brok_daily_inc.php');

					
					// Validation of account master consistency
					include('validate_nadd_inc.php');
					
					// Writing out files for SalesLogix
					
					//REMOVING THIS (NO LONGER USED.)
					//PRAVIN : 12/8/2008
					//BROUGHT BACK
					include('proc_saleslogix_inc.php');
					
					
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} else {
					echo "Trade file does not exist. Processing ....\n<br>";
					//EMAIL ROUTINE TO SUPPORT
								$email_log = '
												<hr>
												<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">
												Trades and Account Master processing could not be initiated because
												NFS files were not found.<br>
												Please contact appropriate TDW Support Personnel to resolve this issue.</font>
												</b>
												<hr>
												<b><font color="#000000">Details of Server where NFS files reside for use by TDW:</font></b><br>
												<font color="#000000">Server: </font><b><font color="#0000FF">bucktdw.buckresearch.com</font></b><br>
												<font color="#000000">Share Name: </font><b><font color="#0000FF">nfs$</font></b>
												<hr>
												<br><br><br><br><br><br><br><br><br><br><br>
														';
								//create mail to send
								$html_body .= zSysMailHeader("");
								$html_body .= $email_log;
								$html_body .= zSysMailFooter ();
								
								$subject = "Urgent Alert : (FILES MISSING, TRADES NOT PROCESSED BY TDW) : (".date('m-d-Y').")";
								$text_body = $subject;
								
								zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
								zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
}
//===================================================================================
//===================================================================================
?>