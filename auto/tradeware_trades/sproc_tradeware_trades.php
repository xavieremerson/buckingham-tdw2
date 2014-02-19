<?php 
ini_set('max_execution_time', 7200);

//*********************************************************************************
//                                                                                *
//             TO BE RUN FROM COMMAND PROMPT ONLY (e.g. BAT FILE)                 *
//                                                                                *
//*********************************************************************************

// SAME DAY PROCESSING i.e AT 10:00PM, process files for the same day.
// RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS

include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 

$source_file_path_local = "D:\\tdw\\etpa\\auto\\tradeware\\data_get\\"; //Trailing Slash required.

function wob ($str) { echo "\n".$str."\n"; }

////SECTION: DO NOT RUN ON WEEKENDS OR HOLIDAYS
//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!<br>";
		exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!<br>";
	}
  echo "Proceeding after holiday/weekend check....<br>";
//====================================================================================================

  wob(date('m-d-Y H:i:sa'));

	$trade_date_to_process = date('Y-m-d'); //previous_business_day();
	//$trade_date_to_process = '2010-07-26';
	wob("trade_date_to_process: ".$trade_date_to_process);
	echo "\n";

	$date_to_process = date('mdy',strtotime($trade_date_to_process));
	//$date_to_process = '072610'; //2010-05-03
	wob ("Date to Process: ".$date_to_process);
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	wob("Checking if the Tradeware Marketcenter files exist");
	
	if (!file_exists($source_file_path_local."TWreports".$date_to_process.".xfr")) {
		wob("Process exiting. Files don't exist on FTP Location"); // at ".$source_file_path);
		
					//===========================================================================================================
					echo "Tradeware File "."TWreports".$date_to_process.".xfr does not exist.<br>";
					//EMAIL ROUTINE TO SUPPORT
					$email_log = '
									<hr> 
									<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">
									Tradeware File TWreports'.$date_to_process.'.xfr does not exist.<br>
									This could be due to faulty ftp transmission from Tradeware or a network issue at Buckingham.<br>
									Please contact appropriate TDW Support Personnel to resolve this issue.</font>
									</b>
									<hr>
									<br><br><br><br><br><br><br><br><br><br><br>
											';
					//create mail to send
					$html_body .= zSysMailHeader("");
					$html_body .= $email_log;
					$html_body .= zSysMailFooter ();
					
					$subject = "Urgent Alert : Tradeware File "."TWreports".$date_to_process.".xfr does not exist. (".date('m-d-Y').")";
					$text_body = $subject;
					
					zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
					//zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
					//===========================================================================================================
		exit;
	} else {
	wob("Files exist. Proceeding.");
	}
	 
	//=================================================================================================================
	//initiate page load time routine
	$time=getmicrotime();
								
	$file = fopen($source_file_path_local."TWreports".$date_to_process.".xfr","r"); 
	
	$qry = "insert into tradeware_trades 
					(
						auto_id,
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
	
	$bulk_insert_string .= " (NULL,".
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
						"'".date('Y-m-d', strtotime($trade_date_to_process))."',".
						"'".date('Y-m-d', strtotime($trade_date_to_process))." ".substr($data[12],0,2).":".substr($data[12],2,2).":".substr($data[12],4,2)."',".
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


    //??????????????????????????????????????????????????????????????????????????????????????
		// Insert into table 
		$qry_consolidate = "insert into tradeware_trades_consolidated select 
												NULL,
												Ticker,
												sum(Quantity),
												avg(fill_price),
												buy_sell,
												max(Exchange),
												count(distinct(Exchange)),
												customer_id,
												Datez,
												max(exec_broker),
												count(distinct(exec_broker)),
												min(manual_time),
												max(manual_time),
												min(parent_id),
												count(distinct(parent_id))
												from tradeware_trades
												WHERE Datez = '".$trade_date_to_process."' 
												 group by Ticker, buy_sell,customer_id,Datez
												 order by Datez, Ticker";
		echo "\n".$qry_consolidate."\n";
		$result_consolidate = mysql_query($qry_consolidate) or die(tdw_mysql_error($qry_consolidate));
		
		//??????????????????????????????????????????????????????????????????????????????????????

	
		echo "Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n";  
  	// 2 ===================================================================================
		ob_flush(); 
		flush();
		
		
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		echo "Tradeware File "."TWreports".$date_to_process.".xfr exists and successfully processed.<br>";
		//EMAIL ROUTINE TO SUPPORT
		$email_log = '
						<hr> 
						<b><font color="#00FF00" size="2" face="Arial, Helvetica, sans-serif">
						Tradeware File TWreports'.$date_to_process.'.xfr exists and successfully processed.<br>
						</b>
						<hr>
						<br><br><br><br><br><br><br><br><br><br><br>
								';
		//create mail to send
		$html_body .= zSysMailHeader("");
		$html_body .= $email_log;
		$html_body .= zSysMailFooter ();
		
		$subject = "Tradeware File "."TWreports".$date_to_process.".xfr exists and successfully processed. (".date('m-d-Y').")";
		$text_body = $subject;
		
		zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
		//zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
		
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
?>