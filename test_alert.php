<?

  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');


// Send email on certain error conditions
function xerr_email($sub, $msg) {
	$html_body .= zSysMailHeader("");
	$html_body .= $msg;
	$html_body .= zSysMailFooter ();
	$subject = "TDW Error Alert: (".date('m/d/Y h:i a').") : ".$sub;
	$text_body = $subject;
	//zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) 
	zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
}



$sub = "Test Alert";
$msg = "This is a test message";
xerr_email($sub, $msg);

?>