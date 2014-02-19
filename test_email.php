<?
include('./includes/functions.php');
include('./includes/global.php');
include('./includes/dbconnect.php');


			$email_log = '
									<table width="100%" border="0" cellspacing="0" cellpadding="10">
										<tr> 
											<td valign="top">
												<p>&nbsp;</p>
												<p><a class="bodytext12"><strong>Test Email using Smarsh (owa.smarshexchange.com) SMTP/TLS Port 587 : '.date('m/d/Y h:ia').'</strong></a></p>			
												<p>&nbsp;</p>
												<p>&nbsp;</p>
												<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p></td>
										</tr>
									</table>
										';
				//create mail to send
				$html_body = "";
				$html_body .= zSysMailHeader("");
				$html_body .= $email_log;
				$html_body .= zSysMailFooter ();
				
				$subject = "TEST EMAIL TDW PROD SERVER : using Smarsh (owa.smarshexchange.com) SMTP/TLS Port 587 : ".date('m/d/Y h:ia');
				$text_body = $subject;
				
				
				
				zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;
				echo "Mail sent";
				zSysMailer('brg-it@buckresearch.com', "BRG IT", $subject, $html_body, $text_body, "") ;
				echo "Mail sent";
?>