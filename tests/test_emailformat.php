<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');


				$link = $_site_url."repsvr.php?rep=DCARV2&src=".rand(10000000,99999999).str_replace('-','N',$trade_date_to_process).str_pad($user_id,10,'Q',1).md5("pprasad@centersys.com");
				
				$email_log = '
									<table width="100%" border="0" cellspacing="0" cellpadding="10">
										<tr> 
											<td valign="top">
												<p><a class="bodytext12"><strong>Daily Compliance Activity Report (v2)</strong></a></p>			
												<p><a class="bodytext12">Trade Date: <strong>'.$date_to_show.'</strong></a></p>
												<p class="bodytext12">Please click <strong><a href="'.$link.'">&gt;&gt;HERE&lt;&lt;</a></strong> to access the report.</p>
												<p>&nbsp;</p>
												<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p></td>
										</tr>
									</table>
										';
				//create mail to send
				$html_body = "";
				$html_body .= zSysMailHeader("");
				$html_body .= $email_log;
				$html_body .= zSysMailFooter();
				
				$subject = "Daily Compliance Activity Report (v2) : (Trade Date: ".$date_to_show.")";
				$text_body = $subject;
				
				zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;
				zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;

?>
