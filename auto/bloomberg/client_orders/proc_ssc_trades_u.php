<?php 

//*********************************************************************************
//                                                                                *
//             TO BE RUN FROM COMMAND PROMPT ONLY (e.g. BAT FILE)                 *
//                                                                                *
//*********************************************************************************

include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 

function wob ($str) { echo "\n".$str."\n"; }
//function wob ($str) { echo "\n<br>".$str."\n<br>"; }  


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

$source_file_path_local = "D:\\tdw\\etpa\\auto\\tradeware\\ssc_trades\\"; //Trailing Slash required.

//initiate page load time routine
$time=getmicrotime();

$date_to_process = date('mdy');

echo $date_to_process."\n";
//$date_to_process = '072610';

wob ("Date to Process: ".$date_to_process); 

//create filenames to download
//$arr_ssc_filenames = array("TWorders".$date_to_process.".xfr","TWreports".$date_to_process.".xfr", "TWallocs".$date_to_process.".xfr");
$arr_ssc_filenames = array("SampleFile.csv");
print_r($arr_ssc_filenames);

wob(date('m-d-Y H:i:sa'));

$output_script_filename = "ssc_auto_process_ftp_tradeware.txt";
$fp = fopen($output_script_filename, "w");
//Put commands in the config file
fputs ($fp, "cd outgoing"."\r\n");
//fputs ($fp, "bin"."\r\n");
fputs ($fp, "get ".$arr_ssc_filenames[0]. " " . $source_file_path_local.$arr_ssc_filenames[0]."\r\n");
fclose($fp);

shell_exec("ssc_auto_process_ftp_tradeware.bat");

echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s."; 

wob("Checking if the SSC/Tradeware files exist on local Location at ".$source_file_path_local);

if (!file_exists($source_file_path_local.$arr_ssc_filenames[0]) ) { //OR !file_exists($source_file_path_local.$arr_tradeware_filenames[1])
	$sub = "One or more SSC/Tradeware files missing.";
	$msg = "Check for existence of files. Files missing."; // in ". $source_file_path;
	urgent_err_email_alert($sub, $msg);
  wob("Process exiting. Files don't exist on SSC/Tradeware FTP Location"); // at ".$source_file_path);
	exit;
} else {
wob("Files exist. Proceeding.");
}
 
//Empty orders table
	$_query_empty = "TRUNCATE TABLE ssc_trade_order";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());

$file = fopen($source_file_path_local.$arr_ssc_filenames[0],"r"); 

$qry = "INSERT INTO ssc_trade_order (
					auto_id ,
					ordr_trade_id ,
					ordr_order_id ,
					ordr_created_by ,
					ordr_direction ,
					ordr_symbol ,
					ordr_limit_type ,
					ordr_limit ,
					ordr_quantity ,
					ordr_filled_quantity ,
					ordr_filled_average_price ,
					ordr_cancel_quantity ,
					ordr_cancel_date ,
					ordr_cancel_time ,
					ordr_date ,
					ordr_time ,
					ordr_destination ,
					ordr_client_import_id 
					)
					VALUES ";
				
$bulk_insert_string = "";
while ($data = fgetcsv($file,4096, ",")) 
{

	//echo $data[0]."\n";
	if ($data[0] == 'O') {
	
			if ($data[12] == "") {
				$val_dt_cxl = 'NULL';
			} else {
				$val_dt_cxl = "'".date('Y-m-d', strtotime($data[12]))."'";
			}
			if ($data[14] == "") {
				$val_dt_ord = 'NULL';
			} else {
				$val_dt_ord = "'".date('Y-m-d', strtotime($data[14]))."'";
			}
		
			if ($data[13] == "") {
				$val_time_cxl = 'NULL';
			} else {
				$val_time_cxl = "'".date('h:i:s',strtotime($data[13]))."'";
			}
			if ($data[15] == "") {
				$val_time_ord = 'NULL';
			} else {
				$val_time_ord = "'".date('h:i:s',strtotime($data[15]))."'";
			}

	
		$bulk_insert_string .= " (".
				  "null,".
					"'".$data[1]."',".
					"'".$data[2]."',".
					"'".$data[3]."',".
					"'".$data[4]."',".
					"'".$data[5]."',".
					"'".$data[6]."',".
					"'".$data[7]."',".
					"'".$data[8]."',".
					"'".$data[9]."',".
					"'".$data[10]."',".
					"'".$data[11]."',".
					$val_dt_cxl.",".
					$val_time_cxl.",".
					$val_dt_ord.",".
					$val_time_ord.",".
					"'".$data[16]."',".
					"'".$data[17]."'),";
			}
					
}
  $bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
  
	//echo $qry.$bulk_insert_string;

	$result = mysql_query($qry.$bulk_insert_string) or die(mysql_error($qry.$bulk_insert_string));

	echo " Process Time ". sprintf("%01.4f",((getmicrotime()-$time)/1000))." s.";             

	fclose ($file); 


