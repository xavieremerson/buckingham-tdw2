<?

//$dbuser = "buckingham";
//$dbpassword = "buckingham";
//$dbtnsname = "bucknotes9";

//$dsn="BUCKJOVUS";
//$username="BUCKINGHAM_login";
//$password="BUCKINGHAM_pw";
	
//Location of this script
$scriptlocation = "D:\\tdw\\auto\\bloomberg_anr\\buckjovus\\";   /* Trailing slash must exist */;

// PDF Files on Jovus Server
//$pdflocation_jovus = "J:\\TomcatApps\\buckingham\\WEB-INF\\upload\\publish\\";
$pdflocation_jovus = "V:\\FTP\\Jovus Files\\";

//PDF Files on BuckNotes Server (192.168.20.65) mapped as V Drive
//$pdflocation_bucknotes = "E:\\temp_docs\\";
$pdflocation_bucknotes = "E:\\research_docs\\research_docs\\";

//PDF Files on BuckNotes Server (192.168.20.65) mapped as V Drive
//$pdflocation_bucknotes_tdw = "E:\\temp_docs\\";
$pdflocation_bucknotes_tdw = "D:\\tdw\\tdw\\auto\\bloomberg_anr\\buckpdf\\";

//Confirm that the following Oracle DSN (bucknotes9) exists
//testbuck has been used for testing (testbuck.buckresearch.com is a separate instance of Oracle for
//test purposes only.

//$connect = odbc_connect("bucknotes9", "buckingham", "buckingham");

mysql_connect("localhost", "newadmin", "newpassword") or die(mysql_error());  
mysql_select_db("warehouse") or die(mysql_error());


# SQL Server Connection Information
//$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
//$msdb=mssql_select_db("BUCKINGHAM",$msconnect);

$msconnect=mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");
$msdb=mssql_select_db("BuckinghamTwo",$msconnect);

$email_recipients = "pprasad@centersys.com";


$techsupport = "\n\n\n";
$techsupport.= " -------------------------------------------------------------------- \n";
$techsupport.= "|    CENTERSYS GROUP, INC.  (http://www.centersys.com)               |\n";
$techsupport.= "|                                                                    |\n";
$techsupport.= "|    UTILITY: Populate BuckNotes w/ Research Documents from Jovus    |\n";
$techsupport.= "|                                                                    |\n";
$techsupport.= "|    Technical Support:                                              |\n";
$techsupport.= "|    ------------------                                              |\n";
$techsupport.= "|    PRAVIN PRASAD                                                   |\n";
$techsupport.= "|    CenterSys Group, Inc.                                           |\n";
$techsupport.= "|    339 Fifth Avenue, Suite 405                                     |\n";
$techsupport.= "|    New York, NY 10016                                              |\n";
$techsupport.= "|    Office: 1-212-481-8717                                          |\n";
$techsupport.= "|    Mobile: 1-917-704-1885                                          |\n";
$techsupport.= "|    Fax: 1-212-683-8143                                             |\n";
$techsupport.= "|    PH: 917-704-1885                                                |\n";
$techsupport.= "|    Email: pprasad@centersys.com                                    |\n";
$techsupport.= " -------------------------------------------------------------------- \n";

?>