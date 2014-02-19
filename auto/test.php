<?php

  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

function error_alert_email($subject, $message) {

	//create mail to send
	$html_body = "";
	$html_body .= zSysMailHeader("");
	$html_body .= $message;
	$html_body .= zSysMailFooter();
	
	$text_body = $subject;
	
	zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;
	//zSysMailer('jperno@buckresearch.com', "Jessica Perno", $subject, $html_body, $text_body, "") ;
	//zSysMailer('rdaniels@buckresearch.com', "Robert Daniels", $subject, $html_body, $text_body, "") ;
}



$qry = "select * from users where Am = 'PM'";

$result_insert_trade = mysql_query($qry) or error_alert_email("CRITICAL FAILURE: TDW", $qry); //tdw_mysql_error($qry_insert_trade)

echo "not dying";

















exit;
// basic sequence with LDAP is connect, bind, search, interpret search
// result, close connection

echo "<h3>LDAP query test</h3>";
echo "Connecting ...";
$ds=ldap_connect("192.168.20.45");  // must be a valid LDAP server!
echo "connect result is " . $ds . "<br />";

if ($ds) { 
   echo "Binding ..."; 
   $r=ldap_bind($ds);    // this is an "anonymous" bind, typically
                           // read-only access
   echo "Bind result is " . $r . "<br />";

   echo "Searching for (sn=S*) ...";
   // Search surname entry
   $sr=ldap_search($ds, "o=My Company, c=US", "sn=S*");  
   echo "Search result is " . $sr . "<br />";

   echo "Number of entires returned is " . ldap_count_entries($ds, $sr) . "<br />";

   echo "Getting entries ...<p>";
   $info = ldap_get_entries($ds, $sr);
   echo "Data for " . $info["count"] . " items returned:<p>";

   for ($i=0; $i<$info["count"]; $i++) {
       echo "dn is: " . $info[$i]["dn"] . "<br />";
       echo "first cn entry is: " . $info[$i]["cn"][0] . "<br />";
       echo "first email entry is: " . $info[$i]["mail"][0] . "<br /><hr />";
   }

   echo "Closing connection";
   ldap_close($ds);

} else {
   echo "<h4>Unable to connect to LDAP server</h4>";
}
?> 