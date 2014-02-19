<?
ini_set('max_execution_time', 7200);
?>

<?
include('nfs.config.inc.php');
include('dbconnect.php');
include('nfs.functions.php');
include('mailer_functions.php');

//network location of the nfs files being sent over by NFS via ftp
//$nfs_source_location = "\\\\brgnfs01.buckresearch.com\\nfs$\\";
//Changed the above because of a crash issue where it was not accessible, using IP instead
$nfs_source_location = "\\\\192.168.20.93\\nfs$\\";

//First do a check to see if the filesystem can be accessed
$check_file = "TRDREV_TD.DAT";

for ($i=0;$i<1000;$i++) {

	if (file_exists($nfs_source_location.$check_file)) {
			echo "Successful Connection at ".date('m/d/Y h:i:s a')."!\n\n";
			$success = 1;
	} else {
			echo "Failed to Connect at ".date('m/d/Y h:i:s a')."!\n\n";
			$success = 0;
	}

	if ($success == 0) {
		 //SEND ALERT FOR IMMEDIATE ACTION
		 //SEND OUT A EMAIL WITH THE FAILURE MESSAGE AND EXIT!
		 //EMAIL ROUTINE FOR SENDING THIS ALERT
			$email_log = '
			<hr>
			<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">
			Files on NFS ( \\\\192.168.20.93\\nfs$ ) could not be accessed from TDW Server.<br>
			Please contact appropriate TDW Support Personnel to resolve this issue.</font><br></b>
			<hr><br><br><br><br><br><br><br><br><br><br><br>
									';
			//create mail to send
			$html_body .= zSysMailHeader("");
			$html_body .= $email_log;
			$html_body .= zSysMailFooter ();
			
			$subject = "URGENT ALERT : (TDW COULD NOT ACCESS NFS FILES ON FTP LOCATION) : (".date('m-d-Y').")";
			$text_body = $subject;
			
			zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
	
	}
sleep(5);
}


?>