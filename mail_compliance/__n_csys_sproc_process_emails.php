<?
error_reporting(1);
ini_set('date.timezone', 'America/New_York');
include("_csys_receivemail.class.php");

//$obj= new receiveMail('tdw@buckresearch.com','BRmail678','tdw@buckresearch.com','owa.smarshexchange.com','imap','143',false); 
$obj= new receiveMail('tdw-monitor@buckresearch.com','16286$brMail','tdw-monitor@buckresearch.com','owa.smarshexchange.com','imap','143',false); 

//Connect to the Mail Box
$obj->connect();         //If connection fails give error message and exit

// Get Total Number of Unread Email in mail box
$tot=$obj->getTotalMails(); //Total Mails in Inbox Return integer value

echo "**************************\nTotal Mails:: $tot\n**************************\n";
      
			
//exit;
for($i=$tot;$i>0;$i--)
{
	$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName)
	//print_r($head);

/*
Array
(
    [from] => microsoftexchange329e71ec88ae4615bbc36ab6ce41109e@smarshexchange.com
    [fromName] => Microsoft Exchange
    [toOth] => @
    [toNameOth] =>
    [subject] => Undeliverable: Tradeware File TWreports041312.xfr exists and successfully
 processed. (04-13-2012)
    [to] => "tdw (centersys)" <tdw@buckresearch.com>
)
*/


	echo "From :: ".$head['from']."\n";
	echo "FromName :: ".$head['fromName']."\n"; 
	echo "TO :: ".$head['to']."\n";
	echo "Subjects :: ".$head['subject']."\n";
	echo "Date :: ".$head['msg_date']."\n";
	
	
	//echo "To Other :: ".$head['toOth']."\n";
	//echo "ToName Other :: ".$head['toNameOth']."\n";
	echo "\n";
	//echo $obj->getBody($i);  // Get Body Of Mail number Return String Get Mail id in interger
		
	echo "\n--------------------------------------------------------------------------------\n";
	
	//$obj->deleteMails($i); // Delete Mail from Mail box
	//$obj->moveMails($i); // Move Mail from Mail box so it is not fetched again.
}
$obj->close_mailbox();   //Close Mail Box
?>