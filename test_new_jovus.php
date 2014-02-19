<?
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN JOVUS SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# SQL Server Connection Information

//$msconnect=mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");
//$msdb=mssql_select_db("BuckinghamTwo",$msconnect);

$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


/*$link = mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");

if(!$link)
{
    die('Something went wrong while connecting to MSSQL');
}
exit;*/

$ms_qry_asym = "select * from dbo.persons";  

$ms_results_asym = mssql_query($ms_qry_asym);
$v_count_asym = 0;
while ($row_asym = mssql_fetch_array($ms_results_asym)) {

//print_r($row_asym);

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
		  
	//echo "Processing row ".$v_count_asym."<br>";
	$v_count_asym = $v_count_asym + 1;
}	
echo "$v_count_asym"."\n";
?>

