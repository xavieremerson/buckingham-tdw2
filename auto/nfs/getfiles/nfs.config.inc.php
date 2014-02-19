<?

//network location of the data storage
$net_data_location = "\\\\buckfilesrv\\nfs\\"; //"\\\\buckfilesrv\\e$\\NFS\\";

//Location of this program
$scriptlocation = "D:\\tdw\\tdw\\auto\\nfs\\getfiles\\";   /* Trailing slash must exist */;

// NFS Files Location (file system access)
$filelocation_nfs = "\\\\192.168.20.54\\nfs\\"; //"U:\\";

//NFS Files Storage on Buckingham Server (?.?.?.?) mapped as ? Drive
$filelocation_tdw = "D:\\nfs_data\\"; /* Trailing slash must exist */;

$filelocation_alternate_tdw = "\\\\buckfilesrv\\nfs\\"; //"K:\\NFS\\";


$email_recipients = "brg-it@buckresearch.com,pprasad@centersys.com";
//$email_recipients = "pprasad@centersys.com";


$techsupport = "\n\n\n";
$techsupport.= " -------------------------------------------------------------------- \n";
$techsupport.= "|    UTILITY: Archive NFS Data Files to Intranet Storage             |\n";
$techsupport.= "|                                                                    |\n";
$techsupport.= "|    Technical Support:                                              |\n";
$techsupport.= "|    ------------------                                              |\n";
$techsupport.= "|    PRAVIN PRASAD                                                   |\n";
$techsupport.= "|    CenterSys Group, Inc.                                           |\n";
$techsupport.= "|    339 Fifth Avenue, Suite 405                                     |\n";
$techsupport.= "|    New York, NY 10016                                              |\n";
$techsupport.= "|    Office: 1-212-481-8717                                          |\n";
$techsupport.= "|    Mobile: 1-917-704-1885                                          |\n";
$techsupport.= "|    Fax:    1-212-683-8143                                          |\n";
$techsupport.= "|    Email: pprasad@centersys.com                                    |\n";
$techsupport.= " -------------------------------------------------------------------- \n";

?>