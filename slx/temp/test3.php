<?
	include('includes/dbconnect.php');
	include('includes/global.php');
	include('includes/functionstest.php');  
	
  $trade_date_to_process = previous_business_day();


echo "Testing emails ...<BR>";
							$linkmailbody = "Please click on the following link to access the Trades Report for ".$trade_date_to_process."\n";
							$linkmailbody .= "\n\nhttp://10.10.10.144/compliance/data/exports/Trades_Report_".$trade_date_to_process.".html";
							$linkmailbody .= "\n\nCompSys Mailer";
							
echo $linkmailbody;							
							
						  mail("pprasad@tocqville.com","Trades Report for Date (".$trade_date_to_process.")","Please click on the following link to access the Trades Report for ".$trade_date_to_process."\n","From: compliance@tocqueville.com <compliance@tocqueville.com>","-fcompliance@tocqueville.com");
						  
echo "<BR>test<BR>"; 

//This one works for SURE!!!
mail("pprasad@tocqueville.com","Test Email 2","This is a test from mail() 2","From: compliance@tocqueville.com <compliance@tocqueville.com>","-fcompliance@tocqueville.com");

//This one works for SURE!!!
$var_value = $linkmailbody;
mail("pprasad@tocqueville.com","Test Email 3",$var_value,"From: compliance@tocqueville.com <compliance@tocqueville.com>","-fcompliance@tocqueville.com");

////
// Sending System Mails

function sys_mail_n($email, $mailsubject, $mailbodysubinfo, $emailheading )
		{
		
		// INCLUDE GLOBAL.PHP for CONSTANTS
			 include('includes/global.php');

					$mailbody = '<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					<style type="text/css">
					<!--
					.names {font-family: Verdana; font-size: 12px; font-weight: bold; color: #333399;}
					.others {font-family: Verdana; font-size: 12px; font-weight: normal; color: #336699;}
					.companyname {font-family: "Times New Roman, Times, serif"; font-size: 16px; font-weight: bold; color: #FFFFFF;}
					.appname {font-family: Verdana; font-size: 14px; font-weight: bold; color: #FFFFFF;}
					.heading {font-family: Verdana; font-size: 12px; font-weight: bold; color: #FFFFFF;}
   				.headingblue {font-family: Verdana; font-size: 12px; font-weight: bold; color: #0000FF;}

					tr.tableheading {	font-family: verdana;	font-size: 12px;	text-decoration: underline;	color: #660000;	font-weight: bold;	text-align: left;	vertical-align: middle;	background-position: left center;	border: 1px solid #0000FF;	background-color: #FFFFFF;}
					tr.tablerow {font-family: verdana;font-size: 10px;text-decoration: none;	color: #000099;	font-weight: normal;	text-align: left;	vertical-align: middle;	background-position: left center;	border: 1px solid #0000FF;}
					tr.tablerowhighlight {font-family: verdana;font-size: 10px;text-decoration: none;	color: #FF0000;	font-weight: bold;	text-align: left;	vertical-align: middle;	background-position: left center;	border: 1px solid #0000FF;}
					-->
					</style>					
					</head>
					<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
					<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top" height="14" bgcolor="#21427B">
						<table width="100%">
							<tr>
								<td class="companyname">'.$_company_name.'</td>
								<td class="appname" align="right">'.$_app_name.' '.$_version.'</td>
							</tr>
							<tr>
								<td colspan=2 class="heading">'.$emailheading.'</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="6" bgcolor="#999999"></td>
					</tr>
					<tr>
						<td valign="top" bgcolor="#FFFFFF">';
						
					$mailbody .= $mailbodysubinfo; 
							
					$mailbody .='</td>
										</tr>
										<tr>
											<td height="6" bgcolor="#999999"></td>
										</tr>
								</table>
								</body>
								</html>';
					
					/* To send HTML mail, you can set the Content-type header. */
					$headers  = "MIME-Version: 1.0\n";
					$headers = "Content-type: text/html; charset=iso-8859-1\n";
					$headers .= "From: Compliance <compliance@tocqueville.com>\n";
			
					$fromval = "compliance@tocqueville.com";
					
					$rp    = 'compliance@tocqueville.com';
          $org    = 'tocqueville.com';
          $mailer = 'CenterSys Compliance';
	

  $headers  .= "Return-Path: $rp\n";
  $headers  .= "From: $fromval\n";
  $headers  .= "Sender: $fromval\n";
  $headers  .= "Reply-To: $fromval\n";
  $headers  .= "Organization: $org\n";
  $headers  .= "X-Sender: $fromval\n";
  $headers  .= "X-Priority: 1\n";
  $headers  .= "X-Mailer: $mailer\n";


										
					//mail($email, $mailsubject, $mailbody, "From: $fullname <$useremail>");
	 return		mail($email,$mailsubject,$mailbody,$headers,"-fcompliance@tocqueville.com");

}



?>

