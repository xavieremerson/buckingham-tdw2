<?

include('../../../includes/dbconnect.php');
include('../../../includes/functions.php'); 
include('../../../includes/global.php'); 

$qry = "insert into aaz values('asdasd')";

$result = mysql_query($qry);
if (!$result) {

    $message  = '<br>Invalid query: ' . mysql_error() . "\n<br>";
    $message .= 'Whole query: ' . $qry;

		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		echo "[NOT PROCESSED] Bloomberg File "."Orders_Trades".$date_to_process.".\n";
		//EMAIL ROUTINE TO SUPPORT
		$email_log = '
						<hr> 
						<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">'.$message.'
						</font>
						</b>
						<hr>
						<br><br><br><br><br><br><br><br><br><br><br>
								';
		//create mail to send
		$html_body .= zSysMailHeader("");
		$html_body .= $email_log;
		$html_body .= zSysMailFooter ();
		
		$subject = "[NOT PROCESSED] Bloomberg File "."Orders_Trades".$date_to_process." (".date('m-d-Y').")";
		$text_body = $subject;
		
		zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
		//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

    die($message);
}


?>