<?
//////Functions

// Function to clean strings which somehow appear in weird character sets, 
// as happened in a account shortname download on April 14 2008
// Important Note: & is allowed in the string
function tdw_clean_string($str) {
	return ereg_replace("[^&A-Za-z0-9 _]", "", $str);
}

////
// Used to calculate page load time
function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec*1000); 
} 

//// Function to write data to file
function write_to_file($location, $file, $data) {
	$filename = $location.$file;
	$fp = fopen ($filename, "w");  
	fwrite ($fp,$data);        
	fclose ($fp);   
}

//// Function to write debug information to file for review/troubleshooting
function debug_log($data) {
	$filename = $location.$file;
	$fp = fopen ("d:\\tdw\\tdw\\debug_log.wri", "a");  
	fwrite ($fp, date('Y-m-d h:i:s')."\n");
	fwrite ($fp,$data."\n");        
	fclose ($fp);   
}

////
// Show variable values and debugging information $show_debug = 1; shows debug information
function xdebug ($varname, $varval) {
$show_debug = 1;

if ($show_debug == 1 ) {
	echo '<font face="Verdana" color="#66666" size="1"><strong>'.$varname.'</strong> : </font><font face="Verdana" color="#000000" size="1"><strong>'.$varval.'</font><br>';
	}

/*	if ($show_debug == 1 ) {
	echo '<font color="#0000FF"><strong>'.$varname."</strong></font> = [".$varval."]<br>";
	}
*/
}

////
// Show variable values and debugging information
// $show_debug = 1; shows debug information
function ydebug ($varname, $varval) {
$show_debug = 1;
	if ($show_debug == 1 ) {
	echo $varname." = [".$varval."]\n";
	}
}


////
// Check if a given date is a holiday based on holiday entry in table
function check_holiday ($checkdate) {
	$check = mysql_query("SELECT holi_date from holidays where holi_date = '$checkdate'") or die (mysql_error());
  if (mysql_num_rows($check) >= 1) {
	return 1;
	} else {
	return 0;
	}		
}

////
// Create Trade Report HTML File and put in /data/exports folder
function create_trade_report($email, $mailsubject, $mailbodysubinfo, $emailheading )
		{
		
		// INCLUDE GLOBAL.PHP for CONSTANTS
			 include('includes/global.php');
		 

					$mailbody = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
					<html>
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
								<td class="companyname">Buckingham Capital Management, Inc.</td>
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
								
			//Copy this email body info to a html file and put it in /data/exports folder
			
			$trade_date_to_process_a = previous_business_day();
			
			$fp = fopen($exportlocation."Trades_Report_".$trade_date_to_process_a.".html", "w");

			fputs ($fp, $mailbody);

			fclose($fp);
				
}


////
// Gets the previous business day given a certain day
// IF argument is null then current date (yyyy-mm-dd) is taken as input
//

function previous_business_day($dateval=NULL) {

	if ($dateval==NULL) {
		$working_dateval = date('Y-m-d');
	} else {
		$working_dateval = $dateval;
	}
	
	$i = 1;
	while ($i < 7) {
		 if (date("w",strtotime($working_dateval)-(60*60*24*$i)) > 0 AND
				 date("w",strtotime($working_dateval)-(60*60*24*$i)) < 6 AND
				 check_holiday(date("Y-m-d", strtotime($working_dateval)-(60*60*24*$i))) == 0 ) {
				$val_pbd = date("Y-m-d",strtotime($working_dateval)-(60*60*24*$i));
			 return $val_pbd;
		 } else {
				$i = $i + 1;
		 }
	}
}

/*
function previous_business_day () {

 				$dayname = date("l");
				if ($dayname == "Wednesday" or $dayname == "Thursday" or $dayname == "Friday") {
						$previoustime = time() - (60*60*24*1);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*2);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} elseif ($dayname == "Monday"){
						$previoustime = time() - (60*60*24*3);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*4);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} elseif ($dayname == "Tuesday"){
						$previoustime = time() - (60*60*24*1);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*4);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} elseif ($dayname == "Saturday"){
						$previoustime = time() - (60*60*24*1);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*2);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} else { //essentially Sunday
						$previoustime = time() - (60*60*24*2);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*3);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				}

}
*/

//RETURNS A DAY(YYYY-MM-DD) BEFORE $days BUSINESS DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR BUSINESS DAYS TO GO BACK
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
function business_day_backward($str_date, $days)
{
	$i = 0;
	$j = 0;
	$checkDay = '';
	$subString = $str_date;
	
	while($i < 40)
	{
		$subString = $subString-(60*60*24*1);
		if(date("w", $subString) != 0 AND date("w", $subString) != 6 AND !(check_holiday(date("Y-m-d", $subString))))
		{
			$j++;
			$checkDay =date("Y-m-d", $subString);
		}
		if($j == $days)
		break;
	}
	return $checkDay;
}

