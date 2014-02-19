<?

include('../includes/functions.php');
include('../includes/global.php');
include('../includes/dbconnect.php');

////
// Send email on certain error conditions
function err_email($sub, $msg) {
	$html_body .= zSysMailHeader("");
	$html_body .= $msg;
	$html_body .= zSysMailFooter ();
	$subject = "TDW Error Alert: (".date('m/d/Y h:i a').") : ".$sub;
	$text_body = $subject;
	//zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) 
	zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
}

err_email ("this is a test", "this is a test message");
?>