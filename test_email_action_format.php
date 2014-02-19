<?
include('./includes/functions.php');
include('./includes/generate_pdf.php');
include('./includes/global.php');
include('./includes/dbconnect.php');

$email_log = '<table width="100%" border="0" cellspacing="0" cellpadding="10">
							<tr> 
								<td valign="top">
									<p>&nbsp;</p>
									<script language="javascript">
									function test () {
									alert("test");
									}
									</script>
									<p><a class="bodytext12"><strong>
									Test Email: Checking Email Issues (Sending email from TDW Server) : '.date('m/d/Y h:ia').'</strong></a></p>			
									<p>&nbsp;</p>
										<form action="http://192.168.20.63/tdw/test_email_action.php" method="get" target="_blank">
										<textarea name="zcomment" rows="4" cols="50"></textarea><br>
										<label><input type="radio" name="apprdeny" value="1" id="apprdeny_0">Approved</label>
										&nbsp;&nbsp;
										<label><input type="radio" name="apprdeny" value="0" id="apprdeny_1">Not Approved</label><br>
										<input type="submit" name="Save" value="Save">
										</form>		
										<input type="button" name="test" value="test" onclick="test();">										
									<p>&nbsp;</p>
									<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p></td>
								</tr>
							</table>';
	//create mail to send
	$html_body = "";
	$html_body .= zSysMailHeader("");
	$html_body .= $email_log;
	$html_body .= zSysMailFooter ();
	
	$subject = "TDW (Prospect Proposed by XYZ on ...) ".date('m/d/Y h:ia');
	$text_body = $subject;
	
	
	
	zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;
	echo "Email Sent" . "<br>" . rand(1111,9999);
?>