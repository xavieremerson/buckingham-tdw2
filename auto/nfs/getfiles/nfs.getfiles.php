<?
ini_set('max_execution_time', 1600);
include('nfs.config.inc.php');
include('dbconnect.php');
include('nfs.functions.php');
include('mailer_functions.php');

//network location of the nfs files being sent over by NFS via ftp
//$nfs_source_location = "\\\\brgnfs01.buckresearch.com\\nfs$\\";
//Changed the above because of a crash issue where it was not accessible, using IP instead
//$nfs_source_location = "\\\\192.168.20.93\\nfs$\\";
$nfs_source_location = "\\\\192.168.20.54\\nfs\\";



//This process has been failing at times for reasons not known yet but the idea is to
//run this process 5-6 times at intervals of 15 minutes so a failure would indicate a
//rather probe-able situation.

echo "\nChecking if the previous NFS filecopy attempt succeeded.\n\n";

//checking for file existense in the required folder
$file_at_target = $filelocation_tdw . previous_business_day()."\\TRDREV_TD.DAT";


echo "Checking for existence of ".$file_at_target."\n\n";
if (!file_exists($file_at_target)) { //|| 1==1
echo "Have to process the filecopy sequence, as files do not exist at the target location.\n\n";
} else {
echo "Files exist at the target location, no further processing required.\n\n";
exit;
}

//====================================================================================================
//  NEEDS TO RUN ONLY ON NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
//  THIS RUNS ON Tue Wed Thu Fri Sat as a TASK
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
		 ) {
		echo "Today is a holiday, hence terminating program execution!";
		exit;
	} else {
		echo "Today is not a holiday, hence proceeding with program execution!";
	}
  echo "Proceeding after holiday check....";
//====================================================================================================


//First do a check to see if the filesystem can be accessed
$check_file = "TRDREV_TD.DAT";

if (file_exists($nfs_source_location.$check_file)) {
    //PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
		include('nfs.filecopy.php');
} else {
		//wait for 10 minutes and then retry.
		sleep(10);
		echo "Waited for 10 seconds...<br>";
		if (file_exists($nfs_source_location.$check_file)) {
				//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
				include('nfs.filecopy.php');
		} else {
				//wait for 10 more minutes and then retry.
				sleep(3);
				echo "Waited for 10 more seconds...<br>";
				if (file_exists($nfs_source_location.$check_file)) {
						//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
						include('nfs.filecopy.php');
				} else {
						//final try : wait for 10 more seconds and then retry.
						sleep(3);
						echo "Waited for 10 more seconds, final try...<br>";
						if (file_exists($nfs_source_location.$check_file)) {
								//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
								include('nfs.filecopy.php');
						} else {
								$success = 0;
						}
	
				}
		}
}

if ($success == 1) {
								$email_log = "<p class='emailbodytext12'>NFS Files for Trade Date ".$date_to_process. " successfully copied to <a href='".$net_data_location.$folder_to_create."'>".$net_data_location.$folder_to_create."</a><br>\n\n";
								$email_log .= $log_info."</p>";
								//create mail to send
								$html_body .= zSysMailHeader("");
								$html_body .= $email_log;
								$html_body .= zSysMailFooter ();
								
								$subject = "Notice: NFS Files for Trade Date ".$date_to_process . " copied successfully.";
								$text_body = $subject;
								
								zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
								zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
								
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
								<b><font color="#000000">Details of Server which receives NFS Files:</font></b><br>
								<font color="#000000">Location: </font><b><font color="#FF0000">'.$nfs_source_location.'</font></b><br>
								<hr>
								<b>
								<font color="#0000ff" size="2" face="Arial, Helvetica, sans-serif">
								Please, manually copy all files on this share to an alternate location<br>
								preferably <u>R:\CenterSys</u>, in a new folder, as these files get <br>
								overwritten by NFS on a daily basis.<br> 
								Thanks.</font></b>
								<hr><br><br><br><br><br><br><br><br><br><br><br>
														';
								//create mail to send
								$html_body .= zSysMailHeader("");
								$html_body .= $email_log;
								$html_body .= zSysMailFooter ();
								
								$subject = "URGENT ALERT : (TDW COULD NOT ACCESS NFS FILES ON FTP LOCATION ".$nfs_source_location.") : (".date('m-d-Y').")";
								$text_body = $subject;
								
								zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
								zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;

}


exit;
?>