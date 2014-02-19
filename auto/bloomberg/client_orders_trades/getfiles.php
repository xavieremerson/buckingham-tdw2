<?
ini_set('max_execution_time', 3600);

//
// This has to be run on all business days, after close of business (say 8:00PM)
// 

include('d:/tdw/tdw/includes/functions.php');
include('d:/tdw/tdw/includes/dbconnect.php');
include('d:/tdw/tdw/includes/global.php');

function itemize_dir($contents) {
	 foreach ($contents as $file) {
			 if(ereg("([-dl][rwxst-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)", $file, $regs)) {
					 $type = (int) strpos("-dl", $regs[1]{0});
					 $tmp_array['date'] = date("m-d",strtotime($regs[6]));
					 $tmp_array['time'] = $regs[7];
					 $tmp_array['name'] = $regs[9];
			 }
			 $dir_list[] = $tmp_array;
	 }
	 return $dir_list;
}

//ini_set('error_reporting', E_ALL);
//echo ini_get('safe_mode');
//exit;


//************************ BEGIN IMPORTANT CONFIG DATA ************************************
$archive_location =  "\\\\buckfilesrv\\e$\\BloombergArchive\\ClientOrdersTrades\\";  /* Trailing slash must exist */
$temp_location     = "D:\\tdw\\tdw\\auto\\bloomberg\\client_orders_trades\\files\\";   /* Trailing slash must exist */


define("FTPSITE", "bfmrr.bloomberg.com");
define("FTPUSER", "T2618L2");
define("FTPPASS", "JYq5uvQ.");
define("UPLOADDIR","/");
define("DOWNLOADDIR","/");

define("TECHSUPPORT", "pprasad@centersys.com");
define("APP_PATH","d:\\tdw\\tdw\\auto\\bloomberg\\client_orders_trades\\files\\");

//CLEANUP TEMP DIRECTORY
echo "del /Q " .APP_PATH . "temp\\*.*\n";
shell_exec("del /Q " .APP_PATH . "temp\\*.*");


$email_recipient_user = array();
//$email_recipient_user["BGR IT"] = "brg-it@buckresearch.com";
$email_recipient_user["Pravin Prasad"] = "pprasad@centersys.com";

$email_recipient_admin = array();
//$email_recipient_admin["BGR IT"] = "brg-it@buckresearch.com";
$email_recipient_admin["Pravin Prasad"] = "pprasad@centersys.com";
//************************* END IMPORTANT CONFIG DATA *************************************

//exit;

	//Log file information
	$log_info = "LOG CREATED ON ".date("D, m/d/Y h:i a")."\n";
	// Mail Info Variable : This will be sent to recipients.
	$mail_to_admins = '';
	
	//Today's Date should be applied here.
	$date_processed_on = date('Y-m-d');
	
							$date_to_process = date('Ymd');
							echo "Date to process: ".$date_to_process."\n"; 
							//ydebug('date_to_process',$date_to_process);
	
							//exit;
							$log_info .= "Processing date = ". $date_to_process . "\n\n";
							
							$folder_to_create = $date_to_process;
							
							if (!file_exists($archive_location . $folder_to_create)) {
								shell_exec("mkdir " . $archive_location . $folder_to_create);
								$log_info .= "Folder created = ". $folder_to_create . "\n\n\r";
							} else {
							  echo "Directory exists: ".$archive_location . $folder_to_create. "\n\n\r";
								$log_info .= "Directory exists: ".$archive_location . $folder_to_create. "\n\n\r";
							}
							
								//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
								//FTP PROCESS
								$conn_id = ftp_connect(FTPSITE); 
								$login_result = ftp_login($conn_id, FTPUSER, FTPPASS); 
								
								// check connection
								if ((!$conn_id) || (!$login_result)) { 
										 $log_info .= "FTP connection has failed!";
										 $log_info .= "Attempted to connect to ".FTPSITE." for user ".FTPUSER; 
										 //exit; 
									 } else {
										 $log_info .= "Connected to ".FTPSITE." for user ".FTPUSER;
									 }
								
								$log_info .= "Current directory: " . ftp_pwd($conn_id);
						
								//Try to change the directory to "upload"
								if (ftp_chdir($conn_id, UPLOADDIR)) {
									 
									 $log_info .= "Changed current directory is now: " . ftp_pwd($conn_id);
						
												 //Orders_Trades20130724
												 //$get_filename = "Orders_Trades20130920";
												 $get_filename = "Orders_Trades".date('Ymd');
												 echo $get_filename;
						
													 //initiate download sequence for the particular file
													 echo "Initiating download of file [".$get_filename."]\n";
													 $download = ftp_get($conn_id, APP_PATH."temp\\".$get_filename, $get_filename, FTP_BINARY); 
														

														if (!file_exists(APP_PATH."temp\\".$get_filename)) { 
																 $log_info .= "\nFTP download of file ".$get_filename." has failed!";
																 echo "\nFTP download of file ".$get_filename." has failed!";
																		$status_val = 0;
														} else {
																 $log_info .= "\nDownloaded file ".$get_filename.".";
																	echo "\nDownloaded file ".$get_filename.".";
																 $status_val = 1; 
														}
						
										// close the FTP stream 
										ftp_close($conn_id);
									  echo "FTP Connection is now closed.";
										
								} else { 
									 echo "Couldn't change directory";
								}
						
							//ZIP ALL FILES IN THE bloomberg\temp folder
							//Unique Zip File Name
							$unique_zip_name = $date_to_process.".zip";
							ydebug("unique_zip_name",$unique_zip_name);
							$cmd_zip = "wzzip " . APP_PATH . "temp\\" . $unique_zip_name ." ". APP_PATH . "temp\\" . "*.*";
							ydebug("cmd_zip",$cmd_zip);
							$output = shell_exec($cmd_zip);
							echo "<pre>$output</pre>";

							//shell_exec("mkdir " . $filelocation_alternate_tdw . $folder_to_create);
							$cmd_copy = "copy " .APP_PATH . "temp\\" . $unique_zip_name ." ". $archive_location . $folder_to_create;
							$log_info .= "\n".$cmd_copy."\n";
							ydebug("cmd_copy",$cmd_copy);
							shell_exec($cmd_copy);

							//shell_exec("mkdir " . $filelocation_alternate_tdw . $folder_to_create);
							$cmd_copy = "copy " .APP_PATH . "temp\\" . $get_filename ." ". APP_PATH . $get_filename;
							$log_info .= "\n".$cmd_copy."\n";
							ydebug("cmd_copy",$cmd_copy);
							shell_exec($cmd_copy);
						  
							sleep(2);
							//CLEANUP TEMP DIRECTORY
							echo "del /Q " .APP_PATH . "temp\\*.*\n";
							shell_exec("del /Q " .APP_PATH . "temp\\*.*");
							
							$mail_to_admins .= "\Bloomberg Client Orders Trades for Trade Date ".$date_to_process. " successfully copied to ".$archive_location . $folder_to_create."\n\n";
							
							$file_name="log_".date("l").".wri";     
							$fp = fopen ($file_name, "w");  
							fwrite ($fp,$log_info);        
							fclose ($fp);   
						
							////
							//   MAIL to Admins with information relating to abnormalities in processing
							if ($mail_to_admins != '') {
							$mail_to_admins .= TECHSUPPORT;
							ydebug("tech support",TECHSUPPORT);
							//mail($email_recipients, "Notice: NFS Files for Trade Date ".$date_to_process . " copied successfully.", $mail_to_admins, "From: Pravin Prasad <pprasad@centersys.com>");
							
							}
?>