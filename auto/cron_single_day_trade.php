<?

ini_set('max_execution_time', 3600);

//This is a once a day process in the morning.
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

echo "**********************************************************************\n<br>";
echo "*                        DAILY PROCESS                               *\n<br>";
echo "**********************************************************************\n<br>";
echo "Current Date/Time: [".date("l, m-d-Y h:i a")."]\n\n<br>";

//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CAN USE THIS AS A POST FILE UPLOAD REDIRECT

//$trade_date_to_process = previous_business_day();
$trade_date_to_process = '2006-06-16'; //ENTER THE DATE REQUIRED TO BE PROCESSED
ydebug('trade_date_to_process',$trade_date_to_process);

							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//Check if trades have been processed for the trade_date_to_process
							//
							$trades_process_status = check_process_status ("Trades", $trade_date_to_process);
							if ($trades_process_status == 1) {
								echo "Trades already processed for ".$trade_date_to_process."\n\n<br>";
								//do nothing
							} else {
								echo "Trades not processed for ".$trade_date_to_process."\n\n<br>";
								echo "Starting process for Trades.\n\n<br>";
							
							//skip the trades import process until file for 1/19/06 is obtained
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
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
?>