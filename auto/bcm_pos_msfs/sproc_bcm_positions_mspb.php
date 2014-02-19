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

$source_file_path_local = "D:\\tdw\\tdw\\auto\\bcm_pos_msfs\\data_files\\"; //Trailing Slash required.

$source_file = "BCM-MS8040SW.080812.2141.csv";


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
		//exit;
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
	
	wob("Checking if the Morgan Stanley Position Files exist");
	
	//if (!file_exists($source_file_path_local."TWreports".$date_to_process.".xfr")) {
	if (!file_exists($source_file_path_local.$source_file)) {
		wob("Process exiting. Files don't exist on FTP Location"); // at ".$source_file_path);
		
					//===========================================================================================================
					echo "BCM Position Files for ".$date_to_process." does not exist.<br>";
					//EMAIL ROUTINE TO SUPPORT
					$email_log = '
									<hr> 
									<b><font color="#FF0000" size="2" face="Arial, Helvetica, sans-serif">
									BCM Position Files for '.$date_to_process.' does not exist.<br>
									This could be due to faulty ftp download from Morgan Stanley or file not available
									on Morgan Stanley FTO Site.<br>
									Please contact appropriate TDW Support Personnel to resolve this issue.</font>
									</b>
									<hr>
									<br><br><br><br><br><br><br><br><br><br><br>
											';
					//create mail to send
					$html_body .= zSysMailHeader("");
					$html_body .= $email_log;
					$html_body .= zSysMailFooter ();
					
					$subject = "Urgent Alert : BCM Position Files for ".$date_to_process." does not exist. (".date('m-d-Y').")";
					$text_body = $subject;
					
					zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
					//zSysMailer("brg-it@buckresearch.com", "", $subject, $html_body, $text_body, "") ;
					//===========================================================================================================
		exit;
	} else {
	wob("Files exist. Proceeding.");
	}

		//exit;

	//=================================================================================================================
	//initiate page load time routine
	$time=getmicrotime();
								
	$file = fopen($source_file_path_local.$source_file,"r"); 
	
	$qry = "insert into warehouse.pos_bcm_positions 
					(
						auto_id,
						pos_portfolio_id,
						pos_cusip,
						pos_symbol,
						long_short,
						transaction_number,
						acquired_date,
						executing_broker,
						quantity,
						price_base,
						market_value_net_base,
						unit_cost_base,
						cost_net_base,
						price_issue,
						market_value_net_issue,
						unit_cost_issue,
						cost_net_issue,
						currency_issue,
						country_of_issue,
						sedol,
						security_description,
						asset_class_level_2,
						fx_rate,
						fx_rate_multiplier_divider_indicator,
						money_manager,
						custodian,
						bloomberg_ticker_place_holder_only,
						contract_id,
						cost_gross_base,
						cost_gross_issue,
						deal_id,
						deal_description,
						deal_level_1,
						deal_level_2,
						deal_level_3,
						deal_level_4,
						epix_issuer_code_description_place_holder_only,
						epix_issuer_code_place_holder_only,
						isin,
						market_value_gross_base,
						market_value_gross_issue,
						notional_flag,
						position_type_description,
						position_type,
						pos_quick_id,
						reporting_date,
						ric
					) values ";
	
	$bulk_insert_string = "";	
	$n_at_a_time = 0;	
	$k = 0;		
	while ($data = fgetcsv($file,4096, ",")) 
	{
	$k = $k + 1;
	if ($k >= 3) {
		if ($data[1] != 'BKFSTOTAL') {
	$bulk_insert_string .= " (NULL,".
						"'".str_replace("null","",$data[0])."',".
						"'".str_replace("null","",$data[1])."',".
						"'".str_replace("null","",$data[2])."',".
						"'".str_replace("null","",$data[3])."',".
						"'".str_replace("null","",$data[4])."',".
						"'".date('Y-m-d', strtotime($data[5]))."',".
						"'".str_replace("null","",$data[6])."',".
						"'".str_replace("null","",$data[7])."',".
						"'".str_replace("null","",$data[8])."',".
						"'".str_replace("null","",$data[9])."',".
						"'".str_replace("null","",$data[10])."',".
						"'".str_replace("null","",$data[11])."',".
						"'".str_replace("null","",$data[12])."',".
						"'".str_replace("null","",$data[13])."',".
						"'".str_replace("null","",$data[14])."',".
						"'".str_replace("null","",$data[15])."',".
						"'".str_replace("null","",$data[16])."',".
						"'".str_replace("null","",$data[17])."',".
						"'".str_replace("null","",$data[18])."',".
						"'".str_replace("'","\\'",$data[19])."',".
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
						"'".str_replace("null","",$data[38])."',".
						"'".str_replace("null","",$data[39])."',".
						"'".str_replace("null","",$data[40])."',".
						"'".str_replace("null","",$data[41])."',".
						"'".str_replace("null","",$data[42])."',".
						"'".str_replace("null","",$data[43])."',".
						"'".date('Y-m-d', strtotime($data[44]))."',".
						"'".str_replace("null","",$data[45])."'),";
					
						
						if ($n_at_a_time == 1000) {
							$n_at_a_time = 0;
							
							$bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
							$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
							
							$bulk_insert_string = "";
						} else {
							
							$n_at_a_time = $n_at_a_time + 1;
						
						}
						
						//xdebug("qry",$qry);
				
			} //last row not processed.
		} //$k condition
						
	}
		$bulk_insert_string = substr($bulk_insert_string,0,(strlen($bulk_insert_string)-1));
		echo $qry.$bulk_insert_string;
		//exit;
	
		$result = mysql_query($qry.$bulk_insert_string) or die(tdw_mysql_error($qry.$bulk_insert_string));
	
		wob ("Rows Processed : ". $k);
		
		//$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		fclose ($file); 

exit;
?>