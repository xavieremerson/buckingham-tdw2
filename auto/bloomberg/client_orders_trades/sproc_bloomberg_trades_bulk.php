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

	$filelist = array("Orders_Trades20130422","Orders_Trades20130423","Orders_Trades20130424","Orders_Trades20130425","Orders_Trades20130426","Orders_Trades20130427","Orders_Trades20130428","Orders_Trades20130429","Orders_Trades20130430","Orders_Trades20130501","Orders_Trades20130502","Orders_Trades20130503","Orders_Trades20130504","Orders_Trades20130505","Orders_Trades20130506","Orders_Trades20130507","Orders_Trades20130508","Orders_Trades20130509","Orders_Trades20130510","Orders_Trades20130511","Orders_Trades20130512","Orders_Trades20130513","Orders_Trades20130514","Orders_Trades20130515","Orders_Trades20130516","Orders_Trades20130517","Orders_Trades20130518","Orders_Trades20130519","Orders_Trades20130520","Orders_Trades20130521","Orders_Trades20130522","Orders_Trades20130524","Orders_Trades20130525","Orders_Trades20130526","Orders_Trades20130527","Orders_Trades20130528","Orders_Trades20130529","Orders_Trades20130530","Orders_Trades20130531","Orders_Trades20130601","Orders_Trades20130602","Orders_Trades20130603","Orders_Trades20130604","Orders_Trades20130605","Orders_Trades20130606","Orders_Trades20130607","Orders_Trades20130608","Orders_Trades20130609","Orders_Trades20130610","Orders_Trades20130611","Orders_Trades20130612","Orders_Trades20130613","Orders_Trades20130614","Orders_Trades20130615","Orders_Trades20130616","Orders_Trades20130617","Orders_Trades20130618","Orders_Trades20130619","Orders_Trades20130620","Orders_Trades20130621","Orders_Trades20130622","Orders_Trades20130623","Orders_Trades20130624","Orders_Trades20130625","Orders_Trades20130626","Orders_Trades20130627","Orders_Trades20130628","Orders_Trades20130629","Orders_Trades20130630","Orders_Trades20130701","Orders_Trades20130702","Orders_Trades20130703","Orders_Trades20130704","Orders_Trades20130705","Orders_Trades20130706","Orders_Trades20130707","Orders_Trades20130708","Orders_Trades20130709","Orders_Trades20130710","Orders_Trades20130711","Orders_Trades20130712","Orders_Trades20130713","Orders_Trades20130714","Orders_Trades20130715","Orders_Trades20130716","Orders_Trades20130717","Orders_Trades20130718","Orders_Trades20130719","Orders_Trades20130720","Orders_Trades20130721","Orders_Trades20130722","Orders_Trades20130723","Orders_Trades20130724","Orders_Trades20130725","Orders_Trades20130726","Orders_Trades20130727","Orders_Trades20130728","Orders_Trades20130729","Orders_Trades20130730","Orders_Trades20130731","Orders_Trades20130801","Orders_Trades20130802","Orders_Trades20130803","Orders_Trades20130804","Orders_Trades20130805","Orders_Trades20130807","Orders_Trades20130808","Orders_Trades20130809","Orders_Trades20130810","Orders_Trades20130811","Orders_Trades20130812","Orders_Trades20130813","Orders_Trades20130814","Orders_Trades20130815","Orders_Trades20130816","Orders_Trades20130817","Orders_Trades20130818","Orders_Trades20130819","Orders_Trades20130820","Orders_Trades20130821","Orders_Trades20130822","Orders_Trades20130823","Orders_Trades20130824","Orders_Trades20130825","Orders_Trades20130826","Orders_Trades20130827","Orders_Trades20130828","Orders_Trades20130829","Orders_Trades20130830","Orders_Trades20130904","Orders_Trades20130905","Orders_Trades20130906","Orders_Trades20130909","Orders_Trades20130910","Orders_Trades20130911","Orders_Trades20130912","Orders_Trades20130913","Orders_Trades20130916","Orders_Trades20130917","Orders_Trades20130918","Orders_Trades20130919","Orders_Trades20130920","Orders_Trades20130923");
	
	foreach ($filelist as $zindex=>$zval) {
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
	//$trade_date_to_process = date('Y-m-d'); //previous_business_day();
	//$trade_date_to_process = '2013-09-20';
	$trade_date_to_process = substr($zval,13,4)."-".substr($zval,17,2)."-".substr($zval,19,2);
	wob("trade_date_to_process: ".$trade_date_to_process);
	echo "\n";

	$date_to_process = date('Ymd',strtotime($trade_date_to_process));
	//$date_to_process = '072610'; //2010-05-03
	wob ("Date to Process: ".$date_to_process);
	
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
												 group by Ticker, buy_sell,customer_id,Datez
												 order by Datez, Ticker";
		//echo "\n".$qry_consolidate."\n";
		$result_consolidate = mysql_query($qry_consolidate) or die(tdw_mysql_error($qry_consolidate));
		
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

		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	}
	
	

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
?>