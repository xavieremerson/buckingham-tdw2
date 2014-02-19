<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN JOVUS SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# SQL Server Connection Information
/*$msconnect=mssql_connect("1Z92.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);*/

$msconnect=mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");
$msdb=mssql_select_db("BuckinghamTwo",$msconnect);


		ydebug("\n".'Process Start Time', date('m/d/Y H:i:s a'));
	  ydebug('Connecting to Jovus Server @ Buckingham','Successful');
    //Getting Coverage Universe from Jovus
		 
		$str_tickers = "";

		$ms_qry_tickers   =  "SELECT distinct(dbo.ExchangeSecurities.Ticker) as CUSIP
													FROM dbo.Issuers 
													INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID
													ORDER BY dbo.ExchangeSecurities.Ticker DESC";


		$ms_results_tickers = mssql_query($ms_qry_tickers);
		
   	$rowcount = 0;
		while ($row_tickers = mssql_fetch_array($ms_results_tickers)) {
								
					$str_tickers = $row_tickers[0].",".$str_tickers;


		$rowcount = $rowcount + 1;		
		}
		
		$result_truncate = mysql_query("truncate table cvr_coverage_universe") or die(tdw_mysql_error("truncate table cvr_coverage_universe"));
    $qry_insert = "insert into cvr_coverage_universe values('".$str_tickers."')";
		$result_insert = mysql_query($qry_insert) or die(tdw_mysql_error($qry_insert));

	 ydebug('Number of Symbols inserted : ',$rowcount);
	 ydebug('Process Finish Time', date('m/d/Y H:i:s a'));

?>
