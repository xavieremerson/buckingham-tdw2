<?
//************************************************************************************************
//************************************************************************************************
/*
THIS FILE IS TO BE RUN AS A JOB AT 6:00am IN THE MORNING SO USING THE PREVIOUS BUSINESS DAY FUNCTION
IT WILL ONLY PROCESS FILES THAT ARE CREATED IN THE PREVIOUS BUSINESS DAY. WILL CREATE SEPARATE EMAILS
FOR EACH RESEARCH DOCUMENT.

THIS FILE MUST BE RUN AS SHELL CMD IN BAT
*/
//************************************************************************************************
//************************************************************************************************
include('fax.config.inc.php');
include('fax.functions.php');

include('../../includes/functions.php');

$serial_number = '8311906';
$str_query = 'SELECT FPFAXINFO.FAXPRESSSERIALNUMBER as id, 
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
													WHERE (((FPFAXINFO.FAXPRESSSERIALNUMBER)='.$serial_number.') AND ((FPJOBTYPE.JOBTYPE)="Outgoing Fax"));';
													
/*
													
//---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
echo "\n\n\n\n".$str_query."\n\n\n\n";
//$str_query = 'SELECT * from test';
$conn=odbc_connect('faxmgmt','','');
if (!$conn)
  {exit("Connection Failed: " . $conn);}
$sql=$str_query;
$rs=odbc_exec($conn,$sql);
if (!$rs)
  {exit("Error in SQL");}
echo "<table><tr>";
echo "<th>Companyname</th>";
echo "<th>Contactname</th></tr>";
while (odbc_fetch_row($rs))
{
  $compname=odbc_result($rs,"id");
  $conname=odbc_result($rs,"name");
  echo "<tr><td>$compname</td>";
  echo "<td>$conname</td></tr>";
}
odbc_close($conn);
echo "</table>";



//---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
//$mdb_location = "\\\\buckfax2\\CASTELLE\\ARCHIVE\\DATABASE\\archive.mdb";
$mdb_location = "\\\\buckfax2\\fpressdb\\archive.mdb";
$db_conn = new COM("ADODB.Connection"); 
$connstr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=". realpath($mdb_location).";"; 
$db_conn->open($connstr); 
$rS = $db_conn->execute($str_query); 
$f1 =  $rS->Fields(0); 
$f2 =  $rS->Fields(1); 
while (!$rS->EOF) 
{ 
    print $f1->value." ".$f2->value."<br />\n"; 
    $rS->MoveNext(); 
} 
$rS->Close(); 
$db_conn->Close(); 
//---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+

//$mdb_location = "\\\\buckfax2\\CASTELLE\\ARCHIVE\\DATABASE\\archive.mdb";

//---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+
*/

$str_query = 'SELECT * from FPFAXINFO';
//$mdb_location = "R:\\PravinPrasad\\test\\test.mdb";
$mdb_location = "\\\\buckfax2\\fpressdb\\archive.mdb";
$db_conn = new COM("ADODB.Connection"); 
$connstr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=". realpath($mdb_location).";"; 
$db_conn->open($connstr); 
$rS = $db_conn->execute($str_query); 
$f1 =  $rS->Fields(0); 
$f2 =  $rS->Fields(1); 
while (!$rS->EOF) 
{ 
    print $f1->value." ".$f2->value."<br />\n"; 
    $rS->MoveNext(); 
} 
$rS->Close(); 
$db_conn->Close(); 
//---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+---+











exit;


















$del_cmd = 'del ' . $pdflocation_bucknotes_tdw .'*.pdf';
echo $del_cmd."\n";
shell_exec($del_cmd);

echo 'Connecting to Jovus Server @ Buckingham' . "\n\n";

//Previous Business Day should be applied here.
//$date_to_process = '2008-01-09';
$date_to_process = previous_business_day();
echo $date_to_process . "\n\n";

$msquery = "exec prGetAllPublishedDocIds '".$date_to_process."'";
$msresults= mssql_query($msquery);

$v_count_docs = 0;
while ($row = mssql_fetch_array($msresults)) {

	/*
    [0] => Paperand_20080108
    [DocID] => Paperand_20080108
    [1] => Hone EPS Est.s and Price Tgts; Upgrade NP to Accumulate
    [Headline] => Hone EPS Est.s and Price Tgts; Upgrade NP to Accumulate
    [2] => Industry Report
    [ProductName] => Industry Report
    [3] => Jan 8 2008  9:15AM
    [StatusDateTime] => Jan 8 2008  9:15AM
    [4] => Published
    [Status] => Published	
	*/	
	//show_array($row);
	
	$docid = $row[0];
	$headline = $row[1];
	$doctype = $row[2];
	$publish_time = $row[3];
	$status = $row[4];
	
	//get filename for the pdf's
	$v_filename = $pdflocation_jovus . get_pdfname($docid);
  echo "Processing file : " . $v_filename."\n";

	if (file_exists($v_filename) ) {  //and 1==2
		
		 //Copying PDF File from Jovus to TDW location
		 $copy_cmd = 'copy "'.$pdflocation_jovus.get_pdfname($docid). '" "'.$pdflocation_bucknotes_tdw.'"';
     //echo "Copy Command = ". $copy_cmd."\n";
		 //xdebug('copy_cmd',$copy_cmd);
		 shell_exec($copy_cmd);
		 //echo get_pdfname($docid). " copied successfully...\n";	
		 
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //Create and send emails:
				$email_log_txt = 'ATTACHMENT FILENAME : '. get_pdfname($docid)."\n".
												 'HEADLINE : '.$headline."\n".
												 'PUBLISHED : '.$publish_time."\n";

				$email_log_html = 'ATTACHMENT FILENAME : '. get_pdfname($docid)."<br>".
													'HEADLINE : '.$headline."<br>".
													'PUBLISHED : '.$publish_time."<br>";

				//create mail to send
				
				$subject = "[Buckingham Research] ".get_pdfname($docid);
				$text_body = $email_log_txt;
				$html_body = $email_log_html;
				
				$arr_attachpdf = array(get_pdfname($docid)=>$pdflocation_bucknotes_tdw . get_pdfname($docid));
				
				zTextMailer('prasad_pravin@yahoo.com', "", $subject, $html_body, $text_body, $arr_attachpdf) ;
				//zTextMailer('brgtec@yahoo.com', "", $subject, $html_body, $text_body, $arr_attachpdf) ;
			  //zTextMailer('pprasad@centersys.com', "", $subject, $html_body, $text_body, $arr_attachpdf) ;
				zTextMailer('anrgroup@bloomberg.net', "", $subject, $html_body, $text_body, $arr_attachpdf) ;
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++		 
	}  else {
				// FILE DOES NOT EXIST
				echo "\n\nERROR!\n\n";
	}
	$v_count_docs = $v_count_docs + 1;
	echo "\nCOUNT = ".$v_count_docs."\n";
}

$del_cmd = 'del ' . $pdflocation_bucknotes_tdw .'*.pdf';
echo $del_cmd."\n";
shell_exec($del_cmd);

exit;
?>