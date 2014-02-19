<?
ini_set('max_execution_time', 600);

//
// Getting files from Bloomberg and saving it to a folder on the network.
// Also, of the downloaded files, the OATS file needs to be uploaded to Tradeware via sFTP
// Alert mechanism to show errors
// Get files from Bloomberg

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
$archive_location =  "\\\\buckfilesrv\\e$\\BloombergArchive\\ClientOrders\\";  /* Trailing slash must exist */
$temp_location     = "D:\\tdw\\tdw\\auto\\bloomberg\\client_orders\\files\\";   /* Trailing slash must exist */


define("FTPSITE", "bfmrr.bloomberg.com");
define("FTPUSER", "T2618L2");
define("FTPPASS", "JYq5uvQ.");
define("UPLOADDIR","/");
define("DOWNLOADDIR","/");

define("TECHSUPPORT", "pprasad@centersys.com");
define("APP_PATH","d:\\tdw\\tdw\\auto\\bloomberg\\client_orders\\files\\");

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

//CLEANUP TEMP DIRECTORY
//echo "del /Q " .APP_PATH . "temp\\*.*\n";
//shell_exec("del /Q " .APP_PATH . "temp\\*.*");

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
						
												 //$get_filename = "BuckOrders";
												 $get_filename = "BuckOrdersTest";
												//check each file for date/time to determine suitability for download
													 //show_array($arr_fileinfo);
													 
													 //echo $arr_fileinfo["name"]."\n";
													 //echo APP_PATH."temp\\".$arr_fileinfo["name"]."\n";
						
													 //initiate download sequence for the particular file
													 echo "Initiating download of file [".$get_filename."]\n";
													 $download = ftp_get($conn_id, APP_PATH."temp\\".$get_filename, $get_filename, FTP_BINARY); 
														
														// check download status
														//$status_val = 0;
														//while ($status_val == 0) {
														
														if (!file_exists(APP_PATH."temp\\".$get_filename)) { 
																 $log_info .= "\nFTP download of file ".$get_filename." has failed!";
																 echo "\nFTP download of file ".$get_filename." has failed!";
																	//ftp_close($conn_id);
																	//$conn_id = ftp_connect(FTPSITE); 
																	//$login_result = ftp_login($conn_id, FTPUSER, FTPPASS);
																	//$download = ftp_get($conn_id, APP_PATH."temp\\".$arr_fileinfo["name"], $arr_fileinfo["name"], FTP_BINARY); 
																	//	if (!file_exists(APP_PATH."temp\\".$arr_fileinfo["name"])) { 
																		$status_val = 0;
																	//	} else {
																	//	$status_val = 1;
																	//echo "\nDownloaded file ".$arr_fileinfo["name"].".";
																	//	ob_flush();
																	//	flush();
																	//	}
														} else {
																 $log_info .= "\nDownloaded file ".$get_filename.".";
																	echo "\nDownloaded file ".$get_filename.".";
																 $status_val = 1; 
														}
														//}
						
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
							$cmd_copy = "copy " .APP_PATH . "temp\\" . $get_filename ." ". APP_PATH . $get_filename.$date_to_process;
							$log_info .= "\n".$cmd_copy."\n";
							ydebug("cmd_copy",$cmd_copy);
							shell_exec($cmd_copy);
						  
							sleep(2);
							//CLEANUP TEMP DIRECTORY
							echo "del /Q " .APP_PATH . "temp\\*.*\n";
							shell_exec("del /Q " .APP_PATH . "temp\\*.*");
							
							$mail_to_admins .= "\Bloomberg Client Orders for Trade Date ".$date_to_process. " successfully copied to ".$archive_location . $folder_to_create."\n\n";
							
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
/*
ftp_alloc -- Allocates space for a file to be uploaded
ftp_cdup -- Changes to the parent directory
ftp_chdir -- Changes the current directory on a FTP server
ftp_chmod -- Set permissions on a file via FTP
ftp_close -- Closes an FTP connection
ftp_connect -- Opens an FTP connection
ftp_delete -- Deletes a file on the FTP server
ftp_exec -- Requests execution of a command on the FTP server
ftp_fget -- Downloads a file from the FTP server and saves to an open file
ftp_fput -- Uploads from an open file to the FTP server
ftp_get_option -- Retrieves various runtime behaviours of the current FTP stream
ftp_get -- Downloads a file from the FTP server
ftp_login -- Logs in to an FTP connection
ftp_mdtm -- Returns the last modified time of the given file
ftp_mkdir -- Creates a directory
ftp_nb_continue -- Continues retrieving/sending a file (non-blocking)
ftp_nb_fget -- Retrieves a file from the FTP server and writes it to an open file (non-blocking)
ftp_nb_fput -- Stores a file from an open file to the FTP server (non-blocking)
ftp_nb_get -- Retrieves a file from the FTP server and writes it to a local file (non-blocking)
ftp_nb_put -- Stores a file on the FTP server (non-blocking)
ftp_nlist -- Returns a list of files in the given directory
ftp_pasv -- Turns passive mode on or off
ftp_put -- Uploads a file to the FTP server
ftp_pwd -- Returns the current directory name
ftp_quit -- Alias of ftp_close()
ftp_raw -- Sends an arbitrary command to an FTP server
ftp_rawlist -- Returns a detailed list of files in the given directory
ftp_rename -- Renames a file or a directory on the FTP server
ftp_rmdir -- Removes a directory
ftp_set_option -- Set miscellaneous runtime FTP options
ftp_site -- Sends a SITE command to the server
ftp_size -- Returns the size of the given file
ftp_ssl_connect -- Opens an Secure SSL-FTP connection
ftp_systype -- Returns the system type identifier of the remote FTP server
*/
?>