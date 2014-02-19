<?php 
ini_set('max_execution_time', 7200);

//*********************************************************************************
//                                                                                *
//             TO BE RUN FROM COMMAND PROMPT ONLY (e.g. BAT FILE)                 *
//                                                                                *
//*********************************************************************************

include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 

$source_file_path_local = "D:\\tdw\\etpa\\auto\\tradeware\\data\\"; //Trailing Slash required.

function wob ($str) { echo "\n".$str."\n"; }

wob(date('m-d-Y H:i:sa'));




$date_begin = "2010-04-26"; //2008-12-05 

for ($bizdays=1; $bizdays < 100; $bizdays++) { 

			if (strtotime(business_day_forward(strtotime($date_begin),$bizdays)) > strtotime("now")) {
				echo business_day_forward(strtotime($date_begin),$bizdays)."\n";
				echo "Exit condition met... Program exiting normally\n";
				exit;
				
			} else {
							$trade_date_to_process = business_day_forward(strtotime($date_begin),$bizdays);
							wob("trade_date_to_process: ".$trade_date_to_process);
							echo "\n";
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							$date_to_process = date('mdy',strtotime($trade_date_to_process));
							//$date_to_process = '120508';
							wob ("Date to Process: ".$date_to_process);
							
							wob("Checking if the Tradeware Marketcenter files exist");
							
							if (!file_exists($source_file_path_local."TWreports".$date_to_process.".xfr")) {
								wob("Process exiting. Files don't exist on FTP Location"); // at ".$source_file_path);
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
								
								fclose ($file); 
							
								echo "Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n";  
							// 2 ===================================================================================
							  ob_flush();
								flush();

							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
      }
			
}
?>