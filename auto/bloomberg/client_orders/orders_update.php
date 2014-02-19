<?php 

//*********************************************************************************
//                                                                                *
//             TO BE RUN FROM COMMAND PROMPT ONLY (e.g. BAT FILE)                 *
//                                                                                *
//*********************************************************************************

include('../includes/dbconnect.php');
include('../includes/functions.php'); 
include('../includes/global.php'); 

//function wob ($str) { echo "\n".$str."\n"; }
function wob ($str) { echo "\n<br>".$str."\n<br>"; }  


//=================================================================================

	$str_date = date('Y-m-d');
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!<br>";
		exit;
	}


//=================================================================================

$source_file_path_local = "D:\\tdw\\tdw\\auto\\bloomberg\\client_orders\\files\\"; //Trailing Slash required.

//initiate page load time routine
$time=getmicrotime();

$date_to_process = date('mdy');

echo $date_to_process."\n";
//$date_to_process = '072610';

$client_order_file = "BuckOrdersTest".date('Ymd');


//$client_order_file = "BuckOrders.csv";
//$client_order_file = "BuckOrders20130502";

if (1==1) {
//urgent_err_email_alert("subject", "message");
}
wob("Checking if the Bloomberg Client Orders file exists in local Location at ".$source_file_path_local);

if (!file_exists($source_file_path_local.$client_order_file)) {
	$sub = "Bloomberg Client Orders file missing.";
	$msg = "Check for existence of files. Files missing."; // in ". $source_file_path;
	//urgent_err_email_alert($sub, $msg);
  wob("Process exiting. Files don't exist on FTP Location"); // at ".$source_file_path);
	exit;
} else {
wob("Files exist. Proceeding.");
}
 
// 2 ===================================================================================

//initiate page load time routine
$time=getmicrotime();
 
//Empty the fidelity_emp_trades_raw table
	//$_query_empty = "TRUNCATE TABLE client_orders_street";
	//$_result_empty = mysql_query($_query_empty) or die(mysql_error());


$flush_table = mysql_query("truncate table client_orders_street");

$file = fopen($source_file_path_local.$client_order_file,"r"); 

$k = 0;		
while ($data = fgetcsv($file,4096, ",")) 
{
$k = $k + 1;

//print_r($data);
if ($k > 1) { //&& $data[36] == 'ORDER'

	//For limit orders, make the date current if missing
	ydebug("date field",$data[37]);
	if ($data[37] == "") {
		$last_order_time_val = date('Y-m-d H:i:s');
	} else {
		$last_order_time_val = date('Y-m-d H:i:s',strtotime($data[37]));
	}
	

$insert_qry = "insert into client_orders_street 
				(R,		Ticker, 	Quantity,		fill_price,		limit_price,		buy_sell,		Exchange,		customer_id,		app_id,		Datez,		Timez,
		Firm,		Branch,		seq_num,		Leaves,		stop_price,		trading_account,		customer_acct,		give_up,		Basket,		Major_id,
		Comm_rate,		Comm_type,		Markup,		Comment,		exec_broker,		busted_flag,		contra_specialist,		contra_mnemonic,		contra_badge,
		contra_qty,		contra_date,		contra_time,		tran_id,		parent_id,		minor_id,		last_trade) values "." (".
					"'".str_replace("null","",$data[0])."',".
					"'".str_replace("null","",$data[1])."',".
					"'".str_replace("null","",$data[2])."',".
					"'".str_replace("null","",$data[3])."',".
					"'".str_replace("null","",$data[4])."',".
					"'".str_replace("null","",$data[5])."',".
					"'".str_replace("null","",$data[6])."',".
					"'".str_replace("null","",$data[7])."',".
					"'".str_replace("null","",$data[8])."',".
					"'".date('Y-m-d')."',".
					"'".date('Y-m-d H:i:s',strtotime($data[10]))."',".
					"'',".
					"'',".
					"'".str_replace("null","",$data[13])."',".
					"'".str_replace("null","",$data[14])."',".
					"'".str_replace("null","",$data[15])."',".
					"'".str_replace("null","",$data[16])."',".
					"'".str_replace("null","",$data[17])."',".
					"'".str_replace("null","",$data[18])."',".
					"'".str_replace("null","",$data[19])."',".
					"'".str_replace("null","",$data[20])."',".
					"'".str_replace("null","",$data[21])."',".
					"'".str_replace("null","",$data[22])."',".
					"'".str_replace("null","",$data[23])."',".
					"'".str_replace("null","",$data[24])."',".
					"'".str_replace("null","",$data[25])."',".
					"'".str_replace("null","",$data[26])."',".
					"'".str_replace("null","",$data[27])."',".
					"'".str_replace("null","",$data[28])."',".
					"'".str_replace("null","",$data[29])."',".
					"'".str_replace("null","",$data[30])."',".
					"'".str_replace("null","",$data[31])."',".
					"'".str_replace("null","",$data[32])."',".
					"'".str_replace("null","0000-00-00","null")."',".
					"'".str_replace("null","000000",$data[34])."',".
					"'".str_replace("null","000000",$data[35])."',".
					"'".$last_order_time_val."')";
					
					echo ">>>>"."\n\n".$insert_qry."\n\n";
					
					$result = mysql_query($insert_qry); // or die(tdw_mysql_error($insert_qry));
					
}			  
					
			
					
}
	

  wob ("Rows Processed : ". ($k-1));
	
	//$result = mysql_query($qry) or die(tdw_mysql_error($qry));
	fclose ($file); 

  echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";  
// 2 ===================================================================================

?>