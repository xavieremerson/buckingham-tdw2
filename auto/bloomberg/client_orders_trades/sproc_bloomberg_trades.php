<?php 
ini_set('max_execution_time', 7200);

//*********************************************************************************
//                                                                                *
//             TO BE RUN FROM COMMAND PROMPT ONLY (e.g. BAT FILE)                 *
//                                                                                *
//*********************************************************************************

// SAME DAY PROCESSING i.e AT 10:00PM, process files for the same day.
// RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS

include('../../../includes/dbconnect.php');
include('../../../includes/functions.php'); 
include('../../../includes/global.php'); 

$source_file_path_local = "D:\\tdw\\tdw\\auto\\bloomberg\\client_orders_trades\\files\\"; //Trailing Slash required.

function wob ($str) { echo "\n".$str."\n"; }

////SECTION: DO NOT RUN ON WEEKENDS OR HOLIDAYS
//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."\n";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!\n";
		exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!\n";
	}
  echo "Proceeding after holiday/weekend check....\n";
//====================================================================================================

  wob(date('m-d-Y H:i:sa'));

	$trade_date_to_process = date('Y-m-d'); //runs same trading day
	wob("trade_date_to_process: ".$trade_date_to_process);
	echo "\n";

	$date_to_process = date('Ymd',strtotime($trade_date_to_process));
	//$date_to_process = '072610'; //2010-05-03
	wob ("Date to Process: ".$date_to_process);
	
//	exit;
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	wob("Checking if the Bloomberg files exist");
	
	if (!file_exists($source_file_path_local."Orders_Trades".$date_to_process)) {
		wob("Process exiting. Files don't exist on FTP Location"); // at ".$source_file_path);
		
					//===========================================================================================================
					echo "Bloomberg File "."Orders_Trades".$date_to_process." does not exist.\n";
					//EMAIL ROUTINE TO SUPPORT
					$email_log = '
									<hr> 
									<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">
									Bloomberg File Orders_Trades'.$date_to_process.' does not exist.<br>
									This could be due to faulty ftp transmission from Bloomberg or a network issue at Buckingham.<br>
									Please contact appropriate TDW Support Personnel to resolve this issue.</font>
									</b>
									<hr>
									<br><br><br><br><br><br><br><br><br><br><br>
											';
					//create mail to send
					$html_body .= zSysMailHeader("");
					$html_body .= $email_log;
					$html_body .= zSysMailFooter ();
					
					$subject = "Urgent Alert : Bloomberg File "."Orders_Trades".$date_to_process." does not exist. (".date('m-d-Y').")";
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
								
	$file = fopen($source_file_path_local."Orders_Trades".$date_to_process."","r"); 
	
	$bulk_insert_string = "";	
	$n_at_a_time = 0;	
	$k = 0;
	$z = 0;		
	while ($data = fgetcsv($file,4096, ",")) 
	{
	$k = $k + 1;
	
			if ($k > 1 && $data[36] != 'ORDER') {
		
					$qry = "insert into tradeware_trades 
									(
									auto_id, R, Ticker, Suffix, Quantity, fill_price, limit_price, mkt_lmt, buy_sell, Exchange, customer_id, app_id,
									Datez, Timez, Firm, Branch, seq_num, Leaves, stop_price, trading_account, customer_accou, give_up, Basket, Major_id,
									Comm_rate, Comm_type, Markup, Comment, exec_broker, busted_flag, contra_specialist, contra_mnemonic, contra_badge,
									contra_qty, contra_date, contra_time, manual_time, tran_id, parent_id, minor_id
									) values (NULL,".
									"'".str_replace("null","",$data[0])."',".
									"'".str_replace("null","",$data[1])."',".
									"'',".
									"'".str_replace("null","",$data[2])."',".
									"'".str_replace("null","",$data[3])."',".
									"'".str_replace("null","",$data[4])."',".
									"0,".
									"'".str_replace("null","",$data[5])."',".
									"'".str_replace("null","",$data[6])."',".
									"'".str_replace("null","",$data[7])."',".
									"'".str_replace("null","",$data[8])."',".
									"'".date('Y-m-d',strtotime($data[10]))."',".
									"'".substr($data[10],6,4)."-".substr($data[10],0,2)."-".substr($data[10],3,2)." ".substr($data[10],11,8)."',".
									"'".str_replace("null","",$data[11])."',".
									"'".str_replace("null","",$data[12])."',".
									"0,".
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
									"'".substr($data[10],11,8)."',".
									"'".str_replace("null","",$data[33])."',".
									"'".str_replace("null","",$data[34])."',".
									"'".str_replace("null","",$data[35])."')";
									
/*										if ($k==2) {
											wob ($qry);
										}
*/									
										$result = mysql_query($qry) or die(tdw_mysql_error($qry));
										$z++;
			}
	}
	
		wob ("Rows Processed : ". $k);
		wob ("Rows Inserted : ". $z);
		
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
												 group by Ticker, buy_sell,customer_id";
												 //order by Datez, Ticker"; //,Datez
		//echo "\n".$qry_consolidate."\n";
		$result_consolidate = mysql_query($qry_consolidate); // or die(tdw_mysql_error($qry_consolidate));
		
		if (!$result_consolidate) {
		
				$message  = '<br>ERROR: ' . mysql_error() . "\n<br>";
				$message .= 'QUERY: ' . $qry_consolidate;
		
				//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
				echo "[NOT PROCESSED] Bloomberg File "."Orders_Trades".$date_to_process.".\n";
				//EMAIL ROUTINE TO SUPPORT
				$email_log = '
								<hr> 
								<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">'.$message.'
								</font>
								</b>
								<hr>
								<br><br><br><br><br><br><br><br><br><br><br>
										';
				//create mail to send
				$html_body .= zSysMailHeader("");
				$html_body .= $email_log;
				$html_body .= zSysMailFooter ();
				
				$subject = "[NOT PROCESSED] Bloomberg File "."Orders_Trades".$date_to_process." (".date('m-d-Y').")";
				$text_body = $subject;
				
				zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
				//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
				die($message);
		}
		
		//??????????????????????????????????????????????????????????????????????????????????????

	
		echo "Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n";  
  	// 2 ===================================================================================
		ob_flush(); 
		flush();
		
		
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		echo "Bloomberg File "."Orders_Trades".$date_to_process." exists and successfully processed.\n";
		//EMAIL ROUTINE TO SUPPORT
		$email_log = '
						<hr> 
						<b><font color="#00FF00" size="2" face="Arial, Helvetica, sans-serif">
						Bloomberg File Orders_Trades'.$date_to_process.' exists and successfully processed.<br>
						</b>
						<hr>
						<br><br><br><br><br><br><br><br><br><br><br>
								';
		//create mail to send
		$html_body .= zSysMailHeader("");
		$html_body .= $email_log;
		$html_body .= zSysMailFooter ();
		
		$subject = "Bloomberg File "."Orders_Trades".$date_to_process." exists and successfully processed. (".date('m-d-Y').")";
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