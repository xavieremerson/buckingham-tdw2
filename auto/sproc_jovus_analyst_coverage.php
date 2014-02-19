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
echo "Updating Analyst Coverage from Jovus to TDW\n";
ydebug('Connecting to Jovus Server @ Buckingham','Successful');
//Getting Coverage Universe from Jovus

$ms_qry_asym = "SELECT dbo.Products.ProductID, dbo.Issuers.PriceChartId, dbo.Persons.WorkEmail, left(convert(varchar(20),
                dbo.Prod_Statuses.DateTime,20),10) as releasedate
								FROM ((((dbo.Products 
								INNER JOIN dbo.Prod_Persons ON dbo.Products.ProductID = dbo.Prod_Persons.ProductID) 
								INNER JOIN dbo.Persons ON dbo.Prod_Persons.PersonID =  dbo.Persons.PersonID) 
								INNER JOIN dbo.Prod_Issuers ON dbo.Products.ProductID = dbo.Prod_Issuers.ProductID) 
								INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID) 
								INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID
								WHERE (((dbo.Prod_Statuses.StatusTypeID)=3))
								and dbo.Prod_Statuses.DateTime > getdate()-30
								ORDER BY dbo.Prod_Statuses.DateTime";

/*
After Jovus Migration the following removed since it does not seem to use/populate dbo.Products.ReportSubType
								and 
									(dbo.Products.ReportSubType = 'Company Report' or 
									 dbo.Products.ReportSubType = 'Morning Meeting Note')

*/

$ms_results_asym = mssql_query($ms_qry_asym);
$v_count_asym = 0;
while ($row_asym = mssql_fetch_array($ms_results_asym)) {

	//show_array($row_asym);

	/*
	0 = [10113]
	ProductID = [10113]
	1 = [TIF]
	CUSIP = [TIF]
	2 = [bwyckoff@buckresearch.com]
	WorkEmail = [bwyckoff@buckresearch.com]
	3 = [2006-11-27]
	releasedate = [2006-11-27]
  */
		
	$result_exist = mysql_query("SELECT * FROM acv_analyst_coverage 
																where acv_symbol = '".strtoupper(trim($row_asym["PriceChartId"]))."'
																and acv_email = '".strtolower(trim($row_asym["WorkEmail"]))."'") or die (mysql_error());
	$countx = mysql_num_rows($result_exist);
	if ($countx == 0) {
	$qry_insert = "INSERT INTO acv_analyst_coverage
										(auto_id,
										 acv_symbol,
										 acv_email,
										 acv_latest_date,
										 acv_tdw_userid,
										 acv_tdw_rr_num,
										 acv_last_updated) 
									VALUES (
									NULL , 
									'".strtoupper(trim($row_asym["PriceChartId"]))."', 
									'".strtolower(trim($row_asym["WorkEmail"]))."', 
									'".$row_asym["releasedate"]."', 
									0, 
									'', 
									now())";
									
			echo strtoupper(trim($row_asym["PriceChartId"]))."<br>";
			ob_flush();
			flush();

	$result_insert = mysql_query($qry_insert); // or die (tdw_mysql_error($qry_insert));
	} else {
	
  $qry_upd = "update acv_analyst_coverage
									set	acv_latest_date = '".$row_asym["releasedate"]."',
									acv_last_updated = now()
									WHERE acv_symbol = '".strtoupper(trim($row_asym["PriceChartId"]))."'
									AND acv_email = '".strtolower(trim($row_asym["WorkEmail"]))."'";
									
			echo strtoupper(trim($row_asym["PriceChartId"]))."<br>";
			ob_flush();
			flush();

	$result_upd = mysql_query($qry_upd); // or die (tdw_mysql_error($qry_upd));	
	}
  
	//echo "Processing row ".$v_count_asym."<br>";
	$v_count_asym = $v_count_asym + 1;
}	
									 
ydebug('Inserted records from Jovus to TDW','Successful');

$qry_blanks = "SELECT distinct(acv_email) as acv_email FROM acv_analyst_coverage 
																where acv_tdw_userid is NULL or acv_tdw_userid = 0
																or acv_tdw_rr_num is NULL";
//xdebug("qry_blanks",$qry_blanks);
$result_blanks = mysql_query($qry_blanks) or die (tdw_mysql_error($qry_blanks));
																
while($row_blanks = mysql_fetch_array($result_blanks))
{
$qry_update = "update acv_analyst_coverage 
                set acv_tdw_userid = (select ID from users where lower(Email) = '".$row_blanks["acv_email"]."'),
								    acv_tdw_rr_num = (select rr_num from users where lower(Email) = '".$row_blanks["acv_email"]."')
								where acv_email = '".$row_blanks["acv_email"]."'";
//xdebug("qry_update",$qry_update);
$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
}
ydebug('Updated records in TDW','Successful');
ydebug('Process Finish Time', date('m/d/Y H:i:s a'));

//AMATURO ISSUE
$qry_amaturo = "delete FROM acv_analyst_coverage 
								where acv_email = 'jamaturo@buckresearch.com' and 
								acv_symbol in ('NAV', 'HGN', 'PH', 'BA', 'AME', 'ANR', 'CF', 'ETN', 'WCC', 'HON', 'TDG')"; 
$result_amaturo = mysql_query($qry_amaturo) or die (tdw_mysql_error($qry_amaturo));



?>