//RETURNS A DAY(YYYY-MM-DD) BEFORE $days DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR DAYS TO GO BACK
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
function days_backward($str_date, $days)
{
	$checkDay = '';
	$subString = $str_date;
	$subString = $subString-(60*60*24*$days);
	$checkDay =date("Y-m-d", $subString);
	
	return $checkDay;
}


//RETURNS A DAY(YYYY-MM-DD) AFTER $days BUSINESS DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR BUSINESS DAYS TO GO FORWARD
//THIS IS MAINLY TO CALCULATE SETTLE DATE
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
//This function will also be used to do a bulk process starting on a given day and going forward n days.

function business_day_forward($str_date, $days)
{
	$i = 0;
	$j = 0;
	$checkDay = '';
	$subString = $str_date;
	
	while($i < 40)
	{
		$subString = $subString+(60*60*24*1);
		if(date("w", $subString) != 0 AND date("w", $subString) != 6 AND !(check_holiday(date("Y-m-d", $subString))))
		{
			$j++;
			$checkDay =date("Y-m-d", $subString);
		}
		if($j == $days)
		break;
	}
	return $checkDay;
}

//RETURNS A DAY(YYYY-MM-DD) AFTER $days DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR DAYS TO GO FORWARD
//THIS IS MAINLY TO CALCULATE CHECKS PAYMENT DATA
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
//This function will also be used to do a bulk process starting on a given day and going forward n days.

function day_forward($str_date, $days)
{
	$i = 0;
	$j = 0;
	$checkDay = '';
	$subString = $str_date;
	
	while($i < 40)
	{
		$subString = $subString+(60*60*24*1);
			$j++;
			$checkDay =date("Y-m-d", $subString);
		if($j == $days)
		break;
	}
	return $checkDay;
}


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

//Embed all images that need to go in the emails here
//usage is AddEmbeddedImage(PATH, CID, NAME);
$mail->AddEmbeddedImage('d:\\\\tdw\\tdw\\auto\\autoftp\\includes\\logo.gif', 'tdwlogo', 'logo.gif');
$mail->AddEmbeddedImage('d:\\\\tdw\\tdw\\auto\\autoftp\\includes\\client_app.jpg', 'client_app', 'client_app.jpg');


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
function zSysMailHeader ($headerinfo) {

	return '<html>
<head>
<style type="text/css">
<!--
.bodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.size12bold {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;}
.bodytext8 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 8px;	color: #003399;}
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

function zSysMailFooter () {

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
                <td><a class="bodytext12">Please do not reply to this email address. It is not equipped to handle user enquiries.</a></td><td>&nbsp;</td>
              </tr>
              <tr>
                <td class="bodytext8">Powered by <a href="http://www.centersys.com">CenterSys</a></td><td align="right"><a class="bodytext9">'.date("D, m/d/Y h:i a").'</a></td>
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


function tdw_mysql_error_email($qry) {
				$email_log  = "<b>TDW encountered a serious data error</b>.\n<br>Query: " . $qry . "<br>\nError: (" . mysql_errno() . ") " . mysql_error();

				//create mail to send
				$html_body = "";
				$html_body .= zSysMailHeader("");
				$html_body .= $email_log;
				$html_body .= zSysMailFooter();
				
				$subject = "TDW encountered a serious data error [".date('m/d/Y h:i:sa')."]";
				$text_body = $subject;
				
				zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;

}
////
// Echo the contents of a simple or nested array (Helpful with POST/GET variables)
function show_array($array) { 
    if (is_array($array)){
			foreach ($array as $key => $value) { 
					if (is_array($value)) { 
							echo "[" .$key. "]<br>";
							show_array($value); 
					} else { 
							echo "&nbsp;&nbsp;".$key . " = [" .$value. "]<br>"; 
					} 
			} 
		} else {
							echo "EMPTY ARRAY!<br>"; 
		}
}


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

////
//find if rows were returned from the database.
function empty_qry($result) { 
	$num_rows = mysql_num_rows($result); 
	if ($num_rows > 0) {
	return 1;
	} else {
	return 0;
	}
}


////
// Return a single value from a database query.
// must call the single value single_val
function db_single_val($qry) {
	$result = mysql_query($qry) or die (tdw_mysql_error($qry));
	$count = mysql_num_rows($result);
	//xdebug("Count of single val", $count);
	if ($count > 0) {
		while ( $row = mysql_fetch_array($result) ) 
		{
			$returnval = $row["single_val"];
		}
		return $returnval;
	} else {
		return '';
	}
}

////
// Function to output formatted message in relevant colors
function showmsg ($severity, $msgtext) {
	echo '<table class="msgtbl_'.$severity.'">
			  <tr><td><img src="images/info_'.$severity.'.png" border="0"> '.$msgtext.'</td></tr>
			  </table>';
}
?>