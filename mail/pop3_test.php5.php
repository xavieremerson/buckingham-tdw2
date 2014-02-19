<?php
// vi: expandtab sw=4 ts=4 sts=4 nowrap nu:
/**
 *
 * @author: j0inty.sL
 * @email: bestmischmaker@web.de
 */
error_reporting(E_ALL);
$strRootPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
require_once( $strRootPath ."pop3.class.php5.inc");

// Socket Options
$bUseSockets = TRUE;
$bIPv6 = FALSE;
$arrConnectionTimeout = array( "sec" => 10,
                               "usec" => 500 );
// POP3 Options
$strHost = "192.168.20.38";
$intPort = 110;
$strUser = "pprasad";
$strPass = "Buck123";
$bAPOP = FALSE;
$bAPopAutoDetect = TRUE;
$bHideUsernameAtLog = FALSE;

// Logging Options
$strLogFile = $strRootPath. "pop3.log";

try
{
    // Instance the POP3 object
    $objPOP3 = new POP3( $bUseSockets, $strLogFile, $bAPopAutoDetect, $bHideUsernameAtLog );
    
    // Connect to the POP3 server
    $objPOP3->connect($strHost,$intPort,$arrConnectionTimeout,$bIPv6);
    
    // Logging in
    $objPOP3->login($strUser, $strPass, $bAPOP);
    
    // Stat command
    //$strStat = $objPOP3->stat();
    
    // Recv a email in raw format
    //$strEMail = $objPOP3->recv(/*MsgNum*/);
    // Recv the mail and split every line in an single index of an numeric array
    // e.g
    // TODO

    //$arrEMail = $objPOP3->getMail(/*MsgNum*/);
	$arrOfficeStatus = $objPOP3->getOfficeStatus();
	//var_dump($arrOfficeStatus);

    for($i=$arrOfficeStatus["count"]; $i>= 1; $i-- )
    {
        $fp = fopen($strRootPath.$i.".mail.txt","a");
        fwrite($fp, $objPOP3->retr($i), $arrOfficeStatus[$i]["octets"]);
        fclose($fp);
        //$objPOP3->dele($i);
    }

    // Send the quit command
    $objPOP3->quit();

    // Disconnect from the server
    // !!! CAUTION !!!
    // - this function does not send the QUIT command to the server
    //   so all as delete marked message will NOT delete
    //   To delete the mails from the server you have to send the quit command themself before disconnecting from the server
    $objPOP3->disconnect();
}
catch( POP3_Exception $e )
{
    die($e);
}

// Your next code

?> 
