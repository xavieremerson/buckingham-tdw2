<?
//************************************************************************************************
//************************************************************************************************
/*
THIS FILE IS TO BE RUN AS A JOB AT 2:00am IN THE MORNING SO USING THE PREVIOUS BUSINESS DAY FUNCTION
IT WILL ONLY PROCESS FILES THAT ARE CREATED IN THE PREVIOUS BUSINESS DAY. WILL CREATE SEPARATE EMAILS
FOR EACH FAX DOCUMENT.

THIS FILE MUST BE RUN AS SHELL CMD IN BAT
*/
//************************************************************************************************
//************************************************************************************************
include('fax.config.inc.php');
include('fax.functions.php');

include('../../includes/functions.php');

//---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&
function faxMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) {

	$mail = new PHPMailer();
	$mail->IsSMTP();                           // tell the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Port       = 587;                    // set the SMTP server port
	$mail->Host       = "owa.smarshexchange.com"; // SMTP server
	$mail->Username   = "TDW@buckresearch.com";     // SMTP server username
	$mail->Password   = "BRmail678";            // SMTP server password

	$mail->AddReplyTo("TDW@buckresearch.com","TDW Buckingham");

	$mail->From       = "TDW@buckresearch.com";
	$mail->FromName   = "FAX Archive Util";


$mail->Subject  = $subject;

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
		
		echo "<br>Mail sent to ".$to_name. " (".$to_email. ")<br>";
}	
//---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&---&

//DATE TO PROCESS
//$date_to_process = previous_business_day();
//$date_to_process = "2008-03-17";
//*************************************************************************************************
if($_GET) {
	$date_input = $d; //
	$emailbox = $e;
	$it_email = $a;  
} else {
	$date_input = date('Y-m-d'); //
	$emailbox = 'faxarchive@buckresearch.com';
	$it_email = 'brg-it@buckresearch.com';  
	//echo "Cannot proceed without input parameters. USAGE: ?d=YYYY-MM-DD&e=xxxxx@buckresearch.com";
}

$previousday = mktime(0,0,0,substr($date_input,5,2),substr($date_input,8,2)-1,substr($date_input,0,4));
//echo "Previous day is ".date("Y/m/d", $previousday);

$date_to_process = date("Y-m-d", $previousday);

echo "Input/Run Date : ".$date_input."<br>";
echo "Date to process : ".$date_to_process."<br>"; //$yesterday = mktime(0,0,0,substr($date_to_process,5,2),substr($date_to_process,5,2)-1,substr($date_to_process,5,2));
echo "Recipient Email Address : ".$e."<br><br>";
//exit;
//*************************************************************************************************

$match_date = str_replace("-","/",$date_to_process);
$folder_year = substr($date_to_process,0,4);
$arr_months = array("01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr","05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");
$folder_month = $arr_months[substr($date_to_process,5,2)];

$serial_number = '8311906';
$str_query = "SELECT FPFAXINFO.FAXPRESSSERIALNUMBER as id, 
																FPFAXINFO.JOBID as name, 
																FPFAXINFO.DATETIME, 
																FPUSERS.USERNAME, 
																FPFAXINFO.DESTINATIONNUMBER, 
																FPFAXINFO.COMPANYRTI, 
																FPFAXINFO.DESCRIPTION, 
																FPFAXINFO.STATUS, 
																FPFAXINFO.PAGES, 
																FPMISCINFO.INFOVALUE, 
																FPFILEINFO.FILENAME
													FROM (FPFILEINFO 
																	INNER JOIN (
																							FPUSERS 
																							INNER JOIN (
																													FPJOBTYPE 
																													INNER JOIN FPFAXINFO ON FPJOBTYPE.JOBTYPEID = FPFAXINFO.JOBTYPEID
																													) 
																							ON FPUSERS.USERID = FPFAXINFO.USERID
																							) 
																	ON FPFILEINFO.FILEID = FPFAXINFO.FILEID
																	) 
															INNER JOIN FPMISCINFO ON FPFAXINFO.RESOLUTIONID = FPMISCINFO.MISCINFOID
													WHERE (((FPFAXINFO.FAXPRESSSERIALNUMBER)=".$serial_number.") 
													  AND ((FPJOBTYPE.JOBTYPE)='Outgoing Fax'))
														AND FPFAXINFO.STATUS='Sent';";
													

//$str_query = "SELECT * from FPFAXINFO where STATUS = 'Sent'";
//$mdb_location = "R:\\PravinPrasad\\test\\test.mdb";

//$mdb_location = "\\\\buckfax2\\fpressdb\\archive.mdb";
//$faxfile_location = "\\\\buckfax2\\c$\\CASTELLE\\ARCHIVE\\FAXES\\Outgoing\\";

$mdb_location = "\\\\bucknav2\\fpressdb\\archive.mdb";
$faxfile_location = "\\\\bucknav2\\CASTELLE\\ARCHIVE\\FAXES\\Outgoing\\";

$db_conn = new COM("ADODB.Connection"); 
$connstr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=". realpath($mdb_location).";"; 
$db_conn->open($connstr); 
$rS = $db_conn->execute($str_query); 

