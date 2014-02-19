<?
ini_set('max_execution_time', 1600);
include('dbconnect.php');
include('nfs.functions.php');
include('mailer_functions.php');

//====================================================================================================
//network location of the data storage
$net_data_location = "\\\\bucksnapNY\\SHARE1\\NFS\\";

//Location of this program
$scriptlocation = "D:\\tdw\\tdw\\auto\\nfs\\getreports\\";   /* Trailing slash must exist */;


//Changed on 21 September 2010 after ftp server migrated to a new ftp location
// NFS Files Location (file system access)
//$filelocation_nfs = "N:\\";
$filelocation_nfs = "\\\\192.168.20.54\\nfs\\";
//$nfs_source_location = "\\\\192.168.20.93\\nfs$\\";
$nfs_source_location = "\\\\192.168.20.54\\nfs\\";


//NFS Files Storage on Buckingham Server (?.?.?.?) mapped as ? Drive
$filelocation_tdw = "D:\\tdw\\tdw\\auto\\nfs\\getreports\\tmp\\"; /* Trailing slash must exist */;

//Changed on 21 September 2010 after ftp server migrated to a new ftp location
//Also moved older files from \\bucknapny\share1\nfs to current location below
//$filelocation_alternate_tdw = "T:\\NFS\\FBNR_FILES\\";
$filelocation_alternate_tdw = "\\\\buckfilesrv\\e$\\nfs\\_FBNR_FILES\\";

//$filelocation_alternate_tdw = "D:\\tdw\\tdw\\auto\\nfs\\getreports\\tmp1\\"; /* Trailing slash must exist */;

//$email_recipients = "brg-it@buckresearch.com,pprasad@centersys.com";
$email_recipients = "pprasad@centersys.com";


$techsupport = "\n\n\n";
$techsupport.= " -------------------------------------------------------------------- \n";
$techsupport.= "|    UTILITY: Archive NFS Report Files to Intranet Storage           |\n";
$techsupport.= "|                                                                    |\n";
$techsupport.= "|    Technical Support:                                              |\n";
$techsupport.= "|    ------------------                                              |\n";
$techsupport.= "|    PRAVIN PRASAD                                                   |\n";
$techsupport.= "|    Mobile: 1-917-704-1885                                          |\n";
$techsupport.= "|    Email: pprasad@centersys.com                                    |\n";
$techsupport.= " -------------------------------------------------------------------- \n";
//==========================================================================================================

$datecriteria = date('md');
//$datecriteria = '0921';
$date_to_process = substr($datecriteria,0,2)."/".substr($datecriteria,2,4); //date('m/d/Y');

//Today's Date should be applied here.
$date_processed_on = date('m/d/Y');
$log_info .= "Processing Day = <b>". $date_to_process . "</b><br><br>\n\n";




$filenames = array('FBNR216A'.$datecriteria.'.TXT','FBNR215A'.$datecriteria.'.TXT','FBNR213A'.$datecriteria.'.TXT','FBNR213B'.$datecriteria.'.TXT');
$check_file = 'FBNR216A'.$datecriteria.'.TXT';
//check if ANY file from above exists at the source location
$source_has_files = 0;
foreach($filenames as $k=>$v) {
	if(file_exists($nfs_source_location.$v)) {
		$source_has_files = 1;
	}
}

//====================================================================================================
//  NEEDS TO RUN ONLY ON NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
//  THIS RUNS ON Tue Wed Thu Fri Sat as a TASK
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
		 ) {
		echo "Today is a holiday, hence terminating program execution!\n";
		exit;
	} else {
		echo "Today is not a holiday, hence proceeding with program execution!\n";
	}
  echo "Proceeding after holiday check....\n";
//====================================================================================================

echo "\nChecking if the previous NFS filecopy attempt succeeded.\n\n";
//checking for file existense in the required folder
$file_at_target = $scriptlocation ."tmp\\".$check_file;
echo "Checking for existence of ".$file_at_target."\n\n";
if (!file_exists($file_at_target)) {
	echo "Have to process the filecopy sequence, as files do not exist at the target location.\n\n";
} else {
	echo "Files exist at the target location, no further processing required.\n\n";
	exit;
}


if ($source_has_files == 1) {
    //PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
		include('nfs.filecopy.php');
} else {
		//wait for 10 minutes and then retry.
		sleep(10);
		echo "Waited for 10 seconds...<br>";
		if ($source_has_files == 1) {
				//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
				include('nfs.filecopy.php');
		} else {
				//wait for 10 more minutes and then retry.
				sleep(3);
				echo "Waited for 10 more seconds...<br>";
				if ($source_has_files == 1) {
						//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
						include('nfs.filecopy.php');
				} else {
						//final try : wait for 10 more seconds and then retry.
						sleep(3);
						echo "Waited for 10 more seconds, final try...<br>";
						if ($source_has_files == 1) {
								//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
								include('nfs.filecopy.php');
						} else {
								$success = 0;
						}
	
				}
		}
}

if ($success == 1) {
								$email_log = "<p class='emailbodytext12'>NFS Reports [213A, 213B, 215A, 216A] for Date ".$date_to_process."</a><br>\n\n";
								$email_log .= $log_info."</p>";
								//create mail to send
								$html_body .= zSysMailHeader("");
								$html_body .= $email_log;
								$html_body .= zSysMailFooter ();
								
								$attachment = array();
								foreach($actual_files as $k=>$v) {
									$attachment[$v] = $filelocation_tdw.$v;
								}
								
								print_r($attachment);
																
								$subject = "NFS Reports [213A, 213B, 215A, 216A] for Date ".$date_to_process;
								$text_body = $subject;
								
								zSysMailer("pprasad@centersys.com", "Pravin Prasad", $subject, $html_body, $text_body, $attachment) ;
								zSysMailer("erolon@buckresearch.com", "Estelle Rolon", $subject, $html_body, $text_body, $attachment) ;
								//zSysMailer("rdaniels@buckresearch.com", "Robert Daniels", $subject, $html_body, $text_body, $attachment) ;
								
} else {
							 //SEND ALERT FOR IMMEDIATE ACTION
							 //SEND OUT A EMAIL WITH THE FAILURE MESSAGE AND EXIT!
							 //EMAIL ROUTINE FOR SENDING THIS ALERT
								$email_log = '
								<hr>
								<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">
								Files on NFS ( '.$nfs_source_location.' ) could not be accessed from TDW Server.<br>
								Please contact appropriate TDW Support Personnel to resolve this issue.</font><br></b>
								<hr>
								<b>
								Thanks.</font></b>
								<hr><br><br><br><br><br><br><br><br><br><br><br>
														';
								//create mail to send
								$html_body .= zSysMailHeader("");
								$html_body .= $email_log;
								$html_body .= zSysMailFooter ();
								
								$subject = "URGENT ALERT : (TDW COULD NOT ACCESS NFS REPORTS ON FTP LOCATION) : (".date('m-d-Y').")";
								$text_body = $subject;
								
								zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
								//zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;

}

	$str_cmd = "del " .$filelocation_tdw ."*.TXT";
	echo $str_cmd."\n"; 
	shell_exec($str_cmd);
	
	$str_cmd = "del " .$filelocation_tdw ."*.zip";
	echo $str_cmd."\n"; 
	shell_exec($str_cmd);

?>