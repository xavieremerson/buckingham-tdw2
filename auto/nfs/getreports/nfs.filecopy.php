<?
//==================================================================================================
// UPDATED FILE FOR MORE ROBUST ERROR CHECKING TO MAKE SURE MAIN PROCESS OF DATABASE UPDATE DOES NOT
// KICK-OFF AND CAUSE FATAL EXCEPTIONS
// THIS FILE IS INCLUDED IN NFS.GETFILES.PHP
//==================================================================================================

$dayname = date("l");
if ($dayname == 'Sxunday' or $dayname == 'Sxaturday' ) {
	echo "Not required to run the script on ".$dayname;
	exit;
} else {
	//Log file information
	$log_info = "";
	//$log_info .= "LOG CREATED ON ".date("D, m/d/Y h:i a")."\n<br>";
	// Mail Info Variable : This will be sent to recipients.
	$mail_to_admins = '';
		
	//Start with this value and change it to zero on failure
	$success = 1;
	
	//$files_to_copy = array('ACCTBALD.DAT','ACTVYD.DAT','COMMSD.DAT','NABASE.DAT','NAMED.DAT','ORDER.DAT','PENDOORD.DAT','POSITD.DAT','SECMAST.DAT','TRADED.DAT','TRDREV_TD.DAT');

	$files_to_copy = $filenames;
	//print_r($files_to_copy);
	$actual_files = array();

	$log_info .= "<BR>Files attached in email:<br><br>"; 
	
	foreach( $files_to_copy as $key => $filename) {
		if (!file_exists($filelocation_tdw . $filename)) {
			$cmd_filecopy = "copy " . $filelocation_nfs . $filename . " " . $filelocation_tdw ;
			echo $cmd_filecopy."\n"; 
			shell_exec($cmd_filecopy);
			if (file_exists($filelocation_tdw .$filename) && round(((time() - strtotime(date("m/d/Y",filemtime($filelocation_tdw .$filename))))/86400),0) < 30) {  //30 is just days
				$log_info .=  $filename." (Attached)\n\r<br>";
				$actual_files[] = $filename;
				echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $filename) . " bytes ::: Copied file ".$filename." \n\r";
			} else {
				$log_info .= $filename." (File Not Found.)\n\r<br>";
				echo "Failed to copy file ".$filename." \n\r";
				$success = 1;
			}
		} else {
			$log_info .= "File ".$filename." exists. Not overwriting.\n\r<br>";
			echo "File ".$filename." exists. Not overwriting. \n\r";
		}
	}
	


	//ZIP ALL DAT FILES
	//Unique Zip File Name
	echo "Creating ZIP file...\n\n";
	$unique_zip_name = 'FBNR_'. date('Y')."-".substr($datecriteria,0,2)."-".substr($datecriteria,2,4) . "_". substr(md5(rand(1111111111,9999999999)), 0, 8).".zip";
	shell_exec("wzzip " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_tdw ."*.TXT");
	
	echo "Copying ZIP file...\n\n";
	$str_cmd = "copy " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_alternate_tdw;
	echo $str_cmd."<br>"; 
	shell_exec($str_cmd);
	
	$log_info .= "<br><br><br><br><br><br><br>\n\r";

}

	$file_name="log_".date("l").".html";     
	echo "Writing status file ".$file_name."\n\n";
	$fp = fopen ($file_name, "w");  
	fwrite ($fp,$log_info);        
	fclose ($fp); 
?>
