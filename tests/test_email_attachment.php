<?
include('../includes/functions.php');
include('../includes/generate_pdf.php');
include('../includes/global.php');
include('../includes/dbconnect.php');

$html_body = zSysMailHeader('test');
$html_body .= "In the final decision-making meeting with Alan Greenspan as the Fed chairman, the U.S. central bank raised the federal funds rate -- 
the interest rate banks charge each other on overnight loan -- to the highest level in near five years. It was also the Fed's 14th 
quarter-point increase in a credit-tightening that began about 20 months ago. 

    In a statement released after the meeting, the U.S. Fed said Although recent economic data have been uneven, 
	the expansion in economic activity appears solid. Core inflation has stayed relatively low in recent months and 
	longer-term inflation expectations remain contained. 

    Nevertheless, possible increases in resource utilization as well as elevated energy prices have the potential to 
	add to inflation pressures, it said. 

    The Committee judges that some further policy firming may be needed to keep the risks to the attainment of both 
	sustainable economic growth and price stability roughly in balance, the statement said. ";
	
$html_body .= zSysMailFooter('test');

$mailsubject = "This is a test subject w/ attachment";
$text_body = "This is a test";

//create file attachments
$attachment = array();
$attachment['a.pdf'] = $exportlocation."a.pdf";
$attachment['b.pdf'] = $exportlocation."b.pdf";
$attachment['c.pdf'] = $exportlocation."c.pdf";

print_r($attachment);
zSysMailer('pprasad@centersys.com', 'Pravin Prasad', $mailsubject, $html_body, $text_body, $attachment);

/*
$trade_date_to_process = previous_business_day();

$htmlfiledata = pdf_header_portrait($trade_date_to_process);
$htmlfiledata .= "This is a test";															
$htmlfiledata .= pdf_footer_portrait();
$str_filename = "TREMP_".$trade_date_to_process.".html";
$str_pdfname = "TREMP_".$trade_date_to_process.".pdf";
$fp = fopen($exportlocation.$str_filename, "w");
fputs ($fp, $htmlfiledata);
fclose($fp);
shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							
xdebug("File Written and PDF Created",$str_filename);
xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
$mailsubject = "Compliance Report for ". $trade_date_to_process;
$email_heading = "Compliance Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
xdebug("email_heading",$email_heading);
$fileattach = $str_pdfname;
$control_id = gen_control_number();
$mailbodysubinfo = 'Please find attached (PDF Format), the Compliance Report you generated on '.date("D, m/d/Y h:i a");

//zMailer('tundra@sundra.com', 'Tundra Sundra', 'pprasad@centersys.com', 'pravin prasad', $mailsubject, $html_body, $text_body, $exportlocation.$str_pdfname, $str_pdfname);
*/

?>