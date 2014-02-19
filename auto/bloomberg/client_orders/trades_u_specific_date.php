<?php 

//*********************************************************************************
//                                                                                *
//             TO BE RUN FROM COMMAND PROMPT ONLY (e.g. BAT FILE)                 *
//                                                                                *
//*********************************************************************************

include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 

/*$source_file_path_local = "D:\\tdw\\etpa\\auto\\tradeware\\data\\"; //Trailing Slash required.

function wob ($str) { echo "\n".$str."\n"; }

wob(date('m-d-Y H:i:sa'));

//initiate page load time routine
$time=getmicrotime();

$date_to_process = date('mdy');
//$date_to_process = '120508';
wob ("Date to Process: ".$date_to_process);

//create filenames to download
$arr_tradeware_filenames = array("TWorders".$date_to_process.".xfr","TWreports".$date_to_process.".xfr");
//print_r($arr_tradeware_filenames);

//first copy the files from the netwobk/ftp location to the local location
$cmd = 'copy "M:\\Tradeware Files\\' . $arr_tradeware_filenames[0] . '" "' . $source_file_path_local . '"';
wob ($cmd);
shell_exec($cmd);
$cmd = 'copy "M:\\Tradeware Files\\' . $arr_tradeware_filenames[1] . '" "' . $source_file_path_local . '"';
wob ($cmd);
shell_exec($cmd);*/

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

$source_file_path_local = "D:\\tdw\\etpa\\auto\\tradeware\\data_get\\"; //Trailing Slash required.

//initiate page load time routine
$time=getmicrotime();

//$date_to_process = date('mdy');
$date_to_process = '112210'; //date('mdy'); 
echo $date_to_process."\n";
//$date_to_process = '072610';

wob ("Date to Process: ".$date_to_process); 

//create filenames to download
$arr_tradeware_filenames = array("TWorders".$date_to_process.".xfr","TWreports".$date_to_process.".xfr", "TWallocs".$date_to_process.".xfr");
print_r($arr_tradeware_filenames);

wob(date('m-d-Y H:i:sa'));

$output_script_filename = "auto_process_ftp_tradeware.txt";
$fp = fopen($output_script_filename, "w");
//Put commands in the config file
fputs ($fp, "cd outgoing"."\r\n");
//fputs ($fp, "bin"."\r\n");
fputs ($fp, "get ".$arr_tradeware_filenames[0]. " " . $source_file_path_local.$arr_tradeware_filenames[0]."\r\n");
fputs ($fp, "get ".$arr_tradeware_filenames[1]. " " . $source_file_path_local.$arr_tradeware_filenames[1]."\r\n");
fputs ($fp, "get ".$arr_tradeware_filenames[2]. " " . $source_file_path_local.$arr_tradeware_filenames[2]."\r\n");
fclose($fp);

shell_exec("auto_process_ftp_tradeware.bat");

echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";  



if (1==1) {
//urgent_err_email_alert("subject", "message");
}
wob("Checking if the Tradeware Marketcenter files exist on local Location at ".$source_file_path_local);

if (!file_exists($source_file_path_local.$arr_tradeware_filenames[0]) OR !file_exists($source_file_path_local.$arr_tradeware_filenames[1])) {
	$sub = "One or more Tradeware Marketcenter files missing.";
	$msg = "Check for existence of files. Files missing."; // in ". $source_file_path;
	urgent_err_email_alert($sub, $msg);
  wob("Process exiting. Files don't exist on FTP Location"); // at ".$source_file_path);
	exit;
} else {
wob("Files exist. Proceeding.");
}
 
//Empty the fidelity_emp_trades_raw table
	$_query_empty = "TRUNCATE TABLE tradeware_orders_raw";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());

$file = fopen($source_file_path_local.$arr_tradeware_filenames[0],"r"); 

$qry = "insert into tradeware_orders_raw
				(
					auto_id,
					major_id,
					msg_type,
					Ticker,
					Suffix,
					Quantity,
					limit_price,
					stop_price,
					mkt_lmt,
					buy_sell,
					Marketplace,
					mp_instance,
					Exchange,
					Customer_id,
					app_id,
					Date,
					Time,
					Firm,
					Branch,
					seq_num,
					trading_account,
					customer_account,
					time_in_force,
					is_poss_dupe,
					is_resend,
					is_program,
					Basket,
					Sector,
					Exchange_time,
					Commission,
					commission_type,
					Markup,
					Trader,
					Comment,
					parent_id,
					minor_id,
					locate_id,
					Settlement_Type,
					Fut_Sett_Date,
					manual_time,
					Is_electronic 
				) values ";
				
