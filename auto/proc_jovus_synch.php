<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN JOVUS SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# SQL Server Connection Information
$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


	xdebug('Connecting to Jovus Server @ Buckingham','Successful');
  //Getting Coverage Universe from Jovus
		 
		$str_tickers = "";

		$ms_qry_tickers   = 	"SELECT distinct(dbo.Issuers.CUSIP) as CUSIP
														FROM dbo.Issuers
														WHERE (((dbo.Issuers.CUSIP)<>''))";


		$ms_results_tickers = mssql_query($ms_qry_tickers);
		
   	while ($row_tickers = mssql_fetch_array($ms_results_tickers)) {
					
					$str_tickers = $row_tickers[0].",".$str_tickers;
		}
		
		//echo $str_tickers;

		$result_truncate = mysql_query("truncate table cvr_coverage_universe") or die(tdw_mysql_error("truncate table cvr_coverage_universe"));
    $qry_insert = "insert into cvr_coverage_universe values('".$str_tickers."')";
		$result_insert = mysql_query($qry_insert) or die(tdw_mysql_error($qry_insert));

	show_array(get_coverage_universe());
?>
