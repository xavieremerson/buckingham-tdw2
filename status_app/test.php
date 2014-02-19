<?php //iframe for chat module
set_time_limit(0);
include "connect.php";

$arr_ppl = array("James Sterling","Michelle Baker","John Broward","System Message","Nancy O'Hare","Patrick Sizemore","Bethany Mole","Margaret Maples","System Message");
$arr_messages = array(
								"This is a sample message for demonstations purposes.",
								"Message is for demostration.",
								"This test message is sent by a message generator for demo.",
								"This can be a message containing a request for preauthorization of trade.");

for ($i = 0; $i < 500000; $i++) {
$message_by = $arr_ppl[rand(0,8)];
	if($message_by == "System Message" ) {
		$message_type = 0;
		} else {
		$message_type = 1;
		}

$insert_message="INSERT INTO smes_messages( ID , poster , message , registered , time ) VALUES ('', '".$message_by."', '".$arr_messages[rand(0,3)]."', '".$message_type."', NOW())";
$result_insert_message=mysql_query($insert_message) ; //or die("Could not insert message ". $i)
  echo $insert_message. "\n";
	echo "Message inserted: ". $i . "\n";
	sleep(rand(2,5)); 

	if ($i == 20) {
	$result_delete=mysql_query("DELETE FROM smes_messages limit 20") ; //or die("Could not insert message ". $i)
	echo "Table cleared" . "\n";
	$i = 0;
	}

}


?>




  



