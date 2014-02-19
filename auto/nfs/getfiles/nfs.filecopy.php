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
	$log_info .= "LOG CREATED ON ".date("D, m/d/Y h:i a")."\n<br>";
	// Mail Info Variable : This will be sent to recipients.
	$mail_to_admins = '';
	
	//Today's Date should be applied here.
	$date_processed_on = date('Y-m-d');
	$date_to_process = previous_business_day();
	$log_info .= "Previous Business Day = <b>". $date_to_process . "</b><br>\n\n";
	$log_info .= "NFS FTP Location @ BRG = <b>". $nfs_source_location . "</b><br><br>\n\n";
	xdebug ('date_to_process', $date_to_process);
	
	$folder_to_create = $date_to_process;
	echo $date_to_process . "\n\n\r";

	//Start with this value and change it to zero on failure
	$success = 1;
	
	if (!file_exists($filelocation_tdw . $folder_to_create)) {
	shell_exec("mkdir " . $filelocation_tdw . $folder_to_create);
	//$log_info .= "Folder created = ". $folder_to_create . "\n\n\r<br><br>";
	} else {
	$log_info .= "Folder exists: ". $folder_to_create . "\n\n\r<br><br>";
	echo "Folder exists: ". $folder_to_create . "\n\n\r";
	}

	$log_info .= '<font face="Courier">';
	$log_info .= "FILE TIME &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;::: PROCESS TIME::: FILE SIZE&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;::: ACTION<br>";


	
	if (!file_exists($filelocation_tdw . $folder_to_create)) {
		$success = 0;
	}

	$files_to_copy = array('ACCTBALD.DAT','ACTVYD.DAT','COMMSD.DAT','NABASE.DAT','NAMED.DAT','ORDER.DAT','PENDOORD.DAT','POSITD.DAT','SECMAST.DAT','TRADED.DAT','TRDREV_TD.DAT');

	foreach( $files_to_copy as $key => $filename) {
		if (!file_exists($filelocation_tdw . $folder_to_create."\\". $filename)) {
			
			$str_cmd = "copy " . $filelocation_nfs . $filename . " " . $filelocation_tdw . $folder_to_create."\\";
			echo $str_cmd . "\n";
			shell_exec($str_cmd);
			if (file_exists($filelocation_tdw . $folder_to_create."\\". $filename)) {
				$log_info .= date ("m/d/Y h:i:s a", filemtime($filelocation_tdw . $folder_to_create."\\". $filename)) ." ::: ". date("h:i:s a")." ::: ". str_pad(filesize($filelocation_tdw . $folder_to_create."\\". $filename)." bytes",19,".",1) . " ::: Copied file ".$filename." \n\r<br>";
				echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". $filename) . " bytes ::: Copied file ".$filename." \n\r";
			} else {
				$log_info .= "Failed to copy file ".$filename." \n\r<br>";
				echo "Failed to copy file ".$filename." \n\r";
				$success = 0;
			}
		} else {
			$log_info .= "File ".$filename." exists. Not overwriting.\n\r<br>";
			echo "File ".$filename." exists. Not overwriting. \n\r";
		}
	}

		$log_info .= '</font>';

	//date ("F d Y H:i:s.", filemtime($filename))

	//ZIP ALL DAT FILES
	//Unique Zip File Name
	echo "Creating ZIP file...\n\n";
	$unique_zip_name = $date_to_process . "___".substr(md5(rand(1111111111,9999999999)), 0, 8).".zip";
	$str_cmd_a = "wzzip " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_tdw . $folder_to_create."\\*.DAT";
	echo $str_cmd_a."\n";
	shell_exec($str_cmd_a);
	
	echo "Copying ZIP file...\n\n";
	shell_exec("mkdir " . $filelocation_alternate_tdw . $folder_to_create);
	$str_cmd = "copy " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_alternate_tdw . $folder_to_create;
	echo $str_cmd."\n"; 
	shell_exec($str_cmd);
	
	echo $filelocation_alternate_tdw . $folder_to_create ."\\". $unique_zip_name."\n";
	if (file_exists($filelocation_alternate_tdw . $folder_to_create ."\\". $unique_zip_name)) {
			$log_info .= "<br><br>Zipped file ".$unique_zip_name. " successfully copied to <a href='".$net_data_location.$folder_to_create."'>".$net_data_location.$folder_to_create."</a><br>\n\r";
			echo "Zipped file ".$unique_zip_name. " successfully copied to <a href='".$net_data_location.$folder_to_create."'>".$net_data_location.$folder_to_create."</a>\n\r";
	} else {
			$log_info .= "<br><br>Zip file not created.<br>\n\r";
			echo "Zip file not created.\n\r";
	}

	$log_info .= "<br><br><br><br><br><br><br><br><br><br><br><br>\n\r";

}

	$file_name="log_".date("l").".html";     
	echo "Writing status file ".$file_name."\n\n";
	$fp = fopen ($file_name, "w");  
	fwrite ($fp,$log_info);        
	fclose ($fp);   

?>