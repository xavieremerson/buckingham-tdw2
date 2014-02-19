<?php 
//get TDW functions
require_once("../includes/functions.php");
require_once("../includes/dbconnect.php");
require_once("../includes/global.php");

function show_nice_date ($str) {

	$show_str = format_date_ymd_to_mdy(substr($str,0,10));
	
	$date1 = mktime( substr($str,11,2)-5, substr($str,14,2), 0, 0, 0, 0 );
	//echo date("h:ia", $date1);  	
	$show_str .= " ".date("h:ia", $date1);

	return $show_str;
}

$trade_date_to_process = previous_business_day();
//$trade_date_to_process ='2007-12-05';


// Modify the paths to these class files as needed. 
require_once("class_http.php"); 
require_once("class_xml.php"); 

// Change these values for your Exchange Server. 
$exchange_server = "http://192.168.20.74";  //evs1
$exchange_username = "tdw";  //@buckresearch.com
$exchange_password = "Trade!23";   // tdw/Buck123  IP=74

// We use Troy's http class object to send the XML-formatted WebDAV request 
// to the Exchange Server and to receive the response from the Exchange Server. 
// The response is also XML-formatted. 
$h = new http(); 
$h->headers["Content-Type"] = 'text/xml; charset="UTF-8"'; 
$h->headers["Depth"] = "0"; 
//$h->headers["Range"] = "rows=0-100"; 
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
		FROM scope(\'shallow traversal of "'.$exchange_server.'/exchange/BuckInvUpdate/Inbox"\')
	WHERE "DAV:ishidden"=False AND "DAV:isfolder"=False
 </a:sql>
</a:searchrequest>';

if (!$h->fetch($exchange_server."/exchange/BuckInvUpdate/Inbox", 0, null, $exchange_username, $exchange_password, "SEARCH")) { 
  echo "<h2>There is a problem with the http request!</h2>"; 
  echo $h->log; 
  exit(); 
} 

$x = new xml(); 
if (!$x->fetch($h->body)) { 
    echo "<h2>There was a problem parsing your XML!</h2>"; 
    echo "<pre>".$h->log."</pre><hr />\n"; 
    echo "<pre>".$h->header."</pre><hr />\n"; 
    echo "<pre>".$h->body."</pre><hr />\n"; 
    echo "<pre>".$x->log."</pre><hr />\n"; 
    exit(); 
} 

echo '<table border="1">'; 
foreach($x->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$item) { 
    
		if (substr($item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text,0,10) == $trade_date_to_process) { //$item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text
		
			echo '<tr>' 
					.'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text.'</td>' 
					//.'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text.'</td>' 
					.'<td>'.show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATE[0]->_text).'</td>' 
					//.'<td>'.show_nice_date($item->A_PROPSTAT[0]->A_PROP[0]->D_DATERECEIVED[0]->_text).'</td>' 
					.'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->D_FROM[0]->_text.'</td>' 
					.'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->D_TO[0]->_text.'</td>' 
					//.'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->D_PRIORITY[0]->_text.'</td>' 
					//.'<td><a href="'.$item->A_HREF[0]->_text.'">Click to open via OWA</a></td>' 
					//.'<td><a href="Outlook:Inbox/'.$item->A_PROPSTAT[0]->A_PROP[0]->A_DISPLAYNAME[0]->_text.'">Click to open via Outlook</a></td>' 
					."</tr>\n"; 
				
		}
} 
echo "<table>\n"; 

?> 