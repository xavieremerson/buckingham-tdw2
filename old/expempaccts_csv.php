<?

  include('includes/dbconnect.php');
  include('includes/global.php'); 

$fp = fopen($exportlocation."EmployeeAccounts_".Date("m-d-Y").".csv", "w");

$result = mysql_query("SELECT * FROM Employee_accounts ORDER BY acct_number") or die (mysql_error());

$string = "\"REP\",\"ACCT. NUMBER\", \"NAME1\", \"NAME2\", \"OPEN DATE\n";
fputs ($fp, $string);

	while ( $row = mysql_fetch_array($result) ) {

		$string = "\"".$row["acct_rep"]."\",\"".$row["acct_number"]."\",\"".$row["acct_name1"]."\",\"".$row["acct_name2"]."\",\"".$row["acct_open_date"]."\"\n";
		fputs ($fp, $string);
	}

fclose($fp);

//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//This works!
Header("Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv");


//Trying this (works too, has some issues)

//header('Content-type: application/pdf');
/*header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv"');
readfile("data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv");
*/

?>