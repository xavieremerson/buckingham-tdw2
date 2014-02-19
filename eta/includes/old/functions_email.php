<?
//////Email Functions

//// 
// Sending HTML Emails with or without attachments

function html_email_system ($to, $mailsubject, $mailbodysubinfo, $emailheading, $fileattach, $control_id) {
								  
	include('includes/class.Email.php');
	include('global.php');    
	
	$Sender = $_system_email_sender;
	$Recipient = $to; 
	$Cc = ""; 
	$Bcc = ""; 
	
	if(substr($fileattach,-3,3) == 'tml' OR substr($fileattach,-3,3) == 'htm')
	{
		$file_type = 'text/html'; 
		$file_format = 'HTML Format';
	}
	else
	{
		$file_type = 'application/pdf';
		$file_format = 'PDF Format';
	}

	//** create HTML version of the body content.
	$htmlVersion = '<html>
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
												<table width="100%" height="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#990000">
													<tr> 
														<td>
															<table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
																<tr> 
																	<td height="55" valign="top">
																	<!-- Header background="'.$_site_url.'/images/table_bkground_grey.jpg"-->
																		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
																			<tr> 
																				<td width="50" height="50"><img src="'.$_client_logo_url.'" border="0"></td>
																				<td align="left" valign="top" class="bodytext12bb">'.$emailheading.'</td>
																				<td width="200">
																					<table width="100%">
																						<tr>
																							<td align="right" valign="top" nowrap><a class="AppName">&nbsp;&nbsp;&nbsp;&nbsp;'.$_app_fullname.'</a></td>
																						</tr>
																						<tr>
																							<td align="right" nowrap><a class="AppName"> </a></td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="100" valign="top">
																	<a class="bodytext12">'.$mailbodysubinfo.'  </a>
																	</td>
																</tr>
																<tr> 
																	<td valign="bottom">
																		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																			<tr> 
																				<td>&nbsp;</td>
																			</tr>
																			<tr>
																				<td><a class="bodytext12">&nbsp;</a></td>
																			</tr>
																			<tr> 
																				<td valign="bottom" align="right"><a class="bodytext9">'.date("D, m/d/Y h:i a").' CTRL# : '.$control_id.'</a></td>
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

	unset($msg); 
	//** create the new message using the to, from, and email subject. 
	$msg = new Email($Recipient, $Sender, $mailsubject); 
	$msg->Cc = $Cc; 
	$msg->Bcc = $Bcc; 
											
	//** set the message to be text only and set the email content. 
	$msg->TextOnly = false; 
	$msg->Content = $htmlVersion; 
			
	//** attach this script itself to the message.
	if($fileattach != '')
	{
		$msg->Attach($exportlocation.$fileattach, $file_type); 										
	}
	
	//** send the email message. 
	$SendSuccess = $msg->Send(); 
			
	//echo "HTML email w/attachment was ", ($SendSuccess ? "sent" : "not sent"), "<br>"; 
	unset($msg); 

}

//THE DIFFERENCE BETWEEN THIS FUNCTION AND HTML_EMAILS IS THAT THIS CAN HAVE A DYNAMIC SENDER 
//AS WELL AS DYNAMIC MAILBODYSUBINFO WHICH ARE PASSED AS AN ARGUMENT.
function html_email_person ($to, $from, $mailsubject, $mailbodysubinfo, $emailheading, $fileattach, $control_id) {
								  
	include('includes/class.Email.php');
	include('includes/global.php');    
	
	//* establish to,from, and any other recipients. 
	//$Sender = "compliance@yahoo.com"; 
	$Sender = $from;
	$Recipient = $to; 
	$Cc = ""; 
	$Bcc = ""; 
	
	$mailbodysubinfo = str_replace ("\n", "<br>", $mailbodysubinfo);

	//** create HTML version of the body content.
	$htmlVersion = '<html>
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
												<table width="100%" height="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#990000">
													<tr> 
														<td>
															<table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
																<tr> 
																	<td height="55" valign="top">
																	<!-- Header background="'.$_site_url.'/images/table_bkground_grey.jpg"-->
																		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
																			<tr> 
																				<td width="50" height="50"><img src="'.$_client_logo_url.'" border="0"></td>
																				<td align="left" valign="top" class="bodytext12bb">'.$emailheading.'</td>
																				<td width="200">
																					<table width="100%">
																						<tr>
																							<td align="right" valign="top" nowrap><a class="AppName">&nbsp;&nbsp;&nbsp;&nbsp;'.$_app_fullname.'</a></td>
																						</tr>
																						<tr>
																							<td align="right" nowrap><a class="AppName"> </a></td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="100" valign="top">
																	<a class="bodytext12">
																		'.$mailbodysubinfo.'
																	</a>
																	</td>
																</tr>
																<tr> 
																	<td valign="bottom">
																		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																			<tr> 
																				<td>&nbsp;</td>
																			</tr>
																			<tr>
																				<td><a class="bodytext12">IMPORTANT: Please do not reply to this message as this email address is not equipped to handle customer service inquiries.</a></td>
																			</tr>
																			<tr> 
																				<td valign="bottom" align="right"><a class="bodytext9">'.date("D, m/d/Y h:i a").' CTRL# : '.$control_id.'</a></td>
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

	unset($msg); 
	//** create the new message using the to, from, and email subject. 
	$msg = new Email($Recipient, $Sender, $mailsubject); 
	$msg->Cc = $Cc; 
	$msg->Bcc = $Bcc; 
											
	//** set the message to be text only and set the email content. 
	$msg->TextOnly = false; 
	$msg->Content = $htmlVersion; 
			
	//** attach this script itself to the message. 
	//$msg->Attach('/var/www/html/dev_demo_compliance/data/exports/'.$fileattach, "application/pdf"); 										
	
	//** send the email message. 
	$SendSuccess = $msg->Send(); 
			
	//echo "HTML email w/attachment was ", ($SendSuccess ? "sent" : "not sent"), "<br>"; 
	unset($msg); 

}

?>