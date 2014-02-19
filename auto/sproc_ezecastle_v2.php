<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

function error_alert_email($subject, $message) {

	//create mail to send
	$html_body = "";
	$html_body .= zSysMailHeader("");
	$html_body .= $message;
	$html_body .= zSysMailFooter();
	
	$text_body = $subject;
	
	zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;
	//zSysMailer('jperno@buckresearch.com', "Jessica Perno", $subject, $html_body, $text_body, "") ;
	//zSysMailer('rdaniels@buckresearch.com', "Robert Daniels", $subject, $html_body, $text_body, "") ;
}




		//Previous Business Day should be applied here.
		$trade_date_to_process = previous_business_day();
		//$trade_date_to_process = '2010-07-26';
		
		//$date_match_val = date("M j Y",strtotime('2006-08-02'));
		$date_match_val = date("j-M",strtotime($trade_date_to_process));
		
		$date_start = date("m/d/Y",strtotime($trade_date_to_process));
		//$date_start = '12/02/2010';
		$date_end = date("m/d/Y");
	
		ydebug("\n".'Process Start Time', date('m/d/Y H:i:s a'));
		ydebug("date_start",$date_start);
		ydebug("date_end",$date_end);


		//exit;

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN EZECASTLE SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		# SQL Server Connection Information
		//$msconnect=mssql_connect("buckez","pprasad","pprasad");
		$msconnect=mssql_connect("buckezdb","pprasad","pprasad");
		$msdb=mssql_select_db("TCArchive",$msconnect);


    //Most recent research date from Jovus

		$arr_rres = array();
		$arr_rres_symbols = array();

		
		//First, just so there are no duplicates, delete all trades from the table where the date fulfills the
		//criteria in the ecs query
		$qry_clean_dupes = "delete from oth_other_trades where oth_trade_date  >= '".$trade_date_to_process."'";
		xdebug("qry_clean_dupes",$qry_clean_dupes);
		$result_clean_dupes = mysql_query($qry_clean_dupes) or die (tdw_mysql_error($qry_clean_dupes));
		
		//ecs eze castle software
		$ms_qry_ecs   = 	"exec TradesByBroker_new ". "'".$date_start."'" .", " . "'".$date_end."'";
		//echo $ms_qry_ecs;
		//exit;
		echo 'Connecting to EzeCastle Server (buckez.buckresearch.com) @ '.date('m-d-Y h:i:s a')."\n";
		echo 'Executing statement: '.$ms_qry_ecs."\n";

		$ms_results_ecs = mssql_query($ms_qry_ecs);
		echo "Side,Date,Amount,Symbol,Price,Broker,Commission,NetMoney,BrokerAddTime"."\n";
		

    $count_return_rows = mssql_num_rows($ms_results_ecs);
		//xdebug("Rows in data",$count_return_rows);

		if ($count_return_rows == 0) {
			$msgtxt = 'TDW could not get BCM data from Ezecastle. The Compliance Report has to be rerun.<br><br>
								 Please email <a href="mailto:support@centersys.com">CenterSys Technical Support</a> requesting
								 a rerun of the report, when the data is available.<br><br>Thanks.<br><br>TDW Administrator.';
	
			$subjecttxt = "TDW: Ezecastle Data NOT AVAILABLE. [".date('m/d/Y h:i:sa')."]";
	
			error_alert_email($subjecttxt, $msgtxt);
	
			echo "exiting...";
			exit;
		}
		
		$ztemp_string = "";
		
		$countrow = 0;
		while ($row = mssql_fetch_array($ms_results_ecs)) {
		    
				//$ztemp_string .= "\n".implode(",",$row);
				//show_array($row);
				//print_r($row);
				//echo $row["Side"].",".$row["Date"].",".$row["Amount"].",".$row["Symbol"].",".$row["Price"].",".$row["Broker"].",".$row["Commission"].",".$row["NetMoney"].",".$row["BrokerAddTime"]."\n";

/*
  Side = [Buy]
  Date = [09/18/2009]
  Amount = [31700.0000000000]
  Symbol = [GIL]
  Price = [18.8511000000]
  Broker = [MSCO ]
  Commission = [1268.0000000000]
  NetMoney = [598847.8700000000]
  BrokerAddTime = [09:34:28]
  PM_Code = [DK ]
  EMPClient = [BCM-Client]
  EmpAlloc = [-]
  Trade_ID = [200909180024]
  Tradets = [Sep 18 2009 7:59AM]
  FirstExec = [Sep 18 2009 9:35AM]
  LastExec = [Sep 18 2009 9:35AM]
*/
				
				//insert this data into database
				$qry_insert_trade = "INSERT INTO oth_other_trades 
														( auto_id , 
															oth_trade_date , 
															oth_broker , 
															oth_buysell , 
															oth_symbol , 
															
															oth_quantity , 
															 
															oth_price , 
															oth_commission , 
															oth_net_money , 
															oth_trade_time , 
															
															oth_pm_code,  
															oth_emp_client,  
															oth_emp_alloc,
															oth_trade_id,
															oth_trade_ts,
															oth_first_exec,
															oth_last_exec, 	
																													
															oth_isactive ) 
														VALUES (
														  NULL , 
															STR_TO_DATE('".$row["Date"]."', '%m/%d/%Y'), 
															'".$row["Broker"]."', 
															'".$row["Side"]."', 
															'".$row["Symbol"]."', 
															
															round('".$row["Amount"]."',0),
															
															round('".$row["Price"]."',2), 
															round('".$row["Commission"]."',2),
															round('".$row["NetMoney"]."',2),
															'".$row["BrokerAddTime"]."', 

															'".$row["PM_Code"]."', 
															'".$row["EMPClient"]."', 
															'".$row["EmpAlloc"]."', 
															'".$row["Trade_ID"]."', 
															STR_TO_DATE( '".$row["Tradets"]."', '%b %e %Y %l:%i%p' ), 
															STR_TO_DATE( '".$row["FirstExec"]."', '%b %e %Y %l:%i%p' ),  
															STR_TO_DATE( '".$row["LastExec"]."', '%b %e %Y %l:%i%p' ), 
															
															'1'
														 )";
				 //xdebug("qry_insert_trade",$qry_insert_trade);
				 $result_insert_trade = mysql_query($qry_insert_trade) or error_alert_email("CRITICAL FAILURE: TDW", $qry_insert_trade); //die (error_alert_email("CRITICAL FAILURE: TDW", $qry_insert_trade)); //tdw_mysql_error($qry_insert_trade)
					
			$countrow++;
			echo $countrow."\n<br>";									 
			}		
			
//echo $ztemp_string;
ydebug('Process Finish Time', date('m/d/Y H:i:s a'));
?>
