<?
include('../includes/functions.php');
include('../includes/generate_pdf.php');
include('../includes/global.php');
include('../includes/dbconnect.php');


			$email_log = '
									<table width="100%" border="0" cellspacing="0" cellpadding="10">
										<tr> 
											<td valign="top">
												<p>&nbsp;</p>
												<p><a class="bodytext12"><strong>Test Email: Checking Email Issues (Sending email from TDW Server) : '.date('m/d/Y h:ia').'</strong></a></p>			
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
				
				$subject = "Test Email: Checking Email Issues (Sending email from TDW Server) : ".date('m/d/Y h:ia');
				$text_body = $subject;
				
				
				
				zSysMailer('pprasad@centersys.com', "", $subject, $html_body, $text_body, "") ;
				//zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
				echo $link . "<br>";


?>