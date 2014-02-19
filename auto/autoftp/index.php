<?

//THIS PROGRAM CAN RUN BOTH IN SHELL AND BROWSER MODE
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

					include('./includes/functions.php');


          //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
					function no_connect_ftp_server () {
					
							$email_log = '
												<table width="100%" border="0" cellspacing="0" cellpadding="10">
													<tr> 
														<td valign="top">
															<p><a class="bodytext12"><strong>Guggenheim: Couldn\'t connect to FTP Server (zftp02.wanlink.us)' . " (".date('m/d/Y').')</strong></a></p>			
															<p class="size12bold">&nbsp;</p>
															<p>&nbsp;</p>
															<p><a class="bodytext12"><strong>Administrator<br>TDW AUTO FUNCTIONS</strong></a></p></td>
													</tr>
												</table>
													';
							//create mail to send
							$html_body = "";
							$html_body .= zSysMailHeader("");
							$html_body .= $email_log;
							$html_body .= zSysMailFooter ();
							
							$subject = "[FAILURE] Guggenheim: Couldn't connect to FTP Server (ftp02.wanlink.us)" . " (".date('m/d/Y').")";
							$text_body = $subject;
			
							zSysMailer("pprasad@centersys.com", "Pravin Prasad", $subject, $html_body, $text_body, "") ;
							zSysMailer("brg-it@buckresearch.com", "BGR IT", $subject, $html_body, $text_body, "") ;

							echo "Mail sent to Admin about Connection Failure";
					
					}
          //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
					
					define("SITE_URL", "http://192.168.20.63/tdw/");  
					define("RECIPIENT", "Guggenheim FTP"); 
					
					//define("FTPSITE", "207.40.2.4"); //Provided by Shter, Vitaly [vitaly.shter@GuggenheimAdvisors.com]
					
					define("FTPSITE", "ftp02.wanlink.us"); //Provided by Louie Celiberti LCeliberti@GuggenheimAdvisors.com 212-381-7545

					/*
					Username: gpcftpLxiii 
					pa$sword: Kuuxj98H
					*/
					
					define("FTPUSER","gpcftpLxiii"); 
					define("FTPPASS","Kuuxj98H"); 
					define("FTPCHDIR","./"); 
					define("FTPPORT",22);
					define("ENCRYPT", 1);
					define("ENCRYPT_ID", 'CE029EDF');
					define("FILELOCATION", "d:\\tdw\\tdw\\auto\\autoftp\\");   //trailing slash required
					define("FILENAME", "bcm-gugg.txt"); 
					define("FILECOPY","pagugg.csv");
					
					
					//define("FILEPATH","\\\\bucksnapNY\\SHARE2\\Port\\Axys3\\txt\\");
					
					define("FILEPATH","\\\\buckpartner\\Advent\\Axys3\\txt\\");
					
					define("TECHSUPPORT", "support@centersys.com");
					define("APP_PATH","d:\\tdw\\encryption\\");
					
					$email_recipient_user = array();
					$email_recipient_user["Pravin Prasad"] = "pprasad@centersys.com";
					
					$email_recipient_admin = array();
					$email_recipient_admin["BGR IT"] = "brg-it@buckresearch.com";
					
					$arr_recipient = array();
					
					//test
					//$arr_recipient[0] = 'pprasad@centersys.com';
					
					//production
					$arr_recipient["Pravin Prasad"] = "pprasad@centersys.com";
					$arr_recipient["Alex Prylucki"] = "aprylucki@buckresearch.com";
					$arr_recipient["Lauren Domb"] = "ldomb@buckresearch.com";
					$arr_recipient["BRG IT"] = "brg-it@buckresearch.com";
					$arr_recipient["Jason Cohen"] = "jcohen@buckresearch.com";
					$arr_recipient["Mei Fung"] = "mfung@buckresearch.com";
					$arr_recipient["Tracy Morgan"] = "TMorgan@buckresearch.com";
					$arr_recipient["Roy Goldstein"] = "RGoldstein@BuckResearch.com";


		
