<?php
/*
  Sample File not more !!!
  Author: Jointy <bestmischmaker@web.de>
  
  Please read Readme.txt !!!
*/

require("pop3.class.inc");

// Constructor
// optional
$apop_detect = TRUE;    // default = FALSE
$log = TRUE;            // default = FALSE
$log_file = "pop3.class.log"; // must be set when $log = TRUE !!!
$qmailer = FALSE;


// func $pop3->connect()
$server = "192.168.20.38";//pop.lycos.de
// optional !!
// $port = "110";
// $conn_timeout = "25";  // Connection Timeout
// $sock_timeout = "10,500"; // Socket Timeout

// func $pop3->login()
$username = "pprasad";
$password = "Buck123";
// optional
//$apop = "0";

// MySQL Vars for connect to DB Server
$db["addr"] = "localhost";
$db["user"] = "newadmin";
$db["pass"] = "newpassword";
$db["link"] = FALSE;
$db["use"] = "mail";
// optional
$db["dir_table"] = "inbox";    // Table for header data
$db["msg_table"] = "messages"; // Table for complete Messages (/w header)...


// Your own free Vars
// Save to MySQL ??
$savetomysql = TRUE;
$savetofile = FALSE;
$delete = FALSE;






$pop3 = new POP3($log,$log_file,$apop_detect);

if($pop3->connect($server)){
    if($pop3->login($username,$password)){
        if(!$msg_list = $pop3->get_office_status()){
            echo $pop3->error;
            return;
        }
    }else{
        echo $pop3->error;
        return;
    }
}else{
    echo $pop3->error;
    return;
}

$db["link"] = mysql_connect($db["addr"],$db["user"],$db["pass"]) or die(mysql_error());
mysql_select_db($db["use"],$db["link"]) or die(mysql_error());

$noob = TRUE;

for($i=1;$i<=$msg_list["count_mails"];$i++){
    if(!$header = $pop3->get_top($i)){
        echo $pop3->error;
    }
    // Get Message ID and set $unique_id for save2file()
        $g = 0;
        while(!ereg("</HEADER>",$header[$g])){
            if(eregi("Message-ID",$header[$g])){
                $unique_id = md5($header[$g]);
            }
            $g++;
        }
    unset($g);
    
    $query = 'SELECT `unique_id` FROM `'.$msg_table.'` WHERE 1 AND `unique_id` = \''.$unique_id.'\' LIMIT 0, 1';
    $result = mysql_query($query,$db["link"]) or die(mysql_error());
    
    if($rows = mysql_fetch_array($result)){
        $get_msg = FALSE;
        $savetofile = FALSE;
        $savetomysql = FALSE;

    }
    mysql_free_result($result);
    unset($rows);
    
    
    
    






    if($get_msg){
        if(!$message = $pop3->get_mail($i, $qmailer)){
            echo $pop3->error;
            $savetofile = FALSE;
            $savetomysql = FALSE;
            $delete = FALSE;
        }
    }


    
    // Save to File !!!
    if($savetofile){



        $filename = ".//mails//".$unique_id.".txt";

        if(!is_file($filename)){
        if(!$filesize = $pop3->save2file($message,$filename)){
            echo $pop3->error;
            return;
        }else{
            echo "File saved to ".$filename." (".$filesize." Bytes written) !! \r\n <br>";
        }
        }else{
            echo "File <b>(".$filename.")</b> already exists. !! \r\n <br>";
        }
    }
    
    // Save to MySQL
    if($savetomysql){

        if($count_bytes = $pop3->save2mysql($message,$db["link"],$db["dir_table"],$db["msg_table"])){
            echo "File save to MySQL complete. (".$count_bytes." Bytes written) !! \r\n <br>";
        }else{
            echo $pop3->error;
            return;
        }
    }
    
    // Send Noob command !!
    if($noop){
        if(!$pop3->noop()){
            echo $pop3->error;
            $noob = FALSE;
        }
    }
    
    // Delete MSG
    if($delete){
        if($pop3->delete_mail($i)){
            echo "Nachricht als gelöscht markiert !!! \r\n <br>";
        }else{
            echo $pop3->error;
        }
    }


    
    
    
}
if($msg_list["count_mails"] == "0"){
    echo "Keine neuen Nachrichten !!";
}

mysql_close($db["link"]);
$pop3->close();
























?>
