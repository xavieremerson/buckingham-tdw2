<?
//// System Emailer with single or multiple file attachments
// $attachmentname, $attachmentfullpath is in an associative array called $attachment

require("class.phpmailer.php");

function zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) {

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

    // HTML body
    $mail->Body    = $html_body;
    // Plain text body (for mail clients that cannot read HTML)
    $mail->AltBody = $text_body;
		
    $mail->AddAddress($to_email, $to_name);
    
		if (is_array($attachment)) {
			foreach ($attachment as $attachmentname => $attachmentfullpath) {
			echo $attachmentname."<br>";
			echo $attachmentfullpath."<br>";
				$mail->AddAttachment($attachmentfullpath, $attachmentname);
			}	
		}
		
    if(!$mail->Send())
        echo "There has been a mail error sending to ".$to_name." (".$to_email.")";

    // Clear all addresses and attachments for next loop
    $mail->ClearAddresses();
    $mail->ClearAttachments();
		
		echo "<br>Mail sent to ".$to_name. " (".$to_email. ")";
}	


////
//
function zSysMailHeader ($headerinfo) {

	return '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.bodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.bodytext9 {	font-family: "Times New Roman", Times, serif;	font-size: 9px;	color: #003399;}
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
-->
</style>
</head>
									
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="10">
	<tr> 
		<td>
			<table width="100%" height="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#91B5E7">
				<tr> 
					<td>
						<table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
							<tr> 
								<td valign="top">
									<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
										<tr> 
											<td width="50"><img src="http://csysg.dyndns.org:83/brg/images/email/logo.gif" border="0"></td>
											<td align="left" valign="top" class="bodytext12bb">&nbsp;</td>
											<td width="200" valign="top"><img src="http://csysg.dyndns.org:83/brg/images/email/client_app.jpg" border="0"></td>
										</tr>
									</table>
									<hr size="4" noshade color="#91B5E7">
								</td>
							</tr>
							<tr>
								<td height="100" valign="top">';
}

function zSysMailFooter () {

	return '
								</td>
							</tr>
							<tr> 
								<td valign="bottom">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr> 
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><a class="bodytext12">Please do not reply to this email address. It is not equipped to handle user enquiries.</a></td>
										</tr>
										<tr> 
											<td valign="bottom" align="right"><a class="bodytext9">'.date("D, m/d/Y h:i a").'</a></td>
										</tr>
									</table>																	
								</td>
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


?>