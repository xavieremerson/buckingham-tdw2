<?

error_reporting(E_ALL); 
ini_set ('display_errors', true);

ini_set('mssql.timeout', '600'); 

ini_set('max_execution_time', 7200);
ini_set('memory_limit','512M');
 
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


	//$trade_date_to_process = business_day_forward(strtotime($start_date_seed),$bizdays);
	$trade_date_to_process = previous_business_day();
	ydebug('trade_date_to_process',$trade_date_to_process);
								
	//$date_match_val = date("M j Y",strtotime('2006-08-02'));
	$date_match_val = date("j-M",strtotime($trade_date_to_process));
	
	$date_start = date("m/d/Y",strtotime($trade_date_to_process));
	$date_start = '01/23/2014';
	$date_end = date("m/d/Y");
	
	ydebug("\n".'Process Start Time', date('m/d/Y H:i:s a'));
	ydebug("date_start",$date_start); 
	ydebug("date_end",$date_end);
	
	ob_flush();
	flush();
	//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// BEGIN EZECASTLE SECTION
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			# SQL Server Connection Information
			//$msconnect=mssql_connect("buckez","pprasad","pprasad");
			//$msconnect=mssql_connect("buckezdb","pprasad","pprasad");

      $msconnect=mssql_connect("10.194.26.227","pprasad","pprasad"); //post move to new BCM Location Dec 16 2012
			if ( !$msconnect ) {
				die('MSSQL error: ' . mssql_get_last_message());
			}
			$msdb=mssql_select_db("TCArchive",$msconnect);

			echo 'MSSQL error: ' . mssql_get_last_message();
			
			$ms_qry_ecs   = 	"exec TradesByBroker_new ". "'".$date_start."'" .", " . "'".$date_end."'";
			$ms_results_ecs = mssql_query($ms_qry_ecs);

			echo 'MSSQL error: ' . mssql_get_last_message();

			$count_return_rows = mssql_num_rows($ms_results_ecs);
			ydebug("Rows in data",$count_return_rows);

			echo 'MSSQL error: ' . mssql_get_last_message();

			while ($row = mssql_fetch_array($ms_results_ecs)) {
				print_r($row);
			}

			exit;


// Connect to MSSQL and select the database
mssql_connect('KALLESPC\SQLEXPRESS', 'sa', 'phpfi');
mssql_select_db('php');

// Make a query that will fail
$query = @mssql_query('SELECT * FROM [php].[dbo].[not-found]');

			if ( !$msconnect ) {
				if ( function_exists('error_get_last') ) {
					 var_dump(error_get_last());
				}
				die('connection failed');
			}

			$ms_qry_ecs   = 	"exec TradesByBroker_new ". "'".$date_start."'" .", " . "'".$date_end."'";
			
			$row = mssql_fetch_array($ms_results_ecs);
			
			print_r($row);
			
			
			$count_return_rows = mssql_num_rows($ms_results_ecs);
			ydebug("Rows in data",$count_return_rows);
			exit;
			
			$proc = mssql_init("TradesByBroker_new ". "'".$date_start."'" .", " . "'".$date_end."'", $msconnect);
			$proc_result = mssql_execute($proc);
			
			$count_return_rows = mssql_num_rows($proc_result);
			ydebug("Rows in data",$count_return_rows);
			exit;
			
			$ms_qry_ecs   = 	"exec TradesByBroker_new ". "'".$date_start."'" .", " . "'".$date_end."'";
			
	//echo $ztemp_string;
	ydebug('Process Finish Time', date('m/d/Y H:i:s a'));
	//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

exit;
?>