$bulk_insert_string = "";
while ($data = fgetcsv($file,4096, ",")) 
{

$bulk_insert_string .= " (".
				  "null,".
					"'".str_replace("null","",$data[0])."',".
					"'".str_replace("null","",$data[1])."',".
					"'".str_replace("null","",$data[2])."',".
					"'".str_replace("null","",$data[3])."',".
					"'".str_replace("null","",$data[4])."',".
					"'".str_replace("null","",$data[5])."',".
					"'".str_replace("null","",$data[6])."',".
					"'".str_replace("null","",$data[7])."',".
					"'".str_replace("null","",$data[8])."',".
					"'".str_replace("null","",$data[9])."',".
					"'".str_replace("null","",$data[10])."',".
					"'".str_replace("null","",$data[11])."',".
					"'".str_replace("null","",$data[12])."',".
					"'".str_replace("null","",$data[13])."',".
					"DATE_FORMAT('".str_replace("null","000000",$data[14])."','%m%d%y'),".
					"'".str_replace("null","000000",$data[15])."',".
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
					"'".str_replace("null","",$data[33])."',".
					"'".str_replace("null","",$data[34])."',".
					"'".str_replace("null","",$data[35])."',".
					"'".str_replace("null","",$data[36])."',".
					"'".str_replace("null","",$data[37])."',".
					"'".str_replace("null","000000",$data[38])."',".
					"'".str_replace("null","",$data[39])."'),";
			  //xdebug("qry",$qry);
					
}
  $bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
  //echo $qry.$bulk_insert_string;
  //exit;

	//$result = mysql_query($qry.$bulk_insert_string) or die(mysql_error($qry.$bulk_insert_string));


	fclose ($file); 

              echo " Process Time ". sprintf("%01.4f",((getmicrotime()-$time)/1000))." s.";             


// 2 ===================================================================================
//initiate page load time routine
$time=getmicrotime();
 
//Empty the fidelity_emp_trades_raw table
	$_query_empty = "TRUNCATE TABLE tradeware_reports_raw";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());


$file = fopen($source_file_path_local.$arr_tradeware_filenames[1],"r"); 

$qry = "insert into tradeware_reports_raw 
				(
					R,
					Ticker,
					Suffix,
					Quantity,
					fill_price,
					limit_price,
					mkt_lmt,
					buy_sell,
					Exchange,
					customer_id,
					app_id,
					Datez,
					Timez,
					Firm,
					Branch,
					seq_num,
					Leaves,
					stop_price,
					trading_account,
					customer_accou,
					give_up,
					Basket,
					Major_id,
					Comm_rate,
					Comm_type,
					Markup,
					Comment,
					exec_broker,
					busted_flag,
					contra_specialist,
					contra_mnemonic,
					contra_badge,
					contra_qty,
					contra_date,
					contra_time,
					manual_time,
					tran_id,
					parent_id,
					minor_id
				) values ";

$bulk_insert_string = "";	
$n_at_a_time = 0;	
$k = 0;		
while ($data = fgetcsv($file,4096, ",")) 
{
$k = $k + 1;

$bulk_insert_string .= " (".
					"'".str_replace("null","",$data[0])."',".
					"'".str_replace("null","",$data[1])."',".
					"'".str_replace("null","",$data[2])."',".
					"'".str_replace("null","",$data[3])."',".
					"'".str_replace("null","",$data[4])."',".
					"'".str_replace("null","",$data[5])."',".
					"'".str_replace("null","",$data[6])."',".
					"'".str_replace("null","",$data[7])."',".
					"'".str_replace("null","",$data[8])."',".
					"'".str_replace("null","",$data[9])."',".
					"'".str_replace("null","",$data[10])."',".
					"'".date('Y-m-d')."',".
					"'".date('Y-m-d')." ".substr($data[12],0,2).":".substr($data[12],2,2).":".substr($data[12],4,2)."',".
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
					"'".str_replace("null","",$data[36])."',".
					"'".str_replace("null","",$data[37])."',".
					"'".str_replace("null","",$data[38])."'),";
			  
					
					if ($n_at_a_time == 1000) {
						$n_at_a_time = 0;
						
						$bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
						$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
						
					  $bulk_insert_string = "";
					} else {
						
						$n_at_a_time = $n_at_a_time + 1;
					
					}
					
					//xdebug("qry",$qry);
			
					
}
  $bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
  //echo $qry.$bulk_insert_string;
  //exit;

	$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));

  wob ("Rows Processed : ". $k);
	
	//$result = mysql_query($qry) or die(tdw_mysql_error($qry));
	fclose ($file); 

              echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";  
// 2 ===================================================================================

?>