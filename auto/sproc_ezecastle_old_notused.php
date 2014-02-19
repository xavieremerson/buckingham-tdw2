<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

		//Previous Business Day should be applied here.
		$trade_date_to_process = previous_business_day();
		//$trade_date_to_process = '2008-02-19';
		
		//$date_match_val = date("M j Y",strtotime('2006-08-02'));
		$date_match_val = date("j-M",strtotime($trade_date_to_process));
		
		$date_start = date("m/d/Y",strtotime($trade_date_to_process));
		//$date_start = '11/27/2006';
		$date_end = date("m/d/Y");
	
		ydebug("\n".'Process Start Time', date('m/d/Y H:i:s a'));
		ydebug("date_start",$date_start);
		ydebug("date_end",$date_end);

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
		//xdebug("qry_clean_dupes",$qry_clean_dupes);
		$result_clean_dupes = mysql_query($qry_clean_dupes) or die (tdw_mysql_error($qry_clean_dupes));
		
		//ecs eze castle software
		$ms_qry_ecs   = 	"exec TradesByBroker ". "'".$date_start."'" .", " . "'".$date_end."'";
		//echo $ms_qry_ecs;
		//exit;
		echo 'Connecting to EzeCastle Server (buckez.buckresearch.com) @ '.date('m-d-Y h:i:s a')."\n";
		echo 'Executing statement: '.$ms_qry_ecs."\n";

		$ms_results_ecs = mssql_query($ms_qry_ecs);
		echo "Side,Date,Amount,Symbol,Price,Broker,Commission,NetMoney,BrokerAddTime"."\n";
		while ($row = mssql_fetch_array($ms_results_ecs)) {
				//print_r($row);
				//echo $row["Side"].",".$row["Date"].",".$row["Amount"].",".$row["Symbol"].",".$row["Price"].",".$row["Broker"].",".$row["Commission"].",".$row["NetMoney"].",".$row["BrokerAddTime"]."\n";
				
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
															oth_isactive ) 
														VALUES (
														  NULL , 
															STR_TO_DATE('".$row["Date"]."', '%m/%d/%Y'), 
															'".$row["Broker"]."', 
															'".$row["Side"]."', 
															'".$row["Symbol"]."', 
															'".$row["Amount"]."', 
															round('".$row["Price"]."',2), 
															round('".$row["Commission"]."',2),
															round('".$row["NetMoney"]."',2),
															'".$row["BrokerAddTime"]."', 
															'1'
														 )";
				 //xdebug("qry_insert_trade",$qry_insert_trade);
				 $result_insert_trade = mysql_query($qry_insert_trade) or die (tdw_mysql_error($qry_insert_trade));
														 
			}		
ydebug('Process Finish Time', date('m/d/Y H:i:s a'));
?>