// 2 ===================================================================================
//initiate page load time routine
$time=getmicrotime();
 
//Empty the fidelity_emp_trades_raw table
	$_query_empty = "TRUNCATE TABLE ssc_trade_staged_order";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());
	
$file = fopen($source_file_path_local.$arr_ssc_filenames[0],"r"); 	

$qry = "INSERT INTO ssc_trade_staged_order (
				auto_id ,
				stgord_trade_id ,
				stgord_trader ,
				stgord_created_by ,
				stgord_direction ,
				stgord_symbol ,
				stgord_limit_type ,
				stgord_limit ,
				stgord_solicited_flag ,
				stgord_held_flag ,
				stgord_discretionary_flag ,
				stgord_borrowed_from ,
				stgord_quantity ,
				stgord_filled_quantity ,
				stgord_filled_average_price ,
				stgord_date ,
				stgord_time ,
				stgord_client_import_id 
				)
				VALUES ";

$bulk_insert_string = "";	
$n_at_a_time = 0;	
$k = 0;		
while ($data = fgetcsv($file,4096, ",")) 
{
	//==================================================================================================
	if ($data[0] == 'S') {
	
			if ($data[15] == "") {
				$val_dt_stgord = 'NULL';
			} else {
				$val_dt_stgord = "'".date('Y-m-d', strtotime($data[15]))."'";
			}
			if ($data[16] == "") {
				$val_time_stgord = 'NULL';
			} else {
				$val_time_stgord = "'".date('h:i:s', strtotime($data[16]))."'";
			}
			
		$k = $k + 1;
		
		$bulk_insert_string .= " (".
				  "null,".
					"'".$data[1]."',".
					"'".$data[2]."',".
					"'".$data[3]."',".
					"'".$data[4]."',".
					"'".$data[5]."',".
					"'".$data[6]."',".
					"'".$data[7]."',".
					"'".$data[8]."',".
					"'".$data[9]."',".
					"'".$data[10]."',".
					"'".$data[11]."',".
					"'".$data[12]."',".
					"'".$data[13]."',".
					"'".$data[14]."',".
					$val_dt_stgord.",".
					$val_time_stgord.",".
					"'".$data[17]."'),";
			}
	//==================================================================================================

					
			if ($n_at_a_time == 1000) {
				$n_at_a_time = 0;
				
				$bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
				//echo $bulk_insert_string;
				$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
				
				$bulk_insert_string = "";
			} else {
				
				$n_at_a_time = $n_at_a_time + 1;
			
			}			
					
}

  $bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
	$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
  wob ("Rows Processed : ". $k);
	fclose ($file); 
  echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";  
// 2 ===================================================================================

// 3 ===================================================================================
//initiate page load time routine
$time=getmicrotime();
 
//Empty the fidelity_emp_trades_raw table
	$_query_empty = "TRUNCATE TABLE ssc_trade_execution";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());
	
$file = fopen($source_file_path_local.$arr_ssc_filenames[0],"r"); 	

$qry = "INSERT INTO ssc_trade_execution (
				auto_id ,
				exec_trade_id ,
				exec_fill_id ,
				exec_direction ,
				exec_symbol ,
				exec_quantity ,
				exec_price ,
				exec_destination ,
				exec_trader ,
				exec_date ,
				exec_time ,
				exec_client_import_id ,
				exec_order_id ,
				exec_liquidity 
				)
				VALUES ";

