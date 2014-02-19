<?
ini_set('max_execution_time', 1600);
?>

<?
include('nfs.config.inc.php');
include('nfs.functions.php');
include('mailer_functions.php');

//network location of the nfs files being sent over by NFS via ftp
$nfs_source_location = "\\\\brgnfs01.buckresearch.com\\nfs$\\";

//First do a check to see if the filesystem can be accessed
$check_file = "TRDREV_TDx.DAT";

if (file_exists($nfs_source_location.$check_file)) {
    //PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
} else {
		//wait for 3 minutes and then retry.
		sleep(3);
		echo "Waited for 3 seconds...<br>";
		if (file_exists($nfs_source_location.$check_file)) {
				//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
		} else {
				//wait for 3 more minutes and then retry.
				sleep(3);
				echo "Waited for 3 more seconds...<br>";
				if (file_exists($nfs_source_location.$check_file)) {
						//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
				} else {
						//final try : wait for 3 more minutes and then retry.
						sleep(3);
						echo "Waited for 3 more seconds, final try...<br>";
						if (file_exists($nfs_source_location.$check_file)) {
								//PROCEED WITH THE BACKUP AND COPYING OF NFS FILES
						} else {
								//final try : wait for 3 more minutes and then retry.
								sleep(3);
								echo "Waited for 3 more seconds, final try...<br>";
							
							 //SEND ALERT FOR IMMEDIATE ACTION
							
							 //SEND OUT A EMAIL WITH THE FAILURE MESSAGE AND EXIT!

								//EMAIL ROUTINE FOR SENDING THIS ALERT
								$email_log = '
								<hr>
								<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">
								Files on NFS ( \\\\192.168.20.93\\nfs$ ) could not be accessed from TDW Server.<br>
								Please contact appropriate TDW Support Personnel to resolve this issue.</font><br></b>
								<hr>
								<b><font color="#000000">Details of Server which receives NFS Files:</font></b><br>
								<font color="#000000">Server: </font><b><font color="#FF0000">192.168.20.93</font></b><br>
								<font color="#000000">Share Name: </font><b><font color="#FF0000">nfs$</font></b>
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
								
								$subject = "URGENT ALERT : (TDW COULD NOT ACCESS NFS FILES ON FTP LOCATION) : (".date('m-d-Y').")";
								$text_body = $subject;
								
								zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
							
								}
	
				}
		}
}


























?>