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
	$log_info .= "Previous Business Day = <b>". $date_to_process . "</b><br><br>\n\n";
	xdebug ('date_to_process', $date_to_process);
	
	$folder_to_create = $date_to_process;
	echo $date_to_process . "\n\n\r";

	//Start with this value and change it to zero on failure
	$success = 1;
	
	if (!file_exists($filelocation_tdw . $folder_to_create)) {
	shell_exec("mkdir " . $filelocation_tdw . $folder_to_create);
	$log_info .= "Folder created = ". $folder_to_create . "\n\n\r<br><br>";
	} else {
	$log_info .= "Folder exists: ". $folder_to_create . "\n\n\r<br><br>";
	echo "Folder exists: ". $folder_to_create . "\n\n\r";
	}
	
	if (!file_exists($filelocation_tdw . $folder_to_create)) {
		$success = 0;
	}

	shell_exec("copy " . $filelocation_nfs . "ACCTBALD.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "ACCTBALD.DAT")) {
			$log_info .=  date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "ACCTBALD.DAT") . " bytes ::: Copied file ACCTBALD.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "ACCTBALD.DAT") . " bytes ::: Copied file ACCTBALD.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file ACCTBALD.DAT \n\r<br>";
			echo "Failed to copy file ACCTBALD.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "ACTVYD.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "ACTVYD.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "ACTVYD.DAT") . " bytes ::: Copied file ACTVYD.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "ACTVYD.DAT") . " bytes ::: Copied file ACTVYD.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file ACTVYD.DAT \n\r<br>";
			echo "Failed to copy file ACTVYD.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "COMMSD.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "COMMSD.DAT")) {
			$log_info .=  date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "COMMSD.DAT") . " bytes ::: Copied file COMMSD.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "COMMSD.DAT") . " bytes ::: Copied file COMMSD.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file COMMSD.DAT \n\r";
			echo "Failed to copy file COMMSD.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "NABASE.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "NABASE.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "NABASE.DAT") . " bytes ::: Copied file NABASE.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "NABASE.DAT") . " bytes ::: Copied file NABASE.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file NABASE.DAT \n\r";
			echo "Failed to copy file NABASE.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "NAMED.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "NAMED.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "NAMED.DAT") . " bytes ::: Copied file NAMED.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "NAMED.DAT") . " bytes ::: Copied file NAMED.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file NAMED.DAT \n\r";
			echo "Failed to copy file NAMED.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "ORDER.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "ORDER.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "ORDER.DAT") . " bytes ::: Copied file ORDER.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "ORDER.DAT") . " bytes ::: Copied file ORDER.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file ORDER.DAT \n\r";
			echo "Failed to copy file ORDER.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "PENDOORD.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "PENDOORD.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "PENDOORD.DAT") . " bytes ::: Copied file PENDOORD.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "PENDOORD.DAT") . " bytes ::: Copied file PENDOORD.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file PENDOORD.DAT \n\r";
			echo "Failed to copy file PENDOORD.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "POSITD.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "POSITD.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "POSITD.DAT") . " bytes ::: Copied file POSITD.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "POSITD.DAT") . " bytes ::: Copied file POSITD.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file POSITD.DAT \n\r";
			echo "Failed to copy file POSITD.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "SECMAST.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "SECMAST.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "SECMAST.DAT") . " bytes ::: Copied file SECMAST.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "SECMAST.DAT") . " bytes ::: Copied file SECMAST.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file SECMAST.DAT \n\r";
			echo "Failed to copy file SECMAST.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "TRADED.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "TRADED.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "TRADED.DAT") . " bytes ::: Copied file TRADED.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "TRADED.DAT") . " bytes ::: Copied file TRADED.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file TRADED.DAT \n\r";
			echo  "Failed to copy file TRADED.DAT \n\r";
			$success = 0;
		}
	shell_exec("copy " . $filelocation_nfs . "TRDREV_TD.DAT " . $filelocation_tdw . $folder_to_create."\\");
		if (file_exists($filelocation_tdw . $folder_to_create."\\". "TRDREV_TD.DAT")) {
			$log_info .= date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "TRDREV_TD.DAT") . " bytes ::: Copied file TRDREV_TD.DAT \n\r<br>";
			echo date("h:i:s a")." ::: ". filesize($filelocation_tdw . $folder_to_create."\\". "TRDREV_TD.DAT") . " bytes ::: Copied file TRDREV_TD.DAT \n\r";
		} else {
			$log_info .= "Failed to copy file TRDREV_TD.DAT \n\r";
			echo "Failed to copy file TRDREV_TD.DAT \n\r";
			$success = 0;
		}

	//ZIP ALL DAT FILES
	//Unique Zip File Name
	echo "Creating ZIP file...\n\n";
	$unique_zip_name = $date_to_process . "___".substr(md5(rand(1111111111,9999999999)), 0, 8).".zip";
	shell_exec("wzzip " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_tdw . $folder_to_create."\\*.DAT");
	
	echo "Copying ZIP file...\n\n";
	shell_exec("mkdir " . $filelocation_alternate_tdw . $folder_to_create);
	$str_cmd = "copy " .$filelocation_tdw . $unique_zip_name ." ". $filelocation_alternate_tdw . $folder_to_create;
	echo $str_cmd."<br>"; 
	shell_exec($str_cmd);
	
	echo $filelocation_alternate_tdw . $folder_to_create ."\\". $unique_zip_name;
	if (file_exists($filelocation_alternate_tdw . $folder_to_create ."\\". $unique_zip_name)) {
			$log_info .= "<br><br>Zipped file ".$unique_zip_name. " successfully copied to ". $filelocation_alternate_tdw . $folder_to_create ."<br>\n\r";
			echo "Zipped file ".$unique_zip_name. " successfully copied to ". $filelocation_alternate_tdw . $folder_to_create ."\n\r";
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
