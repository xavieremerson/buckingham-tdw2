<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');


if (!$str) {
  echo "Data not fed to program appropriately.";
} else {

	$arr_str = explode("^",$str);
	$val_feature = $arr_str[0];
	$val_sales_id = $arr_str[1];
	$val_qtr = $arr_str[2];
	$val_year = $arr_str[3];
	$val_scount = $arr_str[4];
	
	$email = db_single_val("select Email as single_val from users where ID = ".	$val_sales_id);

	if ($val_feature == 'c') { //send reminder email to be sent  notify_email($email, $sub, $msg)
		//send email
		$sub = "Reminder for completion of Analyst Allocation Sheet for Q".$val_qtr . " ".$val_year;
		$msg = "Reminder:<br><br>Please complete your Analyst Allocation for Q".$val_qtr . " ".$val_year. "<br><br>Thank You.";
		notify_email($email, $sub, $msg);
		//notify_email("pprasad@centersys.com", $sub, $msg);

		//send return message.
		echo $val_feature."^"."Email reminder sent to ".$email."^".$val_scount;
	} elseif ($val_feature == 'b') { // make the user data editable
	
	  $str_sql = "update pay_analyst_allocations 
								set pay_final = 0 
								where pay_sales_id = '".$val_sales_id ."'
								  and pay_qtr = '".$val_qtr ."'
									and pay_year = '".$val_year ."'";
	  $result = mysql_query($str_sql) or die (tdw_mysql_error($str_sql));
		
		$sub = "Analyst Allocation Sheet for Q".$val_qtr . " ".$val_year. " is unlocked for editing";
		$msg = "<br><br>Analyst Allocation Sheet for Q".$val_qtr . " ".$val_year. " is unlocked for editing<br><br>Please complete your Analyst Allocation for Q".$val_qtr . " ".$val_year. "<br><br>Thank You.";
		notify_email($email, $sub, $msg);
		//notify_email("pprasad@centersys.com", $sub, $msg);
		echo $val_feature."^"."Analyst Allocation unlocked for editing and email notification sent to ".$email."^".$val_scount;
	} else {
		echo "ERROR";
	}
}
?>