$f1 =  $rS->Fields(0); 
$f2 =  $rS->Fields(1); 
$f3 =  $rS->Fields(2); 
$f4 =  $rS->Fields(3); 
$f5 =  $rS->Fields(4); 
$f6 =  $rS->Fields(5); 
$f7 =  $rS->Fields(6); 
$f8 =  $rS->Fields(7); 
$f9 =  $rS->Fields(8); 
$f10 = $rS->Fields(9); 
$f11 = $rS->Fields(10); 

$str_file_text = "";
$str_file_html = "";
$doc_count = 0;

while (!$rS->EOF) 
{ 
    		//print $f1->value." ".$f2->value." ".$f3->value." ".$f4->value." ".$f5->value." ".$f6->value." ".$f7->value." ".$f8->value." ".$f9->value." ".$f10->value." ".$f11->value."<br />\n"; 
		 
				if (substr($f3->value,0,10)==$match_date) {
							
							$doc_count = $doc_count + 1;			
							
							$str_file_text .= "\n".$f11->value;
							$str_file_html .= "<br>".$f11->value;
							
							$copy_cmd = 'copy "'.$faxfile_location.$folder_year."\\".$folder_month."\\".$f11->value. '" "'.$faxlocation_tdw.'"';
							//echo "Copy Command = ". $copy_cmd."\n<br>";
							echo "Processing file : " . $faxfile_location.$folder_year."\\".$folder_month."\\".$f11->value."\n<br>";
							shell_exec($copy_cmd);
					
							//---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
							$v_filename = $faxlocation_tdw . $f11->value;
							echo "Local/Temporary file : " . $v_filename."\n";
						
							if (file_exists($v_filename) ) {  //and 1==2
								
								 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
								 //Create and send emails:
										$email_log_txt = 'ATTACHMENT FILENAME : '. $f11->value ."\n".
																		 'FAX DESTINATION : '.str_replace("\$",'',$f5->value)."\n".
																		 'DATE/TIME : '.$f3->value."\n".
																		 'COMPANY : '.$f6->value."\n".
																		 'PAGES : '.$f9->value."\n".
																		 'DESCRIPTION : '.$f7->value."\n";

										$email_log_html = 'ATTACHMENT FILENAME : '. $f11->value ."<br>".
																			'FAX DESTINATION : '.str_replace("\$",'',$f5->value)."<br>".
																			'DATE/TIME : '.$f3->value."<br>".
																		  'COMPANY : '.$f6->value."<br>".
																		  'PAGES : '.$f9->value."<br>".
																		  'DESCRIPTION : '.$f7->value."<br>";

										//create mail to send
										$subject = "FAX: Destination = ".str_replace("\$",'',$f5->value). " Date/Time = ".$f3->value." File = ".$f11->value;
										$text_body = $email_log_txt;
										$html_body = $email_log_html;
										
										$arr_attachpdf = array($f11->value=>$faxlocation_tdw ."\\". $f11->value);
										
										faxMailer($emailbox, "", $subject, $html_body, $text_body, $arr_attachpdf);
										//faxMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, $arr_attachpdf);
										echo "<br>";
										ob_flush();
										flush();

								 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++		 
							}  else {
										// FILE DOES NOT EXIST
										echo "\n\nERROR!\n\n";
							}
							//---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+

					}
					
		$rS->MoveNext(); 
} 
$rS->Close(); 
$db_conn->Close(); 

//000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000
//Create and send admin report email:

if ($doc_count > 0) {
	$email_log_txt = "\n".'Count of faxes archived via email : '. $doc_count ."\n".
									 'Files : '.str_replace("\$",'',$str_file_text)."\n".
									 'Process completed at : '.date('m/d/Y h:i:sa')."\n";

	$email_log_html = "<br>".'Count of faxes archived via email : '. $doc_count ."<br>".
									 'Files : '.str_replace("\$",'',$str_file_html)."<br>".
									 'Process completed at : '.date('m/d/Y h:i:sa')."<br>";
									 
	$subject = $doc_count . " fax(s) archived via email using Fax Archive Util";
} else {
	$email_log_txt = "\n".'No faxes detected by Fax Archive Util'."\n".
									 'Process completed at : '.date('m/d/Y h:i:sa')."\n";

	$email_log_html = "<br>".'No faxes detected by Fax Archive Util'."<br>".
									 'Process completed at : '.date('m/d/Y h:i:sa')."<br>";
									 
	$subject = "No faxes detected by Fax Archive Util";
}

$text_body = $email_log_txt;
$html_body = $email_log_html;

faxMailer($it_email, "", $subject, $html_body, $text_body, "");
faxMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "");
echo "<br>";
ob_flush();
flush();

//000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000+++000

echo "Deleting temporary files ...<br>";
$del_cmd = 'del ' . $faxlocation_tdw .'*.pdf';
//echo $del_cmd."\n";
shell_exec($del_cmd);

echo "<br><img src='cmg.gif' border='0'> <font color='green'><strong>Process completed.</strong></font>";
exit;
?>