<?
//////Alert Functions

// This function uses the PHPMailer to sent html/text emails with or without attachments.
// Tested with the following
// Outlook 2003
// Yahoo
// Hotmail
// Gmail
// AOL

// TODO : Let the attachment be passed either as arrays or singletons and proces them within function depending on what they are.

require("class.phpmailer.php");

//// System Emailer with single or multiple file attachments
// $attachmentname, $attachmentfullpath is in an associative array called $attachment
function alertMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) {

	$mail = new PHPMailer();
	$mail->IsSMTP();                           // tell the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Port       = 587;                    // set the SMTP server port
	$mail->Host       = "owa.smarshexchange.com"; // SMTP server
	$mail->Username   = "TDW@buckresearch.com";     // SMTP server username
	$mail->Password   = "BRmail678";            // SMTP server password

	$mail->AddReplyTo("TDW@buckresearch.com","TDW Buckingham");

	$mail->From       = "TDW@buckresearch.com";
	$mail->FromName   = "TDW Buckingham";

$mail->Subject  = $subject;

//Embed all images that need to go in the emails here
//usage is AddEmbeddedImage(PATH, CID, NAME);
$mail->AddEmbeddedImage('d:\\\\tdw\\tdw\\images\\themes\\standard\\email\\logo.gif', 'tdwlogo', 'logo.gif');
$mail->AddEmbeddedImage('d:\\\\tdw\\tdw\\images\\themes\\standard\\email\\client_app.jpg', 'client_app', 'client_app.jpg');


    // HTML body
    $mail->Body    = $html_body;
    // Plain text body (for mail clients that cannot read HTML)
    $mail->AltBody = $text_body;
		
    $mail->AddAddress($to_email, $to_name);
    
		if (is_array($attachment)) {
			foreach ($attachment as $attachmentname => $attachmentfullpath) {
			//echo $attachmentname."<br>";
			//echo $attachmentfullpath."<br>";
				$mail->AddAttachment($attachmentfullpath, $attachmentname);
			}	
		}
		
    if(!$mail->Send())
        echo "There has been a mail error sending to ".$to_name." (".$to_email.")";

    // Clear all addresses and attachments for next loop
    $mail->ClearAddresses();
    $mail->ClearAttachments();
		
		//echo "<br>Mail sent to ".$to_name. " (".$to_email. ")";
}	

////
//
function alertMailHeader ($headerinfo) {

	return '<html>
<head>
<style type="text/css">
<!--
.bodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.bodytext9 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 9px;	color: #003399;}
.CompanyName {	font-family: "Times New Roman", Times, serif;	font-size: 14px;	font-weight: bold;	color: #21427B;	letter-spacing: 3px;}
.AppName {	font-family: Verdana;	font-size: 14px;	font-weight: bold; 	color: #21427B;}
.bodytext12bb {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal;}
.emailbodytext9 {	font-family: "Times New Roman", Times, serif;	font-size: 9px;	color: #003399;}
.emailbodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.emailbodytext12bb {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal;}
.emailbodytext12bluebold {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: bold;}
.background_heading_row {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #FFFFFF;	font-weight: bold; background-color: #792020;}
.background_data_row_color {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal; background-color: #E6E6E6;}
.background_data_row_white {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal; background-color: #FFFFFF;}
.datatable {
	border-top-color: #000000;
	border-right-color: #0000FF;
	border-bottom-color: #000000;
	border-left-color: #0000FF;
	border-style: solid;
	border-width: 1px 2px;
	border-collapse: collapse;
}
.notetext {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333333;
	font-style: normal;
}
-->
</style>
</head>
<body leftmargin="3" topmargin="3" rightmargin="3" bottommargin="3">
<table class="datatable" width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td valign="top" height="40">
			<table width="100%" height="40" border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="50"><img src="cid:tdwlogo" border="0"></td>
          <td align="left" valign="top" class="bodytext12bb">&nbsp;</td>
          <td width="200" valign="top"><img src="cid:client_app" border="0"></td>
        </tr>
      </table>
      <hr size="4" noshade color="#91B5E7">
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table>
				<tr>
					<td valign="top" class="notetext">
					<!--email body-->';
}

function alertMailFooter () {

	return '<!--end email body-->
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table height="100%" width="100%">
        <tr>
          <td valign="bottom" height="30">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a class="bodytext12">Please do not reply to this email address. It is not equipped to handle user enquiries.</a></td>
              </tr>
              <tr>
                <td align="right"><a class="bodytext9">'.date("D, m/d/Y h:i a").'</a></td>
              </tr>
            </table>
					</td>
        </tr>
      </table>
		</td>
	</tr>
</table>
</body>
</html>';

}


////
// Send email on certain error conditions
function alert_email($sub, $msg) {
	$html_body .= alertMailHeader("");
	$html_body .= $msg;
	$html_body .= alertMailFooter ();
	$subject = "TDW Error Alert: (".date('m/d/Y h:i a').") : ".$sub;
	$text_body = $subject;
	alertMailer("prasad_pravin@yahoo.com", "", $subject, $html_body, $text_body, "") ;
}

alert_email("This is a test", "This is a test message");
?>