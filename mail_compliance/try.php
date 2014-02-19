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
/*
		$arr_rd[] = "Research Dissemination"."^".
		            show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
								strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
								strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
								$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text;
*/
$arr_vals = explode("^",$str);

$clean_str_subject = str_replace("'","\\'",$arr_vals[4]);

//echo strlen($clean_str_subject);
//FW: Buckingham QUICK NOTES for 1/22/08: GIL, ANF // ETN, DD, Chemicals // KV’A
//echo "[".substr($clean_str_subject,76,1)."]<br>";

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

// Modify the paths to these class files as needed. 
require_once("class_http.php"); 
require_once("class_xml.php"); 

// Change these values for your Exchange Server. 
$exchange_server = "http://192.168.20.38";  //evs1  //192.168.20.74  //http://evs1.buckresearch.com //192.168.20.38
$exchange_username = "tdw"; 
$exchange_password = "Trade!23"; 

$locval = "/public/Research%20Dissemination";
$locval = "/exchange/BuckInvUpdate/Inbox";
$locval = "/exchange/BuckQuicknote/Inbox";




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

//echo $h->xmlrequest;
//exit(); 

// IMPORTANT -- The END line above must be completely left-aligned. No white-space.

// The 'fetch' method does the work of sending and receiving the request. 
// NOTICE the last parameter passed--'SEARCH' in this example. That is the 
// HTTP verb that you must correctly set according to the type of WebDAV request 
// you are making.  The examples on this page use either 'PROPFIND' or 'SEARCH'. 
if (!$h->fetch($exchange_server.$locval, 0, null, $exchange_username, $exchange_password, "SEARCH")) { //  "administrator","adminbrg$", 
  echo "<h2>There is a problem with the http request!</h2>"; 
  echo $h->log; 
  exit(); 
} 

// Note: The following lines can be uncommented to aid in debugging. 
//echo "<pre>".$h->log."</pre><hr />\n"; 
//echo "<pre>".$h->header."</pre><hr />\n"; 
//echo "<pre>".$h->body."</pre><hr />\n"; 
//exit(); 
// Or, these next lines will display the result as an XML doc in the browser. 
//header('Content-type: text/xml'); 
//echo $h->body; 
//exit(); 


//echo "<font color='red'>".$h->body."</font>"; 


// The assumption now is that we've got an XML result back from the Exchange 
// Server, so let's parse the XML into an object we can more easily access. 
// For this task, we'll use Troy's xml class object. 
$x = new xml(); 
if (!$x->fetch($h->body)) { 
    echo "<h2>There was a problem parsing your XML!</h2>"; 
    echo "<pre>".$h->log."</pre><hr />\n"; 
    echo "<pre>".$h->header."</pre><hr />\n"; 
    echo "<pre>".$h->body."</pre><hr />\n"; 
    echo "<pre>".$x->log."</pre><hr />\n"; 
    exit(); 
} 

if (strlen($h->body) < 500) {

echo ">>>>>>>>>>>>>>>>>> LENGTH = ". strlen($h->body)."<br>";

} else {

echo ">>>>>>>>>>>>>>>>>> LENGTH = ". strlen($h->body)."<br>";


}



// You should now have an object that is an array of objects and arrays that 
// makes it easy to access the parts you need. These next lines can be 
// uncommented to make a raw display of the data object. 
//echo "<pre>\n"; 
//print_r($x->data); 
//echo "</pre>\n"; 
//exit(); 



$arr_rd = array();
$count_rd = 0;
$count_rd_all = 0;


echo '<table border="1">'; 
foreach($x->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$item) { 
    echo '<tr>' 
        .'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text.'</td>' 
        .'<td><a href="'.$item->A_HREF[0]->_text.'">Click to open via OWA</a></td>' 
        .'<td><a href="Outlook:Inbox/~'.$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text.'">Click to open via Outlook</a></td>' 
        ."</tr>\n"; 
} 
echo "<table>\n"; 

/*    
		if (substr($item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text,0,10) == $trade_date_to_process) { //$item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text
		
		if (trim(strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text))=="Research Dissemination") {
		  $count_rd = $count_rd + 1;
			$count_rd_all = $count_rd_all + 1;
		} else {
		  $count_rd_all = $count_rd_all + 1;
		}
		
		$arr_rd[] = "Research Dissemination"."^".
		            show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
								strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
								strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
								$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text;
		
		echo "Research Dissemination"."^".
		            show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text)."^".
								strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text)."^".
								strip_angles($item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text)."^".
								$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text;
		
		echo "<br>";					
		
		}
} 
*/		

//echo "# of Research Dissemination items = ".$count_rd."<br>";
//echo "# of All in Research Dissemination items = ".$count_rd_all."<br>";
//echo "# of Difference = ". ($count_rd_all-$count_rd) . "<br>";


exit;

?>