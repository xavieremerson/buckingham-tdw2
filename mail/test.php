<?
require ('pop3.class.inc');

$pop3 = new POP3;

// Connect to mail server
$do = $pop3->connect ('192.168.20.38');
if ($do == false) {
        die($pop3->error);
}

echo 'Connected to mail server';


// Login to your inbox
$do = $pop3->login ('pprasad@centersys.com', 'Buck123');

if ($do == false) {
        die($pop3->error);
}

echo 'Logged in'; 

// Get office status
$status = $pop3->get_office_status();

if ($status == false) {
        die($pop3->error);
}

$count = $status['count_mails'];

echo 'There are ' . $count . ' new e-mails waiting for you!';


for ($i = 1; $i <= $count; $i++) { //10
        $email = $pop3->get_mail($i);

        if ($email == false) {
                echo $pop3->error;
                continue;
        }

        echo '<pre>';
        
				foreach($email as $k=>$v) {
					//print_r ($email);
					if(substr($v,0,8)=='Subject:') {
						echo $k . " => ".$v."<br>";
					}
				}
        echo '</pre>';
}




















$pop3->close();

?>
