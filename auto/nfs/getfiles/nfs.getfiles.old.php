<?
include('nfs.config.inc.php');
include('nfs.functions.php');

$dayname = date("l");
if ($dayname == 'Sunday' or $dayname == 'Monday' ) {
	echo "Not required to run the script on ".$dayname;
	exit;
} else {
	//Log file information
	$log_info = "LOG CREATED ON ".date("D, m/d/Y h:i a")."\n";
	// Mail Info Variable : This will be sent to recipients.
	$mail_to_admins = '';
	
	//Today's Date should be applied here.
	$date_processed_on = date('Y-m-d');
	$date_to_process = previous_business_day();
	$log_info .= "Previous Business Day = ". $date_to_process . "\n\n";
	xdebug ('date_to_process', $date_to_process);
	
	$folder_to_create = $date_to_process;
	echo $date_to_process . "\n\n\r";
	
	shell_exec("mkdir " . $filelocation_tdw . $folder_to_create);
	$log_info .= "Folder created = ". $folder_to_create . "\n\n\r";
	
	shell_exec("copy " . $filelocation_nfs . "ACCTBALD.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file ACCTBALD.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "ACTVYD.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file ACTVYD.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "COMMSD.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file COMMSD.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "NABASE.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file NABASE.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "NAMED.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file NAMED.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "ORDER.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file ORDER.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "PENDOORD.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file PENDOORD.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "POSITD.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file POSITD.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "SECMAST.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file SECMAST.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "TRADED.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file TRADED.DAT \n\r";
	shell_exec("copy " . $filelocation_nfs . "TRDREV_TD.DAT " . $filelocation_tdw . $folder_to_create."\\");
	$log_info .= "Copied file TRDREV_TD.DAT \n\r";
	
	//ZIP ALL DAT FILES
	//Unique Zip File Name
	$unique_zip_name = $date_to_process . "___".substr(md5(rand(1111111111,9999999999)), 0, 8).".zip";
	shell_exec("wzzip " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_tdw . $folder_to_create."\\*.DAT");
	
	shell_exec("mkdir " . $filelocation_alternate_tdw . $folder_to_create);
	shell_exec("copy " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_alternate_tdw . $folder_to_create);
	
	$mail_to_admins .= "\nNFS Files for Trade Date ".$date_to_process. " successfully copied to ".$net_data_location.$folder_to_create."\n\n";
	
	$file_name="log_".date("l").".wri";     
	$fp = fopen ($file_name, "w");  
	fwrite ($fp,$log_info);        
	fclose ($fp);   

	////
	//   MAIL to Admins with information relating to abnormalities in processing
	if ($mail_to_admins != '') {
	$mail_to_admins .= $techsupport;
	mail($email_recipients, "Notice: NFS Files for Trade Date ".$date_to_process . " copied successfully.", $mail_to_admins, "From: Pravin Prasad <pprasad@centersys.com>");
	}
}
?>
