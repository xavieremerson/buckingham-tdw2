<?
$str_html_file = "";
$str_html_file .= '
<style type="text/css">
<!--
.tdwthbrdr {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 11px;
  color: #000000;
  border-collapse: collapse;
	border: 1px #777777 solid
}
.fblue {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 11px;
  color: #0000ff;
}
-->
</style>';
?>

<?php
//get TDW functions
require_once("../includes/functions.php");
require_once("../includes/dbconnect.php");
require_once("../includes/global.php");

function strip_angles ($str) { //from email addresses  "Buck InvUpdate" <BuckInvUpdate@BuckResearch.com>
	$final_str = str_replace('"','',$str);
	$final_str = substr($final_str,0,stripos($final_str,"<"));
	return $final_str;
}

function show_nice_date ($str) {
	$show_str = format_date_ymd_to_mdy(substr($str,0,10));
	$date1 = mktime( substr($str,11,2)-4, substr($str,14,2), 0, 0, 0, 0 );  // changed -5 to -4 for daylight saving.
	$show_str .= " ".date("h:ia", $date1);
	return $show_str;
}

function to_db($str) {
$arr_vals = explode("^",$str);

$clean_str_subject = str_replace("'","\\'",$arr_vals[4]);

$clean_str_subject = preg_replace('/[^a-z0-9 ]/is', '', $clean_str_subject);

$qry = "INSERT INTO eml_research_emails 
				( auto_id , eml_type, eml_trade_date , eml_subject , eml_from , eml_to , eml_format_time , eml_isactive ) 
				VALUES (
				NULL ,
				'".$arr_vals[0]."', 
				'".previous_business_day()."', 
				'".substr($clean_str_subject,0,200)."', 
				'".str_replace("'","\\'",$arr_vals[2])."', 
				'".str_replace("'","\\'",$arr_vals[3])."', 
				'".$arr_vals[1]."', 
				'1'
				)";
  //echo $qry;
	$result = mysql_query($qry) or die (tdw_mysql_error($qry));
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$trade_date_to_process = previous_business_day();
//$trade_date_to_process ='2008-02-01';
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


$str_html_file .= "<font color='blue'>Trade Date: ".format_date_ymd_to_mdy($trade_date_to_process)."</font><br><br>";

// Modify the paths to these class files as needed. 
require_once("class_http.php"); 
require_once("class_xml.php"); 

// Change these values for your Exchange Server. 
$exchange_server = "http://192.168.20.38";  //evs1  //192.168.20.74  //http://evs1.buckresearch.com //192.168.20.38
$exchange_username = "tdw"; 
$exchange_password = "Trade!23"; 

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//Processing Research Dissemination

$value_exit = 0;
$locval = "/public/Research%20Dissemination";
while ( $value_exit == 0 ) {

	// We use Troy's http class object to send the XML-formatted WebDAV request to the Exchange Server and to receive the response from the Exchange Server. 
	// The response is also XML-formatted. 
	$h = new http(); 
	
	$h->headers["Content-Type"] = 'text/xml; charset="UTF-8"'; 
	$h->headers["Depth"] = "0"; 
	$h->headers["Range"] = "rows=0-100"; 
	$h->headers["Translate"] = "f"; 
	$h->xmlrequest = '<?xml version="1.0"?>'; 
	$h->xmlrequest .= '
	<a:searchrequest xmlns:a=\'DAV:\'>
	 <a:sql>
		SELECT "DAV:href",
					 "urn:schemas:httpmail:subject",
					 "urn:schemas:httpmail:date",
					 "urn:schemas:httpmail:datereceived",
					 "urn:schemas:httpmail:from",
					 "urn:schemas:httpmail:sender",
					 "urn:schemas:httpmail:to",
					 "urn:schemas:httpmail:priority"
			FROM scope(\'shallow traversal of "'.$exchange_server.$locval.'"\')
		WHERE "DAV:ishidden"=False AND "DAV:isfolder"=False
	 </a:sql>
	</a:searchrequest>';
				
	if (!$h->fetch($exchange_server.$locval, 0, null, $exchange_username, $exchange_password, "SEARCH")) { //  "administrator","adminbrg$", 
		echo "<h2>There is a problem with the http request!</h2>"; 
		echo $h->log; 
		//exit(); 
	} 
	
	$x = new xml(); 
	if (!$x->fetch($h->body)) { 
			echo "<h2>There was a problem parsing your XML!</h2>"; 
			echo "<pre>".$h->log."</pre><hr />\n"; 
			echo "<pre>".$h->header."</pre><hr />\n"; 
			echo "<pre>".$h->body."</pre><hr />\n"; 
			echo "<pre>".$x->log."</pre><hr />\n"; 
			//exit(); 
	} 
	
	if (strlen($h->body) > 0) {
		//=============================================================================
		$str_html_file .= "<font color='blue'>Research Dissemination</font>";
		
		$str_html_file .= '<table width="900" border="1" cellspacing="0" cellpadding="0" class="tdwthbrdr">'; 
		$count_rd = 0;
		$count_rd_all = 0;
		foreach($x->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$item) { 
				
				if (substr($item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text,0,10) == $trade_date_to_process) { //$item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text
				
				if (trim(strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text))=="Research Dissemination") {
					$count_rd = $count_rd + 1;
					$count_rd_all = $count_rd_all + 1;
					//echo "<br>RD ==>".trim(strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text));
				} else {
					$count_rd_all = $count_rd_all + 1;
					//echo "<br>Not RD ==>".trim(strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text));
				}
				
				$arr_rd[] = "Research Dissemination"."^".
										show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
										$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text;
				to_db("Research Dissemination"."^".
										show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
										$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text);
				$str_html_file .= '
								<tr>
									<td width="140">'.show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text).'</td>
									<td>TO: Research Dissemination FROM: '.strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text).'</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a class="fblue">'.preg_replace('/[^a-z0-9 ]/is', '', $item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text).'</a></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>';
				}
		} 
		
		echo "# of Research Dissemination items = ".$count_rd."<br>";
		echo "# of All in Research Dissemination items = ".$count_rd_all."<br>";
		echo "# of Difference = ". ($count_rd_all-$count_rd) . "<br>";
		
		$str_html_file .= "</table>\n"; 
		$str_html_file .= "<br><br>";
		echo "File Research Dissemination created";
		//=============================================================================
	
		$value_exit = 1;				
	}

	ob_flush();
	flush();
	//$pauseval = 5;
	//echo "Pausing for ".$pauseval." seconds.<br>";
	//sleep($pauseval);
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// Processing BuckInvUpdate

$value_exit = 0;
$locval = "/exchange/BuckInvUpdate/Inbox";

while ( $value_exit == 0 ) {

	// We use Troy's http class object to send the XML-formatted WebDAV request 
	// to the Exchange Server and to receive the response from the Exchange Server. 
	// The response is also XML-formatted. 
	$h = new http(); 
	$h->headers["Content-Type"] = 'text/xml; charset="UTF-8"'; 
	$h->headers["Depth"] = "0"; 
	$h->headers["Range"] = "rows=0-100"; 
	$h->headers["Translate"] = "f"; 
	$h->xmlrequest = '<?xml version="1.0"?>'; 
	$h->xmlrequest .= '
	<a:searchrequest xmlns:a=\'DAV:\'>
	 <a:sql>
		SELECT "DAV:href",
					 "urn:schemas:httpmail:subject",
					 "urn:schemas:httpmail:date",
					 "urn:schemas:httpmail:datereceived",
					 "urn:schemas:httpmail:from",
					 "urn:schemas:httpmail:sender",
					 "urn:schemas:httpmail:to",
					 "urn:schemas:httpmail:priority"
				FROM scope(\'shallow traversal of "'.$exchange_server.$locval.'"\')
		WHERE "DAV:ishidden"=False AND "DAV:isfolder"=False
	 </a:sql>
	</a:searchrequest>';
	
	if (!$h->fetch($exchange_server.$locval, 0, null, $exchange_username, $exchange_password, "SEARCH")) {  
		echo "<h2>There is a problem with the http request!</h2>"; 
		echo $h->log; 
		//exit(); 
	} 
	
	$x = new xml(); 
	if (!$x->fetch($h->body)) { 
			echo "<h2>There was a problem parsing your XML!</h2>"; 
			echo "<pre>".$h->log."</pre><hr />\n"; 
			echo "<pre>".$h->header."</pre><hr />\n"; 
			echo "<pre>".$h->body."</pre><hr />\n"; 
			echo "<pre>".$x->log."</pre><hr />\n"; 
			//exit(); 
	} 

	if (strlen($h->body) > 0) {
		//=============================================================================
		$str_html_file .= "<font color='blue'>Investment Update</font>";
		
		$str_html_file .= '<table width="900" border="1" cellspacing="0" cellpadding="0" class="tdwthbrdr">'; 
		
		$count_iu = 0;
		foreach($x->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$item) { 
				
				if (substr($item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text,0,10) == $trade_date_to_process) { //$item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text
			
					$count_iu = $count_iu + 1;
		
		
				$arr_iu[] = "Buck InvUpdate"."^".
										show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
										$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text;
				to_db("Buck InvUpdate"."^".
										show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
										$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text);
				$str_html_file .= '
								<tr>
									<td width="140">'.show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text).'</td>
									<td>TO: Buck InvUpdate FROM: '.strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text).'</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a class="fblue">'.preg_replace('/[^a-z0-9 ]/is', '', $item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text).'</a></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>';
				}
		}

		echo "# of Buck InvUpdate items = ".$count_iu."<br>"; 
		$str_html_file .= "</table>\n"; 
		$str_html_file .= "<br><br>";
		echo "File InvUpdate created";
		//=============================================================================
	
		$value_exit = 1;				
	}


	ob_flush();
	flush();
	//$pauseval = 5;
	//echo "Pausing for ".$pauseval." seconds.<br>";
	//sleep($pauseval);

}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// Processing BuckQuicknote

$value_exit = 0;
$locval = "/exchange/BuckQuicknote/Inbox";

while ( $value_exit == 0 ) {

	// We use Troy's http class object to send the XML-formatted WebDAV request 
	// to the Exchange Server and to receive the response from the Exchange Server. 
	// The response is also XML-formatted. 
	$h = new http(); 
	$h->headers["Content-Type"] = 'text/xml; charset="UTF-8"'; 
	$h->headers["Depth"] = "0"; 
	$h->headers["Range"] = "rows=0-100"; 
	$h->headers["Translate"] = "f"; 
	$h->xmlrequest = '<?xml version="1.0"?>'; 
	$h->xmlrequest .= '
	<a:searchrequest xmlns:a=\'DAV:\'>
	 <a:sql>
		SELECT "DAV:href",
					 "urn:schemas:httpmail:subject",
					 "urn:schemas:httpmail:date",
					 "urn:schemas:httpmail:datereceived",
					 "urn:schemas:httpmail:from",
					 "urn:schemas:httpmail:sender",
					 "urn:schemas:httpmail:to",
					 "urn:schemas:httpmail:priority"
				FROM scope(\'shallow traversal of "'.$exchange_server.$locval.'"\')
		WHERE "DAV:ishidden"=False AND "DAV:isfolder"=False
	 </a:sql>
	</a:searchrequest>';
	
	if (!$h->fetch($exchange_server.$locval, 0, null, $exchange_username, $exchange_password, "SEARCH")) {  
		echo "<h2>There is a problem with the http request!</h2>"; 
		echo $h->log; 
		//exit(); 
	} 
	
	$x = new xml(); 
	if (!$x->fetch($h->body)) { 
			echo "<h2>There was a problem parsing your XML!</h2>"; 
			echo "<pre>".$h->log."</pre><hr />\n"; 
			echo "<pre>".$h->header."</pre><hr />\n"; 
			echo "<pre>".$h->body."</pre><hr />\n"; 
			echo "<pre>".$x->log."</pre><hr />\n"; 
			//exit(); 
	} 

	if (strlen($h->body) > 0) {
		//=============================================================================
		$str_html_file .= "<font color='blue'>QuickNote</font>";
		
		$str_html_file .= '<table width="900" border="1" cellspacing="0" cellpadding="0" class="tdwthbrdr">'; 
		
		$count_qn = 0;
		foreach($x->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$item) { 
				
				if (substr($item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text,0,10) == $trade_date_to_process) { //$item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text
			
					$count_qn = $count_qn + 1;
		
		
				$arr_qn[] = "QuickNote"."^".
										show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
										$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text;
				to_db("QuickNote"."^".
										show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
										strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
										$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text);
				$str_html_file .= '
								<tr>
									<td width="140">'.show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text).'</td>
									<td>TO: QuickNote FROM: '.strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text).'</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><a class="fblue">'.preg_replace('/[^a-z0-9 ]/is', '', $item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text).'</a></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>';
				}
		} 

		echo "# of Buck QuickNote items = ".$count_qn."<br>"; 
		$str_html_file .= "</table>\n"; 
		echo "File QuickNote created";
		//=============================================================================
	
		$value_exit = 1;				
	}


	ob_flush();
	flush();
	//$pauseval = 5;
	//echo "Pausing for ".$pauseval." seconds.<br>";
	//sleep($pauseval);

}
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

//clean up unwanted characters
for ($i=0;$i<10;$i++) {
$str_html_file = str_replace(" <","<",$str_html_file);
$str_html_file = str_replace("\t","",$str_html_file);
$str_html_file = str_replace("\n","",$str_html_file);
$str_html_file = str_replace("\n\r","",$str_html_file);
$str_html_file = str_replace("\r","",$str_html_file);
}

//=====================================================================================================
//first clean the counts table and then insert the relevant data

$qry_del = "delete from eml_research_counts where eml_trade_date = '".$trade_date_to_process."'";
$result_del = mysql_query($qry_del) or die(tdw_mysql_error($qry_del));
$qry_insert_count = "INSERT INTO eml_research_counts 
									(auto_id, eml_trade_date,eml_type,eml_count,eml_isactive) 
									VALUES (
									NULL , 
									'".$trade_date_to_process."', 
									'Research Dissemination', 
									'".$count_rd."', 
									'1'
									)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
$qry_insert_count = "INSERT INTO eml_research_counts 
									(auto_id, eml_trade_date,eml_type,eml_count,eml_isactive) 
									VALUES (
									NULL , 
									'".$trade_date_to_process."', 
									'Buck InvUpdate', 
									'".$count_iu."', 
									'1'
									)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
$qry_insert_count = "INSERT INTO eml_research_counts 
									(auto_id, eml_trade_date,eml_type,eml_count,eml_isactive) 
									VALUES (
									NULL , 
									'".$trade_date_to_process."', 
									'Buck QuickNote', 
									'".$count_qn."', 
									'1'
									)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
$qry_insert_count =  "INSERT INTO eml_research_counts 
											(auto_id, eml_trade_date, eml_type, eml_count, eml_isactive) 
											VALUES (
											NULL , 
											'".$trade_date_to_process."', 
											'Total', 
											'".$count_rd_all."', 
											'1'
											)";
$result_insert_count = mysql_query($qry_insert_count) or die(tdw_mysql_error($qry_insert_count));
//=====================================================================================================
//set all data in the table to inactive for this trade date and then insert this record.

			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//check the data for the correctness
			if (($count_iu+$count_qn+$count_rd)==$count_rd_all) {
				echo "Totals ARE correct and there are no exceptions";
				$is_ok = 1;
			} else {
				echo "Totals are NOT incorrect and there are exceptions";
				$is_ok = 0;
			}
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$result_update_isactive = mysql_query("update eml_research_compliance set eml_isactive = 0 where eml_trade_date = '".$trade_date_to_process."'");
$clean_str_file = str_replace("'","\\'",$str_html_file);
$clean_str_file = str_replace("’","\\'",$clean_str_file);

//$clean_str_file = preg_replace('/[^a-z0-9 ]/is', '', $clean_str_file);

$qry_insert_htmlfile = "INSERT INTO eml_research_compliance
													(auto_id, eml_is_ok, eml_trade_date, eml_html_file, eml_isactive) 
												VALUES (
												NULL ,
												'".$is_ok."', 
												'".$trade_date_to_process."', 
												'".$clean_str_file."', 
												'1'
												)";
$result_insert_htmlfile = mysql_query($qry_insert_htmlfile) or die(tdw_mysql_error($qry_insert_htmlfile));

//=====================================================================================================
//show the data FOR NOW THAT IS
$qry_data = "select * from eml_research_compliance where eml_trade_date = '".$trade_date_to_process."' and eml_isactive = 1";
$result_data = mysql_query($qry_data) or die(tdw_mysql_error($qry_data));
while($row_data = mysql_fetch_array($result_data)) {
	//echo $row_data['eml_html_file'];
}

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


exit;
//$locval = "/exchange/BuckInvUpdate/Inbox";
?>