if ($rnd) { //executed from browser.
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					
					$time_start=getmicrotime();
					 
					// Clean up files from created in previous runs.
						$cmd_cleanup = "del ".FILECOPY." ".FILECOPY.".pgp";
						shell_exec($cmd_cleanup);
					
					// Copy file from source location to working directory of this script
						$cmdcopy = 'copy "'.FILEPATH . FILENAME . '" "'. FILELOCATION . FILECOPY . '"';
						shell_exec($cmdcopy);
					
					// Encrypt the file using the vendor supplied public key
						$cmd_gpg = "gpg --always-trust --batch --no-secmem-warning -o ". FILELOCATION.FILECOPY .".pgp --encrypt -r ".ENCRYPT_ID." ".FILELOCATION . FILECOPY;
						shell_exec($cmd_gpg);
					
					// set up a connection or die
						$conn_id = ftp_connect(FTPSITE) or die(no_connect_ftp_server()); 
						ftp_pasv ($conn_id, true) ;
					
					// login with username and password
						$login_result = ftp_login($conn_id, FTPUSER, FTPPASS); 
					
					// check connection
						if (!$login_result) {
								$log_data .= "<b><font color='red'>FTP connection has failed : Login Error for ".FTPUSER."!<br>Please contact Technical Support.". "</font></b><br>";
								$eml_subject_prefix = "[Failure] "; 						
							//die("FTP connection has failed : Login Error for ".ende($ftp_user_name)."!");
						} else {
							if (ftp_chdir($conn_id, FTPCHDIR)) {
								//$log_data .= "Current directory is now: " . ftp_pwd($conn_id) . "<br>";
								
								//upload files
								if (ftp_put($conn_id, FILECOPY .".pgp", FILELOCATION.FILECOPY .".pgp", FTP_BINARY)) {
								 $log_data .= "<b><font color='green'>Successfully uploaded ".FILECOPY .".pgp"."</font></b><br><br>";
								 $log_data .= "<b>FTP UPLOAD PROCESS completed successfully in </b>". sprintf("%01.2f",((getmicrotime()-$time_start)/1000))." s."."<br><br>";
								 $eml_subject_prefix = "[Success] "; 						
								} else {
								 $log_data .= "<b><font color='red'>There was a problem while uploading ".FILECOPY .".pgp"."</b></font><br>";
								 $log_data .= "<br><b><font color='blue'>Please click <a href='".SITE_URL."auto/autoftp/?rnd=".rand(11111111,99999999)."'>HERE</a> when the file ".FILENAME." is ready.</b></font><br>";
								 $log_data .= "<b>FTP UPLOAD PROCESS completed, UNSUCCESSFULLY in </b>". sprintf("%01.2f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						
								 $eml_subject_prefix = "[Failure] "; 						
								}
						
							} else { 
								$log_data .= "Couldn't change directory<br>";
							}
						}
					
					// close the connection
					ftp_close($conn_id);
					
					echo $log_data;
										
					foreach ($arr_recipient as $key => $emailval) {
					
									$email_log = '
														<table width="100%" border="0" cellspacing="0" cellpadding="10">
															<tr> 
																<td valign="top">
																	<p><a class="bodytext12"><strong>FTP UPLOAD STATUS : '.RECIPIENT.'</strong></a></p>			
																	<p class="size12bold">'.$log_data.'</p>
																	<p>&nbsp;</p>
																	<p><a class="bodytext12"><strong>Administrator<br>TDW AUTO FUNCTIONS</strong></a></p></td>
															</tr>
														</table>
															';
									//create mail to send
									$html_body = "";
									$html_body .= zSysMailHeader("");
									$html_body .= $email_log;
									$html_body .= zSysMailFooter ();
									
									$subject = $eml_subject_prefix. "FTP Upload Status for ". RECIPIENT . " (".date('m/d/Y').")";
									$text_body = $subject;
					
									zSysMailer($emailval, "", $subject, $html_body, $text_body, "") ;
					}	
					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++







					//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} else { //executed from shell
					//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
					$time_start=getmicrotime();
					 
					// Clean up files from created in previous runs.
						$cmd_cleanup = "del ".FILECOPY." ".FILECOPY.".pgp";
						shell_exec($cmd_cleanup);
					
					// Copy file from source location to working directory of this script
						$cmdcopy = 'copy "'.FILEPATH . FILENAME . '" "'. FILELOCATION . FILECOPY . '"';
						shell_exec($cmdcopy);
					
					// Encrypt the file using the vendor supplied public key
						$cmd_gpg = "gpg --always-trust --batch --no-secmem-warning -o ". FILELOCATION.FILECOPY .".pgp --encrypt -r ".ENCRYPT_ID." ".FILELOCATION . FILECOPY;
						shell_exec($cmd_gpg);
					
					// set up a connection or die
						$conn_id = ftp_connect(FTPSITE) or die(no_connect_ftp_server()); 
						ftp_pasv ($conn_id, true) ;
					
					// login with username and password
						$login_result = ftp_login($conn_id, FTPUSER, FTPPASS); 
					
					// check connection
						if (!$login_result) {
								$log_data .= "<b><font color='red'>FTP connection has failed : Login Error for ".FTPUSER."!<br>Please contact Technical Support.". "</font></b><br>";
								$eml_subject_prefix = "[Failure] "; 						
							//die("FTP connection has failed : Login Error for ".ende($ftp_user_name)."!");
						} else {
							if (ftp_chdir($conn_id, FTPCHDIR)) {
								//$log_data .= "Current directory is now: " . ftp_pwd($conn_id) . "<br>";
								
								//upload files
								if (ftp_put($conn_id, FILECOPY .".pgp", FILELOCATION.FILECOPY .".pgp", FTP_BINARY)) {
								 $log_data .= "<b><font color='green'>Successfully uploaded ".FILECOPY .".pgp"."</font></b><br><br>";
								 $log_data .= "<b>FTP UPLOAD PROCESS completed successfully in </b>". sprintf("%01.2f",((getmicrotime()-$time_start)/1000))." s."."<br><br>";
								 $eml_subject_prefix = "[Success] "; 						
								} else {
								 $log_data .= "<b><font color='red'>There was a problem while uploading ".FILECOPY .".pgp"."</b></font><br>";
								 $log_data .= "<br><b><font color='blue'>Please click <a href='".SITE_URL."auto/autoftp/?rnd=".rand(11111111,99999999)."'>HERE</a> when the file ".FILENAME." is ready.</b></font><br><br>";
								 $log_data .= "<b>FTP UPLOAD PROCESS completed, UNSUCCESSFULLY in </b>". sprintf("%01.2f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						
								 $eml_subject_prefix = "[Failure] "; 						
								}
						
							} else { 
								$log_data .= "Couldn't change directory<br>";
							}
						}
					
					// close the connection
					ftp_close($conn_id);
					
					echo $log_data;
										
					foreach ($arr_recipient as $key => $emailval) {
					
									$email_log = '
														<table width="100%" border="0" cellspacing="0" cellpadding="10">
															<tr> 
																<td valign="top">
																	<p><a class="bodytext12"><strong>FTP UPLOAD STATUS : '.RECIPIENT.'</strong></a></p>			
																	<p class="size12bold">'.$log_data.'</p>
																	<p>&nbsp;</p>
																	<p><a class="bodytext12"><strong>Administrator<br>TDW AUTO FUNCTIONS</strong></a></p></td>
															</tr>
														</table>
															';
									//create mail to send
									$html_body = "";
									$html_body .= zSysMailHeader("");
									$html_body .= $email_log;
									$html_body .= zSysMailFooter ();
									
									$subject = $eml_subject_prefix. "FTP Upload Status for ". RECIPIENT . " (".date('m/d/Y').")";
									$text_body = $subject;
					
									zSysMailer($emailval, "", $subject, $html_body, $text_body, "") ;
									echo $link . "<br>";
					}	
					//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
}

//=====================================================================================================

?>