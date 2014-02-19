<?

	include('includes/functions.php');

////
//Function send alert email

function error_alert_email($subject, $message, $to, $cc=NULL, $bcc=NULL) {

	//create mail to send
	$html_body = "";
	$html_body .= zSysMailHeader("");
	$html_body .= $message;
	$html_body .= zSysMailFooter();
	
	$text_body = $subject;
	
	zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;
	zSysMailer('jperno@buckresearch.com', "Jessica Perno", $subject, $html_body, $text_body, "") ;
	zSysMailer('rdaniels@buckresearch.com', "Robert Daniels", $subject, $html_body, $text_body, "") ;
}


?>