$bulk_insert_string = "";	
$n_at_a_time = 0;	
$k = 0;		
while ($data = fgetcsv($file,4096, ",")) 
{
	//==================================================================================================
	if ($data[0] == 'E') {
	
			if ($data[9] == "") {
				$val_dt_exec = 'NULL';
			} else {
				$val_dt_exec = "'".date('Y-m-d', strtotime($data[9]))."'";
			}
			if ($data[10] == "") {
				$val_time_exec = 'NULL';
			} else {
				$val_time_exec = "'".date('h:i:s', strtotime($data[10]))."'";
			}
			
		//echo ">>".$val_dt_exec.">>".$val_time_exec.">>\n";
		$k = $k + 1;
		
		$bulk_insert_string .= " \n(".
				  "null,".
					"'".$data[1]."',".
					"'".$data[2]."',".
					"'".$data[3]."',".
					"'".$data[4]."',".
					"'".$data[5]."',".
					"'".$data[6]."',".
					"'".$data[7]."',".
					"'".$data[8]."',".
					$val_dt_exec.",".
					$val_time_exec.",".
					"'".$data[11]."',".
					"'".$data[12]."',".
					"'".$data[13]."'),";
			}
	//==================================================================================================

					
			if ($n_at_a_time == 1000) {
				$n_at_a_time = 0;
				
				$bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
				//echo $bulk_insert_string;
				$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
				
				$bulk_insert_string = "";
			} else {
				
				$n_at_a_time = $n_at_a_time + 1;
			
			}			
					
}

  $bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
	//echo $qry.$bulk_insert_string;
	//exit;
	$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
  wob ("Rows Processed : ". $k);
	fclose ($file); 
  echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";  
// 3 ===================================================================================

// 4 ===================================================================================
//initiate page load time routine
$time=getmicrotime();
 
//Empty the fidelity_emp_trades_raw table
	$_query_empty = "TRUNCATE TABLE ssc_trade_allocation";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());
	
$file = fopen($source_file_path_local.$arr_ssc_filenames[0],"r"); 	

$qry = "INSERT INTO ssc_trade_allocation (
				auto_id ,
				alloc_trade_id ,
				alloc_allocation_id ,
				alloc_direction ,
				alloc_symbol ,
				alloc_quantity ,
				alloc_average_price ,
				alloc_account_id ,
				alloc_account_short ,
				alloc_retail_account ,
				alloc_commission_type ,
				alloc_commission_rate ,
				alloc_commission ,
				alloc_trader ,
				alloc_date ,
				alloc_time ,
				alloc_client_import_id 
				)
				VALUES ";

$bulk_insert_string = "";	
$n_at_a_time = 0;	
$k = 0;		
while ($data = fgetcsv($file,4096, ",")) 
{
	//==================================================================================================
	if ($data[0] == 'A') {
	
			if ($data[14] == "") {
				$val_dt_alloc = 'NULL';
			} else {
				$val_dt_alloc = "'".date('Y-m-d', strtotime($data[14]))."'";
			}
			if ($data[15] == "") {
				$val_time_alloc = 'NULL';
			} else {
				$val_time_alloc = "'".date('h:i:s', strtotime($data[15]))."'";
			}
			
		//echo ">>".$val_dt_exec.">>".$val_time_exec.">>\n";
		$k = $k + 1;
		
		$bulk_insert_string .= " \n(".
				  "null,".
					"'".$data[1]."',".
					"'".$data[2]."',".
					"'".$data[3]."',".
					"'".$data[4]."',".
					"'".$data[5]."',".
					"'".$data[6]."',".
					"'".$data[7]."',".
					"'".$data[8]."',".
					"'".$data[9]."',".
					"'".$data[10]."',".
					"'".$data[11]."',".
					"'".$data[12]."',".
					"'".$data[13]."',".
					$val_dt_alloc.",".
					$val_time_alloc.",".
					"'".$data[16]."'),";
			}
	//==================================================================================================

					
			if ($n_at_a_time == 1000) {
				$n_at_a_time = 0;
				
				$bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
				//echo $bulk_insert_string;
				$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
				
				$bulk_insert_string = "";
			} else {
				
				$n_at_a_time = $n_at_a_time + 1;
			
			}			
					
}

  $bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
	//echo $qry.$bulk_insert_string;
	//exit;
	$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
  wob ("Rows Processed : ". $k);
	fclose ($file); 
  echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";  
// 4 ===================================================================================
					/*"'".str_replace("null","",$data[0])."',".
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
					"'".str_replace("null","",$data[39])."'),";*/
?>